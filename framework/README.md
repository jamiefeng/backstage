91160框架使用说明
======
* 说明

本框架基于Phalcon框架，对Phalcon框架进行了包装，使他可以自动通过配置来初始化Phalcon的系统组件

* 目录结构

```
┌───────┬────  app                  应用程序目录
│       │
│       ├────  Controllers          控制器程序目录
│       │
│       ├────  Models               数据库模型类目录
│       │
│       └────  Views                视图/模板文件目录
│
├────────────  config               配置文件目录
│
├────────────  docs                 程序相关文档目录
│
├────────────  public               web访问入口即index.php
│
├────────────  runtime              日志文件、文件缓存目录，要求可写，虽然以后日志可能不会写在这里，但框架本身的日志默认是在定个目录，所以需要可写权限
│
├────────────  scripts              服务器配置脚本等
│
├────────────  tests                单元测试测试用例
│
├────────────  vendor               composer依赖文件
│
├────────────  codeception.yml      codecept测试框架测试用例
│
├────────────  .gitignore           git忽略文件的配置文件
│
├────────────  requirements.php     系统依赖检查程序
│
└────────────  composer.json        composer依赖配置文件
```
* 关于配置文件

系统正式的配置文件请不要提交到版本库里，提交到版本库内的必须是配置文件的实例文件
* 配置文件实例

```

<?php
// 注意，本配置文件中所有关于组件名的设置均区分大小写
$runTimePath = ROOT_PATH . '/runtime';
return [
    'basePath' => ROOT_PATH . '/app', // 应用程序 的根目录
    'render' => 'json', //定义错误信息输出的页面，debug模式下此参数无效，如果为非debug模式，当设置为非json时会输出html；注意，请不要屏蔽此条配置
    'runtimePath' => $runTimePath, // 运行时生成的文件的目录，主要是用于保存日志
    'defaultNamespace' => 'Joy\Account', // 默认的命名空间
    'errorPage' => null, // 错误页面；允许使用PHP代码；系统提供了两个变量$code,$message，前者表示当前的页面的状态码，后者表示系统输出的错误内容。你可以根据自己的实际需要来显示错误页面
    'components' => [ // 设置系统需要加载的组件的属性
        'router' => [],
        'logger' => [ // 日志，允许同时开启多个日志
            'stream' => [
                'compress.zlib://' . $runTimePath . '/application.log.gz',
                ''
            ]
        ]
        // 'database'=>[''],
        // 'mongo'=>[],
        ,
        'database' => [ // 数据库设置，可以设置多个数据库，每个数据库配置数组的键名为程序调用时使用的组件名
            'db' => [
                'adapter' => 'Mysql',
                'host' => 'localhost',
                'username' => 'DatabaseUser',
                'password' => 'DatabasePassword',
                'dbname' => 'DatabaseName',
                'options' => [
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
                ]
            ]
        ],
        'cache' => [
            // Msgpack暂时无法成功编译到php5.6，所以php5.6下暂时无法启用Msgpack支持
            'frontend' => 'Json', // 为支持与其他语言数据交换，请使用Json和Msgpack；支持Json、Msgpack、Data(php serialized),Output(PHP输出),None(不处理),Base64,IgBinary
            'backend' => [
                [
                    'adapter' => 'Redis',
                    'host' => '127.0.0.1',
                    'port' => 6379
                ]
            ]
        ],
        'metadata' => [
            'adapter' => 'Memory',
            
            // 支持 Memory、Memcache、Redis
            // 公共参数：lifetime prefix
            // Memcache参数：host（主机） port（端口）persistent（是否长连接）
            // Redis参数：Redis（Redis链接url）
            'metaDataDir' => $runTimePath . '/metadata/'
        ]
    ],
    
    'application' => [
        'controllersDir' => APP_PATH . '/controllers/',
        'modelsDir' => APP_PATH . '/models/',
        'views' => [
            'dir' => APP_PATH . '/views/',
            'compiledPath' => $runTimePath . '/views/',
            'compiledExtension' => $runTimePath . '/views/',
            'compiledSeparator' => '%%',
            'compileAlways' => $runTimePath . '/views/'
        ],
        'baseUri' => '/'
    ],
    'error' =>[
        '404' => '/public/404.php',
	 //404错误页面配置
    ],
    'modules' => []

    
];
```
* 控制器实例

