<?php
namespace Ashmiass;

class RaitingList
{
    protected $elements = [];
    /**
     * @param \DOMNodeList $dom
     */
    public function __construct(\DOMNodeList $dom)
    {
        foreach ($dom as $item) {
            $this->elements[] = new RaitingElement($item);
        }
    }

    public function getElements()
    {
        return $this->elements;
    }
}
