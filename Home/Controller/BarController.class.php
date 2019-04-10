<?php
namespace Home\Controller;
class BarController extends CommonController
{
	//用户列表
	public function index()
	{  
        //查询所有会员类型数量	
		$map=array();
		$map['WB_ID']=session('wbid');
		$map['Name']=array('neq','临时卡');		      
        $hylxlist=D('Hylx')->where($map)->select();	   		
		foreach($hylxlist as &$val)
		{
			$val['count']=D('HyInfo')->where(array('WB_ID'=>session('wbid'),'hyCardGuid'=>$val['Guid']))->count();
		}					
		// 获取操作员列表 
		$map=array();
		$map['WB_ID']=session('wbid');
        $operatorlist=D('Yuangong')->where($map)->select();		
		$this->assign('operatorlist',$operatorlist);					
		//获取会员等级列表
			
		//查询剩余金额总额
		//查询奖励金额总额
		
		$sum_surplus=D('HyInfo')->where(array('WB_ID'=>session('wbid')))->sum('surplus');	
		$sum_jlje=D('HyInfo')->where(array('WB_ID'=>session('wbid')))->sum('Jlje');
		
		$this->assign('hylxlist',$hylxlist);
		$firstflag=1;
		$this->assign('firstflag',$firstflag);
		
		$this->assign('hylxlist_str',json_encode($hylxlist));
		$this->assign('sum_surplus',sprintf("%.2f",$sum_surplus));
		$this->assign('sum_jlje',sprintf("%.2f",$sum_jlje));

		$this->display();
	}


	public function getHyinfoList()
    { 
      if(IS_AJAX)
      {
        $page = I('get.page',1,'int');
        $rows = I('get.rows',20,'int');
        $sord = I('get.sord','','string')?:'desc';
        $sidx = I('get.sidx','','string')?:'LastSjTime';

        $sContent      = I('get.sContent','','string');
        $daterange     = I('get.daterange','','string');
        $sName         = I('get.sName','','string');
				
		$hytype         = I('get.hytype','','string');
		$operator         = I('get.operator','','string');		
  	    $map = array();
        $map['WB_ID']=session('wbid');
	
        if(!empty($operator))
        {			
          $map['operation']=$operator;
        } 


        if(!empty($hytype))
        {
          $map['hyCardGuid']=$hytype;
        }  
				
        if(!empty($sContent))
        {
          $map['hyCardNo|zjNo']=array('LIKE','%'.$sContent.'%');
        }  

        if(!empty($sName))
        {
          $map['hyname']=array('LIKE','%'.$sName.'%');
        }  
        if(!empty($daterange))  
        {
          list($start,$end) = explode(' - ',$daterange);    
          $start = str_replace('/','-',$start);            
          $end = str_replace('/','-',$end);                
          $map['NewTime'] = array('BETWEEN',array($start,$end));
        }
        
        $count= D('Bar')->getHyListByMap_Count($map);
        $sql_page=ceil($count/$rows);  
        if($page>$sql_page) $page=1;


        $wblist = D('Bar')->getHyListByMap($map,"$sidx $sord",$page,$rows);
        

        

        $response = new \stdClass();
        $response->records = $wblist['count'];
		
		$response->hylxlist = $wblist['hylxlist'];
		$response->benjinje = $wblist['benjinje'];
		$response->jlje = $wblist['jlje'];
		
        $response->page = $page;
        $response->total = ceil($wblist['count'] / $rows);
        foreach($wblist['list'] as $key => $value)
        {       
          $response->rows[$key]['id'] = $key;
          $response->rows[$key]['cell'] = $value;
        }
        $this->ajaxReturn($response);
      }             
    }




    public function vipcard_detail()
    {
        $wbid= session('wbid');
        $zjNo= I('get.zjNo','','string');

        $hyinfo=D('HyInfo')->where(array('hyCardNo'=>$zjNo,'WB_ID'=>$wbid))->find();
        $hyinfo['surplus'] =sprintf("%.2f", $hyinfo['surplus']);
        $hyinfo['Jlje'] =sprintf("%.2f", $hyinfo['Jlje']);

        $hyinfo['NewTime']=date('Y-m-d H:i:s',strtotime($hyinfo['NewTime']));
        $hyinfo['LastSjTime']=date('Y-m-d H:i:s',strtotime($hyinfo['LastSjTime']));
        $hyinfo['CancelTime']=date('Y-m-d H:i:s',strtotime($hyinfo['CancelTime']));
		$hyinfo['hylx']=D('Hylx')->where(array('WB_ID'=>session('wbid'),'Guid'=>$hyinfo['hyCardGuid']))->getField('Name'); 
		
	    if(empty($hyinfo['Tel']))
		{
			$hyinfo['Tel']='未知';
		}	
		
	    if(empty($hyinfo['bz']))
		{
			$hyinfo['bz']='未知';
		}	
		
        $this->assign('zjNo',$zjNo);
        $this->assign('hyinfo',$hyinfo);
        session('hy_zjNo',$zjNo);
        $this->display();
    }

