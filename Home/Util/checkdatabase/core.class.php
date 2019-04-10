<?php

require_once   "db.inc.php";
require_once   "config.inc.php";
require_once   "function.inc.php";



class Core extends DBSQL 
{
	public function __construct($svrname=ServerName,$port=DBPort,$usrname=UserName,$pass=PassWord,$db=DBName){
		parent::__construct($svrname,$port,$usrname,$pass,$db);
	}




	
	public function insertOneOrder($list='')
	{
		if($list)
		{
			$data=$k=$v=array();
			foreach($list as $key=>$val)
			{
				$k[]=$key;
				$v[]=":".$key;
				$data[":".$key]=$val;
			}
			$sql="INSERT INTO WxGongzhonghaoPayLog(".implode(',',$k).") VALUES(".implode(',',$v).") ";

			return $this->insert($sql,$data);
		}
		else 
		{
			return false;
		}
	}


	
	public function fnCheckBangdinginfo($wbid)
	{
		$sql = "select id,wbid,agent_id from wt_bangding where wbid=:wbid ";
		$data=array();
		$data[':wbid']=$wbid;

		return $ret['agent_id'];
	}
	
    public function fnGetOneAgentNameByAgentid($agent_id)
	{	
		$sql = "select agent_id,agent_realname from wt_agent where agent_id=:agent_id  ";
		$data=array();
		$data[':agent_id']=$agent_id;

		return $ret['agent_realname'];
	}
	
	
	$bangdinginfo=D('Bangding')->where(array('wbid'=>$wbid))->find();
		
		if(!empty($bangdinginfo))
		{
			$agentinfo=D('Agent')->where(array('agent_id'=>$bangdinginfo['agent_id']))->find();
			$agentinfo['bing_status']=$bangdinginfo['bing_status'];
			$agentinfo['dtInsertTime']=$bangdinginfo['dtInsertTime'];
			if($agentinfo['bing_status']==0)
			{
				$agentinfo['bing_text']='未绑定';
			}else if($agentinfo['bing_status']==1)
			{
				$agentinfo['bing_text']='已绑定';
			}else if($agentinfo['bing_status']==2)
			{
				$agentinfo['bing_text']='申请中';
			}
		}
		else
		{
		   	$agentinfo['bing_text']='未绑定';
			$agentinfo['bing_status']=0;	
		}	

        if(!empty($agentinfo))
		{
			$data['result']=1;
			$data['body']=$agentinfo;
		}
		else
        {
			$data['result']=-1;
		}




	public function fnGetOneOrderInfo($wbid,$post_order_no)
	{
         
        $sql = "select * from WxGongzhonghaoPayLog where post_order_no=:post_order_no and wbid=:wbid ";
	
		$data=array();
		$data[':post_order_no']=$post_order_no;
		$data[':wbid']=$wbid;

		return $this->getOne($sql,$data);
	}


	public function fnGetOneYumingInfoById($yuming_id='')
	{
         
        $sql = "select * from wt_yuming where yuming_id=:yuming_id ";
	
		$data=array();
		$data[':yuming_id']=$yuming_id;

		return $this->getOne($sql,$data);
	}




	public function fnQueryOneOrderPayStatusByWbIdAndOrderno($wbid,$post_order_no)
	{
	
		$sql = "select * from WxGongzhonghaoPayLog where wbid=:wbid and post_order_no=:post_order_no  ";
	
		$data=array();
		$data[':wbid']=$wbid;
		$data[':post_order_no']=$post_order_no;
		

		$ret= $this->getOne($sql,$data);

		if(!empty($ret))
		{
	        if( $ret['trade_status']==100)
			{
				$data['result']= 100;
			}
			else
			{
               $data['result']= 1;
			}
		}	
		else
		{
           $data['result']= -1;
		}	
		return $data;
	}


    public function getOneWbid($wbaccount=0)
	{
		$sql = "select WBID from wb_info where WbAccount=:wbaccount";
		$data=array(':wbaccount'=>$wbaccount);
		$ret =$this->getOne($sql,$data);
		return $ret['WBID'];
	}


	



