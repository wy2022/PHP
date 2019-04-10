<?php
namespace Home\Model;
use Think\Model;
class BarModel extends Model{
	protected $tableName = 'WHyCardTable';	//数据表名



  public function getHyListByMap_Count($map=array())
  {
    $map['deleted']=0;
    $count = $this->alias('hy')->where($map)->count();
    return $count;
  }


	public function getHyListByMap($map=array(),$order = '',$page = 1,$rows = 20)
	{

        $count = $this->alias('hy')->where($map)->count();
	    $list  = $this->alias('hy')->where($map)->order($order)->page($page,$rows)->select();
		
		$hylxlist  = $this->field('sum(1) as sumcount,hyCardGuid')->where($map)->group('hyCardGuid')->select();
		
	    $benjinje=0;
		$jlje=0;
		foreach ($list as &$val)
		{                             
		  $val['surplus'] = sprintf("%.2f",$val['surplus']); 
		  $val['NewTime']=date('Y-m-d H:i:s',strtotime($val['NewTime']));
		  $val['LastSjTime']=date('Y-m-d H:i:s',strtotime($val['LastSjTime']));    
		  $val['integral']=$val['integral'].'/'.$val['DhIntegral']; 
		  $val['hylx']=D('Hylx')->where(array('WB_ID'=>session('wbid'),'Guid'=>$val['hyCardGuid']))->getField('Name'); 	  
          
		   
            $benjinje=$benjinje + $val['surplus'];		
            $jlje=$jlje + $val['Jlje'];				
		}
			   
        
		
		return array('list'=>$list,'count'=>$count,'hylxlist'=>$hylxlist,'benjinje'=>$benjinje,'jlje'=>$jlje);
	}

  public function expBar($map=array())
  {
    $map['deleted']=0;
    $count = $this->alias('bar')->where($map)->count();
    $list  = $this->alias('bar')->where($map)->order($order)->page($page,$rows)->select();
      foreach ($list as &$val)
        {                             
          // $val['nProvince'] = D('Area')->getAreaNameById($val['nProvince']); 
          $val['nCity'] = D('Area')->getAreaNameById($val['nCity']); 
          $val['nDistrict'] = D('Area')->getAreaNameById($val['nDistrict']); 
          // $val['area'] = $val['nProvince'].'-'.$val['nCity'].'-'.$val['nDistrict'];
          $val['area'] = $val['nCity'].'-'.$val['nDistrict'];
          $val['nClientCount'] =$val['nOnlineCount'].'/'.$val['nDefaultPcCount'];     
          $val['nOnlineStatus']   = D('Refcode')->getRefcodeNameByLowValue($val['nOnlineStatus'],'OnlineStatus'); 
          $val['nBusinessStatus'] = D('Refcode')->getRefcodeNameByLowValue($val['nBusinessStatus'],'BusinessStatus'); 
          $val['nSoftStatus']    = D('Refcode')->getRefcodeNameByLowValue($val['nSoftStatus'],'SoftStatus'); 
          $val['sBarName2'] = $val['sBarName'];
          
        }
    
    return $list;
  }





	

	/**
	 *	新增网吧
	 *	@access public
	 *	@param array $data 网吧信息
	 *
	 *	@return boolean|string
	 */
	public function insertBar($data)
	{
		if($this->create($data)){
			if($this->add() !== false){
				return true;
			}else{
				return false;
			}
		}else{
			return $this->getError();
		}
	}
	public function deleteBar($wbid)
	{
		if(!empty($wbid))
		{
			$res=$this->where(array('wbid'=>$wbid,'deleted'=>0))->setField('deleted',1);
		}

		if(!empty($res))
		{
          return true;
		}
		else
		{
		  return false;
		}	
	}

	
	// public function getAgentIdByUserId($userid)
	// {
	// 	$agent_id = $this->cache(true)->where(array('userid'=>$userid))->getField('agent_id');
	// 	return empty($agent_id)?0:$agent_id;
	// }
	
	public function getAllWbList() 
	{
		return $this->field(array('wbid,sBarName'))->where(array('deleted'=>0))->select();
	}

	public function getOneWbListByWbid($wbid) 
	{
        $list=$this->where(array('wbid'=>$wbid))->find();

        
		    $list['nOnlineStatus']   = D('Refcode')->getRefcodeNameByLowValue($list['nOnlineStatus'],'OnlineStatus'); 
        $list['nBusinessStatus'] = D('Refcode')->getRefcodeNameByLowValue($list['nBusinessStatus'],'BusinessStatus'); 
        $list['nSoftStatus']     = D('Refcode')->getRefcodeNameByLowValue($list['nSoftStatus'],'SoftStatus');      
        $list['areaid'] = $list['nDistrict']; 

        // $list['nProvince'] = D('Area')->getAreaNameById($list['nProvince']); 
        $list['nCity'] = D('Area')->getAreaNameById($list['nCity']); 
        $list['nDistrict'] = D('Area')->getAreaNameById($list['nDistrict']); 
        // $list['sArea'] = $list['nProvince'].'-'.$list['nCity'].'-'.$list['nDistrict'];

        $list['sArea'] = $list['nCity'].'-'.$list['nDistrict'];
        $list['nClientCount'] =$list['nOnlineCount'].'/'.$list['nDefaultPcCount'];
        // $list['nAccessMode'] =D('Refcode')->getRefcodeNameByLowValue($list['nAccessMode'],'AccessMode');
		
		return $list;
	}


