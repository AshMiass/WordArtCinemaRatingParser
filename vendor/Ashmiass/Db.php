<?php
namespace Ashmiass;

class Db extends BaseDb
{
   
    // 'title' => $element->getTitle(),
    // 'avg_raiting' => $element->getAvgRaiting(),
    // 'raiting' => $element->getRaiting(),
    // 'link' => $element->getLink(),
    // 'position' => $element->getPosition(),
    // 'votes' => $element->getVotes(),
    // 'year' => $element->getYear(),
    // 'poster_path' => $image_local_path,
    // 'poster_url' => $poster_url,
    // 'category_id' => $category_id
    public function saveRaiting($data)
    {
        $sql = "INSERT INTO `categories` (`id`, `title`, `url`) VALUES (NULL, :title, :url); ";
        $sth = $this->pdo->prepare($sql);
        return $sth->execute([]);
    }

    /**
     * @return bool
     */
    public function saveFilm($data)
    {
        $sql = "INSERT IGNORE INTO `categories` (`id`, `title`, `url`) VALUES (NULL, :title, :url); ";
        $sth = $this->pdo->prepare($sql);
        return $sth->execute([
            ':title' => $data['title'],
            ':url' => $data['url']
        ]);
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
}
