<?php
    namespace Home\Model;
    use Think\Model;
    class LskshangjimxModel extends Model 
    {
              
       protected $tableName = 'WTemporaryCardTable_XfMx';	//数据表名

      public function getLskShangjimxListByMap_Count($map=array())
	  {
    	
	       $count = $this->where($map)->count();
	         return $count;
	  }




		public function getLskShangjimxListByMap($map=array(),$order = '',$page = 1,$rows = 30)
		{
			
	        $count = $this->where($map)->count();
		
			$list  = $this->field(array(
				'id'=>'id',	
				'cardNo'=>'cardNo',
				'cpName'=>'cpName',
				'SjTime'=>'SjTime',
				'XjTime'=>'XjTime',
				'foregift'=>'foregift',
				'yje'=>'yje',
				'je'=>'je',
				'sjLx'=>'sjLx',
				'qtje'=>'qtje',
				'bz'=>'bz',
				'JzSyId'=>'SyId',
				'Endoperate'=>'EndOperate',
				'zjNo'=>'zjNo',
				'UserName'=>'UserName'
			
				))
				->where($map)->order('XjTime desc')->page($page,$rows)->select();
		
			

			$k=0;
			$zllist=array();	
			foreach($list as &$val)
			{
               $val['cardLx']='临时卡';
               $val['zlje']= $val['foregift']-$val['je'];
               if($val['zlje']==0)
               {
                 
               }
               else
               {  
		          $zllist[$k]['cardLx']= '临时卡';
               	  $zllist[$k]['SyId']= $val['SyId'];
                  $zllist[$k]['cardNo']= $val['cardNo'];
                  $zllist[$k]['ye']= $val['zlje'];
                  $zllist[$k]['SjTime']= date('Y-m-d H:i:s',strtotime($val['SjTime']));
				  $zllist[$k]['XjTime']= date('Y-m-d H:i:s',strtotime($val['XjTime']));
                  $zllist[$k]['EndOperate']= $val['EndOperate'];
				  $zllist[$k]['cpName']= $val['cpName'];
				  $zllist[$k]['foregift']= $val['foregift'];
				  $zllist[$k]['yje']= $val['yje'];
				  $zllist[$k]['qtje']= $val['qtje'];
				  $zllist[$k]['je']= $val['je'];
				  $zllist[$k]['zlje']= $val['zlje'];
				  $zllist[$k]['sjLx']= $val['sjLx'];
				  

                  $k++;
               }

                	
			}	
			



			return array('list'=>$zllist,'count'=>$count);
		}
		
		public function explskzhaoling_detail($map=array())
		{	
			$list  = $this->field(array(
				'id'=>'id',				
				'cardNo'=>'cardNo',
				'cpName'=>'cpName',
				'SjTime'=>'SjTime',
				'XjTime'=>'XjTime',
				'foregift'=>'foregift',
				'yje'=>'yje',
				'je'=>'je',
				'sjLx'=>'sjLx',
				'qtje'=>'qtje',
				'bz'=>'bz',
				'JzSyId'=>'SyId',
				'Endoperate'=>'EndOperate',
				'zjNo'=>'zjNo',
				'UserName'=>'UserName'
			
				))
				->where($map)->select();
				

		
				
				
				
				
			$k=0;
			$zllist=array();	
	
			foreach($list as &$val)
			{
               $val['cardLx']='临时卡';
               $val['zlje']= $val['foregift']-$val['je'];
               if($val['zlje']==0)
               {
                 
               }
               else
               {  
		          $zllist[$k]['cardLx']= '临时卡';
               	  $zllist[$k]['SyId']= $val['SyId'];
                  $zllist[$k]['cardNo']= $val['cardNo'];
                  $zllist[$k]['ye']= $val['zlje'];
                  $zllist[$k]['SjTime']= date('Y-m-d H:i:s',strtotime($val['SjTime']));
				  $zllist[$k]['XjTime']= date('Y-m-d H:i:s',strtotime($val['XjTime']));
                  $zllist[$k]['EndOperate']= $val['EndOperate'];
				  $zllist[$k]['cpName']= $val['cpName'];
				  $zllist[$k]['foregift']= $val['foregift'];
				  $zllist[$k]['yje']= $val['yje'];
				  $zllist[$k]['qtje']= $val['qtje'];
				  $zllist[$k]['je']= $val['je'];
				  $zllist[$k]['zlje']= $val['zlje'];
				  $zllist[$k]['sjLx']= $val['sjLx'];
				  

                  $k++;
               }

                	
			}	
			
			
           $count = $k;
		   

			return $zllist;
		}		
	  public function getAllLskShangjimxListByMap_Count($map=array())
	  { 	
	    $count = $this->where($map)->count();
	    return $count;
	  }
		
		
		
		public function getAllLskShangjimxListByMap($map=array(),$order = '',$page = 1,$rows = 30)
		{
			
	        $count = $this->where($map)->count();
		
			$list  = $this->field(array(
				'id'=>'id',			
				'cardNo'=>'cardNo',
				'cpName'=>'cpName',
				'SjTime'=>'SjTime',
				'XjTime'=>'XjTime',
				'foregift'=>'foregift',
				'yje'=>'yje',
				'je'=>'je',
				'sjLx'=>'sjLx',
				'qtje'=>'qtje',
				'bz'=>'bz',
				'JzSyId'=>'SyId',
				'Endoperate'=>'EndOperate',
				'zjNo'=>'zjNo',
				'UserName'=>'UserName',
				'sGuid'=>'sGuid'
			
				))
				->where($map)->order('XjTime desc')->page($page,$rows)->select();
			
					
			foreach($list as &$val)
			{
               $val['cardLx']='临时卡';
               // $val['ye']= $val['foregift']-$val['je'];              
		       $val['foregift']= sprintf("%.2f", $val['foregift']); 
			   $val['yje']= sprintf("%.2f", $val['yje']); 
			   $val['je']= sprintf("%.2f", $val['je']); 
			   $val['qtje']= sprintf("%.2f", $val['qtje']);
			   // $val['ye']= sprintf("%.2f", $val['foregift']-$val['yje']); 
               $val['SjTime']= date('Y-m-d H:i:s',strtotime($val['SjTime']));
			   $val['XjTime']= date('Y-m-d H:i:s',strtotime($val['XjTime']));	
               $val['ye']= D('Zhaoling')->where(array('sGuid'=>$val['sGuid']))->getField('je'); $val['ye']= sprintf("%.2f", $val['ye']); 			   
			}	
			
			$sum_shishou_money=$this->where($map)->sum('je');
			$sum_shishou_money=sprintf("%.2f",$sum_shishou_money);
    

			return array('list'=>$list,'count'=>$count,'sum_shishou_money'=>$sum_shishou_money);
		}
        
       public function expAllLskShangjimxList($map=array())
		{
			
			$list  = $this->field(array(
				'id'=>'id',			
				'cardNo'=>'cardNo',
				'cpName'=>'cpName',
				'SjTime'=>'SjTime',
				'XjTime'=>'XjTime',
				'foregift'=>'foregift',
				'yje'=>'yje',
				'je'=>'je',
				'sjLx'=>'sjLx',
				'qtje'=>'qtje',
				'bz'=>'bz',
				'JzSyId'=>'SyId',
				'Endoperate'=>'EndOperate',
				'zjNo'=>'zjNo',
				'UserName'=>'UserName'
			
				))
				->where($map)->select();
			
			foreach($list as &$val)
			{
               $val['cardLx']='临时卡';
               $val['ye']= $val['foregift']-$val['je'];              
		       $val['cardLx']= '临时卡';
               $val['SjTime']= date('Y-m-d H:i:s',strtotime($val['SjTime']));
			   $val['XjTime']= date('Y-m-d H:i:s',strtotime($val['XjTime']));	$val['cardNo']= "'".(string)$val['cardNo'];			                     	
			}	

			
    

			return $list;
		}

		public function getLskShangjimxListByMap2($map=array(),$order = '',$page = 1,$rows = 30)
		{
			
	        $count = $this->where($map)->count();
			
				$list  = $this->field(array(
				'id'=>'id',			
				'cardNo'=>'cardNo',
				'cpName'=>'cpName',
				'SjTime'=>'cTime',
				'XjTime'=>'XjTime',
				'foregift'=>'foregift',
				'yje'=>'yje',
	
				'sjLx'=>'sjLx',
				'qtje'=>'qtje',
				'JzSyId'=>'SyId',
				'Endoperate'=>'Operation',
				'zjNo'=>'zjNo',
				'UserName'=>'UserName'
			
				))
				->where($map)->order('XjTime desc')->page($page,$rows)->select();
				
			foreach($list as &$val)
			{
               $val['cardLx']='临时卡';

               $val['je']= $val['foregift'];
               $val['jlJe']= $val['foregift']-$val['yje'];
               $val['nType']= 3;
			}	

				
			return array('list'=>$list,'count'=>$count);
		}


    }
