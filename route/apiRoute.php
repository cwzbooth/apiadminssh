<?php
/**
 * Api路由
 */

use think\facade\Route;

Route::group('api', function() {
    Route::rule('5f215dc81b06f','api/Photosvote/Get', 'GET')->middleware(['ApiAuth', 'ApiPermission', 'RequestFilter', 'ApiLog']);
    Route::rule('5f2268c4d4333','api/Photosvote/Post', 'POST')->middleware(['ApiAuth', 'ApiPermission', 'RequestFilter', 'ApiLog']);
    Route::rule('5f292e30bb7db','api/Photosvote/Get', 'GET')->middleware(['ApiAuth', 'ApiPermission', 'RequestFilter', 'ApiLog']);
    Route::rule('5f292e30bb7de','api/Photosvote/Post', 'POST')->middleware(['ApiAuth', 'ApiPermission', 'RequestFilter', 'ApiLog']);
    //MISS路由定义
    Route::miss('api/Miss/index');
})->middleware('ApiResponse');
