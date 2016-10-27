<?php
namespace Phalcon\Net;
/*
 * HTTP请求工具类
 */
class Http
{

    /**
     * 设置HTTP请求超时时限
     * 
     * @var int
     */
    public $timeOut = 10;

    /**
     * 请求所使用的HTTP头信息
     * 
     * @var string
     */
    public $header = "Connection: keep-alive\r\nContent-type: application/x-www-form-urlencoded\r\nUser-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:12.0) Gecko/20120427 Firefox/12.0";

    /**
     * 服务器返回的头信息
     * 
     * @var array
     */
    public $httpResponseHeader;

    /**
     * 处理一个GET请求
     * @param string $url
     * @return Ambigous <\Joy\Api\mixed, mixed>
     */
    public function get($url)
    {
        return $this->request($url, 'GET');
    }

    /**
     * 处理一个POST请求
     * @param string $url
     * @param array $data
     * @return Ambigous <\Joy\Api\mixed, mixed>
     */
    public function post($url, $data)
    {
        return $this->request($url, 'POST',$data);
    }

    /**
     * 处理一个PUT请求
     * @param string $url
     * @param array $data
     * @return Ambigous <\Joy\Api\mixed, mixed>
     */
    public function put($url, $data)
    {
        return $this->request($url, 'PUT',$data);
    }

    /**
     * 处理一个PATCH请求
     * @param string $url
     * @param array $data
     * @return Ambigous <\Joy\Api\mixed, mixed>
     */
    public function patch($url, $data)
    {
        return $this->request($url, 'PATCH',$data);
    }

    /**
     * 处理一个DELETE请求
     * @param string $url
     * @param array $data
     * @return Ambigous <\Joy\Api\mixed, mixed>
     */
    public function delete($url)
    {
        return $this->request($url, 'DELETE');
    }

    /**
     * 处理请求
     * @param string $url
     * @param string $method
     * @param string $data
     * @throws \Exception
     * @return mixed
     */
/**
     * 以POST/get方式请求地址
     *
     * @param $url string
     *            请求地址
     * @param $data $header
     *            的content-type格式，
     *            目前在api平台和渠道内只有用到2种格式,xml和json,text
     */
    private  function request($url,   $method, $data = array())
    {
        $arr_header = array(
            'Accept-Language: zh-cn',
            'User-Agent: UserCenter (compatible; MSIE 6.0; Windows NT 5.1; SV1; InfoPath.1)',
            'Cache-Control: no-cache',
            'Content-type: application/x-www-form-urlencoded; charset=utf-8'
        );
        $ch = curl_init();
        if ($method != 'POST' && is_array($data) && count($data) > 0) {
            $url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query($data);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeOut);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
        
        if ($method == 'GET') {
            curl_setopt($ch, CURLOPT_POST, 0);
        } else  {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        }
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            return false;
        }
        $this->httpResponseHeader['status'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->httpResponseHeader['Content-Type'] = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);
        return empty($response) ? $response : json_decode($response,true);
    }   

    /**
     * 以Curl 形式实现 POST/get方式请求地址
     *
     * @param $url string
     *            请求地址
     * @param $data $header
     *            的content-type格式，
     *            目前在api平台和渠道内只有用到2种格式,xml和json,text
     */
    public  function callCurl($url, $data, $type = 'json', $encode = 'utf-8', $method = 'post', $time_out = 10)
    {        
        $datatype = $type == 'xml' ? "text/xml" : "text/plain";
        $arr_header = array(
            'Accept-Language: zh-cn',
            'User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; InfoPath.1)',
            'Cache-Control: no-cache',
            'Content-type: ' . $datatype . '; charset=' . $encode
        );
        $ch = curl_init();
        if ($method == 'get' && is_array($data) && count($data) > 0) {
            $url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query($data);
        }
    
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $time_out);
        if ($method == 'get') {
            curl_setopt($ch, CURLOPT_POST, 0);
        } else {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        
        if(strpos($url,'https://') !== false){
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            return false;
        }
        curl_close($ch);
        return $response;
    }    
}