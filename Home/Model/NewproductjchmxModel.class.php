<?php
    namespace Home\Model;
    use Think\Model;
    class NewproductjchmxModel extends Model 
    {
        protected $tableName = 'cs_goodsjch_mx';
		public function getsxjinfoListByMap_count($map=array())
		{
			$count=$this->where($map)->count(); 	
			
			return $count;
		}

		public function getsxjinfoListByMap($map=array(),$order = '',$page = 1,$rows = 30)
		{
			$count=$this->where($map)->count(); 

			$list= $this->where($map)->page($page,$rows)->order($order)->select();
							
			foreach($list as &$val)
			{   
				// $val['type_id']=D('ProductType')->where(array('type_id'=>$val['type_id']))->getField('type_name');	
				
				 //$val['agent_realname']=D('Agent')->where(array('agent_id'=>$val['agent_id']))->getField('agent_realname');
                // if($val['shangxia_status']==0)
                // {
					// $val['shangxia_type']='上架';
				// }
				// else if($val['shangxia_status']==1)
                // {
				  // $val['shangxia_type']='下架';	
				// } 	
                // $val['shangxia_type']='上架';				
			    						
				 $val['dtInsertTime']=date('Y-m-d H:i:s',strtotime($val['dtInsertTime']));								
			}
	 
			return array('count'=>$count,'list'=>$list); 
		}
		
		
		public function getjhtongji_mx_listByMap_count($map=array())
		{
			$count=$this->alias('jhmx')->join('left join cs_goodsinfo info on info.goods_id=jhmx.goods_id and info.wbid=jhmx.wbid')->where($map)->count(); 	
			
			return $count;
		}

		public function getjhtongji_mx_listByMap($map=array(),$order = '',$page = 1,$rows = 20)
		{
			$count=$this->alias('jhmx')->join('left join cs_goodsinfo info on info.goods_id=jhmx.goods_id and info.wbid=jhmx.wbid')->where($map)->count(); 

			$list= $this->alias('jhmx')->join('left join cs_goodsinfo info on info.goods_id=jhmx.goods_id and info.wbid=jhmx.wbid')
			->field(array(
			   'jhmx.wbid'=>'wbid',
			   'jhmx.goods_id'=>'goods_id',
			   'jhmx.post_order_no'=>'post_order_no',		  
			   'jhmx.changenum'=>'sumnum',
			   'jhmx.sumje'=>'je',
			   'info.goods_name'=>'goods_name',
			  // 'info.unit'=>'unit',
			   //'info.guige'=>'guige',
			   'info.type_id'=>'type_id',
			   'jhmx.jch_type'=>'position',
			   'jhmx.old_hj_num'=>'hj_num',
			   'jhmx.old_ck_num'=>'ck_num',
			   'jhmx.dtInsertTime'=>'dtInsertTime',
	   
			))
			->where($map)->page($page,$rows)->order($order)->select();
				
			
				
			foreach($list as &$val)
			{   
				$val['type_id']=D('ProductType')->where(array('type_id'=>$val['type_id']))->getField('type_name');					
				// $val['agent_realname']=D('Agent')->where(array('agent_id'=>$val['agent_id']))->getField('agent_realname');	
				// $val['agent_name']=D('Agent')->where(array('agent_id'=>$val['agent_id']))->getField('agent_name');						
				$val['dtInsertTime']=date('Y-m-d H:i:s',strtotime($val['dtInsertTime']));								
			}
	 
			return array('count'=>$count,'list'=>$list); 
		}
		  
  }
