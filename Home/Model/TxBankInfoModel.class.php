<?php
    namespace Home\Model;
    use Think\Model;
    class TxBankInfoModel extends Model 
    {
        protected $tableName = 'WbTxBankInfo';
     

      public function addBankInfo($wbid,$data)
      {
          /*
		  $bExist=$this->where(array('wbid'=>$wbid))->find();
          if($bExist)
           {
                          
           }
           else
           {
              
              return $this->data($data)->add();
           } 
		   */
		   return $this->data($data)->add();
          
      }

      public function getImageIdList($data)
      {
          return $this->where($map)->select();
      }
      
      public function getBankInfo($wbid){
          // return $this->where(array('wbid'=>$wbid))->getField('id,kh_hang,bankcardno');
          return $this->where(array('wbid'=>$wbid))->find();
      }

      public function getBankInfoById($wbid)
      {
        
        return  $this->alias('a')->join('LEFT JOIN wb_info b ON b.WBID=a.wbid')->field(array(
          'b.wbname'      =>'wbname',
          'a.wbid'        =>'wbid',
          'a.farenname'   =>'farenname',
          'a.timepost'   =>'timepost',
          'a.phonenum'    =>'phonenum',
          'a.isValid'=>'isValid'))->where(array('a.wbid'=>$wbid))->find(); 
      }

      public function setBankInfoById($wbid,$data)
      {      
        return  $this->where(array('wbid'=>$wbid))->save($data); 
      }

      public function getBankExistInfo($wbid)
      {
          
          return $this->where(array('wbid'=>$wbid))->find();
      }

      public function getUnShenheCount()
      {      
        return $this->where(array('isValid'=>2))->count();

      }


     
      public function getBankInfoList($map=array(),$page,$rows)
      {          
        $count=$this->alias('a')->where($map)->count(); 
        $dai_count=$this->getUnShenheCount();

        $list = $this->alias('a')->join('LEFT JOIN wb_info b ON b.WBID=a.wbid')->field(array(
          'b.wbname'      =>'wbname',
          'a.wbid'        =>'wbid',
          'a.farenname'   =>'farenname',
          'a.shenfenzheng'=>'shenfenzheng',
          'a.phonenum'    =>'phonenum',
          'a.verifycode'  =>'verifycode',
          'a.kh_name'     =>'kh_name',
          'a.kh_hang'     =>'kh_hang',
          'a.kh_zhihang'  =>'kh_zhihang',
          'a.s_province'  =>'s_province',
          'a.s_city'      =>'s_city',
          'a.s_county'    =>'s_county',
          'a.bankcardno'  =>'bankcardno', 
          'a.image_id1'   =>'image_id1',
          'a.image_id2'         =>'image_id2',
          'a.image_id3'         =>'image_id3',
          'a.shenfenzheng_image'=>'shenfenzheng_image', 
          'a.zhizhao_image'     =>'zhizhao_image',
          'a.shouquanshu_image' =>'shouquanshu_image',
          'a.timepost'   =>'timepost',
          'a.isValid'=>'isValid',

          'a.edit'=>'edit'
         
          ))->where($map)->page($page,$rows)->select();

        return array('count'=>$count,'dai_count'=>$dai_count,'list'=>$list); 
      }

  }


