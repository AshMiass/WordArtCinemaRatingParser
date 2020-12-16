<?php
namespace Ashmiass;

class RatingElement
{

    protected $position;
    protected $title;
    protected $year;
    protected $link;
    protected $rating;
    protected $votes;
    protected $avg_rating;

    /**
     * @param \DOMElement  $dom
     */
    public function __construct(\DOMElement  $dom)
    {
        $td_nodes = $dom->getElementsByTagName('td');
        $this->position = (int)$td_nodes->item(0)->textContent;
        if ($td_nodes->item(1)->hasChildNodes()) {
            $a = $td_nodes->item(1)->childNodes[0];
            $this->title = $a->nodeValue;
            $this->year = filter_var($td_nodes->item(1)->childNodes[1]->nodeValue, FILTER_SANITIZE_NUMBER_INT);
            $this->link = ($a && $a->hasAttribute('href'))? $a->getAttribute('href') : null;
        } else {
            $this->title = $td_nodes->item(1)->nodeValue;
        }
        $this->rating = $td_nodes->item(2)->textContent;
        $this->votes = $td_nodes->item(3)->textContent;
        $this->avg_rating = $td_nodes->item(4)->textContent;
    }

    public function getPosition()
    {
        return $this->position;
    }
    
    public function getTitle()
    {
        return $this->title;
    }
    
    public function getYear()
    {
        return $this->year;
    }
    
    public function getLink()
    {
        return $this->link;
    }
    
    public function getRating()
    {
        return $this->rating;
    }
    
    public function getVotes()
    {
        return $this->votes;
    }
    
    public function getAvgRating()
    {
        return $this->avg_rating;
    }
}
