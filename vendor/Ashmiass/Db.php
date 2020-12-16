<?php
namespace Ashmiass;

use Exception;
use PDO;

class Db
{
    /**
     * @var PDO
     */
    private $pdo;

    public function __construct($conf)
    {
        $dsn = "{$conf['prefix']}:host={$conf['host']};dbname={$conf['dbname']};charset=utf8";
        $this->pdo = new PDO($dsn, $conf['user'], $conf['pass']);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if (!$this->pdo) {
            throw new Exception('Error while connection to database establising');
        }
    }

    public function saveRaiting($data)
    {
        $sql = "INSERT INTO `categories` (`id`, `title`, `url`) VALUES (NULL, :title, :url); ";
        $sth = $this->pdo->prepare($sql);
        return $sth->execute([]);
    }

    public function saveCategory($data)
    {
        $sql = "INSERT IGNORE INTO `categories` (`id`, `title`, `url`) VALUES (NULL, :title, :url); ";
        $sth = $this->pdo->prepare($sql);
        return $sth->execute([
            ':title' => $data['title'],
            ':url' => $data['url']
        ]);
    }
    public function getCategoryByUrl($url)
    {
        $sql = "SELECT `id`, `title`, `url` FROM `categories` WHERE `url` = :url LIMIT 1; ";
        $sth = $this->pdo->prepare($sql);
        $sth->execute([
            ':url' => $url
        ]);
        return $sth->fetch(\PDO::FETCH_ASSOC);
    }
}
