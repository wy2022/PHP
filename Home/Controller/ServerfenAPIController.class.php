<?php
namespace Home\Controller;
use Think\Controller;
class ServerfenAPIController extends Controller
{
	/*
 public function  changetime()
  {
	  $map=array();
	  $map['WBID']=2166;
	  
	  $atime='2018-03-05 11:19:37';
	  
	  $data['EndTime']=date('Y-m-d H:i:s',strtotime($atime));
	 $res= D('WbInfo')->where($map)->save($data);
	 echo $res;
	  
  }
  */
  
    //代理商重置一个网吧的密码
    public  function API_password_reset()
	{	
		header('Access-Control-Allow-Origin:*');
		$recv_data_array=I('post.'); 		
		$wbid= $recv_data_array['WBID'];		
		$newpassword= md5('123456hc');  				   
	    if(!empty($wbid))
	    {		             
		    $bar_update_result= D('WbInfo')->where(array('WBID'=>$wbid))->setField('PassWord',$newpassword);	            						
		    if(!empty($bar_update_result))
		    {			    
				$aTempsql= D('WbInfo')->getLastSql();
				$sendstr.= $aTempsql.';';									
			    $res3 =PostTopUpdateDataToWb_lzmByWbid_fenserver($wbid,'Php_To_Top_Sql',$sendstr);
				if(!empty($res3))
				{ 					
					$cmdtype='Qt_Type';
					$data['Cmd']=5;
					$data['Tem_Type']='Tem_NotInDb';
					$data['Guid']=create_guid1();

					$jsondata=$data;
					$res1=PostTopDataToWb_lzm($wbid,$cmdtype,$jsondata);  
					  
					if(!empty($res1))
					{						
						$data['result']=1; 						
					}
					else
					{					
						$data['result']=-1;  
						//writelog('wbid='.$wbid.'重置 密码  命令已发送失败','commonlog');
					} 
			    }
			    else
			    {
				  //writelog($wbid.'修改 密码  命令已发送失败','commonlog');
				  $data['result']=-1;  
			    } 
							 									  		 
		   }
		   else
		   {
			   
			  $data['result']=-1;
		   }				  
		  
	    }
	    else
	    {
		   $data['result']=-1;
	    }	   		
		$this->ajaxReturn($data);	   								 				  
	}
	
	
	
	//修改一个网吧的所有支付权限
	public  function API_payqx_edit()
	{	
		header('Access-Control-Allow-Origin:*');
		$recv_data_array=I('post.'); 		
		$wbid= $recv_data_array['WBID']; 
        $allpay_qx= $recv_data_array['allpay_qx'];		
	    if(!empty($wbid))
	    {				
		    $bar_update_result= D('WbInfo')->where(array('WBID'=>$wbid))->setField('allpay_qx',$allpay_qx);	  
		    if(!empty($bar_update_result))
		    {			  											      		
				$data['result']=1;  			      									  		 
		   }
		   else
		   {
			  $data['result']=-1;
		   }				  
		  
	    }
	    else
	    {
		   $data['result']=-1;
	    }	   		
		$this->ajaxReturn($data);	   								 				  
	}
	
	//代理商清空一个网吧的mac
	public  function API_mac_delete()
	{	
		header('Access-Control-Allow-Origin:*');
		$recv_data_array=I('post.'); 		
		$wbid= $recv_data_array['WBID']; 	
	    if(!empty($wbid))
	    {				
		    $bar_update_result= D('WbInfo')->where(array('WBID'=>$wbid))->setField('Mac','');	  
		    if(!empty($bar_update_result))
		    {
			  
				$aTempsql= D('WbInfo')->getLastSql();
				$sendstr.= $aTempsql.';';											      		 
								
			    $res3 =PostTopUpdateDataToWb_lzmByWbid_fenserver($wbid,'Php_To_Top_Sql',$sendstr);
				if(!empty($res3))
				{ 					
					$cmdtype='Qt_Type';
					$data['Cmd']=5;
					$data['Tem_Type']='Tem_NotInDb';
					$data['Guid']=create_guid1();

					$jsondata=$data;
					$res1=PostTopDataToWb_lzm($wbid,$cmdtype,$jsondata);  
					  
					if(!empty($res1))
					{						
						$data['result']=1; 
						writelog($wbid.'重置 API_mac_delete  命令已发送成功'.$aTempsql,'commonlog');
					}
					else
					{
						$data['result']=-1;  
						writelog($wbid.'重置 API_mac_delete  命令已发送失败','commonlog');
					} 
			    }
			    else
			    {
				  writelog($wbid.'修改 API_mac_delete  命令已发送失败','commonlog');
				  $data['result']=-1;  
			    }  									  		 
		   }
		   else
		   {
			  $data['result']=-1;
		   }				  
		  
	    }
	    else
	    {
		   $data['result']=-1;
	    }	   		
		$this->ajaxReturn($data);	   								 				  
	}
   
     public  function API_querygoodsinfo()
	{	
	    header('Access-Control-Allow-Origin:*');
		$wbaccount=I('post.wbaccount','','string');		
       		
		$wbid=D('WbInfo')->where(array('WbAccount'=>$wbaccount))->getField('WBID');

		if(empty($wbid))
		{
			$data['result']=-1;
			$data['message']='无权限';
		}
		else
        {
		  $map['info.wbid']=$wbid;
		  $data=D('Productkc')->getAllChuhuokucunfoListByMap2($map);
		}			
		echo  json_encode($data);
									 				  
	}
	
	public  function test()
	{
		//echo  11;
		$wbid=1997;
		
		echo   D('WbInfo')->getLastSql();
	}
	
	//请求吧台购买商品权限
    public  function API_querybuygoods_qx_bt()
	{	
	    
	    header('Access-Control-Allow-Origin:*');
		$wbid=I('post.wbid','','string');		
	//	writelog($wbid,'qxbt');
		//$SmLx=D('WbInfo')->where(array('WBID'=>$wbid))->getField('SmLx');
        $barinfo=D('WbInfo')->field('WBID,SmLx,allpay_qx')->where(array('WBID'=>$wbid))->find();	

//writelog('----1----','qxbt');		
		
		$SmLx=$barinfo['SmLx'];
		$allpay_qx=$barinfo['allpay_qx'];
		if(empty($SmLx) || $SmLx==0)
		{
			$SmLx=0;
		}
		else
        {
			$SmLx=1;
		}	
		//writelog('----2----','qxbt');	
		
		if(empty($allpay_qx) || $allpay_qx==0)
		{
			$allpay_qx=0;
		}	
		
		

		
        if(empty($wbid))
        {
			$data['result']=-1;
			$data['bt_sp_qx']=1;
			$this->ajaxReturn($data);
			return;
		}			
       	$map=array();	
		$map['skey']='bt_sp_buy'; 
        $map['wbid']=$wbid;						
		$bt_sp_qx=D('Webini')->where($map)->getField('svalue');
		if($bt_sp_qx ==1)
		{
			$bt_sp_qx=1;
		}
		else
        {
			$bt_sp_qx=0;
		}
		$map=array();
		$map['skey']='bt_chongzhi_qx'; 
        $map['wbid']=$wbid;						
		$bfind=D('Webini')->where($map)->find();
		if(empty($bfind))
		{
			$bt_chongzhi_qx=1; 
		}
		else
        {
			$bt_chongzhi_qx=$bfind['svalue'];
			if($bt_chongzhi_qx==1)
			{
				$bt_chongzhi_qx=1;
			}else
            {
				$bt_chongzhi_qx=0;
			}							
		}
		//writelog('----3----','qxbt');	
		$map=array();
		$map['skey']='khd_shangji_qx'; 
        $map['wbid']=$wbid;						
		$bfind=D('Webini')->where($map)->find();
		if(empty($bfind))
		{
			$khd_shangji_qx=1; 
		}
		else
        {
			$khd_shangji_qx=$bfind['svalue'];
			if($khd_shangji_qx==1)
			{
				$khd_shangji_qx=1;
			}else
            {
				$khd_shangji_qx=0;
			}							
		}
		
		$data['result']=1;
		$data['bt_sp_qx']=$bt_sp_qx;
		$data['bt_chongzhi_qx']=$bt_chongzhi_qx;
        $data['SmLx']=$SmLx;
        $data['khd_shangji_qx']=$khd_shangji_qx;
        $data['allpay_qx']=$allpay_qx;		
		//writelog('----4----','qxbt');	
		$this->ajaxReturn($data);
									 				  
	}
	
	
	

