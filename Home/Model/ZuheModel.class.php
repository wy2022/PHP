<?php
    namespace Home\Model;
    use Think\Model;
    class ZuheModel extends Model 
    {
        protected $tableName = 'wt_goods_zuhe';  
		public function getZuheListByMap_Count($map=array())
		{
			$count = $this->where($map)->count();
			return $count;
		}
			
		public function getZuheListByMap($map=array(),$order = '',$page = 1,$rows = 20)
		{
			$count = $this->where($map)->count();
			$list  = $this->where($map)->order($order)->page($page,$rows)->select();		  			
			return array('list'=>$list,'count'=>$count);
		}
    }
