<?php
    namespace Home\Model;
    use Think\Model;
    class ProductpdmxModel extends Model 
    {
        protected $tableName = 'wt_goodspd_mx';
        public function getpdtongji_mx_listByMap_count($map=array())
		{
			$count=$this->alias('pdmx')->join('left join wt_goodsinfo info on info.goods_id=pdmx.goods_id and info.wbid=pdmx.wbid')->where($map)->count(); 	
			
			return $count;
		}

		public function getpdtongji_mx_listByMap($map=array(),$order = '',$page = 1,$rows = 20)
		{
			$count=$this->alias('pdmx')->join('left join wt_goodsinfo info on info.goods_id=pdmx.goods_id and info.wbid=pdmx.wbid')->where($map)->count(); 

			$list= $this->alias('pdmx')->join('left join wt_goodsinfo info on info.goods_id=pdmx.goods_id and info.wbid=pdmx.wbid')
			->field(array(
			   'pdmx.wbid'=>'wbid',
			   'pdmx.goods_id'=>'goods_id',
			   'pdmx.post_order_no'=>'post_order_no',		  
			   'pdmx.pd_num'=>'pd_num',
			   'pdmx.sunyi_je'=>'je',
			   'info.goods_name'=>'goods_name',
			   'info.unit'=>'unit',
			   'info.guige'=>'guige',
			   'info.type_id'=>'type_id',
			   'pdmx.position'=>'position',
			   'pdmx.hj_num'=>'hj_num',
			   'pdmx.ck_num'=>'ck_num',
			   'pdmx.dtInsertTime'=>'dtInsertTime',
	   
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


    
  }
