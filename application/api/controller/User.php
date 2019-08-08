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

    //上传用户头像
    public function user_icon(){
        //validate
        $data = $this->check_params('user_head_icon',request()->param(true));
        //

        $head_img_path = $this->upload_file($data['user_icon'],'head_img');
        $data['user_icon']=$head_img_path;
        $result = model('User')->upload_head_icon($data);
        if($result==1){
            $this->return_msg(200,'用户头像上传成功',$head_img_path);
        }else{
            $this->return_msg(400,'用户头像上传失败');
        }


    }

    //用户修改密码（token，time，user_name,user_ini_pwd,user_new_pwd）
    public function change_pwd()
    {
        $data = $this->check_params('change_pwd', request()->except(['time', 'token']));
        //检测用户名是手机号还是邮箱
        $user_name_type = $this->checkUsername($data['user_name']);
        switch ($user_name_type) {
            case 'phone':
                $this->check_exist($data['user_name'],'phone',1);
                $where['user_phone']=$data['user_name'];
                break;
            case 'email':
                $this->check_exist($data['user_name'],'email',1);
                $where['user_email']=$data['user_name'];
                break;
        }
        //判断原始密码是否正确
        $db_ini_pwd = model('User')->query_user_pwd($where);
        if($db_ini_pwd!==$data['user_init_pwd']){
            $this->return_msg(200,'你输入原始密码不正确');
        }
        //注入新密码
        $res = model('User')->update_user_pwd($where,$data['user_new_pwd']);
        if($res !== false){
            $this->return_msg(200,'密码修改成功');
        }else{
            $this->return_msg(400,'密码修改失败');
        }
    }

    //用户找回密码 user_name,code,user_pwd
    public function  find_pwd(){
        $data = $this->check_params('find_pwd', request()->except(['time', 'token']));
        //检测验证码
        $this->check_code($data['user_name'],$data['code']);
        //检测用户名
        $user_name_type = $this->checkUsername($data['user_name']);
        switch ($user_name_type){
            case 'phone':
                $this->check_exist($data['user_name'],'phone',1);
                $where['user_phone']=$data['user_name'];
                break;
            case 'email':
                $this->check_exist($data['user_name'],'email',1);
                $where['user_email']=$data['user_name'];
                break;
        }
        //注入新密码
        $res = model('User')->update_user_pwd($where,$data['user_pwd']);
        if($res !== false){
            $this->return_msg(200,'密码修改成功');
        }else{
            $this->return_msg(400,'密码修改失败');
        }

    }

   //绑定邮箱,手机号 user_id,user_name,code
    public function bind_username(){
        $user_type = [];
        $update_data = [];
        $data = $this->check_params('sceneBind_user_email_phone',request()->except(['time','token']));
        $this->check_code($data['user_name'],$data['code']);
        //检测$data['user_name']是手机号还是邮箱
        $user_name_type = $this->checkUsername($data["user_name"]);
        switch ($user_name_type){
            case 'phone':
                $update_data['user_email'] = $data['user_name'];
                $user_type['type'] = '用户手机';
                break;
            case 'email':
                $update_data['user_phone'] = $data['user_name'];
                $user_type['type'] = '用户邮箱';
                break;
        }
        $res = model('User')->bind_username($data,$update_data);
        if($res==1){
            $this->return_msg('200',$user_type['type'].'绑定成功');
        }else{
            $this->return_msg('400',$user_type['type'].'绑定失败');
        }
    }

    //用户设定昵称 user_id,user_nickname
    public function nickname(){
       $data = $this->check_params('user_nickname',request()->except(['time','token']));
       $res = model('User')->setNickname($data);
       switch ($res){
           case 1:
               $this->return_msg('400','该昵称已被占用');
               break;
           case 2:
               $this->return_msg('200','修改昵称成功');
               break;
           case 3:
               $this->return_msg('400','修改昵称失败');
               break;
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
//checkx