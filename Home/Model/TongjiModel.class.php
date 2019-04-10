<?php
    namespace Home\Model;
    use Think\Model;
    class TongjiModel extends Model 
    {
      protected $tableName = 'Wb_JeDayCount';

	    public function getTongjilist_count_day() //获取新闻
       {
             return $this->count();
       }   
      
	   // select  convert(varchar(7),dateadd(month,0,cTime),120) as Tm ,sum(Xj_je) as Xj_je ,sum(qt_je) as qt_je from [Wb_JeDayCount]
 
  // where wb_id=244 group by convert(varchar(7),dateadd(month,0,cTime),120) 
 
 // order by  convert(varchar(7),dateadd(month,0,cTime),120) desc  
 
 
      public function getTongjilist_day($map=array(),$page = 1,$rows = 3,$month='')    
      {                                                                              
        $count=$this->where($map)->count();      
        $list_tem = $this->where($map)->order('cTime DESC')->select(); 
		
		$list=array();
		$i=0;
		
		foreach($list_tem as &$val)
		{				    			
			$val['cTime_tem']= date('Y-m',strtotime($val['cTime'])); 						
			if($val['cTime_tem']==$month)
			{

				$list[$i]['cTime']= $val['cTime'];
				$list[$i]['Sum_Je']= $val['Sum_Je'];
				$list[$i]['Xj_je']= $val['Xj_je'];
				$list[$i]['qt_Je']= $val['qt_Je'];
	            $list[$i]['wb_id']= $val['wb_id'];				
				$i++;
			}
			else
            {
				
			}				

		}
	    $Sum_Je=0;
		$Xj_je=0;
		$qt_Je=0;
		
		foreach($list as &$val)
		{		
			$val['cTime']= date('Y-m-d',strtotime($val['cTime'])); 
			$val['Sum_Je']= sprintf("%.2f", $val['Sum_Je']);  
			$val['Xj_je']= sprintf("%.2f", $val['Xj_je']);  
			$val['qt_Je']= sprintf("%.2f", $val['qt_Je']);  
			 $Sum_Je=$Sum_Je+$val['Sum_Je'];
			$Xj_je=$Xj_je+$val['Xj_je'];
			$qt_Je=$qt_Je+$val['qt_Je'];
		}
		
		
        return array('count'=>$count,'Xj_je'=>$Xj_je,'Sum_Je'=>$Sum_Je,'qt_Je'=>$qt_Je,'list'=>$list); 
      }
	  
	  
	  
	  
	   public function getTongjilist_yue($map=array(),$page = 1,$rows = 3,$year='')    
      {                                                                              
            
        $list_tem = $this->Field(array(
		'convert(varchar(7),dateadd(month,0,cTime),120)'=>'Tm',
		'sum(Xj_je)'=>'Xj_je',
		'sum(qt_Je)'=>'qt_Je',
		'sum(Sum_Je)'=>'Sum_Je',
		//'cTime'=>'cTime'
			
		))->where($map)->order('Tm DESC')->group('convert(varchar(7),dateadd(month,0,cTime),120)')->select(); //返回一个数据集
		
		
		$list=array();
		$i=0;
		
		foreach($list_tem as &$val)
		{		
		    
			$atem_month= $val['Tm'];
			$val['Tm']= date('Y',strtotime($val['Tm'])); 
			
	
			
			if($val['Tm']==$year && $atem_month !='2017-01'  && $atem_month !='2017-02' && $atem_month !='2016-12')
			{
		
				$list[$i]['Tm']= $atem_month;
				$list[$i]['Sum_Je']= $val['Sum_Je'];
				$list[$i]['Xj_je']= $val['Xj_je'];
				$list[$i]['qt_Je']= $val['qt_Je'];
	            $list[$i]['wb_id']= $val['wb_id'];
				
				$i++;
			}
			else
            {
				
			}				

		}
		
		$Sum_Je=0;
		$Xj_je=0;
		$qt_Je=0;
		
		foreach($list as &$val)
		{
		//	$val['cTime']= date('Y-m-d',strtotime($val['cTime'])); 
			$val['Sum_Je']= sprintf("%.2f", $val['Sum_Je']);  
			$val['Xj_je']= sprintf("%.2f", $val['Xj_je']);  
			$val['qt_Je']= sprintf("%.2f", $val['qt_Je']); 
						
		    $Sum_Je=$Sum_Je+$val['Sum_Je'];
			$Xj_je=$Xj_je+$val['Xj_je'];
			$qt_Je=$qt_Je+$val['qt_Je'];

			
			$val['wb_id']= $map['wb_id']; 
		}
		
		$count=strlen($list); 

		return array('count'=>$count,'Xj_je'=>$Xj_je,'Sum_Je'=>$Sum_Je,'qt_Je'=>$qt_Je,'list'=>$list); 
      }

    }
