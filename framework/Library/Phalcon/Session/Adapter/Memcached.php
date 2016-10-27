<?php
/**
 * Phalcon Framework
 * This source file is subject to the New BSD License that is bundled
 * with this package in the file docs/LICENSE.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@phalconphp.com so we can send you a copy immediately.
 *
 * @category    Phalcon
 * @package     Phalcon_Session_Adapter_Memcache
 * @copyright   Copyright (c) 2013 meets-ecommerce GmbH (http://meets-ecommerce.de)
 * @author      Daniel Matuschewsky <dm@meets-ecommerce.de>
 */
namespace Phalcon\Session\Adapter;

use Phalcon;

/**
 * Memcache session adapter for Phalcon framework
 *
 * @category Phalcon
 * @package Phalcon_Session_Adapter_Memcache
 */
class Memcached extends Phalcon\Session\Adapter implements Phalcon\Session\AdapterInterface
{

    /*
     * session id
     */
    private $_sessionId;
    /**
     * Default option for memcache port
     *
     * @var integer
     */
    const DEFAULT_OPTION_PORT = 11211;

    /**
     * Default option for session lifetime
     *
     * @var integer
     */
    const DEFAULT_OPTION_LIFETIME = 8600;

    /**
     * Default option for persistent session
     *
     * @var boolean
     */
    const DEFAULT_OPTION_PERSISTENT = false;

    /**
     * Default option for prefix of sessionId's
     *
     * @var string
     */
    const DEFAULT_OPTION_PREFIX = '';

    /**
     * Contains the memcache instance
     *
     * @var \Memcache
     */
    protected $memcacheInstance = null;

    /**
     * Class constructor.
     *
     * @param null|array $options            
     * @throws Phalcon\Session\Exception
     */
    public function __construct($options = null)
    {
        if (is_array($options)) {
            if (! isset($options["host"])) {
                throw new Phalcon\Session\Exception("No session host given in options");
            }
            if (! isset($options["lifetime"])) {
                $options["lifetime"] = self::DEFAULT_OPTION_LIFETIME;
            }
            
            if (! isset($options["prefix"])) {
                $options["prefix"] = self::DEFAULT_OPTION_PREFIX;
            }
        } else {
            throw new Phalcon\Session\Exception("No configuration given");
        }
        @session_start();
        session_set_save_handler(array(
            $this,
            'open'
        ), array(
            $this,
            'close'
        ), array(
            $this,
            'read'
        ), array(
            $this,
            'write'
        ), array(
            $this,
            'destroy'
        ), array(
            $this,
            'gc'
        ));
        
        parent::__construct($options);
    }

    /**
     *
     * @ERROR!!!
     *
     * @return boolean
     */
    public function open()
    {
        $this->_sessionId = session_id();
        return true;
    }
    public function start(){
        $this->open();
    }
    
    /**
     *
     * @ERROR!!!
     *
     * @return boolean
     */
    public function close()
    {
        return true;
    }

    /**
     *
     * @ERROR!!!
     *
     * @param string $key            
     * @return mixed
     */
    public function read($key)
    {
        $data = $this->getMemcacheInstance()->get($this->getSessionId());
        if(!empty($data[$key])){
            //离过期一半，重新更新数据
            if($data[$key]['writetime'] < (time()-$this->getOption('lifetime')+($this->getOption('lifetime')/2))){
                $this->write($key, $data[$key]);
            }
        }
        return $data[$key]['data'];
    }
    public function get($key, $defaultValue = NULL){
        $value = $this->read($key);
        return $value?$value:$defaultValue;
    }
    /**
     *
     *
     * @param string $key            
     * @param string $data            
     */
    public function write($key, $data)
    {
        $addData = $this->getMemcacheInstance()->get($this->getSessionId());
        //保存写时间
        $addData[$key]['writetime'] = time();
        $addData[$key]['data'] = $data;
        $this->getMemcacheInstance()->set($this->getSessionId(), $addData,MEMCACHE_COMPRESSED, $this->getOption('lifetime'));
    }
    public function set($key, $data){
        $this->write($key, $data);
    }
    /**
     * Destroys session.
     *
     * @param string $key
     *            optional, session id
     *            
     * @return boolean
     */
    public function destroy($key = null)
    {
        if (! $key) {
            $session_id = $this->getSessionId();
            return $this->getMemcacheInstance()->delete($session_id);
        } else {
            $data = $this->getMemcacheInstance()->get($this->getSessionId());
            unset($data[$key]);
            return $this->getMemcacheInstance()->set($this->getSessionId(), $data,MEMCACHE_COMPRESSED, $this->getOption('lifetime'));
        }
    }

    /**
     *
     * @ERROR!!!
     *
     */
    public function gc()
    {}

    /**
     *
     * @ERROR!!!
     *
     * @param string $key            
     * @return mixed
     */
    public function getOption($key)
    {
        $options = $this->getOptions();
        if (isset($options[$key])) {
            return $options[$key];
        }
        
        return null;
    }

    /**
     * Returns the memcache instance.
     *
     * @return \Phalcon\Cache\Backend\Memcache
     */
    protected function getMemcacheInstance()
    {
        if ($this->memcacheInstance === null) {
            $this->memcacheInstance = new \Memcache;
            //foreach ($this->getOption('servers') as $server){
                $this->memcacheInstance->addServer($this->getOption('host'),$this->getOption('port'),$this->getOption('persistent')?$this->getOption('persistent'):self::DEFAULT_OPTION_PERSISTENT);
            //}
        }
        
        return $this->memcacheInstance;
    }

    /**
     * Sets memcache instance.
     *
     * @param Phalcon\Cache\Backend\Memcache $memcacheInstance
     *            memcache instance
     *            
     * @return $this provides fluent interface
     */
    public function setMemcacheInstance(\Phalcon\Cache\Backend\Memcache $memcacheInstance)
    {
        $this->memcacheInstance = $memcacheInstance;
        return $this;
    }

    /**
     * Returns the sessionId with prefix
     *
     * @param string $sessionId            
     * @return string
     */
    protected function getSessionId()
    {
        return ((strlen($this->getOption('prefix')) > 0) ? $this->getOption('prefix') . '_' . $this->_sessionId : $this->_sessionId);
    }
}
