<?php
namespace Joy\Admin\Controllers;

! defined ( 'ROOT_PATH' ) && exit ( 'Do not allow direct access!' );

/**
 * MainController .
 *
 *
 *
 *
 * 系统管理-登录
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
use Joy\Basic\Services\PfsystemServices;
use Joy\Basic\Services\PlatformServices;
use Joy\Basic\Models\Pfsystem;
class PfsystemController  extends Controller{

    private $_pfsystemServices;
    /**
     * 初始化服务
     * @access public
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->_pfsystemServices = new PfsystemServices();
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
     * 获取平台列表数据
     */
    public function getPlatformAction(){
        //获取所有系统列表
        $array = [];
        $platformServices = new PlatformServices();
        $re = $platformServices->getList([], 1, 10000);
        if($re['rows']){
            foreach($re['rows'] as $v){
                $v['text'] = $v['name'];
                $array[] = $v;
            }
        }
        return $this->toJson($array);
    }
    /**
     * 获取列表数据
     */
    public function ajaxlistAction(){
        $params['platformId']    = $this->request->get ( 'platformId', 'string' ,'');
        $re = $this->_pfsystemServices->getList($params, 1, 10000);
        return $this->toJson($re);
    }
    /**
     * 添加/编辑页面
     */
    public function editAction(){
        $this->view->formId    = $this->request->get ( 'formId', 'string');
        $this->view->platformId    = $this->request->get ( 'platformId', 'string');
    }
    /**
     * 处理添加/编辑数据
     */
    public function doEditAction(){
        $pfsystemDate = [];
        $pfsystemDate['systemId'] = $this->request->get ( 'systemId', 'string'); 
        $pfsystemDate['platformId']    = $this->request->get ( 'platformId', 'string');
        $pfsystemDate['name'] = $this->request->get ( 'name', 'string');
        $pfsystemDate['note'] = $this->request->get ( 'note', 'string');
        $pfsystemDate['orderNo'] = $this->request->get ( 'orderNo', 'int');
        $pfsystemDate['url'] = $this->request->get ( 'url', 'string');
        $re = $this->_pfsystemServices->saveDate($pfsystemDate);
        return $this->jsonResult($re['code'],$re['msg']);
    }
    /**
     * 检查名称唯一性
     */
    public function checkNameAction(){
        $name     = $this->request->get ( 'name', 'string');
        $systemId = $this->request->get ( 'systemId', 'string');
        $re = $this->_pfsystemServices->checkName($name,$systemId);
        return $this->jsonResult($re['code'],$re['msg']);
    }
    /**
     * 删除记录
     */
    public function delAction(){
        $ids     = $this->request->get ('ids');
        $re = $this->_pfsystemServices->delById(new Pfsystem(),$ids);
        return $this->jsonResult($re['code'],$re['msg']);
    }
}
