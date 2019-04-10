<?php
namespace Home\Model;
use Think\Model;
class CreditsModel extends Model{
    protected $tableName = 'WIntegral_JlTable';

     public function getCredis($vipLevelId,$wbid)
	  {
        $vipLevelGuid = D('VipLevel')->getVipLevelGuidById($vipLevelId);		
		$creditslist=$this->where(array('HyLxGuid'=>$vipLevelGuid,'Wb_id'=>session('wbid')))->select();
	
		$j=0;
		foreach($creditslist as &$val)
		{
			$list['list'][$j]['Integral']=$val['Integral'];
			$list['list'][$j]['Je']=$val['Je'];
			$list['list'][$j]['Lx']=$val['Lx'];
			$list['list'][$j]['SpName']=$val['SpName'];
			$list['list'][$j]['id']=$val['id'];
			$j++;
		}
		
		$list['count']=$j;
		$list['result']=1;
			
		return $list;
		
    }
	public function deleteVipLevel($viplevelGuid)
	{
        return $this->where(array('Wb_id'=>session('wbid'),'HyLxGuid'=>$viplevelGuid))->delete();
    }

}