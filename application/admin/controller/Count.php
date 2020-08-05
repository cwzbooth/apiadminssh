<?php
/**
 * 后台请求列表管理
 * @since   2020-07-31
 * @author  Fmoons
 */

namespace app\admin\controller;

use app\model\AdminCount;
use app\util\ReturnCode;

class Count extends Base {

    /**
     * 获取接口请求列表
     * @return array
     * @throws \think\exception\DbException
     * @author Fmoons
     */
    public function index() {
        $limit = $this->request->get('size', config('apiadmin.ADMIN_LIST_DEFAULT'));
        $start = $this->request->get('page', 1);
        $type = $this->request->get('type', '');
        $keywords = $this->request->get('keywords', '');
        $hash = $this->request->get('hash', '');
        $obj = new AdminCount();
        if ($type) {
            switch ($type) {
                case 1:
                    $obj = $obj->whereLike('username', "%{$keywords}%");
                    break;
                case 2:
                    $obj = $obj->where('openid', $keywords);
                    break;
                case 3:
                    $obj = $obj->whereLike('hash', "%{$keywords}%");
                    break;
                case 4:
                    $obj = $obj->whereLike('group_hash', "%{$keywords}%");
                    break;
                case 5:
                    $obj = $obj->whereLike('app_id', "%{$keywords}%");
                    break;
                case 6:
                    $obj = $obj->whereLike('app_group', "%{$keywords}%");
                    break;
            }
        }
		if ($hash) {
			 $obj = $obj->where('hash', $hash);
			 $u = getInfo('AdminList', $hash);
			 $obj->where('uid', $u['uid']);
		}
        $listObj = $obj->order('create_time', 'DESC')->paginate($limit, false, ['page' => $start])->toArray();

        return $this->buildSuccess([
            'list'  => $listObj['data'],
            'count' => $listObj['total']
        ]);
    }

    /**
     * 删除
     * @return array
     * @author Fmoons
     */
    public function del() {
        $id = $this->request->get('id');
        if (!$id) {
            return $this->buildFailed(ReturnCode::EMPTY_PARAMS, '缺少必要参数');
        }
        AdminCount::destroy($id);

        return $this->buildSuccess();
    }
}
