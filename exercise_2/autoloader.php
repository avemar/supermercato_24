<?php

/**
 * @author avemar vrsndr@gmail.com
 * @license WTFPL
 */

spl_autoload_register('defaultAutoloader');

function defaultAutoloader()
{
    include dirname(__FILE__) . '/Path/Path.php';
    include dirname(__FILE__) . '/FileSystem/FileSystem.php';
}
