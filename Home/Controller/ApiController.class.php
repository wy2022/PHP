<?php
namespace Home\Controller;
class ApiController extends CommonController
{
	public function index()
	{
		
	}
    public function expshift()
    {//导出Excel

        $daterange   =I('get.daterange','','string');
        $jiaobanren  =I('get.jiaobanren','','string');     
        $map=array();   
        
        if(!empty($daterange))  
        {
           list($start,$end) = explode(' - ',$daterange);    
           $start = str_replace('/','-',$start);            
           $end = str_replace('/','-',$end);                
           $map['cTime'] = array('BETWEEN',array($start,$end));
        }
        
        if(!empty($jiaobanren))
        {
            $map['cName']=$jiaobanren;
        }
        $map['WB_ID']=session('wbid');
        $xlsName  = "shift";

        $xlsCell  = array(
        array('SyId','收银端'),
        array('cName','交班人'),
        array('cTime','交班时间'),
        array('inje','实交金额'),
		array('keepje','留给下班'),
        array('YjJe','应交金额'),
        array('TemCardJe','临时卡收入'),  
        array('Hyje','会员收入'), 
        array('Spje','商品收入'), 
		array('bz','备注'),
        );
		
         $xlsData  = D('Shift')->expShift($map);		 
         exportExcel($xlsName,$xlsCell,$xlsData);   
    }
	
	
    public function exphykaddmoney_detail()
    {//导出Excel

        $daterange   =I('get.daterange','','string');  
        $cardno   =I('get.cardno','','string');
      
        $map=array();   
         if(!empty($daterange))  
         {
           list($start,$end) = explode(' - ',$daterange);    
           $start = str_replace('/','-',$start);            
           $end = str_replace('/','-',$end);                
           $map['addmoney.cTime'] = array('BETWEEN',array($start,$end));
         }
        
        if(!empty($cardno))
        {
            $map['addmoney.HyCardNo']=$cardno;
        }
		$map['addmoney.WB_ID']=session('wbid');
        $xlsName  = "hykaddmoney_detail";

        $xlsCell  = array(
		array('SyId','收银端'),
        array('cardNo','卡号'),
        array('hylevel','会员等级'),
        array('hyname','姓名'),
        array('je','充值金额'),
        array('jlJe','奖励金额'),
        array('fqje','分期金额'),  
        array('FqCount','分期期数'), 
		array('cTime','时间'),  
        array('Operation','操作员'), 
        );
         $xlsData  = D('Hyaddmoneymx')->expHykaddmoney_detail($map);
         exportExcel($xlsName,$xlsCell,$xlsData); 
   
    }

    public function explskaddmoney_detail()
    {//导出Excel

        $daterange   =I('get.daterange1','','string');  
        $cardno   =I('get.cardno1','','string');
      
        $map=array();   
         if(!empty($daterange))  
         {
           list($start,$end) = explode(' - ',$daterange);    
           $start = str_replace('/','-',$start);            
           $end = str_replace('/','-',$end);                
           $map['cTime'] = array('BETWEEN',array($start,$end));
         }
        
        if(!empty($cardno))  
		{
			 $map['cardNo'] = array('LIKE','%'.$cardno.'%');
		}
		$map['WB_ID']=session('wbid');
        $xlsName  = "lskaddmoney_detail";

        $xlsCell  = array(
		array('SyId','收银端'),
        array('cardNo','卡号'),
        array('je','金额'),
		array('cTime','日期'),  
        array('Operation','收银员'), 
        );
         $xlsData  = D('Lskaddmoneymx')->explskaddmoney_detail($map);
         exportExcel($xlsName,$xlsCell,$xlsData); 
   
    }
    public function explskzhaoling_detail()
    {//导出Excel

        $daterange   =I('get.daterange2','','string');  
        $cardno   =I('get.cardno2','','string');
		    
        $map=array();   
         if(!empty($daterange))  
         {
           list($start,$end) = explode(' - ',$daterange);    
           $start = str_replace('/','-',$start);            
           $end = str_replace('/','-',$end);                
           $map['XjTime'] = array('BETWEEN',array($start,$end));
         }
		 
		 
        
        if(!empty($cardno))  
		{	
			$map['cardNo'] = array('LIKE','%'.$cardno.'%');
		}
		
		
		
		$map['WB_ID']=session('wbid');
        $xlsName  = "lskzhaoling_detail";

        $xlsCell  = array(
		array('SyId','收银端'),
        array('cardNo','卡号'),
        array('zlje','实找金额'),
		array('SjTime','日期'),  
        array('EndOperate','收银员'), 
        );
         $xlsData  = D('Lskshangjimx')->explskzhaoling_detail($map);
				 
         exportExcel($xlsName,$xlsCell,$xlsData); 
		 
    }

