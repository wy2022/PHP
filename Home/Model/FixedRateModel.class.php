<?php
namespace Home\Model;
use Think\Model;
class FixedRateModel extends Model{
    protected $tableName = 'WDeFl';

    public function getFixedRate($map = array())
	{
	
        return $this->where($map)->select();
    }

    public function getFreeRate($map = array())
    {
        return $this->where($map)->select();
    }

    public function addFixedRate($data)
    {
        return $this->data($data)->add();
    }

    public function updateFixedRateByGuid($Guid,$data)
    {
        return $this->where(array('WB_ID'=>session('wbid'),'Guid'=>$Guid))->data($data)->save();
    }

    public function deleteFixedRateByGuid($Guid)
    {
        return $this->where(array('WB_ID'=>session('wbid'),'Guid'=>$Guid))->delete();
    }

    public function addFreeRateByGuid($districtGuid)
    {
        $data['GroupGuid']=$districtGuid;
        $data['Guid']     =getGuid();
        $data['name']     ='自由计费';
        $data['TimeSize'] =0;
        $data['je']       =0;
        $data['WB_ID']    =session('wbid');
        return $this->data($data)->add();
    }

    public function deleteFreeRate($districtGuid)
    {
        return $this->where(array('WB_ID'=>session('wbid'),'GroupGuid'=>$districtGuid,'name'=>'自由计费'))->delete();
    }
}