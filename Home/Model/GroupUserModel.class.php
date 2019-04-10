<?php
    namespace Home\Model;
    use Think\Model;
    class GroupUserModel extends Model 
    {
        protected $tableName = 'WGroupUserTable';  
        public function getGroupUserListByMap($map=array())
		{
		   $list  = $this->where($map)->select();

		   foreach($list  as &$val)
		   {
               $val['usernum_ght']=D('Yuangong')->where(array('WB_ID'=>session('wbid'),'group_id'=>$val['id']))->count();
		   }	


		   return $list;
		}


		 public function updateGroupUserByid($id,$data=array())
		{
		    if($this->create($data))
		    {
				if($this->where(array('id'=>$id))->save() !== false)
				{
					return true;
				}
				else
				{
					return null;
				}
			}
			else
			{
				return $this->getError();
			}
		}	



    }
