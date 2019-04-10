<?php
    namespace Home\Model;
    use Think\Model;
    class ProductxsModel extends Model 
    {
        protected $tableName = 'wt_goodsxs';
        public function getxstongjilistByMap_count($map=array())
		{
			$count=$this->where($map)->count(); 	
			
			return $count;
		}

		public function getxstongjilistByMap($map=array(),$order = '',$page = 1,$rows = 20)
		{
			$count=$this->where($map)->count(); 
			$list= $this->where($map)->page($page,$rows)->order($order)->select();												
			foreach($list as &$val)
			{   						
				$val['dtInsertTime']=date('Y-m-d H:i:s',strtotime($val['dtInsertTime']));
				$val['sum_sr_je']=sprintf("%.2f", $val['sum_sr_je']); 
				$val['sum_sp_je']=sprintf("%.2f", $val['sum_sp_je']); 
				$val['sum_zl_je']=sprintf("%.2f", $val['sum_zl_je']); 
																	
				$val['sum_payinfo']='商品总价：'.$val['sum_sp_je'].' 收入：'.$val['sum_sr_je'].' 找零：'.$val['sum_zl_je'];
				if($val['pay_type']==1)
				{
				  $attach = D('Wxpay')->where(array('wbid'=>session('wbid'),'post_order_no'=>$val['post_order_no']))->getField('attach');	
				  $attach=json_decode($attach,true);
				  $cpname=$attach['cpname'] ;
				  $hycardno=$attach['hycardno'] ;
				}
				else if($val['pay_type']==2)
                {
				  $goodsinfo = D('Zfbpay')->where(array('wbid'=>session('wbid'),'Post_Order_no'=>$val['post_order_no']))->getField('goodsinfo');
				  $attach=json_decode($goodsinfo,true);
				  
				  $cpname=$attach['cpname'] ;
				  $hycardno=$attach['hycardno'] ;

				  
				}
				else if($val['pay_type']==3)
                {
				 
				  $bz_str=json_decode($val['bz'],true);
				  				  
				  $cpname=$bz_str['cpname'] ;
				  $hycardno=$bz_str['hycardno'] ;			  
				}					
				
				$val['cpname']=  $cpname;
				$val['hycardno']=  $hycardno;
											               				
			}
	 
			return array('count'=>$count,'list'=>$list); 
		}
		
		public function expxstj($map=array())
		{
			$list= $this->where($map)->select();			
			foreach($list as &$val)
			{   						
				$val['dtInsertTime']=date('Y-m-d H:i:s',strtotime($val['dtInsertTime']));								
			}	 
			return $list;
		}


    
  }
