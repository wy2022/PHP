<?php
    namespace Home\Model;
    use Think\Model;
    class SpCtrlIpModel extends Model 
    {
      protected $tableName = 'WCtrlIp'; 

      public function getSpIniById($wbid,$syid) 
      {             
        return $this->where(array('Wb_id'=>$wbid,'Syid'=>$syid))->find();     
      }   

      public function getAllSpIniById($wbid) 
      {             
        return $this->where(array('Wb_id'=>$wbid))->select();       
      } 

      public function updateSpIniById($map=array(),$data=array()) 
      {             
        return $this->where($map)->save($data);       
      }   

      // public function addSpIniById($wbid,$syid)
      // {             

      //   $bExist=$this->getSpIniById($wbid,$syid);

      //   if($bExist)
      //   {
      //     return null;
      //   }
      //    else
      //   {
      //     return $this->data(array('wbid'=>$wbid,'syid'=>$syid))->add(); 
      //   } 

              
      // }   
  }
