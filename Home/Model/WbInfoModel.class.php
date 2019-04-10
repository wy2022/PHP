<?php
    namespace Home\Model;
    use Think\Model;
    class WbInfoModel extends Model 
    {
        protected $tableName = 'WB_Info';

       public function getOneWbInfoByid($wbid) //获取新闻
       {
          $wbinfo=$this->where(array('WBID'=>$wbid))->find();

	      $wbinfo['LastDateTime'] = date('Y-m-d H:i:s',strtotime($wbinfo['LastDateTime'])); 
	    if(empty($wbinfo['WbName']))
		{
			$wbinfo['WbName']='未知';
		}
		if(empty($wbinfo['address']))
		{
			$wbinfo['address']='未知';
		}
		if(empty($wbinfo['WBManager']))
		{
			$wbinfo['WBManager']='未知';
		}
		if(empty($wbinfo['WBTel']))
		{
			$wbinfo['WBTel']='未知';
		}
		if(empty($wbinfo['CpCount']))
		{
			$wbinfo['CpCount']='未知';
		}
		if(empty($wbinfo['LastDateTime']))
		{
			$wbinfo['LastDateTime']='未知';
		}
		if(empty($wbinfo['VerNo']))
		{
			$wbinfo['VerNo']='未知';
		}
		if(empty($wbinfo['Email']))
		{
			$wbinfo['Email']='未知';
		}
		if(empty($wbinfo['Card']))
		{
			$wbinfo['Card']='未知';
		}

		  return $wbinfo;
       }  

      public function SetGzhValid($wbid,$data) //获取新闻
       {
          return $this->where(array('WBID'=>$wbid))->data($data)->save();
       }  
	   
	public function chkPass($username,$password)
	{
		$user = $this->where(array('WbAccount'=>$username,'deleted'=>0))->find(); //首先查询用户名是否存在
		 
		if(!empty($user) && $user['PassWord'] == md5($password.'hc')) //存在则判断密码是否正确
		{
			return $user;//用户名正确则返回userid,roleid,realname这三个字段
		}
		else
		{   
			//echo "登陆失败";
			return false;
		}
	}


    public function updateOneBarInfo($data)
    {
      if($this->create($data)){
        if($this->save() !== false){
          return true;
        }else{
          return false;
        }
      }else{
        return $this->getError();
      }
    }



    public function InsertOneBar($data)
    {
      $data['PassWord'] = md5($data['PassWord'].'hc');
      if($this->create($data))
      {
        if($this->add())
        {
          return true;
        }
        else
        {
          return false;
        }
     }
     else
     {
      return $this->getError();
    }
  }

  }
