<?php
namespace Home\Controller;
use Think\Controller;
class PublicController extends Controller{
	/*
	 *	登录
	 */
	 
	public function  test()
    {
		$res=D('WbInfo')->where(array('WBID'=>1997))->select();
		echo  json_encode($res);
	}	
	 
	public function login()
	{
		 // header('location:http://check.wbzzsf.cn');
		 //  echo  '系统维护中';
		 // return;
        if(IS_GET)//判断是否是exe程序登录
		{
			$username = I('get.username');//从login.html页面用post方法获取到username,password,formhash数据
			$password = I('get.password');
			$flag = I('get.flag','','string');
			$username1 = I('get.username1','','string');
			$password1 = I('get.password1','','string');
			$yuangong_wbid = I('get.wbid','','string');			        
             //员工登录
			if(!empty($username1) )
			{
               if(!empty($yuangong_wbid))
               {                
               	  $wbid=$yuangong_wbid;              
               	  $nowtime=date('Y-m-d H:i:s');
               	  $wb_endtime= D('WbInfo')->where(array('WBID'=>$wbid))->getField('EndTime');
                  $shijiancha=getTimeCha($wb_endtime,$nowtime);
                  $wb_endtime= date('Y-m-d H:i:s',strtotime(D('WbInfo')->where(array('WBID'=>$wbid))->getField('EndTime')));              
               	  if($shijiancha>0)
               	  {
                    $data['result']=-1;
					$this->ajaxReturn($data);  //授权到期
                    return; 
               	  }                				  				 
                  $oneyuangonginfo = D('Yuangong')->where(array('name'=>$username1,'pw'=>md5($password1.'!@#BGS159357'),'WB_ID'=>$wbid))->find();                			  
                  if(!empty($oneyuangonginfo))
                  {                     
                        session('username',$oneyuangonginfo['name']); 
						session('roleid',$oneyuangonginfo['role_id']);
						session('wbid',$oneyuangonginfo['WB_ID']);
						session('endtime',$wb_endtime);
						
	                    $realname=D('WbInfo')->where(array('WBID'=>$oneyuangonginfo['WB_ID']))->getField('WbName');
						session('realname',$realname);
						session('logintime',date(' Y-m-d H:i:s',time()));
						
						$aLoginGuid=getGuid();
						D('Yuangong')->where(array('name'=>$username1,'pw'=>md5($password1.'!@#BGS159357'),'WB_ID'=>$wbid))->setField('sLoginGuid',$aLoginGuid);
						session('LoginGuid',$aLoginGuid); 
                        session('qx',1);											   
						//判断是否有销售页面的权限，没有则跳转到首页
						$role_perm=D('Role')->where(array('WB_ID'=>session('wbid'),'role_id'=>session('roleid')))->getField('role_perm');
                        $bFangwen= strpos($role_perm, '223');
						if($bFangwen)
						{
							$this->redirect('Chaoshi/xiaoshou');
						}
						else
                        {
						   $this->redirect('Index/Index');	
						} 																													
                  }
                  else
                  {   			         
                    $data['result']=0;
					echo json_encode($data);
                  }	
               }
               else
               {                   
                    $data['result']=0;
					echo json_encode($data);
               }	
			}
		    else  if(!empty($username) && !empty($password))
			{						
				$user= D('WbInfo')->where(array('WbAccount'=>$username,'PassWord'=>trim($password)))->find(); 							
				if($user)//判断数组是否为空
				{						
                  $wbid=$user['WBID'];				 
                  $nowtime=date('Y-m-d H:i:s');			 
               	  $wb_endtime=$user['EndTime'];
				  			  
                  $shijiancha=getTimeCha($wb_endtime,$nowtime);				  
                  $wb_endtime= date('Y-m-d H:i:s',strtotime($user['EndTime']));
				 
               	  if($shijiancha>0)
               	  {
                    $data['result']=-1;
					$this->ajaxReturn($data);  //授权到期

                     return; 
               	  }               
				    session('username',$user['WbAccount']);                         						
					session('roleid','999999');
					session('wbid',$user['WBID']);
					session('realname',$user['WbName']);
					session('endtime',$wb_endtime);	
					$aLoginGuid=getGuid();
					D('WbInfo')->where(array('WBID'=>$user['WBID']))->setField('sLoginGuid',$aLoginGuid);
					session('logintime',date(' Y-m-d H:i:s',time()));
					session('LoginGuid',$aLoginGuid);
                    session('qx',-1);					
                   
				     $data['result']=1;
					 if($flag=='1')
					 { 
						$list=D('Yuangong')->where(array('WB_ID'=>session('wbid')))->select();
						
						foreach($list as &$val)
						{
						  $namelist.=$val['name'].',';	
						}						
						$data['data']=$namelist;
						$data['wbid']=session('wbid');	
						$data['wbname']=D('WbInfo')->where(array('WBID'=>session('wbid')))->getField('WbName');;
					 }	 					 
					 echo json_encode($data);
				}
				else
				{		                     		
					$data['result']=0;
					echo json_encode($data);  //帐号或密码错误					
				}
			}	
			                     		
		}
		
		if(IS_POST)//判断是否是web网站登录
		{
			$username = I('post.username');//从login.html页面用post方法获取到username,password,formhash数据
			$password = I('post.password');

			$username1 = I('post.username1');
			$username2= I('post.username2');
			$password1 = I('post.password1');           
             //员工登录
			if(!empty($username1) && !empty($username2))
			{
               $bossaccount = D('WbInfo')->where(array('WbAccount'=>$username1))->find(); 
			 
               if(!empty($bossaccount))
               {
               	  $wbid=$bossaccount['WBID'];
               	  $nowtime=date('Y-m-d H:i:s');
               	  $wb_endtime= D('WbInfo')->where(array('WBID'=>$wbid))->getField('EndTime');
                  $shijiancha=getTimeCha($wb_endtime,$nowtime);

                  $wb_endtime= date('Y-m-d H:i:s',strtotime(D('WbInfo')->where(array('WBID'=>$wbid))->getField('EndTime')));
                  
               	  if($shijiancha>0)
               	  {
                    $data['result']=-1;
					$this->ajaxReturn($data);  //授权到期

                     return; 
               	  }                 
				   $oneyuangonginfo = D('Yuangong')->where(array('name'=>$username2,'pw'=>md5($password1.'!@#BGS159357'),'WB_ID'=>$wbid))->find();
                  if(!empty($oneyuangonginfo))
                  {
					writelog('网吧id:'.$oneyuangonginfo['WB_ID'].'员工:'.$oneyuangonginfo['name'].'登录成功','login');
					session('username',$oneyuangonginfo['name']); 
					session('roleid',$oneyuangonginfo['role_id']);
					session('wbid',$oneyuangonginfo['WB_ID']);
					session('endtime',$wb_endtime);

					
					$realname=D('WbInfo')->where(array('WBID'=>$oneyuangonginfo['WB_ID']))->getField('WbName');
					session('realname',$realname);
					session('logintime',date(' Y-m-d H:i:s',time()));
					
					$aLoginGuid=getGuid();
					D('Yuangong')->where(array('name'=>$username2,'pw'=>md5($password1.'!@#BGS159357'),'WB_ID'=>$wbid))->setField('sLoginGuid',$aLoginGuid);
					session('LoginGuid',$aLoginGuid); 
					session('qx',1);						
				   
					$data['result']=1;
					$this->ajaxReturn($data);					  
                  }
                  else
                  {   			          
                    $data['result']=0;
					$this->ajaxReturn($data);  //帐号或密码错误
                  }	

               }
               else
               {                      
                    $data['result']=2;
					$this->ajaxReturn($data);  //帐号或密码错误
               }	


			}
		    else   //老板登录
			{
				$user = D('WbInfo')->chkPass($username,$password);  
				
				if($user)//判断数组是否为空
				{		
				
                  $wbid=$user['WBID'];				 
                  $nowtime=date('Y-m-d H:i:s');			 
               	  $wb_endtime=$user['EndTime'];
				  			  
                  $shijiancha=getTimeCha($wb_endtime,$nowtime);				  
                  $wb_endtime= date('Y-m-d H:i:s',strtotime($user['EndTime']));
				 
               	  if($shijiancha>0)
               	  {
                    $data['result']=-1;
					$this->ajaxReturn($data);  //授权到期

                     return; 
               	  }
                 
                    writelog('网吧id:'.$wbid.'老板:'.$user['WbAccount'].'登录成功','login');
				    session('username',$user['WbAccount']);                         						
					session('roleid','999999');
					session('wbid',$user['WBID']);
                    //$realname=D('WbInfo')->where(array('WBID'=>$user['wbid']))->getField('WbName');
					session('realname',$user['WbName']);
					session('endtime',$wb_endtime);
		
					$aLoginGuid=getGuid();
					D('WbInfo')->where(array('WBID'=>$user['WBID']))->setField('sLoginGuid',$aLoginGuid);
					session('logintime',date(' Y-m-d H:i:s',time()));
					session('LoginGuid',$aLoginGuid);
                    session('qx',-1);					

				     $data['result']=1;
				     $this->ajaxReturn($data);
				}
				else
				{		
                       		
					$data['result']=0;
					$this->ajaxReturn($data);  //帐号或密码错误
					
				}
			}	
			                     		
		}

		$this->display();       //失败的时候依然运行本login.html，展示数据
	}
	
	
	
	
	
	
	

	
	public function login_fu()
	{
	
       	
        if(IS_GET)//判断是否是exe登录
		{
			$username = I('get.username');//从login.html页面用post方法获取到username,password,formhash数据
			$password = I('get.password');
			$flag = I('get.flag','','string');
			$loginrole   = I('get.loginrole','','string');
			$username1 = I('get.username1','','string');
			$password1 = I('get.password1','','string');
			$username2 = I('get.username2','','string');
			
			//$username=base64_decode($username);
			//$username = iconv("gb2312","UTF-8",$username);
			writelog(' IP:'.$_SERVER["REMOTE_ADDR"].'---0-1---'.$username,'login');
			
			$username=urldecode($username);
			$password=urldecode($password);
			$username1=urldecode($username1);
			$username2=urldecode($username2);			
			$password1=urldecode($password1);
			// writelog('---0-2---'.$username,'login');
			// writelog('---0-3---'.$password,'login');
			
			 writelog('---1----'.$username2,'login');			 
			if($flag=='web')  //浏览器登录,老板登录
			{	
                if($loginrole==1) //老板
                {
					
					$user = D('WbInfo')->chkPass($username,$password); 
					if($user)//判断数组是否为空
					{			                				 
					  $nowtime=date('Y-m-d H:i:s');			 
					  $wb_endtime=$user['EndTime'];				  	                             				 
					  $shijiancha=getTimeCha($wb_endtime,$nowtime);				  
					  $wb_endtime= date('Y-m-d H:i:s',strtotime($user['EndTime']));		 				
					  if($shijiancha>0)
					  {
						redirect('http://check.wbzzsf.cn/index.php', 1, '授权到期,登陆失败...');														
					  }
					  
					  
					  
					  //判断是否是连锁
					  if(empty($user['is_liansuo']))  //单体网吧
					  {
						$wbid=$user['WBID'];

						//判断是否是周口网吧
						
						if($user['province']==10 &&  $user['city']==799 &&  $user['area']==711 && $user['VerNo'] >1 && $user['VerNo'] <46 )
						{
							redirect('http://check.wbzzsf.cn/index.php', 1, '请升级到最新版,登陆失败...');
						}
                       /*
						if($wbid==1357 )
						{
							redirect('http://check.wbzzsf.cn/index.php', 1, '请升级到最新版,登陆失败...');
						}
						*/
						
						/*
						$xianzhi_str='2912,2328,2068,2015,1958,3183,1763,2136,2767,2801,2948,2245,3236,3059,362,2128,2806,2070,1103,2337,1357,';						
						$xianzhi_array = explode(",", $xianzhi_str);						
						$bxianzhi = in_array($wbid,$xianzhi_array);
						if($bxianzhi)
						{
							redirect('http://check.wbzzsf.cn/index.php', 1, '请升级到最新版,登陆失败...');
						}	
                        */  
						
						
				
						
						
						//&&  ($user['VerNo'] <=43)
						
						
						//2912,2328,2068,2015,1958,3183,1763,2136,2767,2801,2948,2245,3236,3059,362,2128,2806,2070,1103,2337,1357
						
						

						
						session('username',$user['WbAccount']);                         						
						session('roleid','999999');
						session('wbid',$wbid);
						session('realname',$user['WbName']);
						session('endtime',$wb_endtime);
						
						$aLoginGuid=getGuid();
						D('WbInfo')->where(array('WBID'=>$user['WBID']))->setField('sLoginGuid',$aLoginGuid);
						
						
						session('logintime',date(' Y-m-d H:i:s',time()));
						session('LoginGuid',$aLoginGuid);
						session('liansuo_id','');
						session('qx',-1);	
						writelog(' IP:'.$_SERVER["REMOTE_ADDR"].'---登陆成功--1---wbid=-'.$wbid.' username='.$user['WbAccount'],'login');  					               	 					   
					  }
					  else   //连锁网吧
					  {
						 
						if($user['liansuo_admin']==1) //连锁网吧的总管理员
						{					
							session('username',$user['WbAccount']);                         						
							session('roleid','888888');
							session('wbid',0);                  
							session('realname',$user['WbName']);
							session('endtime',$wb_endtime);
							
							$aLoginGuid=getGuid();
							D('WbInfo')->where(array('WBID'=>$user['WBID']))->setField('sLoginGuid',$aLoginGuid);
							session('logintime',date(' Y-m-d H:i:s',time()));
							session('LoginGuid',$aLoginGuid);
							session('qx',-3);   //qx=-1  单体网吧老板  qx=-2 单体网吧员工   qx=-3 连锁网吧总管理   qx=-4连锁网吧某个店老板   qx=-5 连锁网吧某个店员工
							session('indexqx',-1);   // 如果是普通网吧的话，权限是0
							session('liansuo_id',$user['Ls_Id']);
							
							 writelog(' IP:'.$_SERVER["REMOTE_ADDR"].'---登陆成功2-连锁总----wbid=-'.$wbid.' username='.$user['WbAccount'],'login'); 
						}
						else  //连锁网吧某个店的老板
						{
							session('username',$user['WbAccount']);                         						
							session('roleid',$user['role_id']);
							session('wbid',$user['WBID']);                  
							session('realname',$user['WbName']);
							session('endtime',$wb_endtime);
							$aLoginGuid=getGuid();
							D('WbInfo')->where(array('WBID'=>$user['WBID']))->setField('sLoginGuid',$aLoginGuid);
							session('logintime',date(' Y-m-d H:i:s',time()));
							session('LoginGuid',$aLoginGuid);
							session('qx',-4);   //qx=-1  单体网吧老板  qx=-2 单体网吧员工   qx=-3 连锁网吧总管理   qx=-4连锁网吧某个店老板   qx=-5 连锁网吧某个店员工
							session('liansuo_id',$user['Ls_Id']);  
							 
                            writelog(' IP:'.$_SERVER["REMOTE_ADDR"].'---登陆成功--3---wbid=-'.$user['WBID'].' username='.$user['WbAccount'],'login'); 							 
						}
					  }					  				  				 					               	                                              					
						$this->redirect('Index/index');														           
					}
					else
					{
					  
					   redirect('http://check.wbzzsf.cn/index.php', 1, '密码错误...');
					}
				}
				else  if($loginrole==2) //员工登陆 
                {
					
					if(!empty($username1) && !empty($username2))
					{  
				       
						$onebarinfo = D('WbInfo')->where(array('WbAccount'=>$username1))->find(); 
						if(empty($onebarinfo['WBID']))
						{
							redirect('http://check.wbzzsf.cn/index.php', 1, '网吧员工登陆失败...');
						}
                     					
						$wbid=$onebarinfo['WBID']; 
						$nowtime=date('Y-m-d H:i:s');				  				  
						$wb_endtime= D('WbInfo')->where(array('WBID'=>$wbid))->getField('EndTime');
						$shijiancha=getTimeCha($wb_endtime,$nowtime);
						$wb_endtime= date('Y-m-d H:i:s',strtotime(D('WbInfo')->where(array('WBID'=>$wbid))->getField('EndTime')));
						  
						if($shijiancha>0)
						{
							redirect('http://check.wbzzsf.cn/index.php', 1, '授权到期,登陆失败...');										
						}
						
						
						if($onebarinfo['province']==10 &&  $onebarinfo['city']==799 &&  $onebarinfo['area']==711 && $onebarinfo['VerNo'] >1 && $onebarinfo['VerNo'] <46 )
						{
							redirect('http://check.wbzzsf.cn/index.php', 1, '请升级到最新版,登陆失败...');
						}
						/*
						if($wbid==1357 )
						{
							redirect('http://check.wbzzsf.cn/index.php', 1, '请升级到最新版,登陆失败...');
						}
						*/
						
						/*				
						$xianzhi_str='2912,2328,2068,2015,1958,3183,1763,2136,2767,2801,2948,2245,3236,3059,362,2128,2806,2070,1103,2337,1357,';						
						$xianzhi_array = explode(",", $xianzhi_str);						
						$bxianzhi = in_array($wbid,$xianzhi_array);
						if($bxianzhi)
						{
							redirect('http://check.wbzzsf.cn/index.php', 1, '请升级到最新版,登陆失败...');
						}	
                        */
						
					  							 
					    $oneyuangonginfo = D('Yuangong')->where(array('name'=>$username2,'pw'=>md5($password1.'!@#BGS159357'),'WB_ID'=>$wbid))->find();  

					
					    if(!empty($oneyuangonginfo))
					    {                     
							session('username',$oneyuangonginfo['name']); 
							session('roleid',$oneyuangonginfo['role_id']);
							session('wbid',$oneyuangonginfo['WB_ID']);
							session('endtime',$wb_endtime);
						
							$realname=D('WbInfo')->where(array('WBID'=>$oneyuangonginfo['WB_ID']))->getField('WbName');
							session('realname',$realname);
							session('logintime',date(' Y-m-d H:i:s',time()));
							
							$aLoginGuid=getGuid();
							D('Yuangong')->where(array('name'=>$username2,'pw'=>md5($password1.'!@#BGS159357'),'WB_ID'=>$wbid))->setField('sLoginGuid',$aLoginGuid);
							
						 
	  
							session('LoginGuid',$aLoginGuid); 
							
							$aShujukuGuid=session('LoginGuid');
							
							writelog(' IP:'.$_SERVER["REMOTE_ADDR"].'username='.$oneyuangonginfo['name'].' wbid='.$wbid.' '.$aShujukuGuid,'yuangong_login1');
	                        writelog('wbid='.$wbid.' '.$aLoginGuid,'yuangong_login1');
							
							session('qx',1);	
			
							
										   
						  //判断是否有销售页面的权限，没有则跳转到首页
							$role_perm=D('Role')->where(array('WB_ID'=>session('wbid'),'role_id'=>session('roleid')))->getField('role_perm');
							$bFangwen= strpos($role_perm, '223');
							if($bFangwen)
							{
								$this->redirect('Chaoshi/xiaoshou');
							}
							else
							{
							   $this->redirect('Index/Index');	
							}
							
																					 																										
						}
						else
						{   			         
							redirect('http://check.wbzzsf.cn/index.php', 1, '网吧员工登陆失败...');						
						}
					}	
                }				    
			}
			else if($flag=='exe')  //exe登录
			{		
		        if($roleid==2 && !empty($username1) && !empty($yuangong_wbid))
		        {                   							
				   
				  $wbid=$yuangong_wbid;      
               	  $nowtime=date('Y-m-d H:i:s');
               	  $wb_endtime= D('WbInfo')->where(array('WBID'=>$wbid))->getField('EndTime');
                  $shijiancha=getTimeCha($wb_endtime,$nowtime);

                  $wb_endtime= date('Y-m-d H:i:s',strtotime(D('WbInfo')->where(array('WBID'=>$wbid))->getField('EndTime')));
                  
               	  if($shijiancha>0)
               	  {
                    $data['result']=-1;
					$this->ajaxReturn($data);  //授权到期
                    return; 
               	  }
                  
				  				 
                  $oneyuangonginfo = D('Yuangong')->where(array('name'=>$username1,'pw'=>md5($password1.'!@#BGS159357'),'WB_ID'=>$wbid))->find();                 					  
                  if(!empty($oneyuangonginfo))
                  {                     
                        session('username',$oneyuangonginfo['name']); 
						session('roleid',$oneyuangonginfo['role_id']);
						session('wbid',$oneyuangonginfo['WB_ID']);
						session('endtime',$wb_endtime);
					
	                    $realname=D('WbInfo')->where(array('WBID'=>$oneyuangonginfo['WB_ID']))->getField('WbName');
						session('realname',$realname);
						session('logintime',date(' Y-m-d H:i:s',time()));
						
						$aLoginGuid=getGuid();
						D('Yuangong')->where(array('name'=>$username1,'pw'=>md5($password1.'!@#BGS159357'),'WB_ID'=>$wbid))->setField('sLoginGuid',$aLoginGuid);
						session('LoginGuid',$aLoginGuid); 
                        session('qx',1);						
					   
						$role_perm=D('Role')->where(array('WB_ID'=>session('wbid'),'role_id'=>session('roleid')))->getField('role_perm');
						
						 writelog(' IP:'.$_SERVER["REMOTE_ADDR"].'---登陆成功--5---wbid=-'.$oneyuangonginfo['WB_ID'].' username='.$oneyuangonginfo['name'],'loginexe'); 	
                        $bFangwen= strpos($role_perm, '223');
						if($bFangwen)
						{
						   $this->redirect('Chaoshi/xiaoshou');
						}
						else
                        {
						   $this->redirect('Index/Index');	
						} 																												
	                }
	                else
	                {   			         
	                    $data['result']=0;
						echo json_encode($data);
	                }	 
		        }
		        else
		        {
	                if(!empty($username) && !empty($password))
					{								
						$user= D('WbInfo')->where(array('WbAccount'=>$username,'PassWord'=>trim($password)))->find(); 	
						
						
						if($user)//判断数组是否为空
						{		
	                     					
						    $data['result']=1;																				 	 						 
							echo json_encode($data);
						}
						else
						{	
	                       					
							$data['result']=0;
							echo json_encode($data);  			
						}
					}
		        }			      
			}
			else 
			{
                $this->success('登录失败2', 'Index/index',3); 
			}		                     		
		}
		
		
	}
	
	
	
	
	
	

