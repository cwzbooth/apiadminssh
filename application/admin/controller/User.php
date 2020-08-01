<?php
/**
 * 用户管理
 * @since   2018-02-06
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\admin\controller;

use app\model\AdminAuthGroupAccess;
use app\model\AdminUser;
use app\model\AdminUserData;
use app\model\AdminApp;
use app\model\AdminAppGroup;
use app\model\AdminList;
use app\model\AdminGroup;
use app\util\ReturnCode;
use app\util\Tools;
use app\util\Strs;
use think\Db;

class User extends Base {

    /**
     * 获取用户列表
     * @return array
     * @throws \think\exception\DbException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function index() {
        $limit = $this->request->get('size', config('apiadmin.ADMIN_LIST_DEFAULT'));
        $start = $this->request->get('page', 1);
        $type = $this->request->get('type', '', 'intval');
        $keywords = $this->request->get('keywords', '');
        $status = $this->request->get('status', '');

        $obj = new AdminUser();
        if (strlen($status)) {
            $obj = $obj->where('status', $status);
        }
        if ($type) {
            switch ($type) {
                case 1:
                    $obj = $obj->whereLike('username', "%{$keywords}%");
                    break;
                case 2:
                    $obj = $obj->whereLike('nickname', "%{$keywords}%");
                    break;
            }
        }

        $listObj = $obj->order('create_time', 'DESC')
            ->paginate($limit, false, ['page' => $start])->each(function($item, $key){
                $item->userData;
            })->toArray();
        $listInfo = $listObj['data'];
        $idArr = array_column($listInfo, 'id');

        $userGroup = AdminAuthGroupAccess::all(function($query) use ($idArr) {
            $query->whereIn('uid', $idArr);
        });
        $userGroup = Tools::buildArrFromObj($userGroup);
        $userGroup = Tools::buildArrByNewKey($userGroup, 'uid');


        foreach ($listInfo as $key => &$value) {
            if ($value['userData']) {
                $value['userData']['last_login_ip'] = long2ip($value['userData']['last_login_ip']);
                $value['userData']['last_login_time'] = date('Y-m-d H:i:s', $value['userData']['last_login_time']);
                $value['create_ip'] = long2ip($value['create_ip']);
            }
            if (isset($userGroup[$value['id']])) {
                $value['group_id'] = explode(',', $userGroup[$value['id']]['group_id']);
            } else {
                $value['group_id'] = [];
            }
        }

        return $this->buildSuccess([
            'list'  => $listInfo,
            'count' => $listObj['total']
        ]);
    }

    /**
     * 新增用户
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function add() {
        $groups = '';
        $postData = $this->request->post();
		
		$obj = new AdminUser();
		$listObj = $obj->where('username', $postData['username'])->find();
		if (!empty($listObj)) {
			 return $this->buildFailed(ReturnCode::DATA_EXISTS, '该账户已经存在');
		}
		
        $postData['create_ip'] = request()->ip(1);
        $postData['password'] = Tools::userMd5($postData['password']);
        if (isset($postData['group_id']) && $postData['group_id']) {
            $groups = trim(implode(',', $postData['group_id']), ',');
            unset($postData['group_id']);
        }
		$postData['status'] = 1;
        $res = AdminUser::create($postData);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }
		
        AdminAuthGroupAccess::create([
            'uid'      => $res->id,
            'group_id' => $groups
        ]);
		$postData['uid'] = $res->id;
		$this->addDefault($postData);

        return $this->buildSuccess();
    }
	
	/**
	 * 新增用户默认接口数据
	 * @return array
	 * @author Fmoons
	 */
	public function addDefault($arr) {
		
		//1、添加默认应用组数据
		$dataAppGroup = array(
			'uid' => $arr['uid'],
			'hash' => uniqid(),
			'description' => !empty($arr['account_name']) ? $arr['account_name'] . '网站接口管理' : '女神来了网站接口管理',
			'name' => !empty($arr['account_name']) ? $arr['account_name'] : "女神来了"
		);
		$res = AdminAppGroup::create($dataAppGroup);
		if ($res === false) {
		    return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
		}
		
		//2、添加默认接口组数据
		$dataGroup = array(
			'uid' => $arr['uid'],
			'hash' => uniqid(),
			'description' => !empty($arr['activity_name']) ? $arr['activity_name'] . '活动分组接口管理' :"女神来了投票活动分组接口管理",
			'name' => !empty($arr['activity_name']) ? $arr['activity_name'] :"女神来了投票活动名称",
			'apiUrl' => !empty($arr['activity_url']) ? $arr['activity_url'] :"https://wx.fmoons.com/app/index.php?i=6&c=entry&a=wxapp&m=fm_photosvote&rid=53",
			'image' => $arr['avatar']
		);
		$res = AdminGroup::create($dataGroup);
		if ($res === false) {
		    return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
		}
		
		//3、添加默认活动接口		
		$datalist = [
			[
				'uid' => $arr['uid'],			
				'api_class' =>  "Photosvote/Get",
				'group_name' =>  $dataGroup['name'],
				'group_apiUrl' =>  $dataGroup['apiUrl'],
				'group_hash' =>  $dataGroup['hash'],
				'hash' =>  uniqid(),
				'info' =>  "获取".$dataGroup['name']."接口GET",
				'method' =>  2,
				'access_token' =>  0,
				'is_test' =>  0,
				'hash_type' =>  2,
			],
			[
				'uid' => $arr['uid'],			
				'api_class' =>  "Photosvote/Post",
				'group_name' =>  $dataGroup['name'],
				'group_apiUrl' =>  $dataGroup['apiUrl'],
				'group_hash' =>  $dataGroup['hash'],
				'hash' =>  uniqid(),
				'info' =>  "获取".$dataGroup['name']."接口POST",
				'method' =>  1,
				'access_token' =>  0,
				'is_test' =>  0,
				'hash_type' =>  2,
			]
		];
		//print_r($postData);exit;
		foreach ($datalist as $k => $r) {
			$res = AdminList::create($r);
		}
		
		if ($res === false) {
		    return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
		}
		
		//4、添加默认应用数据
		
		$dataApp = [
		    'app_id'       => uniqid(),
		    'app_secret'   => Strs::randString(32),
		    'app_name'     => !empty($arr['app_name']) ? $arr['app_name'] : '女神来了',
		    'app_info'     => !empty($arr['app_name']) ? $arr['app_name'] . '应用管理' : '女神来了应用管理',
		    'app_group'    => $dataAppGroup['hash'],
		    'uid'    => $arr['uid'],
		    'app_add_time' => time(),
		    'app_api'      => '',
		    'app_api_show' => ''
		];
		$app_api = [$dataGroup['hash'] => [$datalist[0]['hash'],$datalist[1]['hash']]];
		if ($app_api) {
		    $appApi = [];
		    $dataApp['app_api_show'] = json_encode($app_api);
		    foreach ($app_api as $value) {
		        $appApi = array_merge($appApi, $value);
		    }
		    $dataApp['app_api'] = implode(',', $appApi);
		}
		$res = AdminApp::create($dataApp);
		if ($res === false) {
		    return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
		}
	
	    return $this->buildSuccess();
	}
	

    /**
     * 获取当前组的全部用户
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\DbException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function getUsers() {
        $limit = $this->request->get('size', config('apiadmin.ADMIN_LIST_DEFAULT'));
        $page = $this->request->get('page', 1);
        $gid = $this->request->get('gid', 0);
        if (!$gid) {
            return $this->buildFailed(ReturnCode::PARAM_INVALID, '非法操作');
        }

        $totalNum = (new AdminAuthGroupAccess())->where('find_in_set("' . $gid . '", `group_id`)')->count();
        $start = $limit * ($page - 1);
        $sql = "SELECT au.* FROM admin_user as au LEFT JOIN admin_auth_group_access as aaga " .
            " ON aaga.`uid` = au.`id` WHERE find_in_set('{$gid}', aaga.`group_id`) " .
            " ORDER BY au.create_time DESC LIMIT {$start}, {$limit}";
        $userInfo = Db::query($sql);

        $uidArr = array_column($userInfo, 'id');
        $userData = (new AdminUserData())->whereIn('uid', $uidArr)->select();
        $userData = Tools::buildArrByNewKey($userData, 'uid');

        foreach ($userInfo as $key => $value) {
            if (isset($userData[$value['id']])) {
                $userInfo[$key]['last_login_ip'] = long2ip($userData[$value['id']]['last_login_ip']);
                $userInfo[$key]['login_times'] = $userData[$value['id']]['login_times'];
                $userInfo[$key]['last_login_time'] = date('Y-m-d H:i:s', $userData[$value['id']]['last_login_time']);
            }
            $userInfo[$key]['create_ip'] = long2ip($userInfo[$key]['create_ip']);
        }

        return $this->buildSuccess([
            'list'  => $userInfo,
            'count' => $totalNum
        ]);
    }

    /**
     * 用户状态编辑
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function changeStatus() {
        $id = $this->request->get('id');
        $status = $this->request->get('status');
        $res = AdminUser::update([
            'id'     => $id,
            'status' => $status
        ]);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }
        if($oldAdmin = cache('Login:' . $id)) {
            cache('Login:' . $oldAdmin, null);
        }

        return $this->buildSuccess();
    }

    /**
     * 编辑用户
     * @return array
     * @throws \think\exception\DbException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function edit() {
        $groups = '';
        $postData = $this->request->post();
        if ($postData['password'] === 'ApiAdmin') {
            unset($postData['password']);
        } else {
            $postData['password'] = Tools::userMd5($postData['password']);
        }
        if (isset($postData['group_id']) && $postData['group_id']) {
            $groups = trim(implode(',', $postData['group_id']), ',');
            unset($postData['group_id']);
        }
        $res = AdminUser::update($postData);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }
		
        $has = AdminAuthGroupAccess::get(['uid' => $postData['id']]);
        if ($has) {
            AdminAuthGroupAccess::update([
                'group_id' => $groups
            ], [
                'uid' => $postData['id'],
            ]);
        } else {
            AdminAuthGroupAccess::create([
                'uid'      => $postData['id'],
                'group_id' => $groups
            ]);
        }
        if($oldAdmin = cache('Login:' . $postData['id'])) {
            cache('Login:' . $oldAdmin, null);
        }

        return $this->buildSuccess();
    }

    /**
     * 修改自己的信息
     * @return array
     * @throws \think\exception\DbException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function own() {
        $postData = $this->request->post();
        $headImg = $postData['head_img'];

        if ($postData['password'] && $postData['oldPassword']) {
            $oldPass = Tools::userMd5($postData['oldPassword']);
            unset($postData['oldPassword']);
            if ($oldPass === $this->userInfo['password']) {
                $postData['password'] = Tools::userMd5($postData['password']);
            } else {
                return $this->buildFailed(ReturnCode::INVALID, '原始密码不正确');
            }
        } else {
            unset($postData['password']);
            unset($postData['oldPassword']);
        }
        $postData['id'] = $this->userInfo['id'];
        unset($postData['head_img']);
        $res = AdminUser::update($postData);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }
        $userData = AdminUserData::get(['uid' => $postData['id']]);
        $userData->head_img = $headImg;
        $userData->save();
        if($oldWiki = cache('WikiLogin:' . $postData['id'])) {
            cache('WikiLogin:' . $oldWiki, null);
        }

        return $this->buildSuccess();
    }

    /**
     * 删除用户
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function del() {
        $id = $this->request->get('id');
        if (!$id) {
            return $this->buildFailed(ReturnCode::EMPTY_PARAMS, '缺少必要参数');
        }

        $isAdmin = Tools::isAdministrator($id);
        if ($isAdmin) {
            return $this->buildFailed(ReturnCode::INVALID, '超级管理员不能被删除');
        }
        AdminUser::destroy($id);
        AdminAuthGroupAccess::destroy(['uid' => $id]);
        if($oldAdmin = cache('Login:' . $id)) {
            cache('Login:' . $oldAdmin, null);
        }

        return $this->buildSuccess();
    }
	
	/**
	 * 获取用户信息
	 * @return array
	 * @author FMOONS
	 */
	public function getUserOne() {
	    $uid = $this->request->get('uid');
	    if (!$uid) {
	        return $this->buildFailed(ReturnCode::EMPTY_PARAMS, '缺少必要参数');
	    }
		$obj = new AdminUser();
		$listObj = $obj->where('id', $uid)->find();
	    return $this->buildSuccess($listObj['nickname'].'('.$listObj['id'].')');
	}
}
