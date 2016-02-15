<?php
define('TSInterfaceROOT','http://166.111.7.72:8088/datamanager-worker/');
class TSInterface {
	
	public static function get($name){
		$data = array(
			'G1' => '/api/v1/modelfile/query/filter',
			'G2' => '/api/v1/task/submit',
			'G3' => '/api/v1/task/query',
			'G6' => '/api/v1/modelfile/query',
		);
		if(isset($data[$name])){
			return TSInterfaceROOT. $data[$name];
		}
		else{
			return '';
		}
	}
}

?>