   	//请求吧台购买商品权限
    public  function API_querybuygoods_qx_bt2()
	{	
	    header('Access-Control-Allow-Origin:*');
		$wbid=I('post.wbid','','string');	
		$cpname=I('post.cpname','','string');
		
		if(empty($wbid) || empty($cpname))
        {
			$data['result']=-1;
			$this->ajaxReturn($data);
			return;
		}
		
		
		$groupguid=D('Computerlist')->where(array('WB_ID'=>$wbid,'Name'=>$cpname))->getField('GroupNameGuid');		
		if(empty($groupguid) )
        {
			$data['result']=-1;
			$this->ajaxReturn($data);
			return;
		}
		
		$rate_str= D('Grouplist')->where(array('WB_ID'=>$wbid,'Guid'=>$groupguid))->getField('FlList');
		$rate_array=json_decode($rate_str,true);
		foreach($rate_array as &$val)
		{
			if($val['guid']=='{92461B99-8E93-480A-8D74-83784E4B4E3A}')
			{
				$m_StarPrice=$val['m_StarPrice'];
				break;
			}	
		}			
		$wbinfo=D('WbInfo')->field('SmLx,WH_Status')->where(array('WBID'=>$wbid))->find();		
		if(empty($wbinfo))
		{
			$SmLx=0;
            $WH_Status=0;				
		}
		else
        {
			if($wbinfo['SmLx']==0)
			{
				$SmLx=0;
			}
			else
            {
				$SmLx=1;	
			}
            
			if($wbinfo['WH_Status']==2)
			{
				$WH_Status=1;
			}
			else
            {
				$WH_Status=0;	
			}

			
		}
		     
		if(empty($m_StarPrice))
		{
			$m_StarPrice=0;
		}
		else
        {
			$m_StarPrice=sprintf("%.2f", $m_StarPrice); 
		}			
		
		$map=array();
		$map['skey']='bt_chongzhi_qx'; 
        $map['wbid']=$wbid;						
		$bfind=D('Webini')->where($map)->find();
		if(empty($bfind))
		{
			$bt_chongzhi_qx=1; 
		}
		else
        {
			$bt_chongzhi_qx=$bfind['svalue'];
			if($bt_chongzhi_qx==1)
			{
				$bt_chongzhi_qx=1;
			}else
            {
				$bt_chongzhi_qx=0;
			}							
		}
		
		$map=array();
		$map['skey']='khd_shangji_qx'; 
        $map['wbid']=$wbid;						
		$bfind=D('Webini')->where($map)->find();
		if(empty($bfind))
		{
			$khd_shangji_qx=1; 
		}
		else
        {
			$khd_shangji_qx=$bfind['svalue'];
			if($khd_shangji_qx==1)
			{
				$khd_shangji_qx=1;
			}else
            {
				$khd_shangji_qx=0;
			}							
		}
		
		$data['result']=1;
		$data['bt_chongzhi_qx']=$bt_chongzhi_qx;
        $data['SmLx']=$SmLx;
        $data['khd_shangji_qx']=$khd_shangji_qx;		
		$data['m_StarPrice']=$m_StarPrice;	
		$data['WH_Status']=$WH_Status;	
		
		$this->ajaxReturn($data);
									 				  
	}	
	
	
	public  function API_query_oneagentinfo()
	{	
		header('Access-Control-Allow-Origin:*');

		$page = I('get.page',1,'int');
		$rows = I('get.rows',20,'int');
		$sord = I('get.sord','','string')?:'asc';
		$sidx = I('get.sidx','','string')?:'dtInsertTime';

		$agent_name    = I('get.agent_name','','string');		
		$agent_name='admin';					
		$map = array(); 
		if(!empty($agent_name))
		{
			$map['agent_name']=$agent_name;
			$count= D('Agent')->getAgentListByCommonqx_count($map);
			$sql_page=ceil($count/$rows);  
			if($page>$sql_page) $page=1;	
			$wblist = D('Agent')->getAgentListByCommonqx($map,"$sidx $sord",$page,$rows);	
		}
			
		$response = new \stdClass();
		$response->records = $wblist['count'];
		$response->page = $page;
		$response->total = ceil($wblist['count'] / $rows);
		foreach($wblist['list'] as $key => $value)
		{       
		  $response->rows[$key]['id'] = $key;
		  $response->rows[$key]['cell'] = $value;
		}
		
		$this->ajaxReturn($response);	   								 				  
	}
	
		
	public  function API_bar_edit()
	{	
		header('Access-Control-Allow-Origin:*');
		$recv_data_array=I('post.'); 		
		$wbid= $recv_data_array['WBID'];
				
		$bar_update_data['province']=$recv_data_array['province'];
		$bar_update_data['city']=$recv_data_array['city'];
		$bar_update_data['area']=$recv_data_array['area'];		  
		$bar_update_data['WbName']=$recv_data_array['WbName'];
		$bar_update_data['addr']=$recv_data_array['addr'];
		$bar_update_data['WBManager']=$recv_data_array['WBManager'];
		$bar_update_data['WBTel']=$recv_data_array['WBTel'];		  
		$bar_update_data['EMail']=$recv_data_array['EMail'];
		$bar_update_data['Card']=$recv_data_array['Card'];		
	    if(!empty($wbid))
	    {				
	        
		    $bar_update_result= D('WbInfo')->where(array('WBID'=>$wbid))->save($bar_update_data);	             			
		    if(!empty($bar_update_result))
		    {
							  
				$aTempsql= D('Bar')->getLastSql();
				$sendstr.= $aTempsql.';';											      		 
				$res =PostTopUpdateDataToWb_lzmByWbid($wbid ,'Php_To_Top_Sql',$sendstr);
				
			    if(!empty($res))
			    {
				 $data['result']=1;  
				 writelog($wbid.'edit API_bar_edit  命令已发送成功','commonlog');
			    }
			    else
			    {
				  $data['result']=-1;  
				 writelog($wbid.'edit API_bar_edit  命令已发送失败','commonlog');
			    }  									  		 
		   }
		   else
		   {
			  $data['result']=-1;
		   }				  
		  
	    }
	    else
	    {
		   $data['result']=-1;
	    }	   		
		$this->ajaxReturn($data);	   								 				  
	}
	
	
	
    public  function API_barchongzhi_edit()
	{	
		header('Access-Control-Allow-Origin:*');
		$recv_data_array=I('post.'); 		
		$wbid= $recv_data_array['WBID'];
		
		
		$bar_update_data['EndTime']=$recv_data_array['EndTime'];
		$bar_update_data['CpCount']=$recv_data_array['CpCount'];
        $bar_update_data['bz']=$recv_data_array['bz'];
			
	    if(!empty($wbid))
	    {				
	        $result=true;
            D()->startTrans();
			
		    if(D('WbInfo')->where(array('WBID'=>$wbid))->save($bar_update_data)===false)
            {
				$result=false;
			} 				
			$aTempsql= D('WbInfo')->getLastSql();	
			$sendstr.=$aTempsql.';';
			
			$LzmWbChange_insert_data=array();
		    $LzmWbChange_insert_data['WB_id']=$wbid;
			$LzmWbChange_insert_data['WbInfo_Tag']=1;
						
			if(D('LzmWbChange')->add($LzmWbChange_insert_data)===false)
			{
				$result=false;
			}
					
		    if($result)
		    {	
		         D()->commit();
				 $data['result']=1;
		   }
		   else
		   {
			   D()->rollback();
			  $data['result']=-1;
		   }				  
		  
	    }
	    else
	    {
		   $data['result']=-1;
	    }	   		
		$this->ajaxReturn($data);	   								 				  
	}
	
	
	
