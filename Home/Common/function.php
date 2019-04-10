<?php
/*
 *	生成sidebar
 *	@param (array)perm 树表
 *
 *	@echo string
 */

    

class Ucpaas
{

    /**
     *  云之讯REST API版本号。当前版本号为：2014-06-30
     */
    const SoftVersion = "2014-06-30";
    /**
     * API请求地址
     */
    const BaseUrl = "https://api.ucpaas.com/";
    /**
     * @var string
     * 开发者账号ID。由32个英文字母和阿拉伯数字组成的开发者账号唯一标识符。
     */
    private $accountSid;
    /**
     * @var string
     * 开发者账号TOKEN
     */
    private $token;
    /**
     * @var string
     * 时间戳
     */
    private $timestamp;


    /**
     * @param $options 数组参数必填
     * $options = array(
     *
     * )
     * @throws Exception
     */
    public function  __construct($options)
    {
        if (is_array($options) && !empty($options)) {
            $this->accountSid = isset($options['accountsid']) ? $options['accountsid'] : '';
            $this->token = isset($options['token']) ? $options['token'] : '';
            $this->timestamp = date("YmdHis") + 7200;
        } else {
            throw new Exception("非法参数");
        }
    }

    /**
     * @return string
     * 包头验证信息,使用Base64编码（账户Id:时间戳）
     */
    private function getAuthorization()
    {
        $data = $this->accountSid . ":" . $this->timestamp;
        return trim(base64_encode($data));
    }

    /**
     * @return string
     * 验证参数,URL后必须带有sig参数，sig= MD5（账户Id + 账户授权令牌 + 时间戳，共32位）(注:转成大写)
     */
    private function getSigParameter()
    {
        $sig = $this->accountSid . $this->token . $this->timestamp;
        return strtoupper(md5($sig));
    }

    /**
     * @param $url
     * @param string $type
     * @return mixed|string
     */
    private function getResult($url, $body = null, $type = 'json',$method)
    {
        $data = $this->connection($url,$body,$type,$method);
        if (isset($data) && !empty($data)) {
            $result = $data;
        } else {
            $result = '没有返回数据';
        }
        return $result;
    }

    /**
     * @param $url
     * @param $type
     * @param $body  post数据
     * @param $method post或get
     * @return mixed|string
     */
    private function connection($url, $body, $type,$method)
    {
        if ($type == 'json') {
            $mine = 'application/json';
        } else {
            $mine = 'application/xml';
        }
        if (function_exists("curl_init")) {
            $header = array(
                'Accept:' . $mine,
                'Content-Type:' . $mine . ';charset=utf-8',
                'Authorization:' . $this->getAuthorization(),
            );
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            if($method == 'post'){
                curl_setopt($ch,CURLOPT_POST,1);
                curl_setopt($ch,CURLOPT_POSTFIELDS,$body);
            }
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $result = curl_exec($ch);
            curl_close($ch);
        } else {
            $opts = array();
            $opts['http'] = array();
            $headers = array(
                "method" => strtoupper($method),
            );
            $headers[]= 'Accept:'.$mine;
            $headers['header'] = array();
            $headers['header'][] = "Authorization: ".$this->getAuthorization();
            $headers['header'][]= 'Content-Type:'.$mine.';charset=utf-8';

            if(!empty($body)) {
                $headers['header'][]= 'Content-Length:'.strlen($body);
                $headers['content']= $body;
            }

            $opts['http'] = $headers;
            $result = file_get_contents($url, false, stream_context_create($opts));
        }
        return $result;
    }

    /**
     * @param string $type 默认json,也可指定xml,否则抛出异常
     * @return mixed|string 返回指定$type格式的数据
     * @throws Exception
     */
    public function getDevinfo($type = 'json')
    {
        if ($type == 'json') {
            $type = 'json';
        } elseif ($type == 'xml') {
            $type = 'xml';
        } else {
            throw new Exception("只能json或xml，默认为json");
        }
        $url = self::BaseUrl . self::SoftVersion . '/Accounts/' . $this->accountSid . '?sig=' . $this->getSigParameter();
        $data = $this->getResult($url,null,$type,'get');
        return $data;
    }


    /**
     * @param $appId 应用ID
     * @param $clientType 计费方式。0  开发者计费；1 云平台计费。默认为0.
     * @param $charge 充值的金额
     * @param $friendlyName 昵称
     * @param $mobile 手机号码
     * @return json/xml
     */
    public function applyClient($appId, $clientType, $charge, $friendlyName, $mobile, $type = 'json')
    {
        $url = self::BaseUrl . self::SoftVersion . '/Accounts/' . $this->accountSid . '/Clients?sig=' . $this->getSigParameter();
        if ($type == 'json') {
            $body_json = array();
            $body_json['client'] = array();
            $body_json['client']['appId'] = $appId;
            $body_json['client']['clientType'] = $clientType;
            $body_json['client']['charge'] = $charge;
            $body_json['client']['friendlyName'] = $friendlyName;
            $body_json['client']['mobile'] = $mobile;
            $body = json_encode($body_json);
        } elseif ($type == 'xml') {
            $body_xml = '<?xml version="1.0" encoding="utf-8"?>
                        <client><appId>'.$appId.'</appId>
                        <clientType>'.$clientType.'</clientType>
                        <charge>'.$charge.'</charge>
                        <friendlyName>'.$friendlyName.'</friendlyName>
                        <mobile>'.$mobile.'</mobile>
                        </client>';
            $body = trim($body_xml);
        } else {
            throw new Exception("只能json或xml，默认为json");
        }
        $data = $this->getResult($url, $body, $type,'post');
        return $data;
    }

    /**
     * @param $clientNumber
     * @param $appId
     * @param string $type
     * @return mixed|string
     * @throws Exception
     */
    public function releaseClient($clientNumber,$appId,$type = 'json'){
        $url = self::BaseUrl . self::SoftVersion . '/Accounts/' . $this->accountSid . '/dropClient?sig=' . $this->getSigParameter();
        if($type == 'json'){
            $body_json = array();
            $body_json['client'] = array();
            $body_json['client']['clientNumber'] = $clientNumber;
            $body_json['client']['appId'] = $appId;
            $body = json_encode($body_json);
        }elseif($type == 'xml'){
            $body_xml = '<?xml version="1.0" encoding="utf-8"?>
                        <client>
                        <clientNumber>'.$clientNumber.'</clientNumber>
                        <appId>'.$appId.'</appId >
                        </client>';
            $body = trim($body_xml);
        }else {
            throw new Exception("只能json或xml，默认为json");
        }
        $data = $this->getResult($url, $body, $type,'post');
        return $data;
    }

    /**
     * @param $appId
     * @param $start
     * @param $limit
     * @param string $type
     * @return mixed|string
     * @throws Exception
     */
    public function getClientList($appId,$start,$limit,$type = 'json'){
        $url = self::BaseUrl . self::SoftVersion . '/Accounts/' . $this->accountSid . '/clientList?sig=' . $this->getSigParameter();
        if($type == 'json'){
            $body_json = array('client'=>array(
                'appId'=>$appId,
                'start'=>$start,
                'limit'=>$limit
            ));
            $body = json_encode($body_json);
        }elseif($type == 'xml'){
            $body_xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
                        <client>
                            <appId>'.$appId.'</appId>
                            <start>'.$start.'</start>
                            <limit>'.$limit.'</limit>
                        </client>';
            $body = trim($body_xml);
        }else {
            throw new Exception("只能json或xml，默认为json");
        }
        $data = $this->getResult($url, $body, $type,'post');
        return $data;
    }

    /**
     * @param $appId
     * @param $clientNumber
     * @param string $type
     * @return mixed|string
     * @throws Exception
     */
    public function getClientInfo($appId,$clientNumber,$type = 'json'){
        if ($type == 'json') {
            $type = 'json';
        } elseif ($type == 'xml') {
            $type = 'xml';
        } else {
            throw new Exception("只能json或xml，默认为json");
        }
        $url = self::BaseUrl . self::SoftVersion . '/Accounts/' . $this->accountSid . '?sig=' . $this->getSigParameter(). '&clientNumber='.$clientNumber.'&appId='.$appId;
        $data = $this->getResult($url,null,$type,'get');
        return $data;
    }

    /**
     * @param $appId
     * @param $mobile
     * @param string $type
     * @return mixed|string
     * @throws Exception
     */
    public function getClientInfoByMobile($appId,$mobile,$type = 'json'){
        if ($type == 'json') {
            $type = 'json';
        } elseif ($type == 'xml') {
            $type = 'xml';
        } else {
            throw new Exception("只能json或xml，默认为json");
        }
        $url = self::BaseUrl . self::SoftVersion . '/Accounts/' . $this->accountSid . '/ClientsByMobile?sig=' . $this->getSigParameter(). '&mobile='.$mobile.'&appId='.$appId;
        $data = $this->getResult($url,null,$type,'get');
        return $data;
    }

    /**
     * @param $appId
     * @param $date
     * @param string $type
     * @return mixed|string
     * @throws Exception
     */
    public function getBillList($appId,$date,$type = 'json'){
        $url = self::BaseUrl . self::SoftVersion . '/Accounts/' . $this->accountSid . '/billList?sig=' . $this->getSigParameter();
        if($type == 'json'){
            $body_json = array('appBill'=>array(
                'appId'=>$appId,
                'date'=>$date,
            ));
            $body = json_encode($body_json);
        }elseif($type == 'xml'){
            $body_xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
                        <appBill>
                            <appId>'.$appId.'</appId>
                            <date>'.$date.'</date>
                        </appBill>';
            $body = trim($body_xml);
        }else {
            throw new Exception("只能json或xml，默认为json");
        }
        $data = $this->getResult($url, $body, $type,'post');
        return $data;
    }

