<?php
/**
 * Time spent: 5h+5h+5h+7h
 */
require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR .'autoload.php';
$conf = require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . "db.conf.php";
chdir(__DIR__);

use Ashmiass\ApiHandler;
use Ashmiass\CliHandler;

if (php_sapi_name() === 'cli') {
    $cli = new CliHandler($conf);
    $res = $cli->handle();
    echo $res? 'Done': 'Error ocured';
    return;
}
$api = new ApiHandler($conf);
echo $api->handleRequest($_REQUEST, $_SERVER['REQUEST_METHOD']);
