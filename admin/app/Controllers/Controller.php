<?php
namespace Joy\Admin\Controllers;

! defined('ROOT_PATH') && exit('Do not allow direct access!');

use Phalcon\Http\Response;

/**
 * *Controller.php文件
 * ==============================================
 * 版权所有 2010-2016 
 * ----------------------------------------------
 * 用于REST API的基类
 * ==============================================
 * @date: 2016-6-23
 *
 * @author : fengyanchao
 * @version :
 *         
 */
use Joy\Basic\Services\ModuleServices;
class Controller extends \Joy\Web\Controller {
    
    
    /**
     * 通用公共设置
     *
     * @var array
     */
    public $globalSetting;
    /**
     * session id
     */
    public $sessionId;
	/**
	 * 分页
	 */
	public $pageSize = 20;
	/**
	 * 不需要验证登录的控制器和方法
	 * @var unknown
	 */
    protected $access_list = [
        //不需要验证的方法
        'unlogin_action' => [
            'login' => ['index'],
        ]
    ];
    /**
     * 不需要验证模块权限控制器
     * @var unknown
     */
    protected $access_module_list = [
        'login',
        'main',
    ];
	/*
	 * 登录用户信息
	 */
	protected $userInfo;
	/**
	 * 初始化服务
	 * @access public
	 * @return void
	 */
	public function initialize() {
	    $this->view->disableLevel(array(
	        \Phalcon\Mvc\View::LEVEL_LAYOUT => true,
	        \Phalcon\Mvc\View::LEVEL_MAIN_LAYOUT => true
	    ));
	    
	}
	
	/**
	 * 校验用户权限，根据控制器名和动作名来校验用户的权限
	 * $accessToken系统根据控制器名和动作名来自动生成
	 * ```
	 * $accessToken = substr(md5($module .
	 *
	 * $controller . $action), 0, 6);
	 * ```
	 *
	 * @see \Joy\Web\Controller::beforeExecuteRoute()
	 * @see \Joy\Web\Controller::checkAccess()
	 */
	public function checkAccess($accessToken)
	{
	    // 通用配置
	    $this->view->globalSetting = $this->globalSetting = \Joy::$config ['globalSetting'];
	    $action = $this->dispatcher->getActionName();
        $controller = $this->dispatcher->getControllerName();
        $access = true; //是否要验证
        if (isset($this->access_list['unlogin_action'][$controller]) && in_array($action, $this->access_list['unlogin_action'][$controller])) {
            //控制器中少部分方法不用登陆
            $access = false;
        }
        $this->view->userInfo = $this->userInfo = $this->session->get('userInfo');
        
        //echo '<pre>';print_r($this->userInfo);exit;
        if ((empty($this->userInfo['userId']) || empty($this->userInfo)) && $access) {
            if ($this->request->isAjax()) {
                //弹窗口
                $dialog = $this->request->get ( 'dialog', 'int');
                if($dialog>0){
                    //$this->view->loginOut = true;
                }else{
                    //header('Content-type: application/json');
                    //die(json_encode(['code' => -1,'rows'=>[], 'msg' => '页面超时-请重新登录', 'loginUrl' => $this->url->get(['for' => 'login_login'])]));
                }
                header('Location: '.$this->globalSetting['pfUrl'].'login/index.html');
            } else {
                $this->parentMsgJump('页面超时-请重新登录', 'login_login');
            }
        }
        
        if(!in_array($controller, $this->access_module_list) && $access){
            //检查模块权限
            $systemId  = $this->request->get ( 'systemId', 'string');
            $moduleId  = $this->request->get ( 'moduleId', 'string');
            $moduleKey = $this->request->get ( 'moduleKey', 'string');
            $moduleServices = new ModuleServices();
            if($moduleId && $moduleKey){
                $this->session->set('moduleId',$moduleId);
                $checkKey = $moduleServices->getModuleKey($systemId,$moduleId, $this->userInfo['aclState'][$moduleId]);
                if($moduleKey != $checkKey){
                    //die('key错误');
                    header('Location: '.$this->globalSetting['pfUrl'].'login/index.html');
                }
            }else{
                $moduleId = $this->session->get('moduleId');
            }
            $re = $moduleServices->checkModuleRight($systemId,$moduleId, $this->userInfo['aclState']);
            
            if($re['code']!=0){
                //die($re['msg']);
                header('Location: '.$this->globalSetting['pfUrl'].'login/index.html');
            }
        }
        $this->view->sessionId = $this->sessionId = @session_id();
        return true;;
	}
	
	
	
	/**
	 * 通过 Js 进行页面跳转
	 *
	 * @param unknown $msg
	 */
	public function parentMsgJump($msg, $url)
	{
		$url = strpos($url, '://') ? $url : $this->url->get([
				'for' => $url
		]);
		echo "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"><script>function sptips(){" . ($msg ? "alert('{$msg}');" : '') . "top.document.location.href=\"{$url}\";}</script></head><body onload=\"sptips()\"></body></html>";
		exit();
	}
	
	/**
	 * 通过 Js 进行页面跳转
	 *
	 * @param unknown $msg
	 */
	public function msgJump($msg, $url)
	{
	    $url = strpos($url, '://') ? $url : $this->url->get([
	        'for' => $url
	    ]);
	    echo "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"><script>function sptips(){" . ($msg ? "alert('{$msg}');" : '') . "location.href=\"{$url}\";}</script></head><body onload=\"sptips()\"></body></html>";
	    exit();
	}
	
	/**
	 * 通过 Js 进行内容弹窗
	 *
	 * @param unknown $msg
	 */
	public function msgBack($msg)
	{
	    echo "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"><script>function sptips(){alert(\"{$msg}\");}</script></head><body onload=\"sptips()\"></body></html>";
	    exit();
	}
	/**
	 * 返回json结果
	 */
	public function jsonResult($code,$msg='',$data=[]){
	    $re = ["code" => $code,"msg"  => $msg];
	    if($data){
	        $re['data'] = $data;
	    }
	    return json_encode($re);
	}
	/**
	 * 数组转json结果
	 */
	public function toJson($data){
	    return json_encode($data);
	}
}