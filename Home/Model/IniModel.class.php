<?php
namespace Home\Model;
use Think\Model;
class IniModel extends Model
{
	protected $tableName = 'wt_ini';

	public function getIniDataByKey($key)
	{
	    return $this->field('sValue')->where(array('sKey'=>$key))->select();
	}	
}