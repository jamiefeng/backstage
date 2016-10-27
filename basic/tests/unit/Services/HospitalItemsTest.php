<?php
use Joy\Overseasbasic\Services\Hospital\HospitalItemsServices;

/**
 * 医院项目单元测试
 * 2016-4-20
 * @author fengyanchao
 *
 */
class HospitalItemsTest extends \Codeception\TestCase\Test
{
    private $hospitalItemsServices;

    protected function setUp()
    {
        $this->hospitalItemsServices = new HospitalItemsServices();
    }

    protected function tearDown()
    {

    }
    /**
     * 获取医院项目介绍
     */
    public function test_getHospitalItemsList(){
        $re = $this->hospitalItemsServices->getHospitalItemsList(9);
        $this->assertTrue($re['count']>0);
    }

}
