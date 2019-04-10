<?php
    namespace Home\Model;
    use Think\Model;
    class GzhpayModel extends Model 
    {
        protected $tableName = 'WxGongzhonghaoPayLog';

       public function getGzhPayRecordyWbid($wbid) 
       {
         return $this->where(array('wbid'=>$wbid))->limit(3)->select();
       }  

      public function getGzhPaycount($map=array()) //获取新闻
       {
             return $this->where($map)->count();
       }   
	   
	   public function getGzhPaycount2($map=array()) //获取新闻
       {
             return $this->where($map)->count();
       }   

      public function getGzhPaycount1() //获取新闻
       {
             return $this->count();
       }  

       public function updateOneBar_GzhShouruMoney_Bymaxid()    
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
        $dai_addje=$this->where($map)->sum('notify_total_fee'); 
        if(empty($dai_addje))
        {
			$dai_addje=0;
		} 		
        $bExist=D('Tixian')->where(array('wbid'=>$wbid))->find();  
         		
        if(!empty($bExist))//已经存在此值直接更新
        {						
			if(D('Tixian')->where(array('wbid'=>$wbid))->setInc('sum_gzh_in',$dai_addje)===false)
			{
			  $result =false;
			}
        }
        else  //不存在则创建一条新记录
        {  
            $gzh_insert_data=array();
			$gzh_insert_data['wbid']=$wbid;
			$gzh_insert_data['sum_gzh_in']=$dai_addje;        
            if(D('Tixian')->add($gzh_insert_data)===false)
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


     function fnQueryOrderList($map=array(),$page = 1,$rows = 3)
     {           
       	 
        $count=$this->getGzhPaycount($map);
        $list = $this->where($map)->page($page,$rows)->field(array( 
        'post_order_no',
        'post_total_fee',
        'notify_total_fee',
        'body','hycardno',
        'time_start',
        'time_end'  
        ))->order('time_end DESC')->select(); //返回一个数据集

        return array('count'=>$count,'list'=>$list); 

     } 

 
       

      public function getGzhPayList($map=array(),$page = 1,$rows = 10,$flag_status)    
      {                                                                            
        $count=$this->where($map)->count();                 
        $wbid=$map['wbid'];
		 $map['notify_total_fee']=array('gt',0);  
		

                 
        $list = $this->where($map)->page($page,$rows)->field(array( 
        'time_post',
        'post_order_no' ,
        'transaction_id',
        'trade_status',
        'refund_status',
        'cancel_status',
        'post_total_fee',
        'notify_total_fee',
		'wf_je',
		'sp_je',
        'wxid' ,
        'hycardno',
		'body',
         'syid',
        'xiaofei_lx' ,		 
   
        ))->order('time_post DESC')->select(); //返回一个数据集

        foreach ($list as &$val) 
        {
			
          $val['hyname']= D('HyInfo')->where(array('WB_ID'=>$wbid,'hyCardNo'=>$val['hycardno']))->getField('hyname');
          $val['time_post']= date('Y-m-d H:i:s',strtotime($val['time_post'])); 
          $val['notify_total_fee']= sprintf("%.2f", $val['notify_total_fee']);   
          
          $val['wf_je']= sprintf("%.2f", $val['wf_je']);
          $val['sp_je']= sprintf("%.2f", $val['sp_je']);		  
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
			
			
			$syname=D('SpCtrlIp')->where(array('Wb_id'=>session('wbid'),'Syid'=>$val['syid']))->getField('syname');
			
			if(empty($syname))
			{
			  $val['syid']= $val['syid'];
			}
			else
            {
			  $val['syid']=$syname;	
			}
			
			if($val['xiaofei_lx']==1)   //吧台固定二维码支付
			{
			  $hycardno =D('WxMx')->where(array('QtZfNo'=>$val['post_order_no'],'wb_id'=>session('wbid')))->getField('CardNo',true);
			  $s= implode(",", $hycardno);
			  $val['hycardno']= $s;
			}
			else if($val['xiaofei_lx']==2)
			{
				$val['hycardno']= '预购商品';
			}	
					
        }



        if($flag_status==1)
        {
          $pay_sum   =$this->where($map)->sum('notify_total_fee'); 
          $map['trade_status']=100;
          $map['refund_status']=1;
          $map['cancel_status']=0; 
          $refund_sum=$this->where($map)->sum('refund_fee');  //获取该时段内支付宝退款总额         
        }
        else if($flag_status==3)
        {
          $refund_sum=$this->where($map)->sum('refund_fee');  //获取该时段内支付宝退款总额
          $map['trade_status']=100;
          $map['refund_status']=0;
          $map['cancel_status']=0;
          $pay_sum   =$this->where($map)->sum('notify_total_fee'); 
        }
		else if($flag_status===0)
        {
			$pay_sum   =$this->where($map)->sum('notify_total_fee'); 
			$refund_sum=$this->where(array('wbid'=>session('wbid'),'refund_status'=>1))->sum('notify_total_fee'); 
		}  
  

        $refund_sum=sprintf("%.2f", $refund_sum); 
        $pay_sum=sprintf("%.2f", $pay_sum);

        return array('count'=>$count,'pay_sum'=>$pay_sum,'refund_sum'=>$refund_sum,'list'=>$list); 
      }
	  
	  
	  
	  
	  
	  public function getGzhPayList3($map=array(),$page = 1,$rows = 10,$flag_status)    
      {                                                                            
        $count=$this->where($map)->count();                 
        // $wbid=$map['wbid'];
		 $map['notify_total_fee']=array('gt',0);     
        $list = $this->where($map)->page($page,$rows)->field(array( 
        'time_post',
        'post_order_no' ,
        'transaction_id',
        'trade_status',
        'refund_status',
        'cancel_status',
        'post_total_fee',
        'notify_total_fee',
        'wxid' ,
        'hycardno',
         'syid',
        'xiaofei_lx' ,	
        'wbid',		
   
        ))->order('time_post DESC')->select(); //返回一个数据集

		
        foreach ($list as &$val) 
        {
          $val['hyname']= D('HyInfo')->where(array('WB_ID'=>$val['wbid'],'hyCardNo'=>$val['hycardno']))->getField('hyname');
		  
          $val['time_post']= date('Y-m-d H:i:s',strtotime($val['time_post'])); 
          $val['notify_total_fee']= sprintf("%.2f", $val['notify_total_fee']);      

           if(empty($hycardno))
		   {
				$val['hycardno']=D('WxMx')->where(array('wb_id'=>$val['wbid'],'QtZfNo'=>$val['post_order_no']))->getField('CardNo');
				$val['hyname']= D('HyInfo')->where(array('WB_ID'=>$val['wbid'],'hyCardNo'=>$val['hycardno']))->getField('hyname');
				
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
			
			
			// $syname=D('SpCtrlIp')->where(array('Wb_id'=>session('wbid'),'Syid'=>$val['syid']))->getField('syname');
			
			if(empty($syname))
			{
			  $val['syid']= $val['syid'];
			}
			else
            {
			  $val['syid']=$syname;	
			}
			


            $val['wbid']= D('WbInfo')->where(array('WBID'=>$val['wbid']))->getField('WbName');	

            
            		
        }



        if($flag_status==1)
        {
          $pay_sum   =$this->where($map)->sum('notify_total_fee'); 
          $map['trade_status']=100;
          $map['refund_status']=1;
          $map['cancel_status']=0; 
          $refund_sum=$this->where($map)->sum('refund_fee');  //获取该时段内支付宝退款总额         
        }
        else if($flag_status==3)
        {
          $refund_sum=$this->where($map)->sum('refund_fee');  //获取该时段内支付宝退款总额
          $map['trade_status']=100;
          $map['refund_status']=0;
          $map['cancel_status']=0;
          $pay_sum   =$this->where($map)->sum('notify_total_fee'); 
        }
		else if($flag_status===0)
        {
			$pay_sum   =D('Tixian')->where(array('wbid'=>session('wbid')))->getField('sum_gzh_in'); 
			$refund_sum=$this->where(array('wbid'=>session('wbid'),'refund_status'=>1))->sum('notify_total_fee'); 
		}  
  

        $refund_sum=sprintf("%.2f", $refund_sum); 
        $pay_sum=sprintf("%.2f", $pay_sum);

        return array('count'=>$count,'pay_sum'=>$pay_sum,'refund_sum'=>$refund_sum,'list'=>$list); 
      }
  
  

     public function expgzhpay($map=array())    
      { 

        $list = $this->where($map)->page($page,$rows)->field(array( 
        'time_post',
        'post_order_no' ,
        'transaction_id',
        'trade_status',
        'refund_status',
        'cancel_status',
        'post_total_fee',
        'notify_total_fee',
        'wxid' ,
        'hycardno'   
   
        ))->order('time_post DESC')->select(); //返回一个数据集

        foreach ($list as &$val) 
        {
          $val['hyname']= D('HyInfo')->where(array('WB_ID'=>$wbid,'hyCardNo'=>$val['hycardno']))->getField('hyname');
          $val['time_post']= date('Y-m-d H:i:s',strtotime($val['time_post'])); 
          $val['notify_total_fee']= sprintf("%.2f", $val['notify_total_fee']);  
			$val['post_order_no']="'".$val['post_order_no'];
			if(!empty($val['transaction_id'])){
				$val['transaction_id']="'".$val['transaction_id'];
			}
			
			$val['hycardno']="'".$val['hycardno'];
          

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



      public function getGzhPayList1($map=array(),$page = 1,$rows = 10)    //传进来的数据map为array('BETWEEN',array($start,$end));
      {                                                                              //$condition['id'] = array(between,array('2001-1-1','2005-1-1'));相当于查询 where('id' between '2001-1-1' 
        $count=$this->where($map)->count(); //获取该时段内临时卡加钱记录数量      
        $pay_sum=$this->where($map)->sum('notify_total_fee'); //获取该时段内支付宝收入总额
        $refund_sum=$this->where($map)->sum('refund_fee');  //获取该时段内支付宝退款总额

        $pay_sum=sprintf("%.2f", $pay_sum);
        $refund_sum=sprintf("%.2f", $refund_sum);

        $list = $this->where($map)->page($page,$rows)->field(array( 
        'time_post',
        'post_order_no' ,
        'transaction_id',
        'trade_status',
        'post_total_fee',
        'notify_total_fee',
        'wxid' ,
        'hycardno'   
   
        ))->order('time_post DESC')->select(); //返回一个数据集
        // $list='';

        return array('count'=>$count,'pay_sum'=>$pay_sum,'refund_sum'=>$refund_sum,'list'=>$list); 
      }



      public function getGzhPayList2($map=array(),$page = 1,$rows = 10)    //传进来的数据map为array('BETWEEN',array($start,$end));
      {                                                                              //$condition['id'] = array(between,array('2001-1-1','2005-1-1'));相当于查询 where('id' between '2001-1-1' 
        $count=$this->where($map)->count(); //获取该时段内临时卡加钱记录数量      
        $pay_sum=$this->where($map)->sum('notify_total_fee'); //获取该时段内支付宝收入总额
        $refund_sum=$this->where($map)->sum('refund_fee');  //获取该时段内支付宝退款总额

        $list = $this->table('Wxgongzhonghaopaylog as a,whycardtable as b')->where($map)->where('a.hycardno=b.hycardno and a.wbid=b.wb_id')->page($page,$rows)->field(
        'a.time_post as time_post,a.post_order_no,a.transaction_id,a.trade_status,a.post_total_fee,a.notify_total_fee,a.wxid ,a.hycardno,b.hyname as hyname'   
   
        )->order('hycardno')->select(); //返回一个数据集

         return array('count'=>$count,'pay_sum'=>$pay_sum,'refund_sum'=>$refund_sum,'list'=>$list); 
      }
  }
