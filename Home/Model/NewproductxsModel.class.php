<?php
    namespace Home\Model;
    use Think\Model;
    class NewproductxsModel extends Model 
    {
        protected $tableName = 'cs_goodsxs';
        public function getxstjlistByMap_count($map=array())
		{
			$count=$this->where($map)->count(); 	
			
			return $count;
		}

		public function getxstjlistByMap($map=array(),$order = '',$page = 1,$rows = 20)
		{
			$count=$this->where($map)->count(); 
			$list= $this->where($map)->page($page,$rows)->order($order)->select();
					
			foreach($list as &$val)
			{   
				$val['sum_sr_je']=sprintf("%.2f", $val['sum_sr_je']);				
			}
	 
			return array('count'=>$count,'list'=>$list); 
		}
		
		
		
		public function getxstongji_mx_listByMap2($map=array())
		{
		
			$list= $this->alias('xsmx')->join('left join wt_goodsinfo info on info.goods_id=xsmx.goods_id and info.wbid=xsmx.wbid')
			->field(array(
			   'xsmx.wbid'=>'wbid',
			   'xsmx.goods_id'=>'goods_id',
			   'xsmx.post_order_no'=>'post_order_no',
			   'xsmx.xiaoshou_num'=>'xiaoshou_num',
			   'xsmx.je'=>'je',
			   'info.goods_name'=>'goods_name',
			   'info.unit'=>'unit',
			   'info.guige'=>'guige',
			   'info.type_id'=>'type_id',
			   'xsmx.is_zuhe_goods'=>'is_zuhe_goods',
			   'info.zuhe_id'=>'zuhe_id',
			   'xsmx.position'=>'position',
			   'xsmx.ck_num'=>'ck_num',
			   'xsmx.hj_num'=>'hj_num',
			   'xsmx.dtInsertTime'=>'dtInsertTime',
	   
			))
			->where($map)->select();					 
			return $list; 
		}
		
		
		public function getxstongji_mx_listByMap_count_zongzhang($map=array())
		{
			return $this->where($map)->count(); 			
		}

		public function getxstongji_mx_listByMap_zongzhang($map=array(),$order = '',$page = 1,$rows = 20)
		{
			$count=$this->where($map)->count(); 
			$list= $this->where($map)->page($page,$rows)->order($order)->select();
            $goodslist=  D('Product')->Field('goods_id,goods_name')->where(array('wbid'=>session('wbid')))->select();	         
             
			
			foreach($list as &$val)
			{   
				//$val['type_id']=D('ProductType')->where(array('type_id'=>$val['type_id']))->getField('type_name');					
				//$val['goods_name']=D('Product')->where(array('goods_id'=>$val['goods_id'],'wbid'=>session('wbid')))->getField('goods_name');	
                foreach($goodslist as &$val1)
			    {
					if($val['goods_id']==$val1['goods_id'])
					{
						$val['goods_name']=$val1['goods_name'];
						break;
					}	
				}				
				$val['dtInsertTime']=date('Y-m-d H:i:s',strtotime($val['dtInsertTime']));								
			}
	 
			return array('count'=>$count,'list'=>$list); 
		}



    
  }
