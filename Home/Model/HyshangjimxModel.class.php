<?php
    namespace Home\Model;
    use Think\Model;
    class HyshangjimxModel extends Model 
    {
              
       protected $tableName = 'WHyCardTable_XfMx';	//数据表名

      public function getHyShangjimxListByMap_Count($map=array())
	  {       	
	    return $this->alias('hyxfmx')->where($map)->count();        
	  }



		public function getHyShangjimxListByMap($map=array(),$order = '',$page = 1,$rows = 20)
		{		
         		
	        $count = $this->alias('hyxfmx')->where($map)->count();	
			$list  = $this->alias('hyxfmx')
			->join('left join WHyCardTable ON hyxfmx.WB_ID = WHyCardTable.WB_ID and WHyCardTable.hyCardNo = hyxfmx.cardNo')		
			->field(array(
				'hyxfmx.id'=>'id',			
				'hyxfmx.cardNo'=>'cardNo',
				'hyxfmx.cpName'=>'cpName',
				'hyxfmx.SjTime'=>'SjTime',
				'hyxfmx.XjTime'=>'XjTime',
				'hyxfmx.foregift'=>'foregift',
				'hyxfmx.yje'=>'yje',
				'hyxfmx.je'=>'je',
				'hyxfmx.sjLx'=>'sjLx',

				'hyxfmx.qtje'=>'qtje',
				'hyxfmx.bz'=>'bz',
				'hyxfmx.EndOperate'=>'EndOperate',
				'hyxfmx.zjNo'=>'zjNo',
				'WHyCardTable.hyname'=>'UserName'	
				))
			->where($map)->order($order)->page($page,$rows)->select();
			
			$sum_shishou_money=$this->alias('hyxfmx')->where($map)->sum('je');
			$sum_shishou_money=sprintf("%.2f",$sum_shishou_money);
			
     
 
			foreach($list as &$val)
			{
               $val['cardLx']='会员卡';
               $val['foregift']= sprintf("%.2f", $val['foregift']); 
			   $val['yje']= sprintf("%.2f", $val['yje']); 
			   $val['je']= sprintf("%.2f", $val['je']); 
			   $val['ye']= sprintf("%.2f", $val['foregift']-$val['yje']); 
			   $val['SjTime']=date('Y-m-d H:i:s',strtotime($val['SjTime']));
			   $val['XjTime']=date('Y-m-d H:i:s',strtotime($val['XjTime']));
		
			   
			   $ahyguid= D('HyInfo')->where(array('WB_ID'=>session('wbid'),'hyCardNo'=>$val['cardNo']))->getField('hyCardGuid');
               $val['hydj']=D('Hylx')->where(array('WB_ID'=>session('wbid'),'Guid'=>$ahyguid))->getField('Name');			   
			}	

			
			return array('list'=>$list,'count'=>$count,'sum_shishou_money'=>$sum_shishou_money);
		}

		public function expHyShangjimxList($map=array())
		{		
         		
			$list  = $this->alias('hyxfmx')
			->join('left join WHyCardTable ON hyxfmx.WB_ID = WHyCardTable.WB_ID and WHyCardTable.hyCardNo = hyxfmx.cardNo')		
			->field(array(
				'hyxfmx.id'=>'id',			
				'hyxfmx.cardNo'=>'cardNo',
				'hyxfmx.cpName'=>'cpName',
				'hyxfmx.SjTime'=>'SjTime',
				'hyxfmx.XjTime'=>'XjTime',
				'hyxfmx.foregift'=>'foregift',
				'hyxfmx.yje'=>'yje',
				'hyxfmx.je'=>'je',
				'hyxfmx.sjLx'=>'sjLx',

				'hyxfmx.qtje'=>'qtje',
				'hyxfmx.bz'=>'bz',
				'hyxfmx.EndOperate'=>'EndOperate',
				'hyxfmx.zjNo'=>'zjNo',
				'WHyCardTable.hyname'=>'UserName'	
				))
			->where($map)->select();

 
			foreach($list as &$val)
			{
               $val['cardLx']='会员卡';
               $val['foregift']= sprintf("%.2f", $val['foregift']); 
			   $val['yje']= sprintf("%.2f", $val['yje']); 
			   $val['je']= sprintf("%.2f", $val['je']); 
			   $val['ye']= sprintf("%.2f", $val['foregift']-$val['yje']); 
			   $val['SjTime']=date('Y-m-d H:i:s',strtotime($val['SjTime']));
			   $val['XjTime']=date('Y-m-d H:i:s',strtotime($val['XjTime']));
		       
			   
			   $ahyguid= D('HyInfo')->where(array('WB_ID'=>session('wbid'),'hyCardNo'=>$val['cardNo']))->getField('hyCardGuid');
               $val['hydj']=D('Hylx')->where(array('WB_ID'=>session('wbid'),'Guid'=>$ahyguid))->getField('Name');	
               $val['cardNo']= "'".(string)$val['cardNo'];  			   
			}	

			
			return $list;
		}

       /*
		public function getHyShangjiTimerankingListByMap($map=array())
		{		
		   $wbid=$map['WB_ID'];
		   $wbname=D('WbInfo')->where(array('WBID'=>$wbid))->getField('WbName');
           $list=$this->where($map)
           ->field(array(
            'sum(TimeSize)'=> 'sumtime',
            'cardNo'=>'cardNo'
           	))
           ->order('sumtime desc')->group('cardNo,TimeSize')->limit(20)->select();

            $i=1;
           foreach($list as &$val)
           {
             $val['rankid']=$i;
             $val['WbName']=$wbname;
			 $map2=array();
			 $map2['hyCardNo']=$val['cardNo'];
			 $map2['WB_ID']=$wbid;
			 
			 $val['hyname']=D('HyInfo')->where($map2)->getField('hyname');
			 
             $i++;
           }	
           return array('list'=>$list,'count'=>20);
		}
		
		*/
		
		public function getHyShangjiTimerankingListByMap($map=array())
		{		
		   $wbid=$map['WB_ID'];
		   $wbname=D('WbInfo')->where(array('WBID'=>$wbid))->getField('WbName');
           $list=$this->where($map)
           ->field(array(
            'sum(TimeSize)'=> 'sumtime',
            'cardNo'=>'cardNo'
           	))
           ->order('sumtime desc')->group('cardNo')->select();
		   
		   
		   		   
		   $sort = array(  
           'direction' => 'SORT_DESC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序  
           'field'     => 'sumtime',       //排序字段  
		   );  
		   
		  
		   	$arrSort = array();  
			foreach($list as $uniqid => $row)
			{  
				foreach($row as $key=>$value)
				{  
					$arrSort[$key][$uniqid] = $value;  
				}  
			}  
			 if($sort['direction'])
			{  
				array_multisort($arrSort[$sort['field']], constant($sort['direction']), $list);  
			} 
		   		   
		   $alist=array();	   
		   $j=0;
           foreach($list as &$val)
           {
			  if($j<20) 
			  {
				$val['rankid']=$j+1;
                $val['WbName']=$wbname;			 
			    $map2=array();
			    $map2['hyCardNo']=$val['cardNo'];
			    $map2['WB_ID']=$wbid;			 
			    $val['hyname']=D('HyInfo')->where($map2)->getField('hyname');
				
				$alist[$j]=$list[$j];											

			  }	 		 
               $j++;
           }
		   	
           return array('list'=>$alist,'count'=>20);
		}

		public function getHyxfjerankingListByMap($map=array())
		{		
			$wbid=$map['WB_ID'];
			$wbname=D('WbInfo')->where(array('WBID'=>$wbid))->getField('WbName');

            $list=$this->where($map)
           ->field(array(
            'sum(je)'=> 'sumje',
            'cardNo'=>'cardNo'
           	))
           ->order('sumje desc')->group('cardNo,je')->limit(20)->select();
           $i=1;
           foreach($list as &$val)
           {
             $val['rankid']=$i;
             $val['WbName']=$wbname;
             $i++;
           }	
           return array('list'=>$list,'count'=>20);
		}
		
		


		
		
		public function getHyxfjerankingListByMap_ght($map=array())
		{		
			$wbid=$map['WB_ID'];
			$wbname=D('WbInfo')->where(array('WBID'=>$wbid))->getField('WbName');

            $list=$this->where($map)
           ->field(array(
            'sum(je)'=> 'sumje',
            'cardNo'=>'cardNo'
           	))->group('cardNo')->select();
			
		   
		   $sort = array(  
           'direction' => 'SORT_DESC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序  
           'field'     => 'sumje',       //排序字段  
		   );  
		   
		  
		   	$arrSort = array();  
			foreach($list as $uniqid => $row)
			{  
				foreach($row as $key=>$value)
				{  
					$arrSort[$key][$uniqid] = $value;  
				}  
			}  
			 if($sort['direction'])
			{  
				array_multisort($arrSort[$sort['field']], constant($sort['direction']), $list);  
			} 
		   		   
		   $alist=array();	   
		   $j=0;
           foreach($list as &$val)
           {
			  if($j<20) 
			  {
				$val['rankid']=$j+1;
                $val['WbName']=$wbname;			 
			    $map2=array();
			    $map2['hyCardNo']=$val['cardNo'];
			    $map2['WB_ID']=$wbid;			 
			    $val['hyname']=D('HyInfo')->where($map2)->getField('hyname');
				
				$alist[$j]=$list[$j];											

			  }	 		 
               $j++;
           }		   
            
           return array('list'=>$alist,'count'=>20);
		}
		
		
		

    }
