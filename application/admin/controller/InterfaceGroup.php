<?php
/**
 * 接口组维护
 * @since   2018-02-11
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\admin\controller;

use app\model\AdminApp;
use app\model\AdminGroup;
use app\model\AdminList;
use app\util\ReturnCode;

class InterfaceGroup extends Base {

    /**
     * 获取接口组列表
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

        $obj = new AdminGroup();
        if (strlen($status)) {
            $obj = $obj->where('status', $status);
        }
        if ($type) {
            switch ($type) {
                case 1:
                    $obj = $obj->where('hash', $keywords);
                    break;
                case 2:
                    $obj = $obj->whereLike('name', "%{$keywords}%");
                    break;
                case 3:
                    $obj = $obj->whereLike('app_id', "%{$keywords}%");
                    break;
                case 4:
                    $obj = $obj->where('uid', $keywords);
                    break;
            }
        }
		if (UID != 1) {			
			$obj = $obj->where('uid', UID);
		}
        $listObj = $obj->order('create_time', 'desc')->paginate($limit, false, ['page' => $start])->toArray();
		foreach ($listObj['data'] as $k => $r) {
			$listObj['data'][$k]['app_name'] = getInfo('AdminApp', $r['app_id'], 2);
			$listObj['data'][$k]['username'] = getInfo('AdminUser', $r['uid'], 2);
		}
        return $this->buildSuccess([
            'list'  => $listObj['data'],
            'count' => $listObj['total']
        ]);
    }

    /**
     * 获取全部有效的接口组
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAll() {
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
		
		
        $listInfo = $obj->where(['status' => 1])->select();

        return $this->buildSuccess([
            'list' => $listInfo
        ]);
    }

    /**
     * 接口组状态编辑
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function changeStatus() {
        $id = $this->request->get('id');
        $status = $this->request->get('status');
        $res = AdminGroup::update([
            'id'     => $id,
            'status' => $status,
        ]);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }

        return $this->buildSuccess();
    }

    /**
     * 添加接口组
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return array
     */
    public function add() {
        $postData = $this->request->post();
		$postData['uid'] = UID;
		
		$app = getInfo('AdminApp', $postData['app_id']);
		$postData['app_name'] = $app['app_name'];
		$postData['app_secret'] = $app['app_secret'];
		
        $res = AdminGroup::create($postData);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }
	
		$obj = getInfo('AdminApp', $postData['app_id']);
		countNums($obj['uid'], 'AdminGroup', ['app_web' => $obj['app_web'], 'app_group' => $obj['app_group'], 'app_id' => $postData['app_id']]);
        return $this->buildSuccess();
    }

    /**
     * 接口组编辑
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return array
     */
    public function edit() {
        $postData = $this->request->post();
		
		$obj = new AdminGroup();
		$listObj = $obj->where('uid', $postData['uid'])->find();
		if (empty($listObj)) {
			$postData['uid'] = UID;
		}
		
		$app = getInfo('AdminApp', $postData['app_id']);
		$postData['app_name'] = $app['app_name'];
		$postData['app_secret'] = $app['app_secret'];
		
        $res = AdminGroup::update($postData);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }

        return $this->buildSuccess();
    }

    /**
     * 接口组删除
     * @return array
     * @throws \think\exception\DbException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function del() {
        $hash = $this->request->get('hash');
        if (!$hash) {
            return $this->buildFailed(ReturnCode::EMPTY_PARAMS, '缺少必要参数');
        }
        if ($hash === 'default') {
            return $this->buildFailed(ReturnCode::INVALID, '系统预留关键数据，禁止删除！');
        }
        $has = (new AdminList())->where(['group_hash' => $hash])->count();
        if ($has) {
            return $this->buildFailed(ReturnCode::EMPTY_PARAMS, '当前分组存在' . $has . '个接口，禁止删除');
        }

        AdminList::update(['group_hash' => 'default'], ['group_hash' => $hash]);

        $hashRule = AdminApp::all([
            'app_api_show' => ['like', "%$hash%"]
        ]);
        if ($hashRule) {
            foreach ($hashRule as $rule) {
                $appApiShowArr = json_decode($rule->app_api_show, true);
                if (!empty($appApiShowArr[$hash])) {
                    if (isset($appApiShowArr['default'])) {
                        $appApiShowArr['default'] = array_merge($appApiShowArr['default'], $appApiShowArr[$hash]);
                    } else {
                        $appApiShowArr['default'] = $appApiShowArr[$hash];
                    }
                }
                unset($appApiShowArr[$hash]);
                $rule->app_api_show = json_encode($appApiShowArr);
                $rule->save();
            }
        }
		$objGroup = (new AdminGroup())->where(['hash' => $hash])->find();		
		$obj = getInfo('AdminApp', $objGroup['app_id']);
		
        AdminGroup::destroy(['hash' => $hash]);

		countNums($obj['uid'], 'AdminGroup', ['app_web' => $obj['app_web'], 'app_group' => $obj['app_group'], 'app_id' => $objGroup['app_id']], 'dec');
        return $this->buildSuccess();
    }
}
