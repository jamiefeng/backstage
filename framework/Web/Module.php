<?php

/**
 * 本类来源于PhalconEye；并对其进行了调整
 * @author dancebear <dancebear@gmail.com>
 *
 */
namespace Joy\Web;

use Phalcon\DiInterface;

class Module implements \Phalcon\Mvc\ModuleDefinitionInterface
{

    /**
     *
     * @var string
     */
    protected $_moduleName = "";

    protected $_acl = '\Joy\Mvc\ACL';

    public function __construct()
    {}

    public static function dependencyInjection(DiInterface $di)
    {}

    public function registerAutoloaders()
    {}

    /**
     * 注册服务
     */
    public function registerServices($di)
    {
        $this->_moduleName = $di->get('router')->getModuleName();
        
        // Create an event manager
        $eventsManager = \Joy::$app->getEventsManager();
        $this->setViewDir();
        $this->setDispatcher();
        $this->_initLocale();
    }

    /**
     * 重载Dispatcher类
     *
     */
    protected function setDispatcher()
    {
        $eventsManager = \Joy::$app->getEventsManager();
        if (! JOY_DEBUG) {
            $eventsManager->attach('dispatch:beforeExecuteRoute', new CacheAnnotation());
        }
        // $eventsManager->attach('dispatch:afterExecuteRoute', new \Joy\Mvc\Router\Event());
        // Create dispatcher
        $dispatcher = \Joy::$app->get('dispatcher');
        $dispatcher->setEventsManager($eventsManager);
    }

    public function setViewDir()
    {
        $moduleDirectory = $this->getModulePath() . DIRECTORY_SEPARATOR;
        $view = \Joy::$app->get('view');
        $view->setViewsDir($moduleDirectory . 'Views');
        $view->setLayoutsDir('Layouts');
        $view->setPartialsDir('Partials');
        $view->setLayout('Main');
        // print_r($this->_di->get('view'));exit;
    }

    /**
     * 初始化语言包
     */
    private function _initLocale()
    {}

    public function getModuleName()
    {
        return $this->_moduleName;
    }

    public function setModuleName($moduleName)
    {
        $this->_moduleName = $moduleName;
    }

    public function getModulePath()
    {
        return \Joy::$app->getModulePath() . DIRECTORY_SEPARATOR . $this->getModuleName();
    }
}