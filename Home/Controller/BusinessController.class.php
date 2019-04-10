<?php
namespace Home\Controller;
class BusinessController extends CommonController
{

  public function ranking()
    {
		$loginguid=session('LoginGuid');
		$this->assign('loginguid',$loginguid);
        $this->display();
    }


    public function getHyxfmxranking()
    { 

      if(IS_AJAX)
      {
        $wbid=session('wbid');
        $map = array();
        if(!empty($wbid))
        {
          $map['WB_ID']=$wbid;
        } 
       
	   $daterange=I('get.daterange'); 
        if(!empty($daterange))  
        {
          list($start,$end) = explode(' - ',$daterange);    
          $start = str_replace('/','-',$start);            
          $end = str_replace('/','-',$end);                
          $map['SjTime'] = array('BETWEEN',array($start,$end));
        }
        

        $wblist = D('Hyshangjimx')->getHyxfjerankingListByMap_ght($map);               
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
	
	
	public function  vip()
	{
		$this->display();
	}
	
	
	public function  getHyLiushiData()
	{
	    if(IS_AJAX)
		{
			$wbid=session('wbid');
			
			
			$map['WB_ID']=$wbid;
			$daterange=I('post.daterange'); 

		  
			if(!empty($daterange))  
			{
			  list($start,$end) = explode(' - ',$daterange);    
			  $start = str_replace('/','-',$start);            
			  $end = str_replace('/','-',$end);                
			}
		   
		   //注：=会员收入+临时卡收入+现金商品销售收入；
		   //营业额=计费会员上机消费+临时卡上机消费+现金购买商品消费
		   
		   //如果周期在一个月内，就是每天出一个点
		   //如果周期在1-3个月内 就是每周出1个点
		   

		   // 现金收入
		   $daycount=getdayjiange($start,$end);
	  

		   //查询条件开始的那天所在范围0-23点
		   
		   $startTime= date('Y-m-d 00:00:00',strtotime($start));
		   $endTime  = date('Y-m-d 23:59:59',strtotime($start));
		   
		   
		   
		   //获取小时差
		   $startTime_hour= date('Y-m-d H:i:s',strtotime($start));
		   $endTime_hour  = date('Y-m-d H:i:s',strtotime($start));
		   $hourcount = getonehourjiange($startTime_hour,$endTime_hour);
		   
			$datalist0=array();
			$datalist1=array();
		   
			//一周出一条数据
		   
		   if($daycount >7)
		   {

			$weekstartTime=  date('Y-m-d ',strtotime($start));
			$weekendTime=    date('Y-m-d ',strtotime($end));
			$weekarray=getweekjiange( $weekstartTime,$weekendTime);

			$k=0;
			for($i=0;$i<count($weekarray);$i++)
			 {
			   //获取新增会员每天的开始和结束时间
				$oneweekbegtime= $weekarray[$i][0];
				$oneweekendtime= $weekarray[$i][1];			
				$oneweekbegtime_add = $oneweekbegtime;
				$oneweekendtime_add = $oneweekendtime;
				
			
				//获取流失会员每天的开始和结束时间
				
				$k=$i-90;
				$oneweekbegtime_liushi= strtotime('+'.$k.'days',strtotime($oneweekbegtime_add));
				$oneweekendtime_liushi= strtotime('+'.$k.'days',strtotime($oneweekendtime_add));

				$oneweekbegtime_liushi= date('Y-m-d H:i:s',$oneweekbegtime_liushi);
				$oneweekendtime_liushi= date('Y-m-d H:i:s',$oneweekendtime_liushi);
				
				
				$str1=substr($oneweekbegtime,5,5);          
				$xiasdata= $str1;  

		       //新增会员
				$map=array();
				$map['WB_ID']=session('wbid');
				$map['NewTime']=array('BETWEEN',array($oneweekbegtime_add,$oneweekendtime_add));
				$hycount_add = D('HyInfo')->where($map)->count();
				if(empty($hycount_add))
				{
					$hycount_add=0;
				}
				
				//流失会员			
				$map=array();
				$map['WB_ID']=session('wbid');
				$map['LastSjTime']=array('BETWEEN',array($oneweekbegtime_liushi,$oneweekendtime_liushi));
							
				$hycount_liushi = D('HyInfo')->where($map)->count();
				

				if(empty($hycount_liushi))
				{
					$hycount_liushi=0;
				}
				
		
			   $datalist0[$i]= (int)$hycount_add;
			   $datalist1[$i]= (int)$hycount_liushi;
	 
			   $axislist[$i]=$xiasdata;
			 
			 }            
		   } 
		  
			  
			//查询该日前7天内的新增会员
		
			
	       $k=0;
		   if($daycount<=7)
		   {
			 for($i=0;$i<$daycount;$i++)
			 {
			   //获取新增会员每天的开始和结束时间
			    
				$onedaybegtime= strtotime('+'.$i.'days',strtotime($start));
				$onedayendtime= strtotime('+'.$i.'days',strtotime($endTime));

				$onedaybegtime= date('Y-m-d H:i:s',$onedaybegtime);
				$onedayendtime= date('Y-m-d H:i:s',$onedayendtime);
				
				
				
				//获取流失会员每天的开始和结束时间
				$k=$i-90;
				$onedaybegtime_liushi= strtotime('+'.$k.'days',strtotime($start));
				$onedayendtime_liushi= strtotime('+'.$k.'days',strtotime($endTime));

				$onedaybegtime_liushi= date('Y-m-d H:i:s',$onedaybegtime_liushi);
				$onedayendtime_liushi= date('Y-m-d H:i:s',$onedayendtime_liushi);
				
				

				$str1=substr($onedaybegtime,5,5);
				$xiasdata= $str1;  
			   
				
                //新增会员
				$map=array();
				$map['WB_ID']=session('wbid');
				$map['NewTime']=array('BETWEEN',array($onedaybegtime,$onedayendtime));
				$hycount_add = D('HyInfo')->where($map)->count();
				if(empty($hycount_add))
				{
					$hycount_add=0;
				}
				
				//流失会员			
				$map=array();
				$map['WB_ID']=session('wbid');
				$map['LastSjTime']=array('BETWEEN',array($onedaybegtime_liushi,$onedayendtime_liushi));
				$hycount_liushi = D('HyInfo')->where($map)->count();
				if(empty($hycount_liushi))
				{
					$hycount_liushi=0;
				}
				
		
			   $datalist0[$i]= (int)$hycount_add;
			   $datalist1[$i]= (int)$hycount_liushi;
			
			  // $yingye_money= $yingye_money+$incomelist['Xj_je'];
			  // $shouru_money= $shouru_money+$incomelist['qt_Je'];
			   
			   $axislist[$i]=$xiasdata;
			   
			 }          
		   } 
		   
		   
			 

		   $list['money'][0]['data']= $datalist0;
		   $list['money'][1]['data']= $datalist1;
	
		   $list['xAxis']= $axislist;
		 //  $list['yingye']= $yingye_money;
		  // $list['shouru']= $shouru_money;
		  // $list['shourusum']= $yingye_money+$shouru_money;


			$this->ajaxReturn($list);
		}  
  	
	}
	
	
	

    public function getHyshangjimxranking()
    { 

      if(IS_AJAX)
      {
        $wbid=session('wbid');
        $map = array();
        if(!empty($wbid))
        {
          $map['WB_ID']=$wbid;
        } 
       
	   $daterange=I('get.daterange'); 
        if(!empty($daterange))  
        {
          list($start,$end) = explode(' - ',$daterange);    
          $start = str_replace('/','-',$start);            
          $end = str_replace('/','-',$end);                
          $map['SjTime'] = array('BETWEEN',array($start,$end));
        }
        

        $wblist = D('Hyshangjimx')->getHyShangjiTimerankingListByMap($map);               
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

    public function getHyaddmoneymxranking()
    { 

      if(IS_AJAX)
      {
        $wbid=session('wbid');
        $map = array();
        if(!empty($wbid))
        {
          $map['WB_ID']=$wbid;
        } 
       

        $wblist = D('Hyaddmoneymx')->getHyaddmoneyrankingListByMap($map);               
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



    public function income()
    {
        $this->display();
    }
	
	

	
	public function getIncomeData()
    { 

      if(IS_AJAX)
      {
        $wbid=session('wbid');
        $map['WB_ID']=$wbid;
        $daterange=I('post.daterange'); 

      
        if(!empty($daterange))  
        {
          list($start,$end) = explode(' - ',$daterange);    
          $start = str_replace('/','-',$start);            
          $end = str_replace('/','-',$end);                
        }
       
       //注：现金收入=会员收入+临时卡收入+现金商品销售收入；
       //营业额=计费会员上机消费+临时卡上机消费+现金购买商品消费
       
       //如果周期在一个月内，就是每天出一个点
       //如果周期在1-3个月内 就是每周出1个点
       

       // 现金收入
       $daycount=getdayjiange($start,$end);
  

       //查询条件开始的那天所在范围0-23点
       
       $startTime= date('Y-m-d 00:00:00',strtotime($start));
       $endTime  = date('Y-m-d 23:59:59',strtotime($start));
	   
	   
	   
	   //获取小时差
	   $startTime_hour= date('Y-m-d H:i:s',strtotime($start));
       $endTime_hour  = date('Y-m-d H:i:s',strtotime($start));
	   $hourcount = getonehourjiange($startTime_hour,$endTime_hour);
	   
	    $datalist1=array();
		$datalist2=array();
       
        //一周出一条数据
	   
       if($daycount >31)
       {

        $weekstartTime=  date('Y-m-d ',strtotime($start));
        $weekendTime=    date('Y-m-d ',strtotime($end));
        $weekarray=getweekjiange( $weekstartTime,$weekendTime);

        for($i=0;$i<count($weekarray);$i++)
         {
           //获取每天的开始和结束时间
            $oneweekbegtime= $weekarray[$i][0];
            $oneweekendtime= $weekarray[$i][1];

            $str1=substr($oneweekbegtime,5,5);          
            $xiasdata= $str1;  

           //现金收入
            $map=array();
			$map['wb_id']=session('wbid');
			$map['cTime']=array('BETWEEN',array($oneweekbegtime,$oneweekendtime));
			$incomelist = D('Tongji')->where($map)->find();
			if(empty($incomelist))
			{
				$incomelist['Xj_je']=0;
				$incomelist['qt_Je']=0;
			}

           $datalist1[$i]= (float)sprintf("%.2f", $incomelist['Xj_je']);;
		   $datalist2[$i]= (float)sprintf("%.2f", $incomelist['qt_Je']);
		   $datalist3[$i]= $datalist1[$i]+$datalist2[$i];

           $yingye_money= $yingye_money+$incomelist['Xj_je'];
		   $shouru_money= $shouru_money+$incomelist['qt_Je'];
 
           $axislist[$i]=$xiasdata;
         
         }            
       } 
      
       	  
        //查询该日前30天内的记录
	
		
		//计算30天内的收入

       if(($daycount<=31) && ($daycount>1))
       {
         for($i=0;$i<$daycount;$i++)
         {
           //获取每天的开始和结束时间
            $onedaybegtime= strtotime('+'.$i.'days',strtotime($start));
            $onedayendtime= strtotime('+'.$i.'days',strtotime($endTime));

            $onedaybegtime= date('Y-m-d H:i:s',$onedaybegtime);
            $onedayendtime= date('Y-m-d H:i:s',$onedayendtime);

            $str1=substr($onedaybegtime,5,5);
            $xiasdata= $str1;  
           
            

           	$map=array();
			$map['wb_id']=session('wbid');
			$map['cTime']=array('BETWEEN',array($onedaybegtime,$onedayendtime));
			$incomelist = D('Tongji')->where($map)->find();
			if(empty($incomelist))
			{
				$incomelist['Xj_je']=0;
				$incomelist['qt_Je']=0;
			}

           $datalist1[$i]= (float)sprintf("%.2f", $incomelist['Xj_je']);;
		   $datalist2[$i]= (float)sprintf("%.2f", $incomelist['qt_Je']);
		   $datalist3[$i]= $datalist1[$i]+$datalist2[$i];
		   $yingye_money= $yingye_money+$incomelist['Xj_je'];
		   $shouru_money= $shouru_money+$incomelist['qt_Je'];
		   
           $axislist[$i]=$xiasdata;
           
         }          
       } 
	   
	   
	     

       $list['money'][0]['data']= $datalist3;
       $list['money'][1]['data']= $datalist1;
	   $list['money'][2]['data']= $datalist2;
       $list['xAxis']= $axislist;
       $list['yingye']= $yingye_money;
       $list['shouru']= $shouru_money;
	   $list['shourusum']= $yingye_money+$shouru_money;


        $this->ajaxReturn($list);
      }  
                   
    }
    
   /*
    public function getIncomeData()
    { 

      if(IS_AJAX)
      {
        $wbid=session('wbid');
        $map['WB_ID']=$wbid;
        $daterange=I('post.daterange'); 

      

        if(!empty($daterange))  
        {
          list($start,$end) = explode(' - ',$daterange);    
          $start = str_replace('/','-',$start);            
          $end = str_replace('/','-',$end);                
        }
       
       //注：现金收入=会员收入+临时卡收入+现金商品销售收入；
       //营业额=计费会员上机消费+临时卡上机消费+现金购买商品消费
       
       //如果周期在一个月内，就是每天出一个点
       //如果周期在1-3个月内 就是每周出1个点
       


       // 现金收入
       $daycount=getdayjiange($start,$end);
  

       //查询条件开始的那天所在范围0-23点
       
       $startTime= date('Y-m-d 00:00:00',strtotime($start));
       $endTime  = date('Y-m-d 23:59:59',strtotime($start));
       
        //一周出一条数据
       if($daycount >30)
       {

        $weekstartTime=  date('Y-m-d ',strtotime($start));
        $weekendTime=    date('Y-m-d ',strtotime($end));
        $weekarray=getweekjiange( $weekstartTime,$weekendTime);

        for($i=0;$i<count($weekarray);$i++)
         {
           //获取每天的开始和结束时间
            $oneweekbegtime= $weekarray[$i][0];
            $oneweekendtime= $weekarray[$i][1];

            $str1=substr($oneweekbegtime,5,5);          
            $xiasdata= $str1;  

           //现金收入
           $map1=array();
           $map1['SjTime']=array('BETWEEN',array($oneweekbegtime,$oneweekendtime));
           $map1['WB_ID']=$wbid;
           $hyincomedata  = D('Hyxfmx')->where($map1)->sum('je');

           $map2=array();
           $map2['SjTime']=array('BETWEEN',array($oneweekbegtime,$oneweekendtime));
           $map2['WB_ID']=$wbid;
           $lskincomedata = D('Lskshangjimx')->where($map2)->sum('je');

           $map3=array();
           $map3['Rq']=array('BETWEEN',array($oneweekbegtime,$oneweekendtime));
           $map3['WB_ID']=$wbid;

           $spxsincomedata = D('Spxs')->where($map3)->sum('totalprice');

           $datalist1[$i]= $hyincomedata+ $lskincomedata +$spxsincomedata;
        

         //营业额
         
           $map11=array();
           $map11['cTime']=array('BETWEEN',array($oneweekbegtime,$oneweekendtime));
           $map11['WB_ID']=$wbid;
           $hyaddmoneyincomedata  = D('Hyaddmoneymx')->where($map11)->sum('je');

           $map22=array();
           $map22['debug_InsertTime']=array('BETWEEN',array($oneweekbegtime,$oneweekendtime));
           $map22['WB_ID']=$wbid;
           $lskaddmoneyincomedata = D('Lskaddmoneymx')->where($map22)->sum('je');

           $map33=array();
           $map33['Rq']=array('BETWEEN',array($oneweekbegtime,$oneweekendtime));
           $map33['WB_ID']=$wbid;

           $spxsincomedata = D('Spxs')->where($map33)->sum('totalprice');

           $datalist2[$i]= $hyaddmoneyincomedata+ $lskaddmoneyincomedata +$spxsincomedata;
         
           $axislist[$i]=$xiasdata;
         
         }            
       } 
    

       if($daycount<=30)
       {
         for($i=0;$i<$daycount;$i++)
         {
           //获取每天的开始和结束时间
            $onedaybegtime= strtotime('+'.$i.'days',strtotime($startTime));
            $onedayendtime= strtotime('+'.$i.'days',strtotime($endTime));

            $onedaybegtime= date('Y-m-d H:i:s',$onedaybegtime);
            $onedayendtime= date('Y-m-d H:i:s',$onedayendtime);



             $str1=substr($onedaybegtime,5,5);
             $xiasdata= $str1;  
           

           //现金收入
           $map1=array();
           $map1['SjTime']=array('BETWEEN',array($onedaybegtime,$onedayendtime));
           $map1['WB_ID']=$wbid;
           $hyincomedata  = D('Hyxfmx')->where($map1)->sum('je');

           $map2=array();
           $map2['SjTime']=array('BETWEEN',array($onedaybegtime,$onedayendtime));
           $map2['WB_ID']=$wbid;
           $lskincomedata = D('Lskshangjimx')->where($map2)->sum('je');

           $map3=array();
           $map3['Rq']=array('BETWEEN',array($onedaybegtime,$onedayendtime));
           $map3['WB_ID']=$wbid;

           $spxsincomedata = D('Spxs')->where($map3)->sum('totalprice');

           $datalist1[$i]= $hyincomedata+ $lskincomedata +$spxsincomedata;
        

         //营业额
         
           $map11=array();
           $map11['cTime']=array('BETWEEN',array($onedaybegtime,$onedayendtime));
           $map11['WB_ID']=$wbid;
           $hyaddmoneyincomedata  = D('Hyaddmoneymx')->where($map11)->sum('je');
           


           $map22=array();
           $map22['cTime']=array('BETWEEN',array($onedaybegtime,$onedayendtime));
           $map22['WB_ID']=$wbid;
           $lskaddmoneyincomedata = D('Lskaddmoneymx')->where($map22)->sum('je');


           $map33=array();
           $map33['Rq']=array('BETWEEN',array($onedaybegtime,$onedayendtime));
           $map33['WB_ID']=$wbid;

           //$spxsincomedata = D('Spxs')->where($map33)->sum('totalprice');

           $datalist2[$i]= $hyaddmoneyincomedata+ $lskaddmoneyincomedata +$spxsincomedata;
		   


           $shouru_money= $shouru_money+$datalist1[$i];
           $yingye_money= $yingye_money+$datalist2[$i];
         
           $axislist[$i]=$xiasdata;
           
         }          
       } 

       $list['money'][0]['data']= $datalist1;
       $list['money'][1]['data']= $datalist2;
       $list['xAxis']= $axislist;
       $list['yingye']= $yingye_money;
       $list['shouru']= $shouru_money;


        $this->ajaxReturn($list);
      }  
                   
    }

	*/







    


      public function getFenxiData()
    { 

      if(IS_AJAX)
      {
        $wbid=session('wbid');
        $map['WB_ID']=$wbid;
        $daterange=I('post.daterange'); 

        if(!empty($daterange))  
        {
          list($start,$end) = explode(' - ',$daterange);    
          $start = str_replace('/','-',$start);            
          $end = str_replace('/','-',$end);                
        }
       
       //注：现金收入=会员收入+临时卡收入+现金商品销售收入；
       //营业额=计费会员上机消费+临时卡上机消费+现金购买商品消费
       
       //如果周期在一个月内，就是每天出一个点
       //如果周期在1-3个月内 就是每周出1个点
       
       // 现金收入
       $daycount=getdayjiange($start,$end);


       //查询条件开始的那天所在范围0-23点
       
       $startTime= date('Y-m-d 00:00:00',strtotime($start));
       $endTime  = date('Y-m-d 23:59:59',strtotime($start));
       
       //获取网吧机器分组
       

       // $grouplist=D('Grouplist')->alias('grouplist')->join('left join WComputerList as pclist on pclist.GroupNameGuid= grouplist.Guid and pclist.WB_ID=grouplist.WB_ID')
       // ->field(array(
       //  'grouplist.GroupName'=>'GroupName',
       //  'grouplist.Guid'=>'Guid',
       //  'pclist.Name'=>'pcname',
       //  ))
       // ->where(array('grouplist.WB_ID'=>$wbid))->select();
       
       $grouplist= D('Grouplist')->where(array('WB_ID'=>$wbid))->select();
       // for($i=0;$i<count($grouplist);$i++)
       // {
       //   $grouplist[$]['Guid']
       // } 

       // foreach($grouplist as &$val)
       // {
          // if()
          // {


          // }  
       // } 
   
      if($daycount<=30)
      {
         foreach($grouplist as &$val)
         {
             
              for($i=0;$i<$daycount;$i++)
             {
               //获取每天的开始和结束时间
                $onedaybegtime= strtotime('+'.$i.'days',strtotime($startTime));
                $onedayendtime= strtotime('+'.$i.'days',strtotime($endTime));

                $onedaybegtime= date('Y-m-d H:i:s',$onedaybegtime);
                $onedayendtime= date('Y-m-d H:i:s',$onedayendtime);



                 $str1=substr($onedaybegtime,5,5);
                 $xiasdata= $str1;  
               

               //现金收入
               $map1=array();
               $map1['SjTime']=array('BETWEEN',array($onedaybegtime,$onedayendtime));
               $map1['WB_ID']=$wbid;
               // $hyincomedata  = D('Hyxfmx')->join()->where($map1)->sum('je');
               
               $map2=array();
               $map2['SjTime']=array('BETWEEN',array($onedaybegtime,$onedayendtime));
               $map2['WB_ID']=$wbid;
               $lskincomedata = D('Lskshangjimx')->where($map2)->sum('je');

               $map3=array();
               $map3['Rq']=array('BETWEEN',array($onedaybegtime,$onedayendtime));
               $map3['WB_ID']=$wbid;

               $spxsincomedata = D('Spxs')->where($map3)->sum('totalprice');
               $datalist1[$i]= $hyincomedata+ $lskincomedata +$spxsincomedata;
            
             //营业额      
               $shouru_money= $shouru_money+$datalist1[$i];   
               $axislist[$i]=$xiasdata;
    
             }          
         } 
      }  


  

       

       $list['money'][0]['data']= $datalist1;
       // $list['money'][1]['data']= $datalist2;
       $list['xAxis']= $axislist;
       $list['yingye']= $yingye_money;
       $list['shouru']= $shouru_money;


   


        
 

        // $wblist = D('Hyshangjimx')->getHyxfjerankingListByMap($map);  

        // $response = new \stdClass();
        // $response->records = $wblist['count'];
        // $response->page = $page;
        // $response->total = ceil($wblist['count'] / $rows);


        // foreach($wblist['list'] as $key => $value)
        // {       
        //   $response->rows[$key]['id'] = $key;
        //   $response->rows[$key]['cell'] = $value;
        // }
        $this->ajaxReturn($list);
      }  
                   
    }


    public function jingying()
    {
		$loginguid=session('LoginGuid');
		$this->assign('loginguid',$loginguid);
        $this->display();
    }


    public function getJinyingData()
    { 

      if(IS_AJAX)
      {


        //获取一下网吧的分组     
        $daterange=I('post.daterange'); 
        if(!empty($daterange))  
        {
          list($start,$end) = explode(' - ',$daterange);    
          $start = str_replace('/','-',$start);            
          $end = str_replace('/','-',$end);                
        }
             
       //如果周期在一个月内，就是每天出一个点
       //如果周期在1-3个月内 就是每周出1个点
      
       // 现金收入
       $daycount=getdayjiange($start,$end);       
       $startTime= date('Y-m-d 00:00:00',strtotime($start));
       $endTime  = date('Y-m-d 23:59:59',strtotime($start));
       
        //一周出一条数据
       if($daycount >30)
       {

	   }

       if($daycount<=30)
       {  
           
       } 

       $list['money'][0]['data']= $datalist1;
       $list['money'][1]['data']= $datalist2;
       $list['xAxis']= $axislist;
       $list['yingye']= $yingye_money;
       $list['shouru']= $shouru_money;

        $this->ajaxReturn($list);
      }  
                   
    }
	
	
	
	public function incomesum()
	{
	
	    $moren_month=date('Y-m',time());
		$this->assign('moren_month',$moren_month);
		
		$moren_year=date('Y',time());
		$this->assign('moren_year',$moren_year);
		
		$this->display();
	}
	
    public function getalipayinfo_yue()
    {
        
        if(IS_AJAX)
        {       
 
            $page = I('get.page',1,'int');
            $rows = I('get.rows',20,'int');
            $sord = I('get.sord','','string')?:'desc';
            $sidx = I('get.sidx','','string')?:'cTime';
			
            $year     = I('get.year','','string');//获取交班时间

            $map = array();
      
           	if(empty($year))
			{
				$year=date('Y',time());
			}	
			



	
            $map['wb_id']=session('wbid');		
               
         //   $count=D('Tongji')->getTongjilist_count_day($map);

           // $sql_page=ceil($count/$rows);   
            
        
			 

            $zfbpaydata = D('Tongji')->getTongjilist_yue($map,$page,$rows,$year);  
            $count=	$zfbpaydata['count'];		
			
			// $sql_page=ceil($count/$rows);   
            
            // if($page<=0)   $page=1;       
            // if($page>$sql_page) 
            // {
              // $page=1; 
            // }
			
			$page=1; 
			
             

							
            
            $response = new \stdClass();
            $response->count       = $zfbpaydata['count'];//返回的数组的第一个字段记录总条数
            $response->nowPage     = $page ;              //每页显示的记录数目               
            $response->total       = ceil($zfbpaydata['count'] / $rows);          
            $response->Sum_Je     = $zfbpaydata['Sum_Je'] ;
            $response->Xj_je  = $zfbpaydata['Xj_je'] ;
			 $response->qt_Je  = $zfbpaydata['qt_Je'] ;
       
            $response->rows   = $zfbpaydata['list'] ;
            $this->ajaxReturn($response);
        }
    } 

	
    public function getalipayinfo_day()
    {
        
        if(IS_AJAX)
        {       
 
            $page = I('get.page',1,'int');
            $rows = I('get.rows',20,'int');
            $sord = I('get.sord','','string')?:'desc';
            $sidx = I('get.sidx','','string')?:'cTime';

            $month     = I('get.month','','string');//获取交班时间
			
			if(empty($month))
			{
				$month=date('Y-m',time());
			}	
            $map = array();
            $map['wb_id']=session('wbid');		                              		
            $zfbpaydata = D('Tongji')->getTongjilist_day($map,$page,$rows,$month);  
            $count=	$zfbpaydata['count'];								
			$page=1; 
			
             				          
            $response = new \stdClass();
            $response->count       = $zfbpaydata['count'];//返回的数组的第一个字段记录总条数
            $response->nowPage     = $page ;              //每页显示的记录数目               
            $response->total       = ceil($zfbpaydata['count'] / $rows);          
            $response->Sum_Je     = $zfbpaydata['Sum_Je'] ;
            $response->Xj_je  = $zfbpaydata['Xj_je'] ;
			 $response->qt_Je  = $zfbpaydata['qt_Je'] ;
			
       
            $response->rows   = $zfbpaydata['list'] ;
            $this->ajaxReturn($response);
        }
    } 











}
