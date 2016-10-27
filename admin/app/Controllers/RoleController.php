<?php
namespace Joy\Admin\Controllers;

! defined ( 'ROOT_PATH' ) && exit ( 'Do not allow direct access!' );

/**
 * MainController .
 *
 *
 *
 *
 * 角色管理
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
use Joy\Basic\Services\RoleServices;
use Joy\Basic\Services\ModuleServices;
use Joy\Basic\Services\AclServices;
use Joy\Basic\Models\Role;
class RoleController  extends Controller{

    private $_roleServices;
    /**
     * 初始化服务
     * @access public
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->_roleServices = new RoleServices();
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
        $re = $this->_roleServices->getList($params, $page, $pageSize);
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
        $roleDate = [];
        $roleDate['roleId'] = $this->request->get ( 'roleId', 'string'); 
        $roleDate['name'] = $this->request->get ( 'name', 'string');
        $roleDate['note'] = $this->request->get ( 'note', 'string');
        $re = $this->_roleServices->saveDate($roleDate);
        return $this->jsonResult($re['code'],$re['msg']);
    }
    /**
     * 检查名称唯一性
     */
    public function checkNameAction(){
        $name     = $this->request->get ( 'name', 'string');
        $roleId = $this->request->get ( 'roleId', 'string');
        $re = $this->_roleServices->checkName($name,$roleId);
        return $this->jsonResult($re['code'],$re['msg']);
    }
    /**
     * 删除记录
     */
    public function delAction(){
        $ids     = $this->request->get ('ids');
        $re = $this->_roleServices->delById(new Role(),$ids);
        return $this->jsonResult($re['code'],$re['msg']);
    }
    /**
     * 分配权限
     */
    public function rolemoduleAction(){
        
    }
    /**
     * 获取系统模块列表数据
     */
    public function getSystemModulePvalueAction(){
        $systemId = $this->request->get ( 'systemId', 'string');
        $releaseSn = $this->request->get ( 'releaseSn', 'string');
        $releaseId = $this->request->get ( 'releaseId', 'string');
        $moduleServices = new ModuleServices();
        $rows = $moduleServices->getModuleListTree($systemId);
        //判断此角色是否拥有操作权限
        $aclServices = new AclServices();
        $acl = $aclServices->getListByRelease($releaseSn, $releaseId);
        foreach($rows as &$mv){
            foreach($acl as $av){
                if($mv['moduleId']==$av['moduleId']){
                    foreach ($mv['pvs'] as &$pvs){
                        $pvs['flag'] = $aclServices->getPermission($av['aclState'],$pvs['position']);
                    }
                }
            }
        }
        $re = ['totla'=>count($rows),'rows'=>$rows];
        return $this->toJson($re);
    }
    /**
     * 设置角色操作权限
     */
    public function setaclAction(){
        $re = ['code'=>200,'msg'=>'失败'];
        $platformId = $this->request->get ( 'platformId', 'string');
        $systemId = $this->request->get ( 'systemId', 'string');
        $moduleId = $this->request->get ( 'moduleId', 'string');
        $position = $this->request->get ( 'position', 'string');
        $releaseSn = $this->request->get ( 'releaseSn', 'string');
        $releaseId = $this->request->get ( 'releaseId', 'string');
        $yes = $this->request->get ( 'yes');
        $aclServices = new AclServices();
        
        if(empty($moduleId)){//整个系统全选/取消
            if($systemId && $releaseSn && $releaseId){//必须有值
                if($yes=='true'){//选择
                    $re = $aclServices->addAclBySystemId($platformId,$systemId, $releaseSn, $releaseId);
                }else{
                    $re = $aclServices->delRealById(['systemId'=>$systemId,'releaseSn'=>$releaseSn,'releaseId'=>$releaseId]);
                }
            }
        }elseif(!isset($position)){ //整个模块
            
            if($systemId && $releaseSn && $releaseId && $moduleId){//必须有值
                if($yes=='true'){//选择
                    $re = $aclServices->addAclByModuleId($platformId,$moduleId, $releaseSn, $releaseId);
                }else{
                    $re = $aclServices->delRealById(['systemId'=>$systemId,'releaseSn'=>$releaseSn,'releaseId'=>$releaseId,'moduleId'=>$moduleId]);
                }
            }
        }else{//单个
            
            if($systemId && $releaseSn && $releaseId && $moduleId && isset($position)){//必须有值
                if($yes=='true'){
                    $yes = true;//选择
                }else{
                    $yes = false;//取消';
                }
                $re = $aclServices->updateState(['platformId'=>$platformId,'systemId'=>$systemId,'releaseSn'=>$releaseSn,'releaseId'=>$releaseId,'moduleId'=>$moduleId,'position'=>$position,'yes'=>$yes]);
            }
        }
        return $this->toJson($re);
    }
}
