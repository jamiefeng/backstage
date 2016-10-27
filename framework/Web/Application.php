<?php
namespace Joy\Web;
/**
 * 
 * @author dancebear
 * @property \Phalcon\Mvc\Url $url
 * @property \Phalcon\Flash\Session $flash
 * @property \Phalcon\Session\Adapter $session
 * @property \Phalcon\Mvc\Router\Route $router
 *
 */
class Application extends \Joy\Application
{

    /**
     * 设置flash组件，flash组件主要是用于在当前页面或跨页面传递信息，如用在用户输入数据的验证信息的显示
     *
     * @return \Phalcon\Flash\Direct|\Phalcon\Flash\Session
     */
    private function setFlash()
    {
        /**
         * Register the flash service with custom CSS classes
         */
        \Joy::$di->set('flash', function ()
        {
            $flash = new \Phalcon\Flash\Direct(array(
                'error' => 'alert alert-error',
                'success' => 'alert alert-success',
                'notice' => 'alert alert-info'
            ));
            return $flash;
        });
        
        \Joy::$di->set('flashSession', function ()
        {
            $flash = new \Phalcon\Flash\Session(array(
                'error' => 'alert alert-error',
                'success' => 'alert alert-success',
                'notice' => 'alert alert-info'
            ));
            return $flash;
        });
    }

    /**
     * 设置会话管理器
     * 
     * @return \Phalcon\Session\AdapterInterface|\Joy\Application
     */
    private function setSession()
    {
        if (\Joy::$config->components->offsetExists('session')) {
            $config = \Joy::$config->components->session->toArray();
        } else {
            $config = [];
        }
        /**
         * Start the session the first time some component request the session service
         */
        if(isset($config['adapter']) && $config['adapter']=='Memcache'){      
            \Joy::$di->set('session', function() use ($config){
            
                $session = new \Phalcon\Session\Adapter\Memcached([
                      'host'       => $config['servers'][0]['host'],
                      'port'       => $config['servers'][0]['port'],
                      'persistent' => $config['persistent'],
                      'lifetime'   => $config['lifetime'],
                      'prefix'     => $config['prefix']
                  ]);
                $session->start();
                return $session;
            });
        }else{
            \Joy::$di->set('session', function ()
            {
                $session = new \Phalcon\Session\Adapter\Files();
                $session->start();
                return $session;
            });
        }
        return $this;
    }

    /**
     * 设置应用程序的路由信息
     * 当处于debug模式时，程序自动读取控制器及动作的注释路由信息
     * 当程序处于线上模式时，程序将加载配置文件目录下的router.php，
     * 因此在上线之前，我们需要生成一次路由信息。
     * 
     * @link http://docs.phalconphp.com/en/latest/reference/routing.html
     * @see \Phalcon\Mvc\Router\Route
     */
    private function setRouter()
    {
        /**
         * Include the application routes
         */
        $config = \Joy::$config->components->router;
        $this->_dependencyInjector->set('router', function () use($config)
        {
            $router = new \Phalcon\Mvc\Router();
            $class = $this->defaultNamespace . '\\Controllers\\Routes';
            $router->mount(new $class());
            $modules = $this->getModules();
            foreach ((array) $modules as $module => $moduleDefine) {
                $class = $this->defaultNamespace . '\\' . $moduleDefine['moduleName'] . '\\Controllers\\Routes';
                $router->mount(new $class());
            }
            return $router;
        });
    }

    /**
     * 设置URL组件的默认URL
     *
     * @return \Phalcon\Mvc\Url
     */
    private function setUrl()
    {
        /**
         * The URL component is used to generate all kind of urls in the application
         */
        \Joy::$di->set('url', function ()
        {
            $url = new \Phalcon\Mvc\Url();
            $url->setBaseUri(\Joy::$config->application->baseUri);
            return $url;
        });
    }

