<?php
namespace Home\Controller;
class RateController extends CommonController{
    //费率设置
    public function index()
    {		
        $map=array();
		$map['WB_ID']=session('wbid');		
        $this->assign('viptypelist',D('VipLevel')->getVipLevelList($map));
        $map=array();
        $map['WB_ID']=session('wbid');  
        $this->assign('districtlist',D('District')->getDistrictList($map));
		
        $this->display();

    }

    public function post_restart_server()
    {

      $wbid=session('wbid');
      $cmdtype='Qt_Type';

      $data['Cmd']=4;
	  $data['YuMing']='lzm1.wbzzsf.com';
      $data['Tem_Type']='Tem_NotInDb';
      $data['Guid']=create_guid1();

      $jsondata=$data;
      $res=PostTopDataToWb_lzm($wbid,$cmdtype,$jsondata);
      if(!empty($res))
      {
        $data['result']=1;
      }
      else
      {
        $data['result']=-1;
      }  
      $this->ajaxReturn($data);
	 
    }

    //区域设置
    public function district()
    {
        $map['WB_ID']=session('wbid');
		//判断计算机是否有分组，没有此分组，则清空分组
		$result=true;
		D()->startTrans();
		$cplist=D('Computerlist')->getComputerList($map);
		foreach($cplist as &$val)
		{
			if(!empty($val['GroupNameGuid']))
			{
				if(D('District')->where(array('WB_ID'=>session('wbid'),'Guid'=>$val['GroupNameGuid']))->find())
				{
					
				}
				else
                {
				    if(D('Computerlist')->where(array('GroupNameGuid'=>$val['GroupNameGuid'],'WB_ID'=>session('wbid')))->setField('GroupNameGuid','')===false)
					{
						$result = false;
					}  

					$aTempsql= D('Computerlist')->getLastSql(); 
					$sendstr.=$aTempsql.';'; 	
				}					
			}	
		}	
		if(!empty($sendstr))
		{
			if($result )
			{
				D()->commit();  //提交事务
				$result =PostTopUpdateDataToWb_lzm('Php_To_Top_Sql',$sendstr);
				if(!empty($result))
				{
				   writelog($wbid.'district首页清除无分组的分组guid 成功'.$sendstr,'commonlog');
				}
				else
				{
				   writelog($wbid.'district首页清除无分组的分组guid失败'.$sendstr,'commonlog');
				} 
			}
			else
			{
				D()->rollback();    //回滚
			}
		}	
		
			
        $this->assign('viptypelist',D('VipLevel')->getVipLevelList($map));
        $this->assign('districtlist',D('District')->getDistrictList($map));
        $this->display();
    }
    //定额费率
    public function fixedrate()
    {
        $map['WB_ID']=session('wbid');
        $this->assign('districtlist',D('District')->getDistrictList($map));
        $this->display();
    }
    //累计优惠
    public function discount()
    {    
        $this->display();
    }
    //会员等级
    public function viplevel()
    {
        $map['WB_ID']=session('wbid');
        $map['Name']=array('neq','临时卡');
        $this->assign('viplevellist',D('VipLevel')->getVipLevelList($map));	
        $this->display();
    }
	
    //奖励计划
    public function awardplan()
    {    
        $map['WB_ID']=session('wbid');
        $this->assign('viplevellist',D('VipLevel')->getVipLevelList($map));
    		$monthlist=array();
    		for($i=0;$i<31;$i++)
    		{
    			$monthlist[$i]= $i+1;
    		}	
    		
    		$this->assign('monthlist',$monthlist);

        //展示表格数据
        $hytype_name_list='';
        $hytype_array=explode(',', $hytype);
 
        $AwardPlanlist=D('AwardPlan')->getAwardPlan($map);
        foreach($AwardPlanlist as &$val)
        {      


          $hytype_name_list='';
          $hytype_array=explode(',', $val['HyCardGuid']);

          for($i=0;$i<count($hytype_array);$i++)
          {
            $ahytype=D('VipLevel')->where(array('WB_ID'=>session('wbid'),'Guid'=>$hytype_array[$i]))->getField('Name');   
            $hytype_name_list.=$ahytype.'、';
          } 
          $val['HyCardGuid']= $hytype_name_list;  
           
            if($val['FqLx']==0)
            {
                $val['FqJe']='无';
                $val['FqCount']='无';
            }
            else
            {
               $val['FqJe']=sprintf("%.2f", $val['FqJe']);

            }  

            $val['AddMoney']=sprintf("%.2f", $val['AddMoney']);
            $val['JLMoney']=sprintf("%.2f", $val['JLMoney']);
    
            if($val['LimitTimeLx']==0)
            {
                $val['LimitTimeLx']='无限制';
            }
            else
            {
                $val['LimitTimeBegin']=date('Y-m-d H:i:s',strtotime($val['LimitTimeBegin']));
                $val['LimitTimeEnd']=date('Y-m-d H:i:s',strtotime($val['LimitTimeEnd']));
                $val['LimitTimeLx']=$val['LimitTimeBegin'].' 至 '.$val['LimitTimeEnd'];
            }

            if($val['LimitDayLx']==0)
            {
                $val['LimitDays']='无限制';
            }
            else if($val['LimitDayLx']==1)
            {
               $val['LimitDays']='仅每月'.$val['LimitDays'];
            }
            else if($val['LimitDayLx']==2)
            {
               $val['LimitDays']='仅每周'.$val['LimitDays'];
            }
        }    

        $this->assign('awardplanlist',$AwardPlanlist);
        $this->display();
    }


    public function add_fixedrate()
    {  	
    	$district     = I('get.district','','string');
    	$this->assign('district',$district);
      $map=array();
      $map['WB_ID']=session('wbid');
      $this->assign('viptypelist',D('VipLevel')->getVipLevelList($map));
      $this->display();
    }



    public function add_fixedrate_set()
    {
      $wbid=session('wbid');
      if(IS_AJAX)
      {                        
          $start        = I('post.start','00:00','string')?:'00:00';
          $end          = I('post.end','00:00','string')?:'00:00';
          $time         = I('post.time',0,'int');
          $price        = I('post.price','','string');         
          $bfq          = I('post.bfq',0,'int');
          $autogotobs   = I('post.autogotobs',0,'int');
          $hytype       = I('post.hytype','','string');             
          $districtId   = I('post.district','','string');
          $districtGuid = D('District')->getDistrictGuidById($districtId);

         
		  $price=sprintf("%.2f",$price);
		 
		   
          $hytype_array=explode(',', $hytype);
          $hytype_list='';
          $hyname_list='';
          for($i=0;$i<count($hytype_array);$i++)
          {
            $ahytype=D('VipLevel')->where(array('WB_ID'=>session('wbid'),'id'=>$hytype_array[$i]))->getField('Guid');   
            $hytype_list.=$ahytype.',';       
          } 
           
          if($autogotobs==0)
          {
            $name= '('.$price.')元'.$time.'分钟--非自动';
          }
          else  if($autogotobs==1)
          {
            $name= '('.$price.')元'.$time.'分钟--自动';
          }	
          
          $data = array(
                      'GroupGuid' =>  $districtGuid,
                      'Guid'  =>  getGuid(),
                      'name'  =>  $name,
                      'TimeSize'  =>  $time,
                      'WB_ID'  =>  session('wbid'),
                      'je'    => $price,
                      'BeginTime' =>  date('Y-m-d H:i:s',strtotime($start)),
                      'EndTime'   =>  date('Y-m-d H:i:s',strtotime($end)),
                      'AutoChange'=>$autogotobs,
                      'OwnerHyLxGuid'=>$hytype_list,
                      'Lx'=>$bfq
                  );
	        $fixedrate_insert_result = D('FixedRate')->addFixedRate($data); 
          $sendstr=D('FixedRate')->getLastSql();

	        if(!empty($fixedrate_insert_result))
	        {
        
             $result =PostTopUpdateDataToWb_lzm('Php_To_Top_Sql',$sendstr);
            if(!empty($result))
            {
              writelog($wbid.'新增 WDeFl 表命令已发送成功','commonlog');
            }
            else
            {
              writelog($wbid.'新增 WDeFl 表命令已发送失败','commonlog');
            } 
	          $data['result']='1';
	        }
	        else
	        {
	         $data['result']='0';
	        }    

            $this->ajaxReturn($data);
        }    
		
    }


    public function edit_fixedrate()
    {   

    	$district     = I('get.district','','string');
    	$this->assign('district',$district);
    	$id     = I('get.id','','string');
      $oneFixedRateInfo=D('FixedRate')->where(array('id'=>$id,'WB_ID'=>session('wbid')))->find();  
      $hytype_id_list='';
      $hytype_array=explode(',', $oneFixedRateInfo['OwnerHyLxGuid']);

      for($i=0;$i<count($hytype_array);$i++)
      {
        $ahytype=D('VipLevel')->where(array('WB_ID'=>session('wbid'),'Guid'=>$hytype_array[$i]))->getField('id');   
        $hytype_id_list.=$ahytype.',';
      } 

      $oneFixedRateInfo['OwnerHyLxGuid']= $hytype_id_list; 
	    
      $oneFixedRateInfo['BeginTime']=  substr($oneFixedRateInfo['BeginTime'],11,5); 
      $oneFixedRateInfo['EndTime']= substr($oneFixedRateInfo['EndTime'],11,5); 
      $oneFixedRateInfo['je']= sprintf("%.2f",$oneFixedRateInfo['je']); 


      $this->assign('oneFixedRateInfo',$oneFixedRateInfo);
 
      $this->assign('bianhao',$id);
      $map=array();
      $map['WB_ID']=session('wbid');
      $this->assign('viptypelist',D('VipLevel')->getVipLevelList($map));
      $this->display();

    }



    public function edit_fixedrate_set()
    {
		 
      $wbid=session('wbid');
      if(IS_AJAX)
      {
          $id     = I('post.bianhao','','string');                      
          $start = I('post.start','00:00','string')?:'00:00';
          $end   = I('post.end','00:00','string')?:'00:00';
          $time  = I('post.time',0,'int');
          $price = I('post.price','','string');         
          $bfq          = I('post.bfq',0,'int');
          $autogotobs   = I('post.autogotobs',0,'int');
          $hytype       = I('post.hytype','','string'); 
		  
		  $price=sprintf("%.2f",$price);
		  
          $hytype_array=explode(',', $hytype);
          $hytype_list='';
          for($i=0;$i<count($hytype_array);$i++)
          {
            $ahytype=D('VipLevel')->where(array('id'=>$hytype_array[$i],'WB_ID'=>session('wbid')))->getField('Guid');   
            $hytype_list.=$ahytype.','; 
          } 
           
          if($autogotobs==0)
          {
            $name= '('.$price.')元'.$time.'分钟--非自动';
          }
          else  if($autogotobs==1)
          {
            $name= '('.$price.')元'.$time.'分钟--自动';
          }	

          $aFixedRateGuid=D('FixedRate')->where(array('id'=>$id,'WB_ID'=>session('wbid')))->getField('Guid');   
          $fixedrate_update_data = array(
                                      
                        'name'  =>  $name,
                        'TimeSize'  =>  $time,
                        'WB_ID'  =>  session('wbid'),
                        'je'    =>  $price,
                        'BeginTime' =>  date('Y-m-d H:i:s',strtotime($start)),
                        'EndTime'   =>  date('Y-m-d H:i:s',strtotime($end)),
                        'AutoChange'=>$autogotobs,
                        'OwnerHyLxGuid'=>$hytype_list,
                        'Lx'=>$bfq
                    );

	        $fixedrate_update_result = D('FixedRate')->updateFixedRateByGuid($aFixedRateGuid,$fixedrate_update_data); 

            $sendstr=D('FixedRate')->getLastSql();
	        if(!empty($fixedrate_update_result))
	        {
            $result =PostTopUpdateDataToWb_lzm('Php_To_Top_Sql',$sendstr);
            if(!empty($result))
            {
              writelog($wbid.'修改WDeFl 表命令已发送成功','commonlog');
            }
            else
            {
              writelog($wbid.'修改 WDeFl 表命令已发送失败','commonlog');
            }  
	          $data['result']='1';
	        }
	        else
	        {
	           $data['result']='0';
	        }    
          
          $this->ajaxReturn($data);
      }  
	  
    }


    public function add_award()
    {		  
      $map=array();
      $map['WB_ID']=session('wbid');
      $map['Name']=array('neq','临时卡');
      $this->assign('viplevellist',D('VipLevel')->getVipLevelList($map));
         
      $monthlist=array();
      for($i=0;$i<31;$i++)
      {
        $monthlist[$i]= $i+1;
      }   
      
      $this->assign('monthlist',$monthlist);
      $this->assign('oneawardplan',$oneawardplan);
      $this->display();
	   
    }





    public function add_award_set()
    {		
      $wbid=session('wbid');
      if(IS_AJAX)
      {
          $hytype     = I('post.hytype','','string');        
          $price      = I('post.price','','string');
          $auto       = I('post.auto','','string');
          $toaccount  = I('post.toaccount','','string');         
          $sxsd       = I('post.sxsd','','string');
          $sxsdlime   = I('post.sxsdlime',0,'int');
          $start      = I('post.start','','string');
          $end        = I('post.end','','string');  
          $mon        = I('post.mon','','string');
          $week       = I('post.week','','string'); 
          $fq         = I('post.fq','','string');
          $fq_time    = I('post.fq_time','','string');
          $fq_total   = I('post.fq_total','','string');
          $BlJl       = I('post.bljl','','string');

           //获取会员等级guid
          $hytype_array=explode(',', $hytype);
          $hytype_list='';
          for($i=0;$i<count($hytype_array);$i++)
          {
            $ahytype=D('VipLevel')->where(array('id'=>$hytype_array[$i],'WB_ID'=>session('wbid')))->getField('Guid');   
            $hytype_list.=$ahytype.','; 
          }  

          if($sxsd==0)
          {
             $award_insert_data['LimitTimeLx']     = 0;
             $award_insert_data['LimitTimeBegin'] = '';
             $award_insert_data['LimitTimeEnd']   = '';
          }
          else if($sxsd==1)
          {
             $award_insert_data['LimitTimeLx']      = 1;
             $award_insert_data['LimitTimeBegin']  = $start;
             $award_insert_data['LimitTimeEnd']    = $end;
          }  

          if($sxsdlime==0)
          {
             $award_insert_data['LimitDayLx'] = 0;
             $award_insert_data['LimitDays']  = '';

          }
          else if($sxsdlime==1)
          {
             $award_insert_data['LimitDayLx'] = 1;
             $award_insert_data['LimitDays']  = $mon;
          }
          else if($sxsdlime==2)
          {
             $award_insert_data['LimitDayLx'] = 2;
             $award_insert_data['LimitDays']  = $week;
          } 


          if($fq==0)
          {  
            $award_insert_data['FqLx']      = 0;
            $award_insert_data['FqJe']      = 0;
            $award_insert_data['FqCount']   = 0;
          }
          else if($fq==1)
          {
            $award_insert_data['FqLx']     = 1;
            $award_insert_data['FqJe']      = sprintf("%.2f", $fq_total);
            $award_insert_data['FqCount']   = $fq_time;
          }  
          $award_insert_data['HyCardGuid']= $hytype_list;
          $award_insert_data['AddMoney']  =sprintf("%.2f", $price) ;
          $award_insert_data['JLMoney']   =sprintf("%.2f", $auto);
          $award_insert_data['Lx']   = $toaccount;
          $award_insert_data['WB_ID']   = session('wbid');
          $award_insert_data['Guid']   = getGuid();
          $award_insert_data['Bljl']   = $BlJl;

      
          $award_insert_result = D('AwardPlan')->addAwardPlan($award_insert_data); 


          $sendstr=D('AwardPlan')->getLastSql();
          if(!empty($award_insert_result))
          {
            $result =PostTopUpdateDataToWb_lzm('Php_To_Top_Sql',$sendstr);
            if(!empty($result))
            {
              writelog($wbid.'新增 WHyLxTable_JLjh 表命令已发送成功','commonlog');
            }
            else
            {
              writelog($wbid.'新增 WHyLxTable_JLjh 表命令已发送失败','commonlog');
            } 
            $data['result']='1';
          }
          else
          {
            $data['result']='0';
          }    

        $this->ajaxReturn($data);
      }   
     
    }