    public function expshangji()
    {//导出Excel

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
	 
			
				$xlsData = D('Hyshangjimx')->expHyShangjimxList($map);
				
							
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


				$xlsData = D('Lskshangjimx')->expAllLskShangjimxList($map);
						
			}

        $xlsName  = "shangjimx";

        $xlsCell  = array(
        array('cardLx','卡类型'),
        array('cardNo','卡号'),
        array('hydj','会员等级'),
        array('UserName','姓名'),
        array('sjLx','上机类型'),
        array('cpName','机器号'),  
        array('foregift','押金'), 
        array('yje','应收'), 
		array('je','实收'),
        array('qtje','其他金额'),
        array('ye','余额'),
        array('SjTime','上机时间'),
        array('XjTime','下机时间'),
        array('EndOperate','操作员'),  
        array('bz','备注'), 
        );

         exportExcel($xlsName,$xlsCell,$xlsData); 
   
    }
    public function expjifen()
    {//导出Excel

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
        $xlsName  = "jifen";

        $xlsCell  = array(
        array('syid','收银端'),
        array('HyCardNo','会员卡号'),
        array('hyname','姓名'),
        array('hydj','会员等级'),
        array('Integral','兑换积分'),
        array('Lx_2','兑换类型'),  
		array('sp','兑换物品'),
		array('cTime','时间'),  
		array('Operate','操作人'),

        );

        $xlsData  = D('Change')->expSpChangeinfo($map);		 		 
        exportExcel($xlsName,$xlsCell,$xlsData); 
   
    }

    public function expwxpay()
    {//导出Excel

        $min_amount = I('get.min_amount',0,'int');
		$max_amount =  I('get.max_amount',500,'int');
		$trade_status  = I('get.trade_status','','string');
		$sContent      = I('get.sContent','','string');
		$daterange     = I('get.daterange','','string');
          
            $map = array();
           
            if(!empty($trade_status))
            {
                $map['trade_status']=$trade_status;
            }

            if(!empty($sContent ))
            {
              $map['mx.CardNo|wx.post_order_no']= array('LIKE',"%$sContent%");
            }  

            $map['wx.wbid']=session('wbid');
        
            if(!empty($daterange))  
            {
                list($start,$end) = explode(' - ',$daterange); 
                $start = str_replace('/','-',$start);            
                $end = str_replace('/','-',$end);                
                $map['wx.time_post'] = array('BETWEEN',array($start,$end));
            }
            
              
            if($min_amount<=$max_amount)  
            {    
              $map['wx.total_fee'] = array('BETWEEN',array($min_amount,$max_amount));
            }
        $xlsName  = "wxpay";

        $xlsCell  = array(
        array('time_post','交易时间'),
        array('post_order_no','商户订单号'),
        array('transaction_id','流水账号'),
        array('s_status','交易状态'),
        array('total_fee','交易金额(元)'),
        array('CardNo','会员卡号'), 
        array('kcardlx','商品信息'), 
        array('operator_id','收银员ID'), 		
        );

         $xlsData  = D('Wxpay')->expWxPay($map,$flag_status);
         exportExcel($xlsName,$xlsCell,$xlsData); 
   
    }


    public function expalipay()
    {//导出Excel

        $min_amount = I('get.min_amount',0,'int');
		$max_amount =  I('get.max_amount',500,'int');
		$trade_status  = I('get.trade_status','','string');
		$sContent      = I('get.sContent','','string');
		$daterange     = I('get.daterange','','string');
          
            $map = array();
           
            if(!empty($trade_status))
            {
                $map['zfb.trade_status']=$trade_status;
            }

            if(!empty($sContent ))
            {
			   $map['mx.CardNo|zfb.Post_Order_no']= array('LIKE',"%$sContent%");
            }  

            $map['zfb.wbid']=session('wbid');
        
            if(!empty($daterange))  
            {
                list($start,$end) = explode(' - ',$daterange); 
                $start = str_replace('/','-',$start);            
                $end = str_replace('/','-',$end);                
                $map['zfb.time_post'] = array('BETWEEN',array($start,$end));
            }
            
              
            if($min_amount<=$max_amount)  
            {    
              $map['zfb.total_fee'] = array('BETWEEN',array($min_amount,$max_amount));
            }
        $xlsName  = "alipay";

        $xlsCell  = array(
        array('time_post','交易时间'),
        array('Post_Order_no','商户订单号'),
        array('return_trade_no','流水账号'),
        array('s_status','交易状态'),
        array('receipt_amount','交易金额(元)'),
        array('CardNo','会员卡号'), 
        array('buyer_login_id','买家支付账号'), 
		array('goods_name','商品信息'), 
        array('operator_id','收银员ID'), 		
        );

         $xlsData  = D('Zfbpay')->expalipay($map,$flag_status);
         exportExcel($xlsName,$xlsCell,$xlsData); 
   
    }
    public function expgzhpay()
    {//导出Excel

        $min_amount = I('get.min_amount',0,'int');
		$max_amount =  I('get.max_amount',500,'int');
		$trade_status  = I('get.trade_status','','string');
		$sContent      = I('get.sContent','','string');
		$daterange     = I('get.daterange','','string');
          
            $map = array();
           
            if(!empty($trade_status))
            {
                $map['trade_status']=$trade_status;
            }

            if(!empty($sContent ))
            {
               $map['hycardno|post_order_no']= array('LIKE',"%$sContent%");
            }    

            $map['wbid']=session('wbid');
        
            if(!empty($daterange))  
            {
                list($start,$end) = explode(' - ',$daterange); 
                $start = str_replace('/','-',$start);            
                $end = str_replace('/','-',$end);                
                $map['time_post'] = array('BETWEEN',array($start,$end));
            }
            
              
            if($min_amount<=$max_amount)  
            {    
              $map['total_fee'] = array('BETWEEN',array($min_amount,$max_amount));
            }
        $xlsName  = "Gzhpay";

        $xlsCell  = array(
        array('time_post','交易时间'),
        array('post_order_no','商户订单号'),
        array('transaction_id','流水账号'),
        array('s_status','交易状态'),
        array('notify_total_fee','交易金额(元)'),
        array('hycardno','会员卡号'), 
        array('hyname','会员姓名'), 		
        );

         $xlsData  = D('Gzhpay')->expgzhpay($map,$flag_status);
         exportExcel($xlsName,$xlsCell,$xlsData); 
   
    }
   public function expjchtj()
    {//导出Excel

        $daterange   =I('get.daterange','','string');
        $jch_position  =I('get.jch_position','','string');
		$post_order_no   =I('get.cardno','','string');



        $map=array();   
        
        if(!empty($daterange))  
		{
		  list($start,$end) = explode(' - ',$daterange);    
		  $start = str_replace('/','-',$start);            
		  $end = str_replace('/','-',$end);                
		  $map['dtInsertTime'] = array('BETWEEN',array($start,$end));
		}
        
        if($jch_position=='1')
		{
			$map['jch_type']=1;
		}
		else if($jch_position=='2')
		{
			$map['jch_type']=0;
		}
		
		if(!empty($post_order_no))
		{
		  $map['post_order_no']=array('LIKE','%'.$post_order_no.'%');
		}  
        $map['wbid']=session('wbid');
        $xlsName  = "jchtj";

        $xlsCell  = array(
        array('dtInsertTime','操作时间'),
        array('post_order_no','订单号'),
        array('jch_type_caption','单据类型'),
        array('info','商品信息'),
		array('operate','操作人'),
        array('bz','备注'),
        );
		

         $xlsData  = D('Productjch')->expjchtj($map);
		 
		 
         exportExcel($xlsName,$xlsCell,$xlsData); 
   
    }
   public function expxstj()
    {//导出Excel

        $daterange   =I('get.daterange1','','string');
		$post_order_no   =I('get.cardno1','','string');

	  

        $map=array();   
        
        if(!empty($daterange))  
		{
		  list($start,$end) = explode(' - ',$daterange);    
		  $start = str_replace('/','-',$start);            
		  $end = str_replace('/','-',$end);                
		  $map['dtInsertTime'] = array('BETWEEN',array($start,$end));
		}
        
		
		if(!empty($post_order_no))
		{
		  $map['post_order_no']=array('LIKE','%'.$post_order_no.'%');
		}  
        $map['wbid']=session('wbid');
        $xlsName  = "xstj";

        $xlsCell  = array(
        array('dtInsertTime','操作时间'),
        array('post_order_no','订单号'),
        array('info','商品信息'),
		array('sum_sr_je','操作时间'),
        array('sum_sp_je','商品金额'),
        array('sum_zl_je','找零金额'),
		array('operate','操作人'),
        array('bz','备注'),
        );
		

         $xlsData  = D('Productxs')->expxstj($map);
		
		 
         exportExcel($xlsName,$xlsCell,$xlsData); 
   
    }
   public function expsxjtj()
    {//导出Excel

        $daterange   =I('get.daterange2','','string');
		$post_order_no   =I('get.cardno2','','string');

	  

        $map=array();   
        
        if(!empty($daterange))  
		{
		  list($start,$end) = explode(' - ',$daterange);    
		  $start = str_replace('/','-',$start);            
		  $end = str_replace('/','-',$end);                
		  $map['dtInsertTime'] = array('BETWEEN',array($start,$end));
		}
        
		
		if(!empty($post_order_no))
		{
		  $map['post_order_no']=array('LIKE','%'.$post_order_no.'%');
		}  
        $map['wbid']=session('wbid');
        $xlsName  = "sxjtj";

        $xlsCell  = array(
        array('dtInsertTime','操作时间'),
        array('post_order_no','订单号'),
        array('shangxia_type','单据类型'),
		array('info','上下架信息'),
		array('operate','操作人'),
        array('bz','备注'),
        );
		

         $xlsData  = D('Productsxj')->expsxjtj($map);
         exportExcel($xlsName,$xlsCell,$xlsData); 
   
    }
	
   public function exppdtj()
    {//导出Excel

        $daterange   =I('get.daterange3','','string');
		$post_order_no   =I('get.cardno3','','string');

	  

        $map=array();   
        
        if(!empty($daterange))  
		{
		  list($start,$end) = explode(' - ',$daterange);    
		  $start = str_replace('/','-',$start);            
		  $end = str_replace('/','-',$end);                
		  $map['dtInsertTime'] = array('BETWEEN',array($start,$end));
		}
        
		
		if(!empty($post_order_no))
		{
		  $map['post_order_no']=array('LIKE','%'.$post_order_no.'%');
		}  
        $map['wbid']=session('wbid');
        $xlsName  = "pdtj";

        $xlsCell  = array(
        array('dtInsertTime','操作时间'),
        array('post_order_no','订单号'),
        array('position','单据类型'),
		array('info','商品信息'),
		array('operate','操作人'),
        array('bz','备注'),
        );
		

         $xlsData  = D('Productpd')->exppdtj($map);
         exportExcel($xlsName,$xlsCell,$xlsData); 
   
    }
   public function expjbtj()
    {//导出Excel

        $daterange   =I('get.daterange4','','string');
		$post_order_no   =I('get.cardno4','','string');

	  

        $map=array();   
        
        if(!empty($daterange))  
		{
		  list($start,$end) = explode(' - ',$daterange);    
		  $start = str_replace('/','-',$start);            
		  $end = str_replace('/','-',$end);                
		  $map['dtInsertTime'] = array('BETWEEN',array($start,$end));
		}
        
		
		if(!empty($post_order_no))
		{
		  $map['post_order_no']=array('LIKE','%'.$post_order_no.'%');
		}  
        $map['wbid']=session('wbid');
        $xlsName  = "pdtj";

        $xlsCell  = array(
        
        array('post_order_no','订单号'),
		array('info','商品信息'),
		array('dtInsertTime','时间'),
		array('operate','操作人'),
        array('bz','备注'),
        );
		

         $xlsData  = D('Productjb')->expjbtj($map);
         exportExcel($xlsName,$xlsCell,$xlsData); 
   
    }
}
