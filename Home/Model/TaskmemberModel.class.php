<?php
namespace Home\Model;
use Think\Model;
class TaskmemberModel extends Model{
	protected $tableName = 'taskmember';
	protected $fields = array(
		'id','taskid','receive','readed','status','_type'=>array(
			'id'	=>	'int',
			'taskid'=>	'int',
			'receive'	=>	'smallint',
			'readed'	=>	'tinyint',
			'status'	=>	'tinyint'
			)
		);

	public function getTaskmemberList(){
		$map['receive'] = session('userid');
		$taskmemberlist = $this->where($map)->select();
		return $taskmemberlist;
	}
	public function getTaskmemberInfo($map){
		return $this->where($map)->find();
	}
	public function getUnreadTask(){
		$userid = session('userid');
		$tasks = $this->where("receive='$userid' AND readed='0'")->getField('taskid',true);
		if(!empty($tasks)){
			$tasks = implode(',',$tasks);
			$tasks = D('Task')->where("id IN ($tasks) AND deleted='0'")->field('id,title,owner,createtime')->select();
			return $tasks;
		}
	}
	public function insertTaskmember($data){
		if($this->create($data)){
			if($this->add()){
				return true;
			}else{
				return true;
			}
		}else{
			return false;
		}
	}

	public function updateTaskmember($map,$data){
		$this->where($map)->save($data);
	}

	public function clearTaskmember($taskid){
		$this->where(array('taskid'=>$taskid))->delete();
	}
	//检查任务状态，如果所有成员的状态为2，修改认为的状态为2
	public function checkTaskStatus($taskid){
		$taskmembers = $this->where(array('taskid'=>$taskid))->select();
		$notDone = false;
		foreach($taskmembers as $value){
			if($value['status'] != 2){
				$notDone = true;
			}
		}
		if(!$notDone){
			D('Task')->updateTask($taskid,array('status'=>2));
		}
	}
}