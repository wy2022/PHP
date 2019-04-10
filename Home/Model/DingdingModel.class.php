<?php
    namespace Home\Model;
    use Think\Model;
    class DingdingModel extends Model 
    {
        protected $tableName = 'wt_ding_order';



      public function getDingdingcount($map=array()) //获取新闻
       {
             return $this->alias('dingding')

			 ->where($map)->count();
       }   





      public function getDingdingList($map=array(),$page = 1,$rows = 10,$flag_status)    
      {                                                                            
        $count=$this->alias('dingding')

		->where($map)->count();                   


        $wbid=$map['dingding.wbid'];
      
      
		$list = $this->alias('dingding')->where($map)->page($page,$rows)->order('dtInsertTime DESC')->select(); 
		
		 
          foreach ($list as &$val)
          {                             
            $val['dtInsertTime']= date('Y-m-d H:i:s',strtotime($val['dtInsertTime']));      
            $status= D('ZfbAddMoneyMx')->where(array('QtzfNo'=>$val['post_order_no']))->find(); 
            if(!empty($status))
            {
				$val['pay_status']='加钱成功';
			}
			else
            {
				$val['pay_status']='加钱失败';
			}				
			
          }
		 
        return array('count'=>$count,'list'=>$list); 
      }
	  
	  
	  

	
  }