  public function getOneWbInfoByWbid($wbid) 
  {
        $list=$this->where(array('wbid'=>$wbid))->find();
        $list['nOnlineStatus']   = D('Refcode')->getRefcodeNameByLowValue($list['nOnlineStatus'],'OnlineStatus'); 
        // $list['nBusinessStatus'] = D('Refcode')->getRefcodeNameByLowValue($list['nBusinessStatus'],'BusinessStatus'); 
        $list['nSoftStatus']     = D('Refcode')->getRefcodeNameByLowValue($list['nSoftStatus'],'SoftStatus');      
        $list['areaid'] = $list['nDistrict']; 

        // $list['nProvince'] = D('Area')->getAreaNameById($list['nProvince']); 
        $list['nCity'] = D('Area')->getAreaNameById($list['nCity']); 
        $list['nDistrict'] = D('Area')->getAreaNameById($list['nDistrict']); 
        // $list['sArea'] = $list['nProvince'].'-'.$list['nCity'].'-'.$list['nDistrict'];

        $list['sArea'] = $list['nCity'].'-'.$list['nDistrict'];
        $list['nClientCount'] =$list['nOnlineCount'].'/'.$list['nDefaultPcCount'];
        // $list['nAccessMode'] =D('Refcode')->getRefcodeNameByLowValue($list['nAccessMode'],'AccessMode');
    
    return $list;
  }

	public function getOneWbInfoByMap($sContent) 
	{
        $list=$this->where(array('sBarCode'=>$sContent))->find();

        if(!empty($list))
        {
           $list['result']='1'; 
        }
        else
        {
          $list['result']='0'; 
        }


		$list['nOnlineStatus']   = D('Refcode')->getRefcodeNameByLowValue($list['nOnlineStatus'],'OnlineStatus'); 
        $list['nBusinessStatus'] = D('Refcode')->getRefcodeNameByLowValue($list['nBusinessStatus'],'BusinessStatus'); 
        $list['nSoftStatus']     = D('Refcode')->getRefcodeNameByLowValue($list['nSoftStatus'],'SoftStatus');      
        $list['areaid'] = $list['nDistrict']; 

        $list['nCity'] = D('Area')->getAreaNameById($list['nCity']); 
        $list['nDistrict'] = D('Area')->getAreaNameById($list['nDistrict']); 
        $list['sArea'] = $list['nCity'].'-'.$list['nDistrict'];
        // $list['nClientCount'] =$list['nOnlineCount'].'/'.$list['nDefaultPcCount'];	
        
	
		return $list;
	}

   	public function updateBarInfoByWbid($wbid,$data=array())
	{
	    if($this->create($data))
	    {
			if($this->where(array('wbid'=>$wbid))->save() !== false)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return $this->getError();
		}
	}	




	public function expZhongduan($map=array())
	{
    $map['deleted']=0;
		$list  = $this->alias('bar')->where($map)->select();

	    foreach ($list as &$val)
        {                             
          $val['nCity'] = D('Area')->getAreaNameById($val['nCity']); 
          $val['nDistrict'] = D('Area')->getAreaNameById($val['nDistrict']); 
          $val['area'] = $val['nCity'].'-'.$val['nDistrict'];

          $pccount=D('Zhongduanlist')->where(array('wbid'=>$val['wbid']))->count();

          $val['anzhuanglv'] =  $pccount/$val['nDefaultPcCount'] *100;
          $val['nMaxClientCount'] =  $pccount;
          $val['anzhuanglv']  =sprintf("%.2f",$val['anzhuanglv']); 
          $val['anzhuanglv'] =$val['anzhuanglv'] ."%"; //5.06%
          $val['nOnlineStatus'] = D('Refcode')->getRefcodeNameByLowValue($val['nOnlineStatus'],'OnlineStatus');
        }
		return $list;
	}

   public function getAllWbListInfo_Count($map=array())
  {
    $map['deleted']=0;
     $count = $this->alias('bar')->where($map)->count();
    return $count;
  }

