<?php
    namespace Home\Model;
    use Think\Model;
    class NewproductxsmxModel extends Model 
    {
        protected $tableName = 'cs_goodsxs_mx';
        public function getxstongji_mx_listByMap_count($map=array())
		{
			$count=$this->where($map)->count(); 				
			return $count;
		}

		public function getxstongji_mx_listByMap($map=array(),$order = '',$page = 1,$rows = 20)
		{
			$count=$this->where($map)->count();            
			$list= $this->where($map)->page($page,$rows)->order($order)->select();
			
			$all_goods_list=D('Newproduct')->where(array('wbid'=>session('wbid')))->getField('goods_id,goods_name');
            $type_list=D('ProductType')->getField('type_id,type_name');
			
			foreach($list as &$val)
			{   
                if(array_key_exists($val['goods_id'],$all_goods_list))
                {
					$val['goods_name']=$all_goods_list[$val['goods_id']];
				}
				else
                {
					$val['goods_name']='';
				}
				
				if(array_key_exists($val['type_id'],$type_list))
                {
					$val['type_name']=$type_list[$val['type_id']];
				}
				else
                {
					$val['type_name']='';
				}
				$val['je']=sprintf("%.2f", $val['je']); 
				$val['dtInsertTime']=date('Y-m-d H:i:s',strtotime($val['dtInsertTime']));								
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
          //  $goodslist=  D('Product')->Field('goods_id,goods_name')->where(array('wbid'=>session('wbid')))->select();	         
             
			
			foreach($list as &$val)
			{   
	
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


		
		
		public function getallxstongji_mx_listByMap_count($map=array())
		{
			$list=$this->field('goods_id,sum(xiaoshou_num) as num')
		    ->group('goods_id')->where($map)->select();	
            $count=count($list); 			
			return $count;
		}

		public function getallxstongji_mx_listByMap($map=array(),$order = '',$page = 1,$rows = 20)
		{
			          
		
			$list=$this->field('goods_id,sum(xiaoshou_num) as num')
		    ->group('goods_id')->where($map)->select();
									
			$all_goods_list=D('Newproduct')->where(array('wbid'=>$map['wbid']))->getField('goods_id,goods_name');	
							
			foreach($list as &$val)
			{   
                if(array_key_exists($val['goods_id'],$all_goods_list))
                {
					$val['goods_name']=$all_goods_list[$val['goods_id']];
				}
				else
                {
					$val['goods_name']='';
				}											
			}
			
			$count=count($list);  
	 
			return array('count'=>$count,'list'=>$list); 
		}

    
  }