    public function query_bangding_set()
	{		
	   header('Access-Control-Allow-Origin:*');  
	   $wbid=I('post.wbid','','string');    
	   $agent_id=I('post.agent_id','','string'); 		
	   $bangding_insert_data['wbid']=$wbid;
	   $bangding_insert_data['agent_id']=$agent_id;
	   $bangding_insert_data['dtInsertTime']=date('Y-m-d H:i:s ',time());
	   $bangding_insert_data['bing_status']=2;			       
	   $agent_bangding_result=D('Bangding')->add($bangding_insert_data);
	  
	   if(!empty($agent_bangding_result))
	   {               
		 $data['status']=1;
	   }
	   else
	   {
		$data['status']=0;
	   }	

	  $this->ajaxReturn($data);             	  
	}
	
	public function API_query_txquery_set()
	{		
	   header('Access-Control-Allow-Origin:*'); 	   
	    $wbid = I('post.wbid','','string');
		$orderno = I('post.orderno','','string');
        $tx_status=I('post.tx_status','','string');  
		$paytype=I('post.paytype','','string');
		
		$map=array();
		$map['wbid']=$wbid;
		$map['orderno']=$orderno;
		
		$tx_update_data['time_end']=date('Y-m-d H:i:s',time()); 
        $tx_update_data['tx_status']=$tx_status; 
        $tx_update_data['paytype']=$paytype; 
	   
		       
	   $tx_update_result= D('TxQueryInfo')->where($map)->save($tx_update_data); ;
	  
	   if(!empty($tx_update_result))
	   {               
		 $data['result']=1;
	   }
	   else
	   {
		$data['result']=-1;
	   }	

	  $this->ajaxReturn($data);             	  
	}
	
	//===========================公众号API===============
	public function API_query_barlist()
	{		

	   header('Access-Control-Allow-Origin:*'); 	   
	    $hycardno = I('post.hycardno','','string');
		$password = I('post.password','','string');
		
		$_MDBMask= '!@#BGS159357';
        $password=md5($password.$_MDBMask);
		
		$map=array();
		$map['hyCardNo']=$hycardno;
		$map['pw']=$password;
		
		$hylist=D('HyInfo')->Field('hyCardNo,pw,WB_ID')->where($map)->select();						
        foreach($hylist as &$val)
        {
			$val['WbName']=D('WbInfo')->where(array('WBID'=>$val['WB_ID']))->getField('WbName');
		}		
		 	    		
	   if(!empty($hylist))
	   {               
		 $data['result']=1;
		 $data['body']=$hylist;
	   }
	   else
	   {
		$data['result']=-1;
	   }	

	  $this->ajaxReturn($data);             	  
	}
	
	
	public function API_query_verifyHyinfo()
	{		
	    header('Access-Control-Allow-Origin:*'); 	   
	    $hycardno = I('post.hycardno','','string');
		$password = I('post.password','','string');
		$wbid     = I('post.wbid','','string');
		
		$_MDBMask= '!@#BGS159357';
        $password=md5($password.$_MDBMask);
		
		$map=array();
		$map['hyCardNo']=$hycardno;
		$map['pw']=$password;
		$map['WB_ID']=$wbid;
		
		$hyinfo=D('HyInfo')->where($map)->find();	 	   
	    if(!empty($hyinfo))
	    {               
		  $data['result']=1;
	    }
	    else
	    {
		  $data['result']=-1;
	    }	
	  $this->ajaxReturn($data);             	  
	}
	
	//查询会员在线状态
	public function API_query_hyonlnestatus()
	{		
	    header('Access-Control-Allow-Origin:*'); 	   
	    $hycardno = I('post.hycardno','','string');	
		$wbid = I('post.wbid','','string');
		

		
		$map=array();
		$map['hyCardNo']=$hycardno;
		$map['WB_ID']=$wbid;
		
		$hyinfo=D('HyInfo')->Field('WB_ID,hyCardNo,OnlineState,pw')->where($map)->find();	 	    		
	    if(!empty($hyinfo))
	    {               
		  $data['result']=1;
		  $data['body']=$hyinfo;
	    }
	    else
	    {
		 $data['result']=-1;
	    }	

	  $this->ajaxReturn($data);             	  
	}
	
	//查询一个会员的信息
	public function API_query_onehyinfo()
	{		
	    header('Access-Control-Allow-Origin:*'); 	   
	    $hycardno = I('post.hycardno','','string');	
		$wbid = I('post.wbid','','string');
		
		$map=array();
		$map['hyCardNo']=$hycardno;
		$map['WB_ID']=$wbid;
		
		$hyinfo=D('HyInfo')->where($map)->find();	
        $hyinfo['ye']=$hyinfo['Jlje']+$hyinfo['surplus'];  	
        $hyinfo['WbName']=D('WbInfo')->where(array('WBID'=>$wbid))->getField('WbName');  			
	    if(!empty($hyinfo))
	    {               
		  $data['result']=1;
		  $data['body']=$hyinfo;
	    }
	    else
	    {
		 $data['result']=-1;
	    }	

	  $this->ajaxReturn($data);             	  
	}
	
	public function API_make_onehy_xiaji()
	{		
	    header('Access-Control-Allow-Origin:*'); 	   
	    $hycardno = I('post.hycardno','','string');	
		$wbid = I('post.wbid','','string');
		$jsonChild=array();
		$jsonChild['HyCardNo']=$hycardno;
		
		$jsonFather=array();
		$jsonFather['Guid']=getGuid();
		$jsonFather['Cmd']=3;
		$jsonFather['Data']=$jsonChild;
	
		$res=PostGzhDataToWb_lzm($wbid,$jsonFather);			
	    if(!empty($res))
	    {               
		  $data['result']=1;
	    }
	    else
	    {
		 $data['result']=-1;
	    }	

	  $this->ajaxReturn($data);             	  
	}
	
	//查询一个会员的充值信息
	public function API_query_onehy_gzh_orderinfo()
	{		
	    header('Access-Control-Allow-Origin:*'); 	   
	
		$wbid     = I('post.wbid','','string');   
        $wxid     = I('post.wxid','','string');   
        $page     = I('post.hiddenNowPage',1,'int'); 
		
        $map=array();
        $map['wbid']=$wbid;
        $map['wxid']=$wxid;
        $map['trade_status']=100; 
		$map['xiaofei_lx']=1;

        $count=D('Gzhpay')->getGzhPaycount($map);
   
        $rows=10;
        $sql_page=ceil($count/$rows);   
        
        if($page<=0)   $page=1;       
        if($page>$sql_page) 
        {
          $page=1; 
        }
        else
        {

        }      
        $gzhpaydata=D('Gzhpay')->fnQueryOrderList($map,$page,$rows);
		
        $response = new \stdClass();
        $response->count       = $gzhpaydata['count'];//返回的数组的第一个字段记录总条数
        $response->nowPage     = $page ;              //每页显示的记录数目               
        $response->total       = ceil($gzhpaydata['count'] / $rows);          
          
        foreach ($gzhpaydata['list'] as &$val)
        {                             
          $val['time_end']= date('Y-m-d H:i:s',strtotime($val['time_end']));  
          $val['notify_total_fee']= sprintf("%.2f", $val['notify_total_fee']);      
        }

        $response->rows   = $gzhpaydata['list'] ;
		
	    if(!empty($gzhpaydata))
	    {               
		  $data['result']=1;
		  $data['body']=$response;
	    }
	    else
	    {
		 $data['result']=-1;
	    }	

	  $this->ajaxReturn($data);             	  
	}
	
	public function API_make_onehy_shangji()
	{		
	    header('Access-Control-Allow-Origin:*'); 	   
	    $pcname = I('post.CpName','','string');	
		$wbid = I('post.wbid','','string');
		$hycardno = I('post.HyCardNo','','string');
		$password = I('post.HyPw','','string');
				
		$jsonChild=array('CpName'=>$pcname,'HyCardNo' =>$hycardno,'HyPw'=>$password);	
		$jsonFather=array();
		$jsonFather['Guid']=getGuid();
		$jsonFather['Cmd']=1;
		$jsonFather['Data']=$jsonChild;
		
		
		$res=PostGzhDataToWb_lzm($wbid,$jsonFather);		
	    if(!empty($res))
	    {               
		  $data['result']=1;
	    }
	    else
	    {
		 $data['result']=-1;
	    }	

	  $this->ajaxReturn($data);             	  
	}
	
	
		//查询一个网吧的奖励计划信息
	public function API_query_onebar_jljh1()
	{		
	    header('Access-Control-Allow-Origin:*'); 	   
	    $hycardno = I('post.hycardno','','string');	
		$wbid = I('post.wbid','','string');
					
        $response=D('HyJl')->getMoneyRecordsbyWbIdAndHyCardNo($wbid,$hycardno); 		
	    if(!empty($response))
	    {               
		  $data['result']=1;
		  $data['body']=$response;
	    }
	    else
	    {
		 $data['result']=-1;
	    }	

	  $this->ajaxReturn($data);             	  
	}
	
