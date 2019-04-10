<?php
    namespace Home\Model;
    use Think\Model;
    class WbchangenumModel extends Model 
    {
        protected $tableName = 'wt_ChangeCpnumLog';
		
	 public function getjiajianjilog_Count($map=array())
	  {
		$count = $this->where($map)->count();
		return $count;
	  }


		public function getjiajianjilog($map=array(),$order = '',$page = 1,$rows = 20)
		{

		   $count = $this->where($map)->count();
			$list  = $this->where($map)->page($page,$rows)->order($order)->select();
		 foreach ($list as &$val)
		{      
           $val['dtInsertTime']=date('Y-m-d H:i:s',strtotime($val['dtInsertTime']));
		   $val['oldendtime']=date('Y-m-d H:i:s',strtotime($val['oldendtime']));
		   $val['newendtime']=date('Y-m-d H:i:s',strtotime($val['newendtime']));

		}
				   
			
			
			return array('list'=>$list,'count'=>$count);
		}
	
	
    }