    /**
     * 初始化用于Web应用的其他服务
     * 
     * @see \Joy\Application::init()
     * @return \Joy\RPC\Json\Application
     */
    public function init()
    {
        parent::init();
        $this->setUrl();
        $this->setRouter();
        $this->setFlash();
        $this->setSession();
        return $this;
    }
    
//     public function handle($uri = null)
//     {
    
//         $dependencyInjector = $this->_dependencyInjector;
//         if (!is_object($dependencyInjector)) {
//             throw new \Exception("A dependency injection object is required to access internal services");
//         }
    
//         $eventsManager = $this->_eventsManager;
    
//         /**
//          * Call boot event, this allow the developer to perform initialization actions
//          */
//         if (is_object($eventsManager)){
//             if ($eventsManager->fire("application:boot", $this) === false ){
//                 return false;
//             }
//         }
        
//         /**
//          * @var $router \Phalcon\Mvc\Router 
//          */
//         $router = $dependencyInjector->getShared("router");
    
//         /**
//          * Handle the URI pattern (if any)
//         */
//         $router->handle($uri);
        
//         /**
//          * If the router doesn't return a valid module we use the default module
//         */
//         $moduleName = $router->getModuleName();
//         if (!$moduleName) {
//             $moduleName = $this->_defaultModule;
//         }
    
//         $moduleObject = null;
    
//         /**
//          * Process the module definition
//          */
//         if ($moduleName) {
    
//             if (is_object($eventsManager)) {
//                 if ($eventsManager->fire("application:beforeStartModule", $this, $moduleName) === false) {
//                     return false;
//                 }
//             }
    
//             /**
//              * Gets the module definition
//              */
//             $module = $this->getModule($moduleName);
    
//             /**
//              * A module definition must ne an array or an object
//             */
//             if (is_array($module)&&!is_object($module)) {
//                 throw new \Exception("Invalid module definition");
//             }
    
//             /**
//              * An array module definition contains a path to a module definition class
//              */
//             if (is_array($module)){
    
//                 /**
//                  * Class name used to load the module definition
//                  */
//                 if (isset($module["className"])) {
//                     $className=$module["className"];
//                 }else{
//                    $className = "Module";
//                 }
    
//                 /**
//                  * If developer specify a path try to include the file
//                  */
//                 if (isset($module["path"])&&$path=$module["path"] ){
//                     if (!class_exists($className)) {
//                         if (file_exists($path)) {
//                             require $path;
//                         } else {
//                             throw new \Exception("Module definition path '" . $path . "' doesn't exist");
//                         }
//                     }
//                 }
    
//                 $moduleObject = $dependencyInjector->get($className);
    
//                 /**
//                  * 'registerAutoloaders' and 'registerServices' are automatically called
//                 */
//                 $moduleObject->registerAutoloaders($dependencyInjector);
//                 $moduleObject->registerServices($dependencyInjector);
    
//             } else {
    
//                 /**
//                  * A module definition object, can be a Closure instance
//                  */
//                 if ($module instanceof \Closure) {
//                     $moduleObject = call_user_func_array($module, [$dependencyInjector]);
//                 } else {
//                     throw new \Exception("Invalid module definition");
//                 }
//             }
    
//             /**
//              * Calling afterStartModule event
//              */
//             if($eventsManager) {
//                 $eventsManager->fire("application:afterStartModule", $this, $moduleObject);
//             }
    
//         }
    
//         /**
//          * Check whether use implicit views or not
//          */
//         $implicitView = $this->_implicitView;
    
//         if ($implicitView === true ){
//             $view = $dependencyInjector->getShared("view");
//         }
    
//         /**
//          * We get the parameters from the router and assign them to the dispatcher
//          * Assign the values passed from the router
//          */
//         $dispatcher = $dependencyInjector->getShared("dispatcher");
//         $dispatcher->setModuleName($router->getModuleName());
//         $dispatcher->setNamespaceName($router->getNamespaceName());
//         $dispatcher->setControllerName($router->getControllerName());
//         $dispatcher->setActionName($router->getActionName());
//         $dispatcher->setParams($router->getParams());
    
//         /**
//          * Start the view component (start output buffering)
//         */
//         if ($implicitView === true ){
//             $view->start();
//         }
    
//         /**
//          * Calling beforeHandleRequest
//          */
//         if (is_object($eventsManager)) {
//             if($eventsManager->fire("application:beforeHandleRequest", $this, $dispatcher) === false ){
//                 return false;
//             }
//         }
    
//         /**
//          * The dispatcher must return an object
//          */
//         $controller = $dispatcher->dispatch();
    
//         /**
//          * Get the latest value returned by an action
//         */
//         $possibleResponse = $dispatcher->getReturnedValue();
//         if (is_object($possibleResponse)) {
    
//             /**
//              * Check if the returned object is already a response
//              */
//             $returnedResponse = $possibleResponse instanceof \Phalcon\Http\ResponseInterface;
//         } else {
//             $returnedResponse = false;
//         }
    
//         /**
//          * Calling afterHandleRequest
//          */
//         if ($eventsManager == "object" ){
//             $eventsManager->fire("application:afterHandleRequest", $this, $controller);
//         }
    
//         /**
//          * If the dispatcher returns an object we try to render the view in auto-rendering mode
//          */
//         if ($returnedResponse === false ){
//             if($implicitView === true ){
//                 if(is_object($controller)) {
    
//                     $renderStatus = true;
    
//                     /**
//                      * This allows to make a custom view render
//                      */
//                     if (is_object($eventsManager)){
//                         $renderStatus = $eventsManager->fire("application:viewRender", $this, $view);
//                     }
    
//                     /**
//                      * Check if the view process has been treated by the developer
//                      */
//                     if($renderStatus !== false) {
    
//                         /**
//                          * Automatic render based on the latest controller executed
//                          */
//                         $view->render(
//                             $dispatcher->getControllerName(),
//                             $dispatcher->getActionName(),
//                             $dispatcher->getParams()
//                         );
//                     }
//                 }
//             }
//         }
    
//         /**
//          * Finish the view component (stop output buffering)
//          */
//         if ($implicitView === true) {
//             $view->finish();
//         }
    
//         if($returnedResponse === false) {
    
//             $response = $dependencyInjector->getShared("response");
//             if($implicitView === true ){
    
//                 /**
//                  * The content returned by the view is passed to the response service
//                  */
//                 $response->setContent($view->getContent());
//             }
    
//         } else {
    
//             /**
//              * We don't need to create a response because there is one already created
//              */
//             $response = $possibleResponse;
//         }
    
//         /**
//          * Calling beforeSendResponse
//          */
//         if (is_object($eventsManager)){
//             $eventsManager->fire("application:beforeSendResponse", $this, $response);
//         }
    
//         /**
//          * Headers and Cookies are automatically send
//          */
//         $response->sendHeaders();
//         $response->sendCookies();
    
//         /**
//          * Return the response
//         */
//         return $response;
//     }
}