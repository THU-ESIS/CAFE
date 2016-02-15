<?php
class SearchController extends Controller{
	public function actionForm(){
		$formSet = $this->_getFormInfo();
		$formInfo = array();
		$temporalRange = array();
		if($formSet && $formSet['success']){
			foreach ($formSet['data'] as $field=>$items){
				$formInfo[ucfirst($field)]=$items;
				if(strtolower($field) == 'temporalrange'){
					$temporalRange = array();
					$temporalRange['start_year'] = substr($items['start'],0,4);
					$temporalRange['start_month'] = substr($items['start'],3);
					$temporalRange['end_year'] = substr($items['end'],0,4);
					$temporalRange['end_month'] = substr($items['end'],3);
				}
			}
		}
		$arrTpl = array('formItems'=>$formInfo);
		$arrTpl['temporalRange'] = $temporalRange;
		$arrTpl['form_radio'] = array('experiment','frequency','modelingrealm','variablename');
		$arrTpl['form_checkbox'] = array('institute','model','ensemblemember');
		
		$nlcTypes = EnumData::NCL_Types();
		$arrTpl['nlcTypes'] = $nlcTypes;
		Yii::app()->clientScript->registerCoreScript('jquery');
		
		$this->render('form',$arrTpl);
	}
	
	private function _getFormInfo(){
		$url=TSInterface::get('G1');
		$content = NetHelper::curl_file_get_contents($url);
		//echo $content;
		if($content){
			return json_decode($content,true);
		}
		else{
			return array();
		}
	}
	
	public function actionAjaxSearchList(){
		$filters = Yii::app()->request->getPost('filters');
		
		$page = Yii::app()->request->getPost('page');
		$page = intval($page);
		
		$pageSize = 10;
		
		$arrTpl = array();
		$url=TSInterface::get('G6');
		$url = $url . '?page='.$page.'&pageSize='.$pageSize.'&' . $filters;
		$arrTpl['url'] = $url;
		$content = NetHelper::curl_file_get_contents($url);
		$arrTpl['debugContent'] = $content;
		$data = json_decode($content,true);
		$count = $data['data']['modelList']['rowCount'];
		
		$rows = $data['data']['modelList']['list'];
		
		$pageCount = intval($count/$pageSize);
		if($count%$pageSize >0){
			$pageCount ++;
		}
		
		$arrTpl['page'] = $page;
		$arrTpl['count'] = $count;
		$arrTpl['pageCount'] = $pageCount;
		
		$arrTpl['data'] = $rows;
		
		$this->renderPartial('searchlist',$arrTpl);
	}
	
	public function actionAjaxSubmit(){
		if(MenuLoader::isGuest()){
			echo json_encode(array(
				'success'=>false,
				'msg' =>"You can't submit search task without login.",
			));
			Yii::app()->end();
		}
		
		$url=TSInterface::get('G2');
		$models = Yii::app()->request->getPost('items');
		foreach ($models as $key=>&$value){
			$value = json_decode($value);
		}
		$nclScript = array(
			'name' => Yii::app()->request->getPost('name'),
			'temporalStart' => Yii::app()->request->getPost('start'),
			'temporalEnd' => Yii::app()->request->getPost('end'),
			'latMin' => Yii::app()->request->getPost('latMin'),
			'latMax' => Yii::app()->request->getPost('latMax'),
			'lonMin' => Yii::app()->request->getPost('lonMin'),
			'lonMax' => Yii::app()->request->getPost('lonMax'),
		);
		$data = array(
			'models'=>$models,
			'nclScript' => $nclScript,
		);
		$content = NetHelper::curl_file_post_contents($url, json_encode($data));
		Yii::log($content,CLogger::LEVEL_INFO);
		
		$ret = json_decode($content,true);
		
		$task_id = 0;
		if($ret && $ret['success']){
			$task_id = $ret['data'];
		}
		else{
			echo json_encode(array(
					'success'=>false,
					'msg' =>"Error when sending data.".$content,
			));
			Yii::app()->end();
		}
		
		$task = new Task();
		$task_record_id = 0;
		//$find = $task->find('task_params=:task_params AND status in (0,1,2)',array(':task_params'=>json_encode($data)));
		//if($find){
		//	$task_record_id = $find->id;
		//}
		//else{
			$task->task_id = $task_id;
			$task->task_params = json_encode($data);
			$task->task_result = '';
			$task->create_time = time();
			$task->finish_time = 0;
			$task->save();
			$task_record_id = $task->id;
		//}
		
		$userTask = new UserTask();
		$userTask->user_id = MenuLoader::getUserId();
		$userTask->task_id = $task_id;
		$userTask->create_time = time();
		$userTask->task_name = Yii::app()->request->getPost('taskName');
		$userTask->ncl_name = $nclScript['name'];
		$userTask->params = json_encode($data);
		$userTask->save(false);
		
		echo json_encode(array(
					'success'=>true,
					'msg' =>"Task created.",
			));
	}
}