<?php
namespace Home\Controller;
use Think\Controller;
class ProductAPIController extends Controller
{
	//检测网吧是否超时
	public  function API_querywbinfo()	
	{	
	    header('Access-Control-Allow-Origin:*');				
		$wbaccount=I('post.wbaccount','','string');	
		$password=I('post.password','','string');							
		$wbinfo=D('WbInfo')->field('WBName,WBID')->where(array('WbAccount'=>$wbaccount,'PassWord'=>md5($password.'hc')))->find();    
		if(empty($wbinfo))
		{
			$data['result']=-3;	
            $data['msg']='网吧账号信息错误';	  			
		}
		else
        {				
		    $yuangonglist=D('Yuangong')->field('id,name,boss_qx')->where(array('WB_ID'=>$wbinfo['WBID']))->select();
		  	$data['result']=1;     			
			$data['list']=$yuangonglist;			            			
		}				
		echo  json_encode($data);								 				  
	}
	

	
	public  function API_queryyuangonginfo()	
	{	
	    header('Access-Control-Allow-Origin:*');
		$wbaccount=I('post.wbaccount','','string');	
		$wbpassword=I('post.wbpassword','','string');		
        $username=I('post.username','','string');
        $password=I('post.password','','string');	
		
		$wbinfo=D('WbInfo')->field('WbName,WbAccount,WBID,EndTime')->where(array('WbAccount'=>$wbaccount,'PassWord'=>md5($wbpassword.'hc')))->find();
		if(empty($wbinfo))
		{
			$data['result']=-3;
            $data['msg']='网吧账号信息错误';				
		}
		else
        {
			
			
		  $nowtime=date('Y-m-d H:i:s');			 
		  $wb_endtime=$wbinfo['EndTime'];				  	                             				 
		  $shijiancha=getTimeCha($wb_endtime,$nowtime);				  
		  $wb_endtime= date('Y-m-d H:i:s',strtotime($user['EndTime']));		 				
		  if($shijiancha>0)
		  {
			$data['result']=-3;	
            $data['msg']='授权到期,登陆失败'; 
			echo  json_encode($data);	
            return; 			
		  }
			
			//网吧信息成功，则判断token表是否有该网吧数据 如果没有，就插入一条新数据
		   $yuangonginfo=D('Yuangong')->field('id,role_id,WB_ID,name,boss_qx')->where(array('WB_ID'=>$wbinfo['WBID'],'pw'=>md5($password.'!@#BGS159357'),'name'=>$username))->find();		  
		   if($yuangonginfo)
		   {		
		       /*   
	            $roleinfo=D('Role')->field('bh_qx,th_qx,xj_qx')->where(array('WB_ID'=>$wbinfo['WBID'],'role_id'=>$yuangonginfo['role_id']))->find();  	   
				
				$ck_qx= D('Webini')->where(array('wbid'=>$wbinfo['WBID'],'skey'=>'FAllowNewproductCk'))->getField('svalue');
				if($ck_qx==1)
				{
				   $ck_qx=1;  
				}
				else
				{
				  $ck_qx=0; 	
				}

				*/

				
			
			   	$sLoginGuid_cs=getGuid_cs();				
				$nowtime=date('Y-m-d H:i:s',time());			 								
				$tokeninfo=D('Token')->where(array('wbid'=>$wbinfo['WBID']))->find();
			    if(empty($tokeninfo))
			    {
					$token_insert_data=array();
					$token_insert_data['wbid']=     $wbinfo['WBID'];
					$token_insert_data['WbAccount']=$wbinfo['WbAccount'];
					$token_insert_data['dtLogintime']=     $nowtime;
					$token_insert_data['sLoginGuid_cs']=   $sLoginGuid_cs;					
					$res=D('Token')->add($token_insert_data);					
			    }
				else
				{					
					$token_update_data=array();
					$token_update_data['dtLogintime']=     $nowtime;
					$token_update_data['sLoginGuid_cs']=   $sLoginGuid_cs;
                    $res=D('Token')->where(array('wbid'=>$wbinfo['WBID']))->save($token_update_data);
				}	
			
				if(empty($res))
				{		
					$data['result']=-3;	
                    $data['msg']='token操作失败';
				}
				else
                {
	               	$exe_sp_version=D('Webini')->where(array('wbid'=>$wbinfo['WBID'],'skey'=>'exe_sp_version'))->getField('svalue');
					if(empty($exe_sp_version) || $exe_sp_version==0)
					{
						$exe_sp_version=0;
					}else if($exe_sp_version==1)
					{
						$exe_sp_version=1;
					}
					
					$shiji_goods_list= D('Newproduct')->field('id,goods_id,goods_name,goods_pinyin,goods_quanpin')->where(array('wbid'=>$wbinfo['WBID'],'deleted'=>0))->select();	
					
					/*
					$yuangonginfo['bh_qx']=$roleinfo['bh_qx'];
					$yuangonginfo['th_qx']=$roleinfo['th_qx'];
					$yuangonginfo['xj_qx']=$roleinfo['xj_qx'];
					$yuangonginfo['ck_qx']=$ck_qx;
					*/
					
				   $data['result']=1; 
				   $data['userinfo']=$yuangonginfo;
                   $data['WbName']=$wbinfo['WbName'];    				   
				   $data['wbid']=$wbinfo['WBID'];			   
				   $data['loginguid']=$sLoginGuid_cs;
                   $data['exe_sp_version']=$exe_sp_version;
				   $data['shiji_goods_list']=$shiji_goods_list;
						   
				}								
		   }
		   else
           {
			    $data['result']=-2;	
                $data['msg']='员工信息错误';					
		   }			   	  	
		}						
		echo  json_encode($data);								 				  
	}
	
	
	public function fnGetOneBarInfo($wbaccount,$loginguid)
	{
		$wbid=D('Token')->where(array('WbAccount'=>$wbaccount,'sLoginGuid_cs'=>$loginguid))->getField('wbid');
		return  $wbid;
	}
	
	public  function API_querygoodsinfo()	
	{	
	    header('Access-Control-Allow-Origin:*');
		$wbaccount=I('get.wbaccount','','string');	
		$loginguid=I('get.loginguid','','string');	
		
		$wbid=$this->fnGetOneBarInfo($wbaccount,$loginguid);
		if(empty($wbid))
		{
			$data['result']=-3;
			$data['msg']='登陆超时';
			echo  json_encode($data);
			return;
		}
		else
        {		
	        $map['wbid']=$wbid;  	 
            $map['deleted']=0;	
            $map['kc_num'] =array('gt',0);	
            $map['is_zuhe'] =array('neq',1);			  
		 	$list= D('Newproduct')->field('id,goods_id,type_id,goods_name,barcode,kc_num,ck_num,xiaoshou_num,shou_price,goods_pinyin,goods_quanpin')->Order('id desc')->where($map)->select();			
		}			
		
		$list2['shiji_goods_list']=$list;
        $list2['result']=1;		
		echo  json_encode($list2);									 				  
	}
	
	
	
	public  function API_querygoodsinfo_moban()	
	{	
	    header('Access-Control-Allow-Origin:*');
		$wbaccount=I('get.wbaccount','','string');	
		$loginguid=I('get.loginguid','','string');			
		$wbid=$this->fnGetOneBarInfo($wbaccount,$loginguid);	
		if(empty($wbid))
		{
			$data['result']=-3;
			$data['msg']='登陆超时';
			echo  json_encode($data);
			return;
		}
		else
        {			      	 		  
		 	$list= D('Productinfomb')->field('goods_id,type_id,goods_name,goods_barcode,goods_image,shou_price,goods_pinyin,goods_quanpin')->select();			
		}			
		
		$list2['shiji_goods_list']=$list;
        $list2['result']=1;		
		echo  json_encode($list2);									 				  
	}
	
	public  function API_querygoodsinfo_khd()
	{	
		header('Access-Control-Allow-Origin:*');
		$wbaccount=I('get.wbaccount','','string');
		
		$page=I('get.page','','string');
		$rows=I('get.num','','string');
		$type_id=I('get.type_id','0','string');
		
		
		
						
		$wbid=D('Token')->where(array('WbAccount'=>$wbaccount))->getField('wbid');	       	
		if(empty($wbid))
		{
			$data['result']=-3;
			$data['msg']='登陆超时';
			echo  json_encode($data);
			return;
		}
		else
        {				  

	        $map=array();
	        $map['wbid']=$wbid;  	 	 
            $map['deleted']=0;
            $map['is_zuhe']=array('neq',2);
			
			if(!empty($type_id))
			{
				$map['type_id']=$type_id;	
			}							
		 	$list= D('Newproduct')->field('id,goods_id,type_id,zuhe_id,is_zuhe,goods_name,kc_num,xiaoshou_num,goods_image,shou_price,goods_pinyin,goods_quanpin')->where($map)->Order('id desc')->page($page,$rows)->select();			
		}
		
		foreach($list as &$val)
		{
			 $val['shou_price']=sprintf("%.2f", $val['shou_price']); 
		}
		

		
		
		$zuhe_goodslist=D('Newproduct')->field('goods_id,kc_num,goods_name')->where(array('wbid'=>$wbid,'deleted'=>0,'is_zuhe'=>2))->select();

		 foreach ($zuhe_goodslist as &$val) {
         	$val['sellNum']=0;
         	$val['is_zuhe']=2;
         	$val['zuhe_id']=$val['goods_id'];
         } 
					      	
		$list2['result']=1;
		$list2['shiji_goods_list']=$list;
		$list2['list']='';
		$list2['zuhe_goods_list']=$zuhe_goodslist;		
		$list2['count']=D('Newproduct')->where(array('wbid'=>$wbid,'deleted'=>0))->count();
		
       

		echo  json_encode($list2);	
									 				  
	}
	
    public  function API_querygoodsinfo2()	
	{	
	    header('Access-Control-Allow-Origin:*');
		$wbaccount=I('get.wbaccount','','string');
        $loginguid=I('get.loginguid','','string');	
		
		$page=I('get.page','','string');
		$rows=I('get.num','','string');
		$type_id=I('get.type_id','0','string');
		$goods_id=I('get.goods_id','','string');
						
		$wbid=$this->fnGetOneBarInfo($wbaccount,$loginguid);       	
		if(empty($wbid))
		{
			$data['result']=-3;
			$data['msg']='登陆超时';
			echo  json_encode($data);
			return;
		}
		else
        {	
            $map=array();		
	        $map['wbid']=$wbid;  	 	 
            $map['deleted']=0;	
            $map['kc_num'] =array('gt',0);	
            $map['is_zuhe'] =array('neq',1);
			if(!empty($type_id))
			{
				$map['type_id']=$type_id;	
			}	
			
			if(!empty($goods_id))
			{
				$map['goods_id']=$goods_id;	
			}				
		 	$list= D('Newproduct')->field('id,goods_id,type_id,goods_name,barcode,kc_num,ck_num,xiaoshou_num,goods_image,shou_price,goods_pinyin,goods_quanpin')->where($map)->Order('id desc')->page($page,$rows)->select();			
		}
					      	
		$list2['result']=1;
		$list2['shiji_goods_list']=$list;

	    $map=array();		
        $map['wbid']=$wbid;  	 	 
        $map['deleted']=0;	
        $map['kc_num'] =array('gt',0);	
        $map['is_zuhe'] =array('neq',1);
		$list2['count']=D('Newproduct')->where($map)->count();
		
		echo  json_encode($list2);									 				  
	}
	