    public function edit_award()
    {
		 
        $map['WB_ID']=session('wbid');
        $id=I('get.id','','string');
        $oneawardplan=D('AwardPlan')->where(array('id'=>$id,'WB_ID'=>session('wbid')))->find();     
        $map['Name']=array('neq','临时卡');
        $this->assign('viplevellist',D('VipLevel')->getVipLevelList($map));     
        $monthlist=array();
        for($i=0;$i<31;$i++)
        {
          $monthlist[$i]= $i+1;
        }   
        
        $this->assign('monthlist',$monthlist);
        $hytype_id_list='';
        $hytype_array=explode(',', $oneawardplan['HyCardGuid']);

        for($i=0;$i<count($hytype_array);$i++)
        {
          $ahytype=D('VipLevel')->where(array('WB_ID'=>session('wbid'),'Guid'=>$hytype_array[$i]))->getField('id');   
          $hytype_id_list.=$ahytype.',';
        } 
        $oneawardplan['HyCardGuid']= $hytype_id_list;  

        $oneawardplan['LimitTimeBegin']= date('Y-m-d H:i:s',strtotime($oneawardplan['LimitTimeBegin'])); 
        $oneawardplan['LimitTimeEnd'] = date('Y-m-d H:i:s',strtotime($oneawardplan['LimitTimeEnd'])); 

        $this->assign('oneawardplan',$oneawardplan);
        $this->display();
		
    }



    public function edit_award_set()
    {
		   
      $wbid=session('wbid');
      if(IS_AJAX)
      {
          $id         =I('post.bianhao','','string');
          $hytype     = I('post.hytype','','string');     
          $price      = I('post.price','','string');
          $auto       = I('post.auto','','string');
          $toaccount  = I('post.toaccount','','string');            
          $sxsd       = I('post.sxsd','','string');
          $sxsdlime   = I('post.sxsdlime',0,'int');
          $start      = I('post.start','','string');
          $end        = I('post.end','','string');       
          $mon        = I('post.mon','','string');
          $week       = I('post.week','','string'); 
          $fq         = I('post.fq','','string');
          $fq_time    = I('post.fq_time','','string');
          $fq_total   = I('post.fq_total','','string');
          $BlJl       = I('post.bljl','','string');

           //获取会员等级guid
          $hytype_array=explode(',', $hytype);
          $hytype_list='';
          for($i=0;$i<count($hytype_array);$i++)
          {
            $ahytype=D('VipLevel')->where(array('id'=>$hytype_array[$i],'WB_ID'=>session('wbid')))->getField('Guid');   
            $hytype_list.=$ahytype.','; 
          }  

          if($sxsd==0)
          {
            $award_update_data['LimitTimeLx']     = 0;
            $award_update_data['LimitTimeBegin'] = '';
            $award_update_data['LimitTimeEnd']   = '';
          }
          else if($sxsd==1)
          {
            $award_update_data['LimitTimeLx']      = 1;
            $award_update_data['LimitTimeBegin']  = $start;
            $award_update_data['LimitTimeEnd']    = $end;
          }  

          if($sxsdlime==0)
          {
            $award_update_data['LimitDayLx'] = 0;
            $award_update_data['LimitDays']  = '';
          }
          else if($sxsdlime==1)
          {
            $award_update_data['LimitDayLx'] = 1;
            $award_update_data['LimitDays']  = $mon;
          }
          else if($sxsdlime==2)
          {
            $award_update_data['LimitDayLx'] = 2;
            $award_update_data['LimitDays']  = $week;
          } 


          if($fq==0)
          {  
            $award_update_data['FqLx']      = 0;
            $award_update_data['FqJe']      = 0;
            $award_update_data['FqCount']   = 0;
          }
          else if($fq==1)
          {
            $award_update_data['FqLx']     = 1;
            $award_update_data['FqJe']      = $fq_total;
            $award_update_data['FqCount']   = $fq_time;
          }  

          $award_update_data['HyCardGuid']= $hytype_list;
          $award_update_data['AddMoney']  = $price ;
          $award_update_data['JLMoney']   = $auto;
          $award_update_data['Lx']   = $toaccount;
          $award_update_data['WB_ID']   = session('wbid');
          $award_update_data['Bljl']   = $BlJl;
          $aAwardGuid=D('AwardPlan')->where(array('id'=>$id,'WB_ID'=>session('wbid')))->getField('Guid');     
          $award_update_result = D('AwardPlan')->updateAwardPlanByGuid($aAwardGuid,$award_update_data); 

          $sendstr=D('AwardPlan')->getLastSql();
          if(!empty($award_update_result))
          {
            $result =PostTopUpdateDataToWb_lzm('Php_To_Top_Sql',$sendstr);
            if(!empty($result))
            {
               writelog($wbid.'修改 WHyLxTable_JLjh 表命令已发送成功','commonlog');
            }
            else
            {
              writelog($wbid.'修改 WHyLxTable_JLjh 表命令已发送失败','commonlog');
            } 
            $data['result']='1';
          }
          else
          {
            $data['result']='0';
          }    
   	
          $this->ajaxReturn($data);
      }    
      
    }
    function deleteAwardPlan()
    {
		
      if(IS_AJAX)
      {
          $id=I('post.id','','string');
          $aAwardGuid=D('AwardPlan')->where(array('id'=>$id,'WB_ID'=>session('wbid')))->getField('Guid');
          $award_delete_result = D('AwardPlan')->deleteAwardPlanByGuid($aAwardGuid); 
          $sendstr=D('AwardPlan')->getLastSql();
          if(!empty($award_delete_result))
          {
            $result =PostTopUpdateDataToWb_lzm('Php_To_Top_Sql',$sendstr);
            if(!empty($result))
            {
              writelog($wbid.'删除 WHyLxTable_JLjh 表命令已发送成功','commonlog');
            }
            else
            {
              writelog($wbid.'删除 WHyLxTable_JLjh 表命令已发送失败','commonlog');
            }  
            $data['status']='1';
          }
          else
          {
            $data['status']='0';
          }    
           
          $this->ajaxReturn($data);
      }
	  
    }


    //积分兑换
    public function credits()
    {	   
        $map['WB_ID']=session('wbid');
        $map['Name']=array('neq','临时卡');
  		  $this->assign('viptypelist',D('VipLevel')->getVipLevelList($map));
  		  $res=D('Spinfo')->where(array('WB_ID'=>session('wbid')))->select();
  		  $this->assign('spinfolist',$res);
        $this->display();
		  
    }
    //附加费
    public function surcharge()
    {		  
      $this->display();
	     
    }
    //特定费率
    public function specialrate()
    {
		//femalerate
	   
	   $m_WomanDiscount	 =D('WIni')->getOneRecordByName('m_WomanDiscount');  
       $oneserverinfo['m_WomanDiscount']=$m_WomanDiscount;
	   if(!empty($m_WomanDiscount))
	   {
		    
	   }
	   else
       {
		  $oneserverinfo['m_WomanDiscount']='0';  
	   }		   
       $this->assign('oneserverinfo',$oneserverinfo);	   
      $this->display();	 
    }
	
	public function setSpecialRate()
    {
     
      if(IS_AJAX)
      {
		$m_WomanDiscount   =I('post.femalerate','0','string');
		if(!empty($m_WomanDiscount))
		{
			 
		}
		else
		{
			$m_WomanDiscount='0';
		}
		
	    $res=D('WIni')->postOneRecord('m_WomanDiscount',$m_WomanDiscount);     // 加钱一元送一积分   
	    $aTempstr=D('WIni')->getLastSql();
	    $sendstr.=$aTempstr.';';  

		$res =PostTopUpdateDataToWb_lzm('Php_To_Top_Sql',$sendstr);
		if(!empty($res))
		{
			writelog($wbid.'更新setSpecialRate  命令已发送成功','commonlog');
		}
		else
		{
		   writelog($wbid.'更新 setSpecialRate 命令已发送失败','commonlog');
		} 
		$data['result']=1;	
        $this->ajaxReturn($data);
      }
	  
    }


    public function getRate()
  	{   	
      if(IS_AJAX)
      {
        $vipLevelId = I('get.type',0,'int');
        $districtId = I('get.district',0,'int');	
        $rate = D('District')->getRate($vipLevelId,$districtId);			
  		$rate['result']='1';			   
  		$this->ajaxReturn($rate);
      }
	 
    }
    public function setRate()
    {   
      if(IS_AJAX)
      {
          $districtid = I('post.district',0,'int');
          $vipLevelId = I('post.viptype',0,'int');
          $time = I('post.time',array());
          $rate = I('post.rate',0,'string');

          $aDistrictGuid=D('District')->where(array('id'=>$districtid,'WB_ID'=>session('wbid')))->getField('Guid');
          $aVipLevelGuid=D('VipLevel')->where(array('id'=>$vipLevelId,'WB_ID'=>session('wbid')))->getField('Guid');

          $result= D('District')->updateRateByGuid($aDistrictGuid,$aVipLevelGuid,$time,$rate);	

          $sendstr=D('District')->getLastSql();

          if(!empty($result))
          {
            $res =PostTopUpdateDataToWb_lzm('Php_To_Top_Sql',$sendstr);
            if(!empty($res))
            {
               writelog($wbid.'新增 WGroupTable 表命令已发送成功','commonlog');
            }
            else
            {
               writelog($wbid.'新增 WGroupTable 表命令已发送失败','commonlog');
            } 		
            $this->success();
          }
          else
          {
			    
            $this->error();
          }
      }
	 
    }
    public function setRateConfig()
    {   
      if(IS_AJAX)
      {
          $districtid = I('post.district',0,'int');
          $vipLevel = I('post.viptype',0,'int');
          $enableHour = I('post.enable_hour','','string');
          $ignoreMinute = I('post.ignoreminute','','string');
          $minPrice = I('post.minprice','','string');
          $startPrice = I('post.startprice','','string');

          $aDistrictGuid=D('District')->where(array('id'=>$districtid,'WB_ID'=>session('wbid')))->getField('Guid');

          $result= D('District')->updateRateConfigByGuid($aDistrictGuid,$vipLevel,$enableHour,$ignoreMinute,$minPrice,$startPrice);

          $sendstr=D('District')->getLastSql();
          if(!empty($result))
          {
            $res =PostTopUpdateDataToWb_lzm('Php_To_Top_Sql',$sendstr);
            if(!empty($res))
            {
               writelog($wbid.'修改 WGroupTable 表命令已发送成功','commonlog');
            }
            else
            {
               writelog($wbid.'修改 WGroupTable 表命令已发送失败','commonlog');
            }  
			  
            $this->success();
          }
          else
          {
			    
            $this->error();
          }
      }
	  
    }
    public function getDistrict()
    {   
	
	    
        if(IS_AJAX)
        {
            $districtId = I('get.district',0,'int');
            $district = D('District')->where(array('id'=>$districtId,'WB_ID'=>session('wbid')))->find();

            $districtGuid = $district['Guid'];
            $box1List = $box2List = array();
            //该分区计算机列表
            $wbid=session('wbid');;       
            $map=array();
            $map['WB_ID']=$wbid;
            $map['GroupNameGuid']=$districtGuid;

            $box1 = D('Computerlist')->getComputerList($map);
            foreach($box1 as $value)
            {
              $box1List[] = $value['Name'];
            }

            $map=array();
            $map['WB_ID']=$wbid;
            $map['GroupNameGuid']=array('neq','');
            $id_list = D('Computerlist')->where($map)->getField('id',true);
             
            //未分区计算机列表
            
            $map=array(); 
            $map['WB_ID']=session('wbid'); 
            $map['GroupNameGuid']=array('eq','');
            $box2 = D('Computerlist')->getComputerList($map);
            foreach($box2 as $value)
            {
              $box2List[] = $value['Name'];
            }
            					  
            //该分区允许上机的会员类型
            $vipLevelIds = D('VipLevel')->getAccessVipLevel($district['HyCardGuids']);
            $data['box1']=$box1List;
            $data['box2']=$box2List;
            $data['viptypeids']=$vipLevelIds;
            $data['fenzu_count']=count($box1List);
            $data['daifenzu_count']=count($box2List);;

            $this->ajaxReturn($data);

        }
		
    }


    public function setDistrict()
    {   
      if(IS_AJAX)
      {
            $districtId = I('post.district',0,'int');
            $aDistrictGuid=D('District')->where(array('id'=>$districtId,'WB_ID'=>session('wbid')))->getField('Guid');
            $box1 = I('post.box1',array());
            $box2 = I('post.box2',array());
            $accessList = I('post.access',array());
            $result = true;


            $sendstr='';
            D()->startTrans();  //启用事务
            
            foreach($box1 as $value)
            {
                $map=array();
                $map['Name']=$value;
                $map['WB_ID']=session('wbid');
				$map['HyCardNo']='';				
				//$map['HyCardNo'] = array('exp',' is  null or HyCardNo = ""');
				
				//$map['HyCardNo'] =array('eq',array('is  null',''),'OR');
				
			//	$where = ' Name= '.$value.' and WB_ID='.session('wbid').'HyCardNo is null or HyCardNo ="" '

                $data=array();
                $data['GroupNameGuid']= $aDistrictGuid;
                if(D('Computerlist')->updateComputerList($map,$data) === false)
                {
                  $result = false;
                }
                $aTempsql= D('Computerlist')->getLastSql();
                $sendstr.= $aTempsql.';';

            }

            foreach($box2 as $value)
            {
                $map=array();
                $map['Name']=$value;
                $map['WB_ID']=session('wbid');
				$map['HyCardNo']='';
			//	$map['HyCardNo'] =array('eq',array('is  null',''),'OR');
                $data['GroupNameGuid']='';
				

                if(D('Computerlist')->updateComputerList($map,$data) === false)
                {
                  $result = false;
                }
                $aTempsql= D('Computerlist')->getLastSql();
                $sendstr.= $aTempsql.';';
            }
			
			
            $access = '';
            foreach($accessList as $value)
            {
                $access .= D('VipLevel')->getVipLevelGuidById($value) . ',';
            }

            if(D('District')->updateDistrictByGuid(array('WB_ID'=>session('wbid'),'Guid'=>$aDistrictGuid),array('HyCardGuids'=>$access)) === false)  //更新该区选中的会员类型
            {
              $result = false;    
            }

            $aTempsql= D('District')->getLastSql();
            $sendstr.= $aTempsql.';';
           
		   if(!empty($sendstr))
		   {
			    if($result)
				{
				  D()->commit();  //提交事务
				  $res =PostTopUpdateDataToWb_lzm('Php_To_Top_Sql',$sendstr);
				  if(!empty($res))
				  {
					 writelog($wbid.'修改 分组  命令已发送成功','commonlog');
				  }
				  else
				  {
					 writelog($wbid.'修改 分组  命令已发送失败','commonlog');
				  } 		  
				  $this->success();
				}
				else
				{
				  D()->rollback();   
				      
				  $this->error();
				}  
		   }	   
  
      }
	  
    }

    public function  deleteUnFenzuComputerList()
    {
		  
      if(IS_AJAX)
      {
            $result = true;
            $districtId = I('post.district',0,'int');
            $districtGuid = D('District')->getDistrictGuidById($districtId);
            $box1 = I('post.box1',array());
            $box2 = I('post.box2',array());
            D()->startTrans(); 
            
            $sendstr='';
            foreach($box2 as $value)
            {
                $map=array();
                $map['Name']=$value;
                $map['WB_ID']=session('wbid');
                $data['GroupNameGuid']='';

                if(D('Computerlist')->deleteComputerList($map,$data) === false)
                {
                  $result = false;
                }
                $aTempsql= D('Computerlist')->getLastSql();
                $sendstr.=$aTempsql.';';
            }
            if(!empty($sendstr))
			{
			    if($result)
				{
				  D()->commit();  //提交事务
				  $res =PostTopUpdateDataToWb_lzm('Php_To_Top_Sql',$sendstr);
				  if(!empty($res))
				  {
					writelog($wbid.'删除未分组计算机  命令已发送成功','commonlog');
				  }
				  else
				  {
					writelog($wbid.'删除未分组计算机   命令发送失败','commonlog');
				  }  
				  $data['result']=1;
				    
				}
				else
				{
				  D()->rollback();    //回滚
				   
				  $data['result']=-1;
				}	
			}	
          
            $this->ajaxReturn($data);
      }  
	  
    }


