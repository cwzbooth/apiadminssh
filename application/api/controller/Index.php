<?php

namespace app\api\controller;

use think\facade\App;

class Index extends Base {

    public function index() {
        $this->debug([
            'TpVersion' => App::version()
        ]);

        return $this->buildSuccess([
            'ToYou'   => "I'm glad to meet you（终于等到你！）"
        ]);
    }
}
