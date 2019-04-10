<?php
    namespace Home\Model;
    use Think\Model;
    class YuangongModel extends Model 
    {
        protected $tableName = 'WUserTable';  
        public function getYgListByMap($map=array())
    		{
    		   $list  = $this->where($map)->select();
               foreach($list as &$val)
               {
                  $val['group_name']=D('Role')->where(array('WB_ID'=>session('wbid'),'role_id'=>$val['role_id']))->getField('role_name');
               } 
     
               return $list;
    		}

        

        public function updateOneYuangongByGuid($Guid,$data=array())
        {
           
            return    $this->where(array('WB_ID'=>session('wbid'),'Guid'=>$Guid))->data($data)->save();       
        }  


         
        public function updateOneYuangongByRoleid($map,$data=array())
        {
           return  $this->where($map)->data($data)->save(); 
        }
    }
