<?php
namespace Ashmiass;

class RatingList
{
    protected $elements = [];
    /**
     * @param \DOMNodeList $dom
     */
    public function __construct(\DOMNodeList $dom)
    {
        foreach ($dom as $item) {
            $this->elements[] = new RatingElement($item);
        }
    }

    public function getElements()
    {
        return $this->elements;
    }
}
