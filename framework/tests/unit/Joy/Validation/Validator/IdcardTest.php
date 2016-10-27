<?php
namespace Joy\Validation\Validator;

use \Phalcon\Validation\Validator\IdCard as vaildator;
use \Phalcon\Validation;
class IdcardTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
        //$app = new \Joy\Web\Application();
        //$app->configure(UNIT_PATH.'/config.php')->init();
    }

    protected function _after()
    {
    }

    // tests
    public function testCanAddIdcardValidator()
    {
        $validation = new Validation();
        $validation->add('Idcard', new vaildator(
            [
                'lengthInvalid' => '表单 ":field" 输入的身份证号码长度错误，要求15或18位；实际 :length位。'
            ]
        ));
        $this->assertInstanceOf('Phalcon\Validation', $validation);
    }
    
    public function testMe()
    {
        $validation = new Validation();
        $validation->add('Idcard', new vaildator(
            [
                'lengthInvalid' => '表单 ":field" 输入的身份证号码长度错误，要求15或18位；实际 :length位。'
            ]
        ));
        $messages = $validation->validate(array(
               'Idcard' => '422422198111237331',
        ));
        $this->assertCount(0, $messages);
    }
    
    public function testWrongVerify()
    {
        $validation = new Validation();
        $validation->add('Idcard', new vaildator(
            [
                'lengthInvalid' => '表单 ":field" 输入的身份证号码长度错误，要求15或18位；实际 :length位。'
            ]
        ));
        $messages = $validation->validate(array(
            'Idcard' => '42242219811123733x',
        ));
        $this->assertCount(1, $messages);
        $this->tester->assertEquals('表单 "Idcard" 输入的身份证校验失败', $messages[0]->getMessage());
    }
    public function testWrongLength()
    {
        $validation = new Validation();
        $validation->add('Idcard', new vaildator(
            [
                'lengthInvalid' => '表单 ":field" 输入的身份证号码长度错误，要求15或18位；实际 :length位。'
            ]
        ));
        $messages = $validation->validate(array(
            'Idcard' => '42242219811123733x1',
        ));
        $this->assertCount(1, $messages);
        $this->tester->assertEquals('表单 "Idcard" 输入的身份证号码长度错误，要求15或18位；实际 19位。', $messages[0]->getMessage());
    }

}