	public function API_query_onebar_jljh2()
	{		
	    header('Access-Control-Allow-Origin:*'); 	   
	    $wbid     = I('post.wbid',0,'int'); 
        $hycardno = I('post.hycardno','','string'); 
        $je       = I('post.je','','string'); 
    
        $response=D('HyJl')->getMoneyRecordsbyWbIdAndHyCardNoAndJe($wbid,$hycardno,$je);							
	    if(!empty($response))
	    {               
		  $data['result']=1;
		  $data['body']=$response;
	    }
	    else
	    {
		 $data['result']=-1;
	    }	

	  $this->ajaxReturn($data);             	  
	}
	

	public function API_query_onebar_gzhqx()
	{		
	    header('Access-Control-Allow-Origin:*'); 	   
		$wbid = I('post.wbid','','string');	
		$map=array();
		$map['WBID']=$wbid;	
        $barinfo=D('WbInfo')->Field('WBID,isValid,VerNo,WH_Status')->where($map)->find();		
	    if(!empty($barinfo))
	    {               
		  $data['result']=1;
		  $data['body']=$barinfo;
	    }
	    else
	    {
		 $data['result']=-1;
	    }	
      
	  $this->ajaxReturn($data);             	  
	}	
	
		
	public function API_make_onehy_gzh_chongzhi()
	{		
	    header('Access-Control-Allow-Origin:*'); 	   
	    $aa = I('post.aa','','string');	
		$orderinfo=base64_decode($aa);
		$orderinfo=json_decode($orderinfo,true);
		
		
		/*
		$order_insert_data=array();	
        $order_insert_data['post_order_no']=$orderinfo['post_order_no'];
		
        $order_insert_data['wbid']=$orderinfo['wbid'];	
        $order_insert_data['wxid']=$orderinfo['wxid'];
        $order_insert_data['body']=$orderinfo['body'];		
		$order_insert_data['transaction_id']=$orderinfo['transaction_id'];
        $order_insert_data['post_total_fee']=$orderinfo['post_total_fee'];		
		$order_insert_data['notify_total_fee']=$orderinfo['notify_total_fee'];				
		$order_insert_data['trade_type']='JSAPI';		
		$order_insert_data['return_code']='SUCCESS';
		$order_insert_data['result_code']='SUCCESS';				
		$order_insert_data['trade_status']=100;
		$order_insert_data['cancel_status']=0;
		$order_insert_data['refund_status']=0;
        $order_insert_data['time_post']=$orderinfo['time_post'];		
		$order_insert_data['time_end']=date('Y-m-d H:i:s',time());					
		$order_insert_data['txflag']=0;
		$order_insert_data['syid']=$orderinfo['syid'];			
		$order_insert_data['send_flag']=1;
			
	    $order_insert_result =D('Gzhpay')->add($order_insert_data);
		if($order_insert_result)
		{
			$data['result']=1;
			$this->ajaxReturn($data);
			return;
		}	
		*/
		
		
	   
	    $wbid= $orderinfo['wbid'];
		
		$guid=$orderinfo['guid'];
		
		$order_pay_status=D('Gzhpay')->where(array('wbid'=>$orderinfo['wbid'],'post_order_no'=>$orderinfo['post_order_no']))->getField('trade_status');
		if($order_pay_status==100)
		{
			writelog('订单状态已更改，不在推送数据','gzh_notify');
		   $data['result']=-1;
		   return;
		}
		
		
		//向总服务器发送请求，判断是否已发送过该数据
		$map=array();
		$map['WB_ID']   =$orderinfo['wbid'];
		$map['hyCardNo']=$orderinfo['hycardno'];
		
		$hycardinfo= D('HyInfo')->where($map)->find();		
	    $hyCardGuid=$hycardinfo['hyCardGuid'];
		if(empty($hyCardGuid))
		{
		   writelog('获取网吧hyCardGuid数据错误','gzh_notify');
		   $data['result']=-1;
		   return;
		}
		
		$wbversioninfo=D('WbInfo')->where(array('WBID'=>$wbid))->getField('VerNo');
		if($wbversioninfo <=28)
		{
			writelog('获取网吧版本低于28,无法推送消息','gzh_notify');
			$data['result']=-1;
			return;	
		}	

		
		
		
		$result=true;
        D()->startTrans();		
		$guid= getGuid();
		$guid='ght_gzh_cz_'.$guid;
								
		//插入订单数据		
		
		$order_update_data=array();		
		$order_update_data['transaction_id']=$orderinfo['transaction_id'];	
		$order_update_data['notify_total_fee']=$orderinfo['notify_total_fee'];				
		$order_update_data['trade_type']='JSAPI';		
		$order_update_data['return_code']='SUCCESS';
		$order_update_data['result_code']='SUCCESS';				
		$order_update_data['trade_status']=100;
		$order_update_data['cancel_status']=0;
		$order_update_data['refund_status']=0;				
		$order_update_data['time_end']=date('Y-m-d H:i:s',time());					
		$order_update_data['txflag']=0;
		$order_update_data['syid']=$orderinfo['syid'];			
		$order_update_data['send_flag']=1;

		
	    if(D('Gzhpay')->where(array('wbid'=>$orderinfo['wbid'],'post_order_no'=>$orderinfo['post_order_no']))->save($order_update_data)===false)
		{
			$result=false;
			writelog('----3-1 error----','gzh_notify');
		}	
			
			
		$jsonChild=array();
		$jsonChild['HyCardNo']=$orderinfo['hycardno'];
		$jsonChild['HyCardGuid']=$hyCardGuid;
		$jsonChild['QtZfNo']=$orderinfo['post_order_no'];
		
		
		$chongzhiinfo=$orderinfo['note'];
		$chongzhiinfo=json_decode($chongzhiinfo,true);
	
		$jsonChild['ChongJe']=$chongzhiinfo['chongmoney'];
		$jsonChild['SongJe']=$chongzhiinfo['songmoney'];	
		$jsonChild['lx']=$chongzhiinfo['lx'];
		$jsonChild['fqlx']=$chongzhiinfo['fqlx'];
		$jsonChild['fqje']=$chongzhiinfo['fqje'];
		$jsonChild['fqcount']=$chongzhiinfo['fqcount'];
		$jsonChild['ZfLx']=2;         // 支付类型  钉钉 3，公众号2，没有1
			
	 
		              
			
		$jsonFather=array();
		$jsonFather['Guid']=$guid;
		$jsonFather['Cmd']=2;
		$jsonFather['Data']=$jsonChild;
		$jsonFather['ExeInsert']='No';	   
				
		$aPostData=array();
		$aPostData['Wbid']=$wbid;
		$aPostData['Topic']='a';
		$aPostData['Data']=$jsonFather;
		$aPostData['MessageTime']=date('Y-m-d H:i:s',time());
		$aPostData['MessageID']=$guid;
					
		$LzmPhpMessageTable_insert_data=array();
		$LzmPhpMessageTable_insert_data['aGuid']= $guid;
		$LzmPhpMessageTable_insert_data['wb_id']= $wbid;
		$LzmPhpMessageTable_insert_data['Nr']=stripslashes(json_encode($aPostData));
				  
						
		if(D('Lzmphpmessage')->add($LzmPhpMessageTable_insert_data)===false)
		{
			$result=false;
			writelog('LzmPhpMessageTable--error','gzh_notify');
		}
		

	
	    if($result)
	    {   
	    
          D()->commit();
		  $res=PostGzhDataToWb_lzm($wbid,$jsonFather);
		  if(!empty($res))
		  {
			  writelog('消息已推送--','gzh_notify');
		  }else
          {
			writelog('消息未推送--','gzh_notify');  
		  }			  
		   
		  
		  $data['result']=1;
	    }
	    else
	    {
		  D()->rollback();	
		  $data['result']=-1;
	    }	

	  $this->ajaxReturn($data);             	  
	}
	