	/*
	 *	注销
	 */
	public function logout()
	{
		session(null);
		redirect('http://check.wbzzsf.cn/login');
	}

	public function loginverify()
	{
	   $aLoginGuid=session('LoginGuid');
	   $aUserid=session('wbid');
	   $aShujukuGuid=D('User')->where(array('wbid' =>$aUserid))->getField('sLoginGuid');

	   if($aLoginGuid==$aShujukuGuid)
	   {
          
	   }
	   else
	   {
          session(null);
          header("Content-Type:text/html; charset=utf-8");
          die("<script>alert('该账号已在其他机器登录！'); document.location.href=\"<?php echo U('Public/login'); ?>\";</script>");
	   }	
	}
	
	
	
	public function getPhoneVerifycode() 
    {
       $verifycode=(mt_rand(100000,999999));  
       writelog('getPhoneVerifycode 产生的6位随机码----'.$verifycode,'yzm');
      
       session('phone_verifycode',$verifycode);
       $WbAccount=I('get.WbAccount','','string');
	   
	   $register_guid=session('register_guid');
	   $aGetGuid=I('get.register_guid','','string');
	   
	   $phonenum=D('WbInfo')->where(array('WbAccount'=>$WbAccount))->getField('WBTel');

       writelog('手机phonenum----'.$phonenum,'yzm');
      if(!empty($phonenum) && ($register_guid== $aGetGuid) &&!empty($register_guid) && !empty($aGetGuid))
      {    
        SendToTelOfAccNo($phonenum,$verifycode);
		session('register_guid',null);
		
      }
      else
      {
        writelog('手机短信验证码不发送----'.$verifycode,'yzm');
		$data['status']='0';
		session('register_guid',null);
	    echo json_encode($data);	
      }       
    }
	
