<?php
namespace Home\Controller;
class ChaoshiController extends CommonController 
{   
   //============================商品管理页面========包括商品新增 修改 删除===================================
   
    public function check_newcs_qx()
	{
		//判断新超市权限
	    $exe_sp_version=D('Webini')->where(array('wbid'=>session('wbid'),'skey'=>'exe_sp_version'))->getField('svalue');
		if($exe_sp_version==1)
		{
			$exe_sp_version=1;
			return false;
		}
		else
        {
			$exe_sp_version=0;
			return true;
		}			
		
	}
    public function danju()
	{
		$bOpen=$this->check_newcs_qx();
		if($bOpen===false)
		{		  
			$this->redirect('rate/fujia', array('cate_id' => 2), 1, '请在附加设置里开启设置...');	  
		}
		$map=array();
		$map['wbid']=session('wbid');
		$map['deleted']=0;	
		$map['is_zuhe']=array('neq',2);	
		
	    $goodslist=D('Product')->where($map)->select();
		foreach($goodslist as &$val)
		{
		  $val['value']=$val['goods_pinyin'].','.$val['goods_name'].','.$val['goods_quanpin'];
		  $val['label']=$val['goods_pinyin'].','.$val['goods_name'].','.$val['goods_quanpin'];	  	  
		}
		
        //单个有仓库库存商品的列表
		$this->assign('goodslist',json_encode($goodslist));	
		$this->display();
	}
	
	
	
   	public function shangpin()
	{
		$bOpen=$this->check_newcs_qx();
		if($bOpen===false)
		{		  
			$this->redirect('rate/fujia', array('cate_id' => 2), 1, '请在附加设置里开启设置...');	  
		}
		$wbid=session('wbid');		
		$typelist=D('ProductType')->select();
        $this->assign('typelist',$typelist);	
		
	    $list=array();
	    $i=0;
		$map=array();
		$map['wbid']=$wbid;
		$map['is_zuhe']=array('neq',1);
		$map['deleted']=0;
		
	    $goods_list=D('Product')->where($map)->select();	
        foreach($goods_list as &$val)
        {			
            if($val['is_zuhe']==0)
            {
				$list[$i]['goods_id']=$val['goods_id'];	
				$list[$i]['goods_name']=$val['goods_name'];
				$list[$i]['unit']=$val['unit'];
				$list[$i]['guige']=$val['guige'];
				$list[$i]['shou_price']=sprintf("%.2f",$val['shou_price']);;
				$list[$i]['is_zuhe']=$val['is_zuhe'];
				$list[$i]['zuhe_id']=$val['zuhe_id'];
				foreach($typelist as &$val1)
				{
					if($val['type_id']==$val1['type_id'])
					{
						$list[$i]['type_name']=$val1['type_name'];
						break;
					}	
				}
				$i++;
			}
			else if($val['is_zuhe']==2)
            {			
				$zuhe_id=$val['goods_id'];
				$zuhe_goods_array=array();
				$map=array();
				
				$map['is_zuhe']=1;
				$map['zuhe_id']=$zuhe_id;	
                $map['wbid']=$wbid;					
				$zuhe_goods_array=D('Product')->where($map)->select();
													
				$list[$i]['goods_id']=$val['goods_id'];
				foreach($typelist as &$val1)
				{
					if($val['type_id']==$val1['type_id'])
					{
						$list[$i]['type_name']=$val1['type_name'];
						break;
					}	
				}
				
				$list[$i]['goods_name']=$val['goods_name'];
				$list[$i]['unit']=$val['unit'];
				$list[$i]['guige']=$val['guige'];
				$list[$i]['shou_price']=sprintf("%.2f",$val['shou_price']);;
				$list[$i]['is_zuhe']=$val['is_zuhe'];
				$list[$i]['zuhe_id']=$val['zuhe_id'];
				
				if(!empty($zuhe_goods_array))
				{
					$list[$i]['zuhelist']=$zuhe_goods_array;
				}
				else
                {
					$list[$i]['zuhelist']='';
				}					
											
				$i++;
			}													
		}	
		$this->assign('goods_list',json_encode($list));	
	
	    $this->display();		
	}
	
	
	public function shangpin_add()
    {	   
        $type_list=D('ProductType')->select();
        $this->assign('type_list',$type_list);	  
	  	session('goods_id_list',null);
	    $wbid=session('wbid');	   
	    $goodslist=D('Productinfomb')->select();		
		foreach($goodslist as &$val)
		{
		  $val['value']=$val['goods_pinyin'].','.$val['goods_name'].','.$val['goods_quanpin'];
		  $val['label']=$val['goods_pinyin'].','.$val['goods_name'].','.$val['goods_quanpin'];
		  $val['goods_image_moren']=$val['goods_image'];		  
		  $val['goods_image']=C('SHANGPIN_MUBAN_TUPIAN_PATH_URL').$val['goods_image'];		  	  	  
		}			     
		$this->assign('goodslist',json_encode($goodslist));			
	    $this->display();
    }
  		
