<?php
/**
 * 设置应用程序启用时间
 */
defined('JOY_BEGIN_TIME') or define('JOY_BEGIN_TIME', microtime(true));
// 定义是否开启调试模式
defined('JOY_DEBUG') or define('JOY_DEBUG', false);
// 系统文件路径
defined('JOY_PATH') or define('JOY_PATH', dirname(__FILE__));
// debug资源文件路径
defined('JOY_DEBUG_URI') or define('JOY_DEBUG_URI', 'http://static.ym85.com/debug/1.2.0/');

class Joy
{

    /**
     * 应用程序实例
     *
     * @var \Joy\Application
     */
    public static $app;

    /**
     * 依赖注入管理器实例
     *
     * @var \Phalcon\DiInterface
     */
    public static $di;

    /**
     * 系统配置信息
     *
     * @var \Phalcon\Config
     */
    public static $config;

    /**
     * 语言翻译
     *
     * @param string $category
     * @param string $message
     * @param array $params
     * @return string
     */
    public static function t($category, $message, $params = [], $language = null)
    {
        $p = [];
        foreach ((array) $params as $name => $value) {
            $p['{' . $name . '}'] = $value;
        }

        return ($p === []) ? $message : strtr($message, $p);
    }
}
$loader = new \Phalcon\Loader();
$loader->registerNamespaces([
    'Joy' => JOY_PATH,
    'Phalcon' => JOY_PATH . '/Library/Phalcon',
    'CommonApi' => JOY_PATH . '/../CommonApi'
]);
$loader->register();
$di = new \Phalcon\DI\FactoryDefault();
$di->setShared('loader', $loader);
Joy::$di = $di;
