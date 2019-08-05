<?php
/**
 * Created by PhpStorm.
 * User: Swift
 * Date: 2019/7/30
 * Time: 22:17
 */
namespace app\api\controller;
use think\Controller;
use think\facade\Validate;
use think\facade\Env;
use think\Image;

define('ROOTPATH',Env::get('root_path'));
define('DS',DIRECTORY_SEPARATOR);

class Common extends Controller
{
    protected $request;
    protected $params;//过滤后符合要求的参数
    protected function initialize(){
        $this->request = request();
        //$request->only(['time'])请求参数：仅包含time
        //验证请求时间
        //$this->check_time($this->request->only(['time']));
        //验证token
        //$request->param()请求所有参数
       // $this->check_token($this->request->param());
        //验证参数过滤
        //request->except(['time','token'])剔除time，token
    }
    public function check_time($arr){
        //intval()函数用于获取变量的整数值。需求返回是一窜数字
        if(!isset($arr['time'])||intval($arr['time'])<=1){
          $this->return_msg(400,'时间戳不正确',['请添加当前时间戳再来访问']);
        }
        if(time()-intval($arr['time'])>60){
           $this->return_msg(400,'请求超时',['超过60s']);
        }
    }
    public function check_token($arr){
        if(!isset($arr['token'])||empty($arr['token'])){
            $this->return_msg(400,'token为空',['token不能为空！！！']);
        }
        $api_token = $arr['token'];
        $service_token = '';
        //服务器生成token
        unset($arr['token']);//删除请求的token
        foreach ($arr as $key=>$value){
            $service_token .=md5($value);
        }
        $service_token = md5('api'.$service_token.'api');
       // echo $service_token;die;
        if($service_token!==$api_token){
            $this->return_msg(400,'token值不正确',['前端token值不正确']);
        }
    }
    public function return_msg($code,$msg='',$data=[]){
        $return_data['code']=$code;
        $return_data['msg']=$msg;
        $return_data['reason']=$data;
        echo json_encode($return_data);
        die;
    }
    //验证用户名是手机还是email
    public function checkUsername($user_name)
    {
        $is_email = Validate::isEmail($user_name) ? 1:0;
        $is_phone = preg_match('/^1[345678]\d{9}$/',$user_name) ? 4:2;
        $flag= $is_email+$is_phone;
        switch ($flag){
            case 2:
                $this->return_msg('400','邮箱,手机号错误');
                break;
            case 3:
                return 'email';
                break;
            case 4:
                return 'phone';
                break;
        }
    }
    //检测是否超时
    public function check_code($user_name,$code){
       $last_time = session($user_name.'_last_send_time');
       if(time()-$last_time>600000){
           $this->return_msg(400,'验证超时，请在一分钟内验证');
       }
       $md5_code = md5($user_name.'_'.md5($code));
       if(session($user_name.'_code'!==$md5_code)){
           $this->return_msg(400,'验证码不正确');
       }
       session($user_name.'_code',null);

    }
    //检测是否存在
    public function check_exist($username,$type,$exist){
        $type_num = $type == 'phone' ? 2:4;
        $flag = $type_num + $exist;
        $result = model('User')->check_exist($username);
        $phone_res = $result['phone_res'];
        $email_res = $result['email_res'];
        switch ($flag){
            case 2:
                if($phone_res){
                    $this->return_msg(400,'手机号被占用');
                }
                break;
            case 3:
                if(!$phone_res){
                    $this->return_msg(400,'手机号不存在');
                }
                break;
            case 4:
                if($email_res){
                    $this->return_msg(400,'邮箱被占用');
                }
                break;
            case 5:
                if(!$email_res){
                    $this->return_msg(400,'邮箱不存在');
                }
                break;
        }
    }
    //上传文件
    public function upload_file($file,$type=''){
       $info  = $file->move(ROOTPATH.'public'.DS.'uploads');
       if($info){
           $path = '/uploads/'.$info->getSaveName();
           if(!empty($type)){
               $this->image_edit($path,$type);
               return str_replace('\\','/',$path);
           }else{
               $this->return_msg(400,$file->getError());
           }
       }
    }
    //裁剪图片
    public function image_edit($path,$type){
        $image = Image::open(ROOTPATH.'public'.$path);
        switch ($type){
            case 'head_img':
                $image->thumb(200,200,Image::THUMB_CENTER)->save(ROOTPATH.'public'.$path);
                break;
        }

    }
}