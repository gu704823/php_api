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
     public function upload_head_icon($data){
         $result = $this->where('user_id',$data['user_id'])->setField('user_icon',$data['user_icon']);
         if($result){
             return 1;
         }else{
             return $this->getError();
         }

     }
     public function query_user_pwd($data){
         return $this->where($data)->value('user_pwd');
     }
    public function update_user_pwd($data1,$data2){
        $result =  $this->where($data1)->setField('user_pwd',$data2);
        return $result;
    }
    public function bind_User_name($data1,$data2){
         $result = $this->where('user_id',$data1['user_id'])->update($data2);
         if($result!==false){
            return 1;
        }
    }
    public function setNickname($data){
         $result = $this->where('user_nickname',$data['user_nickname'])->find();
         if($result){
             return 1;
         }else{
             $result = $this->where('user_id',$data['user_id'])->setField('user_nickname',$data['user_nickname']);
             if($result){
                 return 2;
             }else{
                 return 3;
             }
         }


    }




}