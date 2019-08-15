<?php
/**
 * Created by PhpStorm.
 * User: Swift
 * Date: 2019/8/15
 * Time: 21:45
 */

namespace app\api\controller;
use think\Controller;


class Wx extends  Common
{
public function index(){
    $param =  request()->except(['time', 'token']);

    $url = 'https://api.weixin.qq.com/sns/jscode2session';
    //参数
    $data['appid'] = 'wx2f87cd3db4567f3d';
    $data['secret'] = '2332ba69371d2944fa027f27147c50bf';
    $data['js_code'] = $param['code'];
    $data['grant_type']='authorization_code';
    $user_phone = $param['user_phone'];
    $arr = $this->httpCurl($url, $data, 'POST');
    $arr = json_decode($arr,true);
    print_r($arr);
   //判断是否获取到session_key 和 openid
if(isset($arr['errcode'])&&!empty($arr['errcode'])){
    $this->return_msg('2',$arr['errmsg'],[]);
}
    $open_id=$arr['openid'];
    $session_key = $arr['session_key'];

}

    function httpCurl($url, $params, $method = 'POST', $header = array(), $multi = false)
    {
        date_default_timezone_set('PRC');
        $opts = array(
            CURLOPT_TIMEOUT => 30,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_COOKIESESSION => true,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_COOKIE => session_name() . '=' . session_id(),
        );
        /* 根据请求类型设置特定参数 */
        switch (strtoupper($method)) {
            case 'GET':
                // $opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
                // 链接后拼接参数  &  非？
                $opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
                break;
            case 'POST':
                //判断是否传输文件
                $params = $multi ? $params : http_build_query($params);
                $opts[CURLOPT_URL] = $url;
                $opts[CURLOPT_POST] = 1;
                $opts[CURLOPT_POSTFIELDS] = $params;
                break;
            default:
                throw new Exception('不支持的请求方式！');
        }
        /* 初始化并执行curl请求 */
        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        $data = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if ($error) throw new Exception('请求发生错误：' . $error);
        return $data;
    }

}