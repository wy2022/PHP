<?php
namespace Home\Controller;
use Think\Controller;
class KehuController extends Controller
{	
	public  function  daochu()
	{

	//	$wbid=1702;
		
		
		
		$wbaccount=$_GET['wbaccount'];
		$wbaccount='zztzxswk';
		
		$wbid=D('WbInfo')->where(array('WbAccount'=>$wbaccount))->getField('WBID');
		
		//$wbid=1702;
		$begtime=I('get.begtime','','string');
		$endtime=I('get.endtime','','string');
		
		
		$map['wbid']=$wbid;
		$daterange = I('get.daterange','','string');//获取交班时间		
	    if(!empty($daterange))  
		{
		  list($start,$end) = explode(' - ',$daterange);    
		  $start = str_replace('/','-',$start);            
		  $end = str_replace('/','-',$end);   
		  $map['dtInsertTime']=array('BETWEEN',array($start,$end));
		}
		
		
		
	    $all_goods_list=D('Newproduct')->where(array('wbid'=>$wbid))->getField('goods_id,goods_name');
		
		
		
	//	$lastbegtime=date('Y-m-d H:i:S',strtotime($begtime));
       // $lastendtime=date('Y-m-d H:i:S',strtotime($endtime));
		
		
	//	$map['dtInsertTime']=array('BETWEEN',array($lastbegtime,$lastendtime));	
		
		
	    $xiaoshoulist=D('Newproductxsmx')->field('goods_id,sum(xiaoshou_num) as num')
		->group('goods_id')->where($map)->select();
		
		
		// echo  D('Newproductxsmx')->getLastSql();
		// return;
		
		foreach($xiaoshoulist as &$val)
		{   
			if(array_key_exists($val['goods_id'],$all_goods_list))
			{
				$val['goods_name']=$all_goods_list[$val['goods_id']];
			}
			else
			{
				$val['goods_name']='';
			}
			
			//$val['je']=sprintf("%.2f", $val['je']); 
			//$val['dtInsertTime']=date('Y-m-d H:i:s',strtotime($val['dtInsertTime']));								
		}
		
		//echo D('Newproductxsmx')->getLastSql();
//echo 3;
        $xlsName  = 'shift';
        $xlsCell  = array(
        array('goods_name','商品名称'),
         // array('goods_id','商品id'),
        array('num','销售数量'),
        );
		
      //   $xlsData  = xiaoshoulist;		 
         exportExcel($xlsName,$xlsCell,$xiaoshoulist);  


	//	 echo  json_encode($xiaoshoulist,JSON_UNESCAPED_UNICODE);
				   	   	     	   
	}
	
	/*
	
	public function getallxslist_zongzhang()
	{
		header('Access-Control-Allow-Origin:*');	
	  
		{
			$page = I('get.page',1,'int');
			$rows = I('get.rows',10,'int');
			$sord = I('get.sord','','string')?:'desc';
			$sidx = I('get.sidx','','string')?:'dtInsertTime';
			$daterange     = I('get.daterange','','string');
			
			$wbaccount='zztzxswk';		
		    $wbid=D('WbInfo')->where(array('WbAccount'=>$wbaccount))->getField('WBID');
																	  								 
			$map = array(); 
			$map['wbid']=$wbid;	
			
			if(!empty($daterange))  
			{
			  list($start,$end) = explode(' - ',$daterange);    
			  $start = str_replace('/','-',$start);            
			  $end = str_replace('/','-',$end);                
			  $map['dtInsertTime'] = array('BETWEEN',array($start,$end));
			}
			
	        
       		

											
			$count= D('Sptjview')->getallxstongji_mx_listByMap_count($map);
			
		
			
			$sql_page=ceil($count/$rows);  
			if($page>$sql_page) $page=1;	
			$wblist = D('Sptjview')->getallxstongji_mx_listByMap($map,"$sidx $sord",$page,$rows);	
			
			$all_goods_list=D('Newproduct')->field('goods_id,goods_name,kc_num')->where(array('wbid'=>2490))->select();	
			
			foreach($all_goods_list as &$val1)
			{
				$bFind=false;
				foreach($wblist['list'] as $val2)
				{
					if($val1['goods_id']==$val2['goods_id'])
					{
						$val1['num1']=$val2['num2'];
						bFind=true;
						break;
					}	
				}
			}

    		
			
	        $response = new \stdClass();
			$response->records = $wblist['count'];
			$response->page = $page;
			$response->total = ceil($wblist['count'] / $rows);
			foreach($wblist['list'] as $key => $value)
			{       
			  $response->rows[$key]['id'] = $key;
			  $response->rows[$key]['cell'] = $value;
			}
			$this->ajaxReturn($response);
		}  
	}
	*/
	
