<?php
namespace Joy\RPC\XML;

/**
 * 处理Json-RPC的路由
 * 
 * @author dancebear
 *        
 */
class Router extends \Phalcon\Mvc\Router
{

    protected $_data;

    public function __construct($data)
    {
        $this->_data = $data;
    }

    public function handle($data = null)
    {
        if (! $data) {
            $data = $this->_data;
        }
        
        $data = xmlrpc_decode($data, 'utf-8');
        if ($data && xmlrpc_is_fault($data)) {
            trigger_error("xmlrpc: $data[faultString] ($data[faultCode])");
        }
        
        $method = explode('.', $data['methodName']);
        
        $this->_controller = $method[0];
        $this->_action = $method[1];
        $this->_params = $data['params'];
    }
}