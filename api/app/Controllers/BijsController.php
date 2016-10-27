<?php
namespace Joy\Biapi\Controllers;
use Joy\Bibasic\Services\UserlogServices;
/**
 * 埋点js
 *
 * 2016-09-09
 *
 * @author fengyc
 */
class BijsController extends Controller
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
     * 引用js
     */
    public function indexAction(){  
        $httpReferer =  parse_url($_SERVER['HTTP_REFERER']);
        //来路域名错误
        if(strpos($httpReferer['host'],".csc86.com")==-1){
            return 'role error';
        }
        //站点不存在
        $siteId = $this->request->get('siteId', 'int');
        if(!in_array($siteId,[1,2,3])){
            return 'siteid error';
        }
        //记录数据请求地址
        $logaction = $this->globalSetting['website_url'].$this->url->get(['for'=>'log_index']);
        
        $sessionid = $this->getSessionId();
        $logjs = '';
        $logjs = $this->getJquery();
        $logjs .= '
var logcookieid = "'.$this->getCookieId().'";
var f = document,
i = window,
m = navigator,
n = f.location,
p = i.screen,
h = encodeURIComponent,
l = decodeURIComponent,
k = "https:" == n.protocol ? "https:": "http:",
g = function(a, c , obj,logid) {
    try {
        var b = [];
        b.push("siteid='.$siteId.'");
		b.push("status=" + h(a.status));
        b.push("name=" + h(a.name));
        b.push("msg=" + h(a.message));
        b.push("r=" + h(f.referrer));
        b.push("page=" + h(n.href));
        b.push("agent=" + h(m.userAgent));
        b.push("ex=" + h(c));
        b.push("rnd=" + Math.floor(2147483648 * Math.random()));
        b.push("logcookieid=" + logcookieid);
        b.push("logsessionid=" + logGetCookie("logsessionid"));
        if(obj!==null){
            b.push("logid=" + h(obj.attr("data-logid")));
        }else if(logid!==null && logid!=undefined){
           b.push("logid=" + logid);
        }
        var str = "'.$logaction.'?" + b.join("&").replace("%3C", "(|");
        if(obj!==null && obj.attr("target")!="_blank"){
        	 (new Image).src = str;
			 window.onunload = function(){(new Image).src = str;return true;}
        }else{
		    (new Image).src = str;
		}
    } catch(d) {}
};
//end q.prototype
$(function(){
	try {
		logCheckCookie();
        logSetCookie("logsessionid","'.$sessionid.'");
		var r=new Object();
		r.status = "200";
		r.name   = "view";
		r.message= "ok";
        g(r, "ok" , null);
    } catch(r) {
		//r.status = "404";
        //g(r, "view failed" , null)
    }
        
});
        
$(".logclick").click(function(){
	try {
		var r=new Object();
		r.status = "200";
		r.name   = "aclick";
		r.message= "ok";
        g(r, "ok" , $(this))
    } catch(r) {
		//r.status = "404";
        //g(r, "aclick failed" , $(this))
    }
});
$(".logmouseover").mouseover(function(){
	try {
		var r=new Object();
		r.status = "200";
		r.name   = "mouseover";
		r.message= "ok";
        g(r, "ok" , $(this))
    } catch(r) {
		//r.status = "404";
        //g(r, "mouseover failed" , $(this))
    }
});
function logaction(logid){
       try {
			var r=new Object();
			r.status = "200";
			r.name   = "click";
			r.message= "ok";
            g(r, "ok" , null,logid)
        } catch(r) {
			//r.status = "404";
            //g(r, "clickaction failed" , null)
        }
}
//检查cookie
function logCheckCookie()
{
	logcookieid_tmp = logGetCookie("logcookiename");
    if (logcookieid_tmp==null || logcookieid_tmp=="")
    {
       logSetCookie("logcookiename",logcookieid,365)
    }else{
       logcookieid = logcookieid_tmp;
    }
}
//写cookie
function logSetCookie(c_name,value,expiredays)
{
    var exdate=new Date();
    exdate.setDate(exdate.getDate()+expiredays);
    document.cookie=c_name+ "=" +escape(value)+
    ((expiredays==null) ? "" : ";expires="+exdate.toGMTString())+
    ";domain=.csc86.com";
}
//读cookie
function logGetCookie(c_name)
{
    if (document.cookie.length>0)
      {
      c_start=document.cookie.indexOf(c_name + "=")
      if (c_start!=-1)
        {
        c_start=c_start + c_name.length+1
        c_end=document.cookie.indexOf(";",c_start)
        if (c_end==-1) c_end=document.cookie.length
        return unescape(document.cookie.substring(c_start,c_end))
        }
      }
    return "";
}';
        return $logjs;
    }
    /**
     * 获取session id
     */
    private function getSessionId(){
        session_start();
        return session_id();
    }
    /**
     * 获取CookieId
     */
    private function getCookieId(){
        return md5(uniqid().mt_rand(1,1000000));
    }
    /**
     * 获取jquery
     */
    private function getJquery(){
        return @file_get_contents($this->globalSetting['website_url']."static/js/jquery-1.4.1.min.js");
    }
}