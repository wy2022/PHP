<?php
    namespace Home\Model;
    use Think\Model;
    class TxQueryInfoModel extends Model 
    {
        protected $tableName = 'WBTxQueryInfo';


      public function addQueryTxInfo($data=array())
      {
          return $this->data($data)->add();
      }
      
      public function updateQueryTxInfo($map=array(),$data=array())
      {
          return $this->where($map)->data($data)->save();
      }


      public function getQueryTxInfoList($map=array(),$page = 1,$rows = 10)
      {

        $count=$this->where($map)->count();      
        $wbid=$map['WBTxQueryInfo.wbid'];
        
        $dai_tx_je   =$this-> getQueryTxSumJeByMap($map,2);
        $yi_tx_je    =$this-> getQueryTxSumJeByMap($map,1);
      
        $dai_tx_je=sprintf("%.2f", $dai_tx_je);
        $yi_tx_je=sprintf("%.2f", $yi_tx_je); 



        $list = $this
		// ->join('LEFT JOIN Wb_Info ON WBTxQueryInfo.wbid=Wb_Info.WBID')
		// ->join('LEFT JOIN WbTxBankInfo ON WBTxQueryInfo.wbid=WbTxBankInfo.wbid')

        // ->field(array( 
          // 'WBTxQueryInfo.orderno'=>'orderno',
          // 'WBTxQueryInfo.qqtx_je'=>'qqtx_je',
          // 'WBTxQueryInfo.bankcardno'=>'bankcardno',
          // 'WBTxQueryInfo.time_post'=>'time_post',
          // 'WBTxQueryInfo.time_end'=>'time_end',
          // 'WBTxQueryInfo.tx_status'=>'tx_status',
          
          // 'Wb_Info.WbName'=>'WbName',
          // 'WbTxBankInfo.phonenum'=>'phonenum',
          // 'WbTxBankInfo.farenname'=>'farenname',
          // 'WbTxBankInfo.kh_hang'=>'kh_hang',
          // 'WBTxQueryInfo.beizhu'=>'beizhu'))
        ->where($map)->order('time_post DESC')->page($page,$rows)->select();
		foreach($list as &$val){
			  $val['time_end']= date('Y-m-d H:i:s',strtotime($val['time_end']));
			  $val['time_post']= date('Y-m-d H:i:s',strtotime($val['time_post'])); 
              $val['sum_je']= sprintf("%.2f",  $val['sum_je']);    
              $val['qqtx_je']= sprintf("%.2f",  $val['qqtx_je']);    
		}

        return array('count'=>$count,'dai_tx_je'=>$dai_tx_je,'yi_tx_je'=>$yi_tx_je,'list'=>$list); 

      }

      
      public function getOneQueryTxInfoById($map=array())
      {

          return   $list= $this->
          join('LEFT JOIN Wb_Info ON WBTxQueryInfo.wbid=Wb_Info.WBID')->
          join('LEFT JOIN WbTxBankInfo ON WBTxQueryInfo.wbid=WbTxBankInfo.wbid')
        ->field('WBTxQueryInfo.orderno,WBTxQueryInfo.id,WBTxQueryInfo.qqtx_je,WBTxQueryInfo.bankcardno,WBTxQueryInfo.tx_status,Wb_Info.WbName,WbTxBankInfo.farenname')
        ->where($map)->find();
      }

      public function getQueryTxInfoListbyBoss($map=array(),$page = 1,$rows = 10)
      {
        $count=$this->where($map)->count(); //获取该时段内临时卡加钱记录数量   
        

        $dai_tx_je   =$this-> getQueryTxSumJeByMap($map,2);
        $yi_tx_je    =$this-> getQueryTxSumJeByMap($map,1);

        $dai_tx_je=sprintf("%.2f", $dai_tx_je);
        $yi_tx_je =sprintf("%.2f", $yi_tx_je); 

        $list = $this->join('LEFT JOIN Wb_Info ON WBTxQueryInfo.wbid=Wb_Info.WBID')
                     ->join('LEFT JOIN WbTxBankInfo ON WBTxQueryInfo.wbid=WbTxBankInfo.wbid')
                     -> field(array( 
                        'WBTxQueryInfo.orderno'=>'orderno',
                        'WBTxQueryInfo.id'=>'id',
                        'WBTxQueryInfo.wbid'=>'wbid',
                        'WBTxQueryInfo.qqtx_je'=>'qqtx_je',
                        'WBTxQueryInfo.bankcardno'=>'bankcardno',
                        'WBTxQueryInfo.time_post'=>'time_post',
                        'WBTxQueryInfo.time_end'=>'time_end',
                        'WBTxQueryInfo.tx_status'=>'tx_status',
                        'Wb_Info.WbName'=>'WbName',
                        'WbTxBankInfo.phonenum'=>'phonenum',
                        'WbTxBankInfo.farenname'=>'farenname',
                        'WbTxBankInfo.kh_hang'=>'kh_hang',
                        'WBTxQueryInfo.beizhu'=>'beizhu'                   
                        ))->where($map)->page($page,$rows)->order('time_post DESC')->select();

        return array('count'=>$count,'dai_tx_je'=>$dai_tx_je,'yi_tx_je'=>$yi_tx_je,'list'=>$list); 
      }

      public function getQueryTxInfoCount($map=array())
      {
          return  $this->where($map)->count();
      }
      

      public function getQueryTxSumJeById($wbid)
      {
        return $this->where(array('wbid'=>$wbid))->sum('qqtx_je');      
      }

      public function getQueryTxSumJeByMap($map=array(),$tx_status)
      {     

        return $this->where($map)->where(array('tx_status'=>$tx_status))->sum('qqtx_je');       
      }

      public function getQueryTxSumJe($wbid,$tx_status)
      {      
         if($tx_status==0)
         {
           return $this->where(array('wbid' =>$wbid))->sum('qqtx_je'); 
         }
         else
         {
           return $this->where(array('wbid' =>$wbid,'tx_status'=>$tx_status))->sum('qqtx_je'); 
         } 
          
                 
      }


      

    
  }
