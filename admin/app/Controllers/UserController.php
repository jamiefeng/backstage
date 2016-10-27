<?php
namespace Joy\Admin\Controllers;

! defined ( 'ROOT_PATH' ) && exit ( 'Do not allow direct access!' );

/**
 * MainController .
 *
 *
 *
 *
 *  用户管理
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
use Joy\Basic\Services\UserServices;
use Joy\Basic\Services\RoleServices;
use Joy\Basic\Services\DepartmentServices;
use Joy\Basic\Models\User;
class UserController  extends Controller{

    private $_userServices;
    /**
     * 初始化服务
     * @access public
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->_userServices = new UserServices();
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
     * 获取部门数结构
     * @return string
     */
    public function getDepartmentTreeAction(){
        $departmentServices = new DepartmentServices();
        $re = $departmentServices->getUserDepartmentTree();
        return $this->toJson($re);
    }
    /**
     * 获取列表数据
     */
    public function ajaxlistAction(){      
        $params['departmentId'] = $this->request->get ( 'departmentId', 'string');
        $page     = $this->request->get ( 'page', 'int' ,1);
        $pageSize = $this->request->get ( 'pageSize', 'int' , $this->pageSize );
        //查询
        $params['username']  = $this->request->get ( 'username', 'string');
        $params['realName']  = $this->request->get ( 'realName', 'string');
        $params['mobile']  = $this->request->get ( 'mobile', 'string');
        $params['email']  = $this->request->get ( 'email', 'string');
        $re = $this->_userServices->getList($params, $page, $pageSize);
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
        $date = [];
        $date['userId'] = $this->request->get ( 'userId', 'string'); 
        $date['username'] = $this->request->get ( 'username', 'string');
        $date['sex'] = $this->request->get ( 'sex', 'string');
        $date['realName'] = $this->request->get ( 'realName', 'string');
        $date['tel'] = $this->request->get ( 'tel', 'string');
        $date['mobile'] = $this->request->get ( 'mobile', 'string');
        $date['fax'] = $this->request->get ( 'fax', 'string');
        $date['email'] = $this->request->get ( 'email', 'string');
        $date['departmentId'] = $this->request->get ( 'departmentId', 'string');
        $date['address'] = $this->request->get ( 'address', 'string');
        $re = $this->_userServices->saveDate($date);
        return $this->jsonResult($re['code'],$re['msg']);
    }
    /**
     * 设置用户状态
     */
    public function setStatusAction(){
        $date = [];
        $date['userId'] = $this->request->get ( 'userId', 'string');
        $status = $this->request->get ( 'status', 'string');
        if($status=='DISABLE'){
            $date['status'] = User::DISABLE;
        }elseif($status=='ENABLE'){
            $date['status'] = User::ENABLE;
        }else{
            return $this->jsonResult(100,'参数错误');
        }
        $re = $this->_userServices->saveDate($date);
        return $this->jsonResult($re['code'],$re['msg']);
    }
    /**
     * 检查名称唯一性
     */
    public function checkNameAction(){
        $username     = $this->request->get ( 'username', 'string');
        $userId = $this->request->get ( 'userId', 'string');
        $re = $this->_userServices->checkName($username,$userId);
        return $this->jsonResult($re['code'],$re['msg']);
    }
    /**
     * 删除记录
     */
    public function delAction(){
        $ids     = $this->request->get ('ids');
        $re = $this->_userServices->delById(new User(),$ids);
        return $this->jsonResult($re['code'],$re['msg']);
    }
    /**
     * 分配角色页面
     */
    public function addRoleAction(){
        
    }
    /**
     * 获取角色列表
     */
    public function getRoleListAction(){
        $roleServices = new RoleServices();
        $page     = $this->request->get ( 'page', 'int' ,1);
        $pageSize = $this->request->get ( 'pageSize', 'int' , $this->pageSize );
        //用户选中的角色
        $userId = $this->request->get ( 'userId', 'string');
        $userRole = $this->_userServices->getUserRole($userId);
        $params['name'] = $this->request->get ( 'name', 'string');
        $re = $roleServices->getList($params, $page, $pageSize);
        if($userRole && $re['rows']){
            foreach ($re['rows'] as &$v){
                foreach($userRole as $u){
                    if($u['roleId'] == $v['roleId']){
                        $v['checked'] = 1;
                    }
                }
            }
        }
        return $this->toJson($re);
    }
    /**
     * 获取用户角色
     */
    public function getUserRoleAction(){
        $roleServices = new RoleServices();
        $userId = $this->request->get ( 'userId', 'string');
        $userRole = $this->_userServices->getUserRole($userId);
        return $this->toJson($userRole);
    }
    /**
     * 添加用户角色
     * @return string
     */
    public function doAddRoleAction(){
        $userId = $this->request->get ( 'userId', 'string');
        $roleIds = $this->request->get ( 'roleIds');
        $re = $this->_userServices->updateUserRole($userId,$roleIds);
        return $this->jsonResult($re['code'],$re['msg']);
    }
    
}
