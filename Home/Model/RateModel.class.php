<?php
namespace Home\Model;
use Think\Model;
class RateModel extends Model{
    protected $tableName = 'WFLTable';

    private static $_weekday = array(
        'MON',
        'TUE',
        'WED',
        'THU',
        'FRI',
        'SAT',
        'SUN'
    );

  
	
	  public function getRate($vipLevelId,$districtId)
	  {
        $vipLevelGuid = D('VipLevel')->getVipLevelGuidById($vipLevelId);
		
		$flList=D('District')->where(array('id'=>$districtId))->getField('FlList');
		
		$listarray=json_decode($flList,true);
			
		for($i=0;$i<count($listarray);$i++)
		{  
	        if($listarray[$i]['guid']==$vipLevelGuid)
			{
				 $k=0;
				 $sendlist=array(); 		 
				 $tempfllist=array();
				 $tempfllist= $listarray[$i]['fl'];
				 
				 for($j=0;$j<7;$j++)
				 {
					 for($m=0;$m<24;$m++)
					 {
						$sendlist[$k] = sprintf("%.1f",$tempfllist[$j][$m]);
						 
						$k++;
					 }	 		
				 }	
				 
				 $startprice=$listarray[$i]['m_StarPrice'];
				 $minprice=$listarray[$i]['m_SmallPrice'];
				 $ignoreminute=$listarray[$i]['m_IgnoreTime'];
				 $enable_hour=$listarray[$i]['m_EffectiveTime'];
				 
				
				 //添加一个会员guid 下的数据
				 

                  
				  

				  
				    $onevipLevelinfo = D('VipLevel')->where(array('WB_ID'=>session('wbid'),'Guid'=>$vipLevelGuid))->find();					
					$SmallIntegral= $onevipLevelinfo['SmallIntegral']*1;
					$SjDiscount   = $onevipLevelinfo['SjDiscount']*1;
					$SpDiscount   = $onevipLevelinfo['SpDiscount']*1;
					
				 
				 
			}	
	         	
		}
			
				$pricelist=array_unique($sendlist);

		
		$n=0;
		foreach($pricelist as $key=>$val)
		{
		  $pricelist1[$n]=$val;
          $n++;		  
		}
		
		
		
		  $list=array();
		  $list['rate']=$sendlist;
		  $list['pricelist']=$pricelist1;
		  $list['startprice']=$startprice;
		  $list['minprice']=$minprice;
		  $list['ignoreminute']=$ignoreminute;
		  $list['enable_hour']=$enable_hour;		  
		  $list['SmallIntegral']=$SmallIntegral;
		  $list['SjDiscount']=$SjDiscount;
		  $list['SpDiscount']=$SpDiscount;

		  
		
		

		
		  // $list=array('rate'=>$sendlist,'pricelist'=>$pricelist1,'startprice'=>$startprice,'minprice'=>$minprice,'ignoreminute'=>$ignoreminute,'enable_hour'=>$enable_hour);
			 
    
			return $list;	
    }

    public function updateRate($districtId,$vipLevelId,$time,$rate)
    {

        $vipLevelGuid = D('VipLevel')->getVipLevelGuidById($vipLevelId);
        $districtGuid = D('District')->getDistrictGuidById($districtId);
		
		
		$flList=D('District')->where(array('id'=>$districtId))->getField('FlList');	

		$listarray=json_decode($flList,true);//解json
				
		for($i=0;$i<count($listarray);$i++) //有几个会员等级
		{  
	        if($listarray[$i]['guid']==$vipLevelGuid)   //找到要更新的会员类型
			{
				 $k=0;
				 $sendlist=array(); 		 // 拆分后的费率168
				 $tempfllist=array();       
				 $tempfllist= $listarray[$i]['fl'];   //要更新的那个费率
				 
				 for($j=0;$j<7;$j++)
				 {
					 for($m=0;$m<24;$m++)
					 {
						$sendlist[$k] = sprintf("%.1f",$tempfllist[$j][$m]);
						 
						$k++;
					 }	 		
				 }	
			}	      	
		}
		
		

		
		for($n=0;$n<count($time);$n++)  //更新的下标个数
		{
			for($i=0;$i<count($sendlist);$i++)  //更显的168个点
			{
				if($i==$time[$n])   //等于更新下标的值
				{			
				   $sendlist[$i]=$rate;  //改变值
				}
			}	
		}	

				
		//168个点 拆分
		
	    for($i=0;$i<count($listarray);$i++)
		{  
	        if($listarray[$i]['guid']==$vipLevelGuid)
			{			
				 $k=0;					 						 
				 for($j=0;$j<7;$j++)
				 {
					 for($m=0;$m<24;$m++)
					 {					
					   $list[$j][$m]	= $sendlist[$k];
					   $k++;					
					 }	 		
				 }	
				 $listarray[$i]['fl']= $list;
			}	      	
		}
		
	
		
	
		
		$res=D('District')->where(array('WB_ID'=>session('wbid'),'id'=>$districtId))->setField('FlList',json_encode($listarray));

		if(!empty($res))
		{
          return true;
		}
		else
		{
            return false;
		}	
	
        
    }


