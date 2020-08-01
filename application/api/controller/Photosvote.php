<?php

namespace app\api\controller;

use think\facade\App;

class Photosvote extends Base {

    public function Index() {
		$param = $this->request->param();
		return $this->buildFailed();
    }
	
	public function Get() {
		$param = $this->request->param();
		//print_r($param);exit;
		$apiUrl = getUrl($param,'get');
		$get = ihttp_get($apiUrl);
		if ($get['code'] == 200) {
			$content = @json_decode($get['content'], true);
			if ($content['errno'] == 0) {
				countHits($param);
				return $this->buildSuccess($content['data']);
			}else{
				countHits($param,'get',$content['errno']);
				return $this->buildFailed($content['errno'],$content['message'],$content['data']);
			}
			
		}else{
			countHits($param,'get',$get['code']);
			return $this->buildFailed();
		}
		//print_r( request() );      
	}
	
	public function Post() {
		$param = $this->request->param();
		$api = getUrl($param, 'post');
		
		//countHits();
		$post = ihttp_post($api['apiurl'], $api['post']);
		
		if ($post['code'] == 200) {
			$content = @json_decode($post['content'], true);
			if ($content['errno'] == 0) {
				countHits($param,'post');
				return $this->buildSuccess($content['data']);
			}else{
				countHits($param,'post',$content['errno']);
				return $this->buildFailed($content['errno'],$content['message'],$content['data']);
			}
			
		}else{
			countHits($param,'post',$get['code']);
			return $this->buildFailed(-1, $post['content']);
		}
		//print_r( request() );      
	}
	
}
