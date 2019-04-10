<?php
/*
 *	表名：wt_agent
 *	该表为代理商信息表
 *
 *	@author 珈蓝陌（李强）
 *
 */
namespace Home\Model;
use Think\Model;

class AgentModel extends Model
{
	protected $tableName = 'wt_agent';


	public function getAgentListByCommonqx_count($map=array())
	{
        return $this->where($map)->count(); 
	}

	
	public function getAgentListByCommonqx($map=array(),$order = '',$page = 1,$rows = 20)
	{
        $count=$this->where($map)->count(); 
	    $list=$this->where($map)->page($page,$rows)->order($order)->select(); 
		
   	
		foreach($list as &$val)
        {
		    $val['dtInsertTime']= date('Y-m-d H:i:s',strtotime($val['dtInsertTime']));
			$val['province']= D('Area')->getAreaNameById($val['province']);
			$val['city']=   D('Area')->getAreaNameById($val['city']);
			$val['area']=   D('Area')->getAreaNameById($val['area']);
			$val['diqu']=  $val['province'].'-'.$val['city'].'-'.$val['area'];
			$val['wbid']=  session('wbid');
			// $agent_id=D('Bangding')->where(arraroley('wbid'=>$val['wbid']))->getField('agent_id');
			

			 $val['bing_status']= D('Bangding')->where(array('agent_id'=>$val['agent_id'],'wbid'=>session('wbid'),'deleted'=>0))->getField('bing_status');
			 if(empty($val['bing_status']))
			 {
				$val['bing_status']=0; 
			 }	 
				
		}	
		

		return array('count'=>$count,'list'=>$list); 
	}

}