<?php
    namespace Home\Model;
    use Think\Model;
    class WIniModel extends Model 
    {
      protected $tableName = 'WInitable';

      //有的话更新，没有的话新增
      public function postOneRecord($key,$value)
      {
          $wbid=session('wbid');
          $data['NValue']=$value;

          $bExist=$this->where(array('WB_ID'=>$wbid,'Name'=>$key))->find();
          if(!empty($bExist))
          {
             return $this->where(array('WB_ID'=>$wbid,'Name'=>$key))->data($data)->save();
          }
          else
          {
			$ini_insert_data=array(); 
			$ini_insert_data['Name']=$key;
			$ini_insert_data['NValue']=$value;
			$ini_insert_data['WB_ID']=$wbid;
			
            return $this->add($ini_insert_data);
          }  
          
      }

      public function addOneMobanRecord($wbid,$key,$value)
      {

          $data['WB_ID']=$wbid;
          $data['Name']=$key;
          $data['NValue']=$value;
          $bExist=$this->where(array('WB_ID'=>$wbid,'Name'=>$key))->find();
          if(!empty($bExist))
          {
             return $this->where(array('WB_ID'=>$wbid,'Name'=>$key))->data($data)->save();
          }
          else
          {
            return $this->data($data)->add();
          }  
          
      }
	  
	  
	  public function addOneMobanRecord_ght($wbid,$key,$value)
      {

          $data['WB_ID']=$wbid;
          $data['Name']=$key;
          $data['NValue']=$value;
         
         return $this->data($data)->add();
           
          
      }
	  
	  
	  public function addOneMobanRecord2($wbid,$key,$value)
      {
		$data['WB_ID']=$wbid;
		$data['Name']=$key;
		$data['NValue']=$value;
		return $this->data($data)->add();                
      }
	  

      public function getOneRecordByName($Name)
      {
          $wbid=session('wbid');
          $data['NValue']=$value;
          return $this->where(array('WB_ID'=>$wbid,'Name'=>$Name))->getField('NValue');      
      }
	  
	  public function getOneRecordByName2($wbid,$Name)
      {
         
          $data['NValue']=$value;
          return $this->where(array('WB_ID'=>$wbid,'Name'=>$Name))->getField('NValue');      
      }
  }
