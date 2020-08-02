<?php

namespace app\api\controller;

use app\util\StrRandom;
use think\facade\App;

class Miss extends Base {

    public function index() {
        $this->debug([
            'TpVersion' => App::version(),
            'Float'     => StrRandom::randomPhone()
        ]);
		
		$param = $this->request->param();
		$url = getSiteroot();
		
		//print_r($url);
		//print_r(request());

        return $this->buildSuccess([
            'ToYou'   => "I'm glad to meet you（终于等到你！）",
			'from'		=> $url
        ]);
    }
}
