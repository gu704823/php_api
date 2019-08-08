<?php
/**
 * Created by PhpStorm.
 * User: Swift
 * Date: 2019/7/31
 * Time: 0:24
 */
namespace app\commom\validate;
use think\Validate;
class User extends Validate
{
    //验证规则
  protected $rule = [
      'user_name|用户名'=>'require',
      'user_pwd|密码'=>'require|length:32',

      'user_init_pwd|密码'=>'require|length:32',

      'username|用户名'=>'require',
      'is_exist|用户是否存在'=>'require|length:1',

      'code|验证码'=>'require|number|length:6',

      'user_id|用户ID'=>'require|number',
      'user_icon|用户头像'=>'require|image|fileSize:2000000|fileExt:jpeg,jpg,png',

      'phone'|'用户注册手机号'=>['require','regex'=>'/^1[34578]\d{9}$/'],
      'email'|'用户注册邮箱'=>'require'|'email',

      'user_nickname'|'用户昵称'=>'require'|'chsDash',

  ];
  //登录场景
  protected  function sceneLogin(){
      return $this->only(['user_name','user_pwd']);
  }
  //验证码场景
    protected  function sceneGet_code(){
        return $this->only(['username','is_exist']);
    }
    //注册场景
    protected  function  sceneRegister(){
      return $this->only(['username','user_pwd','code']);
    }
    //用户头像上传场景
    protected function sceneUser_head_icon(){
      return $this->only(['user_icon,user_id']);
    }
    //修改密码
    protected function sceneChange_pwd(){
        return $this->only(['user_name,user_init_pwd,user_pwd']);
    }
    //找回密码
    protected function sceneFind_pwd(){
        return $this->only(['user_name,code,user_pwd']);
    }
    //绑定用户邮箱,密码
    protected function sceneBind_user_email_phone(){
      return $this->only(['user_id','phone','email','code','user_pwd']);
    }
    //用户昵称
    protected function sceneUser_nickname(){
        return $this->only(['user_id','user_nickname']);
    }

}
