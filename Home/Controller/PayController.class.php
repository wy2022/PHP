<?php
namespace Home\Controller;
class PayController extends CommonController 
{
 
   public function  checkqx()
   {
	   $allpay_qx=D('WbInfo')->where(array('WBID'=>session('wbid')))->getField('allpay_qx');
	   if($allpay_qx==2)
	   {
		 return  false;
					
	   }else
       {
		   return  true;
	   } 		   
   }

    public function wxpay()
    { 
		$result= $this->checkqx();
		if($result)
		{
			  $this->display();
		}
		else
		{
			 echo  '没有访问权限！';
			 return;
		}		                    
    }


    public function getwxpayinfo()
    {
        if(IS_AJAX)
        {
   
            $page = I('get.page',1,'int');
            $rows = I('get.rows',20,'int');
            $sord = I('get.sord','','string')?:'desc';
            $sidx = I('get.sidx','','string')?:'time_post';

            $min_amount = I('get.min_amount',0,'int');
            $max_amount =  I('get.max_amount',500,'int');
            // $trade_status  = I('get.trade_status','','string');
			
			$sOrderNo      = I('get.sOrderNo','','string'); 
            $sHyCardNo     = I('get.sHyCardNo','','string');  		
            $daterange     = I('get.daterange','','string');
           
            $map = array();         
       
		    $map['wx.trade_status']=100;
            $map['wx.refund_status']=0;
            $map['wx.cancel_status']=0;
			
			
		     if(!empty($sHyCardNo ))
            {
              $map1['CardNo']= array('LIKE',"%$sHyCardNo%");		
              $map1['wb_id']= session('wbid');	 
			  $order_no_list=D('WxMx')->where($map1)->getField('QtZfNo',true);
              $map['wx.post_order_no']=array('IN',$order_no_list);			  
            } 	
				  
            if(!empty($sOrderNo ))
            {
              $map['wx.post_order_no']= array('LIKE',"%$sOrderNo%");			  
            }  

            $map['wx.wbid']=session('wbid');
        
            if(!empty($daterange))  
            {
                list($start,$end) = explode(' - ',$daterange);    //explode() 函数把字符串以"-"为标志分割为数组，包含两个元素，赋值给list，调用时候直接$start,$end
                $start = str_replace('/','-',$start);            //把分割的第一个数据里的"/"用"-"号替换
                $end = str_replace('/','-',$end);                //把分割的第二个数据里的"/"用"-"号替换
                $map['wx.time_post'] = array('BETWEEN',array($start,$end));//返回数据array(值，值)---第一个值是字符串'between'，第二个值是一个数组
            }
                        
            if($min_amount<=$max_amount)  
            {    
              $map['wx.total_fee'] = array('BETWEEN',array($min_amount,$max_amount));
            } 
                    
            $count=D('Wxpay')->getWxPaycount($map);
			
			
            $sql_page=ceil($count/$rows);              
            if($page<=0)   $page=1;       
            if($page>$sql_page) 
            {
              $page=1; 
            }
			
            $wxpaydata = D('Wxpay')->getWxPayList($map,$page,$rows,$flag_status);   
			
		

          
         // 2.重新包装数据，并将所有数据放进response
            $response = new \stdClass();
            $response->count       = $wxpaydata['count'];//返回的数组的第一个字段记录总条数
            $response->nowPage     = $page ;              //每页显示的记录数目               
            $response->total       = ceil($wxpaydata['count'] / $rows);          
            $response->pay_sum     = $wxpaydata['pay_sum'] ;
            $response->refund_sum  = $wxpaydata['refund_sum'] ;
           
            $response->rows        = $wxpaydata['list'] ;
            $this->ajaxReturn($response);
        }
    } 




    public function alipay()
    { 
	    $result= $this->checkqx();
		if($result)
		{
			  $this->display();
		}
		else
		{
			echo  '没有访问权限！';
			return;
		}                
    }

