<?php
    namespace Home\Model;
    use Think\Model;
    class ProductkcModel extends Model 
    {
        protected $tableName = 'wt_goodskc';
        public function getkucunfoListByMap_count($map=array())
		{   $map['kc.deleted']=0;
			$count=$this->alias('kc')->join('left join wt_goodsinfo info on info.goods_id=kc.goods_id and info.wbid=kc.wbid')->where($map)->count(); 	
			
			return $count;
		}

		public function getkucunfoListByMap($map=array(),$order = '',$page = 1,$rows = 20)
		{  $map['kc.deleted']=0;
			$count=$this->alias('kc')->join('left join wt_goodsinfo info on info.goods_id=kc.goods_id and info.wbid=kc.wbid')->where($map)->count(); 

			$list= $this->alias('kc')->join('left join wt_goodsinfo info on info.goods_id=kc.goods_id and info.wbid=kc.wbid')
			->field(array(
			   'kc.wbid'=>'wbid',
			   'kc.goods_id'=>'goods_id',
			   'kc.num'=>'num',
			   'info.goods_name'=>'goods_name',
			   'info.unit'=>'unit',
			   'info.guige'=>'guige',
			   'info.type_id'=>'type_id',
			   'kc.position'=>'position',
	   
			))
			->where($map)->page($page,$rows)->order($order)->select();
				
				
				
			foreach($list as &$val)
			{   	
				$val['type_id']=D('ProductType')->where(array('type_id'=>$val['type_id']))->getField('type_name');	
				$val['allname']=$val['goods_name'].$val['guige'];	
				$val['price']=D('Product')->where(array('wbid'=>session('wbid'),'goods_id'=>$val['goods_id']))->getField('shou_price');
				
				$val['price']=sprintf("%.2f",$val['price']);
				$val['sumje']=$val['price']*$val['num'];

				// $val['agent_realname']=D('Agent')->where(array('agent_id'=>$val['agent_id']))->getField('agent_realname');	
				// $val['agent_name']=D('Agent')->where(array('agent_id'=>$val['agent_id']))->getField('agent_name');						
				// $val['dtInsertTime']=date('Y-m-d H:i:s',strtotime($val['dtInsertTime']));								
			}
	 
			return array('count'=>$count,'list'=>$list); 
		}
		
		
		
		
		public function getAllkucunfoListByMap($map=array(),$canshu='')
		{
			$map['info.type_id']=$canshu;
			
			$count=$this->alias('kc')->join('left join wt_goodsinfo info on info.goods_id=kc.goods_id and info.wbid=kc.wbid')->where($map)->count(); 

			$list= $this->alias('kc')->join('left join wt_goodsinfo info on info.goods_id=kc.goods_id and info.wbid=kc.wbid')
			->field(array(
			   'kc.wbid'=>'wbid',
			   'kc.goods_id'=>'goods_id',
			   'kc.num'=>'num',
			   'info.goods_name'=>'goods_name',
			   'info.unit'=>'unit',
			   'info.guige'=>'guige',
			   'info.type_id'=>'type_id',
			   'kc.position'=>'position',
			   'info.shou_price'=>'shou_price',
			   'info.goods_pinyin'=>'goods_pinyin',
			   'info.goods_quanpin'=>'goods_quanpin',
			   	'info.is_zuhe'=>'is_zuhe',
			    'info.zuhe_id'=>'zuhe_id',
	   
			))
			->where($map)->select();
								
				
			foreach($list as &$val)
			{   

	
				$val['type_id']=D('ProductType')->where(array('type_id'=>$val['type_id']))->getField('type_name');	
				$val['allname']=$val['goods_name'].$val['guige'];	
				$val['shou_price']=sprintf("%.2f",$val['shou_price']);												
			}
	 
			return $list; 
		}
		
		
		public function getAllChuhuokucunfoListByMap($map=array())
		{					
			$list= $this->alias('kc')
			->join('left join wt_goodsinfo info on info.goods_id=kc.goods_id and info.wbid=kc.wbid')
			->join('left join wt_goods_zuhe zuhe on zuhe.zuhe_id=kc.goods_id and zuhe.wbid = kc.wbid')
			->field(array(
			   'kc.wbid'=>'wbid',
			   'kc.goods_id'=>'goods_id',
			   'kc.num'=>'num',
			   'zuhe.deleted'=>'deleted',
			   'zuhe.isValid'=>'isValid',
			   'info.goods_name'=>'goods_name',
			   'info.one_jian_num'=>'one_jian_num',
			   'info.one_jian_jin_price'=>'one_jian_jin_price',
			   'info.one_ge_jin_price'=>'one_ge_jin_price',
			   'info.barcode'=>'barcode',
			   'info.unit'=>'unit',
			   'info.guige'=>'guige',
			   'info.type_id'=>'type_id',
			   'info.shou_price'=>'shou_price',
			   'info.goods_pinyin'=>'goods_pinyin',
			   'info.goods_quanpin'=>'goods_quanpin',
			   'info.is_zuhe'=>'is_zuhe',
			   'info.zuhe_id'=>'zuhe_id',
	   
			))
			->where($map)->select();
															
			return $list; 
		}
		
		public function getAllChuhuokucunfoListByMap2($map=array())
		{			
		    $type_list=D('ProductType')->select();
			
			
			$map1=array();
			$map1['info.wbid']=$map['info.wbid'];
			$map1['info.is_zuhe'] =2;
            $map1['kc.position']  =1;
            $map1['kc.num']=array('gt',0);	
			
			$zuhe_goods_list=D('Productkc')->getAllChuhuokucunfoListByMap($map1);
			
			
	
			$map['kc.position']=1;
			$map['info.shou_price']=array('gt',0);
			$map['info.is_zuhe']=array('neq',2);					 	
			$list= $this->alias('kc')->join('left join wt_goodsinfo info on info.goods_id=kc.goods_id and info.wbid=kc.wbid')
			->field(array(
			   'kc.wbid'=>'wbid',
			   'kc.goods_id'=>'goods_id',
			   'kc.num'=>'num',
			   'kc.position'=>'position',
			   'info.goods_name'=>'goods_name',		 
			   'info.type_id'=>'type_id',
			   'info.is_zuhe'=>'is_zuhe',
			   'info.zuhe_id'=>'zuhe_id',
			   'info.goods_image'=>'goods_image',			   			   
			   'info.shou_price'=>'shou_price',
			))->where($map)->select();
			
			$shiji_goods_list=array();
			$i=0;
			foreach($list as &$val)
			{   			
                $a_goods_id=$val['goods_id'];
				$is_zuhe=$val['is_zuhe'];
				if($val['is_zuhe']==0)
				{
					if($val['num'] >0)
					{								
						$shiji_goods_list[$i]['goods_id']=$val['goods_id'];
						$shiji_goods_list[$i]['num']=$val['num'];
						$shiji_goods_list[$i]['goods_name']=$val['goods_name'];
						$shiji_goods_list[$i]['type_id']=$val['type_id'];
						$shiji_goods_list[$i]['is_zuhe']=0;
						$shiji_goods_list[$i]['zuhe_id']=0;
						
						if(!empty($val['goods_image']))
						{
							$filepath= C('UPLOAD_SHANGPIN_DIR').$val['goods_image'];														
							if(file_exists($filepath))
							{
							  $shiji_goods_list[$i]['goods_image']=C('SHANGPIN_TUPIAN_PATH_URL').$val['goods_image'];	
							}
							else
							{
								$shiji_goods_list[$i]['goods_image']=C('SHANGPIN_TUPIAN_PATH_URL').'moren.png';
							}												
						}
						else
						{
							$shiji_goods_list[$i]['goods_image']=C('SHANGPIN_TUPIAN_PATH_URL').'moren.png';
						}
						
						$shiji_goods_list[$i]['shou_price']=sprintf("%.2f",$val['shou_price']);
						$i++;
				    }
				}
				else if($val['is_zuhe']==1)
                {
					$bValid=false;
					foreach($zuhe_goods_list as &$val2)
					{				
						if($val2['goods_id']==$val['zuhe_id'])
						{
							if($val2['num'] >0)
							{
								$val['num']=0;
								$bValid=true;
								break;
							}	
						}							
					}
					
					if($bValid==true)
					{
						$shiji_goods_list[$i]['goods_id']=$val['goods_id'];
						$shiji_goods_list[$i]['num']=0;
						$shiji_goods_list[$i]['goods_name']=$val['goods_name'];
						$shiji_goods_list[$i]['type_id']=$val['type_id'];
						$shiji_goods_list[$i]['is_zuhe']=1;
						$shiji_goods_list[$i]['zuhe_id']=$val['zuhe_id'];
						
						if(!empty($val['goods_image']))
						{
							$filepath= C('UPLOAD_SHANGPIN_DIR').$val['goods_image'];														
							if(file_exists($filepath))
							{
							  $shiji_goods_list[$i]['goods_image']=C('SHANGPIN_TUPIAN_PATH_URL').$val['goods_image'];	
							}
							else
							{
								$shiji_goods_list[$i]['goods_image']=C('SHANGPIN_TUPIAN_PATH_URL').'moren.png';
							}												
						}
						else
						{
							$shiji_goods_list[$i]['goods_image']=C('SHANGPIN_TUPIAN_PATH_URL').'moren.png';
						}
						
						$shiji_goods_list[$i]['shou_price']=sprintf("%.2f",$val['shou_price']);
						$i++;
					}	
					
					
				}																							
			}								 
		    
			
			$list2['shiji_goods_list']=$shiji_goods_list;
			$list2['zuhe_goodslist']=$zuhe_goods_list;
			return $list2; 
		}
		
		public function getAllChuhuokucunfoListByMap2_count($map=array())
		{			
			$map['kc.position']=1;
			$map['kc.num']=array('gt',0);		 	
			$count= $this->alias('kc')->join('left join wt_goodsinfo info on info.goods_id=kc.goods_id and info.wbid=kc.wbid')
			->field(array(
			   'kc.wbid'=>'wbid',
			   'kc.goods_id'=>'goods_id',
			   'kc.num'=>'num',
			   'info.goods_name'=>'goods_name',		 
			   'info.type_id'=>'type_id',
			   'info.goods_image'=>'goods_image',		   
			   'kc.position'=>'position',
			   'info.shou_price'=>'shou_price',
			))
			->where($map)->group('kc.wbid,kc.goods_id,kc.num,info.goods_name,info.type_id,kc.position,info.shou_price,info.goods_pinyin,info.goods_quanpin,goods_image')->count();
								 
		   return $count; 
		}
		
		
		
		public function getAllChuhuokucunfoListByMap3($map=array(),$page_beg,$page_end,$type_id)
		{			
			$map['kc.position']=1;
			$map['kc.num']=array('gt',0);
			
			if($type_id==0)
			{
				$list= $this->alias('kc')->join('left join wt_goodsinfo info on info.goods_id=kc.goods_id and info.wbid=kc.wbid')
				->field(array(
				   'kc.wbid'=>'wbid',
				   'kc.goods_id'=>'goods_id',
				   'kc.num'=>'num',
				   'info.goods_name'=>'goods_name',		 
				   'info.type_id'=>'type_id',
				   'info.goods_image'=>'goods_image',
				   
				   'kc.position'=>'position',
				   'info.shou_price'=>'shou_price',
				))
				->where($map)->group('kc.wbid,kc.goods_id,kc.num,info.goods_name,info.type_id,kc.position,info.shou_price,info.goods_pinyin,info.goods_quanpin,goods_image')->limit($page_beg.','.$page_end)->select();
				
			}
			else if($type_id >0)
            {
				$map['info.type_id']=$type_id;
				$list= $this->alias('kc')->join('left join wt_goodsinfo info on info.goods_id=kc.goods_id and info.wbid=kc.wbid')
				->field(array(
				   'kc.wbid'=>'wbid',
				   'kc.goods_id'=>'goods_id',
				   'kc.num'=>'num',
				   'info.goods_name'=>'goods_name',		 
				   'info.type_id'=>'type_id',
				   'info.goods_image'=>'goods_image',			   
				   'kc.position'=>'position',
				   'info.shou_price'=>'shou_price',
				))
				->where($map)->group('kc.wbid,kc.goods_id,kc.num,info.goods_name,info.type_id,kc.position,info.shou_price,info.goods_pinyin,info.goods_quanpin,goods_image')->limit($page_beg.','.$page_end)->select();
				
			}				
		 	

			
			foreach($list as &$val)
			{   
			   
			    $val['shou_price']=sprintf("%.2f",$val['shou_price']);
				if(!empty($val['goods_image']))
				{
					$filepath= C('UPLOAD_SHANGPIN_DIR').$val['goods_image'];
					
					
					
					if(file_exists($filepath))
					{
					  $val['goods_image']=C('SHANGPIN_TUPIAN_PATH_URL').$val['goods_image'];	
					}
					else
                    {
						$val['goods_image']=C('SHANGPIN_TUPIAN_PATH_URL').'moren.png';
					}						
						
				}
				else
				{

					$val['goods_image']=C('SHANGPIN_TUPIAN_PATH_URL').'moren.png';
				}											
			}								 
		   return $list; 
		}
		
		public function getAllChuhuokucunfoListByMap3_count($map=array(),$type_id)
		{			
			$map['kc.position']=1;
			$map['kc.num']=array('gt',0);		 	
			$count= $this->alias('kc')->join('left join wt_goodsinfo info on info.goods_id=kc.goods_id and info.wbid=kc.wbid')
			->field(array(
			   'kc.wbid'=>'wbid',
			   'kc.goods_id'=>'goods_id',
			   'kc.num'=>'num',
			   'info.goods_name'=>'goods_name',		 
			   'info.type_id'=>'type_id',
			   'info.goods_image'=>'goods_image',		   
			   'kc.position'=>'position',
			   'info.shou_price'=>'shou_price',
			))
			->where($map)->count();
			
			
			if($type_id==0)
			{
				$count= $this->alias('kc')->join('left join wt_goodsinfo info on info.goods_id=kc.goods_id and info.wbid=kc.wbid')
				->field(array(
				   'kc.wbid'=>'wbid',
				   'kc.goods_id'=>'goods_id',
				   'kc.num'=>'num',
				   'info.goods_name'=>'goods_name',		 
				   'info.type_id'=>'type_id',
				   'info.goods_image'=>'goods_image',		   
				   'kc.position'=>'position',
				   'info.shou_price'=>'shou_price',
				))
				->where($map)->count();
			}
			else if($type_id >0)
            {
				$map['info.type_id']=$type_id;
				$count= $this->alias('kc')->join('left join wt_goodsinfo info on info.goods_id=kc.goods_id and info.wbid=kc.wbid')
				->field(array(
				   'kc.wbid'=>'wbid',
				   'kc.goods_id'=>'goods_id',
				   'kc.num'=>'num',
				   'info.goods_name'=>'goods_name',		 
				   'info.type_id'=>'type_id',
				   'info.goods_image'=>'goods_image',		   
				   'kc.position'=>'position',
				   'info.shou_price'=>'shou_price',
				))
				->where($map)->count();
			}	
			
								 
		   return $count; 
		}
        
		
		public function getAllzuhegoodsListByMap($map=array())
		{
			
			
			$count=$this->alias('kc')->join('left join wt_goodsinfo info on info.goods_id=kc.goods_id and info.wbid=kc.wbid')->where($map)->count(); 

			$list= $this->alias('kc')->join('left join wt_goodsinfo info on info.goods_id=kc.goods_id and info.wbid=kc.wbid')
			->field(array(
			   'kc.wbid'=>'wbid',
			   'kc.goods_id'=>'goods_id',
			   'kc.num'=>'num',
			   'info.goods_name'=>'goods_name',
			   'kc.position'=>'position',
			   'info.shou_price'=>'shou_price',
			    'info.is_zuhe'=>'is_zuhe',
			    'info.zuhe_id'=>'zuhe_id',   
			))
			->where($map)->select();														 
			return $list; 
		}
		
		//=====================================================进出货页面专用==========================================
		public function getAllkucunfoListByMap_jinchuhuo($map=array())
		{					
			$count=$this->alias('kc')->join('left join wt_goodsinfo info on info.goods_id=kc.goods_id and info.wbid=kc.wbid')->where($map)->count(); 
			$list= $this->alias('kc')->join('left join wt_goodsinfo info on info.goods_id=kc.goods_id and info.wbid=kc.wbid')
			->field(array(
			   'kc.wbid'=>'wbid',
			   'kc.goods_id'=>'goods_id',
			   'kc.num'=>'num',
			   'info.goods_name'=>'goods_name',
			   'info.one_jian_num'=>'one_jian_num',
			   'info.one_jian_jin_price'=>'one_jian_jin_price',
			   'info.one_ge_jin_price'=>'one_ge_jin_price',			
			   'info.unit'=>'unit',
			   'info.guige'=>'guige',			
			   'info.shou_price'=>'shou_price',
			   'info.goods_pinyin'=>'goods_pinyin',
			   'info.goods_quanpin'=>'goods_quanpin',
			   'info.is_zuhe'=>'is_zuhe',
			   'info.zuhe_id'=>'zuhe_id',   
			))
			->where($map)->select();
			
												
			foreach($list as &$val)
			{   	
				$val['type_id']=D('ProductType')->where(array('type_id'=>$val['type_id']))->getField('type_name');	
				$val['allname']=$val['goods_name'].$val['guige'];	
              
			    $val['value']=$val['goods_pinyin'].','.$val['goods_name'].','.$val['goods_quanpin'];
				$val['label']=$val['goods_pinyin'].','.$val['goods_name'].','.$val['goods_quanpin'];
				
				$val['hj_num']=D('Productkc')->where(array('goods_id'=>$val['goods_id'],'wbid'=>$map['info.wbid'],'position'=>1))->getField('num');
				$val['ck_num']=D('Productkc')->where(array('goods_id'=>$val['goods_id'],'wbid'=>$map['info.wbid'],'position'=>0))->getField('num');					
			}
	 
			return $list; 
		}
		
		//================================================进出货页面专用================================================    
  }
