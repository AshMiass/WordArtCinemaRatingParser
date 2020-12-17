<?php
/**
 * Time spent: 5h+5h+5h
 */
require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR .'autoload.php';

use Ashmiass\Parser;
use Ashmiass\Db;

chdir(__DIR__);
$conf = require '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR ."db.conf.php";
$db = new Db($conf['connection']);

$base_url = "http://www.world-art.ru/cinema/";
$next_page_url = [
    "rating_top.php",
    "rating_tv_top.php?public_list_anchor=1",
    "rating_tv_top.php?public_list_anchor=3",
    "rating_bottom.php",
    "rating_tv_top.php?public_list_anchor=4",
    "rating_tv_top.php?public_list_anchor=2"
];
$parser = new Parser($base_url, $db);
$parser->parse($next_page_url[0]);
echo "Done";
