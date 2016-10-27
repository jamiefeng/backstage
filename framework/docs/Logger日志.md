#通用日志配置与使用

**配置**

```
<?php
// 注意，本配置文件中所有关于组件名的设置均区分大小写
$runTimePath = ROOT_PATH . '/runtime';
return [
    'basePath' => ROOT_PATH . '/app', // 应用程序 的根目录
    'render' => 'json', //定义错误信息输出的页面，debug模式下此参数无效，如果为非debug模式，当设置为非json时会输出html
    'runtimePath' => $runTimePath, // 运行时生成的文件的目录，主要是用于保存日志
    'defaultNamespace' => 'Joy\Account', // 默认的命名空间
    'errorPage' => null, // 错误页面；允许使用PHP代码；系统提供了两个变量$code,$message，前者表示当前的页面的状态码，后者表示系统输出的错误内容。你可以根据自己的实际需要来显示错误页面
    'components' => [ // 设置系统需要加载的组件的属性
        'router' => [],
        'logger' => [ // 日志，允许同时开启多个日志
            'stream' => [  //日志驱动, 支持的驱动：stream文件流, file文件流
                'compress.zlib://' . $runTimePath . '/application.log.gz',
                
            ]
        ]
        ......
        
```




**使用**

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
    	$logger = \Joy::$di->get("logger");
        $logger->log('日志内容');
        $logger->error('日志内容');
        $logger->debug('日志内容');
        
	.......

```

#Pblog日志配置与使用

**配置**

```
<?php
// 注意，本配置文件中所有关于组件名的设置均区分大小写
$runTimePath = ROOT_PATH . '/runtime';
return [
    'basePath' => ROOT_PATH . '/app', // 应用程序 的根目录
    'render' => 'json', //定义错误信息输出的页面，debug模式下此参数无效，如果为非debug模式，当设置为非json时会输出html
    'runtimePath' => $runTimePath, // 运行时生成的文件的目录，主要是用于保存日志
    'defaultNamespace' => 'Joy\Account', // 默认的命名空间
    'errorPage' => null, // 错误页面；允许使用PHP代码；系统提供了两个变量$code,$message，前者表示当前的页面的状态码，后者表示系统输出的错误内容。你可以根据自己的实际需要来显示错误页面
    'components' => [ // 设置系统需要加载的组件的属性
        'router' => [],
        'logger' => [ // 日志，允许同时开启多个日志
           'Pblog' => [  //驱动必须为 Pblog 
                [
                    'host' => '10.1.1.202',  //日志接收服务器 所使用的IP
                    'port' => '18088',  //端口
                    'proto' => [        //默认的Pb格式对象类
                        'proto' => "Joy\\UserCenter\\Models\\Proto\\CommonLog\\CommonLog",  //提交数据对象类
                        'protoReturn' => "Joy\\UserCenter\\Models\\Proto\\CommonLog\\CommonLogReturn"  //接口返回的数据对象类
                    ],
                    'defaultData' => [  //预定义的默认日志数据， 此处需要根据 你的提交数据对象类来定义
                        'create_time' => time().'000',   
                        'pb_type' => '101',
                        'log_type' => NULL,
                        'data' =>  json_encode([]) ,  
                    ]
               ]                
           ]
        ]
        ......
        
```


**使用**

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
    	$logger = \Joy::$di->get("logger");
        
        $logger->log('日志内容');
        $logger->error('日志内容');
        
        //更多的日志信息
        $message = [
            'pb_type' => '101',
            'create_time' => time().'000',
            'log_type' => 'audit',
            'action' => '运营后台用户aaa登录',
            'sys_type' => 5,
            'business_type' =>  0,
            'data' =>  json_encode([]),
            'message'=>'用户登录信息'
        ];
        //对于数组必须转化为 json 字符串，不然 Logger 不作处理
        $message = json_encode($message);        
        $logger->debug($message);
        
	.......

```