<?php
namespace Home\Controller;
use Think\Controller;
class RateAPIController extends Controller
{	
	public  function  APIgetRateContent()
	{
		$key=C('_PhprateJmKey');
					
		
	    $wbinfo=I('post.bb');		
		$wbinfo= aesDeJm($wbinfo, $key);          
        $wbinfo=base64_decode($wbinfo);            
        $wbinfo = json_decode( $wbinfo,true );
				   
	    $map=array();
		$map['WbAccount']=$wbinfo['wbaccount'];
		$map['PassWord']=MD5($wbinfo['password'].'hc');	
	    $newwbid=D('WbInfo')->where($map)->getField('WBID');
		if(empty($newwbid))
		{
			$data['status']=-1;
			echo  json_encode($data);
		   	return;
		}
		
						
	    
	    $alldata=I('post.aa');	  
	    $alldata= aesDeJm($alldata, $key);          
        $alldata=base64_decode($alldata);            
        $alldata = json_decode( $alldata,true );
	        
	   $temprate = $alldata['temprate'];   
	   $fixed    = $alldata['fixed'];
	   $pclist   = $alldata['pclist'];
	   $hytype   = $alldata['hytype'];
	   $jiangliplan=$alldata['jiangliplan'];
	   
	   
	   
	   	  
	   $result=true;
	   D()->startTrans();
	   //插入计算机列表  
	
	   if(D('Computerlist')->where(array('WB_ID'=>$newwbid))->delete()===false)		   
	   {
		   $result=false; 
		   
	   }	   
	   
	   	$aTempsql= D('Computerlist')->getLastSql(); 
	
		   
	   for($i=0;$i<count($pclist);$i++)
	   {
		   $nowtime=date('Y-m-d H:i:s',time());
		   $apcinfo=array();
		   $apcinfo['Name']=$pclist[$i]['Name'];	   	   
		   $apcinfo['WB_ID']=$newwbid;
		   $apcinfo['GroupNameGuid']=$pclist[$i]['GroupNameGuid'];
		   $apcinfo['Guid']=$pclist[$i]['Guid'];   //是否需要此值
		   $apcinfo['insertTime']=$nowtime;		  	   
		   if(D('Computerlist')->add($apcinfo)===false)
		   {
			   $result=false;
			   writelog('--2-2-error--','postrate');
		   }	
           $aTempsql= D('Computerlist')->getLastSql(); 
		   		   
	    }	   
	    //插入会员类型
	
	   if(D('Hylx')->where(array('WB_ID'=>$newwbid))->delete()===false)		   
	   {
		   $result=false; 
		   writelog('--3-1--error-','postrate');
	   }		
       $aTempsql= D('Hylx')->getLastSql(); 
	     
       for($i=0;$i<count($hytype);$i++)
	   {
		   $ahytypeinfo=array();
		   $ahytypeinfo['WB_ID']=$newwbid;
		   $ahytypeinfo['Name']=$hytype[$i]['Name'];		   
		   $ahytypeinfo['SjDiscount']=$hytype[$i]['SjDiscount'];
		   $ahytypeinfo['SpDiscount']=$hytype[$i]['SpDiscount'];
		   $ahytypeinfo['SmallIntegral']=$hytype[$i]['SmallIntegral'];
		   $ahytypeinfo['Guid']=$hytype[$i]['Guid'];	
	  	   
		   if(D('Hylx')->add($ahytypeinfo)===false)
		   {
			   $result=false;
			   writelog('--3-2-error--','postrate');
		   }
            $aTempsql= D('Hylx')->getLastSql();       		        		   
	    } 
       
		
       // 插入固定费率
	   if(D('FixedRate')->where(array('WB_ID'=>$newwbid))->delete()===false)		   
	   {
		   $result=false; 
            writelog('--4-1--error-','postrate');		   
	   }
	    $aTempsql= D('FixedRate')->getLastSql(); 
		
	      
	   for($i=0;$i<count($fixed);$i++)
	   {
		   $nowtime=date('Y-m-d H:i:s',time());
		   $afixedinfo=array();
		   $afixedinfo['WB_ID']=$newwbid;
		   $afixedinfo['GroupGuid']=$fixed[$i]['GroupGuid'];	
		   $afixedinfo['Guid']=$fixed[$i]['Guid'];			   
		   $afixedinfo['name']=$fixed[$i]['name'];		   
		   $afixedinfo['TimeSize']=$fixed[$i]['TimeSize'];	   
		   $afixedinfo['je']=$fixed[$i]['je'];	       
           $afixedinfo['BeginTime']=$fixed[$i]['BeginTime'];	
           $afixedinfo['EndTime']=$fixed[$i]['EndTime'];	
           $afixedinfo['AutoChange']=$fixed[$i]['AutoChange'];		   
		   $afixedinfo['isBj']=$fixed[$i]['isBj'];	
		   $afixedinfo['OwnerHyLxGuid']=$fixed[$i]['OwnerHyLxGuid'];	
		   $afixedinfo['Lx']=$fixed[$i]['Lx'];	
		   $afixedinfo['inserttime']=$nowtime;			   	    	   
		   if(D('FixedRate')->add($afixedinfo)===false)
		   {
			   $result=false;
			   writelog('--4-2--error-','postrate');
		   }
           	$aTempsql= D('FixedRate')->getLastSql(); 
	        		   
	    } 	
	    
   		
       // 插入动态费率
       if(D('District')->where(array('WB_ID'=>$newwbid))->delete()===false)		   
	   {
		   $result=false; 
		   writelog('--5-1-error--','postrate');
	   }	
        $aTempsql= D('District')->getLastSql(); 

	   for($i=0;$i<count($temprate);$i++)
	   {		
		   $nowtime=date('Y-m-d H:i:s',time());
		   $atemprateinfo=array();
		   $atemprateinfo['WB_ID']=$newwbid;
		   $atemprateinfo['GroupName']=$temprate[$i]['GroupName'];	
		   $atemprateinfo['Guid']=$temprate[$i]['Guid'];			   
		   $atemprateinfo['HyCardGuids']=$temprate[$i]['HyCardGuids'];		   
		   $atemprateinfo['FlList']=json_encode($temprate[$i]['FlList']);	   
		   $atemprateinfo['isBj']=0;	       	   
		   $atemprateinfo['inserttime']=$nowtime;		
		   
		   if(D('District')->add($atemprateinfo)===false)
		   {
			   $result=false;
			   writelog('--5-3--error-','postrate');
		   }
		
		   $aTempsql= D('District')->getLastSql(); 
        			   
	    } 

		
       //插入奖励计划

	   
       if(D('AwardPlan')->where(array('WB_ID'=>$newwbid))->delete()===false)		   
	   {
		   $result=false; 
		   writelog('--6-1-error--','postrate');
	   }	
        $aTempsql= D('AwardPlan')->getLastSql(); 
			
	   for($i=0;$i<count($jiangliplan);$i++)
	   {		
   
		   $nowtime=date('Y-m-d H:i:s',time());
		   $aJingliPlaninfo=array();
		   $aJingliPlaninfo['WB_ID']=$newwbid;
		   $aJingliPlaninfo['Guid']=$jiangliplan[$i]['Guid'];
	       $aJingliPlaninfo['HyCardGuid']=$jiangliplan[$i]['HyCardGuid'];		  	   
		   $aJingliPlaninfo['AddMoney']=$jiangliplan[$i]['AddMoney'];			    		   
		   $aJingliPlaninfo['JLMoney']=$jiangliplan[$i]['JLMoney'];	
           $aJingliPlaninfo['Lx']=$jiangliplan[$i]['Lx'];			  	   
		   $aJingliPlaninfo['FqLx']=$jiangliplan[$i]['FqLx'];	
		   $aJingliPlaninfo['LimitTimeLx']=$jiangliplan[$i]['LimitTimeLx'];	
		   $aJingliPlaninfo['LimitDayLx']=$jiangliplan[$i]['LimitDayLx'];	
		   $aJingliPlaninfo['LimitTimeBegin']=$jiangliplan[$i]['LimitTimeBegin'];		   
		   $aJingliPlaninfo['LimitTimeEnd']=$jiangliplan[$i]['LimitTimeEnd'];
           $aJingliPlaninfo['FqJe']=$jiangliplan[$i]['FqJe'];
           $aJingliPlaninfo['FqCount']=$jiangliplan[$i]['FqCount'];
           $aJingliPlaninfo['LimitDays']=$jiangliplan[$i]['LimitDays'];
           $aJingliPlaninfo['Bljl']=$jiangliplan[$i]['Bljl'];	   
		   $aJingliPlaninfo['inserttime']=$nowtime;		   
		   if(D('AwardPlan')->add($aJingliPlaninfo)===false)
		   {
			   $result=false;
			   writelog('--6-2--error-','postrate');
		   }
		   $aTempsql= D('AwardPlan')->getLastSql(); 	      	   
	    }
	   
      
        if($result)
		{
			writelog('--7---','postrate');
			D()->commit();	
            //成功后设置标记
				
		    $TabChangeArr=array();
            $TabChangeArr['DeFl_Tag']= 1;  				
            $TabChangeArr['HyJLjh_Tag']= 1; 
            $TabChangeArr['ComputerList_Tag']=2;			  
			$TabChangeArr['GroupTable_Tag']=1;
			$TabChangeArr['HyLxTable_Tag']=1;
			
			$TabChangeArr['cTime'] = date('Y-m-d H:i:s',time());
			       	
		    $bExist=D('WbSetChange')->where(array('wb_id'=>$newwbid))->find();
		    if(!empty($bExist))
		    {
			  writelog('--7-1-error-','postrate');	
			  $TabChangeArrResult= D('WbSetChange')->where(array('wb_id'=>$newwbid))->save($TabChangeArr);					 
		    }
		    else
		    {	
                writelog('--7-2-error-','postrate');			
			   $TabChangeArrResult= D('WbSetChange')->add($TabChangeArr);				   
		    }	
						  				  				
			$data['status']=1;
		}
		else
        {
			writelog('--8---','postrate');
		  D()->rollback();	
		  $data['status']=-1;
		}			
		
		echo  json_encode($data);
				   	   	     	   
	}
		  
}