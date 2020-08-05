<?php
/**
 * 应用管理
 * @since   2018-02-11
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\admin\controller;

use app\model\AdminApp;
use app\model\AdminList;
use app\model\AdminGroup;
use app\model\AdminAppWeb;
use app\util\ReturnCode;
use app\util\Strs;
use app\util\Tools;

class App extends Base {

    /**
     * 获取应用列表
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

        $obj = new AdminApp();
        if (strlen($status)) {
            $obj = $obj->where('app_status', $status);
        }
        if ($type) {
            switch ($type) {
                case 1:
                    $obj = $obj->where('app_id', $keywords);
                    break;
                case 2:
                    $obj = $obj->whereLike('app_name', "%{$keywords}%");
                    break;
                case 3:
                    $obj = $obj->whereLike('app_group', "%{$keywords}%");
                    break;
                case 4:
                    $obj = $obj->where('uid', $keywords);
                    break;
            }
        }
		if (UID != 1) {			
			$obj = $obj->where('uid', UID);
		}
		
        $listObj = $obj->order('app_add_time', 'DESC')->paginate($limit, false, ['page' => $start])->toArray();
		
		foreach ($listObj['data'] as $k => $r) {
			$listObj['data'][$k]['app_group_name'] = getInfo('AdminAppGroup', $r['app_group'], 2);
			$listObj['data'][$k]['username'] = getInfo('AdminUser', $r['uid'], 2);
		}

        return $this->buildSuccess([
            'list'  => $listObj['data'],
            'count' => $listObj['total']
        ]);
    }

    /**
     * 获取AppId,AppSecret,接口列表,应用接口权限细节
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function getAppInfo() {
        $apiArr = new AdminList();
		 $uid = $this->request->get('uid', 0);
		//if (UID != 1) {			
			$apiArr = $apiArr->where('uid', $uid);
		//}
		
        $apiArr = $apiArr->all();
		//print_r($apiArr);exit;
		
        foreach ($apiArr as $api) {
            $res['apiList'][$api['group_hash']][] = $api;
        }
		$groupArr = new AdminGroup();
		//if (UID != 1) {			
			$groupArr = $groupArr->where('uid', $uid);
		//}
        $groupArr =$groupArr->all();
        $groupArr = Tools::buildArrFromObj($groupArr);
        $res['groupInfo'] = array_column($groupArr, 'name', 'hash');
        $id = $this->request->get('id', 0);
        if ($id) {
			$appInfo = new AdminApp();
			//if (UID != 1) {			
				$appInfo = $appInfo->where('uid', $uid);
			//}
            $appInfo = $appInfo->get($id)->toArray();
            $res['app_detail'] = json_decode($appInfo['app_api_show'], true);
        } else {
            $res['app_id'] = mt_rand(1, 9) . Strs::randString(7, 1);
            $res['app_secret'] = Strs::randString(32);
        }

        return $this->buildSuccess($res);
    }
	
	/**
	 * 获取全部有效的应用
	 * @author Fmoons
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function getAppId() {
		$obj = new AdminApp();
		
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
	    $listInfo = $obj->where(['app_status' => 1])->select();
	
	    return $this->buildSuccess([
	        'list' => $listInfo
	    ]);
	}

    /**
     * 刷新APPSecret
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return array
     */
    public function refreshAppSecret() {
        $data['app_secret'] = Strs::randString(32);

        return $this->buildSuccess($data);
    }

    /**
     * 新增应用
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function add() {
        $postData = $this->request->post();
		$group = getInfo('AdminAppGroup', $postData['app_group']);
		$web = getInfo('AdminAppWeb', $group['app_web']);
        $data = [
            'app_id'       => $postData['app_id'],
            'app_secret'   => $postData['app_secret'],
            'app_name'     => $postData['app_name'],
            'app_info'     => $postData['app_info'],
            'app_group'    => $postData['app_group'],
            'app_group_name'    => $group['name'],
            'app_web'    => $web['hash'],
            'app_web_name'    => $web['name'],
            'uid'    => UID,
            'app_add_time' => time(),
            'app_api'      => '',
            'app_api_show' => ''
        ];
        if (isset($postData['app_api']) && $postData['app_api']) {
            $appApi = [];
            $data['app_api_show'] = json_encode($postData['app_api']);
            foreach ($postData['app_api'] as $value) {
                $appApi = array_merge($appApi, $value);
            }
            $data['app_api'] = implode(',', $appApi);
        }
        $res = AdminApp::create($data);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }
		
		countNums($group['uid'], 'AdminApp', ['app_web' => $group['app_web'], 'app_group' => $postData['app_group']]);

        return $this->buildSuccess();
    }

    /**
     * 应用状态编辑
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function changeStatus() {
        $id = $this->request->get('id');
        $status = $this->request->get('status');
        $res = AdminApp::update([
            'id'         => $id,
            'app_status' => $status
        ]);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }
        $appInfo = AdminApp::get($id);
        cache('AccessToken:Easy:' . $appInfo['app_secret'], null);
        if($oldWiki = cache('WikiLogin:' . $id)) {
            cache('WikiLogin:' . $oldWiki, null);
        }

        return $this->buildSuccess();
    }

    /**
     * 编辑应用
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function edit() {
        $postData = $this->request->post();
		$group = getInfo('AdminAppGroup', $postData['app_group']);
		$web = getInfo('AdminAppWeb', $group['app_web']);
        $data = [
            'app_secret'   => $postData['app_secret'],
            'app_name'     => $postData['app_name'],
            'app_info'     => $postData['app_info'],
            'app_group'    => $postData['app_group'],
            'app_group_name'    => $group['name'],
            'app_web'    => $web['hash'],
            'app_web_name'    => $web['name'],
            'app_api'      => '',
            'app_api_show' => ''
        ];
		
		$obj = new AdminApp();
		$listObj = $obj->where('uid', $postData['uid'])->find();
		if (empty($listObj)) {
			$data['uid'] = UID;
		}
		
        if (isset($postData['app_api']) && $postData['app_api']) {
            $appApi = [];
            $data['app_api_show'] = json_encode($postData['app_api']);
            foreach ($postData['app_api'] as $value) {
                $appApi = array_merge($appApi, $value);
            }
            $data['app_api'] = implode(',', $appApi);
        }
        $res = AdminApp::update($data, ['id' => $postData['id']]);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }
        $appInfo = AdminApp::get($postData['id']);
        cache('AccessToken:Easy:' . $appInfo['app_secret'], null);
        if($oldWiki = cache('WikiLogin:' . $postData['id'])) {
            cache('WikiLogin:' . $oldWiki, null);
        }

        return $this->buildSuccess();

    }

    /**
     * 删除应用s
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function del() {
        $id = $this->request->get('id');
        if (!$id) {
            return $this->buildFailed(ReturnCode::EMPTY_PARAMS, '缺少必要参数');
        }
        $appInfo = AdminApp::get($id);
		$has = (new AdminGroup())->where(['app_id' => $appInfo['app_id']])->count();
		if ($has) {
		    return $this->buildFailed(ReturnCode::EMPTY_PARAMS, '当前分组存在' . $has . '个接口组，禁止删除');
		}
		
        cache('AccessToken:Easy:' . $appInfo['app_secret'], null);

        AdminApp::destroy($id);
		
		countNums($appInfo['uid'], 'AdminApp', ['app_web' => $appInfo['app_web'], 'app_group' => $appInfo['app_group']], 'dec');
        if($oldWiki = cache('WikiLogin:' . $id)) {
            cache('WikiLogin:' . $oldWiki, null);
        }
		

        return $this->buildSuccess();
    }
	
}
