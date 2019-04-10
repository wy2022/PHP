<?php
namespace Home\Model;
use Think\Model;
class RoleModel extends Model{
	protected $tableName = 'role';
	// protected $fields = array(
	// 	'role_id','role_name','role_perm','ip_whitelist','_type'=>array(
	// 		'role_id'	=>	'tinyint',
	// 		'role_name'	=>	'varchar',
	// 		'role_perm'		=>	'varchar',
	// 		'ip_whitelist'	=>	'varchar'
	// 		)
	// 	);

	protected $pk = 'role_id';

	public function getRoleList()
	{
		return $this->select();
	}
	public function getRoleDetail($role_id)
	{
		return $this->where(array('role_id'=>$role_id))->find();
	}
	public function addRole($data)
	{
		return $this->data($data)->add();
	}
	public function updateRole($data)
	{
		return $this->data($data)->save();
	}

	public function verifyIp($role_id,$ip)//登陆人员的roleid和客户端ip
	{
		$whitelist = $this->where(array('role_id'=>$role_id))->getField('ip_whitelist');
		
		$whitelist = explode(',', $whitelist);
		if(in_array('*',$whitelist))
		{
			return true;
		}
		array_push($whitelist,'127.0.0.1');
		if(in_array($ip, $whitelist))
		{
			return true;
		}
		return false;
	}

	    public function getGroupUserListByMap($map=array())
		{
		   $list  = $this->where($map)->select();

		   foreach($list  as &$val)
		   {
               $val['usernum_ght']=D('Yuangong')->where(array('WB_ID'=>session('wbid'),'role_id'=>$val['role_id']))->count();
		   }	


		   return $list;
		}

	    public function updateGroupUserByid($id,$data=array())
		{
		
				if($this->where(array('role_id'=>$id,'WB_ID'=>session('wbid')))->data($data)->save())
				{
					return true;
				}
				else
				{
					return null;
				}
		}	


}