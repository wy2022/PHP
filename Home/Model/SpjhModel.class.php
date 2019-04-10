<?php
    namespace Home\Model;
    use Think\Model;
    class SpjhModel extends Model 
    {
      protected $tableName = 'WspJh';

      public function getSpjhcount($map=array()) 
      {
        return  $this->join('LEFT JOIN WspProductInfo ON WspJh.WB_ID=WspProductInfo.WB_ID and  WspJh.SpId=WspProductInfo.SpId')
        ->field('WspProductInfo.name as goodsname,WspJh.syid as syid,WspProductInfo.unit,WspJh.count,WspJh.rq,WspJh.machineId')->where($map)->count();  
      }   

      public function getSylist($wbid) 
      {       
        return $this->field('syid')->where(array('WB_ID'=>$wbid))->group('syid')->select();
      }   

      public function getSylistById($wbid) 
      {       
        return $this->field(array(
          'WB_ID'=>'wbid',
          'SyId'=>'syid'))
        ->where(array('WB_ID'=>$wbid))->group('WB_ID,SyId')->select();
      } 
 

      
      public function getSpjhList($map=array(),$page = 1,$rows = 10)    
      {                                                                             
        $count=$this->getSpjhcount($map);     
        $list = $this->join('LEFT JOIN WspProductInfo ON WspJh.WB_ID=WspProductInfo.WB_ID and  WspJh.SpId=WspProductInfo.SpId')
                     ->join('LEFT JOIN WCtrlIp ON WspJh.WB_ID=WCtrlIp.Wb_id and  WspJh.SyId=WCtrlIp.Syid')


          ->field(array(
          'wspproductinfo.name'=>'goodsname',
          'WspJh.SyId'=>'syid',
          'WspProductInfo.unit'=>'unit',
          'WspJh.rq'=>'rq',  
          'WspJh.count'=>'count',
          'WspJh.machineId'=>'machineId',
          'WCtrlIp.syname'=>'syname'
          ))->where($map)->page($page,$rows)->order('rq DESC')->select();
		  
		 foreach ($list as &$val) 
        {                         	   
		  if(empty($val['syname']))
		  {
			$val['syname']= $val['syid'];
		  }            		
        }
    
        return array('count'=>$count,'list'=>$list); 
      }
  }
