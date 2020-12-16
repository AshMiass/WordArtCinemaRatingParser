<?php
namespace Ashmiass;

class RaitingPageObject extends BasePageObject
{
    /**
     * @var string
     */
    protected $list_xpath = "//tr[1]/td//*[text() = 'Наименование']/ancestor-or-self::table[1]//tr[position()>1]";
    protected $pagination_prev_xpath = "string(//td[@align='left']/a[@class='estimation']/@href)";
    protected $pagination_next_xpath = "string(//td[@align='right']/a[@class='estimation' or text()='следующие 50']/@href)";
    protected $page_title_xpath = "string(//html//head/title)";

    /**
     * @var RaitingList
     */
    protected $list;

    /**
     * @return RaitingList
     */
    public function getListElements()
    {
        if (!$this->list) {
            $this->list = new RaitingList($this->dom->query($this->list_xpath));
        }
        return $this->list;
    }

    public function getPrevPageUrl()
    {
        return $this->dom->evaluate($this->pagination_prev_xpath);
    }

    public function getNextPageUrl()
    {
        return $this->dom->evaluate($this->pagination_next_xpath);
    }

    public function getPageTitle()
    {
        return $this->dom->evaluate($this->page_title_xpath);
    }
}
