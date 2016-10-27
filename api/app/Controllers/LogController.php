<?php
namespace Joy\Biapi\Controllers;
use Joy\Basic\Services\UserlogServices;
use Joy\Basic\Models\Log;
/**
 * 记录数据
 *
 * 2016-09-09
 *
 * @author fengyc
 */
class LogController extends Controller
{

    private $_userlogServices;
    /**
     * 初始化操作
     */
    public function initialize()
    {
        parent::initialize();
        $this->_userlogServices = new UserlogServices();
    }
    
    /**
     * 记录数据
     */
    public function indexAction(){  
        $Log = new Log();
        $Log->siteId   = $this->request->get('siteid','int');
        $Log->clientId = 1;
        $Log->pageId   = 1;
        $Log->blockId  = 1;
        $Log->typeId   = 2;
        $Log->objectId = 3; //对象id
        $Log->memberId = 4;
        $Log->orderId  = 5;
        $Log->opeName  = $this->request->get('name','string');
        $Log->rUrl     = $this->request->get('r','string');
        $Log->url      = $this->request->get('page','string');
        $Log->sourceId = $this->getSourceId($url);
        $Log->clientInfo= $this->request->get('agent','string');
        $Log->cookieId  = $this->request->get('logcookieid','string');
        $Log->sessionId = $this->request->get('logsessionid','string');
        $Log->clientIp  = $this->getClientIp();
        $re = $this->_userlogServices->saveDate($Log);
        return $re;
    }
    public function getSourceId($url){
        $sourceId = 0;
        if(!empty($url)){
            $url=parse_url($url);
            if(isset($url['query'])){
                $queryArray = explode("&", $url['query']);
                if(empty($queryArray)){
                    $queryArray[] = $url['query'];
                }
                foreach($queryArray as $qv){
                    $parameter = explode("=", $qv);
                    if($parameter[0]==='sourceId'){
                        $sourceId = $parameter[1];
                    }
                }
            }
        }
        return $sourceId;
    }
    /**
     * 获取IP地址
     */
    public function getClientIp()
    {
        $onlineip = '';
        if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), '0.0.0.0')) {
            $onlineip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), '0.0.0.0')) {
            $onlineip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), '0.0.0.0')) {
            $onlineip = getenv('REMOTE_ADDR');
        } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], '0.0.0.0')) {
            $onlineip = $_SERVER['REMOTE_ADDR'];
        }
        preg_match('/\d+\.\d+\.\d+\.\d+/is', $onlineip, $match);
        return !empty($match[0]) ? $match[0] : '0.0.0.0';
    }
}