	public function  resetpassword()
	{ 	    			
		if(IS_POST)
		{
		  $password=I('post.NewPassWord','','string');	
		  $confirm_password=I('post.confirm_password','','string');
		  $username=I('post.WbAccount','','string');
		  
		  writelog('-----beg------','resetpassword');
		  writelog($username,'resetpassword');
		  	  	
			if($password != $confirm_password)
			{
				$data['result']=-1;
				
			}
			else                             //数据无问题，则插入到数据库
			{
				$result=true; 
				D()->startTrans();  //启用事务
				//更新网吧info 
				
				if(D('WbInfo')->where(array('WbAccount'=>$username))->setField('PassWord',md5($password.'hc'))===false)
				{
				  $result=false;             				  
				}
				$aTempsql= D('WbInfo')->getLastSql();
				$sendstr= $aTempsql.';';
				
									
				if($result)
				{	
                   		
				   D()->commit();  //提交事务
				  $wbid=D('WbInfo')->where(array('WbAccount'=>$username))->getField('WBID');	
				  if(!empty($wbid))
				  {
					$res =PostTopUpdateDataToWb_lzmByWbid($wbid,'Php_To_Top_Sql',$sendstr);  
					if(!empty($res))
					  {
						 writelog($wbid.'修改 密码  命令已发送成功','resetpassword');					 
					  }
					  else
					  {
						 writelog($wbid.'修改 密码  命令已发送失败','resetpassword');
					  } 
				  }	  				  				
					$data['result']=1;
				
				}
				else
				{   
					 D()->rollback();    //回滚
					 $data['result']=-1;
					$this->write_log(30,30,'账号:'.$username,'失败');
				
				}						
	           $this->ajaxReturn($data);
			}
		}  	   				  
		$username=I('get.WbAccount','','string');
        $this->assign('WbAccount',$username);		
		$this->display();
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
	
	
	public function findpassword()
	{	
        	if(IS_POST)
			{
			  $yzm=I('post.yzm','','string');	
			  $WbAccount=I('post.WbAccount','','string');
			  $verify=session('phone_verifycode');	
			   $data['result']=-1;
			   $phonenum=D('WbInfo')->where(array('WbAccount'=>$WbAccount))->getField('WBTel');
			   if(!empty($phonenum))
			   {
					if(trim($yzm) <> trim($verify))
					{
					   $data['result']=-1;
					}
					else
					{
					   $data['result']=1;
					} 
					
							  
			   }	 
			  $this->ajaxReturn($data);		   
					  
			}	
	    $aGuid=getGuid();
		session('register_guid',$aGuid);	
		$this->assign('register_guid',$aGuid);    
		$this->display();
	}
	
		public function findpassword_set()
		{
			if(IS_POST)
			{
			  $yzm=I('post.yzm','','string');	
			  $WbAccount=I('post.WbAccount','','string');
			  			  		  
			  $verify=session('phone_verifycode');
	
			   $data['result']=-1;
			   $phonenum=D('WbInfo')->where(array('WbAccount'=>$WbAccount))->getField('WBTel');
			   if(!empty($phonenum))
			   {
					if(trim($yzm) <> trim($verify))
					{
					   $data['result']=-1;
					}
					else
					{
					   $data['result']=1;
					} 
					
							  
			   }	 
			  $this->ajaxReturn($data);		   
					  
			}
		}
		/*
		public function  daoru()
		{
			return;
			$yuangonglist=D('Yuangong')->select();
			$result=true;
			D()->startTrans();  //启用事务
			foreach($yuangonglist as &$val)
			{
				if(D('Role')->where(array('WB_ID'=>$val['WB_ID']))->find())
				{
				  	
				}
				else
                {
					 //如果不存在,就加俩模板
					 
						$moban_role_data=D('RoleMb')->select(); 
						foreach($moban_role_data as &$mbval)
						{
						  $role_insert_data=array();
						  $new_role_id=D('Role')->max('role_id')+1;
						  $role_insert_data['role_id']= $new_role_id; 
						  $role_insert_data['WB_ID']=$val['WB_ID'];          
						  $role_insert_data['role_name']=$mbval['role_name'];
						  $role_insert_data['groupqx']=addyanma($mbval['groupqx']);
						  $role_insert_data['boss_qx']=$mbval['bossqx'];
						  $role_insert_data['role_perm']=$mbval['role_perm'];
						  $role_insert_data['dtInsertTime']=date('Y-m-d H:i:s'); 

						  if(D('Role')->add($role_insert_data) === false)
						  {         
							 $result=false;
						  }  
						   $aTempstr=D('Role')->getLastSql();
						   $sendstr.=$aTempstr.';';  
						}    
		
				}					
			}
			  
		
			// $yuangonglist2=D('Yuangong')->select();
			// foreach($yuangonglist2 as &$val2)
			// {
			  	// if(!empty($val2['Guid']) && !empty($val2['role_id']))
				// {
					
				// }
				// else
                // {
                   // $yuangong_update_data['Guid']= getGuid();
				   // $yuangong_update_data['role_id']= D('Role')->where(array('WB_ID'=>$val2['WB_ID'],'boss_qx'=>1))->getField('role_id');
  				   
				   // if($val2['name']=='boss')
				   // {
					   // if($role_id=D('Yuangong')->where(array('WB_ID'=>$val2['WB_ID'],'boss_qx'=>1))->data(yuangong_update_data)->save()===false)
                       // {
						  // $result=false; 
				
					   // }	
                        // $aTempstr=D('Yuangong')->getLastSql();
						// $sendstr.=$aTempstr.';';  	
                 					
				   // }	   
				   
				// }					
			// }
			     
			
            if($result)
            {
              D()->commit();  //提交事务
              $wbid=0;
              // $res =PostTopUpdateDataToWb_lzm('Php_To_Top_Sql','WComputerList,WGroupTable');
              $res =PostRegisterDataToWb_lzm($wbid,'Php_To_Top_Sql',$sendstr);
              if(!empty($res))
              {    
				 	
              }
              else
              {
				
              }  
              $this->success();
            }
            else
            {

              D()->rollback();    //回滚
              $this->error();
            }
			
		}

         */
   









}


