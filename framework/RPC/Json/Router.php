<?php
namespace Joy\RPC\Json;

/**
 * 处理Json-RPC的路由
 * 
 * @author dancebear
 *        
 */
class Router extends \Phalcon\Mvc\Router
{

    private $uri;

    public function setParams($params)
    {
        $this->_defaultParams = $params;
    }

    public function setUrl($uri)
    {
        $this->uri = $uri;
    }

    public function handle($uri = null)
    {
        if ($uri == null)
            $uri = $this->uri;
        parent::handle($uri);
    }
}