    /**
     * @param $appId
     * @param $clientNumber
     * @param $chargeType
     * @param $charge
     * @param string $type
     * @return mixed|string
     * @throws Exception
     */
    public function chargeClient($appId,$clientNumber,$chargeType,$charge,$type = 'json'){
        $url = self::BaseUrl . self::SoftVersion . '/Accounts/' . $this->accountSid . '/chargeClient?sig=' . $this->getSigParameter();
        if($type == 'json'){
            $body_json = array('client'=>array(
                'appId'=>$appId,
                'clientNumber'=>$clientNumber,
                'chargeType'=>$chargeType,
                'charge'=>$charge
            ));
            $body = json_encode($body_json);
        }elseif($type == 'xml'){
            $body_xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
                        <client>
                            <clientNumber>'.$clientNumber.'</clientNumber>
                            <chargeType>'.$chargeType.'</chargeType>
                            <charge>'.$charge.'</charge>
                            <appId>'.$appId.'</appId>
                        </client>';
            $body = trim($body_xml);
        }else {
            throw new Exception("只能json或xml，默认为json");
        }
        $data = $this->getResult($url, $body, $type,'post');
        return $data;

    }

    /**
     * @param $appId
     * @param $fromClient
     * @param $to
     * @param null $fromSerNum
     * @param null $toSerNum
     * @param string $type
     * @return mixed|string
     * @throws Exception
     */
    public function callBack($appId,$fromClient,$to,$fromSerNum=null,$toSerNum=null,$type = 'json'){
        $url = self::BaseUrl . self::SoftVersion . '/Accounts/' . $this->accountSid . '/Calls/callBack?sig=' . $this->getSigParameter();
        if($type == 'json'){
            $body_json = array('callback'=>array(
                'appId'=>$appId,
                'fromClient'=>$fromClient,
                'fromSerNum'=>$fromSerNum,
                'to'=>$to,
                'toSerNum'=>$toSerNum
            ));
            $body = json_encode($body_json);
        }elseif($type == 'xml'){
            $body_xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
                        <callback>
                            <fromClient>'.$fromClient.'</clientNumber>
                            <fromSerNum>'.$fromSerNum.'</chargeType>
                            <to>'.$to.'</charge>
                            <toSerNum>'.$toSerNum.'</toSerNum>
                            <appId>'.$appId.'</appId>
                        </callback>';
            $body = trim($body_xml);
        }else {
            throw new Exception("只能json或xml，默认为json");
        }
        $data = $this->getResult($url, $body, $type,'post');
        return $data;
    }

    /**
     * @param $appId
     * @param $verifyCode
     * @param $to
     * @param string $type
     * @return mixed|string
     * @throws Exception
     */
    public function voiceCode($appId,$verifyCode,$to,$type = 'json'){
        $url = self::BaseUrl . self::SoftVersion . '/Accounts/' . $this->accountSid . '/Calls/voiceCode?sig=' . $this->getSigParameter();
        if($type == 'json'){
            $body_json = array('voiceCode'=>array(
                'appId'=>$appId,
                'verifyCode'=>$verifyCode,
                'to'=>$to
            ));
            $body = json_encode($body_json);
        }elseif($type == 'xml'){
            $body_xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
                        <voiceCode>
                            <verifyCode>'.$verifyCode.'</clientNumber>
                            <to>'.$to.'</charge>
                            <appId>'.$appId.'</appId>
                        </voiceCode>';
            $body = trim($body_xml);
        }else {
            throw new Exception("只能json或xml，默认为json");
        }
        $data = $this->getResult($url, $body, $type,'post');
        return $data;
    }

    /**
     * @param $appId
     * @param $to
     * @param $templateId
     * @param null $param
     * @param string $type
     * @return mixed|string
     * @throws Exception
     */
    public function templateSMS($appId,$to,$templateId,$param=null,$type = 'json'){
        $url = self::BaseUrl . self::SoftVersion . '/Accounts/' . $this->accountSid . '/Messages/templateSMS?sig=' . $this->getSigParameter();
        if($type == 'json'){
            $body_json = array('templateSMS'=>array(
                'appId'=>$appId,
                'templateId'=>$templateId,
                'to'=>$to,
                'param'=>$param
            ));
            $body = json_encode($body_json);
        }elseif($type == 'xml'){
            $body_xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
                        <templateSMS>
                            <templateId>'.$templateId.'</templateId>
                            <to>'.$to.'</to>
                            <param>'.$param.'</param>
                            <appId>'.$appId.'</appId>
                        </templateSMS>';
            $body = trim($body_xml);
        }else {
            throw new Exception("只能json或xml，默认为json");
        }
        $data = $this->getResult($url, $body, $type,'post');
        return $data;
    }
} 


    

  		//初始化 $options必填
		function SendToTelOfAccNo($mobile,$verify)
		{	
			writelog('开始发送短信mobile='.$mobile,'yzm');
			$options['accountsid']='1109d61ece64203332d97f4afcb8a4a1';
			$options['token']='57cce11070bb044aee523cf6f1c8eefa';
			$ucpass = new Ucpaas($options);

			$appId = "a66d61485d2046f59186cac3f33d7895";
			$to = $mobile;
			$templateId = "30218";
			$param= $verify;

			writelog( $ucpass->templateSMS($appId,$to,$templateId,$param).'mobile='.$mobile,'yzm');
			writelog('发送短信完成mobile='.$mobile.' verify='.$verify,'yzm');
		}







function getRadomFileName() 
{
   $filename = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8); 
   return $filename;
} 

function get_sidebar($perm){
	$html = '<li';
	if(CONTROLLER_NAME.'/'.ACTION_NAME == 'Index/index'){
		$html .= ' class="active"';
	}
	$html .= '><a href="'.U('Index/index').'"><i class="fa fa-dashboard fa-lg"></i><span class="menu-text">控制台</span></a></li>';
	foreach($perm as $value_1){
		if($value_1['is_show'] == 1){
			switch ($value_1['perm_current']) {
				case 'Bar':
					$icon = 'fa fa-list fa-lg';
					break;
				case 'Account':
					$icon = 'fa fa-folder-open fa-lg';
					break;
			    case 'Business':
					$icon = 'fa fa-desktop fa-lg';
					break;
				case 'Pay':
					$icon = 'fa fa-gamepad fa-lg';
					break;
				case 'Cash':
					$icon = 'fa fa-comments fa-lg';
					break;
				case 'Goods':
					$icon = 'fa fa-bar-chart fa-lg';
					break;
				case 'User':
					$icon = 'fa fa-user fa-lg';
					break;
				default:
					$icon = 'fa fa-tag fa-lg';
			}

			$html .= '<li';
			if($value_1['perm_current'] == CONTROLLER_NAME){
				$html .= ' class="active open"';
			}
			$html .= '><a href="#" class="dropdown-toggle"><i class="'.$icon.'"></i><span class="menu-text">'.$value_1['perm_name'].'</span><b class="arrow fa fa-angle-down fa-lg"></b></a><ul class="submenu">';
			foreach ($value_1['_'] as $value_2){
				if($value_2['is_show'] == 1){
					$html .= '<li';
					if($value_2['perm_current'] == strtolower(CONTROLLER_NAME).'_'.ACTION_NAME){
						$html .= ' class="active open"';
					}
					$html .= '><a href="'.U($value_2['perm_value']).'"><i class="fa fa-angle-double-right"></i>'.$value_2['perm_name'].'</a></li>';
				}
			}
			$html .= '</ul></li>';
		}
	}
	echo $html;
}
/*
 *	生成breadcrumbs
 *	@param (array)perm 树表
 *
 *	@echo string
 */
function get_breadcrumbs($perm){
	$bdc = array();
	for($i=0;$i<count($perm);$i++){
		if($perm[$i]['perm_current'] == CONTROLLER_NAME){
			$bdc[] =& $perm[$i];
			if(isset($perm[$i]['_'])){
				for($ii=0;$ii<count($perm[$i]['_']);$ii++){
					if($perm[$i]['_'][$ii]['perm_current'] == strtolower(CONTROLLER_NAME).'_'.ACTION_NAME){
						$bdc[] =& $perm[$i]['_'][$ii];
					}
				}
			}
		}
	}
	$html = '';
	for($i=0;$i<count($bdc);$i++){
		if($i == count($bdc)-1){
			$html .= '<li class="active">'.$bdc[$i]['perm_name'].'</li>';
		}else{
			$html .= '<li><a href="'.U($bdc[$i]['perm_value']).'">'.$bdc[$i]['perm_name'].'</a></li>';
		}
	}
	echo $html;
}
/*
 *	生成page_header
 *	@param (array)perm 树表
 *
 *	@echo string
 */
