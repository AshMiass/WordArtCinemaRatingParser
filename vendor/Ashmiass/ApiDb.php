<?php
namespace Ashmiass;

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
        if (empty($criteria['parsed_at'])) {
            $sql = "SELECT DISTINCT(`parsed_at`) FROM `rating` ORDER BY `parsed_at` DESC LIMIT 1";
            $sth = $this->pdo->prepare($sql);
            $sth->execute();
            $criteria['parsed_at'] = $sth->fetch(\PDO::FETCH_COLUMN);
        }
        $sql = "SELECT * FROM `rating` ".
                " LEFT JOIN `films` ON (`films`.`id` = `rating`.`film_id`) ".
                " LEFT JOIN `posters` ON (`posters`.`film_id` = `rating`.`id`) ".
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
