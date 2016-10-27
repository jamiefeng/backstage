<?php
namespace Joy;

/**
 * 基于PHPUnit的测试用例；虽然codeception的单元测试用例使用的仍然是PHPUnit，但是显然直接继承PHPUnit的测试用例可以提供更好的代码提示效果
 * @author dancebear
 *
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * 
     * @var \Joy\Application
     */
    private $app;

    protected function setUp()
    {
        $this->app = new \Joy\Web\Application();
        $this->app->configure(UNIT_PATH.'/config.php')->init();
        
    }

    protected function tearDown()
    {
    }

    // tests
    public function testMe()
    {
        //测试BasePath是否正常
        $this->assertEquals(\Joy::$config->basePath, $this->app->getBasePath());
    }
    
    public function testSetDatabase(){
        $this->assertInstanceOf('\Phalcon\Db\Adapter\Pdo\Mysql', \Joy::$di->get('db'));
    }

}