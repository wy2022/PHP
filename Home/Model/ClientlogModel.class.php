<?php
    namespace Home\Model;
    use Think\Model;
    class ClientlogModel extends Model 
    {
        protected $tableName = 'OperatLog';


      public function getClientlogList_count($map=array()) //获取新闻
      {
        return $this->where($map)->count();
      } 
           
      public function getClientlogList($map=array(),$page = 1,$rows = 20)    
      {                                                                             
        $count=$this->where($map)->count();    
        $list = $this->where($map)->page($page,$rows)->order('cTime DESC')->select(); 

        foreach($list as &$val)
        {
          $val['cTime']=date('Y-m-d H:i:s',strtotime($val['cTime']));
        }  



        return array('count'=>$count,'list'=>$list); 
      }



  }
