<?php
use Joy\Overseasbasic\Services\Users\UsersServices;

/**
 * 用户单元测试
 * 2016-5-5
 * @author fengyanchao
 *
 */
class UsersTest extends \Codeception\TestCase\Test
{
    private $usersServices;

    protected function setUp()
    {
        $this->usersServices = new UsersServices();
    }

    protected function tearDown()
    {

    }
    /**
     * 获取用户名称列表
     */
    public function test_getUserName(){
        $re = $this->usersServices->getUserName([1,2,3]);
        $this->assertTrue(!empty($re));
    }
    /**
     * 根据供应商id获取用户
     */
    public function test_findFirstBySuppliersId(){
        $re = $this->usersServices->findFirstBySuppliersId(45);
        $this->assertTrue(!empty($re));
    }
    /**
     * 根据供应商id获取用户
     */
    public function test_findFirstByHospitalId(){
        $re = $this->usersServices->findFirstByHospitalId(156);
        $this->assertTrue(!empty($re));
    }
    /**
     * 根据供应商id获取用户
     */
    public function test_getUserByUsername(){
        $re = $this->usersServices->getUserByUsername('admin');
        $this->assertTrue(!empty($re));
    }
    /**
     * 根据供应商id获取用户
     */
    public function test_checkUserName(){
        $re = $this->usersServices->checkUserName('admin');
        $this->assertTrue($re);
    }
    /**
     * 检测密码的有效性
     */
    public function test_checkPassword(){
        $re = $this->usersServices->checkPassword('47DTPSkJcCyUUyajsMe2Xie5W5UlWE+Z++flJpVTR3Cdcpv9rf','admin810bf');
        $this->assertTrue($re);
    }
    /**
     * 获取用户信息
     */
    public function test_getUserById(){
        $re = $this->usersServices->getUserById(1);
        $this->assertEquals('1',$re['userId']);
    }
    /**
     * 根据关键词获取账号名ID/供应商名ID
     */
    public function test_getUserIdOrsuppliersId(){
        $re = $this->usersServices->getUserIdOrsuppliersId('测试');
        $this->assertTrue(count($re['userId'])>0);
    }
    /**
     * 获取用户列表
     */
    public function test_getUsersList(){
        $tmpSearch['startTime'] = '2015-01-01';
        $tmpSearch['endTime'] = '2017-01-01';
        $tmpSearch['status']  = 1;
        $tmpSearch['isDel'] = '1';
        $tmpSearch['keywords']['userId'] = 43;
        $tmpSearch['keywords']['suppliersId'] = 42;
        $re = $this->usersServices->getUsersList($tmpSearch, 1, 20);
        $this->assertTrue(count($re['data'])>0);
    }
    //其它条件
    public function test_getUsersList_1(){
        $tmpSearch['keywords']['userId'] = 43;
        $tmpSearch['keywords']['suppliersId'] = 0;
        $re = $this->usersServices->getUsersList($tmpSearch, 1, 20);
        $this->assertTrue(is_array($re));
    }
    //其它条件
    public function test_getUsersList_2(){
        $tmpSearch['keywords']['userId'] = 0;
        $tmpSearch['keywords']['suppliersId'] = 1;
        $re = $this->usersServices->getUsersList($tmpSearch, 1, 20);
        $this->assertTrue(count($re['data'])>0);
    }
    /**
     * 删除账号
     */
    public function test_deleteUser(){
        $re = $this->usersServices->deleteUser([48]);
        $this->assertEquals('0',$re['code']);
    }
    //异常
    public function test_deleteUser_1(){
        $re = $this->usersServices->deleteUser([]);
        $this->assertEquals('1',$re['code']);
    }
    /**
     * 状态设置
     */
    public function test_setStatus(){
        $re = $this->usersServices->setStatus(47,2);
        $this->assertEquals('0',$re['code']);
    }
    //异常
    public function test_setStatus_1(){
        $re = $this->usersServices->setStatus('','');
        $this->assertEquals('1',$re['code']);
    }
    //异常
    public function test_setStatus_2(){
        $re = $this->usersServices->setStatus(1,1111111);
        $this->assertEquals('2',$re['code']);
    }
    //异常
    public function test_setStatus_3(){
        $re = $this->usersServices->setStatus(1111111,1);
        $this->assertEquals('3',$re['code']);
    }
    /**
     * 添加后台用户
     */
    public function test_createUsers(){
        $data = [];
        $postData['userName'] = 'u'.time();
        $postData['email'] = '54645@qq.com';
        $postData['mobile'] = '1375265874';
        $postData['realName'] = '联系人';
        $postData['roleId'] ='';
        $postData['userType'] = '2';
        $postData['suppliersId'] = '45';
        $postData['hospitalId'] = '';
        $postData['isReminder'] = 0;
        $postData['status'] = 1;
        $postData['password'] = '123456';
        $re = $this->usersServices->createUsers($postData);
        $this->assertTrue($re>0);
    }
    /**
     * 修改用户信息
     */
    public function test_updateUsers(){
        $data = [];
        $userId = 60;
        $postData['password'] = '123456';
        $re = $this->usersServices->updateUsers($userId,$postData);
        $this->assertTrue($re);
    }
    
}
