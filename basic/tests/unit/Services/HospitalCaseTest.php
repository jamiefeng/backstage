<?php
use Joy\Overseasbasic\Services\Hospital\HospitalCaseServices;

/**
 * 案例单元测试
 * 2016-4-20
 * @author fengyanchao
 *
 */
class HospitalCaseTest extends \Codeception\TestCase\Test
{
    private $hospitalCaseServices;

    protected function setUp()
    {
        $this->hospitalCaseServices = new HospitalCaseServices();
    }

    protected function tearDown()
    {

    }
    /**
     * 获取医院治疗案例列表
     */
    public function test_getHospitalCaseList(){
        $re = $this->hospitalCaseServices->getHospitalCaseList(9);
        $this->assertTrue($re['count']>0);
    }

}