	//请求获取网吧的状态，在线数量，最后登录时间  版本等信息
	public  function API_query_allbar_accountlist()
	{	

	    header('Access-Control-Allow-Origin:*');	
		$wblist=D('WbInfo')->Field('WBID,VerNo,WH_Status,LastDateTime')->select();
		if(!empty($wblist))
		{
			$data['result']=1;
			$data['body']=$wblist;
		}
		else
        {
		  $data['result']=1;
		}			
		 $this->ajaxReturn($data); 									 				  
	} 
	
	// ============================小程序专用接口=============================================================
	public  function API_query_onebarinfo()
	{	
	    header('Access-Control-Allow-Origin:*');
        $wbaccount = I('post.wbaccount','','string');	
        $password  = I('post.password','','string');		
		
		$map=array();
		$map['WbAccount']=$wbaccount;
		$map['PassWord']=$password;
		
		$wblist=D('WbInfo')->Field('WBID,VerNo,WH_Status')->where($map)->find();
		if(!empty($wblist))
		{
			$data['result']=1;
			$data['body']=$wblist;
		}
		else
        {
		  $data['result']=1;
		}			
		 $this->ajaxReturn($data); 									 				  
	} 
	
	
	/*
	public  function API_query_allbar_jfmx()
	{	
	    header('Access-Control-Allow-Origin:*');
        $wbidlist = I('post.wbidlist','','string');		
		$wbidlist= base64_decode($wbidlist);		
		$wbidlist= json_decode($wbidlist,true);		          		
		if(!empty($wbidlist))
		{		   	     	  	
			$nowtime=date('Y-m-d 00:00:00');
			$nowtime1= strtotime($nowtime);
		 
			$k=0;
			$rateonline_array=array();
			
			$onebarje_array=array();
			$onebartime_array=array();
            $barinfo_array=array();			
			
			for($i=7;$i>0;$i--)
			{
				$last7daytime = strtotime('-'.$i.' days',$nowtime1);
				$last7daytime = date('Y-m-d 00:00:00', $last7daytime) ;
				
				$sumje=0;
				$j=0;
				foreach($wbidlist as &$val )
				{
					$onebarinfo=D('WbInfo')->where(array('WBID'=>$val['wbid']))->find();
					$wbid=$onebarinfo['WBID'];
					$wbname=$onebarinfo['WbName'];
														
					$map = array();   
					$map['Wb_Id']=$wbid;
					$onebaronlineinfo = D('Barstate')->where($map)->getField('WbState');
					$onebaronlineinfo=json_decode($onebaronlineinfo ,true);
					
					
					$onebarjelist=array();
					$map = array();   
					$map['wb_id']=$wbid;
					$map['cTime']=array('BETWEEN',array($last7daytime,$nowtime));	
					$onebarjelist = D('Tongji')->Field('Sum_Je,Xj_je,qt_Je,cTime')->where($map)->find(); 
					if(empty($onebarjelist))
					{
						$onebarjelist['Sum_Je']=0;
					}
					else
					{
						$onebarjelist['Sum_Je']= sprintf("%.2f", $onebarjelist['Sum_Je']); 
					}	
					
					$onebarje_array[$j][$k]=$onebarjelist['Sum_Je'];
					$onebartime_array[$j][$k]= date('m-d', strtotime($last7daytime));
					
					$rateonline_array[$j]['wbid']=$wbid;
					$rateonline_array[$j]['groupinfo']=$onebaronlineinfo['GroupInfo'] ;
					$rateonline_array[$j]['TemOnline']=$onebaronlineinfo['TemOnline'] ;
					$rateonline_array[$j]['HyOnline']=$onebaronlineinfo['HyOnline'] ;
					$rateonline_array[$j]['wbName']=$wbname;	
					$rateonline_array[$j]['linedata']=$onebarje_array[$j];
					$rateonline_array[$j]['linetime']=$onebartime_array[$j];
					
					
					$barinfo_array[$j]['wbName']=$wbname;
					$barinfo_array[$j]['wbid']=$wbid;
					
					$sumje= $sumje+$onebarjelist['Sum_Je'];			
					$sumje= sprintf("%.2f", $sumje); 
					$j++;
					
				}             				     								
				$money_array[$k]=(float)$sumje;
				$time_array[$k] = date('m-d', strtotime($last7daytime)) ;			                				
				$k++;							
			}	
				
			$data['status']=1;
			$data['linedata']=$money_array;
			$data['linetime']=$time_array;
			$data['rateonline']=$rateonline_array;	
			
			$data['Wb']=$barinfo_array;	
							
			$data['result']=1;

		}
		else
		{
			  $data['result']=-1;
		}			
		 $this->ajaxReturn($data); 									 				  
	} 
	*/
	
	
	public  function API_query_allbar_jfmx()
	{	
	    header('Access-Control-Allow-Origin:*');
        $wbidlist = I('post.wbidlist','','string');		
		$wbidlist= base64_decode($wbidlist);			
		$wbidlist= json_decode($wbidlist,true);		           		
		if(!empty($wbidlist))
		{		   	     	  	
			$nowtime=date('Y-m-d 00:00:00');
			$nowtime1= strtotime($nowtime);
		 
			$k=0;
			$rateonline_array=array();
			
			$onebarje_array=array();
			$onebartime_array=array();
            $barinfo_array=array();			
			
			for($i=7;$i>0;$i--)
			{
				$last7daytime = strtotime('-'.$i.' days',$nowtime1);
				$last7daytime = date('Y-m-d 00:00:00', $last7daytime) ;
				
				$sumje=0;
				$j=0;
				foreach($wbidlist as &$val )
				{
					$onebarinfo=D('WbInfo')->where(array('WBID'=>$val['wbid']))->find();
					$wbid=$onebarinfo['WBID'];
					$wbname=$onebarinfo['WbName'];
														
					$map = array();   
					$map['Wb_Id']=$wbid;
					$onebaronlineinfo = D('Barstate')->where($map)->getField('WbState');
					if(!empty($onebaronlineinfo))
					{
						$onebaronlineinfo=json_decode($onebaronlineinfo ,true);
					}
               						
					
					
					
					$onebarjelist=array();
					$map = array();   
					$map['wb_id']=$wbid;
					$map['cTime']=array('BETWEEN',array($last7daytime,$nowtime));	
					$onebarjelist = D('Tongji')->Field('Sum_Je,Xj_je,qt_Je,cTime')->where($map)->find(); 
					if(empty($onebarjelist))
					{
						$onebarjelist['Sum_Je']=0;
					}
					else
					{
						$onebarjelist['Sum_Je']= sprintf("%.2f", $onebarjelist['Sum_Je']); 
					}	
					
					$onebarje_array[$j][$k]=$onebarjelist['Sum_Je'];
					$onebartime_array[$j][$k]= date('m-d', strtotime($last7daytime));
					
					$rateonline_array[$j]['wbid']=$wbid;
					$rateonline_array[$j]['groupinfo']=$onebaronlineinfo['GroupInfo'] ;
					if(empty($onebaronlineinfo['TemOnline']))
					{
						$rateonline_array[$j]['TemOnline']=0 ;
					}
					else
                    {
						$rateonline_array[$j]['TemOnline']=$onebaronlineinfo['TemOnline'] ;
					}						
					
					if(empty($onebaronlineinfo['HyOnline']))
					{
						$rateonline_array[$j]['HyOnline']=0 ;
					}
					else
                    {
						$rateonline_array[$j]['HyOnline']=$onebaronlineinfo['HyOnline'] ;
					}
					
	
					$rateonline_array[$j]['wbName']=$wbname;	
					$rateonline_array[$j]['linedata']=$onebarje_array[$j];
					$rateonline_array[$j]['linetime']=$onebartime_array[$j];
					
					
					$barinfo_array[$j]['wbName']=$wbname;
					$barinfo_array[$j]['wbid']=$wbid;
					
					$sumje= $sumje+$onebarjelist['Sum_Je'];			
					$sumje= sprintf("%.2f", $sumje); 
					$j++;
					
				}             				     								
				$money_array[$k]=(float)$sumje;
				$time_array[$k] = date('m-d', strtotime($last7daytime)) ;			                				
				$k++;							
			}	
				
			$data['status']=1;
			$data['linedata']=$money_array;
			$data['linetime']=$time_array;
			$data['rateonline']=$rateonline_array;	
			
			$data['Wb']=$barinfo_array;	
							
			$data['result']=1;

		}
		else
		{
			  $data['result']=-1;
		}			
		 $this->ajaxReturn($data); 									 				  
	} 
	
	
	public  function API_getNowShiftShouruByWxid()
	{		   	
     	header('Access-Control-Allow-Origin:*');
        $wbidlist = I('post.wbidlist','','string');		
		$wbidlist= base64_decode($wbidlist);	
		$wbidlist= json_decode($wbidlist,true);	

        $i=0;
        foreach($wbidlist as &$val )
		{
			$wbid=$val['wbid'];
			$money_array[$i]['todayje']=getTodayShouru_zzb($wbid);
			$money_array[$i]['nowshiftje']=getNowShiftShouru_zzb($wbid);
		 		   
		   $money_array[$i]['wbid']=$wbid;	
		   $i++;
        } 			
	    
		if(!empty($money_array))
		{
			$data['status']=1;
		    $data['body']=$money_array;
		}else
        {
			$data['status']=-1;
		}									
       $this->ajaxReturn($data); 							 				  
	}
	
	
	
