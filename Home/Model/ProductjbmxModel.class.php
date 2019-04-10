<?php
    namespace Home\Model;
    use Think\Model;
    class ProductjbmxModel extends Model 
    {
        protected $tableName = 'wt_goodsjb_mx';
        public function getjbtongji_mx_listByMap_count($map=array())
		{
			$count=$this->alias('jbmx')->join('left join wt_goodsinfo info on info.goods_id=jbmx.goods_id and info.wbid=jbmx.wbid')->where($map)->count(); 	
			
			return $count;
		}

		public function getjbtongji_mx_listByMap($map=array(),$order = '',$page = 1,$rows = 20)
		{
			$count=$this->alias('jbmx')->join('left join wt_goodsinfo info on info.goods_id=jbmx.goods_id and info.wbid=jbmx.wbid')->where($map)->count(); 

			$list= $this->alias('jbmx')->join('left join wt_goodsinfo info on info.goods_id=jbmx.goods_id and info.wbid=jbmx.wbid')
			->field(array(
			   'jbmx.wbid'=>'wbid',
			   'jbmx.goods_id'=>'goods_id',
			   'jbmx.shangjia_num'=>'shangjia_num',
			   'jbmx.xiajia_num'=>'xiajia_num',
			   'info.goods_name'=>'goods_name',
			   'info.unit'=>'unit',
			   'info.guige'=>'guige',
			   'info.shou_price'=>'shou_price',
			   'info.type_id'=>'type_id',
			   'jbmx.post_order_no'=>'post_order_no',
			   'jbmx.position'=>'position',
			   'jbmx.old_hj_num'=>'old_hj_num',
			   'jbmx.now_hj_num'=>'now_hj_num',
			   'jbmx.id'=>'id',
			   'jbmx.dtInsertTime'=>'dtInsertTime',
			   
			   
	   
			))
			->where($map)->page($page,$rows)->order($order)->select();
				
				
				
			foreach($list as &$val)
			{   

	
				$val['type_id']=D('ProductType')->where(array('type_id'=>$val['type_id']))->getField('type_name');	
							
				$val['xiaoshou_num']=$val['old_hj_num']+$val['shangjia_num']- $val['now_hj_num'];
				
				$val['xiaoshou_je']=$val['xiaoshou_num']*$val['shou_price'];
				

				// $val['agent_realname']=D('Agent')->where(array('agent_id'=>$val['agent_id']))->getField('agent_realname');	
				// $val['agent_name']=D('Agent')->where(array('agent_id'=>$val['agent_id']))->getField('agent_name');						
				// $val['dtInsertTime']=date('Y-m-d H:i:s',strtotime($val['dtInsertTime']));								
			}
	 
			return array('count'=>$count,'list'=>$list); 
		}
		
		
		
		
		 public function getjb_fenleitongji_mx_listByMap_count($map=array())
		{
			$count=$this->alias('jbmx')->join('left join wt_goodsinfo info on info.goods_id=jbmx.goods_id and info.wbid=jbmx.wbid')->where($map)->count(); 	
			
			return $count;
		}

		public function getjb_fenleitongji_mx_listByMap($map=array(),$order = '',$page = 1,$rows = 20)
		{
		   
			   $map['wbid']=session('wbid');
			   $map['post_order_no']=session('post_order_no');
			 
			   $list= $this->field(array('type_id'=>'type_id'))->where($map)->group('type_id')->select();
				
				
				
			foreach($list as &$val)
			{   
               $map=array(); 
			   $xiaoshou_je=0;
			   $map['wbid']=session('wbid');
			   $map['post_order_no']=session('post_order_no');
			   $map['type_id']=$val['type_id'];
	
				$val['type_id']=D('ProductType')->where(array('type_id'=>$val['type_id']))->getField('type_name');	
				$val['shangjia_num']=D('Productjbmx')->where($map)->sum('shangjia_num');
				$val['xiajia_num']  =D('Productjbmx')->where($map)->sum('xiajia_num');
				$val['old_hj_num']  =D('Productjbmx')->where($map)->sum('old_hj_num');
				$val['now_hj_num']  =D('Productjbmx')->where($map)->sum('now_hj_num');
				
				$goodslist=D('Productjbmx')->where($map)->select();
				foreach($goodslist as &$val1)
				{
					$one_sp_je=D('Product')->where(array('wbid'=>session('wbid'),'goods_id'=>$val1['goods_id']))->getField('shou_price');
					$one_sp_num= $val1['shangjia_num']+$val1['old_hj_num']-$val1['now_hj_num'];
					$xiaoshou_je+= $one_sp_je*$one_sp_num;
				}
								
				
				$val['xiaoshou_num']=$val['shangjia_num']+$val['old_hj_num']-$val['now_hj_num'];
				$val['xiaoshou_je'] =$xiaoshou_je;												
			}
	 
			return array('count'=>$count,'list'=>$list); 
		}


    
  }
