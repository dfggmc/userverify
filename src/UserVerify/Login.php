<?php

namespace UserVerify\UserVerify;

use Exception;

class Login
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
     * 登录验证函数
     *
     * 验证用户登录信息，包括用户名、密码和验证码。如果验证通过，则返回用户数据；否则返回错误信息。
     *
     * @param array $postData 包含用户提交的POST数据
     * @param array $sessionData 包含当前会话的数据
     * @return string|array 返回用户信息的JSON字符串或错误信息
     */
    public function LoginCheck($postData, $sessionData)
    {
        $userName = $this->getPostParam($postData, 'LoginUserName');
        $password = $this->getPostParam($postData, 'LoginUserPassWord');
        $inputVerificationCode = strtoupper($this->getPostParam($postData, 'LoginUserVerificationCode'));

        if (empty($userName) || empty($password) || empty($inputVerificationCode)) {
            return $this->displaySnackbar('登录失败:必填项不能为空');
        }

        if ($inputVerificationCode !== strtoupper($sessionData['GenerateVerificationCode'] ?? '')) {
            return $this->displaySnackbar('登录失败:验证码错误');
        }

        try {
            $query = 'SELECT user_id, username, password FROM users WHERE username = :name';
            $result = $this->db->DatabaseOperations($query, [':name' => $userName]);
            $this->db =null;
            if (empty($result)) {
                return $this->displaySnackbar('登录失败:用户不存在');
            }

            $storedPassword = $result[0]['password'];
            if (!password_verify($password, $storedPassword)) {
                return $this->displaySnackbar('登录失败:密码错误');
            }

            $loginStatusMatchingCode = bin2hex(random_bytes(32));
            $userData = json_encode([
                'UserName' => $userName,
                'UserId' => $result[0]['user_id'],
                'LoginToken' => $loginStatusMatchingCode
            ]);

            return $userData;
        } catch (Exception $e) {
            throw new Exception($e);
        }
    }

    private function getPostParam($postData, $key)
    {
        return trim($postData[$key] ?? '');
    }

    private function displaySnackbar($message)
    {
        return $message;
    }
}
