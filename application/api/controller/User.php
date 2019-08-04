<?php
/**
 * Created by PhpStorm.
 * User: Swift
 * Date: 2019/7/29
 * Time: 21:53
 */
namespace app\api\controller;
use think\Controller;
class User extends Common
{
    //登录
    public function login()
    {
        //validate
        $data = $this->check_params('login', request()->except(['time', 'token']));
        $user_name_type = $this->checkUsername($data['user_name']);
        switch ($user_name_type){
            case 'phone':
                $this->check_exist($data['user_name'],'phone',1);
                $result = model('User')->user_login($data['user_name'],'phone');
                break;
            case 'email':
                $this->check_exist($data['user_name'],'email',1);
                $result = model('User')->user_login($data['user_name'],'email');
                break;
        }
        if($result['user_pwd']!==$data['user_pwd']){
            $this->return_msg(400,'用户名或者密码错误');
        }else{
            unset($result['user_pwd']);
            $this->return_msg(200,'登录成功',$result);
    }

    }

    //注册
    public function register()
    {
        //validate
        $data = $this->check_params('register',request()->except(['time','token']));
        //检测验证码
        $this->check_code($data['username'],$data['code']);
        //检测用户名
        $user_name_type = $this->checkUsername($data['username']);
        switch ($user_name_type){
            case 'phone':
                $data['user_phone'] = $data['username'];
                break;
            case 'email':
                $data['user_email'] = $data['username'];
                break;
        }
        unset($data['username']);
        $data['user_rtime'] = time();
        $result = model('User')->user_register($data);
        if($result==1){
            $this->return_msg(200,'用户注册成功');
        }else{
            $this->return_msg(400,'用户注册失败'.$result);
        }
    }
    public function user_icon(){
        //validate
        $data = $this->check_params('user_head_icon',request()->param(true));
        //
        $head_img_path = $this->upload_file($data['user_icon'],'head_img');
        $head_icon['user_id']=$data['user_id'];
        $head_icon['user_icon']=$head_img_path;
        $result = model('User')->upload_head_icon($head_icon);
        if($result==1){
            $this->return_msg(200,'用户头像上传成功',$head_img_path);
        }else{
            $this->return_msg(400,'用户头像上传失败');
        }


    }



//validate 场景
    public function check_params($check_scene, $arr)
    {
        $validate = new \app\commom\validate\User();
        if (!$validate->scene($check_scene)->check($arr)) {
            $this->return_msg(400, $validate->getError
            ());
        }
        return $arr;
    }
}
//check