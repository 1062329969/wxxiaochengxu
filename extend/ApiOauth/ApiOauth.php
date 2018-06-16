<?php
namespace ApiOauth;
//微信授权类

class ApiOauth
{
	//access_token是公众号的全局唯一接口调用凭据，公众号调用各接口时都需使用access_token。开发者需要进行妥善保存。access_token的存储至少要保留512个字符空间。access_token的有效期目前为2个小时，需定时刷新，重复获取将导致上次获取的access_token失效。
	
	//传入的参数
	//info['appid']
	//info['appsecret']
	
	public function update_authorizer_access_token($info = NULL)
	{
	 
        $now = time();
        
        if($info == NULL)
        {
            return ;
        }

        $info["appid"] = trim($info["appid"]);
        $info["appsecret"] = trim($info["appsecret"]);

        $data = json_decode(file_get_contents("../apitext/access_token.json"));
        if ($data->expire_time < time() )
        {
               $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$info['appid']}&secret={$info['appsecret']}";
               $res = json_decode(httpGet($url));
               $access_token = $res->access_token;
               if ($access_token)
               {
                   $data->expire_time = time() + 6000;
                   $data->access_token = $access_token;
                   $fp = fopen("../apitext/access_token.json", "w");
                   fwrite($fp, json_encode($data));
                   fclose($fp);
              }
        }
        else
        {
            
            $access_token = $data->access_token;
        }
        
        return $access_token;

	}

	// jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
	public function getJsApiTicket($appid, $appsecret)
	{
		$data = json_decode(file_get_contents("../apitext/jsapi_ticket.json"));
		if ($data->expire_time < time())
		{
		   $info['appid']=$appid;
		   $info['appsecret']=$appsecret;

		   $accessToken = $this->update_authorizer_access_token($info);
		   $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
		   $res = json_decode(httpGet($url));
		   $ticket = $res->ticket;
		   if ($ticket)
		   {
			  $data->expire_time = time() + 7000;
			  $data->jsapi_ticket = $ticket;
			  $fp = fopen("../apitext/jsapi_ticket.json", "w");
			  fwrite($fp, json_encode($data));
			  fclose($fp);
		   }
		}
		else
		{
			 $ticket = $data->jsapi_ticket;
		}
	
		return $ticket;
	}
	
	//获取JSDK签名
    public function getSignature($nonceStr,$ticket,$timeStamp,$url)
    {
        $array  = array(
            "noncestr"      => $nonceStr,       
            "jsapi_ticket"  => $ticket,
            "timestamp"     => $timeStamp,
            "url"           => $url,
        );

        ksort($array);
        $signPars   = '';

        foreach($array as $k => $v) {
            if("" != $v && "sign" != $k) {
                if($signPars == ''){
                    $signPars .= $k . "=" . $v;
                }else{
                    $signPars .=  "&". $k . "=" . $v;
                }
            }
        }

        return SHA1($signPars);;
    }

	//网页授权
	public function webOauth($info, $scope = "", $fansInfo = "")
	{
		$now = time();
		$redirect_uri = getSelfUrl();    //获得当前的url地址
		$codeUrl = $this->get_code_url($info, $redirect_uri, $scope, "");   //获得微信的授权地址
        
		if (empty($_GET["code"]) && empty($_GET["state"]))
		{
			header("Location: $codeUrl");
			exit();
		}
		else
		{
			//获得code同意授权
			$code = $_GET["code"];
            
			//根据code 获取access_token
			if (!empty($code))
			{
				$res = $this->get_web_access_token($info, $code);
                
				if (empty($res["errcode"]))
				{
					$data = array("access_token" => $res["access_token"], "openid" => $res["openid"]);
					return $data;
				}
				else
				{
					exit("授权错误，请检查公众号权限和设置");
				}
			}
		}
	}
	
	//去微信服务器 拉去用户信息
	public function get_fans_info($access_token, $openid)
	{
		$url = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid . "&lang=zh_CN";
		$res = $this->https_request($url);
		return $res;
	}

	public function get_wechat_user_list($access_token, $openid){
        $url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token=".$access_token."&next_openid=".$openid;
        $res = $this->https_request($url);
        return $res;
    }
	
	
	//刷新 网页授权的access_token
	public function get_refresh_web_access_token($info)
	{
		if (($info["type"] == 1) && ($info["winxintype"] == 3) && ($info["oauth"] == 1))
		{
			$component_access_token = $this->get_component_access_token();
			$tokenurl = "https://api.weixin.qq.com/sns/oauth2/component/refresh_token?appid=" . $info["appid"] . "&grant_type=refresh_token&component_appid=" . $this->appId . "&component_access_token=" . $component_access_token . "&refresh_token=" . $info["web_refresh_token"];
		}
		else
		{
			$tokenurl = "https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=" . $info["appid"] . "&grant_type=refresh_token&refresh_token=" . $info["web_refresh_token"];
		}

		$res = $this->https_request($tokenurl);
		return $res;
	}
	
	
	//$redirect_uri 跳转的url地址
	//scope 微信授权类型
	public function get_code_url($info, $redirect_uri = "", $scope = "", $state = "oauth")
	{
		if (empty($scope))
		{
			if ($info["oauthinfo"])
			{
				$scope = "snsapi_userinfo";
			}
			else
			{
				$scope = "snsapi_base";
			}
		}

		$redirect_uri = urlencode($redirect_uri);
		$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$info["appid"]}&redirect_uri=$redirect_uri&response_type=code&scope=$scope&state=$state#wechat_redirect";
		
		return $url;
	}
	
	public function get_web_access_token($info, $code)
	{
		
		$tokenurl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $info["appid"] . "&secret=" . $info["appsecret"] . "&code=" . $code . "&grant_type=authorization_code";
		$res = $this->https_request($tokenurl);
		return $res;
	}
	
	
	public function https_request($url, $data = NULL)
	{
		$curl = curl_init();
		$header = "Accept-Charset: utf-8";
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		//curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		if (!empty($data))
		{
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}

		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($curl);
		
		//F("1", $output);
		$errorno = curl_errno($curl);

		if ($errorno)
		{
			return array("curl" => false, "errorno" => $errorno);
		}
		else
		{
			$res = json_decode($output, 1);

			if (isset($res["errcode"]))
			{
				return array("errcode" => $res["errcode"], "errmsg" => $res["errmsg"]);
			}
			else
			{
				return $res;
			}
		}

		curl_close($curl);
	}

    function getopenid($info,$js_code){
        $info["appid"] = trim($info["appid"]);
        $info["appsecret"] = trim($info["appsecret"]);
        if(empty($js_code)) return array('status'=>0,'info'=>'缺少js_code');

        $appid = $info["appid"];
        $appsecret = $info["appsecret"];
        $curl = 'https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code';
        $curl = sprintf($curl,$appid,$appsecret,$js_code);
        $result = request($curl);
        return array('status'=>1,'info'=>json_decode($result,true));
    }

}
?>