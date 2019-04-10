<?php
    namespace Home\Model;
    use Think\Model;
    class ChangeModel extends Model 
    {
      protected $tableName = 'WIntegral_JlTable_Mx';
      public function getSpChangeinfoList($map=array(),$order = '',$page = 1,$rows)  
      {  
		   $count = $this->alias('jfchange')->join('left join WHyCardTable ON   WHyCardTable.hyCardNo = jfchange.HyCardNo')->where($map)->count();                                                                       
		   
		   $list = $this->alias('jfchange')->join('left join WHyCardTable ON  WHyCardTable.hyCardNo = jfchange.HyCardNo')->field(array(
			   'jfchange.id'=>'id',
			   'jfchange.wb_id'=>'wb_id',
			   'jfchange.HyCardNo'=>'HyCardNo',
			   'jfchange.Integral'=>'Integral',
			   'jfchange.Je'=>'Je',
			   'jfchange.Lx'=>'Lx',
			   'jfchange.SpName'=>'SpName',
			   'jfchange.syid'=>'syid',
			   'jfchange.Operate'=>'Operate',
			   'jfchange.SpSl'=>'SpSl',
			   'jfchange.cTime'=>'cTime',
			   'WHyCardTable.hyname'=>'hyname',
			   'WHyCardTable.hyCardGuid'=>'hyCardGuid',
		    ))->where($map)->order($order)->page($page,$rows)->select();
		   
		  foreach($list as &$val)
		  {
			  $val['hydj']=D('Hylx')->where(array('WB_ID'=>session('wbid'),'Guid'=>$val['hyCardGuid']))->getField('Name');
			  $val['cTime'] = date('Y-m-d H:i:s',strtotime($val['cTime'])); 
		  }
       return array('count'=>$count,'list'=>$list);
      }
	  
      public function expSpChangeinfo()  
      {  
        $list = $this->alias('jfchange')->join('left join WHyCardTable ON jfchange.wb_id = WHyCardTable.WB_ID and WHyCardTable.hyCardNo = jfchange.HyCardNo')->field(array(
	       'jfchange.id'=>'id',
		   'jfchange.wb_id'=>'wb_id',
		   'jfchange.HyCardNo'=>'HyCardNo',
		   'jfchange.Integral'=>'Integral',
		   'jfchange.Je'=>'Je',
		   'jfchange.Lx'=>'Lx',
		   'jfchange.SpName'=>'SpName',
		   'jfchange.syid'=>'syid',
		   'jfchange.Operate'=>'Operate',
		   'jfchange.SpSl'=>'SpSl',
		   'jfchange.cTime'=>'cTime',
		   'WHyCardTable.hyname'=>'hyname',
		   'WHyCardTable.hyCardGuid'=>'hyCardGuid',
	    ))->where($map)->order($order)->page($page,$rows)->select();
	      // $list1=D('WbInfo')->select();
		  // foreach($list1 as &$val)
		  // {
			// $val['ceshi']='商品';
			// $val['ceshi2']='商品2';  
		  // }
		  
	
		   return $list;
	   
		  // foreach($list1 as &$val)
		  // {
			// $val['hydj']=D('Hylx')->where(array('WB_ID'=>session('wbid'),'Guid'=>$val['hyCardGuid']))->getField('Name');
			// $val['cTime'] = date('Y-m-d H:i:s',strtotime($val['cTime'])); 
			// $syname = D('SpCtrlIp')->where(array('Wb_id'=>session('wbid'),'Syid'=>$val['syid']))->getField('syname');  
			// if($syname !='')
			// {
			  // $val['syid']= $syname;
			// }
			// $val['HyCardNo']= "'".(string)$val['HyCardNo'];		
			// $val['sp']='';
			
			//$content = iconv("utf-8","gb2312//IGNORE",$content);
			
			// if(!($val['SpName']==null))
			// {
				// $val['sp']=$val['SpName'].'('.$val['SpSl'].')';
			// }
			// else
			// {
				// $val['sp']=$val['Je'];
			// }
			 
			// if($val['Lx']=='0')
			// {
				// $val['Lx_2']='商品';
			// }
			// else
			// {
				// $val['Lx_2']='网费';
			// }	 
			// $val['ceshi']='商品';
			// $val['ceshi2']='商品2';
			 
		  // }
		  

      }
	  
		public function getSpChangeinfoList_count($map=array())
		 {
			return  $this->alias('jfchange')->where($map)->count();
		 }





  }