    public function setDistrictInfo()
    {
		   
      if(IS_AJAX)
      {
            $districtId = I('post.district',0,'int');
            $name = I('post.name','','string');
            $aDistrictGuid=D('District')->where(array('id'=>$districtId,'WB_ID'=>session('wbid')))->getField('Guid');

            $sendstr='';             
            if(empty($name))
            {
              $this->error();
            }
            else
            {          
              $oneDistrict=D('District')->where(array('WB_ID'=>session('wbid'),'GroupName'=>$name))->find();
              if(!empty($oneDistrict))
              { 
                 if($oneDistrict['id']==$districtId)
                 {
                    if(D('District')->addOneDistrictByName(array('id'=>$districtId),array('GroupName'=>$name)) === false)
                    {
                      $this->error();
                    }  
                    $sendstr= D('District')->getLastSql();
                 }
                 else
                 {
                   $this->error();
                 }        
              }
              else
              {
                if(D('District')->updateDistrictByGuid(array('WB_ID'=>session('wbid'),'Guid'=>$aDistrictGuid),array('GroupName'=>$name)) === false)
                {
                  $this->error();
                }
                $sendstr= D('District')->getLastSql();
              } 
              $res =PostTopUpdateDataToWb_lzm('Php_To_Top_Sql',$sendstr);
              if(!empty($res))
              {
                 writelog($wbid.'修改 WGroupTable  命令已发送成功','commonlog');
              }
              else
              {
                 writelog($wbid.'修改 WGroupTable  命令已发送失败','commonlog');
              }  
                    
                $this->success();
            }
      }
	   
    }
    public function addDistrict()
    {  
        if(IS_AJAX)
        {
           $result = true;
           $groupname = I('post.name','','string');         
           $bExist=D('District')->where(array('WB_ID'=>session('wbid'),'GroupName'=>$groupname))->find(); //查询区域表是否有此区域名
           if(!empty($bExist))
           {
             $data['result']=-1;
           }
           else
           {                 
                if(D('District')->addOneDistrictByName($groupname) === false)
                {
                  $result = false;
                }  
                $sendstr= D('District')->getLastSql();
				if($result)
				{
					$res =PostTopUpdateDataToWb_lzm('Php_To_Top_Sql',$sendstr);
					if(!empty($res))
					{
						writelog($wbid.'新增 WGroupTable  命令已发送成功','commonlog');
					}
					else
					{
						writelog($wbid.'新增 WGroupTable  命令已发送失败','commonlog');
					}  
	 
					$data['result']=1;
				}
				else
				{
					$data['result']=-1;
				}       
		    } 
			  
            $this->ajaxReturn($data);        
        }
		
    }

    public function addcomputernum()
    {   
	    
        if(IS_AJAX)
        {
			$pcqz = I('post.pcqz','','string');
			$name_start = I('post.name_start','','string');
			$jq_num = I('post.jq_num','','string');
			$bh_num = I('post.bh_num','','string');						
			if($jq_num>500)
			{
				$jq_num=500;
			}

            if($bh_num>3)
            {
				$bh_num=3;
			}	
			
			

			$wbid=session('wbid');
			$changshu= pow(10,$bh_num);
			$computername_list=array();
			$j=0;
			
			$newcplist='';
			for($i=$name_start;$i<$name_start+$jq_num;$i++)
			{						
				$computername_list[$j]['Name']= $pcqz.substr($changshu+$i,1,$bh_num);
				$computername_list[$j]['WB_ID']=$wbid;
				$j=$j+1;
				
				$acpname=$pcqz.substr($changshu+$i,1,$bh_num);			
			    $newcplist=$newcplist.$acpname.',';                				
			}	
			
		
			if(!empty($newcplist))
			{
				$map1=array();
				$map1['WB_ID']=$wbid;
				$map1['Name']=array('in',$newcplist);
				$bChongfu=D('Computerlist')->where($map1)->select();
				if(!empty($bChongfu))
				{
					$data['result']=-2; 
					$this->ajaxReturn($data);
					return;						
				}	
			}    		
		    $result=true;
			      
            D()->startTrans(); 
          
      		for($i=0;$i<$jq_num;$i++)
      		{	
                   				   
      		        $data=array();
      				$data['Name']= $computername_list[$i]['Name'];
      				$data['WB_ID']= $computername_list[$i]['WB_ID'];
                    $data['GroupNameGuid']='';   				  				
					if(D('Computerlist')->add($data)===false)
					{
					  $result=false;
					} 	
					$aTempsql= D('Computerlist')->getLastSql();	
					$sendstr.=$aTempsql.';';			   				       			                				
      		}		
         	
           if(!empty($sendstr))
		   {
			  if($result)
			  {
				  D()->commit();	
				  $result =PostTopUpdateDataToWb_lzm('Php_To_Top_Sql',$sendstr);
				  if(!empty($result))
				  {
					writelog($wbid.'新增 WComputerList  命令已发送成功','commonlog');
				  }
				  else
				  {
					writelog($wbid.'新增 WComputerList  命令已发送失败','commonlog');
				  }  

				  $data['result']=1; 
				    
			  }
			  else
			  {
				D()->rollback(); 
				$data['result']=-1; 
			  
			  }    
		   }	   
           
			 $this->ajaxReturn($data);		
        }  
    }
	
    public function deleteDistrict()
    {		   
        if(IS_AJAX)
        {
		    $wbid=session('wbid');
            $oper = I('post.oper','','string');
            $districtId = I('post.district',0,'int');
            $districtGuid=D('District')->where(array('id'=>$districtId,'WB_ID'=>session('wbid')))->getField('Guid');

            $sendstr='';
            if($oper == 'del' && !empty($districtGuid))
            {
				$result = true;
				D()->startTrans();  //开启事务
				if(D('District')->deleteDistrict($districtGuid) === false)  //1.删除一个分组
				{
					$result = false;
				}

				$aTempsql= D('District')->getLastSql(); 
				$sendstr.=$aTempsql.';';  

				  //删除该分组计算机里的GroupNameGuid
				if(D('Computerlist')->where(array('GroupNameGuid'=>$districtGuid,'WB_ID'=>session('wbid')))->setField('GroupNameGuid','')===false)
				{
					$result = false;
				}  

				$aTempsql= D('Computerlist')->getLastSql(); 
				$sendstr.=$aTempsql.';'; 			  
				  
				if(!empty($sendstr))
				{
					if($result)
					{
						$result =PostTopUpdateDataToWb_lzm('Php_To_Top_Sql',$sendstr);						
						if(!empty($result))
						{						   						   
						   writelog($wbid.'删除分组  命令已发送成功','commonlog');
						}
						else
						{
						   writelog($wbid.'删除分组  命令已发送失败','commonlog');
						} 
						D()->commit(); 

						$this->success();
					}
					else
					{
						D()->rollback();    
						$this->error();
					} 
			    }	  

            }
        }

    }


    public function getFixedRate()
    {   
      if(IS_AJAX)
      {
          $districtId = I('get.district',0,'int');
          $districtGuid=D('District')->where(array('WB_ID'=>session('wbid'),'id'=>$districtId))->getField('Guid');
          $map=array();
          $map['GroupGuid']=$districtGuid;
          $map['WB_ID']=session('wbid');

          $fixedRates = D('FixedRate')->getFixedRate($map);
          $fixed = array();
          $free = false;

          foreach($fixedRates as &$val)
          {
              if($val['name'] == '自由计费')
              {
                $free = true;
              }
              else
              {

                $hytype_array=explode(',', $val['OwnerHyLxGuid']);           
                $hyname_list='';
                for($i=0;$i<count($hytype_array);$i++)
                {
                  $ahylx_name_type=D('VipLevel')->where(array('WB_ID'=>session('wbid'),'Guid'=>$hytype_array[$i]))->getField('Name');  

                  $hyname_list.=$ahylx_name_type.',';       
                } 
				
                $fixed[] = array(
                    'name'=>$val['name'].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$hyname_list,
                    'id'=>$val['id'],
                    'time'=>$val['TimeSize'],
                    'price'=>$val['je'],
					
                    'start'=>$val['BeginTime'],
                    'end'=>$val['EndTime'],			
					'bfq'=>$val['Lx'],
                    );
              }
          }
		  
		  foreach( $fixed as &$val)
		  {
			$BeginTime= substr($val['start'],11,5); 
			$EndTime= substr($val['end'],11,5); 
			 $val['price']=sprintf("%.2f",$val['price']);
			$val['start']=$BeginTime;
			$val['end']=$EndTime;
			if(empty($val['bfq']))
			{
				$val['bfq']=0;
			}	
		  }
		  
          $this->ajaxReturn(array('free'=>$free,'fixed'=>$fixed));
      }
	  
    }
    public function setFixedRate()
    {
		   
      if(IS_AJAX)
      {
            $oper = I('post.oper','','string');
            $districtId = I('post.district',0,'int')?:0;
            $name = I('post.name','','string');
            $time = I('post.time',0,'string')?:0;
            $price = I('post.price',0,'string')?:0;
            $start = I('post.start','00:00','string')?:'00:00';
            $end = I('post.end','00:00','string')?:'00:00';
            $id = I('post.id',0,'int')?:0;


            $sendstr=''; 

            $districtGuid=D('District')->where(array('id'=>$districtId,'WB_ID'=>session('wbid')))->getField('Guid');
            $aFixedRateGuid=D('FixedRate')->where(array('id'=>$id,'WB_ID'=>session('wbid')))->getField('Guid');

            if(!empty($name) && !empty($districtGuid))
            {
                if($oper == 'edit' && !empty($id))
                {
                    $data = array(
                        'GroupGuid' =>  $districtGuid,
                        'name'  =>  $name,
                        'TimeSize'  =>  $time,
                        'je'    =>  $price,
                        'BeginTime' =>  date('Y-m-d H:i:s',strtotime($start)),
                        'EndTime' =>  date('Y-m-d H:i:s',strtotime($end))
                    );
                    if(D('FixedRate')->updateFixedRateByGuid($aFixedRateGuid,$data) === false)
                    {  
                      $this->error();
                    }
                    else
                    {

                      $sendstr= D('FixedRate')->getLastSql(); 

                      $result =PostTopUpdateDataToWb_lzm('Php_To_Top_Sql',$sendstr);
                      if(!empty($result))
                      {
                        writelog($wbid.'更新 WDeFl  命令已发送成功','commonlog');
                      }
                      else
                      {
                        writelog($wbid.'更新 WDeFl  命令已发送失败','commonlog');
                      } 
					     
                      $this->success();                 
                    }
                }
                elseif($oper == 'add')
                {
                  $data = array(
                      'GroupGuid' =>  $districtGuid,
                      'Guid'  =>  getGuid(),
                      'name'  =>  $name,
                      'TimeSize'  =>  $time,
                      'WB_ID'  =>  session('wbid'),
                      'je'    =>  $price,
                      'BeginTime' =>  date('Y-m-d H:i:s',strtotime($start)),
                      'EndTime' =>  date('Y-m-d H:i:s',strtotime($end))
                  );
                  if(D('FixedRate')->addFixedRate($data) === false)
                  {
					     
                    $this->error();
                  }
                  else
                  {
                    $sendstr= D('FixedRate')->getLastSql(); 
                    $result =PostTopUpdateDataToWb_lzm('Php_To_Top_Sql',$sendstr);
                    if(!empty($result))
                    {
                      writelog($wbid.'新增 WDeFl  命令已发送成功','commonlog');
                    }
                    else
                    {
                      writelog($wbid.'新增 WDeFl  命令已发送失败','commonlog');
                    } 
					   
                    $this->success();
                  }
                }
                else
                {
					  
                  $this->error();
                }
            }
			   
            $this->error();
        }
		   
    }
    public function deleteFixedRate()
    {
		   
      if(IS_AJAX)
      {
          $oper = I('post.oper','','string');
          $districtId = I('post.district',0,'int')?:0;
          $id = I('post.id',0,'int')?:0;
          

          $districtGuid=D('District')->where(array('id'=>$districtId,'WB_ID'=>session('wbid')))->getField('Guid');
          $aFixedRateGuid=D('FixedRate')->where(array('id'=>$id,'WB_ID'=>session('wbid')))->getField('Guid');

          $sendstr=''; 

          if($oper == 'del' && !empty($districtGuid))
          {       
              if(D('FixedRate')->deleteFixedRateByGuid($aFixedRateGuid) === false)
              {   
                $this->error();
              }
              else
              {
                $sendstr= D('FixedRate')->getLastSql(); 
                $result =PostTopUpdateDataToWb_lzm('Php_To_Top_Sql',$sendstr);
                if(!empty($result))
                {
                   writelog($wbid.'删除 deleteFixedRate  命令已发送成功','commonlog');
                }
                else
                {
                   writelog($wbid.'删除 deleteFixedRate  命令已发送失败','commonlog');
                } 
				  
                $this->success();
              }    
          }
		   
        $this->error();
      }
	     
    }
    public function addFreeRate()
    {
		   
      if(IS_AJAX)
      {
          $wbid=session('wbid');
          $districtId = I('post.district',0,'int');     
          $districtGuid=D('District')->where(array('id'=>$districtId,'WB_ID'=>session('wbid')))->getField('Guid');

 
          if(!empty($districtGuid))
          {           
              if(D('FixedRate')->where(array('WB_ID'=>$wbid,'name'=>'自由计费','GroupGuid'=>$districtGuid))->find())
              {      
                 
                $this->error();
              }
              else
              {                
                  if(D('FixedRate')->addFreeRateByGuid($districtGuid) === false)
                  {                           
                     $this->error();
                  }
                  else
                  {
                    $sendstr= D('FixedRate')->getLastSql(); 
                    $result =PostTopUpdateDataToWb_lzm('Php_To_Top_Sql',$sendstr);
                    if(!empty($result))
                    {
                      writelog($wbid.'新增 addFreeRate  命令已发送成功','commonlog');
                    }
                    else
                    {
                      writelog($wbid.'新增 addFreeRate  命令已发送失败','commonlog');
                    } 
					   
                    $this->success();
                  }           
              }          
          }
          else
          {
              
            $this->error();
          }   
      }
	     
    }
    public function deleteFreeRate()
    {
		  
      if(IS_AJAX)
      {
          $oper = I('post.oper','','string');
          $districtId = I('post.district',0,'int');
          $districtGuid=D('District')->where(array('id'=>$districtId,'WB_ID'=>session('wbid')))->getField('Guid');
          if($oper == 'del' && !empty($districtGuid))
          {
            if(D('FixedRate')->deleteFreeRate($districtGuid) === false)
            {
				   
              $this->error();
            }
            else
            {   
                $sendstr= D('FixedRate')->getLastSql(); 
                $result =PostTopUpdateDataToWb_lzm('Php_To_Top_Sql',$sendstr);
                if(!empty($result))
                {
                  writelog($wbid.'删除 deleteFreeRate  命令已发送成功','commonlog');
                }
                else
                {
                  writelog($wbid.'删除 deleteFreeRate  命令已发送失败','commonlog');
                } 
				   
              $this->success();
            }
          }
      }
	    
    }
    public function setDiscountCardType()
    {		   
      if(IS_AJAX)
      {
        $data = I('post.');		   
        $this->ajaxReturn($data);
      }
	  
    }
    public function setDiscount()
    {
		   
      if(IS_AJAX)
      {
          $data = I('post.');
		   
          $this->ajaxReturn($data);
      }
	  
    }


