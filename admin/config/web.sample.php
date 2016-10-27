<?php
// 注意，本配置文件中所有关于组件名的设置均区分大小写
$runTimePath = ROOT_PATH . '/runtime';
return [
    'basePath' => ROOT_PATH . '/app', // 应用程序 的根目录
    //'render' => 'json', //定义错误信息输出的页面，debug模式下此参数无效，如果为非debug模式，当设置为非json时会输出html
    'runtimePath' => $runTimePath, // 运行时生成的文件的目录，主要是用于保存日志
    'defaultNamespace' => 'Joy\Geneadmin', // 默认的命名空间
    'errorPage' => null, // 错误页面；允许使用PHP代码；系统提供了两个变量$code,$message，前者表示当前的页面的状态码，后者表示系统输出的错误内容。你可以根据自己的实际需要来显示错误页面
    'components' => [ // 设置系统需要加载的组件的属性
        'router' => [],
        'logger' => [ // 日志，允许同时开启多个日志
            'stream' => [
                '' . $runTimePath . '/logs/application.log',
                ''
            ]
        ]
        // 'database'=>[''],
        // 'mongo'=>[],
        ,
        'database' => [ // 数据库设置，可以设置多个数据库，每个数据库配置数组的键名为程序调用时使用的组件名
            'db' => [
                'adapter' => 'Mysql',
                'host' => '192.168.10.1',
                'username' => 'root',
                'password' => 'nykj,./mysql',//
                'dbname' => 'gene',
                'options' => [
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                    PDO::ATTR_AUTOCOMMIT => 1,
                ]
            ],
        ],
        'cache' => [
	        // Msgpack暂时无法成功编译到php5.6，所以php5.6下暂时无法启用Msgpack支持
	        'frontend' => 'Json', // 为支持与其他语言数据交换，请使用Json和Msgpack；支持Json、Msgpack、Data(php serialized),Output(PHP输出),None(不处理),Base64,IgBinary
	        'backend' => [
		        [
			        'adapter' => 'Memcache',
			        'host' => '127.0.0.1',
			        'port' => 11211
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
            'dir' => APP_PATH . '/Views/',
            'compiledPath' => $runTimePath . '/views/',
            'compiledExtension' => $runTimePath . '/views/',
            'compiledSeparator' => '%%',
            'compileAlways' => $runTimePath . '/views/'
        ],
        'baseUri' => '/'
    ],
    'modules' => []

    
];
