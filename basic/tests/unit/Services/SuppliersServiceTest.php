<?php
use Joy\Overseasbasic\Services\Suppliers\SuppliersServiceServices;

/**
 * 供应商服务单元测试
 * 2016-4-20
 * @author fengyanchao
 *
 */
class SuppliersServiceTest extends \Codeception\TestCase\Test
{
    private $suppliersServiceServices;

    protected function setUp()
    {
        $this->suppliersServiceServices = new SuppliersServiceServices();
    }

    protected function tearDown()
    {

    }
    /**
     * 获取供应商服务关系
     */
    public function test_getSuppliersService(){
        $re = $this->suppliersServiceServices->getSuppliersService(45);
        $this->assertTrue(is_array($re));
    }
    /**
     * 获取供应商服务列表
     */
    //全部
    public function test_getServiceList(){
        $re = $this->suppliersServiceServices->getServiceList();
        $this->assertTrue(is_array($re));
    }
    //某个服务子服务
    public function test_getServiceList_parentId(){
        $re = $this->suppliersServiceServices->getServiceList(1);
        $this->assertTrue(is_array($re));
    }
    
}
