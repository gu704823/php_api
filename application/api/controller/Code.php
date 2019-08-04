<?php
/**
 * Created by PhpStorm.
 * User: hansh
 * Date: 2019-07-31
 * Time: 10:15
 */

namespace app\api\controller;

class Code extends Common
{
  public function get_code(){
      $params = $this->check_params_code(request()->except(['time','token']));

      $user_name = $params['username'];
      $is_exit = $params['is_exist'];
      $username_type = $this->checkUsername($user_name);
      switch ($username_type){
          case 'phone':
              $this->get_code_by_username($user_name,'phone',$is_exit);
              break;
          case 'email':
              $this->get_code_by_username($user_name,'email',$is_exit);
              break;
      }

  }
  public function get_code_by_username($user_name,$type,$exist){
      if($type=='phone'){
          $type_name = '手机';
      }else{
          $type_name = '邮箱';
      }
      $this->check_exist($user_name,$type,$exist);

//判断（当前作用域）是否赋值
//session('?name');
      if(session("?".$user_name.'_last_send_time')){
          if(time()-session($user_name.'_last_send_time')<60){
              $this->return_msg(400,$type_name.'验证码,每60s只能发送一次');
          }
      }
      $code= $this->make_code(6);
      $md5_code = md5($user_name.'_'.md5($code));
      session($user_name.'_code',$md5_code);
      session($user_name.'_last_send_time',time());
      if($type=='phone'){
          $this->send_code_to_phone($user_name,$code);
      }else{
          $this->send_code_to_email($user_name,$code);
      }
  }
  //随机数,位数$num
  public function make_code($num){
      $min = pow(10,$num-1);
      $max = pow(10,$num)-1;
     return mt_rand($min,$max);
  }
  //
    public function send_code_to_phone($user_name,$code){
      echo 'send_code_to_phone';
    }
    public function send_code_to_email($user_name,$code){
        $title = '验证码:';
        $content = '验证码为:';
        $result =  mailto($user_name,$code,$title,$content);
        if($result==1){
            $this->return_msg(200,'邮件发送成功');
        }else{
            $this->return_msg(400,$result);
        }
    }

  public function check_params_code($arr)
  {
      $validate = new \app\commom\validate\User();
      if(!$validate->scene('Get_code')->check($arr)){
          $this->return_msg(400,$validate->getError
          ());
      }
      return $arr;
  }
}