   public function addVipLevel_set()
   {
	      
      if(IS_AJAX)
      {
            $wbid= session('wbid');
            $oper = I('post.oper','','string');
            $name = I('post.name','','string');
            $minpoints = I('post.minpoints',0,'int')?:0; 
            $logsdiscount = I('post.logsdiscount',100,'int')?:100;
            $goodsdiscount = I('post.goodsdiscount',100,'int')?:100;
            $id = I('post.id',0,'int')?:0;

            $result = true;
            $sendstr='';
            if(!empty($name))
            {      
                $aNewHylxGuid=getGuid();
                $data = array(
                      'WB_ID'=>session('wbid'), 
                      'Name'  =>  $name,
                      'SmallIntegral'  =>  $minpoints,
                      'SjDiscount' =>$logsdiscount,
                      'SpDiscount' =>$goodsdiscount,
                      'Guid'=>$aNewHylxGuid
                  );
                   D()->startTrans();  //开启事务

                
                  if(D('VipLevel')->addVipLevel($data) === false)
                  {
                     $result = false;
                    
                  }
                  $aTempsql1=D('VipLevel')->getLastSql(); 
				  
				  
                 
                  $aTempsql2=D('District')->updateAllDistrictByAddOneVipLevel($aNewHylxGuid,$name);
               
                  if($aTempsql2===false)
                  {
                     $result = false;
                  }
                  else
                  {
                    $sendstr=$aTempsql1.';'.$aTempsql2;
                  }  
        
                  
                  
                  if($result)
                  {
                     D()->commit();  //提交事务
                    $res =PostTopUpdateDataToWb_lzm('Php_To_Top_Sql',$sendstr);
                    if(!empty($res))
                    {
                      writelog($wbid.'更新 WHyLxTable,WGroupTable  命令已发送成功','commonlog');
                    }
                    else
                    {
                      writelog($wbid.'更新 WHyLxTable,WGroupTable  命令已发送失败'.'commonlog');
                    }   
                    
            
                    $data['result']=1;
                  }
                  else
                  {
                      
                    D()->rollback();    //回滚
                    $data['result']=-1;
                  }                 
            }
            else
            {
           
                $data['result']=-1;
            }
			  
            $this->ajaxReturn($data);
      }
	  
   }
	

    public function editVipLevel_set()
    {
		  
      if(IS_AJAX)
      {
            $wbid= session('wbid');
            $oper = I('post.oper','','string');
            $name = I('post.name','','string');
			      $minpoints = I('post.minpoints',0,'int')?:0;	
            $logsdiscount = I('post.logsdiscount',100,'int')?:100;
            $goodsdiscount = I('post.goodsdiscount',100,'int')?:100;
			      $id = I('post.id',0,'int')?:0;

            $result = true;
            if(!empty($name))
            {            
                  $data = array(
                      'Name'  =>  $name,
                      'SmallIntegral'  =>  $minpoints,
                      'SjDiscount' =>$logsdiscount,
                      'SpDiscount' =>$goodsdiscount
                  );

                  $oneViplevelGuid=D('VipLevel')->where(array('WB_ID'=>$wbid,'id'=>$id))->getField('Guid');

                  $aTempsql1=D('VipLevel')->getLastSql(); 
                  if(D('VipLevel')->updateVipLevelByGuid($oneViplevelGuid,$wbid,$data) === false)
                  {
                    $result = false;
                  }
              
			   $s=D('District')->updateAllDistrictByEditOneVipLevel_Info($oneViplevelGuid,$name,$minpoints,$logsdiscount,$goodsdiscount) ;

                  if($s==false)
                  {
                    $result = false;
                  }
                  else
                  {
                    $aTempsql2=$s;
                  }

                  if($result)
                  {
                    $sendstr=$aTempsql1.';'.$aTempsql2;
                    $res =PostTopUpdateDataToWb_lzm('Php_To_Top_Sql',$sendstr);
                    if(!empty($res))
                    {
                      writelog($wbid.'更新 editVipLevel_set  命令已发送成功','commonlog');
                    }
                    else
                    {
                      writelog($wbid.'更新 editVipLevel_set  命令已发送失败','commonlog');
                    }   
                    D()->commit();  
					   
                    $this->success();
                  }
                  else
                  {
                    D()->rollback();    
					   
                    $this->error();
                  } 
            }
            else
            {
				   
             $this->error(); 
            }            
      }
	  
    }

	
	 public function deleteVipLevel()
		{
			   
      if(IS_AJAX)
      {
          $oper = I('post.oper','','string');
          $id = I('post.id',0,'int');
	      $viplevelGuid = D('VipLevel')->where(array('id'=>$id,'WB_ID'=>session('wbid')))->getField('Guid');

          $result=true;
          if($oper == 'del'&&!empty($viplevelGuid))
          {
				    
			D()->startTrans();

            if(D('VipLevel')->deleteViplevelByGuid($viplevelGuid)===false)
            {
              $result=false;
            }

            $aTempsql1=D('VipLevel')->getLastSql(); 

            if(D('Credits')->deleteVipLevel($viplevelGuid)===false)
            {
              $result=false;
            }
            $aTempsql2=D('Credits')->getLastSql(); 
            

  			$s=D('District')->updateAllDistrictByDeleteOneVipLevel($viplevelGuid);

            if($s==false)
  			{
  			   $result=false;
  			}
            else
            {
              $aTempsql3=$s;
            }

			
            if($result)
  					{
						D()->commit();
						$sendstr= $aTempsql1.';'.$aTempsql2.';'.$aTempsql3;
						
						$res =PostTopUpdateDataToWb_lzm('Php_To_Top_Sql',$sendstr);
						if(!empty($res))
						{
						  writelog($wbid.'删除 deleteVipLevel  命令已发送成功','commonlog');
						}
						else
						{
						  writelog($wbid.'删除 deleteVipLevel  命令已发送失败','commonlog');
						} 
  				
  						$data['result']=1; 
  					     
  					}
  					else
  					{
  						D()->rollback(); 
  						$data['result']=-1; 	
  					
            }
          }  
             	  
          $this->ajaxReturn($data);       
      }
	  
    }

    public function setExchangeMoney()
    {
		   
      if(IS_AJAX)
		  {        		
  			$type = I('post.type','','string');
  			$lx = I('post.Lx','','string');
  			$integral = I('post.points','','string');
  			$je = I('post.money','','string');
  			$wbid=session('wbid');
  			
  			$duihuan_insert_data['Wb_id']= $wbid;
  			$duihuan_insert_data['HyLxGuid']= D('VipLevel')->where(array('WB_ID'=>$wbid,'id'=>$type))->getField('Guid');
  			$duihuan_insert_data['Guid']= getGuid();
  			$duihuan_insert_data['Integral']= $integral;
  			$duihuan_insert_data['Je']= $je;
  			$duihuan_insert_data['Lx']= $lx;
  			$duihuan_insert_data['SpName']= '';
  			$duihuan_insert_data['SpSfRk']= 0;
  			$duihuan_insert_data['SpId']= 0;
  			
  			



  			$duihuan_insert_result=D('Credits')->add($duihuan_insert_data);

            $sendstr=D('Credits')->getLastSql(); 
  			if(!empty($duihuan_insert_result))
  			{
            $res =PostTopUpdateDataToWb_lzm('Php_To_Top_Sql',$sendstr);
            if(!empty($res))
            {
              writelog($wbid.'添加setExchangeMoney  命令已发送成功','commonlog');
            }
            else
            {
              writelog($wbid.'添加 setExchangeMoney  命令已发送失败','commonlog');
            } 
  				$data['status']=1;
  			}
  			else
        {
  				$data['status']=0;
  			}
   		
          $this->ajaxReturn($data);
        }
		
    }
    public function editExchangeMoney()
    {
		   
      if(IS_AJAX)
  		{
  			
  			$id = I('post.id','','string');
  			$integral = I('post.points','','string');
  			$je = I('post.money','','string');
  			$wbid=session('wbid');
  			$aCreditsGuid=D('Credits')->where(array('id'=>$id,'Wb_id'=>session('wbid')))->getField('Guid');

  			$duihuan_update_data['Integral']= $integral;
  			$duihuan_update_data['Je']= $je;
  			$duihuan_update_result=D('Credits')->where(array('Wb_id'=>$wbid,'Guid'=>$aCreditsGuid))->save($duihuan_update_data);

            $sendstr=D('Credits')->getLastSql(); 

  			if(!empty($duihuan_update_result))
  			{
            $res =PostTopUpdateDataToWb_lzm('Php_To_Top_Sql',$sendstr);
            if(!empty($res))
            {
              writelog($wbid.'修改editExchangeMoney  命令已发送成功','commonlog');
            }
            else
            {
              writelog($wbid.'修改 editExchangeMoney  命令已发送失败','commonlog');
            } 
  				$data['status']=1;
  			}
  			else
        {
  				$data['status']=0;
  			}		
            			
          $this->ajaxReturn($data);
      }
	 
    }
    public function setExchangeGoods()
    {
		  
      if(IS_AJAX)
  		{
  			
  			$type = I('post.type','','string');
  			$lx = I('post.Lx','','string');
  			$integral = I('post.points','','string');
  			$spname = I('post.goods','','string');
  			$isgoods = I('post.isgoods','','string');
  			$wbid=session('wbid');
  			$duihuan_insert_data['Wb_id']= $wbid;
  			$duihuan_insert_data['HyLxGuid']= D('VipLevel')->where(array('WB_ID'=>$wbid,'id'=>$type))->getField('Guid');
  			$duihuan_insert_data['Guid']= getGuid();
  			$duihuan_insert_data['Integral']= $integral;
  			$duihuan_insert_data['Je']= 0;
  			$duihuan_insert_data['Lx']= $lx;
  			$duihuan_insert_data['SpName']=$spname;
  			$duihuan_insert_data['SpSfRk']=$isgoods;
  			$duihuan_insert_data['SpId']= 0;
  			
  			
  			$duihuan_insert_result=D('Credits')->add($duihuan_insert_data);

           $sendstr=D('Credits')->getLastSql(); 
  			if(!empty($duihuan_insert_result))
  			{
            $res =PostTopUpdateDataToWb_lzm('Php_To_Top_Sql',$sendstr);
            if(!empty($res))
            {
              writelog($wbid.'新增setExchangeGoods 命令已发送成功','commonlog');
            }
            else
            {
              writelog($wbid.'新增 setExchangeGoods  命令已发送失败','commonlog');
            } 
  				$data['status']=1;
  			}
  			else
            {
  				$data['status']=0;
  			}
         		
          $this->ajaxReturn($data);
      }
	  
    }
    public function editExchangeGoods()
    {
		 
        if(IS_AJAX)
    	{
    			$id = I('post.id','','string');
    			$integral = I('post.points','','string');
    			$spname = I('post.goods','','string');
    			$isgoods = I('post.isgoods','','string');
    			$wbid=session('wbid');
    			
    			$duihuan_update_data['Integral']= $integral;
    			$duihuan_update_data['SpName']=$spname;
    			$duihuan_update_data['SpSfRk']=$isgoods;
     
                $aCreditsGuid=D('Credits')->where(array('id'=>$id,'Wb_id'=>session('wbid')))->getField('Guid');
    			
    			$duihuan_update_result=D('Credits')->where(array('Wb_id'=>$wbid,'Guid'=>$aCreditsGuid))->save($duihuan_update_data);

                $sendstr=D('Credits')->getLastSql(); 
    			if(!empty($duihuan_update_result))
    			{
				$res =PostTopUpdateDataToWb_lzm('Php_To_Top_Sql',$sendstr);
				if(!empty($res))
				{
				  writelog($wbid.'修改editExchangeGoods  命令已发送成功','commonlog');
				}
				else
				{
				  writelog($wbid.'修改 editExchangeGoods  命令已发送失败','commonlog');
				} 
    				$data['status']=1;
    			}
    			else
                {
    				$data['status']=0;
    			}	
            
        $this->ajaxReturn($data);
      }
	  
    }
    public function setExchangeAccess()
    {
		 
      if(IS_AJAX)
      {
        $data = I('post.');
		
        $this->ajaxReturn($data);
      }
	  
    }
	
    public function getCredits()
  	{
		  
        if(IS_AJAX)
        {
    	   $wbid=session('wbid');
           $vipLevelId = I('get.type',0,'int');
           $credits = D('Credits')->getCredis($vipLevelId,$wbid);
    					
    		$credits['result']='1';	
    		$this->ajaxReturn($credits);
        }
		
    }
    public function deleteCredits()
    {
		
      if(IS_AJAX)
      {
          $oper = I('post.oper','','string');
          $id = I('post.id',0,'int')?:0;
          $aCreditsGuid=D('Credits')->where(array('id'=>$id,'Wb_id'=>session('wbid')))->getField('Guid');


          if($oper == 'del')
          {
            $duihuan_del_result=D('Credits')->where(array('Wb_id'=>session('wbid'),'Guid'=>$aCreditsGuid))->delete();

            $sendstr=D('Credits')->getLastSql();
      			if(!empty($duihuan_del_result))
      			{
              $res =PostTopUpdateDataToWb_lzm('Php_To_Top_Sql',$sendstr);
              if(!empty($res))
              {
                writelog($wbid.'删除deleteCredits  命令已发送成功','commonlog');
              }
              else
              {
                writelog($wbid.'删除 deleteCredits 命令已发送失败','commonlog');
              } 
      				$data['result']=1;
      			}
      			else
            {
      				$data['result']=0;
      			}	
            }
		   
		      $this->ajaxReturn($data);
      }
	 
    }

    public function getSurcharge()
    {
		 
        if(IS_AJAX)
        {
          if(I('get.district') == 0)
          {
            $data = array(array('name'=>'haha','price'=>10.00));
          }
          else
          {
            $data = array();
          }
		 
          $this->ajaxReturn($data);
        }
		
    }
    public function setSurcharge()
    {
		 
      if(IS_AJAX)
      {
        $data = I('post.');		
        $this->ajaxReturn($data);
      }
	  
    }


