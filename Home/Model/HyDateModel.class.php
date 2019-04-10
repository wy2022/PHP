<?php
    namespace Home\Model;
    use Think\Model;
    class HyDateModel extends Model 
    {
        protected $tableName = 'WHyDateJl';


      public function getHyDateCount($wbid)
      {
        return $this->where(array('wb_id'=>$wbid))->count();
      }

      public function getHyDateValid($wbid)
      {
        $nowtime=date('Y-m-d G:i:s',time());

        // $nowtime='2016-05-18 10:19:00';
        $nowday=((int)substr($nowtime,8,2));//取得几号
        $nowhour=substr($nowtime,11,8);//取得几号
        
  
     
        $count=$this->getHyDateCount($wbid);    
        
        if($count==0)
        {
          
         
          return null;
          
        }
        else
        {
         
          $list = $this->where(array('wb_id'=>$wbid))->field(array('AddMoney','JLMoney','DayDate','EndDayDate'))->select(); //返回一个数据集 
          $resultdata =array('count'=>$count,'list'=>$list); 

          foreach ($resultdata['list'] as &$val)
          {                             
            $val['DayDate']= date('Y-m-d G:i:s',strtotime($val['DayDate']));  
            $val['EndDayDate']= date('Y-m-d G:i:s',strtotime($val['EndDayDate']));  

            $hyday_beg=((int)substr($val['DayDate'],8,2));//取得几号
            // $hyday_end=((int)substr($val['EndDayDate'],8,2));//取得几号

            $h_beg=substr($val['DayDate'],11,8);//取得几点
            $h_end=substr($val['EndDayDate'],11,8);//取得几点
     
            if(($nowday==$hyday_beg)&&($h_beg<$nowhour)&&($nowhour<$h_end))
            {

            }else
            {
              $val['AddMoney']=0;
              $val['JLMoney']=0;
            }              
          }


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
              $test['list'][$i]['DayDate']   =$value['DayDate'];
              $test['list'][$i]['EndDayDate']=$value['EndDayDate'];
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

  }
