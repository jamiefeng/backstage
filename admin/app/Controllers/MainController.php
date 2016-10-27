<?php
namespace Joy\Admin\Controllers;

! defined ( 'ROOT_PATH' ) && exit ( 'Do not allow direct access!' );

/**
 * MainController .
 *
 *
 *
 *
 * 管理后台-管理主页
 * ==============================================
 * 版权所有 2010-2016 
 * ----------------------------------------------
 * 未经授权不许任何使用和传播。
 * ==============================================
 * @date: 2016-7-4
 *
 * @author :  <冯焰超>
 * @version :
 *
 */
use Joy\Basic\Services\PlatformServices;
use Joy\Basic\Services\AclServices;
use Joy\Basic\Services\UserServices;
use Joy\Basic\Services\ModuleServices;
use Joy\Basic\Models\User;


class MainController  extends Controller{
    
    private $_aclServices;
    private $_moduleServices;
    /**
     * 初始化服务
     * @access public
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->_aclServices = new AclServices();
        $this->_moduleServices = new ModuleServices();
    }
    
    /**
     * 默认主页
     * 
     * @access public
     * @return void
     */
    public function indexAction() {
        $platformServices = new PlatformServices();
        $this->view->platform = $platformServices->getUserPlatform($this->userInfo['userId']);
        if(empty($this->view->platform)){
            die('此账号没有任何权限，<a href="javascript:history.go(-1)">后退>></a>');
        }
    }
    /**
     * 系统首页
     *
     * @access public
     * @return void
     */
    public function systemAction() {
        $platformId = $this->request->get ( 'platformId', 'string' );
        $this->view->systems = $this->_aclServices->getSystemsByUserId($this->userInfo['userId'],$platformId);
        if(empty($platformId) || empty($this->view->systems)){
            die('此账号没有任何权限，<a href="javascript:history.go(-1)">后退>></a>');
        }
        $this->view->loadSystemId  = $this->view->systems[0]['systemId'];
        $this->view->loadSystemUrl = $this->view->systems[0]['url'];
        $this->view->loadSystemName = $this->view->systems[0]['name'];
        //获取平台信息
        $platformServices = new PlatformServices();
        $this->view->platform = $platformServices->getOneByPlatformId($platformId);
    }
    /**
     * 系统桌面
     */
    public function desktopAction(){
        
    }
    /**
     * 获得左侧菜单
     */
    public function lefttreeAction() {
        $systemId = $this->request->get ( 'systemId', 'string' );
        $url = $this->request->get ( 'url', 'string' );
        $data = $this->_moduleServices->getModuleTree($systemId,$url,$this->sessionId,$this->userInfo['aclState']);
        return $this->toJson($data);
    }
    /**
     * 重置密码
     */
    public function setpasswordAction(){
        if ($this->request->getPost()) {
            $oldpwd = $this->request->get ( 'oldpwd', 'string' );
            $newpwd = $this->request->get ( 'newpwd', 'string' );
            $renewpwd = $this->request->get ( 'renewpwd', 'string' );
            if(empty($oldpwd) || empty($oldpwd) || empty($oldpwd)){
                return $this->jsonResult(100,'信息错误');
            }
            if($newpwd!=$renewpwd){
                return $this->jsonResult(100,'两次输入的密码不一致');
            }
            $userServices = new UserServices();
            $userModel = new User();
            $userModel->username = $this->userInfo['username'];
            $userModel->password = $oldpwd;
            $userLogin = $userServices->doLogin($userModel);
            if($userLogin['code']==0){
                //重置密码
                $date = $userLogin['data'];
                $date['userId']   = $this->userInfo['userId'];
                $date['password'] = md5(md5($this->globalSetting['passwordPrefix'].$newpwd));
                $re = $userServices->saveDate($date);
                return $this->jsonResult($re['code'],$re['msg']);
            }
            return $this->jsonResult(200,'原始密码错误');
        }
    }
}
