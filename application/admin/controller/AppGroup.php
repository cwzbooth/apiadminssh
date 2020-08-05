<?php
/**
 *
 * @since   2018-02-11
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\admin\controller;

use app\model\AdminApp;
use app\model\AdminAppGroup;
use app\model\AdminAppWeb;
use app\util\ReturnCode;

class AppGroup extends Base {

    /**
     * 获取应用组列表
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

        $obj = new AdminAppGroup();
        if (strlen($status)) {
            $obj = $obj->where('status', $status);
        }
        if ($type) {
            switch ($type) {
                case 1:
                    if (strlen($keywords)) {
                        $obj = $obj->where('hash', $keywords);
                    }
                    break;
                case 2:
                    $obj = $obj->whereLike('name', "%{$keywords}%");
                    break;
                case 3:
                    $obj = $obj->whereLike('app_web', "%{$keywords}%");
                    break;
                case 4:
                    $obj = $obj->where('uid', $keywords);
                    break;
            }
        }
		if (UID != 1) {			
			$obj = $obj->where('uid', UID);
		}
        $listObj = $obj->order('id', 'DESC')->paginate($limit, false, ['page' => $start])->toArray();
		
		foreach ($listObj['data'] as $k => $r) {
			$listObj['data'][$k]['app_web_name'] = getInfo('AdminAppWeb', $r['app_web'], 2);
			$listObj['data'][$k]['username'] = getInfo('AdminUser', $r['uid'], 2);
		}

        return $this->buildSuccess([
            'list'  => $listObj['data'],
            'count' => $listObj['total']
        ]);
    }

    /**
     * 获取全部有效的应用组
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAll() {
		$obj = new AdminAppGroup();
		
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
     * 获取全部有效的网站组
     * @author Fmoons
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getWeb() {
		$obj = new AdminAppWeb();
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
     * 应用组状态编辑
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function changeStatus() {
        $id = $this->request->get('id');
        $status = $this->request->get('status');
        $res = AdminAppGroup::update([
            'id'     => $id,
            'status' => $status
        ]);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }

        return $this->buildSuccess();
    }

    /**
     * 添加应用组
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return array
     */
    public function add() {
        $postData = $this->request->post();
		$postData['uid'] = UID;
		unset($postData['http']);
		
		$web = getInfo('AdminAppWeb', $postData['app_web']);
		$postData['app_web_name'] = $web['name'];
		$postData['siteroot'] = $web['siteroot'];
        $res = AdminAppGroup::create($postData);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }
		countNums($web['uid'], 'AdminAppGroup', ['app_web' => $postData['app_web']]);
        return $this->buildSuccess();
    }

    /**
     * 应用组编辑
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return array
     */
    public function edit() {
        $postData = $this->request->post();
		
		$obj = new AdminAppGroup();
		$listObj = $obj->where('uid', $postData['uid'])->find();
		if (empty($listObj)) {
			$postData['uid'] = UID;
		}
		unset($postData['http']);
		$web = getInfo('AdminAppWeb', $postData['app_web']);
		$postData['app_web_name'] = $web['name'];
		$postData['siteroot'] = $web['siteroot'];
        $res = AdminAppGroup::update($postData);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }

        return $this->buildSuccess();
    }

    /**
     * 应用组删除
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return array
     */
    public function del() {
        $hash = $this->request->get('hash');
        if (!$hash) {
            return $this->buildFailed(ReturnCode::EMPTY_PARAMS, '缺少必要参数');
        }

        $has = (new AdminApp())->where(['app_group' => $hash])->count();
        if ($has) {
            return $this->buildFailed(ReturnCode::EMPTY_PARAMS, '当前分组存在' . $has . '个应用，禁止删除');
        }
		
		$obj = (new AdminAppGroup())->where(['hash' => $hash])->find();

        AdminAppGroup::destroy(['hash' => $hash]);
		
		
		countNums($obj['uid'], 'AdminAppGroup', ['app_web' => $obj['app_web']], 'dec');

        return $this->buildSuccess();
    }
}
