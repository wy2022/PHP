<?php
namespace Home\Controller;
use Think\Controller;
class GzhController extends Controller 
{   
  	public function gzhpay()
    {
      $sessionid=$_COOKIE['SessionID'];  
      $ajson=getloginuserinfo( $sessionid);
      $aUserInfo=json_decode($ajson,true);
      $wbid=$aUserInfo['userinfo']['wb_id']; 
      session('wbid',$wbid);

      checkSessionTimeOut();
	    $res=D('Gzhpay')->getGzhPayRecordyWbid($wbid);   
      $this->display();        
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
          $val['notify_total_fee']= sprintf("%.2f", $val['notify_total_fee']);      
        }

        $response->rows   = $gzhpaydata['list'] ;
 
        $this->ajaxReturn($response);
    
    }

   public function chongzhi()
   {
       $wbid     = I('post.wbid',0,'int'); 
       $hycardno     = I('post.hycardno','','string'); 
	  
   
       
      $response=D('HyJl')->getMoneyRecordsbyWbIdAndHyCardNo($wbid,$hycardno);
     $this->ajaxReturn($response);
   }



    public function chongzhi3()
   {
      $wbid     = I('post.wbid',0,'int'); 
      $hycardno     = I('post.hycardno','','string'); 
      $wbid     = 1086; 
      $hycardno     = '609801607';     
      $response=D('HyJl')->getMoneyRecordsbyWbIdAndHyCardNo($wbid,$hycardno);
      $this->ajaxReturn($response);
   }

  public function chongzhi2()
   {
        $wbid     = I('post.wbid',0,'int'); 
        $hycardno = I('post.hycardno','','string'); 
        $je       = I('post.je','','string'); 


       
      $response=D('HyJl')->getMoneyRecordsbyWbIdAndHyCardNoAndJe($wbid,$hycardno,$je);
      $this->ajaxReturn($response);
   }






    public function getgzhpayinfo()
    {
      checkSessionTimeOut();
        if(IS_AJAX)
        {
 
            $page          = I('post.hiddenNowPage',1,'int');        
            $min_amount    = I('post.min_amount',0,'float');
            $max_amount    = I('post.max_amount',100,'float');
            $trade_status  = I('post.trade_status',0,'int');
            $hycardno      = I('post.hycardno','','string'); 
            $daterange     = I('post.daterange','','string');//获取交班时间
            $post_order_no = I('post.post_order_no','','string');
          
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

            if(!empty($hycardno ))
            {
               $map['hycardno']= array('LIKE',"%$hycardno%");
            }    

            if(!empty($post_order_no ))
            {
              $map['post_order_no']=array('LIKE',"%$post_order_no%");
            }  
     
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
                  $map['notify_total_fee'] = array('BETWEEN',array($min_amount,$max_amount));
                } 

                  

                //    if($min_amount<=$max_amount)  
                // {           
                //   $map['notify_total_fee'] = array('BETWEEN',array($min_amount,$max_amount));
                // }  

            $count=D('Gzhpay')->getGzhPaycount($map);

           
             
            $rows=10;
            $sql_page=ceil($count/$rows);   
            
            if($page<=0)   $page=1;       
            if($page>$sql_page) 
            {
              $page=1; 
            }else
            {

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
            foreach ($gzhpaydata['list'] as &$val)
            {                             
              $val['time_post']= date('Y-m-d H:i:s',strtotime($val['time_post']));      
            }

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
 		
}
