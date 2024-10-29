<?php

namespace UserVerify\UserVerify;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * 助手
 */
class Helpers
{
    /**
     * 邮件发送
     * 
     * MailRecipient -> 收件人地址
     * MailSubject -> 邮件标题
     * MailMessage -> 邮件内容
     * 
     * @param mixed $MailJsonContent 邮件json数组
     * @param mixed $debug 是否启用debug
     * @throws Exception 发送失败
     */
    public function SendMail($MailJsonContent, $debug = null)
    {
        // 创建一个新的PHPMailer实例
        $mail = new PHPMailer(true);
        $mail->setLanguage('zh_cn');
        //debug
        if ($debug) {
            $mail->SMTPDebug = 3;
        }
        try {
            // 配置SMTP参数
            $mail->isSMTP();
            $mail->SMTPAuth = true;
            $mail->Host = $MailJsonContent['smtp']['Host'];
            $mail->Username = $MailJsonContent['smtp']['Username']; // 邮箱地址
            $mail->Password = $MailJsonContent['smtp']['Password']; // 邮箱授权码
            $mail->SMTPSecure = $MailJsonContent['smtp']['SMTPSecure']; //加密方法
            $mail->Port = $MailJsonContent['smtp']['Port']; //端口

            // 设置发件人和收件人
            $mail->setFrom($MailJsonContent['smtp']['FromEmail'], $MailJsonContent['smtp']['FromName']); // 发件人地址和名称
            $mail->addAddress($MailJsonContent['MailRecipient']); // 收件人地址

            $mail->Subject = $MailJsonContent['MailSubject']; //设置邮件
            $mail->Body = $MailJsonContent['MailMessage']; //邮件内容

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';

            // 发送邮件
            $mail->send();
            return true;
        } catch (Exception $e) {
            throw new Exception('邮件发送失败: ' . $e->getMessage());
        }
    }

    /**
     * 检查邮箱验证码是否过期
     * 
     * @return bool true 没有过期
     * @return bool false 已过期
     */
    public static function isEmailCaptchaExpired($sessionData)
    {
        // 获取当前时间戳
        $currentTime = time();

        // 检查上次发送时间是否存在于 session 中
        if (isset($sessionData['EmailVerification']['SendTime'])) {
            $lastSendTime = strtotime($sessionData['EmailVerification']['SendTime']);

            // 判断时间差是否小于10分钟
            return ($currentTime - $lastSendTime) > 600; // 600 秒 = 10 分钟
        }

        // 如果没有发送时间，则视为未过期
        return false;
    }
}
