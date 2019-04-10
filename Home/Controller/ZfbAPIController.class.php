<?php
namespace Home\Controller;
use Think\Controller;
class ZfbAPIController extends Controller
{

     public  function API_query_tellist()
	{	


		$map=array();
		$map['wxid']=array('neq','');
		$tellist=D('Telaccount')->Field('wxid,TelNo,Wb_AccS,SessionId')->where($map)->select();
	
		if(!empty($tellist))
		{
			$data['result']=1;
			$data['body']=$tellist;
		}
		else
        {
           $data['result']=-1;
		}			
   	
		$data  =json_encode($data);
		
		echo $data;								 				  
	}


   
     public  function API_querygoodsinfo()
	{	
		$wbaccount=I('post.wbaccount','','string');		
		
		
       		
		$wbid=D('WbInfo')->where(array('WbAccount'=>$wbaccount))->getField('WBID');
		
		
		
		
		$fangwen_insert_data=array();
		$fangwen_insert_data['wbid']=$wbid;
		$fangwen_insert_data['dtInsertTime']=date('Y-m-d H:i:s',time());
		$fangwen_insert_data['num']=1;
		$fangwen_insert_data['modelname']='clientgoods';
		$res=D('Fangwentj')->add($fangwen_insert_data);
		
		
		

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
	
	
	 public  function API_querywbinfo()
	{	
	
		$wbaccount=I('get.wbaccount','','string');
      	$password =I('get.password','','string');
		
		$wbinfo=D('WbInfo')->Field('WbAccount,WBName,WBTel,WBManager,addr,QQ,EMail')->where(array('WbAccount'=>$wbaccount,'PassWord'=>md5($password.'hc')))->find();
       

		if(!empty($wbinfo))
		{
			$data['result']=1;
			$data['wbinfo']=$wbinfo;
		}
		else
        {
           $data['result']=-1;
		}			
   
		
		$data  =json_encode($data);
		$data=base64_encode($data);
		echo $data;
									 				  
	}
	
	
    public  function API_querywbinfo_lzmcheck()
	{	
	
		$wbaccount=I('post.wbaccount','','string');
      	$password =I('post.password','','string');			
		$wbinfo=D('WbInfo')->Field('WbAccount,WBName,WBTel,WBManager')->where(array('WbAccount'=>$wbaccount,'PassWord'=>md5($password.'hc')))->find();	
		if(!empty($wbinfo))
		{
			$data['result']=1;
		
		}
		else
        {
           $data['result']=-1;
		}			
   	
		$data  =json_encode($data);
	
		echo $data;								 				  
	}
	

	public  function API_client_buygoods()
	{		   		
		if(IS_AJAX)
		{   
           	
           	
			if(!checkToken($_POST['token']))
			{  
		        writelog('API_client_buygoods---重复提交拦截');
				$data['status']=-2;          								
			    $this->ajaxReturn($data);
                return;   			   
			}
			else
			{
			
			}	
		   
			         
	        $post_order_no = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);  //网站订单号
			$post_order_no='XS'.$post_order_no;
            $dtInsertTime=date('Y-m-d H:i:s',time());     			
			$str=I('post.aa','','string');	
            $str=htmlspecialchars_decode($str); 		
			$goodslist=json_decode($str,true);
			if(empty($goodslist))
			{	
              $data['status']=-1;
			  $this->ajaxReturn($data);
			  return;	
			}
			
			
				
			$wbaccount=$goodslist['wbaccount'];		 
		    $wbid=D('WbInfo')->where(array('WbAccount'=>$wbaccount))->getField('WBID');
			
			$guid=$goodslist['guid'];
			// 获取本次所有的zuhe_id 列表			
			$xiaoshou_goodslist=$goodslist['goodsinfo'];
		    
			
			$map=array();
			$map['info.wbid']=$wbid;
			$map['info.deleted'] =0;
			$map['info.is_zuhe'] =2;
            $map['kc.position']  =1;			
			$zuhe_goodslist=D('Productkc')->getAllChuhuokucunfoListByMap($map);
			
			foreach($zuhe_goodslist as &$val)
			{
				$zuhe_id=$val['goods_id'];
				foreach($xiaoshou_goodslist as &$val2)
				{
					if($zuhe_id==$val2['zuhe_id'])
					{
						$val['xiaoshou_num']+=$val2['xiaoshou_num'];
					}	
				}
			}
			
					
			$i=0;
			$list=array();
			foreach($zuhe_goodslist as &$val)
			{
			   if($val['xiaoshou_num'] > 0)                 //需要插入的组合商品记录
			   {
				   $list[$i]['goods_id']=$val['goods_id'];	
				   $list[$i]['goods_name']=$val['goods_name'];
                   $list[$i]['xiaoshou_num']=$val['xiaoshou_num'];				   				   
				   $list[$i]['price']=$val['shou_price'];
				   $list[$i]['je']=$val['shou_price']* $val['xiaoshou_num'];
				   $list[$i]['hj_num']=$val['num'];	
				   $list[$i]['ck_num']=0;	
				   $list[$i]['is_zuhe_goods']=2;
				   $i++;
			   }	   
			}

					
			if(empty($xiaoshou_goodslist))
			{	
              $data['status']=-1;
			  $this->ajaxReturn($data);
			  return;	
			}
						
			$sum_sr_je=$goodslist['total_je'];
			$sum_sp_je=$goodslist['total_je'];	
			$sum_zl_je=0;
			$info='';

			$result=true;
			D()->startTrans();  //启用事务
			
			foreach($list  as &$val)                                     //先处理组合商品的明细
			{
				$xiaoshoumx_insert_data=array();  			
				$xiaoshoumx_insert_data['xiaoshou_num']     =$val['xiaoshou_num'];
				$xiaoshoumx_insert_data['ck_num']  =0;
				$xiaoshoumx_insert_data['hj_num']  =$val['hj_num'];
				$xiaoshoumx_insert_data['je']=$val['je'];
				$xiaoshoumx_insert_data['price']=$val['price'];
				$xiaoshoumx_insert_data['post_order_no']=$post_order_no;
				$xiaoshoumx_insert_data['ordertype']=1;
				$xiaoshoumx_insert_data['wbid']=$wbid;				
				$xiaoshoumx_insert_data['dtCharuTime']=$dtInsertTime;	
				$xiaoshoumx_insert_data['goods_id']=$val['goods_id'];
				$xiaoshoumx_insert_data['zuhe_id']=$val['goods_id'];
				$xiaoshoumx_insert_data['is_zuhe_goods']=2;
				$xiaoshoumx_insert_data['zuhe_flag']=1;
				//$xiaoshoumx_insert_data['lingqu_status']=2;
				if(D('Productxsmxzh')->add($xiaoshoumx_insert_data)===false)
				{					
					$result=false;
				}
			}
				
			
			$sumje=0;
			foreach( $xiaoshou_goodslist as &$val)
			{		
			  
                $xiaoshoumx_insert_data=array();  			
				$xiaoshoumx_insert_data['xiaoshou_num']     =$val['xiaoshou_num'];
				$xiaoshoumx_insert_data['ck_num']  =D('Productkc')->where(array('wbid'=>$wbid,'position'=>0,'goods_id'=>$val['goods_id']))->getField('num');
				$xiaoshoumx_insert_data['hj_num']  =D('Productkc')->where(array('wbid'=>$wbid,'position'=>1,'goods_id'=>$val['goods_id']))->getField('num');
				$xiaoshoumx_insert_data['je']=$val['xiaoshou_num']*$val['price'];
				$xiaoshoumx_insert_data['price']=$val['price'];
				$xiaoshoumx_insert_data['post_order_no']=$post_order_no;
				$xiaoshoumx_insert_data['ordertype']=1;
				$xiaoshoumx_insert_data['wbid']=$wbid;
				$xiaoshoumx_insert_data['dtInsertTime']=$dtInsertTime;	
                $xiaoshoumx_insert_data['zuhe_id']=0;
				$xiaoshoumx_insert_data['is_zuhe_goods']=0;
				$xiaoshoumx_insert_data['zuhe_flag']=1;	
				//$xiaoshoumx_insert_data['lingqu_status']=2;
				
				$sumje+= $xiaoshoumx_insert_data['je'];
											 				
				$goodsinfo=D('Product')->where(array('wbid'=>$wbid,'goods_id'=>$val['goods_id']))->find();	
				
				
				if($goodsinfo['is_zuhe']==0)
				{
					
					$xiaoshoumx_insert_data['goods_id']=$val['goods_id'];
					$xiaoshoumx_insert_data['zuhe_id']=0;
					if(D('Productxsmx')->add($xiaoshoumx_insert_data)===false)
					{					
						$result=false;
					}
					 
					$now_hjkc_num= D('Productkc')->where(array('wbid'=>$wbid,'position'=>1,'goods_id'=>$val['goods_id']))->getField('num');
					if(empty($now_hjkc_num))
					{
						$now_hjkc_num=0;
					}	
					
					
					if($val['xiaoshou_num'] >= $now_hjkc_num)
					{
						$now_sj_xiaoshou_num =$now_hjkc_num;
					}
					else
					{
						$now_sj_xiaoshou_num =$val['xiaoshou_num'];
					}	
                     					

				}
				else if($goodsinfo['is_zuhe']==1)
                {
					
					$zuhe_id= $goodsinfo['zuhe_id'];				
					$xiaoshoumx_insert_data['goods_id']=$val['goods_id'];					
					$xiaoshoumx_insert_data['zuhe_id']=$zuhe_id;
				    $xiaoshoumx_insert_data['is_zuhe_goods']=1;
					$xiaoshoumx_insert_data['zuhe_flag']=1;
                   // $xiaoshoumx_insert_data['lingqu_status']=2;					
					if(D('Productxsmx')->add($xiaoshoumx_insert_data)===false)
					{					
						$result=false;
					}					
					$now_hjkc_num= D('Productkc')->where(array('wbid'=>$wbid,'position'=>1,'goods_id'=>$zuhe_id))->getField('num');
					if($val['xiaoshou_num'] >= $now_hjkc_num)
					{
						$now_sj_xiaoshou_num =$now_hjkc_num;
					}
					else
					{
						$now_sj_xiaoshou_num =$val['xiaoshou_num'];
					}
                     				
				}														
				$val['goods_name']=D('Product')->where(array('wbid'=>$wbid,'goods_id'=>$val['goods_id']))->getField('goods_name');				
				$info.= $val['goods_name'].':'.$val['xiaoshou_num'].' ';
			}
				
			//更新库存表
			
			$xiaoshou_insert_data['post_order_no']=$post_order_no;
			$xiaoshou_insert_data['ordertype']=1;
			$xiaoshou_insert_data['wbid']=$wbid;
			$xiaoshou_insert_data['info']=$info;
			$xiaoshou_insert_data['sum_sp_je']=$sum_sp_je;	
			$xiaoshou_insert_data['sum_sr_je']=$sum_sr_je;
			$xiaoshou_insert_data['sum_zl_je']=$sum_zl_je;
            $xiaoshou_insert_data['pay_position']=1;			
			$xiaoshou_insert_data['operator']='kehuduanxiaoshou';								
			$xiaoshou_insert_data['detailinfo']=$str;
			$xiaoshou_insert_data['zuhe_flag']=1;			
            $xiaoshou_insert_data['bz']=$str;
            $xiaoshou_insert_data['operator']='kehuduanxiaoshou';               
			$xiaoshou_insert_data['pay_type']=3; 		//客户端现金支付		
			$xiaoshou_insert_data['dtCharuTime']=$dtInsertTime;
			$xiaoshou_insert_data['position']=1;
			$xiaoshou_insert_data['sGuid']=$guid;
			
			
			if($sum_zl_je)
			{
			  $xiaoshou_insert_data['sum_zl_je']=$sum_zl_je;	
			}
			else
			{
				$xiaoshou_insert_data['sum_zl_je']=0;
			} 
			
			if($sum_sp_je)
			{
			  $xiaoshou_insert_data['sum_sp_je']=$sum_sp_je;	
			}
			else
			{
				$xiaoshou_insert_data['sum_sp_je']=0;
			} 

                if($sum_sr_je)
				{
				  $xiaoshou_insert_data['sum_sr_je']=$sum_sr_je;	
				}
				else
                {
					 $xiaoshou_insert_data['sum_sr_je']=0;
				} 				
				
				
				
				if(D('Productxs')->add($xiaoshou_insert_data)===false)
				{
				
					$result=false;
				}	
			   
			
			if($result)
            {
              D()->commit();  //提交事务
 
               $data['status']=1;
            }
            else
            {
              D()->rollback();    //回滚
              $data['status']=-1;
            }
								
			$this->ajaxReturn($data);
		}									 				  
	}
	
	
	public  function API_client_Querygoodsinfo_yimai()
	{		   			   
		$wbaccount=I('get.wbaccount','','string');		 
		$wbid=D('WbInfo')->where(array('WbAccount'=>$wbaccount))->getField('WBID');
		$sGuid=I('get.sGuid','','string');	
		$hycardno=I('get.hycardno','','string');	
		
		
		
		
		$orderinfo=D('Productxs')->where(array('wbid'=>$wbid,'sGuid'=>$sGuid))->order('id desc')->find();
		
		
		
		$orderinfo['dtCharuTime']=date('Y-m-d H:i:s',$orderinfo['dtCharuTime']);

		  
		    if($orderinfo['lingqu_status']==0)
			{
				$s1='未派送';
				$s2=iconv('GB2312', 'UTF-8', $s1);
				$orderinfo['lingqu_jieguo']=$s2;				
			}
			else
			if($orderinfo['lingqu_status']==1)
			{
				
				$s='已确认,正在派送';
				$s2=iconv('GB2312', 'UTF-8', $s1);
				$orderinfo['lingqu_jieguo']=$s2;
			}
			else if($orderinfo['lingqu_status']==2)
			{
				
				$s1='已取消';
				$s2=iconv('GB2312', 'UTF-8', $s1);
				$orderinfo['lingqu_jieguo']=$s2;
				$endtime=date('Y-m-d H:i:s',$orderinfo['dtInsertTime']) ;
				$orderinfo['dtCharuTime'] =$endtime;
			}	

            if($orderinfo['pay_type']==1)
            {
				$s1='微信';
				$s2=iconv('GB2312', 'UTF-8', $s1);
				$orderinfo['pay_fangshi']=$s2;	
			}else if($orderinfo['pay_type']==2)
            {
				$s1='支付宝';
				$s2=iconv('GB2312', 'UTF-8', $s1);
				$orderinfo['pay_fangshi']=$s2;	
			}else if($orderinfo['pay_type']==3)
            {
				$s1='现金';
				$s2=iconv('GB2312', 'UTF-8', $s1);
				$orderinfo['pay_fangshi']=$s2;	
			}				
		

		
				
		$this->assign('orderinfo',$orderinfo);
		$this->display();
								 				  
	}
	
	
	public  function  APIgetRateContent()
	{
	    $wbinfo=I('post.bb');	
        $wbinfo=base64_decode($wbinfo);	   
	    $wbinfo= characet1($wbinfo);
	    $wbinfo=json_decode($wbinfo,true);
	     
	    $map=array();
		$map['WbAccount']=$wbinfo['wbaccount'];
		$map['PassWord']=MD5($wbinfo['password'].'hc');	
	    $newwbid=D('WbInfo')->where($map)->getField('WBID');
		if(empty($newwbid))
		{
			$data['status']=-1;
			echo  json_encode($data);
		   	return;
		}	
	  
	   $alldata=I('post.aa');	   

	   $alldata=base64_decode($alldata);	   
	   $alldata= characet1($alldata); 
	   $alldata=json_decode($alldata,true);	
	        
	   $temprate = $alldata['temprate'];   
	   $district = $alldata['district'];
	   $fixed    = $alldata['fixed'];
	   $pclist   = $alldata['pclist'];
	   $hytype   = $alldata['hytype'];
	   

	   
	   $result=true;
	   D()->startTrans();
	   //插入计算机列表  
	  
	   if(D('Computerlist')->where(array('WB_ID'=>$newwbid))->delete()===false)		   
	   {
		   $result=false; 
		   writelog('--2-1--error-','log');
	   }	   
	   
	   	$aTempsql= D('Computerlist')->getLastSql(); 
		$sendstr.=$aTempsql.';'; 
	   
	   for($i=0;$i<count($pclist);$i++)
	   {
		   $nowtime=date('Y-m-d H:i:s',time());
		   $apcinfo=array();
		   $apcinfo['Name']=$pclist[$i]['sCom'];
    	   	   
		   $apcinfo['WB_ID']=$newwbid;
		   $apcinfo['GroupNameGuid']=$pclist[$i]['GroupNameGuid'];
		   $apcinfo['Guid']=$pclist[$i]['Guid'];
		   $apcinfo['insertTime']=$nowtime;		  	   
		   if(D('Computerlist')->add($apcinfo)===false)
		   {
			   $result=false;
			   writelog('--2-2-error--','log');
		   }	
           $aTempsql= D('Computerlist')->getLastSql(); 
		   $sendstr.=$aTempsql.';'; 		   
	    }	   
	    //插入会员类型
		
	   if(D('Hylx')->where(array('WB_ID'=>$newwbid))->delete()===false)		   
	   {
		   $result=false; 
		   writelog('--3-1--error-','log');
	   }		
       $aTempsql= D('Hylx')->getLastSql(); 
	   $sendstr.=$aTempsql.';'; 
	   
       for($i=0;$i<count($hytype);$i++)
	   {
		   $ahytypeinfo=array();
		   $ahytypeinfo['WB_ID']=$newwbid;
		   $ahytypeinfo['Name']=$hytype[$i]['Name'];		   
		   $ahytypeinfo['SjDiscount']=$hytype[$i]['SjDiscount'];
		   $ahytypeinfo['SpDiscount']=$hytype[$i]['SpDiscount'];
		   $ahytypeinfo['SmallIntegral']=$hytype[$i]['SmallIntegral'];
		   $ahytypeinfo['Guid']=$hytype[$i]['Guid'];	
	  	   
		   if(D('Hylx')->add($ahytypeinfo)===false)
		   {
			   $result=false;
			   writelog('--3-1-error--','log');
		   }
            $aTempsql= D('Hylx')->getLastSql(); 
	        $sendstr.=$aTempsql.';'; 		   
         		   
	    } 
       
       // 插入固定费率
	   if(D('FixedRate')->where(array('WB_ID'=>$newwbid))->delete()===false)		   
	   {
		   $result=false; 
		   
	   }
	    $aTempsql= D('FixedRate')->getLastSql(); 
	    $sendstr.=$aTempsql.';'; 
	   for($i=0;$i<count($fixed);$i++)
	   {
		   $nowtime=date('Y-m-d H:i:s',time());
		   $afixedinfo=array();
		   $afixedinfo['WB_ID']=$newwbid;
		   $afixedinfo['GroupGuid']=$fixed[$i]['GroupGuid'];	
		   $afixedinfo['Guid']=$fixed[$i]['Guid'];			   
		   $afixedinfo['name']=$fixed[$i]['name'];		   
		   $afixedinfo['TimeSize']=$fixed[$i]['TimeSize'];	   
		   $afixedinfo['je']=$fixed[$i]['je'];	       
           $afixedinfo['BeginTime']=$fixed[$i]['BeginTime'];	
           $afixedinfo['EndTime']=$fixed[$i]['EndTime'];	
           $afixedinfo['AutoChange']=$fixed[$i]['AutoChange'];		   
		   $afixedinfo['isBj']=$fixed[$i]['isBj'];	
		   $afixedinfo['OwnerHyLxGuid']=$fixed[$i]['OwnerHyLxGuid'];	
		   $afixedinfo['Lx']=$fixed[$i]['Lx'];	
		   $afixedinfo['inserttime']=$nowtime;			   	    	   
		   if(D('FixedRate')->add($afixedinfo)===false)
		   {
			   $result=false;
			   writelog('--4-2-error--','log');
		   }
           	$aTempsql= D('FixedRate')->getLastSql(); 
	        $sendstr.=$aTempsql.';';		   
	    } 	
	    
      	
       // 插入动态费率
       if(D('District')->where(array('WB_ID'=>$newwbid))->delete()===false)		   
	   {
		   $result=false; 
		   writelog('--5-1-error--','log');
	   }	
        $aTempsql= D('District')->getLastSql(); 
	    $sendstr.=$aTempsql.';';   	   
	   for($i=0;$i<count($temprate);$i++)
	   {		
		   $nowtime=date('Y-m-d H:i:s',time());
		   $atemprateinfo=array();
		   $atemprateinfo['WB_ID']=$newwbid;
		   $atemprateinfo['GroupName']=$temprate[$i]['GroupName'];	
		   $atemprateinfo['Guid']=$temprate[$i]['Guid'];			   
		   $atemprateinfo['HyCardGuids']=$temprate[$i]['HyCardGuids'];		   
		   $atemprateinfo['FlList']=json_encode($temprate[$i]['FlList']);	   
		   $atemprateinfo['isBj']=0;	       	   
		   $atemprateinfo['inserttime']=$nowtime;		
		   
		   if(D('District')->add($atemprateinfo)===false)
		   {
			   $result=false;
			   writelog('--5-2--error-','log');
		   }
		   $aTempsql= D('District')->getLastSql(); 
	       $sendstr.=$aTempsql.';';
		   
	    } 		
      
        if($result)
		{
			
			D()->commit();
			
			$res =PostTopUpdateDataToWb_lzmByWbid($newwbid,'Php_To_Top_Sql',$sendstr);
			if(!empty($res))
			{
				writelog('--6-1--- 同步消息已发送','log');
			}
			else
            {
				writelog('--6-2--error-同步消息发送失败','log');
			}				
			
			$data['status']=1;
		}
		else
        {
			writelog('--7--error-','log');
		  D()->rollback();	
		  $data['status']=-1;
		}			
		
		echo  json_encode($data);
				   	   	     	   
	}
	
	
		