    public function getalipayinfo()
    {
        
        if(IS_AJAX)
        {       
 
            $page = I('get.page',1,'int');
            $rows = I('get.rows',20,'int');
            $sord = I('get.sord','','string')?:'desc';
            $sidx = I('get.sidx','','string')?:'time_post';

            $min_amount = I('get.min_amount',0,'float');
            $max_amount =  I('get.max_amount',500,'float');
            // $trade_status  = I('get.trade_status','','string');
            // $sContent      = I('get.sContent','','string');
            // $post_order_no = I('get.post_order_no','','string');
			$sOrderNo      = I('get.sOrderNo','','string'); 
            $sHyCardNo     = I('get.sHyCardNo','','string');
            $daterange     = I('get.daterange','','string');//获取交班时间

            $map = array();

           

            // if(!empty($sContent ))
            // {
               // $map['mx.CardNo|zfb.Post_Order_no']= array('LIKE',"%$sContent%");
            // }
			
		    if(!empty($sHyCardNo ))
            {
              $map1['CardNo']= array('LIKE',"%$sHyCardNo%");		
              $map1['wb_id']= session('wbid');	 
			  $order_no_list=D('ZfbAddMoneyMx')->where($map1)->getField('QtzfNo',true);
              $map['zfb.Post_Order_no']=array('IN',$order_no_list);			  
            } 	
				  
            if(!empty($sOrderNo ))
            {
              $map['zfb.Post_Order_no']= array('LIKE',"%$sOrderNo%");			  
            }  

			

            // if(!empty($post_order_no ))
            // {
            //   $map['zfb.Post_Order_no']=array('LIKE',"%$post_order_no%");
            // }  

            $map['zfb.wbid']=session('wbid');
            if(!empty($daterange))  
            {
                list($start,$end) = explode(' - ',$daterange);    //explode() 函数把字符串以"-"为标志分割为数组，包含两个元素，赋值给list，调用时候直接$start,$end
                $start = str_replace('/','-',$start);            //把分割的第一个数据里的"/"用"-"号替换
                $end = str_replace('/','-',$end);                //把分割的第二个数据里的"/"用"-"号替换
                $map['zfb.time_post'] = array('BETWEEN',array($start,$end));//返回数据array(值，值)---第一个值是字符串'between'，第二个值是一个数组
            }
                 

                if($min_amount<=$max_amount)  
                {           
                    $map['zfb.receipt_amount'] = array('BETWEEN',array($min_amount,$max_amount));//返回数据array(值，值)---第一个值是字符串'between'，第二个值是一个数组
                } 


          //   处理分页               
            $count=D('Zfbpay')->getZfbPaycount($map);

            // $rows=10;
            $sql_page=ceil($count/$rows);   
            
            if($page<=0)   $page=1;       
            if($page>$sql_page) 
            {
              $page=1; 
            }
             

            $zfbpaydata = D('Zfbpay')->getZfbPaycountList($map,$page,$rows,$flag_status);             
            
            $response = new \stdClass();
            $response->count       = $zfbpaydata['count'];//返回的数组的第一个字段记录总条数
            $response->nowPage     = $page ;              //每页显示的记录数目               
            $response->total       = ceil($zfbpaydata['count'] / $rows);          
            $response->pay_sum     = $zfbpaydata['pay_sum'] ;
            $response->refund_sum  = $zfbpaydata['refund_sum'] ;
       
            $response->rows   = $zfbpaydata['list'] ;
            $this->ajaxReturn($response);
        }
    } 















    public function order()
    {

        $wbid     = I('post.WbAccount',0,'int');   
        $wxid     = I('post.wxid','','string');   
        $page  = I('post.hiddenNowPage',1,'int'); 

   
        // $wxid='omCcpsxi3y2DBZOGgMjLeLakxZcQ';
        // $wbid=5; 


        $map=array();
        $map['wbid']=$wbid;
        $map['wxid']=$wxid;
        $map['trade_status']=100; 


        $count=D('Gzhpay')->getGzhPaycount($map);
       

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

        $gzhpaydata=D('Gzhpay')->fnQueryOrderList($map,$page,$rows);

        $response = new \stdClass();
        $response->count       = $gzhpaydata['count'];//返回的数组的第一个字段记录总条数
        $response->nowPage     = $page ;              //每页显示的记录数目               
        $response->total       = ceil($gzhpaydata['count'] / $rows);          
   
            // $response->sumtx       = $zfbpaydata['sumtx'] ;
          

        foreach ($gzhpaydata['list'] as &$val)
        {                             
          $val['time_start']= date('Y-m-d H:i:s',strtotime($val['time_start']));      
        }

        $response->rows   = $gzhpaydata['list'] ;
 
        $this->ajaxReturn($response);
  
    }