	//查询某商品变动
	public function API_queryOneGoods_mx()
	{		
		header('Access-Control-Allow-Origin:*');
		$wbaccount=I('get.wbaccount','','string');
        $loginguid=I('get.loginguid','','string');	
	
		$page=I('get.page','','string');
		$rows=I('get.num','','string');
		$goods_id=I('get.goods_id','','string');
						
		$wbid=$this->fnGetOneBarInfo($wbaccount,$loginguid);        		
		if(empty($wbid))
		{
			$data['result']=-3;
			$data['msg']='登陆超时';
			echo  json_encode($data);
			return;
		}
		else
        {					
	        $map['wbid']=$wbid; 
			$map['goods_id']=$goods_id;

			$map['kc_num'] =array('gt',1);	
            $map['is_zuhe'] =array('neq',1);
			
			$nowtime=date('Y-m-d H:i:s',time());
		    $lastshiftinfo= D('Newproductjb')->where(array('wbid'=>$wbid))->order('dtEndTime desc')->limit(1)->find();		
		    $lastbegtime=date('Y-m-d H:i:s',strtotime($lastshiftinfo['dtBegTime']));
            $lastendtime=date('Y-m-d H:i:s',strtotime($lastshiftinfo['dtEndTime']));
			
			if(!empty($lastshiftinfo))
			{		     					
				$map['dtInsertTime']=array('BETWEEN',array($lastendtime,$nowtime));	
			}
			else
			{
				$map['dtInsertTime']=array('BETWEEN',array($lastendtime,$nowtime));	
			}	
			          				
		 	$list= D('NewproductmxView')->where($map)->Order('dtInsertTime desc')->page($page,$rows)->select();		
                       		
		}
					      	
		$list2['result']=1;	
        $list2['shiji_goods_list']=$list;			
		$list2['count']=D('NewproductmxView')->where($map)->count();
		
		echo  json_encode($list2);	
	}
	
	
	public function API_goods_add()    //添加商品
	{	   	
	    header('Access-Control-Allow-Origin:*');	 
        $goods_info=file_get_contents("php://input");  
        
		writelog($goods_info,'addgoods');

		
		$goods_info=json_decode($goods_info,true);		

		
		$wbaccount=$goods_info['wbaccount'];
		$loginguid=$goods_info['loginguid'];
		
        $wbid=$this->fnGetOneBarInfo($wbaccount,$loginguid);
		$filepath = C('UPLOAD_SHANGPIN_DIR');
		if(empty($wbid))
		{
			$data['result']=-2;
			$data['msg']='登陆超时';
			echo  json_encode($data);
			return;
		}
		
		$one_goods_name=$goods_info['goods_name'];	
		$token=$goods_info['token'];
					
		$flag=$goods_info['changFlag'];               //使用模板库图片
        if($flag==1)
		{
			$one_goods_image=$goods_info['imgName'];
		}
		else if($flag==2)                            //使用自己上传的图片
        {					  	
			$imgBase64 = $goods_info['imgName'];
			$type = $goods_info['imgtype'];							
			
			if (!file_exists($new_file)) 
			{
				mkdir($new_file,0755,true);
			}
			//图片名字			
			$new_file=getRadomFileName();				  
			$new_file=$new_file.$type;		
			$filepath=$filepath.$new_file;			
			$imgBase64=base64_decode($imgBase64);
	
			if (file_put_contents($filepath,$imgBase64)) 
			{
				$one_goods_image=$new_file;
			}
			else 
			{
				$one_goods_image='';
			}		 						
		}
		
		
        if(file_exists($filepath.$one_goods_image))
		{
			
		}
		else
        {
			$one_goods_image='moren.png';
		} 			
		
		
        if(empty($one_goods_image))
		{
			$one_goods_image='moren.png';
		}	
			
		  			
		$res=D('Token')->checkToken($wbid,$token);
		if($res==-1)
		{
		   $data['result']=-3; 
		   $data['msg']='重复提交';
		   $this->ajaxReturn($data);
		   return;	
		}	
		
		  
		$data['result']=-1;	            		   		
	    if(D('Newproduct')->where(array('wbid'=>$wbid,'goods_name'=>$one_goods_name,'deleted'=>0))->find())
	    {
		   $data['result']=-4; 
		   $data['msg']='商品名称重复';
		   $this->ajaxReturn($data);
		   return;			   
	    }
	   
	    $result=true;
		D()->startTrans();
		$barcode=$goods_info['barcode'];
		$num=$goods_info['num'];
		$type_id=$goods_info['type_id'];
		$shou_price=$goods_info['shou_price'];
        $operate=$goods_info['operate'];
		
	    $goods_insert_data['wbid']=$wbid;
	    $goods_insert_data['goods_id']=D('Newproduct')->max('goods_id')+1;
	    $goods_insert_data['type_id']=$type_id;			   
	    $goods_insert_data['goods_name']=$one_goods_name;	
        $goods_insert_data['goods_image']=$one_goods_image;
		
	    $goods_insert_data['goods_pinyin']= getpinyin($one_goods_name);
	    $goods_insert_data['goods_quanpin']=getAllPY($one_goods_name);	  	   
	    $goods_insert_data['barcode']=$barcode;	   
	    $goods_insert_data['shou_price']=$shou_price;	   
	    $goods_insert_data['dtInsertTime']= date('Y-m-d H:i:s',time());	
		$goods_insert_data['kc_num']= $num;	
	    $goods_insert_data['operate']= $operate;	
	   
		if(D('Newproduct')->add($goods_insert_data)===false)
		{
			$result=false;
		}	
		
				
	   $sxj_insert_data['new_kc_num']= $num;
	   $sxj_insert_data['shangxia_status']= 0;
	   $sxj_insert_data['wbid']= $wbid;
	   $sxj_insert_data['goods_id']= $goods_insert_data['goods_id'];
	   $sxj_insert_data['operate']= $operate;
	   $sxj_insert_data['goods_name']=$one_goods_name;	
	   $sxj_insert_data['dtInsertTime']= date('Y-m-d H:i:s',time());
	   $sxj_insert_data['old_kc_num']= 0;
	   $sxj_insert_data['change_num']= $num;
 
	   //上下架表	      
	   if(D('Newproductsxj')->add($sxj_insert_data)===false)
	   {
		  $result=false;  
	   }
		
			
		if($result)
		{
			D()->commit();
			$data['result']=1; 
            $data['msg']='新增成功';				
		}else
        {
			D()->rollback();
			$data['result']=-1; 
            $data['msg']='新增失败';			
		} 																			 						 						
		$this->ajaxReturn($data);						   	  		   
	}
	
	
	//修改商品库存
	
	public function API_goods_edit()
	{	  
	    
        header('Access-Control-Allow-Origin:*');			  
		$goods_info=file_get_contents("php://input");
		 
		if(empty($goods_info))
		{
			$data['result']=-2;  
			$data['msg']='商品信息为空';		
	        $this->ajaxReturn($data);			
			return;
		}	 	  
	  	 
		$goods_info=json_decode($goods_info,true);		

	   	 		
		$wbaccount=$goods_info['wbaccount'];
		$loginguid=$goods_info['loginguid'];	
		
        $wbid=$this->fnGetOneBarInfo($wbaccount,$loginguid);
		if(empty($wbid))
		{
			$data['result']=-3;
			$data['msg']='登陆超时';
			echo  json_encode($data);
			return;
		}
		$ck_qx= D('Webini')->where(array('wbid'=>$wbid,'skey'=>'FAllowNewproductCk'))->getField('svalue');
		if($ck_qx==1)
		{
		   $ck_qx=1;  
		}
		else
		{
		  $ck_qx=0; 	
		}

		$goods_id=$goods_info['goods_id'];		  
		$one_goods_name=$goods_info['goods_name'];
		$token=$goods_info['token'];	
        $changeflag=$goods_info['changFlag'];
		
		$res=D('Token')->checkToken($wbid,$token);
		if($res==-1)
		{
			 
		   $data['result']=-3; 
		   $data['msg']='数据重复提交';
		   $this->ajaxReturn($data);
		   return;	
		}
		
						
		$barcode=$goods_info['barcode'];
		$change_num=$goods_info['change_num'];
		$type_id=$goods_info['type_id'];
		$shou_price=$goods_info['shou_price'];
		$shangxia_status=$goods_info['shangxia_status'];
		$flag=$goods_info['flag'];   //flag=0  上架   flag=1  下架   flag=2 仅仅修改商品属性
		$operate=$goods_info['operate'];
		
		$post_order_no = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);  //网站订单号
		
		$changeflag=$goods_info['changFlag'];
		if($changeflag==1)
		{					 						
			$imgBase64 = $goods_info['imgName'];									 		
				//获取图片类型   
			$type = $goods_info['imgtype'];									
			//图片保存路径
			$filepath = C('UPLOAD_SHANGPIN_DIR');
			if (!file_exists($new_file)) 
			{
				mkdir($new_file,0755,true);
			}
			//图片名字			
			$new_file=getRadomFileName();				  
			$new_file=$new_file.$type;				
			$filepath=$filepath.$new_file;
			$imgBase64=base64_decode($imgBase64);			
			if (file_put_contents($filepath,$imgBase64)) 
			{
				$one_goods_image=$new_file;
			}
			else 
			{
				$one_goods_image='';
			}
		}						
		if($flag==2)  //不修改数量
		{
			$goods_update_data['type_id']=$type_id;			    	   
			$goods_update_data['barcode']=$barcode;			
			$goods_update_data['shou_price']=$shou_price;
            if($changeflag==1)
			{
				$goods_update_data['goods_image']=$one_goods_image;
			}	       		
			$goods_update_result=D('Newproduct')->where(array('wbid'=>$wbid,'goods_id'=>$goods_id))->save($goods_update_data);
			
			if($goods_update_result)
			{				
				$data['result']=1; 				 
			}
			else
			{			
				$data['result']=-1;
                $data['msg']='修改失败';				
			}  						 						
			$this->ajaxReturn($data);	
			return;		
		}
		
		$kcinfo=D('Newproduct')->field('kc_num,ck_num,goods_name')->where(array('wbid'=>$wbid,'goods_id'=>$goods_id))->find();
        $now_kc_num=$kcinfo['kc_num'];
        $old_ck_num=$kcinfo['ck_num'];		
		if($shangxia_status==1)
		{
			if($change_num > $now_kc_num)
			{						
			  $data['result']=-2;  				 			  						 						
			  $this->ajaxReturn($data);					   	   		   
			}	
		}			
			

	    $result=true;		    	  
	    D()->startTrans(); 	  				
		$nowtime=date('Y-m-d H:i:s',time());
		$lastshiftinfo= D('Newproductjb')->where(array('wbid'=>$wbid))->order('dtEndTime desc')->limit(1)->find();		
		$lastbegtime=date('Y-m-d H:i:s',strtotime($lastshiftinfo['dtBegTime']));
        $lastendtime=date('Y-m-d H:i:s',strtotime($lastshiftinfo['dtEndTime']));		
					
		$dtInsertTime=date('Y-m-d H:i:s',time());
			
