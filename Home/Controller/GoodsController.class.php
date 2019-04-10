<?php
namespace Home\Controller;
class GoodsController extends CommonController {

    
    public function index()
    {
      $this->display();
    }

    public function set()
    {
      $this->display();
    }

     public function getSpIniinfo()
     {
        if(IS_AJAX)
        {
            $page = I('get.page',1,'int');
            $rows = I('get.rows',20,'int');
            $sidx = I('get.sidx','','string')?:'id';
            $sord = I('get.sord','','string')?:'asc';
             
           $wbid=session('wbid');       
           $sylist=D('SpCtrlIp')->getAllSpIniById($wbid);     

           $this->ajaxReturn($sylist);

        }
    }
    

    public function syidedit()
    { 
      $wbid = I('get.wbid',1,'int');
      $syid  = I('get.syid',1,'int');   
      $syinfo = D('SpCtrlIp')->getSpIniById($wbid,$syid);
      $this->assign('syinfo',$syinfo );
      $this->display();        
    }


    public function setsyinfo()
    { 
      if(IS_AJAX)
      {
        $wbid = I('post.wbid',1,'int');
        $syid = I('post.syid',20,'int');
        $syname = I('post.syname','','string');
        $map=array();
        $map['Wb_id']=$wbid ;
        $map['Syid']=$syid;

        $updateData['syname']=$syname;


        if(D('SpCtrlIp')->updateSpIniById($map,$updateData))
        {
          $response['data']=1;
        }
        else
        {
          $response['data']=-1;
        }
            
        $this->ajaxReturn($response);          
    }
  }



    


    public function getGoods(){
        if(IS_AJAX){
            $page = I('get.page',1,'int');
            $rows = I('get.rows',20,'int');
            $sidx = I('get.sidx','','string')?:'id';
            $sord = I('get.sord','','string')?:'asc';
            
            
            $map=array();
            $map['WB_ID']=session('wbid');
            $goodsInfos = D('Spinfo')->getGoodsInfoList($map,"$sidx $sord",$page,$rows);

            $response = new \stdClass();
            $response->records = $goodsInfos['count'];
            $response->page = $page;
            $response->total = ceil($goodsInfos['count'] / $rows);
            foreach($goodsInfos['list'] as $key => $value){
                $value['Rq'] = $value['Rq']?date('Y-m-d H:i:s',strtotime($value['Rq'])):null;
                $response->rows[$key]['id'] = $key;
                $response->rows[$key]['cell'] = $value;
            }
            $this->ajaxReturn($response);
        }
    }
    public function setGoods(){
        if(IS_AJAX){
            $data = I('post.');
            if(isset($data['oper'])){
                switch($data['oper']){
                    case 'edit':
                        $updateData = array(
                            'name'  =>  $data['name'],
                            'unit'  =>  $data['unit'],
                            'guige' =>  $data['guige'],
                            'price' =>  $data['price']
                        );

                          $map=array();
                          $map['WB_ID']=session('wbid');
                          $map['SpId']=$data['SpId'];
                        
                        if(D('Spinfo')->updateGoodsInfo($map,$updateData)){
                            $this->success();
                        }else{
                            $this->error();
                        }
                        break;
                    case 'del':

                        $map=array();
                        $map['WB_ID']=session('wbid');
                        $map['SpId']=$data['SpId'];
                 
                        if(D('Spinfo')->deleteGoodsInfo($map))
                        {
                            $this->success();
                        }
                        else
                        {
                            $this->error();
                        }
                        break;
                    case 'add':
                        $addData = array(
                            'name'  =>  $data['name'],
                            'unit'  =>  $data['unit'],
                            'guige' =>  $data['guige'],
                            'price' =>  $data['price']
                        );

                        $map=array();
                        $map['WB_ID']=session('wbid');
                        
                        if(D('Spinfo')->addGoodsInfo($map,$addData))
                        {
                            $this->success();
                        }else{
                            $this->error();
                        }
                        break;
                }
            }
        }
    }

    public function kclog()
    { 
      $this->display();              
    }

	
		

