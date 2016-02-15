<?php
class NetHelper {
	public static function curl_file_get_contents($durl) {
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $durl );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true ); // 获取数据返回
		curl_setopt ( $ch, CURLOPT_BINARYTRANSFER, true ); // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回
		$r = curl_exec ( $ch );
		curl_close ( $ch );
		return $r;
	}
	public static function curl_file_post_contents($durl,$post_data) {
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $durl );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true ); // 获取数据返回
		curl_setopt ( $ch, CURLOPT_BINARYTRANSFER, true ); // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回
		curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt ($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($post_data))
		);
		$r = curl_exec ( $ch );
		
		$info = curl_getinfo($ch);
		
		curl_close ( $ch );
		return $r;
	}
	public static function access_url($url) {
		if ($url=='') return false;
		$fp = fopen($url, 'r') or exit('Open url faild!');
		if($fp){
			while(!feof($fp)) {
				$file.=fgets($fp)."";
			}
			fclose($fp);
		}
		return $file;
	}
}