<?php
    namespace Home\Model;
    use Think\Model;
    class ProductModel extends Model 
    {
        protected $tableName = 'wt_goodsinfo';
        public function getProductinfoListByMap_count($map=array())
		{
			$map['sp.deleted']=0;
			$count=$this->alias('sp')->where($map)->count(); 	
			
			return $count;
		}

		public function getProductinfoListByMap($map=array(),$order = '',$page = 1,$rows = 20)
		{   
		    $map['sp.deleted']=0;
			$count=$this->alias('sp')->where($map)->count(); 

			$list= $this->alias('sp')->where($map)->page($page,$rows)->order($order)->select();
				
				
				
			foreach($list as &$val)
			{
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
							
				// $val['agent_name']=D('Agent')->where(array('agent_id'=>$val['agent_id']))->getField('agent_name');						
				// $val['dtInsertTime']=date('Y-m-d H:i:s',strtotime($val['dtInsertTime']));								
			}
	 
			return array('count'=>$count,'list'=>$list); 
		}
		
		
		
		public function getProductinfoListByMap_count_zongzhang($map=array())
		{
			$map['deleted']=0;
			$count=$this->where($map)->count(); 		
			return $count;
		}
		
		public function getProductinfoListByMap_zongzhang($map=array(),$order = '',$page = 1,$rows = 20)
		{   
		    $map['deleted']=0;
			$map['is_zuhe']=array('neq',2);
			$wbid=session('wbid');
			$count=$this->where($map)->count(); 
			$list= $this->where($map)->page($page,$rows)->order($order)->select();	

            $map1=array();
            $map1['wbid']=session('wbid'); 
			if(!empty($map['dtInsertTime']))
			{
				$map1['dtInsertTime']=$map['dtInsertTime']; 
			}	
			
			$type_list=D('ProductType')->select();
			
			
			foreach($list as &$val)
			{
				$map1['goods_id']=$val['goods_id']; 
				foreach($type_list as &$val1)
				{
					if($val1['type_id']==$val['type_id'])
					{
						$val['type_id']=$val1['type_name'];
						break;
					}	
				}				
				$val['jinhuo_num']=D('Productjchmx')->where(array('wbid'=>$wbid,'goods_id'=>$val['goods_id']))->sum('sumnum');
				$val['xiaoshou_num']=D('Productxsmx')->where($map1)->sum('xiaoshou_num');
			
                if(empty($val['xiaoshou_num']))
				{
					$val['xiaoshou_num']=0; 
				}
                if(empty($val['jinhuo_num']))
				{
					$val['jinhuo_num']=0; 
				}					
				
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

    
  }
