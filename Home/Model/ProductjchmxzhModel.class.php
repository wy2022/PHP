<?php
namespace Home\Model;
use Think\Model;
class ProductjchmxzhModel extends Model 
{
	protected $tableName = 'wt_goodsjch_mx_zuhe';
		public function getjhtongji_mx_zh_listByMap($map=array())
		{
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
