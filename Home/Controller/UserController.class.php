<?php
namespace Home\Controller;
class UserController extends CommonController
{
	//用户列表
	public function index()
	{
		A('Public')->loginverify();
		$this->chkPerm('user_index');
		$user_model = D('User');
		$page = I('get.page',1,'int');
		$rows = I('get.rows',20,'int');

		$wbaccount = I('get.wbaccount','','string');
		$wbname = I('get.wbname','','string');

		$roleid = I('get.roleid',0,'int');
		$id = I('get.id',0,'int');
		$action = I('get.action','','string');
		if($action=='del' && !empty($id)){
			$user_model->deleteUser($id);
			$this->write_log(33,33,'用户id:'.$id,'成功');
		}

		$map = array();
		if(!empty($wbaccount))
		{
			$map['info.WbAccount'] = array('LIKE',"%$wbaccount%");
		}
		if(!empty($wbname)){
			$map['info.WbName'] = array('LIKE',"%$wbname%");
		}
		if(!empty($roleid))
		{
			$map['admin.role_id'] = $roleid;
		}
		
		$users = $user_model->getUserList($map,$page,$rows);
		$this->assign('user_list',$users['list']);

		$this->assign('count',$users['count']);
		$this->assign('role_list',D('Role')->field('role_id,role_name')->select());
		$this->display();
	}
	//新增用户
	public function add()
	{
		A('Public')->loginverify();
		$this->chkPerm('user_index');
		if(IS_POST){
			if(I('post.password') != I('post.repassword')){
				$this->assign('alert','error');
				$this->assign('msg','两次密码不一致');
			}
			else
			{
				$user_insert_data = array(
					'user_name'		=>	I('post.user_name','','string'),
					'user_pass'		=>	I('post.password','','string'),
					'realname'		=>	I('post.realname','','string'),
					'phone'			=>	I('post.phone','','string'),
					'email'			=>	I('post.email','',FILTER_VALIDATE_EMAIL),
					'roleid'		=>	I('roleid',0,'int'),
					'createdtime'	=>	date('Y-m-d H:i:s'),
					'agent_id'		=>	$agent_id
					);
				$user_insert_result = D('User')->insertUser($user_insert_data);
				if($user_insert_result === true ){
					$this->write_log(32,32,'用户名:'.$user_insert_data['user_name'],'成功');
					$this->assign('alert','success');
					$this->assign('msg','新增用户成功');
				}elseif($user_insert_result === false){
					$this->write_log(32,32,'用户名:'.$user_insert_data['user_name'],'失败');
					$this->assign('alert','error');
					$this->assign('msg','新增用户失败');
				}else{
					$this->assign('alert','error');
					$this->assign('msg',$user_insert_result);
				}
			}
		}
		$this->assign('role_list',D('Role')->field('role_id,role_name')->select());
		$this->display();
	}
	//编辑用户
	public function edit()
	{
		A('Public')->loginverify();
		$this->chkPerm('user_index');
		$wbid = session('wbid');
		if(!empty($wbid))
		{
			$user_model = D('User');
			if(IS_POST)
			{
				$user = $user_model->getUserInfo($wbid);
				$roleid = I('post.role_id',0,'int');


                $WBTel = I('post.WBTel','','string');
				$Email = I('post.Email','','string');
				$WBManager = I('post.WBManager','','string');
				$QQ = I('post.QQ','','string');
				$Card = I('post.Card','','string');



               $bar_update_data = array();
               $bar_update_data['WBID']=$wbid;
               $bar_update_data['WBTel']=$WBTel;
               $bar_update_data['Email']=$Email;
               $bar_update_data['WBManager']=$WBManager;
               $bar_update_data['QQ']=$QQ;
               $bar_update_data['Card']=$Card;

                $bar_update_result=D('WbInfo')->updateOneBarInfo($bar_update_data);
				$user_update_data = array();
				$user_update_data['wbid']= $wbid;
				$user_update_data['role_id']=$roleid;

				$user_update_result = $user_model->where(array('wbid'=>$wbid))->setField('role_id',$roleid);
				if(!empty($user_update_result))			
				{
					
					$this->assign('alert','success');
					$this->assign('msg','修改成功');
				}else
				{
				
					$this->assign('alert','error');
					$this->assign('msg','修改失败');
				}

				
			}

			if(I('get.action')=='resetpwd')
			{
				$result = $user_model->updateUserInfo(array('wbid'=>$wbid,'user_pass'=>md5('123456hc')));

				$result1 = D('WbInfo')->where(array('WBID'=>$wbid))->setField('PassWord',md5('123456hc'));
				

				if($result === true)
				{
					$this->write_log(29,29,'用户id:'.$wbid,'成功');
					$this->assign('alert','success');
					$this->assign('msg','密码重置成功，默认密码为123456！');
				}
			}
			$wbinfo = D('WbInfo')->getOneWbInfoByid($wbid);

			$user = $user_model->getUserInfo($wbid);

			$this->assign('user',$user);
			$this->assign('wbinfo',$wbinfo);
			$this->assign('role_list',D('Role')->field('role_id,role_name')->select());
			$this->display();
		}
		else
		{
			$this->error('非法访问');
		}
	}
    public function showinfo()
	{
		$wbid = session('wbid');
		$wbinfo = D('WbInfo')->getOneWbInfoByid($wbid);
		$wbinfo['EndTime'] = date('Y-m-d H:i:s',strtotime($wbinfo['EndTime']));
		$nowtime=date('Y-m-d H:i:s',time());
		
		$oldtimejiange=getdayjiange($nowtime, $wbinfo['EndTime'] );
		
		if($oldtimejiange <4 && $oldtimejiange > 0 )
		{
			$this->assign('disable_flag',1);
		}
	    $posturl=C('DAILI_URL_ZONG');  
		
		
		$post_data['wbid']=$wbid;
		$url= $posturl.'/index.php/ServerzongAPI/API_query_bar_chongzhirecord.html';
		$res= sendRequsttoOneServer($url, $post_data,30);
													
		// 截取不带前三个字段				
		//$res= substr($res, 3);				
		$res2=json_decode($res,true);	
		
		if($res2['result']==1)
		{
			writelog('wbid='.$wbid.'允许加减机','barinfoedit');
			$editflag=1;   //允许显示
		}
		else
		{
			writelog('wbid='.$wbid.'允许加减机失败','barinfoediterror');
			$editflag=0;   //不允许显示
		}
			
		$this->assign('editflag',$editflag);		
	    $this->assign('wbinfo',$wbinfo);
		$this->display();
	}
    public function yzinfo()
    { 
	  
      $wbid=session('wbid');  
      $wbinfo= D('WbInfo')->getOneWbInfoByid($wbid);
      $str=$wbinfo['WBTel'];
      
      $wbinfo['WBTel']=substr($str,0,3).'*****'.substr($str,7,strlen($str));

      $this->assign('wbinfo',$wbinfo);	
      $this->display();          
    }
    public function yzinfo_yzm()
    { 
      $yzm=I('post.yzm','','string');
      $phone_verifycode=session('phone_verifycode');     
      if($yzm==$phone_verifycode)
      {
        echo 'true';
      }
      else
      { 
        echo 'false';
      }             
    }
    public function yzinfo_result()
    { 
    	
		$session_verify=session('phone_verifycode');
      $verifycode=I('post.yzm','','string'); 
      if($session_verify==$verifycode)
      {

        $data['result']='1';
      }
      else
      {
        $data['result']='-1';
      }
      $this->ajaxReturn($data);
    }

