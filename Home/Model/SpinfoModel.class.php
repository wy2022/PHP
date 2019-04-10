<?php
    namespace Home\Model;
    use Think\Model;
    class SpinfoModel extends Model 
    {
        protected $tableName = 'WspProductInfo';


  
     
      public function getSpjhcount($map) 
       {

          $count=$this->alias('info')
         ->join(' left join WspJh    on info.WB_ID= WspJh.WB_ID and info.SpId=WspJh.SpId')
         ->join('left join WCtrlIp on WspJh.WB_ID= WCtrlIp.Wb_id and WspJh.SyId=WCtrlIp.Syid')
         ->field(array( 
          'info.name'=>'name',
          'info.SpId'=>'spid',
          'WspJh.SyId'=>'syid',
          'info.unit'=>'unit',
          'info.guige'=>'guige',
          'info.price'=>'price', 
          'WCtrlIp.syname'=>'syname',

          'sum(WspJh.count)'=>'count'))
        ->where($map)->group('info.name ,info.SpId,info.unit,info.guige,info.price,WspJh.SyId,WCtrlIp.syname')->count();

          return $count;
       } 






       public function getSpkccount($map=array()) //获取新闻
       {
           $list = $this->join(' left join WspKc  on wspproductinfo.WB_ID= WspKc.WB_ID and wspproductinfo.SpId=WspKc.SpId')
          ->field('wspproductinfo.name ,wspproductinfo.SpId,WspKc.SyId,WspKc.currHd,WspKc.HdFw,wspproductinfo.unit,wspproductinfo.guige,wspproductinfo.price,sum(WspKc.count) as count')->where($map)->group('wspproductinfo.name ,wspproductinfo.spid,wspproductinfo.unit,wspproductinfo.guige,wspproductinfo.price,wspkc.syid,wspkc.currHd,wspkc.HdFw')->select();                     
          return count($list);  
       }  

 


      public function getSpkcList($map=array(),$page = 1, $rows = 20)   
      {                                                                          

        $list = $this->join(' left join WspKc  on wspproductinfo.WB_ID= WspKc.WB_ID and wspproductinfo.SpId=WspKc.SpId')
        ->join(' left join WCtrlIp  on WCtrlIp.Wb_id= WspKc.wb_id and WCtrlIp.Syid=WspKc.SyId')
        ->field(array(
          'wspproductinfo.name'=>'name',
          'wspproductinfo.SpId'=>'spid',
          'WspKc.currHd'=>'currHd',
          'WspKc.HdFw'=>'HdFw',
          'wspproductinfo.unit'=>'unit',
          'wspproductinfo.guige'=>'guige',
          'wspproductinfo.price'=>'price',
          'sum(WspKc.count)'=>'count',
          'WCtrlIp.syname'=>'syname',
		  'WspKc.SyId'=>'syid'
          ))
        ->where($map)->page($page,$rows)->group('wspproductinfo.name ,wspproductinfo.SpId,wspproductinfo.unit,wspproductinfo.guige,wspproductinfo.price,WspKc.SyId,WspKc.currHd,WspKc.HdFw,WCtrlIp.syname')->select(); 
		
		foreach ($list as &$val) 
        {                         	   
		  if(empty($val['syname']))
		  {
			$val['syname']= $val['syid'];
		  }            		
        }
   
		
        $count=count($list);                   
        return array('count'=>$count,'list'=>$list); 
      }



      public function getSpjhtjList($map=array(),$page = 1, $rows = 20)   
      {                                                                           
      
        $count=$this->alias('info')
         ->join(' left join WspJh    on info.WB_ID= WspJh.WB_ID and info.SpId=WspJh.SpId')
         ->join('left join WCtrlIp on WspJh.WB_ID= WCtrlIp.Wb_id and WspJh.SyId=WCtrlIp.Syid')
         ->field(array( 
          'info.name'=>'name',
          'info.SpId'=>'spid',
          'WspJh.SyId'=>'syid',
          'info.unit'=>'unit',
          'info.guige'=>'guige',
          'info.price'=>'price', 
          'WCtrlIp.syname'=>'syname',
          'sum(WspJh.count)'=>'count'))
        ->where($map)->group('info.name ,info.SpId,info.unit,info.guige,info.price,WspJh.SyId,WCtrlIp.syname')->count();

        
        $list=$this->alias('info')
         ->join(' left join WspJh    on info.WB_ID= WspJh.WB_ID and info.SpId=WspJh.SpId')
         ->join('left join WCtrlIp on WspJh.WB_ID= WCtrlIp.Wb_id and WspJh.syid=WCtrlIp.Syid')
         ->field(array( 
          'info.name'=>'name',
          'info.SpId'=>'spid',
          'WspJh.SyId'=>'syid',
          'info.unit'=>'unit',
          'info.guige'=>'guige',
          'info.price'=>'price', 
          'WCtrlIp.syname'=>'syname',

          'sum(wspjh.count)'=>'count'))
        ->where($map)->group('info.name ,info.SpId,info.unit,info.guige,info.price,wspjh.SyId,WCtrlIp.syname')->page($page,$rows)->select();

        foreach ($list as &$val) 
        {
           $val['sum_money']=  $val['count']* $val['price'];                            	   
		  if(empty($val['syname']))
		  {
			$val['syname']= $val['syid'];
		  }            		
        }
  
        $tongji_list=$this->alias('info')
         ->join(' left join WspJh    on info.WB_ID= WspJh.WB_ID and info.SpId=WspJh.SpId')
         ->join('left join WCtrlIp on WspJh.WB_ID= WCtrlIp.Wb_id and WspJh.SyId=WCtrlIp.Syid')
         ->field(array( 
          'info.name'=>'name',
          'info.SpId'=>'spid',
          'info.unit'=>'unit',
          'info.guige'=>'guige',
          'info.price'=>'price',   
          'sum(WspJh.count)'=>'count'))
        ->where($map)->group('info.name ,info.SpId,info.unit,info.guige,info.price')->select();

        foreach ($tongji_list as &$val) 
        {
           $val['sum_money']=  $val['count']* $val['price'];
        }

        return array('count'=>$count,'list'=>$list,'tongji_count'=>count($tongji_list),'tongji_list'=>$tongji_list); 
      }


 
       public function getSpxscount($map) 
       {
         $count=$this->alias('info')
         ->join(' left join WspXs    on info.WB_ID= WspXs.WB_ID and info.SpId=WspXs.Spid')
         ->join('left join WCtrlIp on WspXs.WB_ID= WCtrlIp.Wb_id and WspXs.Syid=WCtrlIp.Syid')
         ->field(array( 
          'info.name'=>'name',
          'info.SpId'=>'spid',
          'WspXs.Syid'=>'syid',
          'info.unit'=>'unit',
          'info.guige'=>'guige',
          'info.price'=>'price', 
          'WCtrlIp.syname'=>'syname',
          'sum(WspXs.sl)'=>'count'))
        ->where($map)->group('info.name ,info.SpId,info.unit,info.guige,info.price,WspXs.Syid,WCtrlIp.syname')->count();

        return   $count;
       }   


      public function getSpxstjList($map=array(),$page = 1, $rows = 20)   
      {                                                                     

 
        $count=$this->alias('info')
         ->join(' left join WspXs    on info.WB_ID= WspXs.WB_ID and info.SpId=WspXs.Spid')
         ->join('left join WCtrlIp on WspXs.WB_ID= WCtrlIp.Wb_id and WspXs.Syid=WCtrlIp.Syid')
         ->field(array( 
          'info.name'=>'name',
          'info.SpId'=>'spid',
          'WspXs.Syid'=>'syid',
          'info.unit'=>'unit',
          'info.guige'=>'guige',
          'info.price'=>'price', 
          'WCtrlIp.syname'=>'syname',
          'sum(WspXs.sl)'=>'count'))
        ->where($map)->group('info.name ,info.SpId,info.unit,info.guige,info.price,WspXs.Syid,WCtrlIp.syname')->count();


         $list=$this->alias('info')
         ->join(' left join WspXs    on info.WB_ID= WspXs.WB_ID and info.SpId=WspXs.Spid')
         ->join(' left join WCtrlIp    on WspXs.WB_ID= WCtrlIp.Wb_id and WspXs.Syid=WCtrlIp.Syid')
         ->field(array( 
          'info.name'=>'name',
          'info.SpId'=>'spid',
          'WspXs.Syid'=>'syid',
          'info.unit'=>'unit',
          'info.guige'=>'guige',
          'info.price'=>'price', 
          'WCtrlIp.syname'=>'syname',   
          'sum(WspXs.Sl)'=>'count'))
        ->where($map)->group('info.name ,info.SpId,info.unit,info.guige,info.price,WspXs.Syid,WCtrlIp.syname')->page($page,$rows)->select();

        
		


        foreach ($list as &$val) 
        {
           $val['sum_money']=  $val['count']* $val['price'];
		   if(empty($val['syname']))
		  {
			$val['syname']= $val['syid'];
		  }  
        }
            
        $tongji_list=$this->alias('info')
         ->join(' left join WspXs    on info.WB_ID= WspXs.WB_ID and info.SpId=WspXs.Spid')
         ->join('left join WCtrlIp on WspXs.WB_ID= WCtrlIp.Wb_id and WspXs.Syid=WCtrlIp.Syid')
         ->field(array( 
          'info.name'=>'name',
          'info.SpId'=>'spid',
          'info.unit'=>'unit',
          'info.guige'=>'guige',
          'info.price'=>'price',   
          'sum(WspXs.sl)'=>'count'))
        ->where($map)->group('info.name ,info.SpId,info.unit,info.guige,info.price')->select();
		
	

        foreach ($tongji_list as &$val) 
        {
           $val['sum_money']=  $val['count']* $val['price'];
        }
        return array('count'=>$count,'list'=>$list,'tongji_count'=>count($tongji_list),'tongji_list'=>$tongji_list); 
		
		
      }
       
       
    public function getGoodsInfoList($map = array(), $order = '', $page = 1, $rows = 20){
        $list = $this->where($map)->order($order)->page($page,$rows)->select();
        $count = $this->where($map)->count();
        return array('count'=>$count,'list'=>$list);
    }

    public function updateGoodsInfo($map=array(),$data){
        return $this->where($map)->data($data)->save();
    }

    public function deleteGoodsInfo($map=array()){
        return $this->where($map)->delete();
    }

    public function addGoodsInfo($map=array(),$data)
    {
        $data['SpId'] = $this->max('SpId') + 1;
        
        return $this->where($map)->data($data)->add();
    } 
      

  }
