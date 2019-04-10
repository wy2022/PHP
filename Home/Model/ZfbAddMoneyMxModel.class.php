<?php
    namespace Home\Model;
    use Think\Model;
    class ZfbAddMoneyMxModel extends Model 
    {
        protected $tableName = 'WZfbAddMoneyMx';

         public function getzfbAddmoneyList_count($map=array())
         {
            return  $this->where($map)->count();
         }




	      public function getzfbAddmoneyList($map=array(),$page = 1,$rows = 3)    
	      {                                                                            
	        $count=$this->where($map)->count();  
	        $list = $this->where($map)->page($page,$rows)->select();
	        return array('count'=>$count,'list'=>$list); 
	      }
    }
