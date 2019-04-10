<?php
class DBSQL{
	protected $conn = "";
	public function __construct($svrname,$port,$usrname,$pass,$db)
	{
		$_dsn="sqlsrv:Server=".$svrname.",".$port.";Database=".$db;
		$_opts=array(
			         PDO::ATTR_EMULATE_PREPARES=>false,
			         PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES utf8',
			         PDO::ATTR_DEFAULT_FETCH_MODE =>PDO::FETCH_ASSOC
			        );	
		try
		{
			$this->conn=new PDO($_dsn,$usrname,$pass,$_opts);
			$this->conn ->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//设置以异常的形式报错
		}
		catch(PDOException $e)
		{
			echo "DB Error!";
			exit;
		}
	}

	public function safesql($str='')
	{
		if(!is_numeric($str)&&!empty($str))
		{
			$str=$this->conn->quote($str);
		}
		return $str;
	}

	public function select($sql = "",$data=''){	
		try{
			$stmt=$this->conn->prepare($sql);
			if($data) $ret=$stmt->execute($data);
			else $ret=$stmt->execute();
			if($ret){
				return $stmt->fetchAll(PDO::FETCH_ASSOC);
			}else return false;
		}catch(PDOException $e){
			return false;
		}
	}


	public function startTrans()
	{
       $this->conn->beginTransaction();
	}

	public function commit()
	{
       $this->conn->commit();
	}

	public function rollback()
	{
       $this->conn->rollback();
	}

 

	public function getOne($sql = "",$data='')
	{	
		try{
			$stmt=$this->conn->prepare($sql);
			if($data)
			{
				$ret=$stmt->execute($data);
			} 				
			else 
			{
				$ret=$stmt->execute();
			}	
				
			if($ret)
			{
				return $stmt->fetch(PDO::FETCH_ASSOC);
			}
			else
			{
				return false;
			}			 
		}
		catch(PDOException $e)
		{
			return false;
		}
	}


	 // function getOne($sql, $parm = array())	
  //   {
	 //        $stmt = $this->conn->prepare($sql);//生成一个PDOStatement实例  
	 //        foreach($parm as $k=>$v)
	 //        {
  //              $stmt->bindParam($k, $v, PDO::PARAM_STR); 
	 //        } 
         
		// 	$stmt->execute(); //正式执行。
		// 	$res = $stmt->fetch(PDO::FETCH_ASSOC);
	 //        return $res;
  //   } 

	public function insert($sql = "",$data="",$last=true){
		try{
			$stmt=$this->conn->prepare($sql);
			if($data) $stmt->execute($data);
			else $stmt->execute();
			if($last) return $this->conn->lastInsertId();
			else return $stmt->rowCount();
		}
		catch(PDOException $e)
		{
			
			return false;
		}
	}

	public function getInsertId(){
		return $this->conn->lastInsertId();
	}

	public function update($sql = "",$data=""){
		try{
			$stmt=$this->conn->prepare($sql);
			if($data) $ret=$stmt->execute($data);
			else $ret=$stmt->execute();
			return $stmt->rowCount();
		}catch(PDOException $e){
			return false;
		}
	}

	public function delete($sql = "",$data=''){
		try{
			$stmt=$this->conn->prepare($sql);
			if($data) $ret=$stmt->execute($data);
			else $ret=$stmt->execute();
			return $stmt->rowCount();
		}catch(PDOException $e){
			return false;
		}
	}

	public function getInfo($db,$name,$value,$field="*")
	{
		$sql = "SELECT ".$field." FROM " . $db . " WHERE ".$name." =:".$name;
		$data=array(":".$name=>$value);
		return $this->getOne($sql,$data);
	}

	public function insertData($name,$data,$last=true)
	{
		$field = implode(',',array_keys($data));
		$i = 0;
		$d=$k=$v=array();
		foreach($data as $key => $val)
		{
			$v[]=":".$key;
			$d[":".$key]=$val;
		}
		$sql = "INSERT INTO " . $name . "(" . $field . ") VALUES(" . implode(',',$v) . ")";
		return $this->insert($sql,$d,$last);
	}

	public function updateData($db,$name,$val,$data){	 
		$col=array();
		$pool=array(":".$name=>$val);
		foreach ($data as $k => $v)
		{
			$pool[":".$k]=$v;
			$col[] = $k . "=:".$k;
		}
		$sql = "UPDATE " . $db . " SET " . implode(',',$col) . " WHERE ".$name." =:".$name;
		return $this->update($sql,$pool);
	}

	public function delData($dbTable,$name,$val)
	{
		$sql = "DELETE FROM " . $dbTable . " WHERE ".$name." =:".$name;
		$data=array(":".$name=>$val);
		return $this->delete($sql,$data);
	}

	public function close(){
		$this->conn=null;
	}

	public function __distruct(){
		$this->close();
	}
}
?>