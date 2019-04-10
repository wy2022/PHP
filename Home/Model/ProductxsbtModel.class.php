<?php
    namespace Home\Model;
    use Think\Model;
    class ProductxsbtModel extends Model 
    {
        protected $tableName = 'wt_goods_order_bt';
        public function getxstongjilist_bt_ByMap_count($map=array())
		{
			$count=$this->where($map)->count(); 				
			return $count;
		}

		public function getxstongjilist_bt_ByMap($map=array(),$order = '',$page = 1,$rows = 20)
		{
			$count=$this->where($map)->count(); 
			$list= $this->where($map)->page($page,$rows)->order($order)->select();												
			foreach($list as &$val)
			{   						
			//	$val['dtInsertTime']=date('Y-m-d H:i:s',strtotime($val['dtInsertTime']));
			//	$val['sum_sr_je']=sprintf("%.2f", $val['sum_sr_je']); 
				$val['sp_je']=sprintf("%.2f", $val['sp_je']); 
			//	$val['sum_zl_je']=sprintf("%.2f", $val['sum_zl_je']); 																										               				
			} 
			return array('count'=>$count,'list'=>$list); 
		}		 
    }
