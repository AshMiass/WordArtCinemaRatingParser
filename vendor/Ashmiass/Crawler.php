<?php
namespace Ashmiass;

use Exception;

class Crawler
{
    /**
     * @var string
     */
    protected $url;
    protected $base_url;
    protected $page_url;
    protected $use_cache;
    protected $rating_page_object;
    protected $download_dir;

    public function __construct(string $base_url, string $download_dir, bool $use_cache = false)
    {
        if (!is_dir($download_dir)) {
            throw new Exception("Directory for downloading poster {$download_dir} doesn`t exists");
        }
        $this->base_url = $base_url;
        $this->use_cache = $use_cache;
        $this->download_dir = $download_dir;
    }

    public function loadPage(string $page_url)
    {
        $this->page_url = $page_url;
        $this->fetchRatingPage($this->getUrl());
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getUrl()
    {
        if (!$this->page_url) {
            throw new Exception('page_url is empty, please use loadPage($page_url) method');
        }
        return $this->base_url . $this->page_url;
    }

    public function getHTML($url)
    {
        $file_name = hash('md5', $url);
        $cachePath = join(DIRECTORY_SEPARATOR, [__DIR__ , '..' , '..' , 'temp' , $file_name]);
        $useragent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko)";
        $opts = [
            "http" => [
                "method" => "GET",
                "header" => "Accept-language: ru\r\n" .
                    "Accept: text/html\r\n" .
                    "Referer: {$url}\r\n" .
                    "User-Agent: {$useragent}"
            ]
        ];
        $context = stream_context_create($opts);
        if (!file_exists($cachePath) || !$this->use_cache) {
            $html = trim(file_get_contents($url, false, $context));
            if (!$html) {
                throw new Exception("Error occured while loading document: {$url}");
            }
            file_put_contents($cachePath, $html);
        }
        return $cachePath;
    }

    /**
     * @return \DOMXpath
     */
    public function getDocument($url)
    {
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTMLFile($this->getHTML($url));
        $xpath = new \DOMXPath($dom);
        libxml_use_internal_errors(false);
        return $xpath;
    }

    public function fetchRatingPage(string $url)
    {
        $this->rating_page_object = new RatingPageObject($this->getDocument($url));
    }
    
    /**
     * @return RatingPageObject
     */
    public function getRatingPageObject()
    {
        return $this->rating_page_object;
    }

    public function getDescriptionPage($url)
    {
        $url = $this->base_url . $url;
        return new DescriptionPageObject($this->getDocument($url));
    }

    public function downloadImage($image_url, $film_id = null)
    {
        $extention = substr($image_url, strrpos($image_url, '.'));
        $file_name = $this->download_dir . DIRECTORY_SEPARATOR . md5($image_url) . $extention;
        if (!is_dir($this->download_dir) || (!file_exists($file_name) && !copy($image_url, $file_name))) {
            return;
        }
        return $file_name;
    }
}
