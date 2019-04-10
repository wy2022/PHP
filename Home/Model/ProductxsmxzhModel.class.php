<?php
    namespace Home\Model;
    use Think\Model;
    class ProductxsmxzhModel extends Model 
    {
        protected $tableName = 'wt_goodsxs_mx_zuhe';
				
		public function getxstongji_mx_zh_listByMap($map=array())
		{
		
			$list= $this->alias('xsmx')
			->join('left join wt_goodsinfo info on info.goods_id=xsmx.goods_id and info.wbid=xsmx.wbid')
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
			   'xsmx.zuhe_id'=>'zuhe_id',			   
			   'xsmx.position'=>'position',
			   'xsmx.ck_num'=>'ck_num',
			   'xsmx.hj_num'=>'hj_num',
			   'xsmx.dtInsertTime'=>'dtInsertTime',	   
			))
			->where($map)->select();					 
			return $list; 
		}
   
  }
