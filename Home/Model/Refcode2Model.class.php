<?php
    namespace Home\Model;
    use Think\Model;
    class Refcode2Model extends Model 
    {
      protected $tableName = 'wt_refcode2'; 
      public function getRefcodeNameByLowValue($low_value,$meaning) //获取新闻
       {
         if(empty($low_value))
         {
           return;
         } 

          return $this->where(array('low_value'=>$low_value,'meaning'=>$meaning))->getField('mid_value');            
       }  


  }
