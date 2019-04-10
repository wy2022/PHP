<?php
  namespace Home\Model;
  use Think\Model;
  class HyJlModel extends Model 
  {
    protected $tableName = 'WHyLxTable_JLjh';

    public function getMoneyRecordsbyWbId($wbid) 
    {

      $count=$this->getMoneycount($wbid);
      if($count==0)
      {
        return null;
      }
      else
      {
        $list = $this->where(array('WB_ID'=>$wbid))->field(array('AddMoney','JLMoney'))->select(); 
        $resultdata =array('count'=>$count,'list'=>$list); 

        $test=array();
        $i=0;
        foreach ($resultdata['list'] as $key=>$value)
        {                             

          if(($value['AddMoney']==0)&&($value['JLMoney']==0))
          {
                       
          }
          else
          {
            $test['list'][$i]['AddMoney']  =$value['AddMoney'];
            $test['list'][$i]['JLMoney']   =$value['JLMoney'];
            $i=$i+1; 
          }
        }   
        
          if(count($test['list'])==0)
          {
            return null;
          }
          else
          {
           $chongzhidata=array();
           $chongzhidata['count']=count($test['list']);
           $chongzhidata['list']=$test['list'];
           
           return $chongzhidata;
          } 
      }          
    }  

    public function getMoneycount($wbid) //获取新闻
    {
      return $this->where(array('WB_ID'=>$wbid))->count();
    }   
	
	
	
	public function getMoneyRecordsbyWbIdAndHyCardNoAndJe($wbid,$hycardno,$je) 
    {

      $count=$this->where(array('WB_ID'=>$wbid))->count();
      if($count==0)
      {
        return null;
      }

      $onehyguid=D('HyInfo')->where(array('hyCardNo'=>$hycardno,'WB_ID'=>$wbid))->getField('hyCardGuid');
      
      //1.过滤会员guid
       $list1=$this->where(array('WB_ID'=>$wbid))->select(); 
       foreach($list1 as &$val) 
       {
          if(strpos($val['HyCardGuid'],$onehyguid)!== false)
          {
            $idlist.=$val['id'].',';
          }
       }

       $map=array();
       $map['WB_ID']=$wbid;
       $map['id']=array('IN',$idlist);
       $list = $this->where($map)->select();      
       //2.过滤生效时间
        $nowtime=date('Y-m-d H:i:s',time());
        $nowyue=((int)substr($nowtime,8,2));//取得几号
        $nowzhou= date("w");

        $idlist1='';
        foreach ($list as &$val) 
        {
            $nowtime1=$nowtime;
            $bFind=0;
            //判断会员日的这条记录是否符合
            if($val['LimitTimeLx']==1)
            {
                $begtime= strtotime(date('Y-m-d H:i:s',strtotime($val['LimitTimeBegin'])));
                $endtime= strtotime(date('Y-m-d H:i:s',strtotime($val['LimitTimeEnd'])));
                $nowtime1= strtotime( $nowtime1);

                if($nowtime1>=$begtime && $nowtime1<=$endtime)
                {
                   $bFind=1;
                }
                else
                {
                   $bFind=2; 
                }  
            }
            else if($val['LimitTimeLx']==0)
            {
               $bFind=1;
            }

            if($bFind==1)
            {
              $idlist1.=$val['id'].',';
            }  
        }  

        //3.过滤会员日,过滤周几，过滤出符合条件的会员日(无重复的)+符合条件的非会员日id
        
        $map2=array();
        $map2['WB_ID']=$wbid;
        $map2['id']=array('IN',$idlist1);
        $map2['LimitDayLx']=array('IN','1,2');
        
        $idlist2='';
        $list2 = $this->where($map2)->select(); 
        foreach ($list2 as &$val) 
        {
            $bFind=0;
            $onelimitdays=$val['LimitDays'];
            $oneaddmoney=$val['AddMoney'];
            if($val['LimitDayLx']==1)
            {
              $yuelist = explode(',', $val['LimitDays']);
              for($i=0;$i< count($yuelist)-1;$i++)
              {
                 if($yuelist[$i]==$nowyue)
                 {
                   $map6=array();
                   $map6['WB_ID']=$wbid;
                   $map6['id']=array('in',$idlist2);
                   $map6['AddMoney']= $oneaddmoney;
                   $bExist=$this->where($map6)->select();
                   if(empty($bExist))
                   {
                      $bFind=1;
                   } 

                 } 
              }       
            }
            else if($val['LimitDayLx']==2)
            {
              if(strpos($val['LimitDays'],$nowzhou)!== false)
              {
                   $map6=array();
                   $map6['WB_ID']=$wbid;
                   $map6['id']=array('in',$idlist2);
                   $map6['AddMoney']= $oneaddmoney;
                   $bExist=$this->where($map6)->select();
                   if(empty($bExist))
                   {
                      $bFind=1;
                   } 
              }
            }
             
            if($bFind==1)
            {
              $idlist2.=$val['id'].',';
            }   
        }    
         
        //4.获取不重复的 LimitDayLx=0的数据
        if(!empty($idlist2))
        {
          $map6=array();
          $map6['id']=array('in',$idlist2);
          $map6['WB_ID']=$wbid;
          $moneylist=$this->where($map6)->getField('AddMoney',true);
        }  


        $map3=array();
        $map3['WB_ID']=$wbid;
        $map3['id']=array('IN',$idlist1);
        $map3['LimitDayLx']=0;
        if(!empty($moneylist))
        {
          $map3['AddMoney']=array('not in',$moneylist);
        }  
        
        $list3 = $this->where($map3)->select(); 
        foreach($list3 as &$val)
        {
             $bFind=0;
             $oneaddmoney=$val['AddMoney'];

        
             if(!empty($idlist3))
             {
               $map6=array();
               $map6['WB_ID']=$wbid;
               $map6['id']=array('in',$idlist3); 
               $map6['AddMoney']= $oneaddmoney;
               $bExist=$this->where($map6)->select();
               if(empty($bExist))
               {
                  $bFind=1;
               } 
             }
             else
             {
                $bFind=1;
             } 
                           
            if($bFind==1)
            {
              $idlist3.=$val['id'].',';
            }  
        }  

        //可用的id 
        $ky_idlist=$idlist2.$idlist3;
        $map4=array();
        $map4['WB_ID']=$wbid;
        $map4['id']=array('IN',$ky_idlist);      
        $ky_listinfo=$this->where($map4)->select();
        if(!empty($ky_idlist))
        {
           $map5=array();
           $map5['AddMoney']=$je;
           $map5['id']=array('IN',$ky_idlist);
           //$info=$this->where($map5)->order('AddMoney desc')->limit(1)->find();
           $info=$this->where($map5)->find();

           // echo $this->getLastSql();
           //   echo json_encode($info);
           // return;
           if(!empty($info))
           {
                $data[0]['AddMoney']=$je;
                $data[0]['JLMoney']=$info['JLMoney'];
                $data[0]['Lx']=$info['Lx'];
                $data[0]['FqLx']=$info['FqLx'];
                $data[0]['FqJe']=$info['FqJe'];
                $data[0]['FqCount']=$info['FqCount'];
                $data[0]['Bljl']=$info['Bljl'];
           }
           else
           {
                $map5=array();
                $map5['id']=array('IN',$ky_idlist);
                $map5['AddMoney']=array('lt',$je);
                $info=$this->where($map5)->order('AddMoney desc')->limit(1)->find();

                if(!empty($info))
                 {
                     $beishu=floor($je/$info['AddMoney']);
                     $yushu= $je%$info['AddMoney'];  

                     if($info['Bljl']==1)
                     {
                        $data[0]['AddMoney']=$je;
                        $data[0]['JLMoney']=$info['JLMoney'] * $beishu;
                        $data[0]['Lx']=$info['Lx'];
                        $data[0]['FqLx']=$info['FqLx'];
                        $data[0]['FqJe']=$info['FqJe'];
                        $data[0]['FqCount']=$info['FqCount'];
                        $data[0]['Bljl']=1;
                     }
                     else
                     {
                        $data[0]['AddMoney']=$je;
                        $data[0]['JLMoney']=$info['JLMoney'];
                        $data[0]['Lx']=$info['Lx'];
                        $data[0]['FqLx']=$info['FqLx'];
                        $data[0]['FqJe']=$info['FqJe'];
                        $data[0]['FqCount']=$info['FqCount'];
                        $data[0]['Bljl']=0;
                     }
                 }
                 else
                 {
                      $data[0]['AddMoney']=$je;
                      $data[0]['JLMoney']=0;
                      $data[0]['Lx']=0;
                      $data[0]['FqLx']=0;
                      $data[0]['FqJe']=0;
                      $data[0]['FqCount']=0;
                      $data[0]['Bljl']=0;
                 } 
           } 

        }  

    
        return $data;          
    } 
	
	
	
	
	
	 public function getMoneyRecordsbyWbIdAndHyCardNo($wbid,$hycardno) 
    {

      $count=$this->where(array('WB_ID'=>$wbid))->count();
      if($count==0)
      {
        return null;
      }

      $onehyguid=D('HyInfo')->where(array('hyCardNo'=>$hycardno,'WB_ID'=>$wbid))->getField('hyCardGuid');


      //1.过滤会员guid
       if(!empty($onehyguid))
       {
          $list1=$this->where(array('WB_ID'=>$wbid))->select(); 
           foreach($list1 as &$val) 
           {
              if(strpos($val['HyCardGuid'],$onehyguid)!== false)
              {
                $idlist.=$val['id'].',';
              }
           }
       }
       else
       {
            return null;
       } 
       

       $map=array();
       $map['WB_ID']=$wbid;
       $map['id']=array('IN',$idlist);
       $list = $this->where($map)->select();  

       // echo json_encode($list);
       // return;
        
       $data[0]['AddMoney']=5;
       $data[1]['AddMoney']=10;
       $data[2]['AddMoney']=20;
       $data[3]['AddMoney']=50;
       $data[4]['AddMoney']=100;

       
       //2.过滤生效时间
        $nowtime=date('Y-m-d H:i:s',time());
        $nowyue=((int)substr($nowtime,8,2));//取得几号
        $nowzhou= date("w");

        $idlist1='';
        foreach ($list as &$val) 
        {
            $nowtime1=$nowtime;
            $bFind=0;
            //判断会员日的这条记录是否符合
            if($val['LimitTimeLx']==1)
            {
                $begtime= strtotime(date('Y-m-d H:i:s',strtotime($val['LimitTimeBegin'])));
                $endtime= strtotime(date('Y-m-d H:i:s',strtotime($val['LimitTimeEnd'])));
                $nowtime1= strtotime( $nowtime1);

                if($nowtime1>=$begtime && $nowtime1<=$endtime)
                {
                   $bFind=1;
                }
                else
                {
                   $bFind=2; 
                }  
            }
            else if($val['LimitTimeLx']==0)
            {
               $bFind=1;
            }

            if($bFind==1)
            {
              $idlist1.=$val['id'].',';
            }  
        } 

        //3.过滤会员日,过滤周几，过滤出符合条件的会员日(无重复的)+符合条件的非会员日id
        
        $map2=array();
        $map2['WB_ID']=$wbid;
        $map2['id']=array('IN',$idlist1);
        $map2['LimitDayLx']=array('IN','1,2');
        
        $idlist2='';
        $list2 = $this->where($map2)->select(); 

        foreach ($list2 as &$val) 
        {
            $bFind=0;
            $onelimitdays=$val['LimitDays'];
            $oneaddmoney=$val['AddMoney'];
            if($val['LimitDayLx']==1)
            {
              $yuelist = explode(',', $val['LimitDays']);
              for($i=0;$i< count($yuelist)-1;$i++)
              {
                 if($yuelist[$i]==$nowyue)
                 {
                   $map6=array();
                   $map6['WB_ID']=$wbid;
                   $map6['id']=array('in',$idlist2);
                   $map6['AddMoney']= $oneaddmoney;
                   $bExist=$this->where($map6)->select();
                   if(empty($bExist))
                   {
                      $bFind=1;
                   } 

                 } 
              }       
            }
            else if($val['LimitDayLx']==2)
            {
              if(strpos($val['LimitDays'],$nowzhou)!== false)
              {
                   $map6=array();
                   $map6['WB_ID']=$wbid;
                   $map6['id']=array('in',$idlist2);
                   $map6['AddMoney']= $oneaddmoney;
                   $bExist=$this->where($map6)->select();
                   if(empty($bExist))
                   {
                      $bFind=1;
                   } 
              }
            }
             
            if($bFind==1)
            {
              $idlist2.=$val['id'].',';
            }   
        }    
         
        //4.获取不重复的 LimitDayLx=0的数据
        if(!empty($idlist2))
        {
          $map6=array();
          $map6['id']=array('in',$idlist2);
          $map6['WB_ID']=$wbid;
          $moneylist=$this->where($map6)->getField('AddMoney',true);
        }  


        $map3=array();
        $map3['WB_ID']=$wbid;
        $map3['id']=array('IN',$idlist1);
        $map3['LimitDayLx']=0;
        if(!empty($moneylist))
        {
          $map3['AddMoney']=array('not in',$moneylist);
        }  
        
        $list3 = $this->where($map3)->select(); 
        foreach($list3 as &$val)
        {
             $bFind=0;
             $oneaddmoney=$val['AddMoney'];

        
             if(!empty($idlist3))
             {
               $map6=array();
               $map6['WB_ID']=$wbid;
               $map6['id']=array('in',$idlist3); 
               $map6['AddMoney']= $oneaddmoney;
               $bExist=$this->where($map6)->select();
               if(empty($bExist))
               {
                  $bFind=1;
               } 
             }
             else
             {
                $bFind=1;
             } 
                           
            if($bFind==1)
            {
              $idlist3.=$val['id'].',';
            }  
        }  

        //可用的id 
        $ky_idlist=$idlist2.$idlist3;

        // echo $ky_idlist;
        // return;
        $map4=array();
        $map4['WB_ID']=$wbid;
        $map4['id']=array('IN',$ky_idlist);      
        $ky_list=$this->where($map4)->select();

        for($i=0;$i<5;$i++)
        {

            $aje=$data[$i]['AddMoney'];

       

            $bFind=0;
            foreach($ky_list as &$val)
            {
               if($val['AddMoney']==$aje)
               {
                  

                  $data[$i]['AddMoney']=$aje;
                  $data[$i]['JLMoney']=$val['JLMoney'];
                  $data[$i]['Lx']=$val['Lx'];
                  $data[$i]['FqLx']=$val['FqLx'];
                  $data[$i]['FqJe']=$val['FqJe'];
                  $data[$i]['FqCount']=$val['FqCount'];
                  $data[$i]['Bljl']=$val['Bljl'];
                  $bFind=1;
                  break;
               } 
            } 

             
            if($bFind==1)
            {

            }
            else
            {
                $map5=array();
                $map5['id']=array('IN',$ky_idlist);
                $map5['AddMoney']=array('lt',$aje);
                $info=$this->where($map5)->order('AddMoney desc')->limit(1)->find();
                // echo $this->getLastSql();
                // return;

                if(!empty($info))
                 {
                  //走等比
                     
                     $beishu=floor($aje/$info['AddMoney']);
                     $yushu= $aje%$info['AddMoney'];  

                     if($info['Bljl']==1)
                     {
                        $data[$i]['AddMoney']=$aje;
                        $data[$i]['JLMoney']=$info['JLMoney'] * $beishu;
                        $data[$i]['Lx']=$info['Lx'];
                        $data[$i]['FqLx']=$info['FqLx'];
                        $data[$i]['FqJe']=$info['FqJe'];
                        $data[$i]['FqCount']=$info['FqCount'];
                        $data[$i]['Bljl']=1;
                     }
                     else
                     {
                        $data[$i]['AddMoney']=$aje;
                        $data[$i]['JLMoney']=$info['JLMoney'] ;
                        $data[$i]['Lx']=$info['Lx'];
                        $data[$i]['FqLx']=$info['FqLx'];
                        $data[$i]['FqJe']=$info['FqJe'];
                        $data[$i]['FqCount']=$info['FqCount'];
                        $data[$i]['Bljl']=0;
                     }                               
                 }
                 else
                 {
                      $data[$i]['AddMoney']=$aje;
                      $data[$i]['JLMoney']=0;
                      $data[$i]['Lx']=0;
                      $data[$i]['FqLx']=0;
                      $data[$i]['FqJe']=0;
                      $data[$i]['FqCount']=0;
                      $data[$i]['Bljl']=0;
                 } 


            }  
           
        }  

   
        return $data;            
    }  
	


}
