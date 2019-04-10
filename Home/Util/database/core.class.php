<?php

require_once   "db.inc.php";
require_once   "config.inc.php";
require_once   "function.inc.php";



class Core extends DBSQL 
{
	public function __construct($svrname=ServerName,$port=DBPort,$usrname=UserName,$pass=PassWord,$db=DBName){
		parent::__construct($svrname,$port,$usrname,$pass,$db);
	}


    public function getOneDayShouru($wbaccount=0)
	{
		$sql = "  exec [dbo].[YearMDSum] 1884, 1, 100";
		$data=array();
	
		$ret =$this->select($sql,$data);
		return $ret;
	}


	public function getOneWbid($wbaccount=0)
	{
		$sql = "select WBID from wb_info where WbAccount=:wbaccount";
		$data=array(':wbaccount'=>$wbaccount);
		$ret =$this->getOne($sql,$data);
		return $ret['WBID'];
	}

	public function getOneWbidByOrderno($post_order_no=0)
	{
		$sql = "select * from WBZhifubao where Post_Order_no=:post_order_no";
		$data=array(':post_order_no'=>$post_order_no);
		$ret =$this->getOne($sql,$data);
		return $ret['wbid'];
	}


	public function getOneMoneyByOrderno($wbid,$post_order_no='')
	{
		$sql = "select * from WBZhifubao where Post_Order_no=:post_order_no  and wbid=:wbid ";
		$data=array();
		$data[':wbid']=$wbid;
		$data[':post_order_no']=$post_order_no;
		$ret =$this->getOne($sql,$data);
		return $ret['total_OrderMoney'];
	}


	





	public function insertOneZfbOrder($list=array())
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
			$sql="INSERT INTO ".Prefix."WBzhifubao(".implode(',',$k).") VALUES(".implode(',',$v).") ";