function get_page_header($perm){
	$ph = array();
	for($i=0;$i<count($perm);$i++){
		if($perm[$i]['perm_current'] == CONTROLLER_NAME){
			$ph[] =& $perm[$i];
			if(isset($perm[$i]['_'])){
				for($ii=0;$ii<count($perm[$i]['_']);$ii++){
					if($perm[$i]['_'][$ii]['perm_current'] == strtolower(CONTROLLER_NAME).'_'.ACTION_NAME){
						$ph[] =& $perm[$i]['_'][$ii];
					}
				}
			}
		}
	}
	$html = '';
	if(count($ph) == 0){
		$html .= '<h1>控制台<small><i class="fa fa-angle-double-right fa-lg"></i></small></h1>';
	}else{
		$html .= '<h1>'.$ph[0]['perm_name'].'<small>';
		for($i=1;$i<count($ph);$i++){
			$html .= '<i class="fa fa-angle-double-right fa-lg"></i> '.$ph[$i]['perm_name'];
		}
		if(strtolower(CONTROLLER_NAME) == 'bar'){			//网吧详情
			$id = I('get.id');
			if(!empty($id)){
				$html .= '<i class="fa fa-angle-double-right fa-lg"></i> '.D('Bar')->getBarNameById($id);
			}
		}elseif(strtolower(CONTROLLER_NAME.'/'.ACTION_NAME) == 'game/edit'){//游戏详情
			$id = I('get.id');
			if(!empty($id)){
				$html .= '<i class="fa fa-angle-double-right fa-lg"></i> '.D('Game')->getGameNameById($id);
			}
		}
		$html .= '</small></h1>';
	}
	echo $html;
}
/*
 *	格式化大小
 *	@param (int)bytesize 大小(byte)
 *
 *	@return string
 */
function size_format($bytesize){
	$i = 0;
	while($bytesize >= 1024){
		$bytesize = $bytesize / 1024;
		$i++;
		if($i == 4)
			break;
	}
	$units = array('Bytes','KB','MB','GB','TB');
	$newsize = round($bytesize,2);
	return $newsize . $units[$i];
}
/*
 *	生成分页pagination
 *	@param (int)count 总数
 *	@param (int)page 当前页数
 *	@param (int)rows=20 每页显示数
 *
 *	@echo string
 */
function get_pagination($count){
	$page = I('get.page',1);
	$rows = I('get.rows',20);
	$pages = ceil($count / $rows);
	$pages = $pages == 0 ? 1 : $pages;
	$param = I('get.');
	$html = '<ul class="pagination">';
	//前一页
	if($page == 1){
		$html .= '<li class="disabled"><a href="javascript:void(0);"><i class="fa fa-angle-double-left"></i></a></li>';
	}else{
		//首页
		$param['page'] = 1;
		$html .= '<li><a href="'.U('#').format_pagination_param($param).'">首页</a></li>';
		$param['page'] = $page - 1;
		$html .= '<li><a href="'.U('#').format_pagination_param($param).'"><i class="fa fa-angle-double-left"></i></a></li>';
	}
	for($i=max(1,$page-4);$i<=min($pages,$page+4);$i++){
		if($i == $page){
			$html .= '<li class="active"><a href="javascript:void(0);">'.$i.'</a></li>';
		}else{
			$param['page'] = $i;
			$html .= '<li><a href="'.U('#').format_pagination_param($param).'">'.$i.'</a></li>';
		}
	}
	//后一页
	if($page == $pages){
		$html .= '<li class="disabled"><a href="javascript:void(0);"><i class="fa fa-angle-double-right"></i></a></li>';
	}else{
		$param['page'] = $page + 1;
		$html .= '<li><a href="'.U('#').format_pagination_param($param).'"><i class="fa fa-angle-double-right"></i></a></li>';
		//最后一页
		$param['page'] = $pages;
		$html .= '<li><a href="'.U('#').format_pagination_param($param).'">最后一页</a></li>';
	}
	$html .= '</ul>';
	echo $html;
}
function format_pagination_param($param){
	$str = '?';
	foreach ($param as $key => $value) {
		$str .= $key.'='.$value.'&';
	}
	return rtrim($str,'&');
}
//清空文件夹
function empty_dir($dir){
	if(substr($dir, -1) == '/'){
		$dir = rtrim($dir,'/');
	}
	$dh = opendir($dir);
	while(($file = readdir($dh)) !== false){
		if($file != '.' && $file != '..'){
			$fullpath = $dir . '/' . $file;
			if(is_dir($fullpath)){
				delete_dir($fullpath);
			}else{
				unlink($fullpath);
			}
		}
	}
	closedir($dh);
	if(count(scandir($dir)) == 2){
		return true;
	}else{
		return false;
	}
}
//删除文件夹
function delete_dir($dir){
	//清空当前文件夹
	empty_dir($dir);
	//删除当前文件夹
	if(rmdir($dir)){
		return true;
	}else{
		return false;
	}
}
function get_excel_file($bars,$filename){
	include dirname(__FILE__).'/../Util/PHPExcel.php';
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->getProperties()->setTitle($filename)
				->setCreator('Wangshang CO.,LTD.')
				->setLastModifiedBy('Wangshang CO.,LTD.');
	$objPHPExcel->setActiveSheetIndex(0);
	$objActSheet = $objPHPExcel->getActiveSheet();

	//设置Sheet标题
	$objActSheet->setTitle($filename);

	//设置字体
	$objActSheet->getStyle('A:I')->getFont()->setName('宋体');

	//设置列宽
	$objActSheet->getColumnDimension('A')->setWidth(52);
	$objActSheet->getColumnDimension('B')->setWidth(86);
	$objActSheet->getColumnDimension('C')->setWidth(10);
	$objActSheet->getColumnDimension('D')->setWidth(28);
	$objActSheet->getColumnDimension('E')->setWidth(19);
	$objActSheet->getColumnDimension('F')->setWidth(14);
	$objActSheet->getColumnDimension('G')->setWidth(8);
	$objActSheet->getColumnDimension('H')->setWidth(8);
	$objActSheet->getColumnDimension('I')->setWidth(24);

	//设置格式
	$objActSheet->getStyle('H')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE);

	//设置对齐
	$objActSheet->getStyle('D')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$objActSheet->getStyle('A1:I1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	//设置列标题
	$objActSheet->setCellValue('A1','网吧名称');
	$objActSheet->setCellValue('B1','地址');
	$objActSheet->setCellValue('C1','联系人');
	$objActSheet->setCellValue('D1','联系电话');
	$objActSheet->setCellValue('E1','公网IP');
	$objActSheet->setCellValue('F1','今日最大在线');
	$objActSheet->setCellValue('G1','总台数');
	$objActSheet->setCellValue('H1','在线率');
	$objActSheet->setCellValue('I1','最后验证时间');

	for($i=0;$i<count($bars);$i++){
		$col = $i + 2;
		$objActSheet->setCellValue("A$col",$bars[$i]['bar_name']);
		$objActSheet->setCellValue("B$col",D('Area')->getAreaNameById($bars[$i]['province']).'-'.D('Area')->getAreaNameById($bars[$i]['city']).'-'.D('Area')->getAreaNameById($bars[$i]['area']).'-'.$bars[$i]['addr']);
		$objActSheet->setCellValue("C$col",$bars[$i]['contact']);
		$objActSheet->setCellValue("D$col",$bars[$i]['phone']);
		$objActSheet->setCellValue("E$col",$bars[$i]['wanip']);
		$objActSheet->setCellValue("F$col",$bars[$i]['max_online']);
		$objActSheet->setCellValue("G$col",$bars[$i]['pccount']);
		$objActSheet->setCellValue("H$col",round($bars[$i]['max_online']/$bars[$i]['pccount'],2));
		$objActSheet->setCellValue("I$col",$bars[$i]['validatetime']);
	}

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
	header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
    header("Content-Type:application/force-download");
    header("Content-Type:application/vnd.ms-excel;");
    header("Content-Type:application/octet-stream");
    header("Content-Disposition:attachment;filename=$filename.xls");
    header("Content-Transfer-Encoding:binary");
	$objWriter->save('php://output');
}




 function exportExcel($expTitle,$expCellName,$expTableData)
 {
        $xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称GBK
        $fileName = $_SESSION['account'].date('_YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
        $cellNum = count($expCellName);
        $dataNum = count($expTableData);


        // vendor("PHPExcel.PHPExcel");

        // include dirname(__FILE__).'/../Util/PHPExcel.php';

        include C('EXCEL_PATH2').'PHPExcel.php';

  
        $objPHPExcel = new PHPExcel();
        $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');
        
        $objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$cellNum-1].'1');//合并单元格
       // $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle.'  Export time:'.date('Y-m-d H:i:s'));  
        for($i=0;$i<$cellNum;$i++){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'2', $expCellName[$i][1]); 
        } 
          // Miscellaneous glyphs, UTF-8   
        for($i=0;$i<$dataNum;$i++)
        {
          for($j=0;$j<$cellNum;$j++)
          {
            $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+3), $expTableData[$i][$expCellName[$j][0]]);
          }             
        }  
        
        ob_clean() ;
        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
        header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');  
        $objWriter->save('php://output'); 
        exit;   
    }


  function sendPostRequst($url, $post_data = '', $timeout = 5)
  {  
    $data_string = json_encode($post_data);
    $ch = curl_init($url);
    $headers =  array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string),      
        'User-Agent: ozilla/5.0 (X11; Linux i686) AppleWebKit/535.1 (KHTML, like Gecko) Chrome/14.0.835.186 Safari/535.1'
    );
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS,$data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch, CURLOPT_ENCODING, "gzip"); // 关键在这里
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
     
    $result = curl_exec($ch);
    return  $result;
  }


 function saveimage($keyname)
{
    $redis = new Redis();
    $redis->connect('202.102.245.106',9004);
    $redis->auth('foobared_287413288_13693009088');          
    $file_id = $redis->get($keyname); 

    if(!empty($file_id))
    {
       
        file_put_contents('D:\WWW\4.jpg', $file_id);
        return true;
    }
    else
    {   
    	
        return false;
    }   
}


