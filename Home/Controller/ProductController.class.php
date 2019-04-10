<?php
namespace Home\Controller;
use Think\Controller;
class ProductController extends Controller
{
	//用户列表
	public function index()
	{		
	   
	    $hycardno = $_GET['hycardno'];
	    $wbaccount= $_GET['wbaccount']; 
	    $cpname   = $_GET['cpname'];
	    $bgs      = $_GET['Bgs'];
	    $guid      = $_GET['guid'];
		
		
		
		
		
	    
 
		if(!empty($bgs))
		{
			header('Location: https://wx1.sinaimg.cn/large/6bdf06f1ly1fgijz3orv1j21hc0xcn3s.jpg');
			return;		
		}	  
		
		
	
 	    $yuming_id = C('YUMING_ID');
	    $yuming_url_fen= C('YUMING_URL_FEN');
	    $yuming_url_zong=C('YUMING_URL_ZONG');
		
		if(empty($wbaccount))
		{		
			$this->display('moban1');
			return;		
		}	
		
	    
		$wbid=D('WbInfo')->where(array('WbAccount'=>$wbaccount))->getField('WBID');	
		
	

		
		$khd_sp_buy=  D('Webini')->getWebIniByWbid($wbid,'khd_sp_buy'); 
		$khd_wxzfb_buy=D('Webini')->getWebIniByWbid($wbid,'khd_wxzfb_buy'); 
		
  
	

		if($khd_wxzfb_buy==='0')
		{
		   $khd_wxzfb_buy=0;  
		}
		else
		{
		  $khd_wxzfb_buy=1; 	
		}	
		        
		$this->assign ( 'hycardno', $hycardno);	 
		$this->assign ( 'wbaccount', $wbaccount);	 
		$this->assign ( 'cpname', $cpname);	 
		$this->assign ( 'bgs', $bgs);	 
		$this->assign ( 'guid', $guid);	
        $this->assign ( 'yuming_id', $yuming_id );		
		$this->assign ( 'yuming_url_fen', $yuming_url_fen ); 
        $this->assign ( 'yuming_url_zong', $yuming_url_zong );	
		
				
		$this->assign ( 'khd_sp_buy', $khd_sp_buy);	 
		$this->assign ( 'qx_khd_wxzfb_buy', $khd_wxzfb_buy);
			
		if($khd_sp_buy==='0')
		{
		   $this->display('moban1');
		}
		else
		{
			
		}	
	    $this->display();
		
	}

	public function getgoodsinfolist()
	{
		
		$wbaccount=I('post.wbaccount','','string');
        $pageNum=I('post.pageNum','','string');	
		$type_id=I('post.type_id','','string');
		
		
		  
		$wbid=D('WbInfo')->where(array('WbAccount'=>$wbaccount))->getField('WBID');
		
		if(empty($wbid))
		{
			$data['result']=-1;
			$data['message']='无权限';
		}
		else
        {
		  $map['info.wbid']=$wbid;
		  $pageSize = 10; //每页显示数
		  $page_beg = $pageNum*$pageSize; //开始记录 
		    
		  $page_end = $startPage+$pageSize; 
		   
		   
		  $data['list']=D('Productkc')->getAllChuhuokucunfoListByMap3($map,$page_beg,$page_end,$type_id);
		  
		 
		  
		  $count = D('Productkc')->getAllChuhuokucunfoListByMap3_count($map,$type_id);
		  
		  $total = $count;//总记录数 
		 
		  $totalPage = ceil($total/$pageSize); //总页数 
		  
		  $data['total']=$total;
		  $data['pageSize']=$pageSize;
		  $data['totalPage']=$totalPage;
		  
		  
		}			
 
		echo  json_encode($data);
		
	}	
	
	
	
	

 
}
