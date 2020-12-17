<?php
namespace Ashmiass;

/**
 * TODO: rewrite all SQL queries with insert ignore statement
 */
class ParserDb extends BaseDb
{
    /**
     * @param array[
     *     'avg_rating',
     *     'rating',
     *     'votes',
     *     'position',
     *     'category_id'
     * ]
     *
     * TODO: make method slim
     */
    public function saveRating(array $data)
    {
        //clear film_id for given position
        $sql = "UPDATE `rating` SET `film_id` = NULL " .
            "   WHERE (`parsed_at` = :parsed_at AND `category_id` = :category ".
            "   AND `position` = :pos AND `film_id` <> :film)";
        $this->pdo->prepare($sql)->execute(
            [
                ':film' => $data['film_id'],
                ':category' => $data['category_id'],
                ':pos' => $data['position'],
                ':parsed_at' => $data['parsed_at']
            ]
        );

        $sql = "UPDATE `rating` SET `film_id` = :film, ".
                "   `avg_rating` = :avg_rating, ".
                "   `rating` = :rating, ".
                "   `votes` = :votes, " .
                "   `updated_at` = now() ".
                " WHERE `category_id` = :category AND `position` = :pos AND `parsed_at` = :parsed_at; ";
        $sth = $this->pdo->prepare($sql);
        $sth->execute(
            [
                ':film' => $data['film_id'],
                ':avg_rating' => $data['avg_rating'],
                ':rating' => $data['rating'],
                ':votes' => $data['votes'],
                ':category' => $data['category_id'],
                ':pos' => $data['position'],
                ':parsed_at' => $data['parsed_at']
                ]
        );
        if ($sth->rowCount() < 1) {
            $sql = "INSERT INTO `rating` ".
                    " (`position`, `film_id`, `category_id`, `avg_rating`, `rating`, `votes`, `parsed_at`)".
                    " VALUES (:pos, :film, :category, :avg_rating, :rating, :votes, :parsed_at);";
            $sth = $this->pdo->prepare($sql);
            $sth->execute(
                [
                    ':pos' => $data['position'],
                    ':film' => $data['film_id'],
                    ':category' => $data['category_id'],
                    ':avg_rating' => $data['avg_rating'],
                    ':rating' => $data['rating'],
                    ':votes' => $data['votes'],
                    ':parsed_at' => $data['parsed_at'],
                ]
            );
        }
    }

    /**
     * @param int
     * @return bool
     */
    public function getPoster(int $film_id)
    {
        $sql = "SELECT * FROM `posters` WHERE `film_id` = :film LIMIT 1";
        $stm = $this->pdo->prepare($sql);
        $stm->execute([':film' => $film_id]);
        return $stm->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * @param int
     * @return bool
     */
    public function filmHasPoster(int $film_id)
    {
        $sql = "SELECT `id` FROM `posters` WHERE `film_id` = :film LIMIT 1";
        $stm = $this->pdo->prepare($sql);
        $stm->execute([':film' => $film_id]);
        return (bool) $stm->fetch();
    }

    /**
     * @param int
     * @return bool
     */
    public function filmHasDescription(int $film_id)
    {
        $sql = "SELECT `short_description` FROM `films` WHERE `id` = :film LIMIT 1";
        $stm = $this->pdo->prepare($sql);
        $stm->execute([':film' => $film_id]);
        $data = $stm->fetchColumn();
        return (bool) $data;
    }

    /**
     * TODO: add check for record existing before insert
     */
    public function savePoster($film_id, $poster_url, $file_path)
    {
        $sql = "INSERT IGNORE INTO `posters` (`film_id`, `poster_url`, `file_path`) ".
            " VALUES (:film, :poster_url, :file_path);";
        $this->pdo->prepare($sql)->execute([
            ':film' => $film_id,
            ':poster_url' => $poster_url,
            ':file_path' => $file_path
        ]);
    }

    public function saveFilmDescription($film_id, $short_description)
    {
        $sql = "UPDATE  `films` SET short_description = :short_description ".
            " WHERE `id` = :film;";
        $this->pdo->prepare($sql)->execute([
            ':film' => $film_id,
            ':short_description' => $short_description
        ]);
    }

    /**
     * @return bool
    */
    public function saveFilm($data)
    {
        $film = $this->getFilmByTitleAndYear($data['title'], $data['year']);
        if ($film) {
            return $film;
        }
        $sql = "INSERT IGNORE INTO `films` (`title`, `year`, `description_url`) VALUES (:title, :year, :url);";
        $sth = $this->pdo->prepare($sql);
        $sth->execute([
            ':title' => $data['title'],
            ':year' => $data['year'],
            ':url' => $data['url']
        ]);
        return $this->getFilmByTitleAndYear($data['title'], $data['year']);
    }
    public function getFilmByTitleAndYear($title, $year)
    {
        $sql = "SELECT `id`, `title`, `year` FROM `films`WHERE `title` = :title AND `year` = :year LIMIT 1;";
        $sth = $this->pdo->prepare($sql);
        $sth->execute([
            ':title' => $title,
            ':year' => $year
        ]);
        return $sth->fetch(\PDO::FETCH_ASSOC);
    }
    /**
     * @return bool
     */
    public function saveCategory($data)
    {
        $sql = "INSERT IGNORE INTO `categories` (`id`, `title`, `url`) VALUES (NULL, :title, :url); ";
        $sth = $this->pdo->prepare($sql);
        return $sth->execute([
            ':title' => $data['title'],
            ':url' => $data['url']
        ]);
    }

    /**
     * @return array
     */
    public function getCategoryByUrl($url)
    {
        $sql = "SELECT `id`, `title`, `url` FROM `categories` WHERE `url` = :url LIMIT 1; ";
        $sth = $this->pdo->prepare($sql);
        $sth->execute([
            ':url' => $url
        ]);
        return $sth->fetch(\PDO::FETCH_ASSOC);
    }
    /**
     * @return array
     */
    public function getCategoryByTitle($title)
    {
        $sql = "SELECT `id`, `title`, `url` FROM `categories` WHERE `title` = :title LIMIT 1; ";
        $sth = $this->pdo->prepare($sql);
        $sth->execute([
            ':title' => $title
        ]);
        return $sth->fetch(\PDO::FETCH_ASSOC);
    }
}