// function getTimeCha($begtime,$nowtime)
// {
  
    // $startdate=$begtime;
    // $enddate=$nowtime;

    // $date=floor((strtotime($enddate)-strtotime($startdate))/86400);
    // $hour=floor((strtotime($enddate)-strtotime($startdate))%86400/3600);
    // $minute=floor((strtotime($enddate)-strtotime($startdate))%86400/60);
    // $second=floor((strtotime($enddate)-strtotime($startdate))%86400%60);


    // $cha=$date*24*60+ $hour*60+$minute;
    // return $cha; 
// }


function gethourcha($startdate,$enddate)
{
    // $startdate="2012-12-11 11:40:00";
    // $enddate  ="2012-12-12 11:45:09";

    $date     =floor((strtotime($enddate)-strtotime($startdate))/86400);
    $hour     =floor((strtotime($enddate)-strtotime($startdate))%86400/3600);

    return  $date*24+$hour;  
}



function checkSessionTimeOut() 
{
   
       if(isset($_SESSION['logintime']))
      {
        $begtime=session('logintime');
        $nowtime=date('Y-m-d h:i:s',time());
        $cha=getTimeCha($begtime,$nowtime);
       
        if($cha<=20)
        {
         
          session('logintime',$nowtime); 
        }
        else
        {   
         
         
          session('wbid',null); 
          session('logintime',null); 
          header('Location: http://www.wbzzsf.com/'); 
        } 
      }
      else
      {
       
   
        session('logintime',date('Y-m-d h:i:s',time())); 
      } 
}


function getdayjiange($startdate,$enddate)
{  
    $startdate = strtotime($startdate);//开始时间 时间戳
    $enddate   = strtotime($enddate);//结束时间 时间戳
    $cle = $enddate - $startdate; //得出时间戳差值
    $date = floor($cle/3600/24);
    $hour = floor(($cle%(3600*24))/3600);  //%取余
    return (int)(($date*24+$hour)/24);   
}


function getonehourjiange($startdate,$enddate)
{  
    $startdate = strtotime($startdate);//开始时间 时间戳
    $enddate   = strtotime($enddate);//结束时间 时间戳
    $cle = $enddate - $startdate; //得出时间戳差值
    $date = floor($cle/3600/24);
    $hour = floor(($cle%(3600*24))/3600);  //%取余
    return (int)(($date*24+$hour));   
}



function getweekjiange($startdate,$enddate) 
{ 
	//参数不能为空 
	if(!empty($startdate) && !empty($enddate)){ 

	    //先把两个日期转为时间戳 
	    $startdate=strtotime($startdate); 
	    $enddate=strtotime($enddate); 
	    //开始日期不能大于结束日期 
	    if($startdate<=$enddate)
	    { 
	        $end_date=strtotime("next monday",$enddate); 
	        if(date("w",$startdate)==1)
	        { 
	          $start_date=$startdate; 
	        }
	        else
	        { 
	          $start_date=strtotime("last monday",$startdate); 
	        } 
	        //计算时间差多少周 
	        $countweek=($end_date-$start_date)/(7*24*3600); 
	        for($i=0;$i<$countweek;$i++)
	        { 
	            $sd=date("Y-m-d",$start_date); 
	            $ed=strtotime("+ 6 days",$start_date); 
	            $eed=date("Y-m-d",$ed); 
	            $arr[]=array($sd,$eed); 
	            $start_date=strtotime("+ 1 day",$ed); 
	        } 


	        for($i=0;$i<count($arr);$i++)
	        {
	          $arr[$i][0]= date('Y-m-d 00:00:00',strtotime($arr[$i][0]));
	          $arr[$i][1]= date('Y-m-d 23:59:59',strtotime($arr[$i][1]));
	        }


	        return $arr;     
	    } 
	} 
}





function  getTodayShouru()
{
     //最后一次交班时间
         // $shiftinfo= D('Shift')->where(array('WB_ID'=>session('wbid')))->Order('id desc')->limit(1)->find();
         // $lastshifttime=date('Y-m-d H:i:s',strtotime($shiftinfo['cTime']));       
        // var_dump($lastshifttime);
        $nowtime1=date('Y-m-d H:i:s');
        $todaybegtime= date('Y-m-d 00:00:00',strtotime($nowtime1));
        $nowtime      = date('Y-m-d H:i:s');


        //获取本日开始时间
        
        //网费
        $map=array();
        $map['WB_ID']=session('wbid');
        $map['cTime']=array('BETWEEN',array($todaybegtime,$nowtime));
        $hyk_jq_money=D('Hyaddmoneymx')->where($map)->sum('je');
        $lsk_jq_money=D('Lskaddmoneymx')->where($map)->sum('je');
		
		$map=array();
        $map['WB_ID']=session('wbid');
        $map['XjTime']=array('BETWEEN',array($todaybegtime,$nowtime));      
		//$lsk_zl_money=D('Lskshangjimx')->where($map)->sum('je');
		
	    $je=D('Lskshangjimx')->where($map)->sum('je');
		$foregift=D('Lskshangjimx')->where($map)->sum('foregift');
		$qtje=D('Lskshangjimx')->where($map)->sum('qtje');
		
		$lsk_zl_money=$foregift-$je;
		
        $wf_money= $hyk_jq_money+ $lsk_jq_money-$lsk_zl_money;
		
		$lsk_money=  $lsk_jq_money-$lsk_zl_money;




        $map=array();
        $map['wb_id']=session('wbid');
        $map['cTime']=array('BETWEEN',array($todaybegtime,$nowtime));
        $zfb_money=D('ZfbAddMoneyMx')->where($map)->sum('je');

        $map=array();
        $map['wb_id']=session('wbid');
        $map['cTime']=array('BETWEEN',array($todaybegtime,$nowtime));
     
	 
        $wx_money=D('WxMx')->where($map)->sum('je');
        $sum_money= $wf_money+  $zfb_money+ $wx_money;

        if(!empty($wf_money))
        {
           $money_array['wf_money']=sprintf("%.2f", $wf_money); 
        }
        else
        {
           $money_array['wf_money']='0.00'; 
        }   

        if(!empty($hyk_jq_money))
        {
           $money_array['hyk_money']=sprintf("%.2f", $hyk_jq_money); 
        }
        else
        {
           $money_array['hyk_money']='0.00'; 
        }   


        if(!empty($lsk_money))
        {
           $money_array['lsk_money']=sprintf("%.2f", $lsk_money); 
        }
        else
        {
           $money_array['lsk_money']='0.00'; 
        }   




        
        if(!empty($wx_money))
        {
           $money_array['wx_money']=sprintf("%.2f", $wx_money); 
        }
        else
        {
           $money_array['wx_money']='0.00'; 
        }   

        
        if(!empty($zfb_money))
        {
           $money_array['zfb_money']=sprintf("%.2f", $zfb_money); 
        }
        else
        {
           $money_array['zfb_money']='0.00'; 
        }   

        if(!empty($sum_money))
        {
           $money_array['sum_money']=sprintf("%.2f", $sum_money); 
        }
        else
        {
           $money_array['sum_money']='0.00'; 
        }
       return $money_array;
}


function getDayShouru_bing()
{
             //最后一次交班时间
         // $shiftinfo= D('Shift')->where(array('WB_ID'=>session('wbid')))->Order('id desc')->limit(1)->find();
         // $lastshifttime=date('Y-m-d H:i:s',strtotime($shiftinfo['cTime']));

        $nowtime1=date('Y-m-d H:i:s');
        $todaybegtime= date('Y-m-d 00:00:00',strtotime($nowtime1));
        $nowtime      = date('Y-m-d H:i:s');
		
		
	
        //网费
        $map=array();
        $map['WB_ID']=session('wbid');
        $map['cTime']=array('BETWEEN',array($todaybegtime,$nowtime));
        $hyk_jq_money=D('Hyaddmoneymx')->where($map)->sum('je');
        $lsk_jq_money=D('Lskaddmoneymx')->where($map)->sum('je');
		
		
		
		$map=array();
        $map['WB_ID']=session('wbid');
        $map['XjTime']=array('BETWEEN',array($todaybegtime,$nowtime));      
		//$lsk_zl_money=D('Lskshangjimx')->where($map)->sum('je');	
		
	    $je=D('Lskshangjimx')->where($map)->sum('je');
		$foregift=D('Lskshangjimx')->where($map)->sum('foregift');
		$qtje=D('Lskshangjimx')->where($map)->sum('qtje');
		
		$lsk_zl_money=$foregift-$je;
		
		
		
        $wf_money= $hyk_jq_money+ $lsk_jq_money-$lsk_zl_money;
		
		
		



        $map=array();
        $map['wb_id']=session('wbid');
        $map['cTime']=array('BETWEEN',array($todaybegtime,$nowtime));
        $zfb_money=D('ZfbAddMoneyMx')->where($map)->sum('je');

        $map=array();
        $map['wb_id']=session('wbid');
        $map['cTime']=array('BETWEEN',array($todaybegtime,$nowtime));
     
	 
        $wx_money=D('WxMx')->where($map)->sum('je');
        $sum_money= $wf_money+  $zfb_money+ $wx_money;
		
		
		
		if(!empty($wf_money))
        {
           $wf_money=sprintf("%.2f", $wf_money); 
        }
        else
        {
           $wf_money='0.00'; 
        } 
		
		if(!empty($wx_money))
        {
           $wx_money=sprintf("%.2f", $wx_money); 
        }
        else
        {
           $wx_money='0.00'; 
        } 
		
		if(!empty($zfb_money))
        {
           $zfb_money=sprintf("%.2f", $zfb_money); 
        }
        else
        {
           $zfb_money='0.00'; 
        } 





   
        $moneylist=array();
        $moneylist[0]['value']= sprintf("%.2f", $wf_money);
        $moneylist[0]['name']='网费';


        $moneylist[2]['value']= sprintf("%.2f", $wx_money);
        $moneylist[2]['name']='微信收入';

        $moneylist[3]['value']= sprintf("%.2f", $zfb_money);
        $moneylist[3]['name']='支付宝收入';
		


		
		

        return $moneylist;
}