    public function getPhoneVerifycode() 
    {
      $verifycode=(mt_rand(100000,999999)); 
     
      
      session('phone_verifycode',$verifycode);    
      $phonenum= D('WbInfo')->where(array('WBID'=>session('wbid')))->getField('WBTel');
      if(!empty($phonenum))
      {              
    
        SendToTelOfAccNo($phonenum,$verifycode);
      }
      else
      {
        writelog('手机短信验证码不发送----'.$verifycode,'yzm');
      }  
      
      $data['status']='0';
      echo json_encode($data);
    }

	public function info()
	{
		$wbid = session('wbid');
		if(!empty($wbid))
		{
			
			if(IS_POST)
			{
				$user = D('User')->getUserInfo($wbid);
	
                $WBTel = I('post.WBTel','','string');
				$EMail = I('post.EMail','','string');
				$WBManager = I('post.WBManager','','string');
				$Card = I('post.Card','','string');
				$Address = I('post.address','','string');


               $bar_update_data = array();
           
               $bar_update_data['WBTel']=$WBTel;
               $bar_update_data['EMail']=$EMail;
               $bar_update_data['WBManager']=$WBManager;
               $bar_update_data['Card']=$Card;
			   $bar_update_data['addr']=$Address;
               $bar_update_result=D('WbInfo')->where(array('WBID'=>$wbid))->save($bar_update_data);
				
				
				if($bar_update_result)			
				{	
        		        				   
				   $bar_update_data['WBTel']=$WBTel;
				   $bar_update_data['EMail']=$EMail;
				   $bar_update_data['WBManager']=$WBManager;
				   $bar_update_data['Card']=$Card;
				   $bar_update_data['addr']=$Address;
				   $bar_update_data['wbid']=$wbid;
					
					$url= 'http://ght.wbzzsf.com/index.php/ServerzongAPI/API_bar_update_info.html';
					$res= sendRequsttoOneServer($url, $bar_update_data,30);
																
					// 截取不带前三个字段				
					//$res= substr($res, 3);				
					$res2=json_decode($res,true);	
				
					if($res2['result']==1)
					{
						//更新成功
						writelog('wbid='.$wbid.'修改网吧资料','barinfoedit');
					}
					else
                    {
						writelog('wbid='.$wbid.'修改网吧资料','barinfoediterror');
					}	 												   				      						
					$this->success('修改成功','showinfo',3);
				}
				else
				{				
					$this->assign('alert','error');
					$this->assign('msg','修改失败');
				}
			
			}
			else
			{
			   	$wbinfo = D('WbInfo')->getOneWbInfoByid($wbid);
				$user = D('User')->getUserInfo($wbid);
				$this->assign('user',$user);
				$this->assign('wbinfo',$wbinfo);
				$this->display();	
			}	
		}
		else
		{
			$this->error('非法访问');
		}
	}


