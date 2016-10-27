<?php
namespace Joy\Basic\Models;

use Phalcon\Mvc\Model\Message;

/**
 * 模块授权数据模型
 */
class Acl extends \Phalcon\Mvc\Model
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
     * 来源id
     * @Column(type="string",nullable=true, column="release_id")
     * @var string
     */ 
    public $releaseId;
    
    /**
     * 来源标示role标示角色user 标示用户
     * @Column(type="string",nullable=true, column="release_sn")
     * @var string
     */ 
    public $releaseSn;
    
    /**
     * 平台id
     * @Column(type="string",nullable=false, column="platform_id")
     * @var string
     */
    public $platformId;
    
    /**
     * 系统id
     * @Column(type="string",nullable=false, column="system_id")
     * @var string
     */
    public $systemId;
    /**
     * 模块id
     * @Column(type="string",nullable=true, column="module_id")
     * @var string
     */ public $moduleId;
    
    /**
     *
     * @Column(type="integer",nullable=true, column="acl_state")
     * @var integer
     */ public $aclState;
    
    /**
     * model路径
     */
    const MP = 'Joy\Basic\Models\Acl';

    /**
     * 表名前缀
     */
    private $_tableName = 'tbl_privilege_';
    /**
     * 表名后缀
     */
    private $_source = 'acl';
    
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
