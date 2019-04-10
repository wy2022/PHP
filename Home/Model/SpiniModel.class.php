<?php
    namespace Home\Model;
    use Think\Model;
    class SpiniModel extends Model 
    {
      protected $tableName = 'WghtIniTable'; 

      public function getSpIniById($wbid,$syid) 
      {             
        return $this->where(array('wbid'=>$wbid,'syid'=>$syid))->find();     
      }   

      public function getAllSpIniById($wbid) 
      {             
        return $this->where(array('wbid'=>$wbid))->select();       
      } 

      public function updateSpIniById($map=array(),$data=array()) 
      {             
        return $this->where($map)->save($data);       
      }   

      public function addSpIniById($wbid,$syid)
      {             

        $bExist=$this->getSpIniById($wbid,$syid);

        if($bExist)
        {
          return null;
        }
         else
        {
          return $this->data(array('wbid'=>$wbid,'syid'=>$syid))->add(); 
        } 

              
      }   
  }
