<?php
define('JOY_DEBUG', true);
defined('ROOT_PATH') or define('ROOT_PATH', dirname(__DIR__));
defined('APP_PATH') or define('APP_PATH', ROOT_PATH . '/app');
include ROOT_PATH . '/../framework/Joy.php';
// 系统文件路径
$servicesPath = ROOT_PATH . '/app/Services';//服务类目录
$modelsPath   = ROOT_PATH . '/app/Models';//Models类目录
$libraryPath   = ROOT_PATH . '/app/Library';//Library类目录
$apiPath   = ROOT_PATH . '/app/Api';//Api类目录
include ROOT_PATH . '/app/Services/BaseServices.php';
includeAppSMdir($apiPath);
includeAppSMdir($libraryPath);
includeAppSMdir($modelsPath);
includeAppSMdir($servicesPath);

//读取文件夹文件
function includeAppSMdir($path){
    $dh = opendir($path);//打开目录
    while(($d = readdir($dh)) != false){
        //逐个文件读取，添加!=false条件，是为避免有文件或目录的名称为0
        if($d=='.' || $d == '..'){//判断是否为.或..，默认都会有
            continue;
        }
        $file = $path.'/'.$d;
        if(is_dir($file)){//如果为目录
            includeAppSMdir($file);//继续读取该目录下的目录或文件
        }elseif($file && file_exists($file) && $d!='BaseServices.php'){
            include $file;//引用类
        }
    }
}
(new \Joy\Web\Application())->run();
?>