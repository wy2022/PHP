<?php
namespace Home\Controller;
use Think\Controller;
class GoodsAPIController extends Controller 
{   
   
	public  function API_queryclient_ini()
	{	
	
		$wbaccount=I('get.wbaccount','','string');		 
		$wbid=D('WbInfo')->where(array('WbAccount'=>$wbaccount))->getField('WBID');
		
	
		if(empty($wbid))
		{
			$data['result']=-1;
			$data['message']='wu  wbid';
		    echo  json_encode($data);  
            return; 			
		}
		
		
		//查询是否允许客户端开启商品购买
	    $khd_sp_buy = D('Webini')->where(array('wbid'=>$wbid,'skey'=>'khd_sp_buy'))->getField('svalue');
		$khd_wxzfb_buy= D('Webini')->where(array('wbid'=>$wbid,'skey'=>'khd_wxzfb_buy'))->getField('svalue');
		
	    if($khd_sp_buy==='0')
		{
		   $khd_sp_buy=0;  
		}else
		{
			 $khd_sp_buy=1; 
		}	
	

        if($khd_wxzfb_buy==='0')
		{
		   $khd_wxzfb_buy=0;  
		}
		else
		{
		  $khd_wxzfb_buy=1; 	
		}	
		
		$data=array();
		$data['result']=1;
		$data['khd_sp_buy']   = $khd_sp_buy;
		$data['khd_wxzfb_buy']= $khd_wxzfb_buy;
						
 
		echo  json_encode($data);
									 				  
	}
	
	
	
	
	public function GetShiftinfo()
    {

        $wbid     = I('post.WbAccount',0,'int');   
        $page     = I('post.hiddenNowPage',1,'int'); 

   
        $map=array();
        $map['wbid']=$wbid;
 
        $count=D('Productjb')->where($map)->count();
      

        $rows=10;
        $sql_page=ceil($count/$rows);   
        
        if($page<=0)   $page=1;       
        if($page>$sql_page) 
        {
          $page=1; 
        }
        else
        {

        }      

        $gzhpaydata=D('Productjb')->fnQueryOrderList($map,$page,$rows);

        $response = new \stdClass();
        $response->count       = $gzhpaydata['count'];//返回的数组的第一个字段记录总条数
        $response->nowPage     = $page ;              //每页显示的记录数目               
        $response->total       = ceil($gzhpaydata['count'] / $rows);          
   
          

        $response->rows   = $gzhpaydata['list'] ;
 
        $this->ajaxReturn($response);    
    }
	
	
	
