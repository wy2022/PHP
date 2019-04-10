<?php
namespace Home\Model;
use Think\Model;
class AwardPlanModel extends Model{
    protected $tableName = 'WHyLxTable_JLjh';

    public function getAwardPlan($map = array())
    {
        $AwardPlanList= $this->where($map)->select();
		 return $AwardPlanList;
    }

    public function addAwardPlan($data)
    {
        return $this->data($data)->add();
    }

    public function updateAwardPlanByGuid($Guid,$data)
    {
        return $this->where(array('WB_ID'=>session('wbid'),'Guid'=>$Guid))->data($data)->save();
    }
    
    public function deleteAwardPlanByGuid($Guid)
    {
        return $this->where(array('WB_ID'=>session('wbid'),'Guid'=>$Guid))->delete();
    }


}