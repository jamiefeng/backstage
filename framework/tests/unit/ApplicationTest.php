<?php
class ApplicationTest extends \Codeception\TestCase\Test
{
    /**
     * 
     * @var \Joy\Application
     */
    private $app;
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
        $this->app = new \Joy\Web\Application();
        $this->app->configure(UNIT_PATH.'/config.php')->init();
        
    }

    protected function _after()
    {
    }

    // tests
    public function testMe()
    {
        //测试BasePath是否正常
        $this->tester->assertEquals(\Joy::$config->basePath, $this->app->getBasePath());
    }
    
    /**
     * 测试缓存是否加载
     */
    public function testCache()
    {
        $cache = \Joy::$di->get('cache');
        $this->tester->assertNotNull($cache);
        $this->assertInstanceOf('\Phalcon\Cache\BackendInterface',$cache);
    }

}