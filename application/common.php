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
use app\model\AdminAppWeb;
use app\model\AdminUser;
use app\model\AdminHome;

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
	$app_web = $data['APP_CONF_DETAIL']['app_web'];
	$app_name = $data['APP_CONF_DETAIL']['app_name'];
	
	$AdminList = new AdminList();
	
	
	$uid =  $AdminList->where('hash', $hash)->value('uid');
	$da['uid'] = $uid;
	$da['api_class'] = $data['API_CONF_DETAIL']['api_class'];
	$da['hash'] = $hash;
	$da['group_hash'] = $group_hash;
	$da['from_url'] = getSiteroot();
	$da['app_id'] = $app_id;
	$da['app_group'] = $app_group;
	$da['app_web'] = $app_web;
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
	
	$AdminAppWeb = new AdminAppWeb();
	$res = $AdminAppWeb->where('hash', $app_web)->setInc('hits');
	if ($res === false) {
	    return false;
	}
	
	$AdminUser = new AdminUser();
	$res = $AdminUser->where('id', $uid)->setInc('hits');
	if ($res === false) {
	    return false;
	}
	$AdminHome = new AdminHome();
	$res = $AdminHome->where('id', 1)->setInc('hits');
	if ($res === false) {
	    return false;
	}
	
	return true;	
}

/**
 * 
 * 统计应用、接口总数据库
 *data:请求数据
 */
function countNums($uid, $name, $hash=[], $type = 'inc') {
	$app_web = isset($hash['app_web']) ? $hash['app_web']: '';
	$app_group = isset($hash['app_group']) ? $hash['app_group']: '';
	$app_id = isset($hash['app_id']) ? $hash['app_id']: '';
	$group_hash = isset($hash['group_hash']) ? $hash['group_hash']: '';
	
	
	switch($name) {
		case 'AdminUser':
			if ($type == 'dec') {
				$res =(new AdminHome())->where('id', 1)->setDec('num_users');
			}else{
				$res =(new AdminHome())->where('id', 1)->setInc('num_users');
			}
			if ($res === false) {
			    return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
			}
		break;
		case 'AdminAppWeb':
			if ($type == 'dec') {
				$res =(new AdminHome())->where('id', 1)->setDec('num_app_web');
				$res =(new AdminUser())->where('id', $uid)->setDec('num_app_web');
			}else{
				$res =(new AdminHome())->where('id', 1)->setInc('num_app_web');
				$res =(new AdminUser())->where('id', $uid)->setInc('num_app_web');
			}
			if ($res === false) {
			    return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
			}
		break;
		case 'AdminAppGroup':
			if ($type == 'dec') {
				$res =(new AdminHome())->where('id', 1)->setDec('num_app_web');
				$res =(new AdminUser())->where('id', $uid)->setDec('num_app_web');
				$res = (new AdminAppWeb())->where('hash', $app_web)->setDec('num_app_group');
			}else{
				$res =(new AdminHome())->where('id', 1)->setInc('num_app_web');
				$res =(new AdminUser())->where('id', $uid)->setInc('num_app_web');
				$res = (new AdminAppWeb())->where('hash', $app_web)->setInc('num_app_group');
			}
			
			if ($res === false) {
			    return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
			}
			
		break;
		case 'AdminApp':
			if ($type == 'dec') {
				$res =(new AdminHome())->where('id', 1)->setDec('num_app');
				$res =(new AdminUser())->where('id', $uid)->setDec('num_app');
				$res = (new AdminAppWeb())->where('hash', $app_web)->setDec('num_app');
				$res = (new AdminAppGroup())->where('hash', $app_group)->setDec('num_app');
				
			}else{
				$res =(new AdminHome())->where('id', 1)->setInc('num_app');
				$res =(new AdminUser())->where('id', $uid)->setInc('num_app');
				$res = (new AdminAppWeb())->where('hash', $app_web)->setInc('num_app');
				$res = (new AdminAppGroup())->where('hash', $app_group)->setInc('num_app');
				
			}			
			if ($res === false) {
			    return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
			}
			
		break;
		case 'AdminGroup':
			if ($type == 'dec') {
				$res =(new AdminHome())->where('id', 1)->setDec('num_interface_group');
				$res =(new AdminUser())->where('id', $uid)->setDec('num_interface_group');
				$res = (new AdminAppWeb())->where('hash', $app_web)->setDec('num_interface_group');
				$res = (new AdminAppGroup())->where('hash', $app_group)->setDec('num_interface_group');
				$res = (new AdminApp())->where('app_id', $app_id)->setDec('num_interface_group');
			}else{
				$res =(new AdminHome())->where('id', 1)->setInc('num_interface_group');
				$res =(new AdminUser())->where('id', $uid)->setInc('num_interface_group');
				$res = (new AdminAppWeb())->where('hash', $app_web)->setInc('num_interface_group');
				$res = (new AdminAppGroup())->where('hash', $app_group)->setInc('num_interface_group');
				$res = (new AdminApp())->where('app_id', $app_id)->setInc('num_interface_group');
			}
			
			
			if ($res === false) {
			    return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
			}
			
		break;
		case 'AdminList':
			if ($type == 'dec') {		
				$res =(new AdminHome())->where('id', 1)->setDec('num_interface');		
				$res =(new AdminUser())->where('id', $uid)->setDec('num_interface');
				$res = (new AdminAppWeb())->where('hash', $app_web)->setDec('num_interface');
				$res = (new AdminAppGroup())->where('hash', $app_group)->setDec('num_interface');
				$res = (new AdminApp())->where('app_id', $app_id)->setDec('num_interface');			
				$res = (new AdminGroup())->where('hash', $group_hash)->setDec('num_interface');
			}else{				
				$res =(new AdminHome())->where('id', 1)->setInc('num_interface');
				$res =(new AdminUser())->where('id', $uid)->setInc('num_interface');
				$res = (new AdminAppWeb())->where('hash', $app_web)->setInc('num_interface');
				$res = (new AdminAppGroup())->where('hash', $app_group)->setInc('num_interface');
				$res = (new AdminApp())->where('app_id', $app_id)->setInc('num_interface');			
				$res = (new AdminGroup())->where('hash', $group_hash)->setInc('num_interface');
			}
			if ($res === false) {
			    return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
			}
			
		break;
		
		default:
		break;
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

function getInfo($name, $hash, $type = 1) {
	switch($name) {
		case 'AdminAppWeb':
			$obj = new AdminAppWeb();
		break;
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
		case 'AdminUser':
			$obj = new AdminUser();
		break;
		
		default:
		break;
	}
	if ($name == 'AdminApp') {
		$obj = $obj->where('app_id', $hash);
	}else if ($name == 'AdminUser') {
		$obj = $obj->where('id', $hash);
	}else{
		$obj = $obj->where('hash', $hash);
	}
	if ($type == 2) {
		if ($name == 'AdminApp') {
			$listObj = $obj->value('app_name');
		}else if ($name == 'AdminUser') {
			$listObj = $obj->value('username');
		}else{
			$listObj = $obj->value('name');
		}
	}else {
		$listObj = $obj->find();
	}
	
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