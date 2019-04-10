<?php
	namespace Home\Model;
    use Think\Model;
    class SptjviewModel extends Model 
    {
        protected $tableName = 'View_Goods_luishui';
        public function getallxstongji_mx_listByMap_count($map=array())
		{      	
			$list  = $this->where($map)->select();
			$count=count($list );
				
			return $count;
		}
		
	
		public function getallxstongji_mx_listByMap($map=array(),$order = '',$page = 1,$rows = 20)
		{      	
			$list  = $this->field('goods_id,sum(sumnum) as xiaoshounum,sum(sjsl) as sjsl')->group('goods_id')->where($map)->select();
			
			$count=count($list );
			
			 $all_goods_list=D('Newproduct')->field('goods_id,goods_name,kc_num')->where(array('wbid'=>$map['wbid']))->select();	

			foreach($list as &$val)
		    {   
			
			    foreach($all_goods_list as &$val2)
				{
					if($val['goods_id']==$val2['goods_id'])
					{
						$val['goods_name']=$val2['goods_name']; 
						$val['kc_num']=$val2['kc_num'];
						break;
				   }
					
				}
									
		    }
			
			
				
			return array('list'=>$list,'count'=>$count);
		}
    }
