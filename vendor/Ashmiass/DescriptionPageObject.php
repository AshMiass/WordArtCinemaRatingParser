<?php
namespace Ashmiass;

class DescriptionPageObject extends BasePageObject
{
    /**
     * @var string
     */
    protected $poster_src_xpath = "string(//div[@class='comment_block']//img/@src)";
    protected $short_description_xpath = "string((//p[@class='review'][@align='justify'])[1])";

    public function getPosterSrc()
    {
        return $this->dom->evaluate($this->poster_src_xpath);
    }
    public function getShortDescription()
    {
        $text = $this->dom->evaluate($this->short_description_xpath);
        return $text != 'Выборка фильмов из базы данных:'? $text : 'нет описания';
    }
}
