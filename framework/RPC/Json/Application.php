<?php
namespace Joy\RPC\Json;

/**
 * Json-RPC 应用程序；当前不支持批量操作
 * 
 * @author dancebear
 *        
 */
class Application extends \Joy\Application
{

    protected function setRouter()
    {
        $this->_dependencyInjector->set('router', function ()
        {
            $http = new \Phalcon\Http\Request();
            $data = $http->getRawBody();
            $data = json_decode($data, true);
            if (! isset($data['jsonrpc'])) {
                throw new \Phalcon\Mvc\Router\Exception("The request is not Json-RPC");
            }
            
            // \Joy::$di->set('json::id',isset($data['id'])?$data['id']:null);
            
            $router = new Router();
            $router->setParams($data['params']);
            $router->setUrl('/' . str_replace('.', '/', $data['method']));
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

    protected function setResponse()
    {
        $this->_dependencyInjector->set('response', function ()
        {
            return new Response();
        });
    }

    public function init()
    {
        parent::init();
        $this->setRouter();
        $this->setResponse();
        return $this;
    }
}