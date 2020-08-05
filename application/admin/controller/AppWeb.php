<?php
/**
 *
 * @since   2020-08-03
 * @author  Fmoons
 */

namespace app\admin\controller;

use app\model\AdminApp;
use app\model\AdminAppGroup;
use app\model\AdminAppWeb;
use app\util\ReturnCode;

class AppWeb extends Base {

    /**
     * 获取网站组列表
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

        $obj = new AdminAppWeb();
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
            }
        }
		if (UID != 1) {			
			$obj = $obj->where('uid', UID);
		}
        $listObj = $obj->order('id', 'DESC')->paginate($limit, false, ['page' => $start])->toArray();

        return $this->buildSuccess([
            'list'  => $listObj['data'],
            'count' => $listObj['total']
        ]);
    }

    /**
     * 获取全部有效的网站组
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAll() {
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
     * 网站组状态编辑
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function changeStatus() {
        $id = $this->request->get('id');
        $status = $this->request->get('status');
        $res = AdminAppWeb::update([
            'id'     => $id,
            'status' => $status
        ]);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }

        return $this->buildSuccess();
    }

    /**
     * 添加网站组
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return array
     */
    public function add() {
        $postData = $this->request->post();
		$postData['uid'] = UID;
		$obj = new AdminAppWeb();
		
		$web = $obj->where(['siteroot' => $postData['siteroot']])->find();
		if (!empty($web)) {
			 return $this->buildFailed(ReturnCode::DATA_EXISTS, '网站地址已经存在，请重新填写');
		}
		
		$postData['siteroot'] = ($postData['http']==1) ? 'https://'.$postData['siteroot'] : 'http://'.$postData['siteroot'];
		unset($postData['http']);
        $res = $obj->create($postData);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }
		countNums($postData['uid'], 'AdminAppWeb');
        return $this->buildSuccess();
    }

    /**
     * 网站组编辑
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return array
     */
    public function edit() {
        $postData = $this->request->post();
		
		$obj = new AdminAppWeb();
		$listObj = $obj->where('uid', $postData['uid'])->find();
		if (empty($listObj)) {
			$postData['uid'] = UID;
		}		
		
		$siteroot = $obj->where('id','<>', $postData['id'])->where('siteroot','=', $postData['siteroot'])->find();
		
		if (!empty($siteroot)) {
			 return $this->buildFailed(ReturnCode::DATA_EXISTS, '网站地址已经存在，请重新填写');
		}
			
		
		unset($postData['http']);
        $res = $obj->update($postData);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }

        return $this->buildSuccess();
    }

    /**
     * 网站组删除
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return array
     */
    public function del() {
        $hash = $this->request->get('hash');
        if (!$hash) {
            return $this->buildFailed(ReturnCode::EMPTY_PARAMS, '缺少必要参数');
        }

        $has = (new AdminAppGroup())->where(['app_web' => $hash])->count();
        if ($has) {
            return $this->buildFailed(ReturnCode::EMPTY_PARAMS, '当前分组存在' . $has . '个应用组，禁止删除');
        }
		$obj = (new AdminAppWeb())->where(['hash' => $hash])->find();
        AdminAppWeb::destroy(['hash' => $hash]);

		countNums($obj['uid'], 'AdminAppWeb', [], 'dec');
        return $this->buildSuccess();
    }
}
