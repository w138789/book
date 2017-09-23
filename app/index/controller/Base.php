<?php

namespace app\index\controller;

use think\Controller;
use think\Request;

class Base  extends Controller{

    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->assign('title','飞剑问道');
    }

    public function getBook() {
        $url = 'http://www.booktxt.net/6_6454/';
        //Header("Location: $url");exit;
        $site = 'http://www.booktxt.net';
        $data = $this->httpRequest($url);
        $data = (iconv("GBK", "UTF-8", $data));
        //echo $data; exit;
        preg_match_all("/[\/]{1}[\d]+[_]{1}[\d]+[\/]{1}[\d]+\.html/", $data, $array);
        //print_r($array); exit;
        $arr   = $array[0];
        $arrs  = model('Book')->select();
        $arrss = [];
        if ($arrs) {
            foreach ($arrs as $k => $v) {
                $arrss[$k] = $v['url'];
            }
        }
        //print_r($arr);
        //print_r($arrss);
        $aa = array_diff($arr, $arrss);
        $aas = array_unique($aa);
        //print_r($aas);
        //exit;
        krsort($aas);
        $datas = [];
        $ks    = 0;
        //print_r($array[0]); exit;
        foreach ($aas as $k => $v) {
            $data              = $site . $v;
            $str               = $this->httpRequest($data);
            $str               = iconv("GBK", "UTF-8", $str);
            $datas[$ks]['url'] = $v;
            $datas[$ks]['str'] = $str;
            $ks++;

        }
        return $datas;
        //return $data;
    }

    public function getHtml() {
        //$s = $this->httpRequest('http://www.booktxt.net/6_6454/2547413.html');
        $data = $this->getBook();
        //print_r($data); exit;
        foreach ($data as $k => $v) {
            $p                = $this->str_get_html($v['str']);
            $datas[$k]['url'] = $v['url'];
            $aa               = $p->find('.bookname@h1');

            //print_r($r); exit;
            $datas[$k]['title'] = $aa[0]->text();

            $bb = $p->find('#content');

            //print_r($r); exit;
            $txt = str_replace("&nbsp;&nbsp;&nbsp;&nbsp;", "<br>", $bb[0]->text());
            //$datas[$k]['value'] = '<html><body><div><h1>' . $datas[$k]['title'] . '</h1>' . $txt . '</div></body></html>';
            $datas[$k]['value'] = $txt;
        }
        //print_r($datas); exit;
        if (!empty($datas)) {
            foreach ($datas as $k => $v) {
                //echo $v['url'] . $v['title'];
                db('book')->insert($v);
            }
        }
        //return $datas;
    }

    /**
     * curl get 或 pust
     * @param $url
     * @param null $data
     * @return mixed
     */
    public function httpRequest($url, $data = null) {
        $curl        = curl_init();
        $this_header = array("Content-Type:text/html;charset=utf-8");
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
    function str_get_html($str, $lowercase = true, $forceTagsClosed = true, $target_charset = 'UTF-8', $stripRN = true, $defaultBRText = "\r\n", $defaultSpanText = " ") {
        $dom = new \simple_html_dom(null, $lowercase, $forceTagsClosed, $target_charset, $stripRN, $defaultBRText, $defaultSpanText);
        if (empty($str) || strlen($str) > 600000) {
            $dom->clear();
            return false;
        }
        $dom->load($str, $lowercase, $stripRN);
        return $dom;
    }
}

//$book = new GetBooks();
//$request = $book->getBook();
//echo $request;
//$request = $book->getHtml();
//echo(iconv("GBK", "UTF-8", $request));
//echo $request;
//$sqlstr = 'select * from book';
//$str = $book->mysqlSelect($sqlstr);
//$str = $book->mysqlInsert();
//print_r($request);
