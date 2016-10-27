<?php
use Joy\Overseasbasic\Services\LanguageServices ;

/**
 * 语言单元测试
 * 2016-4-20
 * @author fengyanchao
 *
 */
class LanguageTest extends \Codeception\TestCase\Test
{
    private $languageServices ;

    protected function setUp()
    {
        $this->languageServices = new LanguageServices ();
    }

    protected function tearDown()
    {

    }
    /**
     * 将数据库数据生成为数组形式
     */
    public function test_createlanguagefile(){
        $re = $this->languageServices->createlanguagefile();
        $this->assertTrue($re);
    }

}
