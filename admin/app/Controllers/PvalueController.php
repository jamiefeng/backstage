<?php
namespace Joy\Admin\Controllers;

! defined ( 'ROOT_PATH' ) && exit ( 'Do not allow direct access!' );

/**
 * MainController .
 *
 *
 *
 *
 * 权限值管理
 * ==============================================
 * 版权所有 2010-2016
 * ----------------------------------------------
 * 未经授权不许任何使用和传播。
 * ==============================================
 * @date: 2016-10-24
 *
 * @author : 冯焰超 
 * @version :
 *
 */
use Joy\Basic\Services\PvalueServices;
use Joy\Basic\Models\Pvalue;
class PvalueController  extends Controller{

    private $_pvalueServices;
    /**
     * 初始化服务
     * @access public
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->_pvalueServices = new PvalueServices();
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
     * 获取权限值列表数据
     */
    public function getPvalueAction(){
        $systemId = $this->request->get ( 'systemId', 'string');
        $re = $this->_pvalueServices->getPvalueBySystemId($systemId);
        return $this->toJson($re);
    }
    
    /**
     * 添加/编辑页面
     */
    public function editAction(){
        $this->view->formId    = $this->request->get ( 'formId', 'string');
        $this->view->id = $this->request->get ( 'id', 'int');
        $this->view->systemId =  $this->request->get ( 'systemId', 'string');
        //添加
        if($this->view->formId == 'formA'){
            $this->view->maxPvalue = $this->_pvalueServices->getMaxPvalueBySystemId($this->view->systemId);
        }
    }
    /**
     * 处理添加/编辑数据
     */
    public function doEditAction(){
        $pvalueDate = [];
        $pvalueDate['id'] = $this->request->get ( 'id', 'int');
        $pvalueDate['systemId'] = $this->request->get ( 'systemId', 'string'); 
        $pvalueDate['name'] = $this->request->get ( 'name', 'string');
        $pvalueDate['sign'] = $this->request->get ( 'sign', 'string');
        $pvalueDate['position'] = $this->request->get ( 'position', 'int');
        $pvalueDate['orderNo'] = $this->request->get ( 'orderNo', 'int');
        $pvalueDate['remark'] = $this->request->get ( 'remark', 'string');
        $re = $this->_pvalueServices->saveDate($pvalueDate);
        return $this->jsonResult($re['code'],$re['msg']);
    }
    /**
     * 检查标识唯一性
     */
    public function checkSignAction(){
        $id = $this->request->get ( 'id', 'int');
        $sign     = $this->request->get ( 'sign', 'string');
        $systemId = $this->request->get ( 'systemId', 'string');
        $re = $this->_pvalueServices->checkSign($id,$sign,$systemId);
        return $this->jsonResult($re['code'],$re['msg']);
    }
    /**
     * 删除记录
     */
    public function delAction(){
        $ids     = $this->request->get ('ids');
        $re = $this->_pvalueServices->delRealById($ids);
        return $this->jsonResult($re['code'],$re['msg']);
    }
}
