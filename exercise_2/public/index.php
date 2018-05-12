<?php

require_once('../autoloader.php');

use exercise_2\Path\Path;

$Path = new Path([
    '/first/second/third',
    '/fourth/fifth/sixth',
    '/first/second/seventh',
    'first/eighth',
]);

echo '<pre>' . print_r($Path->getFSData(), true) . '</pre>';

echo '<h1>' . $Path->currentPath() . '</h1>';