    public function updateRateConfig($districtId,$vipLevelId,$enableHour,$ignoreMinute,$minPrice,$startPrice){

        $vipLevelGuid = D('VipLevel')->getVipLevelGuidById($vipLevelId);
        $districtGuid = D('District')->getDistrictGuidById($districtId);
		// var_dump($districtGuid);
        //0-整点有效；1-半点有效
        switch($enableHour){
            case 'half':
                $enableHour = 1;
                break;
            case 'integral':
                $enableHour = 0;
                break;
            default:
                $enableHour = 1;
        }

		
	    $flList=D('District')->where(array('id'=>$districtId))->getField('FlList');	
		$listarray=json_decode($flList,true);//解json
				
		for($i=0;$i<count($listarray);$i++) //有几个会员等级
		{  
	        if($listarray[$i]['guid']==$vipLevelGuid)   //找到要更新的会员类型
			{

				$listarray[$i]['m_StarPrice']=sprintf("%.2f",$startPrice);
				$listarray[$i]['m_SmallPrice']=sprintf("%.2f",$minPrice);
				$listarray[$i]['m_IgnoreTime']=$ignoreMinute;
				$listarray[$i]['m_EffectiveTime']=$enableHour;
			
			
				
				$onevipLevelinfo = D('VipLevel')->where(array('WB_ID'=>session('wbid'),'Guid'=>$vipLevelGuid))->find();					
				
				$SmallIntegral= $onevipLevelinfo['SmallIntegral'];
				$SjDiscount   = $onevipLevelinfo['SjDiscount'];
				$SpDiscount   = $onevipLevelinfo['SpDiscount'];
				
				
				 $listarray[$i]['m_SmallIntegral']=$SmallIntegral*1;
                 $listarray[$i]['m_SjDiscount']=$SjDiscount*1;
                 $listarray[$i]['m_SpDiscount']=$SpDiscount*1; 				 
				
				
			}	      	
		}
	
		$res=D('District')->where(array('WB_ID'=>session('wbid'),'Guid'=>$districtGuid))->setField('FlList',json_encode($listarray));
		
        if(!empty($res))
		{
          return true;
		}
		else
		{
            return false;
		}	
	
    }

	
	
	

    public function deleteViplevel($CardGuid)
	{
	    $map=array();
		$map['WB_ID']=session('wbid');
		$vipCard_count=D('VipLevel')->where($map)->count();
		$District= D('District')->where($map)->select();// 一元区  三元区
		for($i=0;$i<count($District);$i++)
		{		
	        $old_listarray=array();
			$old_listarray=json_decode($District[$i]['FlList'],true);//解json
			
		
			
			$new_listarray=array();
			$k=0;
			for($j=0;$j<$vipCard_count;$j++)
			{
				if($old_listarray[$j]['guid'] != $CardGuid)
				{
					$new_listarray[$k]=$old_listarray[$j];
					$k++;
				}		
            }
			
			$res=D('District')->where(array('WB_ID'=>$map['WB_ID'],'Guid'=>$District[$i]['Guid']))->setField('FlList',json_encode($new_listarray));
			
			
			$one_HyCardGuids='';
			$one_HyCardGuids_list='';
			$one_HyCardGuids_arraylist=array();
			
			$one_HyCardGuids_list = $District[$i]['HyCardGuids'];
			
			
			
			
			$one_HyCardGuids_arraylist=explode(',',$one_HyCardGuids_list);
			

			
			
			
			for($m=0;$m<count($one_HyCardGuids_arraylist)-1;$m++)
			{
				
				
				if($one_HyCardGuids_arraylist[$m] != $CardGuid)
				{
					
					
					$one_HyCardGuids= $one_HyCardGuids.$one_HyCardGuids_arraylist[$m].',';
					
			
				}
			}	
			

			
		$res1=D('District')->where(array('WB_ID'=>$map['WB_ID'],'Guid'=>$District[$i]['Guid']))->setField('HyCardGuids',$one_HyCardGuids);
			
	
       		
		}	
        
    }
}