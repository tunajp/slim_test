<?php

/*
 * Database
 *
 * @author Mitsunori Inaba <m-inaba@phoenixdesign.jp>
 * Copyright(C) 2013 DesignStudioPhoenix Corporation. All Rights Reserved.
 */

namespace PhoenixDesign\Lib;

/**
 * Database
 *
 * @author Mitsunori Inaba <m-inaba@phoenixdesign.jp>
 */
class Database implements BaseDatabase
{
    /**
     * PDOオブジェクト
     * @var PDO
     */
    private static $cnx;
    
    /**
     * singleton
     * @var type
     */
    private static $instance;

    /**
     * コンストラクタ
     *
     * @throws Exception
     */
    private function __construct()
    {
        try {
            //$dsn = 'mysql:dbname=' . Config::$DBNAME . ';host=' . Config::$DBHOST;
            //5.3.6以降
            $dsn = 'mysql:dbname=' . \Config::$DBNAME . ';host=' . \Config::$DBHOST . ";charset=utf8;";
            //\PDO::ATTR_PERSISTENT => true
            self::$cnx = new \PDO(
                $dsn,
                \Config::$DBUSER,
                \Config::$DBPASSWORD,
                array(
                    \PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET `utf8`"
                )
            );
            if (self::$cnx == null) {
                throw new \Exception('mysql::PDO - データベースと接続に失敗しました');
            }
            self::$cnx->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION); // 例外送出
        } catch (\Exception $excep) {
            throw new \Exception($excep->getMessage());
        }

        //\PhoenixDesign\Lib\Error::error_log('mysql::PDO - データベースと接続が開始されました...');
        
        try {
            $query = "set names utf8";
            self::$cnx->query($query);
        } catch (\Exception $excep) {
            throw new \Exception($excep->getMessage());
        }
    }
    
    /**
     * デストラクタ
     */
    public function __destruct()
    {
        //\PhoenixDesign\Lib\Error::error_log('mysql::PDO - データベースと接続を終了します...');
        self::$cnx = null;
    }
    
    /**
     * インスタンス取得
     *
     * @return pdo
     */
    public static function getInstance()
    {
        if (self::$cnx == null) {
            self::$instance = new Database();
        }
        //return self::$instance;
        return self::$cnx;
    }
    
    /**
     * このインスタンスの複製を許可しないようにする
     *
     * @throws RuntimeException
     */
    final public function __clone()
    {
        throw new RuntimeException('cloneは生成できません' . get_class($this));
    }

    /**
     * 条件を指定せずにテーブルのデータ数を取得します
     *
     * @param type $table_name
     * @return type
     * @throws \Exception
     */
    public static function getAllCount($table_name)
    {
        $pdo = self::getInstance();
        $sql = "select count(*) as count from " . $table_name;
        $stmt = $pdo->prepare($sql);
        $flag = $stmt->execute();
        $result_arr = array();
        if ($flag) {
            $result_arr = $stmt->fetchAll();
        } else {
            throw new \Exception("エラー" . $stmt->errorInfo());
        }
        $max = 0;
        if (count($result_arr) > 0) {
            foreach ($result_arr as $user) {
                $max = $user['count'];
            }
        }
        return $max;
    }
}