   public function chongzhi()
   {

        $wbid     = I('post.webname',0,'int');  
       

        $flag=-1;
        $bEnable=D('Ini')->getAddMoneyToMoneyStatus($wbid);
        if(!empty($bEnable))
        {
          $flag=0;
        }
        else
        {
          $resultdata  =D('HyDate')->getHyDateValid($wbid); 
          if(!empty($resultdata))
          {
            $flag=1;
            $chongzhidata=$resultdata;
          }
          else
          {
            $resultdata=D('HyJl')->getMoneyRecordsbyWbId($wbid);
            if(!empty($resultdata))
            {
              $flag=2;
              $chongzhidata=$resultdata;
            }                 
          }  
        }  
       
          
        if(($flag==1)||($flag==2))
        {
          $response = new \stdClass();
          $response->count       = $chongzhidata['count'];//返回的数组的第一个字段记录总条数      
          $response->flag        = $flag;//返回的数组的第一个字段记录总条数               
          foreach ($chongzhidata['list'] as &$val)
          {                             
            $val['time_start']= date('Y-m-d H:i:s',strtotime($val['time_start']));      
          }

          $response->rows   = $chongzhidata['list'] ;
        }
        else if(($flag==0) || ($flag==-1))
        {
           $response = new \stdClass();
           $response->flag = $flag;
        }
     
        $this->ajaxReturn($response);
   }




    public function gzhpay()
    {
      	$result= $this->checkqx();
		if($result)
		{
			$this->display();
		}
		else
		{
			echo  '没有访问权限！';
			return;
		}        
    }


    public function getgzhpayinfo()
    {
    
        if(IS_AJAX)
        {
            $page = I('get.page',1,'int');
            $rows = I('get.rows',20,'int');          

            $sord = I('get.sord','','string')?:'desc';
            $sidx = I('get.sidx','','string')?:'time_post';

            $min_amount = I('get.min_amount',0,'int');
            $max_amount =  I('get.max_amount',500,'int');
            $trade_status  = I('get.trade_status','','string');
            $sContent      = I('get.sContent','','string');
            // $post_order_no = I('get.post_order_no','','string');
            $daterange     = I('get.daterange','','string');//获取交班时间

            $flag_status=0;      
            $map = array();
            if(!empty($trade_status))
            {
                if($trade_status=='100')
                {
                  $map['trade_status']=100;
                  $map['refund_status']=0;
                  $map['cancel_status']=0;
                  $flag_status=1;
                }
                else if($trade_status=='2')
                {
                  $map['trade_status']=0;
                  $map['refund_status']=0;
                  $map['cancel_status']=1;
                  $flag_status=2;
                }
                else if($trade_status=='3')  
                {
                  $map['trade_status']=100;
                  $map['refund_status']=1;
                  $map['cancel_status']=0;
                  $flag_status=3;
                } 
            }

            if(!empty($sContent ))
            {
               $map['hycardno|post_order_no']= array('LIKE',"%$sContent%");
            }    

            // if(!empty($post_order_no ))
            // {
            //   $map['post_order_no']=array('LIKE',"%$post_order_no%");
            // }  
     
            $map['wbid']=session('wbid');
                  
            if(!empty($daterange))  
            {
                list($start,$end) = explode(' - ',$daterange);    //explode() 函数把字符串以"-"为标志分割为数组，包含两个元素，赋值给list，调用时候直接$start,$end
                $start = str_replace('/','-',$start);            //把分割的第一个数据里的"/"用"-"号替换
                $end = str_replace('/','-',$end);                //把分割的第二个数据里的"/"用"-"号替换
                $map['time_post'] = array('BETWEEN',array($start,$end));//返回数据array(值，值)---第一个值是字符串'between'，第二个值是一个数组
            }
                    

    
                if($min_amount<=$max_amount)  
                {           
                  $map['total_fee'] = array('BETWEEN',array($min_amount,$max_amount));
                } 

                  

            $count=D('Gzhpay')->getGzhPaycount($map);         
            $sql_page=ceil($count/$rows);   
            
            if($page<=0)   $page=1;       
            if($page>$sql_page) 
            {
              $page=1; 
            } 

            $gzhpaydata = D('Gzhpay')->getGzhPayList($map,$page,$rows,$flag_status);   
            
          

            $count     = $gzhpaydata['count'];
            $pay_sum   = $gzhpaydata['pay_sum'];
            $refund_sum= $gzhpaydata['refund_sum'];
            // $sumtx     = $gzhpaydata['sumtx'];
          
            $response = new \stdClass();
            $response->count     = $gzhpaydata['count'];//返回的数组的第一个字段记录总条数
            $response->nowPage     = $page ;              //每页显示的记录数目               
            $response->total       = ceil($gzhpaydata['count'] / $rows);          
            $response->pay_sum     = $gzhpaydata['pay_sum'] ;
            $response->refund_sum  = $gzhpaydata['refund_sum'] ;
            // $response->sumtx       = $gzhpaydata['sumtx'] ;
            // foreach ($gzhpaydata['list'] as &$val)
            // {                             
            //   $val['time_post']= date('Y-m-d H:i:s',strtotime($val['time_post']));      
            // }

            $response->rows   = $gzhpaydata['list'] ;

            $this->ajaxReturn($response);
        }
    }





