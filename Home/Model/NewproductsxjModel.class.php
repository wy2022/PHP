<?php
    namespace Home\Model;
    use Think\Model;
    class NewproductsxjModel extends Model 
    {
        protected $tableName = 'cs_goodsupdown';
		
        public function getsxjtongjiListByMap_count($map=array())
		{
			$count=$this->where($map)->count(); 	
			
			return $count;
		}

		public function getsxjtongjiListByMap($map=array(),$order = '',$page = 1,$rows = 20)
		{
			$count=$this->where($map)->count(); 

			$list= $this->where($map)->page($page,$rows)->order($order)->select();
							
			foreach($list as &$val)
			{   
				// $val['type_id']=D('ProductType')->where(array('type_id'=>$val['type_id']))->getField('type_name');	
				
				 //$val['agent_realname']=D('Agent')->where(array('agent_id'=>$val['agent_id']))->getField('agent_realname');
                // if($val['shangxia_status']==0)
                // {
					// $val['shangxia_type']='上架';
				// }
				// else if($val['shangxia_status']==1)
                // {
				  // $val['shangxia_type']='下架';	
				// } 	
                // $val['shangxia_type']='上架';				
			    						
				 $val['dtInsertTime']=date('Y-m-d H:i:s',strtotime($val['dtInsertTime']));								
			}
	 
			return array('count'=>$count,'list'=>$list); 
		}
		public function expsxjtj($map=array())
		{

			$list= $this->where($map)->select();
							
			foreach($list as &$val)
			{   

				 $val['dtInsertTime']=date('Y-m-d H:i:s',strtotime($val['dtInsertTime']));
				 if($val['shangxia_status']==0){
					 $val['shangxia_type']='shangjia';
				 }else if($val['shangxia_status']==1){
					 $val['shangxia_type']='xiajia';
				 }					
			}
	 
			return $list;
		}

    
  }
