<?php
/**
 * Api路由
 */

use think\facade\Route;

Route::group('api', function() {
    Route::rule('5f215dc81b06f','api/Photosvote/Get', 'GET')->middleware(['ApiAuth', 'ApiPermission', 'RequestFilter', 'ApiLog']);
    Route::rule('5f2268c4d4333','api/Photosvote/Post', 'POST')->middleware(['ApiAuth', 'ApiPermission', 'RequestFilter', 'ApiLog']);
    Route::rule('5f2429a398c80','api/vbcv', 'GET')->middleware(['ApiAuth', 'ApiPermission', 'RequestFilter', 'ApiLog']);
    Route::rule('5f2570800824e','api/sss', 'GET')->middleware(['ApiAuth', 'ApiPermission', 'RequestFilter', 'ApiLog']);
    Route::rule('5f2584162639d','api/Photosvote/Get', 'GET')->middleware(['ApiAuth', 'ApiPermission', 'RequestFilter', 'ApiLog']);
    Route::rule('5f258416263a0','api/Photosvote/Post', 'POST')->middleware(['ApiAuth', 'ApiPermission', 'RequestFilter', 'ApiLog']);
    Route::rule('5f26b9a87f2fc','api/Photosvote/Get', 'GET')->middleware(['ApiAuth', 'ApiPermission', 'RequestFilter', 'ApiLog']);
    Route::rule('5f26b9a87f2ff','api/Photosvote/Post', 'POST')->middleware(['ApiAuth', 'ApiPermission', 'RequestFilter', 'ApiLog']);
    Route::rule('5f26bbebe793c','api/Start/Index', 'POST')->middleware(['ApiAuth', 'ApiPermission', 'RequestFilter', 'ApiLog']);
    Route::rule('5f26d42c03536','api/Photosvote/Get', 'GET')->middleware(['ApiAuth', 'ApiPermission', 'RequestFilter', 'ApiLog']);
    Route::rule('5f26d42c03539','api/Photosvote/Post', 'POST')->middleware(['ApiAuth', 'ApiPermission', 'RequestFilter', 'ApiLog']);
    Route::rule('5f26d4efe9f00','api/Photosvote/Post', 'POST')->middleware(['ApiAuth', 'ApiPermission', 'RequestFilter', 'ApiLog']);
    Route::rule('5f26e32b743af','api/Photosvote/Get', 'GET')->middleware(['ApiAuth', 'ApiPermission', 'RequestFilter', 'ApiLog']);
    Route::rule('5f26e32b743b1','api/Photosvote/Post', 'POST')->middleware(['ApiAuth', 'ApiPermission', 'RequestFilter', 'ApiLog']);
    Route::rule('5f26e3ca4e415','api/Photosvote/Get', 'GET')->middleware(['ApiAuth', 'ApiPermission', 'RequestFilter', 'ApiLog']);
    Route::rule('5f26e3ca4e417','api/Photosvote/Post', 'POST')->middleware(['ApiAuth', 'ApiPermission', 'RequestFilter', 'ApiLog']);
    //MISS路由定义
    Route::miss('api/Miss/index');
})->middleware('ApiResponse');