function  getNowShiftShouru()
{
     //最后一次交班时间
         $shiftinfo= D('Shift')->where(array('WB_ID'=>session('wbid')))->Order('cTime desc')->limit(1)->find();
         $lastshifttime=date('Y-m-d H:i:s',strtotime($shiftinfo['cTime']));
        
        // var_dump($lastshifttime);
        $nowtime=date('Y-m-d H:i:s');
         // var_dump($nowtime);
        //网费
        $map=array();
        $map['WB_ID']=session('wbid');
        $map['cTime']=array('BETWEEN',array($lastshifttime,$nowtime));
        $hyk_jq_money=D('Hyaddmoneymx')->where($map)->sum('je');		
        $lsk_jq_money=D('Lskaddmoneymx')->where($map)->sum('je');
		
		
		$map=array();
        $map['WB_ID']=session('wbid');
        $map['XjTime']=array('BETWEEN',array($lastshifttime,$nowtime));      
		$je=D('Lskshangjimx')->where($map)->sum('je');
		$foregift=D('Lskshangjimx')->where($map)->sum('foregift');
		$qtje=D('Lskshangjimx')->where($map)->sum('qtje');
		
		$lsk_zl_money=$foregift-$je;
   
   
		$shouru_money= $hyk_jq_money+ $lsk_jq_money-$lsk_zl_money;
		
			
		if(!empty($shouru_money))
        {
           $shouru_money=sprintf("%.2f", $shouru_money);
        }
        else
        {
          $shouru_money='0.00';
        }    
		
		if(!empty($hyk_jq_money))
        {
           $hyk_jq_money=sprintf("%.2f", $hyk_jq_money);
        }
        else
        {
          $hyk_jq_money='0.00';
        }  
		
	    if(!empty($lsk_jq_money))
        {
           $lsk_jq_money=sprintf("%.2f", $lsk_jq_money);
        }
        else
        {
          $lsk_jq_money='0.00';
        }  
		
	   if(!empty($lsk_zl_money))
        {
           $lsk_zl_money=sprintf("%.2f", $lsk_zl_money);
        }
        else
        {
          $lsk_zl_money='0.00';
        }  


       $shift_money['shouru_money']=$shouru_money;
	   $shift_money['hyk_jq_money']=$hyk_jq_money;
	   $shift_money['lsk_jq_money']=$lsk_jq_money;
	   $shift_money['lsk_zl_money']=$lsk_zl_money;

   

       return $shift_money;
}


function  getOneShiftShouru($shiift_begtime,$shift_endtime)  //获取某次交班收入
{
     //最后一次交班时间
     
        $lastshifttime=$shiift_begtime;
        
        // var_dump($lastshifttime);
        $nowtime=$shift_endtime;
         // var_dump($nowtime);
        //网费
        $map=array();
        $map['WB_ID']=session('wbid');
        $map['cTime']=array('BETWEEN',array($lastshifttime,$nowtime));
        $hyk_jq_money=D('Hyaddmoneymx')->where($map)->sum('je');		
        $lsk_jq_money=D('Lskaddmoneymx')->where($map)->sum('je');
		
		
		$map=array();
        $map['WB_ID']=session('wbid');
        $map['XjTime']=array('BETWEEN',array($lastshifttime,$nowtime));      
		$je=D('Lskshangjimx')->where($map)->sum('je');
		$foregift=D('Lskshangjimx')->where($map)->sum('foregift');
		$qtje=D('Lskshangjimx')->where($map)->sum('qtje');
		
		$lsk_zl_money=$foregift-$je;
   
   
		$shouru_money= $hyk_jq_money+ $lsk_jq_money-$lsk_zl_money;
		
			
		if(!empty($shouru_money))
        {
           $shouru_money=sprintf("%.2f", $shouru_money);
        }
        else
        {
          $shouru_money='0.00';
        }    
		
		if(!empty($hyk_jq_money))
        {
           $hyk_jq_money=sprintf("%.2f", $hyk_jq_money);
        }
        else
        {
          $hyk_jq_money='0.00';
        }  
		
	    if(!empty($lsk_jq_money))
        {
           $lsk_jq_money=sprintf("%.2f", $lsk_jq_money);
        }
        else
        {
          $lsk_jq_money='0.00';
        }  
		
	   if(!empty($lsk_zl_money))
        {
           $lsk_zl_money=sprintf("%.2f", $lsk_zl_money);
        }
        else
        {
          $lsk_zl_money='0.00';
        }  


       $shift_money['shouru_money']=$shouru_money;
	   $shift_money['hyk_jq_money']=$hyk_jq_money;
	   $shift_money['lsk_jq_money']=$lsk_jq_money;
	   $shift_money['lsk_zl_money']=$lsk_zl_money;
	   
	   $shift_money['lsk_shouru_money']=$shift_money['lsk_jq_money']-$shift_money['lsk_zl_money'];
	   $shift_money['hyk_shouru_money']=$shift_money['hyk_jq_money'];

   

       return $shift_money;
}




