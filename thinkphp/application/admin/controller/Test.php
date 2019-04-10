<?php


namespace app\admin\controller;


use think\Controller;


class Test extends  Controller
{

    public function index()
    {

        ini_set('display_errors', 'on');


        import('phpqrcode/phpqrcode',EXTEND_PATH);

        $data = 'http://www.baidu.com'; // data
        $ecc = 'H'; // L-smallest, M, Q, H-best
        $size = 10; // 1-50

        $filename = 'static/qrcode_'.time().'.png';
        \QRcode::png($data, $filename, $ecc, $size, 2);
        echo '<img src="'.'/static/'.basename($filename).'" />';

    }

}