    public function jhtj()
    {    
      $wbid=session('wbid'); 
      $sylist=D('SpCtrlIp')->getAllSpIniById($wbid);
		foreach ($sylist as &$val)
		{                             	   
		  if(empty($val['syname']))
		  {
			$val['syname']= $val['Syid'];
		  }            
		}
	 
      $this->assign('sylist',$sylist);
      $this->display();              
    }

    public function xstj()
    { 

      $wbid= session('wbid');

      $sylist=D('SpCtrlIp')->getAllSpIniById($wbid);
	  foreach ($sylist as &$val)
		{                             	   
		  if(empty($val['syname']))
		  {
			$val['syname']= $val['Syid'];
		  }            
		}
      $this->assign('sylist',$sylist);
      $this->display();              
    }


   public function jhlog()
    { 

      $wbid= session('wbid');
      $sylist=D('SpCtrlIp')->getAllSpIniById($wbid);
      $this->assign('sylist',$sylist);
      $this->display();               
    }
    public function xslog()
    { 
      $wbid= session('wbid');
      $sylist=D('SpCtrlIp')->getAllSpIniById($wbid);
      $this->assign('sylist',$sylist);
      $this->display();                
    }



    public function getjhlog()
    {  
        
        if(IS_AJAX)
        {       
            $page = I('get.page',1,'int');
            $rows = I('get.rows',20,'int');
    /*        $sord = I('get.sord','','string')?:'desc';
            $sidx = I('get.sidx','','string')?:'wbid';*/
            // $syid = I('post.position','','string'); 
            $sContent = I('get.sContent','','string');
            $daterange = I('get.daterange','','string');//获取交班时间

  
            $wbid= session('wbid');
            if(empty($wbid))
            {
              $wbid =-1;
            } 

            $map = array();  
            if(!empty($wbid))
            {
               $map['WspJh.WB_ID']= session('wbid');
            } 

          
            if(!empty($sContent ))
            {
               $map['WspProductInfo.name']= array('LIKE',"%$sContent%");
            }    

           
      
            if(!empty($daterange))  
            {
                list($start,$end) = explode(' - ',$daterange);    //explode() 函数把字符串以"-"为标志分割为数组，包含两个元素，赋值给list，调用时候直接$start,$end
                $start = str_replace('/','-',$start);            //把分割的第一个数据里的"/"用"-"号替换
                $end = str_replace('/','-',$end);                //把分割的第二个数据里的"/"用"-"号替换
                $map['WspJh.rq'] = array('BETWEEN',array($start,$end));//返回数据array(值，值)---第一个值是字符串'between'，第二个值是一个数组
            }
                 
    
          //   处理分页               
            $count=D('Spjh')->getSpjhcount($map);
            $rows=10;
            $sql_page=ceil($count/$rows);   
            
            if($page<=0)   $page=1;       
            if($page>$sql_page) 
            {
              $page=1; 
            }else
            {

            }    

            $spjhdata = D('Spjh')->getSpjhList($map,$page,$rows);             
            $count     = $spjhdata['count'];

            $response = new \stdClass();
            $response->count       = $spjhdata['count'];//返回的数组的第一个字段记录总条数
            $response->nowPage     = $page ;              //每页显示的记录数目               
            $response->total       = ceil($spjhdata['count'] / $rows);          

      
            foreach ($spjhdata['list'] as &$val)
            {                             
              $val['rq']= date('Y-m-d H:i:s',strtotime($val['rq']));      
            }
            $response->rows   = $spjhdata['list'] ;
            $this->ajaxReturn($response);
        }
                 
    }

