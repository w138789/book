<?php

namespace app\index\controller;

use think\Controller;
use think\Request;

class Base extends Controller {

    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->assign('title', '王林');
    }

    public function getHtml() {
        $urls = model('Book')->select()->toArray();
        foreach ($urls as $k => $vs) {
            $site = 'http://www.booktxt.net';
            $proxy = 'http://163.125.148.103:9797';
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
                        $p                = $this->str_get_html($str);
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
