<?php
/**
 * 接口管理
 * @since   2018-02-11
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\admin\controller;

use app\model\AdminApp;
use app\model\AdminFields;
use app\model\AdminList;
use app\model\AdminGroup;
use app\util\ReturnCode;
use think\facade\Env;

class InterfaceList extends Base {

    /**
     * 获取接口列表
     * @return array
     * @throws \think\exception\DbException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function index() {
        $limit = $this->request->get('size', config('apiadmin.ADMIN_LIST_DEFAULT'));
        $start = $this->request->get('page', 1);
        $keywords = $this->request->get('keywords', '');
        $type = $this->request->get('type', '');
        $status = $this->request->get('status', '');

        $obj = new AdminList();
        if (strlen($status)) {
            $obj = $obj->where('status', $status);
        }
        if ($type) {
            switch ($type) {
                case 1:
                    $obj = $obj->where('group_hash', $keywords);
                    break;
                case 2:
                    $obj = $obj->whereLike('info', "%{$keywords}%");
                    break;
                case 3:
                    $obj = $obj->whereLike('api_class', "%{$keywords}%");
                    break;
            }
        }
		if (UID != 1) {			
			$obj = $obj->where('uid', UID);
		}
        $listObj = $obj->order('id', 'DESC')->paginate($limit, false, ['page' => $start])->toArray();

		foreach ($listObj['data'] as $k => $r) {
			$listObj['data'][$k]['group_name'] = getInfo('AdminGroup', $r['group_hash'], 2);
			$listObj['data'][$k]['username'] = getInfo('AdminUser', $r['uid'], 2);
		}
        return $this->buildSuccess([
            'list'  => $listObj['data'],
            'count' => $listObj['total']
        ]);
    }

    /**
     * 获取接口唯一标识
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function getHash() {
        $res['hash'] = uniqid();

        return $this->buildSuccess($res);
    }

    /**
     * 新增接口
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function add() {
        $postData = $this->request->post();
        if (!preg_match("/^[A-Za-z0-9_\/]+$/", $postData['api_class'])) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR, '真实类名只允许填写字母，数字和/');
        }
		
		$group = getInfo('AdminGroup', $postData['group_hash']);
		$postData['group_name'] = empty($group['name']) ? '' : $group['name'] ;
		$postData['group_apiUrl'] = empty($group['apiUrl']) ? '' : $group['apiUrl'] ;
		$postData['uid'] = UID;
		//print_r($postData);exit;
        $res = AdminList::create($postData);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }

		$obj = getInfo('AdminApp', $group['app_id']);
		countNums($obj['uid'], 'AdminList', ['app_web' => $obj['app_web'], 'app_group' => $obj['app_group'], 'app_id' => $group['app_id'], 'group_hash' => $group['hash']]);
        return $this->buildSuccess();
    }

    /**
     * 接口状态编辑
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function changeStatus() {
        $hash = $this->request->get('hash');
        $status = $this->request->get('status');
        $res = AdminList::update([
            'status' => $status
        ], [
            'hash' => $hash
        ]);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }
		
        cache('ApiInfo:' . $hash, null);

        return $this->buildSuccess();
    }

    /**
     * 编辑接口
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function edit() {
        $postData = $this->request->post();
        if (!preg_match("/^[A-Za-z0-9_\/]+$/", $postData['api_class'])) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR, '真实类名只允许填写字母，数字和/');
        }
		$group = getInfo('AdminGroup', $postData['group_hash']);
		$postData['group_name'] = $group['name'];
		$postData['group_apiUrl'] = $group['apiUrl'];
		
		$obj = new AdminList();
		$listObj = $obj->where('uid', $postData['uid'])->find();
		if (empty($listObj)) {
			$postData['uid'] = UID;
		}
        $res = AdminList::update($postData);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }
        cache('ApiInfo:' . $postData['hash'], null);

        return $this->buildSuccess();
    }

    /**
     * 删除接口
     * @return array
     * @throws \think\exception\DbException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function del() {
        $hash = $this->request->get('hash');
        if (!$hash) {
            return $this->buildFailed(ReturnCode::EMPTY_PARAMS, '缺少必要参数');
        }

        $hashRule = AdminApp::all([
            'app_api' => ['like', "%$hash%"]
        ]);
        if ($hashRule) {
			$oldInfo = AdminList::get(['hash' => $hash]);
            foreach ($hashRule as $rule) {
                $appApiArr = explode(',', $rule->app_api);
                $appApiIndex = array_search($hash, $appApiArr);
                array_splice($appApiArr, $appApiIndex, 1);
                $rule->app_api = implode(',', $appApiArr);

                $appApiShowArrOld = json_decode($rule->app_api_show, true);
                $appApiShowArr = $appApiShowArrOld[$oldInfo->groupHash];
                $appApiShowIndex = array_search($hash, $appApiShowArr);
                array_splice($appApiShowArr, $appApiShowIndex, 1);
                $appApiShowArrOld[$oldInfo->groupHash] = $appApiShowArr;
                $rule->app_api_show = json_encode($appApiShowArrOld);

                $rule->save();
            }
        }

		
		$objList = (new AdminList())->where(['hash' => $hash])->find();
		$objGroup = (new AdminGroup())->where(['hash' => $objList['group_hash']])->find();		
		$obj = getInfo('AdminApp', $objGroup['app_id']);
		
        AdminList::destroy(['hash' => $hash]);
        AdminFields::destroy(['hash' => $hash]);
		
		
		countNums($obj['uid'], 'AdminList', ['app_web' => $obj['app_web'], 'app_group' => $obj['app_group'], 'app_id' => $obj['app_id'], 'group_hash' => $objGroup['hash']], 'dec');

        cache('ApiInfo:' . $hash, null);

        return $this->buildSuccess();
    }

    /**
     * 刷新接口路由
     * @return array
     * @throws \think\exception\DbException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function refresh() {
        $rootPath = Env::get('root_path');
        $apiRoutePath = $rootPath . 'route/apiRoute.php';
        $tplPath = $rootPath . 'application/install/apiRoute.tpl';
        $methodArr = ['*', 'POST', 'GET'];

        $tplOriginStr = file_get_contents($tplPath);
        $listInfo = AdminList::all(['status' => 1]);
        $tplStr = [];
        foreach ($listInfo as $value) {
            if($value['hash_type'] === 1) {
                array_push($tplStr, 'Route::rule(\'' . addslashes($value->api_class) . '\',\'api/' . addslashes($value->api_class) . '\', \'' . $methodArr[$value->method] . '\')->middleware([\'ApiAuth\', \'ApiPermission\', \'RequestFilter\', \'ApiLog\']);');
            } else {
                array_push($tplStr, 'Route::rule(\'' . addslashes($value->hash) . '\',\'api/' . addslashes($value->api_class) . '\', \'' . $methodArr[$value->method] . '\')->middleware([\'ApiAuth\', \'ApiPermission\', \'RequestFilter\', \'ApiLog\']);');
            }
        }
        $tplOriginStr = str_replace(['{$API_RULE}'], [implode(PHP_EOL . '    ', $tplStr)], $tplOriginStr);

        file_put_contents($apiRoutePath, $tplOriginStr);

        return $this->buildSuccess();
    }

	public function getGroup($hash) {	
	    $obj = new AdminGroup();
		if (UID == 1) {
			$uid = $this->request->get('uid', 0);
			if ($uid > 0) {			
				$obj = $obj->where('uid', $uid);
			}else{
				$obj = $obj->where('uid', UID);
			}
		}else{
			$obj = $obj->where('uid', UID);
		}
		
		
	    $obj = $obj->where('hash', $hash);	   
	    $listObj = $obj->find();	
	    return $listObj;
	}

}
