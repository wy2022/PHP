<?php
    namespace Home\Model;
    use Think\Model;
    class ProductsxjmxzhModel extends Model 
    {
        protected $tableName = 'wt_goodsupdown_mx_zuhe';
		
		public function getsxjinfo_mx_zh_ListByMap($map=array())
		{		
			$list= $this->alias('sxjmx')->join('left join wt_goodsinfo info on info.goods_id=sxjmx.goods_id and info.wbid=sxjmx.wbid')
			->field(array(
			   'sxjmx.wbid'=>'wbid',
			   'sxjmx.goods_id'=>'goods_id',
			   'sxjmx.num'=>'num',
			   'info.goods_name'=>'goods_name',
			   'info.unit'=>'unit',
			   'info.guige'=>'guige',
			    'sxjmx.is_zuhe_goods'=>'is_zuhe_goods',
			    'info.zuhe_id'=>'zuhe_id',
			   'info.type_id'=>'type_id',
			   'sxjmx.post_order_no'=>'post_order_no',
			   'sxjmx.shangxia_status'=>'shangxia_status',
			   'sxjmx.hj_num'=>'hj_num',
			   'sxjmx.ck_num'=>'ck_num',
			   'sxjmx.dtInsertTime'=>'dtInsertTime',   
			))
			->where($map)->select();
				
			return $list; 
		}
		
   
  }
