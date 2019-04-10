<?php
namespace Home\Controller;
use Think\Controller;
class ZhangController extends Controller 
{   
	public function index()
	{
		$this->display();
	}	
	

	
	public function payZfb()
	{
	
	    $moren_month=date('Y-m',time());
		$this->assign('moren_month',$moren_month);
		
		$moren_year=date('Y',time());
		$this->assign('moren_year',$moren_year);
		
		$this->display();
	}
	public function payWx()
	{
		$this->display();
	}
	public function payKhdWx()
	{
		$this->display();
	}
	
	

    public function getwxpayinfo()
    {
        if(IS_AJAX)
        {
            
            $page = I('get.page',1,'int');
            $rows = I('get.rows',20,'int');
            $sord = I('get.sord','','string')?:'desc';
            $sidx = I('get.sidx','','string')?:'time_post';
		
			$sOrderNo      = I('get.sOrderNo','','string'); 
            $sHyCardNo     = I('get.sHyCardNo','','string');  		
            $daterange     = I('get.daterange','','string');
			$wbname     = I('get.wbname','','string');
           
            $map = array();           
		    $map['wx.trade_status']=100;
            $map['wx.refund_status']=0;
            $map['wx.cancel_status']=0;
			
			 
		    if(!empty($sHyCardNo ))
            {
              $map1['CardNo']= array('LIKE',"%$sHyCardNo%");		
			  $order_no_list=D('WxMx')->where($map1)->getField('QtZfNo',true);
              $map['wx.post_order_no']=array('IN',$order_no_list);			  
            } 	
				  
            if(!empty($sOrderNo ))
            {
              $map['wx.post_order_no']= array('LIKE',"%$sOrderNo%");			  
            }  

            // $map['wx.wbid']=session('wbid');
        
            if(!empty($daterange))  
            {
                list($start,$end) = explode(' - ',$daterange);    //explode() 函数把字符串以"-"为标志分割为数组，包含两个元素，赋值给list，调用时候直接$start,$end
                $start = str_replace('/','-',$start);            //把分割的第一个数据里的"/"用"-"号替换
                $end = str_replace('/','-',$end);                //把分割的第二个数据里的"/"用"-"号替换
                $map['wx.time_post'] = array('BETWEEN',array($start,$end));//返回数据array(值，值)---第一个值是字符串'between'，第二个值是一个数组
            }
			
			
            if(!empty($wbname ))
            {
				$wbid=D('WbInfo')->where(array('WbName'=>$wbname))->getField('WBID');
                $map['wx.wbid']=$wbid;
				
            }            
   
            $count=D('Wxpay')->getWxPaycount2($map);
			
            $sql_page=ceil($count/$rows);              
            if($page<=0)   $page=1;       
            if($page>$sql_page) 
            {
              $page=1; 
            }
				 
            $wxpaydata = D('Wxpay')->getWxPayList2($map,$page,$rows,$flag_status);   
			
			 
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

	
	


    public function getalipayinfo()
    {
        
        if(IS_AJAX)
        {       
 
            $page = I('get.page',1,'int');
            $rows = I('get.rows',20,'int');
            $sord = I('get.sord','','string')?:'desc';
            $sidx = I('get.sidx','','string')?:'time_post';

            // $trade_status  = I('get.trade_status','','string');
            // $sContent      = I('get.sContent','','string');
            // $post_order_no = I('get.post_order_no','','string');
			$sOrderNo      = I('get.sOrderNo','','string'); 
            $sHyCardNo     = I('get.sHyCardNo','','string');
            $daterange     = I('get.daterange','','string');//获取交班时间
			$wbname     = I('get.wbname','','string');

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

            // $map['zfb.wbid']=session('wbid');
            if(!empty($daterange))  
            {
                list($start,$end) = explode(' - ',$daterange);    //explode() 函数把字符串以"-"为标志分割为数组，包含两个元素，赋值给list，调用时候直接$start,$end
                $start = str_replace('/','-',$start);            //把分割的第一个数据里的"/"用"-"号替换
                $end = str_replace('/','-',$end);                //把分割的第二个数据里的"/"用"-"号替换
                $map['zfb.time_post'] = array('BETWEEN',array($start,$end));//返回数据array(值，值)---第一个值是字符串'between'，第二个值是一个数组
            }
                 
			 if(!empty($wbname ))
            {
				$wbid=D('WbInfo')->where(array('WbName'=>$wbname))->getField('WBID');
                $map['wbid']=$wbid;
				
            }


          //   处理分页               
            $count=D('Zfbpay')->getZfbPaycount2($map);

            // $rows=10;
            $sql_page=ceil($count/$rows);   
            
            if($page<=0)   $page=1;       
            if($page>$sql_page) 
            {
              $page=1; 
            }
             

            $zfbpaydata = D('Zfbpay')->getZfbPaycountList2($map,$page,$rows,$flag_status);             
            
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

	/*
    public function ceshi()
	{
		//$data=getOneDayShouru();
		//echo  json_encode($data);
		$map=array();
		$map['WBID'] = array('between',array('1501','2100'));
		$list=D('WbInfo')->where($map)->select();
	//	echo json_encode($list);
		//return ;
		$result=true;
		D()->startTrans();
		foreach($list as &$val)
		{
			$role_perm= $val['role_perm'].',319';
			if(D('WbInfo')->where(array('WBID'=>$val['WBID']))->setField('role_perm',$role_perm)===false)
			{
				$result=false;
			}	
		}
		if($result)
		{
			D()->commit();
			$data['status']=1;
		}
		else
        {
			D()->rollback();
			$data['status']=-1;
		}			
		echo  json_encode($data);
		
	}
	*/
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
			



	
            $map['wb_id']=244;		
               
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

             // if(!empty($month))  
			// {
			 // $map['Tm']=$month;
			// }
			



	
            $map['wb_id']=244;		
               
         //   $count=D('Tongji')->getTongjilist_count_day($map);

           // $sql_page=ceil($count/$rows);   
            
        
			 

            $zfbpaydata = D('Tongji')->getTongjilist_day($map,$page,$rows,$month);  
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
    public function getgzhpayinfo()
    {
    
        if(IS_AJAX)
        {
            $page = I('get.page',1,'int');
            $rows = I('get.rows',20,'int');          

            $sord = I('get.sord','','string')?:'desc';
            $sidx = I('get.sidx','','string')?:'time_post';

            $trade_status  = I('get.trade_status','','string');
            $sContent      = I('get.sContent','','string');
            // $post_order_no = I('get.post_order_no','','string');
            $daterange     = I('get.daterange','','string');//获取交班时间
			$wbname     = I('get.wbname','','string');

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

			 if(!empty($wbname ))
            {
				$wbid=D('WbInfo')->where(array('WbName'=>$wbname))->getField('WBID');
                $map['wbid']=$wbid;
				
            }
			

            if(!empty($sContent ))
            {
               $map['hycardno|post_order_no']= array('LIKE',"%$sContent%");
            }    

            // if(!empty($post_order_no ))
            // {
            //   $map['post_order_no']=array('LIKE',"%$post_order_no%");
            // }  
     
            // $map['wbid']=session('wbid');
                  
            if(!empty($daterange))  
            {
                list($start,$end) = explode(' - ',$daterange);    //explode() 函数把字符串以"-"为标志分割为数组，包含两个元素，赋值给list，调用时候直接$start,$end
                $start = str_replace('/','-',$start);            //把分割的第一个数据里的"/"用"-"号替换
                $end = str_replace('/','-',$end);                //把分割的第二个数据里的"/"用"-"号替换
                $map['time_post'] = array('BETWEEN',array($start,$end));//返回数据array(值，值)---第一个值是字符串'between'，第二个值是一个数组
            }
                    

    


                  

            $count=D('Gzhpay')->getGzhPaycount2($map);

    
             
            // $rows=10;
            $sql_page=ceil($count/$rows);   
            
            if($page<=0)   $page=1;       
            if($page>$sql_page) 
            {
              $page=1; 
            } 

            $gzhpaydata = D('Gzhpay')->getGzhPayList3($map,$page,$rows,$flag_status);   
            
          

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


		
}
