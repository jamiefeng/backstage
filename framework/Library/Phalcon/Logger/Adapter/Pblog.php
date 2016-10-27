<?php
/*
 * +------------------------------------------------------------------------+
 * | Phalcon Framework |
 * +------------------------------------------------------------------------+
 * | Copyright (c) 2011-2014 Phalcon Team (http://www.phalconphp.com) |
 * +------------------------------------------------------------------------+
 * | This source file is subject to the New BSD License that is bundled |
 * | with this package in the file docs/LICENSE.txt. |
 * | |
 * | If you did not receive a copy of the license and are unable to |
 * | obtain it through the world-wide-web, please send an email |
 * | to license@phalconphp.com so we can send you a copy immediately. |
 * +------------------------------------------------------------------------+
 * | Authors: Andres Gutierrez <andres@phalconphp.com> |
 * | Eduar Carvajal <eduar@phalconphp.com> |
 * +------------------------------------------------------------------------+
 */
namespace Phalcon\Logger\Adapter;

use Phalcon\Logger\Exception;
use Phalcon\Logger\Adapter;
use Phalcon\Logger\AdapterInterface;
use Phalcon\Net\Socket;

/**
 * Phalcon\Logger\Adapter\Pblog
 *
 * Sends logs to pblog
 *
 * <code>
 * $logger = new \Phalcon\Logger\Adapter\Pblog("php://stderr");
 * $logger->log("This is a message");
 * $logger->log("This is an error", \Phalcon\Logger::ERROR);
 * $logger->error("This is another error");
 * </code>
 */
class Pblog extends Adapter implements AdapterInterface
{

    /**
     * Protobuffer请求格式数据类
     *
     * @var resource
     */
    protected $proto;

    /**
     * Protobuffer请求返回结果数据类
     *
     * @var resource
     */
    protected $protoReturn;

    /**
     * Socknet 通讯协议实例
     *
     * @var resource
     */
    protected $socket;

    /**
     * 日志类型的字符串描述
     * 
     * @var array
     */
    private $logType = [
        \Phalcon\Logger::EMERGENCE => 'emergence',
        \Phalcon\Logger::CRITICAL => 'critical',
        \Phalcon\Logger::ALERT => 'alter',
        \Phalcon\Logger::ERROR => 'error',
        \Phalcon\Logger::WARNING => 'warning',
        \Phalcon\Logger::INFO => 'info',
        \Phalcon\Logger::NOTICE => 'notice',
        \Phalcon\Logger::DEBUG => 'debug',
        \Phalcon\Logger::CUSTOM => 'custom',
        \Phalcon\Logger::SPECIAL => 'special'
    ];

    /**
     *
     * @var array
     */
    protected $defaultData;

    /**
     * Phalcon\Logger\Adapter\Pblog constructor
     *
     * @param
     *            array option
     */
    public function __construct($config)
    {
        if (empty($config['host']) || empty($config['port']) || empty($config['proto'])) {
            throw new Exception("Config param is error");
        }
        try {
            $this->socket = new Socket($config['host'], $config['port'], $config['timeout']);
        } catch (Exception $e) {}
        $this->setFormatter((array) $config['proto']);
        
        $this->defaultData = (array) $config['defaultData'];
    }

    public function getFormatter($formatter)
    {}

    /**
     * 设置日志的格式
     * (non-PHPdoc)
     * 
     * @see \Phalcon\Logger\Adapter::setFormatter()
     */
    public function setFormatter($proto)
    {
        if (empty($proto) || is_object($proto))
            return false;
        if (! class_exists($proto['proto']) || ! class_exists($proto['protoReturn'])) {
            throw new Exception("Proto class not exits");
        }
        $this->proto = new $proto['proto']();
        $this->protoReturn = new $proto['protoReturn']();
        $this->defaultData = false;
    }

    /**
     * Writes the log to the stream itself
     *
     * @param
     *            string message
     * @param
     *            int type
     * @param
     *            int time
     * @param array $context            
     */
    public function logInternal($message, $type, $time, $context)
    {
        $data = json_decode($message, true);
        // 是否为Json格式数据
        $error = json_last_error();
        if ($error > JSON_ERROR_NONE) {
            $data = [
                'message' => $message
            ];
        }
        // 使用配置中的默认数据
        if (! empty($this->defaultData)) {
            $data = array_merge($this->defaultData, $data);
        }
        if (array_key_exists('log_type', $data) && empty($data['log_type'])) {
            $data['log_type'] = $this->logType[$type];
        }
        foreach ($data as $key => $value) {
            $method = 'set_' . $key;
            if (method_exists($this->proto, $method))
                $this->proto->$method($value);
        }
        $data = $this->proto->SerializeToString();
        
        $lenhex = dechex(strlen($data));
        $data = hex2bin(str_repeat('0', 8 - strlen($lenhex)) . $lenhex . bin2hex($data));
        try {
            $result = $this->socket->send($data);
            $this->protoReturn->ParseFromString($result);
            if ($this->protoReturn->errorCode() === 0) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Closes the logger
     *
     * @return boolean
     */
    public function close()
    {}
}