<?php
namespace Home\Model;
use Think\Model;
class AreaModel extends Model{
	protected $tableName = 'wt_area';
	protected $fields = array(
		'id','area_name','post_code','area_code','areacode_source','parent_id','level_deep','sort','pinyin','_type'=>array(
			'id'				=>	'smallint',
			'area_name'			=>	'varchar',
			'post_code'			=>	'varchar',
			'area_code'			=>	'smallint',
			'areacode_source'	=>	'mediumint',
			'parent_id'			=>	'smallint',
			'level_deep'		=>	'tinyint',
			'sort'				=>	'tinyint',
			'pinyin'			=>	'varchar'
			)
		);
	/*
	 *	根据上级地区ID获得地区列表
	 *	@param (int)pid 上级地区ID
	 *
	 *	@return array
	 */
	public function getAreaList($pid = 0,$field = 'id,area_name')
	{
		$roleid = session('roleid');
		if($roleid == 3)
		{
			//代理商根据代理区域选择
			$agent_info = D('User')->getAgentInfoByUserId(session('userid'));
			if($pid == 0)
			{
				//只返回代理的省
				return array(
					array(
						'id'		=>	$agent_info['province'],
						'area_name'	=>	$this->getAreaNameById($agent_info['province'])
					)
				);
			}
			else
			{
				$area_type = $this->getAreaTypeById($pid);
				if($area_type == 'province')
				{
					//如果查询的是省
					if($pid == $agent_info['province'])
					{
						//如果查询的省与代理省相同
						if(!empty($agent_info['city']))
						{
							//如果代理商代理到市级，则只显示所代理的市
							return array(array('id'=>$agent_info['city'],'area_name'=>$this->getAreaNameById($agent_info['city'])));
						}
					}
					else
					{
						//如果查询的省与代理不同，返回null
						return null;
					}
				}
				elseif($area_type == 'city')
				{
					//如果查询的是市
					$area_info = $this->getAreaInfoById($pid);
					if($agent_info['province'] == $area_info['parent_id'])
					{
						//如果查询的市在所代理的省份下
						if(!empty($agent_info['city'])){
							//如果代理到市级
							if($pid == $agent_info['city'])
							{
								//如果查询的是所代理的市
								if(!empty($agent_info['area']))
								{
									//如果代理商代理到区县级，则只显示所代理的区县
									return array(array('id'=>$agent_info['area'],'area_name'=>$this->getAreaNameById($agent_info['area']))); 
								}
							}
							else
							{
								//如果查询的不是所代理的市，返回null
								return null;
							}
						}
					}
					else
					{
						//如果查询的市不在所代理的省份下，返回null
						return null;
					}
				}
			}
		}elseif($roleid == 6)
		{
			if($pid == 0)
			{
				return array(
					array(
						'id'		=>	10,
						'area_name'	=>	'河南'
					)
				);
			}elseif($pid == 10)
			{
				return array(
					array(
						'id'		=>	749,
						'area_name'	=>	'郑州'
					)
				);
			}
		}
		return $this->cache(true)->field($field)->where(array('parent_id'=>$pid))->order('id')->select();
	}



	public function getAreaList2($parent_id)
	{
       return $this->field(array('id'=>'aid','area_name'=>'name'))->where(array('parent_id'=>$parent_id))->select();
	}


	public function getOneAreaIdAndName($id)
	{
       return $this->field(array('id,area_name'))->where(array('id'=>$id))->select();
	}

	public function getDistrictList($parent_id)
	{
       return $this->field(array('id'=>'id','area_name'=>'area_name'))->where(array('parent_id'=>$parent_id))->select();
	}
	/*
	 *	根据上级地区ID获得地区详细列表
	 *	用于SysController
	 *	@param (int)pid 上级地区ID
	 *
	 *	@return array
	 */
	public function getAreaDetailList($pid = 0)
	{
		return $this->getAreaList($pid,'id,area_name,area_code,areacode_source,pinyin');
	}
	/*
	 *	根据地区ID获得地区名称
	 *	@param (int)id 地区ID
	 *
	 *	@return string
	 */
	public function getAreaNameById($id){
		if(empty($id)){
			return '';
		}
		$area_name = $this->cache(true)->where(array('id'=>$id))->getField('area_name');
		return empty($area_name)?'':$area_name;
	}
	public function getAreaIdByName($area_name){
		return $this->cache(true)->where(array('area_name'=>$area_name))->getField('id');
	}
	public function getAreaInfoById($id){
		return $this->cache(true)->where(array('id'=>$id))->find();
	}
	/*
	 *	新增地区
	 *	@param (array)data 新增地区信息
	 *
	 *	@return boolean|string
	 */
	public function insertArea($data){
		if($this->create($data)){
			if($this->add() !== false){
				return true;
			}else{
				return false;
			}
		}else{
			$this->getError();
		}
	}
	/*
	 *	更新地区信息
	 *	@param (array)data 更新信息（必须有主键ID，否则无法更新）
	 *
	 *	@return boolean|string
	 */
	public function updateAreaInfo($data){
		if($this->create($data)){
			if($this->save() !== false){
				return true;
			}else{
				return false;
			}
		}else{
			$this->getError();
		}
	}
	/*
	 *	根据地区ID删除地区
	 *	@param (int)id
	 *
	 *	@return boolean
	 */
	public function deleteArea($id){
		return $this->delete($id)?true:false;
	}
	/*
	 *	根据父ID得到第一个子地区ID
	 *	@param (int)pid
	 *
	 *	@return int
	 */
	public function getFirstChildAreaId($pid){
		return $this->cache(true)->where(array('parent_id'=>$pid))->getField('id');
	}
	public function getAreaTypeById($id){
		$area_info = $this->getAreaInfoById($id);
		$level_deep = $area_info['level_deep'];
		switch($level_deep){
			case 1:
				$type = 'province';
				break;
			case 2:
				$type = 'city';
				break;
			case 3:
				$type = 'area';
				break;
			default:
				$type = null;
		}
		return $type;
	}
}