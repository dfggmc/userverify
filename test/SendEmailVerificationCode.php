<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Database.php';

use UserVerify\UserVerify\SendEmailVerificationCode;

// 初始化数据库连接
$db = new Database(__DIR__ . '/data.db');

// 邮件配置
$emailConfig = [
    'smtp' => [
        'Host' => '',        // 邮箱服务器地址
        'Username' => '',    // 邮箱用户名
        'Password' => '',    // 授权码
        'SMTPSecure' => '',  // 加密方式
        'Port' => '',        // 端口号
        'FromEmail' => '',   // 发件人邮箱
        'FromName' => ''     // 发件人姓名
    ],
    'logo' => 'https://example.com/logo.png',      // 站点 logo
    'siteName' => 'MySite',                        // 站点名称
    'footerLogo' => 'https://example.com/footer-logo.png' // 页脚 logo
];

// 初始化发送验证码类
$emailSender = new SendEmailVerificationCode($db, $emailConfig);

// 模拟 POST 数据
$postData = [
    'RegisterUserEmail' => 'q1432777209@126.com',
    'RegisterUserVerificationCode' => '123456'
];

// 模拟会话数据
$sessionData = [
    'GenerateVerificationCode' => '123456', // 图像验证码
    'EmailVerification' => [
        'SendTime' => '2024-10-29 00:40:27'
    ]
];

// 发送验证码
$result = $emailSender->sendCode($postData, $sessionData);

// 检查发送结果
if (is_array($result)) {
    echo "验证码已发送";
    var_dump($result); // 显示验证码信息
} else {
    echo $result; // 显示错误信息
}
