<?php
use Joy\Overseasbasic\Services\Areas\AreasServices;

/**
 * 地区单元测试
 * 2016-4-20
 * @author fengyanchao
 *
 */
class AreasTest extends \Codeception\TestCase\Test
{
    private $areasServices;

    protected function setUp()
    {
        $this->areasServices = new AreasServices();
    }

    protected function tearDown()
    {

    }
    /**
     * 获取区域列表
     */
    public function test_getAreaList(){
        $re = $this->areasServices->getAreaList();
        $this->assertTrue(is_array($re));
    }

}
