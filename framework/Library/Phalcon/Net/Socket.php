<?php
namespace Phalcon\Net;

/**
 * socket连接 
 */
class Socket{
    private $connect;           //socket连接对象
    private $errorNo;           //连接错误编号
    private $errorMsg;          //连接错误信息
    private $mod     = 0;//0 非阻塞， 1 阻塞
    private $timeout = 1;
    
    /**
     * Socket 构造函数
     * @param string $host  IP
     * @param int $port  端口
     * @param int  $timeout 超时
     * @throws \Exception
     */
    function __construct($host, $port, $timeout=1){
        try {
            $this->timeout = $timeout < 1 ? 1  : $timeout;
            $this->connect  = fsockopen($host, $port, $this->errorNo, $this->errorMsg,  $this->timeout);
            if (!$this->mod)stream_set_blocking($this->connect, 0);
            stream_set_timeout($this->connect, $timeout);
            
        }catch (\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }
    
    /**
     * 发送Socket请求
     * @param string $data 发送的数据 
     */
    public function send($data){
        try {
            if ($this->connect) {
                $res  = fwrite($this->connect, $data);
               	$status = stream_get_meta_data($this->connect);
                $result = '';       
                $start = microtime(true);
                while($buffer = @fread($socket, 1024)){
                	$result .= $buffer;
                }
                fclose($this->connect);

                return $result;
            } else {
                throw new \Exception(sprintf("Error(%s):%s", $this->errorNo, $this->errorMsg));
            }
        }catch (\Exception $e){
            throw new \Exception("写入失败. ". $e->getMessage());
        }
    }
}