    public function selfinfo()
   {

        $wbid     = I('post.webname',0,'int');  
        $wxid     = I('post.wxid','','string');  
       
        $hyinfo=D('HyInfo')->getOneHyInfoByWbidAndWxid($wbid,$wxid);

        $hyinfo['ye']= $hyinfo['Jlje']+$hyinfo['surplus'];

        $response = new \stdClass();
        // $response->info     = $hyinfo;  

        $response->rows   = $hyinfo ;           
        $this->ajaxReturn($response);
   } 



    public function dingding()
    {
 
      $this->display();                  
    }



    public function getdingdinginfo()
    {
        if(IS_AJAX)
        {
   
            $page = I('get.page',1,'int');
            $rows = I('get.rows',20,'int');
            $sord = I('get.sord','','string')?:'desc';
            $sidx = I('get.sidx','','string')?:'dtInsertTime';

            $min_amount = I('get.min_amount',0,'int');
            $max_amount =  I('get.max_amount',500,'int');
            // $trade_status  = I('get.trade_status','','string');
			
			$sOrderNo      = I('get.sOrderNo','','string'); 
            // $sHyCardNo     = I('get.sHyCardNo','','string');  		
            $daterange     = I('get.daterange','','string');
           
            $map = array();         

			

				  
            if(!empty($sOrderNo ))
            {
              $map['dingding.post_order_no']= array('LIKE',"%$sOrderNo%");			  
            }  

            $map['dingding.wbid']=session('wbid');
        
            if(!empty($daterange))  
            {
                list($start,$end) = explode(' - ',$daterange);    //explode() 函数把字符串以"-"为标志分割为数组，包含两个元素，赋值给list，调用时候直接$start,$end
                $start = str_replace('/','-',$start);            //把分割的第一个数据里的"/"用"-"号替换
                $end = str_replace('/','-',$end);                //把分割的第二个数据里的"/"用"-"号替换
                $map['dingding.dtInsertTime'] = array('BETWEEN',array($start,$end));//返回数据array(值，值)---第一个值是字符串'between'，第二个值是一个数组
            }
                        
            if($min_amount<=$max_amount)  
            {    
              $map['dingding.chongje'] = array('BETWEEN',array($min_amount,$max_amount));
            } 
                    
					



        
  
            $count=D('Dingding')->getDingdingcount($map);
			

            $sql_page=ceil($count/$rows);              
            if($page<=0)   $page=1;       
            if($page>$sql_page) 
            {
              $page=1; 
            }
			
            $dingdingdata = D('Dingding')->getDingdingList($map,$page,$rows,$flag_status);   
			
		

          
         // 2.重新包装数据，并将所有数据放进response
            $response = new \stdClass();
            $response->count       = $dingdingdata['count'];//返回的数组的第一个字段记录总条数
            $response->nowPage     = $page ;              //每页显示的记录数目               
            $response->total       = ceil($dingdingdata['count'] / $rows);          

           
            $response->rows        = $dingdingdata['list'] ;
            $this->ajaxReturn($response);
        }
    } 



}