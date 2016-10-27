<?php
namespace Joy;

use Phalcon\Logger, Phalcon\Logger\Formatter\Line as LineFormatter, Phalcon\Exception as Exception, Phalcon\Logger\Multiple as MultipleStream;

/**
 *
 * @author dancebear
 * 
 * @property \Phalcon\Cache\BackendInterface $cache
 * @property \Phalcon\Security $security
 *
 */
class Application extends \Phalcon\Mvc\Application
{

    /**
     *
     * @var string
     */
    protected $_basePath;

    /**
     *
     * @var string
     */
    protected $_runtimePath;

    /**
     * 日志组件
     *
     * @var \Phalcon\Logger\AdapterInterface
     */
    protected $_logger;

    /**
     * 应用程序的命名空间
     *
     * @var string
     */
    public $defaultNamespace;

    /**
     * 配置文件目录
     *
     * @var string
     */
    protected $_configPath;

    /**
     *
     * @var string 终端用户所使用的字符集，推荐使用
     *      [IETF language tags](http://en.wikipedia.org/wiki/IETF_language_tag)，例如：`en`代表英语。
     *      `en-US`代表英语(美国).
     * @see sourceLanguage
     */
    public $language = 'zh-CN';

    /**
     *
     * @var string 应用程序中所使用的语言
     *      这主要指的是信息和视图文件所使用的语言。
     * @see language
     */
    public $sourceLanguage = 'en-US';

