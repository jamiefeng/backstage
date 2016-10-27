<?php
namespace Joy\Bibasic\Controllers;

/**
 * 默认控制器目录的路由配置信息
 *
 * @author 
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
            'namespace' => 'Joy\Overseasbasic\Controllers'
        ]);
        $this->add("/",
            array("controller"=>'index',
                "action"=>'index'));
        
    }
}