		if($ck_qx==0)
		{
		    $sxj_insert_data=array();										
			$sxj_insert_data['old_kc_num']= $now_kc_num;	   
			$sxj_insert_data['change_num']= $change_num;
			if($shangxia_status==0)  //上架
			{
			   $new_kc_num = $now_kc_num+$change_num;
			}
			else  if($shangxia_status==1)  //下架
			{
			   $new_kc_num = $now_kc_num -$change_num;
			}		      	   
			//库存表
			$goods_update_data=array();		
			$goods_update_data['type_id']=$type_id;			    	   
			$goods_update_data['barcode']=$barcode;
			$goods_update_data['kc_num']=$new_kc_num;		
			$goods_update_data['shou_price']=$shou_price;
			if($changeflag==1)
			{
				$goods_update_data['goods_image']=$one_goods_image;
			}
			
			if(D('Newproduct')->where(array('wbid'=>$wbid,'goods_id'=>$goods_id))->save($goods_update_data)===false)
			{
				$result=false;
			}
		   
		   $sxj_insert_data['new_kc_num']= $new_kc_num;
		   $sxj_insert_data['shangxia_status']= $shangxia_status;
		   $sxj_insert_data['wbid']= $wbid;
		   $sxj_insert_data['goods_id']= $goods_id;
		   $sxj_insert_data['operate']= $operate;
		   $sxj_insert_data['goods_name']=D('Newproduct')->where(array('wbid'=>$wbid,'goods_id'=>$goods_id))->getField('goods_name');	
		   $sxj_insert_data['dtInsertTime']= $dtInsertTime;         
		   if(D('Newproductsxj')->add($sxj_insert_data)===false)
		   {
			  $result=false;  
		   }
		}
		else if($ck_qx==1)
        {
			
			$goods_update_data=array();

			if($shangxia_status==0)  //上架
			{
				$new_kc_num = $now_kc_num+$change_num;
			    $goods_update_data['kc_num']=$now_kc_num + $change_num;
                $goods_update_data['ck_num']=$old_ck_num -$change_num;
				$goods_update_data['type_id']=$type_id;			    	   
				$goods_update_data['barcode']=$barcode;				
				$goods_update_data['shou_price']=$shou_price;
				if($changeflag==1)
				{
					$goods_update_data['goods_image']=$one_goods_image;
				}
                if(D('Newproduct')->where(array('wbid'=>$wbid,'goods_id'=>$goods_id))->save($goods_update_data)===false)
				{
					$result=false;
				}	
				
				$sxj_insert_data=array();	
				$sxj_insert_data['post_order_no']= $post_order_no;
				$sxj_insert_data['old_kc_num']= $now_kc_num;	   
				$sxj_insert_data['change_num']= $change_num;
				$sxj_insert_data['new_kc_num']= $new_kc_num;
				$sxj_insert_data['shangxia_status']= $shangxia_status;
				$sxj_insert_data['wbid']= $wbid;
				$sxj_insert_data['goods_id']= $goods_id;
				$sxj_insert_data['operate']= $operate;
				$sxj_insert_data['goods_name']=D('Newproduct')->where(array('wbid'=>$wbid,'goods_id'=>$goods_id))->getField('goods_name');	
				$sxj_insert_data['dtInsertTime']= date('Y-m-d H:i:s',time());         
				if(D('Newproductsxj')->add($sxj_insert_data)===false)
				{
				  $result=false;  
				}

			   
			   //插入一条仓库库存变动 记录
			   
			    $jinhuomx_insert_data=array();
				$jinhuomx_insert_data['post_order_no']= $post_order_no;
			   	$jinhuomx_insert_data['goods_id']=$goods_id;
				$jinhuomx_insert_data['changenum']  =$change_num;
                $jinhuomx_insert_data['old_hj_num']  =$now_kc_num;
				$jinhuomx_insert_data['old_ck_num']  =$old_ck_num;				
				$jinhuomx_insert_data['price']=0;					
				$jinhuomx_insert_data['sumje']=0;
				$jinhuomx_insert_data['jch_type']=4;
				$jinhuomx_insert_data['wbid']=$wbid;
				$jinhuomx_insert_data['dtInsertTime']=$dtInsertTime;
				$jinhuomx_insert_data['operate']=$operate;
	
				if(D('Newproductjchmx')->add($jinhuomx_insert_data)===false)
				{
					$result=false;	
                    writelog('----11------');					
				}
				$jinhuo_insert_data=array();
				$jinhuo_insert_data['post_order_no']=$post_order_no;
				$jinhuo_insert_data['jch_type']=4;
				$jinhuo_insert_data['wbid']=$wbid;
				$jinhuo_insert_data['info']=$kcinfo['goods_name'].':'.$change_num;		
				$jinhuo_insert_data['dtInsertTime']=$dtInsertTime;
				$jinhuo_insert_data['sumje']=0;
				$jinhuo_insert_data['bz']='';
				$jinhuo_insert_data['operate']=$operate;
				
				if(D('Newproductjch')->add($jinhuo_insert_data)===false)
				{
					//writelog('----11------');
					$result=false;
				}			   			   		   
			}
			else  if($shangxia_status==1)  //下架
			{
			    $new_kc_num = $now_kc_num -$change_num;
			    $goods_update_data['kc_num']=$now_kc_num - $change_num;
                $goods_update_data['ck_num']=$old_ck_num +$change_num;
               	$goods_update_data['type_id']=$type_id;			    	   
				$goods_update_data['barcode']=$barcode;				
				$goods_update_data['shou_price']=$shou_price;
				if($changeflag==1)
				{
					$goods_update_data['goods_image']=$one_goods_image;
				}
                if(D('Newproduct')->where(array('wbid'=>$wbid,'goods_id'=>$goods_id))->save($goods_update_data)===false)
				{
					$result=false;
				}				
			   //插入一条仓库库存变动记录
			   
			   	$sxj_insert_data=array();	
				$sxj_insert_data['post_order_no']= $post_order_no;
				$sxj_insert_data['old_kc_num']= $now_kc_num;	   
				$sxj_insert_data['change_num']= $change_num;
				$sxj_insert_data['new_kc_num']= $new_kc_num;
				$sxj_insert_data['shangxia_status']= $shangxia_status;
				$sxj_insert_data['wbid']= $wbid;
				$sxj_insert_data['goods_id']= $goods_id;
				$sxj_insert_data['operate']= $operate;
				$sxj_insert_data['goods_name']=D('Newproduct')->where(array('wbid'=>$wbid,'goods_id'=>$goods_id))->getField('goods_name');	
				$sxj_insert_data['dtInsertTime']= date('Y-m-d H:i:s',time());         
				if(D('Newproductsxj')->add($sxj_insert_data)===false)
				{
				  $result=false;  
				}
			   
			   	$jinhuomx_insert_data=array();
				$jinhuomx_insert_data['post_order_no']= $post_order_no;
			   	$jinhuomx_insert_data['goods_id']=$goods_id;
			    $jinhuomx_insert_data['old_hj_num']  =$now_kc_num;
				$jinhuomx_insert_data['old_ck_num']  =$old_ck_num;	
				$jinhuomx_insert_data['changenum']  =$change_num;																	 				
				$jinhuomx_insert_data['price']=0;					
				$jinhuomx_insert_data['sumje']=0;
				$jinhuomx_insert_data['jch_type']=3;
				$jinhuomx_insert_data['wbid']=$wbid;
				$jinhuomx_insert_data['dtInsertTime']=$dtInsertTime;
				$jinhuomx_insert_data['operate']=$operate;
	
				if(D('Newproductjchmx')->add($jinhuomx_insert_data)===false)
				{
					$result=false;					
				}
				$jinhuo_insert_data=array();
				$jinhuo_insert_data['post_order_no']=$post_order_no;
				$jinhuo_insert_data['jch_type']=3;
				$jinhuo_insert_data['wbid']=$wbid;
				$jinhuo_insert_data['info']=$kcinfo['goods_name'].':'.$change_num;		
				$jinhuo_insert_data['dtInsertTime']=$dtInsertTime;
				$jinhuo_insert_data['sumje']=0;
				$jinhuo_insert_data['bz']='';
				$jinhuo_insert_data['operate']=$operate;
				
				if(D('Newproductjch')->add($jinhuo_insert_data)===false)
				{
					//writelog('----11------');
					$result=false;
				}							 		 		   
			}		      	   									   
		}			
					
	
	     				   						 
