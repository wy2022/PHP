<?php
namespace Home\Model;
use Think\Model;
class DistrictModel extends Model{
    protected $tableName = 'WGroupTable';

    public function getDistrictList($map = array())
    {
        return $this->where($map)->order('id asc')->select();
    }

    public function getDistrict($map = array()){
        return $this->where($map)->find();
    }

    public function getDistrictById($id){
        return $this->getDistrict(array('id'=>$id));
    }

    public function getDistrictByGuid($guid){
        return $this->getDistrict(array('Guid'=>$guid,'WB_ID'=>session('wbid')));
    }

    public function getDistrictGuidById($id)
    {
        return $this->where(array('id'=>$id,'WB_ID'=>session('wbid')))->getField('Guid');
    }
		
    public function updateDistrictByGuid($map,$data)
    {
        return $this->where($map)->data($data)->save();
    }

    public function addDistrict($data)
    {
        return $this->data($data)->add();
    }

    // public function deleteDistrict($id)
    // {
    //     return $this->where(array('id'=>$id))->delete();
    // }

    public function updateOneDistrictByHylx($GroupGuid,$name)
    {
        $map=array();
        $wbid=session('wbid');      
        $vipCardlist= D('VipLevel')->where(array('WB_ID'=>session('wbid')))->select();
    
       for($i=0;$i<count($vipCardlist);$i++)
        {                   
            $k=0;                                            
            for($j=0;$j<7;$j++)
            {
                for($m=0;$m<24;$m++)
                {                   
                   $list[$j][$m]    =4;
                   $k++;                    
                }           
            }   
			
            $listarray[$i]['m_StarPrice']='2.00';
            $listarray[$i]['m_EffectiveTime']=1;
            $listarray[$i]['m_IgnoreTime']=1;
            $listarray[$i]['m_SmallPrice']='2.00';
			
			
								
			$listarray[$i]['SmallIntegral']= $vipCardlist[$i]['SmallIntegral']*1;
			$listarray[$i]['SjDiscount']   = $vipCardlist[$i]['SjDiscount']*1;
			$listarray[$i]['SpDiscount']   = $vipCardlist[$i]['SpDiscount']*1;		
            $listarray[$i]['guid']=$vipCardlist[$i]['Guid'];  
            $listarray[$i]['Name']=$vipCardlist[$i]['Name'];  
            $listarray[$i]['fl']= $list;                    
        }
                
        $res=D('District')->where(array('WB_ID'=>session('wbid'),'Guid'=>$GroupGuid))->setField('FlList',json_encode($listarray));          
        $HyCardGuids='';
        foreach($vipCardlist as &$val)
        {
          $HyCardGuids=$HyCardGuids.$val['Guid'].',';
        }
        
        $res1=D('District')->where(array('WB_ID'=>session('wbid'),'Guid'=>$GroupGuid))->setField('HyCardGuids',$HyCardGuids);   
        if(!empty($res) && !empty($res1))
        {
           return true;
        }
        else
        {
           return false;
        }                   
    }
    

