<?php
class Database
{
    private $pdo;

    /**
     * 构造函数，初始化 SQLite 数据库连接
     *
     * @param string $dbPath SQLite 数据库文件的路径
     */
    public function __construct($dbPath)
    {
        $this->pdo = new PDO("sqlite:" . $dbPath);
        // 设置异常模式以便于调试
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * 执行数据库操作
     *
     * @param string $query SQL 查询字符串
     * @param array $params 查询参数
     * @return array 查询结果数组
     */
    public function DatabaseOperations($query, $params = [])
    {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
