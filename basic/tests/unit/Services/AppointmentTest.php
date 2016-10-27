<?php
use Joy\Overseasbasic\Services\Order\AppointmentServices;
use Joy\Overseasbasic\Models\HospitalAppointment;

/**
 * 预约单元测试
 * 2016-5-3
 * @author fengyanchao
 *
 */
class AppointmentTest extends \Codeception\TestCase\Test
{
    private $appointmeantServices;

    protected function setUp()
    {
        $this->appointmeantServices = new AppointmentServices();
    }
    /**
     * 每次测试完成后都会执行此语句清理数据
     */
    protected function tearDown()
    {
        $query = new \Phalcon\Mvc\Model\Query('DELETE FROM Joy\Overseasbasic\Models\HospitalAppointment', \Joy::$di);
        $query->execute();
    }
    
    /**
     * WAP首页立即预约
     */
    //正常预约
    public function test_saveOrderFromWapHome(){
        //姓名
        $postData['appointmentName'] = '单元测试3';
        //性别
        $postData['sex'] = '';
        //年龄
        $postData['age'] = '';
        //电话
        $postData['tel'] = '13750363584';
        //微信
        $postData['wechat'] = '';
        //意向项目
        $postData['project'] = '赴日本医疗';
        //描述
        $postData['intro'] = '测试成功';
        //来源 ：线上
        $postData['source'] = HospitalAppointment::SOURCE_ONLINE_WAP;
        //渠道来源
        $postData['channelId'] = '1';
        $re = $this->appointmeantServices->saveOrderFromWapHome($postData);
        $this->assertEquals(0,$re['code']);
    }
    //预约姓名为空
    public function test_saveOrderFromWapHome_name(){
        $postData['appointmentName'] = '';
        $postData['tel'] = '13750363581';
        $re = $this->appointmeantServices->saveOrderFromWapHome($postData);
        $this->assertEquals(1,$re['code']);
    }
    //预约电话为空
    public function test_saveOrderFromWapHome_tel(){
        $postData['appointmentName'] = 'name';
        $postData['tel'] = '';
        $re = $this->appointmeantServices->saveOrderFromWapHome($postData);
        $this->assertEquals(1,$re['code']);
    }
    //预约电话格式错误
    public function test_saveOrderFromWapHome_tel_format(){
        $postData['appointmentName'] = 'name';
        $postData['tel'] = '123456';
        $re = $this->appointmeantServices->saveOrderFromWapHome($postData);
        $this->assertEquals(1,$re['code']);
    }
    /**
     * 前台添加预约记录
     */
    //正常预约
    public function test_saveOrder(){
        //姓名
        $postData['appointmentName'] = '单元测试2';
        //性别
        $postData['sex'] = '1';
        //年龄
        $postData['age'] = '18';
        //电话
        $postData['tel'] = '13750363584';
        //医院id
        $postData['hospitalId'] = '193';
        //供应商id
        $postData['suppliersId'] = '';
        //微信
        $postData['wechat'] = '';
        //描述
        $postData['intro'] = '测试成功';
        //来源 ：线上
        $postData['source'] = HospitalAppointment::SOURCE_ONLINE_WAP;
        
        $re = $this->appointmeantServices->saveOrder($postData);
        $this->assertEquals(0,$re['code']);
    }

