<?php
namespace Home\Model;
use Think\model;
class Computerlist1Model extends Model
{
	protected $tableName = 'WComputerList';

    public function getComputerList($map = array())
    {
        return $this->where($map)->select();
    }

    public function updateComputerList($map,$data)
    {
        return $this->where($map)->data($data)->save();
    }

    public function deleteComputerList($map,$data)
    {
        return $this->where($map)->data($data)->delete();
    }
}