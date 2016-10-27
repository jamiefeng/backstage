<?php
namespace Joy\Basic\Models;

use Phalcon\Mvc\Model\Message;

/**
 * 操作权限值表数据模型
 */
class Pvalue extends \Phalcon\Mvc\Model
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
     * @Column(type="string",nullable=true, column="system_id")
     * @var string
     */ public $systemId;
    
    /**
     * 整型的位
     * @Column(type="integer",nullable=true, column="position")
     * @var integer
     */ public $position;
    
    /**
     * 名称
     * @Column(type="string",nullable=false, column="name")
     * @var string
     */ public $name;
    /**
     * 标识
     * @Column(type="string",nullable=false, column="sign")
     * @var string
     */ public $sign;
    /**
     * 排序号
     * @Column(type="integer",nullable=true, column="order_no")
     * @var integer
     */ public $orderNo;
    
    /**
     * 备注
     * @Column(type="string",nullable=true, column="remark")
     * @var string
     */ public $remark;

    /**
     * 表名前缀
     */
    private $_tableName = 'tbl_privilege_';
    /**
     * 表名后缀
     */
    private $_source = 'pvalue';

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
        return $this->createValidation();
    }
    
    /**
     * 添加新数据
     */
    function beforeCreate(){
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
        if (empty ( $this->sign )) {
            $message = new Message ( '标识不能为空', null, 'Error' );
            $this->appendMessage ( $message );
            return false;
        }
        if (!is_numeric($this->position)) {
            $message = new Message ( '整型位不能为空', null, 'Error' );
            $this->appendMessage ( $message );
            return false;
        }
        return true;
    }
}
