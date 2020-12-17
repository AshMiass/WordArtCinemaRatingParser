<?php
chdir(__DIR__);
const ROOT_PATH = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;
require ROOT_PATH . 'vendor' . DIRECTORY_SEPARATOR .'autoload.php';
Ashmiass\Autoload::registerAutoload();
$conf = require ROOT_PATH . 'config' . DIRECTORY_SEPARATOR . "db.conf.php";

use Ashmiass\ApiHandler;
use Ashmiass\CliHandler;

if (php_sapi_name() === 'cli') {
    $cli = new CliHandler($conf, $argv, ROOT_PATH);
    $res = $cli->handle();
    echo $res? 'Done': 'Error ocured';
    return;
}
$api = new ApiHandler($conf);
echo $api->handleRequest($_REQUEST, $_SERVER['REQUEST_METHOD']);