	public  function API_get30dayShiftByWbid()
	{		
        header('Access-Control-Allow-Origin:*');   	
        $wbid=I('post.wbid','','string');
        	
		$nowtime=date('Y-m-d H:i:s');
        $nowtime1= strtotime($nowtime);
        $last30daytime = strtotime('-30 days',$nowtime1);
        $last30daytime = date('Y-m-d H:i:s', $last30daytime) ; 
						
		$map = array();
		$map['WB_ID']=$wbid;
        $map['cTime']=array('BETWEEN',array($last30daytime,$nowtime));	
		
		$money_array = D('Shift')->
		Field(array(
		   'cName'=>'name',
		   'inje'=>'sumje',
		   'keepje'=>'keepje',
		   'YjJe'=>'yjje',
		   'QtZfJe'=>'qtje',
		   'cTime'=>'cTime'
		))	
		->where($map)->order('cTime  desc')->select(); 
		
		foreach($money_array as &$val)
		{		
			$val['cTime']= date('Y-m-d H:i:s',strtotime($val['cTime'])); 
			$val['sumje']= sprintf("%.2f", $val['sumje']);  
			$val['keepje']= sprintf("%.2f", $val['keepje']);  			
			$val['yjje']= sprintf("%.2f", $val['yjje']); 
			$val['qtje']= sprintf("%.2f", $val['qtje']); 
			$val['btje']= $val['sumje']-$val['qtje']; 
		} 		
  
	
		$data['status']=1;
		$data['body']=$money_array;
		
	
		
      echo  json_encode($data);	
  
	}
	
	
	
	public  function API_get30dayShouruByWbid()
	{	
        header('Access-Control-Allow-Origin:*');  	
        $wbid=I('post.wbid','','string');
        
		
		$nowtime=date('Y-m-d H:i:s');
        $nowtime1= strtotime($nowtime);
        $last30daytime = strtotime('-30 days',$nowtime1);
        $last30daytime = date('Y-m-d H:i:s', $last30daytime) ; 
						
		$map = array();
		$map['wb_id']=$wbid;
        $map['cTime']=array('BETWEEN',array($last30daytime,$nowtime));	
		$money_array = D('Tongji')->Field('Sum_Je,Xj_je,qt_Je,cTime')->order('cTime  desc')->where($map)->select(); 
		
		foreach($money_array as &$val)
		{		
			$val['cTime']= date('Y-m-d',strtotime($val['cTime'])); 
			$val['Sum_Je']= sprintf("%.2f", $val['Sum_Je']);  
			$val['Xj_je']= sprintf("%.2f", $val['Xj_je']);  			
			$val['qt_Je']= sprintf("%.2f", $val['qt_Je']);  
		} 		
  
	
		$data['status']=1;
		$data['body']=$money_array;
		
		
      echo  json_encode($data);							 				  
	}
	
	
	
	
	//==================以下执行本服务器的wt_zhangmu_tongji 插入备用数据接口
	/*
	public  function  API_insert_zhangmutongji()
	{
		$barlist=D('WbInfo')->select();
		
	//	echo  json_encode(D('WbInfo')->getLastSql());
		
		D()->startTrans();
		$result=true;
		
		$bill_month_beg= '2016-01-01 00:00:00';
		$bill_month_end= '2020-01-01';
		
		$bill_month_beg=strtotime($bill_month_beg);		
        $monthcount= 48;
      
		foreach($barlist as &$val)
		{
			
			for($i=0;$i<48;$i++)
			{
			    $one_bill_date=strtotime("+".$i." month ",$bill_month_beg);
	            $one_bill_date =date('Y-m',$one_bill_date);
				
				$wbid=$val['WBID'];
				$zhangmu_insert_data=array();
				$zhangmu_insert_data['wbid']=$wbid;
				$zhangmu_insert_data['month']=$one_bill_date;
				
				$zhangmu_insert_data['sumje']=0;
				$zhangmu_insert_data['wxje']=0;
				$zhangmu_insert_data['zfbje']=0;
				$zhangmu_insert_data['ddje']=0;
				$zhangmu_insert_data['gzhje']=0;			
				$zhangmu_insert_data['dtUpdateTime']=date('Y-m-d H:i:s',time());
				if(D('Zhangmu')->add($zhangmu_insert_data)===false)
				{
					$result=false;
				}	
							
			}				
		}
		if($result)
		{
			D()->commit();
			echo  1;
		}else
        {
			D()->rollback();
			echo -1;
		}
	}
	
	
	
	
	

	*/
	
	
	//统计一个月所有网吧的账目
	/*
	
	public  function  API_update_zhangmutongji_bymonth()
	{
		
		$one_bill_date=I('get.billdate','','string');	
		if(empty($one_bill_date))
		{
			echo  -2;
			return;
		}	
		D()->startTrans();
		$result=true;
		//	echo  0;
		//$one_bill_date= '2016-01';
		$barlist=D('WbInfo')->field('WBID,WbName')->select();		
		$bill_month_beg= date('Y-m-01 H:i:s',strtotime($one_bill_date));		
		$bill_month_end= date('Y-m-d H:i:s', strtotime("$bill_month_beg +1 month -1 day"));
			
		foreach($barlist as &$val)
		{								
			$wbid=$val['WBID'];
		//	echo  1;
			 
			$map=array();
			$map['wbid']=$wbid;
			$map['time_post']=array('BETWEEN',array($bill_month_beg,$bill_month_end));
			$map['trade_status']=100;
			$map['receipt_amount'] =array('gt',0);		
		    $wxje=D('Wxpay')->where($map)->sum('receipt_amount');
			if(empty($wxje))
			{
				 $wxje=0;
			}else
            {
				$wxje=sprintf("%.2f", $wxje);  
			}				
			
			//echo  2;	
           		
			$map=array();
			$map['wbid']=$wbid;
			$map['time_post']=array('BETWEEN',array($bill_month_beg,$bill_month_end));
			$map['trade_status']=100;
			$map['receipt_amount'] =array('gt',0);;		
			$zfbje=D('Zfbpay')->where($map)->sum('receipt_amount');
			if(empty($zfbje))
			{
				 $zfbje=0;
			}else
            {
				$zfbje=sprintf("%.2f", $zfbje);  
			}				
			
			$map=array();
			$map['wbid']=$wbid;
			$map['time_post']= array('BETWEEN',array($bill_month_beg,$bill_month_end));
			$map['trade_status']=100;
			$map['notify_total_fee'] =array('gt',0);		
			$gzhje=D('Gzhpay')->where($map)->sum('notify_total_fee');
			if(empty($gzhje))
			{
				 $gzhje=0;
			}else
            {
				$gzhje=sprintf("%.2f", $gzhje); 
			}				
			
		    $map=array();
			$map['wbid']=$wbid;
			$map['dtInsertTime']=array('BETWEEN',array($bill_month_beg,$bill_month_end));
			$map['chongje'] =array('gt',0);;			
			$ddje=D('Dingding')->where($map)->sum('chongje');
			if(empty($ddje))
			{
				 $ddje=0;
			}else
            {
				$ddje=sprintf("%.2f", $ddje); 
			}				
			
			$sumje= $wxje+ $zfbje +$gzhje;
			
			$zhangmu_update_data=array();
			$zhangmu_update_data['wxje']=$wxje;
			$zhangmu_update_data['zfbje']=$zfbje;
			$zhangmu_update_data['ddje']=$ddje;
			$zhangmu_update_data['gzhje']=$gzhje;
            $zhangmu_update_data['sumje']= $sumje;
			
			$zhangmu_update_data['dtUpdateTime']=date('Y-m-d H:i:s',time());
			
			if(D('Zhangmu')->where(array('wbid'=>$wbid,'month'=>$one_bill_date))->save($zhangmu_update_data)===false)
			{
				$result=false;
			}	
													
		}
		//echo  3;
		if($result)
		{
			D()->commit();
			echo  100;
		}else
        {
			D()->rollback();
			echo -100;
		}
		
	}
	
	*/
	
