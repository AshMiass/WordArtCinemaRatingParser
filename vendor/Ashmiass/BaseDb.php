<?php
namespace Ashmiass;

class BaseDb
{
    /**
     * @var \PDO
     */
    protected $pdo;

    public function __construct($conf)
    {
        $dsn = "{$conf['prefix']}:host={$conf['host']};dbname={$conf['dbname']};charset=utf8";
        $this->pdo = new \PDO($dsn, $conf['user'], $conf['pass']);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        if (!$this->pdo) {
            throw new \Exception('Error while connection to database establising');
        }
    }

    /**
     * @return PDOStatement
     */
    public function executeSql(string $sql, array $params = [])
    {
        $sth = $this->pdo->prepare($sql);
        $sth->execute($params);
        return $sth;
    }

    public function getErrorCode()
    {
        return $this->pdo->errorCode();
    }
}