    /**
     *
     * @param \Phalcon\DiInterface $di
     */
    public function __construct($di = null)
    {
        parent::setDI(\Joy::$di);
        \Joy::$app = $this;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Phalcon\DI\Injectable::__get()
     */
    public function __get($property)
    {
        if (method_exists($this, 'get' . $property))
            return $this->{'get' . ucfirst($property)}();
        if ($this->_dependencyInjector->has($property)) {
            return $this->_dependencyInjector->getShared($property);
        }
    }

    /**
     * 加载通过Composer安装的库
     */
    protected function loadComposerLib()
    {
        /**
         * load composer lib
         */
        if (file_exists($this->_basePath . '/../vendor/autoload.php'))
            require ($this->_basePath . '/../vendor/autoload.php');
    }

    /**
     * 加载配置文件
     *
     * @param string|\Phalcon\Config|array $file
     *            配置文件路径，此文件必须返回一个配置信息数组
     * @return \Joy\Application
     */
    public function configure($file)
    {
        if (! is_string($file)) {
            $configData = $file;
            $this->_configPath = null;
        } else {
            if (! file_exists($file)) {
                throw new Exception('配置文件"' . $file . '"不存在');
            }
            /**
             * Read the configuration
             */
            $configData = include ($file);
            $this->_configPath = basename($file);
        }
        $config = new \Phalcon\Config($configData);
        \Joy::$config = $config;
        $this->defaultNamespace = $config->defaultNamespace;
        $this->setBasePath($config->basePath);
        $this->setRuntimePath($config->runtimePath);
        return $this;
    }

    /**
     * 设置应用程序根目录
     *
     * @param string $path
     */
    public function setBasePath($path)
    {
        $this->_basePath = $path;
    }

    /**
     * 返回应用程序根目录
     *
     * @return string
     */
    public function getBasePath()
    {
        return $this->_basePath;
    }

    /**
     * 获取应用程序的模块目录
     *
     * @return string 应用程序模块目录。默认为{@link basePath}的子目录`Modules`。
     */
    public function getModulePath()
    {
        if ($this->_modulePath !== null)
            return $this->_modulePath;
        else
            return $this->_modulePath = $this->getBasePath() . DIRECTORY_SEPARATOR . 'Modules';
    }

    /**
     * 设置应用程序的模块目录
     *
     * @param string $value
     *            应用程序的模块目录
     * @throws Exception 无效的模块目录
     */
    public function setModulePath($value)
    {
        if (($this->_modulePath = realpath($value)) === false || ! is_dir($this->_modulePath))
            throw new Exception(\Joy::t('Joy', 'The module path "{path}" is not a valid directory.', array(
                '{path}' => $value
            )));
    }

    /**
     * 设置运行时应用程序目录
     *
     * @param string $path
     */
    public function setRuntimePath($path)
    {
        $this->_runtimePath = $path;
    }

    /**
     * 取得运行时文件目录
     *
     * @return string
     */
    public function getRuntimePath()
    {
        return $this->_runtimePath;
    }

    /**
     * 设置应用程序日志组件；日志组件允许同时使用多种日志输出方式
     *
     * @link http://docs.phalconphp.com/en/latest/reference/logging.html#adapters
     * @see \Phalcon\Logger\Multiple
     * @see \Phalcon\Logger\Adapter\File
     *
     * @throws Exception
     */
    protected function setLogger()
    {
        \Joy::$di->set('logger', function ()
        {
            $logger = new MultipleStream();
            // get environment variables
            $hostName = getenv('host');
            if ($hostName === false) {
                $logger->log('environment variables "host" for logger is not exists', Logger::ERROR);
                // throw new Exception('environment variables "host" for logger is not exists');
                $hostName = 'localhost';
            }
            foreach (\Joy::$config->components->logger as $adapter => $loggerConfig) {
                $adapter = '\Phalcon\Logger\Adapter\\' . $adapter;
                $_config = isset($loggerConfig[1]) ? $loggerConfig[1] : null;
                $_logger = new $adapter($loggerConfig[0], $_config);
                $logger->push($_logger);
            }
            $formatter = new LineFormatter($hostName . '-%date% - %message%');
            $logger->setFormatter($formatter);
            return $logger;
        });
    }

    /**
     * 设置应用程序的控制器模块信息
     * 系统会依据配置文件中的modules区块来启用相应的模块。
     * 以下是一个相应配置的实例：
     * ```
     * return [
     * 'components' => [
     * ],
     * 'modules' => [
     * 'User'
     * ]
     * ]
     * ```
     * 上述例子启用了一个名为User的模块，相应的程序目录为：
     * ``app/Modules/User``
     * 在此目录下包含相应的``Controllers`` ``Models`` ``Views``目录。
     * User目录的下还可以通过一个名为``Module.php``的文件来继承``Joy\Web\Module``类来实现模块的一些自定义设置。
     */
    protected function setModules()
    {
        $namespaces = [];
        foreach ((array) \Joy::$config->modules as $module) {
            $moduleClassFile = $this->getModulePath() . DIRECTORY_SEPARATOR . $module . '/Module.php';
            $moduleClass = $this->defaultNamespace . '\\' . $module . '\Module';
            if (! file_exists($moduleClassFile)) {
                $moduleClassFile = JOY_PATH . '/Web/Module.php';
                $moduleClass = 'Joy\Web\Module';
            }
            $modules[$module] = array(
                'className' => $moduleClass,
                'path' => realpath($moduleClassFile),
                'moduleName' => $module
            );
            $namespaces[$this->defaultNamespace . '\\' . $module] = $this->getModulePath() . DIRECTORY_SEPARATOR . $module;
        }
        $namespaces[$this->defaultNamespace . '\\Controllers'] = $this->getBasePath() . '/Controllers';
        $namespaces[$this->defaultNamespace . '\\Models'] = $this->getBasePath() . '/Models';
        \Joy::$di->get('loader')->registerNamespaces($namespaces, true);
    }

    /**
     * 设置系统的事件管理器
     * 这里将实例化一个``\Phalcon\Events\Manager``类，并将它注册微系统的默认事件管理器。
     */
    protected function setEvents()
    {
        $this->setEventsManager(new \Phalcon\Events\Manager());
    }

    /**
     * 设置找不到页面的事件
     */
    protected function setNotFound()
    {
        \Joy::$di->set('dispatcher', function () {
            $eventsManager = new \Phalcon\Events\Manager();
            // Attach a listener
            $eventsManager->attach("dispatch:beforeException", function ($event, $dispatcher, $exception) {
                
                // Handle 404 exceptions
                if ($exception instanceof \Phalcon\Mvc\Dispatcher\Exception) {
                    $this->sendErrorPage();
                }
                // Alternative way, controller or action doesn't exist
                if ($event->getType() == 'beforeException') {
                    switch ($exception->getCode()) {
                        case \Phalcon\Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                        case \Phalcon\Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                            $this->sendErrorPage();
                    }
                }
            });
            $dispatcher = new \Phalcon\Mvc\Dispatcher();
            // Bind the EventsManager to the dispatcher
            $dispatcher->setEventsManager($eventsManager);
            return $dispatcher;
        }, true);
    }

    /**
     * 输出错误信息或错误页面
     */
    private function sendErrorPage()
    {
        $config = \Joy::$config;
        $config = $config->toArray();
        $this->response->setStatusCode(404, 'page not found')->sendHeaders();
        if ($config['render'] == 'json') {
            $this->response->setJsonContent([
                'status' => 404,
                'message' => 'page not found'
            ])->send();
        } else {
            $errorPage = JOY_PATH . '/Web/error/404.php';
            if (isset($config['error']['404'])) {
                $custom = ROOT_PATH . $config['error']['404'];
                if (file_exists($custom))
                    $errorPage = $custom;
            }
            include_once $errorPage;
        }
        throw new \Joy\Web\HttpException(404);
    }
    /**
     * 初始化系统的视图组件，支持直接使用PHP模板和volt模板引擎
     *
     * @return \Phalcon\Mvc\View
     */
    protected function setView()
    {
        /**
         * Setting up the view component
         */
        \Joy::$di->set('view', function ()
        {
            $view = new \Phalcon\Mvc\View();
            $volt = new \Phalcon\Mvc\View\Engine\Volt($view, \Joy::$di);
            $php = new \Phalcon\Mvc\View\Engine\Php($view, \Joy::$di);
            $config = \Joy::$config->application->views;
            $volt->setOptions([
                "compiledPath" => function ($templatePath) use($config)
                {
                    $templatePath = str_replace(DIRECTORY_SEPARATOR, $config->compiledSeparator, str_replace(realpath(\Joy::$config->basePath), '', realpath($templatePath)));
                    return $config->compiledPath . '/' . $templatePath . $config->compiledExtension;
                },

                // 'compiledExtension' => $config->compiledExtension,
                // 'compiledSeparator' => $config->compiledSeparator,
                'compileAlways' => JOY_DEBUG && $config->compileAlways
            ]);

            $view->setViewsDir($config->dir);
            $view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_LAYOUT);

            $view->registerEngines([
                ".volt" => $volt,
                '.php' => $php
            ]);

            if (JOY_DEBUG) {
                $em = $this->getEventsManager();
                $em->attach("view", function ($event, $view)
                {
                    if ($event->getType() == 'beforeRender') {
                        if (! $view->renderStart)
                            $view->renderStart = microtime(true);
                    }
                    if ($event->getType() == 'afterRender') {
                        $output = $view->getContent();
                        $timestamp = microtime(true);
                        $viewTime = round($timestamp - $view->renderStart, 3);
                        $totleTime = round($timestamp - JOY_BEGIN_TIME, 3);
                        if (\Joy::$di->has('profiler')) {
                            /**
                             *
                             * @var \Phalcon\Db\Profiler $profiler
                             */
                            $dbProfiler = \Joy::$di->get('profiler');
                            $querys = 'Total ' . $dbProfiler->getNumberTotalStatements() . ' query';
                            $costs = 'Cost ' . round($dbProfiler->getTotalElapsedSeconds(), 3) . 's';
                        } else {
                            $querys = '';
                            $costs = '';
                        }
                        $output = strtr($output, [
                            '<!-- {total} -->' => 'Page:' . $totleTime . 's',
                            '<!-- {view} -->' => 'View:' . $viewTime . 's',
                            '<!-- {querys} -->' => $querys,
                            '<!-- {costs} -->' => $costs
                        ]);
                        $view->setContent($output);
                        // $di->get('logger')->log('渲染客户端js、css结束'.json_encode($view));
                        return $view;
                    }
                    if ($event->getType() == 'notFoundView') {
                        throw new Exception('View not found - "' . $view->getActiveRenderPath() . '"');
                    }
                });
                $view->setEventsManager($em);
            }

            return $view;
        });
    }