	public function GetTwoDaysSumShouru()
    {

        $wbid     = I('post.WbAccount',0,'int');        		 		
		$nowtime=date('Y-m-d H:i:s',time());		
		$startTime= date('Y-m-d 00:00:00',strtotime($nowtime));
        $endTime  = date('Y-m-d 23:59:59',strtotime($nowtime));
				
		// 今天		
		$today_begtime= $startTime;
		$today_endtime= $endTime;
		 		
		//昨天
		
        $onedaybegtime= strtotime('-1 days',strtotime($startTime));
        $onedayendtime= strtotime('-1 days',strtotime($endTime));	
        $yestoday_begtime= date('Y-m-d H:i:s',$onedaybegtime);
        $yestoday_endtime= date('Y-m-d H:i:s',$onedayendtime);
		
		     
	   //今日现金收入
		   
	   $map1=array();
	   $map1['cTime']=array('BETWEEN',array($today_begtime,$today_endtime));
	   $map1['WB_ID']=$wbid;
	   $hyincomedata  = D('Hyaddmoneymx')->where($map1)->sum('je');
	   
	 

	   $map2=array();
	   $map2['cTime']=array('BETWEEN',array($today_begtime,$today_endtime));
	   $map2['WB_ID']=$wbid;
	   $lskincomedata = D('Lskaddmoneymx')->where($map2)->sum('je');
	   
	 

	   $map3=array();
	   $map3['Rq']=array('BETWEEN',array($today_begtime,$today_endtime));
	   $map3['WB_ID']=$wbid;

	   $spxsincomedata = D('Spxs')->where($map3)->sum('totalprice');
	   
		if(empty($hyincomedata))
		{
			$hyincomedata=0;
		}
		
		if(empty($lskincomedata))
		{
			$lskincomedata=0;
		}
		
		if(empty($spxsincomedata))
		{
			$spxsincomedata=0;
		}

	   $datalist[0]['cashje']= $hyincomedata+ $lskincomedata +$spxsincomedata;	

	   
		   
			//微信收入
		$map=array();
		
		$map['dtInsertTime']=array('between',array($today_begtime,$today_endtime));
		$map['lingqu_status']=1;		
		$map['wbid']=session('wbid');
		$map['pay_type']=1;
		
		$wx_sum_money =D('Productxs')->where($map)->sum('sum_sp_je');
		
		
	
		//支付宝收入，现金收入
		$map['pay_type']=2;
		$zfb_sum_money =D('Productxs')->where($map)->sum('sum_sp_je');
			
			
			
			//客户端现金收入
			$map['pay_type']=3;
			$cash_sum_money =D('Productxs')->where($map)->sum('sum_sp_je');
			
			if(empty($wx_sum_money))
			{
				$wx_sum_money=0;
			}
			
			if(empty($zfb_sum_money))
			{
				$zfb_sum_money=0;
			}
			
			if(empty($cash_sum_money))
			{
				$cash_sum_money=0;
			}
		   
		   
		   $datalist[0]['notcashje']= $wx_sum_money+ $zfb_sum_money + $cash_sum_money;		   
		   $datalist[0]['sumje']  = $datalist[0]['cashje'] + $datalist[0]['notcashje'];	   
		   $datalist[0]['daytime']= date('Y-m-d',time());
        

         //昨天现金收入
		   	   
           $map1=array();
           $map1['cTime']=array('BETWEEN',array($yestoday_begtime,$yestoday_endtime));
           $map1['WB_ID']=$wbid;
           $hyincomedata  = D('Hyaddmoneymx')->where($map1)->sum('je');

           $map2=array();
           $map2['cTime']=array('BETWEEN',array($yestoday_begtime,$yestoday_endtime));
           $map2['WB_ID']=$wbid;
           $lskincomedata = D('Lskaddmoneymx')->where($map2)->sum('je');

           $map3=array();
           $map3['Rq']=array('BETWEEN',array($yestoday_begtime,$yestoday_endtime));
           $map3['WB_ID']=$wbid;

           $spxsincomedata = D('Spxs')->where($map3)->sum('totalprice');
		   
		   	if(empty($hyincomedata))
			{
				$hyincomedata=0;
			}
			
			if(empty($lskincomedata))
			{
				$lskincomedata=0;
			}
			
			if(empty($spxsincomedata))
			{
				$spxsincomedata=0;
			}


           $datalist[1]['cashje']= $hyincomedata+ $lskincomedata +$spxsincomedata;	   
		   
         
           //昨天非现金收入
		   
		   		   		//微信收入
			$map=array();
			
			$map['dtInsertTime']=array('between',array($yestoday_begtime,$yestoday_endtime));
			$map['lingqu_status']=1;		
			$map['wbid']=session('wbid');
			$map['pay_type']=1;
			
			$wx_sum_money =D('Productxs')->where($map)->sum('sum_sp_je');
			
			
		
			//支付宝收入，现金收入
			$map['pay_type']=2;
			$zfb_sum_money =D('Productxs')->where($map)->sum('sum_sp_je');
			
			
			
			//客户端现金收入
			$map['pay_type']=3;
			$cash_sum_money =D('Productxs')->where($map)->sum('sum_sp_je');
			
			if(empty($wx_sum_money))
			{
				$wx_sum_money=0;
			}
			
			if(empty($zfb_sum_money))
			{
				$zfb_sum_money=0;
			}
			
			if(empty($cash_sum_money))
			{
				$cash_sum_money=0;
			}
		   
		   
		   $datalist[1]['notcashje']= $wx_sum_money+ $zfb_sum_money + $cash_sum_money;		   
		   $datalist[1]['sumje']  = $datalist[1]['cashje'] + $datalist[1]['notcashje'];	   
		   $datalist[1]['daytime']= date('Y-m-d',$onedaybegtime);
           
              
 
        $this->ajaxReturn($datalist);    
    }
	
	
	
