<?php
namespace Joy\Admin\Controllers;

! defined ( 'ROOT_PATH' ) && exit ( 'Do not allow direct access!' );

/**
 * MainController .
 *
 *
 *
 *
 * 部门管理
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
use Joy\Basic\Services\DepartmentServices;
use Joy\Basic\Models\Department;
class DepartmentController  extends Controller{

    private $_departmentServices;
    /**
     * 初始化服务
     * @access public
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->_departmentServices = new DepartmentServices();
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
        $re = $this->_departmentServices->getDepartmentTree();
        return $this->toJson($re);
    }
    /**
     * 添加/编辑页面
     */
    public function editAction(){
        $this->view->formId    = $this->request->get ( 'formId', 'string');
        $this->view->pid    = $this->request->get ( 'pid', 'string');
    }
    /**
     * 处理添加/编辑数据
     */
    public function doEditAction(){
        $departmentDate = [];
        $departmentDate['pid'] = $this->request->get ( 'pid', 'string');
        $departmentDate['departmentId'] = $this->request->get ( 'departmentId', 'string'); 
        $departmentDate['name'] = $this->request->get ( 'name', 'string');
        $departmentDate['note'] = $this->request->get ( 'note', 'string');

        $re = $this->_departmentServices->saveDate($departmentDate);
        return $this->jsonResult($re['code'],$re['msg']);
    }

    /**
     * 删除记录
     */
    public function delAction(){
        $ids     = $this->request->get ('ids');
        $re = $this->_departmentServices->delById(new Department(),$ids);
        return $this->jsonResult($re['code'],$re['msg']);
    }
}
