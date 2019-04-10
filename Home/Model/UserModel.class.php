<?php
namespace Home\Model;
use Think\Model;
class UserModel extends Model{
	protected $tableName = 'wt_admin';	//数据表名

	// protected $pk = 'userid';

	/*
	 *	验证密码
	 *	@param username	用户名
	 *	@param password	密码（明文）
	 *
	 *	@return array || boolean
	 */


	public function chkUserName($user_name){
		if(strlen($user_name) < 3){
			return '用户名不能小于3位';
		}
		$result = $this->where(array('user_name'=>$user_name))->find();
		if(empty($result)){
			return true;
		}else{
			return '用户名已被使用';
		}
	}
	public function getUserList($map,$page,$rows){
		$map['deleted'] = 0;
		// $list = $this->alias('admin')->join('LEFT JOIN wt_role ON admin.roleid=wt_role.role_id')->where($map)->order('admin.userid desc')->page($page,$rows)->select();
		
		$list = $this->alias('admin')->field(array(
         'admin.wbid'=>'wbid',
         'info.WbAccount'=>'WbAccount',
         'admin.role_id'=>'role_id',
         'info.WbName'=>'WbName',
         'info.WBTel'=>'WBTel',
         'admin.login_ip'=>'login_ip',
         'admin.login_time'=>'login_time',
         'admin.login_counts'=>'login_counts'
			))
		->join('left join WB_info  info on info.WBID= admin.wbid ')->where($map)->select();

		foreach ($list as &$val) 
		{
			$val['role_name']=D('role')->where(array('role_id'=>$val['role_id']))->getField('role_name');
		}



		// $list = $this->alias('admin')->where($map)->page($page,$rows)->select();
		$count = $this->alias('admin')->where($map)->count();
		return array('list'=>$list,'count'=>$count);
	}
	public function insertUser($data){
		$data['user_pass'] = md5($data['user_pass'].'hc');
		if($this->create($data)){
			if($this->add()){
				return true;
			}else{
				return false;
			}
		}else{
			return $this->getError();
		}
	}
	public function deleteUser($userid){
		$this->where(array('wbid'=>$wbid))->data(array('deleted'=>1))->save();
	}
	public function getUserInfo($wbid)
	{

		$map['admin.wbid'] =$wbid;
		$map['admin.deleted']=0;

		// return $this->where(array('wbid'=>$wbid))->find();


		 $list = $this->alias('admin')->field(array(
         'admin.wbid'=>'wbid',
         'info.WbAccount'=>'WbAccount',
         'admin.role_id'=>'role_id',
         'info.WbName'=>'WbName',
         'info.WBTel'=>'WBTel'
 
			))
		->join('left join WB_info  info on info.WBID= admin.wbid ')->where($map)->find();

		return $list;


	}
	public function updateUserInfo($data)
	{
		if($this->create($data)){
			if($this->save() !== false){
				return true;
			}else{
				return false;
			}
		}else{
			return $this->getError();
		}
	}
	public function getRealNameByAgentId($agent_id){
		if($agent_id == 0){
			return '000000';
		}else{
			$name = $this->cache(true)->where(array('agent_id'=>$agent_id))->getField('realname');
			if(!empty($name)){
				return $name;
			}else{
				return 'unknown';
			}
		}
	}
	public function getRealNameByUserId($userid){
		$name = $this->cache(true)->where(array('userid'=>$userid))->getField('realname');
		if(!empty($name))
		{
			return $name;
		}
		else
		{
			return 'unknown';
		}
	}
	
	public function getAgentIdByUserId($userid)
	{
		$agent_id = $this->cache(true)->where(array('userid'=>$userid))->getField('agent_id');
		return empty($agent_id)?0:$agent_id;
	}
	/*
	 *	根据用户ID获得代理商信息
	 *	@param (int)user_id 用户ID
	 *
	 *	@return array
	 */
	public function getAgentInfoByUserId($userid){
		return $this->cache(true)->join('LEFT JOIN wt_agent ON wt_admin.agent_id=wt_agent.agent_id')->where(array('wt_admin.userid'=>$userid))->find();
	}

	public function setLoginInfo($userid)
	{
		$data=array();
		$data['login_ip'] = get_client_ip();
		$data['login_time'] = date('Y-m-d H:i:s');
		$this->where(array('userid'=>$userid))->data($data)->save();
		$this->where(array('userid'=>$userid))->setInc('login_counts');
	}

	public function updateUserInfoByUserId($userid,$data){
		if($this->create($data)){
			if($this->where(array('userid'=>$userid))->save() !== false){
				return true;
			}else{
				return false;
			}
		}else{
			return $this->getError();
		}
	}
	public function chkUserPass($user_pass)
	{
		$user_name=session('username');

		$result = $this->where(array('user_name'=>$user_name,'user_pass'=>md5($user_pass.'hc')))->find();

		if(!empty($result))
		{
			return true;
		}else
		{
			return '原密码错误';
		}
	}
	public function chkPass($username,$password)
	{
		$user = $this->where(array('user_name'=>$username,'deleted'=>0))->find(); //首先查询用户名是否存在
   
		if(!empty($user) && $user['user_pass'] == md5($password.'hc')) //存在则判断密码是否正确
		{
			return $user;//用户名正确则返回userid,roleid,realname这三个字段
		}
		else
		{   
			//echo "登陆失败";
			return false;
		}
	}

	
}