<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Database.php';

use UserVerify\UserVerify\Login;

// 初始化数据库连接
$db = new Database(__DIR__ . '/data.db');

// 初始化登录类
$login = new Login($db);

// 模拟 POST 数据
$postData = [
    'LoginUserName' => 'newuser',
    'LoginUserPassWord' => 'securePass1',
    'LoginUserVerificationCode' => '123456' // 图像验证码
];

// 模拟会话数据
$sessionData = [
    'GenerateVerificationCode' => '123456' // 图像验证码
];

// 检查登录信息
$result = $login->LoginCheck($postData, $sessionData);

// 输出登录结果
if (is_string($result)) {
    exit($result); // 输出错误信息并终止程序
} else {
    var_dump($result); // 显示登录成功后的信息
    echo "登录成功";
    // 删除会话中的验证码信息
}
