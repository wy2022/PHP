<?php
namespace Home\Model;
use Think\Model;
class VipLevelModel extends Model{
    protected $tableName = 'WHyLxTable';

    public function getVipLevelList($map)
    {     
        return $this->where($map)->select(); 
    }

    public function getVipLevel($map = array())
    {
        return $this->where($map)->find();
    }

    public function getVipLevelById($id)
    {
        return $this->getVipLevel(array('id'=>$id));
    }

    public function getVipLevelByGuid($guid)
    {
        return $this->getVipLevel(array('Guid'=>$guid));
    }

    public function getVipLevelGuidById($id)
    {
        return $this->where(array('id'=>$id))->getField('Guid');
    }

    public function getAccessVipLevel($vipLevelGuidString)
    {
        $vipLevelGuidString = rtrim($vipLevelGuidString,',');
        $vipLevelGuids = explode(',',$vipLevelGuidString);
        $vipLevelIds = array();

        foreach($vipLevelGuids as $vipLevelGuid)
        {
            // $vipLevel = $this->getVipLevelByGuid($vipLevelGuid);
            $vipLevel_id = $this->where(array('WB_ID'=>session('wbid'),'Guid'=>$vipLevelGuid))->getField('id');
          
            if(!empty($vipLevel_id))
            {
                $vipLevelIds[] = $vipLevel_id;
            }
        }
        return $vipLevelIds;
    }
    public function updateVipLevelByGuid($Guid,$wbid,$data)
    { 
        return $this->where(array('Guid'=>$Guid,'WB_ID'=>$wbid))->data($data)->save();    
    }

    public function addVipLevel($data)
    {
        $bExist=$this->where(array('WB_ID'=>session('wbid'),'Name'=>$data['Name']))->find();
        if(!empty($bExist))
        {
           return false;
        }
        else
        {
          return $this->data($data)->add();  
        }   

        
    }
	public function deleteViplevelByGuid($Guid)
    {
        return $this->where(array('WB_ID'=>session('wbid'),'Guid'=>$Guid))->delete();
    }
	
}