    public function getxslog()
    { 
         
        if(IS_AJAX)
        {       
            $page = I('get.page',1,'int');
            $rows = I('get.rows',20,'int');
    /*        $sord = I('get.sord','','string')?:'desc';
            $sidx = I('get.sidx','','string')?:'wbid';*/
            // $syid = I('post.position','','string'); 
            $sContent = I('get.sContent','','string');
            $daterange = I('get.daterange','','string');//获取交班时间

      

            $wbid= session('wbid');
            if(empty($wbid))
            {
              $wbid =-1;
            } 

            $map = array();  
            if(!empty($wbid))
            {
               $map['Wspxs.WB_ID']= session('wbid');
            } 
    




            
           // if(!empty($syid))  
           //  {
           //     $map['Wspxs.syid']=$syid ;
           //  }


            if(!empty($sContent ))
            {
               $map['WspProductInfo.name']= array('LIKE',"%$sContent%");
            }    

      


            if(!empty($daterange))  
            {
                list($start,$end) = explode(' - ',$daterange);    //explode() 函数把字符串以"-"为标志分割为数组，包含两个元素，赋值给list，调用时候直接$start,$end
                $start = str_replace('/','-',$start);            //把分割的第一个数据里的"/"用"-"号替换
                $end = str_replace('/','-',$end);                //把分割的第二个数据里的"/"用"-"号替换
                $map['Wspxs.Rq'] = array('BETWEEN',array($start,$end));//返回数据array(值，值)---第一个值是字符串'between'，第二个值是一个数组
                // $map['Rq'] = array('BETWEEN',array($start,$end));//返回数据array(值，值)---第一个值是字符串'between'，第二个值是一个数组
            }
                 
    
          //   处理分页               
            $count=D('Spxs')->getSpxscount($map);
            $rows=10;
            $sql_page=ceil($count/$rows);   
            
            if($page<=0)   $page=1;       
            if($page>$sql_page) 
            {
              $page=1; 
            }else
            {

            }    

            $spxsdata = D('Spxs')->getSpxsList($map,$page,$rows);             
            $count     = $spxsdata['count'];

          
         // 2.重新包装数据，并将所有数据放进response
            $response = new \stdClass();
            $response->count       = $spxsdata['count'];//返回的数组的第一个字段记录总条数
            $response->nowPage     = $page ;              //每页显示的记录数目               
            $response->total       = ceil($spxsdata['count'] / $rows);          

        
            foreach ($spxsdata['list'] as &$val)
            {                             
              $val['rq']= date('Y-m-d H:i:s',strtotime($val['rq']));      
            }
            $response->rows   = $spxsdata['list'] ;

            // var_dump($spjhdata);
            $this->ajaxReturn($response);
        }
                 
    }


    public function getkcinfo()
    { 
       
        if(IS_AJAX)
        {       
            $page = I('get.page',1,'int');
            $rows = I('get.rows',20,'int');
            $sord = I('get.sord','','string')?:'desc';
            $sidx = I('get.sidx','','string')?:'SpId';
            // $daterange = I('get.daterange','','string');
            // $goodsname = I('get.goodsname','','string');

            // $min_amount = I('post.min_amount',0,'float');
            // $max_amount =  I('post.max_amount',100,'float');
             //$s= $this->getLastSql();
		

            $map = array();           
            $map['WspKc.WB_ID']=session('wbid');


            if(!empty($goodsname ))
            {
               $map['wspproductinfo.name']= array('LIKE',"%$goodsname%");
            }   

                  
            $count=D('Spinfo')->getSpkccount($map);
    
            $rows=10;
            $sql_page=ceil($count/$rows);   
            
            if($page<=0)   $page=1;       
            if($page>$sql_page) 
            {
              $page=1; 
            }else
            {

            }    
          
            $kctjdata = D('Spinfo')->getSpkcList($map,$page,$rows);         
            $count     = $kctjdata['count'];
            $response = new \stdClass();
            $response->count       = $kctjdata['count'];//返回的数组的第一个字段记录总条数
            $response->nowPage     = $page ;              //每页显示的记录数目               
            $response->total       = ceil($kctjdata['count'] / $rows);          
       

            $response->rows   = $kctjdata['list'] ;
            $this->ajaxReturn($response);
        }
                 
    }

