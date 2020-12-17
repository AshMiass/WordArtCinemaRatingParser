<?php
namespace Ashmiass;

use DateTime;

class ApiDb extends BaseDb
{
    
    public function getFilm(int $film_id)
    {
        $sql = "SELECT * FROM `films` WHERE `id` = :film LIMIT 1";
        $stm = $this->pdo->prepare($sql);
        $stm->execute([':film' => $film_id]);
        return $stm->fetch(\PDO::FETCH_ASSOC);
    }
    
    public function getRatings(array $criteria)
    {
        $categories_sql = "SELECT `id`, `title` FROM `categories`";
        $stm = $this->pdo->prepare($categories_sql);
        $stm->execute();
        $categories = $stm->fetchAll(\PDO::FETCH_ASSOC);
        $order = "ORDER BY `" . ($criteria['sort']?? 'position') ."`";
        $sql = "SELECT * FROM `rating` ".
                " LEFT JOIN `films` ON (`films`.`id` = `rating`.`film_id`) ".
                " WHERE `category_id` = :category AND `parsed_at` = :parsed_at $order LIMIT 10";
        $sth = $this->pdo->prepare($sql);
        foreach ($categories as $category) {
            $sth->execute(
                [
                    ':category' => $category['id'],
                    ':parsed_at' => $criteria['parsed_at']
                ]
            );
            $data[$category['title']] = $sth->fetchAll(\PDO::FETCH_ASSOC);
        }
        return $data;
    }
}
