<?php

namespace Home\Controller;
class ChangeController extends CommonController {


    public function index()
    {   
        $this->display();          
    }

    public function getSpChangeinfo()
    {
        if(IS_AJAX){
		    $page = I('get.page',1,'int');
            $rows = I('get.rows',20,'int');
            $sidx = I('get.sidx','','string')?:'cTime';
            $sord = I('get.sord','','string')?:'desc';
        
            $daterange=I('get.daterange','','string');
            $HyCardNo=I('get.HyCardNo','','string');
            $Lx=I('get.lx',-1,'int');

            
            $map=array();
            $map['jfchange.WB_ID']=session('wbid');
      
           
            if(!empty($daterange))  
            {
              list($start,$end) = explode(' - ',$daterange);    
              $start = str_replace('/','-',$start);            
              $end = str_replace('/','-',$end);                
              $map['jfchange.cTime'] = array('BETWEEN',array($start,$end));
            }

            if(!empty($HyCardNo))
            {
            	$map['jfchange.HyCardNo']=array('LIKE','%'.$HyCardNo.'%');
            }

            if(($Lx==0)||($Lx==1))
            {
            	$map['jfchange.Lx']=$Lx;
            }

			$spInfos = D('Change')->getSpChangeinfoList($map,"$sidx $sord",$page,$rows);

           foreach ($spInfos['list'] as &$val)
            {                             
              $val['cTime'] = date('Y-m-d H:i:s',strtotime($val['cTime'])); 
            }
            $response = new \stdClass();
            $response->records = $spInfos['count'];
            $response->page = $page;
            $response->total = ceil($spInfos['count'] / $rows);
            foreach($spInfos['list'] as $key => $value)
            {
                $response->rows[$key]['id'] = $key;
                $response->rows[$key]['cell'] = $value;
            }

            $this->ajaxReturn($response);
			
        }
    }



}




