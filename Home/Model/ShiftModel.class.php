<?php
    namespace Home\Model;
    use Think\Model;
    class ShiftModel extends Model 
    {
       protected $tableName = 'WChangeWork';

       public function getShiftListByMap_Count($map=array())
	  {
	
	    $count = $this->alias('hy')->where($map)->count();
	    return $count;
	  }


		public function getShiftListByMap($map=array(),$order = '',$page = 1,$rows = 20)
		{
			

	        $count = $this->alias('hy')->where($map)->count();
			$list  = $this->alias('hy')->where($map)->order($order)->page($page,$rows)->select();
		    foreach ($list as &$val)
	        {                             
              $val['SyId']= $val['SyID']; 
	          $val['inje']= sprintf("%.2f", $val['inje']); 
	          $val['keepje']= sprintf("%.2f", $val['keepje']); 
	          $val['YjJe']= sprintf("%.2f", $val['YjJe']); 
	          $val['Hyje']= sprintf("%.2f", $val['Hyje']);
	          $val['Spje']= sprintf("%.2f", $val['Spje']);
	          $val['TemCardJe']= sprintf("%.2f", $val['TemCardJe']);
	          
	          $val['TempYj']= sprintf("%.2f", $val['TempYj']);

	          $val['BeginTime']= date('Y-m-d H:i:s',strtotime($val['BeginTime']));
	          $val['cTime']= date('Y-m-d H:i:s',strtotime($val['cTime']));
              

              $val['BeginTime_shijianchuo']= strtotime($val['BeginTime']);
	          $val['cTime_shijianchuo']= strtotime($val['cTime']);
		
	          $val['ceshi']='dadwag阿房宫挨个啊';
	        }
			

			 
			$sum_shijiao_money=$this->alias('hy')->where($map)->sum('inje');
			$sum_liuxia_money=$this->alias('hy')->where($map)->sum('keepje');
			
            $sum_shijiao_money=sprintf("%.2f", $sum_shijiao_money); 
	
  
			
			return array('list'=>$list,'count'=>$count,'sum_shijiao_money'=>$sum_shijiao_money,'sum_liuxia_money'=>$sum_liuxia_money);
		}
		
	    public function expShift($map=array())
		  {
            $list  = $this->alias('hy')->where($map)->select(); 
			foreach ($list as &$val)
	        {                             
              $val['SyId']= $val['SyID']; 
			  $syname = D('SpCtrlIp')->where(array('Wb_id'=>session('wbid'),'Syid'=>$val['SyId']))->getField('syname');  
			  if($syname !='')
			  {
				$val['SyId']= $syname;
			  }
	          $val['inje']= sprintf("%.2f", $val['inje']); 
	          $val['keepje']= sprintf("%.2f", $val['keepje']); 
	          $val['YjJe']= sprintf("%.2f", $val['YjJe']); 
	          $val['Hyje']= sprintf("%.2f", $val['Hyje']);
	          $val['Spje']= sprintf("%.2f", $val['Spje']);
	          $val['TemCardJe']= sprintf("%.2f", $val['TemCardJe']);
	          
	          $val['TempYj']= sprintf("%.2f", $val['TempYj']);

	          $val['BeginTime']= date('Y-m-d H:i:s',strtotime($val['BeginTime']));
	          $val['cTime']= date('Y-m-d H:i:s',strtotime($val['cTime']));
              

              $val['BeginTime_shijianchuo']= strtotime($val['BeginTime']);
	          $val['cTime_shijianchuo']= strtotime($val['cTime']);
	        }  
			

			
			return $list;
		  }
		
       public function getOneShiftListByMap_Count($map=array())
	  {
	
	    $count = $this->alias('hy')->where($map)->count();
	    return $count;
	  }


		public function getOneShiftListByMap($map=array(),$order = '',$page = 1,$rows = 20)
		{

			$id=session('shift_id');

	        $count = $this->alias('hy')->where($map)->count();
			$list  = $this->alias('hy')->where(array('WB_ID'=>session('wbid'),'id'=>$id))->select();
		    foreach ($list as &$val)
	        {                             

	          $val['inje']= sprintf("%.2f", $val['inje']); 
	          $val['keepje']= sprintf("%.2f", $val['keepje']); 
	          $val['YjJe']= sprintf("%.2f", $val['YjJe']); 
	          $val['Hyje']= sprintf("%.2f", $val['Hyje']);
	          $val['Spje']= sprintf("%.2f", $val['Spje']);
			  $val['SyId']=  $val['SyID'];
	          $val['TemCardJe']= sprintf("%.2f", $val['TemCardJe']);
	          
	          $val['TempYj']= sprintf("%.2f", $val['TempYj']);

	          $val['BeginTime']= date('Y-m-d H:i:s',strtotime($val['BeginTime']));
	          $val['cTime']= date('Y-m-d H:i:s',strtotime($val['cTime']));
              

              $val['BeginTime_shijianchuo']= strtotime($val['BeginTime']);
	          $val['cTime_shijianchuo']= strtotime($val['cTime']);

			    
	        }
			
			return array('list'=>$list,'count'=>$count);
		}
		
		
		
		
    }