	public  function API_getNowShiftShouruByWxid()
	{		   	
        $wxid=I('get.wxid','','string');

		$account_list=D('Telaccount')->where(array('wxid'=>$wxid))->getField('Wb_AccS');
		if(empty($account_list))
		{
		  $data['status']=-1;
		    echo  json_encode($data);		
		  return;
		}
		else
        {
		  $account_list=json_decode($account_list);							
		} 		

		for($i=0;$i<count($account_list);$i++)
		{	
			$wbid=D('WbInfo')->where(array('WbAccount'=>$account_list[$i]))->getField('WBID');					    
			
			$money_array[$i]['todayje']=getTodayShouru_zzb($wbid);
			$money_array[$i]['nowshiftje']=getNowShiftShouru_zzb($wbid);
		 		   
		   $money_array[$i]['wbid']=$wbid;					
		}	
		
		$data['status']=1;
		$data['body']=$money_array;
		
		
      echo  json_encode($data);							 				  
	}
	
	

	  
	
	public  function API_get30dayShouruByWbid()
	{		   	
        $wbid=I('get.wbid','','string');
        
		
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
	
	
	
	
	public  function API_getAllBar7dayShouruByWxid()
	{		   	
        $wxid=I('get.wxid','','string');
		
		$account_list=D('Telaccount')->where(array('wxid'=>$wxid))->getField('Wb_AccS');
		if(empty($account_list))
		{
		  $data['status']=-1;
		  echo  json_encode($data);		
		  return;
		}
		else
        {
		  $account_list=json_decode($account_list);							
		} 		
		
		
		
		$nowtime=date('Y-m-d 00:00:00');
        $nowtime1= strtotime($nowtime);
     
        $k=0;
		$rateonline_array=array();
		
		$onebarje_array=array();
        $onebartime_array=array();	
		
		for($i=7;$i>0;$i--)
		{
			$last7daytime = strtotime('-'.$i.' days',$nowtime1);
            $last7daytime = date('Y-m-d 00:00:00', $last7daytime) ;
			
			$sumje=0;
		
        			
			for($j=0;$j<count($account_list);$j++)
			{					
				$onebarinfo=D('WbInfo')->where(array('WbAccount'=>$account_list[$j]))->find();
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
				
				$sumje= $sumje+$onebarjelist['Sum_Je'];			
				$sumje= sprintf("%.2f", $sumje); 
 				
			}

    	     $money_array[$k]=(float)$sumje;
             $time_array[$k] = date('m-d', strtotime($last7daytime)) ;			                				
			$k++;							
		}	
		
		
		$data['status']=1;
		$data['linedata']=$money_array;
		$data['linetime']=$time_array;
		$data['rateonline']=$rateonline_array;			
      echo  json_encode($data);							 				  
	}
	
	
	

		

	
	
	public  function API_get30dayShiftByWbid()
	{		   	
        $wbid=I('get.wbid','','string');
        	

	
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
	

	public function income_zong()
    {
		$wbid=I('get.wbid','','string');
		session('wbid',$wbid);
		
		$token=I('get.token','','string');
		$token=trim($token);

		$username=aesDeJm($token,'890123');	
		
		
		
		
		$username=trim($username);
		
		if($username !='admin')
		{
			echo  '无访问权限';
			return;
		}	
		
		
		$wbname=D('WbInfo')->where(array('WBID'=>$wbid))->getField('WBName');
		$this->assign('wbname',$wbname);
		
		//aes 解密			
		$moren_month=date('Y-m',time());
		$this->assign('moren_month',$moren_month);
		
		$moren_year=date('Y',time());
		$this->assign('moren_year',$moren_year);
        $this->display();
    }	  
	
	public function getIncomeData()
    { 

      if(IS_AJAX)
      {
        $wbid=session('wbid');
        $map['WB_ID']=$wbid;
        $daterange=I('post.daterange'); 

      
        if(!empty($daterange))  
        {
          list($start,$end) = explode(' - ',$daterange);    
          $start = str_replace('/','-',$start);            
          $end = str_replace('/','-',$end);                
        }
       
       //注：现金收入=会员收入+临时卡收入+现金商品销售收入；
       //营业额=计费会员上机消费+临时卡上机消费+现金购买商品消费
       
       //如果周期在一个月内，就是每天出一个点
       //如果周期在1-3个月内 就是每周出1个点
       

       // 现金收入
       $daycount=getdayjiange($start,$end);
  

       //查询条件开始的那天所在范围0-23点
       
       $startTime= date('Y-m-d 00:00:00',strtotime($start));
       $endTime  = date('Y-m-d 23:59:59',strtotime($start));
	   
	   
	   
	   //获取小时差
	   $startTime_hour= date('Y-m-d H:i:s',strtotime($start));
       $endTime_hour  = date('Y-m-d H:i:s',strtotime($start));
	   $hourcount = getonehourjiange($startTime_hour,$endTime_hour);
	   
	    $datalist1=array();
		$datalist2=array();
       
        //一周出一条数据
	   
       if($daycount >30)
       {

        $weekstartTime=  date('Y-m-d ',strtotime($start));
        $weekendTime=    date('Y-m-d ',strtotime($end));
        $weekarray=getweekjiange( $weekstartTime,$weekendTime);

        for($i=0;$i<count($weekarray);$i++)
         {
           //获取每天的开始和结束时间
            $oneweekbegtime= $weekarray[$i][0];
            $oneweekendtime= $weekarray[$i][1];

            $str1=substr($oneweekbegtime,5,5);          
            $xiasdata= $str1;  

           //现金收入
            $map=array();
			$map['wb_id']=session('wbid');
			$map['cTime']=array('BETWEEN',array($oneweekbegtime,$oneweekendtime));
			$incomelist = D('Tongji')->where($map)->find();
			if(empty($incomelist))
			{
				$incomelist['Xj_je']=0;
				$incomelist['qt_Je']=0;
			}

           $datalist1[$i]= (float)sprintf("%.2f", $incomelist['Xj_je']);;
		   $datalist2[$i]= (float)sprintf("%.2f", $incomelist['qt_Je']);
		   $datalist3[$i]= $datalist1[$i]+$datalist2[$i];

           $yingye_money= $yingye_money+$incomelist['Xj_je'];
		   $shouru_money= $shouru_money+$incomelist['qt_Je'];
 
           $axislist[$i]=$xiasdata;
         
         }            
       } 
      
       	  
        //查询该日前30天内的记录
	
		
		//计算30天内的收入

       if(($daycount<=30) && ($daycount>1))
       {
         for($i=0;$i<$daycount;$i++)
         {
           //获取每天的开始和结束时间
            $onedaybegtime= strtotime('+'.$i.'days',strtotime($start));
            $onedayendtime= strtotime('+'.$i.'days',strtotime($endTime));

            $onedaybegtime= date('Y-m-d H:i:s',$onedaybegtime);
            $onedayendtime= date('Y-m-d H:i:s',$onedayendtime);

            $str1=substr($onedaybegtime,5,5);
            $xiasdata= $str1;  
           
            

           	$map=array();
			$map['wb_id']=session('wbid');
			$map['cTime']=array('BETWEEN',array($onedaybegtime,$onedayendtime));
			$incomelist = D('Tongji')->where($map)->find();
			if(empty($incomelist))
			{
				$incomelist['Xj_je']=0;
				$incomelist['qt_Je']=0;
			}

           $datalist1[$i]= (float)sprintf("%.2f", $incomelist['Xj_je']);;
		   $datalist2[$i]= (float)sprintf("%.2f", $incomelist['qt_Je']);
		   $datalist3[$i]= $datalist1[$i]+$datalist2[$i];
		   $yingye_money= $yingye_money+$incomelist['Xj_je'];
		   $shouru_money= $shouru_money+$incomelist['qt_Je'];
		   
           $axislist[$i]=$xiasdata;
           
         }          
       } 
	   
	   
	     

       $list['money'][0]['data']= $datalist3;
       $list['money'][1]['data']= $datalist1;
	   $list['money'][2]['data']= $datalist2;
       $list['xAxis']= $axislist;
       $list['yingye']= $yingye_money;
       $list['shouru']= $shouru_money;
	   $list['shourusum']= $yingye_money+$shouru_money;


        $this->ajaxReturn($list);
      }  
                   
    }
	
	
	
    public function getalipayinfo_yue()
    {
        
        if(IS_AJAX)
        {       
 
            $page = I('get.page',1,'int');
            $rows = I('get.rows',20,'int');
            $sord = I('get.sord','','string')?:'desc';
            $sidx = I('get.sidx','','string')?:'cTime';
			
            $year     = I('get.year','','string');//获取交班时间

            $map = array();
      
           	if(empty($year))
			{
				$year=date('Y',time());
			}	
			
            $map['wb_id']=session('wbid');		                        
            $zfbpaydata = D('Tongji')->getTongjilist_yue($map,$page,$rows,$year);  
            $count=	$zfbpaydata['count'];		
					
			$page=1; 
			
            
						          
            $response = new \stdClass();
            $response->count       = $zfbpaydata['count'];//返回的数组的第一个字段记录总条数
            $response->nowPage     = $page ;              //每页显示的记录数目               
            $response->total       = ceil($zfbpaydata['count'] / $rows);          
            $response->Sum_Je     = $zfbpaydata['Sum_Je'] ;
            $response->Xj_je  = $zfbpaydata['Xj_je'] ;
			 $response->qt_Je  = $zfbpaydata['qt_Je'] ;
       
            $response->rows   = $zfbpaydata['list'] ;
            $this->ajaxReturn($response);
        }
    } 

	
    public function getalipayinfo_day()
    {
        
        if(IS_AJAX)
        {       
 
            $page = I('get.page',1,'int');
            $rows = I('get.rows',20,'int');
            $sord = I('get.sord','','string')?:'desc';
            $sidx = I('get.sidx','','string')?:'cTime';

            $month     = I('get.month','','string');//获取交班时间
			
			if(empty($month))
			{
				$month=date('Y-m',time());
			}	
            $map = array();
            $map['wb_id']=session('wbid');		                              		
            $zfbpaydata = D('Tongji')->getTongjilist_day($map,$page,$rows,$month);  
            $count=	$zfbpaydata['count'];								
			$page=1; 
			
             				          
            $response = new \stdClass();
            $response->count       = $zfbpaydata['count'];//返回的数组的第一个字段记录总条数
            $response->nowPage     = $page ;              //每页显示的记录数目               
            $response->total       = ceil($zfbpaydata['count'] / $rows);          
            $response->Sum_Je     = $zfbpaydata['Sum_Je'] ;
            $response->Xj_je  = $zfbpaydata['Xj_je'] ;
			 $response->qt_Je  = $zfbpaydata['qt_Je'] ;
			
       
            $response->rows   = $zfbpaydata['list'] ;
            $this->ajaxReturn($response);
        }
    } 
	
	
	  
}