    /**
     * 设置数据库事件探查器
     *
     * @return \Phalcon\Db\Profiler
     */
    protected function setProfile()
    {
        \Joy::$di->set('profiler', function ()
        {
            return new \Phalcon\Db\Profiler();
        }, true);
    }
    
    /**
     * 根据配置文件信息添加mongo数据库实例
     */
    protected function setMongo(){
    	if (\Joy::$config->components->offsetExists('mongo')) {
    		$config = \Joy::$config->components->mongo->toArray();
    	} else {
    		$config = [];
    	}
    	if (count($config) > 0) {
	    	\Joy::$di->set('mongo', function() use ($config) {
	    		$collection = $config['username'].':'.$config['password'].'@'.$config['host'];
	    		$mongo = new \Phalcon\Db\Adapter\Mongo\Client("mongodb://".$collection);
	    		return $mongo->selectDB($config['dbname']);
	    	}, true);
    	}
    }

    /**
     * 根据配置文件信息添加数据库实例
     */
    protected function setDatabase()
    {
        $config = \Joy::$config->components->database;
        if ($config->count() > 0) {
            foreach ($config as $name => $database) {
                $this->addDatabase($name, $database);
            }
        }
    }

    /**
     * 设置数据库连接
     * 请在配置文件的components的database小节下添加
     * ```
     * 'db' => [
     * 'adapter' => 'Mysql',
     * 'host' => 'localhost',
     * 'username' => 'root',
     * 'password' => 'root',
     * 'dbname' => 'tool',
     * 'options' => [
     * PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
     * ],
     * ]
     * ```
     * 其内容依据使用的数据库类型不同而有所区别，具体请参考(@link http://docs.phalconphp.com/en/latest/reference/db.html)
     * 他会自动添加一个名为db的数据库实例；使用方法示例：
     * ```
     * $db=\Joy::$di->get('db');
     * ```
     *
     * @param string $name
     * @param \Phalcon\Config $config
     * @return \Phalcon\Db\AdapterInterface
     */
    public function addDatabase($name, \Phalcon\Config $config)
    {
        /**
         * Database connection is created based in the parameters defined in the configuration file
         */
        \Joy::$di->set($name, function () use($config,$name)
        {

            $eventsManager = $this->getEventsManager();
            $adapter = '\Phalcon\Db\Adapter\Pdo\\' . $config->adapter;
            /**
             * @var $connection \Phalcon\Db\Adapter
             */
            $connection = new $adapter($config->toArray());
            // 设置SQL优化日志
            if (JOY_DEBUG || (defined('JOY_DB_DEBUG') && JOY_DB_DEBUG)) {
                // Attach logger & profiler
                $logger = new \Phalcon\Logger\Adapter\File($this->getRuntimePath() . '/logs/'.$name.'.log');
                /**
                 * @var $profiler \Phalcon\Db\Profiler
                 */
                $profiler = \Joy::$di->getProfiler();

                $eventsManager->attach($name, function ($event, $connection) use($logger, $profiler)
                {
                    if ($event->getType() == 'beforeQuery') {
                        $statement = $connection->getSQLStatement();
                        $profiler->startProfile($statement);
                    }
                    if ($event->getType() == 'afterQuery') {
                        // Stop the active profile
                        $profiler->stopProfile();
                        $last = $profiler->getLastProfile();
                        $logger->log($last->getSQLStatement()."\t\t" .json_encode($last->getSQLVariables())."\t\t" .$last->getTotalElapsedSeconds(), \Phalcon\Logger::DEBUG);
                    }
                });
                $connection->setEventsManager($eventsManager);
            }
            return $connection;
        });
    }