    public function server()
    {
		  
	   $oneserverinfo=array();	
       
	   $FCardNoCount= D('WIni')->getOneRecordByName('FCardNoCount');	   
       $oneserverinfo['FCardNoCount']=strtolower($FCardNoCount);
	   	   
	   $m_TemOpenDigTime=D('WIni')->getOneRecordByName('m_TemOpenDigTime');
       $oneserverinfo['m_TemOpenDigTime']= strtolower($m_TemOpenDigTime);
	   
	   $m_TemOpenDigResult=D('WIni')->getOneRecordByName('m_TemOpenDigResult');  
       $oneserverinfo['m_TemOpenDigResult']= strtolower($m_TemOpenDigResult);
	   	   
	   $FTemDefJe	 =D('WIni')->getOneRecordByName('FTemDefJe');  
       $oneserverinfo['FTemDefJe']=strtolower($FTemDefJe);
	   
	   
	   $FHyAddLowestJe	 =D('WIni')->getOneRecordByName('FHyAddLowestJe');  
       $oneserverinfo['FHyAddLowestJe']=strtolower($FHyAddLowestJe);
	   	   
	   $m_TemCardInvalidTime	 =D('WIni')->getOneRecordByName('m_TemCardInvalidTime');  
       $oneserverinfo['m_TemCardInvalidTime']=strtolower($m_TemCardInvalidTime);
	   
	   $m_HyOpenDigTime	 =D('WIni')->getOneRecordByName('m_HyOpenDigTime');  
       $oneserverinfo['m_HyOpenDigTime']=strtolower($m_HyOpenDigTime);	   
	   
	   $m_HyOpenDigResult	 =D('WIni')->getOneRecordByName('m_HyOpenDigResult');  
       $oneserverinfo['m_HyOpenDigResult']=strtolower($m_HyOpenDigResult);
	   
	   
	   $FHyAutoOpenCard	 =D('WIni')->getOneRecordByName('FHyAutoOpenCard');  
       $oneserverinfo['FHyAutoOpenCard']=strtolower($FHyAutoOpenCard);
 
	   $FHyAllowBs	 =D('WIni')->getOneRecordByName('FHyAllowBs');  
       $oneserverinfo['FHyAllowBs']=strtolower($FHyAllowBs);
	   
	   
	 

	   $m_HyZhengSongBaoShi	 =D('WIni')->getOneRecordByName('m_HyZhengSongBaoShi');  
       $oneserverinfo['m_HyZhengSongBaoShi']=strtolower($m_HyZhengSongBaoShi);
	   
	   
	   $m_HyYa_JinToYu_e	 =D('WIni')->getOneRecordByName('m_HyYa_JinToYu_e');  
       $oneserverinfo['m_HyYa_JinToYu_e']=strtolower($m_HyYa_JinToYu_e);
	   
	   
	   
	   
 
	   $FAllowHyCardToTemCardXf	 =D('WIni')->getOneRecordByName('FAllowHyCardToTemCardXf');  
       $oneserverinfo['FAllowHyCardToTemCardXf']=strtolower($FAllowHyCardToTemCardXf);
	   

	   $m_HyDeductScheme	 =D('WIni')->getOneRecordByName('m_HyDeductScheme');  
       $oneserverinfo['m_HyDeductScheme']=strtolower($m_HyDeductScheme);
	   
       // $oneserverinfo['FBjAllowOneCp']=D('WIni')->getOneRecordByName('FBjAllowOneCp');
	   
	   $m_HyBsChangeBz	 =D('WIni')->getOneRecordByName('m_HyBsChangeBz');  
       $oneserverinfo['m_HyBsChangeBz']=strtolower($m_HyBsChangeBz);
	   
	   $m_TemCardAutoBs	 =D('WIni')->getOneRecordByName('m_TemCardAutoBs');  
       $oneserverinfo['m_TemCardAutoBs']=strtolower($m_TemCardAutoBs);	   
   
	   $m_EndTimeCard	 =D('WIni')->getOneRecordByName('m_EndTimeCard');  
       $oneserverinfo['m_EndTimeCard']=strtolower($m_EndTimeCard);
	   
	   $m_MessageCount	 =D('WIni')->getOneRecordByName('m_MessageCount');  
       $oneserverinfo['m_MessageCount']=strtolower($m_MessageCount);
      
      
           

 
	   $m_ProhibitHyAutoEnd	 =D('WIni')->getOneRecordByName('m_ProhibitHyAutoEnd');  
       $oneserverinfo['m_ProhibitHyAutoEnd']=strtolower($m_ProhibitHyAutoEnd);
	   
    
	   $m_ChangeJe	 =D('WIni')->getOneRecordByName('m_ChangeJe');  
       $oneserverinfo['m_ChangeJe']=strtolower($m_ChangeJe);
	   
      
	   $m_HyEndDigTime	 =D('WIni')->getOneRecordByName('m_HyEndDigTime');  
       $oneserverinfo['m_HyEndDigTime']=strtolower($m_HyEndDigTime);
	   
    
	   $m_TemEndDigTime	 =D('WIni')->getOneRecordByName('m_TemEndDigTime');  
       $oneserverinfo['m_TemEndDigTime']=strtolower($m_TemEndDigTime);
	   
    
	   $m_HyEndDigResult	 =D('WIni')->getOneRecordByName('m_HyEndDigResult');  
       $oneserverinfo['m_HyEndDigResult']=strtolower($m_HyEndDigResult);
	   
    
	   $m_TemEndDigResult	 =D('WIni')->getOneRecordByName('m_TemEndDigResult');  
       $oneserverinfo['m_TemEndDigResult']=strtolower($m_TemEndDigResult);
	   
	   $m_HyEndTimeAnswer	 =D('WIni')->getOneRecordByName('m_HyEndTimeAnswer');  
       $oneserverinfo['m_HyEndTimeAnswer']=strtolower($m_HyEndTimeAnswer);
	   
       
	   $m_TemCardEndTimeAnswer	 =D('WIni')->getOneRecordByName('m_TemCardEndTimeAnswer');  
       $oneserverinfo['m_TemCardEndTimeAnswer']=strtolower($m_TemCardEndTimeAnswer);
	      
	   $FLockLoginCloseComputerTime	 =D('WIni')->getOneRecordByName('FLockLoginCloseComputerTime');  
       $oneserverinfo['FLockLoginCloseComputerTime']=strtolower($FLockLoginCloseComputerTime);
	   
	   $FLockLoginCloseComputer	 =D('WIni')->getOneRecordByName('FLockLoginCloseComputer');  
       $oneserverinfo['FLockLoginCloseComputer']=strtolower($FLockLoginCloseComputer);
	   
	   $FHyCloseCpEndAcc	 =D('WIni')->getOneRecordByName('FHyCloseCpEndAcc');  
       $oneserverinfo['FHyCloseCpEndAcc']=strtolower($FHyCloseCpEndAcc);
	   
      
	   $m_ChangeShiftsVisible	 =D('WIni')->getOneRecordByName('m_ChangeShiftsVisible');  
       $oneserverinfo['m_ChangeShiftsVisible']=strtolower($m_ChangeShiftsVisible);
	
	   $FChangeWorkNotKeepJe	 =D('WIni')->getOneRecordByName('FChangeWorkNotKeepJe');  
       $oneserverinfo['FChangeWorkNotKeepJe']=strtolower($FChangeWorkNotKeepJe);
	   

     



	   $m_ClientChangeCp	 =D('WIni')->getOneRecordByName('m_ClientChangeCp');  
       $oneserverinfo['m_ClientChangeCp']=strtolower($m_ClientChangeCp);
	      
	   $FClientGroupChangeCp	 =D('WIni')->getOneRecordByName('FClientGroupChangeCp');  
       $oneserverinfo['FClientGroupChangeCp']=strtolower($FClientGroupChangeCp);
	      
	   $m_ClientChangeXf	 =D('WIni')->getOneRecordByName('m_ClientChangeXf');  
       $oneserverinfo['m_ClientChangeXf']=strtolower($m_ClientChangeXf);
	   
	   $m_ClientOkChangeCp	 =D('WIni')->getOneRecordByName('m_ClientOkChangeCp');  
       $oneserverinfo['m_ClientOkChangeCp']=strtolower($m_ClientOkChangeCp);
	   
	   $m_NotShowHyJiFen	 =D('WIni')->getOneRecordByName('m_NotShowHyJiFen');  
       $oneserverinfo['m_NotShowHyJiFen']=strtolower($m_NotShowHyJiFen);
	   
	   
	   
	   $m_ClientChangeCp	 =D('WIni')->getOneRecordByName('m_ClientChangeCp');  
       $oneserverinfo['m_ClientChangeCp']=strtolower($m_ClientChangeCp);
	   
	   
	   $m_ClientEditHyPw	 =D('WIni')->getOneRecordByName('m_ClientEditHyPw');  
       $oneserverinfo['m_ClientEditHyPw']=strtolower($m_ClientEditHyPw);
	   
	   $m_SuperJMTime	 =D('WIni')->getOneRecordByName('m_SuperJMTime');  
       $oneserverinfo['m_SuperJMTime']=strtolower($m_SuperJMTime);
	   
		$m_SuperCount	 =D('WIni')->getOneRecordByName('m_SuperCount');  
       $oneserverinfo['m_SuperCount']=strtolower($m_SuperCount);
	
		
	
		
		
  
	   $m_ClientCloseYorN	 =D('WIni')->getOneRecordByName('m_ClientCloseYorN');  
       $oneserverinfo['m_ClientCloseYorN']=strtolower($m_ClientCloseYorN);
	   

	
	   $m_ClientCloseMin	 =D('WIni')->getOneRecordByName('m_ClientCloseMin');  
       $oneserverinfo['m_ClientCloseMin']=strtolower($m_ClientCloseMin);
	   
  
	   $m_ClientUnLockPw	 =D('WIni')->getOneRecordByName('m_ClientUnLockPw');  
       $oneserverinfo['m_ClientUnLockPw']=strtolower($m_ClientUnLockPw);
	   
     
	   $m_TempCardPw	 =D('WIni')->getOneRecordByName('m_TempCardPw');  
       $oneserverinfo['m_TempCardPw']=strtolower($m_TempCardPw);
	   
    
	   $FGlwAddDefaultPw	 =D('WIni')->getOneRecordByName('FGlwAddDefaultPw');  
       $oneserverinfo['FGlwAddDefaultPw']=strtolower($FGlwAddDefaultPw);
	   
	   
	 
    
  
	   $FHyDhJf	 =D('WIni')->getOneRecordByName('FHyDhJf');  
       $oneserverinfo['FHyDhJf']=strtolower($FHyDhJf);
	   
     
	   $FHyDhJfClient	 =D('WIni')->getOneRecordByName('FHyDhJfClient');  
       $oneserverinfo['FHyDhJfClient']=strtolower($FHyDhJfClient);
	   

	   $FHyAutoLevel	 =D('WIni')->getOneRecordByName('FHyAutoLevel');  
       $oneserverinfo['FHyAutoLevel']=strtolower($FHyAutoLevel);
	   

	   $m_Jifen_Jiangli	 =D('WIni')->getOneRecordByName('m_Jifen_Jiangli');  
       $oneserverinfo['m_Jifen_Jiangli']=strtolower($m_Jifen_Jiangli);
	   
	   
	   // foreach( $oneserverinfo as $k=>$val)
	   // {
		  // $oneserverinfo[$k]=strtolower($oneserverinfo[$k]); 
	   // }
	   
	   // write(json_encode($oneserverinfo),'read');

	   
	   
       $this->assign('oneserverinfo',$oneserverinfo);
	  
       $this->display();
	    
	   
    }
	
	public function  updateServerIni()
	{

	}