	public function shangpin_add_set()
	{
	   $wbid=session('wbid');
	   if(IS_AJAX)		   
	   {   

           $result=true;
		   $data['status']=-1; 
		   D()->startTrans();
		   
		   
           $dtInsertTime= date('Y-m-d H:i:s',time());
        	

		   $one_goods_name=I('post.goods_name','','string');
		   if(D('Product')->where(array('wbid'=>session('wbid'),'goods_name'=>$one_goods_name,'deleted'=>0))->find())
		   {
			   $data['status']=-2; 
               $this->ajaxReturn($data);
               return;			   
		   }

			$select_flag=I('post.select_flag','','string');
			if($select_flag==0)
			{
				
				$upload_dir=C('UPLOAD_SHANGPIN_DIR');     
				$first_file  = $_FILES['photo'];          //获取文件1的信息

				if ($first_file['error'] == UPLOAD_ERR_OK)
				{              
				   $temp_name = $first_file['tmp_name']; //上传文件1在服务器上的临时存放路径    

				   if ($first_file['type'] == "image/png")
				   {
					  $filename1=getRadomFileName();
					  
					  $filename1=$filename1.'.png';
					   
				   }
				   else if ($first_file['type'] == "image/jpeg")
				   {          
					  $filename1=getRadomFileName();
					  $filename1=$filename1.'.jpg';           
				   } 

				   move_uploaded_file($temp_name, iconv("UTF-8","gb2312",   $upload_dir.$filename1)); //移动临时文件夹中的文件1到存放上传文件的目录，并重命名为真实名称
				  
				}
				else
				{
					echo '[文件1]上传失败!<br/>';
					return;
				}
				
				$shenfenzheng_image = $filename1;
				$goods_insert_data['goods_image'] =$shenfenzheng_image;	
			}
			else  if($select_flag==1)
			{
			  $goods_insert_data['goods_image'] =I('post.goods_image','','string');	
			} 			
		   		   	   	  
		   $goods_insert_data['wbid']=$wbid;
		   $goods_insert_data['goods_id']=D('Product')->max('goods_id')+1;
		   $goods_insert_data['type_id']=I('post.fenlei','','string');
		   		   
		   $goods_insert_data['goods_name']=$one_goods_name;		   
		   $goods_insert_data['goods_pinyin']= getpinyin($one_goods_name);
           $goods_insert_data['goods_quanpin']=getAllPY($one_goods_name);	  	   
		   $goods_insert_data['barcode']=I('post.barcode','','string');
		   $goods_insert_data['one_jian_num']=I('post.one_jian_num',1,'int');
		   $goods_insert_data['shou_price']=I('post.shou_price','','string');
		   
		   $goods_insert_data['dtInsertTime']= $dtInsertTime;
		   		   		   	  		   				
			if(D('Product')->add($goods_insert_data)===false)
			{
				$result=false;
			}	
		
			   //如果是新商品，则在库存表新增两条记录
		   $kc_insert_data1['dtInsertTime']= $dtInsertTime;
		   $kc_insert_data1['goods_id']= $goods_insert_data['goods_id'];
		   $kc_insert_data1['wbid']= session('wbid');
		   $kc_insert_data1['position']= 0;
		   
		   
		   if(D('Productkc')->add($kc_insert_data1)===false)
		   {
			  $result=false; 
		   }
		   		   
		   $kc_insert_data2['dtInsertTime']= $dtInsertTime;
		   $kc_insert_data2['goods_id']= $goods_insert_data['goods_id'];
		   $kc_insert_data2['wbid']= session('wbid');
		   $kc_insert_data2['position']= 1;
		   
		   if(D('Productkc')->add($kc_insert_data2)===false)
		   {
			  $result=false;  
		   }	
		   		  		              
            if($result)
			{	
                D()->commit();    //提交				
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

	
	public function shangpin_edit()
    {
	    $wbid=session('wbid');
        $goods_id=I('get.goods_id',0,'int');	  
	    $goods_info=D('Product')->where(array('wbid'=>$wbid,'goods_id'=>$goods_id))->find();	  	   
	    $goods_info['shou_price'] =sprintf("%.2f",$goods_info['shou_price']);
		if(empty($goods_info['goods_image']))
		{
		  $goods_info['goods_image'] =C('SHANGPIN_TUPIAN_PATH_URL').'moren.png';	
		}
		else
        {
			$path =C('UPLOAD_SHANGPIN_DIR').$goods_info['goods_image'];	
			if(file_exists($path))
			{
			  $goods_info['goods_image'] =C('SHANGPIN_TUPIAN_PATH_URL').$goods_info['goods_image'];	
			}
			else
			{
				$goods_info['goods_image'] =C('SHANGPIN_TUPIAN_PATH_URL').'moren.png';	
			}			    
		}			
	
		  
	  $this->assign('goods_info',$goods_info);		
	  $type_list=D('ProductType')->select();
	  	   $bEdit=1;//允许修改
	   	$ck_num = D('Productkc')->where(array('wbid'=>session('wbid'),'goods_id'=>$goods_id,'position'=>0))->getField('num');
		$hj_num = D('Productkc')->where(array('wbid'=>session('wbid'),'goods_id'=>$goods_id,'position'=>1))->getField('num');
		if(!empty($ck_num) || !empty($hj_num))
		{
			$bEdit=0;
		}
		$this->assign('bEdit',$bEdit);	
        $this->assign('type_list',$type_list);	
        $this->display();
    }
	
	public function shangpin_edit_set()
	{
	   $wbid=session('wbid');
	   if(IS_AJAX)		   
	   {   
           $data['status']=-1;  	
		   $one_goods_name=I('post.goods_name','','string');
		   $goods_id=I('post.goods_id',0,'int');
		   
		   $old_goods_name= D('Product')->where(array('wbid'=>session('wbid'),'goods_id'=>$goods_id))->getField('goods_name');	   
		   if($one_goods_name!=$old_goods_name )
		   {
			   if(D('Product')->where(array('wbid'=>session('wbid'),'goods_name'=>$one_goods_name))->find())
			   {
				   $data['status']=-2; 
				   $this->ajaxReturn($data);
				   return;			   
			   }
		   }

		   $select_flag=I('post.select_flag','','string');		   		   
			if($select_flag==1)
			{
			  
			}
			else if($select_flag==0)
			{
			  	$upload_dir=C('UPLOAD_SHANGPIN_DIR');     
				$first_file  = $_FILES['photo'];          //获取文件1的信息				
				if ($first_file['error'] == UPLOAD_ERR_OK)
				{              
				   $temp_name = $first_file['tmp_name']; //上传文件1在服务器上的临时存放路径    

				   if ($first_file['type'] == "image/png")
				   {
					  $filename1=getRadomFileName();					  
					  $filename1=$filename1.'.png';
				   }
				   else if ($first_file['type'] == "image/jpeg")
				   {          
					  $filename1=getRadomFileName();
					  $filename1=$filename1.'.jpg';   				  
				   } 
         	
				   move_uploaded_file($temp_name, iconv("UTF-8","gb2312",   $upload_dir.$filename1)); //移动临时文件夹中的文件1到存放上传文件的目录，并重命名为真实名称
				
				}
				else
				{
					echo '[文件1]上传失败!<br/>';
				
					return;
				}
				
				$shenfenzheng_image = $filename1;										
				$goods_update_data['goods_image'] =$shenfenzheng_image;		
			}	
			
		               	   
		   $goods_update_data['type_id']=I('post.fenlei','','string');		  
		   $goods_update_data['barcode']=I('post.barcode','','string');
		   $goods_update_data['one_jian_num']=I('post.one_jian_num','1','string');
		   $goods_update_data['shou_price']=I('post.shou_price',0,'float');	   
		   $goods_update_data['goods_name']=$one_goods_name;		   
		   $goods_update_data['goods_pinyin']= getpinyin($one_goods_name);
           $goods_update_data['goods_quanpin']=getAllPY($one_goods_name);
		   	   
		   if(!empty($wbid))
		   {				
				$goods_update_result=D('Product')->where(array('wbid'=>$wbid,'goods_id'=>$goods_id))->save($goods_update_data);
				
				if(!empty($goods_update_result))
				{
					$data['status']=1; 
				}
				else
                {
				  $data['status']=-1;  	
				}					
		   
			} 
			$this->ajaxReturn($data);
					
	   }	   
		   
	}
	
	
	public function shangpin_delete_set()
	{
	   $wbid=session('wbid');
	   if(IS_AJAX)		   
	   {   
           $goods_id=I('post.goods_id',0,'int');
           $data['status']=-1;  			  	   
		   if((!empty($wbid)) &&(!empty($goods_id)))
		   {				
	            //判断下 如果该商品有单据，就不能删除
				$ck_num = D('Productkc')->where(array('wbid'=>$wbid,'goods_id'=>$goods_id,'position'=>0))->getField('num');
				$hj_num = D('Productkc')->where(array('wbid'=>$wbid,'goods_id'=>$goods_id,'position'=>1))->getField('num');
				if(!empty($ck_num) || !empty($hj_num))
				{
					$data['status']=-2;
					$this->ajaxReturn($data);
					return;
				}							
				$goods_delete_result=D('Product')->where(array('wbid'=>$wbid,'goods_id'=>$goods_id))->setField('deleted',1);			
				if(!empty($goods_delete_result))
				{
					$data['status']=1; 
					$goodskc_delete_result=D('Productkc')->where(array('wbid'=>$wbid,'goods_id'=>$goods_id))->setField('deleted',1);
				}
				else
                {
				  $data['status']=-1;  	
				}							   
			} 
			$this->ajaxReturn($data);
					
	   }	   		   
	}
	
	//======================================商品信息页面结束==========================================
	
	//======================================组合管理开始=================================================
	public function zuhe()
	{
		$wbid=session('wbid');
		$goods_id=I('get.goods_id','','string');			
		$map = array();
        $map['wbid']=$wbid;
		$map['goods_id']=$goods_id;		
		$goodsinfo=D('Product')->where($map)->find();	
		$this->assign('goodsinfo',$goodsinfo);

		
		$zuhelist=D('Zuhe')->where(array('wbid'=>$wbid,'deleted'=>0))->select();
		$this->assign('zuhelist',$zuhelist);
		$this->assign('zuhe_list_price',json_encode($zuhelist));
		creatToken();	
		$this->display();
	}
	
	public function getallzuhelist()
	{		    
      if(IS_AJAX)
      {
		$wbid=session('wbid');  
        $page = I('get.page',1,'int');
        $rows = I('get.rows',20,'int');
        $sord = I('get.sord','','string')?:'desc';
        $sidx = I('get.sidx','','string')?:'id';


  	    $map = array();
        $map['wbid']=$wbid;
        $map['deleted']=0;

        $count= D('Zuhe')->getZuheListByMap_Count($map);
        $sql_page=ceil($count/$rows);  
        if($page>$sql_page) $page=1;


        $wblist = D('Zuhe')->getZuheListByMap($map,"$sidx $sord",$page,$rows);
         

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
	

	public function zuheguanli()
	{		
        	
		$this->display();
	}
	
	public  function zuhe_add_set()
	{
		header('Access-Control-Allow-Origin:*');
		$zuhe_name=I('get.zuhe_name','','string');
		$zuhe_price=I('get.zuhe_price','0','float');
		$zuhe_type=I('get.zuhe_type','1','float');
			
		$wbid=session('wbid');
		$map=array();
		$map['wbid']=$wbid;
		$map['zuhe_name']=$zuhe_name;
        $map['deleted']=0;		
		$bFind=D('Zuhe')->where($map)->find();
		
		if($bFind)
		{
			$data['result']=-2;
		}
		else
        {	 	
	        $result=true;
			D()->startTrans();
			
			$maxid=D('Product')->where(array('wbid'=>$wbid))->max('goods_id');
			
			if(empty($maxid))
			{
				$maxid=1;
			}				
			$zuhe_id=$maxid+1;
			$zuhe_insert_data['zuhe_name']=$zuhe_name;
			$zuhe_insert_data['zuhe_price']=$zuhe_price;
			$zuhe_insert_data['zuhe_id']=$zuhe_id;
			$zuhe_insert_data['zuhe_type']=$zuhe_type;
			$zuhe_insert_data['wbid']=$wbid;
			$zuhe_insert_data['isValid']=0;
			
			if(D('Zuhe')->add($zuhe_insert_data)===false)
			{
				$result=false;
			}	
			
			$goods_insert_data['type_id']=1;
			$goods_insert_data['goods_id']=$zuhe_id;			
			$goods_insert_data['is_zuhe']=2;
			$goods_insert_data['zuhe_id']=0;
			$goods_insert_data['type_id']=$zuhe_type;
			$goods_insert_data['goods_name']=$zuhe_name;
			
			$goods_insert_data['goods_pinyin']= getpinyin($zuhe_name);
            $goods_insert_data['goods_quanpin']=getAllPY($zuhe_name);
			
			$goods_insert_data['shou_price']=$zuhe_price;
			$goods_insert_data['wbid']=$wbid;
			$goods_insert_data['dtInsertTime']=date('Y-m-d H:i:s',time());
							
			if(D('Product')->add($goods_insert_data)===false)
			{
				$result=false;
			}	
			
			
			
			//如果是新商品，则在库存表新增两条记录
		   $kc_insert_data1['dtInsertTime']= $dtInsertTime;
		   $kc_insert_data1['goods_id']= $zuhe_id;
		   $kc_insert_data1['wbid']= $wbid;
		   $kc_insert_data1['position']= 0;
		   $kc_insert_data1['dtInsertTime']=date('Y-m-d H:i:s',time());	   
		   if(D('Productkc')->add($kc_insert_data1)===false)
		   {
			  $result=false; 
		   }


		   $kc_insert_data2['dtInsertTime']= $dtInsertTime;
		   $kc_insert_data2['goods_id']= $zuhe_id;
		   $kc_insert_data2['wbid']= $wbid;
		   $kc_insert_data2['position']= 1;
		   $kc_insert_data2['dtInsertTime']=date('Y-m-d H:i:s',time());	
		   if(D('Productkc')->add($kc_insert_data2)===false)
		   {
			  $result=false;  
		   }
												
			if($result)
			{
		        D()->commit();
				$data['result']=1;
			}else
            {
		        D()->rollback();
				$data['result']=-1;
			}					
		}			
		
		$this->ajaxReturn($data);
	}
	
		
	public  function zuhe_delete_set()
	{
		header('Access-Control-Allow-Origin:*');
		$zuhe_id=I('post.zuhe_id','','string');
 	
		$bFind=false;
		
		$wbid=session('wbid');
		$map=array();
		$map['wbid']=$wbid;
		$map['zuhe_id']=$zuhe_id;	
		$bFind=D('Zuhe')->where($map)->find();
		
		if(empty($bFind))
		{
			$data['result']=-3;
			$this->ajaxReturn($data);
			return;
		}
		
		$map=array();
		$map['wbid']=$wbid;
		$map['zuhe_id']=$zuhe_id;
        $map['is_zuhe']=1;		
		$bFind1=D('Product')->where($map)->find();
		
		if(!empty($bFind1))
		{
			$data['result']=-2;
			$this->ajaxReturn($data);
			return;
		}	
		
		
   
		$result=true; 
		D()->startTrans();		
		$map=array();
		$map['wbid']=$wbid;
		$map['zuhe_id']=$zuhe_id;	
			
		if(D('Zuhe')->where($map)->setField('deleted',1)===false)
		{
			$result=false;
		}	
	 	 
		$map=array();
		$map['wbid']=$wbid;
		$map['goods_id']=$zuhe_id;	
		
	    if(D('Product')->where($map)->setField('deleted',1)===false)
		{
            $result=false;
		}	
		 
		if($result)
		{
	        D()->commit();
			$data['result']=1;
		}
		else
		{ 
	        D()->rollback();
			$data['result']=-1;
		}					
						
		$this->ajaxReturn($data);
	}
		
	public function zuhe_edit()
	{	
		$zuhe_id=I('get.zuhe_id','','string');
		$wbid=session('wbid');
		$map=array();
		$map['wbid']=$wbid;
		$map['zuhe_id']=$zuhe_id;
		$zuheinfo=D('Zuhe')->where($map)->find();
		
		$this->assign('zuheinfo',$zuheinfo);
		
		$this->display();
	}
	
	
	public  function zuhe_edit_set()
	{
		header('Access-Control-Allow-Origin:*');
		$zuhe_id=I('post.zuhe_id','','string');
		$zuhe_name=I('post.zuhe_name','','string');
		$zuhe_type=I('post.zuhe_type','','string');
			
		$bFind=false;
		
		$wbid=session('wbid');
		$map=array();
		$map['wbid']=$wbid;
		$map['zuhe_id']=$zuhe_id;	
		$bFind=D('Zuhe')->where($map)->find();
		
		if(empty($bFind))
		{
			$data['result']=-2;
			$this->ajaxReturn($data);
			return;
		}
		

		$result=true; 
		D()->startTrans();		
		$map=array();
		$map['wbid']=$wbid;
		$map['zuhe_id']=$zuhe_id;
		
        $zuhe_update_data['zuhe_name']=$zuhe_name;
        $zuhe_update_data['zuhe_type']=$zuhe_type;
		
		if(D('Zuhe')->where($map)->save($zuhe_update_data)===false)
		{
			$result=false;
		}	
	 
	 		
        $map=array();
		$map['wbid']=$wbid;
		$map['goods_id']=$zuhe_id;
		
	    $goods_update_data['goods_name']=$zuhe_name;
		$goods_update_data['type_id']=$zuhe_type;
	    if(D('Product')->where($map)->save($goods_update_data)===false)
		{
            $result=false;
		}	
		 

		if($result)
		{
	        D()->commit();
			$data['result']=1;
		}
		else
		{ 
	        D()->rollback();
			$data['result']=-1;
		}											
		$this->ajaxReturn($data);
	}
	
	
	
	//===================新增一个商品到组合===========================
		
	public  function addgoods_to_zuhe_set()
	{
		header('Access-Control-Allow-Origin:*');
		$zuhe_id=I('get.zuhe_id','','string');
		$goods_id=I('get.goods_id','','string');
		
		
		if(!checkToken($_GET['token']))
		{  
			writelog('addgoods_to_zuhe_set---重复提交');
			$data['result']=-4;          								
			$this->ajaxReturn($data);
			return;   			   
		}
		else
		{
			//writelog('addgoods_to_zuhe_set---未重复提交');
		}
		
	
		
		$wbid=session('wbid');
	    $goods_name=D('Product')->where(array('wbid'=>$wbid,'goods_id'=>$goods_id))->getField('goods_name');
		$zuhe_goods_name=D('Product')->where(array('wbid'=>$wbid,'goods_id'=>$zuhe_id))->getField('goods_name');

		if(empty($zuhe_id) || empty($goods_id))
		{
			$data['result']=-2;
			$this->ajaxReturn($data);
			return;
		}
		else
        {		
            //如果本商品已经单卖过，不允许组合
		
			$nowtime=date('Y-m-d H:i:s',time());
		    $lastshiftinfo= D('Productjb')->where(array('wbid'=>$wbid))->order('shifttime desc')->limit(1)->find();
		    $lastshifttime=date('Y-m-d H:i:s',strtotime($lastshiftinfo['shifttime']));
			
			$map1=array();
			$map1['wbid']=$wbid;	
			$map1['dtInsertTime']=array('BETWEEN',array($lastshifttime,$nowtime));
			$map1['goods_id']=$goods_id;
			$bXiaoshou= D('Productxsmx')->where($map1)->find();		
            if(!empty($bXiaoshou))
			{
				$data['result']=-3;
				$this->ajaxReturn($data);
				return;
			}      			
            D()->startTrans();
            $result=true;
			
			//添加一条总上架记录
			$post_order_no = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);  //网站订单号
			$post_order_no='ZH'.$post_order_no;
            $dtInsertTime=date('Y-m-d H:i:s',time());
			

			
										
			$goods_update_data['is_zuhe']=1;
			$goods_update_data['zuhe_id']=$zuhe_id;										
			if(D('Product')->where(array('wbid'=>$wbid,'goods_id'=>$goods_id))->save($goods_update_data)===false)
			{
				$result=false;
			}	
							
			//更新该商品库存
            $map=array();
            $map['goods_id']=$goods_id;
            $map['position']=1;
            $map['wbid']=$wbid;			
			$hj_num=D('Productkc')->where($map)->getField('num');
								
			if(D('Productkc')->where($map)->setField('num',0)===false)
			{
				$result=false;
			}				
			//更新组合商品的 货架数量
            
			if(empty($hj_num))
			{
				$hj_num=0;
			}				
			
			//添加一条组合商品 上架明细记录
			$shangxiajiamx_insert_data=array();
			$shangxiajiamx_insert_data['goods_id']=$zuhe_id;
			$shangxiajiamx_insert_data['num']     =$hj_num;
			$shangxiajiamx_insert_data['ck_num']  =0;
			$shangxiajiamx_insert_data['hj_num']  =D('Productkc')->where(array('wbid'=>$wbid,'position'=>1,'goods_id'=>$val['zuhe_id']))->getField('num');				
			$shangxiajiamx_insert_data['post_order_no']=$post_order_no;
			$shangxiajiamx_insert_data['shangxia_status']=0;
			$shangxiajiamx_insert_data['wbid']=$wbid;
			$shangxiajiamx_insert_data['operate']=session('username');
			$shangxiajiamx_insert_data['dtInsertTime']=$dtInsertTime;
			$shangxiajiamx_insert_data['zuhe_id']=0;
			$shangxiajiamx_insert_data['is_zuhe_goods']=2;
			$shangxiajiamx_insert_data['zuhe_flag']=1;
			if(D('Productsxjmxzh')->add($shangxiajiamx_insert_data)===false)
			{					
				$result=false;
			}
			
			$map=array();
            $map['goods_id']=$zuhe_id;
            $map['position']=1;
            $map['wbid']=$wbid;							
			if(D('Productkc')->where($map)->setInc('num',$hj_num)===false)
			{
				$result=false;
			}
			
			$bz_array=array();
			$bz_array[0]['zuhe_id']=$zuhe_id;
			$bz_array[0]['goods_id']=$goods_id;
			$bz_array[0]['is_zuhe']=1;
			$bz_array[0]['shangjia_num']=$hj_num;
			
			$bz_array[1]['zuhe_id']=0;
			$bz_array[1]['goods_id']=$zuhe_id;
			$bz_array[1]['is_zuhe']=2;
			$bz_array[1]['shangjia_num']=$hj_num;
			
			
			$shangxiajia_insert_data['post_order_no']=$post_order_no;
			$shangxiajia_insert_data['shangxia_status']=0;
			$shangxiajia_insert_data['wbid']=$wbid;
			$shangxiajia_insert_data['info']='货架数量:'.$goods_name.' -'.$hj_num.'个 '.$zuhe_goods_name.' (组合)+'.$hj_num.'个';		
			$shangxiajia_insert_data['dtInsertTime']=$dtInsertTime;
			$shangxiajia_insert_data['operator']=session('username');
			$shangxiajia_insert_data['bz']='将商品 '.$goods_name.'组合到'.$zuhe_goods_name;
			$shangxiajia_insert_data['detailinfo']=json_encode($bz_array);
			$shangxiajia_insert_data['zuhe_flag']=1;					
			if(D('Productsxj')->add($shangxiajia_insert_data)===false)
			{					
				$result=false;
			}	

			
			
			
			
			//更新组合库 里的商品列表
			$map=array();
			$map['zuhe_id']=$zuhe_id;
			$map['wbid']=$wbid;
			$old_goodsid_list=D('Zuhe')->where($map)->getField('goodsid_list');
			
			$zuhe_update_data['goodsid_list']=$old_goodsid_list.$goods_id.',';
			$zuhe_update_data['isValid']=1;
			if(D('Zuhe')->where($map)->save($zuhe_update_data)===false)
			{
				$result=false;
			}
				
					
			if($result)
			{
		        D()->commit();
				$data['result']=1;
			}else
            {
		        D()->rollback();
				$data['result']=-1;
			}					
		}			
		
		$this->ajaxReturn($data);
	}
	
	//=================================拆分一个商品=================================
	
	public function chaifen()
	{
		$goods_id=I('get.goods_id','','string');
		$zuhe_id=I('get.zuhe_id','','string');
		$this->assign('zuhe_id',$zuhe_id);
		$this->assign('goods_id',$goods_id);
		$disabl=I('get.disabl','','string');
		$this->assign('disabl',$disabl);
	
		$zh_name=D('Product')->where(array('wbid'=>session('wbid'),'goods_id'=>$zuhe_id))->getField('goods_name');
		$this->assign('zh_name',$zh_name);
		$wbid=session('wbid');
		
		$map=array();
		$map['goods_id']=$zuhe_id;
        $map['position']=1;
        $map['wbid']=$wbid;			

		
		$sumnum=D('Productkc')->where($map)->getField('num');
		if(empty($sumnum))
		{
			$sumnum=0;
		}	
	
        $this->assign('sumnum',$sumnum);
		creatToken();	
		$this->display();
	}
	
	public  function chafengoods_from_zuhe_set()
	{
		header('Access-Control-Allow-Origin:*');
		$goods_num=I('get.goods_num','0','string');
		$goods_id=I('get.goods_id','','string');
		$zuhe_id=I('get.zuhe_id','','string');
		if(!checkToken($_GET['token']))
		{  
			writelog('chafengoods_from_zuhe_set---重复提交');
			$data['status']=-2;          								
			$this->ajaxReturn($data);
			return;   			   
		}
		else
		{
			//writelog('chafengoods_from_zuhe_set---未重复提交');
		}
		
		$wbid=session('wbid');
		
		//添加一条总上架记录
		$post_order_no = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);  //网站订单号
		$post_order_no='CF'.$post_order_no;
		$dtInsertTime=date('Y-m-d H:i:s',time());
		
	    $goods_name=D('Product')->where(array('wbid'=>$wbid,'goods_id'=>$goods_id))->getField('goods_name');
		$zuhe_goods_name=D('Product')->where(array('wbid'=>$wbid,'goods_id'=>$zuhe_id))->getField('goods_name');
			

		if(empty($zuhe_id) || empty($goods_id))
		{
			$data['result']=-2;
			$this->ajaxReturn($data);
			return;
		}
		else
        {	
	        $map=array();
            $map['goods_id']=$zuhe_id;
            $map['position']=1;
            $map['wbid']=$wbid;		
			
			$old_hj_num=D('Productkc')->where($map)->getField('num');
			
		    if($goods_num==0 && $old_hj_num==0)
			{	
				
			}
			else if($old_hj_num < $goods_num)
			{
				$data['result']=-3;
				$this->ajaxReturn($data);
				return;
			}
            $result=true;
            D()->startTrans();
			
			if($goods_num >0)
			{
				//如果是拆分最后一个商品的话，拆分数量就是组合商品的总库存
				$map=array();
				$map['zuhe_id']=$zuhe_id;
				$map['is_zuhe']=1;
				$map['wbid']=$wbid;	
				$map['deleted']=0;	
				$shuliang=D('Product')->field('id')->where($map)->count();
				//$shuliang=count($record);

				if($shuliang==1)
				{
					$goods_num= $old_hj_num;
					
					 //如果该商品是最后一个组合商品的话
					//组合数量必须全给此商品，并且
				   $zuhe_update_data=array(); 
				   $zuhe_update_data['isValid']=0;
				   
				   $map['zuhe_id']=$zuhe_id;
				   $map['wbid']=$wbid;	
				   $map['deleted']=0;					   
				   if(D('Zuhe')->where($map)->save($zuhe_update_data)===false)
				   {					
					  $result=false;
				   }				   				
				}
			}	
            
			//1.更新商品信息表 该商品的信息
			$goods_update_data['is_zuhe']=0;
			$goods_update_data['zuhe_id']=0;										
			if(D('Product')->where(array('wbid'=>$wbid,'goods_id'=>$goods_id))->save($goods_update_data)===false)
			{
				$result=false;
			}
			
			if($goods_num !=0)
			{
	
				
				//添加一条组合商品 下架一部分数量 明细记录
				
				$shangxiajiamx_insert_data=array();
				$shangxiajiamx_insert_data['goods_id']=$zuhe_id;
				$shangxiajiamx_insert_data['num']     =$goods_num;
				$shangxiajiamx_insert_data['ck_num']  =0;
				$shangxiajiamx_insert_data['hj_num']  =D('Productkc')->where(array('wbid'=>$wbid,'position'=>1,'goods_id'=>$val['zuhe_id']))->getField('num');				
				$shangxiajiamx_insert_data['post_order_no']=$post_order_no;
				$shangxiajiamx_insert_data['shangxia_status']=1;             //下架
				$shangxiajiamx_insert_data['wbid']=$wbid;
				$shangxiajiamx_insert_data['operate']=session('username');
				$shangxiajiamx_insert_data['dtInsertTime']=$dtInsertTime;
				$shangxiajiamx_insert_data['zuhe_id']=0;
				$shangxiajiamx_insert_data['is_zuhe_goods']=2;
				$shangxiajiamx_insert_data['zuhe_flag']=1;
				if(D('Productsxjmxzh')->add($shangxiajiamx_insert_data)===false)
				{					
					$result=false;
				}
				
				//添加一条单个商品 上架一部分数量 明细记录
				$shangxiajiamx_insert_data=array();
				$shangxiajiamx_insert_data['goods_id']=$goods_id;
				$shangxiajiamx_insert_data['num']     =$goods_num;
				$shangxiajiamx_insert_data['ck_num']  =D('Productkc')->where(array('wbid'=>$wbid,'position'=>0,'goods_id'=>$val['zuhe_id']))->getField('num');	;
				$shangxiajiamx_insert_data['hj_num']  =0;				
				$shangxiajiamx_insert_data['post_order_no']=$post_order_no;
				$shangxiajiamx_insert_data['shangxia_status']=0;             //上架
				$shangxiajiamx_insert_data['wbid']=$wbid;
				$shangxiajiamx_insert_data['operate']=session('username');
				$shangxiajiamx_insert_data['dtInsertTime']=$dtInsertTime;
				$shangxiajiamx_insert_data['zuhe_id']=0;
				$shangxiajiamx_insert_data['is_zuhe_goods']=0;
				$shangxiajiamx_insert_data['zuhe_flag']=1;
				if(D('Productsxjmx')->add($shangxiajiamx_insert_data)===false)
				{					
					$result=false;
				}
				
				
				
				//2.更新该组合商品的货架库存数量		
				$map=array();
				$map['goods_id']=$zuhe_id;
				$map['position']=1;
				$map['wbid']=$wbid;	
				
				if(D('Productkc')->where($map)->setDec('num',$goods_num)===false)
				{
					$result=false;
				}							
				//3.更新拆分出来的商品库存数量
				$map=array();
				$map['goods_id']=$goods_id;
				$map['position']=1;
				$map['wbid']=$wbid;							
				if(D('Productkc')->where($map)->setInc('num',$goods_num)===false)
				{
					$result=false;
				}
				
				$bz_array=array();
				$bz_array[0]['zuhe_id']=$zuhe_id;
				$bz_array[0]['goods_id']=$goods_id;
				$bz_array[0]['is_zuhe']=1;
				$bz_array[0]['xiajia_num']=$goods_num;
				
				$bz_array[1]['zuhe_id']=0;
				$bz_array[1]['goods_id']=$zuhe_id;
				$bz_array[1]['is_zuhe']=2;
				$bz_array[1]['xiajia_num']=$goods_num;
				
				
				$shangxiajia_insert_data['post_order_no']=$post_order_no;
				$shangxiajia_insert_data['shangxia_status']=0;
				$shangxiajia_insert_data['wbid']=$wbid;
				$shangxiajia_insert_data['info']='货架数量:'.$zuhe_goods_name.'(组合) -'.$goods_num.'个  '.$goods_name.'+'.$goods_num.'个';		
				$shangxiajia_insert_data['dtInsertTime']=$dtInsertTime;
				$shangxiajia_insert_data['operator']=session('username');
				$shangxiajia_insert_data['bz']='将'.$zuhe_goods_name.'(组合)拆分到'.$goods_name;
				$shangxiajia_insert_data['detailinfo']=json_encode($bz_array);
				$shangxiajia_insert_data['zuhe_flag']=1;					
				if(D('Productsxj')->add($shangxiajia_insert_data)===false)
				{					
					$result=false;
				}	

      			
				
			}
			
			if($result)
			{
		        D()->commit(); 
				$data['result']=1;
			}
			else
            {
		        D()->rollback();
				$data['result']=-1;
			}					
		}			
		
		$this->ajaxReturn($data);
	}
	//======================================组合管理结束=================================================
	
	//=====================================进出货页面开始=================================================
	public function jinhuo()
	{
	    $bOpen=$this->check_newcs_qx();
		if($bOpen===false)
		{		  
			$this->redirect('rate/fujia', array('cate_id' => 2), 1, '请在附加设置里开启设置...');	  
		}
		session('goods_id_list',null);
		session('plch_status','2');
		$wbid=session('wbid');	
		
		$map['wbid']=$wbid;
		$map['deleted'] =0;
		$map['is_zuhe']=array('neq',2);
		
		$goodslist=D('Product')->where($map)->select();
		
		foreach($goodslist as &$val)
		{
		  $val['value']=$val['goods_pinyin'].','.$val['goods_name'].','.$val['goods_quanpin'];
		  $val['label']=$val['goods_pinyin'].','.$val['goods_name'].','.$val['goods_quanpin'];
		  $val['hj_num']=D('Productkc')->where(array('goods_id'=>$val['goods_id'],'wbid'=>$wbid,'position'=>1))->getField('num');
		  $val['ck_num']=D('Productkc')->where(array('goods_id'=>$val['goods_id'],'wbid'=>$wbid,'position'=>0))->getField('num');	  	  
		}
		$this->assign('goodslist',json_encode($goodslist));	
        
		$map=array();
	    $map['wbid']=$wbid;
		$map['deleted'] =0;
		$map['is_zuhe']=2;
        $zuhe_goodslist=D('Product')->where($map)->select();	
        $this->assign('zuhe_goodslist',json_encode($zuhe_goodslist));
        creatToken();		
		$this->display();
	}
	
	public function chuhuo()
	{
		$bOpen=$this->check_newcs_qx();
		if($bOpen===false)
		{		  
			$this->redirect('rate/fujia', array('cate_id' => 2), 1, '请在附加设置里开启设置...');	  
		}
		session('goods_id_list',null);		
		session('plch_status','3');
		$wbid=session('wbid');
		
		$map=array();
		$map['kc.num']=array('gt',0);		
		$map['kc.position']=0;
		$map['info.deleted']=0;
        $map['info.is_zuhe']=array('neq',2);
        $map['info.wbid']=session('wbid');
		
	    $goodslist=D('Productkc')->getAllkucunfoListByMap_jinchuhuo($map);	
		$this->assign('goodslist',json_encode($goodslist));	
        creatToken();		
		$this->display();
	}
	
    //=====================================进出货页面结束=================================================
	
    public function pdhj()
	{
		$bOpen=$this->check_newcs_qx();
		if($bOpen===false)
		{		  
			$this->redirect('rate/fujia', array('cate_id' => 2), 1, '请在附加设置里开启设置...');	  
		}
		session('goods_id_list',null);
		$wbid=session('wbid');

		session('plch_status','4');
		
		$map=array();
		
		$map['kc.wbid']=session('wbid');
		$map['kc.position']=1;
		$map['kc.deleted']=0;
		$map['info.is_zuhe']=array('neq',1);
		
	    $goodslist=D('Productkc')->getAllChuhuokucunfoListByMap($map);
		foreach($goodslist as &$val)
		{
		  $val['value']=$val['goods_pinyin'].','.$val['goods_name'].','.$val['goods_quanpin'];
		  $val['label']=$val['goods_pinyin'].','.$val['goods_name'].','.$val['goods_quanpin'];
		  $val['hj_num']=D('Productkc')->where(array('goods_id'=>$val['goods_id'],'wbid'=>session('wbid'),'position'=>1))->getField('num');
		  $val['ck_num']=D('Productkc')->where(array('goods_id'=>$val['goods_id'],'wbid'=>session('wbid'),'position'=>0))->getField('num');
		  	  
		}

		$this->assign('goodslist',json_encode($goodslist));	
		$this->display();
	}
	
	

	
	public function pdck()
	{
		$bOpen=$this->check_newcs_qx();
		if($bOpen===false)
		{		  
			$this->redirect('rate/fujia', array('cate_id' => 2), 1, '请在附加设置里开启设置...');	  
		}
		session('goods_id_list',null);
		$wbid=session('wbid');
	    // $sylist=D('SpCtrlIp')->getAllSpIniById($wbid);
		// $this->assign('sylist',$sylist);
		session('plch_status','5');
		
		$map=array();		
		$map['kc.wbid']=session('wbid');
		$map['kc.position']=0;
		$map['kc.deleted']=0;
		
	    $goodslist=D('Productkc')->getAllChuhuokucunfoListByMap($map);
		foreach($goodslist as &$val)
		{
		  $val['value']=$val['goods_pinyin'].','.$val['goods_name'].','.$val['goods_quanpin'];
		  $val['label']=$val['goods_pinyin'].','.$val['goods_name'].','.$val['goods_quanpin'];
		  $val['hj_num']=D('Productkc')->where(array('goods_id'=>$val['goods_id'],'wbid'=>session('wbid'),'position'=>1))->getField('num');
		  $val['ck_num']=D('Productkc')->where(array('goods_id'=>$val['goods_id'],'wbid'=>session('wbid'),'position'=>0))->getField('num');		  		  	  	  
		}	
		$this->assign('goodslist',json_encode($goodslist));			
		$this->display();
	}
	
	
	
	
	
	public function plch()
	{		
		$goods_id_list=I('get.goods_id','0','string');
		if($goods_id_list=='null')
		{
			
		}
		else
        {          
			session('goods_id_list',$goods_id_list);
		} 					
		$this->display();
	}
	
	
		
	public function plch_pd()
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
	
	
	public function plch_sxj()
	{		
		$goods_id_list=I('get.goods_id','0','string');
		if($goods_id_list=='null')
		{
			
		}
		else
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
		 
			$map = array(); 
			
			$map['sp.deleted']=0;
			
			$goods_id_list=session('goods_id_list');
			$plch_status=session('plch_status');
			if($plch_status=='0')     //上架 
			{
			   $map['kc.position']=0;
			   $map['kc.num']=array('gt',0);
                			   
			}else if($plch_status=='1')   //下架    
			{
			   $map['kc.position']=1;
			   $map['kc.num']=array('gt',0);             			   
			}
			else if($plch_status=='2')  //进货
            {
              $map['sp.is_zuhe']=array('neq',2);			   
			}
			else if($plch_status=='3')  //出货
            {
		       $map['kc.position']=0;
			   $map['kc.num']=array('gt',0);
			   $map['sp.is_zuhe']=array('neq',2);
			}
			else if($plch_status=='4')  //盘点货架
            {
		       $map['kc.position']=1;
			   $map['sp.is_zuhe']=array('neq',1);
			  
			}
            else if($plch_status=='5')  //盘点仓库
            {
		       $map['kc.position']=0;
			   $map['sp.is_zuhe']=array('neq',2);
			  
			}								
			
			if(($goods_id_list !='null') &&(!empty($goods_id_list)) && ($goods_id_list!='undefined'))
			{
			  $map['sp.goods_id']=array('not in',$goods_id_list);	
			}	
														
			if($plch_status=='2')
			{
				$map['wbid']=session('wbid');
				if(!empty($goods_type ))
				{
				  $map['type_id']=$goods_type;	
				}
				
				if(!empty($goods_name ))
				{
				  $map['goods_name']=array('LIKE','%'.$goods_name.'%');	
				}
				
				$count= D('Product')->getProductinfoListByMap_count($map);
				$sql_page=ceil($count/$rows);  
				if($page>$sql_page) $page=1;	
				$wblist = D('Product')->getProductinfoListByMap($map,"$sidx $sord",$page,$rows);	
			}
			else
            {
				$map['sp.wbid']=session('wbid');
				if(!empty($goods_name ))
				{
				  $map['sp.goods_name']=array('LIKE','%'.$goods_name.'%');	
				}
				
				if(!empty($goods_type ))
				{
				  $map['sp.type_id']=$goods_type;	
				}
			
			  	$count= D('Product')->getProductkcinfo_ListByMap_count($map);
				$sql_page=ceil($count/$rows);  
				if($page>$sql_page) $page=1;	
				$wblist = D('Product')->getProductkcinfo_ListByMap($map,"$sidx $sord",$page,$rows);		
			
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
	
	
	
	
	
	public function getshangpininfolist()
	{
		if(IS_AJAX)
		{
			$page = I('get.page',1,'int');
			$rows = I('get.rows',20,'int');
			$sord = I('get.sord','','string')?:'desc';
			$sidx = I('get.sidx','','string')?:'dtInsertTime';
						
		    $goods_name    = I('get.goods_name','','string');
			$goods_type      = I('get.goods_type','','string'); 
			$goods_barcode      = I('get.goods_barcode','','string'); 
									  								 
			$map = array(); 
			$map['sp.wbid']=session('wbid');			
						
			if(!empty($goods_name))
			{
			  $map['sp.goods_name']=array('LIKE','%'.$goods_name.'%');
			}  
			
		    if(!empty($goods_type))
			{
			  $map['sp.type_id']=$goods_type;
			} 
			
		    if(!empty($goods_barcode))
			{
			  $map['sp.barcode']=array('LIKE','%'.$goods_barcode.'%');
			} 
											
			$count= D('Product')->getProductinfoListByMap_count($map);
			$sql_page=ceil($count/$rows);  
			if($page>$sql_page) $page=1;	
			$wblist = D('Product')->getProductinfoListByMap($map,"$sidx $sord",$page,$rows);		
			
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
	

	
	
	public function hjkc()
	{
		$bOpen=$this->check_newcs_qx();
		if($bOpen===false)
		{		  
			$this->redirect('rate/fujia', array('cate_id' => 2), 1, '请在附加设置里开启设置...');	  
		}
		session('kc_position',2);
		$this->display();
	}
		
	public function getShangpinlist()
	{
	  if(IS_AJAX)
      {
		  $canshu=I('post.canshu','','string');
		  $map['goods_id']=array('IN',$canshu);
		  $map['wbid']=session('wbid');
		  $list=D('Product')->where($map)->select();
		  foreach($list as &$val)
		  {
			 $val['ck_num']=D('Productkc')->where(array('wbid'=>session('wbid'),'goods_id'=>$val['goods_id'],position=>0))->getField('num'); 
		  }
		 
		  $this->ajaxReturn($list);
	  }		  
	}
	

	
	

	
	public function getShangpinkucuinfolist()
	{
	  if(IS_AJAX)
      {  
		  $canshu=I('post.canshu','','string');
		  $map['goods_id']=array('IN',$canshu);
		  $map['wbid']=session('wbid');
		  $map['position']=0;
		  $list=D('Productkc')->where($map)->select();
		  foreach($list as &$val)
		  {
			$val['ck_num']=$val['num'];  
			$val['goods_name']=D('Product')->where(array('goods_id'=>$val['goods_id'],'wbid'=>session('wbid')))->getField('goods_name');    
			$val['hj_num']= D('Productkc')->where(array('position'=>1,'goods_id'=>$val['goods_id'],'wbid'=>session('wbid')))->getField('num');  
		  }
		  
		  $this->ajaxReturn($list);
	  }		  
	}
	


	
	public function jiaoban_edit_set()
	{
		if(IS_AJAX)
		{    
	
	        if(!checkToken($_POST['token']))
			{  
		        writelog('jiaoban_edit_set---重复提交','jiaoban');
				$data['status']=-2;          								
			    $this->ajaxReturn($data);
                return;   			   
			}
			else
			{
				//writelog('jiaoban_edit_set---未重复提交','jiaoban');
			}
	
	
	        $type_list=D('ProductType')->select(); 
	        $post_order_no = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);  //网站订单号
			$post_order_no='JB'.$post_order_no;
			$wbid=session('wbid');
          
		    $dtInsertTime=date('Y-m-d H:i:s',time());             
			$temptime=strtotime($dtInsertTime);				
			$dtJiaobanXiaoshouTime=date('Y-m-d H:i:s',strtotime('-2 second ',$temptime));
			  			
			$str=I('post.goodsinfo','','string');				
			$sumje=I('post.sumje','','string');
						
			$sumje_wx_khd=I('post.sumje_wx_khd','','string');
			$sumje_zfb_khd=I('post.sumje_zfb_khd','','string');
			$sumje_cash_khd=I('post.sumje_cash_khd','','string');
			$sumje_cash_bt=I('post.sumje_cash_bt','','string');
			$sumje_wx_bt=I('post.sumje_wx_bt','','string');
			$sumje_zfb_bt=I('post.sumje_zfb_bt','','string');
			
							
            $str=htmlspecialchars_decode($str); 		
			$jiaoban_goodslist=json_decode($str,true);
										
			if(empty($jiaoban_goodslist))
			{	
              $data['status']=-1;
			  $this->ajaxReturn($data);
			  return;	
			}	
			
			
			
						//获取当前该网吧所有的商品数据
			$map=array();
			$map['kc.wbid']=$wbid;
			$map['kc.position']=1;
			$map['info.is_zuhe']=array('neq',1);
			$map['info.deleted']=0;
			
			$i=0;
			$list=array();
			$all_goods_list=D('Productkc')->getAllzuhegoodsListByMap($map);

			foreach($all_goods_list as $val)
			{

				$is_zuhe=$val['is_zuhe'];
				
				$bFind=false;
				foreach($jiaoban_goodslist as $val2)
				{
					if($val['goods_id']==$val2['goods_id'])
					{					
				        $list[$i]['goods_id']=$val2['goods_id'];
						$list[$i]['old_hj_num']=$val2['old_hj_num'];
						$list[$i]['now_hj_num']=$val2['now_hj_num'];
						$list[$i]['shangjia_num']=$val2['shangjia_num'];
						$list[$i]['xiajia_num']=$val2['xiajia_num'];	
                        $bFind=true;						
						break;
					}	
				}
				

			    if($bFind==false)
				{
					$map=array();
					$map['wbid']=$wbid;
					$map['goods_id']=$val['goods_id'];
					$map['dtInsertTime']=array('BETWEEN',array($lastshifttime,$nowtime));
					
					$list[$i]['goods_id']=$val['goods_id'];
					if(empty($val['num']))
					{
						$list[$i]['old_hj_num']=0;
						$list[$i]['now_hj_num']=0;
					}
					else
                    {
						$list[$i]['old_hj_num']=$val['num'];
						$list[$i]['now_hj_num']=$val['num'];						
					}	

             															
					if($is_zuhe==0)
					{
						$xijia_num=D('Productsxjmx')->where($map)->sum('num');
					}
					else if($is_zuhe==1)
                    {
						$xijia_num=D('Productsxjmxzh')->where($map)->sum('num');					
					}	
					
					if(empty($xijia_num))
					{
						$list[$i]['xiajia_num']=0;					
					}
					else
                    {
						$list[$i]['xiajia_num']=$xijia_num;											
					}
					
					
				}	
				$i++;

			}
			
        
			$info='交班: 总金额：'.$sumje;
			$result=true;
			D()->startTrans();  //启用事务
			
			$nowtime=date('Y-m-d H:i:s',time());
		    $lastshiftinfo= D('Productjb')->where(array('wbid'=>$wbid))->order('shifttime desc')->limit(1)->find();
		    $lastshifttime=date('Y-m-d H:i:s',strtotime($lastshiftinfo['shifttime']));
			

			$sumje=0;
			$jiaobaninfo='交班销售: ';
			foreach( $jiaoban_goodslist as &$val)
			{
				$goods_info=D('Product')->where(array('wbid'=>$wbid,'goods_id'=>$val['goods_id']))->find();				
			    if($goods_info['is_zuhe']==2)
				{	
			        $jiaobanmx_insert_data=array();
					$jiaobanmx_insert_data['goods_id']      =$val['goods_id'];	              				
					$jiaobanmx_insert_data['type_id']       = $goods_info['type_id'];
					$jiaobanmx_insert_data['old_hj_num']    =$val['old_hj_num'];
					$jiaobanmx_insert_data['shangjia_num']  =$val['shangjia_num'];
					$jiaobanmx_insert_data['xiajia_num']  =$val['xiajia_num'];
					$jiaobanmx_insert_data['now_hj_num']    =$val['now_hj_num']; 			
					$jiaobanmx_insert_data['post_order_no'] =$post_order_no;
					$jiaobanmx_insert_data['position']      =1;
					$jiaobanmx_insert_data['wbid']=$wbid;
					$jiaobanmx_insert_data['zuhe_flag']=1;
					$jiaobanmx_insert_data['is_zuhe_goods']=2;
					$jiaobanmx_insert_data['dtInsertTime']=$dtInsertTime;
					$jiaobanmx_insert_data['shifttime']=$dtInsertTime;				
					if(D('Productjbmx')->add($jiaobanmx_insert_data)===false)
					{		
                       writelog('----3--1--error-','jiaoban');				
						$result=false;
					}
	
			        $map1=array();
				    $map1['wbid']=$wbid;	
				    $map1['dtInsertTime']=array('BETWEEN',array($lastshifttime,$nowtime));
					$map1['goods_id']=$val['goods_id'];
					$real_xiaoshou_num= D('Productxsmxzh')->where($map1)->sum('xiaoshou_num');
                    
					if(empty($real_xiaoshou_num))
					{
					  $real_xiaoshou_num=0;	
					}	
					$jiaoban_xiaoshou_num=$val['shangjia_num']+$val['old_hj_num']- $val['xiajia_num']-$val['now_hj_num']-$real_xiaoshou_num;
													
					//交班销售数量
					$xiaoshoumx_insert_data=array();
					$xiaoshoumx_insert_data['goods_id']=$val['goods_id'];				
					$xiaoshoumx_insert_data['xiaoshou_num']   =$jiaoban_xiaoshou_num;
					$xiaoshoumx_insert_data['ck_num']  =D('Productkc')->where(array('wbid'=>session('wbid'),'position'=>0,'goods_id'=>$val['goods_id']))->getField('num');
					$xiaoshoumx_insert_data['hj_num']  =$val['shangjia_num']+$val['old_hj_num']- $val['xiajia_num']-$real_xiaoshou_num;    //销售时候货架上的数量
					$xiaoshoumx_insert_data['je']=$jiaoban_xiaoshou_num * $goods_info['shou_price'];
					$xiaoshoumx_insert_data['price']=$goods_info['shou_price'];				
					$xiaoshoumx_insert_data['post_order_no']=$post_order_no;
					$xiaoshoumx_insert_data['ordertype']=3;    //交班销售
					$xiaoshoumx_insert_data['wbid']=$wbid;
					$xiaoshoumx_insert_data['is_zuhe_goods']=2;
					$xiaoshoumx_insert_data['zuhe_flag']=1;
					$xiaoshoumx_insert_data['zuhe_id']=0;
					$xiaoshoumx_insert_data['dtInsertTime']=$dtJiaobanXiaoshouTime;
					
					$sumje+= $xiaoshoumx_insert_data['je'];
																	
					if(D('Productxsmxzh')->add($xiaoshoumx_insert_data)===false)
					{		
                        writelog('----3--2--error-','jiaoban');				
						$result=false;
					}					
				}
				else if($goods_info['is_zuhe']==0)
                {
					$jiaobanmx_insert_data=array();
					$jiaobanmx_insert_data['goods_id']      =$val['goods_id'];	              				
					$jiaobanmx_insert_data['type_id']       = $goods_info['type_id'];
					$jiaobanmx_insert_data['old_hj_num']    =$val['old_hj_num'];
					$jiaobanmx_insert_data['shangjia_num']  =$val['shangjia_num'];
					$jiaobanmx_insert_data['xiajia_num']  =$val['xiajia_num'];
					$jiaobanmx_insert_data['now_hj_num']    =$val['now_hj_num']; 			
					$jiaobanmx_insert_data['post_order_no'] =$post_order_no;
					$jiaobanmx_insert_data['position']      =1;
					$jiaobanmx_insert_data['wbid']=$wbid;
					$jiaobanmx_insert_data['zuhe_flag']=1;
					$jiaobanmx_insert_data['is_zuhe_goods']=0;
					$jiaobanmx_insert_data['dtInsertTime']=$dtInsertTime;
					$jiaobanmx_insert_data['shifttime']=$dtInsertTime;				
					if(D('Productjbmx')->add($jiaobanmx_insert_data)===false)
					{		
                        writelog('----3--3--error-','jiaoban');				
						$result=false;
					}
				    $map1=array();
				    $map1['wbid']=$wbid;	
				    $map1['dtInsertTime']=array('BETWEEN',array($lastshifttime,$nowtime));
					$map1['goods_id']=$val['goods_id'];	
					$real_xiaoshou_num= D('Productxsmx')->where($map1)->sum('xiaoshou_num'); 
					
					if(empty($real_xiaoshou_num))
					{
					  $real_xiaoshou_num=0;	
					}	
					$jiaoban_xiaoshou_num=$val['shangjia_num']+$val['old_hj_num']- $val['xiajia_num']-$val['now_hj_num']-$real_xiaoshou_num;
													
					//交班销售数量
					$xiaoshoumx_insert_data=array();
					$xiaoshoumx_insert_data['goods_id']=$val['goods_id'];				
					$xiaoshoumx_insert_data['xiaoshou_num']   =$jiaoban_xiaoshou_num;
					$xiaoshoumx_insert_data['ck_num']  =D('Productkc')->where(array('wbid'=>session('wbid'),'position'=>0,'goods_id'=>$val['goods_id']))->getField('num');
					$xiaoshoumx_insert_data['hj_num']  =$val['shangjia_num']+$val['old_hj_num']- $val['xiajia_num']-$real_xiaoshou_num;    //销售时候货架上的数量
					$xiaoshoumx_insert_data['je']=$jiaoban_xiaoshou_num * $goods_info['shou_price'];
					$xiaoshoumx_insert_data['price']=$goods_info['shou_price'];				
					$xiaoshoumx_insert_data['post_order_no']=$post_order_no;
					$xiaoshoumx_insert_data['ordertype']=3;    //交班销售
					$xiaoshoumx_insert_data['wbid']=$wbid;
					$xiaoshoumx_insert_data['dtInsertTime']=$dtJiaobanXiaoshouTime;
					$xiaoshoumx_insert_data['is_zuhe_goods']=0;
					$xiaoshoumx_insert_data['zuhe_flag']=1;
					$xiaoshoumx_insert_data['zuhe_id']=0;
					
					$sumje+= $xiaoshoumx_insert_data['je'];
																	
					if(D('Productxsmx')->add($xiaoshoumx_insert_data)===false)
					{	
                        writelog('----3--4--error-','jiaoban');				
						$result=false;
					}
				} 	
				
																													
				$val['goods_name']=$goods_info['goods_name'];				
				$jiaobaninfo.= $val['goods_name'].':'.$jiaoban_xiaoshou_num.' ';
																					
				if(D('Productkc')->where(array('wbid'=>$wbid,'goods_id'=>$val['goods_id'],'position'=>1))->setField('num',$val['now_hj_num'])===false)
                {
					$result=false;
				}					
			}
			

					
			//增加一条交班销售总记录				         
			$xiaoshou_insert_data['post_order_no']=$post_order_no;
			$xiaoshou_insert_data['ordertype']=3;
			$xiaoshou_insert_data['wbid']=$wbid;
			$xiaoshou_insert_data['detailinfo']=$jiaobaninfo;
			$xiaoshou_insert_data['sum_sp_je']=$sumje;	
			$xiaoshou_insert_data['sum_sr_je']=$sumje;
			$xiaoshou_insert_data['sum_zl_je']=0;	
			$xiaoshou_insert_data['beizhu']='交班销售';
			$xiaoshou_insert_data['zuhe_flag']=1;
			$xiaoshou_insert_data['operator']=session('username');							
			$xiaoshou_insert_data['dtInsertTime']=$dtJiaobanXiaoshouTime;
												
			if(D('Productxs')->add($xiaoshou_insert_data)===false)
			{		
                writelog('----4--2--error-','jiaoban');		
				$result=false;
			}				
						
			//增加一条正常交班总记录			
			$jiaoban_insert_data['post_order_no']=$post_order_no;
			$jiaoban_insert_data['wbid']=session('wbid');
			$jiaoban_insert_data['info']=$info;		
			$jiaoban_insert_data['dtInsertTime']=$dtInsertTime;
			$jiaoban_insert_data['shifttime']=$dtInsertTime;
			$jiaoban_insert_data['bz']=I('post.bz','','string');
			$jiaoban_insert_data['sumje']=$sumje;						
			$jiaoban_insert_data['sumje_wx_khd']=$sumje_wx_khd;
			$jiaoban_insert_data['sumje_zfb_khd']=$sumje_zfb_khd;
			$jiaoban_insert_data['sumje_cash_khd']=$sumje_cash_khd;
			$jiaoban_insert_data['sumje_cash_bt']=$sumje_cash_bt;
			$jiaoban_insert_data['sumje_wx_bt']=$sumje_wx_bt;
			$jiaoban_insert_data['sumje_zfb_bt']=$sumje_zfb_bt;																									
			$jiaoban_insert_data['operator']=session('username');
			$jiaoban_insert_data['detailinfo']=json_encode($list);
			$jiaoban_insert_data['zuhe_flag']=1;

			if(D('Productjb')->add($jiaoban_insert_data)===false)
			{	
                writelog('----4--5--error-','jiaoban');			
				$result=false;
			}				
			
			if($result)
            {
			  writelog('----5-----','jiaoban');	
              D()->commit();  //提交事务
               $data['status']=1;
            }
            else
            {
				writelog('----5---1 error--','jiaoban');
              D()->rollback();    //回滚
              $data['status']=-1;
            }
								
			$this->ajaxReturn($data);
		}	
	}
	
	
	
	public function getjbtongjilist()
	{
		if(IS_AJAX)
		{
			$page = I('get.page',1,'int');
			$rows = I('get.rows',20,'int');
			$sord = I('get.sord','','string')?:'desc';
			$sidx = I('get.sidx','','string')?:'dtInsertTime';

			$goods_name     = I('get.goods_name','','string');
			$position       = I('get.position',0,'int');   
			$post_order_no  = I('get.cardno4','','string');
            $daterange      = I('get.daterange4','','string');			
			
            
			
					 
			$map = array(); 
	
			$map['wbid']=session('wbid');
			
		
			if(!empty($position))
			{
			  $map['position']=(int)$position-1;
			}  
			
			if(!empty($goods_name))
			{
			  $map['info']=array('LIKE','%'.$goods_name.'%');
			}  
			if(!empty($post_order_no))
			{
			  $map['post_order_no']=array('LIKE','%'.$post_order_no.'%');
			} 
			
			if(!empty($daterange))  
			{
			  list($start,$end) = explode(' - ',$daterange);    
			  $start = str_replace('/','-',$start);            
			  $end = str_replace('/','-',$end);                
			  $map['dtInsertTime'] = array('BETWEEN',array($start,$end));
			}
						
			$count= D('Productjb')->getjbtongjilistByMap_count($map);
			$sql_page=ceil($count/$rows);  
			if($page>$sql_page) $page=1;	
			$wblist = D('Productjb')->getjbtongjilistByMap($map,"$sidx $sord",$page,$rows);		
			
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
	
	
	public function jiaobanmxshow()
	{		
        $res=I('get.res','','string'); 
		
		$res=base64_decode($res);
		$res=urldecode($res);
		
      
		
	    $res=json_decode($res,true);
		
	
		$this->assign('info',$res);
		$this->display();
	}
	
	
	
	public function jiaobanmx()
	{
		$post_order_no=I('get.post_order_no','','string');
		session('post_order_no',$post_order_no); 
						
		$this->display();
	}
	
	
	public function getjb_fenleitongji_mx_listByMap()
	{
		if(IS_AJAX)
		{
			$page = I('get.page',1,'int');
			$rows = I('get.rows',20,'int');
			$sord = I('get.sord','','string')?:'desc';
			$sidx = I('get.sidx','','string')?:'dtInsertTime';

			$goods_name    = I('get.goods_name','','string');
  			           							 
			$map = array(); 
	
	
				
		
			$count= D('Productjbmx')->getjb_fenleitongji_mx_listByMap_count($map);
			$sql_page=ceil($count/$rows);  
			if($page>$sql_page) $page=1;	
			$wblist = D('Productjbmx')->getjb_fenleitongji_mx_listByMap($map,"$sidx $sord",$page,$rows);		
			
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
	 public function getjbtongji_mx_listByMap()
	{
		if(IS_AJAX)
		{
			$map = array(); 
			$map['jbmx.wbid']=session('wbid');
			$map['jbmx.post_order_no']= session('post_order_no');
		    $zuhe_goods_list=D('Pro')->select(); 
			
			
			
			$no_zuhe_list=D()->select();
			
			
			if(empty($zuhe_goods_list))
			{
				$list=$zuhe_goods_list;
			}	
			
			if(empty($no_zuhe_list))
			{
				$list=$no_zuhe_list;
			}	
			
			if(!empty($zuhe_goods_list) && !empty($no_zuhe_list))
			{
				$list=array_merge($no_zuhe_list,$zuhe_order_list);
			}
			 
	
	
			$this->ajaxReturn($list);
		}  
	}
	*/
	
	
	
    public function getjbtongji_mx_listByMap()
	{
		if(IS_AJAX)
		{
			$page = I('get.page',1,'int');
			$rows = I('get.rows',20,'int');
			$sord = I('get.sord','','string')?:'desc';
			$sidx = I('get.sidx','','string')?:'dtInsertTime';

			$goods_name    = I('get.goods_name','','string');
			$position      = I('get.position',0,'int');   			          							 
			$map = array(); 
	
			$map['jbmx.wbid']=session('wbid');
			$map['jbmx.post_order_no']= session('post_order_no');
			
		
			 /*	
			 if(!empty($goods_name))
			 {
			   $map['info.goods_name']=array('LIKE','%'.$goods_name.'%');
			 }  
			*/
			
		
			$count= D('Productjbmx')->getjbtongji_mx_listByMap_count($map);
			$sql_page=ceil($count/$rows);  
			if($page>$sql_page) $page=1;	
			$wblist = D('Productjbmx')->getjbtongji_mx_listByMap($map,"$sidx $sord",$page,$rows);		
			
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
	
	
	
	
	
	public function get_shangxiajiainfo_list()
	{
		if(IS_AJAX)
		{
			$page = I('get.page',1,'int');
			$rows = I('get.rows',20,'int');
			$sord = I('get.sord','','string')?:'desc';
			$sidx = I('get.sidx','','string')?:'dtInsertTime';

			$goods_name    = I('get.goods_name','','string');
			$position      = I('get.position',0,'int');   
			$post_order_no = I('get.cardno2','','string'); 
			$daterange     = I('get.daterange2','','string'); 
			
            
			
					 
			$map = array(); 
	
			$map['wbid']=session('wbid');
			
		
			if(!empty($position))
			{
			  $map['shangxia_status']=(int)$position-1;
			}  
			
			if(!empty($post_order_no))
			{
			  $map['post_order_no']=array('LIKE','%'.$post_order_no.'%');
			} 
			
			if(!empty($daterange))  
			{
			  list($start,$end) = explode(' - ',$daterange);    
			  $start = str_replace('/','-',$start);            
			  $end = str_replace('/','-',$end);                
			  $map['dtInsertTime'] = array('BETWEEN',array($start,$end));
			}
					
		
			$count= D('Productsxj')->getsxjinfoListByMap_count($map);
			$sql_page=ceil($count/$rows);  
			if($page>$sql_page) $page=1;	
			$wblist = D('Productsxj')->getsxjinfoListByMap($map,"$sidx $sord",$page,$rows);		
			
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
	
	
	public function xiajiamx()
	{
		$post_order_no=I('get.post_order_no','','string');
		session('post_order_no',$post_order_no); 
		$this->display();
	}
	public function shangjiamx()
	{
		$post_order_no=I('get.post_order_no','','string');
		session('post_order_no',$post_order_no); 
		$this->display();
	}
	
	
	public function get_shangxiajiainfo_mx_list()
	{	
		$wbid=session('wbid');
		$post_order_no=session('post_order_no');	
		$type_list=D('ProductType')->select();
		
		$list=array();
		
		$map=array();
		$map['wbid']=$wbid;
		$map['post_order_no']=$post_order_no;			
		$zuhe_flag=D('Productsxj')->where($map)->getField('zuhe_flag');
		
	
	
				
		if($zuhe_flag==0) //兼容原来的查询
		{
			$map=array();
			$map['info.wbid']=$wbid;
			$map['sxjmx.post_order_no']=$post_order_no;			
			$list=D('Productsxjmx')->getsxjinfo_mx_ListByMap2($map);
		}
		else if($zuhe_flag==1)
        {
			
			 //1.获取纯组合商品的列表
			$map=array();
			$map['info.wbid']=$wbid;
			$map['sxjmx.post_order_no']=$post_order_no;		
			$zuhe_order_list=D('Productsxjmxzh')->getsxjinfo_mx_zh_ListByMap($map);
			foreach($zuhe_order_list as &$val)
			{																											
				foreach($type_list as $val1)
				{
					if($val1['type_id']==$val['type_id'])
					{
						$val['type_name']=$val1['type_name'];	
						break;
					}	
				}	
                $val['is_zuhe']=2;				
			}
			
			
			
			//2.获取实际卖的商品列表 is_zuhe=0		
			$map=array();
			$map['info.wbid']=$wbid;
			$map['sxjmx.post_order_no']=$post_order_no;
			$map['sxjmx.is_zuhe_goods']=0;			
			$shiji_order_list0=D('Productsxjmx')->getsxjinfo_mx_ListByMap2($map);
			foreach($shiji_order_list0 as &$val)
			{
				$val['is_zuhe']=0;
				foreach($type_list as $val2)
				{
					if($val2['type_id']==$val['type_id'])
					{
						$val['type_name']=$val2['type_name'];	
						break;
					}	
				}
			}
			
			
			
			if(!empty($zuhe_order_list))
			{
				//3.实际卖的商品列表   is_zuhe=1
				$map=array();
				$map['info.wbid']=$wbid;
				$map['sxjmx.post_order_no']=$post_order_no;
				$map['sxjmx.is_zuhe_goods']=1;			
				$shiji_order_list1=D('Productsxjmx')->getsxjinfo_mx_ListByMap2($map);
				
				
				
				if(!empty($shiji_order_list0))
				{
					$list=array_merge($shiji_order_list0,$zuhe_order_list);
				}
				else
                {
					$list=$zuhe_order_list;
				}					
				
						
				if(!empty($list))
				{
					foreach($list as &$val)
					{		
                      				
						if($val['is_zuhe']==2)
						{		
                          					
							$zuhe_id=$val['goods_id'];
							$i=0;
							$zuhe_goods_array=array();	
												
							foreach($shiji_order_list1 as &$val1)
							{
							   if($zuhe_id==$val1['zuhe_id'])
							   {
								
									$zuhe_goods_array[$i]['goods_id']=$val1['goods_id'];
									foreach($type_list as $val2)
									{
										if($val2['type_id']==$val['type_id'])
										{
											$list[$i]['type_name']=$val2['type_name'];	
											break;
										}	
									}				
									$zuhe_goods_array[$i]['goods_name']=$val1['goods_name'];
									$zuhe_goods_array[$i]['unit']=$val1['unit'];
									$zuhe_goods_array[$i]['guige']=$val1['guige'];
									$zuhe_goods_array[$i]['shou_price']=$val1['shou_price'];
									$zuhe_goods_array[$i]['je']=$val1['je'];																	
									$zuhe_goods_array[$i]['is_zuhe']=1;
									$zuhe_goods_array[$i]['zuhe_id']=$val1['zuhe_id'];
									$zuhe_goods_array[$i]['num']=$val1['num'];
									$zuhe_goods_array[$i]['hj_num']=$val1['hj_num'];
									$zuhe_goods_array[$i]['ck_num']=$val1['ck_num'];																
									$i++;
									
							   } 	   
							}

							$val['zuhelist']=$zuhe_goods_array;			
						}										
					}
				}
                 			
			}
			else
            {
				$list=$shiji_order_list0;
			}				
            			
		}														
	
		
        $this->ajaxReturn($list);
	}
	
	/*
    public function get_shangxiajiainfo_mx_list()
	{
	    session('goods_id_list',null);  
		session('plch_status','0');
		$wbid=session('wbid');
		$post_order_no=session('post_order_no'); ;		
		$type_list=D('ProductType')->select();
		
		//is_zuhe=0  和is_zuhe=2
		$map=array();
		$map['info.wbid']=$wbid;
		$map['sxjmx.post_order_no']=$post_order_no;	
        $map['info.is_zuhe']=array('neq',1);		
	    $goods_list=D('Productsxjmx')->getsxjinfo_mx_ListByMap2($map);
		
	    

		
		$i=0;
		$list=array();
		foreach($goods_list as &$val)
		{
			$is_zuhe=$val['is_zuhe'];			
			if($is_zuhe==0)
		    {
				$list[$i]['goods_id']=$val['goods_id'];
				foreach($type_list as &$val1)
				{
					if($val1['type_id']==$val['type_id'])
					{
					    $list[$i]['type_name']=$val1['type_name'];	
						break;
					}	
				}				
				$list[$i]['goods_name']=$val['goods_name'];
				$list[$i]['unit']=$val['unit'];
				$list[$i]['guige']=$val['guige'];
				$list[$i]['shou_price']=$val['shou_price'];
				$list[$i]['is_zuhe']=$val['is_zuhe'];
				$list[$i]['zuhe_id']=$val['zuhe_id'];
				$list[$i]['num']=$val['num'];
				$list[$i]['hj_num']=$val['hj_num'];
				$list[$i]['ck_num']=$val['ck_num'];				
				$list[$i]['zuhelist']='';
			}
			else if($is_zuhe==2)
            {								
				$zuhe_id=$val['goods_id'];
				$zuhe_goods_array=array();
				
				$map=array();				
				$map['info.wbid']=$wbid;
				$map['sxjmx.post_order_no']=$post_order_no;	
				$map['info.is_zuhe']=1;	
                $map['info.zuhe_id']=$zuhe_id;		
				
				$zuhe_goods_array=D('Productsxjmx')->getsxjinfo_mx_ListByMap2($map);			
				$list[$i]['goods_id']=$val['goods_id'];
				foreach($type_list as &$val1)
				{
					if($val1['type_id']==$val['type_id'])
					{
					    $list[$i]['type_name']=$val1['type_name'];	
						break;
					}	
				}
				$list[$i]['goods_name']=$val['goods_name'];
				$list[$i]['unit']=$val['unit'];
				$list[$i]['guige']=$val['guige'];
				$list[$i]['shou_price']=$val['shou_price'];
				$list[$i]['is_zuhe']=$val['is_zuhe'];
				$list[$i]['zuhe_id']=$val['zuhe_id'];
				$list[$i]['num']=$val['num'];
				$list[$i]['hj_num']=$val['hj_num'];
				$list[$i]['ck_num']=$val['ck_num'];
                $list[$i]['zuhelist']=$zuhe_goods_array;				
			}										
			$i++;
		}
        $this->ajaxReturn($list);
	}
	*/
	
		
	
	public function chuhuo_edit_set()
	{
		if(IS_AJAX)
		{   
	        if(!checkToken($_POST['token']))
			{  
		        writelog('xiajia_edit_set---重复提交');
				$data['status']=-2;          								
			    $this->ajaxReturn($data);
                return;   			   
			}
			else
			{
				//writelog('xiajia_edit_set---未重复提交');
			}
	        $post_order_no = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);  //网站订单号
			$post_order_no='CH'.$post_order_no;
            $dtInsertTime=date('Y-m-d H:i:s',time());     			
			$str=I('post.goodsinfo','','string');	
			$unit=I('post.unit','','string');	
			$sumje=I('post.sumje','','string');	
            $str=htmlspecialchars_decode($str); 		
			$chuhuo_goodslist=json_decode($str,true);
			

						
			if(empty($chuhuo_goodslist))
			{	
              $data['status']=-1;
			  $this->ajaxReturn($data);
			  return;	
			}	
			



			$info=' 总金额：'.$sumje.',';
			$result=true;
			D()->startTrans();  //启用事务
			
			foreach( $chuhuo_goodslist as &$val)
			{
				$chuhuomx_insert_data['goods_id']=$val['goods_id'];
				$chuhuomx_insert_data['sumnum']     =$val['sumnum'];
				$chuhuomx_insert_data['hj_num']  =D('Productkc')->where(array('wbid'=>session('wbid'),'position'=>1,'goods_id'=>$val['goods_id']))->getField('num');
				$chuhuomx_insert_data['ck_num']  =D('Productkc')->where(array('wbid'=>session('wbid'),'position'=>0,'goods_id'=>$val['goods_id']))->getField('num');
				$chuhuomx_insert_data['post_order_no']=$post_order_no;
				$chuhuomx_insert_data['position']=0;
				if($unit=='1')
				{
				  $chuhuomx_insert_data['jian_num']=$val['num'];	
				  $chuhuomx_insert_data['type']=1;
				}
				else if($unit=='2')
                {
				  $chuhuomx_insert_data['jian_num']=0;		
				  $chuhuomx_insert_data['type']=2;
				}		
                $chuhuomx_insert_data['price']=$val['price'];					
				$chuhuomx_insert_data['je']=$val['price']*$val['num'];			
				$chuhuomx_insert_data['wbid']=session('wbid');
				$chuhuomx_insert_data['dtInsertTime']=$dtInsertTime;
				$chuhuomx_insert_data['operate']=session('username');
				$chuhuomx_insert_data['zuhe_flag']=1;
				
				if(D('Productjchmx')->add($chuhuomx_insert_data)===false)
				{
					$result=false;
				}	
				
				$now_ckkc_num= D('Productkc')->where(array('wbid'=>session('wbid'),'position'=>0,'goods_id'=>$val['goods_id']))->getField('num');
				if($val['sumnum'] >= $now_ckkc_num)
				{
					$now_sj_chuhuo_num =$now_ckkc_num;
				}
				else
                {
					$now_sj_chuhuo_num =$val['sumnum'];
				}
		
				
				if(D('Productkc')->where(array('wbid'=>session('wbid'),'position'=>0,'goods_id'=>$val['goods_id']))->setDec('num',$now_sj_chuhuo_num)===false)
				{
					$result=false;
				}	
				
	
				$val['goods_name']=D('Product')->where(array('wbid'=>session('wbid'),'goods_id'=>$val['goods_id']))->getField('goods_name');				
				$info.= $val['goods_name'].':'.$val['sumnum'].' ';
			}
			
			//更新库存表
		
	            
				
				$chuhuo_insert_data['post_order_no']=$post_order_no;
				$chuhuo_insert_data['jch_type']=0;
				$chuhuo_insert_data['wbid']=session('wbid');
				$chuhuo_insert_data['info']=$info;	
                $chuhuo_insert_data['sumje']=$sumje;
                $chuhuo_insert_data['zuhe_flag']=1;					
				$chuhuo_insert_data['dtInsertTime']=$dtInsertTime;
				$chuhuo_insert_data['bz']=I('post.bz','','string');
				$chuhuo_insert_data['operator']=session('username');
				$chuhuo_insert_data['detailinfo']=$str;
				
				if(D('Productjch')->add($chuhuo_insert_data)===false)
				{
					$result=false;
				}	
			
			
			if($result)
            {
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
		session('post_order_no',$post_order_no); 				
		$this->display();
	}
	
    public function getchuhuotongji_mx_listByMap()
	{
		if(IS_AJAX)
		{
			$page = I('get.page',1,'int');
			$rows = I('get.rows',20,'int');
			$sord = I('get.sord','','string')?:'desc';
			$sidx = I('get.sidx','','string')?:'dtInsertTime';

			//$goods_name    = I('get.goods_name','','string');
		   
			
            
			
					 
			$map = array(); 	
			$map['jhmx.wbid']=session('wbid');
			$map['jhmx.post_order_no']= session('post_order_no');
			

					
		
			$count= D('Productjchmx')->getjhtongji_mx_listByMap_count($map);
			$sql_page=ceil($count/$rows);  
			if($page>$sql_page) $page=1;	
			$wblist = D('Productjchmx')->getjhtongji_mx_listByMap($map,"$sidx $sord",$page,$rows);		
			
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

	
	public function pdhj_edit_set()
	{
		if(IS_AJAX)
		{    
	        $post_order_no = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);  //网站订单号
			
			$post_order_no='PDHJ'.$post_order_no;
            
			$dtInsertTime=date('Y-m-d H:i:s',time());     			
			$str=I('post.goodsinfo','','string');
            // $position=I('post.position','','string');
			
			// $unit=I('post.unit','','string');	 //按件按个
			$sumje=I('post.sumje','','string');	
			
            $str=htmlspecialchars_decode($str); 		
			$pdhj_goodslist=json_decode($str,true);
			
		    $wbid=session('wbid');
						
			if(empty($pdhj_goodslist))
			{	
              $data['status']=-1;
			  $this->ajaxReturn($data);
			  return;	
			}	
			
			$bs_je=I('post.bs_je','0','float');
			$by_je=I('post.by_je','0','float');	
			$info='盘点货架: 报损金额：'.$bs_je.',报溢金额:'.$by_je;
			$result=true;
			D()->startTrans();  //启用事务
			
			foreach( $pdhj_goodslist as &$val)
			{
				$goodsinfo=D('Product')->where(array('wbid'=>$wbid,'goods_id'=>$val['goods_id']))->find();
				$pdhjmx_insert_data['goods_id']=$val['goods_id'];
				$pdhjmx_insert_data['pd_num']  =$val['pdnum'];
				$pdhjmx_insert_data['hj_num']  =D('Productkc')->where(array('wbid'=>session('wbid'),'position'=>1,'goods_id'=>$val['goods_id']))->getField('num');
				$pdhjmx_insert_data['ck_num']  =D('Productkc')->where(array('wbid'=>session('wbid'),'position'=>0,'goods_id'=>$val['goods_id']))->getField('num');				
			    $pdhjmx_insert_data['position'] =1;							
				$pdhjmx_insert_data['price']=$val['price'];	
				$pdhjmx_insert_data['sunyi_num']=$val['sunyi_num'];	
				$pdhjmx_insert_data['sunyi_status']=$val['sunyi_status'];			
				$pdhjmx_insert_data['sunyi_je']=$val['price']*$val['sunyi_num'];
				$pdhjmx_insert_data['post_order_no']=$post_order_no;				
				$pdhjmx_insert_data['wbid']=session('wbid');
				$pdhjmx_insert_data['dtInsertTime']=$dtInsertTime;
				$pdhjmx_insert_data['operator']=session('username');
												
				if(D('Productpdmx')->add($pdhjmx_insert_data)===false)
				{
					$result=false;
				}
                if(D('Productkc')->where(array('goods_id'=>$val['goods_id'],'position'=>1))->setField('num',$val['pdnum'])===false)
				{
					$result=false;
				}												
				$shangxiajia_num= $pdhjmx_insert_data['hj_num']- $val['pdnum'];
				      //新增一条上架或者下架记录
				//报损：下架
				 if($shangxiajia_num >0)
				 {
					if($goodsinfo['is_zuhe']==0)
					{
						$shangxiajiamx_insert_data=array(); 
						$shangxiajiamx_insert_data['goods_id']=$val['goods_id'];
						$shangxiajiamx_insert_data['num']     =$pdhjmx_insert_data['hj_num']- $val['pdnum'];
						$shangxiajiamx_insert_data['ck_num']  =$pdhjmx_insert_data['ck_num'];
						$shangxiajiamx_insert_data['hj_num']  =$pdhjmx_insert_data['hj_num'];						
						$shangxiajiamx_insert_data['post_order_no']=$post_order_no;
						$shangxiajiamx_insert_data['shangxia_status']=1;					
						$shangxiajiamx_insert_data['dtInsertTime']=$dtInsertTime;
						$shangxiajiamx_insert_data['wbid']=session('wbid');						
						$shangxiajiamx_insert_data['zuhe_id']=0;
						$shangxiajiamx_insert_data['is_zuhe_goods']=0;
						$shangxiajiamx_insert_data['zuhe_flag']=1;						
						$shangxiajiamx_insert_data['bz']='盘点货架报损 --减少商品数量'.$shangxiajiamx_insert_data['num']; 
						if(D('Productsxjmx')->add($shangxiajiamx_insert_data)===false)
						{
							$result=false;
						} 
					}
					else if($goodsinfo['is_zuhe']==2)
                    {
						$shangxiajiamx_insert_data=array(); 
						$shangxiajiamx_insert_data['goods_id']=$val['goods_id'];
						$shangxiajiamx_insert_data['num']     =$pdhjmx_insert_data['hj_num']- $val['pdnum'];
						$shangxiajiamx_insert_data['ck_num']  =$pdhjmx_insert_data['ck_num'];
						$shangxiajiamx_insert_data['hj_num']  =$pdhjmx_insert_data['hj_num'];						
						$shangxiajiamx_insert_data['post_order_no']=$post_order_no;
						$shangxiajiamx_insert_data['shangxia_status']=1;					
						$shangxiajiamx_insert_data['dtInsertTime']=$dtInsertTime;
						$shangxiajiamx_insert_data['wbid']=session('wbid');						
						$shangxiajiamx_insert_data['zuhe_id']=0;
						$shangxiajiamx_insert_data['is_zuhe_goods']=2;
						$shangxiajiamx_insert_data['zuhe_flag']=1;						
						$shangxiajiamx_insert_data['bz']='盘点货架报损 --减少商品数量'.$shangxiajiamx_insert_data['num']; 
						if(D('Productsxjmxzh')->add($shangxiajiamx_insert_data)===false)
						{
							$result=false;
						}
					}						
		
					
			
				 }      //报溢 ：上架
				 else if($shangxiajia_num <0)
                 {
					if($goodsinfo['is_zuhe']==0)
                    {
						$shangxiajiamx_insert_data=array();  
						$shangxiajiamx_insert_data['goods_id']= $val['goods_id'];
						$shangxiajiamx_insert_data['num']     = $val['pdnum']-$pdhjmx_insert_data['hj_num'];
						$shangxiajiamx_insert_data['ck_num']  =$pdhjmx_insert_data['ck_num'];
						$shangxiajiamx_insert_data['hj_num']  =$pdhjmx_insert_data['hj_num'];					
						$shangxiajiamx_insert_data['post_order_no']=$post_order_no;
						$shangxiajiamx_insert_data['shangxia_status']=0;
						$shangxiajiamx_insert_data['wbid']=session('wbid');
						$shangxiajiamx_insert_data['dtInsertTime']=$dtInsertTime;
						$shangxiajiamx_insert_data['zuhe_id']=0;
						$shangxiajiamx_insert_data['is_zuhe_goods']=0;
						$shangxiajiamx_insert_data['zuhe_flag']=1;
						$shangxiajiamx_insert_data['bz']='盘点货架报溢 --增加商品数量'.$shangxiajiamx_insert_data['num'];					
						if(D('Productsxjmx')->add($shangxiajiamx_insert_data)===false)
						{					
							$result=false;
						}
					}
					else if($goodsinfo['is_zuhe']==2)
                    {
						$shangxiajiamx_insert_data=array();  
						$shangxiajiamx_insert_data['goods_id']= $val['goods_id'];
						$shangxiajiamx_insert_data['num']     = $val['pdnum']-$pdhjmx_insert_data['hj_num'];
						$shangxiajiamx_insert_data['ck_num']  =$pdhjmx_insert_data['ck_num'];
						$shangxiajiamx_insert_data['hj_num']  =$pdhjmx_insert_data['hj_num'];					
						$shangxiajiamx_insert_data['post_order_no']=$post_order_no;
						$shangxiajiamx_insert_data['shangxia_status']=0;
						$shangxiajiamx_insert_data['wbid']=session('wbid');
						$shangxiajiamx_insert_data['dtInsertTime']=$dtInsertTime;
					    $shangxiajiamx_insert_data['zuhe_id']=0;
						$shangxiajiamx_insert_data['is_zuhe_goods']=2;
						$shangxiajiamx_insert_data['zuhe_flag']=1;
						$shangxiajiamx_insert_data['bz']='盘点货架报溢 --增加商品数量'.$shangxiajiamx_insert_data['num'];					
						if(D('Productsxjmxzh')->add($shangxiajiamx_insert_data)===false)
						{					
							$result=false;
						}
					}						
 
				 }					 
				
				
				  					              							
			}
							
			$pdhj_insert_data['post_order_no']=$post_order_no;
			$pdhj_insert_data['position']=1;
			$pdhj_insert_data['wbid']=session('wbid');
			$pdhj_insert_data['info']=$info;		
			$pdhj_insert_data['dtInsertTime']=$dtInsertTime;
			$pdhj_insert_data['bs_je']=$bs_je;
			$pdhj_insert_data['by_je']=$by_je;
			$pdhj_insert_data['bz']=I('post.bz','','string');
			$pdhj_insert_data['operator']=session('username');
			$pdhj_insert_data['detailinfo']=$str;
			$pdhj_insert_data['zuhe_flag']=1;
			
			if(D('Productpd')->add($pdhj_insert_data)===false)
			{
				$result=false;
			}	
			
			
			if($result)
            {
              D()->commit();  //提交事  
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
	public function pdck_edit_set()
	{
		if(IS_AJAX)
		{    
	        $post_order_no = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);  //网站订单号
			$post_order_no ='PDCK'.$post_order_no;
            $dtInsertTime=date('Y-m-d H:i:s',time());     			
			$str=I('post.goodsinfo','','string');
			$sumje=I('post.sumje','','string');	
			
            $str=htmlspecialchars_decode($str); 		
			$pdck_goodslist=json_decode($str,true);
			
		 						
			if(empty($pdck_goodslist))
			{	
              $data['status']=-1;
			  $this->ajaxReturn($data);
			  return;	
			}	
				
			$bs_je=I('post.bs_je','0','float');
			$by_je=I('post.by_je','0','float');	
			$info='盘点仓库: 报损金额：'.$bs_je.',报溢金额:'.$by_je;
			
			$result=true;
			D()->startTrans();  //启用事务
			
			foreach( $pdck_goodslist as &$val)
			{
				$pdckmx_insert_data['goods_id']=$val['goods_id'];
				$pdckmx_insert_data['pd_num']  =$val['pdnum'];
				$pdckmx_insert_data['hj_num']  =D('Productkc')->where(array('wbid'=>session('wbid'),'position'=>1,'goods_id'=>$val['goods_id']))->getField('num');
				$pdckmx_insert_data['ck_num']  =D('Productkc')->where(array('wbid'=>session('wbid'),'position'=>0,'goods_id'=>$val['goods_id']))->getField('num');				
			    $pdckmx_insert_data['position'] =0;							
				$pdckmx_insert_data['price']=$val['price'];	
				$pdckmx_insert_data['sunyi_num']=$val['sunyi_num'];	
				$pdckmx_insert_data['sunyi_status']=$val['sunyi_status'];			
				$pdckmx_insert_data['sunyi_je']=$val['price']*$val['sunyi_num'];
				$pdckmx_insert_data['post_order_no']=$post_order_no;				
				$pdckmx_insert_data['wbid']=session('wbid');
				$pdckmx_insert_data['dtInsertTime']=$dtInsertTime;
				$pdckmx_insert_data['operator']=session('username');
				
				if(D('Productpdmx')->add($pdckmx_insert_data)===false)
				{
					$result=false;
				}	
   
                if(D('Productkc')->where(array('goods_id'=>$val['goods_id'],'position'=>0))->setField('num',$val['pdnum'])===false)
				{
					$result=false;
				}  				
				  					              							
			}
			
			
	
			$pdck_insert_data['post_order_no']=$post_order_no;
			$pdck_insert_data['position']=0;
			$pdck_insert_data['wbid']=session('wbid');
			$pdck_insert_data['info']=$info;		
			$pdck_insert_data['dtInsertTime']=$dtInsertTime;
			$pdck_insert_data['bs_je']=$bs_je;
			$pdck_insert_data['by_je']=$by_je;
			$pdck_insert_data['bz']=I('post.bz','','string');
			$pdck_insert_data['operator']=session('username');
			$pdck_insert_data['detailinfo']=$str;
			
			if(D('Productpd')->add($pdck_insert_data)===false)
			{
				$result=false;
			}	
			
			
			if($result)
            {
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
	public function getxstongjilist()
	{
		if(IS_AJAX)
		{
			$page = I('get.page',1,'int');
			$rows = I('get.rows',20,'int');
			$sord = I('get.sord','','string')?:'desc';
			$sidx = I('get.sidx','','string')?:'dtInsertTime';

			$goods_name    = I('get.goods_name','','string');
			$position      = I('get.position',0,'int');   
			$post_order_no = I('get.cardno1','','string');
			$daterange     = I('get.daterange1','','string');
           								 
			$map = array(); 	
			$map['wbid']=session('wbid');				
			if(!empty($post_order_no))
			{
			  $map['post_order_no']=array('LIKE','%'.$post_order_no.'%');
			}  
			
			if(!empty($goods_name))
			{
			  $map['info']=array('LIKE','%'.$goods_name.'%');
			}  
			
			if(!empty($daterange))  
			{
			  list($start,$end) = explode(' - ',$daterange);    
			  $start = str_replace('/','-',$start);            
			  $end = str_replace('/','-',$end);                
			  $map['dtInsertTime'] = array('BETWEEN',array($start,$end));
			}
											
			$count= D('Productxs')->getxstongjilistByMap_count($map);
			$sql_page=ceil($count/$rows);  
			if($page>$sql_page) $page=1;	
			$wblist = D('Productxs')->getxstongjilistByMap($map,"$sidx $sord",$page,$rows);	
            
   	
			
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
	
	
	public function getxstongjilist_zongzhang()
	{
	    if(IS_AJAX)
		{
			$page = I('get.page',1,'int');
			$rows = I('get.rows',10,'int');
			$sord = I('get.sord','','string')?:'desc';
			$sidx = I('get.sidx','','string')?:'dtInsertTime';
						
			
			$goods_name = I('get.spname5','','string');
			$daterange     = I('get.daterange5','','string');
									  								 
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
			


											
			$count= D('Product')->getProductinfoListByMap_count_zongzhang($map);
			$sql_page=ceil($count/$rows);  
			if($page>$sql_page) $page=1;	
			$wblist = D('Product')->getProductinfoListByMap_zongzhang($map,"$sidx $sord",$page,$rows);		
			
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
	
	
	public function getAllShangpinLiuchenglist()
	{
	    if(IS_AJAX)
		{
			$page = I('get.page',1,'int');
			$rows = I('get.rows',10,'int');
			$sord = I('get.sord','','string')?:'desc';
			$sidx = I('get.sidx','','string')?:'dtInsertTime';
						
			
		//	$daterange     = I('get.daterange5','','string');
			$goods_id     = I('get.goods_id','','string');
			$post_order_no     = I('get.order_num','','string');
			
			$map = array(); 
			$map['wbid']=session('wbid');	
			
			
			if(!empty($goods_id ))
			{
				$map['goods_id']=$goods_id;	
			}	
			if(!empty($post_order_no ))
			{
				$map['post_order_no']=$post_order_no;	
			}	
								  								 
											
			$count= D('GoodsmxView')->getGoodsmxListByMap_count($map);
			

			
			$sql_page=ceil($count/$rows);  
			if($page>$sql_page) $page=1;	
			$wblist = D('GoodsmxView')->getGoodsmxListByMap($map,"$sidx $sord",$page,$rows);		
			
	        $response = new \stdClass();
			$response->records = $wblist['count'];
			$response->page = $page;
			$response->total = ceil($wblist['count'] / $rows);
			//$response->goods_name = $goods_name;
			foreach($wblist['list'] as $key => $value)
			{       
			  $response->rows[$key]['id'] = $key;
			  $response->rows[$key]['cell'] = $value;
			}
			$this->ajaxReturn($response);
			
		}  
	}
	
	
	public function xiaoshou_mx_zongzhang()
	{
		$goods_id=I('get.goods_id','','string');
		$this->assign('goods_id',$goods_id);		
		$this->display();
	}
	
	public function getxstongjilist_mx_zongzhang()
	{
		if(IS_AJAX)
		{
			$page = I('get.page',1,'int');
			$rows = I('get.rows',20,'int');
			$sord = I('get.sord','','string')?:'desc';
			$sidx = I('get.sidx','','string')?:'dtInsertTime';

			$goods_id    = I('get.goods_id','','string'); 
			$post_order_no = I('get.cardno1','','string');
			$daterange     = I('get.daterange1','','string');
           								 
			$map = array(); 	
			$map['wbid']=session('wbid');
            $map['goods_id']=$goods_id;	
			
			if(!empty($post_order_no))
			{
			  $map['post_order_no']=array('LIKE','%'.$post_order_no.'%');
			}  
				 		
			if(!empty($daterange))  
			{
			  list($start,$end) = explode(' - ',$daterange);    
			  $start = str_replace('/','-',$start);            
			  $end = str_replace('/','-',$end);                
			  $map['dtInsertTime'] = array('BETWEEN',array($start,$end));
			}
											
			$count= D('Productxsmx')->getxstongji_mx_listByMap_count_zongzhang($map);
			$sql_page=ceil($count/$rows);  
			if($page>$sql_page) $page=1;	
			$wblist = D('Productxsmx')->getxstongji_mx_listByMap_zongzhang($map,"$sidx $sord",$page,$rows);	
            
         	
			
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
	
	

	
	
	public function getxstongjilist_kehuduan()
	{
		if(IS_AJAX)
		{
			$page = I('get.page',1,'int');
			$rows = I('get.rows',20,'int');
			$sord = I('get.sord','','string')?:'desc';
			$sidx = I('get.sidx','','string')?:'dtCharuTime';

			$goods_name    = I('get.goods_name','','string');
			$position      = I('get.position',0,'int');   
			$post_order_no = I('get.cardno1','','string');
			$daterange     = I('get.daterange1','','string');
			
			$lingqu_status     = I('get.lingqu_status','','string');
			
			
			
           								 
			$map = array(); 	
			$map['wbid']=session('wbid');	
            $map['pay_position']=1;			
			if(!empty($post_order_no))
			{
			  $map['post_order_no']=$post_order_no;
			}  
			
			// if(!empty($lingqu_status))
			// {
			  // $map['lingqu_status']=$lingqu_status -1;
			// }	
			/*
			if(!empty($goods_name))
			{
			  $map['info']=array('LIKE','%'.$goods_name.'%');
			}  
			
			if(!empty($daterange))  
			{
			  list($start,$end) = explode(' - ',$daterange);    
			  $start = str_replace('/','-',$start);            
			  $end = str_replace('/','-',$end);                
			  $map['dtInsertTime'] = array('BETWEEN',array($start,$end));
			}
		 */								
			$count= D('Productxs')->getxstongjilistByMap_count($map);
			$sql_page=ceil($count/$rows);  
			if($page>$sql_page) $page=1;	
			$wblist = D('Productxs')->getxstongjilistByMap($map,"$sidx $sord",$page,$rows);	
            
            		
			
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
	
	
	
	public function getxstongjilist_batai()
	{
		if(IS_AJAX)
		{
			$page = I('get.page',1,'int');
			$rows = I('get.rows',20,'int');
			$sord = I('get.sord','','string')?:'desc';
			$sidx = I('get.sidx','','string')?:'dtInsertTime';

			//$goods_name    = I('get.goods_name','','string');
		//	$position      = I('get.position',0,'int');   
			//$post_order_no = I('get.cardno1','','string');
			//$daterange     = I('get.daterange1','','string');
			
			//$lingqu_status     = I('get.lingqu_status','','string');
			
			
			
           								 
			$map = array(); 	
			$map['wbid']=session('wbid');	
			
			if(!empty($post_order_no))
			{
			  $map['post_order_no']=$post_order_no;
			}  
							
			$count= D('Productxsbt')->getxstongjilist_bt_ByMap_count($map);
			$sql_page=ceil($count/$rows);  
			if($page>$sql_page) $page=1;	
			$wblist = D('Productxsbt')->getxstongjilist_bt_ByMap($map,"$sidx $sord",$page,$rows);	
            
            		
			
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
	
	
	public function xiaoshoumx()
	{
		$post_order_no=I('get.post_order_no','','string');
		session('post_order_no',$post_order_no); 		
		$this->display();
	}
	
	public function xiaoshoumx_khd()
	{
		$post_order_no=I('get.post_order_no','','string');
		session('post_order_no',$post_order_no); 		
		$this->display();
	}
	

	/*
    public function getxstongji_mx_listByMap()
	{
		if(IS_AJAX)
		{
			$page = I('get.page',1,'int');
			$rows = I('get.rows',20,'int');
			$sord = I('get.sord','','string')?:'desc';
			$sidx = I('get.sidx','','string')?:'dtInsertTime';

			$goods_name    = I('get.goods_name','','string');
			$position      = I('get.position',0,'int');   
			
            
			
					 
			$map = array(); 
	
			$map['xsmx.wbid']=session('wbid');
			$map['xsmx.post_order_no']= session('post_order_no');
			
		
			if(!empty($position))
			{
			  $map['xsmx.position']=(int)$position-1;
			}  
			
			if(!empty($goods_name))
			{
			  $map['info.goods_name']=array('LIKE','%'.$goods_name.'%');
			}  
			
			
		
			$count= D('Productxsmx')->getxstongji_mx_listByMap_count($map);
			$sql_page=ceil($count/$rows);  
			if($page>$sql_page) $page=1;	
			$wblist = D('Productxsmx')->getxstongji_mx_listByMap($map,"$sidx $sord",$page,$rows);		
			
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
	
	public function getjchtongjilist()
	{
		if(IS_AJAX)
		{
			$page = I('get.page',1,'int');
			$rows = I('get.rows',20,'int');
			$sord = I('get.sord','','string')?:'desc';
			$sidx = I('get.sidx','','string')?:'dtInsertTime';

			$goods_name    = I('get.goods_name','','string');
			$jch_position  = I('get.jch_position','','string');  
            $post_order_no = I('get.cardno','','string');	
            $daterange     = I('get.daterange','','string');			
			
            
							 
			$map = array(); 	
			$map['wbid']=session('wbid');	


			

			// $map['position']=(int)$position-1;
	 
			
			if(!empty($goods_name))
			{
			  $map['info']=array('LIKE','%'.$goods_name.'%');
			}  
			
			if(!empty($post_order_no))
			{
			  $map['post_order_no']=array('LIKE','%'.$post_order_no.'%');
			}  
			
			if(!empty($daterange))  
			{
			  list($start,$end) = explode(' - ',$daterange);    
			  $start = str_replace('/','-',$start);            
			  $end = str_replace('/','-',$end);                
			  $map['dtInsertTime'] = array('BETWEEN',array($start,$end));
			}
			
			
			
			if($jch_position=='1')
			{
				$map['jch_type']=1;
			}
			else if($jch_position=='2')
            {
				$map['jch_type']=0;
			}
				
			$count= D('Productjch')->gejhtongjilistByMap_count($map);
			$sql_page=ceil($count/$rows);  
			if($page>$sql_page) $page=1;	
			$wblist = D('Productjch')->gejhtongjilistByMap($map,"$sidx $sord",$page,$rows);															
			
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
	
	public function jinhuomx()
	{
		$post_order_no=I('get.post_order_no','','string');
		session('post_order_no',$post_order_no);
		$wbid=session('wbid');
		
		// $map['wbid']=$wbid;
		// $map['post_order_no'] =$post_order_no;
		// $goods_id_list=D('Productjchmx')->where($map)->getField('goods_id',true);
		
		

	
		// $map['wbid']=$wbid;
		// $map['deleted'] =0;
		// $map['goods_id']=array('in',$goods_id_list);
		
		// $goodslist=D('Product')->where($map)->select();
		
		// foreach($goodslist as &$val)
		// {
		  // $val['value']=$val['goods_pinyin'].','.$val['goods_name'].','.$val['goods_quanpin'];
		  // $val['label']=$val['goods_pinyin'].','.$val['goods_name'].','.$val['goods_quanpin'];
		  // $val['hj_num']=D('Productkc')->where(array('goods_id'=>$val['goods_id'],'wbid'=>$wbid,'position'=>1))->getField('num');
		  // $val['ck_num']=D('Productkc')->where(array('goods_id'=>$val['goods_id'],'wbid'=>$wbid,'position'=>0))->getField('num');	  	  
		// }
		// $this->assign('goodslist',json_encode($goodslist));	
		
		
		$this->display();
	}
	

	/*
	public function getjhtongji_mx_listByMap()
	{
	    session('goods_id_list',null);  
		session('plch_status','0');
		$wbid=session('wbid');
		$post_order_no=session('post_order_no'); ;		
		$type_list=D('ProductType')->select();
		
		//is_zuhe=0  和is_zuhe=2
		$map=array();
		$map['info.wbid']=$wbid;
		$map['jhmx.post_order_no']=$post_order_no;		
	    $goods_list=D('Productjchmx')->getjhtongji_mx_listByMap2($map);
		
		
		$i=0;
		$list=array();
		foreach($goods_list as &$val)
		{
			$zuhe_flag=$val['zuhe_flag'];
			$is_zuhe=$val['is_zuhe'];
			if($zuhe_flag==0)
			{
				$list[$i]['goods_id']=$val['goods_id'];
				foreach($type_list as &$val1)
				{
					if($val1['type_id']==$val['type_id'])
					{
					    $list[$i]['type_name']=$val1['type_name'];	
						break;
					}	
				}				
				$list[$i]['goods_name']=$val['goods_name'];
				$list[$i]['unit']=$val['unit'];
				$list[$i]['guige']=$val['guige'];
				$list[$i]['shou_price']=$val['shou_price'];
				$list[$i]['is_zuhe']=$val['is_zuhe'];
				$list[$i]['zuhe_id']=$val['zuhe_id'];
				$list[$i]['num']=$val['sumnum'];
				$list[$i]['hj_num']=$val['hj_num'];
				$list[$i]['ck_num']=$val['ck_num'];
                $list[$i]['position']=$val['position'];				
				$list[$i]['zuhelist']='';
				$i++;
			}
			else
            {
				if($is_zuhe==1)
				{
					if($val['position']==0) //进到仓库
					{
						$list[$i]['goods_id']=$val['goods_id'];
						foreach($type_list as &$val1)
						{
							if($val1['type_id']==$val['type_id'])
							{
								$list[$i]['type_name']=$val1['type_name'];	
								break;
							}	
						}				
						$list[$i]['goods_name']=$val['goods_name'];
						$list[$i]['unit']=$val['unit'];
						$list[$i]['guige']=$val['guige'];
						$list[$i]['shou_price']=$val['shou_price'];
						$list[$i]['is_zuhe']=$val['is_zuhe'];
						$list[$i]['zuhe_id']=$val['zuhe_id'];
						$list[$i]['num']=$val['sumnum'];
						$list[$i]['hj_num']=$val['hj_num'];
						$list[$i]['ck_num']=$val['ck_num'];
						$list[$i]['position']=$val['position'];					
						$list[$i]['zuhelist']='';
						$i++;
					}	
			
				}			
				else if($is_zuhe==2)
				{								
					$zuhe_id=$val['goods_id'];
					$zuhe_goods_array=array();
					
					$map=array();				
					$map['info.wbid']=$wbid;
					$map['jhmx.post_order_no']=$post_order_no;	
					$map['info.is_zuhe']=1;	
					$map['info.zuhe_id']=$zuhe_id;	
					$map['jhmx.position']=1;	  //进到货架
					
					
					$zuhe_goods_array=D('Productjchmx')->getjhtongji_mx_listByMap2($map);			
					$list[$i]['goods_id']=$val['goods_id'];
					foreach($type_list as &$val1)
					{
						if($val1['type_id']==$val['type_id'])
						{
							$list[$i]['type_name']=$val1['type_name'];	
							break;
						}	
					}
					$list[$i]['goods_name']=$val['goods_name'];
					$list[$i]['unit']=$val['unit'];
					$list[$i]['guige']=$val['guige'];
					$list[$i]['shou_price']=$val['shou_price'];
					$list[$i]['is_zuhe']=$val['is_zuhe'];
					$list[$i]['zuhe_id']=$val['zuhe_id'];
					$list[$i]['num']=$val['sumnum'];
					$list[$i]['hj_num']=$val['hj_num'];
					$list[$i]['ck_num']=$val['ck_num'];
					$list[$i]['position']=$val['position'];
					$list[$i]['zuhelist']=$zuhe_goods_array;
					$i++;				
				}
			} 				
			
						

										
		}
        $this->ajaxReturn($list);
		
		
	}
	
	*/
	
	public function getjhtongji_mx_listByMap()
	{
        $wbid=session('wbid');
		$post_order_no=session('post_order_no');	
		$type_list=D('ProductType')->select();
		
		$list=array();
		
		$map=array();
		$map['wbid']=$wbid;
		$map['post_order_no']=$post_order_no;			
		$zuhe_flag=D('Productjch')->where($map)->getField('zuhe_flag');
			
		if($zuhe_flag==0) //兼容原来的查询
		{
			$map=array();
			$map['info.wbid']=$wbid;
			$map['jhmx.post_order_no']=$post_order_no;			
			$list=D('Productjchmx')->getjhtongji_mx_listByMap2($map);
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
                $val['is_zuhe']=0;				
			}
			
		}
		else if($zuhe_flag==1)
        {
			
			 //1.获取纯组合商品的listbiao
			$map=array();
			$map['info.wbid']=$wbid;
			$map['jhmx.post_order_no']=$post_order_no;		
			$zuhe_order_list=D('Productjchmxzh')->getjhtongji_mx_zh_listByMap($map);
			foreach($zuhe_order_list as &$val)
			{																											
				foreach($type_list as $val1)
				{
					if($val1['type_id']==$val['type_id'])
					{
						$val['type_name']=$val1['type_name'];	
						break;
					}	
				}
                $val['is_zuhe']=2;				
			}
			
			
			
			//2.获取实际卖的商品列表 is_zuhe=0		
			$map=array();
			$map['info.wbid']=$wbid;
			$map['jhmx.post_order_no']=$post_order_no;
			$map['jhmx.is_zuhe_goods']=0;			
			$shiji_order_list0=D('Productjchmx')->getjhtongji_mx_listByMap2($map);
			foreach($shiji_order_list0 as &$val)
			{
				foreach($type_list as $val1)
				{
					if($val1['type_id']==$val['type_id'])
					{
						$val['type_name']=$val1['type_name'];	
						break;
					}	
				}
				$val['is_zuhe']=0;
			}
			
			//2.1 获取实际进货的商品  is_zuhe=1,但是进到里货架里
			$map=array();
			$map['info.wbid']=$wbid;
			$map['jhmx.post_order_no']=$post_order_no;
			$map['jhmx.is_zuhe_goods']=1;
			$map['jhmx.position']=0;     //货架上			
			$shiji_order_list2=D('Productjchmx')->getjhtongji_mx_listByMap2($map);
			foreach($shiji_order_list2 as &$val)
			{
				foreach($type_list as $val1)
				{
					if($val1['type_id']==$val['type_id'])
					{
						$val['type_name']=$val1['type_name'];	
						break;
					}	
				}
				$val['is_zuhe']=1;
			}
			
			
			
			
			
			if(!empty($zuhe_order_list))
			{
				//3.实际卖的商品列表   is_zuhe=1
				$map=array();
				$map['info.wbid']=$wbid;
				$map['jhmx.post_order_no']=$post_order_no;
				$map['jhmx.is_zuhe_goods']=1;			
				$shiji_order_list1=D('Productjchmx')->getjhtongji_mx_listByMap2($map);
				
				if(!empty($shiji_order_list0))
				{
					$list=array_merge($shiji_order_list0,$zuhe_order_list);
				}
				else
                {
					$list=$zuhe_order_list;
				}	
                
				if(!empty($shiji_order_list2))
				{
					$list=array_merge($shiji_order_list2,$list);
				}
					
				
				
							
				if(!empty($list))
				{
					foreach($list as &$val)
					{									
						if($val['is_zuhe']==2)
						{								
							$zuhe_id=$val['goods_id'];
							$i=0;
							$zuhe_goods_array=array();	
												
							foreach($shiji_order_list1 as &$val1)
							{
							   if($zuhe_id==$val1['zuhe_id'])
							   {
									$zuhe_goods_array[$i]['goods_id']=$val1['goods_id'];
									foreach($type_list as $val2)
									{
										if($val2['type_id']==$val['type_id'])
										{
											$list[$i]['type_name']=$val2['type_name'];	
											break;
										}	
									}				
									$zuhe_goods_array[$i]['goods_name']=$val1['goods_name'];
									$zuhe_goods_array[$i]['unit']=$val1['unit'];
									$zuhe_goods_array[$i]['guige']=$val1['guige'];
									$zuhe_goods_array[$i]['shou_price']=$val1['shou_price'];
									$zuhe_goods_array[$i]['je']=$val1['je'];									
									$zuhe_goods_array[$i]['position']=$val1['position'];
									$zuhe_goods_array[$i]['is_zuhe']=1;
									$zuhe_goods_array[$i]['zuhe_id']=$val1['zuhe_id'];
									$zuhe_goods_array[$i]['num']=$val1['num'];
									$zuhe_goods_array[$i]['hj_num']=$val1['hj_num'];
									$zuhe_goods_array[$i]['ck_num']=$val1['ck_num'];			
									$i++;							 
							   } 	   
							}

							$val['zuhelist']=$zuhe_goods_array;			
						}										
					}
				}
                 			
			}
			else
            {
				$map=array();
				$map['info.wbid']=$wbid;
				$map['jhmx.post_order_no']=$post_order_no;		
				$shiji_order_list1=D('Productjchmx')->getjhtongji_mx_listByMap2($map);	
				foreach($shiji_order_list1 as &$val)
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
				
				$list=$shiji_order_list1;
			}				
            			
		}														
	
		
        $this->ajaxReturn($list);
		
	}
	
	public function getxstongji_mx_listByMap()
	{
	
		$wbid=session('wbid');
		$post_order_no=session('post_order_no');	
		$type_list=D('ProductType')->select();
	
		$list=array();
		
		$map=array();
		$map['wbid']=$wbid;
		$map['post_order_no']=$post_order_no;			
		$zuhe_flag=D('Productxs')->where($map)->getField('zuhe_flag');
				
		if($zuhe_flag==0) //兼容原来的查询
		{
			$map=array();
			$map['info.wbid']=$wbid;
			$map['xsmx.post_order_no']=$post_order_no;			
			$list=D('Productxsmx')->getxstongji_mx_listByMap2($map);
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
                $val['is_zuhe']=0;				
			}
		}
		else if($zuhe_flag==1)
        {
			
			 //1.获取纯组合商品的listbiao
			$map=array();
			$map['info.wbid']=$wbid;
			$map['xsmx.post_order_no']=$post_order_no;		
			$zuhe_order_list=D('Productxsmxzh')->getxstongji_mx_zh_listByMap($map);
			foreach($zuhe_order_list as &$val)
			{																											
				foreach($type_list as $val1)
				{
					if($val1['type_id']==$val['type_id'])
					{
						$val['type_name']=$val1['type_name'];	
						break;
					}	
				}
                $val['is_zuhe']=2;				
			}
			
			
			
			//2.获取实际卖的商品列表 is_zuhe=0		
			$map=array();
			$map['info.wbid']=$wbid;
			$map['xsmx.post_order_no']=$post_order_no;
			$map['xsmx.is_zuhe_goods']=0;			
			$shiji_order_list0=D('Productxsmx')->getxstongji_mx_listByMap2($map);
			
		
			
			if(!empty($shiji_order_list0))
			{
			    foreach($shiji_order_list0 as &$val)
				{
					foreach($type_list as $val1)
					{
						if($val1['type_id']==$val['type_id'])
						{
							$val['type_name']=$val1['type_name'];	
							break;
						}	
					}
					$val['is_zuhe']=0;
				}
			}	

			
			
			
			if(!empty($zuhe_order_list))
			{
				//3.实际卖的商品列表   is_zuhe=1
				$map=array();
				$map['info.wbid']=$wbid;
				$map['xsmx.post_order_no']=$post_order_no;
				$map['xsmx.is_zuhe_goods']=1;			
				$shiji_order_list1=D('Productxsmx')->getxstongji_mx_listByMap2($map);
				
				
				
				if(!empty($shiji_order_list0))
				{
					$list=array_merge($shiji_order_list0,$zuhe_order_list);
				}
				else
                {
					$list=$zuhe_order_list;
				}					
				
							
				if(!empty($list))
				{
					foreach($list as &$val)
					{									
						if($val['is_zuhe_goods']==2)
						{								
							$zuhe_id=$val['goods_id'];
							$i=0;
							$zuhe_goods_array=array();	
												
							foreach($shiji_order_list1 as &$val1)
							{
							   if($zuhe_id==$val1['zuhe_id'])
							   {
									$zuhe_goods_array[$i]['goods_id']=$val1['goods_id'];
									foreach($type_list as $val2)
									{
										if($val2['type_id']==$val['type_id'])
										{
											$list[$i]['type_name']=$val2['type_name'];	
											break;
										}	
									}				
									$zuhe_goods_array[$i]['goods_name']=$val1['goods_name'];
									$zuhe_goods_array[$i]['unit']=$val1['unit'];
									$zuhe_goods_array[$i]['guige']=$val1['guige'];
									$zuhe_goods_array[$i]['shou_price']=$val1['shou_price'];
									$zuhe_goods_array[$i]['je']=$val1['je'];
									
									
									$zuhe_goods_array[$i]['is_zuhe']=1;
									$zuhe_goods_array[$i]['zuhe_id']=$val1['zuhe_id'];
									$zuhe_goods_array[$i]['xiaoshou_num']=$val1['xiaoshou_num'];
									$zuhe_goods_array[$i]['hj_num']=$val1['hj_num'];
									$zuhe_goods_array[$i]['ck_num']=$val1['ck_num'];			
									$i++;							 
							   } 	   
							}

							$val['zuhelist']=$zuhe_goods_array;			
						}										
					}
				}
                 			
			}
			else
            {
				$list=$shiji_order_list0;
			}				
            			
		}														
		
		
        $this->ajaxReturn($list);
	}
	
	public function getpdtongjilist()
	{
		if(IS_AJAX)
		{
			$page = I('get.page',1,'int');
			$rows = I('get.rows',20,'int');
			$sord = I('get.sord','','string')?:'desc';
			$sidx = I('get.sidx','','string')?:'dtInsertTime';

			$goods_name    = I('get.goods_name','','string');
			// $position      = I('get.position3',0,'int');  
            $post_order_no = I('get.cardno3','','string');  
			$daterange     = I('get.daterange3','','string');			        
						 
			$map = array(); 
			 $map['wbid']=session('wbid');
			
		
			// if(!empty($position))
			// {
			  // $map['position']=(int)$position-1;
			// }  
			
			if(!empty($goods_name))
			{
			  $map['info']=array('LIKE','%'.$goods_name.'%');
			}  
			
			if(!empty($post_order_no))
			{
			  $map['post_order_no']=array('LIKE','%'.$post_order_no.'%');
			} 
			
			if(!empty($daterange))  
			{
			  list($start,$end) = explode(' - ',$daterange);    
			  $start = str_replace('/','-',$start);            
			  $end = str_replace('/','-',$end);                
			  $map['dtInsertTime'] = array('BETWEEN',array($start,$end));
			}
						
			$count= D('Productpd')->getpdtongjilistByMap_count($map);
			$sql_page=ceil($count/$rows);  
			if($page>$sql_page) $page=1;	
			$wblist = D('Productpd')->getpdtongjilistByMap($map,"$sidx $sord",$page,$rows);		
			
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
	
	
	public function pandianmx()
	{
		$post_order_no=I('get.post_order_no','','string');
		session('post_order_no',$post_order_no); 			
		$this->display();
	}
	
    public function getpdtongji_mx_listByMap()
	{
		if(IS_AJAX)
		{
			$page = I('get.page',1,'int');
			$rows = I('get.rows',20,'int');
			$sord = I('get.sord','','string')?:'desc';
			$sidx = I('get.sidx','','string')?:'dtInsertTime';

			 $goods_name    = I('get.goods_name','','string');
			// $position      = I('get.position',0,'int');   			          							 
			$map = array(); 
	
			$map['pdmx.wbid']=session('wbid');
			$map['pdmx.post_order_no']= session('post_order_no');
			
	
			
			if(!empty($goods_name))
			{
			  $map['info.goods_name']=array('LIKE','%'.$goods_name.'%');
			}  
			
			
		
			$count= D('Productpdmx')->getpdtongji_mx_listByMap_count($map);
			$sql_page=ceil($count/$rows);  
			if($page>$sql_page) $page=1;	
			$wblist = D('Productpdmx')->getpdtongji_mx_listByMap($map,"$sidx $sord",$page,$rows);		
			
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
	
	public function kehuduan()
	{
		$bOpen=$this->check_newcs_qx();
		if($bOpen===false)
		{		  
			$this->redirect('rate/fujia', array('cate_id' => 2), 1, '请在附加设置里开启设置...');	  
		}
		$this->display();
	}
	
    public function shangpin_lingqu_set()
	{	       
        if(IS_POST)
        {        
	        $result=true;
			D()->startTrans();
			
			$wbid=session('wbid');
			$nowtime=date('Y-m-d H:i:s',time());
			$post_order_no = I('post.post_order_no','','string');
			
			$xiaoshou_update_data=array();
			$xiaoshou_update_data['lingqu_status']=1;
			$xiaoshou_update_data['dtInsertTime']=$nowtime;
			
			

			if(D('Productxs')->where(array('post_order_no'=>$post_order_no,'wbid'=>$wbid))->save($xiaoshou_update_data)==false)
			{
				writelog('---1--error-','kehuduan');
				$result=false;
			}	
			
			
			if(D('Productxsmx')->where(array('post_order_no'=>$post_order_no,'wbid'=>$wbid))->setField('dtInsertTime',$nowtime)==false)
			{
				writelog('---2--error-','kehuduan');
				$result=false;
			}

				
			$map=array();
			$map['wbid']=$wbid;
			$map['post_order_no']=$post_order_no;
			$map['is_zuhe_goods']=2;		
			$zuhe_goods_list=D('Productxsmxzh')->where($map)->select();
			if(!empty($zuhe_goods_list))
			{
				if(D('Productxsmxzh')->where(array('post_order_no'=>$post_order_no,'wbid'=>$wbid))->setField('dtInsertTime',$nowtime)==false)
				{
					writelog('---3--1--error-','kehuduan');
					$result=false;
				}	
			}	
			
         			
			  					  			
			$map=array();
			$map['wbid']=$wbid;
			$map['post_order_no']=$post_order_no;
			$map['is_zuhe_goods']=0;
			
			$shiji_goods_list=D('Productxsmx')->where($map)->select();
		
			

			
		
			$goodsmxlist=array();
			$i=0;			
			foreach($shiji_goods_list as &$val)
			{
				
               	$goodsmxlist[$i]['goods_name'] =D('Product')->where(array('goods_id'=>$val['goods_id'],'wbid'=>$wbid))->getField('goods_name');
				$goodsmxlist[$i]['num']        =$val['xiaoshou_num'];
				$goodsmxlist[$i]['price']      =$val['price'];
				$goodsmxlist[$i]['qianshu']    =$val['je'];			
                
                $hj_num=D('Productkc')->where(array('goods_id'=>$val['goods_id'],'wbid'=>$wbid,'position'=>1))->getField('num');
				if(empty($hj_num))
				{
					$hj_num=0;
				}	
				
				if($hj_num  -$val['xiaoshou_num'] >=0 )
				{
					if(D('Productkc')->where(array('goods_id'=>$val['goods_id'],'wbid'=>$wbid,'position'=>1))->setDec('num',$val['xiaoshou_num'])===false)
					{
						writelog('---4---1----error-','kehuduan');
						$result=false;		
					}
				}
				else
                {
					writelog('---库存不足领取失败-$wbid='.$wbid.'goods_id='.$val['goods_id'].'--error-','kehuduan');
					$result=false;	
				}					
				
				
									            							 
				$i++;				
			}
			
			if(!empty($zuhe_goods_list))
			{
				foreach($zuhe_goods_list as &$val)
				{
					
					$goodsmxlist[$i]['goods_name'] =D('Product')->where(array('goods_id'=>$val['goods_id'],'wbid'=>$wbid))->getField('goods_name');
					$goodsmxlist[$i]['num']        =$val['xiaoshou_num'];
					$goodsmxlist[$i]['price']      =$val['price'];
					$goodsmxlist[$i]['qianshu']    =$val['je'];	
					
					$hj_num=D('Productkc')->where(array('goods_id'=>$val['goods_id'],'wbid'=>$wbid,'position'=>1))->getField('num');
					if(empty($hj_num))
					{
						$hj_num=0;
					}
					
					if($hj_num  -$val['xiaoshou_num'] >=0 )
					{
						if(D('Productkc')->where(array('goods_id'=>$val['goods_id'],'wbid'=>$wbid,'position'=>1))->setDec('num',$val['xiaoshou_num'])===false)
						{
							writelog('---5----1-error--','kehuduan');
							$result=false;		
						}
					}
					else
					{
						writelog('---库存不足领取失败-$wbid='.$wbid.'goods_id='.$val['goods_id'].'--error-','kehuduan');
						$result=false;	
					}																	            							 
					$i++;
				}
			}	
			
	
			  
				 
			$one_goods_order =D('Productxs')->where(array('wbid'=>$wbid,'post_order_no'=>$post_order_no))->find(); 
			$total_je       =   $one_goods_order['sum_sp_je'];
			$total_zhifu    = $one_goods_order['sum_sr_je'];
			$total_zhaoling = $one_goods_order['sum_zl_je'];
			 
			 
			
			$list['goodsinfo'] = $goodsmxlist;
			$list['total_je'] = $total_je;
			$list['total_zhifu'] = $total_zhifu;
			$list['total_zhaoling'] = $total_zhaoling;
			$list['dt'] = $one_goods_order['dtInsertTime'] ;
							
					
			if($result)
			{
				
				D()->commit();
				$list['status']=1;
			}
			else
            {
				writelog('---6----1-error--','kehuduan');
			  D()->rollback();
              $list['status']=-1;			  
			}				
	               
       }          
  
        $this->ajaxReturn($list);  
	}
	
	
 public function shangpin_lingqu_quxiao_set()
	{	       
        if(IS_POST)
        {        
	      $result=true;
          D()->startTrans();		  
          $post_order_no = I('post.post_order_no','','string');
		  
		 

          if(D('Productxs')->where(array('post_order_no'=>$post_order_no))->setField('lingqu_status',2)===false)
		  {
			 $result=false;
			
		  }	 
		
		  $xiaoshoumx_update_data=array();
		  $xiaoshoumx_update_data['deleted']=1;
		  $xiaoshoumx_update_data['bz']='客户端购买商品 订单取消';
		  $xiaoshoumx_update_data['lingqu_status']=2;
		  
		  if(D('Productxsmx')->where(array('post_order_no'=>$post_order_no,'wbid'=>session('wbid')))->save($xiaoshoumx_update_data)===false)
		  {
			 $result=false;
			
		  }
		  
		
			if($result)
			{
				$data['status']=1;
				D()->commit();
			}
			else
			{
				$data['status']=-1;
				D()->rollback();
			}			
                  
       }          
  
        $this->ajaxReturn($data);  
	}
	
	
	
	//===============================新增组合=============================2017.12.6====
	
	

	
	
	
	
	//修改上架==>shangjia_zzb--------------------------------------------
	public function shangjia()
	{   
        $bOpen=$this->check_newcs_qx();
		if($bOpen===false)
		{		  
			$this->redirect('rate/fujia', array('cate_id' => 2), 1, '请在附加设置里开启设置...');	  
		}
	    session('goods_id_list',null);
	    $wbid=session('wbid');
		session('plch_status','0');
		
		$map=array();
		$map['kc.num']=array('gt',0);
		$map['kc.wbid']=session('wbid');
		$map['kc.position']=0;
		$map['kc.deleted']=0;
		$map['info.deleted']=0;	
		$map['info.is_zuhe']=array('neq',2);	
		
	    $goodslist=D('Productkc')->getAllChuhuokucunfoListByMap($map);
		foreach($goodslist as &$val)
		{
		  $val['value']=$val['goods_pinyin'].','.$val['goods_name'].','.$val['goods_quanpin'];
		  $val['label']=$val['goods_pinyin'].','.$val['goods_name'].','.$val['goods_quanpin'];
		  $val['hj_num']=D('Productkc')->where(array('goods_id'=>$val['goods_id'],'wbid'=>session('wbid'),'position'=>1))->getField('num');
		  $val['ck_num']=D('Productkc')->where(array('goods_id'=>$val['goods_id'],'wbid'=>session('wbid'),'position'=>0))->getField('num');
		  	  
		}
		
        //单个有仓库库存商品的列表
		$this->assign('goodslist',json_encode($goodslist));	
		
		
		$map=array();
		$map['is_zuhe']=2;
		$map['deleted']=0;
		$map['wbid']=$wbid;
		$zuhelist=D('Product')->where($map)->select();
		$i=0;
		$list=array();
		foreach($zuhelist as &$val)
		{
			$zuhe_id=$val['goods_id'];
			$zuhe_goods_array=array();
			$map=array();
			
			$map['is_zuhe']=1;
			$map['zuhe_id']=$zuhe_id;	
			$map['wbid']=$wbid;					
			$zuhe_goods_array=D('Product')->where($map)->select();
			foreach($zuhe_goods_array as &$val1)
			{
				$val1['hj_num']=D('Productkc')->where(array('goods_id'=>$val1['goods_id'],'wbid'=>$wbid,'position'=>1))->getField('num');
				$val1['ck_num']=D('Productkc')->where(array('goods_id'=>$val1['goods_id'],'wbid'=>$wbid,'position'=>0))->getField('num');
			}
	
										
			$list[$i]['goods_id']=$val['goods_id'];
			$list[$i]['type_name']=D('ProductType')->where(array('type_id'=>$val['type_id']))->getField('type_name');
			$list[$i]['goods_name']=$val['goods_name'];
			$list[$i]['unit']=$val['unit'];
			$list[$i]['guige']=$val['guige'];
			$list[$i]['shou_price']=$val['shou_price'];
			$list[$i]['is_zuhe']=$val['is_zuhe'];
			$list[$i]['zuhe_id']=$val['zuhe_id'];
			$list[$i]['hj_num']=D('Productkc')->where(array('goods_id'=>$zuhe_id,'wbid'=>$wbid,'position'=>1))->getField('num');
			
			if(!empty($zuhe_goods_array))
			{
				$list[$i]['zuhelist']=$zuhe_goods_array;
			}
			else
			{
				$list[$i]['zuhelist']='';
			}					
										
			$i++;
		}
		
          //组合商品的列表
		$this->assign('zuhegoods_list',json_encode($list));
		
		
		
		// is_zuhe=0  is_zuhe=2 的所有商品
		
		$map=array();
		$map['is_zuhe']=array('neq',1);
		$map['deleted']=0;
		$map['wbid']=$wbid;
		$all_goodsid_list=D('Product')->Field('is_zuhe,goods_id')->where($map)->select();								
		$this->assign('all_goodsid_list',json_encode($all_goodsid_list));
		creatToken();
        $this->display();  
	}
	
	
	public function xiajia()
	{   

	    session('goods_id_list',null);
	    $wbid=session('wbid');
		session('plch_status','0');
		
		$map=array();
		$map['kc.num']=array('egt',0);
		$map['kc.wbid']=$wbid;
		$map['kc.position']=1;
		$map['kc.deleted']=0;
		$map['info.deleted']=0;	
		$map['info.is_zuhe']=array('neq',2);	
		
	    $goodslist=D('Productkc')->getAllChuhuokucunfoListByMap($map);
		foreach($goodslist as &$val)
		{
		  $val['value']=$val['goods_pinyin'].','.$val['goods_name'].','.$val['goods_quanpin'];
		  $val['label']=$val['goods_pinyin'].','.$val['goods_name'].','.$val['goods_quanpin'];
		  $val['hj_num']=D('Productkc')->where(array('goods_id'=>$val['goods_id'],'wbid'=>session('wbid'),'position'=>1))->getField('num');
		  $val['ck_num']=D('Productkc')->where(array('goods_id'=>$val['goods_id'],'wbid'=>session('wbid'),'position'=>0))->getField('num');
		  	  
		}
		
        //单个有仓库库存商品的列表
		$this->assign('goodslist',json_encode($goodslist));	
		
		
		$map=array();
		$map['is_zuhe']=2;
		$map['deleted']=0;
		$map['wbid']=$wbid;
		$zuhelist=D('Product')->where($map)->select();
		$i=0;
		$list=array();
		foreach($zuhelist as &$val)
		{
			$zuhe_id=$val['goods_id'];
			$zuhe_goods_array=array();
			$map=array();
			
			$map['is_zuhe']=1;
			$map['zuhe_id']=$zuhe_id;	
			$map['wbid']=$wbid;					
			$zuhe_goods_array=D('Product')->where($map)->select();
			foreach($zuhe_goods_array as &$val1)
			{
				$val1['hj_num']=D('Productkc')->where(array('goods_id'=>$val1['goods_id'],'wbid'=>$wbid,'position'=>1))->getField('num');
				$val1['ck_num']=D('Productkc')->where(array('goods_id'=>$val1['goods_id'],'wbid'=>$wbid,'position'=>0))->getField('num');
			}
	
										
			$list[$i]['goods_id']=$val['goods_id'];
			$list[$i]['type_name']=D('ProductType')->where(array('type_id'=>$val['type_id']))->getField('type_name');
			$list[$i]['goods_name']=$val['goods_name'];
			$list[$i]['unit']=$val['unit'];
			$list[$i]['guige']=$val['guige'];
			$list[$i]['shou_price']=$val['shou_price'];
			$list[$i]['is_zuhe']=$val['is_zuhe'];
			$list[$i]['zuhe_id']=$val['zuhe_id'];
			$list[$i]['hj_num']=D('Productkc')->where(array('goods_id'=>$zuhe_id,'wbid'=>$wbid,'position'=>1))->getField('num');
			
			if(!empty($zuhe_goods_array))
			{
				$list[$i]['zuhelist']=$zuhe_goods_array;
			}
			else
			{
				$list[$i]['zuhelist']='';
			}					
										
			$i++;
		}
		
          //组合商品的列表
		$this->assign('zuhegoods_list',json_encode($list));
		
		
		
		// is_zuhe=0  is_zuhe=2 的所有商品
		
		$map=array();
		$map['is_zuhe']=array('neq',1);
		$map['deleted']=0;
		$map['wbid']=$wbid;
		$all_goodsid_list=D('Product')->Field('is_zuhe,goods_id')->where($map)->select();	
		$this->assign('all_goodsid_list',json_encode($all_goodsid_list));
		
		
		creatToken();	
        $this->display();  
	}
	//=======================修改上架保存入库========================
	
	public function shangjia_edit_set()
	{
		$wbid=session('wbid');
		if(IS_AJAX)
		{    
	        if(!checkToken($_POST['token']))
			{  
		        writelog('shangjia_edit_set---重复提交');
				$data['status']=-2;          								
			    $this->ajaxReturn($data);
                return;   			   
			}
			else
			{
				//writelog('shangjia_edit_set---未重复提交');
			}
	
	        $post_order_no = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);  //网站订单号
			$post_order_no='SJ'.$post_order_no;
            $dtInsertTime=date('Y-m-d H:i:s',time());     			
			$str=I('post.goodsinfo','','string');	
            $str=htmlspecialchars_decode($str); 		
			$shangjia_goodslist=json_decode($str,true);				
			
			if(empty($shangjia_goodslist))
			{	
              $data['status']=-1;
			  $this->ajaxReturn($data);
			  return;	
			}	
			

			$info='';
			$result=true;
			D()->startTrans();  //启用事务
			
			foreach( $shangjia_goodslist as &$val)
			{
				$shangxiajiamx_insert_data=array();
				$shangxiajiamx_insert_data['goods_id']=$val['goods_id'];
				$shangxiajiamx_insert_data['num']     =$val['num'];
				$shangxiajiamx_insert_data['ck_num']  =D('Productkc')->where(array('wbid'=>$wbid,'position'=>0,'goods_id'=>$val['goods_id']))->getField('num');
				$shangxiajiamx_insert_data['hj_num']  =D('Productkc')->where(array('wbid'=>$wbid,'position'=>1,'goods_id'=>$val['goods_id']))->getField('num');				
				$shangxiajiamx_insert_data['post_order_no']=$post_order_no;
				$shangxiajiamx_insert_data['shangxia_status']=0;
				$shangxiajiamx_insert_data['wbid']=$wbid;
				$shangxiajiamx_insert_data['operate']=session('username');
				$shangxiajiamx_insert_data['dtInsertTime']=$dtInsertTime;
				
				$map=array();
				$map['wbid']=$wbid;
				$map['goods_id']=$val['goods_id'];
				$goodsinfo=D('Product')->where($map)->find();
												
				$now_ckkc_num= D('Productkc')->where(array('wbid'=>$wbid,'position'=>0,'goods_id'=>$val['goods_id']))->getField('num');
				if($val['num'] >= $now_ckkc_num)
				{
					$now_sj_shangjia_num =$now_ckkc_num;
				}
				else
                {
					$now_sj_shangjia_num =$val['num'];
				}	

                if($val['is_zuhe']==0) //该商品的仓库库存减少，货架库存增加 position=1 货架   position=0 仓库
				{
					$shangxiajiamx_insert_data['zuhe_id']=0;
					$shangxiajiamx_insert_data['is_zuhe_goods']=0;
					$shangxiajiamx_insert_data['zuhe_flag']=1;
					if(D('Productsxjmx')->add($shangxiajiamx_insert_data)===false)
					{					
						$result=false;
					}
				    if(D('Productkc')->where(array('wbid'=>$wbid,'position'=>0,'goods_id'=>$val['goods_id']))->setDec('num',$now_sj_shangjia_num)===false)
					{					
						$result=false;
					}
					
					if(D('Productkc')->where(array('wbid'=>$wbid,'position'=>1,'goods_id'=>$val['goods_id']))->setInc('num',$now_sj_shangjia_num)===false)
					{					
						$result=false;
					}
					
				}
				else if($val['is_zuhe']==1)
                {
					if($now_sj_shangjia_num > 0)
					{
						$shangxiajiamx_insert_data['zuhe_id']=$goodsinfo['zuhe_id'];
						$shangxiajiamx_insert_data['is_zuhe_goods']=1;
						$shangxiajiamx_insert_data['zuhe_flag']=1;
						if(D('Productsxjmx')->add($shangxiajiamx_insert_data)===false)
						{					
							$result=false;
						}
						if(D('Productkc')->where(array('wbid'=>$wbid,'position'=>0,'goods_id'=>$val['goods_id']))->setDec('num',$now_sj_shangjia_num)===false)
						{					
							$result=false;
						}
					}
					else
                    {
						continue;
					}						
				}
				else if($val['is_zuhe']==2)
                {
					$shangxiajiamx_insert_data['zuhe_id']=0;
					$shangxiajiamx_insert_data['is_zuhe_goods']=2;
					$shangxiajiamx_insert_data['zuhe_flag']=1;
					
					if(D('Productsxjmxzh')->add($shangxiajiamx_insert_data)===false)
					{					
						$result=false;
					}
					if(D('Productkc')->where(array('wbid'=>$wbid,'position'=>1,'goods_id'=>$val['goods_id']))->setInc('num',$val['num'])===false)
					{					
						$result=false;
					}
				}					
							
				$val['goods_name']=D('Product')->where(array('wbid'=>$wbid,'goods_id'=>$val['goods_id']))->getField('goods_name');				
				$info.= $val['goods_name'].':'.$val['num'].' ';
			}
			
			//更新库存表
		
	
				$shangxiajia_insert_data['post_order_no']=$post_order_no;
				$shangxiajia_insert_data['shangxia_status']=0;
				$shangxiajia_insert_data['wbid']=$wbid;
				$shangxiajia_insert_data['info']=$info;		
				$shangxiajia_insert_data['dtInsertTime']=$dtInsertTime;
				$shangxiajia_insert_data['operator']=session('username');
				$shangxiajia_insert_data['bz']=I('post.bz','','string');
				$shangxiajia_insert_data['detailinfo']=$str;
				$shangxiajia_insert_data['zuhe_flag']=1;
							
				if(D('Productsxj')->add($shangxiajia_insert_data)===false)
				{					
					$result=false;
				}	
			
		
			if($result)
            {
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
	
	
	//=======================修改下架保存入库========================
	
	public function xiajia_edit_set()
	{
		$wbid= session('wbid');
		if(IS_AJAX)
		{    
	        if(!checkToken($_POST['token']))
			{  
		        writelog('xiajia_edit_set---重复提交');
				$data['status']=-2;          								
			    $this->ajaxReturn($data);
                return;   			   
			}
			else
			{
				//writelog('xiajia_edit_set---未重复提交');
			}
	        $post_order_no = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);  //网站订单号
			$post_order_no='XJ'.$post_order_no;
            $dtInsertTime=date('Y-m-d H:i:s',time());     			
			$str=I('post.goodsinfo','','string');	
            $str=htmlspecialchars_decode($str); 		
			$xiajia_goodslist=json_decode($str,true);
												
			if(empty($xiajia_goodslist))
			{	
              $data['status']=1;
			  $this->ajaxReturn($data);
			  return;	
			}	
			

			$info='';
			$result=true;
			D()->startTrans();  //启用事务
			//is_zuhe=0 直接货架减少，库存增加

			
			foreach($xiajia_goodslist as &$val)
			{
				$map=array();
				$map['goods_id']=$val['goods_id'];
				$map['wbid']=$wbid;
				$goods_info=D('Product')->where($map)->find();
				$shangxiajiamx_insert_data=array();
				$shangxiajiamx_insert_data['goods_id']=$val['goods_id'];
				$shangxiajiamx_insert_data['num']     =$val['num'];
				$shangxiajiamx_insert_data['ck_num']  =D('Productkc')->where(array('wbid'=>$wbid,'position'=>0,'goods_id'=>$val['goods_id']))->getField('num');
				$shangxiajiamx_insert_data['hj_num']  =D('Productkc')->where(array('wbid'=>$wbid,'position'=>1,'goods_id'=>$val['goods_id']))->getField('num');
				
				$shangxiajiamx_insert_data['post_order_no']=$post_order_no;
				$shangxiajiamx_insert_data['shangxia_status']=1;             //1 是下架
				$shangxiajiamx_insert_data['wbid']=$wbid;
				$shangxiajiamx_insert_data['dtInsertTime']=$dtInsertTime;
				$shangxiajiamx_insert_data['operate']=session('username');
				

				
				//该商品的实际货架数量
				$now_hjkc_num= D('Productkc')->where(array('wbid'=>$wbid,'position'=>1,'goods_id'=>$val['goods_id']))->getField('num');
				if($val['num'] >= $now_hjkc_num)
				{
					$now_sj_xiajia_num =$now_hjkc_num;
				}
				else
                {
					$now_sj_xiajia_num =$val['num'];
				}
				
				
				//该商品的仓库库存减少，货架库存增加 position=1 货架   position=0 仓库
				if($val['is_zuhe']==0)
				{
					$shangxiajiamx_insert_data['zuhe_flag']=1;
					$shangxiajiamx_insert_data['is_zuhe_goods']=0;
					$shangxiajiamx_insert_data['zuhe_id']=0;
					if(D('Productsxjmx')->add($shangxiajiamx_insert_data)===false)
					{
						$result=false;
					}
					//仓库数量增加                
					if(D('Productkc')->where(array('wbid'=>$wbid,'position'=>0,'goods_id'=>$val['goods_id']))->setInc('num',$val['num'])===false)
					{
						$result=false;
					}
					//货架数量减少
					if(D('Productkc')->where(array('wbid'=>$wbid,'position'=>1,'goods_id'=>$val['goods_id']))->setDec('num',$now_sj_xiajia_num)===false)
					{
						$result=false;
					}
				}
				else if($val['is_zuhe']==1)
                {
					//该商品的仓库数量增加				
					if($val['num'] > 0)
					{
						$shangxiajiamx_insert_data['zuhe_flag']=1;
						$shangxiajiamx_insert_data['is_zuhe_goods']=1;
						$shangxiajiamx_insert_data['zuhe_id']=$goods_info['zuhe_id'];
						if(D('Productsxjmx')->add($shangxiajiamx_insert_data)===false)
						{					
							$result=false;
						}	
						
						if(D('Productkc')->where(array('wbid'=>$wbid,'position'=>0,'goods_id'=>$val['goods_id']))->setInc('num',$val['num'])===false)
						{
							$result=false;
						}
					}
					else
                    {
						continue;
					}
					
				}
				else if($val['is_zuhe']==2)
                {
					$shangxiajiamx_insert_data['zuhe_flag']=1;
					$shangxiajiamx_insert_data['is_zuhe_goods']=2;
					$shangxiajiamx_insert_data['zuhe_id']=0;
					if(D('Productsxjmxzh')->add($shangxiajiamx_insert_data)===false)
					{
						$result=false;
					}
					//该商品的货架数量减少
				    if(D('Productkc')->where(array('wbid'=>$wbid,'position'=>1,'goods_id'=>$val['goods_id']))->setDec('num',$now_sj_xiajia_num)===false)
					{
						$result=false;
					}
				} 					
				
				
				$val['goods_name']=D('Product')->where(array('wbid'=>$wbid,'goods_id'=>$val['goods_id']))->getField('goods_name');				
				$info.= $val['goods_name'].':'.$val['num'].' ';
			}
			
			//更新库存表
			
			$shangxiajia_insert_data['post_order_no']=$post_order_no;
			$shangxiajia_insert_data['shangxia_status']=1;
			$shangxiajia_insert_data['wbid']=$wbid;
			$shangxiajia_insert_data['info']=$info;		
			$shangxiajia_insert_data['dtInsertTime']=$dtInsertTime;
			$shangxiajia_insert_data['bz']=I('post.bz','','string');
			$shangxiajia_insert_data['operator']=session('username');
			$shangxiajia_insert_data['detailinfo']=$str;
			$shangxiajia_insert_data['zuhe_flag']=1;
			
			if(D('Productsxj')->add($shangxiajia_insert_data)===false)
			{
				$result=false;
			}	
						
			if($result)
            {
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
	//======================选择商品======================

	/*
	public function plch_sxj_zzb()
	{
		
		$yixuan_goods_id=I('get.goods_id','','string');
			
		
		$wbid=78;		
		$typelist=D('ProductType')->where(array('wbid'=>$wbid))->select();
        $this->assign('typelist',$typelist);	
		
	    $list=array();
	    $i=0;
		
		$map=array();
		if(!empty($yixuan_goods_id))
		{
			$map['info.goods_id']=array('not in',$yixuan_goods_id);
		}	

	
		$map['kc.num']=array('gt',0);
		$map['kc.wbid']=$wbid;
		$map['kc.position']=1;       // 0是货架   1是仓库
		$map['kc.deleted']=0;
		$map['info.deleted']=0;	
		$map['info.is_zuhe']=array('neq',2);	
		
	    $goods_list=D('Productkc')->getAllChuhuokucunfoListByMap($map);		
        foreach($goods_list as &$val)
        {			
            if($val['is_zuhe']==0)
            {
				$list[$i]['goods_id']=$val['goods_id'];	
				$list[$i]['goods_name']=$val['goods_name'];
				$list[$i]['unit']=$val['unit'];
				$list[$i]['guige']=$val['guige'];
				$list[$i]['shou_price']=$val['shou_price'];
				$list[$i]['is_zuhe']=$val['is_zuhe'];
				$list[$i]['zuhe_id']=$val['zuhe_id'];
				$list[$i]['type_name']=D('ProductType')->where(array('type_id'=>$val['type_id']))->getField('type_name');
				$i++;
			}
			else if($val['is_zuhe']==2)
            {			
				$zuhe_id=$val['goods_id'];
				$zuhe_goods_array=array();
				$map=array();
				
				$map['is_zuhe']=1;
				$map['zuhe_id']=$zuhe_id;	
                $map['wbid']=$wbid;					
				$zuhe_goods_array=D('Product')->where($map)->select();
		
											
				$list[$i]['goods_id']=$val['goods_id'];
				$list[$i]['type_name']=D('ProductType')->where(array('type_id'=>$val['type_id']))->getField('type_name');
				$list[$i]['goods_name']=$val['goods_name'];
				$list[$i]['unit']=$val['unit'];
				$list[$i]['guige']=$val['guige'];
				$list[$i]['shou_price']=$val['shou_price'];
				$list[$i]['is_zuhe']=$val['is_zuhe'];
				$list[$i]['zuhe_id']=$val['zuhe_id'];
				
				if(!empty($zuhe_goods_array))
				{
					$list[$i]['zuhelist']=$zuhe_goods_array;
				}
				else
                {
					$list[$i]['zuhelist']='';
				}					
											
				$i++;
			}				
									
		}	   	
		$this->assign('goods_list',json_encode($list));		
	    $this->display();		
	}
	*/
	
	
	//====================================交班jiaoban_zzb=======================================================
	public function jiaoban()
    {
        $bOpen=$this->check_newcs_qx();
		if($bOpen===false)
		{		  
			$this->redirect('rate/fujia', array('cate_id' => 2), 1, '请在附加设置里开启设置...');	  
		}  		 
	  	$map=array();
		$map['kc.wbid']=session('wbid');
		$map['kc.position']=1;       //货架
		//$map['kc.deleted']=0;	
        $map['info.deleted']=0;
		$map['info.is_zuhe']=array('neq',1);
		
	    $goods_list=D('Productkc')->getAllChuhuokucunfoListByMap($map);
	  
		foreach($goods_list as &$val)
		{
		  $val['value']=$val['goods_pinyin'].','.$val['goods_name'].','.$val['goods_quanpin'];
		  $val['label']=$val['goods_pinyin'].','.$val['goods_name'].','.$val['goods_quanpin'];
		  $val['hj_num']=D('Productkc')->where(array('goods_id'=>$val['goods_id'],'wbid'=>session('wbid'),'position'=>1))->getField('num');
		  $val['ck_num']=D('Productkc')->where(array('goods_id'=>$val['goods_id'],'wbid'=>session('wbid'),'position'=>0))->getField('num');
			  
		}
		
		
		
		$this->assign('goodslist',json_encode($goods_list));	
		$this->assign('goods_list',$goods_list);
			
		$onejbmxinfo= D('Productjb')->where(array('wbid'=>session('wbid')))->order('shifttime desc')->limit(1)->find();  
		
		if(empty($onejbmxinfo))
		{
		  $jieban_time=D('WbInfo')->where(array('WBID'=>session('wbid')))->getField('debug_InsrtTime');	
		}
		else
		{
		  $jieban_time=date('Y-m-d H:i:s',strtotime($onejbmxinfo['shifttime']));		
		}	
			
		$jiaoban_time= date('Y-m-d H:i:s',time());	
		
		//微信收入
		$map=array();		
		$map['dtInsertTime']=array('between',array($jieban_time,$jiaoban_time));
		$map['lingqu_status']=1;		
		$map['wbid']=session('wbid');
		$map['pay_type']=1;
		
		$wx_sum_money =D('Productxs')->where($map)->sum('sum_sp_je');					
		//支付宝收入，现金收入
		$map['pay_type']=2;
		$zfb_sum_money =D('Productxs')->where($map)->sum('sum_sp_je');			
		
		//客户端现金收入
		$map['pay_type']=3;
		$cash_sum_money =D('Productxs')->where($map)->sum('sum_sp_je');
		
		if(empty($wx_sum_money))
		{
			$wx_sum_money='0.00';
		}
		else
		{
			$wx_sum_money=sprintf("%.2f", $wx_sum_money); 
		}
		
		if(empty($zfb_sum_money))
		{
			$zfb_sum_money='0.00';
		}
		else
		{
			$zfb_sum_money=sprintf("%.2f", $zfb_sum_money); 
		}
		
		if(empty($cash_sum_money))
		{
			$cash_sum_money='0.00';
		}else
		{
			$cash_sum_money=sprintf("%.2f", $cash_sum_money); 
		}
		
		
		
		//吧台微信收入
		
		$map=array();		
		$map['dtNotifyTime']=array('between',array($jieban_time,$jiaoban_time));	
		$map['wbid']=session('wbid');
		$map['pay_type']=1;

	
		$wx_sum_money_bt =D('Productxsbt')->where($map)->sum('sp_je');
		
			
		//吧台支付宝收入
		$map=array();		
		$map['dtNotifyTime']=array('between',array($jieban_time,$jiaoban_time));			
		$map['wbid']=session('wbid');
		$map['pay_type']=2;	

		$zfb_sum_money_bt =D('Productxsbt')->where($map)->sum('sp_je');
		
		
		if(empty($wx_sum_money_bt))
		{
			$wx_sum_money_bt='0.00';
		}else
		{
			$wx_sum_money_bt=sprintf("%.2f", $wx_sum_money_bt); 
		}	 
		
		if(empty($zfb_sum_money_bt))
		{
			$zfb_sum_money_bt='0.00';
		}
		else
		{
			$zfb_sum_money_bt=sprintf("%.2f", $zfb_sum_money_bt); 
		}	
		
         						
		$this->assign('jieban_time',$jieban_time);
		$this->assign('jiaoban_time',$jiaoban_time);
		
		$this->assign('wx_sum_money_khd',$wx_sum_money);
		$this->assign('zfb_sum_money_khd',$zfb_sum_money);
		$this->assign('cash_sum_money_khd',$cash_sum_money);
		
		
		$this->assign('wx_sum_money_bt',$wx_sum_money_bt);
		$this->assign('zfb_sum_money_bt',$zfb_sum_money_bt);
        creatToken();	
        $this->display();
    }
	
	//====================交班时商品库存列表=====================================
	public function getAllShangpinkucuinfolist()
	{	
		$wbid=session('wbid');
		$type_list=D('ProductType')->select();//shang pin fenlei
		$map=array();
		$map['is_zuhe']=array('neq',1);
		$map['wbid']=$wbid;
		$map['deleted']=0;
		
		//1.获取is_zuhe=0 和is_zuhe=2的商品列表
		$goods_list=D('Product')->field('goods_id,goods_name,shou_price,zuhe_id,is_zuhe')->where($map)->select();
						 			
	    if(IS_AJAX)
        {  
            $nowtime=date('Y-m-d H:i:s',time());
		    $lastshiftinfo= D('Productjb')->where(array('wbid'=>session('wbid')))->order('shifttime desc')->limit(1)->find();
		    $lastshifttime=date('Y-m-d H:i:s',strtotime($lastshiftinfo['shifttime']));
		  
		    if(!empty($lastshiftinfo))
		    {
				$zuhe_flag=$lastshiftinfo['zuhe_flag'];
				$last_order_no=$lastshiftinfo['post_order_no'];
				if($zuhe_flag==0)
				{										
					$map=array();
					$map['post_order_no']=$last_order_no;
					$map['wbid']=session('wbid');
					$last_db_goods_jbmx=D('Productjbmx')->field('wbid,goods_id,old_hj_num,now_hj_num,shangjia_num,xiajia_num,is_zuhe_goods,zuhe_flag')->where($map)->select();

				}
				else if($zuhe_flag==1)
				{
					$lastshift_goodskc_list= json_decode($lastshiftinfo['detailinfo'],true); 
				}							        			 		 
			}
			else
			{
				$lastshift_goodskc_list='';
			} 			  
		      
		    $map=array();
		    $flag=I('get.flag','0','string');	 //判断网吧所有商品  还是只是有销量商品  flag=1 实际有销量商品  flag =0  无销量商品	  
		    if(!empty($flag))
		    {
			  $map2=array();	  
			  $map2['dtInsertTime']=array('BETWEEN',array($lastshifttime,$nowtime));
			  $map2['wbid']=session('wbid');	        		  	  
			  $id_list=D('Productxsmx')->where($map2)->getField('goods_id',true);  
			  if(!empty($id_list))
			  {
				$map['goods_id']=array('IN',$id_list);  
			  }
			  else
              {
				$map['goods_id']='';   
			  }				  			  
		    }	
		  
		  $map['kc.wbid']=$wbid;
		  $map['kc.position']=1;
		  $map['info.deleted']=0;
		  $map['info.is_zuhe']=array('neq',1);			  	  
		  $list=D('Productkc')->getAllChuhuokucunfoListByMap($map);   //读取is_zuhe=0  和is_zuhe=2的 当前库存数量	
        //		  {[1：10]，[2:15],[3:20]}	  
		  foreach($list as &$val)
		  {
            $is_zuhe=$val['is_zuhe'];										
 			//1.进货到货架的数量     2.销售数量      3.上架数量        4.下架数量
			
			//1.从库存里上架的数量  
			$map1=array();
            $map1['wbid']=$wbid;	
            $map1['dtInsertTime']=array('BETWEEN',array($lastshifttime,$nowtime));	
			$map1['goods_id']=$val['goods_id'];	
			$map1['shangxia_status']=0;
			if($is_zuhe==0)
			{
				$map1['is_zuhe_goods']=0;	
				$kc_shangjia_num= D('Productsxjmx')->where($map1)->sum('num'); 
			}
			else if($is_zuhe==2)
			{
				$kc_shangjia_num= D('Productsxjmxzh')->where($map1)->sum('num');				
			}										        
			if(empty($kc_shangjia_num))
			{
				$kc_shangjia_num=0;
			}	
							
			//2.直接进货上架数量		
			$map1=array();
            $map1['wbid']=$wbid;	
            $map1['dtInsertTime']=array('BETWEEN',array($lastshifttime,$nowtime));	
			$map1['goods_id']=$val['goods_id'];	
			$map1['position']=1;			
			if($is_zuhe==0)
			{
				$map1['is_zuhe_goods']=0;	
				$jinhuo_shangjia_num= D('Productjchmx')->where($map1)->sum('sumnum');
			}
			else if($is_zuhe==2)
			{
				$jinhuo_shangjia_num= D('Productjchmxzh')->where($map1)->sum('sumnum');	
			}			            
            if(empty($jinhuo_shangjia_num))
			{
				$jinhuo_shangjia_num=0;
			}			
			
			$shangjia_num= $kc_shangjia_num+ $jinhuo_shangjia_num;			
			if(empty($shangjia_num))
			{
			  $val['shangjia_num']=0;	
			}
			else
            {
			  $val['shangjia_num']=$shangjia_num;		
			}
									
			//3.下架数量
			$map1=array();
            $map1['wbid']=$wbid;	
            $map1['dtInsertTime']=array('BETWEEN',array($lastshifttime,$nowtime));	
			$map1['goods_id']=$val['goods_id'];	
			$map1['shangxia_status']=1;
			if($is_zuhe==0)
			{
				$map1['is_zuhe_goods']=0;	
				$xiajia_num= D('Productsxjmx')->where($map1)->sum('num'); 
			}
			else if($is_zuhe==2)
			{
				$xiajia_num= D('Productsxjmxzh')->where($map1)->sum('num'); 	
			}
						          	
			if(empty($xiajia_num))
			{
			  $val['xiajia_num']=0;	
			}
			else
            {
			  $val['xiajia_num']=$xiajia_num;		
			}	
			
            //4.销售商品数量			
			$map1=array();
			$map1['wbid']=$wbid;	
			$map1['dtInsertTime']=array('BETWEEN',array($lastshifttime,$nowtime));	
			$map1['goods_id']=$val['goods_id'];			
			$map1['lingqu_status']=array('neq',2);	
			if($is_zuhe==0)
			{	
		        $map1['is_zuhe_goods']=0;		
				$xiaoshou_num= D('Productxsmx')->where($map1)->sum('xiaoshou_num'); 	
			}
			else if($is_zuhe==2)
            {					
				$xiaoshou_num= D('Productxsmxzh')->where($map1)->sum('xiaoshou_num'); 	
			}	
			
			if(empty($xiaoshou_num))
			{
			  $val['xiaoshou_num']=0;	
			}
			else
			{
			  $val['xiaoshou_num']=$xiaoshou_num;		
			}
			
  		    foreach($type_list as $val1)
			{
				if($val['type_id']==$val1['type_id'])
				{
					$val['type_name']=$val1['type_name']; 
					break;
				}	
			}
			
			foreach($goods_list as &$val2)
			{
				if($val['goods_id']==$val2['goods_id'])
				{				
					$val['goods_name']=$val2['goods_name']; 
					$val['shou_price']=$val2['shou_price']; 
					$val['shou_price']=sprintf("%.2f",$val['shou_price']);			
					break;
				}	
			}
			
			if($lastshiftinfo)  //上次交过班
			{
				if($zuhe_flag==0)
				{
                    if(!empty($last_db_goods_jbmx))
					{
						$bFind=false;
						foreach($last_db_goods_jbmx as &$val2)
						{
							if($val['goods_id']==$val2['goods_id'])
							{				
								$val['old_hj_num']=$val2['now_hj_num']; 	
								$bFind=true;						
								break;
							}
						}
						if($bFind==false)
						{
							$val['old_hj_num']=0;
						}	
										
					}
				}
				else if($zuhe_flag==1)
                { 
					if(!empty($lastshift_goodskc_list))
					{
						$bFind=false;
						foreach($lastshift_goodskc_list as &$val2)
						{
							if($val['goods_id']==$val2['goods_id'])
							{				
								$val['old_hj_num']=$val2['now_hj_num']; 	
								$bFind=true;						
								break;
							}
						}
						if($bFind==false)
						{
							$val['old_hj_num']=0;
						}											
					}
				}					
			}
			else
            {
				$val['old_hj_num']=0;
			}											        
            $now_hj_num= $val['old_hj_num']+ $val['shangjia_num']-$val['xiajia_num']-$val['xiaoshou_num']; 			
			if(empty($now_hj_num))
			{
			  $val['now_hj_num']=0;	
			}
			else
            {
			  $val['now_hj_num']=$now_hj_num;		
			}			
										 
		  }
		  
		  array_multisort(array_column($list, 'shou_price'),SORT_NUMERIC, SORT_ASC, $list); 
		  
		  $this->ajaxReturn($list);
	    }		  
	}
     
	 //====================商品销售=====================================
	public function xiaoshou()
	{
		$bOpen=$this->check_newcs_qx();
		if($bOpen===false)
		{		  
			$this->redirect('rate/fujia', array('cate_id' => 2), 1, '请在附加设置里开启设置...');	  
		}
		session('goods_id_list',null);
		//获取所有的组合商品列表和库存
		$wbid=session('wbid');
		$map=array();
		$map['info.is_zuhe']=2;
		$map['info.deleted']=0;
		$map['kc.position']=1;    //货架库存
		$map['kc.wbid']=$wbid;
		$zuhe_goodslist=D('Productkc')->getAllzuhegoodsListByMap($map);
										
		//$map['kc.num']=array('gt',0);
		$map['kc.wbid']=$wbid;
		$map['kc.position']=1;
		$map['kc.deleted']=0;
		$map['info.is_zuhe']=array('neq',2);
		
		$goods_list1=D('Productkc')->getAllkucunfoListByMap($map,1);
		$goods_list2=D('Productkc')->getAllkucunfoListByMap($map,2);
		$goods_list3=D('Productkc')->getAllkucunfoListByMap($map,3);
		$goods_list4=D('Productkc')->getAllkucunfoListByMap($map,4);
		$goods_list5=D('Productkc')->getAllkucunfoListByMap($map,5);
			
			
		$list1=array();
		$i=0;
		foreach($goods_list1 as &$val)
		{
			if($val['is_zuhe']==0)
			{
				if($val['num'] > 0)
				{					
					$list1[$i]['goods_id']  =$val['goods_id'];
					//$list1[$i]['type_name'] =D('ProductType')->where(array('type_id'=>$val['type_id']))->getField('type_name');
					$list1[$i]['goods_name']=$val['goods_name'];
					$list1[$i]['unit']      =$val['unit'];
					$list1[$i]['guige']     =$val['guige'];
					$list1[$i]['shou_price']=$val['shou_price'];
					$list1[$i]['is_zuhe']   =$val['is_zuhe'];
					$list1[$i]['zuhe_id']   =$val['zuhe_id'];
					$list1[$i]['num']       =$val['num'];
					$i++;
				}	
			}
			else if($val['is_zuhe']==1)
            {
				$zuhe_id=$val['zuhe_id'];
				foreach($zuhe_goodslist as $val6)
				{
					if($zuhe_id==$val6['goods_id'])
					{
						if($val6['num'] >0)
						{							
							$list1[$i]['goods_id']  =$val['goods_id'];
						//	$list1[$i]['type_name'] =D('ProductType')->where(array('type_id'=>$val['type_id']))->getField('type_name');
							$list1[$i]['goods_name']=$val['goods_name'];
							$list1[$i]['unit']      =$val['unit'];
							$list1[$i]['guige']     =$val['guige'];
							$list1[$i]['shou_price']=$val['shou_price'];
							$list1[$i]['is_zuhe']   =$val['is_zuhe'];
							$list1[$i]['zuhe_id']   =$val['zuhe_id'];
							$list1[$i]['num']       =$val['num'];
							$i++;
						}							
						break;
					}	
				}				
			}						
		}
		//echo  json_encode($list1);
		//return;
		
		$list2=array();
		$i=0;
		foreach($goods_list2 as &$val)
		{
			if($val['is_zuhe']==0)
			{
				if($val['num'] > 0)
				{					
					$list2[$i]['goods_id']  =$val['goods_id'];
					//$list2[$i]['type_name'] =D('ProductType')->where(array('type_id'=>$val['type_id']))->getField('type_name');
					$list2[$i]['goods_name']=$val['goods_name'];
					$list2[$i]['unit']      =$val['unit'];
					$list2[$i]['guige']     =$val['guige'];
					$list2[$i]['shou_price']=$val['shou_price'];
					$list2[$i]['is_zuhe']   =$val['is_zuhe'];
					$list2[$i]['zuhe_id']   =$val['zuhe_id'];
					$list2[$i]['num']       =$val['num'];
					$i++;
				}	
			}
			else if($val['is_zuhe']==1)
            {
				$zuhe_id=$val['zuhe_id'];
				//找到数组里该元素
				foreach($zuhe_goodslist as $val6)
				{
					if($zuhe_id==$val6['goods_id'])
					{
						if($val6['num'] >0)
						{							
							$list2[$i]['goods_id']  =$val['goods_id'];
							//$list2[$i]['type_name'] =D('ProductType')->where(array('type_id'=>$val['type_id']))->getField('type_name');
							$list2[$i]['goods_name']=$val['goods_name'];
							$list2[$i]['unit']      =$val['unit'];
							$list2[$i]['guige']     =$val['guige'];
							$list2[$i]['shou_price']=$val['shou_price'];
							$list2[$i]['is_zuhe']   =$val['is_zuhe'];
							$list2[$i]['zuhe_id']   =$val['zuhe_id'];
							$list2[$i]['num']       =$val['num'];
							$i++;
						}							
						break;
					}	
				}				
			}						
		}
	
	
	    $list3=array();
		$i=0;
		foreach($goods_list3 as &$val)
		{
			if($val['is_zuhe']==0)
			{
				if($val['num'] > 0)
				{					
					$list3[$i]['goods_id']  =$val['goods_id'];
				//	$list3[$i]['type_name'] =D('ProductType')->where(array('type_id'=>$val['type_id']))->getField('type_name');
					$list3[$i]['goods_name']=$val['goods_name'];
					$list3[$i]['unit']      =$val['unit'];
					$list3[$i]['guige']     =$val['guige'];
					$list3[$i]['shou_price']=$val['shou_price'];
					$list3[$i]['is_zuhe']   =$val['is_zuhe'];
					$list3[$i]['zuhe_id']   =$val['zuhe_id'];
					$list3[$i]['num']       =$val['num'];
					$i++;
				}	
			}
			else if($val['is_zuhe']==1)
            {
				$zuhe_id=$val['zuhe_id'];
				//找到数组里该元素
				foreach($zuhe_goodslist as $val6)
				{
					if($zuhe_id==$val6['goods_id'])
					{
					//	echo $val6['num'];	
						if($val6['num'] >0)
						{			
                            				
							$list3[$i]['goods_id']  =$val['goods_id'];
						//	$list3[$i]['type_name'] =D('ProductType')->where(array('type_id'=>$val['type_id']))->getField('type_name');
							$list3[$i]['goods_name']=$val['goods_name'];
							$list3[$i]['unit']      =$val['unit'];
							$list3[$i]['guige']     =$val['guige'];
							$list3[$i]['shou_price']=$val['shou_price'];
							$list3[$i]['is_zuhe']   =$val['is_zuhe'];
							$list3[$i]['zuhe_id']   =$val['zuhe_id'];
							$list3[$i]['num']       =$val['num'];
							$i++;
						}							
						break;
					}	
				}				
			}						
		}
		
      // echo  json_encode($zuhe_goodslist);
	 //  return;
		
	    $list4=array();
		$i=0;
		foreach($goods_list4 as &$val)
		{
			if($val['is_zuhe']==0)
			{
				if($val['num'] > 0)
				{					
					$list4[$i]['goods_id']  =$val['goods_id'];
					//$list4[$i]['type_name'] =D('ProductType')->where(array('type_id'=>$val['type_id']))->getField('type_name');
					$list4[$i]['goods_name']=$val['goods_name'];
					$list4[$i]['unit']      =$val['unit'];
					$list4[$i]['guige']     =$val['guige'];
					$list4[$i]['shou_price']=$val['shou_price'];
					$list4[$i]['is_zuhe']   =$val['is_zuhe'];
					$list4[$i]['zuhe_id']   =$val['zuhe_id'];
					$list4[$i]['num']       =$val['num'];
					$i++;
				}	
			}
			else if($val['is_zuhe']==1)
            {
				$zuhe_id=$val['zuhe_id'];
				//找到数组里该元素
				foreach($zuhe_goodslist as $val6)
				{
					if($zuhe_id==$val6['goods_id'])
					{
						if($val6['num'] >0)
						{							
							$list4[$i]['goods_id']  =$val['goods_id'];
						//	$list4[$i]['type_name'] =D('ProductType')->where(array('type_id'=>$val['type_id']))->getField('type_name');
							$list4[$i]['goods_name']=$val['goods_name'];
							$list4[$i]['unit']      =$val['unit'];
							$list4[$i]['guige']     =$val['guige'];
							$list4[$i]['shou_price']=$val['shou_price'];
							$list4[$i]['is_zuhe']   =$val['is_zuhe'];
							$list4[$i]['zuhe_id']   =$val['zuhe_id'];
							$list4[$i]['num']       =$val['num'];
							$i++;
						}							
						break;
					}	
				}				
			}						
		}
		
		
	    $list5=array();
		$i=0;
		foreach($goods_list5 as &$val)
		{
			if($val['is_zuhe']==0)
			{
				if($val['num'] > 0)
				{					
					$list5[$i]['goods_id']  =$val['goods_id'];
					//$list5[$i]['type_name'] =D('ProductType')->where(array('type_id'=>$val['type_id']))->getField('type_name');
					$list5[$i]['goods_name']=$val['goods_name'];
					$list5[$i]['unit']      =$val['unit'];
					$list5[$i]['guige']     =$val['guige'];
					$list5[$i]['shou_price']=$val['shou_price'];
					$list5[$i]['is_zuhe']   =$val['is_zuhe'];
					$list5[$i]['zuhe_id']   =$val['zuhe_id'];
					$list5[$i]['num']       =$val['num'];
					$i++;
				}	
			}
			else if($val['is_zuhe']==1)
            {
				$zuhe_id=$val['zuhe_id'];
				//找到数组里该元素
				foreach($zuhe_goodslist as $val6)
				{
					if($zuhe_id==$val6['goods_id'])
					{
						if($val6['num'] >0)
						{							
							$list5[$i]['goods_id']  =$val['goods_id'];
						//	$list5[$i]['type_name'] =D('ProductType')->where(array('type_id'=>$val['type_id']))->getField('type_name');
							$list5[$i]['goods_name']=$val['goods_name'];
							$list5[$i]['unit']      =$val['unit'];
							$list5[$i]['guige']     =$val['guige'];
							$list5[$i]['shou_price']=$val['shou_price'];
							$list5[$i]['is_zuhe']   =$val['is_zuhe'];
							$list5[$i]['zuhe_id']   =$val['zuhe_id'];
							$list5[$i]['num']       =$val['num'];
							$i++;
						}							
						break;
					}	
				}				
			}						
		}
         
		 
			
		$this->assign('goods_list1',$list1);
		$this->assign('goods_list2',$list2);
		$this->assign('goods_list3',$list3);
		$this->assign('goods_list4',$list4);
		$this->assign('goods_list5',$list5);
		
		$this->assign('zuhe_goodslist',json_encode($zuhe_goodslist));
		
		
		
		$nowtime=date('Y-m-d H:i:s',time());
		$lastshiftinfo= D('Productjb')->where(array('wbid'=>session('wbid')))->order('shifttime desc')->limit(1)->find();
		$lastshifttime=date('Y-m-d H:i:s',strtotime($lastshiftinfo['shifttime']));
		
		$map=array();
		$map['wbid']=session('wbid');
		$map['dtInsertTime']=array('BETWEEN',array($lastshifttime,$nowtime));
		$map['isUsed']=0;
		
		
		

		
		$order_list=D('Productxsbt')->where($map)->order('dtInsertTime desc')->select();
		foreach($order_list as &$val)
		{
			$val['sp_je']= sprintf("%.2f", $val['sp_je']);
			if($val['pay_type']==1)
			{
				$val['order_value']=$val['post_order_no'].'(微信  '.$val['sp_je'].'元)';
			}
			else if($val['pay_type']==2)
            {
			   $val['order_value']=$val['post_order_no'].'(支付宝  '.$val['sp_je'].'元)';
			}				
			
		}
		$this->assign('order_list',$order_list);
		
		
		
		
		$FAllowXiaoshouPrint= D('Webini')->where(array('wbid'=>$wbid,'skey'=>'FAllowXiaoshouPrint'))->getField('svalue');
		if($FAllowXiaoshouPrint==='0')
		{
		   $FAllowXiaoshouPrint=0;  
		}
		else
        {
		  $FAllowXiaoshouPrint=1; 		  
		}
     
 		
		$this->assign('FAllowXiaoshouPrint',$FAllowXiaoshouPrint);
		
		
		$map=array();
		//$map['info.is_zuhe']=2;
		$map['info.deleted']=0;
		$map['kc.position']=1;    //货架库存
		$map['kc.wbid']=$wbid;			
	    $goodslist=D('Productkc')->getAllChuhuokucunfoListByMap($map);
		
		
	    $list6=array();
		$i=0;
		foreach($goodslist as &$val)
		{
			if($val['is_zuhe']==0)
			{
				if($val['num'] > 0)
				{					
					$list6[$i]['goods_id']  =$val['goods_id'];
					//$list5[$i]['type_name'] =D('ProductType')->where(array('type_id'=>$val['type_id']))->getField('type_name');
					$list6[$i]['goods_name']=$val['goods_name'];
					$list6[$i]['unit']      =$val['unit'];
					$list6[$i]['guige']     =$val['guige'];
					$list6[$i]['shou_price']=$val['shou_price'];
					$list6[$i]['is_zuhe']   =$val['is_zuhe'];
					$list6[$i]['zuhe_id']   =$val['zuhe_id'];
					$list6[$i]['num']       =$val['num'];										
					$list6[$i]['value']=$val['goods_pinyin'].','.$val['goods_name'].','.$val['goods_quanpin'].','.$val['barcode'];
				    $list6[$i]['label']=$val['goods_pinyin'].','.$val['goods_name'].','.$val['goods_quanpin'].','.$val['barcode'];
				    $list6[$i]['hj_num']=D('Productkc')->where(array('goods_id'=>$val['goods_id'],'wbid'=>$wbid,'position'=>1))->getField('num');
				    $list6[$i]['ck_num']=D('Productkc')->where(array('goods_id'=>$val['goods_id'],'wbid'=>$wbid,'position'=>0))->getField('num');	
					
					$i++;
				}	
			}
			else if($val['is_zuhe']==1)
            {
				$zuhe_id=$val['zuhe_id'];
				//找到数组里该元素
				foreach($zuhe_goodslist as $val6)
				{
					if($zuhe_id==$val6['goods_id'])
					{
						if($val6['num'] >0)
						{							
							$list6[$i]['goods_id']  =$val['goods_id'];
						//	$list6[$i]['type_name'] =D('ProductType')->where(array('type_id'=>$val['type_id']))->getField('type_name');
							$list6[$i]['goods_name']=$val['goods_name'];
							$list6[$i]['unit']      =$val['unit'];
							$list6[$i]['guige']     =$val['guige'];
							$list6[$i]['shou_price']=$val['shou_price'];
							$list6[$i]['is_zuhe']   =$val['is_zuhe'];
							$list6[$i]['zuhe_id']   =$val['zuhe_id'];
							$list6[$i]['num']       =$val['num'];
							$list6[$i]['value']=$val['goods_pinyin'].','.$val['goods_name'].','.$val['goods_quanpin'].','.$val['barcode'];
				            $list6[$i]['label']=$val['goods_pinyin'].','.$val['goods_name'].','.$val['goods_quanpin'].','.$val['barcode'];								
							$list6[$i]['hj_num']=D('Productkc')->where(array('goods_id'=>$val['goods_id'],'wbid'=>$wbid,'position'=>1))->getField('num');
		                    $list6[$i]['ck_num']=D('Productkc')->where(array('goods_id'=>$val['goods_id'],'wbid'=>$wbid,'position'=>0))->getField('num');	
							
							$i++;
						}							
						break;
					}	
				}				
			}						
		}
		
		
		
		
		// foreach($goodslist as &$val)
		// {
		  // $val['value']=$val['goods_pinyin'].','.$val['goods_name'].','.$val['goods_quanpin'].','.$val['barcode'];
		  // $val['label']=$val['goods_pinyin'].','.$val['goods_name'].','.$val['goods_quanpin'].','.$val['barcode'];
		  // $val['hj_num']=D('Productkc')->where(array('goods_id'=>$val['goods_id'],'wbid'=>session('wbid'),'position'=>1))->getField('num');
		  // $val['ck_num']=D('Productkc')->where(array('goods_id'=>$val['goods_id'],'wbid'=>session('wbid'),'position'=>0))->getField('num');			  	  
		// }
		// var_dump(json_encode($goodslist));
		$this->assign('goodslist',json_encode($list6));	
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
			//	writelog('jinhuo_edit_set---未重复提交');
			}
	        $wbid=session('wbid');
	        $post_order_no = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);  //网站订单号
			$post_order_no='JH'.$post_order_no;
            $dtInsertTime=date('Y-m-d H:i:s',time());     			
			$str=I('post.goodsinfo','','string');
            // $position=I('post.position','','string');
			
			$unit=I('post.unit','','string');	 //按件按个
			$sumje=I('post.sumje','','string');	
			
            $str=htmlspecialchars_decode($str); 		
			$jinhuo_goodslist=json_decode($str,true);
			
			$map=array();
			$map['wbid']=$wbid;
			$map['deleted'] =0;
			$map['is_zuhe']=2;
			$zuhe_goodslist=D('Product')->where($map)->field('id,goods_id,goods_name')->select();	
	        
										
			if(empty($jinhuo_goodslist))
			{	
              $data['status']=-1;
			  $this->ajaxReturn($data);
			  return;	
			}	
			

		
			$info=' 总金额：'.$sumje.',';
			$result=true;
			D()->startTrans();  //启用事务
			
			foreach( $jinhuo_goodslist as $val)
			{
				$jinhuomx_insert_data['goods_id']=$val['goods_id'];
				$jinhuomx_insert_data['sumnum']  =$val['sumnum'];
				$jinhuomx_insert_data['hj_num']  =D('Productkc')->where(array('wbid'=>$wbid,'position'=>1,'goods_id'=>$val['goods_id']))->getField('num');
				$jinhuomx_insert_data['ck_num']  =D('Productkc')->where(array('wbid'=>$wbid,'position'=>0,'goods_id'=>$val['goods_id']))->getField('num');
				
				if($unit=='1')
				{
				  $jinhuomx_insert_data['jian_num']=$val['num'];	
				  $jinhuomx_insert_data['type']=1;              //按件
				}
				else if($unit=='2')
                {
				  $jinhuomx_insert_data['jian_num']=0;		
				  $jinhuomx_insert_data['type']=2;            //按个
				}
				
				$jinhuomx_insert_data['price']=$val['price'];					
				$jinhuomx_insert_data['je']=$val['price']*$val['num'];
				$jinhuomx_insert_data['post_order_no']=$post_order_no;
				$jinhuomx_insert_data['position']=$val['jinhuo_position'];
				$jinhuomx_insert_data['wbid']=$wbid;
				$jinhuomx_insert_data['dtInsertTime']=$dtInsertTime;
				$jinhuomx_insert_data['operate']=session('username');
	
				
				$goods_info=D('Product')->where(array('goods_id'=>$val['goods_id'],'wbid'=>$wbid))->find();
				if($val['jinhuo_position']==1)//立即上架
				{					
					//判断该商品是不是组合商品，是的话直接增加库存
					
					if($goods_info['is_zuhe']==0) //普通商品
					{	
                        					
						if((D('Productkc')->where(array('wbid'=>$wbid,'position'=>1,'goods_id'=>$val['goods_id']))->setInc('num',$val['sumnum']))===false)
						{
							$result=false;					
						}									
						$jinhuomx_insert_data['is_zuhe_goods']=0;
						$jinhuomx_insert_data['zuhe_id']=0;
						$jinhuomx_insert_data['zuhe_flag']=1;						
						if(D('Productjchmx')->add($jinhuomx_insert_data)===false)
						{
							writelog('----4---error---');
							$result=false;					
						}
						
					}					
					else if($goods_info['is_zuhe']==1)
                    {
						
						foreach($zuhe_goodslist as &$val1)
						{
							if($val1['goods_id']==$goods_info['zuhe_id'])
							{
								$val1['sumnum']+=$val['sumnum'];
								break;
							}	
						}	
						
						$jinhuomx_insert_data['zuhe_id']=$goods_info['zuhe_id'];
                        $jinhuomx_insert_data['zuhe_flag']=1;
                        $jinhuomx_insert_data['is_zuhe_goods']=1;						
						if(D('Productjchmx')->add($jinhuomx_insert_data)===false)
						{
							writelog('----6---error---');
							$result=false;					
						}
						
					}															
				}
				else    //进入仓库
                {				
					$jinhuomx_insert_data['zuhe_id']=0;
					if($goods_info['is_zuhe']==0)
					{
						$jinhuomx_insert_data['zuhe_id']=0;
                        $jinhuomx_insert_data['zuhe_flag']=1;
                        $jinhuomx_insert_data['is_zuhe_goods']=0;
					}
					else
                    {
						$jinhuomx_insert_data['zuhe_flag']=1;
						$jinhuomx_insert_data['zuhe_id']=$goods_info['zuhe_id'];
                        $jinhuomx_insert_data['is_zuhe_goods']=1;
					}					
					
					if(D('Productjchmx')->add($jinhuomx_insert_data)===false)
					{
						$result=false;					
					}
					
					//直接存到该商品的仓库里
					if((D('Productkc')->where(array('wbid'=>$wbid,'position'=>0,'goods_id'=>$val['goods_id']))->setInc('num',$val['sumnum']))===false)
					{
						$result=false;					
					}	
				}					
						
				$val['goods_name']=D('Product')->where(array('wbid'=>$wbid,'goods_id'=>$val['goods_id']))->getField('goods_name');
                if($unit=='1')
				{
				  $info.= $val['goods_name'].':'.$val['num'].'件'.' ';	
				}
				else if($unit=='2')
                {
					$info.= $val['goods_name'].':'.$val['num'].'个'.' ';
				}  					
                								
			}
			
			//=========================添加所有的组合商品的明细数据=================================
			
			
			foreach( $zuhe_goodslist as &$val)
			{
				
				$jinhuomx_insert_data=array();
				$jinhuomx_insert_data['goods_id']=$val['goods_id'];
				$jinhuomx_insert_data['sumnum']  =$val['sumnum'];
				$jinhuomx_insert_data['hj_num']  =D('Productkc')->where(array('wbid'=>$wbid,'position'=>1,'goods_id'=>$val['goods_id']))->getField('num');
				$jinhuomx_insert_data['ck_num']  =0;														
				$jinhuomx_insert_data['price']=$val['price'];					
				$jinhuomx_insert_data['je']=$val['price']*$val['num'];
				$jinhuomx_insert_data['post_order_no']=$post_order_no;
				$jinhuomx_insert_data['position']=1;
				$jinhuomx_insert_data['wbid']=$wbid;
				$jinhuomx_insert_data['dtInsertTime']=$dtInsertTime;
				$jinhuomx_insert_data['operate']=session('username');				
				$jinhuomx_insert_data['zuhe_id']=0;	
				$jinhuomx_insert_data['zuhe_flag']=1;
				$jinhuomx_insert_data['is_zuhe_goods']=2;
				
				
				if($val['sumnum'] >0)
				{
					
					if(D('Productjchmxzh')->add($jinhuomx_insert_data)===false)
					{
						writelog('----8---error---');
						$result=false;					
					}
					
					//组合商品上架的话 直接增加 zuhe_id 里的货架库存
					if((D('Productkc')->where(array('wbid'=>$wbid,'position'=>1,'goods_id'=>$val['goods_id']))->setInc('num',$val['sumnum']))===false)
					{
						writelog('----9---error---');
						$result=false;					
					}
					
				}													
			}	
			
			
			
			
			//更新库存表	
			$jinhuo_insert_data['post_order_no']=$post_order_no;
			$jinhuo_insert_data['jch_type']=1;
			$jinhuo_insert_data['wbid']=$wbid;
			$jinhuo_insert_data['info']=$info;		
			$jinhuo_insert_data['dtInsertTime']=$dtInsertTime;
			$jinhuo_insert_data['sumje']=$sumje;
			$jinhuo_insert_data['bz']=I('post.bz','','string');
			$jinhuo_insert_data['operator']=session('username');
			$jinhuo_insert_data['detailinfo']=$str;
			$jinhuo_insert_data['zuhe_flag']=1;	
			if(D('Productjch')->add($jinhuo_insert_data)===false)
			{
				writelog('----11---error---');
				$result=false;
			}	
			
			
			if($result)
            {
			  
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
	
	
	
	//===============================销售页面====================
	public function xiaoshou_edit_set()
	{
		if(IS_AJAX)
		{    						
			if(!checkToken($_POST['token']))
			{  
		        writelog('xiaoshou_edit_set---重复提交');
				$data['status']=-2;          								
			    $this->ajaxReturn($data);
                return;   			   
			}
			else
			{
				//writelog('xiaoshou_edit_set---未重复提交');
			}	
			
		
	        $wbid=session('wbid');  
	        $post_order_no = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);  //网站订单号
			$post_order_no='XS'.$post_order_no;
            $dtInsertTime=date('Y-m-d H:i:s',time());     			
			$str=I('post.goodsinfo','','string');	
            $str=htmlspecialchars_decode($str); 		
			$xiaoshou_goodslist=json_decode($str,true);
			
			$zf_order_no=I('post.zf_order_no','','string');
			
			// 获取本次所有的zuhe_id 列表
			
		
			$map=array();
			$map['info.wbid']=$wbid;
			//$map['info.deleted'] =0;
			$map['info.is_zuhe'] =2;
            $map['kc.position']  =1;			
			$zuhe_goodslist=D('Productkc')->getAllChuhuokucunfoListByMap($map);
			
			foreach($zuhe_goodslist as &$val)
			{
				$zuhe_id=$val['goods_id'];
				foreach($xiaoshou_goodslist as &$val2)
				{
					if($zuhe_id==$val2['zuhe_id'])
					{
						$val['xiaoshou_num']+=$val2['xiaoshou_num'];
					}	
				}
			}
			
		
				
			$i=0;
			$list=array();
			foreach($zuhe_goodslist as &$val)
			{
			   if($val['xiaoshou_num'] > 0)                 //需要插入的组合商品记录
			   {
				   $list[$i]['goods_id']=$val['goods_id'];	
				   $list[$i]['goods_name']=$val['goods_name'];
                   $list[$i]['xiaoshou_num']=$val['xiaoshou_num'];				   				   
				   $list[$i]['price']=$val['shou_price'];
				   $list[$i]['je']=$val['shou_price']* $val['xiaoshou_num'];
				   $list[$i]['hj_num']=$val['num'];	
				   $list[$i]['ck_num']=0;	
				   $list[$i]['is_zuhe_goods']=2;
				   $i++;
			   }	   
			}

		
								
			if(empty($xiaoshou_goodslist))
			{	
              $data['status']=-1;
			  $this->ajaxReturn($data);
			  return;	
			}
						
			$sum_sr_je=I('post.sum_sr_je','0','float');
			$sum_sp_je=I('post.sum_sp_je','0','float');	
			$sum_zl_je=$sum_sr_je-$sum_sp_je;
			$info='';

			$result=true;
			D()->startTrans();  //启用事务
			
			foreach($list  as &$val)                                     //先处理组合商品的明细
			{
				$xiaoshoumx_insert_data=array();  			
				$xiaoshoumx_insert_data['xiaoshou_num']     =$val['xiaoshou_num'];
				$xiaoshoumx_insert_data['ck_num']  =0;
				$xiaoshoumx_insert_data['hj_num']  =$val['hj_num'];
				$xiaoshoumx_insert_data['je']=$val['je'];
				$xiaoshoumx_insert_data['price']=$val['price'];
				$xiaoshoumx_insert_data['post_order_no']=$post_order_no;
				$xiaoshoumx_insert_data['ordertype']=1;
				$xiaoshoumx_insert_data['wbid']=$wbid;
				$xiaoshoumx_insert_data['operate']=session('username');
				$xiaoshoumx_insert_data['dtInsertTime']=$dtInsertTime;	
				$xiaoshoumx_insert_data['goods_id']=$val['goods_id'];
				$xiaoshoumx_insert_data['zuhe_id']=$val['goods_id'];
				$xiaoshoumx_insert_data['is_zuhe_goods']=2;
				$xiaoshoumx_insert_data['zuhe_flag']=1;
				if(D('Productxsmxzh')->add($xiaoshoumx_insert_data)===false)
				{					
					$result=false;
				}
			}
			
			
			$sumje=0;
			foreach( $xiaoshou_goodslist as &$val)
			{		
                $xiaoshoumx_insert_data=array();  			
				$xiaoshoumx_insert_data['xiaoshou_num']     =$val['xiaoshou_num'];
				$xiaoshoumx_insert_data['ck_num']  =D('Productkc')->where(array('wbid'=>$wbid,'position'=>0,'goods_id'=>$val['goods_id']))->getField('num');
				$xiaoshoumx_insert_data['hj_num']  =D('Productkc')->where(array('wbid'=>$wbid,'position'=>1,'goods_id'=>$val['goods_id']))->getField('num');
				$xiaoshoumx_insert_data['je']=$val['xiaoshou_num']*$val['price'];
				$xiaoshoumx_insert_data['price']=$val['price'];
				$xiaoshoumx_insert_data['post_order_no']=$post_order_no;
				$xiaoshoumx_insert_data['ordertype']=1;
				$xiaoshoumx_insert_data['wbid']=$wbid;
				$xiaoshoumx_insert_data['operate']=session('username');
				$xiaoshoumx_insert_data['dtInsertTime']=$dtInsertTime;	
                $xiaoshoumx_insert_data['zuhe_id']=0;
				$xiaoshoumx_insert_data['is_zuhe_goods']=0;
				$xiaoshoumx_insert_data['zuhe_flag']=1;	
				
				$sumje+= $xiaoshoumx_insert_data['je'];
											 				
				$goodsinfo=D('Product')->where(array('wbid'=>$wbid,'goods_id'=>$val['goods_id']))->find();						
				if($goodsinfo['is_zuhe']==0)
				{
					$xiaoshoumx_insert_data['goods_id']=$val['goods_id'];
					$xiaoshoumx_insert_data['zuhe_id']=0;
					if(D('Productxsmx')->add($xiaoshoumx_insert_data)===false)
					{					
						$result=false;
					}
					$now_hjkc_num= D('Productkc')->where(array('wbid'=>$wbid,'position'=>1,'goods_id'=>$val['goods_id']))->getField('num');
					if($val['xiaoshou_num'] >= $now_hjkc_num)
					{
						$now_sj_xiaoshou_num =$now_hjkc_num;
					}
					else
					{
						$now_sj_xiaoshou_num =$val['xiaoshou_num'];
					}				
					if(D('Productkc')->where(array('wbid'=>$wbid,'position'=>1,'goods_id'=>$val['goods_id']))->setDec('num',$now_sj_xiaoshou_num)===false)
					{					
						$result=false;
					}
				}
				else if($goodsinfo['is_zuhe']==1)
                {
					$zuhe_id= $goodsinfo['zuhe_id'];				
					$xiaoshoumx_insert_data['goods_id']=$val['goods_id'];					
					$xiaoshoumx_insert_data['zuhe_id']=$zuhe_id;
				    $xiaoshoumx_insert_data['is_zuhe_goods']=1;
					$xiaoshoumx_insert_data['zuhe_flag']=1;	
					if(D('Productxsmx')->add($xiaoshoumx_insert_data)===false)
					{					
						$result=false;
					}					
					$now_hjkc_num= D('Productkc')->where(array('wbid'=>$wbid,'position'=>1,'goods_id'=>$zuhe_id))->getField('num');
					if($val['xiaoshou_num'] >= $now_hjkc_num)
					{
						$now_sj_xiaoshou_num =$now_hjkc_num;
					}
					else
					{
						$now_sj_xiaoshou_num =$val['xiaoshou_num'];
					}					
				   	if(D('Productkc')->where(array('wbid'=>$wbid,'position'=>1,'goods_id'=>$goodsinfo['zuhe_id']))->setDec('num',$now_sj_xiaoshou_num)===false)
					{					
						$result=false;
					}	
				}														
	
				$val['goods_name']=D('Product')->where(array('wbid'=>$wbid,'goods_id'=>$val['goods_id']))->getField('goods_name');				
				$info.= $val['goods_name'].':'.$val['xiaoshou_num'].' ';
			}
			
			
			//如果订单号不为空 就选择一条订单
			if(!empty($zf_order_no))
			{
				$order_update_data=array();
				$order_update_data['xs_order_no']=$post_order_no;
				$order_update_data['dtUsedTime'] =$dtInsertTime;
				$order_update_data['isUsed'] =1;
				$order_update_data['operator'] =session('username');
				$map=array();
				$map['wbid']=session('wbid');
				$map['post_order_no']=$zf_order_no;
				if(D('Productxsbt')->where($map)->save($order_update_data)===false)
				{
					$result=false;
				}	
			}	
		
			
			//更新库存表
			
			$xiaoshou_insert_data['post_order_no']=$post_order_no;
			$xiaoshou_insert_data['ordertype']=1;
			$xiaoshou_insert_data['wbid']=$wbid;
			$xiaoshou_insert_data['info']=$info;
			$xiaoshou_insert_data['sum_sp_je']=$sum_sp_je;	
			$xiaoshou_insert_data['sum_sr_je']=$sum_sr_je;
			$xiaoshou_insert_data['sum_zl_je']=$sum_zl_je;	
			$xiaoshou_insert_data['beizhu']=I('post.bz','','string');
			$xiaoshou_insert_data['operator']=session('username');						
			$xiaoshou_insert_data['dtInsertTime']=$dtInsertTime;
			$xiaoshou_insert_data['detailinfo']=$str;
			$xiaoshou_insert_data['zuhe_flag']=1;
			
			
	
			
			
			
			if($sum_zl_je)
			{
			  $xiaoshou_insert_data['sum_zl_je']=$sum_zl_je;	
			}
			else
			{
				$xiaoshou_insert_data['sum_zl_je']=0;
			} 
			
			if($sum_sp_je)
			{
			  $xiaoshou_insert_data['sum_sp_je']=$sum_sp_je;	
			}
			else
			{
				$xiaoshou_insert_data['sum_sp_je']=0;
			} 

			if($sum_sr_je)
			{
			    $xiaoshou_insert_data['sum_sr_je']=$sum_sr_je;	
			}
			else
			{
				$xiaoshou_insert_data['sum_sr_je']=0;
			} 				
									
			if(D('Productxs')->add($xiaoshou_insert_data)===false)
			{				
				$result=false;
			}				  			
			if($result)
            {
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
	
	//===============================货架库存页面====================
	public function kucun()
	{
		$bOpen=$this->check_newcs_qx();
		if($bOpen===false)
		{		  
			$this->redirect('rate/fujia', array('cate_id' => 2), 1, '请在附加设置里开启设置...');	  
		}
		$type_list=D('ProductType')->select();
        $this->assign('type_list',$type_list);
		$wbid=session('wbid');
		
		$map = array(); 	
		$map['wbid']=$wbid;				  		  		  	
		$map['is_zuhe']=array('neq',2);
		$map['deleted']=0;			
	    $unzuhe_goodslist=D('Product')->where($map)->select();			
        $this->assign('unzuhe_goodslist',json_encode($unzuhe_goodslist));				
		$this->display();
	}
	
	public function getkucuninfolist_hj()
	{
		$wbid=session('wbid');		   
		$goods_type      = I('get.goods_type','','string'); 	
		$goods_id        = I('get.goods_id','','string'); 
		
		$type_list=D('ProductType')->select();
		  							 
		$map = array(); 	
		$map['kc.wbid']=$wbid;		
		$map['kc.position']=1;	
				
		if(!empty($goods_id))
		{
		  $zuhe_id=D('Product')->where(array('wbid'=>$wbid,'goods_id'=>$goods_id))->getField('zuhe_id');
		  if(!empty($zuhe_id))
		  {
			 $map['info.goods_id']=$zuhe_id;  
		  }else
          {
			 $map['info.goods_id']=$goods_id;  
		  }			  
		}  		
		if(!empty($goods_type))
		{
		  $map['info.type_id']=$goods_type;
		}  
	
	    $list=array();
	    $i=0;
		$map['info.is_zuhe']=array('neq',1);
		$map['info.deleted']=0;				
	    $goods_list=D('Productkc')->getAllChuhuokucunfoListByMap($map);		
        foreach($goods_list as &$val)
        {			
            if($val['is_zuhe']==0)
            {
				$list[$i]['goods_id']=$val['goods_id'];	
				$list[$i]['goods_name']=$val['goods_name'];
				$list[$i]['unit']=$val['unit'];
				$list[$i]['guige']=$val['guige'];
				$list[$i]['shou_price']=sprintf("%.2f",$val['shou_price']);
				$list[$i]['is_zuhe']=$val['is_zuhe'];
				$list[$i]['zuhe_id']=$val['zuhe_id'];
				foreach($type_list as $val1)
				{
					if($val['type_id']==$val1['type_id'])
					{
						$list[$i]['type_name']=$val1['type_name'];
						break;
					}	
				}
				
				
				$list[$i]['position']=1;
				$list[$i]['num']=$val['num'];
				$i++;
			}
			else if($val['is_zuhe']==2)
            {			
				$zuhe_id=$val['goods_id'];
				$zuhe_goods_array=array();
				$map=array();
											
				$map['is_zuhe']=1;
				$map['zuhe_id']=$zuhe_id;	
                $map['wbid']=$wbid;					
				$zuhe_goods_array=D('Product')->where($map)->select();													
				$list[$i]['goods_id']=$val['goods_id'];
				foreach($type_list as $val1)
				{
					if($val['type_id']==$val1['type_id'])
					{
						$list[$i]['type_name']=$val1['type_name'];
						break;
					}	
				}
				$list[$i]['goods_name']=$val['goods_name'];
				$list[$i]['unit']=$val['unit'];
				$list[$i]['guige']=$val['guige'];
				$list[$i]['shou_price']=sprintf("%.2f",$val['shou_price']);
				$list[$i]['is_zuhe']=$val['is_zuhe'];
				$list[$i]['zuhe_id']=$val['zuhe_id'];
				$list[$i]['position']=1;				
				$list[$i]['num']=$val['num'];				
				if(!empty($zuhe_goods_array))
				{
					$list[$i]['zuhelist']=$zuhe_goods_array;
				}
				else
                {
					$list[$i]['zuhelist']='';
				}					
											
				$i++;
			}				
									
		}			   	
		$this->ajaxReturn($list);																							  
	}
	//===============================仓库库存页面====================
	public function ckkc()
	{
		$type_list=D('ProductType')->select();
        $this->assign('type_list',$type_list);
		
		
		$wbid=session('wbid');	
		$map = array(); 	
		$map['wbid']=$wbid;				  		  		  	
		$map['is_zuhe']=array('neq',2);
		$map['deleted']=0;			
	    $unzuhe_goodslist=D('Product')->where($map)->select();	
		
        $this->assign('unzuhe_goodslist',json_encode($unzuhe_goodslist));
		
		
		$this->display();
	}
	
	public function getkucuninfolist_ck()
	{			
		$wbid=session('wbid');	
	
		$goods_type      = I('get.goods_type','','string'); 
        $goods_id        = I('get.goods_id','','string'); 	
		$type_list=D('ProductType')->select();
		$map = array(); 	
		$map['kc.wbid']=$wbid;			
		$map['kc.position']=0;		  		  		  		
		if(!empty($goods_id))
		{
		   $map['info.goods_id']=$goods_id;  
		}  
		
		if(!empty($goods_type))
		{
		  $map['info.type_id']=$goods_type;
		}  

		$map['info.is_zuhe']=array('neq',2);
		$map['info.deleted']=0;			
	    $goods_list=D('Productkc')->getAllChuhuokucunfoListByMap($map);	
		
		$map=array();	
        $map['kc.wbid']=$wbid;			
		$map['kc.position']=0;	
        $map['info.is_zuhe']=2;
        $map['info.deleted']=0;		
		
		$zuhe_list=D('Productkc')->getAllChuhuokucunfoListByMap($map);
		
        foreach($goods_list as &$val)
        {			
			if($val['is_zuhe']==2)
            {			
				$zuhe_id=$val['zuhe_id'];
				foreach($zuhe_list as &$val1)
				{
					if($val1['goods_id']==$zuhe_id)
					{
						$val['num']=$val1['num'];
						break;
					}	
				}										
			}
			
			foreach($type_list as $val1)
			{
				if($val['type_id']==$val1['type_id'])
				{
					$val['type_name']=$val1['type_name'];
					break;
				}	
			}
			
			$val['shou_price']=sprintf("%.2f",$val['shou_price']);
            $val['zong_price']=$val['num']*$val['shou_price'];			
									
		}	
		   			
		$this->ajaxReturn($goods_list);
		
		
																			  
	}
	
	
	
	
	
	
	public function jiaoban_mx()
	{
					
		$this->display();
	}
	
	
	
	
	//===============shangjia_zzb=================================
	public function shangjia_zzb()
	{   

	    session('goods_id_list',null);
	    $wbid=session('wbid');
		session('plch_status','0');
		
		$map=array();
		$map['kc.num']=array('gt',0);
		$map['kc.wbid']=session('wbid');
		$map['kc.position']=0;
		$map['kc.deleted']=0;
		$map['info.deleted']=0;	
		$map['info.is_zuhe']=array('neq',2);	
		
	    $goodslist=D('Productkc')->getAllChuhuokucunfoListByMap($map);
		foreach($goodslist as &$val)
		{
		  $val['value']=$val['goods_pinyin'].','.$val['goods_name'].','.$val['goods_quanpin'];
		  $val['label']=$val['goods_pinyin'].','.$val['goods_name'].','.$val['goods_quanpin'];
		  $val['hj_num']=D('Productkc')->where(array('goods_id'=>$val['goods_id'],'wbid'=>session('wbid'),'position'=>1))->getField('num');
		  $val['ck_num']=D('Productkc')->where(array('goods_id'=>$val['goods_id'],'wbid'=>session('wbid'),'position'=>0))->getField('num');
		  	  
		}
		
        //单个有仓库库存商品的列表
		$this->assign('goodslist',json_encode($goodslist));	
		
		
		$map=array();
		$map['is_zuhe']=2;
		$map['deleted']=0;
		$map['wbid']=$wbid;
		$zuhelist=D('Product')->where($map)->select();
		$i=0;
		$list=array();
		foreach($zuhelist as &$val)
		{
			$zuhe_id=$val['goods_id'];
			$zuhe_goods_array=array();
			$map=array();
			
			$map['is_zuhe']=1;
			$map['zuhe_id']=$zuhe_id;	
			$map['wbid']=$wbid;					
			$zuhe_goods_array=D('Product')->where($map)->select();
			foreach($zuhe_goods_array as &$val1)
			{
				$val1['hj_num']=D('Productkc')->where(array('goods_id'=>$val1['goods_id'],'wbid'=>$wbid,'position'=>1))->getField('num');
				$val1['ck_num']=D('Productkc')->where(array('goods_id'=>$val1['goods_id'],'wbid'=>$wbid,'position'=>0))->getField('num');
			}
	
										
			$list[$i]['goods_id']=$val['goods_id'];
			$list[$i]['type_name']=D('ProductType')->where(array('type_id'=>$val['type_id']))->getField('type_name');
			$list[$i]['goods_name']=$val['goods_name'];
			$list[$i]['unit']=$val['unit'];
			$list[$i]['guige']=$val['guige'];
			$list[$i]['shou_price']=$val['shou_price'];
			$list[$i]['is_zuhe']=$val['is_zuhe'];
			$list[$i]['zuhe_id']=$val['zuhe_id'];
			$list[$i]['hj_num']=D('Productkc')->where(array('goods_id'=>$zuhe_id,'wbid'=>$wbid,'position'=>1))->getField('num');
			
			if(!empty($zuhe_goods_array))
			{
				$list[$i]['zuhelist']=$zuhe_goods_array;
			}
			else
			{
				$list[$i]['zuhelist']='';
			}					
										
			$i++;
		}
		
          //组合商品的列表
		$this->assign('zuhegoods_list',json_encode($list));
		
		
		
		// is_zuhe=0  is_zuhe=2 的所有商品
		
		$map=array();
		$map['is_zuhe']=array('neq',1);
		$map['deleted']=0;
		$map['wbid']=$wbid;
		$all_goodsid_list=D('Product')->Field('is_zuhe,goods_id')->where($map)->select();								
		$this->assign('all_goodsid_list',json_encode($all_goodsid_list));
		creatToken();
        $this->display();  
	}
	
}