	/*
	public  function  API_insert_zhangmutongji()
	{

		$barlist=D('WbInfo')->select();
	
		D()->startTrans();
		$result=true;  
		foreach($barlist as &$val)
		{	
            $wbid=$val['WBID'];		
			$bFind=D('Zhangmufen')->where(array('wbid'=>$wbid))->find();
			if(empty($bFind))
			{
				$zhangmu_insert_data=array();
				$zhangmu_insert_data['wbid']=$wbid;			
				$zhangmu_insert_data['sumje']=0;
				$zhangmu_insert_data['wxje']=0;
				$zhangmu_insert_data['zfbje']=0;
				$zhangmu_insert_data['ddje']=0;
				$zhangmu_insert_data['gzhje']=0;			
				$zhangmu_insert_data['dtUpdateTime']=date('Y-m-d H:i:s',time());
				if(D('Zhangmufen')->add($zhangmu_insert_data)===false)
				{
					$result=false;
				}
			}																									
		}
		
		if($result)
		{
			D()->commit();
			echo  1;
		}else
        {
			D()->rollback();
			echo -1;
		}
	}
	
	*/
	
	
	public  function  API_update_zhangmutongji()
	{
	
		D()->startTrans();
		$result=true;  
		
		$wxlist=D('Wxpay')->field(array(
		'sum(receipt_amount)'=>'je',
		'wbid'=>'wbid'
		))->group('wbid')->where(array('trade_status'=>100))->select();
			
		foreach($wxlist as &$val)
		{					
			$wbid=$val['wbid'];
			$je= $val['je'];
			if(empty($je))
			{
				$je=0;
			}
			else
            {
				$je= sprintf("%.2f",$val['je']);
			}		
			
			$zhangmu_update_data=array();
			$zhangmu_update_data['wxje']=$je;	   
			$zhangmu_update_data['dtUpdateTime']=date('Y-m-d H:i:s',time());
			
			if(D('Zhangmufen')->where(array('wbid'=>$wbid))->save($zhangmu_update_data)===false)
			{
				$result=false;
			}													
		}
		
		
		
		$zfblist=D('Zfbpay')->field(array(
		'sum(receipt_amount)'=>'je',
		'wbid'=>'wbid'
		))->group('wbid')->where(array('trade_status'=>100))->select();
			
		foreach($zfblist as &$val)
		{					
			$wbid=$val['wbid'];
			$je= $val['je'];
			if(empty($je))
			{
				$je=0;
			}
			else
            {
				$je= sprintf("%.2f",$val['je']);
			}		
			
			$zhangmu_update_data=array();
			$zhangmu_update_data['zfbje']=$je;	   
			$zhangmu_update_data['dtUpdateTime']=date('Y-m-d H:i:s',time());
			
			if(D('Zhangmufen')->where(array('wbid'=>$wbid))->save($zhangmu_update_data)===false)
			{
				$result=false;
			}													
		}
		
		
		
		$gzhlist=D('Gzhpay')->field(array(
		'sum(notify_total_fee)'=>'je',
		'wbid'=>'wbid'
		))->group('wbid')->where(array('trade_status'=>100))->select();
			
		foreach($gzhlist as &$val)
		{					
			$wbid=$val['wbid'];
			$je= $val['je'];
			if(empty($je))
			{
				$je=0;
			}
			else
            {
				$je= sprintf("%.2f",$val['je']);
			}		
			
			$zhangmu_update_data=array();
			$zhangmu_update_data['gzhje']=$je;	   
			$zhangmu_update_data['dtUpdateTime']=date('Y-m-d H:i:s',time());
			
			if(D('Zhangmufen')->where(array('wbid'=>$wbid))->save($zhangmu_update_data)===false)
			{
				$result=false;
			}													
		}
		
			
		if($result)
		{
			D()->commit();
			echo  1;
		}else
        {
			D()->rollback();
			echo -1;
		}
	}
	
	
	
	
    public  function API_getAllzhangmulist()
	{		   	
     	header('Access-Control-Allow-Origin:*');
		

		
		$zhangmulist=D('Zhangmufen')->select();
    
		if(!empty($zhangmulist))
		{
			$data['result']=1;
		    $data['body']=$zhangmulist;
		}else
        {
			$data['result']=-1;
		}									
       $this->ajaxReturn($data); 							 				  
	}
	
	
	//=============================小程序专用接口============================================================
	
	
	//==============================//数据库转换专用接口=======================================================================
	
	
	public  function API_queryonebar_allhytypelist()
	{	
	    header('Access-Control-Allow-Origin:*');
		$key=C('_PhprateJmKey');
							
	    $wbinfo=I('post.bb');		
		$wbinfo= aesDeJm($wbinfo, $key);          
        $wbinfo=base64_decode($wbinfo);            
        $wbinfo = json_decode( $wbinfo,true );
		
	    $map=array();
		$map['WbAccount']=$wbinfo['wbaccount'];
		$map['PassWord']=MD5($wbinfo['password'].'hc');	
	     		
		$wbid=D('WbInfo')->where($map)->getField('WBID');

		if(empty($wbid))
		{
			$data['status']=-1;
			$data['message']='无权限';
		}
		else
        {

		  $hytypelist=D('Hylx')->where(array('WB_ID'=>$wbid))->select();
		  $data['status']=1;
		  $data['body']=$hytypelist;
		}			
		$this->ajaxReturn($data); 
									 				  
	}
	
	
	/*
	public  function API_edit_qx()
	{	
		header('Access-Control-Allow-Origin:*');
	    $result=true;
		D()->startTrans();
		$list=D('WbInfo')->field('WBID,WbAccount')->select();
		//echo json_encode($list);
		//return;
		
		foreach($list as &$val)
		{
			$wbid=$val['WBID'];
			if(D('Webini')->postOneRecord2($wbid,'bt_sp_buy',0)===false)
			{
				$result=false;
			}	
		}
		 
	  			
		    if(!empty($result))
		    {
				D()->commit();
                $data['result']=1;  			    							 									  		 
		    }
		    else
		    {
			  D()->rollback();	 
			  $data['result']=-1;
		    }				  
		   	   		
		$this->ajaxReturn($data);	
        	
	}
	
	*/
	
	//==============================//数据库转换专用接口=======================================================================
	
	
	
	//===============================xiugai  mima ======================================================
	  
	public  function API_password_find()
	{	
		header('Access-Control-Allow-Origin:*');
		$recv_data_array=I('post.'); 		
		$WbAccount= $recv_data_array['WbAccount'];
		$password= $recv_data_array['PassWord'];
		
		 
	    if(!empty($WbAccount) && !empty($password))
	    {		
		    $bar_update_result= D('WbInfo')->where(array('WbAccount'=>$WbAccount))->setField('PassWord',md5($password.'hc'));	
             			
		    if(!empty($bar_update_result))
		    {
                $data['result']=1;  			    							 									  		 
		    }
		    else
		    {
			  $data['result']=-1;
		    }				  
		  
	    }
	    else
	    {
		   $data['result']=-1;
	    }	   		
		$this->ajaxReturn($data);	   								 				  
	}
	
