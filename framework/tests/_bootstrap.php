<?php
// This is global bootstrap for autoloading
$stage = getenv('APPLICATION_STAGE') ? getenv('APPLICATION_STAGE') : 'development';
if ($stage == 'production') {
    defined('JOY_DEBUG') or define('JOY_DEBUG', false);
}else{
    defined('JOY_DEBUG') or define('JOY_DEBUG', true);
}
defined('ROOT_PATH') or define('ROOT_PATH', dirname(__DIR__).'/');
defined('APP_PATH') or define('APP_PATH', ROOT_PATH.'/app');
include ROOT_PATH.'/Joy.php';