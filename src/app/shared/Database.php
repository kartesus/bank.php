<?php
namespace app\shared;

class Database
{
    private static $instance;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new \PDO('sqlite:' . __DIR__ . '/../db.sqlite');
        }
        return self::$instance;
    }

    public function execute($sql, $params = [])
    {
        $stmt = self::getInstance()->prepare($sql);
        $result = $stmt->execute($params);
        if ($result === false) {
            throw new \Exception('Error executing query');
        }
    }

    public function query($sql, $params = [])
    {
        $stmt = self::getInstance()->prepare($sql);
        $result = $stmt->execute($params);
        if ($result === false) {
            throw new \Exception('Error executing query');
        }
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function queryRow($sql, $params = [])
    {
        $stmt = self::getInstance()->prepare($sql);
        $result = $stmt->execute($params);
        if ($result === false) {
            throw new \Exception('Error executing query');
        }
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

}