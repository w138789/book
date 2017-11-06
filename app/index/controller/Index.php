<?php

namespace app\index\controller;

class Index extends Base {

    public function index() {
        $data = model('Book')->order('id DESC')->paginate(10, true);
        $this->assign('data', $data);
        return $this->fetch();
    }

    public function chapter() {
        $book_id   = input('book_id');
        $data      = model('Chapter')->where(['book_id' => $book_id])->order('id DESC')->paginate(10, true);
        $book_name = model('Book')->where(['id' => $book_id])->value('name');
        $this->assign('data', $data);
        $this->assign('book_name', $book_name);
        return $this->fetch();
    }

    public function info($id = 0) {
        $info = model('Chapter')->where(['id' => $id])->find();
        if (empty($info)) {
            $this->redirect('/index/index/index');
        }
        model('Chapter')->where(['id' => $id])->update(['status' => 1]);
        $map     = [
            'book_id' => $info['book_id'],
        ];
        $previous_id = model('Chapter')->where('id <'.$id)->where($map)->order('id DESC')->value('id');
        $next_id = model('Chapter')->where('id >'.$id)->where($map)->order('id ASC')->value('id');
        $this->assign('info', $info);
        $this->assign('previous_id', $previous_id);
        $this->assign('next_id', $next_id);
        return $this->fetch();
    }

    public function insert() {
        $this->getHtml();
    }
}