```
<?php
namespace Joy\Account\Controllers;

use Joy\Account\Models\Card;

/**
 * 卡片管理接口
 * 
 * @author dancebear
 *        
 */
class CardController extends \Joy\Web\Controller
{

    /**
     * 新建一个卡片类型
     * 
     * @param string $cardName            
     * @param integer $sortId            
     * @param number $unitId            
     * @param string $validator            
     * @param string $comment            
     * @return Ambigous <\Phalcon\Http\Response, \Phalcon\Http\ResponseInterface>|\Phalcon\Http\Response
     */
    public function createAction($cardName, $sortId, $unitId = 0, $validator = null, $comment = '')
    {
        $card = new Card();
        $card->cardName = $cardName;
        $card->sortId = (int) $sortId;
        $card->unitId = (int) $unitId;
        $card->comment = $comment;
        $card->validator = $validator;
        if ($card->save())
            return $this->sendError(200, '卡片数据保存成功');
        else
            return $this->catchModelMessage($card);
    }

    /**
     * 取得卡片列表
     * 
     * @return Ambigous <\Phalcon\Http\Response, \Phalcon\Http\ResponseInterface>
     */
    public function listAction()
    {
        $cards = [];
        foreach (Card::find() as $card) {
            if (! isset($cards[$card->sortId]))
                $cards[$card->sortId] = [];
            $cards[$card->sortId][] = $card->toArray();
        }
        return [
            'status' => 200,
            'cards' => $cards
        ];
    }

    /**
     * 删除卡片记录
     * 
     * @param integer $typeId            
     * @return Ambigous <\Phalcon\Http\Response, \Phalcon\Http\ResponseInterface>
     */
    public function deleteAction($typeId)
    {
        if (Card::findFirst((int) $typeId)->delete())
            return $this->sendError(200, 'success');
        else
            return $this->sendError(502, 'error');
    }
}

```

* 模型类文件实例

