<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 后台主页
 */

namespace app\admin\controller;

use think\Db;
use think\Session;

class Article extends Admin
{
	//判断session是否已经过期 过期自动登录
	protected function _initialize()
	{
		 parent::_initialize();
	}

	public function index()
    {
        $list = Db::name('article')->where(['a_state'=>1])->paginate(15);
        $this->assign('list',$list);
        return $this->fetch();
    }

    public function add(){
        if($this->request->isPost()){
            $data = input('post.');
            unset($data['image']);
            $data['a_img'] = json_encode($data['a_img']);
            $data['a_addtime'] = $data['a_update'] = time();
            $val = Db::name("article")->insert($data);
            if($val){
                Session::flash('code',1);
                Session::flash('msg','添加成功');
            }else{
                Session::flash('code',0);
                Session::flash('msg','添加失败');
            }
            $this->redirect(url('index'));
        }else{
            return $this->fetch();
        }
    }

    public function del(){
        if($this->request->isAjax() && $this->request->isGet()){
            $a_id = input('get.a_id');
            $val = Db::name("article")->where(['a_id'=>$a_id])->update(['a_state'=>2]);
            if($val){
                $this->success('删除成功',url('index'));
            }else{
                $this->error('删除失败');
            }
        }else{
            return $this->fetch();
        }
    }
    public function alter(){
        $a_id = input('param.a_id');
        if($this->request->isPost()){
            $data = input('post.');
            unset($data['image']);
            $data['a_img'] = json_encode($data['a_img']);
            $data['a_update'] = time();
            $val = Db::name("article")->where(['a_id'=>$a_id])->update($data);
            if($val){
                $this->success('修改成功',url('index'));
            }else{
                $this->error('修改失败');
            }
        }else{
            $info = Db::name("article")->find($a_id);
            $this->assign('info',$info);
            return $this->fetch();
        }
    }

    public function upload(){
        $file = request()->file('image');
        $info = $file->move(ROOT_PATH . 'public' . DS . 'static' . DS . 'uploads');
        if($info){
            $returnImg=str_replace('\\','/','/static/uploads/'.$info->getSaveName());
            $this->success('上传成功','',['url'=>$returnImg]);
        }else{
            $this->error('上传失败');
        }
    }
}
