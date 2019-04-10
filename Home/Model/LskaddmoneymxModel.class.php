<?php
    namespace Home\Model;
    use Think\Model;
    class LskaddmoneymxModel extends Model 
    {
      protected $tableName = 'WTemporaryCardTable_AddMoney_Mx';

      public function getLskaddmoneymxListByMap_Count($map=array())
	  {
	 
	    $count = $this->where($map)->count();
	    return $count;
	  }


		public function getLskaddmoneymxListByMap($map=array(),$order = '',$page = 1,$rows = 20)
		{
			
  
	        $count = $this->where($map)->count();
			$list  = $this->where($map)->Field(array(
				'id'=>'id',
				'SyId'=>'SyId',
				'cardNo'=>'cardNo',
				'je'=>'je',
		        'WB_ID'=>'WB_ID',
				'cTime'=>'cTime',
				'Operation'=>'Operation',
				))
			->order('cTime desc')->page($page,$rows)->select();	
            foreach ($list as &$val)
		    {                             	     	 
		      $val['cTime']=date('Y-m-d H:i:s',strtotime($val['cTime'])); 
              $val['je']=sprintf("%.2f", $val['je']);			  
		    }			
			
			return array('list'=>$list,'count'=>$count);
		}
		
		
		
        public function explskaddmoney_detail($map=array())
		{
			
			$list  = $this->where($map)->Field(array(
				'id'=>'id',
				'SyId'=>'SyId',
				'cardNo'=>'cardNo',
				'je'=>'je',
		        'WB_ID'=>'WB_ID',
				'cTime'=>'cTime',
				'Operation'=>'Operation',
				))
			->select();	
            foreach ($list as &$val)
		    {                             	     	 
		      $val['cTime']=date('Y-m-d H:i:s',strtotime($val['cTime']));
			  $syname = D('SpCtrlIp')->where(array('Wb_id'=>session('wbid'),'Syid'=>$val['SyId']))->getField('syname');  
			  if($syname !='')
			  {
				$val['SyId']= $syname;
			  }
			 $val['cardNo']= "'".(string)$val['cardNo'];            	          
		    }			
			
			return $list;
		}





    }


