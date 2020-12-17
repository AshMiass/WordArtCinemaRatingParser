<?php
chdir(__DIR__);
require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR .'autoload.php';
$conf = require '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR ."db.conf.php";
$sql = file_get_contents('..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR ."init.sql");
$sql = str_replace('{DB_NAME}', $conf['connection']['dbname'], $sql);
$db = new Ashmiass\BaseDb($conf['connection']);
$res = $db->executeSql($sql);
echo (bool)$db->getErrorCode()? 'Success' : 'Failure';
