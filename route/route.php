<?php
//登录
Route::rule('user/login','api/user/login','get');
//获取验证码
Route::rule('code','api/code/get_code','post');
//注册
Route::rule('user/register','api/user/register','post');
//用户头像
Route::rule('user/user_icon','api/user/user_icon','post');
//用户修改密码
Route::rule('user/change_pwd','api/user/change_pwd','post');
//用户找回密码
Route::rule('user/find_pwd','api/user/find_pwd','post');
//用户绑定邮箱手机号码
Route::rule('user/bind_username','api/user/bind_username','post');
//用户设定昵称
Route::rule('user/nickname','api/user/nickname','post');

/*文章*/
//设备详情
Route::rule('wx/index','api/wx/index','get|post');


