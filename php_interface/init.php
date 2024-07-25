<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$files = [
    __DIR__ . '/autoload.php',
    __DIR__ . '/handlers.php',
];

foreach ($files as $filePath) {
    if (file_exists($filePath)) {
        require_once($filePath);
    }
}
