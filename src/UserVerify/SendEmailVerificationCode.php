<?php

namespace UserVerify\UserVerify;

use UserVerify\UserVerify\Helpers;
use Exception;

class SendEmailVerificationCode
{
    private $helpers;
    private $db;
    private $emailConfig;

    /**
     * 构造函数
     *
     * @param object $db 数据库连接对象，用于执行数据库操作
     * @param array $emailConfig 包含电子邮件模板配置的数组，包括logo、网站名称和页脚Logo的URL
     */
    public function __construct($db, array $emailConfig)
    {
        $this->helpers = new Helpers();
        $this->db = $db;
        $this->emailConfig = $emailConfig;
    }

    /**
     * 发送邮箱验证码函数
     *
     * 发送用户注册邮箱验证码，验证邮箱格式和验证码频率。如果成功，返回验证码和发送时间；否则返回错误信息。
     *
     * @param array $postData 包含用户提交的POST数据
     * @param array $sessionData 包含当前会话的数据
     * @return array|string 成功时返回验证码信息数组，失败时返回错误信息
     */
    public function sendCode($postData, $sessionData)
    {
        $email = $postData['RegisterUserEmail'] ?? '';
        $inputVerificationCode = strtoupper($postData['RegisterUserVerificationCode'] ?? '');
        $sessionVerificationCode = strtoupper($sessionData['GenerateVerificationCode'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->displaySnackbar('请填写正确的邮件地址!');
        }
        if ($inputVerificationCode !== $sessionVerificationCode) {
            return $this->displaySnackbar('验证码错误');
        }
        if ($this->helpers::isEmailCaptchaExpired($sessionData)) {
            return $this->displaySnackbar('请勿发送太频繁!');
        }
        $query = 'SELECT COUNT(1) as count FROM users WHERE email = :email';
        $result = $this->db->DatabaseOperations($query, [':email' => $email]);
        if ($result[0]['count'] > 0) {
            return $this->displaySnackbar('邮箱账户已被使用!');
        }

        try {
            $emailVerificationCode = rand(100000, 999999);
            $mailPhpContent = file_get_contents(__DIR__ . '/../EmailVerification.html');
            $mailMessage = strtr($mailPhpContent, [
                '{logo}' => $this->emailConfig['logo'],
                '{SiteName}' => $this->emailConfig['siteName'],
                '{EmailVerificationCode}' => $emailVerificationCode,
                '{FooterLogo}' => $this->emailConfig['footerLogo']
            ]);
            $mailContent = [
                'smtp' => $this->emailConfig['smtp'],
                'MailRecipient' => $email,
                'MailSubject' => '注册邮箱验证码',
                'MailMessage' => $mailMessage
            ];

            $this->helpers->SendMail($mailContent, true);

            return [
                'Code' => $emailVerificationCode,
                'Email' => $email,
                'SendTime' => date('Y-m-d H:i:s')
            ];
        } catch (Exception $e) {
            throw new Exception($e);
        }
    }

    private function displaySnackbar($message)
    {
        return $message;
    }
}