function  getAddMoneyByTime($shiift_begtime,$shift_endtime)  //获取某次交班收入
{
     //最后一次交班时间
     
        $lastshifttime=$shiift_begtime;
        $nowtime=$shift_endtime;
 
 
        //网费
        $map=array();
        $map['WB_ID']=session('wbid');
        $map['cTime']=array('BETWEEN',array($lastshifttime,$nowtime));
        $sum_hyk_jq_money=D('Hyaddmoneymx')->where($map)->sum('je');
        $sum_hyk_jl_money=D('Hyaddmoneymx')->where($map)->sum('jlJe');		
        $sum_lsk_jq_money=D('Lskaddmoneymx')->where($map)->sum('je');
		$sum_lsk_zlje=D('Lskzhaolingmx')->where($map)->sum('je');
		
		// $map=array();
        // $map['WB_ID']=session('wbid');
        // $map['XjTime']=array('BETWEEN',array($lastshifttime,$nowtime));    	
		// $sum_lsk_zlje=D('Lskshangjimx')->where($map)->sum('je');
		


		
		if(!empty($sum_hyk_jq_money))
        {
           $sum_hyk_jq_money=sprintf("%.2f", $sum_hyk_jq_money);
        }
        else
        {
          $sum_hyk_jq_money='0.00';
        }    
		
		if(!empty($sum_hyk_jl_money))
        {
           $sum_hyk_jl_money=sprintf("%.2f", $sum_hyk_jl_money);
        }
        else
        {
          $sum_hyk_jl_money='0.00';
        }  
		
	    if(!empty($sum_lsk_jq_money))
        {
           $sum_lsk_jq_money=sprintf("%.2f", $sum_lsk_jq_money);
        }
        else
        {
          $sum_lsk_jq_money='0.00';
        }  
		
	   if(!empty($sum_lsk_zlje))
        {
           $sum_lsk_zlje=sprintf("%.2f", $sum_lsk_zlje);
        }
        else
        {
          $sum_lsk_zlje='0.00';
        }  


       $shift_money['sum_hyk_jq_money']=$sum_hyk_jq_money;
	   $shift_money['sum_hyk_jl_money']=$sum_hyk_jl_money;
	   $shift_money['sum_lsk_jq_money']=$sum_lsk_jq_money;
	   $shift_money['sum_lsk_zlje']    =$sum_lsk_zlje;
	   
	 

   

       return $shift_money;
}



 function create_guid1() {  
    $charid = strtoupper(md5(uniqid(mt_rand(), true)));  
    $hyphen = chr(45);// "-"  
    $uuid = chr(123)// "{"  
    .substr($charid, 0, 8).$hyphen  
    .substr($charid, 8, 4).$hyphen  
    .substr($charid,12, 4).$hyphen  
    .substr($charid,16, 4).$hyphen  
    .substr($charid,20,12)  
    .chr(125);// "}"  
    return $uuid;  
} 


  function tcpsend_data($service_port, $address, $valJson, $command, $timeout = 10)
  {
      $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
  
      socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, array("sec" => 2, "usec" => 0));   //发送超时2秒
      socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array("sec" => $timeout, "usec" => 0)); //接收超时
  
      $result = socket_connect($socket, $address, $service_port);
      if (!$result)
          return '连接失败！';
      // $Json['Cmd']= 1;
      // $Json['data'] = $valJson; 
      $in = json_encode($valJson);
      // $in = '123';
      $ret = '';
    
      // $foo = "1"; // $foo 是字符串类型
      // $data = pack('V', $in1);  // $bar 是整型 
      // // echo $data; return;
      // socket_write($socket, $data);
      // socket_write($socket, $in, strlen($in));
                                      
      // $len = socket_read($socket, 4 ,PHP_BINARY_READ);//PHP_BINARY_READ
      // $len = unpack('V', $len);  // $bar 是整型 
        $in1=sprintf("%010s",strlen($in));
        socket_write($socket,$in1,strlen($in1));
        socket_write($socket,$in,strlen($in));

      $len = socket_read($socket,10,PHP_BINARY_READ);
      $num = 1024;
      while (true)
      {
          if ($len > $num)
          {
              $tmp = socket_read($socket, $num, PHP_BINARY_READ);
              $ret.= $tmp;
              $len-= strlen($tmp);
          } else
          {
              $tmp = socket_read($socket, $len, PHP_BINARY_READ);
              $ret.= $tmp;
              $len-= strlen($tmp);
              if ($len <= 0)
                  break;
          }
      }
  
      $errNo = socket_last_error($socket);
      if (0 != $errNo)
      {
          echo("<script>alertMsg.error('查询超时，请重试！');</script>");
      }
  
      socket_close($socket);
      return $ret;
  }




 function  PostTopDataToWb_lzm($wbid,$cmdtype,$jsondata)
 {
    
    $aPostData=array(
                      "Wbid"=>$wbid,
                      "Topic"=>"a", 
                      "CmdType"=>$cmdtype,     
                      "Data"=>$jsondata,
                      "MessageTime"=>date('Y-m-d h:i:s',time()),
                      "MessageID"=>create_guid1()
                     );

 
    $res=tcpsend_data('58701', '192.168.8.188',$aPostData, 0);
    return $res;
 } 


 function  PostRegisterDataToWb_lzm($wbid,$cmdtype,$tablsql)
 {
     
      $guid=create_guid1();

    $aPostData=array(
                      "Wbid"=>$wbid,
                      "Topic"=>"a", 
                      "CmdType"=>$cmdtype,     
                      "Data"=>'',
                      "MessageTime"=>date('Y-m-d h:i:s',time()),
                      "MessageID"=>$guid
                     );

    $LzmTemBuff_insert_data['guid']='ght_'.$guid;
    $LzmTemBuff_insert_data['nr']=stripslashes($tablsql);
    $LzmTemBuff_insert_data['A1']=1;
   
    $res=tcpsend_data('58701', '192.168.8.188',$aPostData, 0); 

    return $res;
 } 
 
 
 
 function  PostTopUpdateDataToWb_lzmByWbid_fenserver($awbid,$cmdtype,$tablsql)
 {
    try {
            $wbid=$awbid;
            $guid=create_guid1();
          
			if(empty($wbid))
			{
			  return;	 
			}	        
		    writelog('PostTopUpdateDataToWb_lzm--'.$tablsql,'LzmSql');	   
            $aPostData=array(
                              "Wbid"=>$wbid,
                              "Topic"=>"a", 
                              "CmdType"=>$cmdtype,     
                              "Data"=>'',
                              "MessageTime"=>date('Y-m-d h:i:s',time()),
                              "MessageID"=>$guid
                             );
            $LzmTemBuff_insert_data['guid']='ght_'.$guid;
            $LzmTemBuff_insert_data['nr']=stripslashes($tablsql);
            $LzmTemBuff_insert_data['A1']=1;
             
            $TabChangeArr['wb_id']=	$wbid;
            $bInsertTag=false;
            if(stripos($LzmTemBuff_insert_data['nr'], 'WDeFl') > 0)
            {
              $TabChangeArr['DeFl_Tag']= stripos($LzmTemBuff_insert_data['nr'], 'WDeFl');  
			  $bInsertTag=true;
            }			
            if(stripos($LzmTemBuff_insert_data['nr'], 'WIniTable') > 0)
            {
              $TabChangeArr['IniTab_Tag']= stripos($LzmTemBuff_insert_data['nr'], 'WIniTable');  
			  $bInsertTag=true;
            }			
            if(stripos($LzmTemBuff_insert_data['nr'], 'WHyLxTable_JLjh') > 0)
            {
              $TabChangeArr['HyJLjh_Tag']= stripos($LzmTemBuff_insert_data['nr'], 'WHyLxTable_JLjh');   
			  $bInsertTag=true;
            }
			
            if(stripos($LzmTemBuff_insert_data['nr'], 'WIntegral_JlTable') > 0)
            {
              $TabChangeArr['Integral_Jl_Tag']= stripos($LzmTemBuff_insert_data['nr'], 'WIntegral_JlTable');  
			  $bInsertTag=true;
            }		
            if(stripos($LzmTemBuff_insert_data['nr'], 'WComputerList') > 0)
            {
               $TabChangeArr['ComputerList_Tag']= stripos($LzmTemBuff_insert_data['nr'], 'WComputerList');
			   $bInsertTag=true;
            }
			
            if(stripos($LzmTemBuff_insert_data['nr'], 'WGroupTable') > 0)
            {
              $TabChangeArr['GroupTable_Tag']= stripos($LzmTemBuff_insert_data['nr'], 'WGroupTable'); 
               $bInsertTag=true;			  
            }		
			if(stripos($LzmTemBuff_insert_data['nr'], 'WHyLxTable') > 0)
            {
                $TabChangeArr['HyLxTable_Tag']= stripos($LzmTemBuff_insert_data['nr'], 'WHyLxTable');
				$bInsertTag=true;

            }
			
            if(stripos($LzmTemBuff_insert_data['nr'], 'WUserTable') > 0)
            {
			    $TabChangeArr['UserTable_Tag']= stripos($LzmTemBuff_insert_data['nr'], 'WUserTable');
				$bInsertTag=true;
            }
			$TabChangeArr['cTime'] = date('Y-m-d H:i:s',time());
			
            if( $LzmTemBuff_insert_data['nr'] != '')
            {
				D()->startTrans();
				try
                { 
                                   
					if($bInsertTag)	
					{
					   $bExist=D('WbSetChange')->where(array('wb_id'=>$wbid))->select();
					   if(!empty($bExist))
					   {
						  $TabChangeArrResult= D('WbSetChange')->where(array('wb_id'=>$wbid))->save($TabChangeArr);					 					 
					   }
					   else
					   {					   				 
						  $TabChangeArrResult= D('WbSetChange')->add($TabChangeArr);				   
					   }	
					   $sSendstr=D('WbSetChange')->getLastSql(); 			   					   				   
					   if($TabChangeArrResult)
						{ 
							 						   
						}
						else 
						{ 
							
						}
					}				  
				
					// $LzmTemBuff_insert_data['nr']=stripslashes($tablsql).';'.stripslashes($sSendstr);		
					// writelog($LzmTemBuff_insert_data['nr'],'LzmSql');
					// $LzmTemBuff_insert_result= D('TemBuff')->add($LzmTemBuff_insert_data);
					
					D()->commit();
					// if($LzmTemBuff_insert_result)
					// { 
						// writelog(json_encode($aPostData),'LzmMsg'); 
						// $res=tcpsend_data('58701', '192.168.8.188',$aPostData, 0); 
					// }
						writelog(json_encode($aPostData),'LzmMsg'); 
						$res=tcpsend_data('58701', '192.168.8.188',$aPostData, 0); 
                } 
				catch (Exception $e)
                {
				   D()->rollback();
                   writelog('-PostTopUpdateDataToWb_lzm-'.$e->getMessage(),'Err');  
               }
            }
			else
            {
               writelog('PostTopUpdateDataToWb_lzm--'.'空白Sql '.$LzmTemBuff_insert_data['nr'],'LzmSql'); 
            }
            return $res;
    }
	catch(Exception $e) 
	{
        writelog('-PostTopUpdateDataToWb_lzm-'.$e->getMessage(),'Err');  
    }

 } 



function  PostTopUpdateDataToWb_lzmByWbid($awbid,$cmdtype,$tablsql)
 {
    try {
            $wbid=$awbid;
            $guid=create_guid1();                              
            $aPostData=array(
                              "Wbid"=>$wbid,
                              "Topic"=>"a", 
                              "CmdType"=>$cmdtype,     
                              "Data"=>'',
                              "MessageTime"=>date('Y-m-d H:i:s',time()),
                              "MessageID"=>$guid
                             );
            $LzmTemBuff_insert_data['guid']='ght_'.$guid;
            $LzmTemBuff_insert_data['nr']=stripslashes($tablsql);
            $LzmTemBuff_insert_data['A1']=1;          
            $TabChangeArr['wb_id']=	$wbid;
            $bInsertTag=false;
            if(stripos($LzmTemBuff_insert_data['nr'], 'WDeFl') > 0)
            {
              $TabChangeArr['DeFl_Tag']= stripos($LzmTemBuff_insert_data['nr'], 'WDeFl');  
			  $bInsertTag=true;
            }
			
            if(stripos($LzmTemBuff_insert_data['nr'], 'WIniTable') > 0)
            {
              $TabChangeArr['IniTab_Tag']= stripos($LzmTemBuff_insert_data['nr'], 'WIniTable');  
			  $bInsertTag=true;
            }
			
            if(stripos($LzmTemBuff_insert_data['nr'], 'WHyLxTable_JLjh') > 0)
            {
              $TabChangeArr['HyJLjh_Tag']= stripos($LzmTemBuff_insert_data['nr'], 'WHyLxTable_JLjh');   
			  $bInsertTag=true;
            }			
            if(stripos($LzmTemBuff_insert_data['nr'], 'WIntegral_JlTable') > 0)
            {
              $TabChangeArr['Integral_Jl_Tag']= stripos($LzmTemBuff_insert_data['nr'], 'WIntegral_JlTable');  
			  $bInsertTag=true;
            }		
            if(stripos($LzmTemBuff_insert_data['nr'], 'WComputerList') > 0)
            {
               $TabChangeArr['ComputerList_Tag']= stripos($LzmTemBuff_insert_data['nr'], 'WComputerList');
			   $bInsertTag=true;
            }		
            if(stripos($LzmTemBuff_insert_data['nr'], 'WGroupTable') > 0)
            {
              $TabChangeArr['GroupTable_Tag']= stripos($LzmTemBuff_insert_data['nr'], 'WGroupTable'); 
               $bInsertTag=true;			  
            }		
			if(stripos($LzmTemBuff_insert_data['nr'], 'WHyLxTable') > 0)
            {
                $TabChangeArr['HyLxTable_Tag']= stripos($LzmTemBuff_insert_data['nr'], 'WHyLxTable');
				$bInsertTag=true;
            }		
            if(stripos($LzmTemBuff_insert_data['nr'], 'WUserTable') > 0)
            {
			    $TabChangeArr['UserTable_Tag']= stripos($LzmTemBuff_insert_data['nr'], 'WUserTable');
				$bInsertTag=true;
            }
			$TabChangeArr['cTime'] = date('Y-m-d H:i:s',time());
			
            if( $LzmTemBuff_insert_data['nr'] != '')
            {
				D()->startTrans();
				try
                { 
				  if($bInsertTag)	
				  {
				   $bExist=D('WbSetChange')->where(array('wb_id'=>$wbid))->select();
				   if(!empty($bExist))
				   {
					  $TabChangeArrResult= D('WbSetChange')->where(array('wb_id'=>$wbid))->save($TabChangeArr);					 					 
				   }
				   else
				   {										 
					   $TabChangeArrResult= D('WbSetChange')->add($TabChangeArr);				   
				   }	
					   $sSendstr=D('WbSetChange')->getLastSql(); 			   					   				   
					if($TabChangeArrResult)
					{ 
												   
					}
					else 
					{ 
						
					}
				  }				  		
                  $LzmTemBuff_insert_data['nr']=stripslashes($tablsql).';'.stripslashes($sSendstr);

				  D()->commit();
					// writelog(json_encode($aPostData),'LzmMsg'); 
                     $res=tcpsend_data('58701', '192.168.8.188',$aPostData, 0); 
               } 
			   catch (Exception $e)
                {
				   D()->rollback();
                   writelog('-PostTopUpdateDataToWb_lzm-'.$e->getMessage(),'Err');  
               }
            } else
            {
               writelog('空白Sql'.$LzmTemBuff_insert_data['nr'],'LzmSql'); 
            }
            return $res;
    } catch (Exception $e) {
        writelog('-PostTopUpdateDataToWb_lzm-'.$e->getMessage(),'Err');  
    }

 } 
 
 
 
 
 
 