```
<?php
namespace Joy\Account\Models;

use \Phalcon\Mvc\Model\Message;

/**
 * 家庭成员资料表，保存家庭成员信息，在挂号时系统将查询此数据表的信息用于挂号
 *
 * @author dancebear
 *        
 */
class FamilyMembers extends \Phalcon\Mvc\Model
{

    /**
     * 数据的删除标记
     *
     * @var string
     */
    const DELETE = 'D';

    /**
     * 数据的正常标记
     *
     * @var string
     */
    const NOT_DELETE = 'N';

    /**
     * 男性
     *
     * @var string
     */
    const SEX_MALE = 'M';

    /**
     * 女性
     *
     * @var string
     */
    const SEX_FEMALE = 'F';

    /**
     * 未知
     *
     * @var string
     */
    const SEX_NONE = 'N';

    /**
     * 成员关系：本人
     *
     * @var int
     */
    const RELATION_SELF = 1;

    /**
     * 成员关系：其他
     *
     * @var int
     */
    const RELATION_OTHER = 2;

    /**
     * 最多家庭成员个数
     *
     * @var int
     */
    const MEMBER_MAX_NUMBER = 5;

    /**
     * 指定时间段内的最大修改次数
     *
     * @var int
     */
    const MEMBER_MAX_UPDATE = 3;

    /**
     * 未知血型
     * @var int
     */
    const BLOOD_OTHER = 0;
    
    /**
     * A型血
     * @var int
     */
    const BLOOD_A = 1;

    /**
     * B型血
     * @var int
     */
    const BLOOD_B = 2;

    /**
     * O型血
     * @var int
     */
    const BLOOD_O = 3;

    /**
     * AB型血
     * @var int
     */
    const BLOOD_AB = 4;

    /**
     * 已婚
     * @var string
     */
    const MARITAL_MARRIED = 'M';
    
    /**
     * 未婚
     * @var string
     */
    const MARITAL_UNMARRIED = 'U';

    /**
     * 离异
     * @var string
     */
    const MARITAL_DIVORCED = 'D';

    /**
     * 其他
     * @var string
     */
    const MARITAL_OTHER = 'O';
    /**
     * 家庭成员的的唯一身份编号
     *
     * @var int @Primary
     *      @Identity
     *      @Column(type="integer",nullable=false,column='member_id')
     */
    public $memberId;

    /**
     * 关联帐号ID
     * @Column(type="integer",nullable=false,column='user_id')
     *
     * @var int
     */
    public $userId;

    /**
     * 家庭成员手机号码
     * @Column(type="string",nullable=false,length="12")
     *
     * @var string
     */
    public $mobile;

    /**
     * 家庭成员的出生日期
     * @Column(type="date",nullable=true)
     *
     * @var string
     */
    public $birthday;

    /**
     * 成员真实姓名
     * @Column(type="string",nullable=false,length="100",column="true_name")
     *
     * @var string
     */
    public $trueName;

    /**
     * 性别
     * @Column(type="char",nullable=false,length="1")
     *
     * @var string
     */
    public $sex;

    /**
     * 与帐号的关系
     * @Column(type="char",nullable=false,length="1")
     *
     * @var string
     */
    public $relation;

    /**
     * 职业
     * @Column(type="string",nullable=true,length="100")
     *
     * @var string
     */
    public $job;

    /**
     * 帐号注册渠道ID
     * @Column(type="integer",nullable=true,column="channel_id")
     *
     * @var int
     */
    public $channelId;

    /**
     * 帐号注册来源医院ID
     * @Column(type="integer",nullable=true,column="unit_id")
     *
     * @var int
     */
    public $unitId;

    /**
     * 血型
     * @Column(type="integer",nullable=true,column="blood_type")
     *
     * @var int
     */
    public $bloodType;

    /**
     * 婚姻状况
     * @Column(type="char",nullable=true,length="1",column="marital_status")
     *
     * @var string
     */
    public $maritalStatus;

    /**
     * 软删除标记，N表示正常状态，D表示已经删除
     * @Column(type="char",nullable=false,length="1",column="is_delete")
     *
     * @var string
     */
    public $isDelete;

    /**
     * 添加时间
     * @Column(type="datetime",nullable=true,column="creation_date")
     *
     * @var string
     */
    public $creationDate;

    /**
     * 修改时间
     * @Column(type="datetime",nullable=true,column="modified_date")
     *
     * @var string
     */
    public $modifiedDate;

    /**
     * 资料修改状态；用于标记是否增加修改次数
     * 当修改证件如有效证件或社保卡时需要标记此状态为true
     */
    public $changeData = false;

    /**
     * 默认增加修改次数，如果是管理员操作，请将此字段设置为true
     *
     * @var boolean
     */
    public $isAdmin = false;

    /**
     * 成员资料关联的帐号
     *
     * @var AccountInfo
     */
    private $account;

    /**
     * (non-PHPdoc)
     *
     * @see \Phalcon\Mvc\Model::getSource()
     */
    public function getSource()
    {
        return 'family_members';
    }

    /**
     * 初始化软删除标记
     * 初始化数据表外键关联
     */
    public function initialize()
    {
        $this->keepSnapshots(true);
        $this->addBehavior(new \Phalcon\Mvc\Model\Behavior\SoftDelete([
            'field' => 'isDelete',
            'value' => self::DELETE
        ]));
        $this->belongsTo('userId', '\Joy\Account\Models\AccountInfo', 'userId', [
            'alias' => 'Member'
        ]);
        $this->hasMany("memberId", '\Joy\Account\Models\MemberCard', "memberId", [
            'alias' => 'MemberCards'
        ]);
    }

    /**
     * 验证数据的有效性
     */
    public function validation()
    {
        // 去除文本字段的非法字符
        $this->trueName = filter_var($this->trueName, FILTER_SANITIZE_STRING);
        $this->job = filter_var($this->job, FILTER_SANITIZE_STRING);
        $this->maritalStatus = filter_var($this->maritalStatus, FILTER_SANITIZE_STRING);
        $this->channelId = filter_var($this->channelId, FILTER_SANITIZE_NUMBER_INT);
        $this->unitId = filter_var($this->unitId, FILTER_SANITIZE_NUMBER_INT);
        $this->memberId = filter_var($this->memberId,FILTER_SANITIZE_NUMBER_INT);
        try {
            $date = new \DateTime($this->birthday);
            $this->birthday = $date->format('Y-m-d');
        } catch (\Exception $e) {
            $this->birthday = '0000-00-00';
        }
        // fixed return "-0001-11-30" when $this->birthday is null
        // 当$this->birthday 为null或者空时，有的服务器上会返回'-0001-11-30'而非我们需要的'0000-00-00'
        $this->birthday = $this->birthday == '-0001-11-30' ? '0000-00-00' : $this->birthday;
        $this->validate(new \Phalcon\Mvc\Model\Validator\Mobile([
            'field' => 'mobile'
        ]));
        if ($this->validationHasFailed() == true) {
            return false;
        }
        $this->validate(new \Phalcon\Mvc\Model\Validator\StringLength([
            'field' => 'trueName',
            'min' => 2,
            'max' => 100
        ]));
        if ($this->validationHasFailed() == true) {
            return false;
        }
        $this->validate(new \Phalcon\Mvc\Model\Validator\Inclusionin([
            'field' => 'relation',
            'domain' => [
                self::RELATION_SELF,
                self::RELATION_OTHER
            ]
        ]));
        if ($this->validationHasFailed() == true) {
            return false;
        }
        $this->validate(new \Phalcon\Mvc\Model\Validator\StringLength([
            'field' => 'job',
            'min' => 2,
            'max' => 100,
            'allowEmpty' => true
        ]));
        if ($this->validationHasFailed() == true) {
            return false;
        }
        $this->validate(new \Phalcon\Mvc\Model\Validator\Inclusionin([
            'field' => 'bloodType',
            'domain' => [
                self::BLOOD_OTHER,
                self::BLOOD_A,
                self::BLOOD_AB,
                self::BLOOD_B,
                self::BLOOD_O
            ],
            'allowEmpty' => true
        ]));
        if ($this->validationHasFailed() == true) {
            return false;
        }
        $this->validate(new \Phalcon\Mvc\Model\Validator\Inclusionin([
            'field' => 'maritalStatus',
            'domain' => [
                self::MARITAL_DIVORCED,
                self::MARITAL_MARRIED,
                self::MARITAL_OTHER,
                self::MARITAL_UNMARRIED
            ],
            'allowEmpty' => true
        ]));
        if ($this->validationHasFailed() == true) {
            return false;
        }
    }

    /**
     * 验证数据前对未初始化的数据进行初始化
     */
    public function beforeValidation()
    {
        if ($this->bloodType == null)
            $this->bloodType = self::BLOOD_OTHER;
        if ($this->isDelete == null)
            $this->isDelete = self::NOT_DELETE;
        if ($this->sex == null)
            $this->sex = self::SEX_NONE;
        if ($this->channelId == null)
            $this->channelId = 0;
        if ($this->unitId == null)
            $this->unitId = 0;
        if ($this->birthday == null)
            $this->birthday = '0000-00-00';
        if ($this->maritalStatus == null)
            $this->maritalStatus = self::MARITAL_OTHER;
    }

    /**
     * 检测资料修改的次数；总的帐号资料修改次数不得超过3次
     * 渠道用户不允许修改成员资料
     *
     * @return boolean
     */
    public function beforeUpdate()
    {
        // 没有帐号；为渠道准备
        if ($this->userId == 0) {
            $message = new Message('仅渠道用户允许修改成员资料', null, 'Error');
            $this->appendMessage($message);
            return false;
        }
        // 不允许变更成员关系
        if ($this->hasChanged('relation')) {
            $message = new Message('不允许变更成员关系', null, 'Error');
            $this->appendMessage($message);
            return false;
        }
        // 手机号码、证件号码未修改，直接执行SQL
        if (! $this->hasChanged('mobile') && $this->changeData === false && $this->isAdmin == true)
            return true;
        $this->account = AccountInfo::findFirst([
            "conditions" => "userId = ?1",
            "bind" => [
                1 => $this->userId
            ]
        ]);
        if (self::MEMBER_MAX_UPDATE <= $this->account->modifiedCount) {
            return false;
        }
        $this->account->modifiedCount = $this->account->modifiedCount + 1;
        return true;
    }

    /**
     * 检测用户的成员帐号数量
     *
     * @see FamilyMembers::Member_Max_Number
     * @return boolean
     */
    public function beforeCreate()
    {
        // 非用户直接跳过检查；为渠道准备
        if ($this->userId == 0)
            return true;
        $this->account = AccountInfo::findFirst([
            "conditions" => "userId = :userId:",
            "bind" => [
                'userId' => $this->userId
            ]
        ]);
        if ($this->account !== false) {
            // 检测最大成员个数
            $memberNumber = self::count([
                "conditions" => "userId = ?1",
                "bind" => [
                    1 => $this->userId
                ]
            ]);
            // 帐号本人类型的成员帐号只能有一个且不能删除
            if ($memberNumber > 0 && $this->relation == self::RELATION_SELF) {
                $this->appendMessage(new Message('不允许添加多个主成员', null, 'Error'));
                return false;
            }
            if (self::MEMBER_MAX_NUMBER <= $memberNumber || self::MEMBER_MAX_UPDATE <= $this->account->modifiedCount) {
                $this->appendMessage(new Message('最大修改次数或最多允许成员数量超过限制' . $this->account->modifiedCount, null, 'Error'));
                return false;
            }
            // 添加主成员帐号不计入修改次数
            if ($this->relation != self::RELATION_SELF)
                $this->account->modifiedCount = $this->account->modifiedCount + 1;
        }
        return true;
    }

    /**
     * 删除时校验是否是帐号本人，不允许删除帐号本人的成员帐号
     *
     * @todo 修改是否计算编辑次数
     * @return boolean
     */
    public function beforeDelete()
    {
        // 帐号本人类型的成员帐号只能有一个且不能删除
        if ($this->relation == self::RELATION_SELF) {
            $message = new Message('只允许一个登录账号本人类型的成员账号', null, 'Error');
            $this->appendMessage($message);
            return false;
        }
        
        $this->account = AccountInfo::findFirst([
            "conditions" => "userId = ?1",
            "bind" => [
                1 => $this->userId
            ]
        ]);
        if ($this->account !== false) {
            if (self::MEMBER_MAX_UPDATE <= $this->account->modifiedCount) {
                return false;
            }
        }
        return true;
    }

    /**
     * 修改更新次数
     *
     * @return boolean
     */
    private function changeModifiedCount()
    {
        if ($this->userId == 0 || $this->isAdmin)
            return false;
        $this->account->isAdmin = true;
        $this->account->save();
        return true;
    }

    /**
     * 更新或添加完毕后更新修改次数
     *
     * @return boolean
     */
    public function afterSave()
    {
        $this->changeModifiedCount();
        return true;
    }

    /**
     * 删除完毕后更新修改次数
     *
     * @return boolean
     */
    public function afterDelete()
    {
        $this->changeModifiedCount();
        return true;
    }

    /**
     * 获取用户的卡片信息
     * 
     * @param string $parameters            
     * @return multitype:NULL
     */
    public function getCards($parameters = null)
    {
        $cards = [];
        $cardType = new Card();
        foreach ($this->getRelated('MemberCards', $parameters) as $card) {
            $cardType->typeId = $card->cardType;
            $_cardType = $cardType->loadCache();
            unset($_cardType['creationDate'], $_cardType['validator'], $_cardType['isDelete'], $_cardType['modifiedDate'], $_cardType['typeId']);
            $card = array_merge($card->toArray(), $_cardType);
            unset($card['creationDate'], $card['isDelete'], $card['modifiedDate'], $card['memberId']);
            $cards[] = $card;
        }
        return $cards;
    }
}
```

