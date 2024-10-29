# UserVerify Library

用户验证库，集成了用户注册、登录、和邮箱验证的功能。

## 安装

通过 Composer 安装：

```bash
composer require xiaofeng/userverify
```

## 使用：
确保有一个名为``users``表表结构中必须有以下字段
```sql
CREATE TABLE users (
    user_id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL
);
```
使用方法在``test``文件夹中，文件内部已做好详细注释
