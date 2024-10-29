<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Database.php';

use UserVerify\UserVerify\Register;

// 初始化数据库连接
$db = new Database(__DIR__ . '/data.db');

// 初始化注册类
$register = new Register($db);

// 模拟 POST 数据（用户输入）
$postData = [
    'RegisterUserName' => 'newuser',
    'RegisterUserPassWord' => 'securePass1',
    'RegisterUserConfirmPassWord' => 'securePass1',
    'RegisterUserEmail' => 'XXXX@XXX.XXX',
    'RegisterUserEmailVerificationCode' => '123', // 邮箱验证码
    'RegisterUserVerificationCode' => '123456'    // 图像验证码
];

// 模拟会话数据
$sessionData = [
    'GenerateVerificationCode' => '123456', // 生成的图像验证码
    'EmailVerification' => [
        'Code' => '123',                    // 邮箱验证码
        'Email' => 'XXXX@XXX.XXX',
        'SendTime' => '2024-10-29 00:40:27'
    ]
];

// 检查注册信息
$result = $register->check($postData, $sessionData);

// 输出注册结果
if ($result === true) {
    echo "注册成功";
} else {
    echo $result; // 输出错误信息
}
