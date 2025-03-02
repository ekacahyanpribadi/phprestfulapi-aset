<?php
require __DIR__ . '/model/db.class.php';
require __DIR__ . '/model/lib.php';
require __DIR__ . '/model/log.php';

$dirLoc = "/phprestfulapi-aset";
$caseLoc = $dirLoc;

$request = $_SERVER['REQUEST_URI'];
$getId = explode("/", $request);
$id = $getId[count($getId) - 1];

$modelDir = '/model/';

switch ($request) {
    case $caseLoc.'/aset/':
        require __DIR__ . $modelDir . 'aset.php';
    break;

    case $caseLoc.'/kategori/':
        require __DIR__ . $modelDir . 'kategori.php';
        break;

    case $caseLoc.'/kategori/'.$id:
            require __DIR__ . $modelDir . 'kategori.php';
            break;

    default:
        require __DIR__ . $modelDir . '404.php';
}
