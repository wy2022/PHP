<?php
    namespace Home\Model;
    use Think\Model;
    class HyaddmoneymxviewModel extends Model 
    {
        protected $tableName = 'View_all_union';
		
		public function getHyaddmoneymxListByMap_view_Count($map=array())
	   {
         $map['addmoney.ishy']=1;
	     $count = $this->alias('addmoney')->where($map)->count();
	     return $count;
	   }



		public function getHyaddmoneymxListByMap_view($map=array(),$order = '',$page = 1,$rows = 20)
		{
		    $map['addmoney.ishy']=1;
			$count = $this->alias('addmoney')->where($map)->count();
			$list  = $this->alias('addmoney')
			->join('left join WHyCardTable as hytable on addmoney.cardno= hytable.hyCardNo and addmoney.wb_id=hytable.WB_ID')
			
			->Field(array(
			
				'addmoney.syid'=>'SyId',
				'addmoney.cardno'=>'cardNo',
				'addmoney.je'=>'je',
				'addmoney.jlje'=>'jlJe',
				'addmoney.ctime'=>'cTime',
				'addmoney.wb_id'=>'WB_ID',
				'addmoney.type'=>'type',
				'addmoney.Operation'=>'Operation',
				'addmoney.sGuid'=>'sGuid',
				'hytable.hyname'=>'hyname',
				'hytable.hyCardGuid'=>'hyCardGuid',
				
				))->order($order)->page($page,$rows)->where($map)->select();	

				foreach ($list as &$val)
		        {                             
		            $val['jlJe']= sprintf("%.2f", $val['jlJe']);  
		            $val['je']= sprintf("%.2f", $val['je']);  
		            $val['hylevel']=D('Hylx')->where(array('WB_ID'=>session('wbid'),'Guid'=>$val['hyCardGuid']))->getField('Name'); 
		            $val['fqje']=D('HyfqJlMx')->where(array('WB_ID'=>session('wbid'),'FGuid'=>$val['sGuid']))->getField('Fqje'); 
		            $val['FqCount']=D('HyfqJlMx')->where(array('WB_ID'=>session('wbid'),'FGuid'=>$val['sGuid']))->getField('FqCount');
		            $val['cTime']=date('Y-m-d H:i:s',strtotime($val['cTime']));	
		        }
			
			return array('list'=>$list,'count'=>$count);
		}
		
    }
