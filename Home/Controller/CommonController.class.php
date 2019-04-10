<?php
namespace Home\Controller;
use Think\Controller;
class CommonController extends Controller{

	protected function _initialize()
	{
		if(!is_login())
		{   	    
	        $bgs=I('get.Bgs','','string');
			if(!empty($bgs))
			{
				redirect('http://www.baidu.com');
			}	
			
			$url='http://check.wbzzsf.cn/login';
			redirect($url, 1, '登陆超时...');	
			//$this->redirect('Public/login');
			exit;
		}
		else
		{    
            checkChaoshiAndQx();	
			
			
			// $dai_lingqu_num= D('Productxs')->where(array('wbid'=>session('wbid'),'lingqu_status'=>0,'pay_position'=>1))->count();
			// $this->assign('num',$dai_lingqu_num);
			$wbid=session('wbid');
			$map = array(); 	
			$map['wbid']=$wbid;	
			$map['pay_position']=1;		
			$map['lingqu_status']=0;				
			$dai_lingqu_num= D('Productxs')->where($map)->count();
			
			
			$this->assign('dai_lingqu_num',$dai_lingqu_num);
			
			
			$syd_play_song= D('Webini')->where(array('wbid'=>$wbid,'skey'=>'syd_play_song'))->getField('svalue');
			if($syd_play_song==='0')
			{
			   // $syd_play_song=0;  
			   $shengyinurl='';
			}
			else
			{
			  // $syd_play_song=1; 
              $shengyinurl=C('SHANGPIN_SHENGYIN_PATH_URL').'bgstip.wav';			  
			}	

		
			
			
			$this->assign('shengyinurl',$shengyinurl);
			
			$perm = D('Perm')->getPermByRoleid();  //此步开始获取登录的角色的id		
			$perm = list_to_tree($perm,0,'perm_id','parent_id');
			
			/*
			
				include C('EXCEL_PATH2').'database/core.class.php';
	
            $db = new Core();
			$checkdb=new Core();
			$agentid=$checkdb->fnCheckBangdinginfo($wbid);
			if(!empty($agentid))
			{
				$agent_realname=$checkdb->fnGetOneAgentNameByAgentid($agentid);
			}
			else
            {
			  	$agent_realname='';
			}				
			
			*/
			
			
			
			
	     /*
	     if(isset($_SESSION['agent_realname']))
		 {
			$agent_realname= $_SESSION['agent_realname'];
		 }
		 else
         {
			$post_data['wbid']=session('wbid');	
			$daili_url_zong =C('DAILI_URL_ZONG');		
			$url= $daili_url_zong.'/index.php/ServerzongAPI/API_query_onebarbanginfo.html';
			$res= sendRequsttoOneServer($url, $post_data,30);
			$res= substr($res, 3);				
			$res2=json_decode($res,true);	
			$agentinfo=$res2['body'];
			$agent_realname=$agentinfo['agent_realname'];
			session('agent_realname')=$agent_realname;
		 }			 
	   	   */ 
	//	$this->assign('agent_realname',$agent_realname);
      
			
		
			

			$this->assign('perm',$perm);
					
		}
	}
	

		
		
		public  function get_neworder_list()
		{
			//header('Location:http://www.wbzzsf.com');
			//exit;
			$old_dailing_count=session('dailing_count');
			 

			$map = array(); 	
			$map['wbid']=session('wbid');	
			$map['pay_position']=1;		
			$map['lingqu_status']=0;				
			$count= D('Productxs')->where($map)->count();
			
		
			if($old_dailing_count==$count) //不刷新
			{
				$response['status']=-1;
			}
			else if($old_dailing_count > $count) //领取了一个,或者取消了一个，不发出声音
			{
			  $response['status']=2;
			  $response['count']= $count;	   
			  session('dailing_count',$count);	 
			}
			else	
			{	
			  
			  $response['status']=1;
			  $response['count']= $count;
			  $response['url']=C('SHANGPIN_SHENGYIN_PATH_URL').'bgstip.wav';
			  
			  
			  session('dailing_count',$count);
			  
			}
				
			$this->ajaxReturn($response);
			
		}
	


	
	
	public  function get_neworder_kehuduan_list()
	{
		$old_dailing_count=session('dailing_kehuduan_count');
		 	
		$map = array(); 	
		$map['wbid']=session('wbid');	
        $map['pay_position']=1;		
	    $map['lingqu_status']=0;				
		$count= D('Productxs')->where($map)->count();
		
		
	
		if($old_dailing_count==$count) //不刷新
		{
			$response['status']=-1;
			
		}	
		else	
        {	
	      
		  $response['status']=1;
		  $response['count']= $count;	  
		  session('dailing_kehuduan_count',$count);
		  
		
		  
		}
			
		$this->ajaxReturn($response);
		
	}
	
	

	protected function chkPerm($current)
	{
		if(!D('Perm')->chkPerm($current))
		{
			$this->error('没有访问权限！');
		}
	}
	public function empty_cache()
	{
		if(IS_AJAX){
			if(empty_dir('../Runtime')){
				$this->success();
			}else{
				$this->error();
			}
		}
	}
	/*
	 *	通过Id得到下属地区列表
	 *	@param (int)id 上级地区Id
	 *	
	 *	return (array)('status'=>'1','info'=>'$html','url'=>'')
	 */


	
	public function changeUserId(){
		$id = I('get.id',0,'int');
		if(!empty($id)){
			$user = D('User')->getUserInfo($id);
			if($user && session('superadmin') == 1){
				session('username',$user['user_name']);
				session('userid',$user['userid']);
				session('realname',$user['realname']);
				session('roleid',$user['roleid']);
			}
		}
		$this->redirect('Index/index');
	}


    public function write_log($actmodel,$actcode, $actdes, $result = '成功')
    {     
       
        $log_insert_data = array();
        $log_insert_data['nOptModule']        = $actmodel;
        $log_insert_data['nActioncode']        = $actcode;
        $log_insert_data['sActiondesciption'] = $actdes;
        $log_insert_data['sResult']           = $result;	   
	    $log_insert_data['nOperatorId']=session('userid'); 
	    $log_insert_data['dtInsertTime']=date('Y-m-d H:i:s',time()); 

	    $log_result_data=  D('Operatelog')->insertOneOperateLog($log_insert_data);
    }
}

