<?php

namespace UserVerify\UserVerify;

use UserVerify\UserVerify\Helpers;
use Exception;

class Register
{
    private $db;

    /**
     * 构造函数
     *
     * @param object $db 数据库连接对象，用于执行数据库操作
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * 注册验证
     *
     * 验证用户注册信息，包括用户名、密码、确认密码、邮箱和验证码。如果验证通过，则将用户信息存入数据库，否则返回错误信息。
     *
     * @param array $postData 包含用户提交的POST数据
     * @param array $sessionData 包含当前会话的数据
     * @return string 返回成功信息或错误信息
     */
    public function check($postData, $sessionData)
    {
        $userName = $this->getPostParam($postData, 'RegisterUserName');
        $userPassword = $this->getPostParam($postData, 'RegisterUserPassWord', false);
        $userConfirmPassword = $this->getPostParam($postData, 'RegisterUserConfirmPassWord', false);
        $email = $this->getPostParam($postData, 'RegisterUserEmail');
        $userEmailVerificationCode = $this->getPostParam($postData, 'RegisterUserEmailVerificationCode');
        $inputVerificationCode = strtoupper($postData['RegisterUserVerificationCode'] ?? '');
        $sessionVerificationCode = strtoupper($sessionData['GenerateVerificationCode'] ?? '');

        foreach ([$userName, $userPassword, $userConfirmPassword, $email, $userEmailVerificationCode, $inputVerificationCode] as $field) {
            if (empty($field)) return $this->displaySnackbar('必填项不能为空!');
        }

        if ($inputVerificationCode !== $sessionVerificationCode) {
            return $this->displaySnackbar('验证码错误');
        }

        if (!preg_match('/^[a-zA-Z0-9-_]+$/', $userName)) {
            return $this->displaySnackbar('注册失败:用户名不能有特殊字符和中文!');
        }

        if ($userPassword !== $userConfirmPassword) {
            return $this->displaySnackbar('注册失败:两次输入的密码不一致!');
        } elseif (strlen($userPassword) < 8 || !preg_match('/[A-Za-z0-9_-]/', $userPassword) || !preg_match('/\d/', $userPassword)) {
            return $this->displaySnackbar('注册失败:密码安全性不高!');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->displaySnackbar('注册失败:邮箱格式错误!');
        }

        if (!Helpers::isEmailCaptchaExpired($sessionData)) {
            return $this->displaySnackbar('注册失败:邮箱验证码已过期!');
        }
        if (strcasecmp($userEmailVerificationCode, $sessionData['EmailVerification']['Code']) !== 0 || $sessionData['EmailVerification']['Email'] !== $email) {
            return $this->displaySnackbar('注册失败:邮箱验证码错误!');
        }

        try {
            foreach (['username' => $userName, 'email' => $email] as $field => $value) {
                $result = $this->db->DatabaseOperations("SELECT COUNT(1) as count FROM users WHERE $field = :value", [':value' => $value]);
                if ($result[0]['count'] > 0) return $this->displaySnackbar("{$field}已存在!");
            }

            $hashUserPassword = password_hash($userPassword, PASSWORD_DEFAULT);
            $this->db->DatabaseOperations(
                'INSERT INTO users (username, email, password) VALUES (:UserName, :email, :PassWord)',
                [':UserName' => $userName, ':email' => $email, ':PassWord' => $hashUserPassword]
            );

            return $this->displaySnackbar(true);
        } catch (Exception $e) {
            throw new Exception($e);
        }
    }

    private function getPostParam($postData, $key, $sanitize = true)
    {
        $value = $postData[$key] ?? '';
        return $sanitize ? trim(htmlspecialchars($value)) : $value;
    }

    private function displaySnackbar($message)
    {
        return $message;
    }
}
