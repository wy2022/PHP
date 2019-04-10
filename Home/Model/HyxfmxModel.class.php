<?php
    namespace Home\Model;
    use Think\Model;
    class HyxfmxModel extends Model 
    {
      protected $tableName = 'WHyCardTable_XfMx';

      public function getHyxfmxListByMap_Count($map=array())
	  {
	    $count = $this->where($map)->count();
	    return $count;
	  }


		public function getHyxfmxListByMap($map=array(),$order = '',$page = 1,$rows = 20)
		{
			
  
	        $count = $this->where($map)->count();
			$list  = $this->where($map)->order($order)->page($page,$rows)->select();	

            foreach ($list as &$val)
		    {                             	     
		      $val['SjTime']=date('Y-m-d H:i:s',strtotime($val['SjTime']));
		      $val['XjTime']=date('Y-m-d H:i:s',strtotime($val['XjTime']));    
			  $val['ye']=$val['foregift']-$val['je'];
			  $val['SyId']=$val['JzSyId'];
		        	          
		    }
	
			return array('list'=>$list,'count'=>$count);
		}
    }