function  PostTopUpdateDataToWb_lzm($cmdtype,$tablsql)
 {
    try {
            $wbid=session('wbid');
            $guid=create_guid1();
            writelog($tablsql,'yuansql');
			if(empty($wbid))
			{
			  return;	 
			}	        
		    writelog('PostTopUpdateDataToWb_lzm--'.$tablsql,'LzmSql');	   
            $aPostData=array(
                              "Wbid"=>$wbid,
                              "Topic"=>"a", 
                              "CmdType"=>$cmdtype,     
                              "Data"=>'',
                              "MessageTime"=>date('Y-m-d h:i:s',time()),
                              "MessageID"=>$guid
                             );
            $LzmTemBuff_insert_data['guid']='ght_'.$guid;
            $LzmTemBuff_insert_data['nr']=stripslashes($tablsql);
            $LzmTemBuff_insert_data['A1']=1;
             
            $TabChangeArr['wb_id']=	$wbid;
            $bInsertTag=false;
            if(stripos($LzmTemBuff_insert_data['nr'], 'WDeFl') > 0)
            {
              $TabChangeArr['DeFl_Tag']= stripos($LzmTemBuff_insert_data['nr'], 'WDeFl');  
			  $bInsertTag=true;
            }			
            if(stripos($LzmTemBuff_insert_data['nr'], 'WIniTable') > 0)
            {
              $TabChangeArr['IniTab_Tag']= stripos($LzmTemBuff_insert_data['nr'], 'WIniTable');  
			  $bInsertTag=true;
            }			
            if(stripos($LzmTemBuff_insert_data['nr'], 'WHyLxTable_JLjh') > 0)
            {
              $TabChangeArr['HyJLjh_Tag']= stripos($LzmTemBuff_insert_data['nr'], 'WHyLxTable_JLjh');   
			  $bInsertTag=true;
            }
			
            if(stripos($LzmTemBuff_insert_data['nr'], 'WIntegral_JlTable') > 0)
            {
              $TabChangeArr['Integral_Jl_Tag']= stripos($LzmTemBuff_insert_data['nr'], 'WIntegral_JlTable');  
			  $bInsertTag=true;
            }		
            if(stripos($LzmTemBuff_insert_data['nr'], 'WComputerList') > 0)
            {
               $TabChangeArr['ComputerList_Tag']= stripos($LzmTemBuff_insert_data['nr'], 'WComputerList');
			   $bInsertTag=true;
            }
			
            if(stripos($LzmTemBuff_insert_data['nr'], 'WGroupTable') > 0)
            {
              $TabChangeArr['GroupTable_Tag']= stripos($LzmTemBuff_insert_data['nr'], 'WGroupTable'); 
               $bInsertTag=true;			  
            }		
			if(stripos($LzmTemBuff_insert_data['nr'], 'WHyLxTable') > 0)
            {
                $TabChangeArr['HyLxTable_Tag']= stripos($LzmTemBuff_insert_data['nr'], 'WHyLxTable');
				$bInsertTag=true;

            }
			
            if(stripos($LzmTemBuff_insert_data['nr'], 'WUserTable') > 0)
            {
			    $TabChangeArr['UserTable_Tag']= stripos($LzmTemBuff_insert_data['nr'], 'WUserTable');
				$bInsertTag=true;
            }
			$TabChangeArr['cTime'] = date('Y-m-d H:i:s',time());
			
            if( $LzmTemBuff_insert_data['nr'] != '')
            {
				D()->startTrans();
				try
                {                                      
					if($bInsertTag)	
					{
					   $bExist=D('WbSetChange')->where(array('wb_id'=>$wbid))->select();
					   if(!empty($bExist))
					   {
						  $TabChangeArrResult= D('WbSetChange')->where(array('wb_id'=>$wbid))->save($TabChangeArr);					 					 
					   }
					   else
					   {					   				 
						  $TabChangeArrResult= D('WbSetChange')->add($TabChangeArr);				   
					   }
					   
					   //$sSendstr=D('WbSetChange')->getLastSql(); 			   					   				   
					   if($TabChangeArrResult)
						{ 
							//writelog('Wb_Set_Change wbid='.$wbid,'SetChange'); 						   
						}
						else 
						{ 
							// writelog('Wb_Set_Change失败','SetChange');
						}
					}				  
									
					D()->commit();

					writelog(json_encode($aPostData),'LzmMsg'); 
					$res=tcpsend_data('58701', '192.168.8.188',$aPostData, 0); 
                } 
				catch (Exception $e)
                {
				   D()->rollback();
                   writelog('-PostTopUpdateDataToWb_lzm-'.$e->getMessage(),'Err');  
               }
            }
			else
            {
               writelog('PostTopUpdateDataToWb_lzm--'.'空白Sql '.$LzmTemBuff_insert_data['nr'],'LzmSql'); 
            }
            return $res;
    }
	catch(Exception $e) 
	{
        writelog('-PostTopUpdateDataToWb_lzm-'.$e->getMessage(),'Err');  
    }

 } 
 
 
 
   function  PostGzhDataToWb_lzm($wbid,$jsondata)
 {
 	$guid=create_guid1();
	$aPostData=array(
	                  "Wbid"=>$wbid,
	                  "Topic"=>"a",
	                  "Data"=>$jsondata,
	                  "MessageTime"=>date('Y-m-d h:i:s',time()),
	                  "MessageID"=>$guid
		             );
  
   // writelog(json_encode($aPostData),'gzhsendcontent');
	 
	$res=tcpsend_data('58701', '192.168.8.188',$aPostData, 0);
	// writelog(json_encode($aPostData),'sendcontent');

	return $res;
 } 
 
 function getfirstchar($s0){   //获取单个汉字拼音首字母。注意:此处不要纠结。汉字拼音是没有以U和V开头的
    $fchar = ord($s0{0});
    if($fchar >= ord("A") and $fchar <= ord("z") )return strtoupper($s0{0});
    $s1 = iconv("UTF-8","gb2312", $s0);
    $s2 = iconv("gb2312","UTF-8", $s1);
    if($s2 == $s0){$s = $s1;}else{$s = $s0;}
    $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
    if($asc >= -20319 and $asc <= -20284) return "A";
    if($asc >= -20283 and $asc <= -19776) return "B";
    if($asc >= -19775 and $asc <= -19219) return "C";
    if($asc >= -19218 and $asc <= -18711) return "D";
    if($asc >= -18710 and $asc <= -18527) return "E";
    if($asc >= -18526 and $asc <= -18240) return "F";
    if($asc >= -18239 and $asc <= -17923) return "G";
    if($asc >= -17922 and $asc <= -17418) return "H";
    if($asc >= -17922 and $asc <= -17418) return "I";
    if($asc >= -17417 and $asc <= -16475) return "J";
    if($asc >= -16474 and $asc <= -16213) return "K";
    if($asc >= -16212 and $asc <= -15641) return "L";
    if($asc >= -15640 and $asc <= -15166) return "M";
    if($asc >= -15165 and $asc <= -14923) return "N";
    if($asc >= -14922 and $asc <= -14915) return "O";
    if($asc >= -14914 and $asc <= -14631) return "P";
    if($asc >= -14630 and $asc <= -14150) return "Q";
    if($asc >= -14149 and $asc <= -14091) return "R";
    if($asc >= -14090 and $asc <= -13319) return "S";
    if($asc >= -13318 and $asc <= -12839) return "T";
    if($asc >= -12838 and $asc <= -12557) return "W";
    if($asc >= -12556 and $asc <= -11848) return "X";
    if($asc >= -11847 and $asc <= -11056) return "Y";
    if($asc >= -11055 and $asc <= -10247) return "Z";
    return NULL;
    //return $s0;
}
function pinyin_long($zh)
{  //获取整条字符串汉字拼音首字母
    $ret = "";
    $s1 = iconv("UTF-8","gb2312", $zh);
    $s2 = iconv("gb2312","UTF-8", $s1);
    if($s2 == $zh){$zh = $s1;}
    for($i = 0; $i < strlen($zh); $i++){
        $s1 = substr($zh,$i,1);
        $p = ord($s1);
        if($p > 160){
            $s2 = substr($zh,$i++,2);
            $ret .= getfirstchar($s2);
        }else{
            $ret .= $s1;
        }
    }
    return $ret;
}

