<?php

namespace app\index\controller;

use DOMDocument;
use think\Controller;
use think\Request;

class Base extends Controller
{

    public function __construct(Request $request = null)
    {
        ini_set('max_execution_time', 0);   //永不超时
        parent::__construct($request);
        $this->assign('title', 'su');
    }

    /**
     * 拉www.booktxt.net小说
     */
    public function getBookTxtHtml()
    {
        $urls = model('Book')->where(['host_type' => 'booktxt'])->select()->toArray();
        foreach ($urls as $k => $vs) {
            $site = 'http://www.booktxt.net';
            //$proxy = 'http://163.125.148.103:9797';
            $proxy = '';
            $data  = $this->httpRequest($vs['url'], '', $proxy);
            $data  = (iconv("GBK", "UTF-8", $data));
            preg_match_all("/[\/]{1}[\d]+[_]{1}[\d]+[\/]{1}[\d]+\.html/", $data, $array);
            $arr = $array[0];
            if (!empty($arr)) {
                foreach ($arr as $k => $v) {
                    $id = model('Chapter')->where(['url' => $v])->value('id');
                    if (!empty($id)) continue;
                    $data         = $site . $v;
                    $str          = $this->httpRequest($data, '', $proxy);
                    $str          = iconv("GBK", "UTF-8", $str);
                    $p            = $this->str_get_booktxt_html($str);
                    $datas['url'] = $v;
                    $aa           = $p->find('.bookname@h1');
                    echo $datas['title'] = $aa[0]->text();
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
    public function getFenghuoHtml()
    {
        $urls = model('Book')->where(['host_type' => 'fenghuo'])->select();
        $site = 'fenghuo123.com/';
        foreach ($urls as $k => $vs) {
            $proxy = '';
            $data  = $this->httpRequest($vs['url'], '', $proxy);
            //$data = (iconv("GBK", "UTF-8", $data));
            //$data = '<a href="read_sql.asp?cid=19903628&aid=44102&pno=0">第785章  棺材板压不住了</a><a href="read_sql.asp?cid=19900617&aid=44102&pno=0">第784章  你们敬爱的楚大爷</a>';
            //$data = 'CPU Load 33333';
            //preg_match('/[read_sql].*pno=0/',$data, $array);
            //preg_match('/[read_sql.asp?cid=]+[\d]+[&aid=]+[\d]+&pno=0/',$data, $array);
            preg_match_all('/[read_sql.asp?cid=]+[\d]+\&+[amp;aid=]+[\d]+\&amp;qid=\&amp;pno=/', $data, $array);
            $arr  = $array[0];
            $arrs = array_reverse($arr);
            if (!empty($arrs)) {
                foreach ($arrs as $k => $v) {
                    $v  = str_replace("&amp;", "&", $v);
                    $id = model('Chapter')->where(['url' => $v])->value('id');
                    if (!empty($id)) continue;
                    $data = $site . $v;
                    $str  = $this->httpRequest($data, '', $proxy);
                    preg_match_all('/[^加书签].*/', $str, $d);
                    $datas['book_id'] = $vs['id'];
                    $datas['url']     = $v;
                    $string           = '';
                    foreach ($d[0] as $kk => $vv) {
                        $length = mb_strlen($vv, 'UTF-8');
                        if ($length > 1000) {
                            $string = $vv;
                            break;
                        }
                    }
                    if ($string) {
                        $arrays = explode('&nbsp;', $string);
                        echo $datas['title'] = $arrays[0];
                        if (!empty($arrays[5])) {
                            $datas['value'] = $arrays[5];
                            db('chapter')->insert($datas);
                        }
                    }
                    sleep(300);
                }
            }
        }
    }

    /**
     * 拉m.cn3k5.com三千五中文网
     */
    public function getCn3k5Html()
    {
        $urls = model('Book')->where(['host_type' => 'cn3k5'])->select();
        $site = 'https://m.cn3k5.com/';
        foreach ($urls as $k => $vs) {
            $proxy = '';
            $data  = $this->httpRequest($vs['url']);
            $data = (iconv("GBK", "UTF-8", $data));
            //$data = '<a href="read_sql.asp?cid=19903628&aid=44102&pno=0">第785章  棺材板压不住了</a><a href="read_sql.asp?cid=19900617&aid=44102&pno=0">第784章  你们敬爱的楚大爷</a>';
            //$data = 'CPU Load 33333';
            //preg_match('/[read_sql].*pno=0/',$data, $array);
            //preg_match('/[read_sql.asp?cid=]+[\d]+[&aid=]+[\d]+&pno=0/',$data, $array);
            //wapbook-76735-32139270
            preg_match_all('/[wapbook]+\-[\d]+\-+[\d]{5,15}/', $data, $array);
            $arr  = $array[0];
            $arrs = array_reverse($arr);
            if (!empty($arrs)) {
                foreach ($arrs as $k => $v) {
                    $v  = str_replace("&amp;", "&", $v);
                    $id = model('Chapter')->where(['url' => $v])->value('id');
                    if (!empty($id)) continue;
                    $data = $site . $v . '/';
                    //                    $str  = $this->httpRequest($data, '', $proxy);
                    //                    $str  = (iconv("GBK", "UTF-8", $str));
                    //                    preg_match_all('/[^你是天才].*/', $str, $d);
                    //                    $datas['book_id'] = $vs['id'];
                    //                    $datas['url']     = $v;
                    //                    $string           = '';
                    //                    foreach ($d[0] as $kk => $vv) {
                    //                        preg_match_all('/[^\<div\ class="nr_title" id="nr_title">
                    //].*/', $vv, $dss);
                    //                        if ($kk >= 30 && $kk <= 40 && mb_strlen($dss[0][0]) >= 10 && mb_strlen($dss[0][0]) < 30) {
                    //                            echo $datas['title'] = $vv;
                    //                        }
                    //                        $length = mb_strlen($vv);
                    //                        if ($length > 1000) {
                    //                            $string = $vv;
                    //                            break;
                    //                        }
                    //                    }
                    $datas['book_id'] = $vs['id'];
                    $datas['url']     = $v;
                    //建立Dom对象，分析HTML文件；
                    libxml_use_internal_errors(true);
                    $str    = $this->httpRequest($data);
                    $htmDoc = new DOMDocument();
                    $htmDoc->loadHTML(mb_convert_encoding($str, 'HTML-ENTITIES', 'GBK'));
                    //获得到此文档中每一个Table对象；
                    $title = $htmDoc->getElementById('nr_title');
                    $text  = $htmDoc->getElementById('nr1');
                    if ($title->textContent && $text->textContent) {
                        echo $datas['title'] = $title->textContent;
                        $datas['value'] = str_replace('你是天才，一秒记住：三千五中文网，网址:m.cn3k5.com', '', $text->textContent);
                        $datas['value'] = str_replace("\r\n", "<br>", $datas['value']);
                        $datas['value'] = str_replace(chr(194) . chr(160).chr(194) . chr(160), "<br>", $datas['value']);  // 解决方法
                        db('chapter')->insert($datas);
                    }
                    sleep(300);
                    //exit;
                }
            }
        }
    }

    /**
     * curl get 或 pust
     * @param $url
     * @param string $data
     * @return mixed
     */
    public function httpRequest($url, $data = '')
    {
        $ua = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_HEADER, true);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $ua);
        curl_setopt($ch, CURLOPT_COOKIE, 'NID=67=pdjIQN5CUKVn0bRgAlqitBk7WHVivLsbLcr7QOWMn35Pq03N1WMy6kxYBPORtaQUPQrfMK4Yo0vVz8tH97ejX3q7P2lNuPjTOhwqaI2bXCgPGSDKkdFoiYIqXubR0cTJ48hIAaKQqiQi_lpoe6edhMglvOO9ynw; PREF=ID=52aa671013493765:U=0cfb5c96530d04e3:FF=0:LD=en:TM=1370266105:LM=1370341612:GM=1:S=Kcc6KUnZwWfy3cOl; OTZ=1800625_34_34__34_; S=talkgadget=38GaRzFbruDPtFjrghEtRw; SID=DQAAALoAAADHyIbtG3J_u2hwNi4N6UQWgXlwOAQL58VRB_0xQYbDiL2HA5zvefboor5YVmHc8Zt5lcA0LCd2Riv4WsW53ZbNCv8Qu_THhIvtRgdEZfgk26LrKmObye1wU62jESQoNdbapFAfEH_IGHSIA0ZKsZrHiWLGVpujKyUvHHGsZc_XZm4Z4tb2bbYWWYAv02mw2njnf4jiKP2QTxnlnKFK77UvWn4FFcahe-XTk8Jlqblu66AlkTGMZpU0BDlYMValdnU; HSID=A6VT_ZJ0ZSm8NTdFf; SSID=A9_PWUXbZLazoEskE; APISID=RSS_BK5QSEmzBxlS/ApSt2fMy1g36vrYvk; SAPISID=ZIMOP9lJ_E8SLdkL/A32W20hPpwgd5Kg1J');

        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 20);
        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        $result = curl_exec($ch);
        //$last   = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);
        return $result;
    }

    // get html dom from string
    function str_get_booktxt_html($str, $lowercase = true, $forceTagsClosed = true, $target_charset = 'UTF-8', $stripRN = true, $defaultBRText = "\r\n", $defaultSpanText = " ")
    {
        $dom = new \simple_html_dom(null, $lowercase, $forceTagsClosed, $target_charset, $stripRN, $defaultBRText, $defaultSpanText);
        if (empty($str) || strlen($str) > 600000) {
            $dom->clear();
            return false;
        }
        $dom->load($str, $lowercase, $stripRN);
        return $dom;
    }

    public function str_get_fenghuo_html($str, $lowercase = true, $forceTagsClosed = true, $target_charset = 'UTF-8', $stripRN = true, $defaultBRText = "\r\n", $defaultSpanText = " ")
    {
        $dom = new \simple_html_dom(null, $lowercase, $forceTagsClosed, $target_charset, $stripRN, $defaultBRText, $defaultSpanText);
        if (empty($str) || strlen($str) > 600000) {
            $dom->clear();
            return false;
        }
        $dom->load($str, $lowercase, $stripRN);
        return $dom;
    }
}