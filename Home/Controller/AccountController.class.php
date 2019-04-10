<?php
namespace Home\Controller;
class AccountController extends CommonController
{
	 public function shift()
    {
        $jbr_list=D('Yuangong')->where(array('WB_ID'=>session('wbid')))->select();
        $this->assign('jbr_list',$jbr_list);       
        $this->display();
    }



    public function shift_detail()
    {
        $BeginTime = I('get.BeginTime','','string');
        $EndTime = I('get.EndTime','','string');
		$shift_id = I('get.id','','string');
		$BeginTime = date('Y-m-d H:i:s',$BeginTime);		
		$EndTime = date('Y-m-d H:i:s',$EndTime);
       

    
        session('shift_begintime', $BeginTime);
        session('shift_endtime', $EndTime);
		session('shift_id', $shift_id);
		
		$map=array();
		$map['cTime']=array('elt',$BeginTime);
		$map['WB_ID']=session('wbid');			
		$oneshiftinfo=D('Shift')->where($map)->order('cTime desc ')->limit(1)->find();
		$this->assign('keepje',$oneshiftinfo['keepje']);
		

        $this->display();
    }



   
  public  function  getshiftlist_hyaddmoney_detail()
  {
      if(IS_AJAX)
      {
        $page = I('get.page',1,'int');
        $rows = I('get.rows',20,'int');
        $sord = I('get.sord','','string')?:'desc';
        $sidx = I('get.sidx','','string')?:'cTime';
        
		$shift_begintime=session('shift_begintime');
		$shift_endtime=session('shift_endtime');
		  
        //网费
        $map=array();
        $map['WB_ID']=session('wbid');
        $map['cTime']=array('BETWEEN',array($shift_begintime,$shift_endtime));
        

        $count= D('Hyaddmoneymx')->getHyaddmoneymxListByMap_Count($map);
        $sql_page=ceil($count/$rows);  
        if($page>$sql_page) $page=1;


        $wblist = D('Hyaddmoneymx')->getHyaddmoneymxListByMap($map,"$sidx $sord",$page,$rows);


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



    public  function  getshiftlist_lskaddmoney_detail()
  {
      if(IS_AJAX)
      {
        $page = I('get.page',1,'int');
        $rows = I('get.rows',10,'int');
        $sord = I('get.sord','','string')?:'desc';
        $sidx = I('get.sidx','','string')?:'cTime';
		
    	$shift_begintime=session('shift_begintime');
		$shift_endtime=session('shift_endtime');		
        //网费
        $map=array();
        $map['WB_ID']=session('wbid');
        $map['cTime']=array('BETWEEN',array($shift_begintime,$shift_endtime));
        
        $count= D('Lskaddmoneymx')->getLskaddmoneymxListByMap_Count($map);
        $sql_page=ceil($count/$rows);  
        if($page>$sql_page) $page=1;

        $wblist = D('Lskaddmoneymx')->getLskaddmoneymxListByMap($map,"$sidx $sord",$page,$rows);

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

  
  
  public  function  get_lskaddmoney_detail_bytime()
  {
      if(IS_AJAX)
      {
        $page = I('get.page',1,'int');
        $rows = I('get.rows',10,'int');
        $sord = I('get.sord','','string')?:'desc';
        $sidx = I('get.sidx','','string')?:'ctime';
		$daterange = I('get.daterange1','','string');
		$cardno = I('get.cardno1','','string');
     
        //网费
        $map=array();
		
		if(!empty($daterange))  
        {
          list($start,$end) = explode(' - ',$daterange);    
          $start = str_replace('/','-',$start);            
          $end = str_replace('/','-',$end);                
          $map['ctime'] = array('BETWEEN',array($start,$end));
		  $summoney=getAddMoneyByTime($start,$end);
        }
		if(!empty($cardno))  
		{
			 $map['cardno'] = array('LIKE','%'.$cardno.'%');
		}
		
        $map['wb_id']=session('wbid');    
        $count= D('Lskaddmoneymxview')->getLskaddmoneymxListByMap_Count($map);
        $sql_page=ceil($count/$rows);  
        if($page>$sql_page) $page=1;

        $wblist = D('Lskaddmoneymxview')->getLskaddmoneymxListByMap($map,"$sidx $sord",$page,$rows);
		foreach ($wblist['list'] as &$val)
		{                             
		   
		  $syname = D('SpCtrlIp')->where(array('Wb_id'=>session('wbid'),'Syid'=>$val['SyId']))->getField('syname');  
		  if($syname !='')
		  {
			$val['SyId']= $syname;
		  }            
		}

       $shift_money['sum_hyk_jq_money']=$sum_hyk_jq_money;
	   $shift_money['sum_hyk_jl_money']=$sum_hyk_jl_money;
	   $shift_money['sum_lsk_jq_money']=$sum_lsk_jq_money;
	   $shift_money['sum_lsk_zlje']    =$sum_lsk_zlje;
	   
	   if(empty($summoney))
	   {
		  $summoney['sum_hyk_jq_money']='0.00'; 
		  $summoney['sum_hyk_jl_money']='0.00';
		  $summoney['sum_lsk_jq_money']='0.00';
		  $summoney['sum_lsk_zlje']='0.00';
	   }	   

           
        $response = new \stdClass();
        $response->records = $wblist['count'];
        $response->page = $page;
        $response->total = ceil($wblist['count'] / $rows);
	              
        $response->sum_hyk_jq_money=$summoney['sum_hyk_jq_money'];
		$response->sum_hyk_jl_money=$summoney['sum_hyk_jl_money'];
		$response->sum_lsk_jq_money=$summoney['sum_lsk_jq_money'];
	    $response->sum_lsk_zlje    =$summoney['sum_lsk_zlje'];
		
        foreach($wblist['list'] as $key => $value)
        {       
          $response->rows[$key]['id'] = $key;
          $response->rows[$key]['cell'] = $value;
        }
        $this->ajaxReturn($response);
      }             
  }
  
  public  function  get_lskzhaoling_detail_bytime()
  {
      if(IS_AJAX)
      {
        $page = I('get.page',1,'int');
        $rows = I('get.rows',30,'int');
        $sord = I('get.sord','','string')?:'desc';
        $sidx = I('get.sidx','','string')?:'cTime';
        $daterange = I('get.daterange2','','string');
		$cardno = I('get.cardno2','','string');
		
        //网费
        $map=array();
        $map['WB_ID']=session('wbid');
		
		if(!empty($cardno))  
		{
			 $map['cardNo'] = array('LIKE','%'.$cardno.'%');
		}		
				
		if(!empty($daterange))  
        {
          list($start,$end) = explode(' - ',$daterange);    
          $start = str_replace('/','-',$start);            
          $end = str_replace('/','-',$end);                
          $map['cTime'] = array('BETWEEN',array($start,$end));
		  $summoney=getAddMoneyByTime($start,$end);
        }
			          
        $count= D('Zhaoling')->getLskZhaolingmxListByMap_count($map);		
        $sql_page=ceil($count/$rows);  
        if($page>$sql_page) $page=1;

        $wblist = D('Zhaoling')->getLskZhaolingmxListByMap($map,"$sidx $sord",$page,$rows);		
		foreach ($wblist['list'] as &$val)
		{                             	   
		  $syname = D('SpCtrlIp')->where(array('Wb_id'=>session('wbid'),'Syid'=>$val['SyId']))->getField('syname');  
		  if($syname !='')
		  {
			$val['SyId']= $syname;
		  }            
		}
		

       $shift_money['sum_hyk_jq_money']=$sum_hyk_jq_money;
	   $shift_money['sum_hyk_jl_money']=$sum_hyk_jl_money;
	   $shift_money['sum_lsk_jq_money']=$sum_lsk_jq_money;
	   $shift_money['sum_lsk_zlje']    =$sum_lsk_zlje;
	   
	   if(empty($summoney))
	   {
		  $summoney['sum_hyk_jq_money']='0.00'; 
		  $summoney['sum_hyk_jl_money']='0.00';
		  $summoney['sum_lsk_jq_money']='0.00';
		  $summoney['sum_lsk_zlje']='0.00';
	   }	   
         
        $response = new \stdClass();
        $response->records = $wblist['count'];
        $response->page = $page;
        $response->total = ceil($wblist['count'] / $rows);
	              
        $response->sum_hyk_jq_money=$summoney['sum_hyk_jq_money'];
		$response->sum_hyk_jl_money=$summoney['sum_hyk_jl_money'];
		$response->sum_lsk_jq_money=$summoney['sum_lsk_jq_money'];
	    $response->sum_lsk_zlje    =$summoney['sum_lsk_zlje'];
		
        foreach($wblist['list'] as $key => $value)
        {       
          $response->rows[$key]['id'] = $key;
          $response->rows[$key]['cell'] = $value;
        }
        $this->ajaxReturn($response);
      }             
  }

  
  

    public  function  getshiftlist_lskzhaoling_detail()
  {
      if(IS_AJAX)
      {
        $page = I('get.page',1,'int');
        $rows = I('get.rows',30,'int');
        $sord = I('get.sord','','string')?:'desc';
        $sidx = I('get.sidx','','string')?:'cTime';

		$shift_begintime=session('shift_begintime');
		$shift_endtime=session('shift_endtime');
		     
        //网费
        $map=array();
        $map['WB_ID']=session('wbid');
        $map['cTime']=array('BETWEEN',array($shift_begintime,$shift_endtime));				     	        
        $count= D('Zhaoling')->getLskZhaolingmxListByMap_count($map);
		
        $sql_page=ceil($count/$rows);  
        if($page>$sql_page) $page=1;

        $wblist = D('Zhaoling')->getLskZhaolingmxListByMap($map,"$sidx $sord",$page,$rows);		
		foreach ($wblist['list'] as &$val)
		{                             
		   
		  $syname = D('SpCtrlIp')->where(array('Wb_id'=>session('wbid'),'Syid'=>$val['SyId']))->getField('syname');  
		  if($syname !='')
		  {
			$val['SyId']= $syname;
		  }            
		}
		

        $shift_money['sum_hyk_jq_money']=$sum_hyk_jq_money;
	   $shift_money['sum_hyk_jl_money']=$sum_hyk_jl_money;
	   $shift_money['sum_lsk_jq_money']=$sum_lsk_jq_money;
	   $shift_money['sum_lsk_zlje']    =$sum_lsk_zlje;
	   
	   if(empty($summoney))
	   {
		  $summoney['sum_hyk_jq_money']='0.00'; 
		  $summoney['sum_hyk_jl_money']='0.00';
		  $summoney['sum_lsk_jq_money']='0.00';
		  $summoney['sum_lsk_zlje']='0.00';
	   }	   

           
        $response = new \stdClass();
        $response->records = $wblist['count'];
        $response->page = $page;
        $response->total = ceil($wblist['count'] / $rows);
	              
        $response->sum_hyk_jq_money=$summoney['sum_hyk_jq_money'];
		$response->sum_hyk_jl_money=$summoney['sum_hyk_jl_money'];
		$response->sum_lsk_jq_money=$summoney['sum_lsk_jq_money'];
	    $response->sum_lsk_zlje    =$summoney['sum_lsk_zlje'];
        foreach($wblist['list'] as $key => $value)
        {       
          $response->rows[$key]['id'] = $key;
          $response->rows[$key]['cell'] = $value;
        }
        $this->ajaxReturn($response);
      }             
  }


    public function getshiftSpChangeinfo()
    {
        if(IS_AJAX){
        $page = I('get.page',1,'int');
            $rows = I('get.rows',20,'int');
            $sidx = I('get.sidx','','string')?:'cTime';
            $sord = I('get.sord','','string')?:'desc';
			
					
		   $shift_begintime=session('shift_begintime');
		   $shift_endtime=session('shift_endtime');	  

        //网费
          $map=array();
          $map['wb_id']=session('wbid');
          $map['cTime']=array('BETWEEN',array($shift_begintime,$shift_endtime));
                   
           $count= D('Change')->getSpChangeinfoList_count($map);
           $sql_page=ceil($count/$rows);  
           if($page>$sql_page) $page=1;
           
          $spInfos = D('Change')->getSpChangeinfoList($map,"$sidx $sord",$page,$rows); 
           foreach ($spInfos['list'] as &$val)
            {                             
              $val['cTime'] = date('Y-m-d H:i:s',strtotime($val['cTime']));  
              $syname = D('SpCtrlIp')->where(array('Wb_id'=>session('wbid'),'Syid'=>$val['syid']))->getField('syname');  
              if($syname !='')
              {
                $val['syid']= $syname;
              }            
            }

            $response = new \stdClass();
            $response->records = $spInfos['count'];
            $response->page = $page;
            $response->total = ceil($spInfos['count'] / $rows);
            foreach($spInfos['list'] as $key => $value)
            {
                $response->rows[$key]['id'] = $key;
                $response->rows[$key]['cell'] = $value;
            }

            $this->ajaxReturn($response);
      
        }
    }


    public function getshiftlist()
    { 

      if(IS_AJAX)
      {
        $page = I('get.page',1,'int');
        $rows = I('get.rows',20,'int');
        $sord = I('get.sord','','string')?:'desc';
        $sidx = I('get.sidx','','string')?:'id';
		$daterange = I('get.daterange','','string');
		$jiaobanren_id = I('get.jiaobanren','','string');		
        
		$map = array();
        $map['WB_ID']=session('wbid');           
        if(!empty($jiaobanren_id))
        {
          $map['cName']= D('Yuangong')->where(array('WB_ID'=>session('wbid'),'id'=>$jiaobanren_id))->getField('name');
        }  

        if(!empty($daterange))  
        {
          list($start,$end) = explode(' - ',$daterange);    
          $start = str_replace('/','-',$start);            
          $end = str_replace('/','-',$end);                
          $map['cTime'] = array('BETWEEN',array($start,$end));
        }
        
             
        $count= D('Shift')->getShiftListByMap_Count($map);
        $sql_page=ceil($count/$rows);  
        if($page>$sql_page) $page=1;


        $wblist = D('Shift')->getShiftListByMap($map,"$sidx $sord",$page,$rows);
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
		
		$response->sum_shijiao_money = $wblist['sum_shijiao_money'];
		$response->sum_liuxia_money  = $wblist['sum_liuxia_money'];
		

			
        foreach($wblist['list'] as $key => $value)
        {       
          $response->rows[$key]['id'] = $key;
          $response->rows[$key]['cell'] = $value;
        }
        $this->ajaxReturn($response);
      }             
    }
	
	public function getoneshiftlist()
    { 

      if(IS_AJAX)
      {
        $page = I('get.page',1,'int');
        $rows = I('get.rows',20,'int');
        $sord = I('get.sord','','string')?:'desc';
        $sidx = I('get.sidx','','string')?:'id';


        $map = array();
        $map['WB_ID']=session('wbid');
		$map['id']=session('shift_id');
				      
        $count= D('Shift')->getOneShiftListByMap_Count($map);
        $sql_page=ceil($count/$rows);  
        if($page>$sql_page) $page=1;


        $wblist = D('Shift')->getOneShiftListByMap($map,"$sidx $sord",$page,$rows);
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



    public function addmoney()
    {   	   
        $this->display();
    }

    public function gethykaddmoneylist()
    { 
      if(IS_AJAX)
      {
        $page = I('get.page',1,'int');
        $rows = I('get.rows',20,'int');
        $sord = I('get.sord','','string')?:'desc';
        $sidx = I('get.sidx','','string')?:'id';
        
        $daterange = I('get.daterange','','string');
        $cardno = I('get.cardno','','string');
        $hyname = I('get.hyname','','string');

        $map = array();
        $map['addmoney.WB_ID']=session('wbid');  
        if(!empty($cardno))
        {
          $map['addmoney.HyCardNo']=array('LIKE','%'.$cardno.'%');
        }  

        if(!empty($hyname))
        {
          $map['hytable.hyname']=array('LIKE','%'.$hyname.'%');
        }  
  
        if(!empty($daterange))  
        {
          list($start,$end) = explode(' - ',$daterange);    
          $start = str_replace('/','-',$start);            
          $end = str_replace('/','-',$end);                
          $map['addmoney.cTime'] = array('BETWEEN',array($start,$end));
		  $summoney=getAddMoneyByTime($start,$end);
        }

        $count= D('Hyaddmoneymx')->getHyaddmoneymxListByMap2_Count($map);
        $sql_page=ceil($count/$rows);  
        if($page>$sql_page) $page=1;


        $wblist = D('Hyaddmoneymx')->getHyaddmoneymxListByMap2($map,"$sidx $sord",$page,$rows);
		foreach ($wblist['list'] as &$val)
        {                             
           
          $syname = D('SpCtrlIp')->where(array('Wb_id'=>session('wbid'),'Syid'=>$val['SyId']))->getField('syname');  
          if($syname !='')
          {
            $val['SyId']= $syname;
          }            
        }
        

        $shift_money['sum_hyk_jq_money']=$sum_hyk_jq_money;
	   $shift_money['sum_hyk_jl_money']=$sum_hyk_jl_money;
	   $shift_money['sum_lsk_jq_money']=$sum_lsk_jq_money;
	   $shift_money['sum_lsk_zlje']    =$sum_lsk_zlje;
	   
	   if(empty($summoney))
	   {
		  $summoney['sum_hyk_jq_money']='0.00'; 
		  $summoney['sum_hyk_jl_money']='0.00';
		  $summoney['sum_lsk_jq_money']='0.00';
		  $summoney['sum_lsk_zlje']='0.00';
	   }	   

           
        $response = new \stdClass();
        $response->records = $wblist['count'];
        $response->page = $page;
        $response->total = ceil($wblist['count'] / $rows);	              
        $response->sum_hyk_jq_money=$summoney['sum_hyk_jq_money'];
		$response->sum_hyk_jl_money=$summoney['sum_hyk_jl_money'];
		$response->sum_lsk_jq_money=$summoney['sum_lsk_jq_money'];
		$response->sum_lsk_zlje    =$summoney['sum_lsk_zlje'];
     
		
        foreach($wblist['list'] as $key => $value)
        {       
          $response->rows[$key]['id'] = $key;
          $response->rows[$key]['cell'] = $value;
        }
		

		
		
        $this->ajaxReturn($response);
      }             
    }
	   
   	public function get_hykaddmoney_detail_bytime()
    { 
      if(IS_AJAX)
      {
        $page = I('get.page',1,'int');
        $rows = I('get.rows',20,'int');
        $sord = I('get.sord','','string')?:'desc';
        $sidx = I('get.sidx','','string')?:'ctime';
        
        $daterange = I('get.daterange','','string');
        $cardno = I('get.cardno','','string');
      
        $map = array();
        $map['addmoney.wb_id']=session('wbid');
  
        if(!empty($cardno))
        {
          $map['addmoney.cardno']=array('LIKE','%'.$cardno.'%');
        }  

        if(!empty($hyname))
        {
          $map['hytable.hyname']=array('LIKE','%'.$hyname.'%');
        }  

        if(!empty($daterange))  
        {
          list($start,$end) = explode(' - ',$daterange);    
          $start = str_replace('/','-',$start);            
          $end = str_replace('/','-',$end);                
          $map['addmoney.ctime'] = array('BETWEEN',array($start,$end));
		  $summoney=getAddMoneyByTime($start,$end);
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
           
	   if(empty($summoney))
	   {
		  $summoney['sum_hyk_jq_money']='0.00'; 
		  $summoney['sum_hyk_jl_money']='0.00';
		  $summoney['sum_lsk_jq_money']='0.00';
		  $summoney['sum_lsk_zlje']='0.00';
	   }	   

           
        $response = new \stdClass();
        $response->records = $wblist['count'];
        $response->page = $page;
        $response->total = ceil($wblist['count'] / $rows);
	              
        $response->sum_hyk_jq_money=$summoney['sum_hyk_jq_money'];
		$response->sum_hyk_jl_money=$summoney['sum_hyk_jl_money'];
		$response->sum_lsk_jq_money=$summoney['sum_lsk_jq_money'];
	    $response->sum_lsk_zlje    =$summoney['sum_lsk_zlje'];
      
		
        foreach($wblist['list'] as $key => $value)
        {       
          $response->rows[$key]['id'] = $key;
          $response->rows[$key]['cell'] = $value;
        }
				
        $this->ajaxReturn($response);
      }             
    }


    public function shangji()
    {
        $this->display();
    }



    public function getshangjimxlist()
    { 
		  if(IS_AJAX)
		  {
			$page = I('get.page',1,'int');
			$rows = I('get.rows',20,'int');
			$sord = I('get.sord','','string')?:'desc';
			$sidx = I('get.sidx','','string')?:'id';
			$viptype = I('get.viptype','','string');

			$daterange = I('get.daterange','','string');
		   $scardno = I('get.scardno','','string');
		   $sPcName = I('get.sPcName','','string');

			if(empty($viptype))
			{
			  $viptype=1;
			}    

			$map = array();
			if($viptype ==1)
			{
				$map['hyxfmx.WB_ID']=session('wbid');
				if(!empty($sPcName))
				{
				   $map['hyxfmx.cpName']=array('LIKE','%'.$sPcName.'%');;
				}   

				if(!empty($scardno))
				{
				   $map['hyxfmx.cardNo|hyxfmx.zjNo']=array('LIKE','%'.$scardno.'%');;
				}   

				if(!empty($daterange))  
				{
				  list($start,$end) = explode(' - ',$daterange);    
				  $start = str_replace('/','-',$start);            
				  $end = str_replace('/','-',$end);                
				  $map['hyxfmx.SjTime'] = array('BETWEEN',array($start,$end));
				} 
	 
			
				$count= D('Hyshangjimx')->getHyShangjimxListByMap_Count($map);
				
				$sql_page=ceil($count/$rows);  
				if($page>$sql_page) $page=1;


				$wblist = D('Hyshangjimx')->getHyShangjimxListByMap($map,"$sidx $sord",$page,$rows);
				
							
			}
			else if($viptype ==2)
			{
				$map['WB_ID']=session('wbid');

				if(!empty($sPcName))
				{
				   $map['cpName']=array('LIKE','%'.$sPcName.'%');;
				}   

				if(!empty($daterange))  
				{
				  list($start,$end) = explode(' - ',$daterange);    
				  $start = str_replace('/','-',$start);            
				  $end = str_replace('/','-',$end);                
				  $map['SjTime'] = array('BETWEEN',array($start,$end));
				}
				

				if(!empty($scardno))
				{
				   $map['cardNo|zjNo']=array('LIKE','%'.$scardno.'%');;
				} 


				$count= D('Lskshangjimx')->getAllLskShangjimxListByMap_Count($map);
				$sql_page=ceil($count/$rows);  
				if($page>$sql_page) $page=1;
				$wblist = D('Lskshangjimx')->getAllLskShangjimxListByMap($map,"$sidx $sord",$page,$rows);
						
			}
				
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
			$response->sum_shishou_money = $wblist['sum_shishou_money'];
			foreach($wblist['list'] as $key => $value)
			{       
			  $response->rows[$key]['id'] = $key;
			  $response->rows[$key]['cell'] = $value;
			}
		  }	
        $this->ajaxReturn($response);
                   
    }



    public function jifen()
    {   
        $this->display();          
    }

    public function getSpChangeinfo()
    {
        if(IS_AJAX){
            $page = I('get.page',1,'int');
            $rows = I('get.rows',20,'int');
            $sidx = I('get.sidx','','string')?:'cTime';
            $sord = I('get.sord','','string')?:'desc';
        
            $daterange=I('get.daterange','','string');
            $HyCardNo=I('get.HyCardNo','','string');
            $Lx=I('get.lx',-1,'int');
        
            $map=array();
            $map['jfchange.wb_id']=session('wbid');
            if(!empty($daterange))  
            {
              list($start,$end) = explode(' - ',$daterange);    
              $start = str_replace('/','-',$start);            
              $end = str_replace('/','-',$end);                
              $map['jfchange.cTime'] = array('BETWEEN',array($start,$end));
            }

            if(!empty($HyCardNo))
            { 
                $map['jfchange.HyCardNo']=array('LIKE','%'.$HyCardNo.'%');
            }
            if(($Lx==0)||($Lx==1))
            {
                $map['jfchange.Lx']=$Lx;
            }

            $spInfos = D('Change')->getSpChangeinfoList($map,"$sidx $sord",$page,$rows);          
           $response = new \stdClass();
            $response->records = $spInfos['count'];
            $response->page = $page;
            $response->total = ceil($spInfos['count'] / $rows);
            foreach($spInfos['list'] as $key => $value)
            {
                $response->rows[$key]['id'] = $key;
                $response->rows[$key]['cell'] = $value;
            }

            $this->ajaxReturn($response);
            
        }
    }


    public function shangji_detail()
    {
      $sGuid=I('get.guid','','string');	
	  session('sGuid',$sGuid);
      $this->display();	
    }
    
    public function getshangjimxmxlist()
    {
        if(IS_AJAX)
		  {
			$page = I('get.page',1,'int');
			$rows = I('get.rows',20,'int');
			$sord = I('get.sord','','string')?:'desc';
			$sidx = I('get.sidx','','string')?:'id';


			$map = array();
            $map['wb_id']=session('wbid');
			$map['sGuid']=session('sGuid');		
			$count= D('Lskshangjimxmx')->getLskShangjimxmxlist_Count($map);
			
			$sql_page=ceil($count/$rows);  
			if($page>$sql_page) $page=1;


			$list = D('Lskshangjimxmx')->getLskShangjimxmxlist($map,"$sidx $sord",$page,$rows);
			$response = new \stdClass();
			$response->records = $list['count'];
			$response->page = $page;
			$response->total = ceil($list['count'] / $rows);
			foreach($list['list'] as $key => $value)
			{       
			  $response->rows[$key]['id'] = $key;
			  $response->rows[$key]['cell'] = $value;
			}
		  }	
        $this->ajaxReturn($response);
    }
}
