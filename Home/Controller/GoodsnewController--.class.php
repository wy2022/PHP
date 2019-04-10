<?php
namespace Home\Controller;
class GoodsnewController extends CommonController {

    public function check_newcs_qx()
	{
		//判断新超市权限
	    $exe_sp_version=D('Webini')->where(array('wbid'=>session('wbid'),'skey'=>'exe_sp_version'))->getField('svalue');
		if($exe_sp_version==1)
		{
			$exe_sp_version=1;
			return true;
		}
		else
        {
			$exe_sp_version=0;
			return false;
		}			
		
	}
    public function index()
    { 
	  $bOpen=$this->check_newcs_qx();
	  if($bOpen===false)
	  {		  
		  $this->redirect('rate/fujia', array('cate_id' => 2), 1, '请在附加设置里开启新超市权限...');	  
	  }	  	  	
	  $typelist=D('ProductType')->select();
	  $this->assign('typelist',$typelist);
      $this->display();              
    }
	
	public function getkcinfo()
    {      
        if(IS_AJAX)
        {       
            $page = I('get.page',1,'int');
            $rows = I('get.rows',15,'int');
            $sord = I('get.sord','','string')?:'desc';
            $sidx = I('get.sidx','','string')?:'id';
            $map = array();           
            $map['wbid']=session('wbid');
			$type_id = I('get.type_id','','string');
			$sContent = I('get.sContent','','string');
			
			


            if(!empty($sContent ))
            {
               $map['goods_name|barcode']= array('LIKE',"%$sContent%");
            }   

            if(!empty($type_id ))
            {
               $map['type_id']= $type_id;
            }   
			
            $count=D('Newproduct')->getProductinfoListByMap_count($map);
    
            $rows=15;
            $sql_page=ceil($count/$rows);   
            
            if($page<=0)   $page=1;       
            if($page>$sql_page) 
            {
              $page=1; 
            }else
            {

            }    
          
            $kctjdata = D('Newproduct')->getProductinfoListByMap($map,"$sidx $sord",$page,$rows);         
            $count     = $kctjdata['count'];
            $response = new \stdClass();
            $response->count       = $kctjdata['count'];//返回的数组的第一个字段记录总条数
            $response->nowPage     = $page ;              //每页显示的记录数目               
            $response->total       = ceil($kctjdata['count'] / $rows);          
       

            $response->rows   = $kctjdata['list'] ;
            $this->ajaxReturn($response);
        }
                 
    }
	
	
	//================================进货明细==========================
	public function jhtj()
    {    
	  $bOpen=$this->check_newcs_qx();
	  if($bOpen===false)
	  {		  
		  $this->redirect('rate/fujia', array('cate_id' => 2), 1, '请在附加设置里开启新超市权限...');	 	  
	  }	
      $wbid=session('wbid'); 
      $yglist=D('Yuangong')->field('id,name')->where(array('WB_ID'=>$wbid))->select();
      $this->assign('yglist',$yglist);
      $this->display();              
    }
		
