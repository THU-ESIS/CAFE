<?php
class UserController extends Controller{
	public  function beforeAction($action){
		if(MenuLoader::isGuest()){
			$this->redirect(array('site/login'));
			Yii::app()->end();
		}
		return parent::beforeAction($action);
	}
	
	public function actionIndex($ncl=''){
		$tpl_data = array();
		$ncl_type = Yii::app()->request->getParam('ncl');
		$tpl_data['ncl_type'] = $ncl;
		
		$attr = 'user_id=:user_id';
		$condtion = array('user_id'=>MenuLoader::getUserId());
		if($ncl_type){
			$attr .= ' AND ncl_name=:ncl_name';
			$condtion['ncl_name'] = $ncl_type;
		}
		
		$userTasks = UserTask::model()->bytime()->findAllByAttributes($condtion);
		Yii::app()->clientScript->registerCoreScript('jquery');
		
		$tpl_data['tasks'] = $userTasks;
		
		$tpl_data['statusList'] = array(
			-1 => 'failed',
			0 => 'created',
			1 => 'running',
			2 => 'finished',
		);
		
		$this->render('index',$tpl_data);
	}
	
	public function actionTask($id=0){
		$user_id = MenuLoader::getUserId();
		$tpl_data = array();
		
		$tpl_data['statusList'] = array(
				-1 => 'failed',
				0 => 'created',
				1 => 'running',
				2 => 'finished',
		);
		
		$userTask = UserTask::model()->findByPk($id);
		
		if($userTask){
			$task = Task::model()->find('task_id=:task_id',array(':task_id'=>$userTask->task_id));
			$taskResult = $task->task_result;
			if($taskResult){
				$arrTaskResult = json_decode($taskResult,true);
				$tpl_data['arrTaskResult'] = $arrTaskResult;
			}
		}
		
		$tpl_data['userTask'] = $userTask;
		Yii::log('USER_TASK_'.$id.' data:'.json_encode($tpl_data),'info');
		Yii::app()->clientScript->registerCoreScript('jquery');
		$this->render('task',$tpl_data);
	}
	
	public function actionAjaxGetStatus(){
		$task_idstr = Yii::app()->request->getParam('tasks');
		$user_id = MenuLoader::getUserId();
		
		$ids = explode(',', $task_idstr);
		foreach($ids as $k=>&$v){
			$v = '"'.trim($v).'"';
		}
		$idstr = implode(',', $ids);

		$my_tasks = array();
		$tasks_proccess = array();
		if($idstr){
			//Yii::log('task ids:'.$idstr, 'info');
			$criteria = new CDbCriteria(); 
			$criteria->condition = 'task_id IN ('.$idstr.') AND user_id=:user_id';
			$criteria->params = array(':user_id'=>$user_id);
			$my_tasks = UserTask::model()->findAll($criteria);
			
			$base_url=TSInterface::get('G3');
			foreach ($my_tasks as &$task){
				//Yii::log('task id:'.$task->id.'  status:'.$task->status, 'info');
				if($task->status !=0 && $task->status !=1){
					continue;
				}
				$tasks_proccess[$task->id] = array(
					'total'=>0,
					'failed'=>0,
					'running'=>0,
					'finished'=>0,
				);
				$url = $base_url.'?taskId='.$task->task_id;
				$content = NetHelper::curl_file_get_contents($url);
				Yii::log('TASK_'.$task->task_id.' URL:'.$url.' return:'.$content,'info');
				$data = json_decode($content,true);
				if($data && $data['success']){
					$all_status = 'finished';
					if(empty($data['data'])){
						$all_status = 'running';
					}
					foreach ($data['data'] as $item){
						$tasks_proccess[$task->id]['total']++;
						if($item['status'] == 'failed'){
							$all_status = 'failed';
							$tasks_proccess[$task->id]['failed']++;
							break;
						}
						if($item['status'] == 'running'){
							$all_status = 'running';
							$tasks_proccess[$task->id]['running']++;
							break;
						}
						if($item['status'] == 'finished'){
							$tasks_proccess[$task->id]['finished']++;
							//break;
						}
					}
					if($all_status == 'running'){
						$task->status = 1;
					}
					if($all_status == 'failed'){
						$task->status = -1;
					}
					if($all_status == 'finished'){
						$task->status = 2;
					}
					$task->save();
					
					$updateArr = array('status'=>$task->status);
					if($task->status == 2 || $task->status == -1){
						$updateArr['task_result']=$content;
						$updateArr['finish_time']=time();
					}
					Yii::log('UPDATE TASK_'.$task->id.' update_info:'.json_encode($updateArr),'info');
					Task::model()->updateAll($updateArr,'task_id=:task_id',array(':task_id'=>$task->task_id));
					
				}
			}
		}
		
		$retArr = array();
		foreach ($my_tasks as $item){
			//Yii::log('export task id:'.$item->id.'  status:'.$item->status, 'info');
			$retArr[$item->id] = $item->status;
		}
		echo json_encode($retArr);
	}
}
