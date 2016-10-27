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
 * | Authors: Tuğrul Topuz <tugrultopuz@gmail.com> |
 * +------------------------------------------------------------------------+
 */
namespace Phalcon\Db\Adapter\Mongo;

class Collection extends \MongoCollection
{

    public $db;

    public function __construct($db, $name)
    {
        $this->db = $db;
        parent::__construct($db, $name);
    }

    public function __get($name)
    {
        return $this->db->selectMongoCollection($name);
    }

    public function doFind($query = array(), $fields = array())
    {
        return $this->findAsObject('Phalcon\Db\Adapter\Mongo\Document', $query, $fields);
    }

    public function findAsObject($className, $query = array(), $fields = array())
    {
        return new Cursor($this, $className, $query, $fields);
    }

    public function getOne($query = array(), $fields = array())
    {
        return $this->findOneAsObject('Phalcon\Db\Adapter\Mongo\Document', $query, $fields);
    }

    public function findOneAsObject($className, $query = array(), $fields = array())
    {
        return new $className($this, parent::findOne($query, $fields));
    }
    
    /**
     * 记录条数
     * @param array $where 查询条件
     * @return bool
     */
    public function getCount($where = array()){
    	return parent::count($where);
    }

    public function doInsert($params, $options = array())
    {
    	return parent::insert((array) $params, $options);
    }

    public function doBatchInsert(array $col, $options = array())
    {
        // TODO: iterate props and create db refs
    }

    /**
     * 更新数据(注一次只能更新一条记录)
     * @param array $where 查询条件array(key=>value)
     * @param array $update_data 要更新的数据
     * @param array $options
     *            array('upsert'=>true,'multiple'=>true,'safe'=>true,'fsync'=>true,'timeout'=>1);
     *            upsert 条件不存在时是否插入
     *            multiple 修改全部 还是只修改单条，默认为false只改一条数据
     *
     * @return bool
     */
    public function doSave($where, $update_data, $options = array('upsert'=>true))
    {
        return parent::insert($where, $update_data, $options);
    }
    
    /**
     * 更新数据(注一次只能更新一条记录)
     * @param array $where 查询条件array(key=>value)
     * @param array $update_data 要更新的数据
     * @param array $options array('upsert'=>true,'multiple'=>true,'safe'=>true,'fsync'=>true,'timeout'=>1);
     *            upsert 条件不存在时是否插入
     *            multiple 修改全部 还是只修改单条，默认为false只改一条数据
     * @return bool
     */
    public function forUpdate($where, $update_data, $options = array('multiple'=>true)){
    	return parent::update($where, $update_data, $options);
    }
    
    /**
     * 删除记录
     * @param array $where  删除条件
     * @param array $options array('justOne'=>true,'safe'=>true,'fsync'=>true,'timeout'=>1); justOne 只删除一条数据
     * @return bool
     */
    public function doDelete($where, $option = array("justOne"=>false)){
    	if(empty($where)) return false;

    	return parent::remove($where, $option);
    }
}