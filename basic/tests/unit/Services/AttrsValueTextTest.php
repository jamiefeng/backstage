<?php
use Joy\Overseasbasic\Services\Attrs\AttrsValueTextServices;

/**
 * 属性内容单元测试
 * 2016-5-6
 * @author fengyanchao
 *
 */
class AttrsValueTextTest extends \Codeception\TestCase\Test
{
    private $attrsValueTextServices;

    protected function setUp()
    {
        $this->attrsValueTextServices = new AttrsValueTextServices();
    }

    protected function tearDown()
    {

    }
    /**
     * 获取内容
     */
    public function test_getAreaList(){
        $re = $this->attrsValueTextServices->getContent(4,27);
        $this->assertTrue(!empty($re));
    }
    /**
     * 添加或者属性内容
     */
    //编辑
    public function test_attrsValueTextSave(){
        $attrsValueTextData = array(
            'attr_id'=>4,
            'objectId'=>20,
            'content'=>'单元测试'
        );
        $re = $this->attrsValueTextServices->attrsValueTextSave($attrsValueTextData);
        $this->assertEquals(0,$re['code']);
    }
    //新增
    public function test_attrsValueTextSave_add(){
        $attrsValueTextData = array(
            'attr_id'=>5,
            'objectId'=>25,
            'content'=>'单元测试');
        $re = $this->attrsValueTextServices->attrsValueTextSave($attrsValueTextData);
        $this->assertEquals(0,$re['code']);
    }
    //异常-无数据输入
    public function test_attrsValueTextSave_no(){
        $attrsValueTextData = array();
        $re = $this->attrsValueTextServices->attrsValueTextSave($attrsValueTextData);
        $this->assertEquals(1,$re['code']);
    }
    //异常-无数据输入
    public function test_attrsValueTextSave_no2(){
        $attrsValueTextData = array('attr_id'=>5,
            'objectId'=>'50',
            'content'=>'');
        $re = $this->attrsValueTextServices->attrsValueTextSave($attrsValueTextData);
        $this->assertEquals(500,$re['code']);
    }
}