    public function getAllWbListInfo($map=array(),$order = '',$page = 1,$rows = 20)
  {
    $map['deleted']=0;
        $count = $this->alias('bar')->where($map)->count();
    $list  = $this->alias('bar')->where($map)->order($order)->page($page,$rows)->select();

        //获取该网吧所有客户机列表数
        


      foreach ($list as &$val)
        {                             
          // $val['nProvince'] = D('Area')->getAreaNameById($val['nProvince']); 
          // $val['nCity'] = D('Area')->getAreaNameById($val['nCity']); 
          // $val['nDistrict'] = D('Area')->getAreaNameById($val['nDistrict']); 
          // $val['area'] = $val['nProvince'].'-'.$val['nCity'].'-'.$val['nDistrict'];

          // $val['nProvince'] = D('Area')->getAreaNameById($val['nProvince']); 
          $val['nCity'] = D('Area')->getAreaNameById($val['nCity']); 
          $val['nDistrict'] = D('Area')->getAreaNameById($val['nDistrict']); 
          // $val['area'] = $val['nProvince'].'-'.$val['nCity'].'-'.$val['nDistrict'];
          $val['area'] = $val['nCity'].'-'.$val['nDistrict'];

          $pccount=D('Zhongduanlist')->where(array('wbid'=>$val['wbid']))->count();

          $val['anzhuanglv'] =  $pccount/$val['nDefaultPcCount'] *100;
          $val['nMaxClientCount'] =  $pccount;
          $val['anzhuanglv']  =sprintf("%.2f",$val['anzhuanglv']); 
          $val['anzhuanglv'] =$val['anzhuanglv'] ."%"; //5.06%
          // $val['nOnlineStatus'] = D('Refcode')->getRefcodeNameByLowValue($val['nOnlineStatus'],'OnlineStatus');

          // $val['nBusinessStatus'] = D('Refcode')->getRefcodeNameByLowValue($val['nBusinessStatus'],'BusinessStatus'); 
        }
    


    return array('list'=>$list,'count'=>$count);
  }

    public function getAllWbListInfo2($map=array(),$order = '',$page = 1,$rows = 20)
	{
		$map['deleted']=0;
		// $list  = $this->alias('bar')->where($map)->order($order)->page($page,$rows)->select();

		$list = $this->field('sum(1) as othermonthcount,wt_bar.nDistrict')->where($map)->group('wt_bar.nDistrict')->order($order)->page($page,$rows)->select();
		return $list;
	}


	public function getBarCountByDistrictId($nDistrict)
	{
		return $this->where(array('nDistrict'=>$nDistrict))->count();
	}


	public function getWblistByDistrictid($nDistrict) 
	{
		$map['deleted']=0;
		$map['nDistrict']=$nDistrict;

		return $this->field(array(
			'wbid'=>'wbid','sBarName'=>'name'
			))->where($map)->select();
	}


	public function getBarServerStaticData() 
	{
       $onlinecount  =$this->where(array('nOnlineStatus'=>1))->count();
       $lixiancount  =$this->where(array('nOnlineStatus'=>2))->count();
       $yconlinecount=$this->where(array('nOnlineStatus'=>3))->count();
       $sumcount     =$this->count();

       $zaixianlv    =$onlinecount/$sumcount*100;      
       $zaixianlv  =sprintf("%.2f",$zaixianlv); 
       $zaixianlv =$zaixianlv ."%"; //5.06%

       $list['onlinecount']=$onlinecount;
       $list['lixiancount']=$lixiancount;
       $list['yconlinecount']=$yconlinecount;
       $list['sumcount']=$sumcount;
       $list['zaixianlv']=$zaixianlv;

       return $list;
	}

	public function getBarServerStaticDataByArea() 
	{
       $onlinelist = $this->field('sum(1) as onlineareacount,nDistrict')->where(array('nOnlineStatus'=>1))->group('nDistrict')->select();
       $sumlist    = $this->field('sum(1) as sumareacount,nDistrict')->group('nDistrict')->select();
       $districtlist=D('Area')->getAreaList(749);

       foreach ($districtlist as &$val)
       {                                    
           foreach ($onlinelist as &$val1) 
           {
            	if($val['id']==$val1['nDistrict'])
            	{          		 
            	  $val['onlineareacount']=$val1['onlineareacount'];
            	}
           } 
       }
        
       foreach ($districtlist as &$val)
       {                                        
           if(empty($val['onlineareacount']))
        	{          		 
        		$val['onlineareacount']='0';
        	} 
       }



       foreach ($districtlist as &$val)
       {                                    
           foreach ($sumlist as &$val1) 
           {
            	if($val['id']==$val1['nDistrict'])
            	{          		 
            	  $val['sumareacount']=$val1['sumareacount'];
            	}
           } 
       }
        
       foreach ($districtlist as &$val)
       {                                        
           if(empty($val['sumareacount']))
        	{          		 
        		$val['sumareacount']='0';
        	} 
       }


        foreach ($districtlist as &$val)
        {                                                   		 
          $val['zaixianlv']=$val['onlineareacount']/$val['sumareacount']*100;  
          $val['zaixianlv']=sprintf("%.2f",$val['zaixianlv']); 


          $val['zaixianlv'] =$val['zaixianlv'] ."%"; 
        }


       return array('list'=>$districtlist,'count'=>16);
	}

}



