<?php
namespace Ashmiass;

use Ashmiass\Crawler;
use Ashmiass\ParserDb;
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
     * @var ParserDb
     */
    protected $db;
    protected $first_page;
    protected $next_page_url;
    protected $log_path;

    public function __construct(string $base_url, ParserDb $db, $cache, string $log_path)
    {
        $this->base_url = $base_url;
        $this->crawler = new Crawler($base_url, ".".DIRECTORY_SEPARATOR . "uploads", $cache);
        $this->db = $db;
        $this->log_path = $log_path;
    }


    protected function savePosition($start_page, $next_page_url)
    {
        $data = [
            'start_page' => $start_page,
            'next_page_url' => $next_page_url,
        ];
        file_put_contents($this->log_path, json_encode($data));
    }

    public function parse(string $first_page, string $next_page_url = null)
    {
        $this->first_page = $first_page;
        $next_page_url = $next_page_url?? $this->first_page;
        $category_id = null;
        while ($next_page_url) {
            $this->next_page_url = $next_page_url;
            $this->savePosition($this->first_page, $this->next_page_url);
            $this->crawler->loadPage($next_page_url);
            $page = $this->crawler->getRatingPageObject();
            $list = $page->getListElements();
            if ($category_id === null) {
                $category_title = $page->getPageTitle();
                $category = $this->db->getCategoryByTitle($category_title);
                $category_url = $this->base_url . $first_page;
                if (!$category_url && !$this->db->saveCategory(['title'=>$category_title, 'url' => $category_url])) {
                    throw new Exception('Error occured while saving category');
                };
                !$category_url && $category = $this->db->getCategoryByUrl($category_url);
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
            $poster = $this->db->getPoster($film['id']);
            $hasDesciprion = $this->db->filmHasDescription($film['id']);
            if (!$poster || !$hasDesciprion) {
                $descriptionPage = $this->crawler->getDescriptionPage($element->getLink());
                $poster_url = $this->base_url . $descriptionPage->getPosterSrc();
                if (!$hasDesciprion) {
                    $short_description = $descriptionPage->getShortDescription();
                    $this->db->saveFilmDescription($film['id'], $short_description);
                }
                if (!$poster) {
                    if ($poster_url && $poster_url != $this->base_url) {
                        $poster_file = $this->crawler->downloadImage($poster_url);
                    } else {
                        $poster_file = 'none'; //нет постера
                    };
                    if ($poster_file) {
                        $this->db->savePoster($film['id'], $poster_url, $poster_file);
                    };
                }
            }
        }
    }
}
