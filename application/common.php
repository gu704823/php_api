<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function mailto($userMail,$code='',$title,$content){
    $mail = new PHPMailer(true);
    try{
        //发件人信息
        $mail->SMTPDebug= 0;
        $mail->CharSet = 'utf-8';
        $mail->isSMTP();
        $mail->Host = 'smtp.163.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'gu704823@163.com';
        $mail->Password = 'gjb704gjb';
        $mail->SMTPSecure ='ssl';
        $mail->Port = 465;
        //发件人地址，姓名
        $mail->setFrom('gu704823@163.com','swift');
        //发给谁
        $mail->addAddress($userMail);
        $mail->isHTML(true);
        //发送标题
        $mail->Subject = $title;
        //发送内容
        $mail->Body = $content.$code;
        $mail->send();
        return 1;

    }catch (Exception $e){
         return $mail->ErrorInfo;
    }

}