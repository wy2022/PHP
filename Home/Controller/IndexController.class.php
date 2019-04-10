<?php
namespace Home\Controller;
class IndexController extends CommonController 
{
	public function index()  //后台首页页面，进入后就调用一系列函数，展示界面
	{
	
	   $nowtime=date('Y-m-d H:i:s');
	   $endtime=D('WbInfo')->where(array('WBID'=>session('wbid')))->getField('EndTime');
	   
	   $daycha=getdayjiange($nowtime,$endtime);
	   
	   $isFirstlogin=session('isFirstlogin');
	   
	   if(!empty($isFirstlogin))
	   {
		    	  //非首次登录，不需要弹出
          $this->assign('tanchu',0);    	  
	   }
	   else
       {           
		  	  //首次登录需要弹出
		  session('isFirstlogin',1);	  
          $this->assign('tanchu',1);   
	   } 		   
	   $this->assign('daycha',$daycha);     
	   $money_array=getTodayShouru();
       $nowshift_money=getNowShiftShouru();

        $shiftinfo= D('Shift')->where(array('WB_ID'=>session('wbid')))->Order('cTime desc')->limit(1)->find();
        $lastshifttime=date('Y-m-d H:i:s',strtotime($shiftinfo['cTime']));
       
        $nowday['shift_begtime']= $lastshifttime;
        $nowday['nowtime']= date('Y-m-d H:i:s');


        $nowtime1=date('Y-m-d H:i:s');
  
        $today['begtime']=date('Y-m-d 00:00:00',strtotime($nowtime1));
        $today['endtime']=$nowday['nowtime'];
		
		
		$onlinelist= D('OnlineState')->where(array('Wb_Id'=>session('wbid')))->getField('WbState');		
		$online_json= json_decode($onlinelist,true);		
		$area_list= $online_json['GroupInfo'];	
		$this->assign('area_list',$area_list);


		$online_numlist['TemOnline']= $online_json['TemOnline'];
		$online_numlist['HyOnline']=  $online_json['HyOnline'];		
		$this->assign('online_numlist',$online_numlist);			

       $this->assign('today',$today);
       $this->assign('nowday',$nowday);
       $this->assign('nowshift_money',$nowshift_money);
       $this->assign('money_array',$money_array);
	   
	   	  
		  
		 /* 
		$post_data['wbid']=session('wbid');	
        $daili_url_zong =C('DAILI_URL_ZONG');		
		$url= $daili_url_zong.'/index.php/ServerzongAPI/API_query_onebarbanginfo.html';
		$res= sendRequsttoOneServer($url, $post_data,30);
		$res= substr($res, 3);				
		$res2=json_decode($res,true);	
		$agentinfo=$res2['body'];
						
		$this->assign('agent_realname',$agentinfo['agent_realname']);	
			
		*/
	   
   
	   

	     $this->display();
	}
	
	
	public function index_hua()
   {
		$this->display();
	}	

