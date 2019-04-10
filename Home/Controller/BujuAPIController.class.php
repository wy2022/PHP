<?php
namespace Home\Controller;
use Think\Controller;
class BujuAPIController extends Controller
{


	
	
	public  function API_getOneHyInfo()
	{	
        $hyname=I('get.hyname','','string');
		$hycardno=I('get.hycardno','','string');
		
		
		$map['hyCardNo']=$hycardno;
		$map['hyname'] = $hyname;
		 		
		$hylist=D('HyInfo')->Field('hyname,hyCardNo,WB_ID,zjNo,surplus,Jlje')->where($map)->select();
        if(!empty($hylist))
		{
			foreach($hylist as &$val)
			{
				$val['wbname']=D('WbInfo')->where(array('WBID'=>$val['WB_ID']))->getField('WbName');
			}
			
			$data['status']=1;
			$data['response']=$hylist;
		}else 
        {
			$data['status']=-1;
		}			
			
		$this->ajaxReturn($data);
									 				  
	}
	
	
  public function API_getOneBarMoneyInfo()
   {
        $wbid     = I('get.wbid',0,'int'); 
        $hycardno = I('get.hycardno','','string'); 
        $je       = I('get.je','','string'); 
		
		if(empty($wbid) ||empty($hycardno) empty($je))
		{		
			$data['status']=-1;
		}
		else
        {
			$response=D('HyJl')->getMoneyRecordsbyWbIdAndHyCardNoAndJe($wbid,$hycardno,$je);
			$data['status']=1;
			$data['response']=$response;
		}			    
      $this->ajaxReturn($data);
   }
	
	
	
	
	
	
		  
}