   public function  getLastInsertSql($tablename,$insert_data_array=array())
   {
      if(!empty($tablename) && !empty($insert_data_array))
      {
        $s1= ' INSERT INTO '.$tablename.' (';
          foreach($insert_data_array as $key=>$val)
          {
             $s2.= $key.',';
             $s3.= "'".$val."'".',';
          }
          $s2= substr($s2,0,-1);
          $s3= substr($s3,0,-1);

          $sql= $s1.$s2.' )'.' VALUES ('.$s3.');';
      }
      else
      {
        $sql='';
      }  
      
      return $sql;
   }

   
    public function insertOneTongbuData_LzmPhpMessageTable($list='')
	{
        $guid=$list['aguid'];
		if($list)
		{
			
			$data=$k=$v=array();
			foreach($list as $key=>$val)
			{
				$k[]=$key;
				$v[]=":".$key;
				$data[":".$key]=$val;
			}
			
			$sql="INSERT INTO ".Prefix."LzmPhpMessageTable (".implode(',',$k).") VALUES(".implode(',',$v).") ";		
		    
		    $this->startTrans();

	        try 
		    {
		    	  
				$res= $this->insert($sql,$data);
			    // $tablsql = $this->getLastInsertSql('LzmPhpMessageTable',$list);

	            // $LzmTemBuff_insert_data=array();
			    // $LzmTemBuff_insert_data['guid']=$guid;
		        // $LzmTemBuff_insert_data['nr']=stripslashes($tablsql);
		        // $LzmTemBuff_insert_data['A4']=1;    
                 
		        // $res = $this->insertOneTongbuData_LzmTemBuff($LzmTemBuff_insert_data);
                $this->commit();             
			    return true;
		    }
		    catch (Exception $e) 
		    {
		    	 
		      $this->rollback();	
		      
		    }
		}
		else 
		{
			return false;
		}
	}






	function  insertOneTongbuData_LzmTemBuff($list='')
    {
        if(empty($list['nr']))
		{
          return false;
		}
	       
        if($list)
        {      
                                    
            $data=$k=$v=array();
			foreach($list as $key=>$val)
			{
				$k[]=$key;
				$v[]=":".$key;
				$data[":".$key]=$val;
			}
			$sql="INSERT INTO ".Prefix."LzmTemBuff(".implode(',',$k).") VALUES(".implode(',',$v).") ";
        }



        $LzmTemBuff_insert_result= $this->insert($sql,$data);  
        return  $LzmTemBuff_insert_result;
    } 


    

   function  getLastUpdateSql($tablename,$update_data_array=array(),$post_order_no,$wbid)
   {
      if(!empty($tablename) && !empty($update_data_array))
      {
        $s1='UPDATE '.$tablename.' SET ' ;
        foreach($update_data_array as $key=>$val)
        {
            $s2.= $key.'='."'".$val."'".',';      
        }
        $s2= substr($s2,0,-1);
        $s3= '  where post_order_no='."'".$post_order_no."'".' and wbid= '."'".$wbid."'";
         
        $sql=$s1.$s2.$s3;
      }
      else
      {
        $sql='';
      }  
      
      return $sql;
   }


       function  getLastUpdateSql2($tablename,$update_data_array=array(),$tiaojian_array=array())
   {
      if(!empty($tablename) && !empty($update_data_array))
      {
        $s1='UPDATE '.$tablename.' SET ' ;
        foreach($update_data_array as $key=>$val)
        {
            $s2.= $key.'='."'".$val."'".',';      
        }
        $s2= substr($s2,0,-1);

        $s3.=' where ';
        foreach($tiaojian_array as $key1=>$val1)
        {
            $s3.= $key1.'='."'".$val1."'".' and ';      
        }

        $s3=substr($s3,0,-5);

         
        $sql=$s1.$s2.$s3;
      }
      else
      {
        $sql='';
      }  
      
      return $sql;
   }



   public function  fnUpdateOrderTongbuStatus($wbid,$post_order_no,$list=array())
   {
      if($list)
		{
			$data=array();
			$data[':post_order_no']=$post_order_no;
			$data[':wbid']         =$wbid;


			foreach($list as $key=>$val)
			{
				$data[":".$key]=$val;
				$col[]=$key."=:".$key;
			}

			$sql="UPDATE WxGongzhonghaoPayLog SET ".implode(',',$col)." where post_order_no=:post_order_no and wbid=:wbid";
  
           
			$res= $this->update($sql,$data);
			return $res;
		}
		else 
		{
		  return false;	
		}
   }

	public function UpdateOneOrder($wbid,$post_order_no,$list=array())
	{
		if($list)
		{
			$data=array();
			$data[':post_order_no']=$post_order_no;
			$data[':wbid']         =$wbid;


			foreach($list as $key=>$val)
			{
				$data[":".$key]=$val;
				$col[]=$key."=:".$key;
			}

			$sql="UPDATE ".Prefix."WxGongzhonghaoPayLog SET ".implode(',',$col)." where post_order_no=:post_order_no and wbid=:wbid";


			

			$res= $this->update($sql,$data);


			return $res;
		}
		else 
		{
		  return false;	
		}
		
	} 
}