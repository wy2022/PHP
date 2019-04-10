<?php
    namespace Home\Model;
    use Think\Model;
    class ProductpdModel extends Model 
    {
        protected $tableName = 'wt_goodspd';
        
		public function getpdtongjilistByMap_count($map=array())
		{
			$count=$this->where($map)->count(); 	
			
			return $count;
		}

		public function getpdtongjilistByMap($map=array(),$order = '',$page = 1,$rows = 20)
		{
			$count=$this->where($map)->count(); 

			$list= $this->where($map)->page($page,$rows)->order($order)->select();
				
				
				
			foreach($list as &$val)
			{   
				// $val['type_id']=D('ProductType')->where(array('type_id'=>$val['type_id']))->getField('type_name');					
				// $val['agent_realname']=D('Agent')->where(array('agent_id'=>$val['agent_id']))->getField('agent_realname');	
				// $val['agent_name']=D('Agent')->where(array('agent_id'=>$val['agent_id']))->getField('agent_name');						
				$val['dtInsertTime']=date('Y-m-d H:i:s',strtotime($val['dtInsertTime']));								
			}
	 
			return array('count'=>$count,'list'=>$list); 
		}
		public function exppdtj($map=array())
		{


			$list= $this->where($map)->select();
				
				
				
			foreach($list as &$val)
			{   
						
				$val['dtInsertTime']=date('Y-m-d H:i:s',strtotime($val['dtInsertTime']));
				if($val['position']==0){
					 $val['position']='cangku';
				 }else if($val['position']==1){
					 $val['position']='huojia';
				 }									
			}
	 
			return $list;
		}

    
  }
