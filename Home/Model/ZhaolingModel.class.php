<?php
    namespace Home\Model;
    use Think\Model;
    class ZhaolingModel extends Model 
    {
		protected $tableName = 'WTemCardPay_Mx';	//数据表名
        public function getLskZhaolingmxListByMap_Count($map=array())
	    {
    	
	       $count = $this->where($map)->count();
	         return $count;
	    }




		public function getLskZhaolingmxListByMap($map=array(),$order = '',$page = 1,$rows = 30)
		{
			
	        $count = $this->where($map)->count();	
			$list  = $this->where($map)->order('cTime desc')->page($page,$rows)->select();
			foreach($list as &$val)
			{
				$val['cTime']=date('Y-m-d H:i:s',strtotime($val['cTime']));
				$val['je']=sprintf("%.2f", $val['je']);
			}
					
			return array('list'=>$list,'count'=>$count);
		}
    }
