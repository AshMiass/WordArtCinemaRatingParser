<?php
header('Content-Type: application/json');

use Ashmiass\ApiDb;

require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR .'autoload.php';
$conf = require '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR ."db.conf.php";


$db = new ApiDb($conf['connection']);
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    return;
}
$action = $_REQUEST[0]?? 'getRatings';
$today = new DateTime();
$category = $_REQUEST['category']?? 2;
$date = $_REQUEST['date']?? $today->format('Y-m-d');
$sort = $_REQUEST['sort']?? 'position';
$res = [];
if ($action == 'getRatings') {
    $res = $db->getRatings(['parsed_at' => $date, 'sort' => $sort]);
}
echo json_encode($res);
