<?php
/**
 * Created by PhpStorm.
 * User: hansh
 * Date: 2019-07-31
 * Time: 14:01
 */
namespace app\api\model;
use think\Model;
class User extends Model
{
     public function check_exist($username){

         $phone_res = $this->where('user_phone',$username)->find();
         $email_res = $this->where('user_email',$username)->find();
         $data['phone_res']=$phone_res;
         $data['email_res'] = $email_res;
         return $data;
    }
     public function user_register($data){
         $result = $this->allowField(true)->save($data);;
         if($result){
             return 1;
         }else{
             return $result;
         }
     }
     public function user_login($user_name,$type){
         if($type=='phone'){
             $result = $this->field('user_name,user_id,user_phone,user_email,user_rtime,user_pwd')->where('user_phone',$user_name)->find();
         }else{
             $result = $this->field('user_name,user_id,user_phone,user_email,user_rtime,user_pwd')->where('user_email',$user_name)->find();
         }
         return $result;
     }




}