<?php
namespace Ashmiass;

class BasePageObject
{
    /**
     * @var \DOMXPath
     */
    protected $dom;
    public function __construct(\DOMXPath $dom)
    {
        $this->dom = $dom;
    }
}