	public function iindex()
  {
		$bar_model = D('Bar');
		$this->assign('count',$bar_model->getBarCount(749));
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

        $shiftinfo= D('Shift')->where(array('WB_ID'=>session('wbid')))->Order('cTime desc')->limit(1)->find();
        $lastshifttime=date('Y-m-d H:i:s',strtotime($shiftinfo['cTime']));
        $nowtime=date('Y-m-d H:i:s');
        //网费
        $map=array();
        $map['WB_ID']=session('wbid');
        $map['cTime']=array('BETWEEN',array($lastshifttime,$nowtime));
        

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

        $shiftinfo= D('Shift')->where(array('WB_ID'=>session('wbid')))->Order('cTime desc')->limit(1)->find();
        $lastshifttime=date('Y-m-d H:i:s',strtotime($shiftinfo['cTime']));
        $nowtime=date('Y-m-d H:i:s');
        //网费
        $map=array();
        $map['WB_ID']=session('wbid');
        $map['cTime']=array('BETWEEN',array($lastshifttime,$nowtime));
        

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





    public function getshift_SpChangeinfo()
    {
        if(IS_AJAX){
        $page = I('get.page',1,'int');
            $rows = I('get.rows',20,'int');
            $sidx = I('get.sidx','','string')?:'cTime';
            $sord = I('get.sord','','string')?:'desc';
        



            $shiftinfo= D('Shift')->where(array('WB_ID'=>session('wbid')))->Order('cTime desc')->limit(1)->find();
            $lastshifttime=date('Y-m-d H:i:s',strtotime($shiftinfo['cTime']));
            $nowtime=date('Y-m-d H:i:s');
            //网费
            $map=array();
            $map['jfchange.wb_id']=session('wbid');


            $map['jfchange.cTime']=array('BETWEEN',array($lastshifttime,$nowtime));


           
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





    public  function  getshiftlist_lskzhaoling_detail()
  {
      if(IS_AJAX)
      {
        $page = I('get.page',1,'int');
        $rows = I('get.rows',30,'int');
        $sord = I('get.sord','','string')?:'desc';
        $sidx = I('get.sidx','','string')?:'cTime';

        $shiftinfo= D('Shift')->where(array('WB_ID'=>session('wbid')))->Order('cTime desc')->limit(1)->find();
        $lastshifttime=date('Y-m-d H:i:s',strtotime($shiftinfo['cTime']));
        $nowtime=date('Y-m-d H:i:s');
        //网费
        $map=array();
        $map['WB_ID']=session('wbid');
        $map['cTime']=array('BETWEEN',array($lastshifttime,$nowtime));
		
		

               

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




    public  function  getwxpaylist()
  {
      if(IS_AJAX)
      {
        $page = I('get.page',1,'int');
        $rows = I('get.rows',10,'int');
        $sord = I('get.sord','','string')?:'desc';
        $sidx = I('get.sidx','','string')?:'cTime';

        $shiftinfo= D('Shift')->where(array('WB_ID'=>session('wbid')))->Order('cTime desc')->limit(1)->find();
        $lastshifttime=date('Y-m-d H:i:s',strtotime($shiftinfo['cTime']));
        $nowtime=date('Y-m-d H:i:s');
        //网费
        $map=array();
        $map['wb_id']=session('wbid');
        $map['cTime']=array('BETWEEN',array($lastshifttime,$nowtime));
        

        $count= D('WxMx')->getWxAddmoneyList_count($map);
        $sql_page=ceil($count/$rows);  
        if($page>$sql_page) $page=1;


        $wblist = D('WxMx')->getWxAddmoneyList($map,"$sidx $sord",$page,$rows);

            foreach ($wblist['list'] as &$val)
            {                             
               
              $syname = D('SpCtrlIp')->where(array('Wb_id'=>session('wbid'),'Syid'=>$val['SyId']))->getField('syname');  
              if($syname !='')
              {
                $val['SyId']= $syname;
              }        
              $val['je']=sprintf("%.2f", $val['je']);    
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




    public  function  getalipayinfo()
  {
      if(IS_AJAX)
      {
        $page = I('get.page',1,'int');
        $rows = I('get.rows',10,'int');
        $sord = I('get.sord','','string')?:'desc';
        $sidx = I('get.sidx','','string')?:'cTime';

        $shiftinfo= D('Shift')->where(array('WB_ID'=>session('wbid')))->Order('cTime desc')->limit(1)->find();
        $lastshifttime=date('Y-m-d H:i:s',strtotime($shiftinfo['cTime']));
        $nowtime=date('Y-m-d H:i:s');
        //网费
        $map=array();
        $map['wb_id']=session('wbid');
        $map['cTime']=array('BETWEEN',array($lastshifttime,$nowtime));
        

        $count= D('ZfbAddMoneyMx')->getzfbAddmoneyList_count($map);
        $sql_page=ceil($count/$rows);  
        if($page>$sql_page) $page=1;


        $wblist = D('ZfbAddMoneyMx')->getzfbAddmoneyList($map,"$sidx $sord",$page,$rows);

          foreach ($wblist['list'] as &$val)
          {                             
             
            $syname = D('SpCtrlIp')->where(array('Wb_id'=>session('wbid'),'Syid'=>$val['SyId']))->getField('syname');  
            if($syname !='')
            {
              $val['SyId']= $syname;
            }     
            $val['je']=sprintf("%.2f", $val['je']);    
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



	public function getshiftlist()
    { 

      if(IS_AJAX)
      {
        $page = I('get.page',1,'int');
        $rows = I('get.rows',20,'int');
        $sord = I('get.sord','','string')?:'desc';
        $sidx = I('get.sidx','','string')?:'id';


        $map = array();
        $map['WB_ID']=session('wbid');           
        $count= D('Shift')->getShiftListByMap_Count($map);
        $sql_page=ceil($count/$rows);  
        if($page>$sql_page) $page=1;


        $wblist = D('Shift')->getShiftListByMap($map,"id desc",$page,$rows);
 
        

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

    public function getData()
    {

      $list=getDayShouru_bing();
	  $postlist=array();
	  $i=0;
	  foreach($list as &$val)
	  {
		  $postlist['data'][$i]['value']=$val['value'];
		  $postlist['data'][$i]['name']= $val['name'];
		  $i++;
		  
	  }
	  
	  
      $this->ajaxReturn($postlist);
    }



}