		if($result)
		{	
	        writelog('--jiban-4---','API_goods_edit');
			D()->commit();    //提交				
			$data['result']=1; 	
            $data['msg']='修改成功'; 			
		}
		else
		{
			 writelog('--jiban-5---','API_goods_edit');
			D()->rollback();    //回滚 
			$data['result']=-1;  
            $data['msg']='修改失败';			
		}  						 						
		$this->ajaxReturn($data);		
       
		
	}
	
	
	
	
	 //商品销售
		 
	public  function API_postsellgoodslist()  
	{	
	    header('Access-Control-Allow-Origin:*');
		$goods_info=file_get_contents("php://input");			
		$goods_info=json_decode($goods_info,true);	
        $wbaccount=$goods_info['wbaccount'];
		$loginguid=$goods_info['loginguid'];	
		
        $wbid=$this->fnGetOneBarInfo($wbaccount,$loginguid);
		if(empty($wbid))
		{
			$data['result']=-3;
			$data['msg']='登陆超时';
			echo  json_encode($data);
			return;
		}
				
	    $post_order_no = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);  //网站订单号
	    $post_order_no='XS'.$post_order_no;
        $dtInsertTime=date('Y-m-d H:i:s',time());   
		
		if(!empty($goods_info))
		{				
			$sumje=$goods_info['sumje'];
            $sumnum=$goods_info['sumNum'];			
			$paytype=$goods_info['paytype'];
			$ordertype=$goods_info['ordertype'];
			$operate=$goods_info['operate'];
			$token=$goods_info['token'];	
            	 			
			$res=D('Token')->checkToken($wbid,$token);
			if($res==-1)
			{
			   $data['result']=-3; 
			   $data['msg']='数据重复提交';
			   $this->ajaxReturn($data);
			   return;	
			}
				
			
			$xiaoshou_goodslist=$goods_info['goods_list'];
			
			
			$all_goods_list=D('Newproduct')->where(array('wbid'=>$wbid))->getField('goods_id,goods_name');
						
			$result=true;
			D()->startTrans();  //启用事务
			foreach( $xiaoshou_goodslist as &$val)
			{		
			    $xiaoshoumx_insert_data=array(); 
			    if(array_key_exists($val['goods_id'],$all_goods_list))
                {
					$xiaoshoumx_insert_data['goods_name']=$all_goods_list[$val['goods_id']];
				}               
                $xiaoshoumx_insert_data['wbid']=$wbid;	
                $xiaoshoumx_insert_data['type_id']=$val['type_id'];              				
                $xiaoshoumx_insert_data['goods_id']=$val['goods_id'];				
				$xiaoshoumx_insert_data['xiaoshou_num']     =$val['sellNum'];
				$xiaoshoumx_insert_data['kc_num']  =D('Newproduct')->where(array('wbid'=>$wbid,'goods_id'=>$val['goods_id']))->getField('kc_num');			
				$xiaoshoumx_insert_data['shou_price']=$val['shou_price'];				
				$xiaoshoumx_insert_data['post_order_no']=$post_order_no;
                $xiaoshoumx_insert_data['paytype']=$paytype;
			    $xiaoshoumx_insert_data['ordertype']=$ordertype;							
				$xiaoshoumx_insert_data['operate']=$operate;
				$xiaoshoumx_insert_data['dtInsertTime']=$dtInsertTime;	
                $xiaoshoumx_insert_data['je']=$val['shou_price']* $val['sellNum'];															 												
						
				if(D('Newproductxsmx')->add($xiaoshoumx_insert_data)===false)
				{		
                    writelog('---1--1--err-','xiaoshou');			
					$result=false;
				}
				  
				$now_hjkc_num= $xiaoshoumx_insert_data['kc_num'];
				if($val['sellNum'] >= $now_hjkc_num)
				{
					$now_sj_xiaoshou_num =$now_hjkc_num;
				}
				else
				{
					$now_sj_xiaoshou_num =$val['sellNum'];
				}		
				   
               	
               
			    if($ordertype==3)   //退货
				{
					if(D('Newproduct')->where(array('wbid'=>$wbid,'goods_id'=>$val['goods_id']))->setInc('kc_num',$now_sj_xiaoshou_num)===false)
					{		
						 writelog('--2-1---err--','xiaoshou');			
						$result=false;
					}
					
				}
				else   //正常销售
				{
					if(D('Newproduct')->where(array('wbid'=>$wbid,'goods_id'=>$val['goods_id']))->setDec('kc_num',$now_sj_xiaoshou_num)===false)
					{		
						writelog('--2-3---err--','xiaoshou');			
						$result=false;
					}
					
					if(D('Newproduct')->where(array('wbid'=>$wbid,'goods_id'=>$val['goods_id']))->setInc('xiaoshou_num',$now_sj_xiaoshou_num)===false)
					{		
						writelog('--2-4---err--','xiaoshou');			
						$result=false;
					}
					
					
				}	               																		 			
				$xiaoshouinfo.= $val['goods_name'].':'.$val['xiaoshou_num'].' ';
			}
			
																		
							         
			$xiaoshou_insert_data['post_order_no']=$post_order_no;		
			$xiaoshou_insert_data['wbid']=$wbid;
			$xiaoshou_insert_data['info']=$xiaoshouinfo;		
			$xiaoshou_insert_data['sum_sr_je']=$sumje;		
			$xiaoshou_insert_data['bz']='exe销售';
			$xiaoshou_insert_data['paytype']=$paytype;
			$xiaoshou_insert_data['ordertype']=$ordertype;
			$xiaoshou_insert_data['operate']=$operate;							
			$xiaoshou_insert_data['dtInsertTime']=$dtInsertTime;
			$xiaoshou_insert_data['sumnum']=$sumnum;
			$xiaoshou_insert_data['cpname']='吧台';
			$xiaoshou_insert_data['detailinfo']=json_encode($xiaoshou_goodslist);
		
			if(D('Newproductxs')->add($xiaoshou_insert_data)===false)
			{					
				$result=false;
				writelog('---3--1-err--','xiaoshou');
			}
		
			
			if($result)
            {
				writelog('---4-----','xiaoshou');
               D()->commit();  //提交事务           
               $data['result']=1;
			   $data['msg']='销售成功';
            }
            else
            {
				writelog('---5-----','xiaoshou');
               D()->rollback();    //回滚
               $data['result']=-1;
			    $data['msg']='销售失败1';
            }								
			$this->ajaxReturn($data);
							
		}
		else
        {
			writelog('---6-----','xiaoshou');
			$data['result']=-2;
			$data['msg']='销售失败2';
			$this->ajaxReturn($data);
		} 
        	
	}
	
	
	
	
	
	public function API_goods_delete()
	{	  
        header('Access-Control-Allow-Origin:*');		  
		$goods_info=file_get_contents("php://input");
		if(empty($goods_info))
		{
			$data['result']=-2;
            $data['msg']='商品信息为空';			
			return;
		}	   
		$goods_info=json_decode($goods_info,true);		 		 	  
		$wbaccount=$goods_info['wbaccount'];
		$loginguid=$goods_info['loginguid'];	

        $wbid=$this->fnGetOneBarInfo($wbaccount,$loginguid);
		if(empty($wbid))
		{
			$data['result']=-3;
			$data['msg']='登陆超时';
			echo  json_encode($data);
			return;
		}
		
		$kc_num=D('Newproduct')->where(array('wbid'=>$wbid,'goods_id'=>$goods_info['goods_id']))->getField('kc_num');
		if($kc_num >0 )
		{
			$data['result']=-2;
			$data['msg']='该商品库存不为空,禁止删除';
			echo  json_encode($data);
			return;
		}	
		
		
		$nowtime=date('Y-m-d H:i:s',time());
		$lastshiftinfo= D('Newproductjb')->where(array('wbid'=>$wbid))->order('dtEndTime desc')->limit(1)->find();
		$lastshifttime=date('Y-m-d H:i:s',strtotime($lastshiftinfo['dtEndTime']));
		
		$map=array();
		$map['wbid']=$wbid;
		$map['goods_id']=$goods_info['goods_id'];
		
		if(!empty($lastshiftinfo))
		{
			$map['dtInsertTime']=array('BETWEEN',array($lastshifttime,$nowtime));
		}
       	        		  
		$bFind=D('Newproductxsmx')->where($map)->find(); 
		if($bFind)
		{			
			$data['result']=-1;
            $data['msg']='本班该商品有销售,不可删除';
			$this->ajaxReturn($data);	
			return;
		}	
		
		$goods_id=$goods_info['goods_id'];		  										
	    $result=D('Newproduct')->where(array('wbid'=>$wbid,'goods_id'=>$goods_id))->setField('deleted',1);						   		 				 
		if($result)
		{								
			$data['result']=1;
            $data['msg']='删除成功';			
		}
		else
		{			 
			$data['result']=-1;
            $data['msg']='删除失败';			
		}  						 						
		$this->ajaxReturn($data);					
	   	   		   
	}
	
	
	public function API_goods_xiaoshou_list()
	{	  
        header('Access-Control-Allow-Origin:*');		   	
		$wbaccount=I('get.wbaccount','','string');
		$loginguid=I('get.loginguid','','string');	
        $wbid=$this->fnGetOneBarInfo($wbaccount,$loginguid);
		if(empty($wbid))
		{
			$data['result']=-3;
			$data['msg']='登陆超时';
			echo  json_encode($data);
			return;
		}
		
		$page=I('get.page','','string');
		$rows=I('get.num','','string');
		$ordertype=I('get.ordertype','0','string');	
		$paytype=I('get.paytype','0','string');
        $flag=I('get.flag','1','string');
		
		$map['wbid']=$wbid;
		if(!empty($paytype))
		{
			$map['paytype']=$paytype;
		}
										
	    $nowtime=date('Y-m-d H:i:s',time());
		$lastshiftinfo= D('Newproductjb')->where(array('wbid'=>$wbid))->order('dtEndTime desc')->limit(1)->find();
		
		$lastbegtime=date('Y-m-d H:i:s',strtotime($lastshiftinfo['dtBegTime']));
        $lastendtime=date('Y-m-d H:i:s',strtotime($lastshiftinfo['dtEndTime']));		
		if(!empty($lastshiftinfo))
		{
			if($flag==1)    //本班
			{
				$map['dtInsertTime']=array('BETWEEN',array($lastendtime,$nowtime));
				if(!empty($ordertype))
				{
					$map['ordertype']=$ordertype;
				}else
                {
					$map['ordertype']=array('neq',2);
				}					
			}
			else if($flag==2)//上班
			{
				$map['dtInsertTime']=array('BETWEEN',array($lastbegtime,$lastendtime));
				if(!empty($ordertype))
				{
					$map['ordertype']=$ordertype;
				}
			}
		}
		else
        {
			$map['dtInsertTime']=array('BETWEEN',array($lastendtime,$nowtime));
		}											
	    $list=D('Newproductxs')->field('id,dtInsertTime,paytype,cpname,post_order_no,detailinfo,sumnum,sum_sr_je,ordertype,bz')
		->where($map)->order('id desc')->page($page,$rows)->select();	
  
		if($list)
		{								
			$data['result']=1; 				
			foreach($list as &$val)
			{
				$val['detailinfo']=json_decode($val['detailinfo'],true);
				if($val['ordertype'] >3 && $val['paytype'] ==1)
				{
					$val['cpname']=$val['cpname'].'<br>'.$val['bz'];
				}	
 				$val['sum_sr_je']=sprintf("%.2f", $val['sum_sr_je']); 
			}
			$data['list']=$list; 
            $data['count']=D('Newproductxs')->where($map)->count();
			
			$map['ordertype']=array('eq',5);
			$data['nodealcount']=D('Newproductxs')->where($map)->count(); 
			
		}
		else
		{			 
			$data['result']=-1;  
            $data['msg']='数据为空';			
		}  						 						
		$this->ajaxReturn($data);					
	   	   		   
	}
	
	
	
	//补货列表
	
	public function API_goods_buhuo_list()
	{	  
        header('Access-Control-Allow-Origin:*');		  

		$wbaccount=I('get.wbaccount','','string');
		$loginguid=I('get.loginguid','','string');			
        $wbid=$this->fnGetOneBarInfo($wbaccount,$loginguid);
		if(empty($wbid))
		{
			$data['result']=-3;
			$data['message']='登陆超时';
			echo  json_encode($data);
			return;
		}
	
		$page=I('get.page','','string');
		$rows=I('get.num','','string');
		$flag=I('get.flag','1','string');
		
		$nowtime=date('Y-m-d H:i:s',time());
		$lastshiftinfo= D('Newproductjb')->where(array('wbid'=>$wbid))->order('dtEndTime desc')->limit(1)->find();		
		$lastbegtime=date('Y-m-d H:i:s',strtotime($lastshiftinfo['dtBegTime']));
        $lastendtime=date('Y-m-d H:i:s',strtotime($lastshiftinfo['dtEndTime']));		
		if(!empty($lastshiftinfo))
		{
			if($flag==1)
			{
				//本班
				$map['dtInsertTime']=array('BETWEEN',array($lastendtime,$nowtime));
			}
			else if($flag==2)
			{
				//前班
				$map['dtInsertTime']=array('BETWEEN',array($lastbegtime,$lastendtime));
			}
		}
		else
        {
			$map['dtInsertTime']=array('BETWEEN',array($lastendtime,$nowtime));
		}
		$map['wbid']=$wbid;				  										
	    $list=D('Newproductsxj')->field('id,goods_id,goods_name,old_kc_num,change_num,new_kc_num,shangxia_status,dtInsertTime')->where($map)->order('id desc')->page($page,$rows)->select();	 
		if($list)
		{								
			$data['result']=1; 		
			$data['list']=$list; 		
            $data['count']=D('Newproductsxj')->where($map)->count(); 			
		}
		else
		{		 
			$data['result']=-1; 
            $data['msg']='数据为空';			
		}  						 						
		$this->ajaxReturn($data);						   	   		   
	}
	
	
	//获取交班数据
	
	
	public function API_query_goods_jiaobanlist()
	{
		header('Access-Control-Allow-Origin:*');			  	  
		$wbaccount=I('get.wbaccount','','string');
		$loginguid=I('get.loginguid','','string');			
        $wbid=$this->fnGetOneBarInfo($wbaccount,$loginguid);
		if(empty($wbid))
		{
			$data['result']=-3;
			$data['msg']='超时登陆';
			echo  json_encode($data);
			return;
		}

		$nowtime=date('Y-m-d H:i:s',time());
		$lastshiftinfo= D('Newproductjb')->where(array('wbid'=>$wbid))->order('dtEndTime desc')->limit(1)->find();
		$lastshifttime=date('Y-m-d H:i:s',strtotime($lastshiftinfo['dtEndTime']));
		//获取商品列表
		$all_goods_list=D('Newproduct')->field('id,wbid,goods_id,type_id,shou_price,goods_name,kc_num,ku_num_temp')->where(array('wbid'=>$wbid,'deleted'=>0))->order(' id desc ')->select();
		
		$map1=array();
		if(!empty($lastshiftinfo))
		{
			$map1['dtInsertTime']=array('BETWEEN',array($lastshifttime,$nowtime));
		}
		
		if($lastshiftinfo)
		{
			$lastshiftgoods_str=$lastshiftinfo['detailinfo'];
			$lastshiftgoods_list=json_decode($lastshiftgoods_str,true);
		}	
		
			
		$map1['wbid']=$wbid;			
		$lastshift_goodskc_list=D('Newproductxsmx')->field('goods_id,ordertype,sum(xiaoshou_num) as xiaoshou_num')->group('goods_id,ordertype')->where($map1)->select();	
		foreach($all_goods_list as &$val)
		{						
			$bFind=false;
			foreach($lastshift_goodskc_list as &$val2)
			{
				if($val['goods_id']==$val2['goods_id'])
				{	
                    if($val2['ordertype']==1)
                    {
						$xiaoshou_num=$val2['xiaoshou_num'];
					}
					else if($val2['ordertype']==3)
                    {
						$tuihuo_num= $val2['xiaoshou_num'];
					}
					else if($val2['ordertype']==4)
                    {
						$xiaoshou_num= $val2['xiaoshou_num'];
					}											                 					
					$bFind=true;						
					break;
				}
			}
			if($bFind==false)
			{
				$xiaoshou_num=0;
				$tuihuo_num  =0;
			}
			$val['xiaoshou_num']=$xiaoshou_num - $tuihuo_num;
			$val['sumje']=$val['xiaoshou_num'] *$val['shou_price'];
			
									
			$bFind1=false;
			foreach($lastshiftgoods_list as &$val3)
			{
				if($val['goods_id']==$val3['goods_id'])
				{	
 			        $old_kc_num= $val3['now_kc_num'];
					$bFind1=true;						
					break;
				}
			}
			if($bFind1==false)
			{
				$old_kc_num=0;	
			}
			$val['old_kc_num']= $old_kc_num;											
		}
		
		$i=0;
        $shiji_goods_list=array();	
		foreach($all_goods_list as &$val)
		{
			if($val['kc_num']==0 && $val['xiaoshou_num']==0 && $val['old_kc_num']==0)
			{
				
			}
			else
            {
				$shiji_goods_list[$i]['id']=$val['id'];
				$shiji_goods_list[$i]['goods_id']=$val['goods_id'];
				$shiji_goods_list[$i]['type_id']=$val['type_id'];
				
				$shiji_goods_list[$i]['shou_price']=$val['shou_price'];
				$shiji_goods_list[$i]['goods_name']=$val['goods_name'];
				$shiji_goods_list[$i]['kc_num']=$val['kc_num'];
				$shiji_goods_list[$i]['kc_num_temp']=$val['ku_num_temp'];
				$shiji_goods_list[$i]['xiaoshou_num']=$val['xiaoshou_num'];
				$shiji_goods_list[$i]['sumje']=$val['sumje'];
				$shiji_goods_list[$i]['old_kc_num']=$val['old_kc_num'];
				$i++;
			}				
		}

		$list['all_goods_list']=$shiji_goods_list;
						
		$map1=array();
		$map1['dtInsertTime']=array('BETWEEN',array($lastshifttime,$nowtime));
		$map1['wbid']=$wbid;
		
		$sumje_nocash_sjsr=D('Newproductzf')->where($map1)->sum('sp_je');
		if(empty($sumje_nocash_sjsr))
		{
			$sumje_nocash_sjsr=0;
		}
			
		
		$list['sumje_nocash_sjsr']=(float)sprintf("%.2f",$sumje_nocash_sjsr); 
		$moneylist=D('Newproductxs')->Field('paytype,ordertype,sum(sum_sr_je) as sum_je')->group('paytype,ordertype')->where($map1)->select();
		
		foreach($moneylist as &$val)
		{
	
			if($val['paytype']==1 && $val['ordertype']==1)
			{
				$list['sumje_cash_xs']=$val['sum_je'];	
			}
			/*
            if($val['paytype']==1 && $val['ordertype']==4)
			{
				$list['sumje_cash_xs_khd']=$val['sum_je'];	
			}			
			*/
			if($val['paytype']==1 && $val['ordertype']==2)
			{
				$list['sumje_cash_jbxs']=$val['sum_je'];
			}
			
			if($val['paytype']==1 && $val['ordertype']==3)
			{
				$list['sumje_cash_th']=$val['sum_je'];
			}
			
			if($val['paytype']==2 && $val['ordertype']==1 )
			{
				$list['sumje_nocash_xs']=$val['sum_je'];
			}
			/*
		    if($val['paytype']==2 && $val['ordertype']==4)
			{
				$list['sumje_nocash_xs_khd']=$val['sum_je'];	
			}
            */
			
			if($val['paytype']==2 && $val['ordertype']==3)
			{
				$list['sumje_nocash_th']=$val['sum_je'];
			}
			
			if($val['paytype']==3 && $val['ordertype']==1)
			{
				$list['sumje_other_xs']=$val['sum_je'];
			}						
			if($val['paytype']==3 && $val['ordertype']==3)
			{
				$list['sumje_other_th']=$val['sum_je'];
			}
					
		}
		
		if(empty($list['sumje_cash_xs']))
		{
			$list['sumje_cash_xs']=0;
		}
		else
        {
			$list['sumje_cash_xs']=(float)sprintf("%.2f", $list['sumje_cash_xs']); 
		} 			
		
		if(empty($list['sumje_cash_jbxs']))
		{
			$list['sumje_cash_jbxs']=0;
		}else
        {
			$list['sumje_cash_jbxs']=(float)sprintf("%.2f", $list['sumje_cash_jbxs']); 
		} 	
		
		if(empty($list['sumje_cash_th']))
		{
			$list['sumje_cash_th']=0;
		}else
        {
			$list['sumje_cash_th']=(float)sprintf("%.2f", $list['sumje_cash_th']); 
		} 	
		
		if(empty($list['sumje_nocash_xs']))
		{
			$list['sumje_nocash_xs']=0;
		}else
        {
			$list['sumje_nocash_xs']=(float)sprintf("%.2f", $list['sumje_nocash_xs']); 
		}	
		
		if(empty($list['sumje_nocash_th']))
		{
			$list['sumje_nocash_th']=0;
		}else
        {
			$list['sumje_nocash_th']=(float)sprintf("%.2f", $list['sumje_nocash_th']); 
		}	
	
	    if(empty($list['sumje_other_xs']))
		{
			$list['sumje_other_xs']=0;
		}else
        {
			$list['sumje_other_xs']=(float)sprintf("%.2f", $list['sumje_other_xs']); 
		}	
		
		
		if(empty($list['sumje_other_th']))
		{
			$list['sumje_other_th']=0;
		}else
        {
			$list['sumje_other_th']=(float)sprintf("%.2f", $list['sumje_other_th']); 
		}	
		
		/*
	    if(empty($list['sumje_nocash_xs_khd']))
		{
			$list['sumje_nocash_xs_khd']=0;
		}else
        {
			$list['sumje_nocash_xs_khd']=(float)sprintf("%.2f", $list['sumje_nocash_xs_khd']); 
		}
		
		if(empty($list['sumje_cash_xs_khd']))
		{
			$list['sumje_cash_xs_khd']=0;
		}else
        {
			$list['sumje_cash_xs_khd']=(float)sprintf("%.2f", $list['sumje_cash_xs_khd']); 
		}
		*/
		
		
		
   

		
		
		$list['sum_sr_je']=$list['sumje_cash_xs']+$list['sumje_cash_jbxs']-$list['sumje_cash_th']+$list['sumje_nocash_xs']-$list['sumje_nocash_th']+$list['sumje_other_xs']-$list['sumje_other_th'];
		
		$list['sum_sr_je']=(float)sprintf("%.2f", $list['sum_sr_je']);
        $list['to_nextshift_je']=(float)sprintf("%.2f", $lastshiftinfo['to_nextshift_je']);
		
		
        $list['shangbantime']=$lastshifttime;	
        
        $list['endtime']=$nowtime;		
        $list['result']=1; 
		
		$map=array();
		$map['wbid']=$wbid;
        $map['ordertype']=array('eq',5);
		$list['nodealcount']=D('Newproductxs')->where($map)->count();

		$this->ajaxReturn($list);
			
	}
	
	
	
	

   
	public function API_goods_jiaoban_edit()
	{	  
        header('Access-Control-Allow-Origin:*');			  
		$goods_info=file_get_contents("php://input");


			    	
		if(empty($goods_info))
		{
			$data['result']=-2; 
            $data['msg']='提交数据为空';			
			return;
		}	 	  
		
	
		$goods_info=json_decode($goods_info,true);		 		  

		$wbaccount=$goods_info['wbaccount'];
		$loginguid=$goods_info['loginguid'];	

        $wbid=$this->fnGetOneBarInfo($wbaccount,$loginguid);
		if(empty($wbid))
		{
			$data['result']=-3;
			$data['msg']='登陆超时';
			echo  json_encode($data);
			return;
		}


		
		$sum_sr_je    =$goods_info['sum_sr_je'];
		$sum_cash_je  =$goods_info['sumje_cash'];
		$sum_nocash_je=$goods_info['sumje_nocash'];
		$sum_other_je =$goods_info['sumje_other'];
		
		$operate_db =$goods_info['dbyg_str'];
		$operate_jb =$goods_info['jbyg_str'];
		$beizhu =$goods_info['beizhu'];		
		$jbr_password =$goods_info['jbpassword'];

		$to_nextshift_je=$goods_info['to_nextshift_je']; //留给下班的金额
		if(empty($to_nextshift_je))
		{
           $to_nextshift_je=0;
		}	

	//	echo $to_nextshift_je;
	//	return;
		
		
		$from_last_je=$goods_info['from_last_je']; //上班留下的金额
		if(empty($from_last_je))
		{
           $from_last_je=0;
		}	
		
								
		$bLogin= $yuangonginfo=D('Yuangong')->field('id,WB_ID,name,boss_qx')->where(array('WB_ID'=>$wbid,'pw'=>md5($jbr_password.'!@#BGS159357'),'name'=>$operate_jb))->find();
		if(empty($bLogin))
		{
		   $data['result']=-4; 
		   $data['msg']='交班人密码错误'; 
		   $this->ajaxReturn($data);
		   return;	
		}
		else
        {
		   $map=array();
		   $map['name']=array('neq',$operate_jb);
		   $map['WB_ID']=$wbid;
		   $yuangonglist=D('Yuangong')->field('id,name,boss_qx')->where($map)->select();	
		}		

		
		
		$token=$goods_info['token'];		
		$res=D('Token')->checkToken($wbid,$token);
		if($res==-1)
		{
		   $data['result']=-3; 
		   $this->ajaxReturn($data);
		   return;	
		}
			
				
		$jiaoban_goods_list=$goods_info['goods_list'];	       		
		$dtInsertTime= $goods_info['endtime']; //本班结束时间
	
		$i=0;
		$list=array();
		//获取所有商品列表
		$all_goods_list=D('Newproduct')->field('id,wbid,goods_id,shou_price,goods_name,kc_num')->where(array('wbid'=>$wbid,'deleted'=>0))->select();		
		foreach($all_goods_list as $val)
		{							
			$bFind=false;
			foreach($jiaoban_goods_list as $val2)
			{
				if($val['goods_id']==$val2['goods_id'])             //有销量的商品         
				{						
					$list[$i]['goods_id']=$val2['goods_id'];
					$list[$i]['old_kc_num']=$val2['old_kc_num'];
					$list[$i]['now_kc_num']   =$val2['kc_num'];	
					$list[$i]['jb_xs_num']=$val2['NewXiaoShouSl'];	
                    $list[$i]['xiaoshou_num']=$val2['yuanxiaoshousl'];															
					$bFind=true;						
					break;
				}	
			}
			
			if($bFind==false)               //无销量的商品
			{				 
				$map=array();
				$list[$i]['goods_id']=$val['goods_id'];				
				$list[$i]['now_kc_num']=$val['kc_num'];
				$list[$i]['jb_xs_num']=0;					
				$list[$i]['old_kc_num']=$val['kc_num'];	
                $list[$i]['xiaoshou_num']=0;				
			}	
			$i++;			
		}
		
		
			
	    $result=true;		    	  
	    D()->startTrans(); 	  	
						
		$lastshiftinfo= D('Newproductjb')->where(array('wbid'=>$wbid))->order('id desc')->limit(1)->find();
		$lastshifttime=date('Y-m-d H:i:s',strtotime($lastshiftinfo['dtEndTime']));					
		// 当前所有商品列表（ ）	
		$post_order_no = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);  //网站订单号
	    $post_order_no='JB'.$post_order_no;
		

		
		$temptime=strtotime($dtInsertTime);
		$dtJiaobanXiaoshouTime=date('Y-m-d H:i:s',strtotime('-2 second ',$temptime));
		
		$sum_sr_je_jbxs = 0;
		$sum_num_jbxs = 0;		
		$detailinfo_jbxs = '';
		
		$list_jbxs=array();		
		$j=0;
		$onebar_allgoods_list=D('Newproduct')->where(array('wbid'=>$wbid))->getField('goods_id,goods_name');
		foreach($jiaoban_goods_list as &$val)
		{
			writelog('--4----');	
            if($val['NewXiaoShouSl'] >0)
            {
				//交班销售数量
				$xiaoshoumx_insert_data=array();
				$xiaoshoumx_insert_data['type_id']=$val['type_id'];
				$xiaoshoumx_insert_data['goods_id']=$val['goods_id'];
			    if(array_key_exists($val['goods_id'],$onebar_allgoods_list))
                {
					$xiaoshoumx_insert_data['goods_name']=$onebar_allgoods_list[$val['goods_id']];
				} 
                				
				$xiaoshoumx_insert_data['xiaoshou_num']   =$val['NewXiaoShouSl'];
				$xiaoshoumx_insert_data['kc_num']  =$val['ykc'];    //销售时候货架上的数量
				$xiaoshoumx_insert_data['je']=$val['shou_price']*$val['NewXiaoShouSl'];
				$xiaoshoumx_insert_data['shou_price']=$val['shou_price'];				
				$xiaoshoumx_insert_data['post_order_no']=$post_order_no;
				$xiaoshoumx_insert_data['ordertype']=2;    //交班销售
				$xiaoshoumx_insert_data['wbid']=$wbid;
				$xiaoshoumx_insert_data['operate']=$operate_db;
				
				
				$xiaoshoumx_insert_data['dtInsertTime']=$dtJiaobanXiaoshouTime;																				
				if(D('Newproductxsmx')->add($xiaoshoumx_insert_data)===false)
				{					
					$result=false;
					  writelog('---1--error--','jiaoban');
				}	
				
				if(D('Newproduct')->where(array('wbid'=>$wbid,'goods_id'=>$val['goods_id']))->setDec('kc_num',$val['NewXiaoShouSl'])===false)
				{
					writelog('---2--error--','jiaoban');
					$result=false;				
				}
				
				if(D('Newproduct')->where(array('wbid'=>$wbid,'goods_id'=>$val['goods_id']))->setInc('xiaoshou_num',$val['NewXiaoShouSl'])===false)
				{		
					writelog('--2-4---err--','xiaoshou');			
					$result=false;
				}
				
		        $list_jbxs[$j]['type_id']=$val['type_id'];
				$list_jbxs[$j]['goods_id']=$val['goods_id'];
				$list_jbxs[$j]['goods_name']=$val['goods_name'];
				$list_jbxs[$j]['sellNum']=$val['NewXiaoShouSl'];
				$list_jbxs[$j]['shou_price']=$val['shou_price'];
				$list_jbxs[$j]['totalPrice']=$xiaoshoumx_insert_data['je'];
				$list_jbxs[$j]['kc_num']    =$val['ykc'];
				
				
				$sum_sr_je_jbxs+=$xiaoshoumx_insert_data['je'];
				$sum_num_jbxs+=$xiaoshoumx_insert_data['xiaoshou_num'];
				$j++;
			}								
		}
		

		if(D('Newproduct')->where(array('wbid'=>$wbid))->setField('ku_num_temp',0)===false)
		{		
			writelog('--2-4---err--','xiaoshou');			
			$result=false;
		}
		
		

       
	   if(!empty($list_jbxs))
	   {
		   		//增加一条交班销售总记录				         
			$xiaoshou_insert_data['post_order_no']=$post_order_no;
			$xiaoshou_insert_data['ordertype']=2;    //交班销售
			$xiaoshou_insert_data['wbid']=$wbid;
			//$xiaoshou_insert_data['info']=$jiaobaninfo;
			$xiaoshou_insert_data['paytype']=1;     //现金支付方式		
			$xiaoshou_insert_data['sum_sr_je']=$sum_sr_je_jbxs;
			$xiaoshou_insert_data['sumnum']=$sum_num_jbxs;		
			$xiaoshou_insert_data['detailinfo']=json_encode($list_jbxs);		
			$xiaoshou_insert_data['bz']='交班销售';
			$xiaoshou_insert_data['operate']=$operate_db;							
			$xiaoshou_insert_data['dtInsertTime']=$dtJiaobanXiaoshouTime;
															
			if(D('Newproductxs')->add($xiaoshou_insert_data)===false)
			{			
				writelog('---3--error--','jiaoban');  	
				$result=false;
			}
	   }	   
											
		//增加一条正常交班总记录			
		$jiaoban_insert_data['post_order_no']=$post_order_no;			
		$jiaoban_insert_data['wbid']=$wbid;
		$jiaoban_insert_data['info']=$info;			
        $jiaoban_insert_data['dtBegTime']=$lastshifttime;
		$jiaoban_insert_data['dtEndTime']=$dtInsertTime;		
		$jiaoban_insert_data['dtInsertTime']=$dtInsertTime;
		$jiaoban_insert_data['bz']=$beizhu;
		$jiaoban_insert_data['sumje']=$sum_sr_je;						
		$jiaoban_insert_data['sum_cash_je']=$sum_cash_je;
		$jiaoban_insert_data['sum_nocash_je']=$sum_nocash_je;
		$jiaoban_insert_data['sum_other_je']=$sum_other_je;																									
		//$jiaoban_insert_data['operate']=session('username');
		$jiaoban_insert_data['detailinfo']=json_encode($list);		
		$jiaoban_insert_data['operate_db']=$operate_db;
		$jiaoban_insert_data['operate_jb']=$operate_jb;


        $jiaoban_insert_data['from_last_je']=$from_last_je; 
        $jiaoban_insert_data['to_nextshift_je']=$to_nextshift_je;
		
		

		if(D('Newproductjb')->add($jiaoban_insert_data)===false)
		{	
            writelog('---4--error--','jiaoban');	
			$result=false;
		}


		//增加一条交班时  所有商品的仓库库存数据
        
		/*
        $map=array();
        $map['wbid']=$wbid;
        $map['is_zuhe']=array('neq',2);
        $map['deleted']=0;

		$all_shiji_goodslist=D('Newproduct')->field('goods_id,kc_num,_num')->where($map)->select();
		writelog('--8--2--');
    
        $jiaobankc_insert_data=array();
        $jiaobankc_insert_data['post_order_no']=$post_order_no;
        $jiaobankc_insert_data['wbid']=session('wbid');
        $jiaobankc_insert_data['detailinfo']=json_encode($all_shiji_goodslist);
        $jiaobankc_insert_data['dtInsertTime']=$dtJiaobanXiaoshouTime;
	    if(D('Newproductjbkc')->add($jiaobankc_insert_data)===false)
		{	
            writelog('---4--error--','jiaoban');	
			$result=false;
		}	
		*/	

      


		if($result)
		{	
	        writelog('---5--error--','jiaoban');
			D()->commit();    //提交				
			$data['result']=1; 
			$data['msg']='交班成功';
            $data['yuangonglist']=$yuangonglist; 
			$data['jieban_time']=$dtInsertTime; 								
		}
		else
		{
			writelog('---6--error--','jiaoban');
			D()->rollback();    //回滚 
			$data['result']=-1;
            $data['msg']='交班失败';			
		}  						 						
		$this->ajaxReturn($data);						   	   		   
	}
	
		
	public function API_goods_jiaoban_mx_list()
	{	  
        header('Access-Control-Allow-Origin:*');
		
		$wbaccount=I('get.wbaccount','','string');
		$loginguid=I('get.loginguid','','string');	
				
        $wbid=$this->fnGetOneBarInfo($wbaccount,$loginguid);
		if(empty($wbid))
		{
			$data['result']=-3;
			$data['msg']='登陆超时';
			echo  json_encode($data);
			return;
		}

		
	    $list=D('Newproductjb')->Field('id,operate_db,operate_jb,post_order_no,dtBegTime,dtEndTime,sumje,sum_cash_je,sum_nocash_je,sum_other_je,dtInsertTime,bz')->where(array('wbid'=>$wbid))->order('id desc')->limit(1)->find();		   			
		if($list)
		{				

	        $map1=array();
			$map1['dtInsertTime']=array('BETWEEN',array($list['dtBegTime'],$list['dtEndTime']));
			$map1['wbid']=$wbid;
					
			$all_goods_list=D('Newproduct')->field('id,wbid,goods_id,shou_price,goods_name,kc_num')->where(array('wbid'=>$wbid,'deleted'=>0))->select();		
			$lastshift_goodskc_list=D('Newproductxsmx')->field('goods_id,sum(xiaoshou_num) as xiaoshou_num')->group('goods_id')->where($map1)->select();					
			foreach($all_goods_list as &$val)
			{							
				$bFind=false;
				foreach($lastshift_goodskc_list as &$val2)
				{
					if($val['goods_id']==$val2['goods_id'])
					{				
						$xiaoshou_num=$val2['xiaoshou_num']; 
										
						$bFind=true;						
						break;
					}
				}
				if($bFind==false)
				{
					$xiaoshou_num=0;
				}
				$val['sumje']=$xiaoshou_num *$val['shou_price'];			
				$val['xiaoshou_num']=$xiaoshou_num;
				$val['old_kc_num']=$val['xiaoshou_num'] +$val['kc_num'];
			}
			
			
			$list['all_goods_list']=$all_goods_list;									
			$sumje_nocash_sjsr=D('Newproductzf')->where($map1)->sum('sp_je');
			if(empty($sumje_nocash_sjsr))
			{
				$sumje_nocash_sjsr=0;
			}else
            {
			 $sumje_nocash_sjsr=sprintf("%.2f", $sumje_nocash_sjsr); 
		    }	
			
			$moneylist=D('Newproductxs')->Field('paytype,ordertype,sum(sum_sr_je) as sum_je')->group('paytype,ordertype')->where($map1)->select();
			foreach($moneylist as &$val)
			{
		
				if($val['paytype']==1 && $val['ordertype']==1)
				{
					$list['sumje_cash_xs']=(float)sprintf("%.2f", $val['sum_je']);
		
				}	
				
				if($val['paytype']==1 && $val['ordertype']==2)
				{
					$list['sumje_cash_jbxs']=(float)sprintf("%.2f", $val['sum_je']);
				}
				
				if($val['paytype']==1 && $val['ordertype']==3)
				{
					$list['sumje_cash_th']=(float)sprintf("%.2f", $val['sum_je']);
				}
				
				if($val['paytype']==2 && $val['ordertype']==1)
				{
					$list['sumje_nocash_xs']=(float)sprintf("%.2f", $val['sum_je']);
				}
				
						
				if($val['paytype']==2 && $val['ordertype']==3)
				{
					$list['sumje_nocash_th']=(float)sprintf("%.2f", $val['sum_je']);
				}
				
				if($val['paytype']==3 && $val['ordertype']==1)
				{
					$list['sumje_other_xs']=(float)sprintf("%.2f", $val['sum_je']);
				}
								
				if($val['paytype']==3 && $val['ordertype']==3)
				{
					$list['sumje_other_th']=(float)sprintf("%.2f", $val['sum_je']);
				}
			}
			
			if(empty($list['sumje_cash_xs']))
			{
				$list['sumje_cash_xs']=0;
			}
			
			
			if(empty($list['sumje_cash_jbxs']))
			{
				$list['sumje_cash_jbxs']=0;
			}	
			
			if(empty($list['sumje_cash_th']))
			{
				$list['sumje_cash_th']=0;
			}	
			
			if(empty($list['sumje_nocash_xs']))
			{
				$list['sumje_nocash_xs']=0;
			}	
			
			if(empty($list['sumje_nocash_th']))
			{
				$list['sumje_nocash_th']=0;
			}	
		
			if(empty($list['sumje_other_xs']))
			{
				$list['sumje_other_xs']=0;
			}	
			
			if(empty($list['sumje_other_th']))
			{
				$list['sumje_other_th']=0;
			}	
			
			$list['sumje_nocash_sjsr']=$sumje_nocash_sjsr;  		
			$list['sum_sr_je']=$list['sumje'];	

			
            $list['sum_sr_je']=(float)sprintf("%.2f", $list['sum_sr_je']);
			$list['sumje']=(float)sprintf("%.2f", $list['sumje']);
			
			$data['result']=1; 	
			$data['list']=$list; 
						
		}
		else
		{
			 
			$data['result']=-1;
            $data['msg']='数据为空';			
		}  						 						
		 $this->ajaxReturn($data);					
	   	   		   
	}
	
	
	/*得到非现金支付的订单*/
	public function API_goods_paylog_list()
	{	  
        header('Access-Control-Allow-Origin:*');		  	
		$wbaccount=I('get.wbaccount','','string');
		$loginguid=I('get.loginguid','','string');	
		
        $wbid=$this->fnGetOneBarInfo($wbaccount,$loginguid);
		if(empty($wbid))
		{
			$data['result']=-2;
			$data['msg']='登陆超时';
			echo  json_encode($data);
			return;
		}

		$page=I('get.page','','string');
		$rows=I('get.num','','string');
		$flag=I('get.flag','1','string');
		$ordertype=I('get.ordertype','0','string');
		
		
	    $nowtime=date('Y-m-d H:i:s',time());
		$lastshiftinfo= D('Newproductjb')->where(array('wbid'=>$wbid))->order('dtEndTime desc')->limit(1)->find();
		
		$lastbegtime=date('Y-m-d H:i:s',strtotime($lastshiftinfo['dtBegTime']));
        $lastendtime=date('Y-m-d H:i:s',strtotime($lastshiftinfo['dtEndTime']));		
		if(!empty($lastshiftinfo))
		{
			if($flag==1)
			{
				//本班
				$map['dtInsertTime']=array('BETWEEN',array($lastendtime,$nowtime));
			}
			else if($flag==2)
			{
				//前班
				$map['dtInsertTime']=array('BETWEEN',array($lastbegtime,$lastendtime));
			}
		}
		else
        {
			$map['dtInsertTime']=array('BETWEEN',array($lastendtime,$nowtime));
		}			
						
        $map['wbid']=$wbid;		
		if(!empty($ordertype))
		{
			$map['ordertype']=$ordertype;
		}													
	    $list=D('Newproductzf')->where($map)->order('id desc')->page($page,$rows)->select();	  
		if($list)
		{								
			$data['result']=1; 			
			foreach($list as &$val)
			{
				$val['sp_je']=sprintf("%.2f", $val['sp_je']); 			
			}
			$data['list']=$list; 
            $data['count']=D('Newproductzf')->where($map)->count(); 			
		}
		else
		{			 
			$data['result']=-1; 
            $data['msg']='数据为空';			
		}  						 						
		$this->ajaxReturn($data);					
	   	   		   
	}
	
	
	
	
	public function API_goods_lingqu_khd()
	{	  
      	header('Access-Control-Allow-Origin:*');
		$goods_info=file_get_contents("php://input");			
		$goods_info=json_decode($goods_info,true);	
		
        $wbaccount=$goods_info['wbaccount'];
		$loginguid=$goods_info['loginguid'];	
		
        $wbid=$this->fnGetOneBarInfo($wbaccount,$loginguid);
		if(empty($wbid))
		{
			$data['result']=-3;
			$data['msg']='登陆超时';
			echo  json_encode($data);
			return;
		}

		
	    $post_order_no = $goods_info['post_order_no'];  
        $dtInsertTime=date('Y-m-d H:i:s',time());   
		
	
		
		if(!empty($goods_info))
		{				
			$token=$goods_info['token'];	           	 			
			$res=D('Token')->checkToken($wbid,$token);
			if($res==-1)
			{
			   $data['result']=-3; 
			   $data['msg']='数据重复提交';
			   $this->ajaxReturn($data);
			   return;	
			}
	
			
			$operate      = $goods_info['operate'];
			$lingqu_status= $goods_info['lingqu_status'];
			
			$result=true;
			D()->startTrans();  //启用事务			
			if($lingqu_status==1)   //领取该商品
			{
				$xiaoshou_goods_info=D('Newproductxs')->where(array('wbid'=>$wbid,'post_order_no'=>$post_order_no))->find();
                $xiaoshou_goodslist	=json_decode($xiaoshou_goods_info['detailinfo'],true);			
				//$all_goods_list=D('Newproduct')->where(array('wbid'=>$wbid))->getField('goods_id,goods_name');					
		     											
				foreach( $xiaoshou_goodslist as &$val)
				{		
				   
					$now_hjkc_num=D('Newproduct')->where(array('wbid'=>$wbid,'goods_id'=>$val['goods_id']))->getField('kc_num');
					$xiaoshoumx_insert_data=array(); 
					// if(array_key_exists($val['goods_id'],$all_goods_list))
					// {
						// $xiaoshoumx_insert_data['goods_name']=$all_goods_list[$val['goods_id']];
					// }               
					$xiaoshoumx_insert_data['wbid']=$wbid;	
					$xiaoshoumx_insert_data['type_id']=$val['type_id'];              				
					$xiaoshoumx_insert_data['goods_id']=$val['goods_id'];				
					$xiaoshoumx_insert_data['xiaoshou_num']   =$val['sellNum'];
					$xiaoshoumx_insert_data['kc_num']  =$now_hjkc_num;			
					$xiaoshoumx_insert_data['shou_price']=$val['shou_price'];				
					$xiaoshoumx_insert_data['post_order_no']=$post_order_no;
					$xiaoshoumx_insert_data['paytype']=$xiaoshou_goods_info['paytype'];
					$xiaoshoumx_insert_data['ordertype']=1;							
					$xiaoshoumx_insert_data['operate']=$operate;
					$xiaoshoumx_insert_data['dtInsertTime']=$dtInsertTime;	
					$xiaoshoumx_insert_data['je']=$val['shou_price']* $val['sellNum'];															 												
							
					if(D('Newproductxsmx')->add($xiaoshoumx_insert_data)===false)
					{		
						writelog('---4--1--err-','xiaoshou');			
						$result=false;
					}
					  
					if($val['sellNum'] >= $now_hjkc_num)
					{
						writelog('---4--2--err-','xiaoshou');
						$result=false;
						//库存不足
						$data['result']=-3;
			            $data['msg']='商品库存不足';
						$this->ajaxReturn($data);						
					}
					else
					{
						$now_sj_xiaoshou_num =$val['sellNum'];
					}		
					   
						
				   					
					if(D('Newproduct')->where(array('wbid'=>$wbid,'goods_id'=>$val['goods_id']))->setDec('kc_num',$now_sj_xiaoshou_num)===false)
					{		
						writelog('--5-3---err--','xiaoshou');			
						$result=false;
					}
					
	
				}	
				
			    $xiaoshou_update_data['operate']=$operate;							
				$xiaoshou_update_data['dtUpdateTime']=$dtInsertTime;
				$xiaoshou_update_data['ordertype']=1;   //订单已确认
				if(D('Newproductxs')->where(array('wbid'=>$wbid,'post_order_no'=>$post_order_no))->save($xiaoshou_update_data)===false)
				{					
					$result=false;
					writelog('---5--5-err--','xiaoshou');
				}
				
			}
			else
            {
				//只更新该商品的isUsed 状态
			    writelog('---6--','xiaoshou');
				$xiaoshou_update_data['operate']=$operate;							
				$xiaoshou_update_data['dtUpdateTime']=$dtInsertTime;
				$xiaoshou_update_data['ordertype']=6;   //订单已取消
				if(D('Newproductxs')->where(array('wbid'=>$wbid,'post_order_no'=>$post_order_no))->save($xiaoshou_update_data)===false)
				{					
					$result=false;
					writelog('---6--1-err--','xiaoshou');
				}
			} 				
																		
			if($result)
            {
			   writelog('---7-----','xiaoshou');
               D()->commit();  //提交事务           
               $data['result']=1;
			   $data['msg']='提交成功';
            }
            else
            {
				writelog('---8-----','xiaoshou');
               D()->rollback();    //回滚
               $data['result']=-1;
			   $data['msg']='销售失败1';
            }								
			$this->ajaxReturn($data);
							
		}
		else
        {
			writelog('---9-----','xiaoshou');
			$data['result']=-2;
			$data['msg']='销售失败2';
			$this->ajaxReturn($data);
		} 							   	   		   
	}
	
	//客户端购买商品
	public function API_client_buygoods()
	{
		header('Access-Control-Allow-Origin:*');		   		   		         
		$post_order_no = 'XS'.date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8); 		
		$nowtime=date('Y-m-d H:i:s',time());     			
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
		$cpname=$goodslist['cpname'];
		//echo $cpname;
		//return;
		
		$wbid=D('WbInfo')->where(array('WbAccount'=>$wbaccount))->getField('WBID');					
		$xiaoshou_goodslist=$goodslist['goodsinfo'];
		$map=array();
		$map['wbid']=$wbid;
		$map['cpname']=$cpname;
		$map['paytype']=1;
		$map['ordertype']=5;
		$nodeal_ordernum=D('Newproductxs')->where($map)->count();
		
		if($nodeal_ordernum > 2)
		{				
		   $data['status']=-3;								
		   $this->ajaxReturn($data);
		   return;
		}	
		
		
		
		//return;
	 
		$j=0;
		$sumnum=0;
		$list=array();
		foreach($xiaoshou_goodslist as &$val)
		{
			$list[$j]['goods_id']=$val['goods_id'];
			$list[$j]['goods_name']=$val['goods_name'];
			$list[$j]['sellNum']=$val['sellNum'];
			$list[$j]['price']=$val['price'];
			$list[$j]['totalPrice']=$val['qianshu'];
			$list[$j]['type_id']   =$val['typeid'];
			$list[$j]['kc_num']=0;
			$sumnum+=$val['sellNum'];			
			$j++;
		}
		
	
			
		$sum_sr_je=$goodslist['total_zhifu'];
		$sum_sp_je=$goodslist['total_je'];	
		$sum_zl_je=$sum_sr_je-$sum_sp_je;
										
		$xiaoshou_insert_data=array();									
		$xiaoshou_insert_data['post_order_no']=$post_order_no;		
		$xiaoshou_insert_data['wbid']=$wbid;
		$xiaoshou_insert_data['info']='';		
		$xiaoshou_insert_data['sum_sr_je']=$sum_sp_je;
	   // $xiaoshou_insert_data['bz']='付:';
		if($sum_zl_je>0)
		{
			$xiaoshou_insert_data['bz']='付:'.$sum_sr_je.' 需找:'.$sum_zl_je;
		}	
				
		$xiaoshou_insert_data['paytype']=1;                       //现金支付
		$xiaoshou_insert_data['ordertype']=5;                     //默认 5客户端已销售待确认,确认成功该值改成 4，确认后才插入商品的明细数据
		$xiaoshou_insert_data['cpname']=$cpname;     //机器号				
		$xiaoshou_insert_data['operate']='';							
		$xiaoshou_insert_data['dtInsertTime']=$nowtime;            
		$xiaoshou_insert_data['sumnum']=$sumnum;
		$xiaoshou_insert_data['detailinfo']=json_encode($xiaoshou_goodslist);												
		$xiaoshou_insert_result =D('Newproductxs')->add($xiaoshou_insert_data);					
		if($xiaoshou_insert_result)
		{     
		   $data['status']=1;
		   
		             			 
			$guid= create_guid1();
			$guid='ght_cash_khd_'.$guid;	
	
			 
			
			$jsonFather=array();
			$jsonFather['Cmd']=2;
			$jsonFather['wbid']=$wbid;
			$jsonFather['MessageId']=$guid;			
			$jsonFather['cTime']=$nowtime;
				$jsonChild=array();
				$jsonChild['paytype']=3; //现金
				$jsonChild['je']=$sum_sp_je;
				$jsonChild['post_order_no']=$post_order_no ;				
				$jsonChild['CpName']=$cpname;		
			$jsonFather['Msg']=$jsonChild;
			
			$aPostData=$jsonFather;  
			//writelog('jsonFather提交的数据'.json_encode($jsonFather),'native1_notify'); 											 										
			$url='http://127.0.0.1:8090/PHP';			
			$result=PostTopDataToWb_lzm_cs($url,json_encode($aPostData),20);
			//writelog('result'.json_encode($result),'native1_notify');  
			if(!empty($result))
			{
				writelog('购买商品消息推送成功'.'post_order_no= '.$post_order_no,'native1_notify');	   	  
			}	
			else
			{
			  writelog('购买商品消息未推送'.'post_order_no= '.$post_order_no,'native1_notify');
			}				   			   			 		   
		}
		else
		{
		   $data['status']=-1;
		}								
		$this->ajaxReturn($data);
			
	}
	
	//生成二维码
	
	public function  fnCreateOneQrcode()
	{
		//1.将所有临时库存设置成 0
		header('Access-Control-Allow-Origin:*');
		$wbaccount=I('get.wbaccount','','string');	
		$loginguid=I('get.loginguid','','string');	
		
		$wbid=$this->fnGetOneBarInfo($wbaccount,$loginguid);
		if(empty($wbid))
		{
			$data['result']=-3;
			$data['msg']='登陆超时';
			echo  json_encode($data);
			return;
		}
		else
        {			        
			$update_array['ku_num_temp']=0;          			 
		   	$result= D('Newproduct')->where(array('wbid'=>$wbid))->save($update_array);
        } 			
		
					
		if($result)
		{
			$data['result']=1;
			$data['msg']='临时库存清空成功';
		}
		else
		{
			$data['result']=-3;
			$data['msg']='临时库存清空失败';
		}
        echo  json_encode($data);   			
			
		
	}
	
	
	//点货页面
	
	public  function   fnGetSaomaDianhuoNum()   //扫码时候读取所有商品库存到界面
	{
	    header('Access-Control-Allow-Origin:*');
		 $wbaccount=I('get.wbaccount','','string');	
		 $loginguid=I('get.loginguid','','string');	
		
		 $wbid=$this->fnGetOneBarInfo($wbaccount,$loginguid);
		 if(empty($wbid))
		 {
			 $data['result']=-3;
			 $data['msg']='登陆超时';
			 echo  json_encode($data);
			 return;
		 }
	    else
        {		
	        
	        $map['wbid']=$wbid;  	 
            $map['deleted']=0;
            $map['is_zuhe']=array('neq',1);
            $sumnum=D('Newproduct')->where($map)->sum('ku_num_temp');
            if($sumnum==0) 
			{
			   $flag=0;   //取实际库存
			}	
			else
            {
				$flag=1;    //取临时库存
			}	
			
				
				$nowtime=date('Y-m-d H:i:s',time());
				$lastshiftinfo= D('Newproductjb')->where(array('wbid'=>$wbid))->order('dtEndTime desc')->limit(1)->find();
				$lastshifttime=date('Y-m-d H:i:s',strtotime($lastshiftinfo['dtEndTime']));
				//获取商品列表
				$all_goods_list=D('Newproduct')->field('id,wbid,goods_id,type_id,shou_price,goods_name,kc_num,ku_num_temp')->where(array('wbid'=>$wbid,'deleted'=>0))->order(' id desc ')->select();
				
				$map1=array();
				if(!empty($lastshiftinfo))
				{
					$map1['dtInsertTime']=array('BETWEEN',array($lastshifttime,$nowtime));
				}
				
				if($lastshiftinfo)
				{
					$lastshiftgoods_str=$lastshiftinfo['detailinfo'];
					$lastshiftgoods_list=json_decode($lastshiftgoods_str,true);
				}	
				
					
				$map1['wbid']=$wbid;			
				$lastshift_goodskc_list=D('Newproductxsmx')->field('goods_id,ordertype,sum(xiaoshou_num) as xiaoshou_num')->group('goods_id,ordertype')->where($map1)->select();	
				foreach($all_goods_list as &$val)
				{						
					$bFind=false;
					foreach($lastshift_goodskc_list as &$val2)
					{
						if($val['goods_id']==$val2['goods_id'])
						{	
							if($val2['ordertype']==1)
							{
								$xiaoshou_num=$val2['xiaoshou_num'];
							}
							else if($val2['ordertype']==3)
							{
								$tuihuo_num= $val2['xiaoshou_num'];
							}
							else if($val2['ordertype']==4)
							{
								$xiaoshou_num= $val2['xiaoshou_num'];
							}											                 					
							$bFind=true;						
							break;
						}
					}
					if($bFind==false)
					{
						$xiaoshou_num=0;
						$tuihuo_num  =0;
					}
					$val['xiaoshou_num']=$xiaoshou_num - $tuihuo_num;
					$val['sumje']=$val['xiaoshou_num'] *$val['shou_price'];
					
											
					$bFind1=false;
					foreach($lastshiftgoods_list as &$val3)
					{
						if($val['goods_id']==$val3['goods_id'])
						{	
							$old_kc_num= $val3['now_kc_num'];
							$bFind1=true;						
							break;
						}
					}
					if($bFind1==false)
					{
						$old_kc_num=0;	
					}
					$val['old_kc_num']= $old_kc_num;											
				}
				
				$i=0;
				$shiji_goods_list=array();	
				foreach($all_goods_list as &$val)
				{
					if($val['kc_num']==0 && $val['xiaoshou_num']==0 && $val['old_kc_num']==0)
					{
						
					}
					else
					{
						$shiji_goods_list[$i]['id']=$val['id'];
						$shiji_goods_list[$i]['goods_id']=$val['goods_id'];
						$shiji_goods_list[$i]['goods_name']=$val['goods_name'];
						$shiji_goods_list[$i]['kc_num']=$val['kc_num'];
						$shiji_goods_list[$i]['kc_num_temp']=$val['ku_num_temp'];
						$i++;
					}				
				}		   		
		}	

		
		$list2['shiji_goods_list']=$shiji_goods_list;
        $list2['result']=1;
        $list2['flag']=$flag;
    		
    		
		echo  json_encode($list2);		
	}
	
	public  function   fnUpdateAllGoodsTempkunum()   //更新所有商品的临时库存数量
	{
	    header('Access-Control-Allow-Origin:*');
           	
		$goods_info=file_get_contents("php://input");	
		$goods_info=json_decode($goods_info,true);			
        $wbaccount=$goods_info['wbaccount'];
		$loginguid=$goods_info['loginguid'];	
		
		
        $wbid=$this->fnGetOneBarInfo($wbaccount,$loginguid);
		if(empty($wbid))
		{
			$data['result']=-3;
			$data['msg']='登陆超时';
			echo  json_encode($data);
			return;
		}		
			       	
	    if(!empty($goods_info))
		{				
	        
			$token=$goods_info['token'];	           	 			
			$res=D('Token')->checkToken($wbid,$token);
			if($res==-1)
			{
			   $data['result']=-3; 
			   $data['msg']='数据重复提交';
			   $this->ajaxReturn($data);
			   return;	
			}
			
            $result=true;
            D()->startTrans();			
  		    $all_goods_list=$goods_info['goods_list'];

			foreach($all_goods_list as &$val)
			{	
				if(D('Newproduct')->where(array('wbid'=>$wbid,'goods_id'=>$val['goods_id']))->setField('ku_num_temp',$val['num'])===false)
				{
					$result=false;
				}	
			}
			if($result)
			{
				D()->commit();
				$data['result']=1;
			    $data['msg']='提交成功';
			}
			else
            {
				D()->rollback();
				$data['result']=-3;
			    $data['msg']='提交失败';
			}				
		}					
        echo  json_encode($data);
	}
	
	//客户端某个上网用户查看订单数据
	
    public  function API_client_Querygoodsinfo_yimai()
	{		   			   
		$wbaccount=I('get.wbaccount','','string');		 
		$wbid=D('WbInfo')->where(array('WbAccount'=>$wbaccount))->getField('WBID');
		$sGuid=I('get.sGuid','','string');	
		$hycardno=I('get.hycardno','','string');					
		$orderinfo=D('Newproductxs')->where(array('wbid'=>$wbid,'sGuid'=>$sGuid))->order('id desc')->find();	
		$orderinfo['dtCharuTime']=date('Y-m-d H:i:s',$orderinfo['dtCharuTime']);	  
		/*
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
        */		
		$this->assign('orderinfo',$orderinfo);
		$this->display();
								 				  
	}
	
    
    function  fnImportOldGoodsData()
	{
		$wbid=session('wbid');
		$count=D('Newproduct')->where(array('wbid'=>$wbid))->count();
		if($count >0)
		{
			$data['status']=-1;
			$this->ajaxReturn($data);  //新超市有商品则不能导入
			return;
		}	
		
        $map=array();
		$map['sp.wbid']=$wbid;
		$map['sp.deleted']=0;
		$map['kc.position']=1;	//货架数量
		
		$old_goods_list= D('Product')->alias('sp')->join('left join wt_goodskc  as kc on kc.wbid=sp.wbid and kc.goods_id=sp.goods_id')
			->field(array(
			'sp.goods_id'=>'goods_id',
			'sp.goods_name'=>'goods_name',
			'sp.type_id'=>'type_id',
			'sp.goods_pinyin'=>'goods_pinyin',
			'sp.goods_quanpin'=>'goods_quanpin',
			'sp.goods_image'=>'goods_image',
			'sp.barcode'=>'barcode',
			'sp.shou_price'=>'shou_price',
			'kc.num'=>'num'
			))
			->where($map)->select();
			
		//echo  json_encode($old_goods_list);
        //return; 		
      
            if($old_goods_list)
            {
				$result=true;
                D()->startTrans();	
				foreach($old_goods_list as &$val)
				{
				   $goods_insert_data=array();
				   $goods_insert_data['wbid']=$wbid;
				   $goods_insert_data['goods_id']=$val['goods_id'];
				   $goods_insert_data['type_id']=$val['type_id'];
				   $goods_insert_data['goods_name']=$val['goods_name'];
				   $goods_insert_data['goods_pinyin']=$val['goods_pinyin'];
				   $goods_insert_data['goods_quanpin']=$val['goods_quanpin'];
				   
				   if(empty($val['goods_image']))
				   {
					  $val['goods_image']='moren.png';
				   }	
				   
				   $goods_insert_data['goods_image']=$val['goods_image'];
				   $goods_insert_data['barcode']=$val['barcode'];
				   $goods_insert_data['shou_price']=$val['shou_price'];
				   $goods_insert_data['kc_num']=$val['num'];
				   $goods_insert_data['dtInsertTime']=date('Y-m-d H:i:s',time());
				   if(D('Newproduct')->add($goods_insert_data)==false)
				   {
					   $result=false;
				   }	   
				}
			//	echo  D('Newproduct')->getLastSql();
				if($result)
				{
					D()->commit();
					$data['status']=1;
				}
				else
				{
					D()->rollback();
					$data['status']=-1;
				}
			}				
					
			$this->ajaxReturn($data);  //新超市有商品则不能导入
			
		
	}
	
	/*
	public function  fnAddNewProduct()
	{
		$bFind=false;
					    
            if($old_goods_list)
            {
				$result=true;
                D()->startTrans();	
				foreach($old_goods_list as &$val)
				{
				   $goods_insert_data=array();
				   $goods_insert_data['goods_id']=$val['goods_id'];
				   $goods_insert_data['type_id']=$val['type_id'];
				   $goods_insert_data['goods_name']=$val['goods_name'];
				   $goods_insert_data['goods_pinyin']=$val['goods_pinyin'];
				   $goods_insert_data['goods_quanpin']=$val['goods_quanpin'];				   					   
				   $goods_insert_data['goods_image']=$val['goods_image'];
				   $goods_insert_data['barcode']=$val['barcode'];
				   $goods_insert_data['shou_price']=$val['shou_price'];
				   $goods_insert_data['kc_num']=$val['num'];
				   
				   if(D('Newproduct')->add($goods_insert_data)==false)
				   {
					   $result=false;
				   }	   
				}
				
				if($result)
				{
					D()->commit();
					$data['status']=1;
				}
				else
				{
					D()->rollback();
					$data['status']=-2;
				}
			}	
	}
    */
	
	
		
}   