* index.php实例

```

<?php
/**
* 网页入口文件
 */
$stage = getenv('APPLICATION_STAGE') ? getenv('APPLICATION_STAGE') : 'development';
if ($stage == 'production') {
    defined('JOY_DEBUG') or define('JOY_DEBUG', false);
} else {
    defined('JOY_DEBUG') or define('JOY_DEBUG', true);
}
defined('ROOT_PATH') or define('ROOT_PATH', dirname(__DIR__));
defined('APP_PATH') or define('APP_PATH', ROOT_PATH . '/app');
include ROOT_PATH . '/joysoft/framework/Joy.php';
(new \Joy\Web\Application())->run();

```

* 路由设置

```
<?php
namespace Joy\Account\Controllers;

/**
 * 默认控制器目录的路由配置信息
 * 
 * @author dancebear
 *        
 */
class Routes extends \Phalcon\Mvc\Router\Group
{

    public function initialize($paths)
    {
        // Default paths
        $this->setPaths(array(
            'namespace' => 'Joy\Account\Controllers'
        ));
        
        // All the routes start with /
        $this->setPrefix('/v2/');
        
        $this->add('', array(
            'controller' => 'index',
            'action' => 'main'
        ));
        // Add a route to the group
        $this->addPost('user/auth', array(
            'controller' => 'normal',
            'action' => 'login'
        ));
        $this->addPost('user/register', array(
            'controller' => 'normal',
            'action' => 'register'
        ));
        //渠道用户注册API接口
        $this->addPost('user/channelRegister', array(
            'controller' => 'normal',
            'action' => 'registerFromChannel'
        ));
        $this->addPost('user/member', array(
            'controller' => 'user',
            'action' => 'addMember'
        ));
        $this->addPut('user/member', array(
            'controller' => 'user',
            'action' => 'editMemeber'
        ));
        $this->addPost('user/memberCard', array(
            'controller' => 'user',
            'action' => 'addMemberCard'
        ));
        $this->addPost('card/create', array(
            'controller' => 'card',
            'action' => 'create'
        ));
        $this->addGet('card/list', array(
            'controller' => 'card',
            'action' => 'list'
        ));
        
        // Add another route to the group
        $this->addGet('user/{id:([\d+])}', array(
            'controller' => 'user',
            'action' => 'userInfo'
        ));
        
        // This route maps to a controller different than the default
        $this->add('/captcha.png', array(
            'controller' => 'index',
            'action' => 'captcha'
        ));
    }
}

```

* 视图文件

支持纯``php``文件和``volt``格式的视图文件，如果需要使用smarty，请自行重载``Joy\Application``类中的``setView``方法。
Smarty的支持请使用``Phalcon\Mvc\View\Engine\Smarty``类；系统还支持``MustCache``和``Twig``模板引擎，你可以同时混用多种模板引擎。
注意：如果你同时混用多种模板引擎时，不同模板引擎的模板文件是不能够互相包含和继承的。

* nginx配置

```

try_files $uri $uri/ @rewrite;

location @rewrite {
    rewrite ^/(.*)$ /index.php?_url=/$1;
}

```

* Phalcon文档

[http://docs.phalconphp.com/en/latest/index.html](Phalcon文档)

及

[https://github.com/phalcon/incubator/tree/1.3.0](Library文档)

* 关于.gitignore文件

请默认在此文件中添加以下内容以忽略zendstudio的配置文件及系统配置文件
```
/.settings
/vendor
/.buildpath
/.project
/composer.lock
/config/web.php
/deployment.xml
/deployment.properties
/tests/_output/*
```
