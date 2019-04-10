<?php
    namespace Home\Model;
    use Think\Model;
    class YuangongMbModel extends Model 
    {
        protected $tableName = 'WMb_UserTable';

       public function getYuangongMoBanList($wbid)    
      {                                                                             
    
        $map['role.WB_ID']=$wbid;
        $list = $this->join('LEFT JOIN  role ON role.boss_qx=WMb_UserTable.bossqx')
          ->field(array(
          'role.role_name'=>'role_name',
          'role.role_id'=>'role_id',
          'role.role_perm'=>'role_perm',
          'role.groupqx'=>'groupqx',
          'role.boss_qx'=>'bossqx',  
          'WMb_UserTable.name'=>'name',
          ))->where($map)->select();
    
        return $list; 
      }
        
  }
