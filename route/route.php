<?php
//登录
Route::rule('user','api/user/login','get|post');
//获取验证码
Route::rule('code','api/code/get_code','get|post');
//注册
Route::rule('register','api/user/register','get|post');
//登录
Route::rule('login','api/user/login','get|post');
//用户头像
Route::rule('user_icon','api/user/user_icon','post');

