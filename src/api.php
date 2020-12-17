<?php
header('Content-Type: application/json');

use Ashmiass\ApiDb;

require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR .'autoload.php';
$conf = require '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR ."db.conf.php";


$db = new ApiDb($conf['connection']);
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    return;
}
$res = [];
$action = 'ratings';
if (key_exists('film', $_REQUEST)) {
    $action = 'film';
}
if ($action == 'ratings') {
    $today = new DateTime();
    $category = $_REQUEST['category']?? 2;
    $date = $_REQUEST['date']?? $today->format('Y-m-d');
    $sort = $_REQUEST['sort']?? 'position';
    $res = $db->getRatings(['parsed_at' => $date, 'sort' => $sort]);
}
if ($action == 'film' && !empty($_REQUEST['film']) && is_numeric($_REQUEST['film'])) {
    $film_id = $_REQUEST['film'];
    $res = $db->getFilm($film_id);
}
echo json_encode($res);