			$res= $this->insert($sql,$data);
			return $res;
		}
		else 
		{
			return false;
		}
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
				
				/* 以下代码同步时开启
			    $tablsql = $this->getLastInsertSql('LzmPhpMessageTable',$list);                     
	            $LzmTemBuff_insert_data=array();
			    $LzmTemBuff_insert_data['guid']=$guid;
		        $LzmTemBuff_insert_data['nr']=stripslashes($tablsql);
		        $LzmTemBuff_insert_data['A1']=1;

		        $res = $this->insertOneTongbuData_LzmTemBuff($LzmTemBuff_insert_data);
				
				*/
				
				
                $this->commit();             
			    return true;
		    }
		    catch (Exception $e) 
		    {
		      $this->rollback();	
		      writelog('-insertOneTongbuData_LzmPhpMessageTable-'.$e->getMessage(),'sqlerror');  
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


	function  PostTopUpdateDataToWb_lzmByWbid_batai($awbid,$cmdtype,$tablsql)
    {
	    try 
	    {
	        $wbid=$awbid;
	        $guid=create_guid();
	       
	        $aPostData['Wbid']=$wbid;
	        $aPostData['Topic']='a';
	        $aPostData['CmdType']=$cmdtype;
	        $aPostData['Data']='';
	        $aPostData['MessageTime']=date('Y-m-d h:i:s',time());
	        $aPostData['MessageID']=$guid;
	        
	        $LzmTemBuff_insert_data['guid']='ght_zfb_bt_'.$guid;
	        $LzmTemBuff_insert_data['nr']=stripslashes($tablsql);
	        $LzmTemBuff_insert_data['A1']=1;


	        writelog('$LzmTemBuff_insert_data'.json_encode($LzmTemBuff_insert_data),'sql_tongbu');
	     
	  
	        if( $LzmTemBuff_insert_data['nr'] != '')
	        {      
	                                    
	            $data=$k=$v=array();
				foreach($LzmTemBuff_insert_data as $key=>$val)
				{
					$k[]=$key;
					$v[]=":".$key;
					$data[":".$key]=$val;
				}
				$sql="INSERT INTO ".Prefix."LzmTemBuff(".implode(',',$k).") VALUES(".implode(',',$v).") ";


	          
	          $LzmTemBuff_insert_result= $this->insert($sql,$data);;

	         
	          if($LzmTemBuff_insert_result)
	          {     
	                                             
	            $res=tcpsend_data('58701', '192.168.8.188',$aPostData, 0); 
	        
	          }                  
	        } 
	        else
	        {
	    
	          writelog('空白Sql'.$LzmTemBuff_insert_data['nr'],'LzmSql'); 
	        }

	 

	        return $res;
	    } 
	    catch (Exception $e) 
	    {
	      writelog('-PostTopUpdateDataToWb_lzmByWbid_batai-'.$e->getMessage(),'sqlerror');  
	    }
    } 





	public function insertOneZfbLog($list='')
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
			$sql="INSERT INTO ".Prefix."WBZhifubaoTemLog(".implode(',',$k).") VALUES(".implode(',',$v).") ";
                    
			return $this->insert($sql,$data);
		}
		else 
		{
			return false;
		}
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



	public function  fnQueryOneOrderInfo($wbid,$post_order_no)
	{
       	$sql = "select * from WBZhifubao where Post_Order_no=:post_order_no  and wbid=:wbid ";
		$data=array();
		$data[':wbid']=$wbid;
		$data[':post_order_no']=$post_order_no;
		

		return $this->getOne($sql,$data);
	}

    public function getOneOrderInfo($wbid,$post_order_no)
	{
         
        $sql = "select * from WBZhifubao where Post_Order_no=:post_order_no and wbid=:wbid ";
	
		$data=array();
		$data[':post_order_no']=$post_order_no;
		$data[':wbid']=$wbid;

		return $this->getOne($sql,$data);
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
			$sql="UPDATE ".Prefix."WBZhifubao SET ".implode(',',$col)." where Post_Order_no=:post_order_no and wbid=:wbid";
		    $res= $this->update($sql,$data);
			
			$tiaojian_array=array();
			$tiaojian_array['Post_Order_no']=$post_order_no;
			$tiaojian_array['wbid']=$wbid;
			
            $sendstr=$this->getLastUpdateSql2('WBZhifubao',$list,$tiaojian_array);           
           
            writelog($sendstr,'sql_tongbu');
			
            /* 以下数据同步时开启
			if($res)
			{				
              $result =$this->PostTopUpdateDataToWb_lzmByWbid_batai($wbid,'Php_To_Top_Sql',$sendstr);
	          if(!empty($result))
	          {
	            writelog('UpdateOneOrder lzm 数据插入 更新成功'.$sendstr,'sql_tongbu'); 
	          }
	          else
	          {
	            writelog('UpdateOneOrder  lzm数据插入 更新失败'.$sendstr,'sql_tongbu'); 
	          } 
			}
			*/
			return $res;





		}else 
		{
		  return false;	
		}	
	}
	
	
	
	
	public function fnUpdateOneOrder($wbid,$post_order_no,$list=array())
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

			$sql="UPDATE ".Prefix."WBZhifubao SET ".implode(',',$col)." where post_order_no=:post_order_no and wbid=:wbid";
			$res= $this->update($sql,$data);

			return $res;
		}
		else 
		{
		  return false;	
		}		
	} 

	public function UpdateOneOrder2($post_order_no,$list=array())
	{
		if($list)
		{
			$data=array();
			$data[':post_order_no']=$post_order_no;
		


			foreach($list as $key=>$val)
			{
				$data[":".$key]=$val;
				$col[]=$key."=:".$key;
			}
			$sql="UPDATE ".Prefix."WBZhifubao SET ".implode(',',$col)." where Post_Order_no=:post_order_no ";
			return $this->update($sql,$data);
		}else return false;
	}
	
	
	function  PostTopUpdateDataToWb_lzmByWbid($awbid,$cmdtype,$tablsql)
    {
    
   
        $wbid=$awbid;
        $guid=create_guid();
       
        $aPostData['Wbid']=$wbid;
        $aPostData['Topic']='a';
        $aPostData['CmdType']=$cmdtype;
        $aPostData['Data']='';
        $aPostData['MessageTime']=date('Y-m-d h:i:s',time());
        $aPostData['MessageID']=$guid;

        $LzmTemBuff_insert_data=array();
        $LzmTemBuff_insert_data['guid']='ght_wx_'.$guid;
        $LzmTemBuff_insert_data['nr']=stripslashes($tablsql);
        $LzmTemBuff_insert_data['A1']=1;
     
        
        if( $LzmTemBuff_insert_data['nr'] != '')
        {                    
            $list=$LzmTemBuff_insert_data;
	
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
				$LzmTemBuff_insert_result= $this->insert($sql,$data);
				if($LzmTemBuff_insert_result)
				{
				   writelog('同步前的数据:  '.'wbid='.$wbid.' '.' guid= '.$guid.' '.' sql= '.$tablsql.'  LzmTemBuff_insert_data='.json_encode($LzmTemBuff_insert_data),'tongbu');
				   $res=tcpsend_data('58701', '192.168.8.188',$aPostData, 0); 
					writelog(json_encode($res),'tcpsend_data');
					return true;
				}	
				
			}
			else 
			{
				return false;
			}                   
        } 
        else
        {
          writelog('空白Sql'.$LzmTemBuff_insert_data['nr'],'LzmSql'); 
		  return false;
        }   
    }

	
	
	
	
	
	
}