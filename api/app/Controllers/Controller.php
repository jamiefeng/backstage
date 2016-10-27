<?php
namespace Joy\Biapi\Controllers;


/**
 * 公从controller
 * @author Bear
 *
 */
class Controller extends \Joy\Web\Controller
{

    /**
     * 通用公共设置
     *
     * @var array
     */
    public $globalSetting;

    /**
     * 初始化服务
     *
     * @access public
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->view->disableLevel(array(
            \Phalcon\Mvc\View::LEVEL_LAYOUT => true,
            \Phalcon\Mvc\View::LEVEL_MAIN_LAYOUT => true
        ));

        // 通用配置
        $this->view->globalSetting = $this->globalSetting = \Joy::$config ['globalSetting'];
    }
}