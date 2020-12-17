<?php
namespace Ashmiass;

use Ashmiass\Crawler;
use Ashmiass\ParseDb;
use DateTime;
use Exception;

class Parser
{
    /**
     * @var Crawler
     */
    protected $crawler;

    /**
     * @var string
     */
    protected $base_url;
    
    /**
     * @var ParseDb
     */
    protected $db;

    public function __construct(string $base_url, ParseDb $db, $cache = true)
    {
        $this->base_url = $base_url;
        $this->crawler = new Crawler($base_url, "..".DIRECTORY_SEPARATOR . "uploads", $cache);
        $this->db = $db;
    }
    public function parse(string $first_page)
    {
        $next_page_url = $first_page;
        $max_pages = 3;
        $category_id = null;
        while ($next_page_url && $max_pages--) {
            $this->crawler->loadPage($next_page_url);
            $page = $this->crawler->getRatingPageObject();
            $list = $page->getListElements();
            if ($category_id === null) {
                $category_title = $page->getPageTitle();
                $category_url = $this->base_url . $first_page;
                if (!$this->db->saveCategory(['title'=>$category_title, 'url' => $category_url])) {
                    throw new Exception('Error occured while saving category');
                };
                $category = $this->db->getCategoryByUrl($category_url);
                if (!$category || !isset($category['id'])) {
                    throw new Exception('Excpected id of category but none given');
                }
                $category_id = $category? $category['id'] : null;
            }
            $this->parsePage($list->getElements(), $category_id);
            $next_page_url = $page->getNextPageUrl();
        }
    }

    /**
     * @param RatingElement[] $elements
     */
    protected function parsePage(array $elements, int $category_id)
    {
        $day = new DateTime();
        $day = $day->format('Y-m-d');
        foreach ($elements as $element) {
            $film = $this->db->saveFilm(
                [
                    'title'=> $element->getTitle(),
                    'year' => $element->getYear(),
                    'url' => $this->base_url . $element->getLink()
                ]
            );
            $data = [
                'film_id' => $film['id'],
                'avg_rating' => $element->getAvgRating(),
                'rating' => $element->getRating(),
                'position' => $element->getPosition(),
                'votes' => $element->getVotes(),
                'category_id' => $category_id,
                'parsed_at' => $day
            ];
            $this->db->saveRating($data);
            if (!$this->db->filmHasPoster($film['id'])) {
                $poster_url = $this->base_url . $this->crawler->getDescriptionPage($element->getLink())->getPosterSrc();
                $poster_file = $this->crawler->downloadImage($poster_url);
                $this->db->savePoster($film['id'], $poster_url, $poster_file);
            }
        }
    }
}