	//=========================================================================================
	
		//==============充值接口==beg=================/
		  
	public  function API_querywbinfo_chongzhi()
	{	
        header('Access-Control-Allow-Origin:*');
		$wbaccount=I('get.wbaccount','','string');
      	$password =I('get.password','','string');
		
		$wbinfo=D('WbInfo')->Field('WbAccount,WBName,WBTel,WBManager,addr,EndTime,CpCount')->where(array('WbAccount'=>$wbaccount,'PassWord'=>md5($password.'hc')))->find();
      

		if(!empty($wbinfo))
		{
			$data['status']=1;
			$data['wbinfo']=$wbinfo;
		}
		else
        {
           $data['status']=-1;
		}			
   
		

		$this->ajaxReturn($data);
									 				  
	}
   
   
   public  function API_query_onebarinfoByWbid()
	{	
	    header('Access-Control-Allow-Origin:*');
        $wbid = I('post.wbid','','string');	
       // $password  = I('post.password','','string');		
		
		$map=array();
		$map['WBID']=$wbid;
		//$map['PassWord']=md5($password.'hc');
		
		$wbinfo=D('WbInfo')->Field('WBID,VerNo,CpCount,WH_Status,beginTime,EndTime')->where($map)->find();
		if(!empty($wbinfo))
		{
			$wbinfo['result']=1;
					
		}
		else
        {
		  $wbinfo['result']=-1;
		}			
		 $this->ajaxReturn($wbinfo); 									 				  
	} 
	
	
	
	public  function API_barchongzhi_self_edit()
	{	
		header('Access-Control-Allow-Origin:*');
		$recv_data_array=I('post.'); 		
		$wbid= $recv_data_array['WBID'];
		
		
		$bar_update_data['EndTime']=$recv_data_array['EndTime'];
		if(!empty($recv_data_array['CpCount']))
		{
			$bar_update_data['CpCount']=$recv_data_array['CpCount'];
		}	
		
        $bar_update_data['bz']=$recv_data_array['bz'];
			
	    if(!empty($wbid))
	    {				
	        $result=true;
            D()->startTrans();
			
		    if(D('WbInfo')->where(array('WBID'=>$wbid))->save($bar_update_data)===false)
            {
				$result=false;
			} 				
			$aTempsql= D('WbInfo')->getLastSql();	
			$sendstr.=$aTempsql.';';
			
			$LzmWbChange_insert_data=array();
		    $LzmWbChange_insert_data['WB_id']=$wbid;
			$LzmWbChange_insert_data['WbInfo_Tag']=1;
						
			if(D('LzmWbChange')->add($LzmWbChange_insert_data)===false)
			{
				$result=false;
			}
					
		    if($result)
		    {	
		         D()->commit();
				 $data['result']=1;
		   }
		   else
		   {
			   D()->rollback();
			  $data['result']=-1;
		   }				  
		  
	    }
	    else
	    {
		   $data['result']=-1;
	    }	   		
		$this->ajaxReturn($data);	   								 				  
	}
	
	
	
	
  //===================充值接口end==============	
  
  
  	//=============================以下为主分服务器登陆专用接口============================================================
	public  function API_check_OneBarinfo()
	{	
	    header('Access-Control-Allow-Origin:*');
        $wbaccount = I('post.wbaccount','','string');	
        $password  = I('post.password','','string');		
		
		$map=array();
		$map['WbAccount']=$wbaccount;
		$map['PassWord']=md5($password.'hc');
		
		$wblist=D('WbInfo')->Field('WBID,VerNo,WH_Status')->where($map)->find();
		if(!empty($wblist))
		{
			$data['result']=1;
			$data['body']=$wblist;
		}
		else
        {
		  $data['result']=1;
		}			
		 $this->ajaxReturn($data); 									 				  
	} 
	
	public function income_zong()
    {
		$wbid=I('get.wbid','','string');
		session('wbid',$wbid);
        $this->display();
    }
	
	
		
	//========================获取微信授权 beg=============================================
	public function is_weixin()
	{ 
	    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) 
	    {
			return true;
		}else{
			return false;
		} 
	}

      //微信授权
    public function shouquan_wx()
    {       
       header('Access-Control-Allow-Origin:*');
	   /*
       if($this->is_weixin())
       {

       }
       else
       {
       	echo  '请在微信里访问';
       	return;
       }
       */  	   
	   $wbacount=I('get.canshu','','string');  
	   $wxid=I('get.openid','','string');

	   session('wbacount',$wbacount);
	   session('wxid',$wxid);	   
       $barinfo=D('WbInfo')->field('WBTel,WbName,WBManager')->where(array('WbAccount'=>$wbacount))->find();
       $this->assign('barinfo',$barinfo);      
       $this->display();     				                                    
    }
    
    

         //微信授权
    public function shouquan_wx_set()
    {       
        header('Access-Control-Allow-Origin:*');    
	   $wbpassword=I('post.wbpassword','','string');
	   $wbpassword=trim($wbpassword);
	   $wbpassword=md5($wbpassword.'hc');
	       
       $wbacount=session('wbacount');   
       $wxid=session('wxid'); 

       $wbinfo=D('WbInfo')->field('WBTel,WbName,WBManager,wxid')->where(array('WbAccount'=>$wbacount,'PassWord'=>$wbpassword))->find();
       if(empty($wbinfo))
       {
       	  $response['result']=-1;
       	  $this->ajaxReturn($response);	
       	  return;
       }
        
        if(empty($wbinfo['wxid']))
		{
			$bar_update_result=D('WbInfo')->where(array('WbAccount'=>$wbacount))->setField('wxid',$wxid);
			if($bar_update_result)
			{
				$response['status']=1;
			}else
			{
				$response['status']=-3;
			}
		}
		else
		{
			$response['status']=-2;
		}		
											 
		$this->ajaxReturn($response);						                                    
    }

    //分服务器获取wxid
    public function  getwxid()
    {
    	header('Access-Control-Allow-Origin:*');
    	$wbid=session('wbid');
    	$wxid=D('WbInfo')->where(array('WBID'=>$wbid))->getField('wxid');
    	if($wxid)
    	{
           $data['status']=1;
           $data['wxid']=$wxid;
    	}else{
    		$data['status']=-1;
    	}
    	$this->ajaxReturn($data); 
    }
	
	//========================获取微信授权 end=============================================
	
	
	//=========================实名信息修改=======beg======================================
	public  function API_shiming_edit()
	{	
		header('Access-Control-Allow-Origin:*');
		$recv_data_array=I('post.'); 		
		$wbid= $recv_data_array['WBID'];
		$shimingtype= $recv_data_array['shimingtype'];
		
		$shimingtype=$shimingtype-1;
		
		D()->startTrans();
		$result=true;
				
		$bFind=D('WIni')->where($map)->find();
		if($bFind)
		{
			$ini_update_data['NValue']=$shimingtype;		
			$map['WB_ID']=$wbid;
			$map['Name']='FGlwSetLx';
			
			if(D('WIni')->where($map)->save($ini_update_data)===false)
			{
				$result=false;			
			}
		}
		else
		{		
            $ini_insert_data=array(); 	
			$ini_insert_data['WB_ID']=$wbid;	
            $ini_insert_data['Name']='FGlwSetLx';
            $ini_insert_data['NValue']=$shimingtype;						
			if(D('WIni')->add($ini_insert_data)===false)
			{
				$result=false;			
			}
		}

			
		
		if(D('WbSetChange')->where(array('wb_id'=>$wbid))->setField('IniTab_Tag',1)===false)
		{
			$result=false;			
		}
		
		
	
	
	    if($result)
	    {					   
			$data['result']=1;	
            D()->commit();			
	    }
	    else
	    {
		   $data['result']=-1;
		   D()->rollback();	
	    }	   		
		$this->ajaxReturn($data);	   								 				  
	}
		//=========================实名信息修改===end==========================================
	
}