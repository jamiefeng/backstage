<?php
namespace Joy\RPC\XML;

/**
 * XML-RPC支持；注意：XML-RPC支持未经测试
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
            return new Router($http->getRawBody());
        });
    }

    protected function setResponse()
    {
        $this->_dependencyInjector->set('response', function ()
        {
            return new Response();
        });
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Joy\Application::init()
     * @return \Joy\RPC\XML\Application
     */
    public function init()
    {
        parent::init();
        $this->setRouter();
        $this->setResponse();
        return $this;
    }
}