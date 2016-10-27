<?php
/**
 * 网页入口文件
 */

$stage = getenv('APPLICATION_STAGE') ? getenv('APPLICATION_STAGE') : 'production';
if ($stage == 'production') {
    defined('JOY_DEBUG') or define('JOY_DEBUG', true);
} else {
    defined('JOY_DEBUG') or define('JOY_DEBUG', true);
}
defined('ROOT_PATH') or define('ROOT_PATH', dirname(__DIR__));
defined('APP_PATH') or define('APP_PATH', ROOT_PATH . '/app');
defined('BASIC_APP_PATH') or define('BASIC_APP_PATH', ROOT_PATH.'/../basic/app');
include ROOT_PATH . '/../framework/Joy.php';
Joy::$di->get('loader')->registerNamespaces([
    'Joy\Basic\Models'   => BASIC_APP_PATH .'/Models',
    'Joy\Basic\Services' => BASIC_APP_PATH .'/Services',
    'Joy\Basic\Library'  => BASIC_APP_PATH .'/Library',
    'Joy\Basic\Api'      => BASIC_APP_PATH .'/Api',
], true);

(new \Joy\Web\Application())->run();