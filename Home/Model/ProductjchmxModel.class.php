<?php
    namespace Home\Model;
    use Think\Model;
    class ProductjchmxModel extends Model 
    {
        protected $tableName = 'wt_goodsjch_mx';
        public function getjhtongji_mx_listByMap_count($map=array())
		{
			$count=$this->alias('jhmx')->join('left join wt_goodsinfo info on info.goods_id=jhmx.goods_id and info.wbid=jhmx.wbid')->where($map)->count(); 	
			
			return $count;
		}

		public function getjhtongji_mx_listByMap($map=array(),$order = '',$page = 1,$rows = 20)
		{
			$count=$this->alias('jhmx')->join('left join wt_goodsinfo info on info.goods_id=jhmx.goods_id and info.wbid=jhmx.wbid')->where($map)->count(); 

			$list= $this->alias('jhmx')->join('left join wt_goodsinfo info on info.goods_id=jhmx.goods_id and info.wbid=jhmx.wbid')
			->field(array(
			   'jhmx.wbid'=>'wbid',
			   'jhmx.goods_id'=>'goods_id',
			   'jhmx.post_order_no'=>'post_order_no',		  
			   'jhmx.sumnum'=>'sumnum',
			   'jhmx.je'=>'je',
			   'info.goods_name'=>'goods_name',
			   'info.unit'=>'unit',
			   'info.guige'=>'guige',
			   'info.type_id'=>'type_id',
			   'jhmx.position'=>'position',
			   'jhmx.hj_num'=>'hj_num',
			   'jhmx.ck_num'=>'ck_num',
			   'jhmx.dtInsertTime'=>'dtInsertTime',
	   
			))
			->where($map)->page($page,$rows)->order($order)->select();
				
			
				
			foreach($list as &$val)
			{   
				$val['type_id']=D('ProductType')->where(array('type_id'=>$val['type_id']))->getField('type_name');					
				// $val['agent_realname']=D('Agent')->where(array('agent_id'=>$val['agent_id']))->getField('agent_realname');	
				// $val['agent_name']=D('Agent')->where(array('agent_id'=>$val['agent_id']))->getField('agent_name');						
				$val['dtInsertTime']=date('Y-m-d H:i:s',strtotime($val['dtInsertTime']));								
			}
	 
			return array('count'=>$count,'list'=>$list); 
		}

		
		public function getjhtongji_mx_listByMap2($map=array())
		{
			//$count=$this->alias('jhmx')->join('left join wt_goodsinfo info on info.goods_id=jhmx.goods_id and info.wbid=jhmx.wbid')->where($map)->count(); 
			$list= $this->alias('jhmx')->join('left join wt_goodsinfo info on info.goods_id=jhmx.goods_id and info.wbid=jhmx.wbid')
			->field(array(
			   'jhmx.wbid'=>'wbid',
			   'jhmx.goods_id'=>'goods_id',
			   'jhmx.post_order_no'=>'post_order_no',		  
			   'jhmx.sumnum'=>'num',
			   'jhmx.je'=>'je',
			   'info.goods_name'=>'goods_name',
			   'info.unit'=>'unit',
			   'info.guige'=>'guige',
			   	'info.is_zuhe'=>'is_zuhe',
			    'info.zuhe_id'=>'zuhe_id',
			   'info.type_id'=>'type_id',
			   'jhmx.zuhe_flag'=>'zuhe_flag',
			   'jhmx.position'=>'position',
			   'jhmx.hj_num'=>'hj_num',
			   'jhmx.ck_num'=>'ck_num',
			   'jhmx.dtInsertTime'=>'dtInsertTime',
	   
			))
			->where($map)->select();							 
			return $list; 
		}

    
  }
