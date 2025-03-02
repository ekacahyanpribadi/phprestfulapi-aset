<?php
require __DIR__ . '/model/db.class.php';
require __DIR__ . '/model/lib.php';

$dirLoc = "/phprestfulapi-aset";
$caseLoc = $dirLoc;

$request = $_SERVER['REQUEST_URI'];
$modelDir = '/model/';

switch ($request) {
    case $caseLoc.'/aset':
        require __DIR__ . $modelDir . 'aset.php';
    break;

    case $caseLoc.'/kategori':
        require __DIR__ . $modelDir . 'kategori.php';
        break;

    default:
        require __DIR__ . $modelDir . '404.php';
}
