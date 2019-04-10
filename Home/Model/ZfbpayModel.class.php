<?php
    namespace Home\Model;
    use Think\Model;
    class ZfbpayModel extends Model 
    {
        protected $tableName = 'WBzhifubao';

       public function getPayRecordsbyWbid($wbid) //获取新闻
       {
         return $this->where(array('wbid'=>$wbid))->limit(3)->select();
       }  

      public function getZfbPaycount($map=array()) //获取新闻
       {
             return $this->alias('zfb')
			 // ->join('left join WZfbAddMoneyMx as mx  on zfb.Post_Order_no=mx.QtzfNo')
			 ->where($map)->count();
       }   

      public function getZfbPaycount1() //获取新闻
       {
             return $this->count();
       }   

 
      public function getZfbPaycount2() //获取新闻
       {
             return $this->count();
       }   
      
      public function getZfbPaycountList1($map=array(),$page = 1,$rows = 3)    //传进来的数据map为array('BETWEEN',array($start,$end));
      {                                                                              //$condition['id'] = array(between,array('2001-1-1','2005-1-1'));相当于查询 where('id' between '2001-1-1' 
        $count=$this->where($map)->count(); //获取该时段内临时卡加钱记录数量      
        $pay_sum=$this->where($map)->sum('receipt_amount'); //获取该时段内支付宝收入总额       
        $refund_sum=$this->where($map)->sum('refund_fee');  //获取该时段内支付宝退款总额

        $pay_sum=sprintf("%.2f", $pay_sum);
        $refund_sum=sprintf("%.2f", $refund_sum);

        $list = $this->where($map)->page($page,$rows)->field(array( 
        'time_post',
        'Post_Order_no' ,
        'return_trade_no',
        'trade_status',
        'total_fee',
        'receipt_amount',
        'wbid' ,
        'hyCardNo' ,
        'buyer_login_id',     
        'goods_detailinfo' ,    
        'operator_id'   
        ))->order('time_post DESC')->select(); //返回一个数据集



        return array('count'=>$count,'pay_sum'=>$pay_sum,'refund_sum'=>$refund_sum,'list'=>$list); 
      }
	  
	  
	  
	  
	  
	public function updateOneBar_ZfbShouruMoney_Bymaxid()    
    {    
        $result =true;
		$aSendstr='';
        $wbid=session('wbid');                 
        $maxid=$this->max('id');
		
        $map=array();
        $map['wbid']=session('wbid');
		$map['txflag']=0;
		$map['id']=array('elt',$maxid);	
		$map['trade_status']=100;	
		$map['refund_status']=0;	
		$map['cancel_status']=0;	
        $dai_addje=$this->where($map)->sum('receipt_amount');      		
        $bExist=D('Tixian')->where(array('wbid'=>$wbid))->find();
        if(empty($dai_addje))
        {
			$dai_addje=0;
		} 			
         		
        if(!empty($bExist))//说明提现表 里 已经存在此值直接更新
        {					
			if(D('Tixian')->where(array('wbid'=>$wbid))->setInc('sum_zfb_in',$dai_addje)===false)
			{
			  $result =false;
			}
        }
        else  //提现表 里不存在则创建一条新记录
        {  
            $zfb_insert_data=array();
			$zfb_insert_data['wbid']=$wbid;
			$zfb_insert_data['sum_zfb_in']=$dai_addje;        
            if(D('Tixian')->add($zfb_insert_data)===false)
            {
			  $result =false; 
			}				 
        }
		 
		$aTempsql= D('Tixian')->getLastSql();
        $aSendstr= $aTempsql.';'; 
		      
          //更改下当前最大id下的已累加的记录
		  
		$map=array();		 	              
        $map['wbid']=session('wbid');
		$map['txflag']=0;
		$map['id']=array('elt',$maxid);	
		$map['trade_status']=100;	
		$map['refund_status']=0;	
		$map['cancel_status']=0;	
		 
        if($this->where($map)->setField('txflag',1)===false)
		{
			$result =false;
		}	
	    $aTempsql= $this->getLastSql();
        $aSendstr.= $aTempsql.';';
		
		if($result==false)
		{
		   return false;
		}
		else
		{
		   return $aSendstr;
		} 
       
    }
	  
	  
	  public function getZfbPaycountList($map=array(),$page = 1,$rows = 10,$flag_status)    
      {                                                              
         $wbid=$map['zfb.wbid'];
		 
		 $map['zfb.receipt_amount'] =array('gt',0);
		 
         $count=$this->alias('zfb')
		 // ->join('left join WZfbAddMoneyMx as mx  on zfb.Post_Order_no=mx.QtzfNo')
		 ->where($map)->count(); //获取该时段内临时卡加钱记录数量        

       
        $list = $this->alias('zfb')
		// ->join('left join WZfbAddMoneyMx as mx  on zfb.Post_Order_no=mx.QtzfNo')
		->where($map)->page($page,$rows)->field(array( 
        'zfb.time_post'=>'time_post',
        'zfb.Post_Order_no'=> 'Post_Order_no',
        'zfb.return_trade_no'=>'return_trade_no',
        'zfb.trade_status'=>'trade_status',
        'zfb.refund_status'=>'refund_status',
        'zfb.cancel_status'=>'cancel_status',
        'zfb.total_fee'=>'total_fee',
        'zfb.receipt_amount'=>'receipt_amount',
		'zfb.wf_je'=>'wf_je',
		'zfb.sp_je'=>'sp_je',		
        'zfb.wbid' =>'wbid',
        'zfb.hyCardNo'=>'hyCardNo' ,
        'zfb.buyer_login_id'=>'buyer_login_id',     
        'zfb.goods_name' =>'goods_name',    
        'zfb.operator_id'=>'operator_id',
		'zfb.pay_position'=>'pay_position' ,
		'zfb.CodeTitlie'=>'codetitle' ,
        'zfb.syid'=>'syid',
        // 'mx.CardNo'=>'CardNo',

        ))->order('time_post DESC')->select(); 

          foreach ($list as &$val)
          {                             
            $val['time_post']= date('Y-m-d H:i:s',strtotime($val['time_post']));      
            $val['total_fee']= sprintf("%.2f", $val['total_fee']);  
			$val['wf_je']= sprintf("%.2f", $val['wf_je']);
           $val['sp_je']= sprintf("%.2f", $val['sp_je']);	
			
			 if($val['pay_position']==1)
			 {
				 $CardNo =$val['hyCardNo'];
			 }
			 else
             {
				$CardNo= D('ZfbAddMoneyMx')->where(array('wb_id'=>$wbid,'QtzfNo'=>$val['Post_Order_no']))->getField('CardNo'); 
			 }				 
			
				
		
			if(empty($CardNo))
			{
			  $val['CardNo']=''; 	
			}else
            {
			  $val['CardNo']=$CardNo;	
			}	

            if($val['cancel_status']==1)
            {
               $val['s_status']='已取消';
            }

            if($val['refund_status']==1)
            {
               $val['s_status']='已退款';
            }
			
			$syname=D('SpCtrlIp')->where(array('Wb_id'=>session('wbid'),'Syid'=>$val['syid']))->getField('syname');
			
			if(empty($syname))
			{
			  $val['syid']= $val['syid'];
			}
			else
            {
			  $val['syid']=$syname;	
			}				

            if($val['trade_status']==100)
            {
               $val['s_status']='已支付';
            } 
			
				
          }


        //以下处理界面显示的函数
        

    
          $pay_sum   =$this->alias('zfb')->where($map)->sum('receipt_amount'); 
          $map['zfb.trade_status']=100;
          $map['zfb.refund_status']=1;
          $map['zfb.cancel_status']=0; 
          $refund_sum=$this->alias('zfb')
		  // ->join('left join WZfbAddMoneyMx as mx  on zfb.Post_Order_no=mx.QtzfNo')
		  ->where($map)->sum('refund_fee');  //获取该时段内支付宝退款总额         
        


 
        $refund_sum=sprintf("%.2f", $refund_sum); 
        $pay_sum=sprintf("%.2f", $pay_sum);

        return array('count'=>$count,'pay_sum'=>$pay_sum,'refund_sum'=>$refund_sum,'list'=>$list); 
      }
	  

		

    public function getZfbPaycountList2($map=array(),$page = 1,$rows = 10,$flag_status)    
      {                                                              
         // $wbid=$map['zfb.wbid'];
		 
		 $map['zfb.receipt_amount'] =array('gt',0);
		 
         $count=$this->alias('zfb')
		 // ->join('left join WZfbAddMoneyMx as mx  on zfb.Post_Order_no=mx.QtzfNo')
		 ->where($map)->count(); //获取该时段内临时卡加钱记录数量        

       
        $list = $this->alias('zfb')
		// ->join('left join WZfbAddMoneyMx as mx  on zfb.Post_Order_no=mx.QtzfNo')
		->where($map)->page($page,$rows)->field(array( 
        'zfb.time_post'=>'time_post',
        'zfb.Post_Order_no'=> 'Post_Order_no',
        'zfb.return_trade_no'=>'return_trade_no',
        'zfb.trade_status'=>'trade_status',
        'zfb.refund_status'=>'refund_status',
        'zfb.cancel_status'=>'cancel_status',
        'zfb.total_fee'=>'total_fee',
        'zfb.receipt_amount'=>'receipt_amount',
        'zfb.wbid' =>'wbid',
        'zfb.hyCardNo'=>'hyCardNo' ,
        'zfb.buyer_login_id'=>'buyer_login_id',     
        'zfb.goods_name' =>'goods_name',    
        'zfb.operator_id'=>'operator_id',
		'zfb.pay_position'=>'pay_position' ,
        'zfb.syid'=>'syid',
        // 'mx.CardNo'=>'CardNo',

        ))->order('time_post DESC')->select(); 

          foreach ($list as &$val)
          {                             
            $val['time_post']= date('Y-m-d H:i:s',strtotime($val['time_post']));      
            $val['total_fee']= sprintf("%.2f", $val['total_fee']);  
			
			 if($val['pay_position']==1)
			 {
				 $CardNo =$val['hyCardNo'];
			 }
			 else
             {
				$CardNo= D('ZfbAddMoneyMx')->where(array('wb_id'=>$wbid,'QtzfNo'=>$val['Post_Order_no']))->getField('CardNo'); 
			 }				 
			
				

           		
			if(empty($CardNo))
			{
			  $val['CardNo']=''; 	
			}else
            {
			  $val['CardNo']=$CardNo;	
			}	

            if($val['cancel_status']==1)
            {
               $val['s_status']='已取消';
            }

            if($val['refund_status']==1)
            {
               $val['s_status']='已退款';
            }
			
			$syname=D('SpCtrlIp')->where(array('Wb_id'=>session('wbid'),'Syid'=>$val['syid']))->getField('syname');
			
			if(empty($syname))
			{
			  $val['syid']= $val['syid'];
			}
			else
            {
			  $val['syid']=$syname;	
			}				

            if($val['trade_status']==100)
            {
               $val['s_status']='已支付';
            } 
			
			 $val['wbid']= D('WbInfo')->where(array('WBID'=>$val['wbid']))->getField('WbName');	
          }


        //以下处理界面显示的函数
        

    
          $pay_sum   =$this->alias('zfb')->where($map)->sum('receipt_amount'); 
          $map['zfb.trade_status']=100;
          $map['zfb.refund_status']=1;
          $map['zfb.cancel_status']=0; 
          $refund_sum=$this->alias('zfb')
		  // ->join('left join WZfbAddMoneyMx as mx  on zfb.Post_Order_no=mx.QtzfNo')
		  ->where($map)->sum('refund_fee');  //获取该时段内支付宝退款总额         
        


 
        $refund_sum=sprintf("%.2f", $refund_sum); 
        $pay_sum=sprintf("%.2f", $pay_sum);

        return array('count'=>$count,'pay_sum'=>$pay_sum,'refund_sum'=>$refund_sum,'list'=>$list); 
      }
	  		

	    
      public function expalipay($map=array())    
      {                                                              


        $list = $this->alias('zfb')->join('left join WZfbAddMoneyMx as mx  on zfb.Post_Order_no=mx.QtzfNo')->

        where($map)->field(array( 
        'zfb.time_post'=>'time_post',
        'zfb.Post_Order_no'=> 'Post_Order_no',
        'zfb.return_trade_no'=>'return_trade_no',
        'zfb.trade_status'=>'trade_status',
        'zfb.refund_status'=>'refund_status',
        'zfb.cancel_status'=>'cancel_status',
        'zfb.total_fee'=>'total_fee',
        'zfb.receipt_amount'=>'receipt_amount',
        'zfb.wbid' =>'wbid',
        'zfb.hyCardNo'=>'hyCardNo' ,
        'zfb.buyer_login_id'=>'buyer_login_id',     
        'zfb.goods_name' =>'goods_name',    
        'zfb.operator_id'=>'operator_id',
        'mx.QtzfNo'=>'QtzfNo',
        'mx.CardNo'=>'CardNo',

        ))->order('time_post DESC')->select(); 




          foreach ($list as &$val)
          {                             
            $val['time_post']= date('Y-m-d H:i:s',strtotime($val['time_post']));      
            $val['total_fee']= sprintf("%.2f", $val['total_fee']);  
			$val['Post_Order_no']="'".$val['Post_Order_no'];
			$val['return_trade_no']="'".$val['return_trade_no'];
			if(!empty($val['CardNo'])){
				$val['CardNo']="'".$val['CardNo'];
			}
			

            if($val['cancel_status']==1)
            {
               $val['s_status']='已取消';
            }

            if($val['refund_status']==1)
            {
               $val['s_status']='已退款';
            }


            if($val['trade_status']==100)
            {
               $val['s_status']='已支付';
            } 
          }


 
        return $list;
      }
  }
