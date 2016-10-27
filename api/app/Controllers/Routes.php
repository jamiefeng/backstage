<?php
namespace Joy\Biapi\Controllers;

/**
 * 默认控制器目录的路由配置信息
 *
 * @author jamie 
 */
class Routes extends \Phalcon\Mvc\Router\Group
{

    /**
     * 路由初始化
     *
     * @access public
     * @param string $paths 路径
     * @return void
     */
    public function initialize($paths)
    {
             
        // Default paths
        $this->setPaths([
            'namespace' => 'Joy\Biapi\Controllers'
        ]);
        //引用js
        $this->add('/bijs.html', array(
            'controller' => 'bijs',
            'action' => 'index'
        ))->setName('bijs_index');
        //记录数据
        $this->add('/log.html', array(
            'controller' => 'log',
            'action' => 'index'
        ))->setName('log_index');
    }
}
