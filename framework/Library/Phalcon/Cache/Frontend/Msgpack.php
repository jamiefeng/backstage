<?php
namespace Phalcon\Cache\Frontend;

use Phalcon\Cache\Frontend\Data;

/**
 * Class Msgpack
 * 
 * @package Phalcon\Cache\Frontend
 *         
 * @author Yoshihiro Misawa
 */
class Msgpack extends Data
{

    /**
     *
     * @ERROR!!!
     *
     * @param mixed $data            
     * @return string
     */
    public function beforeStore($data)
    {
        return msgpack_pack($data);
    }

    /**
     *
     * @ERROR!!!
     *
     * @param string $data            
     * @return mixed
     */
    public function afterRetrieve($data)
    {
        return msgpack_unpack($data);
    }
}