	//权限列表
	public function perm()
	{
		A('Public')->loginverify();
		$action = I('get.action','','string');
		$id 	= I('get.id',0,'int');
		$this->chkPerm('user_perm');
		$perm_model = D('Perm');
		if($action=='del' && !empty($id))
		{
			$perm_model->deletePerm($id);

		}
		$this->assign('perm_list',list_to_tree($perm_model->getPermList(),0,'perm_id','parent_id','_'));
		$this->display();
	}

	//新增权限
	public function perm_add()
	{
		A('Public')->loginverify();
		$this->chkPerm('user_perm');
		$perm_model = D('Perm');
		if(IS_POST){
			$data = I('post.');
			$result = $perm_model->insertPerm($data);
			if($result){
				$this->assign('alert','success');
				$this->assign('msg','保存成功，需要在角色管理配置权限');
			}else{
				$this->assign('alert','error');
				$this->assign('msg','保存失败');
			}
		}
		$this->assign('perm_list',$perm_model->getFormatPermList());
		$this->display();
	}
	//编辑权限
	public function perm_edit()
	{
		A('Public')->loginverify();
		$this->chkPerm('user_perm');
		$id = I('get.id');
		if(!empty($id)){
			$perm_model = D('Perm');
			if(IS_POST){
				$data = I('post.');
				$data['perm_id'] = $id;
				$data['is_show'] = empty($data['is_show'])?0:$data['is_show'];
				$result = D('Perm')->updatePerm($data);
				if($result !== false){
					$this->assign('alert','success');
					$this->assign('msg','保存成功');
				}else{
					$this->assign('alert','error');
					$this->assign('msg','保存失败');
				}
			}
			$this->assign('perm_detail',$perm_model->getPermDetail($id));
			$this->assign('perm_list',$perm_model->getFormatPermList($id));
			$this->display();
		}else{
			$this->error('非法操作');
		}
	}
	//AJAX编辑权限
	public function update_perm()
	{
		A('Public')->loginverify();
		if(IS_AJAX){
			$get = I('get.');
			if($get['name']=='is_show'){
				$get['value'] = $get['value']=='true'?1:0;
			}
			$data['perm_id'] = $get['id'];
			$data[$get['name']] = $get['value'];
			$result = D('Perm')->updatePerm($data);
			if($result !== false){
				$this->success('');
			}else{
				$this->error('');
			}
		}
	}
	//角色列表
	public function role()
	{
		$this->chkPerm('user_role');
		$action = I('get.action','','string');
		$role_id = I('get.id',0,'int');
		if(!empty($action) && !empty($role_id) && $action == 'del')
		{
			$result = D('role')->delete($role_id);
			if($result)
			{
				$this->assign('alert','success');
				$this->assign('msg','删除成功');
			}else{
				$this->assign('alert','error');
				$this->assign('msg','删除失败');
			}
		}
		$this->assign('role_list',D('Role')->getRoleList());
		$this->display();
	}
	//新增角色
	public function role_add()
	{
		A('Public')->loginverify();
		$this->chkPerm('user_role');
		if(IS_POST){
			$role_perm = I('post.perm',array());
			$role_name = I('post.role_name','','string');
			if(!empty($role_perm) && !empty($role_name)){	//至少有一个权限，否则不能保存
				$role_perm = implode(',',$role_perm);
				$ip_whitelist = implode(',',explode("\n",I('post.ip_whitelist','','string')));
				$result = D('Role')->addRole(array('role_name'=>$role_name,'role_perm'=>$role_perm,'ip_whitelist'=>$ip_whitelist));
				if($result !== false){
					$this->redirect('role_edit',array('alert'=>'success','id'=>$result));
				}else{
					$this->assign('alert','error');
					$this->assign('msg','保存失败');
				}
			}else{
				$this->assign('alert','error');
				$this->assign('msg','未选择任何权限，不能保存');
			}
		}
		$perm_list = list_to_tree(D('Perm')->getPermList(),0,'perm_id','parent_id','_');
		$this->assign('perm_list',$perm_list);
		$this->display();
	}
	//编辑角色
	public function role_edit()
	{
		A('Public')->loginverify();
		$this->chkPerm('user_role');
		$role_id = I('get.id',0,'int');
		if(!empty($role_id))
		{
			if(IS_POST)
			{
				$role_perm = I('post.perm',array());
				$role_name = I('post.role_name','','string');  //角色名称
				$ip_whitelist = implode(',',explode("\n",I('post.ip_whitelist','','string')));
				if(!empty($role_perm) && !empty($role_name)){		//至少有一个权限，否则不能保存
					$role_perm = implode(',',$role_perm);
					$result = D('Role')->updateRole(array('role_id'=>$role_id,'role_name'=>$role_name,'role_perm'=>$role_perm,'ip_whitelist'=>$ip_whitelist));
				}
				if($result !== false){
					$this->assign('alert','success');
					$this->assign('msg','保存成功');
				}else{
					$this->assign('alert','error');
					$this->assign('msg','保存失败');
				}
			}elseif(I('get.alert','','string') == 'success'){	//
				$this->assign('alert','success');
				$this->assign('msg','保存成功');
			}
			$perm_list = list_to_tree(D('Perm')->getPermList(),0,'perm_id','parent_id','_');//获取界面要输出的权限列表
			$this->assign('perm_list',$perm_list);
			$role = D('Role')->getRoleDetail($role_id);
			$role['ip_whitelist'] = explode(',', $role['ip_whitelist']);
			$role['ip_whitelist'] = implode("\n", $role['ip_whitelist']);
			$this->assign('ip_whitelist',$role['ip_whitelist']);
			$this->assign('role_name',$role['role_name']);
			$this->assign('role_perm_list',explode(',',$role['role_perm']));
			$this->display();
		}else{
			$this->error('非法操作');
		}
	}
	//修改密码
	public function setting()
	{
		A('Public')->loginverify();
		if(IS_POST){
			if(I('post.newpassword') != I('post.repassword')){
				$this->assign('alert','error');
				$this->assign('msg','两次密码不一致');
			}else{
				$user_model = D('User');
				if($user_model->chkPass(session('username'),I('post.password'))){
					$result = $user_model->updateUserInfo(array('userid'=>session('userid'),'user_pass'=>md5(I('post.newpassword').'hc')));
					if($result === true){
						$this->assign('alert','success');
						$this->assign('msg','修改成功');
					}elseif($result === false){
						$this->assign('alert','error');
						$this->assign('msg','修改失败');
					}else{
						$this->assign('alert','error');
						$this->assign('msg',$result);
					}
				}else{
					$this->assign('alert','error');
					$this->assign('msg','当前密码错误！');
				}	
			}
		}
		$this->display();
	}
	//AJAX检查用户名是否重复
	public function chkUserName($user_name = '')
	{
		A('Public')->loginverify();
		if(IS_AJAX){
			if(!empty($user_name)){
				$result = D('User')->chkUserName($user_name);
				if($result === true){
					$this->success('OK');
				}else{
					$this->error($result);
				}
			}
		}
	}
    public function passedit()
    { 
      $wbid=session('wbid');  
      $wbinfo= D('WbInfo')->getOneWbInfoByid($wbid);
      $str=$wbinfo['WBTel'];
      
      $wbinfo['WBTel']=substr($str,0,3).'*****'.substr($str,7,strlen($str));

      $this->assign('wbinfo',$wbinfo);	
      $this->display();          
    }
    public function passedit_yzm()
    { 
      $yzm=I('post.yzm','','string');
      $phone_verifycode=session('phone_verifycode');     
      if($yzm==$phone_verifycode)
      {
        echo 'true';
      }
      else
      { 
        echo 'false';
      }             
    }

  
    public function passedit_set()
    { 
      $session_verify=session('phone_verifycode');
      $verifycode=I('post.yzm','','string'); 
	  $password = I('post.password','','string');
	  $repassword = I('post.repassword','','string');
      if($session_verify==$verifycode)
      {
	
		$bar_update_result=D('WbInfo')->where(array('WBID'=>session('wbid')))->setField('PassWord',md5($password.'hc'));
		$aTempsql= D('WbInfo')->getLastSql();
		$sendstr= $aTempsql.';';
							
		if(!empty($bar_update_result))
		{					      		 		  		   
			$post_data['wbid']=session('wbid');
            $post_data['password']=$password;
			
			$url= 'http://ght.wbzzsf.com/index.php/ServerzongAPI/API_bar_passedit_set.html';
			$res= sendRequsttoOneServer($url, $post_data,30);
														
			// 截取不带前三个字段				
			//$res= substr($res, 3);				
			$res2=json_decode($res,true);	
			if($res2['result']==1)
			{
				//更新成功
				writelog('wbid='.$wbid.'修改密码','barinfoedit');				
			  $LzmWbChange_insert_data=array();
			  $LzmWbChange_insert_data['WB_id']=session('wbid');
			  $LzmWbChange_insert_data['WbInfo_Tag']=1;					 				
			  $LzmWbChange_insert_result=D('LzmWbChange')->add($LzmWbChange_insert_data);								
			}
			else
			{
				writelog('wbid='.$wbid.'修改密码','barinfoediterror');
			}
		 	  
          $data['result']='1';
		  session('phone_verifycode',null);
		}
		else
		{   
           $data['result']='-1';
		}	
       
      }
      else
      {
        $data['result']='-1';
      }
      $this->ajaxReturn($data);
    }

	
	public function chkUserPass($user_pass = ''){
		if(IS_AJAX){
			if(!empty($user_pass))
			{
				$result = D('User')->chkUserPass($user_pass);
				if($result === true)
				{
					$this->success('OK');
				}
				else
				{
					$this->error($result);
				}
			}
		}
	}
	
	
	public function  getLastEndTime()
    {
		$wbid=session('wbid');
		if(empty($wbid))
		{
			$this->redirect('http://check.wbzzsf.cn');
			return;
		}	
		$newpcnum=I('post.newpcnum','1','int');		  
		$barinfo=D('WbInfo')->where(array('WBID'=>$wbid))->find();	  
		if(!empty($barinfo))
		{
			$oldpcnum=$barinfo['CpCount'];
			$oldendtime=$barinfo['EndTime'];
			$nowtime=date('Y-m-d H:i:s',time());
			$oldtimejiange=getdayjiange($nowtime, $oldendtime);
			  
			if( $oldtimejiange >0)
			{
				$sum= $oldpcnum *$oldtimejiange*1;
				$newtimejiange = round($sum / $newpcnum*1);
							
				if($newtimejiange>1)
				{
					$nowtime=strtotime($nowtime);
					$newendtime= strtotime('+'.$newtimejiange.'days',$nowtime);	
					$newendtime= date('Y-m-d H:i:s',$newendtime);
					$data['status']=1;
					$data['endtime']=$newendtime;				
				}
				else
                {
					$data['status']=-1;
				}					
			}						  	
		}else
        {
			$data['status']=-1;
		}
        $this->ajaxReturn($data);		
	}	