    /**
     * 设置数据库元数据的缓存
     * 支持以下的元数据存储方式：
     * Memory、Memcache、Redis
     *
     * @return \Phalcon\Mvc\Model\MetaData\Memory
     */
    protected function setMetaData()
    {
        \Joy::$di['modelsMetadata'] = function ()
        {
            /* @var $config \Phalcon\Config  */
            $config = \Joy::$config->components->metadata;
            $adapter = '\Phalcon\Mvc\Model\MetaData\\' . $config->get('adapter', 'Memory');
            $configDefault = new \Phalcon\Config([
                "lifetime" => 86400,
                "prefix" => "my-prefix"
            ]);
            // Instantiate a meta-data adapter
            $metaData = new $adapter($configDefault->merge($config));

            // Set a custom meta-data database introspection
            // 此处是设置支持字段别名
            if(\Phalcon\Version::get()>'2.0'){
            	$Annotations = '\Phalcon\Mvc\Model\MetaData\Strategy\Annotations';
            }else{
            	$Annotations = '\Phalcon\Mvc\Model\MetaData\Strategy\AnnotationsMetaData';
            }
            
            $metaData->setStrategy(new $Annotations());

            return $metaData;
        };
    }

    /**
     * 设置全局的模型管理器
     *
     * @return \Phalcon\Mvc\Model\Manager
     */
    protected function setModelsManager()
    {
        \Joy::$di->setShared('modelsManager', function ()
        {
            $eventsManager = new \Phalcon\Events\Manager();
            $modelsManager = new \Phalcon\Mvc\Model\Manager();
            $modelsManager->setEventsManager($eventsManager);
            return $modelsManager;
        });
    }

