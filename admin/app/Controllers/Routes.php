<?php
namespace Joy\Admin\Controllers;
! defined ( 'ROOT_PATH' ) && exit ( 'Do not allow direct access!' );


/**
 * *Routes.php文件
 * ==============================================
 * 版权所有 2010-2016
 * ----------------------------------------------
 * 未经授权不许任何使用和传播。
 * ==============================================
 * @date: 2016-7-4
 *
 * @author : jamie
 *
 */

class Routes extends \Phalcon\Mvc\Router\Group
{

    /**
     * 路由初始化
     *
     * @access public
     * @param string $paths 路径
     * @return void
     */
    public function initialize($paths)
    {         
        // Default paths
        $this->setPaths([
            'namespace' => 'Joy\Admin\Controllers'
        ]);
        //默认
        $this->add('/', array(
            'controller' => 'main',
            'action' => 'index'
        ));
        //登录页面
        $this->add('/login/index.html', array(
            'controller' => 'login',
            'action' => 'index'
        ))->setName('login_login');
        
        //首页
        $this->add('/main/index.html', array(
            'controller' => 'main',
            'action' => 'index'
        ))->setName('main_index');
        //系统首页
        $this->add('/main/system.html', array(
            'controller' => 'main',
            'action' => 'system'
        ))->setName('main_system');
        //系统桌面
        $this->add('/main/desktop.html', array(
            'controller' => 'main',
            'action' => 'desktop'
        ))->setName('main_desktop');
        //获取左侧菜单
        $this->add('/main/lefttree.html', array(
            'controller' => 'main',
            'action' => 'lefttree'
        ))->setName('main_lefttree');
        //重置密码
        $this->add('/main/setpassword.html', array(
            'controller' => 'main',
            'action' => 'setpassword'
        ))->setName('main_setpassword');
        /****************平台管理**********************/
        //平台管理
        $this->add('/platform/index.html', array(
            'controller' => 'platform',
            'action' => 'index'
        ))->setName('platform_index');
        //获取数据列表
        $this->add('/platform/ajaxlist.html', array(
            'controller' => 'platform',
            'action' => 'ajaxlist'
        ))->setName('platform_ajaxlist');
        //添加、编辑
        $this->add('/platform/edit.html', array(
            'controller' => 'platform',
            'action' => 'edit'
        ))->setName('platform_edit');
        //处理添加、编辑
        $this->add('/platform/doedit.html', array(
            'controller' => 'platform',
            'action' => 'doEdit'
        ))->setName('platform_doedit');
        //检查名称唯一性
        $this->add('/platform/checkName.html', array(
            'controller' => 'platform',
            'action' => 'checkName'
        ))->setName('platform_checkName');
        //删除记录
        $this->add('/platform/del.html', array(
            'controller' => 'platform',
            'action' => 'del'
        ))->setName('platform_del');
        /****************end 平台管理**********************/
        /****************系统管理**********************/
        //系统管理
        $this->add('/pfsystem/index.html', array(
            'controller' => 'pfsystem',
            'action' => 'index'
        ))->setName('pfsystem_index');
        //获取平台列表
        $this->add('/pfsystem/getPlatform.html', array(
            'controller' => 'pfsystem',
            'action' => 'getPlatform'
        ))->setName('pfsystem_getPlatform');
        
        //获取数据列表
        $this->add('/pfsystem/ajaxlist.html', array(
            'controller' => 'pfsystem',
            'action' => 'ajaxlist'
        ))->setName('pfsystem_ajaxlist');
        //添加、编辑
        $this->add('/pfsystem/edit.html', array(
            'controller' => 'pfsystem',
            'action' => 'edit'
        ))->setName('pfsystem_edit');
        //处理添加、编辑
        $this->add('/pfsystem/doedit.html', array(
            'controller' => 'pfsystem',
            'action' => 'doEdit'
        ))->setName('pfsystem_doedit');
        //检查名称唯一性
        $this->add('/pfsystem/checkName.html', array(
            'controller' => 'pfsystem',
            'action' => 'checkName'
        ))->setName('pfsystem_checkName');
        //删除记录
        $this->add('/pfsystem/del.html', array(
            'controller' => 'pfsystem',
            'action' => 'del'
        ))->setName('pfsystem_del');
        /****************end 系统管理**********************/
        
        /****************模块管理**********************/
        //模块管理
        $this->add('/module/index.html', array(
            'controller' => 'module',
            'action' => 'index'
        ))->setName('module_index');
        //获取系统列表
        $this->add('/module/getsystems.html', array(
            'controller' => 'module',
            'action' => 'getSystems'
        ))->setName('module_getSystems');
        //获取模块列表
        $this->add('/module/getmodule.html', array(
            'controller' => 'module',
            'action' => 'getModule'
        ))->setName('module_getModule');
        //添加、编辑
        $this->add('/module/edit.html', array(
            'controller' => 'module',
            'action' => 'edit'
        ))->setName('module_edit');
        //处理添加、编辑
        $this->add('/module/doedit.html', array(
            'controller' => 'module',
            'action' => 'doEdit'
        ))->setName('module_doEdit');
        //删除记录
        $this->add('/module/del.html', array(
            'controller' => 'module',
            'action' => 'del'
        ))->setName('module_del');
        //删除操作权限值
        $this->add('/module/deletePriVal.html', array(
            'controller' => 'module',
            'action' => 'deletePriVal'
        ))->setName('module_deletePriVal');
        //添加操作权限页面
        $this->add('/module/insertPriVal.html', array(
            'controller' => 'module',
            'action' => 'insertPriVal'
        ))->setName('module_insertPriVal');
        //添加操作权限
        $this->add('/module/doInsertPriVal.html', array(
            'controller' => 'module',
            'action' => 'doInsertPriVal'
        ))->setName('module_doInsertPriVal');
        /****************end 模块管理**********************/
        
        /****************权限值管理**********************/
        //权限值管理
        $this->add('/pvalue/index.html', array(
            'controller' => 'pvalue',
            'action' => 'index'
        ))->setName('pvalue_index');
        
        //获取模块列表
        $this->add('/pvalue/getpvalue.html', array(
            'controller' => 'pvalue',
            'action' => 'getPvalue'
        ))->setName('pvalue_getPvalue');
        //添加、编辑
        $this->add('/pvalue/edit.html', array(
            'controller' => 'pvalue',
            'action' => 'edit'
        ))->setName('pvalue_edit');
        //处理添加、编辑
        $this->add('/pvalue/doedit.html', array(
            'controller' => 'pvalue',
            'action' => 'doEdit'
        ))->setName('pvalue_doEdit');
        //检查标识唯一性
        $this->add('/pvalue/checkSign.html', array(
            'controller' => 'pvalue',
            'action' => 'checkSign'
        ))->setName('pvalue_checkSign');
        //删除记录
        $this->add('/pvalue/del.html', array(
            'controller' => 'pvalue',
            'action' => 'del'
        ))->setName('pvalue_del');
        /****************end 权限值管理**********************/
        
        /**************** 角色管理**********************/
        //角色管理
        $this->add('/role/index.html', array(
            'controller' => 'role',
            'action' => 'index'
        ))->setName('role_index');
        //获取数据列表
        $this->add('/role/ajaxlist.html', array(
            'controller' => 'role',
            'action' => 'ajaxlist'
        ))->setName('role_ajaxlist');
        //添加、编辑
        $this->add('/role/edit.html', array(
            'controller' => 'role',
            'action' => 'edit'
        ))->setName('role_edit');
        //处理添加、编辑
        $this->add('/role/doedit.html', array(
            'controller' => 'role',
            'action' => 'doEdit'
        ))->setName('role_doedit');
        //检查名称唯一性
        $this->add('/role/checkName.html', array(
            'controller' => 'role',
            'action' => 'checkName'
        ))->setName('role_checkName');
        //删除记录
        $this->add('/role/del.html', array(
            'controller' => 'role',
            'action' => 'del'
        ))->setName('role_del');
        //分配权限
        $this->add('/role/rolemodule.html', array(
            'controller' => 'role',
            'action' => 'rolemodule'
        ))->setName('role_rolemodule');
        //获取系统模块列表数据
        $this->add('/role/getSystemModulePvalue.html', array(
            'controller' => 'role',
            'action' => 'getSystemModulePvalue'
        ))->setName('role_getSystemModulePvalue');
        //设置角色操作权限
        $this->add('/role/setacl.html', array(
            'controller' => 'role',
            'action' => 'setacl'
        ))->setName('role_setacl');
        
        /****************end 角色管理**********************/
        /****************部门管理**********************/
        //部门管理
        $this->add('/department/index.html', array(
            'controller' => 'department',
            'action' => 'index'
        ))->setName('department_index');
        //获取数据列表
        $this->add('/department/ajaxlist.html', array(
            'controller' => 'department',
            'action' => 'ajaxlist'
        ))->setName('department_ajaxlist');
        //添加、编辑
        $this->add('/department/edit.html', array(
            'controller' => 'department',
            'action' => 'edit'
        ))->setName('department_edit');
        //处理添加、编辑
        $this->add('/department/doedit.html', array(
            'controller' => 'department',
            'action' => 'doEdit'
        ))->setName('department_doedit');
        //删除记录
        $this->add('/department/del.html', array(
            'controller' => 'department',
            'action' => 'del'
        ))->setName('department_del');
        /****************end 部门管理**********************/
        /****************用户管理**********************/
        //用户管理
        $this->add('/user/index.html', array(
            'controller' => 'user',
            'action' => 'index'
        ))->setName('user_index');
        //获取部门数结构
        $this->add('/user/getDepartmentTree.html', array(
            'controller' => 'user',
            'action' => 'getDepartmentTree'
        ))->setName('user_getDepartmentTree');
        //获取数据列表
        $this->add('/user/ajaxlist.html', array(
            'controller' => 'user',
            'action' => 'ajaxlist'
        ))->setName('user_ajaxlist');
        //添加、编辑
        $this->add('/user/edit.html', array(
            'controller' => 'user',
            'action' => 'edit'
        ))->setName('user_edit');
        //处理添加、编辑
        $this->add('/user/doedit.html', array(
            'controller' => 'user',
            'action' => 'doEdit'
        ))->setName('user_doedit');
        //设置用户状态
        $this->add('/user/setStatus.html', array(
            'controller' => 'user',
            'action' => 'setStatus'
        ))->setName('user_setStatus');
        //检查名称唯一性
        $this->add('/user/checkName.html', array(
            'controller' => 'user',
            'action' => 'checkName'
        ))->setName('user_checkName');
        //删除记录
        $this->add('/user/del.html', array(
            'controller' => 'user',
            'action' => 'del'
        ))->setName('user_del');
        //分配角色页面
        $this->add('/user/addRole.html', array(
            'controller' => 'user',
            'action' => 'addRole'
        ))->setName('user_addRole');
        //获取角色列表
        $this->add('/user/getRoleList.html', array(
            'controller' => 'user',
            'action' => 'getRoleList'
        ))->setName('user_getRoleList');
        //获取用户角色
        $this->add('/user/getUserRole.html', array(
            'controller' => 'user',
            'action' => 'getUserRole'
        ))->setName('user_getUserRole');
        //添加用户角色
        $this->add('/user/doAddRole.html', array(
            'controller' => 'user',
            'action' => 'doAddRole'
        ))->setName('user_doAddRole');
        
        /****************end 用户管理**********************/
    }
}
