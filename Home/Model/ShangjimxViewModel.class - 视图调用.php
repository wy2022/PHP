<?php
    namespace Home\Model;
    use Think\Model\ViewModel;
    class ShangjimxViewModel extends ViewModel 
    {
     

	    public $viewFields = array(
	     'WHyCardTable_XfMx'=>array('id','WB_ID','cardNo','cpName','SjTime','XjTime','foregift','yje','je','sjLx','qtje','bz','EndOperate','zjNo','_table'=>'WHyCardTable_XfMx'),
	     'WHyCardTable'=>array('hyname'=>'UserName', '_on'=>'WHyCardTable_XfMx.WB_ID = WHyCardTable.WB_ID','_table'=>'WHyCardTable'),
	     'WHyLxTable'=>array('Name'=>'Cardlx', '_on'=>'WHyCardTable.hyCardGuid = WHyLxTable.Guid','_table'=>'WHyLxTable'),
	   );

   //    public function getShangjimxListByMap_Count($map=array())
	  // {
  	
	  //   $count = $this->where($map)->count();
	  //   return $count;
	  // }


		public function getShangjimxListByMap($map=array(),$order = '',$page = 1,$rows = 20)
		{
			
          	
	        // $count = $this->where($map)->count();
			$list  = $this->where($map)->page($page,$rows)->select();
			$count=count($list);
				
			return array('list'=>$list,'count'=>$count);
		}
    }
