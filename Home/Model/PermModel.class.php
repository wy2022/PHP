<?php
namespace Home\Model;
use Think\Model;
class PermModel extends Model{
	protected $tableName = 'perm';	//数据表名
	protected $fields = array(
		'perm_id','perm_name','perm_value','perm_current','parent_id','is_show','sort','_type'=>array(
			'perm_id'		=>	'smallint',
			'perm_name'		=>	'varchar',
			'perm_value'	=>	'varchar',
			'perm_current'	=>	'varchar',
			'parent_id'		=>	'smallint',
			'is_show'		=>	'tinyint',
			'sort'			=>	'smallint'
			)
		);
	protected $pk = 'perm_id';

	/*
	 *	验证权限
	 *	@param current	当前操作
	 *
	 *	@return boolean
	 */
	public function chkPerm($current)
	{   
	    //用session('roleid')先判断当前登录的角色属于代理商还是商务代表，并返回可操作的内容role_perm列表,内容为 105,116,118,119,106,108,107,122,123
		$role_id=session('roleid');
		if($role_id=='999999')
		{
          $perm_id = explode(',',D('WbInfo')->cache(true,600)->where(array('WBID'=>session('wbid')))->getField('role_perm'));		
		}
		else
		{
			$perm_id = explode(',',D('Role')->cache(true,600)->where(array('role_id'=>session('roleid')))->getField('role_perm'));
		}	
		
		
		$map['perm_id'] = array('IN',$perm_id);
		$map['is_show'] = 1;
		//获取所有perm_id在  105,116,118,119,106,108,107,122,123里,而且可显示的数据
        //返回一组perm_current列表，均为字段名 字典bar_reg,card_index,agent_index,user_role
		$perm = $this->cache(true,600)->where($map)->getField('perm_current',true);

		if(in_array($current,$perm))   //判断传进来的字段 $current在列表$perm里
		{
			return true;
		}else{
			return false;
		}
	}

	/*
	 *	通过perm得到其父perm的id
	 *	@param perm
	 *
	 *	@return int || boolean
	 */
	public function getParentPermId($perm){
		$parent_id = $this->where(array('perm_current'=>$perm))->getField('parent_id');
		return empty($parent_id) ? false : $parent_id;
	}

	/*
	 *	通过roleid得到该角色权限下的perm列表
	 *	@param roleid
	 *
	 *	@return array
	 */
	public function getPermByRoleid()
	{
		$qx=session('qx');

		$perm_id = explode(',',D('WbInfo')->where('WBID='.session('wbid'))->getField('role_perm'));  
			
		//$perm_id = explode(',',D('Role')->cache(true,600)->where('role_id='.session('roleid'))->getField('role_perm'));  
        
        
		if($qx==1)//员工登录
		{
			$perm_id = explode(',',D('Role')->where(array('WB_ID'=>session('wbid'),'role_id'=>session('roleid')))->getField('role_perm')); 
		}	
		

		
		$map['perm_id'] = array('IN',$perm_id);
		$map['is_show'] = 1;
		$perm = $this->cache(true,600)->field('perm_id,perm_name,perm_value,perm_current,sort,parent_id,is_show')->where($map)->order('sort')->select();
		return $perm;
	}
	public function getPermList(){
		return $this->order('sort')->select();
	}
	public function getPermDetail($id){
		return $this->where(array('perm_id'=>$id))->find();
	}
	public function insertPerm($data){
		return $this->data($data)->add();
	}
	public function updatePerm($data){
		return $this->data($data)->save();
	}
	public function deletePerm($id){
		$this->delete($id);
	}
	public function getFormatPermList($current_id=0){
		$perm_tree = list_to_tree($this->getPermList(),0,'perm_id','parent_id');
		return $this->formatPermToSelect($perm_tree,$current_id);
	}
	protected function formatPermToSelect($tree,$id,$str=0){
		foreach($tree as $key=>$value){
			if($value['perm_id'] == $id){
				continue;
			}
			if($str==0){
				$result[] = array('perm_id'=>$value['perm_id'],'perm_name'=>$value['perm_name']);
			}elseif($str==1){
				$result[] = array('perm_id'=>$value['perm_id'],'perm_name'=>'&nbsp;|--'.$value['perm_name']);
			}elseif($str==2){
				$result[] = array('perm_id'=>$value['perm_id'],'perm_name'=>'&nbsp;&nbsp;|--'.$value['perm_name']);
			}
			if(!empty($value['_'])){
				$result = array_merge($result,$this->formatPermToSelect($value['_'],$id,$str+1));
			}
		}
		return $result;
	}
}