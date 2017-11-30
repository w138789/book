<?php

namespace app\index\controller;

use think\Controller;
use think\Request;

class Base extends Controller {

    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->assign('title', 'su');
    }

    /**
     * 拉www.booktxt.net小说
     */
    public function getBookTxtHtml() {
        $urls = model('Book')->where(['host_type'=>'booktxt'])->select()->toArray();
        foreach ($urls as $k => $vs) {
            $site = 'http://www.booktxt.net';
            //$proxy = 'http://163.125.148.103:9797';
            $proxy = '';
            $data = $this->httpRequest($vs['url'], '',$proxy);
            $data = (iconv("GBK", "UTF-8", $data));
            preg_match_all("/[\/]{1}[\d]+[_]{1}[\d]+[\/]{1}[\d]+\.html/", $data, $array);
            $arr = $array[0];
            if (!empty($arr)) {
                    foreach ($arr as $k => $v) {
                        $id = model('Chapter')->where(['url' => $v])->value('id');
                        if(!empty($id)) continue;
                        $data             = $site . $v;
                        $str              = $this->httpRequest($data, '',$proxy);
                        $str              = iconv("GBK", "UTF-8", $str);
                        $p                = $this->str_get_booktxt_html($str);
                        $datas['url']     = $v;
                        $aa               = $p->find('.bookname@h1');
                        echo $datas['title']   = $aa[0]->text();
                        $datas['book_id'] = $vs['id'];
                        $bb               = $p->find('#content');
                        $txt              = str_replace("&nbsp;&nbsp;&nbsp;&nbsp;", "<br>", $bb[0]->text());
                        $datas['value']   = $txt;
                        db('chapter')->insert($datas);
                    }
            }
        }
    }

    /**
     * 拉fenghuo123.com烽火中文网小说
     */
    public function getFenghuoHtml() {
        $urls = model('Book')->where(['host_type'=>'fenghuo'])->select()->toArray();
        $site = 'fenghuo123.com/';
        foreach ($urls as $k => $vs) {
            $proxy = '';
            $data = $this->httpRequest($vs['url'], '',$proxy);
            //$data = (iconv("GBK", "UTF-8", $data));
            //$data = '<a href="read_sql.asp?cid=19903628&aid=44102&pno=0">第785章  棺材板压不住了</a><a href="read_sql.asp?cid=19900617&aid=44102&pno=0">第784章  你们敬爱的楚大爷</a>';
            //$data = 'CPU Load 33333';
            //preg_match('/[read_sql].*pno=0/',$data, $array);
            //preg_match('/[read_sql.asp?cid=]+[\d]+[&aid=]+[\d]+&pno=0/',$data, $array);
            preg_match_all('/[read_sql.asp?cid=]+[\d]+\&+[aid=]+[\d]+\&pno=0/',$data, $array);
            $arr = $array[0];
            $arrs =array_reverse($arr);
            if (!empty($arrs)) {
                foreach ($arrs as $k => $v) {
                    $id = model('Chapter')->where(['url' => $v])->value('id');
                    if(!empty($id)) continue;
                    $data             = $site . $v;
                    $str              = $this->httpRequest($data, '',$proxy);
                    preg_match_all('/[^加书签].*/',$str,$d);
                    $datas['book_id'] = $vs['id'];
                    $datas['url'] = $v;
                    $string = $d[0][34];
                    $arrays = explode('&nbsp;',$string);
                    echo $datas['title'] = $arrays[0];
                    $datas['value'] = $arrays[5];
                    db('chapter')->insert($datas);
                }
            }
        }
    }

    /**
     * curl get 或 pust
     * @param $url
     * @param null $data
     * @return mixed
     */
    public function httpRequest($url, $data = null,$proxy) {
        $curl        = curl_init();
        $this_header = array("Content-Type:text/html;charset=utf-8");
        if(!empty($proxy)){
            curl_setopt ($curl, CURLOPT_PROXY, $proxy);
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this_header);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    // get html dom from string
    function str_get_booktxt_html($str, $lowercase = true, $forceTagsClosed = true, $target_charset = 'UTF-8', $stripRN = true, $defaultBRText = "\r\n", $defaultSpanText = " ") {
        $dom = new \simple_html_dom(null, $lowercase, $forceTagsClosed, $target_charset, $stripRN, $defaultBRText, $defaultSpanText);
        if (empty($str) || strlen($str) > 600000) {
            $dom->clear();
            return false;
        }
        $dom->load($str, $lowercase, $stripRN);
        return $dom;
    }

    public function str_get_fenghuo_html($str, $lowercase = true, $forceTagsClosed = true, $target_charset = 'UTF-8', $stripRN = true, $defaultBRText = "\r\n", $defaultSpanText = " ") {
        $dom = new \simple_html_dom(null, $lowercase, $forceTagsClosed, $target_charset, $stripRN, $defaultBRText, $defaultSpanText);
        if (empty($str) || strlen($str) > 600000) {
            $dom->clear();
            return false;
        }
        $dom->load($str, $lowercase, $stripRN);
        return $dom;
    }
}
