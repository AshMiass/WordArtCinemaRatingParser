<?php
namespace Ashmiass;

use Ashmiass\Crawler;
use Ashmiass\Db;

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
     * @var Db
     */
    protected $db;

    public function __construct(string $base_url, Db $db, $cache = true)
    {
        $this->base_url = $base_url;
        $this->crawler = new Crawler($base_url, "..".DIRECTORY_SEPARATOR . "uploads", $cache);
        $this->db = $db;
    }
    public function parse(string $first_page)
    {
        $next_page_url = $first_page;
        $max_pages = 3;
        $category_title = '';
        while ($next_page_url && $max_pages--) {
            $this->crawler->loadPage($next_page_url);
            $page = $this->crawler->getRaitingPageObject();
            $list = $page->getListElements();
            if (!$category_title) {
                $category_title = $page->getPageTitle();
                $this->db->saveCategory(['title'=>$category_title, 'url' => $this->base_url . $first_page]);
            }
            $this->parsePage($list->getElements());
            $next_page_url = $page->getNextPageUrl();
        }
    }

    /**
     * @param RaitingElement[] $elements
     */
    protected function parsePage(array $elements)
    {
        foreach ($elements as $element) {
            echo "{$element->getTitle()} - {$element->getYear()} - {$this->base_url}{$element->getLink()}\n";
            $poster_url = $this->base_url . $this->crawler->getDescriptionPage($element->getLink())->getPosterSrc();
            $image_local_path = $this->crawler->downloadImage($poster_url);
            echo $image_local_path  . "\n";
            $data = [
                'title' => $element->getTitle(),
                'avg_raiting' => $element->getAvgRaiting(),
                'raiting' => $element->getRaiting(),
                'link' => $element->getLink(),
                'position' => $element->getPosition(),
                'votes' => $element->getVotes(),
                'year' => $element->getYear(),
                'poster_path' => $image_local_path,
                'poster_url' => $poster_url
            ];
            // $this->db->saveRaiting($data);
        }
        // echo count($elements) . "\n";
    }
}
