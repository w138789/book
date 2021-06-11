<?php

namespace app\index\controller;

class Index extends Base
{
    public function __construct()
    {
        parent::__construct();
        header("Access-Control-Allow-Origin: *");
        header('Access-Control-Allow-Headers:token');
    }

    /**
     * 书籍列表
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $data = model('Book')->where('status', 1)->order('id DESC')->paginate(10, true);
        $this->assign('data', $data);
        return $this->fetch();
    }

    /**
     * 文章列表
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function chapter()
    {
        $data     = '';
        $book_id  = input('book_id');
        $page     = input('page');
        $isReaded = model('Chapter')->where(['book_id' => $book_id, 'status' => 1])->count();
        $bookType = model('Book')->where('id', $book_id)->value('host_type');
        switch ($bookType) {
            case 'cn3k5':
            case 'fenghuo':
                $data = model('Chapter')->where(['book_id' => $book_id])->order("SUBSTRING_INDEX(url,'-',-1) + 0 ASC");
                break;
            case 'xbiquge':
            case 'biquku':
                $data = model('Chapter')->where(['book_id' => $book_id])->order("id ASC");
                break;
        }


        if ($page) {
            $data = $data->paginate(10, true);
        } else {
            $data = $data->paginate(10, true, ['page' => floor(($isReaded + 10) / 10)]);
        }
        $book_name = model('Book')->where(['id' => $book_id])->value('name');
        $this->assign('data', $data);
        $this->assign('book_name', $book_name);
        return $this->fetch();
    }

    /**
     * 文章详情
     * @param int $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function info($id = 0)
    {
        $previous_id = '';
        $next_id     = '';
        $info        = model('Chapter')->where(['id' => $id])->find();
        if (empty($info)) {
            $this->redirect('/index/index/index');
        }
        model('Chapter')->where(['id' => $id])->update(['status' => 1]);
        $map      = [
            'book_id' => $info['book_id'],
        ];
        $bookType = model('Book')->where('id', $info['book_id'])->value('host_type');
        switch ($bookType) {
            case 'cn3k5':
            case 'fenghuo':
                $previous_id = model('Chapter')->where("SUBSTRING_INDEX(url,'-',-1) + 0 < " . explode('-', $info['url'])[2])->where($map)->order("SUBSTRING_INDEX(url,'-',-1) + 0 DESC")->value('id');
                $next_id     = model('Chapter')->where("SUBSTRING_INDEX(url,'-',-1) + 0 > " . explode('-', $info['url'])[2])->where($map)->order("SUBSTRING_INDEX(url,'-',-1) + 0 ASC")->value('id');
                break;
            case 'xbiquge':
            case 'biquku':
                $previous_id = model('Chapter')->where('id', '<', $info['id'])->where($map)->order("id DESC")->value('id');
                $next_id     = model('Chapter')->where('id', '>', $info['id'])->where($map)->order("id ASC")->value('id');
                break;
        }

        $this->assign('info', $info);
        $this->assign('previous_id', $previous_id);
        $this->assign('next_id', $next_id);
        return $this->fetch();
    }

    /**
     * 拉文章
     */
    public function insert()
    {
        //拉www.booktxt.net 顶点小说网小说
        //$this->getBookTxtHtml();
        //拉fenghuo123.com 烽火中文网小说
        $hostType = model('Book')->field('host_type')->where('status', 1)->select();
        foreach ($hostType as $k => $v) {
            switch ($v['host_type']) {
                case 'fenghuo':
                    $this->getFenghuoHtml();
                    break;
                case 'cn3k5':
                    $this->getCn3k5Html();
                    break;
                case 'xbiquge':
                    $this->getXbiqugeHtml();
                    break;
                case 'biquku':
                    $this->getBiqukuHtml();
                    break;
            }
        }
    }

    /**
     * 拉全部文章
     */
    public function insertAll()
    {
        //拉www.booktxt.net 顶点小说网小说
        //$this->getBookTxtHtml();
        //拉fenghuo123.com 烽火中文网小说
        //$this->getFenghuoHtml();
        $this->getCn3k5HtmlAll();
    }

    /**
     * 切换夜间模式
     */
    public function swith()
    {
        $model = getSession('night');
        if ($model) {
            session('night', 0, config('prefix'));
        } else {
            session('night', 1, config('prefix'));
        }
    }

    /**
     * 登录
     * @return mixed
     */
    public function login()
    {
        if (IS_POST) {
            $password = input('password');
            if ($password != '7026546') {
                echo "<script> alert('登录错误'); </script>";
            } else {
                $prefix = config('prefix');
                session('isLogin', 1, $prefix);
                $redirectUrl = !getSession('redirectUrl') ? url('index') : getSession('redirectUrl');
                $this->redirect($redirectUrl);
            }
        }
        return $this->fetch();
    }

    /**
     * 书籍列表
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function appIndex()
    {
        $data = model('Book')->where('status', 1)->order('id DESC')->select();
        return $this->sendSuccess($data);
    }

    /**
     * 文章列表
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function appChapter()
    {
        $data     = '';
        $book_id  = input('book_id');
        $bookType = model('Book')->where('id', $book_id)->value('host_type');
        switch ($bookType) {
            case 'cn3k5':
            case 'fenghuo':
                $data = model('Chapter')->field('id, title')->where(['book_id' => $book_id])->order("SUBSTRING_INDEX(url,'-',-1) + 0 ASC");
                break;
            case 'xbiquge':
            case 'biquku':
                $data = model('Chapter')->field('id, title')->where(['book_id' => $book_id])->order("id ASC");
                break;
        }

        $data      = $data->select();
        $book_name = model('Book')->where(['id' => $book_id])->value('name');
        return $this->sendSuccess([
            'data'     => $data,
            'bookNmae' => $book_name
        ]);
    }

    /**
     * 文章详情
     * @param int $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function appInfo($id = 0)
    {
        $previous_id = '';
        $next_id     = '';
        $info        = model('Chapter')->where(['id' => $id])->find();
        if (empty($info)) {
            $this->redirect('/index/index/index');
        }
        model('Chapter')->where(['id' => $id])->update(['status' => 1]);
        $map      = [
            'book_id' => $info['book_id'],
        ];
        $bookType = model('Book')->where('id', $info['book_id'])->value('host_type');
        switch ($bookType) {
            case 'cn3k5':
            case 'fenghuo':
                $previous_id = model('Chapter')->where("SUBSTRING_INDEX(url,'-',-1) + 0 < " . explode('-', $info['url'])[2])->where($map)->order("SUBSTRING_INDEX(url,'-',-1) + 0 DESC")->value('id');
                $next_id     = model('Chapter')->where("SUBSTRING_INDEX(url,'-',-1) + 0 > " . explode('-', $info['url'])[2])->where($map)->order("SUBSTRING_INDEX(url,'-',-1) + 0 ASC")->value('id');
                break;
            case 'xbiquge':
            case 'biquku':
                $previous_id = model('Chapter')->where('id', '<', $info['id'])->where($map)->order("id DESC")->value('id');
                $next_id     = model('Chapter')->where('id', '>', $info['id'])->where($map)->order("id ASC")->value('id');
                break;
        }

        return $this->sendSuccess([
            'info'       => $info,
            'previousId' => $previous_id,
            'nextId'     => $next_id,
        ]);
    }
}
