<?php
namespace Joy\Admin\Controllers;

! defined ( 'ROOT_PATH' ) && exit ( 'Do not allow direct access!' );

/**
 * MainController .
 *
 *
 *
 *
 * 平台管理
 * ==============================================
 * 版权所有 2010-2016
 * ----------------------------------------------
 * 未经授权不许任何使用和传播。
 * ==============================================
 * @date: 2016-7-4
 *
 * @author : 冯焰超 
 * @version :
 *
 */
use Joy\Basic\Services\PlatformServices;
use Joy\Basic\Models\Platform;
class PlatformController  extends Controller{

    private $_platformServices;
    /**
     * 初始化服务
     * @access public
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->_platformServices = new PlatformServices();
    }
    /**
     * 默认主页
     * 
     * @access public
     * @return void
     */
    public function indexAction() {
        
    }
    /**
     * 获取列表数据
     */
    public function ajaxlistAction(){
        $page     = $this->request->get ( 'page', 'int' ,1);
        $pageSize = $this->request->get ( 'pageSize', 'int' , $this->pageSize );
        //系统名称查询
        $params['name']  = $this->request->get ( 'name', 'string');
        $re = $this->_platformServices->getList($params, $page, $pageSize);
        return $this->toJson($re);
    }
    /**
     * 添加/编辑页面
     */
    public function editAction(){
        $this->view->formId    = $this->request->get ( 'formId', 'string');
    }
    /**
     * 处理添加/编辑数据
     */
    public function doEditAction(){
        $pfsystemDate = [];
        $pfsystemDate['platformId'] = $this->request->get ( 'platformId', 'string'); 
        $pfsystemDate['name'] = $this->request->get ( 'name', 'string');
        $pfsystemDate['note'] = $this->request->get ( 'note', 'string');
        $pfsystemDate['orderNo'] = $this->request->get ( 'orderNo', 'int');

        $re = $this->_platformServices->saveDate($pfsystemDate);
        return $this->jsonResult($re['code'],$re['msg']);
    }
    /**
     * 检查名称唯一性
     */
    public function checkNameAction(){
        $name     = $this->request->get ( 'name', 'string');
        $platformId = $this->request->get ( 'platformId', 'string');
        $re = $this->_platformServices->checkName($name,$platformId);
        return $this->jsonResult($re['code'],$re['msg']);
    }
    /**
     * 删除记录
     */
    public function delAction(){
        $ids     = $this->request->get ('ids');
        $re = $this->_platformServices->delById(new Platform(),$ids);
        return $this->jsonResult($re['code'],$re['msg']);
    }
}
