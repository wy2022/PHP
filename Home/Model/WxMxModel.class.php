<?php
    namespace Home\Model;
    use Think\Model;
    class WxMxModel extends Model 
    {
        protected $tableName = 'WWeiXinAddMoneyMx';

         public function getWxAddmoneyList_count($map=array())
         {
            return  $this->where($map)->count();
         }




	      public function getWxAddmoneyList($map=array(),$page = 1,$rows = 3)    
	      {                                                                            
	        $count=$this->where($map)->count();  
	        $list = $this->where($map)->page($page,$rows)->select();
	        return array('count'=>$count,'list'=>$list); 
	      }
    }