    /**
     * 设置缓存组件
     * 缓存的配置信息如下：
     * ```
     * 'cache' =>[
     * 'adapter' => 'Memcache',
     * 'host' => '127.0.0.1',
     * 'port' => 11211,
     * 'persistent' => 1
     * ]
     * ```
     * 如果您需要使用多台memcache服务器，请使用``Libmemcached``组件
     * 我们的系统支持下列组件：
     * ``File``、``Memcached``、``APC``、``Mongo``、``XCache``、``Redis``
     * 同时，我们还可以很方便的扩展更多的组件。关于各组件的配置方式请参考：
     * [@link http://docs.phalconphp.com/en/latest/reference/cache.html#backend-adapters cache]
     * 其中libMemcached与memcache的配置有所区别，请务必注意。
     * 如果需要配置多个缓存组件，请使用如下格式的配置文件：
     * ```
     * 'cache'=>[
     * [
     * [
     * ],
     * 'redis'=>[
     * ],
     * 'Mongo' => [
     * ]
     * ]
     * ]
     * ```
     * 数组的键将会被作为缓存的组件名的一部分，其中这个数组的KEY为0的初始化后的组件将被
     * 作为默认的缓存使用。缓存组件名的格式为：
     * ``cache_[[key]]``。
     */
    protected function setCache()
    {
        if (\Joy::$config->components->offsetExists('cache')) {
            $config = \Joy::$config->components->cache->toArray();
        } else {
            $config = [];
        }
        if (count($config['backend']) > 0) {
            $adapter = '\Phalcon\Cache\Frontend\\' . $config['frontend'];
            $frontCache = new $adapter(array(
                "lifetime" => 172800
            ));
            foreach ($config['backend'] as $key => $_config) {
                $adapter = '\Phalcon\Cache\Backend\\' . $_config['adapter'];
                unset($_config['adapter']);
                $cache = new $adapter($frontCache, $_config);
                $cacheKey = 'cache';
                if ($key !== 0) {
                    $cacheKey = 'cache_' . $key;
                }
                \Joy::$di->set($cacheKey, $cache);
            }
        }
    }
    /**
     * 设置密码安全及CSRF组件
     *
     * @return \Phalcon\Security
     */
    public function setSecurity()
    {
        \Joy::$di->set('security', function ()
        {

            $security = new \Phalcon\Security();
            $workFactor = 8;
            $config = \Joy::$config->components->toArray();
            if (isset($config['security']) && isset($config['security']['workFactor']))
                $workFactor = $config['security']['workFactor'];
                // Set the password hashing factor to 12 rounds
            $security->setWorkFactor($workFactor);

            return $security;
        }, true);
    }
    
    public function setCookies(){
         \Joy::$di->set('cookies', function() {
            $cookies = new \Phalcon\Http\Response\Cookies();
            $cookies->useEncryption(false);
            return $cookies;
        });
    }
    /**
     * 初始化系统组件
     * debug模式时会使用Phalcon\Debug进行调试
     * 否则会输出错误页面
     *
     * @throws \Phalcon\Exception
     * @return \Joy\Application
     */
    public function init()
    {
        if (JOY_DEBUG) {
            $debug = new \Phalcon\Debug();
        } else {
            $debug = new Debug();
            $debug->setView(\Joy::$config->errorPage);
        }
        $debug->setUri(JOY_DEBUG_URI);
        $debug->listen();
        $this->setEvents();
        $this->setLogger();
        $this->setModules();
        $this->setView();
        $this->setMongo();
        $this->setProfile();
        $this->setNotFound();
        $this->setDatabase();
        $this->setCache();
        $this->setModelsManager();
        $this->setMetaData();
        $this->setSecurity();
        $this->setCookies();
        return $this;
    }

    /**
     * 执行应用程序并处理异常
     */
    public function run()
    {
    	$stage = getenv('APPLICATION_STAGE') ? getenv('APPLICATION_STAGE') : '';
    	if(is_file(ROOT_PATH . "/config/web.{$stage}.php")){
    		$stage_config = ROOT_PATH . "/config/web.{$stage}.php";
    	}else{
    		$stage_config = ROOT_PATH . "/config/web.php";
    	}
        echo $this->configure($stage_config)
            ->init()
            ->handle()
            ->getContent();
    }

    /**
     * 输出debug信息
     *
     * @param string $message
     * @param string $context
     */
    public function debug($message, $context = null)
    {
        if ($this->_logger == null) {
            $this->_logger = \Joy::$di->get('logger');
        }
        $this->_logger->debug($message, $context);
    }
}