    public function getHysaledetail()
    { 
      if(IS_AJAX)
      {
        $page = I('get.page',1,'int');
        $rows = I('get.rows',20,'int');
        $sord = I('get.sord','','string')?:'desc';
		$sidx = I('get.sidx','','string')?:'id';
              
        $daterange = I('get.daterange','','string');

        $map = array();
        $map['WB_ID']=session('wbid');
        $map['cardNo']=session('hy_zjNo');

        if(!empty($daterange))  
        {
            list($start,$end) = explode(' - ',$daterange);    
            $start = str_replace('/','-',$start);           
            $end = str_replace('/','-',$end);               
            $map['SjTime'] = array('BETWEEN',array($start,$end));
        }
            
        $count= D('Hyxfmx')->getHyxfmxListByMap_Count($map);
        $sql_page=ceil($count/$rows);  
        if($page>$sql_page) $page=1;


        $wblist = D('Hyxfmx')->getHyxfmxlistByMap($map,"$sidx $sord",$page,$rows);
		foreach ($wblist['list'] as &$val)
		{                             
		   
		  $syname = D('SpCtrlIp')->where(array('Wb_id'=>session('wbid'),'Syid'=>$val['SyId']))->getField('syname');  
		  if($syname !='')
		  {
			$val['SyId']= $syname;
		  }            
		}
        
        $response = new \stdClass();
        $response->records = $wblist['count'];
        $response->page = $page;
        $response->total = ceil($wblist['count'] / $rows);
        foreach($wblist['list'] as $key => $value)
        {       
          $response->rows[$key]['id'] = $key;
          $response->rows[$key]['cell'] = $value;
        }
        $this->ajaxReturn($response);
      }             
    }
	
	public function getHyaddmoneydetail()
    { 
      if(IS_AJAX)
      {
        $page = I('get.page',1,'int');
        $rows = I('get.rows',20,'int');
        $sord = I('get.sord','','string')?:'desc';
        $sidx = I('get.sidx','','string')?:'ctime';

        $daterange = I('get.daterange','','string');


        $map = array();
        $map['addmoney.wb_id']=session('wbid');
        $map['addmoney.cardno']=session('hy_zjNo');


        if(!empty($daterange))  
        {
            list($start,$end) = explode(' - ',$daterange);  
            $start = str_replace('/','-',$start);            
            $end = str_replace('/','-',$end);                
            $map['cTime'] = array('BETWEEN',array($start,$end));
        }

        $count= D('Hyaddmoneymxview')->getHyaddmoneymxListByMap_view_Count($map);

        $sql_page=ceil($count/$rows);  
        if($page>$sql_page) $page=1;

        $wblist = D('Hyaddmoneymxview')->getHyaddmoneymxListByMap_view($map,"$sidx $sord",$page,$rows);			
		foreach ($wblist['list'] as &$val)
		{                             
		   
		  $syname = D('SpCtrlIp')->where(array('Wb_id'=>session('wbid'),'Syid'=>$val['SyId']))->getField('syname');  
		  if($syname !='')
		  {
			$val['SyId']= $syname;
		  }            
		}
		     
        $response = new \stdClass();
        $response->records = $wblist['count'];
        $response->page = $page;
        $response->total = ceil($wblist['count'] / $rows);
        foreach($wblist['list'] as $key => $value)
        {       
          $response->rows[$key]['id'] = $key;
          $response->rows[$key]['cell'] = $value;
        }
        $this->ajaxReturn($response);
      }             
    }

     public function log()
     {
        $this->display();
     }



    public function getclientlog()
    { 
      if(IS_AJAX)
      {
        $page = I('get.page',1,'int');
        $rows = I('get.rows',20,'int');
        $sord = I('get.sord','','string')?:'desc';
        $sidx = I('get.sidx','','string')?:'cTime';

        $sContent      = I('get.sContent','','string');
        $daterange     = I('get.daterange','','string');
   
        $map = array();
        $map['WB_ID']=session('wbid');

        if(!empty($sContent))
        {
          $map['Nr']=array('LIKE','%'.$sContent.'%');
        }  
        if(!empty($daterange))  
        {
          list($start,$end) = explode(' - ',$daterange);    
          $start = str_replace('/','-',$start);            
          $end = str_replace('/','-',$end);                
          $map['cTime'] = array('BETWEEN',array($start,$end));
        }
        
        $count= D('Clientlog')->getClientlogList_count($map);
        $sql_page=ceil($count/$rows);  
        if($page>$sql_page) $page=1;


        $wblist = D('Clientlog')->getClientlogList($map,$page,$rows);
           
        $response = new \stdClass();
        $response->records = $wblist['count'];
        $response->page = $page;
        $response->total = ceil($wblist['count'] / $rows);
        foreach($wblist['list'] as $key => $value)
        {       
          $response->rows[$key]['id'] = $key;
          $response->rows[$key]['cell'] = $value;
        }
        $this->ajaxReturn($response);
      }             
    }



    



}