    public function server_set()
    {  
    	//页面1
     
	   $post_data=I('post.');
	  
	    
       $FCardNoCount=I('post.jh_ls_cardnum','6','string');  
       $m_TemOpenDigTime     =I('post.jh_ls_tltime','60','string');
       $m_TemOpenDigResult   =I('post.jh_ls_ClFs','','string'); 
       $FTemDefJe            =I('post.jh_ls_defaultmoney','5','string');
       $FHyAddLowestJe      =I('post.jh_hy_FHyAddLowestJe','0','string');   
	   
	   
       $m_TemCardInvalidTime =I('post.jh_ls_shixiao','60','string');
       $m_HyOpenDigTime      =I('post.jh_hy_tltime','60','string');
       $m_HyOpenDigResult    =I('post.jh_hy_ClFs','0','string');
       $FHyAutoOpenCard      =I('post.FHyAutoOpenCard','','string');

       if(!empty($FHyAutoOpenCard))
       {
         $FHyAutoOpenCard='true';
       }
       else
       {
         $FHyAutoOpenCard='false';
       }	

       $aTempstr='';
       $res=D('WIni')->postOneRecord('FCardNoCount',$FCardNoCount);     //自定义非实名卡号  
       $aTempstr=D('WIni')->getLastSql();
       $sendstr.=$aTempstr.';';

       $res=D('WIni')->postOneRecord('m_TemOpenDigTime',$m_TemOpenDigTime);             //临时卡会员激活窗口停留时间   m_TemOpenDigTime 
       $aTempstr=D('WIni')->getLastSql();
       $sendstr.=$aTempstr.';'; 

       $res=D('WIni')->postOneRecord('m_TemOpenDigResult',$m_TemOpenDigResult);  //  临时卡激活窗口默认处理方式 
       $aTempstr=D('WIni')->getLastSql();
       $sendstr.=$aTempstr.';';

       $res=D('WIni')->postOneRecord('FTemDefJe',$FTemDefJe);                //临时卡默认押金
       $aTempstr=D('WIni')->getLastSql();
	   
       $sendstr.=$aTempstr.';'; 
	   
	   $res=D('WIni')->postOneRecord('FHyAddLowestJe',$FHyAddLowestJe);     // 会员最少加钱
       $aTempstr=D('WIni')->getLastSql();
       $sendstr.=$aTempstr.';'; 
	   
	  

       $res=D('WIni')->postOneRecord('m_TemCardInvalidTime',$m_TemCardInvalidTime);     // 临时卡未登录失效  m_TemCardInvalidTime
       $aTempstr=D('WIni')->getLastSql();
       $sendstr.=$aTempstr.';'; 

       $res=D('WIni')->postOneRecord('m_HyOpenDigTime',$m_HyOpenDigTime);     //会员开卡激活窗口停留时间 m_HyOpenDigTime  默认60s 
       $aTempstr=D('WIni')->getLastSql();
       $sendstr.=$aTempstr.';'; 

       $res=D('WIni')->postOneRecord('m_HyOpenDigResult',$m_HyOpenDigResult);    //  会员卡激活窗口默认处理方式   0 关闭激活框，1自动激活
       $aTempstr=D('WIni')->getLastSql();
       $sendstr.=$aTempstr.';'; 

       $res=D('WIni')->postOneRecord('FHyAutoOpenCard',$FHyAutoOpenCard); //会员不激活直接上机 FHyAutoOpenCard 
       $aTempstr=D('WIni')->getLastSql();
       $sendstr.=$aTempstr.';'; 

      
      
       
      
      //页面2
      
       $FAllowHyCardToTemCardXf      =I('post.sj_hy_lx_yj','','string');
       $FHyAllowBs                   =I('post.sj_hy_lx_bj','','string');
       $m_HyZhengSongBaoShi          =I('post.sj_hy_lx_jlje','','string');	   
	   $m_HyYa_JinToYu_e             =I('post.sj_m_HyYa_JinToYu_e','','string');
	   
       $m_HyDeductScheme             =I('post.sj_manage_kcset','','string');
       // $FBjAllowOneCp                =I('post.sj_manage_bjset','false','string');
       $m_HyBsChangeBz               =I('post.sj_manage_zhset','','string');
       $m_TemCardAutoBs              =I('post.lsk_go_baoshi','','string');
       $m_EndTimeCard                =I('post.sj_manage_tipmin','10','string');
       $m_MessageCount               =I('post.sj_manage_tiptimes','3','string');
	   
	   


       if(!empty($FAllowHyCardToTemCardXf))
       {
         $FAllowHyCardToTemCardXf='true';
       }
       else
       {
         $FAllowHyCardToTemCardXf='false';
       }
       
       if(!empty($FHyAllowBs))
       {
         $FHyAllowBs='true';
       }
       else
       {
         $FHyAllowBs='false';
       }

       if(!empty($m_HyZhengSongBaoShi))
       {
         $m_HyZhengSongBaoShi='true';
       }
       else
       {
         $m_HyZhengSongBaoShi='false';
       }
	   
	   
	   if(!empty($m_HyYa_JinToYu_e))
       {
         $m_HyYa_JinToYu_e='1';
       }
       else
       {
         $m_HyYa_JinToYu_e='0';
       }


       if(!empty($m_HyDeductScheme))
       {
         $m_HyDeductScheme='0';
       }
       else
       {
         $m_HyDeductScheme='1';
       }

       if(!empty($m_HyBsChangeBz))
       {
         $m_HyBsChangeBz='true';
       }
       else
       {
         $m_HyBsChangeBz='false';
       }

      if(!empty($m_TemCardAutoBs))
       {
         $m_TemCardAutoBs='true';
       }
       else
       {
         $m_TemCardAutoBs='false';
       }


   
       $res=D('WIni')->postOneRecord('FHyAllowBs',$FHyAllowBs);      //本金                 会员定额 可用类型  FAllowHyCardToTemCardXf（押金） FHyAllowBs（本金）m_HyZhengSongBaoShi（奖励金额）
       $aTempstr=D('WIni')->getLastSql();
       $sendstr.=$aTempstr.';'; 

       $res=D('WIni')->postOneRecord('m_HyZhengSongBaoShi',$m_HyZhengSongBaoShi);     //（奖励金额） 会员定额 可用类型  FHyAllowBs（本金）m_HyZhengSongBaoShi（奖励金额）
       $aTempstr=D('WIni')->getLastSql();
       $sendstr.=$aTempstr.';'; 
     
       $res=D('WIni')->postOneRecord('FAllowHyCardToTemCardXf',$FAllowHyCardToTemCardXf); // FAllowHyCardToTemCardXf（押金）
       $aTempstr=D('WIni')->getLastSql();
       $sendstr.=$aTempstr.';'; 

       $res=D('WIni')->postOneRecord('m_HyYa_JinToYu_e',$m_HyYa_JinToYu_e);    //  会员押金上机结束自动转为余额上机
       $aTempstr=D('WIni')->getLastSql();
       $sendstr.=$aTempstr.';'; 
	   
	   
	   $res=D('WIni')->postOneRecord('m_HyDeductScheme',$m_HyDeductScheme);    //  会员上机优先扣除本金余额，再扣除奖励余额   m_HyDeductScheme   0 先扣本金，1先扣除奖励
       // $res=D('WIni')->postOneRecord('FBjAllowOneCp',$FBjAllowOneCp);       //包间内电脑可以作为单机上机    FBjAllowOneCp
       $aTempstr=D('WIni')->getLastSql();
       $sendstr.=$aTempstr.';'; 
	   
	   
	   

       $res=D('WIni')->postOneRecord('m_HyBsChangeBz',$m_HyBsChangeBz);                 //定额结束自动转为标准上机     m_HyBsChangeBz 
       $aTempstr=D('WIni')->getLastSql();
       $sendstr.=$aTempstr.';'; 

       $res=D('WIni')->postOneRecord('m_TemCardAutoBs',$m_TemCardAutoBs);                //允许临时卡自动转包时
       $aTempstr=D('WIni')->getLastSql();
       $sendstr.=$aTempstr.';';

       $res=D('WIni')->postOneRecord('m_EndTimeCard',$m_EndTimeCard );       //剩余时间小于（m_EndTimeCard）分钟进行余额不足提醒 提醒（m_MessageCount）次 每次间隔 m_MessageSpaceTime（2分钟）
       $aTempstr=D('WIni')->getLastSql();
       $sendstr.=$aTempstr.';'; 

       $res=D('WIni')->postOneRecord('m_MessageCount',$m_MessageCount);     //剩余时间小于（m_EndTimeCard）分钟进行余额不足提醒 提醒（m_MessageCount）次 每次间隔 m_MessageSpaceTime（2分钟）
       $aTempstr=D('WIni')->getLastSql();
       $sendstr.=$aTempstr.';'; 

       $res=D('WIni')->postOneRecord('m_MessageSpaceTime',2); //剩余时间小于（m_EndTimeCard）分钟进行余额不足提醒 提醒（m_MessageCount）次 每次间隔 m_MessageSpaceTime（2分钟）
       $aTempstr=D('WIni')->getLastSql();
       $sendstr.=$aTempstr.';'; 

       $res=D('WIni')->postOneRecord('m_hyYouhui','true');  
       $aTempstr=D('WIni')->getLastSql();
       $sendstr.=$aTempstr.';';                    
       
       //页面3
       
        $m_ProhibitHyAutoEnd         =I('post.jz_hy_khd','','string');
        $m_ChangeJe                  =I('post.jz_sy_editmoney','','string');
        $m_HyEndDigTime              =I('post.jz_sy_staytime','0','string');
        $m_TemEndDigTime             =I('post.jz_sy_staytime','0','string');
        $m_HyEndTimeAnswer           =I('post.jz_hyxj_defaultoper','0','string');
        $m_TemCardEndTimeAnswer      =I('post.jz_lsxj_defaultoper','0','string');
        $FLockLoginCloseComputerTime =I('post.jz_xj_closecomputer_time','0','string');
        $FLockLoginCloseComputer     =I('post.jz_xj_closecomputer','','string');
        $FHyCloseCpEndAcc            =I('post.jz_hy_khjclose_time','60','string');
        $m_HyEndDigResult            =I('post.jz_sy_clfs','0','string');
        $m_TemEndDigResult           =I('post.jz_sy_clfs','0','string');



        if(!empty($m_ProhibitHyAutoEnd))
       {
         $m_ProhibitHyAutoEnd='true';
       }
       else
       {
         $m_ProhibitHyAutoEnd='false';
       }

       if(!empty($m_ChangeJe))
       {
         $m_ChangeJe='true';
       }
       else
       {
         $m_ChangeJe='false';
       }

       if(!empty($FLockLoginCloseComputer))
       {
         $FLockLoginCloseComputer='true';
       }
       else
       {
         $FLockLoginCloseComputer='false';
       }
                                      

        $res=D('WIni')->postOneRecord('m_ProhibitHyAutoEnd',$m_ProhibitHyAutoEnd);    //禁止会员在客户端自主结账    m_ProhibitHyAutoEnd
        $aTempstr=D('WIni')->getLastSql();
        $sendstr.=$aTempstr.';';  

        $res=D('WIni')->postOneRecord('m_ChangeJe',$m_ChangeJe);        // 允许修改实收金额         m_ChangeJe
        $aTempstr=D('WIni')->getLastSql();
        $sendstr.=$aTempstr.';';  

        $res=D('WIni')->postOneRecord('m_HyEndDigTime',$m_HyEndDigTime);      //结账窗口停留   秒           m_HyEndDigTime  m_TemEndDigTime     
        $aTempstr=D('WIni')->getLastSql();
        $sendstr.=$aTempstr.';';  

        $res=D('WIni')->postOneRecord('m_TemEndDigTime',$m_TemEndDigTime);     //结账窗口停留   秒           m_HyEndDigTime  m_TemEndDigTime
        $aTempstr=D('WIni')->getLastSql();
        $sendstr.=$aTempstr.';';  

        $res=D('WIni')->postOneRecord('m_HyEndDigResult',$m_HyEndDigResult);    //结账窗口超时默认处理方法  0 取消结账窗口，1结账  m_HyEndDigResult  m_TemEndDigResult 
        $aTempstr=D('WIni')->getLastSql();
        $sendstr.=$aTempstr.';';  

        $res=D('WIni')->postOneRecord('m_TemEndDigResult',$m_TemEndDigResult);   //临时卡窗口超时默认处理方法  0 取消结账窗口，1结账  m_HyEndDigResult  m_TemEndDigResult
        $aTempstr=D('WIni')->getLastSql();
        $sendstr.=$aTempstr.';';  

        $res=D('WIni')->postOneRecord('m_HyEndTimeAnswer',$m_HyEndTimeAnswer);  //会员卡下机以后默认操作   m_HyEndTimeAnswer           0关机 1锁屏 2重启
        $aTempstr=D('WIni')->getLastSql();
        $sendstr.=$aTempstr.';';  

        $res=D('WIni')->postOneRecord('m_TemCardEndTimeAnswer',$m_TemCardEndTimeAnswer);  //临时卡下机以后默认操作   m_TemCardEndTimeAnswer    0关机 1锁屏 2重启
        $aTempstr=D('WIni')->getLastSql();
        $sendstr.=$aTempstr.';';  
        
        $res=D('WIni')->postOneRecord('FLockLoginCloseComputerTime',$FLockLoginCloseComputerTime);   //分钟无人使用则关闭计算机   FLockLoginCloseComputerTime 
        $aTempstr=D('WIni')->getLastSql();
        $sendstr.=$aTempstr.';';  

        $res=D('WIni')->postOneRecord('FLockLoginCloseComputer',$FLockLoginCloseComputer);       //FLockLoginCloseComputer     启用无人上机关闭
        $aTempstr=D('WIni')->getLastSql();
        $sendstr.=$aTempstr.';';

        $res=D('WIni')->postOneRecord('FHyCloseCpEndAcc',$FHyCloseCpEndAcc);   // 会员标准上机，客户机关闭  分钟将会自动结账  FHyCloseCpEndAcc  
        $aTempstr=D('WIni')->getLastSql();
        $sendstr.=$aTempstr.';';    
      
      //页面4
      
       $m_ChangeShiftsVisible   =I('post.fwd_wh_hidemoney','','string');
       $FChangeWorkNotKeepJe    =I('post.fwd_wh_sjmoney','','string');   

       if(!empty($m_ChangeShiftsVisible))
       {
         $m_ChangeShiftsVisible='true';
       }
       else
       {
         $m_ChangeShiftsVisible='false';
       }

       if(!empty($FChangeWorkNotKeepJe))
       {
         $FChangeWorkNotKeepJe='true';
       }
       else
       {
         $FChangeWorkNotKeepJe='false';
       }


      $res=D('WIni')->postOneRecord('m_ChangeShiftsVisible',$m_ChangeShiftsVisible);  //隐藏交班时的上缴金额   m_ChangeShiftsVisible
      $aTempstr=D('WIni')->getLastSql();
      $sendstr.=$aTempstr.';';  
      
      $res=D('WIni')->postOneRecord('FChangeWorkNotKeepJe',$FChangeWorkNotKeepJe);    //现金全部上缴       FChangeWorkNotKeepJe
      $aTempstr=D('WIni')->getLastSql();
      $sendstr.=$aTempstr.';';  


      //页面5
 
      
       $m_ClientChangeCp        =I('post.khd_set_khdhj','','string');
       $FClientGroupChangeCp    =I('post.khd_set_kqyhj','','string');
       $m_ClientChangeXf        =I('post.khd_set_khdzzbs','','string');
       $m_ClientOkChangeCp      =I('post.khd_set_khdhjqr','','string');
       $m_NotShowHyJiFen      =I('post.khd_set_khdxshyjf','','string');   
	   $m_ClientEditHyPw      =I('post.khd_set_hyzzxgmm','','string');
	   
	       
       $m_SuperCount            =I('post.khd_set_max_unlock_count','10','string');
	   $m_SuperJMTime            =I('post.khd_set_max_unlock_time','30','string');
       $m_ClientCloseYorN       =I('post.khd_set_fwdlinkerror','','string');
       $m_ClientCloseMin        =I('post.khd_set_fwdlinkerror_time','60','string');


       $m_ClientUnLockPw        =I('post.khd_set_password','123','string');
       $m_TempCardPw            =I('post.khd_set_ls_password','0','string');
       $FGlwAddDefaultPw        =I('post.khd_set_xzhy_password','0','string');


       if(!empty($m_ClientChangeCp))
       {
         $m_ClientChangeCp='true';
       }
       else
       {
         $m_ClientChangeCp='false';
       }

       if(!empty($FClientGroupChangeCp))
       {
         $FClientGroupChangeCp='true';
       }
       else
       {
         $FClientGroupChangeCp='false';
       }

       if(!empty($m_ClientChangeXf))
       {
         $m_ClientChangeXf='true';
       }
       else
       {
         $m_ClientChangeXf='false';
       }

       if(!empty($m_ClientOkChangeCp))
       {
         $m_ClientOkChangeCp='true';
       }
       else
       {
         $m_ClientOkChangeCp='false';
       }

	   if(!empty($m_NotShowHyJiFen))
       {
         $m_NotShowHyJiFen='1';
       }
       else
       {
         $m_NotShowHyJiFen='0';
       }
	   
	   
	   if(!empty($m_ClientEditHyPw))
       {
         $m_ClientEditHyPw='true';
       }
       else
       {
         $m_ClientEditHyPw='false';
       }
	   
	  	   
	   
       if(!empty($m_ClientCloseYorN))
       {
         $m_ClientCloseYorN='true';
       }
       else
       {
         $m_ClientCloseYorN='false';
       }
       $res=D('WIni')->postOneRecord('m_ClientChangeCp',$m_ClientChangeCp);         // 允许客户端换机     m_ClientChangeCp
	   
       $aTempstr=D('WIni')->getLastSql();	    
       $sendstr.=$aTempstr.';'; 
       $res=D('WIni')->postOneRecord('FClientGroupChangeCp',$FClientGroupChangeCp);     // 允许跨区域换机     FClientGroupChangeCp
       $aTempstr=D('WIni')->getLastSql();
       $sendstr.=$aTempstr.';';  

       $res=D('WIni')->postOneRecord('m_ClientChangeXf',$m_ClientChangeXf);         //允许客户端自主包时   m_ClientChangeXf
       $aTempstr=D('WIni')->getLastSql();
       $sendstr.=$aTempstr.';';  

       $res=D('WIni')->postOneRecord('m_ClientOkChangeCp',$m_ClientOkChangeCp);       //客户端换机需原机确认  m_ClientOkChangeCp
       $aTempstr=D('WIni')->getLastSql();
       $sendstr.=$aTempstr.';';  
      
	   $res=D('WIni')->postOneRecord('m_NotShowHyJiFen',$m_NotShowHyJiFen);       //客户端不显示会员积分  m_NotShowHyJiFen
       $aTempstr=D('WIni')->getLastSql();
       $sendstr.=$aTempstr.';'; 
	   
	   
	   
	   	$res=D('WIni')->postOneRecord('m_ClientEditHyPw',$m_ClientEditHyPw);       //客户端会员自主修改密码  m_ClientHyEditPassword
       $aTempstr=D('WIni')->getLastSql();
       $sendstr.=$aTempstr.';';
	   
	   
	   if(is_numeric($m_SuperCount))
	   {
		  $res=D('WIni')->postOneRecord('m_SuperCount',$m_SuperCount);       // 管理员最大解锁台数：    m_SuperCount  
	   }
	   else
	   {
		  $res=D('WIni')->postOneRecord('m_SuperCount',100);       // 管理员最大解锁台数：    m_SuperCount   
	   }
       $aTempstr=D('WIni')->getLastSql();
       $sendstr.=$aTempstr.';';  

       if(is_numeric($m_SuperJMTime))
	   {
		  $res=D('WIni')->postOneRecord('m_SuperJMTime',$m_SuperJMTime);       // 管理员最大解锁时间：      
	   }
	   else
	   {
		  $res=D('WIni')->postOneRecord('m_SuperJMTime',30);       // 管理员最大解锁时间：       
	   }		   
   
       $aTempstr=D('WIni')->getLastSql();
       $sendstr.=$aTempstr.';';  

       $res=D('WIni')->postOneRecord('m_ClientCloseYorN',$m_ClientCloseYorN);          // m_ClientCloseYorN  启用检测服务端连接失败后（）分钟执行关机
       $aTempstr=D('WIni')->getLastSql();
       $sendstr.=$aTempstr.';';  
       
       $res=D('WIni')->postOneRecord('m_ClientCloseMin',$m_ClientCloseMin);     // 检测与服务端的连接失败后   分钟,将执行   m_ClientCloseMin
       $aTempstr=D('WIni')->getLastSql();
       $sendstr.=$aTempstr.';';  
       
       $res=D('WIni')->postOneRecord('m_ClientUnLockPw',$m_ClientUnLockPw);    // 客户端解锁密码：   默认密码：123               m_ClientUnLockPw
       $aTempstr=D('WIni')->getLastSql();
       $sendstr.=$aTempstr.';';  

       $res=D('WIni')->postOneRecord('m_TempCardPw',$m_TempCardPw);          //临时卡默认上机密码：   默认密码：0    m_TempCardPw     
       $aTempstr=D('WIni')->getLastSql();
       $sendstr.=$aTempstr.';'; 

       $res=D('WIni')->postOneRecord('FGlwAddDefaultPw',$FGlwAddDefaultPw);    //新增会员默认上机密码：   默认密码：0（过滤王实名下生效）  FGlwAddDefaultPw
       $aTempstr=D('WIni')->getLastSql();
       $sendstr.=$aTempstr.';';  
     //页面6
     
       $FHyDhJf    =I('post.qt_jf_dh','','string');
       $FHyDhJfClient    =I('post.qt_jf_khddh','','string');
       $FHyAutoLevel    =I('post.qt_viplever_ts','','string');
       $m_Jifen_Jiangli    =I('post.qt_viplever_jf','0','string');

       if(!empty($FHyDhJf))
       {
         $FHyDhJf='true';
       }
       else
       {
         $FHyDhJf='false';
       }

       if(!empty($FHyDhJfClient))
       {
         $FHyDhJfClient='true';
       }
       else
       {
         $FHyDhJfClient='false';
       }

       if(!empty($FHyAutoLevel))
       {
         $FHyAutoLevel='true';
       }
       else
       {
         $FHyAutoLevel='false';
       }

   
       $res=D('WIni')->postOneRecord('FHyDhJf',$FHyDhJf);                  //允许会员积分兑换            FHyDhJf
       $aTempstr=D('WIni')->getLastSql();
       $sendstr.=$aTempstr.';';  
       
       $res=D('WIni')->postOneRecord('FHyDhJfClient',$FHyDhJfClient);            //允许会员在客户端积分兑换     FHyDhJfClient
       $aTempstr=D('WIni')->getLastSql();
       $sendstr.=$aTempstr.';';  

       $res=D('WIni')->postOneRecord('FHyAutoLevel',$FHyAutoLevel);    //按积分提升会员级别           FHyAutoLevel 
       $aTempstr=D('WIni')->getLastSql();
       $sendstr.=$aTempstr.';';  

       $res=D('WIni')->postOneRecord('m_Jifen_Jiangli',$m_Jifen_Jiangli);     // 加钱一元送一积分   消费一个小时送一积分   m_Jifen_Jiangli   （0 加钱一元送一积分）
       $aTempstr=D('WIni')->getLastSql();
       $sendstr.=$aTempstr.';';  

       $res =PostTopUpdateDataToWb_lzm('Php_To_Top_Sql',$sendstr);


      if(!empty($res))
      {
        writelog($wbid.'更新WInitable  命令已发送成功','common');
      }
      else
      {
        writelog($wbid.'更新 WInitable  命令已发送失败','common');
      } 

      $data['result']=1;
	
      $this->ajaxReturn($data);  
	
    }