	public function sumsptj()
    { 
      header('Access-Control-Allow-Origin:*'); 
	  
         //   echo json_encode($all_goods_list);
       $wbaccount=$_GET['wbaccount'];
$wbaccount='zztzxswk';	   
       session('goods_wbaccount',$wbaccount);	   
      $this->display();               
    }
	
	
	public function getallxslist_zongzhang()
	{
		header('Access-Control-Allow-Origin:*');	
	  
		{
			$page = I('get.page',1,'int');
			$rows = I('get.rows',10,'int');
			$sord = I('get.sord','','string')?:'desc';
			$sidx = I('get.sidx','','string')?:'dtInsertTime';
			$daterange     = I('get.daterange','','string');
			
			$wbaccount=session('goods_wbaccount');	
			
			//$wbaccount='jywk';
            			
		    $wbid=D('WbInfo')->where(array('WbAccount'=>$wbaccount))->getField('WBID');
							
  					
			$map = array(); 
			$map['wbid']=$wbid;	
			
			if(!empty($daterange))  
			{
			  list($start,$end) = explode(' - ',$daterange);    
			  $start = str_replace('/','-',$start);            
			  $end = str_replace('/','-',$end);                
			  $map['dtInsertTime'] = array('BETWEEN',array($start,$end));
			}
			
	        
       		
		    $all_goods_list=D('Newproduct')->field('goods_id,goods_name,kc_num')->where(array('wbid'=>$wbid))->select();	
		    $count= count($all_goods_list);							
			
			$sql_page=ceil($count/$rows);  
			if($page>$sql_page) $page=1;
	
		
			
			
			$wblist = D('Sptjview')->getallxstongji_mx_listByMap($map,"$sidx $sord",$page,$rows);	
			
	
			
			foreach($all_goods_list as &$val1)
			{
				$bFind=false;
				foreach($wblist['list'] as $val2)
				{
					if($val1['goods_id']==$val2['goods_id'])
					{
						$val1['sjsl']=$val2['sjsl'];
						$val1['xiaoshounum']=$val2['xiaoshounum'];
						$val1['kc_num']=$val2['kc_num'];
						$bFind=true;
						break;
					}	
				}
				if($bFind==false)
				{
					$val1['sjsl']=0;
					$val1['xiaoshounum']=0;
				}	
				
			}

    		
			
	        $response = new \stdClass();
			$response->records = count($all_goods_list);
			$response->page = $page;
			$response->total = count($all_goods_list);
			foreach($all_goods_list as $key => $value)
			{       
			  $response->rows[$key]['id'] = $key;
			  $response->rows[$key]['cell'] = $value;
			}
			$this->ajaxReturn($response);
		}  
	}
	
	
	// public  function  daochu()
	// {
		
		// $map['wbid']=$wbid;
		// $daterange = I('get.daterange','','string');//获取交班时间		
	    // if(!empty($daterange))  
		// {
		  // list($start,$end) = explode(' - ',$daterange);    
		  // $start = str_replace('/','-',$start);            
		  // $end = str_replace('/','-',$end);   
		  // $map['dtInsertTime']=array('BETWEEN',array($start,$end));
		// }
				
	    // $all_goods_list=D('Newproduct')->where(array('wbid'=>$wbid))->getField('goods_id,goods_name');		
	    // $xiaoshoulist=D('Newproductxsmx')->field('goods_id,sum(xiaoshou_num) as num')
		// ->group('goods_id')->where($map)->select();
				
		// foreach($xiaoshoulist as &$val)
		// {   
			// if(array_key_exists($val['goods_id'],$all_goods_list))
			// {
				// $val['goods_name']=$all_goods_list[$val['goods_id']];
			// }
			// else
			// {
				// $val['goods_name']='';
			// }							
		// }
		
        // $xlsName  = 'shift';
        // $xlsCell  = array(
        // array('goods_name','商品名称'),
        // array('num','销售数量'),
        // ); 
         // exportExcel($xlsName,$xlsCell,$xiaoshoulist);  			   	   	     	   
	// }

	

		  
}