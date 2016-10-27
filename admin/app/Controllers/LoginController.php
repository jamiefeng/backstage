<?php
namespace Joy\Admin\Controllers;

! defined ( 'ROOT_PATH' ) && exit ( 'Do not allow direct access!' );

/**
 * MainController .
 *
 *
 *
 *
 * 管理后台-登录
 * ==============================================
 * 版权所有 2010-2016
 * ----------------------------------------------
 * 未经授权不许任何使用和传播。
 * ==============================================
 * @date: 2016-7-4
 *
 * @author : fengyanchao
 * @version :
 *
 */
use Joy\Basic\Services\UserServices;
use Joy\Basic\Models\User;
use Joy\Basic\Services\AclServices;

class LoginController  extends Controller{
    /*
     * 用户服务类
     */
    private $_userServices;
    /**
     * 初始化服务
     * @access public
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->_userServices  = new UserServices();
    }
    /**
     * 默认主页
     * 
     * @access public
     * @return void
     */
    public function indexAction() {
        
        //清空session
        $this->session->destroy();
        //保存用户名
        $this->view->username = $this->cookies->get('username');
        if ($this->request->getPost()) {
            $userModel = new User();
            $userModel->username = $this->request->get ( 'username', 'string' );
            $userModel->password = $this->request->get ( 'password', 'string' );
            $selectFlag = $this->request->get ( 'selectFlag', 'int' );
            if($selectFlag){
                $this->cookies->set('username', $userModel->username,time()+86400*30);
            }else{
                $this->cookies->set('username', '');
            }
            //判断登录
            $userLogin = $this->_userServices->doLogin($userModel);
            if($userLogin['code']===0){
                //注册session
                $adminInfo = ['userId'=>$userLogin['data']['userId'],
                    'realName'=>$userLogin['data']['realName'],
                    'username'=>$userLogin['data']['username'],
                    'email'=>$userLogin['data']['email']
                ];
                //获取用户菜单权限
                $this->aclServices = new AclServices();
                $adminInfo['aclState'] = $this->aclServices->getModuleAclByUserId($userLogin['data']['userId']);
                
                $this->session->set('userInfo',$adminInfo);
                return $this->jsonResult($userLogin['code'],'登录成功');
            }else{
                return $this->jsonResult($userLogin['code'],$userLogin['msg']);
            }
        }
    }
}
