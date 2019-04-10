<?php
    namespace Home\Model;
    use Think\Model;
    class HyInfoModel extends Model 
    {
        protected $tableName = 'WhyCardTable';

       public function getHyNamebyWbidAndHyCardNo($map=array()) //获取新闻
       {
         return $this->where($map)->find();
       }  

       public function getOneHyInfoByWbidAndWxid($wbid,$wxid) //获取新闻
       {

        $map['wxgongzhonghao.wbid']=$wbid;
        $map['wxgongzhonghao.wxid']=$wxid;
         return  $this->join('left join wxgongzhonghao  on WhyCardTable.hycardno=wxgongzhonghao.hycardno and wxgongzhonghao.wbid=WhyCardTable.wb_id')
         ->field(array( 
        'WhyCardTable.HyCardNo',
        'WhyCardTable.hyname' ,
        'WhyCardTable.surplus' ,
        'WhyCardTable.Jlje'  
        ))->where($map)->find();

       } 
 
  }
