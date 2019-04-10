<?php
    namespace Home\Model;
    use Think\Model\ViewModel;
    class NewproductmxViewModel extends ViewModel 
    {
        protected $trueTableName = 'View_Goods_liucheng_new';	//数据表名         
		public function getGoodsmxListByMap_count($map=array())
		{	
			$count = $this->where($map)->count();			
			return $count;
		}

		public function getGoodsmxListByMap($map=array(),$order = '',$page = 1,$rows = 10)
		{			        		      			
			
			$goodslist=D('Product')->where(array('wbid'=>session('wbid')))->select();										
			$list  = $this->where($map)->order($order)->page($page,$rows)->select();
			
			foreach($list  as &$val)
			{
				foreach($goodslist as $val1)
				{
                    if($val1['goods_id']==$val['goods_id'])
					{
						$val['goods_name']=$val1['goods_name'];
						break;
					}	
				}
			}				
			
			$count=count($list );				
			return array('list'=>$list,'count'=>$count);
		}
    }
