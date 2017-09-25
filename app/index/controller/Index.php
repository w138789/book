<?php

namespace app\index\controller;

class Index extends Base {
    public function index() {
        $data = model('Book')->order('id DESC')->paginate(10,true);
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function info($id = 0){
        $info = model('Book')->where(['id'=>$id])->find();
        if(empty($info)){
            $this->redirect('/index/index/index');
        }
        model('Book')->where(['id'=>$id])->update(['status'=>1]);
        $this->assign('info',$info);
        return $this->fetch();
    }

    public function insert() {
        $this->getHtml();
    }
}
