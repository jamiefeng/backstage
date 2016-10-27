<?php
namespace Joy\Basic\Models;

use Phalcon\Mvc\Model\Message;

/**
 * 用户表角色关系数据模型
 */
class UserRole extends \Phalcon\Mvc\Model
{

    /**
     * 唯一ID
     * @Primary
     * @Identity
     * @Column(type="integer",column="id")
     * @var integer
     */
    public $id;
    
    /**
     * 用户id
     * @Column(type="string",nullable=true, column="user_id")
     * @var string
     */ public $userId;
    
    /**
     * 角色id
     * @Column(type="string",nullable=true, column="role_id")
     * @var string
     */ public $roleId;

    /**
     * model路径
     */
    const MP = 'Joy\Basic\Models\UserRole';

    /**
     * 表名前缀
     */
    private $_tableName = 'tbl_privilege_';
    /**
     * 表名后缀
     */
    private $_source = 'user_role';
    
    /**
     * 设置数据表名，这里可以设置与类名不一致的数据表名
     * @see \Phalcon\Mvc\Model::setSource()
     * @return string
     */
    public function setSource($source)
    {
        $this->_source = $source;
    }
    /**
     * @see \Phalcon\Mvc\Model::getSource()
     * @return string
     */
    public function getSource()
    {
        return $this->_tableName.$this->_source;
    }
    /**
     * 初始化
     *
     * @access public
     * @return void
     */
    public function initialize()
    {
        
    }
}
