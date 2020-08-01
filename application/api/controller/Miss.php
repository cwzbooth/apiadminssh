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
		$hash = $this->request->get();

        return $this->buildSuccess([
            'ToYou'   => "I'm glad to meet you（终于等到你！）"
        ]);
    }
}
