<?php
namespace Joy\Basic\Models;

use Phalcon\Mvc\Model\Message;

/**
 * 系统表数据模型
 */
class Pfsystem extends \Phalcon\Mvc\Model
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
     * 系统id
     * @Column(type="string",nullable=false, column="system_id")
     * @var string
     */ 
    public $systemId;
    
    /**
     * 平台id
     * @Column(type="string",nullable=false, column="platform_id")
     * @var string
     */ public $platformId;
    
    /**
     * 名称
     * @Column(type="string",nullable=true, column="name")
     * @var string
     */ public $name;
    
    /**
     * 系统url前缀
     * @Column(type="string",nullable=true, column="url")
     * @var string
     */ public $url;
    
    /**
     * 系统备注
     * @Column(type="string",nullable=true, column="note")
     * @var string
     */ public $note;
    
    /**
     * 删除标识1：存在;2：删除
     * @Column(type="integer",nullable=true, column="del_flag")
     * @var integer
     */ public $delFlag;
    
    /**
     * 排序，降序
     * @Column(type="integer",nullable=true, column="order_no")
     * @var integer
     */ public $orderNo;
    
    /**
     * model路径
     */
    const MP = 'Joy\Basic\Models\Pfsystem';
    /**
     * 可用
     */
    const NOT_DELETE = 1;
    /**
     * 删除
     */
    const DELETE = 2;
    /**
     * 表名前缀
     */
    private $_tableName = 'tbl_privilege_';
    /**
     * 表名后缀
     */
    private $_source = 'pfsystem';
    
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
    /**
     * 更新数据
     */
    function beforeUpdate(){
        
    }
    
    /**
     * 添加新数据
     */
    function beforeCreate(){
        $this->delFlag  = self::NOT_DELETE;
        if(!$this->orderNo){
            $this->orderNo  = 255;//默认排序
        }
        return $this->createValidation();
    }
    /**
     * 数据验证
     */
    function createValidation() {
        if (empty ( $this->systemId )) {
            $message = new Message ( '系统id不能为空', null, 'Error' );
            $this->appendMessage ( $message );
            return false;
        }
        if (empty ( $this->name )) {
            $message = new Message ( '名称不能为空', null, 'Error' );
            $this->appendMessage ( $message );
            return false;
        }
        if (empty ( $this->url )) {
            $message = new Message ( '系统url前缀不能为空', null, 'Error' );
            $this->appendMessage ( $message );
            return false;
        }
        return true;
    }
}
