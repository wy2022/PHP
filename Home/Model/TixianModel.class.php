<?php
    namespace Home\Model;
    use Think\Model;
    class TixianModel extends Model 
    {
        protected $tableName = 'WBTixian';
        
     
      //1.向提现表插入一条数据
      public function addOneTxData($data)
      {
        return $this->data($data)->add();
      }
      
      public function getOneTxDataExist($wbid)
      {
        $res= $this->where(array('wbid'=>$wbid))->limit(3)->select(); 
        if($res)
        {
          return $res;
        }
        else
        {
          // return false;
        }  

      }

      //2.更新提现表
      public function updateOneTxData($wbid,$data)
      {
         //更新一下zfb总金额
        $res= $this->where(array('wbid'=>$wbid))->data($data)->save();
        //以下更新一次总额
        $sum_je=$this->getOneSumTxJe($wbid);
        $sumdata=array();
        $sumdata['sum_je']=$sum_je;
        return $this->where(array('wbid'=>$wbid))->data($sumdata)->save();

      }
      

      public function updateOneTxSumData($wbid,$qqtx_je)
      {  
        //以下更新一次总额
        $sum_je=$this->getOneSumTxJe($wbid);
        $sumdata=array();
        $sumdata['sum_je']=$sum_je-$qqtx_je;
        $this->where(array('wbid'=>$wbid))->data($sumdata)->save();

        return $sumdata['sum_je'];

      }


      public function getOneTxJe($wbid)
      {      
        return $this->where(array('wbid'=>$wbid))->find();
      }
      

      public function getOneSumTxJe($wbid)
      {      
        $res=$this->where(array('wbid'=>$wbid))->find();

        if($res)
        {
          $sum_je=$res['sum_wx_in']+$res['sum_zfb_in']+$res['sum_gzh_in'];
        }
        else
        {
          $sum_je=0;
        }  

        return $sum_je;
      }
  }