    public function fujia()
    { 

      $wbid=session('wbid');  
      $wbinfo= D('WbInfo')->getOneWbInfoByid($wbid);
      $this->assign('wbinfo',$wbinfo);
	  
	    
	 $barcode=C('BARCODE_URL');
	 $wxcode_url=C('WXPAY_URL').'wbid='.$wbid.'&yuming_id='.C('YUMING_ID');
	 $zfbcode_url=C('ZFBPAY_URL').'wbid='.$wbid.'&yuming_id='.C('YUMING_ID');
	  
	 
     $this->assign('barcode',$barcode);
	 $this->assign('wxcode_url',$wxcode_url);
	 $this->assign('zfbcode_url',$zfbcode_url);
	  
	   
	  $this->assign('wxcode_url',$wxcode_url);
	  $this->assign('zfbcode_url',$zfbcode_url);
	  	  
	  $this->assign('barname',$wbinfo['WbName']);
	  
	   
	  $syidlist= D('SpCtrlIp')->where(array('Wb_id'=>session('wbid')))->select();
	  foreach($syidlist as &$val)
	  {
		if(empty($val['syname']))
        {
			$val['syname']=$val['Syid'].'___'.$val['Ip'];
		}
		else
        {
			$val['syname']=$val['syname'].'___'.$val['Ip'];
		} 			
	  }
	  
	  
	  //是否允许客户端在线充值
	  $FAllowClientAddMoney= D('WIni')->where(array('WB_ID'=>$wbid,'Name'=>'FAllowClientAddMoney'))->getField('NValue'); 
	  $FAllowClientAddMoney=strtoupper($FAllowClientAddMoney);
	  if(empty($FAllowClientAddMoney) || $FAllowClientAddMoney=='TRUE')
	  {
		$FAllowClientAddMoney_val=1;  
	  }
	  else
      {
		$FAllowClientAddMoney_val=0;   
	  } 		  
	  
	  $this->assign('FAllowClientAddMoney',$FAllowClientAddMoney_val); 	
	  $bt_sp_buy = D('Webini')->where(array('wbid'=>$wbid,'skey'=>'bt_sp_buy'))->getField('svalue');     
	  $khd_sp_buy = D('Webini')->where(array('wbid'=>$wbid,'skey'=>'khd_sp_buy'))->getField('svalue');		
	  $khd_wxzfb_buy= D('Webini')->where(array('wbid'=>$wbid,'skey'=>'khd_wxzfb_buy'))->getField('svalue');		
	  $bt_chongzhi_qx = D('Webini')->where(array('wbid'=>$wbid,'skey'=>'bt_chongzhi_qx'))->getField('svalue');
	  $khd_shangji_qx = D('Webini')->where(array('wbid'=>$wbid,'skey'=>'khd_shangji_qx'))->getField('svalue');
	  
	    if($khd_shangji_qx==='1')
		{
		   $khd_shangji_qx=1;  
		}
		else
		{
			$khd_shangji_qx=0; 
		}
	  
		if($bt_chongzhi_qx==='1')
		{
		   $bt_chongzhi_qx=1;  
		}
		else if($bt_chongzhi_qx==='0')
		{
			$bt_chongzhi_qx=0; 
		}
		else
		{
			$bt_chongzhi_qx=1;  
		}	
				
		if($bt_sp_buy==='0')
		{
		   $bt_sp_buy=0;  
		}
		else
		{
			$bt_sp_buy=1; 
		}
		
		
		
	    if($khd_sp_buy==='0')
		{
		   $khd_sp_buy=0;  
		}
		else
		{
			$khd_sp_buy=1; 
		}	

        if($khd_wxzfb_buy==='0')
		{
		   $khd_wxzfb_buy=0;  
		}
		else
        {
		  $khd_wxzfb_buy=1; 	
		}	
		
		$syd_play_song= D('Webini')->where(array('wbid'=>$wbid,'skey'=>'syd_play_song'))->getField('svalue');
		if($syd_play_song==='0')
		{
		   $syd_play_song=0;  
		}
		else
        {
		  $syd_play_song=1; 	
		}	
		
		
		$FAllowXiaoshouPrint= D('Webini')->where(array('wbid'=>$wbid,'skey'=>'FAllowXiaoshouPrint'))->getField('svalue');
		if($FAllowXiaoshouPrint==='0')
		{
		   $FAllowXiaoshouPrint=0;  
		}
		else
        {
		  $FAllowXiaoshouPrint=1; 	
		}	

        
		
		$exe_sp_version= D('Webini')->where(array('wbid'=>$wbid,'skey'=>'exe_sp_version'))->getField('svalue');
		if($exe_sp_version==1)
		{
		   $exe_sp_version=1;  
		}
		else
        {
		  $exe_sp_version=0; 	
		}
		
		//是否显示提示导入数据到新超市
		
		$oldcount=D('Product')->where(array('wbid'=>$wbid))->count();
		if($oldcount >0)
		{
			$count=D('Newproduct')->where(array('wbid'=>$wbid))->count();
			if($count >0)
			{
				$show_tip_flag=0;
			}
			else
			{
				$show_tip_flag=1;
			}	 
		}else
        {
			$show_tip_flag=0;
		} 			


		
				$FAllowNewproductCk= D('Webini')->where(array('wbid'=>$wbid,'skey'=>'FAllowNewproductCk'))->getField('svalue');
		if($FAllowNewproductCk==1)
		{
		   $FAllowNewproductCk=1;  
		}
		else
        {
		  $FAllowNewproductCk=0; 	
		}
		$this->assign('khd_shangji_qx',$khd_shangji_qx);
	    $this->assign('show_tip_flag',$show_tip_flag);		
        $this->assign('bt_sp_buy',$bt_sp_buy);
        $this->assign('bt_chongzhi_qx',$bt_chongzhi_qx);		
        $this->assign('khd_sp_buy',$khd_sp_buy);
        $this->assign('khd_wxzfb_buy',$khd_wxzfb_buy);		
		$this->assign('syd_play_song',$syd_play_song);		
		$this->assign('exe_sp_version',$exe_sp_version);		
		$this->assign('FAllowXiaoshouPrint',$FAllowXiaoshouPrint);			   
 $this->assign('FAllowNewproductCk',$FAllowNewproductCk);
	    $this->assign('syidlist',$syidlist);  
	    $this->assign('wxcode_url',$wxcode_url);
	    $this->assign('zfbcode_url',$zfbcode_url);   
        $this->display();    
   
    }

    public function fujia_set()
    {    

      if(IS_AJAX)
      {      
        $wbid=session('wbid');          
        $gzh_isvalid = I('post.gzh_isvalid',"0",'string'); 
   
        if($gzh_isvalid=="0")
        {
         $data['isValid']=0;
        }
        else
        {
          $data['isValid']=1;
        } 
		D()->startTrans();
		$result=true;
		
        if(D('WbInfo')->SetGzhValid($wbid,$data)===false)
		{
		  $result=false;	
		}	
          
	   
       	$bt_sp_buy= I('post.bt_sp_buy',"",'string'); 
			
		if(empty($bt_sp_buy))
		{
		   $bt_sp_buy='0';  
		}
		else
		{
		  $bt_sp_buy='1';  
		}	
	    
        if(D('Webini')->postOneRecord('bt_sp_buy',$bt_sp_buy)===false)
		{
			$result=false; 
		}
		   		
		$khd_sp_buy= I('post.khd_sp_buy',"",'string'); 			
		if(empty($khd_sp_buy))
		{
		   $khd_sp_buy='0';  
		}
		else
		{
		  $khd_sp_buy='1';  
		}	
	    
        if(D('Webini')->postOneRecord('khd_sp_buy',$khd_sp_buy)===false)
		{
			$result=false; 
		}
		//==========================允许吧台支付================
		
		
		$bRecord=D('Webini')->where(array('wbid'=>session('wbid'),'skey'=>'bt_chongzhi_qx'))->find();
						
		$bt_chongzhi_qx= I('post.bt_chongzhi_qx',"",'string'); 
        if(empty($bRecord))
		{
			$bt_chongzhi_qx='1';  // 开启
		}
		else
        {
			if(empty($bt_chongzhi_qx) )
			{
			   $bt_chongzhi_qx='0';  // 开启
			}
			else
			{
			  $bt_chongzhi_qx='1';  //关闭
			} 
		}
							    
        if(D('Webini')->postOneRecord('bt_chongzhi_qx',$bt_chongzhi_qx)===false)
		{
			$result=false; 
		}
		
		$exe_sp_version= I('post.exe_sp_version',"",'string');	
        if(empty($exe_sp_version))
		{
		   $exe_sp_version=0;  
		}
		else
		{
		  $exe_sp_version=1;  
		}				
		if(D('Webini')->postOneRecord('exe_sp_version',$exe_sp_version)===false)
		{
			$result=false; 
		}
		
		
		
		
		
		   	        		
		$khd_wxzfb_buy= I('post.khd_wxzfb_buy',"",'string');	
        if(empty($khd_wxzfb_buy))
		{
		   $khd_wxzfb_buy=0;  
		}
		else
		{
		  $khd_wxzfb_buy=1;  
		}
			
		if(D('Webini')->postOneRecord('khd_wxzfb_buy',$khd_wxzfb_buy)===false)
		{
			$result=false; 
		}	
		
		
		$khd_shangji_qx= I('post.khd_shangji_qx',"",'string');	
        if(empty($khd_shangji_qx))
		{
		   $khd_shangji_qx=0;  
		}
		else
		{
		  $khd_shangji_qx=1;  
		}
			
		if(D('Webini')->postOneRecord('khd_shangji_qx',$khd_shangji_qx)===false)
		{
			$result=false; 
		}
		
		
		
		
        		
		$syd_play_song= I('post.syd_play_song',"",'string');	
        if(empty($syd_play_song))
		{
		   $syd_play_song=0;  
		}
		else
		{
		  $syd_play_song=1;  
		}			
		if(D('Webini')->postOneRecord('syd_play_song',$syd_play_song)===false)
		{
			$result=false; 
		}	     
	
		$FAllowXiaoshouPrint= I('post.FAllowXiaoshouPrint',"",'string');	
        if(empty($FAllowXiaoshouPrint))
		{
		   $FAllowXiaoshouPrint=0;  
		}
		else
		{
		  $FAllowXiaoshouPrint=1;  
		}	
		
		if(D('Webini')->postOneRecord('FAllowXiaoshouPrint',$FAllowXiaoshouPrint)===false)
		{
			$result=false; 
		}	     
        $tempsql= D('Webini')->getLastSql();		
	    $sendstr.=$tempsql.';';
		//允许新网吧超市仓库
		$FAllowNewproductCk= I('post.FAllowNewproductCk',"",'string');	
        if(empty($FAllowNewproductCk))
		{
		   $FAllowNewproductCk=0;  
		}
		else
		{
		  $FAllowNewproductCk=1;  
		}	
		
		if(D('Webini')->postOneRecord('FAllowNewproductCk',$FAllowNewproductCk)===false)
		{
			$result=false; 
		}	     
        $tempsql= D('Webini')->getLastSql();		
	    $sendstr.=$tempsql.';';
					
		$FAllowClientAddMoney= I('post.FAllowClientAddMoney',"",'string');
		
        if(empty($FAllowClientAddMoney))
		{
		   $FAllowClientAddMoney='false';  
		}
		else
		{
		  $FAllowClientAddMoney='true';  
		}
				
		if(D('WIni')->postOneRecord('FAllowClientAddMoney',$FAllowClientAddMoney)===false)
		{
			$result=false; 
		}
		
		
		
						        								
		if($result)
		{
			D()->commit();
		}
		else
        {
			D()->rollback();
		}			
        /*
        $result =PostTopUpdateDataToWb_lzm('Php_To_Top_Sql',$sendstr);
        if(!empty($result))
        {
          writelog($wbid.'更新WB_Info  命令已发送成功');
        }
        else
        {
          writelog($wbid.'更新WB_Info  命令已发送失败');
        } 
        */
        $response['data']='1';			 
        $this->ajaxReturn($response);
      }    
 
    }


    public function changejfj()
    { 	 
      $wbid=session('wbid');  
      $wbinfo= D('WbInfo')->getOneWbInfoByid($wbid);
      $str=$wbinfo['WBTel'];     
      $wbinfo['WBTel']=substr($str,0,3).'*****'.substr($str,7,strlen($str));
      $this->assign('wbinfo',$wbinfo);	
      $this->display();       
    }

    public function changejfj_yzm()
    { 
      $yzm=I('post.yzm','','string');
      $phone_verifycode=session('phone_verifycode');     
      if($yzm==$phone_verifycode)
      {
        echo 'true';
      }
      else
      { 
        echo 'false';
      }   
  
    }


    public function getPhoneVerifycode() 
    {		
      $verifycode=(mt_rand(100000,999999));      
      session('phone_verifycode',$verifycode);    
      $phonenum= D('WbInfo')->where(array('WBID'=>session('wbid')))->getField('WBTel');
      if(!empty($phonenum))
      {              
        SendToTelOfAccNo($phonenum,$verifycode);
      }
      else
      {
        writelog('手机短信验证码不发送----'.$verifycode,'changejfj_yzm');
      }       
      $data['status']='0';
      echo json_encode($data);

    }



    

    public function changejfj_set()
    { 	
      $session_verify=session('phone_verifycode');
      $verifycode=I('post.yzm','','string'); 
      if($session_verify==$verifycode)
      {
        D('WbInfo')->where(array('WBID'=>session('wbid')))->setField('Mac','');

        $sendstr=D('WbInfo')->getLastSql();

        $result =PostTopUpdateDataToWb_lzm('Php_To_Top_Sql',$sendstr);
        if(!empty($result))
        {
          writelog($wbid.'更新计费机  changejfj_set命令已发送成功','commonlog');
        }
        else
        {
          writelog($wbid.'更新计费机changejfj_set  命令已发送失败','commonlog');
        } 
        $data['result']='1';
      }
      else
      {
        $data['result']='-1';
      }
	
      $this->ajaxReturn($data);
	 
    }

     public function add_yg()
    {

      $map['WB_ID']=session('wbid');	
      $yggrouplist=D('Role')->getGroupUserListByMap($map);	
      $this->assign('yggrouplist',$yggrouplist);
      $this->display();	

    }

    public function add_yg_set()
    {   
       $wbid=session('wbid');
       if(IS_AJAX)
       {
         $yg_account=I('post.txtUser','','string');       
         $yg_pwd=I('post.txtPwd','','string');
         $yggroup_id=I('post.yggroup_id','','string');       
         $yg_insert_data['WB_ID']=$wbid;          
         $yg_insert_data['name']=$yg_account;
         $yg_insert_data['pw']=md5($yg_pwd.'!@#BGS159357');
         $yg_insert_data['qx']=D('Role')->where(array('WB_ID'=>$wbid,'role_id'=>$yggroup_id))->getField('groupqx');
         $yg_insert_data['boss_qx']=D('Role')->where(array('WB_ID'=>$wbid,'role_id'=>$yggroup_id))->getField('boss_qx');      
         $yg_insert_data['role_id']=$yggroup_id;
         $yg_insert_data['Guid']=getGuid();
		 $yg_insert_data['insertTime']=date('Y-m-d H:i:s',time());
         if(D('Yuangong')->where(array('WB_ID'=>$wbid,'name'=>$yg_account))->find())
         {
            $data['result']=0;
            $data['message']='添加失败,该员工已存在';
            $this->ajaxReturn($data);
         }
         else
         {

            $yg_insert_result=D('Yuangong')->add($yg_insert_data);
            if(!empty($yg_insert_result))
            {
               $sendstr=D('Yuangong')->getLastSql();
              $result =PostTopUpdateDataToWb_lzm('Php_To_Top_Sql',$sendstr);
              if(!empty($result))
              {
                writelog($wbid.'添加add_yg_set  命令已发送成功','commonlog');
              }
              else
              {
                writelog($wbid.'更新add_yg_set  命令已发送失败','commonlog');
              } 

              $data['result']=1;
              $data['message']='添加成功';
            }
            else
            {
              $data['result']=0;
              $data['message']='添加失败';
            }	

            $this->ajaxReturn($data);
         }
       }	
	  
    }

