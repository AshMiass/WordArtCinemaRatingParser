<?php
namespace Ashmiass;

class DescriptionPageObject extends BasePageObject
{
    /**
     * @var string
     */
    protected $poster_src_xpath = "string(//div[@class='comment_block']//img/@src)";

    public function getPosterSrc()
    {
        return $this->dom->evaluate($this->poster_src_xpath);
    }
}
