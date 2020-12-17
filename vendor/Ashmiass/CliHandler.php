<?php
namespace Ashmiass;

use Ashmiass\Parser;
use Ashmiass\ParserDb;

class CliHandler
{
    protected $db;
    protected $base_url = "http://www.world-art.ru/cinema/";
    protected $pages_for_parsing = [
        "rating_top.php",
        "rating_tv_top.php?public_list_anchor=1",
        "rating_tv_top.php?public_list_anchor=3",
        "rating_bottom.php",
        "rating_tv_top.php?public_list_anchor=4",
        "rating_tv_top.php?public_list_anchor=2"
    ];
    public function __construct($conf)
    {
        set_time_limit(0);
        $this->db = new ParserDb($conf['connection']);
    }

    public function handle($params = [])
    {
        $parser = new Parser($this->base_url, $this->db);
        foreach ($this->pages_for_parsing as $page_for_parsing) {
            $parser->parse($page_for_parsing);
        }
        return true;
    }
}