    public function edit_yg()
    {
		
      $id=I('get.id','','string');	
      $map['WB_ID']=session('wbid');	
      $yggrouplist=D('Role')->getGroupUserListByMap($map);	
      $this->assign('yggrouplist',$yggrouplist);
      $oneyginfo=D('Yuangong')->where(array('id'=>$id))->find();
      $this->assign('oneyginfo',$oneyginfo);
      $this->assign('yggrouplist',$yggrouplist);
      $this->display();	
	   
    }
    public function edit_yg_set()
    {  
        if(IS_AJAX)
        {   
        	
          $ygid=I('post.bianhao','','string');
          $yggroup_id=I('post.yggroup_id','','string'); 
          $new_yg_pw=I('post.pw','','string'); 
          $yg_guid= D('Yuangong')->where(array('id'=>$ygid,'WB_ID'=>session('wbid')))->getField('Guid');
          $old_yg_pw= D('Yuangong')->where(array('id'=>$ygid,'WB_ID'=>session('wbid')))->getField('pw');
          if($old_yg_pw==$new_yg_pw)
          {
            
          }
          else
          {
             $yg_update_data['pw']=md5($new_yg_pw.'!@#BGS159357');
          }  
                   
	        $yg_update_data['qx']=D('Role')->where(array('role_id'=>$yggroup_id,'WB_ID'=>session('wbid')))->getField('groupqx');
	        $yg_update_data['boss_qx']=D('Role')->where(array('role_id'=>$yggroup_id,'WB_ID'=>session('wbid')))->getField('boss_qx');       
	        $yg_update_data['role_id']=$yggroup_id;

          $yg_update_result=D('Yuangong')->updateOneYuangongByGuid($yg_guid,$yg_update_data);

          if(!empty($yg_update_result))
          {
            $sendstr=D('Yuangong')->getLastSql();
            $result =PostTopUpdateDataToWb_lzm('Php_To_Top_Sql',$sendstr);
            if(!empty($result))
            {
              writelog($wbid.'更新edit_yg_set  命令已发送成功','commonlog');
            }
            else
            {
              writelog($wbid.'更新edit_yg_set  命令已发送失败','commonlog');
            } 
            $data['result']=1;
            $data['message']='修改成功';
          }
          else
          {
            $data['result']=0;
            $data['message']='修改失败';
          }	

	       $this->ajaxReturn($data);         	
        }
		
    }

    public function yuangong()
    {		 
        $map['WB_ID']=session('wbid');	  
	  //如果不存在名字为员工组或者老板组的 就添加这两个分组	  
	    $wbid=session('wbid');  
	    D()->startTrans();  //启用事务
		$result=true;
	  	$moban_role_data=D('RoleMb')->where(array('role_name'=>'老板组'))->select(); 
        foreach($moban_role_data as &$val)
        {
		  $bFind=D('Role')->where(array('WB_ID'=>session('wbid'),'role_name'=>$val['role_name']))->find();	
		  if(empty($bFind))	
		  {
		    $role_insert_data=array();
		    $new_role_id=D('Role')->max('role_id')+1;
		    $role_insert_data['role_id']= $new_role_id; 
		    $role_insert_data['WB_ID']=$wbid;          
		    $role_insert_data['role_name']=$val['role_name'];
		    $role_insert_data['groupqx']=addyanma($val['groupqx']);
		    $role_insert_data['boss_qx']=$val['bossqx'];
		    $role_insert_data['role_perm']=$val['role_perm'];
		    $role_insert_data['dtInsertTime']=date('Y-m-d H:i:s'); 

		    if(D('Role')->add($role_insert_data) === false)
		    {         
			  $result=false;			  
		    } 
            $aTempstr=D('Role')->getLastSql();
            $sendstr=$aTempstr.';';  			  
		  }	  
        } 
		
		if($result)
        {  
          D()->commit(); 

          $res =PostTopUpdateDataToWb_lzmByWbid($wbid,'Php_To_Top_Sql',$sendstr);
          if(!empty($res))
          {

            
          }
          else
          {
       
          }          
        }
        else
        {
          D()->rollback(); 
        } 	    
      $yggrouplist=D('Role')->getGroupUserListByMap($map);	
      $this->assign('yggrouplist',$yggrouplist);
      $yglist=D('Yuangong')->getYgListByMap($map);	
      $this->assign('yglist',$yglist);
      $this->display();
	  
    }

    public function add_yg_group()
    {  
	  	   
	   $wbid=session('wbid');
	   if(!empty($wbid))
	   {
		  $map=array();
          $map['WB_ID']=$wbid;
          $map['Name']=array('not in','临时卡,普通会员');  		  
		  $hylxlist=D('Hylx')->where($map)->select();
	      $this->assign('hylxlist',$hylxlist);  
	   }	   
  
	  $this->display();
    }

    public function add_yg_group_set()
    {    
        $wbid=session('wbid');

        if(IS_AJAX)
        {   
            $newroleid=D('Role')->max('role_id')+1;    
          	$groupqx=I('post.groupqx','','string');  
            $webqx=I('post.webqx','','string');  
            $groupname=I('post.groupname','','string');
			
			$bh_qx=I('post.bh_qx','0','string');
			$th_qx=I('post.th_qx','0','string');
		

            $groupqx=addyanma($groupqx);  //添加掩码
			
            //分割 groupqx
 
             $groupuser_insert_data['role_id']=$newroleid; 
             $groupuser_insert_data['WB_ID']=$wbid;          
             $groupuser_insert_data['role_name']=$groupname;
             $groupuser_insert_data['groupqx']=$groupqx;
             $groupuser_insert_data['role_perm']=$webqx;
			 
			 $groupuser_insert_data['bh_qx']=$bh_qx;
			 $groupuser_insert_data['th_qx']=$th_qx;

             if(D('Role')->where(array('WB_ID'=>$wbid,'role_name'=>$groupname))->find())
             {
                $data['result']=0;
                $data['message']='添加失败,该分组已存在';             
             }
             else
             {
               $groupuser_insert_result=D('Role')->add($groupuser_insert_data);

               if(!empty($groupuser_insert_result))
	             {
                  $sendstr=D('Role')->getLastSql();
                  $result =PostTopUpdateDataToWb_lzm('Php_To_Top_Sql',$sendstr);
                  if(!empty($result))
                  {
                    writelog($wbid.'更新add_yg_group_set  命令已发送成功','commonlog');
                  }
                  else
                  {
                    writelog($wbid.'更新add_yg_group_set  命令已发送失败','commonlog');
                  } 
	                $data['result']=1;
	                $data['message']='添加成功';
	             }
	             else
	             {
	               $data['result']=0;
	               $data['message']='添加失败';
	             }	
             }	
			
            $this->ajaxReturn($data);
        }
		
    }

    public function delete_yggroup()
    {
		  
      if(IS_AJAX)
      {
          $result=true;
          $yggroupid=I('post.yggroupid','','string');  

          D()->startTrans();     
          if(D('Role')->where(array('WB_ID'=>session('wbid'),'role_id'=>$yggroupid))->delete()===false)
          {
             $result=false;
          }  

          $aTempsql1=D('Role')->getLastSql();
          


          $map['WB_ID']=session('wbid');
          $map['role_id']=$yggroupid;

          $ygdata['qx']='';
          $ygdata['boss_qx']='';
          $ygdata['role_id']='';

          if(D('Yuangong')->updateOneYuangongByRoleid($map,$ygdata)===false)
          {
            $result=false;
          }
           $aTempsql2=D('Yuangong')->getLastSql();



          if($result)
          {
            D()->commit();  
            $sendstr=$aTempsql1.';'.$aTempsql2;
            $res =PostTopUpdateDataToWb_lzm('Php_To_Top_Sql',$sendstr);
            if(!empty($res))
            {
               writelog($wbid.'删除员工 分组delete_yggroup  命令已发送成功','commonlog');
            }
            else
            {
               writelog($wbid.'删除员工 分组delete_yggroup  命令已发送失败','commonlog');
            }  
            $data['result']=1;
          }
          else
          {
            $data['result']=0;
            D()->rollback();    
          }
          $this->ajaxReturn($data);       
      }
	  
    }


    public function yggroup_edit()
    {
      $id=I('get.id','','string');		  
	  $wbid=session('wbid');
	  if(!empty($wbid))
	  {
	    $map=array();
        $map['WB_ID']=$wbid;
        $map['Name']=array('not in','临时卡,普通会员');  		  
		$hylxlist=D('Hylx')->where($map)->select();
	    $this->assign('hylxlist',$hylxlist);  
	  }
	  
	  $one_groupinfo=D('Role')->where(array('role_id'=>$id,'WB_ID'=>session('wbid')))->find();
	  
      $checkboxlist=$one_groupinfo['groupqx'];   
      $checkboxlist=deleteyanma($checkboxlist);    

      $weblist=$one_groupinfo['role_perm'];
      $groupname=$one_groupinfo['role_name'];
	  
	  $bh_qx=$one_groupinfo['bh_qx'];
	  $th_qx=$one_groupinfo['th_qx'];
	  $xj_qx=$one_groupinfo['xj_qx'];
	  
	  if($bh_qx==1)
	  {
		  $bh_qx_str=',999,';
	  } 	  
	  if($th_qx==1)
	  {
		  $th_qx_str=',998,';
	  } 
	  
	  if($xj_qx==1)
	  {
		  $xj_qx_str=',997,';
	  } 
	  
      $this->assign('bianhao',$id);	
      $this->assign('checkboxlist',$checkboxlist.$weblist.$bh_qx_str.$th_qx_str.$xj_qx_str);	
      $this->assign('groupname',$groupname);
	  
	  $this->assign('bh_qx',$bh_qx);
	  $this->assign('th_qx',$th_qx);
	  $this->assign('xj_qx',$xj_qx);
      $this->display();

    }
    
    public function edit_yggroup_set()
    {

        if(IS_AJAX)
        {   
        	
            $yggroupid=I('post.id','0','int');
            $wbid=session('wbid');  
            $groupqx=I('post.groupqx','','string');  
            $webqx=I('post.webqx','','string');  
            $groupname=I('post.groupname','','string'); 
			
			$bh_qx=I('post.bh_qx','0','string');
			$th_qx=I('post.th_qx','0','string');
			$xj_qx=I('post.xj_qx','0','string');

            $groupqx=addyanma($groupqx);  

            $groupuser_update_data['role_name']=$groupname;
            $groupuser_update_data['groupqx']=$groupqx;
            $groupuser_update_data['role_perm']  =$webqx;            
			$groupuser_update_data['bh_qx']  =$bh_qx;
			$groupuser_update_data['th_qx']  =$th_qx;
			$groupuser_update_data['xj_qx']  =$xj_qx;

             //更新员工
             //找到该分组的员工             
            D()->startTrans();  //启用事务
            $result=true;               
            if((D('Role')->where(array('role_id'=>$yggroupid,'WB_ID'=>session('wbid')))->data($groupuser_update_data)->save())===false)
            {
                $result=false;               
            } 

            $sendstr=D('Role')->getLastSql(); 
                     
            $yuangong_list=D('Yuangong')->where(array('role_id'=>$yggroupid,'WB_ID'=>session('wbid')))->select();
            foreach( $yuangong_list as &$val)
            {
              if((D('Yuangong')->where(array('Guid'=>$val['Guid'],'WB_ID'=>$wbid,'role_id'=>$yggroupid))->setField('qx',$groupqx))===false)
              {
                $result=false;
                
              }
              $aTempsql2=D('Yuangong')->getLastSql();
              $sendstr.=$aTempsql2.';';
              
            } 
 
            if($result)
	           {              
	             $data['result']=1;
	             $data['message']='修改成功';
               D()->commit();  
     
                $res =PostTopUpdateDataToWb_lzm('Php_To_Top_Sql',$sendstr);
                if(!empty($res))
                {
                  writelog($wbid.'更新edit_yggroup_set  命令已发送成功','commonlog');
                }
                else
                {
                  writelog($wbid.'更新edit_yggroup_set  命令已发送失败','commonlog');
                } 
	           }
	           else
	           {
	              $data['result']=0;
	              $data['message']='修改失败';
                D()->rollback();      
	           }	
            
	           $this->ajaxReturn($data);
             	
        }
		
    }



    public function delete_yg()
    {
		 
       if(IS_AJAX)
       {
          $ygid=I('post.ygid','','string');     
          $yg_guid= D('Yuangong')->where(array('id'=>$ygid,'WB_ID'=>session('wbid')))->getField('Guid');

          $yg_delete_result=D('Yuangong')->where(array('WB_ID'=>session('wbid'),'Guid'=>$yg_guid))->delete();
            if(!empty($yg_delete_result))
             {  
               $sendstr=D('Yuangong')->getLastSql();
                $result =PostTopUpdateDataToWb_lzm('Php_To_Top_Sql',$sendstr);
                if(!empty($result))
                {
                  writelog($wbid.'删除WUserTable  命令已发送成功','commonlog');
                }
                else
                {
                  writelog($wbid.'删除WUserTable  命令已发送失败','commonlog');
                } 
                $data['result']=1;
             }
             else
             {
               $data['result']=0;
             }	
              
             $this->ajaxReturn($data);
         
       }
	   
    }  
	
	public function gettuoguaninfo()
    { 
      if(IS_AJAX)
      {
        $agent_name    = I('post.agent_name','','string');			        
        $map = array(); 
		if(!empty($agent_name))
        {
            $map['agent_name']=$agent_name;
			$agentinfo = D('Agent')->where($map)->find();	
            if(!empty($agentinfo))
			{
				$data['result']=1;
				$data['body']=$agentinfo;
			}else
            {
				$data['result']=-1;
			}				
				
        }
		else
        {
				$data['result']=-1;
	    } 
        $this->ajaxReturn($data);
      }   
  
    }
	
		
	public function query_bangding_set()
	{			
	   if(IS_AJAX)
       {
          $wbid=I('post.wbid','','string');    
          $agent_id=I('post.agent_id','','string');        		  
		  $bangding_insert_data['wbid']=$wbid;
		  $bangding_insert_data['agent_id']=$agent_id;
		  $bangding_insert_data['dtInsertTime']=date('Y-m-d H:i:s ',time());
		  $bangding_insert_data['bing_status']=2;				       
          $agent_bangding_result=D('Bangding')->add($bangding_insert_data);
		  
           if(!empty($agent_bangding_result))
           {               
                $data['status']=1;
           }
           else
           {
               $data['status']=0;
           }	
           $this->ajaxReturn($data);      
       }
	  
	}
	

	
	public function tuoguan()
	{
				//向总服务器请求数据		
		$wbid=session('wbid');
		$post_data['wbid']=$wbid;	
        $daili_url_zong =C('DAILI_URL_ZONG');
		
		$url= $daili_url_zong.'/index.php/ServerzongAPI/API_query_onebarbanginfo.html';
		$res= sendRequsttoOneServer($url, $post_data,30);
		
		$res= substr($res, 3);				
		$res2=json_decode($res,true);	
		$agentinfo=$res2['body'];						
		$this->assign('wbid',$wbid);		
		$this->assign('agentinfo',$agentinfo);			
		$this->assign('daili_url_zong',$daili_url_zong);
		$this->display(); 															
	}
  	
}

