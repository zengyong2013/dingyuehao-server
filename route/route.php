<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// 用户端
Route::group('web', function(){
    Route::post('user/add', 'index/Index/addUser');
    Route::post('activity/get', 'index/Index/getActivity');
    Route::post('story/get', 'index/Index/getStoryByUserIdAndActivityId');
    Route::post('story/match', 'index/Index/matchStory');
    Route::post('story/add', 'index/Index/addStory');
});

// 管理端
Route::post('admin/login', 'index/Admin/login');
Route::group('admin', function(){
    Route::get('story/list', 'index/Admin/getStoryList');
    Route::get('story/detail', 'index/Admin/getStoryDetail');
    Route::get('activity/list', 'index/Admin/getActivityList');
    Route::get('activty/detail', 'index/Admin/getActivityDetail');

    Route::post('reset', 'index/Admin/updatePassword');
    Route::post('activity/add', 'index/Admin/addActivity');
    Route::post('activity/delete', 'index/Admin/deleteActivity');
    Route::post('activity/update', 'index/Admin/updateActivity');
})->middleware(app\http\middleware\CheckToken::class);
Route::allowCrossDomain();


return [];
