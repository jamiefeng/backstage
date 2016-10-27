<?php
namespace Joy\Admin\Controllers;

! defined ( 'ROOT_PATH' ) && exit ( 'Do not allow direct access!' );

/**
 * MainController .
 *
 *
 *
 *
 * 模块管理-登录
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
use Joy\Basic\Services\ModuleServices;
use Joy\Basic\Services\PfsystemServices;
use Joy\Basic\Services\PvalueServices;
use Joy\Basic\Services\AclServices;
use Joy\Basic\Models\Module;
class ModuleController  extends Controller{

    private $_moduleServices;
    private $_pfsystemServices;
    /**
     * 初始化服务
     * @access public
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->_moduleServices = new ModuleServices();
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
     * 获取系统列表数据
     */
    public function getSystemsAction(){
        //获取所有系统列表
        $re = $this->_pfsystemServices->getPfsystemTree();
        return $this->toJson($re);
    }
    
    /**
     * 获取系统模块列表数据
     */
    public function getModuleAction(){
        $systemId = $this->request->get ( 'systemId', 'string');
        $re = $this->_moduleServices->getModuleListTree($systemId);
        return $this->toJson($re);
    }
    
    /**
     * 添加/编辑页面
     */
    public function editAction(){
        $this->view->formId    = $this->request->get ( 'formId', 'string');
        $this->view->pid = $this->request->get ( 'pid', 'string');
        $this->view->systemId = $this->request->get ( 'systemId', 'string');
    }
    /**
     * 处理添加/编辑数据
     */
    public function doEditAction(){
        $moduleDate = [];
        $moduleDate['moduleId'] = $this->request->get ( 'moduleId', 'string');
        $moduleDate['pid'] = $this->request->get ( 'pid', 'string');
        $moduleDate['systemId'] = $this->request->get ( 'systemId', 'string'); 
        $moduleDate['name'] = $this->request->get ( 'name', 'string');
        $moduleDate['orderNo'] = $this->request->get ( 'orderNo', 'int');
        $moduleDate['url'] = $this->request->get ( 'url', 'string');
        $re = $this->_moduleServices->saveDate($moduleDate);
        return $this->jsonResult($re['code'],$re['msg']);
    }
    /**
     * 删除记录
     */
    public function delAction(){
        $ids     = $this->request->get ('ids');
        $re = $this->_moduleServices->delById(new Module(),$ids);
        return $this->jsonResult($re['code'],$re['msg']);
    }
    
    /**
     * 添加操作权限页面
     */
    public function insertPriValAction(){
        $moduleId  = $this->request->get ( 'moduleId', 'string');
        $systemId  = $this->request->get ( 'systemId', 'string');
        $module    = $this->_moduleServices->getOneByModuleId($moduleId);
        $pvalueServices = new PvalueServices();
        $pvalue    = $pvalueServices->getPvalueBySystemId($systemId);
        if($module && $pvalue){
            $aclServices = new AclServices();
            foreach($pvalue as $k=>$v){
                //已经拥有权限的去掉
                $isok = $aclServices->getPermission($module['state'],$v['position']);
                if($isok){
                    unset($pvalue[$k]);
                }
            }
        }
        $this->view->pvalue = $pvalue;
    }
    /**
     * 添加操作权限
     */
    public function doInsertPriValAction(){
        $moduleId  = $this->request->get ( 'moduleId', 'string');
        $position = $this->request->get ( 'position');
        $module    = $this->_moduleServices->getOneByModuleId($moduleId);
        if($module && $position){
            $aclServices = new AclServices();
            $aclState = $module['state'];
            foreach($position as $v){
                $aclState = $aclServices->setPermission($aclState, $v, true);
            }
            $moduleData = ['moduleId'=>$module['moduleId'],'state'=>$aclState];
            $re = $this->_moduleServices->saveDate($moduleData);
            return $this->jsonResult($re['code'],$re['msg']);
        }else{
            return $this->jsonResult(100,'数据错误');
        }
    }
    /**
     * 删除操作权限
     */
    public function deletePriValAction(){
        $position = $this->request->get ('position','int');
        $moduleId = $this->request->get ( 'moduleId', 'string');
        $re = $this->_moduleServices->deletePriVal($moduleId,$position);
        return $this->jsonResult($re['code'],$re['msg']);
    }
}
