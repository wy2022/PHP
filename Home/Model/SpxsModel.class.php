<?php
    namespace Home\Model;
    use Think\Model;
    class SpxsModel extends Model 
    {
        protected $tableName = 'WspXs';


      public function getSpxscount($map=array()) //获取新闻
       {

          return   $this->join('LEFT JOIN WspProductInfo ON WspXs.WB_ID=WspProductInfo.WB_ID and  WspXs.SpId=WspProductInfo.SpId')
        ->field('WspProductInfo.name as goodsname,WspProductInfo.unit,WspXs.syid,WspXs.Sl as count,WspXs.Rq as rq,WspXs.totalprice,WspXs.machineid as machineId')->where($map)->count();
      
       }   

        public function getSylist($wbid) //获取新闻
       {       
          return $this->field('syid')->where(array('WB_ID'=>$wbid))->group('syid')->select();
       }   

      
      public function getSpxsList($map=array(),$page = 1,$rows = 10)   
      {                                                                
        $count=$this->getSpxscount($map);       
        $list = $this->join('LEFT JOIN WspProductInfo ON WspXs.WB_ID=WspProductInfo.WB_ID and  WspXs.SpId=WspProductInfo.SpId')
                     ->join('LEFT JOIN WCtrlIp ON WspXs.WB_ID=WCtrlIp.Wb_id and  WspXs.SyId=WCtrlIp.Syid')
        ->field('WspProductInfo.name as goodsname,WspProductInfo.unit,WspXs.syid,WspXs.Sl as count,WspXs.Rq as rq,WspXs.totalprice,WspXs.machineid as machineId,WCtrlIp.syname as syname')
        ->where($map)->page($page,$rows)->order('Rq DESC')->select();
		
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
