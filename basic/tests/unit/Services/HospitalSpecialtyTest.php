<?php
use Joy\Overseasbasic\Services\Hospital\HospitalSpecialtyServices;

/**
 * 医院擅长领域单元测试
 * 2016-5-4
 * @author fengyanchao
 *
 */
class HospitalSpecialtyTest extends \Codeception\TestCase\Test
{
    private $hospitalSpecialtyServices;

    protected function setUp()
    {
        $this->hospitalSpecialtyServices = new HospitalSpecialtyServices();
    }

    protected function tearDown()
    {

    }
    /**
     * 根据医院id获取擅长领域
     */
    public function test_getHospitalSpecialty(){
        $data = [196,192,191];
        $re = $this->hospitalSpecialtyServices->getHospitalSpecialty($data);
        $this->assertTrue(is_array($re));
    }
    /**
     * 获取擅长领域
     */
    public function test_getSpecialtyIds(){
        $re = $this->hospitalSpecialtyServices->getSpecialtyIds();
        $this->assertTrue(count($re)>1);
    }
}