        public function updateRateByGuid($aDistrictGuid,$vipLevelGuid,$time,$rate)
        {

      
            
            $flList=D('District')->where(array('WB_ID'=>session('wbid'),'Guid'=>$aDistrictGuid))->getField('FlList'); 

            $listarray=json_decode($flList,true);//解json
                    
            for($i=0;$i<count($listarray);$i++) //有几个会员等级
            {  
                if($listarray[$i]['guid']==$vipLevelGuid)   //找到要更新的会员类型
                {
                     $k=0;
                     $sendlist=array();          // 拆分后的费率168
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
                           $list[$j][$m]    = $sendlist[$k];
                           $k++;                    
                         }          
                     }  
                     $listarray[$i]['fl']= $list;
                }           
            }
            
                  
                  
            $res=D('District')->where(array('WB_ID'=>session('wbid'),'Guid'=>$aDistrictGuid))->setField('FlList',json_encode($listarray));

            if(!empty($res))
            {
              return true;
            }
            else
            {
                return false;
            }         
        } 



          public function getRate($vipLevelId,$districtId)
          {
            $vipLevelGuid = D('VipLevel')->getVipLevelGuidById($vipLevelId);
            
            $flList=D('District')->where(array('id'=>$districtId,'WB_ID'=>session('wbid')))->getField('FlList');
            
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
        
              // $list=array('rate'=>$sendlist,'pricelist'=>$pricelist1,'startprice'=>$startprice,'minprice'=>$minprice,'ignoreminute'=>$ignoreminute,'enable_hour'=>$enable_hour); 
			  
			  
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
				  
				  
				 
			  
			  
			  
                return $list;   
        }


        public function updateRateConfigByGuid($aDistrictGuid,$vipLevelId,$enableHour,$ignoreMinute,$minPrice,$startPrice)
        {
            // $vipLevelGuid = D('VipLevel')->getVipLevelGuidById($vipLevelId);

            $vipLevelGuid = D('VipLevel')->where(array('WB_ID'=>session('wbid'),'id'=>$vipLevelId))->getField('Guid');

            // $districtGuid = D('District')->getDistrictGuidById($districtId);
            // var_dump($districtGuid);
            //0-整点有效；1-半点有效
            switch($enableHour)
            {
                case 'half':
                    $enableHour = 1;
                    break;
                case 'integral':
                    $enableHour = 0;
                    break;
                default:
                    $enableHour = 1;
            }
        
            $flList=D('District')->where(array('WB_ID'=>session('wbid'),'Guid'=>$aDistrictGuid))->getField('FlList'); 
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
            $res=D('District')->where(array('WB_ID'=>session('wbid'),'Guid'=>$aDistrictGuid))->setField('FlList',json_encode($listarray));       
            if(!empty($res))
            {
              return true;
            }
            else
            {
                return false;
            }   
            
        }


        public function updateAllDistrictByAddOneVipLevel($vipGuid,$hylxname)   //新增一个会员等级，就向每个分组里添加一个
        {
            $wbid=session('wbid');
            $map=array();
            $map['WB_ID']=session('wbid');        
                    
            $vipCard_count=D('VipLevel')->where($map)->count();
			
			
            $District= D('District')->where($map)->select();// 一元区  三元区

            $s='';
            $HyCardGuids='';

            $vipCardlist=D('VipLevel')->where($map)->select();
            foreach($vipCardlist as &$val)
            {
              $HyCardGuids=$HyCardGuids.$val['Guid'].',';
            }
                
            $result=true;

            for($i=0;$i<count($District);$i++)
            {   
		       
				
                $old_listarray=array();	
                $old_listarray=json_decode($District[$i]['FlList'],true);//解json
        
                $old_listarray[$vipCard_count-1]['m_StarPrice'] = '2.00';
                $old_listarray[$vipCard_count-1]['m_EffectiveTime']=1;
                $old_listarray[$vipCard_count-1]['m_IgnoreTime']=1;
                $old_listarray[$vipCard_count-1]['m_SmallPrice']='2.00';
                $old_listarray[$vipCard_count-1]['guid']= $vipGuid;
                $old_listarray[$vipCard_count-1]['Name']=$hylxname;
				
							
				$onevipLevelinfo = D('VipLevel')->where(array('WB_ID'=>session('wbid'),'Guid'=>$vipGuid))->find();								
				$SmallIntegral= $onevipLevelinfo['SmallIntegral'];
				$SjDiscount   = $onevipLevelinfo['SjDiscount'];
				$SpDiscount   = $onevipLevelinfo['SpDiscount'];
							
				$old_listarray[$vipCard_count-1]['m_SmallIntegral']=$SmallIntegral*1;
				$old_listarray[$vipCard_count-1]['m_SjDiscount']=$SjDiscount*1;
				$old_listarray[$vipCard_count-1]['m_SpDiscount']=$SpDiscount*1;
				
				
				
				
         
				
				
                for($j=0;$j<7;$j++)
                {
                    for($m=0;$m<24;$m++)
                    {                  
                       $flmoneylist[$j][$m] =4;                 
                    }          
                }  
                $old_listarray[$vipCard_count-1]['fl']= $flmoneylist;
                
                
                $k=0;
                $list=array();
                for($n=0;$n<$vipCard_count;$n++)
                {
                    $list[$k] =$old_listarray[$n];
                    $k++;
                }   
				

                
           
                if(D('District')->where(array('WB_ID'=>session('wbid'),'Guid'=>$District[$i]['Guid']))->setField('HyCardGuids',$HyCardGuids)===false)
                {           
                    $result=false;
                } 
         
                $aTempsql= D('District')->getLastSql();
                $s.= $aTempsql.';'; 
                
               
                if(D('District')->where(array('WB_ID'=>session('wbid'),'Guid'=>$District[$i]['Guid']))->setField('FlList',json_encode($list))===false)
                {
                   
                    $result=false;
                }   
                $aTempsql= D('District')->getLastSql();
                $s.= $aTempsql.';'; 
               
            }   

            if($result==false)
            {
               return false;
            }
            else
            {
               return $s;
            }         
        }

    public function updateAllDistrictByEditOneVipLevel($oneViplevelGuid,$name)
    {

        $wbid=session('wbid');
        $districtlist = $this->where(array('WB_ID'=>$wbid))->select();
        
        $aTempsql=''; 
        $result = true;
        foreach($districtlist  as &$val)
        {

           $aflList=$val['FlList']; 
           $listarray=json_decode($aflList,true);//解json
            for($i=0;$i<count($listarray);$i++) //有几个会员等级
            {  
                if($listarray[$i]['guid']==$oneViplevelGuid)   //找到要更新的会员类型
                {
                     $k=0;
                     $sendlist=array();          // 拆分后的费率168
                     $tempfllist=array();       
                     $listarray[$i]['Name']=$name;                    
                }           
            }
            if(D('District')->where(array('WB_ID'=>session('wbid'),'Guid'=>$val['Guid']))->setField('FlList',json_encode($listarray))===false)
            {
               $result=false;
            }  

            $aTempsql= D('District')->getLastSql();
            $sendstr.=  $aTempsql.';';
        }    

        if($result)
        {
           return  $sendstr;
        }
        else
        {
           return  false; 
        }        
    }








        public function updateAllDistrictByDeleteOneVipLevel($CardGuid)
        {
            $map=array();
            $map['WB_ID']=session('wbid');

            $sendstr='';
            $aTempsql1 =''; 
            $aTempsql2='';
            $result=true;


            $vipCard_count=D('VipLevel')->where($map)->count();
            $District= D('District')->where($map)->select();// 一元区  三元区
            for($i=0;$i<count($District);$i++)
            {       
                $old_listarray=array();
                $old_listarray=json_decode($District[$i]['FlList'],true);//解json 一条记录
                                
                $new_listarray=array();
                $k=0;
                for($j=0;$j<count($old_listarray);$j++)
                {
                    if($old_listarray[$j]['guid'] != $CardGuid)
                    {
                        $new_listarray[$k]=$old_listarray[$j];
                        $k++;
                    }      
					
                }
					
                
                if(D('District')->where(array('WB_ID'=>session('wbid'),'Guid'=>$District[$i]['Guid']))->setField('FlList',json_encode($new_listarray))===false)
                {
                    $result=false;
                }   
                $aTempsql1 =D('District')->getLastSql();  
                $sendstr.= $aTempsql1.';'; 
                
                
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
                                        
              if(D('District')->where(array('WB_ID'=>session('wbid'),'Guid'=>$District[$i]['Guid']))->setField('HyCardGuids',$one_HyCardGuids)===false)
              {
                 $result=false;
              } 

                $aTempsql2 =D('District')->getLastSql();  
                $sendstr.= $aTempsql2.';'; 
            }   

            if($result)
            {
               return $sendstr;
            }
            else
            {
               return false;
            }
        }
		
		
		public function updateAllDistrictByEditOneVipLevel_Info($oneViplevelGuid,$name,$minpoints,$m_SjDiscount,$m_SpDiscount)
    {
        
        $wbid=session('wbid');
        $districtlist = $this->where(array('WB_ID'=>$wbid))->select();
        
        $aTempsql=''; 
        $result = true;
        foreach($districtlist  as &$val)
        {
           
           $aflList=$val['FlList']; 
           $listarray=json_decode($aflList,true);//解json
		   
		   		 	   
		    $j=0;
			$old_listarray=array();
			
            for($i=0;$i<count($listarray);$i++) //有几个会员等级
            {  
                if($listarray[$i]['guid']==$oneViplevelGuid)   //找到要更新的会员类型
                {                   
                    $old_listarray[$j]['m_StarPrice']    =$listarray[$i]['m_StarPrice'];
                    $old_listarray[$j]['m_EffectiveTime']=$listarray[$i]['m_EffectiveTime'];
					
					$old_listarray[$j]['m_SmallPrice']   =$listarray[$i]['m_SmallPrice'];
                    $old_listarray[$j]['m_IgnoreTime']   =$listarray[$i]['m_IgnoreTime'];
					$old_listarray[$j]['fl']             =$listarray[$i]['fl'];
					$old_listarray[$j]['guid']           =$listarray[$i]['guid'];
					$old_listarray[$j]['Name']           =$name; 								
					$old_listarray[$j]['m_SjDiscount']   =$m_SjDiscount;
					$old_listarray[$j]['m_SpDiscount']   =$m_SpDiscount;
					$old_listarray[$j]['m_SmallIntegral']=$minpoints;
													
                }
				else
                {
					$old_listarray[$j]['m_StarPrice']    =$listarray[$i]['m_StarPrice'];
                    $old_listarray[$j]['m_EffectiveTime']=$listarray[$i]['m_EffectiveTime'];
					$old_listarray[$j]['m_SmallIntegral']=$listarray[$i]['m_SmallIntegral'];
					$old_listarray[$j]['m_SmallPrice']   =$listarray[$i]['m_SmallPrice'];
                    $old_listarray[$j]['m_IgnoreTime']   =$listarray[$i]['m_IgnoreTime'];
					$old_listarray[$j]['fl']             =$listarray[$i]['fl'];
					$old_listarray[$j]['guid']           =$listarray[$i]['guid'];
					$old_listarray[$j]['Name']           =$listarray[$i]['Name'];								
					$old_listarray[$j]['m_SjDiscount']   =$listarray[$i]['m_SjDiscount'];
					$old_listarray[$j]['m_SpDiscount']   =$listarray[$i]['m_SpDiscount'];
				}	
               $j++;				
            }
			
			
			
            if(D('District')->where(array('WB_ID'=>session('wbid'),'Guid'=>$val['Guid']))->setField('FlList',json_encode($old_listarray))===false)
            {
               $result=false;
            }  

            $aTempsql= D('District')->getLastSql();
			
		
            $sendstr.=  $aTempsql.';';
        }    

        if($result)
        {
           return  $sendstr;
        }
        else
        {
           return  false; 
        }        
    }



        public function addOneDistrictByName($name)   //增加一个区域，将所有会员类型插入，并更新flist字段
        {
            $map=array();
            $wbid=session('wbid');      
            $vipCardlist= D('VipLevel')->where(array('WB_ID'=>session('wbid')))->select();
        
           for($i=0;$i<count($vipCardlist);$i++)
            {                   
                $k=0;                                            
                for($j=0;$j<7;$j++)
                {
                    for($m=0;$m<24;$m++)
                    {                   
                       $list[$j][$m]    =4;
                       $k++;                    
                    }           
                }   
                $listarray[$i]['m_StarPrice']='1.00';
                $listarray[$i]['m_EffectiveTime']=1;
                $listarray[$i]['m_IgnoreTime']=1;
                $listarray[$i]['m_SmallPrice']='1.00';
																								
				$listarray[$i]['m_SmallIntegral']= $vipCardlist[$i]['SmallIntegral']*1;
				$listarray[$i]['m_SjDiscount']   = $vipCardlist[$i]['SjDiscount']*1;
				$listarray[$i]['m_SpDiscount']   = $vipCardlist[$i]['SpDiscount']*1;								
				
                $listarray[$i]['guid']=$vipCardlist[$i]['Guid'];  
                $listarray[$i]['Name']=$vipCardlist[$i]['Name'];  
                $listarray[$i]['fl']= $list;                    
            }



            $HyCardGuids='';
            foreach($vipCardlist as &$val)
            {
              $HyCardGuids=$HyCardGuids.$val['Guid'].',';
            }


            $district_insert_data=array();
            $district_insert_data['WB_ID']=$wbid;
            $district_insert_data['GroupName']=$name;
            $district_insert_data['Guid']=getGuid();
            $district_insert_data['HyCardGuids']=$HyCardGuids;
            $district_insert_data['FlList']=json_encode($listarray);
            $district_insert_data['isBj']=0;
            $district_insert_data['BjFllist']='';

            
            $district_insert_result=D('District')->add($district_insert_data);

            if(!empty($district_insert_result))
            {
               return true;
            }
            else
            {
               return false;
            }                   
        }


        public function deleteDistrict($Guid)
       {
          return $this->where(array('WB_ID'=>session('wbid'),'Guid'=>$Guid))->delete();
       }




}