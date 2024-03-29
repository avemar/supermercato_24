<?php

/**
 * Entry point for web server testing.
 *
 * @author avemar vrsndr@gmail.com
 * @license WTFPL
 */

require_once '../vendor/autoload.php';

use exercise_2\Path\Path;

$path = new Path([
    '/first/second/third',
    '/fourth/fifth/sixth',
    '/first/second/seventh',
    'first/eighth',
]);

$path->cd()
    ->cd('../../fourth/fifth')
    ->addPaths(['ninth/tenth', '/eleventh'])
    ->cd('ninth')
    ->cd('../../fifth/sixth')
    ->cd('/');

echo '<pre>' . print_r($path->getFileSystemData(), true) . '</pre>';
echo '<h1>' . $path->currentPath() . '</h1>';
