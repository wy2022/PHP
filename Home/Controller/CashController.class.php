<?php
namespace Home\Controller;
class CashController extends CommonController {

	public function getAreaListHtmlById()
    {
        if(IS_AJAX)
        {
            $id = I('get.id',0,'int');
            if(empty($id)){
                $this->success('');
            }else{
                $areas = D('Area')->getAreaList($id);
 
                $html = '';
                foreach($areas as $value)
                {
                    $html .= '<option value="'.$value['id'].'">'.$value['area_name'].'</option>';
                }

                $this->success($html);
            }
        }
    }
    public function getPhoneVerifycode() 
    {
       $verifycode=(mt_rand(100000,999999));  
      
      
       session('phone_verifycode',$verifycode);
       $phonenum=I('get.mobile','','string');
 
     
      if(!empty($phonenum))
      {    
        SendToTelOfAccNo($phonenum,$verifycode);
      }
      else
      {
      
      }  
      
      $data['status']='0';
      echo json_encode($data);
    }




    public function CheckPhoneyzm() 
    {
       $yzm=I('post.yzm');
       $verify=session('phone_verifycode');
        if(trim($yzm) <> trim($verify))
        {
           $data['result']='0';
        }
        else
        {
           $data['result']='1';
        } 
        echo json_encode($data);
    }




   
    public function showcodestatus()
    {
      $post_yzm=$_POST['verifycode'];
      $session_yzm=session('verifycode');    
      if($post_yzm==$session_yzm)
      {
        echo "true";
      }
      else
      {
        echo "false";
      }
    }
   

    public function showtxinfo()  //展示银行卡信息
    {         
        $wbid=session('wbid');  
        if(empty($wbid))
        {
            header('Location: http://www.wbzzsf.com/'); 
            return;
        }      
        $this->display();      
    }



