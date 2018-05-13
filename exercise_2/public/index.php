<?php

/**
 * Entry point for web server testing.
 *
 * @author avemar vrsndr@gmail.com
 * @license WTFPL
 */

require_once('../autoloader.php');

use exercise_2\Path\Path;

$Path = new Path([
    '/first/second/third',
    '/fourth/fifth/sixth',
    '/first/second/seventh',
    'first/eighth',
], '/');

$Path->cd()
    ->cd('../../fourth/fifth')
    ->addPaths(['ninth/tenth', '/eleventh'])
    ->cd('ninth')
    ->cd('../../fifth/sixth')
    ->cd('/');

echo '<pre>' . print_r($Path->getFileSystemData(), true) . '</pre>';
echo '<h1>' . $Path->currentPath() . '</h1>';
