<?php
namespace Home\Controller;
use Think\Controller;
class DailiAPIController extends Controller 
{   
   
	// public  function API_querydaili_info()
	// {	
	
		// $username=I('get.username','','string');		 
	
		// $barstr=D('Agent')->where(array('agent_name'=>$username))->getField('wblist');
		// $bar_array=explode(',',$barstr);   

        // //总网吧数量		
        // $barcount=count($bar_array);
		// print_r($barcount);
		// //在线网吧数量
		// $map=array();
		// $map['WBID']=array('in',$barstr);
		// $map['WH_Status']=array('gt',0);
		// $onlinecount=D('WbInfo')->where($map)->count();
		
			  
	    // //即将到期的网吧数量
	    // $map=array();
		// $map['WBID']=array('in',$barstr);
		// $nowtime=  date('Y-m-d H:i:s', time()) ;
        // $nowtime= strtotime($nowtime);
        // $tgbz_pp_beg_time = strtotime('+30 days',$nowtime);
        // $tgbz_pp_beg_time = date('Y-m-d H:i:s', $tgbz_pp_beg_time) ; 
        // $map['EndTime'] = array('elt',$tgbz_pp_beg_time);	
		// $endcount=D('WbInfo')->where($map)->count();
			
		
		// $data=array();
		// $data['result']=1;
		// $data['onlinecount']   = $onlinecount;
		// $data['endcount']      = $endcount;
		
		
		// //离线数量
		



        // //下级代理商 

		
		// echo  json_encode($data);
									 				  
	// }
	
	
	
	//添加代码开始
	public  function API_querydaili_info()
	{	
	
		$username=I('post.username','','string');	
        $password=I('post.password','','string');		
	    //本人网吧
		$agent_id=D('Agent')->where(array('agent_name'=>$username))->getField('agent_id');			
		$barstr=D('Agent')->where(array('agent_name'=>$username))->getField('wblist');
		
		$bar_array=explode(',',$barstr);   
        //总网吧数量		
        $sumbarcount=count($bar_array)-1;
		//在线网吧数量
		$map=array();
		$map['WBID']=array('in',$barstr);
		$map['WH_Status']=array('gt',0);
		$onlinecount=D('WbInfo')->where($map)->count();
		//离线网吧数量	
		$map=array();
		$map['WBID']=array('in',$barstr);
		$map['WH_Status']=0;
		$unonlinecount=D('WbInfo')->where($map)->count();
		
	   //本人即将到期到期网吧
	    $map=array();
		$map['WBID']=array('in',$barstr);
		$nowtime=  date('Y-m-d H:i:s', time()) ;
        $nowtime= strtotime($nowtime);
        $tgbz_pp_beg_time = strtotime('+30 days',$nowtime);
        $tgbz_pp_beg_time = date('Y-m-d H:i:s', $tgbz_pp_beg_time) ; 
        $map['EndTime'] = array('elt',$tgbz_pp_beg_time);	
		$endcount=D('WbInfo')->where($map)->count();
		
		//下级代理商个数
		$zi_agentnumber=D('Agent')->where(array('parent_id'=>$agent_id))->count();

		
		
		//下级代理商信息
		
		
		$map=array();
		$map['wblist']=array('neq','');
		$map['parent_id']=$agent_id;
		$zi_agentid_list=D('Agent')->where($map)->getField('agent_id',true);
		$zi_agentid_list=implode(",",$zi_agentid_list); //下级代理商 agent_id  一维数组
		
		
			
		$map=array();
		$map['agent_id']=array('in',$zi_agentid_list);
	    $zi_barstr=D('Agent')->where($map)->getField('wblist',true);
		$zi_barstr=implode(",",$zi_barstr);   //下级代理商 网吧列表 一维数组
		
		
		$bar_array1=explode(',',$zi_barstr);   
        //总网吧数量		
		
		$j=0;
		for($i=0;$i<count($bar_array1);$i++)
		{
			if($bar_array1[$i]!='')
			{
				$j++;
			}	
		}	
		$zi_sumbarcount=$j;

		
		
			
			
		
		$map=array();
		$map['WBID']=array('in',$zi_barstr);
		$map['WH_Status']=array('gt',0);
		$zi_onlinecount=D('WbInfo')->where($map)->count();
		
		
		$map=array();
		$map['WBID']=array('in',$zi_barstr);
		$map['WH_Status']=0;
		$zi_unonlinecount=D('WbInfo')->where($map)->count();
		
		
		$map=array();
		$map['WBID']=array('in',$zi_barstr);
		$age_wb=D('WbInfo')->where($map)->count();
		
		
		$map=array();
		$map['WBID']=array('in',$zi_barstr);
		$nowtime=  date('Y-m-d H:i:s', time()) ;
        $nowtime= strtotime($nowtime);
        $tgbz_pp_beg_time = strtotime('+30 days',$nowtime);
        $tgbz_pp_beg_time = date('Y-m-d H:i:s', $tgbz_pp_beg_time) ; 
        $map['EndTime'] = array('elt',$tgbz_pp_beg_time);	
		$zi_endcount=D('WbInfo')->where($map)->count();
			
		
		
		
		
		$data=array();
		$data['result']=1;
		
		$data['zi_endcount']   = $zi_endcount;
		$data['zi_onlinecount']   =$zi_onlinecount;
		$data['zi_unonlinecount']   = $zi_unonlinecount;
		$data['zi_sumbarcount']   = $zi_sumbarcount;
		$data['zi_agentnumber']   = $zi_agentnumber;
			
		
		$data['endcount']      = $endcount;
		$data['onlinecount']   = $onlinecount;	
		$data['unonlinecount'] = $unonlinecount;
		$data['sumwbcount']   = $sumbarcount;

        //下级代理商 
		
		echo  json_encode($data);
									 				  
	}
	
	

	
		
}
