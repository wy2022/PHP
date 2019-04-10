<?php
    namespace Home\Model;
    use Think\Model;
    class  TokenModel extends Model 
    {
        protected $tableName = 'cs_goods_token';
		public function checkToken($wbid,$guid)
        {  
          
           $tokeninfo=$this->where(array('wbid'=>$wbid))->find();
           if(empty($tokeninfo))
           {
				$ini_insert_data=array(); 
				$ini_insert_data['wbid']=$wbid;
				$ini_insert_data['guid']=$guid;
				$ini_insert_data['dtUpdateTime']=date('Y-m-d H:i:s',time());
				
				$res =$this->data($ini_insert_data)->add();
				
				$result=1;

           }
           else
           {
				$db_guid=$tokeninfo['guid']; 
				if($db_guid==$guid)
				{
					//$data['status']=-1; //重复
					$result=-1;
				}
				else
				{    
					$ini_update_data=array(); 			
					$ini_update_data['guid']=$guid;
					$ini_update_data['dtUpdateTime']=date('Y-m-d H:i:s',time());
					$res= $this->where(array('wbid'=>$wbid))->save($ini_update_data);  
				   // $data['status']=1;
					 $result=1;				
				} 				
				       
           }
		     return $result;
		}
    
  }
