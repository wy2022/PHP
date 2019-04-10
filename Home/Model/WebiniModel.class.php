<?php
    namespace Home\Model;
    use Think\Model;
    class WebiniModel extends Model 
    {
      protected $tableName = 'wt_webinitable'; 

      public function getWebIniByWbid($wbid,$skey) 
      {             
        return $this->where(array('wbid'=>$wbid,'skey'=>$skey))->getField('svalue');     
      }   

      // public function getAllSpIniById($wbid) 
      // {             
        // return $this->where(array('Wb_id'=>$wbid))->select();       
      // } 

      public function postOneRecord($key,$value)
      {
          $wbid=session('wbid');
          $data['svalue']=$value;

          $bExist=$this->where(array('wbid'=>$wbid,'skey'=>$key))->find();
		  
          if(!empty($bExist))
          {
             return $this->where(array('wbid'=>$wbid,'skey'=>$key))->data($data)->save();
          }
          else
          {
			$ini_insert_data=array(); 
			$ini_insert_data['skey']=$key;
			$ini_insert_data['svalue']=$value;
			$ini_insert_data['wbid']=$wbid;
			$ini_insert_data['dtInsertTime']=date('Y-m-d H:i:s',time());
			
            return $this->data($ini_insert_data)->add();
          }  
          
      } 
	  
	  public function postOneRecord2($wbid,$key,$value)
      {
         // $wbid=session('wbid');
          $data['svalue']=$value;

          $bExist=$this->where(array('wbid'=>$wbid,'skey'=>$key))->find();
		  
          if(!empty($bExist))
          {
             return true;
          }
          else
          {
			$ini_insert_data=array(); 
			$ini_insert_data['skey']=$key;
			$ini_insert_data['svalue']=$value;
			$ini_insert_data['wbid']=$wbid;
			$ini_insert_data['dtInsertTime']=date('Y-m-d H:i:s',time());
			
            return $this->data($ini_insert_data)->add();
          }  
          
      }


		 
  }
