<?php
    namespace Home\Model;
    use Think\Model;
    class NewproductjchModel extends Model 
    {
        protected $tableName = 'cs_goodsjch';

           public function gejhtongjilistByMap_count($map=array())
		{
			$count=$this->where($map)->count(); 	
			
			return $count;
		}

		public function gejhtongjilistByMap($map=array(),$order = '',$page = 1,$rows = 20)
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
		
		public function expjchtj($map=array())
		{

			$list= $this->where($map)->select();
				
								
			foreach($list as &$val)
			{   					
				$val['dtInsertTime']=date('Y-m-d H:i:s',strtotime($val['dtInsertTime']));	
               // $val['post_order_no']="'".$val['post_order_no'];	
                if($val['jch_type']=='1'){
				  	$val['jch_type_caption']='jinhuo';
				}else if($val['jch_type']=='0'){
					$val['jch_type_caption']='chuhuo';
				}				
			}
	    
			return $list;
		}
		  
  }
