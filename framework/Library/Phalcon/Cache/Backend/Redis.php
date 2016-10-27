<?php
/*
 * +------------------------------------------------------------------------+
 * | Phalcon Framework |
 * +------------------------------------------------------------------------+
 * | Copyright (c) 2011-2012 Phalcon Team (http://www.phalconphp.com) |
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
 * | Nikita Vershinin <endeveit@gmail.com> |
 * +------------------------------------------------------------------------+
 */
namespace Phalcon\Cache\Backend;

use Phalcon\Cache\Exception;

/**
 * Phalcon\Cache\Backend\Redis
 * This backend uses redis as cache backend
 */
class Redis extends \Phalcon\Cache\Backend implements \Phalcon\Cache\BackendInterface
{

    /**
     *
     * @var \Redis
     */
    private $redis;

    /**
     * Class constructor.
     * Redis链接参数
     * ```
     * [
     * 'host' =>'127.0.0.1', //服务器IP
     * 'port' => 6379, //Redis端口
     * 'timeout' => 1, //连接超时时间
     * 'retryInterval' => 2, //重试间隔时间
     * 'persistent' => false, //是否长连接
     * 'reserved' => null, //如果非长连接，请设置为null，长连接时他是连接的身份标识
     * 'password' => null, //Redis授权密码
     * 'dbindex' => 1, //数据库ID
     * ]
     * ```
     *
     * @param \Phalcon\Cache\FrontendInterface $frontend
     * @param array $options
     * @throws \Phalcon\Cache\Exception
     */
    public function __construct($frontend, $options = null)
    {
        if (! isset($options['host'])) {
            throw new Exception("Parameter 'host' is required");
        }
        parent::__construct($frontend, $options);
    }

    /**
     *
     * @return \Redis
     */
    public function _connect()
    {
        $redis = new \Redis();
        $options = $this->getOptions();
        $host = isset($options['host']) ? $options['host'] : '127.0.0.1';
        $port = isset($options['port']) ? $options['port'] : 6379;
        $timeout = isset($options['timeout']) ? $options['timeout'] : 1;
        $retryInterval = isset($options['retryInterval']) ? $options['retryInterval'] : 2;
        $persistent = isset($options['persistent']) ? $options['persistent'] : false;
        $reserved = isset($options['reserved']) ? $options['reserved'] : null;
        $password = isset($options['password']) ? $options['password'] : null;
        $dbindex = isset($options['dbindex']) ? $options['dbindex'] : 1;
        if ($persistent) {
            $connect = $redis->pconnect($host);
        } else {
            $reserved = null;
            $connect = $redis->connect($host, $port, $timeout, $reserved, $retryInterval);
        }
        if ($connect == false)
            throw new Exception("Cannot connect to Redis server");
        if ($password !== null)
            $redis->auth($password);
        $redis->select($dbindex);
        $this->redis = $redis;
        return $redis;
    }

    /**
     * Returns prefixed identifier.
     *
     * @param string $id
     * @return string
     */
    protected function getPrefixedIdentifier($id)
    {
        $options = $this->getOptions();

        if (! empty($options['prefix'])) {
            return $options['prefix'] . $id;
        }

        return $id;
    }

    /**
     *
     * @ERROR!!!
     *
     * @param string $keyName
     * @param integer $lifetime
     * @return mixed|null
     */
    public function get($keyName, $lifetime = null)
    {
        $redis = $this->redis;
        if (! $redis instanceof \Redis) {
            $redis = $this->_connect();
        }
        $value = $redis->get($this->getPrefixedIdentifier($keyName));
        if ($value === false) {
            return null;
        }
        $frontend = $this->getFrontend();
        $this->setLastKey($keyName);
        return $frontend->afterRetrieve($value);
    }

    /**
     *
     * @ERROR!!!
     *
     * @param string $keyName
     * @param string $content
     * @param integer $lifetime
     * @param boolean $stopBuffer
     * @throws \Phalcon\Cache\Exception
     */
    public function save($keyName = null, $content = null, $lifetime = null, $stopBuffer = true)
    {
        if ($keyName === null) {
            $lastKey = $this->_lastKey;
        } else {
            $lastKey = $keyName;
        }
        if (! $lastKey) {
            throw new Exception('The cache must be started first');
        }
        $frontend = $this->getFrontend();
        if ($content === null) {
            $content = $frontend->getContent();
        }
        // Get the lifetime from the frontend
        if ($lifetime === null) {
            $lifetime = $frontend->getLifetime();
        }
        $redis = $this->redis;
        if (! $redis instanceof \Redis) {
            $redis = $this->_connect();
        }
        $redis->setex($this->getPrefixedIdentifier($lastKey), $lifetime, $frontend->beforeStore($content));
        $isBuffering = $frontend->isBuffering();
        // Stop the buffer, this only applies for Phalcon\Cache\Frontend\Output
        if ($stopBuffer) {
            $frontend->stop();
        }
        // Print the buffer, this only applies for Phalcon\Cache\Frontend\Output
        if ($isBuffering) {
            echo $content;
        }
        $this->_started = false;
    }

    /**
     *
     * @ERROR!!!
     *
     * @param string $keyName
     * @return boolean
     */
    public function delete($keyName)
    {
        $redis = $this->redis;
        if (! $redis instanceof \Redis) {
            $redis = $this->_connect();
        }
        return $redis->delete($this->getPrefixedIdentifier($keyName)) > 0;
    }

    /**
     *
     * @ERROR!!!
     *
     * @param string $prefix
     * @return array
     */
    public function queryKeys($prefix = null)
    {
        $options = $this->getOptions();
        $redis = $this->redis;
        if (! $redis instanceof \Redis) {
            $redis = $this->_connect();
        }
        if ($prefix === null) {
            $result = $redis->getKeys($this->getPrefixedIdentifier('*'));
        } else {
            $result = $redis->getKeys($this->getPrefixedIdentifier($prefix) . '*');
        }
        if (! empty($options['prefix']) && ! empty($result)) {
            $optionsPrefix = $options['prefix'];
            array_walk($result, function (&$key) use($optionsPrefix)
            {
                $key = str_replace($optionsPrefix, '', $key);
            });
        }
        return $result;
    }

    /**
     *
     * @ERROR!!!
     *
     * @param string $keyName
     * @param string $lifetime
     * @return boolean
     */
    public function exists($keyName = null, $lifetime = null)
    {
        $redis = $this->redis;
        if (! $redis instanceof \Redis) {
            $redis = $this->_connect();
        }
        return $redis->exists($this->getPrefixedIdentifier($keyName));
    }

    /**
     *
     * @ERROR!!!
     *
     * @return boolean
     */
    public function flush()
    {
        $redis = $this->redis;
        if (! $redis instanceof \Redis) {
            $redis = $this->_connect();
        }
        return $redis->flushAll();
    }
}