    public function gettxinfo()  //展示银行卡信息
    {          
        if(IS_AJAX)
        {       
     
            $page = I('get.page',1,'int');
            $rows = I('get.rows',20,'int');
    /*        $sord = I('get.sord','','string')?:'desc';
            $sidx = I('get.sidx','','string')?:'wbid';*/
  
            $orderno = I('post.orderno','','string');
            $tx_status =  I('post.tx_status',0,'string');
            $daterange =  I('post.daterange','','string');//获取交班时间

            $map = array();
            
            if(!empty($tx_status))
            {          
              $map['WBTxQueryInfo.tx_status']=$tx_status;       
            }          
            
            if(!empty($orderno ))
            {
              $map['WBTxQueryInfo.orderno']=array('LIKE','%'.$orderno.'%');
            }  

            $map['WBTxQueryInfo.wbid']=session('wbid');
            if(!empty($daterange))  
            {
                list($start,$end) = explode(' - ',$daterange);    //explode() 函数把字符串以"-"为标志分割为数组，包含两个元素，赋值给list，调用时候直接$start,$end
                $start = str_replace('/','-',$start);            //把分割的第一个数据里的"/"用"-"号替换
                $end = str_replace('/','-',$end);                //把分割的第二个数据里的"/"用"-"号替换
                $map['WBTxQueryInfo.time_post'] = array('BETWEEN',array($start,$end));//返回数据array(值，值)---第一个值是字符串'between'，第二个值是一个数组
            }
                                  
            $count=D('TxQueryInfo')->getQueryTxInfoCount($map);
            $sql_page=ceil($count/$rows);   
            
            if($page<=0)   $page=1;       
            if($page>$sql_page) 
            {
              $page=1; 
            }else
            {

            }    
        
            $TxQuerydata = D('TxQueryInfo')->getQueryTxInfoList($map,$page,$rows); 

            $response = new \stdClass();
            $response->count       = $TxQuerydata['count'];//返回的数组的第一个字段记录总条数
            $response->nowPage     = $page ;              //每页显示的记录数目               
            $response->total       = ceil($TxQuerydata['count'] / $rows);
            $response->dai_tx_je   = $TxQuerydata['dai_tx_je'] ;
            $response->yi_tx_je    = $TxQuerydata['yi_tx_je']; 
            
            $response->rows   = $TxQuerydata['list'] ;
            $this->ajaxReturn($response);
        }
    }



  

	
	public function addcashinfo_zong()
    {       
        $wbid=session('wbid');
        if(empty($wbid))
        {
          header('Location: http://www.wbzzsf.com/'); 
          return;
        }  
          
        if(IS_AJAX)
        {			
			if(!checkToken($_POST['TOKEN']))
			{  
		        writelog('jiaoban_edit_set---重复提交','jiaoban');
				$data['status']=-2;          								
			    $this->ajaxReturn($data);
                return;   			   
			}
			else
			{
				//writelog('jiaoban_edit_set---未重复提交','jiaoban');
			}
			
			$flag= D('TxQueryInfo')->where(array('wbid'=>session('wbid'),'tx_status'=>2))->find(); 			
			if($flag)
			{
			   $response['data']='3';   
               $response['msg']='您有尚未完成的提现处理，请等待提现完成后再次提交申请'; 
               $this->ajaxReturn($response);
               return;
			}
			
			
			
            $bankcardno= $_POST['bankcardno'];
            $qqtx_je   = $_POST['qqtx_je'];
            $yf_je     = $_POST['yf_je'];
			$beizhu    = $_POST['beizhu'];
           
             if($qqtx_je <100  ||  $qqtx_je > 5000)
             {
               $response['data']='3';   
               $response['msg']='提现金额范围 100----5000元之间'; 
               $this->ajaxReturn($response);
               return;
             }
			 
			 
			 

			 
			 
			 
			 
            
            if(!empty($yf_je))
            {
				
			  $money['sum_zfb_in']=D('Zfbpay')->where(array('wbid'=>$wbid,'trade_status'=>100))->sum('receipt_amount');
			  $money['sum_wx_in'] =D('Wxpay')->where(array('wbid'=>$wbid,'trade_status'=>100))->sum('receipt_amount');
			  $money['sum_gzh_in'] =D('Gzhpay')->where(array('wbid'=>$wbid,'trade_status'=>100))->sum('notify_total_fee');
	
              $sum_je    = $money['sum_zfb_in']+$money['sum_wx_in']+$money['sum_gzh_in']; //获取总额
              $yuan_tx_je= D('TxQueryInfo')->where(array('wbid'=>session('wbid')))->sum('qqtx_je');//获取已经申请的提现总额,包括未确认支付的
              $ky_ye=$sum_je -$yuan_tx_je;   
			  
			  
              if($qqtx_je>$ky_ye)
              {            
                //可用余额不足 
                $response['data']='4';  
                $response['msg']='请求提现金额应小于总可用金额';   
                $this->ajaxReturn($response);
                return;
              } 
              			  
            }
                            
            $result=true;                  
			D()->startTrans(); 
					
		    $daili_url_zong=C('DAILI_URL_ZONG');
		    $post_data=array();
			$post_data['wbid']=$wbid;
			$url= $daili_url_zong.'/index.php/ServerzongAPI/API_query_bar_txbankinfo4.html';
			$res= sendRequsttoOneServer($url, $post_data,30);															
			//$res= substr($res, 3);				
			$res2=json_decode($res,true);
			
			if($res2['result']==1)
			{
			   $this->assign('flag',1);	
			   $bankinfo=$res2['bankinfo'];	       	   
			}
			else
			{
			  $this->assign('flag',0);				
			}

			$out_trade_no = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);        
			$map=array(); 
			$map['bankcardno']  = $bankinfo['bankcardno'];
			$map['time_post']  = date('Y-m-d H:i:s',time());
			$map['wbid']=$wbid;
			$map['qqtx_je']=$yf_je;
			$map['sum_je']=$ky_ye;
			$map['beizhu']=$beizhu;
			$map['orderno']=$out_trade_no;
		
		
			$map['farenname']=$bankinfo['farenname'];
			$map['kh_hang']=$bankinfo['kh_hang'];
			$map['phonenum']=$bankinfo['phonenum'];

			if(D('TxQueryInfo')->addQueryTxInfo($map)===false)
			{
				$result=false;
			}				
									
			$wbname=D('WbInfo')->where(array("WBID"=>$wbid))->getField("WbName");			  
			if(D('Tixian')->where(array('wbid'=>session('wbid')))->setInc('sum_txje_done',$yf_je)===false)
			{
				$result=false; 
			}	
			
						   
			if($result)
			{
				D()->commit(); 
			                
				//发送http请求在 主服务器追加数据				
				$daili_url_zong=C('DAILI_URL_ZONG');
				$post_data=array();
				$post_data['wbid']=$wbid;
				$post_data['time_post']=date('Y-m-d H:i:s',time());
				$post_data['qqtx_je']=$yf_je;
				$post_data['sum_je']=$ky_ye;
				$post_data['beizhu']=$beizhu;
				$post_data['orderno']=$out_trade_no;
				$post_data['bankcardno']  = $bankinfo['bankcardno'];
				
			    $post_data['farenname']=$bankinfo['farenname'];
			    $post_data['kh_hang']=$bankinfo['kh_hang'];
			    $post_data['phonenum']=$bankinfo['phonenum'];
				
				$url= $daili_url_zong.'/index.php/ServerzongAPI/API_add_onetxqueryinfo.html';
				$res= sendRequsttoOneServer($url, $post_data,30);															
				//$res= substr($res, 3);				
				$res2=json_decode($res,true);
				if($res2['result']==1)
				{
				    $response['data']='1';	
					$response['msg']='提交成功';	
				}	
																	
			}
			else
			{
				D()->rollback(); 
				$response['data']='2'; 
				$response['msg']='数据提交失败';					
			}  				
			  					 
			$this->ajaxReturn($response);					              
        }               
    }
		
	public function addcard()
    {
		
       $wbid=session('wbid'); 
	   /*
       if($wbid ==1071 || $wbid ==1702)
	   {
	     
       }else{
		    echo  '提现功能正在维护中,请稍后重试';
		  return;
	   }
       	*/   
       if(empty($wbid))
       {
         header('Location: http://www.wbzzsf.com/'); 
         return;
       }  
	   
	    $daili_url_zong=C('DAILI_URL_ZONG');
	    $post_data=array();
		$post_data['wbid']=$wbid;
	    $url= $daili_url_zong.'/index.php/ServerzongAPI/API_query_bar_txbankinfo.html';
		$res= sendRequsttoOneServer($url, $post_data,30);															
		//$res= substr($res, 3);				
		$res2=json_decode($res,true);
		    	
		if($res2['result']==1)
		{
		   $bExist=true;			
		}
		else
		{
		  $bExist=false;				
		}
		
       if($bExist)
       {
        $this->redirect('Cash/cash'); 	 	 
        return;
       }   

	      
       $barinfo=D('WbInfo')->where(array('WBID'=>$wbid))->Field('WBTel,wxid')->find();        
       $this->assign('phonenum_ght',$barinfo['WBTel']);    
       $yinhanglist=D('Refcode2')->where(array('meaning'=>'yinhang'))->select();
       $this->assign('yinhanglist',$yinhanglist);
	   
	   
	    $post_data=array();
		$post_data['wbid']=$wbid;
	    $url= $daili_url_zong.'/index.php/ServerzongAPI/API_query_bar_txbankinfo2.html';
		$res= sendRequsttoOneServer($url, $post_data,30);
		//$res= substr($res, 3);
		$res2=json_decode($res,true);
		

					
		if($res2['result']==1)
		{
		   $this->assign('flag',1);	
		   $bankinfo=$res2['bankinfo'];	       	   
		}
		else
		{
		  $this->assign('flag',0);				
		}
	   	
        
	   $this->assign('bz',$bankinfo['bz']); 	
	   if(empty($bankinfo['shenfenzheng_image']))
	   {
		   $bankinfo['shenfenzheng_image'] =C('TUPIAN_PATH').'/moren/sfz.jpg'; 
	   }
	   else
       {
		   $bankinfo['shenfenzheng_image'] =C('TUPIAN_PATH_ZONG').$bankinfo['shenfenzheng_image']; 
	   }	


       if(empty($bankinfo['zhizhao_image']))
	   {
		   $bankinfo['zhizhao_image'] =C('TUPIAN_PATH').'/moren/yyzz.jpg'; 
	   }
	   else
       {
		   $bankinfo['zhizhao_image'] =C('TUPIAN_PATH_ZONG').$bankinfo['zhizhao_image']; 
	   }	

       if(empty($bankinfo['shouquanshu_image']))
	   {
		   $bankinfo['shouquanshu_image'] =C('TUPIAN_PATH').'/moren/hezhao.jpg'; 
	   }
	   else
       {
		   $bankinfo['shouquanshu_image'] =C('TUPIAN_PATH_ZONG').$bankinfo['shouquanshu_image']; 
	   }		   
	   	


	   
	   
	   if($bankinfo['wxid'])
	   {
		   $wxid=  $bankinfo['wxid'];
	   }
	   else
	   {
		   $wxid=  $barinfo['wxid'];
	   }
	   
	   if($wxid)
	   {
		  $this->assign('showwxid',0);	
	   }
	   else
	   {
		   $this->assign('showwxid',1);
	   }
	   
      
       $this->assign('daili_url_zong',$daili_url_zong);	
	   $this->assign('wbid',$wbid);	
	   $this->assign('bankinfo',$bankinfo); 	
       $this->assign('wxid',$wxid);    	   
	   $this->assign('province_list',D('Area')->getAreaList());  
       $this->display();  
    }
	
	
	
	public function cash()
    {       
        $wbid= session('wbid'); 	
        if(empty($wbid))
        {
          header('Location: http://www.wbzzsf.com/'); 
          return;
        }  
		$result=true;
		$sendstr='';
        D()->startTrans(); 
		
		$zfbpay_update_result_str  =D('Zfbpay')->updateOneBar_ZfbShouruMoney_Bymaxid();
		if($zfbpay_update_result_str===false)
		{
			$result = false;
		}
		else
		{
			$sendstr=$zfbpay_update_result_str.';';
		}  
		
		$wxpay_update_result_str   =D('Wxpay') ->updateOneBar_WxShouruMoney_Bymaxid();
		
		if($wxpay_update_result_str===false)
		{
			$result = false;
		}
		else
		{
			$sendstr.=$wxpay_update_result_str.';';
		}  
		  
		$gzhpay_update_result_str  =D('Gzhpay')->updateOneBar_GzhShouruMoney_Bymaxid();
		
		
			
	    if($gzhpay_update_result_str===false)
		{
			$result = false;
		}
		else
		{
			$sendstr.=$gzhpay_update_result_str.';';
		} 
			
		//更新下总的收入金额
		$zfb_money =D('Tixian')->where(array('wbid'=>session('wbid')))->getField('sum_zfb_in');
		$gzh_money =D('Tixian')->where(array('wbid'=>session('wbid')))->getField('sum_gzh_in');
		$wx_money  =D('Tixian')->where(array('wbid'=>session('wbid')))->getField('sum_wx_in');
		
		$sum_je= $zfb_money+ $gzh_money+ $wx_money;
		
		if(D('Tixian')->where(array('wbid'=>session('wbid')))->setField('sum_je',$sum_je)===false)
		{
			$result = false;
		}	
		
					
		if($result)
		{
		  D()->commit();  //提交事务  
		}
		else
		{
		  D()->rollback();    //回滚

		}		
            
	   //所有总额   

       	
              
        //查询请求提现表,获取已提和待提取的总额     
        $yi_tx_je   =D('TxQueryInfo')->getQueryTxSumJe($wbid,1);
        $dai_tx_je  =D('TxQueryInfo')->getQueryTxSumJe($wbid,2);
        $sum_qqtx_je=D('TxQueryInfo')->getQueryTxSumJe($wbid,0); //总请求金额
        
        $txjeinfo=D('Tixian')->getOneTxJe($wbid);   	
        $money=array();
		$money['sum_zfb_in']=D('Zfbpay')->where(array('wbid'=>$wbid,'trade_status'=>100))->sum('receipt_amount');
		$money['sum_wx_in'] =D('Wxpay')->where(array('wbid'=>$wbid,'trade_status'=>100))->sum('receipt_amount');
		$money['sum_gzh_in'] =D('Gzhpay')->where(array('wbid'=>$wbid,'trade_status'=>100))->sum('notify_total_fee');
        $money['sum_je']     =$money['sum_zfb_in']+$money['sum_wx_in']+$money['sum_gzh_in']; 
        $money['sum_qq_je']  =$sum_qqtx_je; 
        $money['sum_dai_je'] =$dai_tx_je;
        $money['sum_ytx_je'] =$yi_tx_je;
        $money['sum_ky_je']  =$money['sum_je']-$money['sum_qq_je'];

        foreach($money as $key => $value)   
        {  
          $money[$key] =sprintf("%.2f",$value);  
        } 
	
	
	    $post_data=array();
		$post_data['wbid']=$wbid;
		$daili_url_zong=C('DAILI_URL_ZONG');
		
	    $url= $daili_url_zong.'/index.php/ServerzongAPI/API_query_bar_txbankinfo3.html';
		$res= sendRequsttoOneServer($url, $post_data,30);															
		//$res= substr($res, 3);				
		$res2=json_decode($res,true);		
		if($res2['result']==1)
		{
		   $this->assign('shenhe_status',2);     	   
		}

		 
        $post_data=array();
		$post_data['wbid']=$wbid;
	    $url= $daili_url_zong.'/index.php/ServerzongAPI/API_query_bar_txbankinfo4.html';
		$res= sendRequsttoOneServer($url, $post_data,30);	
		
		//$res= substr($res, 1);		
	
		$res2=json_decode($res,true);	
		$bankinfo=$res2['bankinfo'];
		$bankinfo['bankcardno']=substr($bankinfo['bankcardno'],-4); 
			
		
		$this->assign('daili_url_zong',$daili_url_zong);	
        $this->assign('money',$money);    // 可用总额      
        $this->assign('bankinfo',$bankinfo);
		
		
		$allpay_qx=D('WbInfo')->where(array('WBID'=>session('wbid')))->getField('allpay_qx');
	   if($allpay_qx==2)
	   {
		 $allpay_qx=2;
					
	   }else
       {
		   $allpay_qx=1;
	   } 
		$this->assign('allpay_qx',$allpay_qx);
		
		$wxid=D('WbInfo')->where(array("WBID"=>session('wbid')))->getField("wxid");
		if(empty($wxid))
		{ 
			$txshenhe=1;    //需要上传微信或者支付宝
		}
		else
		{
			$txshenhe=0;
		}
		
		$this->assign('txshenhe',$txshenhe);
		
		creatToken();	
        $this->display();       
    }
	
	
	public function wxmessage()
    {
    	$wbid=session('wbid');

    	$wbaccount=D('WbInfo')->where(array('WBID'=>$wbid))->getField('WbAccount');
        $url= 'http://zhifu.wbzzsf.com/test2/bgs_oauth2.php?canshu='.$wbaccount;
	    $url=urlencode($url);	
	    $wxurl='https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxa62f41fd154d8da7&redirect_uri='.$url.'&response_type=code&scope=snsapi_base&state=getopenid#wechat_redirect';
	    $this->assign('wxurl',$wxurl); 
	    $this->assign('wbaccount',$wbaccount); 
	    $this->display(); 
    }
		
	
}
