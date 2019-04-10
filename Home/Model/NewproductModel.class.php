<?php
    namespace Home\Model;
    use Think\Model;
    class  NewproductModel extends Model 
    {
        protected $tableName = 'cs_goodsinfo';
        /*
        public function getProductinfoListByMap_count($map=array())
		{
			$map['deleted']=0;
			$count=$this->where($map)->count(); 			
			return $count;
		}

		public function getProductinfoListByMap($map=array(),$order = '',$page = 1,$rows = 15)
		{   
		    $map['deleted']=0;
			$count=$this->where($map)->count(); 
            $type_list=D('ProductType')->select();
			$list= $this->where($map)->page($page,$rows)->order($order)->select();											
			foreach($list as &$val)
			{
				foreach($type_list as $val2)
				{
					if($val2['type_id']==$val['type_id'])
					{
						$val['type_name']=$val2['type_name'];	
						break;
					}	
				}	
				$val['shou_price']=sprintf("%.2f",$val['shou_price']);
			
				if(!empty($val['goods_image']))
				{
					$filepath= C('UPLOAD_SHANGPIN_DIR').$val['goods_image'];
					if(file_exists($filepath))
					{
					  $val['goods_image']= "<img src=".C('SHANGPIN_TUPIAN_PATH_URL').$val['goods_image']." width='40' height='40'/>";	
					}
					else
					{
						$val['goods_image']= "<img src=".C('SHANGPIN_TUPIAN_PATH_URL').'moren.png'." width='40' height='40'/>";
					}	
				}
				else
                {
					$val['goods_image']= "<img src=".C('SHANGPIN_TUPIAN_PATH_URL').'moren.png'." width='40' height='40'/>";
				}																				
			}
	 
			return array('count'=>$count,'list'=>$list); 
		}
		*/
	

	    public function getProductinfoListByMap_count($map=array())
		{
			$map['sp.deleted']=0;
			$count=$this->alias('sp')->where($map)->count(); 	
			
			return $count;
		}

		public function getProductinfoListByMap($map=array(),$order = '',$page = 1,$rows = 20)
		{   
		    $typelist=D('ProductType')->select();
			$count=$this->alias('sp')->where($map)->count(); 
			$list= $this->alias('sp')->where($map)->page($page,$rows)->order($order)->select();
												
			foreach($list as &$val)
			{
				/*
				$val['type_id']=D('ProductType')->where(array('type_id'=>$val['type_id']))->getField('type_name');	
				$val['hj_num']=D('Productkc')->where(array('goods_id'=>$val['goods_id'],'position'=>1))->getField('num');
				$val['ck_num']=D('Productkc')->where(array('goods_id'=>$val['goods_id'],'position'=>0))->getField('num');
				$val['shou_price']=sprintf("%.2f",$val['shou_price']);
			
				if(!empty($val['goods_image']))
				{
					$filepath= C('UPLOAD_SHANGPIN_DIR').$val['goods_image'];
					if(file_exists($filepath))
					{
					  $val['goods_image']= "<img src=".C('SHANGPIN_TUPIAN_PATH_URL').$val['goods_image']." width='40' height='40'/>";	
					}
					else
					{
						$val['goods_image']= "<img src=".C('SHANGPIN_TUPIAN_PATH_URL').'moren.png'." width='40' height='40'/>";
					}	
				}
				else
                {
					$val['goods_image']= "<img src=".C('SHANGPIN_TUPIAN_PATH_URL').'moren.png'." width='40' height='40'/>";
				}	
				*/	
			
		        if($val['is_zuhe']==0)
	            {
					foreach($typelist as &$val1)
					{
						if($val['type_id']==$val1['type_id'])
						{
							$val['type_name']=$val1['type_name'];
							break;
						}	
					}				
				}else if($val['is_zuhe']==1)
				{
                    foreach($typelist as &$val1)
					{
						if($val['type_id']==$val1['type_id'])
						{
							$val['type_name']=$val1['type_name'];
							break;
						}	
					}
				}
				else if($val['is_zuhe']==2)
	            {			
					$zuhe_id=$val['goods_id'];
					$zuhe_goods_array=array();
					$map=array();					
					$map['is_zuhe']=1;
					$map['zuhe_id']=$zuhe_id;	
	                $map['wbid']=session('wbid');					
					$zuhe_goods_array=D('Newproduct')->alias('sp')
					->join('left join wt_goodstype  as tp on tp.type_id=sp.type_id ')					
					->field(array(
					'sp.goods_id'=>'goods_id',
					'sp.goods_name'=>'goods_name',
					'sp.type_id'=>'type_id',
					'sp.kc_num'=>'hj_num',
					'sp.ck_num'=>'ck_num',
					'sp.zuhe_id'=>'zuhe_id',
					'sp.is_zuhe'=>'is_zuhe',
					'tp.type_name'=>'type_name',
					))->where($map)->select();
														
					foreach($typelist as &$val1)
					{
						if($val['type_id']==$val1['type_id'])
						{
							$val['type_name']=$val1['type_name'];
							break;
						}	
					}
									
					if(!empty($zuhe_goods_array))
					{
						$val['zuhelist']=$zuhe_goods_array;
					}
					else
	                {
						$val['zuhelist']='';
					}																	
				}																				
			}
	 
			return array('count'=>$count,'list'=>$list); 
		}



		public function getProductinfoListByMap2($map=array())
		{   
		    $typelist=D('ProductType')->select();
			$list= $this->where($map)->select();												
			foreach($list as &$val)
			{						            			
				$zuhe_id=$val['goods_id'];
				$zuhe_goods_array=array();
				$map=array();					
				$map['is_zuhe']=1;
				$map['zuhe_id']=$zuhe_id;	
                $map['wbid']=session('wbid');					
				$zuhe_goods_array=D('Newproduct')->where($map)->select();
				foreach($zuhe_goods_array as  &$val3) {
		            $val3['hj_num']=$val3['kc_num'];
		        }					
				if(!empty($zuhe_goods_array))
				{
					$val['zuhelist']=$zuhe_goods_array;
				}
				else
                {
					$val['zuhelist']='';
				}																																									
			}
	 
			return $list ; 
		}
		
		public function getxstongji_mx_listByMap_count($map=array())
		{
			$count=$this->alias('xsmx')->join('left join wt_goodsinfo info on info.goods_id=xsmx.goods_id and info.wbid=xsmx.wbid')->where($map)->count(); 	
			
			return $count;
		}

			
		public function getProductinfoListByMap_count_zongzhang($map=array())
		{
			$map['deleted']=0;
			$count=$this->where($map)->count(); 		
			return $count;
		}
		
		public function getProductinfoListByMap_zongzhang($map=array(),$order = '',$page = 1,$rows = 20,$beg_goods_ck_str)
		{   
		    $map['deleted']=0;
			$wbid=session('wbid');
			$count=$this->where($map)->count(); 
			$list= $this->where($map)->page($page,$rows)->order($order)->select();	

            $map1=array();
            $map1['wbid']=$wbid; 
			if(!empty($map['dtInsertTime']))
			{
				$map1['dtInsertTime']=$map['dtInsertTime']; 
			}	
			
			$kc_changelist=D('Newproductsxjmx')->field('goods_id,shangxia_status,sum(change_num) as change_num')
			->group('goods_id,shangxia_status')->where($map1)->select();
			
			
			$lastshift_goodskc_list=D('Newproductxsmx')->field('goods_id,ordertype,sum(xiaoshou_num) as xiaoshou_num')
			->group('goods_id,ordertype')->where($map1)->select();			
		    											
			foreach($list as &$val)
			{								
				$bFind=false;
				foreach($lastshift_goodskc_list as &$val2)
				{
					if($val['goods_id']==$val2['goods_id'])
					{				
						if($val2['ordertype']==1)
						{
							$xiaoshou_num=$val2['xiaoshou_num'];
						}
						else if($val2['ordertype']==3)
						{
							$tuihuo_num= $val2['xiaoshou_num'];
						} 									
						$bFind=true;						
						break;
					}
				}
				if($bFind==false)
				{
					$xiaoshou_num=0;
				}				
				$val['xiaoshou_num']=$xiaoshou_num;
				
				
				$bFind1=false;
				foreach($kc_changelist as &$val3)
				{
					if($val['goods_id']==$val3['goods_id'])
					{				
						if($val3['shangxia_status']==0)      //库存增加
 						{
							$kc_add_num=$val3['change_num'];
						}
						else if($val3['shangxia_status']==1)  //库存减少
						{
							$kc_dec_num= $val3['change_num'];
						} 									
						$bFind1=true;						
						break;
					}
				}
				if($bFind1==false)
				{
					$kc_add_num=0;
					$kc_dec_num=0;
				}	






				$val['change_num']=$kc_add_num -$kc_dec_num ;																					
			}
	 
			return array('count'=>$count,'list'=>$list); 
		}
		
		
		
		public function getProductkcinfo_ListByMap_count($map=array())
		{
			$map['sp.deleted']=0;
			$count=$this->alias('sp')->join('left join wt_goodskc  as kc on kc.wbid=sp.wbid and kc.goods_id=sp.goods_id')->where($map)->count(); 	
			
			return $count;
		}
				
		public function getProductkcinfo_ListByMap($map=array(),$order = '',$page = 1,$rows = 20)
		{
			$map['sp.deleted']=0; 
			$count=$this->alias('sp')->join('left join wt_goodskc  as kc on kc.wbid=sp.wbid and kc.goods_id=sp.goods_id')->where($map)->count(); 

			$list= $this->alias('sp')->join('left join wt_goodskc  as kc on kc.wbid=sp.wbid and kc.goods_id=sp.goods_id')
			->field(array(
			'sp.goods_id'=>'goods_id',
			'sp.goods_name'=>'goods_name',
			'sp.type_id'=>'type_id',
			'sp.guige'=>'guige',
			'sp.unit'=>'unit',
			'kc.position'=>'position',
			'kc.num'=>'num'
			))
			->where($map)->page($page,$rows)->order($order)->select();
				
				
				
			foreach($list as &$val)
			{
				$val['type_id']=D('ProductType')->where(array('type_id'=>$val['type_id']))->getField('type_name');	
				// $val['agent_realname']=D('Agent')->where(array('agent_id'=>$val['agent_id']))->getField('agent_realname');	
				// $val['agent_name']=D('Agent')->where(array('agent_id'=>$val['agent_id']))->getField('agent_name');						
				// $val['dtInsertTime']=date('Y-m-d H:i:s',strtotime($val['dtInsertTime']));
                $val['allname']=$val['goods_name'].$val['guige'];	
				$val['hj_num']=D('Productkc')->where(array('goods_id'=>$val['goods_id'],'position'=>1))->getField('num');
				$val['ck_num']=D('Productkc')->where(array('goods_id'=>$val['goods_id'],'position'=>0))->getField('num');
				// $val['price']=D('Product')->where(array('wbid'=>session('wbid'),'goods_id'=>$val['goods_id']))->getField('shou_price');
				// $val['sumje']=$val['price']*$val['num'];				
			}
	 
			return array('count'=>$count,'list'=>$list); 
		}
        
		// public function getProductkcinfo_ListByMap2($map=array())
		// {
			// $list= $this->alias('info')->join('left join wt_goodskc  as kc on kc.wbid=sp.wbid and kc.goods_id=sp.goods_id')
			// ->field(array(
			// 'info.goods_id'=>'goods_id',
			// 'info.goods_name'=>'goods_name',
			// 'info.type_id'=>'type_id',
			// 'info.guige'=>'guige',
			// 'info.unit'=>'unit',
			// 'kc.position'=>'position',
			// 'kc.num'=>'num'
			// ))
			// ->where($map)->page($page,$rows)->order($order)->select();
				
				
				

	 
			// return $list; 
		// }
		
		public function getProductinfoListByMap_zongzhang_zuhe_02_count($map=array())
		{
            $map['deleted']=0;
			$count=$this->where($map)->count(); 
			return $count;
		}


		public function getProductinfoListByMap_zongzhang_zuhe_02($map=array(),$order = '',$page = 1,$rows = 20)
		{   
			$typelist=D('ProductType')->select();
		    $map['deleted']=0;
			$wbid=session('wbid');
			$count=$this->where($map)->count(); 
			$list= $this->where($map)->page($page,$rows)->order($order)->select();


			$map1=array();
            $map1['wbid']=$wbid; 
			if(!empty($map['dtInsertTime']))
			{
				$map1['dtInsertTime']=$map['dtInsertTime']; 
			}

			$all_goodsxsmx_list=D('Newproductxsmx')->field('goods_id,ordertype,sum(xiaoshou_num) as xiaoshou_num')
			->group('goods_id,ordertype')->where($map1)->select();	


			foreach($list as &$val)
			{   
	
           		foreach($typelist as $val2)
				{
					if($val2['type_id']==$val['type_id'])
					{
						$val['type_name']=$val2['type_name'];	
						break;
					}	
				}	

				$bFind=false;
				$tuihuo_num=0;
				$xiaoshou_num=0;
				foreach($all_goodsxsmx_list as &$val3)
				{
					if($val['goods_id']==$val3['goods_id'])
					{				
						if($val3['ordertype']==1)
						{
							$xiaoshou_num=$val3['xiaoshou_num'];
						}
						else if($val2['ordertype']==3)
						{
							$tuihuo_num= $val3['xiaoshou_num'];
						} 									
						$bFind=true;						
						break;
					}
				}
				if($bFind==false)
				{
					$xiaoshou_num=0;
				}				
				$val['xiaoshou_num']=$xiaoshou_num-$tuihuo_num;







			}


	

            
/*
			
			$kc_changelist=D('Newproductsxjmx')->field('goods_id,shangxia_status,sum(change_num) as change_num')
			->group('goods_id,shangxia_status')->where($map1)->select();
			
			
		
		    											
			foreach($list as &$val)
			{								
				$bFind=false;
				foreach($lastshift_goodskc_list as &$val2)
				{
					if($val['goods_id']==$val2['goods_id'])
					{				
						if($val2['ordertype']==1)
						{
							$xiaoshou_num=$val2['xiaoshou_num'];
						}
						else if($val2['ordertype']==3)
						{
							$tuihuo_num= $val2['xiaoshou_num'];
						} 									
						$bFind=true;						
						break;
					}
				}
				if($bFind==false)
				{
					$xiaoshou_num=0;
				}				
				$val['xiaoshou_num']=$xiaoshou_num;
				
				
				$bFind1=false;
				foreach($kc_changelist as &$val3)
				{
					if($val['goods_id']==$val3['goods_id'])
					{				
						if($val3['shangxia_status']==0)      //库存增加
 						{
							$kc_add_num=$val3['change_num'];
						}
						else if($val3['shangxia_status']==1)  //库存减少
						{
							$kc_dec_num= $val3['change_num'];
						} 									
						$bFind1=true;						
						break;
					}
				}
				if($bFind1==false)
				{
					$kc_add_num=0;
					$kc_dec_num=0;
				}	

				$val['change_num']=$kc_add_num -$kc_dec_num ;																					
			}
	        */
			return array('count'=>$count,'list'=>$list); 
		}
		
	


		public function getProductinfoListByMap_zongzhang_zuhe_01_count($map=array())
		{
            $map['deleted']=0;
			$count=$this->where($map)->count(); 
			return $count;
		}


		public function getProductinfoListByMap_zongzhang_zuhe_01($map=array(),$order = '',$page = 1,$rows = 20,$map1)
		{   
			$typelist=D('ProductType')->select();
		    $map['deleted']=0;
			$wbid=session('wbid');
			$count=$this->where($map)->count(); 
			$list= $this->where($map)->page($page,$rows)->order($order)->select();


		

			$all_goodsxsmx_list=D('Newproductxsmx')->field('goods_id,ordertype,sum(xiaoshou_num) as xiaoshou_num')
			->group('goods_id,ordertype')->where($map1)->select();	



			$all_goodssxjmx_list=D('Newproductsxjmx')->field('goods_id,shangxia_status,sum(num) as change_num')
			->group('goods_id,shangxia_status')->where($map1)->select();

	
	        $all_goodsjchmx_list=D('Newproductjchmx')->field('goods_id,jch_type,sum(changenum) as change_num')
			->group('goods_id,jch_type')->where($map1)->select();
			
			//writelog(json_encode($map1),'Newproductjchmx');
			//writelog(json_encode($all_goodsjchmx_list),'Newproductjchmx');
	

			foreach($list as &$val)
			{   
	       
           		foreach($typelist as $val2)
				{
					if($val2['type_id']==$val['type_id'])
					{
						$val['type_name']=$val2['type_name'];	
						break;
					}	
				}	

				if($val['is_zuhe']==0)
				{
                    $bFind=false;
					$tuihuo_num=0;
					$xiaoshou_num=0;
					foreach($all_goodsxsmx_list as &$val3)
					{
						if($val['goods_id']==$val3['goods_id'])
						{				
							if($val3['ordertype']==1)
							{
								$xiaoshou_num=$val3['xiaoshou_num'];
							}
							else if($val2['ordertype']==3)
							{
								$tuihuo_num= $val3['xiaoshou_num'];
							} 									
							$bFind=true;						
							break;
						}
					}
					if($bFind==false)
					{
						$xiaoshou_num=0;
					}				
					$val['xiaoshou_num']=$xiaoshou_num-$tuihuo_num;
					
					
					$bFind=false;
					$th_num=0;
					$jh_num=0;
					foreach($all_goodsjchmx_list as &$val4)
					{
						if($val['goods_id']==$val4['goods_id'])
						{				
							if($val4['jch_type']==1)
							{
								$jh_num=$val4['change_num'];
							}
							else if($val4['jch_type']==2)
							{
								$th_num= $val4['change_num'];
							} 									
							$bFind=true;						
							break;
						}
					}
					if($bFind==false)
					{
						$jh_num=0;
						$th_num=0;
						
					}				
					$val['jh_num']=$jh_num;
					$val['th_num']=$th_num;
					
										
					
				}
				else if($val['is_zuhe']==1)
				{
					
					$bFind1=false;
					foreach($all_goodssxjmx_list as &$val4)
					{
						if($val['goods_id']==$val4['goods_id'])
						{				
							if($val4['shangxia_status']==0)      //库存增加
	 						{
								$kc_add_num=$val4['change_num'];
							}
							else if($val4['shangxia_status']==1)  //库存减少
							{
								$kc_dec_num= $val4['change_num'];
							} 									
							$bFind1=true;						
							//break;
						}
					}
					if($bFind1==false)
					{
						$kc_add_num=0;
						$kc_dec_num=0;
					}	

					$val['xiaoshou_num']=$kc_add_num -$kc_dec_num ;	
					
				}			
			}
			return array('count'=>$count,'list'=>$list); 
		}	

    
  }