 //加减机
   	public function  saveLastEndTime()
    {
		$wbid=session('wbid');
		if(empty($wbid))
		{
			$this->redirect('http://check.wbzzsf.cn');
			return;
		}	
		$newpcnum=I('post.newpcnum','1','int');
		  
	        		
		$barinfo=D('WbInfo')->where(array('WBID'=>$wbid))->find();	  
		if(!empty($barinfo))
		{
			$oldpcnum=$barinfo['CpCount'];
			$oldendtime=$barinfo['EndTime'];
			$nowtime=date('Y-m-d H:i:s',time());
			$oldtimejiange=getdayjiange($nowtime, $oldendtime);
			  
			if( $oldtimejiange >0)
			{
				$sum= $oldpcnum *$oldtimejiange*1;
				$newtimejiange = round($sum / $newpcnum*1);
		
				if($newtimejiange>1)
				{
					$nowtime=strtotime($nowtime);
					$newendtime= strtotime('+'.$newtimejiange.'days',$nowtime);	
					$newendtime= date('Y-m-d H:i:s',$newendtime);
					$data['status']=1;
					$data['endtime']=$newendtime;
					
					
					$result =true;
					D()->startTrans();
					
					if($newpcnum <60 ){
						$data['status']=-1;
						$this->ajaxReturn($data);
						return;
					}
					
										
					$bar_update_data=array();
					$bar_update_data['EndTime']=$newendtime;
					$bar_update_data['CpCount']=$newpcnum;									
					if(D('WbInfo')->where(array('WBID'=>$wbid))->save($bar_update_data)===false)
					{
						
						$result=false;
					}	
					$cpnum_update_data=array();
					$cpnum_update_data['wbid']=$wbid;
					$cpnum_update_data['oldendtime']=$oldendtime;
					$cpnum_update_data['oldpcnum']=$oldpcnum;				
					$cpnum_update_data['newendtime']=$newendtime;
					$cpnum_update_data['newpcnum']=$newpcnum;	
  					
					$cpnum_update_data['dtInsertTime']=date('Y-m-d H:i:s',time());
					$cpnum_update_data['operator']=session('username');
					
				
					if(D('Wbchangenum')->add($cpnum_update_data)===false)
					{
						
						$result=false;
					}
			
					if($result)
					{
						D()->commit();
						$data['status']=1;										   				   
				        $post_data['EndTime']=$newendtime;
				        $post_data['CpCount']=$newpcnum;
				        $post_data['wbid']=$wbid;
					
					    $posturl=C('DAILI_URL_ZONG');
					    $url= $posturl.'/index.php/ServerzongAPI/API_bar_update_cpinfo.html';
					    $res= sendRequsttoOneServer($url, $post_data,30);
																
					// 截取不带前三个字段				
					  // $res= substr($res, 3);				
					   $res2=json_decode($res,true);	
					   if($res2['result']==1)
					  {
						//更新成功
						writelog('wbid='.$wbid.'修改到期时间','barinfoedit');
					  }
					  else
                      {
						writelog('wbid='.$wbid.'修改到期时间','barinfoediterror');
					  }						
						   				  																						
						//给小刘发消息
					  $LzmWbChange_insert_data=array();
					  $LzmWbChange_insert_data['WB_id']=$wbid;
					  $LzmWbChange_insert_data['WbInfo_Tag']=1;					 				
					  $LzmWbChange_insert_result=D('LzmWbChange')->add($LzmWbChange_insert_data);					  
						   
					}else
                    {
						D()->rollback();
                        $data['status']=-1;						
					}						
					
				}
				else
                {					
					$data['status']=-1;
				}					
			}
			else
			{
				$data['status']=-1;
			}						  	
		}
		else
        {
			$data['status']=-1;
		}
        $this->ajaxReturn($data);		
	}	
   public function getjiajianjilog()
	{
		if(IS_AJAX)
		  {
			$page = I('get.page',1,'int');
			$rows = I('get.rows',20,'int');
			$sord = I('get.sord','','string')?:'desc';
			$sidx = I('get.sidx','','string')?:'dtInsertTime';

            $daterange     = I('get.daterange','','string');
			$map = array();
			$map['wbid']=session('wbid');
		     
			if(!empty($daterange))  
			{
			  list($start,$end) = explode(' - ',$daterange);    
			  $start = str_replace('/','-',$start);            
			  $end = str_replace('/','-',$end);                
			  $map['dtInsertTime'] = array('BETWEEN',array($start,$end));
			}
			
			$count= D('Wbchangenum')->getjiajianjilog_Count($map);
			$sql_page=ceil($count/$rows);  
			if($page>$sql_page) $page=1;

			$loglist = D('Wbchangenum')->getjiajianjilog($map,"$sidx $sord",$page,$rows);
			
			$response = new \stdClass();
			$response->records = $loglist['count'];
			$response->page = $page;
			$response->total = ceil($loglist['count'] / $rows);
			foreach($loglist['list'] as $key => $value)
			{       
			  $response->rows[$key]['id'] = $key;
			  $response->rows[$key]['cell'] = $value;
			}
			$this->ajaxReturn($response);
		  }   
		  
	} 	
	
	
	
	

}