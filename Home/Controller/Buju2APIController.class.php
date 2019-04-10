<?php
namespace Home\Controller;
use Think\Controller;
class Buju2APIController extends Controller
{
	
	public  function API_getcplist()
	{			    
		$num= I('post.num','','string'); 
	    header("Access-Control-Allow-Origin:*"); 
	    $map['WB_ID']=1384;
		
		$num =(int) $num;
	    // $cpcount= D('Computerlist')->where($map)->count();
		$cplist=D('Computerlist')->Field('WB_ID,Name,Ip')->where($map)->limit($num)->select();	
		
		echo json_encode($cplist);								 				  
	}
	
	
	public  function API_testdata()
	{			    
		$num= I('post.num','','string'); 
	    header("Access-Control-Allow-Origin:*"); 
	    $map['WB_ID']=1037;
		$num=10;
		//$num =(int) $num;
	
		$cplist=D('Computerlist')->Field('WB_ID,Name,Ip')->where($map)->limit($num)->select();	
		
		echo json_encode($cplist);								 				  
	}
	
	
		  
}