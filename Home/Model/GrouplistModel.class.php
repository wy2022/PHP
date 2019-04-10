<?php
    namespace Home\Model;
    use Think\Model;
    class GrouplistModel extends Model 
    {
        protected $tableName = 'WGroupTable'; 





		


	    

       public function deleteDistrict($GroupGuid)
       {
          return $this->where(array('GroupGuid'=>$GroupGuid))->delete();
       }



  }
