<?php
    namespace Home\Model;
    use Think\Model;
    class HyaddmoneymxModel extends Model 
    {
      protected $tableName = 'WHyCardTable_AddMoney_Mx';

      public function getHyaddmoneymxListByMap_Count($map=array())
	  {

	    $count = $this->where($map)->count();
	    return $count;
	  }





		public function getHyaddmoneymxListByMap($map=array(),$order = '',$page = 1,$rows = 20)
		{
			
  
	        $count = $this->where($map)->count();
			$list  = $this->where($map)->Field(array(
				'id'=>'id',
				'SyId'=>'SyId',
				'HyCardNo'=>'cardNo',
				'je'=>'je',
				'jlJe'=>'jlJe',
				'cTime'=>'cTime',
				'WB_ID'=>'WB_ID',
				'Operation'=>'Operation',
				'sGuid'=>'sGuid'
				))->order('cTime desc')->page($page,$rows)->select();	


				  foreach ($list as &$val)
		          {                             
		            $val['jlJe']= sprintf("%.2f", $val['jlJe']);  
		            $val['je']= sprintf("%.2f", $val['je']);  

		            $val['hyname']= D('HyInfo')->where(array('WB_ID'=>session('wbid'),'hyCardNo'=>$val['cardNo']))->getField('hyname');  
		          
		            $ahylevel= D('HyInfo')->where(array('WB_ID'=>session('wbid'),'hyCardNo'=>$val['cardNo']))->getField('hyCardGuid');  

		            $val['hylevel']=D('Hylx')->where(array('WB_ID'=>session('wbid'),'Guid'=>$ahylevel))->getField('Name'); 

		            $val['fqje']=D('HyfqJlMx')->where(array('WB_ID'=>session('wbid'),'FGuid'=>$val['sGuid']))->getField('Fqje'); 
		            $val['FqCount']=D('HyfqJlMx')->where(array('WB_ID'=>session('wbid'),'FGuid'=>$val['sGuid']))->getField('FqCount');
		          
		            $val['cTime']=date('Y-m-d H:i:s',strtotime($val['cTime']));
		          }
		
			return array('list'=>$list,'count'=>$count);
		}


		public function getHyaddmoneyrankingListByMap($map=array())
		{		
			$wbid=$map['WB_ID'];
			$wbname=D('WbInfo')->where(array('WBID'=>$wbid))->getField('WbName');

            $list=$this->where($map)
           ->field(array(
            'sum(je)'=> 'sumje',
            'HyCardNo'=>'HyCardNo'
           	))
           ->order('sumje desc')->group('HyCardNo,je')->limit(20)->select();
           $i=1;
           foreach($list as &$val)
           {
             $val['rankid']=$i;
             $val['WbName']=$wbname;
			 $map2=array();
			 $map2['hyCardNo']=$val['HyCardNo'];
			 $map2['WB_ID']=$wbid;
			 
			 $val['hyname']=D('HyInfo')->where($map2)->getField('hyname');
             $i++;
           }	
           return array('list'=>$list,'count'=>20);
		}



	public function getHyaddmoneymxListByMap2_Count($map=array())
	  {

	    $count = $this->alias('addmoney')->where($map)->count();
	    return $count;
	  }



		public function getHyaddmoneymxListByMap2($map=array(),$order = '',$page = 1,$rows = 20)
		{
			
			$count = $this->alias('addmoney')->where($map)->count();
			$list  = $this->alias('addmoney')
			->join('left join WHyCardTable as hytable on addmoney.HyCardNo= hytable.hyCardNo and addmoney.WB_ID=hytable.WB_ID')
			
			->Field(array(
				'addmoney.id'=>'id',
				'addmoney.SyId'=>'SyId',
				'addmoney.HyCardNo'=>'cardNo',
				'addmoney.je'=>'je',
				'addmoney.jlJe'=>'jlJe',
				'addmoney.cTime'=>'cTime',
				'addmoney.WB_ID'=>'WB_ID',
				'addmoney.Operation'=>'Operation',
				'addmoney.sGuid'=>'sGuid',
				'hytable.hyname'=>'hyname',
				'hytable.hyCardGuid'=>'hyCardGuid',
				
				))->order($order)->page($page,$rows)->where($map)->select();	

				  foreach ($list as &$val)
		          {                             
		            $val['jlJe']= sprintf("%.2f", $val['jlJe']);  
		            $val['je']= sprintf("%.2f", $val['je']);  

		            // $val['hyname']= D('HyInfo')->where(array('WB_ID'=>session('wbid'),'hyCardNo'=>$val['cardNo']))->getField('hyname');  
		          
		            // $ahylevel= D('HyInfo')->where(array('WB_ID'=>session('wbid'),'hyCardNo'=>$val['cardNo']))->getField('hyCardGuid');  

		            $val['hylevel']=D('Hylx')->where(array('WB_ID'=>session('wbid'),'Guid'=>$val['hyCardGuid']))->getField('Name'); 

		            $val['fqje']=D('HyfqJlMx')->where(array('WB_ID'=>session('wbid'),'FGuid'=>$val['sGuid']))->getField('Fqje'); 
		            $val['FqCount']=D('HyfqJlMx')->where(array('WB_ID'=>session('wbid'),'FGuid'=>$val['sGuid']))->getField('FqCount');
		          
		            $val['cTime']=date('Y-m-d H:i:s',strtotime($val['cTime']));
		          }
			
			return array('list'=>$list,'count'=>$count);
		}

	    public function expHykaddmoney_detail($map=array())
		  {
    
			$list  = $this->alias('addmoney')
			->join('left join WHyCardTable as hytable on addmoney.HyCardNo= hytable.hyCardNo and addmoney.WB_ID=hytable.WB_ID')
			
			->Field(array(
				'addmoney.id'=>'id',
				'addmoney.SyId'=>'SyId',
				'addmoney.HyCardNo'=>'cardNo',
				'addmoney.je'=>'je',
				'addmoney.jlJe'=>'jlJe',
				'addmoney.cTime'=>'cTime',
				'addmoney.WB_ID'=>'WB_ID',
				'addmoney.Operation'=>'Operation',
				'addmoney.sGuid'=>'sGuid',
				'hytable.hyname'=>'hyname',
				'hytable.hyCardGuid'=>'hyCardGuid',
				
				))->where($map)->select();
			foreach ($list as &$val)
		          { 
                    $syname = D('SpCtrlIp')->where(array('Wb_id'=>session('wbid'),'Syid'=>$val['SyId']))->getField('syname');  
					  if($syname !='')
					  {
						$val['SyId']= $syname;
					  }
			  
		            $val['jlJe']= sprintf("%.2f", $val['jlJe']);  
		            $val['je']= sprintf("%.2f", $val['je']);  
					$val['cardNo']= "'".(string)$val['cardNo'];  

		            $val['hylevel']=D('Hylx')->where(array('WB_ID'=>session('wbid'),'Guid'=>$val['hyCardGuid']))->getField('Name'); 

		            $val['fqje']=D('HyfqJlMx')->where(array('WB_ID'=>session('wbid'),'FGuid'=>$val['sGuid']))->getField('Fqje'); 
		            $val['FqCount']=D('HyfqJlMx')->where(array('WB_ID'=>session('wbid'),'FGuid'=>$val['sGuid']))->getField('FqCount');
		          
		            $val['cTime']=date('Y-m-d H:i:s',strtotime($val['cTime']));
		          }
				   
			return $list;
		  }


    }
