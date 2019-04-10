<?php
    namespace Home\Model;
    use Think\Model;
    class WxpayModel extends Model 
    {
        protected $tableName = 'WBwxpay';

       public function getWxPayRecordyWbid($wbid) 
       {
         return $this->where(array('wbid'=>$wbid))->limit(3)->select();
       }  

      public function getWxPaycount($map=array()) //获取新闻
       {
             return $this->alias('wx')
			 // ->join('left join WWeiXinAddMoneyMx as mx  on wx.post_order_no=mx.QtzfNo')
			 ->where($map)->count();
       }   

      public function getWxPaycount1() //获取新闻
       {
             return $this->count();
       }   

      
	public function updateOneBar_WxShouruMoney_Bymaxid()    
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
        
        if(empty($dai_addje))
        {
			$dai_addje=0;
		} 	 		
        $bExist=D('Tixian')->where(array('wbid'=>$wbid))->find();  
         		
        if(!empty($bExist))//已经存在此值直接更新
        {		
			if(D('Tixian')->where(array('wbid'=>$wbid))->setInc('sum_wx_in',$dai_addje)===false)
			{
			  $result =false;
			}	
        }
        else  //不存在则创建一条新记录
        {  
            $wx_insert_data=array();
			$wx_insert_data['wbid']=$wbid;
			$wx_insert_data['sum_wx_in']=$dai_addje;        
            if(D('Tixian')->add($wx_insert_data)===false)
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
	

      
      public function getWxPayList1($map=array(),$page = 1,$rows = 3)    //传进来的数据map为array('BETWEEN',array($start,$end));
      {                                                                              //$condition['id'] = array(between,array('2001-1-1','2005-1-1'));相当于查询 where('id' between '2001-1-1' 
        $count=$this->where($map)->count(); //获取该时段内临时卡加钱记录数量      
        $pay_sum=$this->where($map)->sum('receipt_amount'); //获取该时段内支付宝收入总额
        $pay_sum=sprintf("%.2f", $pay_sum);

        $refund_sum=$this->where($map)->sum('refund_fee');  //获取该时段内支付宝退款总额
        $refund_sum=sprintf("%.2f", $refund_sum);

        $list = $this->where($map)->page($page,$rows)->field(array( 
        'time_post',
        'post_order_no' ,
        'transaction_id',
        'trade_status',
        'total_fee',
        'receipt_amount',
        'wbid' ,
        'hycardno' ,
        // 'buyer_login_id',     
        'goods_detailinfo' ,    
        'operator_id'   
        ))->order('time_post DESC')->select(); //返回一个数据集

       

        return array('count'=>$count,'pay_sum'=>$pay_sum,'refund_sum'=>$refund_sum,'list'=>$list); 
      }



      public function getWxPayList($map=array(),$page = 1,$rows = 10,$flag_status)    
      {                                                                            
        $count=$this->alias('wx')
		// ->join('left join WWeiXinAddMoneyMx as mx  on wx.post_order_no=mx.QtzfNo')
		->where($map)->count();                   

       
        //1.查询支付宝表里所有未统计的数据，获取最大的id号，并将统计后的数据的标记设置为
        // $maxid=$this->max('id');
        $wbid=$map['wx.wbid'];
      
        // $dai_addje=$this->where(array('wbid'=>$wbid,'txflag'=>0))->where('id <='.$maxid)->sum('receipt_amount'); 
        
        // $data=array();
        // $data['wbid']=$wbid;
        // $data['sum_wx_in']=$dai_addje;


          
        // $bExist=D('Tixian')->getOneTxDataExist($wbid);

        // if($bExist)//已经存在此值直接更新
         // {

            // $yuan_je=D('Tixian')->getOneTxJe($wbid);
           

            // $data['sum_wx_in']=$data['sum_wx_in']+$yuan_je['sum_wx_in'];

          
         

            // $res=  D('Tixian')->updateOneTxData($wbid,$data);
            // if($res)
            // {
               
            // }else
            // {
            
            // }
            
         // }else//不存在则创建一条新记录
         // {  
           
            // $res= D('Tixian')->addOneTxData($data);
            // if($res)
            // {
            
            // }else
            // {
            
            // }      
         // }
           // //更改下当前最大id下的已累加的记录
         // $data2=array();
         // $data2['txflag']=1;
         // $bSave=$this->where(array('wbid'=>$wbid,'txflag'=>0))->where('id <='.$maxid)->save($data2); 
         // if(!empty($bSave))
         // {
         
         // } 
         
         
        //2.访问提现表，判断数据是否存在,不存在则新增一条，存在则更新
        //3.更新完成后，读取数据
        
   
      
        // $list = $this->alias('wx')->join('left join WWeiXinAddMoneyMx as mx  on wx.post_order_no=mx.QtzfNo')
        // ->where($map)->page($page,$rows)->field(array( 
        // 'wx.time_post'=>'time_post',
        // 'wx.post_order_no'=>'post_order_no' ,
        // 'wx.transaction_id'=>'transaction_id',
        // 'wx.trade_status'=>'trade_status',
        // 'wx.refund_status'=>'refund_status',
        // 'wx.cancel_status'=>'cancel_status',
        // 'wx.total_fee'=>'total_fee',
        // 'wx.receipt_amount'=>'receipt_amount',
        // 'wx.wbid' =>'wbid',
        // 'wx.hycardno'=>'hycardno' ,
        // // 'buyer_login_id',     
        // 'wx.kcardlx' =>'kcardlx',    
        // 'wx.operator_id'=>'operator_id',  
        // 'mx.QtzfNo'=>'QtzfNo',
        // 'mx.CardNo'=>'CardNo', 

        // ))->order('time_post DESC')->select(); 
		
		
		
		$list = $this->alias('wx')
		//->join('left join WWeiXinAddMoneyMx as mx  on wx.post_order_no=mx.QtzfNo')
		
        ->where($map)->page($page,$rows)->field(array( 
        'wx.time_post'=>'time_post',
        'wx.post_order_no'=>'post_order_no' ,
        'wx.transaction_id'=>'transaction_id',
        'wx.trade_status'=>'trade_status',
        'wx.refund_status'=>'refund_status',
        'wx.cancel_status'=>'cancel_status',
        'wx.total_fee'=>'total_fee',
        'wx.receipt_amount'=>'receipt_amount',
        'wx.wbid' =>'wbid',
        'wx.hycardno'=>'hycardno' ,
		'wx.pay_position'=>'pay_position' ,
        // 'buyer_login_id',     
        'wx.kcardlx' =>'kcardlx',    
        'wx.operator_id'=>'operator_id',  
		 'wx.goods_name'=>'goods_name', 
        // 'mx.QtzfNo'=>'QtzfNo',
        // 'mx.CardNo'=>'CardNo', 

        ))->order('time_post DESC')->select(); 
		
		 

          foreach ($list as &$val)
          {                             
            $val['time_post']= date('Y-m-d H:i:s',strtotime($val['time_post']));      
            $val['total_fee']= sprintf("%.2f", $val['total_fee']); 
            if($val['pay_position']==1)
            {
			   $CardNo =$val['hycardno'];
               $val['kcardlx']=$val['goods_name'];
               			   
			}
			else
            {
			   $CardNo= D('WxMx')->where(array('wb_id'=>$wbid,'QtZfNo'=>$val['post_order_no']))->getField('CardNo');	
			}				
					
			if(empty($CardNo))
			{
			  $val['CardNo']=''; 	
			}
			else
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


            if($val['trade_status']==100)
            {
               $val['s_status']='已支付';
            } 
          }


  
          $pay_sum   =$this->alias('wx')->where($map)->sum('receipt_amount'); 	  
          $map['wx.trade_status']=100;
          $map['wx.refund_status']=1;
          $map['wx.cancel_status']=0; 
          $refund_sum=$this->alias('wx')->where($map)->sum('refund_fee');  ////查询总退款钱数         
        
        // else if($flag_status==3)
        // {
          // $refund_sum=$this->alias('wx')->where($map)->sum('refund_fee');  //获取该时段内支付宝退款总额
          // $map['wx.trade_status']=100;
          // $map['wx.refund_status']=0;
          // $map['wx.cancel_status']=0;
          // $pay_sum   =$this->alias('wx')->where($map)->sum('receipt_amount'); 
        // }
		// else if($flag_status===0)
        // {

			// $pay_sum   =D('Tixian')->where(array('wbid'=>session('wbid')))->getField('sum_wx_in'); 			
			// $refund_sum=$this->where(array('wbid'=>session('wbid'),'refund_status'=>1))->sum('receipt_amount'); 
		// }			
      
        $refund_sum=sprintf("%.2f", $refund_sum); 
        $pay_sum=sprintf("%.2f", $pay_sum);
		 
        return array('count'=>$count,'pay_sum'=>$pay_sum,'refund_sum'=>$refund_sum,'list'=>$list); 
      }
	  
	  
	  
	  
	   public function getWxPaycount2($map=array()) //获取新闻
       {
             return $this->alias('wx')->where($map)->count();
       } 
	  
	  
	  public function getWxPayList2($map=array(),$page = 1,$rows = 10,$flag_status)    
      {                                                                            
        $count=$this->alias('wx')->where($map)->count();                       

        // $wbid=$map['wx.wbid'];	
		$list = $this->alias('wx')->where($map)->page($page,$rows)->field(array( 
        'wx.time_post'=>'time_post',
        'wx.post_order_no'=>'post_order_no' ,
        'wx.transaction_id'=>'transaction_id',
        'wx.trade_status'=>'trade_status',
        'wx.refund_status'=>'refund_status',
        'wx.cancel_status'=>'cancel_status',
        'wx.total_fee'=>'total_fee',
        'wx.receipt_amount'=>'receipt_amount',
        'wx.wbid' =>'wbid',
        'wx.hycardno'=>'hycardno' ,
		'wx.pay_position'=>'pay_position' ,    
        'wx.kcardlx' =>'kcardlx',    
        'wx.operator_id'=>'operator_id',  
		 'wx.goods_name'=>'goods_name', 

        ))->order('time_post DESC')->select(); 
		
		
		
			 

          foreach ($list as &$val)
          {                             
            $val['time_post']= date('Y-m-d H:i:s',strtotime($val['time_post']));      
            $val['total_fee']= sprintf("%.2f", $val['total_fee']); 
            if($val['pay_position']==1)
            {
			   $CardNo =$val['hycardno'];
               $val['kcardlx']=$val['goods_name'];
               			   
			}
			else
            {
			   $CardNo= D('WxMx')->where(array('wb_id'=>$wbid,'QtZfNo'=>$val['post_order_no']))->getField('CardNo');	
			}				
					
			if(empty($CardNo))
			{
			  $val['CardNo']=''; 	
			}
			else
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


            if($val['trade_status']==100)
            {
               $val['s_status']='已支付';
            } 
			
			
            $val['wbid']= D('WbInfo')->where(array('WBID'=>$val['wbid']))->getField('WbName');
          }


  
          $pay_sum   =$this->alias('wx')->where($map)->sum('receipt_amount'); 	  
          $map['wx.trade_status']=100;
          $map['wx.refund_status']=1;
          $map['wx.cancel_status']=0; 
          $refund_sum=$this->alias('wx')->where($map)->sum('refund_fee');  ////查询总退款钱数         
           
        $refund_sum=sprintf("%.2f", $refund_sum); 
        $pay_sum=sprintf("%.2f", $pay_sum);
		 
        return array('count'=>$count,'pay_sum'=>$pay_sum,'refund_sum'=>$refund_sum,'list'=>$list); 
      }
	  
	  
	  
      public function expWxPay($map=array())    
      {                                                                            
        $list = $this->alias('wx')->join('left join WWeiXinAddMoneyMx as mx  on wx.post_order_no=mx.QtzfNo')
        ->where($map)->field(array( 
        'wx.time_post'=>'time_post',
        'wx.post_order_no'=>'post_order_no' ,
        'wx.transaction_id'=>'transaction_id',
        'wx.trade_status'=>'trade_status',
        'wx.refund_status'=>'refund_status',
        'wx.cancel_status'=>'cancel_status',
        'wx.total_fee'=>'total_fee',
        'wx.receipt_amount'=>'receipt_amount',
        'wx.wbid' =>'wbid',
        'wx.hycardno'=>'hycardno' ,
        // 'buyer_login_id',     
        'wx.kcardlx' =>'kcardlx',    
        'wx.operator_id'=>'operator_id',  
        'mx.QtzfNo'=>'QtzfNo',
        'mx.CardNo'=>'CardNo', 

        ))->order('time_post DESC')->select(); //返回一个数据集
        
    
           foreach ($list as &$val)
          {                             
            $val['time_post']= date('Y-m-d H:i:s',strtotime($val['time_post']));      
            $val['total_fee']= sprintf("%.2f", $val['total_fee']);  
			$val['post_order_no']="'".$val['post_order_no'];
			$val['transaction_id']="'".$val['transaction_id'];

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
