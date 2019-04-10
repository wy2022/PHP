<?php
    namespace Home\Model;
    use Think\Model;
    class LskshangjimxmxModel extends Model 
    {
              
      protected $tableName = 'WTemChangeModeXf_Mx';	//数据表名
	  
	   public function getLskShangjimxmxlist_Count($map=array())
	  {
	
	    $count = $this->where($map)->count();
		
	    return $count;
	  }
      public function getLskShangjimxmxlist($map=array(),$order = '',$page = 1,$rows = 20)
	  {
    	$count = $this->where($map)->count();	
	    $list= $this->where($map)->order($order)->page($page,$rows)->select();
		foreach($list as &$val){
			$val['je']=sprintf("%.2f",$val['je']);
			$val['cTime']=date('Y-m-d H:i:s',strtotime($val['cTime']));
		}

		return array('list'=>$list,'count'=>$count);
	  }


    }
