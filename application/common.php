<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
//加载请求
require_once 'communication.php';


use app\model\AdminCount;
use app\model\AdminList;
use app\model\AdminGroup;
use app\model\AdminApp;
use app\model\AdminAppGroup;
/**
 *获取接口链接地址
*/
function getUrl($param, $type = 'get') {
	
	$url = isset($param['API_CONF_DETAIL']) ? $param['API_CONF_DETAIL']['group_apiUrl'] : 'https://apiadmin.fmoons.com/api';
	if (isset($param['APP_CONF_DETAIL'])) {
		unset($param['APP_CONF_DETAIL']);
	}
	if (isset($param['API_CONF_DETAIL'])) {
		unset($param['API_CONF_DETAIL']);
	}
	
	$do = isset($param['do']) ? $param['do'] : 'None' ;
	if ($type == 'post') {
		
		$da['apiurl'] = $url.'&do='.$do;
		if (isset($param['do'])) {
			unset($param['do']);
		}
		$da['post'] = $param;
		
		return $da;
	}else{
		$param = http_build_query($param);
		$apiurl = $url.'&'.$param;
		return $apiurl;
	}
}

/**
 * 
 * 接口请求量统计
 *data:请求数据
 */
function countHits($data,$type='get',$code=1) {
	if ($type=='post') {
		$da = $data['os'];
	}else{
		$da = json_decode($data['os'],true);
	}
	
	$hash = $data['API_CONF_DETAIL']['hash'];
	$group_hash = $data['API_CONF_DETAIL']['group_hash'];
	$app_id = $data['APP_CONF_DETAIL']['app_id'];
	$app_group = $data['APP_CONF_DETAIL']['app_group'];
	
	$AdminList = new AdminList();
	
	$da['uid'] = $AdminList->where('hash', $hash)->value('uid');
	$da['app_name'] = $data['APP_CONF_DETAIL']['app_name'];
	$da['api_class'] = $data['API_CONF_DETAIL']['api_class'];
	$da['hash'] = $hash;
	$da['group_hash'] = $group_hash;
	$da['app_id'] = $app_id;
	$da['app_group'] = $app_group;
	$da['create_time'] = time();
	$da['create_ip'] = !empty($da['create_ip']) ? $da['create_ip']: getip();
	$da['code'] = $code;
	$da['type'] = $type;
	$res = AdminCount::create($da);
	if ($res === false) {		
	    return false;
	}
	$res = $AdminList->where('hash', $hash)->setInc('hits');
	if ($res === false) {
	    return false;
	}
	
	$AdminGroup = new AdminGroup();
	$res = $AdminGroup->where('hash', $group_hash)->setInc('hits');
	if ($res === false) {
	    return false;
	}
	$AdminApp = new AdminApp();
	$res = $AdminApp->where('app_id', $app_id)->setInc('hits');
	if ($res === false) {
	    return false;
	}
	$AdminAppGroup = new AdminAppGroup();
	$res = $AdminAppGroup->where('hash', $app_group)->setInc('hits');
	if ($res === false) {
	    return false;
	}
	
	
	return true;	
}


function getip() {
	static $ip = '';
	if (isset($_SERVER['REMOTE_ADDR'])) {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	if (isset($_SERVER['HTTP_CDN_SRC_IP'])) {
		$ip = $_SERVER['HTTP_CDN_SRC_IP'];
	} elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
		foreach ($matches[0] as $xip) {
			if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
				$ip = $xip;
				break;
			}
		}
	}
	if (preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $ip)) {
		return $ip;
	} else {
		return '127.0.0.1';
	}
}

function getInfo($name, $hash) {
	switch($name) {
		case 'AdminAppGroup':
			$obj = new AdminAppGroup();
		break;
		case 'AdminGroup':
			$obj = new AdminGroup();
		break;
		case 'AdminList':
			$obj = new AdminList();
		break;
		case 'AdminApp':
			$obj = new AdminApp();
		break;
		
		default:
		break;
	}
	if ($name == 'AdminApp') {
		$obj = $obj->where('app_id', $hash);
	}else{
		$obj = $obj->where('hash', $hash);
	}	
	$listObj = $obj->find();	
	return $listObj;
}

function getSiteroot() {
	$http = explode('//', $_SERVER['HTTP_REFERER']);
	$url = explode('/', $http[1]);
	$url = $http[0].'//'.$url[0].'/';
	
	return $url;
}











function iserializer($value) {
	return serialize($value);
}


function iunserializer($value) {
	if (empty($value)) {
		return array();
	}
	if (!is_serialized($value)) {
		return $value;
	}
	if(version_compare(PHP_VERSION, '7.0.0', '>=')){
		$result = unserialize($value, array('allowed_classes' => false));
	}else{
		if(preg_match('/[oc]:[^:]*\d+:/i', $seried)){
			return array();
		}
		$result = unserialize($value);
	}
	if ($result === false) {
		$temp = preg_replace_callback('!s:(\d+):"(.*?)";!s', function ($matchs){
			return 's:'.strlen($matchs[2]).':"'.$matchs[2].'";';
		}, $value);
		return unserialize($temp);
	} else {
		return $result;
	}
}