function getpinyin($s)
{
	$s1=pinyin_long($s);
	$s2=strtolower($s1);
	return $s2;
}


function getAllPY($one_goods_name)
{
	$one_goods_name = iconv("UTF-8","gb2312", $one_goods_name);
	include C('EXCEL_PATH2').'Pinyin.php';
	
    $PingYing = new GetPingYing();
    $quanpin=$PingYing->getAllPY($one_goods_name);
	return $quanpin;
}


function getOneDayShouru($sql)
{

	include C('EXCEL_PATH2').'database/core.class.php';
	
    $db = new Core();
    $quanpin=$db->getOneDayShouru($sql);
	return $quanpin;
}



function  getTodayShouru_zzb($wbid)
{
     //最后一次交班时间	 
        $nowtime1=date('Y-m-d H:i:s');
        $todaybegtime= date('Y-m-d 00:00:00',strtotime($nowtime1));
        $nowtime      = date('Y-m-d H:i:s');


        //获取本日开始时间
        
        //网费
        $map=array();
        $map['WB_ID']=$wbid;
        $map['cTime']=array('BETWEEN',array($todaybegtime,$nowtime));
        $hyk_jq_money=D('Hyaddmoneymx')->where($map)->sum('je');
        $lsk_jq_money=D('Lskaddmoneymx')->where($map)->sum('je');
		
		$map=array();
        $map['WB_ID']=$wbid;
        $map['XjTime']=array('BETWEEN',array($todaybegtime,$nowtime));      
			
	    $je=D('Lskshangjimx')->where($map)->sum('je');
		$foregift=D('Lskshangjimx')->where($map)->sum('foregift');
		$qtje=D('Lskshangjimx')->where($map)->sum('qtje');
		
		$lsk_zl_money=$foregift-$je;
		
        $wf_money= $hyk_jq_money+ $lsk_jq_money-$lsk_zl_money;
		
		$lsk_money=  $lsk_jq_money-$lsk_zl_money;



        $map=array();
        $map['wb_id']=$wbid;
        $map['cTime']=array('BETWEEN',array($todaybegtime,$nowtime));
        $zfb_money=D('ZfbAddMoneyMx')->where($map)->sum('je');

        $map=array();
        $map['wb_id']=$wbid;
        $map['cTime']=array('BETWEEN',array($todaybegtime,$nowtime));
        $wx_money=D('WxMx')->where($map)->sum('je');
		
        $sum_money= $wf_money+  $zfb_money+ $wx_money;

        if(!empty($wf_money))
        {
           $money_array['wf_money']=sprintf("%.2f", $wf_money); 
        }
        else
        {
           $money_array['wf_money']='0.00'; 
        }   

        if(!empty($hyk_jq_money))
        {
           $money_array['hyk_money']=sprintf("%.2f", $hyk_jq_money); 
        }
        else
        {
           $money_array['hyk_money']='0.00'; 
        }   


        if(!empty($lsk_money))
        {
           $money_array['lsk_money']=sprintf("%.2f", $lsk_money); 
        }
        else
        {
           $money_array['lsk_money']='0.00'; 
        }   
    
        if(!empty($wx_money))
        {
           $money_array['wx_money']=sprintf("%.2f", $wx_money); 
        }
        else
        {
           $money_array['wx_money']='0.00'; 
        }   

        
        if(!empty($zfb_money))
        {
           $money_array['zfb_money']=sprintf("%.2f", $zfb_money); 
        }
        else
        {
           $money_array['zfb_money']='0.00'; 
        }   

        if(!empty($sum_money))
        {
           $money_array['sum_money']=sprintf("%.2f", $sum_money); 
        }
        else
        {
           $money_array['sum_money']='0.00'; 
        }
       return $money_array;
}



function  getNowShiftShouru_zzb($wbid)
{
     //最后一次交班时间	 
            $shiftinfo= D('Shift')->where(array('WB_ID'=>$wbid))->Order('cTime desc')->limit(1)->find();
			$lastshifttime=date('Y-m-d H:i:s',strtotime($shiftinfo['cTime']));       
			$nowtime=date('Y-m-d H:i:s');
			
			//网费
			$map=array();
			$map['WB_ID']=$wbid;
			$map['cTime']=array('BETWEEN',array($lastshifttime,$nowtime));
			$hyk_jq_money=D('Hyaddmoneymx')->where($map)->sum('je');
			$lsk_jq_money=D('Lskaddmoneymx')->where($map)->sum('je');
			
			$map=array();
			$map['WB_ID']=$wbid;
			$map['XjTime']=array('BETWEEN',array($lastshifttime,$nowtime));      		
			
			$je=D('Lskshangjimx')->where($map)->sum('je');
			$foregift=D('Lskshangjimx')->where($map)->sum('foregift');
			$qtje=D('Lskshangjimx')->where($map)->sum('qtje');
			
			$lsk_zl_money=$foregift-$je;			
			$wf_money= $hyk_jq_money+ $lsk_jq_money-$lsk_zl_money;			
			$lsk_money=  $lsk_jq_money-$lsk_zl_money;

			$map=array();
			$map['wb_id']=$wbid;
			$map['cTime']=array('BETWEEN',array($lastshifttime,$nowtime));
			$zfb_money=D('ZfbAddMoneyMx')->where($map)->sum('je');

			$map=array();
			$map['wb_id']=$wbid;
			$map['cTime']=array('BETWEEN',array($lastshifttime,$nowtime));
		 
		 
			$wx_money=D('WxMx')->where($map)->sum('je');
			$sum_money= $wf_money+  $zfb_money+ $wx_money;

			if(!empty($wf_money))
			{
			   $money_array['wf_money']=sprintf("%.2f", $wf_money); 
			}
			else
			{
			   $money_array['wf_money']='0.00'; 
			}   

			if(!empty($hyk_jq_money))
			{
			   $money_array['hyk_money']=sprintf("%.2f", $hyk_jq_money); 
			}
			else
			{
			   $money_array['hyk_money']='0.00'; 
			}   

			if(!empty($lsk_money))
			{
			   $money_array['lsk_money']=sprintf("%.2f", $lsk_money); 
			}
			else
			{
			   $money_array['lsk_money']='0.00'; 
			}   
		
			if(!empty($wx_money))
			{
			   $money_array['wx_money']=sprintf("%.2f", $wx_money); 
			}
			else
			{
			   $money_array['wx_money']='0.00'; 
			}   

			
			if(!empty($zfb_money))
			{
			   $money_array['zfb_money']=sprintf("%.2f", $zfb_money); 
			}
			else
			{
			   $money_array['zfb_money']='0.00'; 
			}   

			if(!empty($sum_money))
			{
			   $money_array['sum_money']=sprintf("%.2f", $sum_money); 
			}
			else
			{
			   $money_array['sum_money']='0.00'; 
			}
       return $money_array;
}

/*
function  get30dayShourumx_zzb($wbid)
{
     //最后一次交班时间	 
     
		$nowtime=date('Y-m-d H:i:s');
        $nowtime1= strtotime($nowtime);
        $last30daytime = strtotime('-30 days',$nowtime1);
        $last30daytime = date('Y-m-d H:i:s', $last30daytime) ; 
						
		$map = array();
		$map['wb_id']=$wbid;
        $map['cTime']=array('BETWEEN',array($last30daytime,$nowtime));	
		$money_array = D('Tongji')->where($map)->select(); 	
  
       return $money_array;
}
*/


function hexToStr($hex)//十六进制转字符串
{   
    $string=""; 
    for($i=0;$i<strlen($hex)-1;$i+=2)
    $string.=chr(hexdec($hex[$i].$hex[$i+1]));
    return  $string;
}

function aesDeJm($Str, $key)
{     
     $Str=hexToStr($Str);
     // error_log(date('Y-m-d H:i:s:ms') ." ".$Str."\r\n", 3, './log/errors.log');  
     $block = mcrypt_get_block_size('rijndael_128', 'ecb');
     $pad = $block - (strlen($key) % $block);
 
     $key .= str_repeat(chr($pad), $pad);   
     $JmStr= mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $Str, MCRYPT_MODE_ECB); 
     
  return $JmStr;
}



	function sendRequsttoOneServer($url, $post_data = '', $timeout = 5)
	{
        $ch = curl_init();
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_POST, 1);
        if($post_data != ''){
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        }
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $file_contents = curl_exec($ch);
		$err_code = curl_errno($ch);
		$info  = curl_getinfo( $ch );
		

		
        curl_close($ch);
        return $file_contents;
    }


	function PostTopDataToWb_lzm_cs($url, $post_data = '', $timeout = 5)
	{
        $ch = curl_init();
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_POST, 1);
        if($post_data != ''){
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        }
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $file_contents = curl_exec($ch);
		$err_code = curl_errno($ch);
		$info  = curl_getinfo( $ch );
		
	//	writelog($err_code ,'error');
		
	//	writelog('---1--'.json_encode($info) ,'error');
		
        curl_close($ch);
        return $file_contents;
    }