<?php

namespace app\api\controller;

use think\facade\App;

use app\admin\controller\User;


class Start extends Base {

    public function Index() {
		$postData = $this->request->post();
		
		$user = new User();
		$user = $user->add();
		//print_r($user);exit;
		
		
		return $this->buildSuccess();
    }

	
}
