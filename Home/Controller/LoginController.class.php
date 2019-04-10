<?php
namespace Home\Controller;
use Think\Controller;
class LoginController extends   Controller 
{


    public function index()
    {

    }
	
	


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


    public function register()
    {    
	    $aGuid=getGuid();
		session('register_guid',$aGuid);
		
		$this->assign('register_guid',$aGuid);    
        $this->assign('province_list',D('Area')->getAreaList());      
        $this->display();
    }

    public function checkWbAccountID()
    {


        $regaccount=I('post.WbAccount','','string');
        $bExist=D('WbInfo')->where(array('WbAccount'=>$regaccount))->find();
        if(!empty($bExist))
        {
          echo 'false';
        }
        else
        { 
          echo 'true';
        } 
    }



    public function getPhoneVerifycode() 
    {
       $verifycode=(mt_rand(100000,999999));  
       writelog('getPhoneVerifycode 产生的6位随机码----'.$verifycode,'yzm');
      
       session('phone_verifycode',$verifycode);
	   
	   $register_guid=session('register_guid');
	   
       $phonenum=I('get.mobile','','string');
	   $aGetGuid=I('get.register_guid','','string');
 
       writelog('手机phonenum----'.$phonenum,'yzm');
      if(!empty($phonenum) && ($register_guid== $aGetGuid)  && !empty($aGetGuid) && !empty($register_guid))
      {    
        
        SendToTelOfAccNo($phonenum,$verifycode);
		session('register_guid_fu',$register_guid);		
		session('register_guid',null);		
      }
      else
      {
        writelog('手机短信验证码不发送----'.$verifycode,'yzm');
		session('register_guid',null);
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
           echo 'false';
        }
        else
        {
           echo 'true';
        } 
    }

	
	
	
	public function setregisterinfo_fenfuwuqi()
    {        
        $one_new_wbinfo=I('post.');  
        $account_type= $one_new_wbinfo['account_type'];
        if($account_type==1)   //普通账号注册
        {
			
            $result = true;
	        $newwbid= $one_new_wbinfo['WBID'];
			
			$res=D('WbInfo')->where(array('WBID'=>$newwbid))->find();
			if(!empty($res))
			{
				writelog('--error---0-1----','register');
				$data['result']=0;
				$this->ajaxReturn($data);
				return;
			}	
		
            $wbinfo_insert_data['SmLx'] = $one_new_wbinfo['SmLx'];
	        $wbinfo_insert_data['WbAccount'] = $one_new_wbinfo['WbAccount'];
	        $wbinfo_insert_data['WBID']      =$newwbid;
	        $wbinfo_insert_data['WbName']    =$one_new_wbinfo['WbName'];
			$wbinfo_insert_data['province']  = $one_new_wbinfo['province'];
	        $wbinfo_insert_data['city']      =$one_new_wbinfo['city'];
	        $wbinfo_insert_data['area']      =$one_new_wbinfo['area'];	
	        $wbinfo_insert_data['WBManager'] =$one_new_wbinfo['WBManager'];  
	        $wbinfo_insert_data['Card']      =$one_new_wbinfo['Card'];  
	        $wbinfo_insert_data['PassWord']  =$one_new_wbinfo['PassWord'];  
	        $wbinfo_insert_data['WBTel']     =$one_new_wbinfo['WBTel'];  
	        $wbinfo_insert_data['EMail']     =$one_new_wbinfo['EMail'];  
	        $wbinfo_insert_data['CpCount']   =$one_new_wbinfo['CpCount']; 
	        $wbinfo_insert_data['addr']      =$one_new_wbinfo['addr'];
            $wbinfo_insert_data['is_dianjing']      =$one_new_wbinfo['is_dianjing']; 			
	        $wbinfo_insert_data['debug_InsrtTime']      =date('Y-m-d H:i:s');
			$wbinfo_insert_data['VerNo']      =1;
			$wbinfo_insert_data['role_perm']  =$one_new_wbinfo['role_perm'];
	        $wbinfo_insert_data['beginTime']      =date('Y-m-d H:i:s');
	        $wbinfo_insert_data['EndTime']        =date('Y-m-d H:i:s',strtotime("+ 30 days",strtotime($wbinfo_insert_data['beginTime'])));
	        $wbinfo_insert_data['ls_ID']      =0;
			// $wbinfo_insert_data['liansuo_admin']   =0;
			// $wbinfo_insert_data['is_liansuo']      =0;
            
	        D()->startTrans();  //启用事务
			
	
			if(D('WbInfo')->where(array('WbAccount'=>$wbinfo_insert_data['WbAccount']))->delete()===false)
	        {
	          $result=false;
	          writelog('--error---0-3------','register');
	        }     
            $aTempstr=D('WbInfo')->getLastSql();
            $sendstr.=$aTempstr.';';  
		
	        if(D('WbInfo')->InsertOneBar($wbinfo_insert_data)===false)
	        {
	          $result=false;
	          writelog('--error---0-4------','register');
	        }  
			$aTempstr=D('WbInfo')->getLastSql();
            $sendstr.=$aTempstr.';';  

	      
	  
	          //1.插入两条角色 权限数据	   
	        $moban_role_data=D('RoleMb')->select(); 
	        foreach($moban_role_data as &$val)
	        {
	          $role_insert_data=array();
	          $new_role_id=D('Role')->max('role_id')+1;
	          $role_insert_data['role_id']= $new_role_id; 
	          $role_insert_data['WB_ID']=$newwbid;          
	          $role_insert_data['role_name']=$val['role_name'];
	          $role_insert_data['groupqx']=addyanma($val['groupqx']);
	          $role_insert_data['boss_qx']=$val['bossqx'];
	          $role_insert_data['role_perm']=$val['role_perm'];
			  
			  $role_insert_data['th_qx']=$val['th_qx'];
			  $role_insert_data['sxj_qx']=$val['sxj_qx'];			  
			  $role_insert_data['jch_qx']=$val['jch_qx'];
			  $role_insert_data['spinfo_qx']=$val['spinfo_qx'];
			  $role_insert_data['ch_qx']=$val['ch_qx'];
			  $role_insert_data['xiajia_qx']=$val['xiajia_qx'];
			  
			  
			  
			  
			  
	          $role_insert_data['dtInsertTime']=date('Y-m-d H:i:s'); 

	          if(D('Role')->add($role_insert_data) === false)
	          {         
	            $result=false;
	            writelog('--error---1-1------','register');
	          }  
			  $aTempstr=D('Role')->getLastSql();
              $sendstr.=$aTempstr.';';  
			  
	        }    

	        //1.1 插入2条员工数据 --白班 夜班	        
		    $moban_yuangong_data =D('YuangongMb')->getYuangongMoBanList($newwbid);
		    foreach($moban_yuangong_data as &$val)
		    {  
		        $yuangong_insert_data=array();
		        $yuangong_insert_data['WB_ID']=$newwbid; 
		        $yuangong_insert_data['name']=$val['name']; 
		        $yuangong_insert_data['pw']=md5('!@#BGS159357'); 
		        $yuangong_insert_data['qx']=$val['groupqx']; 
		        $yuangong_insert_data['boss_qx']=$val['bossqx']; 
		        $yuangong_insert_data['role_id']=$val['role_id'];
		        $yuangong_insert_data['Guid']=getGuid();

		        if(D('Yuangong')->add($yuangong_insert_data) === false)
		        {
		          $result=false;
		           writelog('---error--2-1------','register');
		        }  
				$aTempstr=D('Yuangong')->getLastSql();
                $sendstr.=$aTempstr.';';  
		   
		    }  
		        
	    
	          //（1）默认会员卡的类型 插入4条	          
	        $mb_hylx_data=D('HylxMb')->select();
	        foreach( $mb_hylx_data as &$val)
	        { 
	          $hylx_insert_data=array();
	          $hylx_insert_data['WB_ID']=  $newwbid;
	          $hylx_insert_data['Name']=   $val['Name'];
	          $hylx_insert_data['Guid']=   $val['Guid'];
	          $hylx_insert_data['SmallIntegral']=   $val['SmallIntegral'];
	          $hylx_insert_data['SjDiscount']=   $val['SjDiscount'];
	          $hylx_insert_data['SpDiscount']=   $val['SpDiscount'];               
	          if(D('VipLevel')->add($hylx_insert_data) === false)
	          {
	            $result = false;
	            writelog('--error---4-1------','register');
	          }
			  	$aTempstr=D('VipLevel')->getLastSql();
                $sendstr.=$aTempstr.';';  
	               
	        }  
	           //(2) 分组表 ---添加默认分组                  

	        $mb_district_data=D('GroupMb')->select();
	        foreach( $mb_district_data as &$val)
	        {
	          $district_insert_data=array();
	          $district_insert_data['GroupName']=  $val['GroupName'];
	          $district_insert_data['Guid']=  $val['Guid'];
	          $district_insert_data['WB_ID']=  $newwbid;
	          $district_insert_data['HyCardGuids']=  $val['HyCardGuids'];
	          $district_insert_data['FlList']=   $val['FlList'];	          
	          if(D('District')->addDistrict($district_insert_data) === false)
	          {
	            $result = false;
	            writelog('---error--5-1------','register');
	          }
			   $aTempstr=D('District')->getLastSql();
               $sendstr.=$aTempstr.';';  
	       	           
	        }  

	      
	                 
	        //(3)注册时候添加默认配置winitableb表的数据
						
		    if(D('WIni')->where(array('WB_ID'=>$newwbid))->delete()===false)
			{
			  $result=false;
			  writelog('---error--6-1------','register');
			} 			
			$aTempstr=D('WIni')->getLastSql();
			$sendstr.=$aTempstr.';';
										
	        $mb_Initable_data=D('WIniMb')->select();
	        foreach($mb_Initable_data as &$val)
	        {         
	          if(D('WIni')->addOneMobanRecord2($newwbid,$val['Name'],$val['NValue']) === false)
	          {
	            $result = false;
	            writelog('---error--6-2------','register');
	          }
			  $aTempstr=D('WIni')->getLastSql();
			  $sendstr.=$aTempstr.';';
	        }  

	        if(D('WIni')->addOneMobanRecord($newwbid,'m_WbID',$newwbid) === false)
	        {
	          $result = false;
			   writelog('---error--6-3------','register');
	        }  	 
            $aTempstr=D('WIni')->getLastSql();
			$sendstr.=$aTempstr.';';			
	        if(D('WIni')->addOneMobanRecord($newwbid,'FWbName',$wbinfo_insert_data['WbName']) === false)
	        {
	          $result = false;
			  writelog('---error--6-4------','register');
	        } 
            $aTempstr=D('WIni')->getLastSql();
			$sendstr.=$aTempstr.';';			
	      
	        if(D('WIni')->addOneMobanRecord($newwbid,'LastCalTime',date('Y-m-d H:i:s')) === false)
	        {
	          $result = false;
			  writelog('---error--6-5------','register');
	        }  
            $aTempstr=D('WIni')->getLastSql();
			$sendstr.=$aTempstr.';';	
			
	
			//插入一条默认收银端的id		  
			  $ctrl_insert_data=array();
			  $ctrl_insert_data['Wb_id']=$newwbid;
			  $ctrl_insert_data['Syid']=1;
			  $ctrl_insert_data['Ip']='127.0.0.1';
			  $ctrl_insert_data['syname']='1';
			  $ctrl_insert_data['sGuid']=getGuid();
			  $ctrl_insert_data['insertTime']=date('Y-m-d H:i:s',time());			  
			  if(D('SpCtrlIp')->add($ctrl_insert_data)===false)
			  {
				$result = false;  
				writelog('---error--7-1------','register');
			  }	
			  $aTempstr=D('SpCtrlIp')->getLastSql();
			  $sendstr.=$aTempstr.';';
			  			  			
			
			$webini_insert_data=array();
			$webini_insert_data['wbid']=$newwbid;
			$webini_insert_data['skey']='exe_sp_version';
			$webini_insert_data['svalue']=1;							
			if(D('Webini')->add($webini_insert_data)===false)
			{
				$result=false; 
			}
			  
			  
			$bFind=D('Zhangmufen')->where(array('wbid'=>$newwbid))->find();
			if(empty($bFind))
			{
				$zhangmufen_insert_data=array();
				$zhangmufen_insert_data['wbid']=$newwbid;			
				$zhangmufen_insert_data['sumje']=0;
				$zhangmufen_insert_data['wxje']=0;
				$zhangmufen_insert_data['zfbje']=0;
				$zhangmufen_insert_data['ddje']=0;
				$zhangmufen_insert_data['gzhje']=0;			
				$zhangmufen_insert_data['dtUpdateTime']=date('Y-m-d H:i:s',time());
				if(D('Zhangmufen')->add($zhangmufen_insert_data)===false)
				{
					$result=false;
				}
			}
			
						//插入默认账目统计
			
			$bill_month_beg= '2016-01-01 00:00:00';
			$bill_month_end= '2021-01-01';
			
			$bill_month_beg=strtotime($bill_month_beg);		
			$monthcount= 48;
		  		
			for($i=0;$i<48;$i++)
			{
				$one_bill_date=strtotime("+".$i." month ",$bill_month_beg);
				$one_bill_date =date('Y-m',$one_bill_date);
				
				
				$zhangmu_insert_data=array();
				$zhangmu_insert_data['wbid']=$newwbid;
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
        else if($account_type==2) //连锁总账号
        {
            $result = true;
	        $newwbid= $one_new_wbinfo['WBID'];

	        $wbinfo_insert_data['WbAccount'] = $one_new_wbinfo['WbAccount'];
	        $wbinfo_insert_data['WBID']      =$newwbid;
	        $wbinfo_insert_data['WbName']    =$one_new_wbinfo['WbName'];
			$wbinfo_insert_data['province']  = $one_new_wbinfo['province'];
	        $wbinfo_insert_data['city']      =$one_new_wbinfo['city'];
	        $wbinfo_insert_data['area']      =$one_new_wbinfo['area'];	
	        $wbinfo_insert_data['WBManager'] =$one_new_wbinfo['WBManager'];  
	        $wbinfo_insert_data['Card']      =$one_new_wbinfo['Card'];  
	        $wbinfo_insert_data['PassWord']  =$one_new_wbinfo['PassWord'];  
	        $wbinfo_insert_data['WBTel']     =$one_new_wbinfo['WBTel'];  
	        $wbinfo_insert_data['EMail']     =$one_new_wbinfo['EMail'];  
	        $wbinfo_insert_data['CpCount']   =$one_new_wbinfo['CpCount']; 
	        $wbinfo_insert_data['addr']      =$one_new_wbinfo['addr'];  
	        $wbinfo_insert_data['debug_InsrtTime']      =date('Y-m-d H:i:s');
			$wbinfo_insert_data['VerNo']      =1;
			$wbinfo_insert_data['role_perm']  =$one_new_wbinfo['role_perm'];
	        $wbinfo_insert_data['beginTime']      =date('Y-m-d H:i:s');
	        $wbinfo_insert_data['EndTime']        =date('Y-m-d H:i:s',strtotime("+ 30 days",strtotime($wbinfo_insert_data['beginTime'])));
	        $wbinfo_insert_data['Ls_Id']      =$newwbid;
			$wbinfo_insert_data['ls_admin']   =1;
			$wbinfo_insert_data['is_liansuo']      =1;

	        D()->startTrans();  //启用事务						
			if(D('WbInfo')->where(array('WbAccount'=>$wbinfo_insert_data['WbAccount']))->delete()===false)
	        {
	          $result=false;
	          writelog('-----0-1------','register');
	        }  	     
	        if(D('WbInfo')->InsertOneBar($wbinfo_insert_data)===false)
	        {
	          $result=false;
	          writelog('-----1-1------','register');
	        }  
	  
	        //插入wt_liansuo_info表一条新数据							
			$liansuo_info_data=array();
			$liansuo_info_data['Ls_Id']  =$one_new_wbinfo['WBID'];
			$liansuo_info_data['liansuo_name']=$one_new_wbinfo['WbName'];
			$liansuo_info_data['barlist']   ='';
			$liansuo_info_data['yuming_id'] =$one_new_wbinfo['yuming_id'];					
			if(D('Liansuoinfo')->add($liansuo_info_data)===false)
			{
				$result=false;
			}	  
	          //1.插入两条角色 权限数据	   
	        $moban_role_data=D('RoleMb')->select(); 
	        foreach($moban_role_data as &$val)
	        {
	          $role_insert_data=array();
	          $new_role_id=D('Role')->max('role_id')+1;
	          $role_insert_data['role_id']= $new_role_id; 
	          $role_insert_data['WB_ID']=$newwbid;          
	          $role_insert_data['role_name']=$val['role_name'];
	          $role_insert_data['groupqx']=addyanma($val['groupqx']);
	          $role_insert_data['boss_qx']=$val['bossqx'];
	          $role_insert_data['role_perm']=$val['role_perm'];
	          $role_insert_data['dtInsertTime']=date('Y-m-d H:i:s'); 

	          if(D('Role')->add($role_insert_data) === false)
	          {         
	            $result=false;
	            writelog('-----1-1------','register');
	          }  
	        }    
	     
	        //1.1 插入2条员工数据 --白班 夜班	        
		    $moban_yuangong_data =D('YuangongMb')->getYuangongMoBanList($newwbid);
		    foreach($moban_yuangong_data as &$val)
		    {  
		        $yuangong_insert_data=array();
		        $yuangong_insert_data['WB_ID']=$newwbid; 
		        $yuangong_insert_data['name']=$val['name']; 
		        $yuangong_insert_data['pw']=md5('!@#BGS159357'); 
		        $yuangong_insert_data['qx']=$val['groupqx']; 
		        $yuangong_insert_data['boss_qx']=$val['bossqx']; 
		        $yuangong_insert_data['role_id']=$val['role_id'];
		        $yuangong_insert_data['Guid']=getGuid();

		        if(D('Yuangong')->add($yuangong_insert_data) === false)
		        {
		          $result=false;
		           writelog('-----2-1------','register');
		        }  		        
		    }  	        
	       	
	          //（1）默认会员卡的类型 插入4条          
	        $mb_hylx_data=D('HylxMb')->select();
	        foreach( $mb_hylx_data as &$val)
	        { 
	          $hylx_insert_data=array();
	          $hylx_insert_data['WB_ID']=  $newwbid;
	          $hylx_insert_data['Name']=   $val['Name'];
	          $hylx_insert_data['Guid']=   $val['Guid'];
	          $hylx_insert_data['SmallIntegral']=   $val['SmallIntegral'];
	          $hylx_insert_data['SjDiscount']=   $val['SjDiscount'];
	          $hylx_insert_data['SpDiscount']=   $val['SpDiscount'];	               
	          if(D('VipLevel')->add($hylx_insert_data) === false)
	          {
	            $result = false;
	            writelog('-----4-1------','register');
	          }
	          	     
	        }  
	           //(2) 分组表 ---添加默认分组                  
	        
	        $mb_district_data=D('GroupMb')->select();
	        foreach( $mb_district_data as &$val)
	        {
	          $district_insert_data=array();
	          $district_insert_data['GroupName']=  $val['GroupName'];
	          $district_insert_data['Guid']=  $val['Guid'];
	          $district_insert_data['WB_ID']=  $newwbid;
	          $district_insert_data['HyCardGuids']=  $val['HyCardGuids'];
	          $district_insert_data['FlList']=   $val['FlList'];	          
	          if(D('District')->addDistrict($district_insert_data) === false)
	          {
	            $result = false;
	            writelog('-----5-1------','register');
	          }
	          	           
	        }  	        	                 
	        //(3)注册时候添加默认配置winitableb表的数据
	        $mb_Initable_data=D('WIniMb')->select();
	        foreach($mb_Initable_data as &$val)
	        {         
	          if(D('WIni')->addOneMobanRecord($newwbid,$val['Name'],$val['NValue']) === false)
	          {
	            $result = false;
	            writelog('-----6-1------','register');
	          }
	        }  

	        if(D('WIni')->addOneMobanRecord($newwbid,'m_WbID',$newwbid) === false)
	        {
	          $result = false;
	        }  	             	     

	        if(D('WIni')->addOneMobanRecord($newwbid,'FWbName',$wbinfo_insert_data['WbName']) === false)
	        {
	          $result = false;
	        }  
	                                              
	        if(D('WIni')->addOneMobanRecord($newwbid,'LastCalTime',date('Y-m-d H:i:s')) === false)
	        {
	          $result = false;
	        }   	 				
			//插入一条默认收银端的id
			  
			$ctrl_insert_data=array();
			$ctrl_insert_data['Wb_id']=$newwbid;
			$ctrl_insert_data['Syid']=1;
			$ctrl_insert_data['Ip']='127.0.0.1';
			$ctrl_insert_data['syname']='1';
			$ctrl_insert_data['sGuid']=getGuid();
			$ctrl_insert_data['insertTime']=date('Y-m-d H:i:s',time());
			  
			if(D('SpCtrlIp')->add($ctrl_insert_data)===false)
			{
				$result = false;  
			}	
        }
		if($account_type==3)   //普通连锁帐号
        {
            $result = true;
	        $newwbid= $one_new_wbinfo['WBID'];

	        $wbinfo_insert_data['WbAccount'] = $one_new_wbinfo['WbAccount'];
	        $wbinfo_insert_data['WBID']      =$newwbid;
	        $wbinfo_insert_data['WbName']    =$one_new_wbinfo['WbName'];
			$wbinfo_insert_data['province']  = $one_new_wbinfo['province'];
	        $wbinfo_insert_data['city']      =$one_new_wbinfo['city'];
	        $wbinfo_insert_data['area']      =$one_new_wbinfo['area'];	
	        $wbinfo_insert_data['WBManager'] =$one_new_wbinfo['WBManager'];  
	        $wbinfo_insert_data['Card']      =$one_new_wbinfo['Card'];  
	        $wbinfo_insert_data['PassWord']  =$one_new_wbinfo['PassWord'];  
	        $wbinfo_insert_data['WBTel']     =$one_new_wbinfo['WBTel'];  
	        $wbinfo_insert_data['EMail']     =$one_new_wbinfo['EMail'];  
	        $wbinfo_insert_data['CpCount']   =$one_new_wbinfo['CpCount']; 
	        $wbinfo_insert_data['addr']      =$one_new_wbinfo['addr'];  
	        $wbinfo_insert_data['debug_InsrtTime']      =date('Y-m-d H:i:s');
			$wbinfo_insert_data['VerNo']      =1;
			$wbinfo_insert_data['role_perm']  =$one_new_wbinfo['role_perm'];
	        $wbinfo_insert_data['beginTime']      =date('Y-m-d H:i:s');
	        $wbinfo_insert_data['EndTime']        =date('Y-m-d H:i:s',strtotime("+ 30 days",strtotime($wbinfo_insert_data['beginTime'])));
	        $wbinfo_insert_data['Ls_Id']      =0;
			$wbinfo_insert_data['liansuo_admin']   =0;
			$wbinfo_insert_data['is_liansuo']      =1;

	        D()->startTrans();  //启用事务					
			if(D('WbInfo')->where(array('WbAccount'=>$wbinfo_insert_data['WbAccount']))->delete()===false)
	        {
	          $result=false;
	          writelog('-----0-1------','register');
	        }  	     
	        if(D('WbInfo')->InsertOneBar($wbinfo_insert_data)===false)
	        {
	          $result=false;
	          writelog('-----1-1------','register');
	        }  	    	  
	          //1.插入两条角色 权限数据	   
	        $moban_role_data=D('RoleMb')->select(); 
	        foreach($moban_role_data as &$val)
	        {
	          $role_insert_data=array();
	          $new_role_id=D('Role')->max('role_id')+1;
	          $role_insert_data['role_id']= $new_role_id; 
	          $role_insert_data['WB_ID']=$newwbid;          
	          $role_insert_data['role_name']=$val['role_name'];
	          $role_insert_data['groupqx']=addyanma($val['groupqx']);
	          $role_insert_data['boss_qx']=$val['bossqx'];
	          $role_insert_data['role_perm']=$val['role_perm'];
	          $role_insert_data['dtInsertTime']=date('Y-m-d H:i:s'); 

	          if(D('Role')->add($role_insert_data) === false)
	          {         
	            $result=false;
	            writelog('-----1-1------','register');
	          }  
	        }    
	       
	        //1.1 插入2条员工数据 --白班 夜班        
		    $moban_yuangong_data =D('YuangongMb')->getYuangongMoBanList($newwbid);
		    foreach($moban_yuangong_data as &$val)
		    {  
		        $yuangong_insert_data=array();
		        $yuangong_insert_data['WB_ID']=$newwbid; 
		        $yuangong_insert_data['name']=$val['name']; 
		        $yuangong_insert_data['pw']=md5('!@#BGS159357'); 
		        $yuangong_insert_data['qx']=$val['groupqx']; 
		        $yuangong_insert_data['boss_qx']=$val['bossqx']; 
		        $yuangong_insert_data['role_id']=$val['role_id'];
		        $yuangong_insert_data['Guid']=getGuid();
		        if(D('Yuangong')->add($yuangong_insert_data) === false)
		        {
		          $result=false;
		           writelog('-----2-1------','register');
		        }  		        
		    }  		        
	       				          
	        $mb_hylx_data=D('HylxMb')->select();
	        foreach( $mb_hylx_data as &$val)
	        { 
	          $hylx_insert_data=array();
	          $hylx_insert_data['WB_ID']=  $newwbid;
	          $hylx_insert_data['Name']=   $val['Name'];
	          $hylx_insert_data['Guid']=   $val['Guid'];
	          $hylx_insert_data['SmallIntegral']=   $val['SmallIntegral'];
	          $hylx_insert_data['SjDiscount']=   $val['SjDiscount'];
	          $hylx_insert_data['SpDiscount']=   $val['SpDiscount'];
	               
	          if(D('VipLevel')->add($hylx_insert_data) === false)
	          {
	            $result = false;
	            writelog('-----4-1------','register');
	          }
	              
	        }  
	           //(2) 分组表 ---添加默认分组                  
	      
	        $mb_district_data=D('GroupMb')->select();
	        foreach( $mb_district_data as &$val)
	        {
	          $district_insert_data=array();
	          $district_insert_data['GroupName']=  $val['GroupName'];
	          $district_insert_data['Guid']=  $val['Guid'];
	          $district_insert_data['WB_ID']=  $newwbid;
	          $district_insert_data['HyCardGuids']=  $val['HyCardGuids'];
	          $district_insert_data['FlList']=   $val['FlList'];	          
	          if(D('District')->addDistrict($district_insert_data) === false)
	          {
	            $result = false;
	            writelog('-----5-1------','register');
	          }
	         	           
	        }  
	                         
	        //(3)注册时候添加默认配置winitableb表的数据
	        $mb_Initable_data=D('WIniMb')->select();
	        foreach($mb_Initable_data as &$val)
	        {         
	          if(D('WIni')->addOneMobanRecord($newwbid,$val['Name'],$val['NValue']) === false)
	          {
	            $result = false;
	            writelog('-----6-1------','register');
	          }
	        }  
	        if(D('WIni')->addOneMobanRecord($newwbid,'m_WbID',$newwbid) === false)
	        {
	          $result = false;
	        }  	                  
	        if(D('WIni')->addOneMobanRecord($newwbid,'FWbName',$wbinfo_insert_data['WbName']) === false)
	        {
	          $result = false;
	        }  	                                        	      
	        if(D('WIni')->addOneMobanRecord($newwbid,'LastCalTime',date('Y-m-d H:i:s')) === false)
	        {
	          $result = false;
	        }   	 					
			//插入一条默认收银端的id
			  
			  $ctrl_insert_data=array();
			  $ctrl_insert_data['Wb_id']=$newwbid;
			  $ctrl_insert_data['Syid']=1;
			  $ctrl_insert_data['Ip']='127.0.0.1';
			  $ctrl_insert_data['syname']='1';
			  $ctrl_insert_data['sGuid']=getGuid();
			  $ctrl_insert_data['insertTime']=date('Y-m-d H:i:s',time());
			  
			  if(D('SpCtrlIp')->add($ctrl_insert_data)===false)
			  {
				$result = false;  
			  }	
        }	
      		     
     
       if($result)
       {  
          D()->commit(); 

		  $res =PostTopUpdateDataToWb_lzmByWbid($newwbid,'Php_To_Top_Sql',$sendstr);
		 
          if(!empty($res))
          {
            writelog('-----8-2------','register');
            writelog($newwbid.'注册新用户  命令已发送成功','register');
          }
          else
          {
            writelog($newwbid.'注册新用户  命令已发送失败','register');
          }  
         $data['result']=1;     
       }
       else
       {
         $data['result']=0;
         D()->rollback(); 
       } 
       $this->ajaxReturn($data);  
    }
	




    
}