    public function getjhtjinfo()
    {       
        if(IS_AJAX)
        {       
            $page = I('get.page',1,'int');
            $rows = I('get.rows',20,'int');
            $sord = I('get.sord','','string')?:'desc';
            $sidx = I('get.sidx','','string')?:'SpId';
           
            $daterange = I('get.daterange','','string');//获取交班时间
            $syid =    I('get.position','','string');
            $sContent =    I('get.sContent','','string');


            $wbid= session('wbid');
            if(empty($wbid))
            {
              $wbid =-1;
            }	
            $map = array();    
            if(!empty($wbid))
            {
              $map['WspJh.WB_ID']= $wbid;
            } 

            if(!empty($syid))
            {
              $map['WspJh.SyId']=$syid ;
            }  

            if(!empty($sContent))
            {
              $map['info.name']=array('LIKE',"%$sContent%");
            }


            if(!empty($daterange))  
            {
              list($start,$end) = explode(' - ',$daterange);    
              $start = str_replace('/','-',$start);            
              $end = str_replace('/','-',$end);   
              $map['WspJh.rq']=array('BETWEEN',array($start,$end));
            }
     
            $count=D('Spinfo')->getSpjhcount($map);  

            $rows=10;
            $sql_page=ceil($count/$rows);   
            
            if($page<=0)   $page=1;       
            if($page>$sql_page) 
            {
              $page=1; 
            }
            else
            {

            }    

            $jhtjdata = D('Spinfo')->getSpjhtjList($map,$page,$rows);         
            $count    = $jhtjdata['count'];
            $response = new \stdClass();
            $response->count       = $jhtjdata['count'];//返回的数组的第一个字段记录总条数
            $response->nowPage     = $page ;              //每页显示的记录数目               
            $response->total       = ceil($jhtjdata['count'] / $rows); 
           
            $response->rows          = $jhtjdata['list'] ;
            $response->tongji_list   = $jhtjdata['tongji_list'] ;
            $response->tongji_count   = $jhtjdata['tongji_count'] ;
            $this->ajaxReturn($response);
        }
                 
    }

    public function getxstjinfo()
    { 
          
        if(IS_AJAX)
        {       

            $page = I('get.page',1,'int');
            $rows = I('get.rows',20,'int');
            $sord = I('get.sord','','string')?:'desc';
            $sidx = I('get.sidx','','string')?:'Spid';
            $syid = I('get.position','','string');
            $daterange = I('get.daterange','','string');//获取交班时间
            $sContent =    I('get.sContent','','string');


            $wbid= session('wbid');
            if(empty($wbid))
            {
              $wbid =-1;
            }	

            $map = array();  
            if(!empty($wbid))
            {
               $map['WspXs.WB_ID']= session('wbid');
            } 
    
                      
           if(!empty($syid))  
            {
               $map['WspXs.Syid']=$syid ;
            }

            if(!empty($sContent))
            {
              $map['info.name']=array('LIKE',"%$sContent%");
            }

    
            if(!empty($daterange))  
            {
              list($start,$end) = explode(' - ',$daterange);    
              $start = str_replace('/','-',$start);            
              $end = str_replace('/','-',$end);   
              $map['WspXs.Rq']=array('BETWEEN',array($start,$end));
            }

                          
            $count=D('Spinfo')->getSpxscount($map);
                  
            $rows=10;
            $sql_page=ceil($count/$rows);   
            
            if($page<=0)   $page=1;       
            if($page>$sql_page) 
            {
              $page=1; 
            }
            else
            {

            }    

            $xstjdata = D('Spinfo')->getSpxstjList($map,$page,$rows);         
            $count     = $xstjdata['count'];
            $response = new \stdClass();
            $response->count       = $xstjdata['count'];//返回的数组的第一个字段记录总条数
            $response->nowPage     = $page ;              //每页显示的记录数目               
            $response->total       = ceil($xstjdata['count'] / $rows);    
            $response->tongji_list   = $xstjdata['tongji_list'] ;
            $response->tongji_count   = $xstjdata['tongji_count'] ;      
       

            $response->rows   = $xstjdata['list'] ;
            $this->ajaxReturn($response);
        }
                 
    }

}
