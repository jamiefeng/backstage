<?php
/**
 * 网页入口文件
 */
$stage = getenv('APPLICATION_STAGE') ? getenv('APPLICATION_STAGE') : 'production';
if ($stage == 'production') {
    defined('JOY_DEBUG') or define('JOY_DEBUG', false);
} else {
    defined('JOY_DEBUG') or define('JOY_DEBUG', true);
}
defined('ROOT_PATH') or define('ROOT_PATH', dirname(__DIR__));
defined('APP_PATH') or define('APP_PATH', ROOT_PATH . '/app');
include ROOT_PATH . '/../framework/Joy.php';
Joy::$di->get('loader')->registerNamespaces([
'Joy\Api' => APP_PATH . '/Api',
'Joy\Library' => APP_PATH . '/Library/',
'Joy\Overseasbasic\Models' => APP_PATH .'/Models',
'Joy\Overseasbasic\Services' => APP_PATH .'/Services',
'Joy\Overseasbasic\Library' => APP_PATH .'/Library',
'Joy\Overseasbasic\Api' => APP_PATH .'/Api',
], true);
(new \Joy\Web\Application())->run();

