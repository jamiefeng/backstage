<?php
use Joy\Overseasbasic\Services\Hospital\HospitalInformationServices;
use Joy\Overseasbasic\Services\Hospital\HospitalServices;
/**
 * 医院详细信息单元测试
 * 2016-5-4
 * @author fengyanchao
 *
 */
class HospitalInformationTest extends \Codeception\TestCase\Test
{
    private $hospitalInformationServices;
    private $hospitalServices;
    protected function setUp()
    {
        $this->hospitalInformationServices = new HospitalInformationServices();
        $this->hospitalServices = new HospitalServices();
    }

    protected function tearDown()
    {

    }
    /**
     * 根据医院ID获取医院详细信息
     */
    public function test_getHospitalInformation(){
        $re = $this->hospitalInformationServices->getHospitalInformation(7);
        $this->assertTrue(is_array($re['hospital']));
    }
    /**
     * 获取医院联系方式
     */
    public function test_getContact(){
        $re = $this->hospitalInformationServices->getContact(175);
        $this->assertTrue(is_array($re));
    }
    /**
     * 增加/编辑医院联系方式
     */
    //编辑
    public function test_saveHospitalInformation(){
        $data = [];
        $data['id']=42;
        $data['userId']=1;
        //营业时间
        $data['ferialStart']='9:00';
        $data['ferialEnd']='18:00';
        //周六营业时间
        $data['satStart']='9:00';
        $data['satEnd']='18:00';
        //周日营业时间
        $data['sunStart']='9:00';
        $data['sunEnd']='18:00';
        //电话
        $data['tel']='1375656451';
        //网址
        $data['website']='http://www.91160.com';
        //地址
        $data['address']='韩国首尔市瑞草区瑞草洞XX大厦15-17层 ';
        //中国事业部联系人联系方式
        $data['officeQq']='704551241';
        $data['officeWeixin']='weixin';
        $data['officeEmail']='704544512@qq.com';
        $data['officeWeibo']='weibo';
        $data['hospitalId']='7';
        $re = $this->hospitalInformationServices->saveHospitalInformation($data);
        $this->assertEquals('0',$re['code']);
    }
    //编辑 - 异常
    public function test_saveHospitalInformation_no(){
        $data = [];
        $data['id']=42;
        $data['userId']=1;
        //营业时间
        $data['ferialStart']='9:00';
        $data['ferialEnd']='18:00';
        //周六营业时间
        $data['satStart']='9:00';
        $data['satEnd']='18:00';
        //周日营业时间
        $data['sunStart']='9:00';
        $data['sunEnd']='18:00';
        //电话
        $data['tel']='1375656451';
        //网址
        $data['website']='http://www.91160.com';
        //地址
        $data['address']='韩国首尔市瑞草区瑞草洞XX大厦15-17层 ';
        //中国事业部联系人联系方式
        $data['officeQq']='704551241';
        $data['officeWeixin']='weixin';
        $data['officeEmail']='704544512@qq.com';
        $data['officeWeibo']='weibo';
        $data['hospitalId']='';//不能为空
        $re = $this->hospitalInformationServices->saveHospitalInformation($data);
        $this->assertEquals('500',$re['code']);
    }
    //新增
    public function test_saveHospitalInformation_add(){
        //医院数据
        $data['hospitalId'] = '';
        $data['userId'] = 1;
        //医院名称
        $data['hospitalName'] = '单元测试，新增医院';
        //外文医院名称
        $data['foreignName'] = '单元测试，新增医院';
        //医院概况
        $data['hospitalIntro'] = '单元测试，新增医院';
        //地区ID
        $data['areaId'] = 2;
        //医院性质
        $data['nature'] = 1;
        //招揽外国患者医疗机构许可证
        $data['attractMedical'] = 0;
        //医院创建时间
        $data['createTime'] = '2016-5';
        
        //医院口号
        $subject = ['环境好','单元测试'];
        $data['subject'] = $subject?implode("|", $subject):"";
        
        //logo图片ID
        $data['attachmentId'] = 10;
        $data['oldAattachmentId'] = 0;
        /*
         * 医院，擅长关联表数据
         */
        $data['specialtyIds'] = [];
        $re = $this->hospitalServices->saveHospital($data);
        
        $data = [];
        $data['id']='';
        $data['userId']=1;
        //营业时间
        $data['ferialStart']='9:00';
        $data['ferialEnd']='18:00';
        //周六营业时间
        $data['satStart']='9:00';
        $data['satEnd']='18:00';
        //周日营业时间
        $data['sunStart']='9:00';
        $data['sunEnd']='18:00';
        //电话
        $data['tel']='1375656451';
        //网址
        $data['website']='http://www.91160.com';
        //地址
        $data['address']='韩国首尔市瑞草区瑞草洞XX大厦15-17层 ';
        //中国事业部联系人联系方式
        $data['officeQq']='704551241';
        $data['officeWeixin']='weixin';
        $data['officeEmail']='704544512@qq.com';
        $data['officeWeibo']='weibo';
        $data['hospitalId']=$re['data'];//新增医院
        $re = $this->hospitalInformationServices->saveHospitalInformation($data);
        $this->assertTrue(is_array($re));
    }
}
