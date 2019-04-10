<?php
    namespace Home\Model;
    use Think\Model;
    class ProductjbModel extends Model 
    {
        protected $tableName = 'wt_goodsjb';
		
        public function getjbtongjilistByMap_count($map=array())
		{
			$count=$this->where($map)->count(); 	
			
			return $count;
		}

		public function getjbtongjilistByMap($map=array(),$order = '',$page = 1,$rows = 20)
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
		
		
		function fnQueryOrderList($map=array(),$page = 1,$rows = 3)
		{        
			
			$count=$this->where($map)->count();
			$list = $this->where($map)->page($page,$rows)->field(array( 
			'post_order_no',
			'info',
			'shifttime',
			'sumje',
			'operator',
			))->order('shifttime DESC')->select(); //返回一个数据集
			
			foreach($list as &$val)
			{   					
				$val['shifttime']=date('Y-m-d H:i:s',strtotime($val['shifttime']));	
                $val['sumje']= sprintf("%.2f", $val['sumje']);				
			}

			return array('count'=>$count,'list'=>$list); 
		 } 
	 

		public function expjbtj($map=array())
		{

			$list= $this->where($map)->select();
							
			foreach($list as &$val)
			{   
					
				 $val['dtInsertTime']=date('Y-m-d H:i:s',strtotime($val['dtInsertTime']));								
			}
	 
			return $list;
		}
    
  }
