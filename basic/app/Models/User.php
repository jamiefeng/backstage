<?php
namespace Joy\Basic\Models;

use Phalcon\Mvc\Model\Message;

/**
 * 用户表数据模型
 */
class User extends \Phalcon\Mvc\Model
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
    * @Column(type="string",nullable=false, column="user_id")
    * @var string
    */ public $userId;
    
    /**
    * 真实姓名
    * @Column(type="string",nullable=true, column="real_name")
    * @var string
    */ public $realName;
    
    /**
    * 用户名
    * @Column(type="string",nullable=true, column="username")
    * @var string
    */ public $username;
    
    /**
    * 密码
    * @Column(type="string",nullable=true, column="password")
    * @var string
    */ public $password;
    
    /**
    * 座机
    * @Column(type="string",nullable=true, column="tel")
    * @var string
    */ public $tel;

    /**
    * 手机
    * @Column(type="string",nullable=true, column="mobile")
    * @var string
    */ public $mobile;
    
    /**
    * 邮箱
    * @Column(type="string",nullable=true, column="email")
    * @var string
    */ public $email;
    
    /**
    * 头像
    * @Column(type="string",nullable=true, column="image")
    * @var string
    */ public $image;
    
    /**
    * 部门id
    * @Column(type="string",nullable=true, column="department_id")
    * @var string
    */ public $departmentId;
    
    
    /**
    * 性别 标示男 1标示女 2
    * @Column(type="integer",nullable=true, column="sex")
    * @var integer
    */ public $sex;
    
    /**
    * 地址
    * @Column(type="string",nullable=true, column="address")
    * @var string
    */ public $address;
    
    /**
    * 传真
    * @Column(type="string",nullable=true, column="fax")
    * @var string
    */ public $fax;
    
    /**
     * 状态 1：启用; 2：禁用
     * @Column(type="integer",nullable=true, column="status")
     * @var integer
     */ 
    public $status;
    /**
    * 删除标识 1：存在; 2：删除
    * @Column(type="integer",nullable=true, column="del_flag")
    * @var integer
    */ public $delFlag;
    /**
     * 可用
     */
    const NOT_DELETE = 1;
    /**
     * 删除
     */
    const DELETE = 2;
    /**
     * 启用
     */
    const ENABLE = 1;
    /**
     * 禁用
     */
    const DISABLE = 2;
    /**
     * model路径
     */
    const MP = 'Joy\Basic\Models\User';

    /**
     * 表名前缀
     */
    private $_tableName = 'tbl_privilege_';
    /**
     * 表名后缀
     */
    private $_source = 'user';
    
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
        $this->delFlag  = self::NOT_DELETE;
        $this->status   = self::ENABLE;
        return $this->createValidation();
    }
    /**
     * 数据验证
     */
    function createValidation() {
        if (empty ( $this->userId )) {
            $message = new Message ( 'id不能为空', null, 'Error' );
            $this->appendMessage ( $message );
            return false;
        }
        if (empty ( $this->username )) {
            $message = new Message ( '用户名不能为空', null, 'Error' );
            $this->appendMessage ( $message );
            return false;
        }
        if (empty ( $this->sex )) {
            $message = new Message ( '性别不能为空', null, 'Error' );
            $this->appendMessage ( $message );
            return false;
        }
        if (empty ( $this->realName )) {
            $message = new Message ( '姓名不能为空', null, 'Error' );
            $this->appendMessage ( $message );
            return false;
        }
        if (empty ( $this->email )) {
            $message = new Message ( '邮箱不能为空', null, 'Error' );
            $this->appendMessage ( $message );
            return false;
        }
        if (empty ( $this->departmentId )) {
            $message = new Message ( '部门不能为空', null, 'Error' );
            $this->appendMessage ( $message );
            return false;
        }
        return true;
    }
}