	public function Getshourudetaillist()
    {

        $wbid     = I('post.wbid','','string');  
		
		
      	 
		
		$nowtime=date('Y-m-d H:i:s',time());		
		$startTime= date('Y-m-d 00:00:00',strtotime($nowtime));
        $endTime  = date('Y-m-d 23:59:59',strtotime($nowtime));
				
		   			  	   
		 for($i=0;$i<30;$i++)
         {
			 
           //获取每天的开始和结束时间
            $onedaybegtime= strtotime('-'.$i.'days',strtotime($startTime));
            $onedayendtime= strtotime('-'.$i.'days',strtotime($endTime));

            $onedaybegtime= date('Y-m-d H:i:s',$onedaybegtime);
            $onedayendtime= date('Y-m-d H:i:s',$onedayendtime);
			
			
           //现金收入
           $map1=array();
           $map1['cTime']=array('BETWEEN',array($onedaybegtime,$onedayendtime));
           $map1['WB_ID']=$wbid;
           $hyincomedata  = D('Hyaddmoneymx')->where($map1)->sum('je');

           $map2=array();
           $map2['cTime']=array('BETWEEN',array($onedaybegtime,$onedayendtime));
           $map2['WB_ID']=$wbid;
           $lskincomedata = D('Lskaddmoneymx')->where($map2)->sum('je');

           $map3=array();
           $map3['Rq']=array('BETWEEN',array($onedaybegtime,$onedayendtime));
           $map3['WB_ID']=$wbid;

           $spxsincomedata = D('Spxs')->where($map3)->sum('totalprice');
		   
		   
		   	if(empty($hyincomedata))
			{
				$hyincomedata=0;
			}
			
			if(empty($lskincomedata))
			{
				$lskincomedata=0;
			}
			
			if(empty($spxsincomedata))
			{
				$spxsincomedata=0;
			}
		   	   
		   $datalist[$i]['cashje']= $hyincomedata+ $lskincomedata +$spxsincomedata;
	
	
		   	//微信收入
			$map=array();			
			$map['time_notify']=array('between',array($onedaybegtime,$onedayendtime));
			$map['trade_status']=100;
            $map['refund_status']=0;	
			$map['wbid']=$wbid;
			
			
			$wx_sum_money =D('Wxpay')->where($map)->sum('receipt_amount');
		
					
		
			//支付宝收入		
			$zfb_sum_money =D('Zfbpay')->where($map)->sum('receipt_amount');
			//公众号收入
			
			$map=array();			
			$map['time_end']=array('between',array($onedaybegtime,$onedayendtime));
			$map['trade_status']=100;
            $map['refund_status']=0;	
			$map['wbid']=$wbid;	
			$gzh_sum_money =D('Gzhpay')->where($map)->sum('notify_total_fee');
			
						
			//客户端现金收入
			$map=array();			
			$map['dtInsertTime']=array('between',array($onedaybegtime,$onedayendtime));		
			$map['wbid']=$wbid;	
			$map['pay_type']=3;
			$cash_sum_money =D('Productxs')->where($map)->sum('sum_sp_je');
			

			
			
			
			
			if(empty($gzh_sum_money))
			{
				$gzh_sum_money=0;
			}
			
			if(empty($wx_sum_money))
			{
				$wx_sum_money=0;
			}
			
			if(empty($zfb_sum_money))
			{
				$zfb_sum_money=0;
			}
			
			if(empty($cash_sum_money))
			{
				$cash_sum_money=0;
			}	
 			

			
           $datalist[$i]['notcashje']= $wx_sum_money+ $zfb_sum_money + $cash_sum_money +$gzh_sum_money;	   
		   $datalist[$i]['sumje']  = $datalist[$i]['cashje'] + $datalist[$i]['notcashje'];	   
		   $datalist[$i]['daytime']= date('Y-m-d',strtotime($onedaybegtime));                            
         }   
		 
             	   
        $this->ajaxReturn($datalist);    
    }
	
	
	public function chongzhi()
   {
       $wbid     = I('post.wbid',0,'int'); 
       $hycardno     = I('post.hycardno','','string'); 
	  
	  




       $wbid     = 1086; 
       $hycardno     = '609801607'; 
       
    
	 
	 $response=D('WbInfo')->where('wbid=1086')->find();
	  
	 
	  
     
     $this->ajaxReturn($response);
   }
	  
	
		
}