	public function getjhtjmx()
    {       
        if(IS_AJAX)
        {              
            $page = I('get.page',1,'int');
            $rows = I('get.rows',15,'int');
            $sord = I('get.sord','','string')?:'desc';
            $sidx = I('get.sidx','','string')?:'id';
           
            $daterange = I('get.daterange','','string');//获取交班时间
            $shangxiatype =    I('get.shangxiatype','0','string');
            $sContent =    I('get.sContent','','string');
			$operate =     I('get.operate','','string');


            $wbid= session('wbid');
            if(empty($wbid))
            {
              $wbid =-1;
            }	
            $map = array();    
            if(!empty($wbid))
            {
              $map['wbid']= $wbid;
            } 
			
			
			if(!empty($shangxiatype))
			{
				if($shangxiatype==1)
				{
					$map['shangxia_status']=0;
				}else if($shangxiatype==2)
                {
					$map['shangxia_status']=1;
				}					
			}	

           	if(!empty($operate ))
            {
			   $name=D('Yuangong')->where(array('WB_ID'=>session('wbid'),'id'=>$operate))->getField('name');	
               $map['operate']= array('LIKE',"%$name%");
            }  

            if(!empty($sContent))
            {
              $map['goods_name']=array('LIKE',"%$sContent%");
            }


            if(!empty($daterange))  
            {
              list($start,$end) = explode(' - ',$daterange);    
              $start = str_replace('/','-',$start);            
              $end = str_replace('/','-',$end);   
              $map['dtInsertTime']=array('BETWEEN',array($start,$end));
            }
    
            $count=D('Newproductsxj')->getsxjinfoListByMap_count($map);  

            $rows=15;
            $sql_page=ceil($count/$rows);   
            
            if($page<=0)   $page=1;       
            if($page>$sql_page) 
            {
              $page=1; 
            }
            else
            {

            }    

            $jhtjdata = D('Newproductsxj')->getsxjinfoListByMap($map,"$sidx $sord",$page,$rows);  
			
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
    //================================进货明细==========================
	
	
	
	//==============================销售明细===========================

	
	public function xstj()
    { 
	   $bOpen=$this->check_newcs_qx();
	  if($bOpen===false)
	  {		  
		  $this->redirect('rate/fujia', array('cate_id' => 2), 1, '请在附加设置里开启新超市权限...');	  
	  }	
      $wbid= session('wbid');
      $yglist=D('Yuangong')->field('id,name')->where(array('WB_ID'=>$wbid))->select();
      $this->assign('yglist',$yglist);
      $this->display();               
    }
	
	
	public function getxstjmx()
    {          
        if(IS_AJAX)
        {       
            $page = I('get.page',1,'int');
            $rows = I('get.rows',20,'int');
            $sord = I('get.sord','','string')?:'desc';
            $sidx = I('get.sidx','','string')?:'id';

            $sContent = I('get.sContent','','string');
            $daterange = I('get.daterange','','string');
			$ordertype = I('get.ordertype','','string');
			$paytype = I('get.paytype','','string');
			$operate = I('get.operate','','string');
			

            $wbid= session('wbid');
            if(empty($wbid))
            {
              $wbid =-1;
            } 

            $map = array();  
            if(!empty($wbid))
            {
               $map['wbid']= session('wbid');
            } 
              
            if(!empty($ordertype))  
            {
               $map['ordertype']=$ordertype ;
            }
			
			if(!empty($operate ))
            {
			   $name=D('Yuangong')->where(array('WB_ID'=>session('wbid'),'id'=>$operate))->getField('name');	
               $map['operate']= array('LIKE',"%$name%");
            } 
			
			if(!empty($paytype))  
            {
               $map['paytype']=$paytype ;
            }


            if(!empty($sContent ))
            {
               $map['info|post_order_no']= array('LIKE',"%$sContent%");
            }    
    
            if(!empty($daterange))  
            {
              list($start,$end) = explode(' - ',$daterange);    
              $start = str_replace('/','-',$start);            
              $end = str_replace('/','-',$end);   
              $map['dtInsertTime']=array('BETWEEN',array($start,$end));
            }
                 
                            
            $count=D('Newproductxs')->getxstjlistByMap_count($map);
            $rows=10;
            $sql_page=ceil($count/$rows);   
            
            if($page<=0)   $page=1;       
            if($page>$sql_page) 
            {
              $page=1; 
            }else
            {

            }    

            $spxsdata = D('Newproductxs')->getxstjlistByMap($map,"$sidx $sord",$page,$rows);             
            $count     = $spxsdata['count'];

          
            $response = new \stdClass();
            $response->count       = $spxsdata['count'];//返回的数组的第一个字段记录总条数
            $response->nowPage     = $page ;              //每页显示的记录数目               
            $response->total       = ceil($spxsdata['count'] / $rows);          
           
		    $sumje=0;
			$sum_nocash_je=0;
			$sum_cash_je=0;
			$sum_other_je=0;
			
			$sum_xs_num=0;
			$sum_jbxs_num=0;
			$sum_th_num=0;
			foreach($spxsdata['list'] as &$val)
			{
				if($val['paytype']==1)
				{
					$sum_cash_je+=$val['sum_sr_je'];
				}
				else  if($val['paytype']==2)
                {
					$sum_nocash_je+=$val['sum_sr_je'];
				}
				else  if($val['paytype']==3)
                {
					$sum_other_je+=$val['sum_sr_je'];
				}	
                 
				if($val['ordertype']==1)
				{
					$sum_xs_num+=$val['sum_num'];
				}
				else  if($val['ordertype']==2)
                {
					$sum_jbxs_num+=$val['sum_num'];
				}
				else  if($val['ordertype']==3)
                {
					$sum_th_num+=$val['sum_num'];
				} 
				
			}
			$sumje=	$sum_nocash_je+$sum_cash_je+$sum_other_je;
        
 
            $response->rows   = $spxsdata['list'] ;
			
			$response->sumje   = $sumje ;
			$response->sum_cash_je   = $sum_cash_je ;
			$response->sum_nocash_je   = $sum_nocash_je ;
			$response->sum_other_je   = $sum_other_je ;
			$response->sum_xs_num   = $sum_xs_num ;
			$response->sum_th_num   = $sum_th_num;
			$response->sum_jbxs_num   = $sum_jbxs_num ;
            $this->ajaxReturn($response);
        }
                 
    }
	

     //销售明细
	public function xiaoshoumx()
	{
		$bOpen=$this->check_newcs_qx();
		if($bOpen===false)
		{		  
			$this->redirect('rate/fujia', array('cate_id' => 2), 1, '请在附加设置里开启新超市权限...');	 	  
		}	
		$id=I('get.id','','string');
		session('xiaoshou_id',$id); 		
		$this->display();
	}
		
	public function getxstongji_mx_listByMap()
	{
	
		$wbid=session('wbid');
		$xiaoshou_id=session('xiaoshou_id');
		$xiaoshoumx_str=D('Newproductxs')->where(array('wbid'=>session('wbid'),'id'=>$xiaoshou_id))->getField('detailinfo');
        $type_list=D('ProductType')->select();
		$list=json_decode($xiaoshoumx_str,true);
		foreach($list as &$val)
		{																											
			foreach($type_list as $val1)
			{
				if($val1['type_id']==$val['type_id'])
				{
					$val['type_name']=$val1['type_name'];	
					break;
				}	
			}				
		}
        $this->ajaxReturn($list);		  
	}	
		
	//============================销售明细=============================
	
	
	//===========================交班日志================================
	public function jbtj()
    { 
	  $bOpen=$this->check_newcs_qx();
	  if($bOpen===false)
	  {		  
		  $this->redirect('rate/fujia', array('cate_id' => 2), 1, '请在附加设置里开启新超市权限...');	  
	  }	
      $wbid= session('wbid');
      $yglist=D('Yuangong')->field('id,name')->where(array('WB_ID'=>$wbid))->select();
      $this->assign('yglist',$yglist);
      $this->display();               
    }
  

    public function getjblog()
    {  
        
        if(IS_AJAX)
        {       
            $page = I('get.page',1,'int');
            $rows = I('get.rows',15,'int');
            $sord = I('get.sord','','string')?:'desc';
            $sidx = I('get.sidx','','string')?:'id';

            $sContent = I('get.sContent','','string');
            $daterange = I('get.daterange','','string');
			
			$operate_db = I('get.operate_db','','string');
			$operate_jb = I('get.operate_jb','','string');
			

  
            $wbid= session('wbid');
            if(empty($wbid))
            {
              $wbid =-1;
            } 

            $map = array();  
            if(!empty($wbid))
            {
               $map['wbid']= session('wbid');
            } 

          
            if(!empty($sContent ))
            {
               $map['bz']= array('LIKE',"%$sContent%");
            }    

			if(!empty($operate_db ))
            {
			   $name=D('Yuangong')->where(array('WB_ID'=>session('wbid'),'id'=>$operate_db))->getField('name');	
               $map['operate_db']= array('LIKE',"%$name%");
            } 

			if(!empty($operate_jb ))
            {
               $name=D('Yuangong')->where(array('WB_ID'=>session('wbid'),'id'=>$operate_jb))->getField('name');	
               $map['operate_jb']= array('LIKE',"%$name%");
            } 
           
      
            if(!empty($daterange))  
            {
              list($start,$end) = explode(' - ',$daterange);    
              $start = str_replace('/','-',$start);            
              $end = str_replace('/','-',$end);   
              $map['dtEndTime']=array('BETWEEN',array($start,$end));
            }
                 
    
          //   处理分页               
            $count=D('Newproductjb')->getjbtongjilistByMap_count($map);
            $rows=15;
            $sql_page=ceil($count/$rows);   
            
            if($page<=0)   $page=1;       
            if($page>$sql_page) 
            {
              $page=1; 
            }else
            {

            }    

            $spjhdata = D('Newproductjb')->getjbtongjilistByMap($map,"$sidx $sord",$page,$rows);  			
            $count     = $spjhdata['count'];

            $response = new \stdClass();
            $response->count       = $spjhdata['count'];
            $response->nowPage     = $page ;                          
            $response->total       = ceil($spjhdata['count'] / $rows);          

            $response->rows   = $spjhdata['list'] ;
            $this->ajaxReturn($response);
        }
                 
    }
	
	//-------------------------交班明细--------------------------------
	public function jiaobanmx()
	{
		$id=I('get.id','','string');
		session('jiaoban_id',$id); 				
		$this->display();
	}
	
	//-------------------------交班明细---------------------------------
	
	//===========================交班日志================================
	
	//==========================商品统计========================================
	public function sptj()
    { 
	  $bOpen=$this->check_newcs_qx();
	  if($bOpen===false)
	  {		  
		 $this->redirect('rate/fujia', array('cate_id' => 2), 1, '请在附加设置里开启新超市权限...');	   
	  }	
      $wbid= session('wbid');
	  $typelist=D('ProductType')->select();
	  $this->assign('typelist',$typelist);
      $yglist=D('Yuangong')->field('id,name')->where(array('WB_ID'=>$wbid))->select();
      $this->assign('yglist',$yglist);
      $this->display();               
    }
  

     public function getxstongjilist_zongzhang()
	{
	    if(IS_AJAX)
		{
			$page = I('get.page',1,'int');
			$rows = I('get.rows',10,'int');
			$sord = I('get.sord','','string')?:'desc';
			$sidx = I('get.sidx','','string')?:'dtInsertTime';
						
			
			$sContent = I('get.sContent','','string');
            $daterange = I('get.daterange','','string');
			$ordertype = I('get.ordertype','','string');
			$paytype = I('get.paytype','','string');
			$operate = I('get.operate','','string');
			$type_id = I('get.type_id','','string');
									  								 
			$map = array(); 
			$map['wbid']=session('wbid');	
			
			if(!empty($daterange))  
			{
			  list($start,$end) = explode(' - ',$daterange);    
			  $start = str_replace('/','-',$start);            
			  $end = str_replace('/','-',$end);                
			  $map['dtInsertTime'] = array('BETWEEN',array($start,$end));
			}
			
			if(!empty($ordertype))  
            {
               $map['ordertype']=$ordertype ;
            }
			if(!empty($type_id))  
            {
               $map['type_id']=$type_id ;
            }
			
			
			
			if(!empty($operate ))
            {
			   $name=D('Yuangong')->where(array('WB_ID'=>session('wbid'),'id'=>$operate))->getField('name');	
               $map['operate']= array('LIKE',"%$name%");
            } 
			
			if(!empty($paytype))  
            {
               $map['paytype']=$paytype ;
            }


            if(!empty($sContent ))
            {
               $map['goods_name|post_order_no']= array('LIKE',"%$sContent%");
            }  
			
																	
			$count= D('Newproductxsmx')->getxstongji_mx_listByMap_count($map);
			$sql_page=ceil($count/$rows);  
			if($page>$sql_page) $page=1;	
			$wblist = D('Newproductxsmx')->getxstongji_mx_listByMap($map,"$sidx $sord",$page,$rows);	


		    $sumje=0;
			$sum_nocash_je=0;
			$sum_cash_je=0;
			$sum_other_je=0;
			
			$sum_xs_num=0;
			$sum_jbxs_num=0;
			$sum_th_num=0;
			
		   $xsmx_goods_list=D('Newproductxsmx')->field('goods_id,paytype,ordertype,sum(je) as je,sum(xiaoshou_num) as xiaoshou_num')->group('goods_id,ordertype,paytype')->where($map)->select();			
		//   print_r(json_encode($xsmx_goods_list));
		   foreach($xsmx_goods_list as &$val)	
           {
			    
			    if($val['ordertype']==1) //销售
				{
					$sum_xs_num+=$val['xiaoshou_num'];
				}
				else  if($val['ordertype']==2)  //交班销售
				{
					$sum_jbxs_num+=$val['xiaoshou_num'];
				}
				else  if($val['ordertype']==3)  //退货
				{
					$sum_th_num+=$val['xiaoshou_num'];
				}

                if($val['paytype']==1) //现金
				{
					$sum_cash_je+=$val['je'];
				}
				else  if($val['ordertype']==2)  //非现金
				{
					$sum_nocash_je+=$val['je'];
				}
				else  if($val['ordertype']==3)  //其他
				{
					$sum_other_je+=$val['je'];
				}			
		   }  		
		


            $sumje=	$sum_nocash_je+$sum_cash_je+$sum_other_je;               				
	        $response = new \stdClass();
			$response->records = $wblist['count'];
			$response->page = $page;
			$response->total = ceil($wblist['count'] / $rows);
			
			$response->sumje   = $sumje ;
			$response->sum_cash_je   = $sum_cash_je ;
			$response->sum_nocash_je   = $sum_nocash_je ;
			$response->sum_other_je   = $sum_other_je ;
			$response->sum_xs_num   = $sum_xs_num ;
			$response->sum_th_num   = $sum_th_num;
			$response->sum_jbxs_num   = $sum_jbxs_num ;
			foreach($wblist['list'] as $key => $value)
			{       
			  $response->rows[$key]['id'] = $key;
			  $response->rows[$key]['cell'] = $value;
			}
			$this->ajaxReturn($response);
		}  
	}
	
	
	
	//=====================================进出货页面开始=================================================
	public function jinhuo()
	{
		session('goods_id_list',null);
		session('plch_status','2');
		$wbid=session('wbid');	
		
		$map['wbid']=$wbid;
		$map['deleted'] =0;	
		$goodslist=D('Newproduct')->where($map)->select();	
		foreach($goodslist as &$val)
		{
		  $val['value']=$val['goods_pinyin'].','.$val['goods_name'].','.$val['goods_quanpin'];
		  $val['label']=$val['goods_pinyin'].','.$val['goods_name'].','.$val['goods_quanpin'];
		  $val['hj_num']=$val['kc_num'];	
          $val['one_jian_jin_price']=0;	
		  $val['one_ge_jin_price']=0;	
		  
		  
		}
		$this->assign('goodslist',json_encode($goodslist));	
        
        creatToken();		
		$this->display();
	}
	
	
	
	
	//===============================进货页面====================
	public function jinhuo_edit_set()
	{
		if(IS_AJAX)
		{    
	        if(!checkToken($_POST['token']))
			{  
		        writelog('jinhuo_edit_set---重复提交');
				$data['status']=-2;          								
			    $this->ajaxReturn($data);
                return;   			   
			}
			else
			{
				writelog('jinhuo_edit_set---未重复提交');
			}
	        $wbid=session('wbid');
	        $post_order_no = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);  //网站订单号
			$post_order_no='JH'.$post_order_no;
            $dtInsertTime=date('Y-m-d H:i:s',time());     			
			$str=I('post.goodsinfo','','string');
			
			$unit=I('post.unit','','string');	 //按件按个
			$sumje=I('post.sumje','','string');	
			
            $str=htmlspecialchars_decode($str); 		
			$jinhuo_goodslist=json_decode($str,true);									
			if(empty($jinhuo_goodslist))
			{	
              $data['status']=-1;
			  $this->ajaxReturn($data);
			  return;	
			}	
			
			
			$all_googs_list=array();
			$all_googs_list=D('Newproduct')->field('goods_id,goods_name,ck_num,kc_num')->where(array('wbid'=>$wbid))->select();
	
			$info=' 总金额：'.$sumje.',';
			$result=true;
			D()->startTrans();  //启用事务
			
			foreach( $jinhuo_goodslist as $val)
			{
				$jinhuomx_insert_data['goods_id']=$val['goods_id'];
				$jinhuomx_insert_data['changenum']  =$val['sumnum'];
				
				foreach($all_googs_list as $val2)
				{
					if($val2['goods_id']==$val['goods_id'])
					{
						$jinhuomx_insert_data['old_hj_num']  =$val2['kc_num'];
				        $jinhuomx_insert_data['old_ck_num']  =$val2['ck_num'];
                        $goods_name=$val2['goods_name']	;					
						break;
					}	
				}	
													 				
				$jinhuomx_insert_data['price']=$val['price'];					
				$jinhuomx_insert_data['sumje']=$val['price']*$val['sumnum'];
				$jinhuomx_insert_data['post_order_no']=$post_order_no;
				$jinhuomx_insert_data['jch_type']=1;
				$jinhuomx_insert_data['wbid']=$wbid;
				$jinhuomx_insert_data['dtInsertTime']=$dtInsertTime;
				$jinhuomx_insert_data['operate']=session('username');
	
				if(D('Newproductjchmx')->add($jinhuomx_insert_data)===false)
				{
					$result=false;					
				}
				
				//直接存到该商品的仓库里
				if((D('NewProduct')->where(array('wbid'=>$wbid,'goods_id'=>$val['goods_id']))->setInc('ck_num',$val['sumnum']))===false)
				{
					$result=false;					
				}																							              
				$info.= $goods_name.':'.$val['sumnum'].'个'.' ';				  					               								
			}
			
			//=========================添加所有的组合商品的明细数据=================================
				
			//更新库存表	
			$jinhuo_insert_data['post_order_no']=$post_order_no;
			$jinhuo_insert_data['jch_type']=1;
			$jinhuo_insert_data['wbid']=$wbid;
			$jinhuo_insert_data['info']=$info;		
			$jinhuo_insert_data['dtInsertTime']=$dtInsertTime;
			$jinhuo_insert_data['sumje']=$sumje;
			$jinhuo_insert_data['bz']=I('post.bz','','string');
			$jinhuo_insert_data['operate']=session('username');
			
			if(D('Newproductjch')->add($jinhuo_insert_data)===false)
			{
				writelog('----11------');
				$result=false;
			}	
					
			if($result)
            {
			  writelog('----12------');
              D()->commit();  //提交事务          
               $data['status']=1;
            }
            else
            {
              D()->rollback();    //回滚
              $data['status']=-1;
            }
								
			$this->ajaxReturn($data);
		}	
	}
	
	
	
	public function getcktjmx()
    {       
        if(IS_AJAX)
        {              
            $page = I('get.page',1,'int');
            $rows = I('get.rows',15,'int');
            $sord = I('get.sord','','string')?:'desc';
            $sidx = I('get.sidx','','string')?:'id';
           
            $daterange = I('get.daterange','','string');//获取交班时间
            $jch_type =    I('get.jch_type','0','string');
            $sContent =    I('get.sContent','','string');
			$operate =     I('get.operate','','string');


            $wbid= session('wbid');
            if(empty($wbid))
            {
              $wbid =-1;
            }	
            $map = array();    
            if(!empty($wbid))
            {
              $map['wbid']= $wbid;
            } 
			
			
		    if(!empty($jch_type ))
            {
	
               $map['jch_type']= $jch_type;
            } 

           	if(!empty($operate ))
            {
			   $name=D('Yuangong')->where(array('WB_ID'=>session('wbid'),'id'=>$operate))->getField('name');	
               $map['operate']= array('LIKE',"%$name%");
            }  

            if(!empty($sContent))
            {
              $map['goods_name']=array('LIKE',"%$sContent%");
            }


            if(!empty($daterange))  
            {
              list($start,$end) = explode(' - ',$daterange);    
              $start = str_replace('/','-',$start);            
              $end = str_replace('/','-',$end);   
              $map['dtInsertTime']=array('BETWEEN',array($start,$end));
            }
    
            $count=D('Newproductjch')->gejhtongjilistByMap_count($map);  

            $rows=15;
            $sql_page=ceil($count/$rows);   
            
            if($page<=0)   $page=1;       
            if($page>$sql_page) 
            {
              $page=1; 
            }
            else
            {

            }    

            $jhtjdata = D('Newproductjch')->gejhtongjilistByMap($map,"$sidx $sord",$page,$rows);  
			
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
	
	public function chuhuo()
	{
		session('goods_id_list',null);		
		session('plch_status','3');

		$wbid=session('wbid');
		if(empty($wbid))
		{
			echo  'error';
			return;
		}	
		$map=array();
		$map['deleted']=0;
        $map['wbid']=$wbid;
		$map['ck_num']=array('gt',0);
		
	    $goodslist=D('Newproduct')->field('goods_id,goods_name,ck_num,kc_num')->where($map)->select();
		foreach($goodslist as &$val)
		{
		  $val['value']=$val['goods_pinyin'].','.$val['goods_name'].','.$val['goods_quanpin'];
		  $val['label']=$val['goods_pinyin'].','.$val['goods_name'].','.$val['goods_quanpin'];
		  $val['hj_num']=$val['kc_num'];	
          $val['one_jian_jin_price']=0;	
		  $val['one_ge_jin_price']=0;	
		  
		  
		}
		$this->assign('goodslist',json_encode($goodslist));	
        creatToken();		
		$this->display();
	}
	
	
	
	public function chuhuo_edit_set()
	{
		if(IS_AJAX)
		{    
	        if(!checkToken($_POST['token']))
			{  
		        writelog('jinhuo_edit_set---重复提交');
				$data['status']=-2;          								
			    $this->ajaxReturn($data);
                return;   			   
			}
			else
			{
				writelog('jinhuo_edit_set---未重复提交');
			}
	        $wbid=session('wbid');
	        $post_order_no = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);  //网站订单号
			$post_order_no='JH'.$post_order_no;
            $dtInsertTime=date('Y-m-d H:i:s',time());     			
			$str=I('post.goodsinfo','','string');
			
			$unit=I('post.unit','','string');	 //按件按个
			$sumje=I('post.sumje','','string');	
			
            $str=htmlspecialchars_decode($str); 		
			$jinhuo_goodslist=json_decode($str,true);									
			if(empty($jinhuo_goodslist))
			{	
              $data['status']=-1;
			  $this->ajaxReturn($data);
			  return;	
			}	
			
			
			$all_googs_list=array();
			$all_googs_list=D('Newproduct')->field('goods_id,goods_name,ck_num,kc_num')->where(array('wbid'=>$wbid))->select();
	
			$info=' 总金额：'.$sumje.',';
			$result=true;
			D()->startTrans();  //启用事务
			
			foreach( $jinhuo_goodslist as $val)
			{
				$jinhuomx_insert_data['goods_id']=$val['goods_id'];
				$jinhuomx_insert_data['changenum']  =$val['sumnum'];
				
				foreach($all_googs_list as $val2)
				{
					if($val2['goods_id']==$val['goods_id'])
					{
						$jinhuomx_insert_data['old_hj_num']  =$val2['kc_num'];
				        $jinhuomx_insert_data['old_ck_num']  =$val2['ck_num'];
                        $goods_name=$val2['goods_name']	;					
						break;
					}	
				}	
													 				
				$jinhuomx_insert_data['price']=$val['price'];					
				$jinhuomx_insert_data['sumje']=$val['price']*$val['sumnum'];
				$jinhuomx_insert_data['post_order_no']=$post_order_no;
				$jinhuomx_insert_data['jch_type']=2;
				$jinhuomx_insert_data['wbid']=$wbid;
				$jinhuomx_insert_data['dtInsertTime']=$dtInsertTime;
				$jinhuomx_insert_data['operate']=session('username');
	
				if(D('Newproductjchmx')->add($jinhuomx_insert_data)===false)
				{
					$result=false;					
				}
				
				//直接存到该商品的仓库里
				if((D('NewProduct')->where(array('wbid'=>$wbid,'goods_id'=>$val['goods_id']))->setDec('ck_num',$val['sumnum']))===false)
				{
					$result=false;					
				}																							              
				$info.= $goods_name.':'.$val['sumnum'].'个'.' ';				  					               								
			}
			
			//=========================添加所有的组合商品的明细数据=================================
				
			//更新库存表	
			$jinhuo_insert_data['post_order_no']=$post_order_no;
			$jinhuo_insert_data['jch_type']=2;
			$jinhuo_insert_data['wbid']=$wbid;
			$jinhuo_insert_data['info']=$info;		
			$jinhuo_insert_data['dtInsertTime']=$dtInsertTime;
			$jinhuo_insert_data['sumje']=$sumje;
			$jinhuo_insert_data['bz']=I('post.bz','','string');
			$jinhuo_insert_data['operate']=session('username');
			
			if(D('Newproductjch')->add($jinhuo_insert_data)===false)
			{
				writelog('----11------');
				$result=false;
			}	
					
			if($result)
            {
			  writelog('----12------');
              D()->commit();  //提交事务          
               $data['status']=1;
            }
            else
            {
              D()->rollback();    //回滚
              $data['status']=-1;
            }
								
			$this->ajaxReturn($data);
		}	
	}
	
	
	public function chuhuomx()
	{
		$post_order_no=I('get.post_order_no','','string');
		$jch_type=I('get.jch_type','','string');
		
		session('post_order_no',$post_order_no); 
        session('jch_type',$jch_type); 		
		$this->display();
	}
	
	public function jinhuomx()
	{
		$post_order_no=I('get.post_order_no','','string');
		session('post_order_no',$post_order_no); 
		
        $jch_type=I('get.jch_type','','string'); 
        session('jch_type',$jch_type); 	
		
		$this->display();
	}
	
    public function getjchtongji_mx_listByMap()
	{
		if(IS_AJAX)
		{
			$page = I('get.page',1,'int');
			$rows = I('get.rows',20,'int');
			$sord = I('get.sord','','string')?:'desc';
			$sidx = I('get.sidx','','string')?:'dtInsertTime';


							 
			$map = array(); 	
			$map['jhmx.wbid']=session('wbid');
			$map['jhmx.post_order_no']= session('post_order_no');
			$map['jhmx.jch_type']= session('jch_type');

					
		
			$count= D('Newproductjchmx')->getjhtongji_mx_listByMap_count($map);
			$sql_page=ceil($count/$rows);  
			if($page>$sql_page) $page=1;	
			$wblist = D('Newproductjchmx')->getjhtongji_mx_listByMap($map,"$sidx $sord",$page,$rows);		
			
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
	
	
	public function plch_jch()
	{		
		$goods_id_list=I('get.goods_id','0','string');
		if($goods_id_list=='null')
		{
			
		}else
        {          
			session('goods_id_list',$goods_id_list);
		} 			
	   	$type_list=D('ProductType')->select();
        $this->assign('type_list',$type_list);	
		$this->display();
	}
	
	
	public function getshangpininfo_plch_list()
	{
		if(IS_AJAX)
		{
			$page = I('get.page',1,'int');
			$rows = I('get.rows',20,'int');
			$sord = I('get.sord','','string')?:'asc';
			$sidx = I('get.sidx','','string')?:'goods_id';
			
			
			$goods_name = I('get.goods_name','','string');		
			$goods_type = I('get.goods_type','','string');
		 
		
			$goods_id_list=session('goods_id_list');
			$plch_status=session('plch_status');
			
			$map = array(); 			
			$map['deleted']=0;
            $map['wbid']=session('wbid');			
			
			if($plch_status=='3')  //出货
            {
			   $map['ck_num']=array('gt',0);
			}
										
			if(($goods_id_list !='null') &&(!empty($goods_id_list)) && ($goods_id_list!='undefined'))
			{
			  $map['goods_id']=array('not in',$goods_id_list);	
			}	
																							
			if(!empty($goods_name ))
			{
			  $map['goods_name']=array('LIKE','%'.$goods_name.'%');	
			}
			
			$count= D('Newproduct')->getProductinfoListByMap_count($map);
			$sql_page=ceil($count/$rows);  
			if($page>$sql_page) $page=1;	
			$wblist = D('Newproduct')->getProductinfoListByMap($map,"$sidx $sord",$page,$rows);	
			
				
			
            					
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
	
	/*
	   public function getxstongjilist_zongzhang()
	{
	    if(IS_AJAX)
		{
			$page = I('get.page',1,'int');
			$rows = I('get.rows',10,'int');
			$sord = I('get.sord','','string')?:'desc';
			$sidx = I('get.sidx','','string')?:'dtInsertTime';
						
			
			//$goods_name = I('get.spname5','','string');
			//$daterange     = I('get.daterange5','','string');
									  								 
			$map = array(); 
			$map['wbid']=session('wbid');	
			
			if(!empty($daterange))  
			{
			  list($start,$end) = explode(' - ',$daterange);    
			  $start = str_replace('/','-',$start);            
			  $end = str_replace('/','-',$end);                
			  $map['dtInsertTime'] = array('BETWEEN',array($start,$end));
			}
			
						
			if(!empty($goods_name))
			{
			  $map['goods_name']=array('LIKE','%'.$goods_name.'%');
			}  
			

											
			$count= D('Newproduct')->getProductinfoListByMap_count_zongzhang($map);
			$sql_page=ceil($count/$rows);  
			if($page>$sql_page) $page=1;	
			$wblist = D('Newproduct')->getProductinfoListByMap_zongzhang($map,"$sidx $sord",$page,$rows);		
			
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
	
	
	
}