    /**
     * 查询预约列表
     */
    public function test_getAppointmentList(){
        $params['startTime'] = '2015-05-01';
        $params['endTime'] = '2017-09-01';
        $params['ids'] = '405,406,407,408';
        $params['isRead'] = 2;
        $params['isDel'] = 1;
        $params['keywords']['hospitalId'] = 43;
        $params['keywords']['suppliersId'] = 42;
        $re = $this->appointmeantServices->getAppointmentList($params);
        $this->assertTrue(is_array($re));
    }
    //其它条件
    public function test_getAppointmentList1(){
        $params['startTime'] = '2015-05-01';
        $params['endTime'] = '2017-09-01';
        $params['ids'] = '';
        $params['isRead'] = 2;
        $params['isDel'] = 1;
        $params['keywords']['hospitalId'] = 43;
        $params['keywords']['suppliersId'] = 0;
        $re = $this->appointmeantServices->getAppointmentList($params);
        $this->assertTrue(is_array($re));
    }
    //其它条件
    public function test_getAppointmentList2(){
        $params['startTime'] = '2015-05-01';
        $params['endTime'] = '2017-09-01';
        $params['ids'] = '';
        $params['isRead'] = 2;
        $params['isDel'] = 1;
        $params['keywords']['hospitalId'] = 0;
        $params['keywords']['suppliersId'] = 42;
        $re = $this->appointmeantServices->getAppointmentList($params);
        $this->assertTrue(is_array($re));
    }
    /**
     * 获取预约单审核记录
     */
     public function test_getRecord(){
           $re = $this->appointmeantServices->getRecord(441);
           $this->assertTrue(is_array($re));
     }
     public function test_getRecord2(){
         $re = $this->appointmeantServices->getRecord(441,2);
         $this->assertTrue(is_array($re));
     }
     /**
      * 删除预约单
      */
     public function test_delectAppointment(){
         //姓名
         $postData['appointmentName'] = '单元测试3';
         //性别
         $postData['sex'] = '';
         //年龄
         $postData['age'] = '';
         //电话
         $postData['tel'] = '13750363584';
         //微信
         $postData['wechat'] = '';
         //意向项目
         $postData['project'] = '赴日本医疗';
         //描述
         $postData['intro'] = '测试成功';
         //来源 ：线上
         $postData['source'] = HospitalAppointment::SOURCE_ONLINE_WAP;
         //渠道来源
         $postData['channelId'] = '1';
         $addre = $this->appointmeantServices->saveOrderFromWapHome($postData);
         $re = $this->appointmeantServices->delectAppointment([$addre['data']]);
         $this->assertEquals(0,$re['code']);
     }
     public function test_delectAppointment1(){
         $re = $this->appointmeantServices->delectAppointment([]);
         $this->assertEquals(1,$re['code']);
     }
     public function test_delectAppointment2(){
         $re = $this->appointmeantServices->delectAppointment([2000000]);
         $this->assertEquals(1,$re['code']);
     }
     /**
      * 预约单详情
      */
     public function test_getDetail(){
         //姓名
         $postData['appointmentName'] = '单元测试3';
         //性别
         $postData['sex'] = 1;
         //年龄
         $postData['age'] = 26;
         //电话
         $postData['tel'] = '13750363584';
         //微信
         $postData['wechat'] = '';
         //意向项目
         $postData['project'] = '赴日本医疗';
         //描述
         $postData['intro'] = '测试成功';
         //来源 ：线上
         $postData['source'] = HospitalAppointment::SOURCE_ONLINE_WAP;
         //渠道来源
         $postData['channelId'] = '1';
         $addre = $this->appointmeantServices->saveOrderFromWapHome($postData);
         $re = $this->appointmeantServices->getDetail($addre['data']);
         $this->assertEquals($addre['data'],$re['appointmentId']);
     }
     /**
      * 获取预约记录基本信息
      */
     public function test_findFirst(){
         //姓名
         $postData['appointmentName'] = '单元测试3';
         //性别
         $postData['sex'] = '';
         //年龄
         $postData['age'] = '';
         //电话
         $postData['tel'] = '13750363584';
         //微信
         $postData['wechat'] = '';
         //意向项目
         $postData['project'] = '赴日本医疗';
         //描述
         $postData['intro'] = '测试成功';
         //来源 ：线上
         $postData['source'] = HospitalAppointment::SOURCE_ONLINE_WAP;
         //渠道来源
         $postData['channelId'] = '1';
         $addre = $this->appointmeantServices->saveOrderFromWapHome($postData);
         $re = $this->appointmeantServices->findFirst($addre['data']);
         $this->assertEquals($addre['data'],$re['appointmentId']);
     }
     /**
     * 设置预约单状态
     */
     public function test_setStatus(){
         //姓名
         $postData['appointmentName'] = '单元测试3';
         //性别
         $postData['sex'] = '';
         //年龄
         $postData['age'] = '';
         //电话
         $postData['tel'] = '13750363584';
         //微信
         $postData['wechat'] = '';
         //意向项目
         $postData['project'] = '赴日本医疗';
         //描述
         $postData['intro'] = '测试成功';
         //来源 ：线上
         $postData['source'] = HospitalAppointment::SOURCE_ONLINE_WAP;
         //渠道来源
         $postData['channelId'] = '1';
         $addre = $this->appointmeantServices->saveOrderFromWapHome($postData);
         
        $data['appointmentId'] = $addre['data'];
        $data['status'] = 30;
        $data['explain'] = '备注';
        $data['interpret'] = '对特殊状态进行详细说明';
        $data['ask'] = '状态详情';
        $data['logType'] = 1;
        $data['userId'] = 1;
        $re = $this->appointmeantServices->setStatus($data);
        $this->assertEquals(0,$re['code']);
     }
     /**
      * 后台添加预约记录
      */
     public function test_addAppointment(){
        //姓名
        $postData['appointmentName'] = '单元测试33';
        //性别
        $postData['sex'] = '1';
        //年龄
        $postData['age'] = '18';
        //电话
        $postData['tel'] = '13750363584';
        //医院id
        $postData['hospitalId'] = '193';
        //供应商id
        $postData['suppliersId'] = '42';
        //微信
        $postData['wechat'] = 'weiche';
        //描述
        $postData['intro'] = '测试成功';
        $postData['isValidity'] = 1;
        //来源 ：线上
        $postData['source'] = AppointmentServices::SOURCE_LINE;
        $re = $this->appointmeantServices->addAppointment($postData);
        $this->assertEquals(0,$re['code']);
     }
     /**
      * 编辑预约记录
      */
     public function test_editAppointment(){
     $hospitalAppointment = new HospitalAppointment();
        $hospitalAppointment->appointmentName = 'sfda';
        $hospitalAppointment->sex             = 1;
        $hospitalAppointment->age             = 12;
        $hospitalAppointment->tel             = '13750363581';
        $hospitalAppointment->isRead = 1;
        $hospitalAppointment->isValidity= 1;
        $hospitalAppointment->customerServiceStatus = 200;
        $hospitalAppointment->intro           = 'dfg';
        if ($hospitalAppointment->save() === false) {
            print_r($this->appointmeantServices->catchModelMessage($hospitalAppointment));
        }
         //编辑
         $postData['appointmentId'] = $hospitalAppointment->appointmentId;
         //姓名
         $postData['appointmentName'] = '编辑单元测试33';
         $re = $this->appointmeantServices->editAppointment($postData);
         $this->assertEquals(0,$re['code']);
     }
     /**
      * 供应闪预约数量
      */
     public function test_getSuppliersAppointmentNum(){   
         //姓名
         $postData['appointmentName'] = '单元测试3';
         //性别
         $postData['sex'] = '';
         //年龄
         $postData['age'] = '';
         //电话
         $postData['tel'] = '13750363584';
         //微信
         $postData['wechat'] = '';
         //意向项目
         $postData['project'] = '赴日本医疗';
         //描述
         $postData['intro'] = '测试成功';
         //来源 ：线上
         $postData['source'] = HospitalAppointment::SOURCE_ONLINE_WAP;
         //渠道来源
         $postData['channelId'] = '1';
         $addre = $this->appointmeantServices->saveOrderFromWapHome($postData);
         $params['startTime'] = '2015-05-01';
         $params['endTime'] = '2016-09-01';
         $params['isDel'] = 1;
         $re = $this->appointmeantServices->getSuppliersAppointmentNum($params);
         $this->assertTrue($re>0);
     }
     
}
