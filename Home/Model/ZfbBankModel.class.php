<?php
    namespace Home\Model;
    use Think\Model;
    class ZfbBankModel extends Model 
    {
        protected $tableName = 'WZfbAddBankInfo';


      // public function updateDistrict($map,$data){
      //     return $this->where($map)->data($data)->save();
      // }

      public function addBankInfo($data){
          return $this->data($data)->add();
      }

      public function getImageIdList($data)
      {
          return $this->where($map)->select();
      }
      
      public function getBankInfo($wb_id){
          
          // return $this->where(array('wb_id'=>$wb_id))->field( 'id','bankcard')->select();
          // $map=array('id','bankcard');

          return $this->where(array('wb_id'=>$wb_id))->getField('id,kh_hang,bankcard');
      }

      public function getBankInfo2($wb_id)
      {
          
          return $this->where(array('wb_id'=>$wb_id))->find();
      }


      
      // public function addFreeRate($GroupGuid){
      //   return $this->data(array(
      //       'GroupGuid' =>  $GroupGuid,
      //       'Guid'  =>  getGuid(),
      //       'name'  =>  '自由计费',
      //       'TimeSize'  =>  0,
      //       'je'    =>  0
      //   ))->add();
      // }
 
  }
