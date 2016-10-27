<?php
use Joy\Overseasbasic\Services\Hospital\HospitalExpertServices;
use Joy\Overseasbasic\Models\HospitalExpert;

/**
 * 医生单元测试
 * 2016-5-3
 * @author fengyanchao
 *
 */
class HospitalExpertTest extends \Codeception\TestCase\Test
{
    private $hospitalExpertServices;

    protected function setUp()
    {
        $this->hospitalExpertServices = new HospitalExpertServices();
    }

    protected function tearDown()
    {

    }
    /**
     * 获取排前列的医生
     */
    public function test_getTopDoc(){
        $re = $this->hospitalExpertServices->getTopDoc(4,10);
        $this->assertTrue(count($re)>0);
    }
    /**
     * 获取专家列表
     */
    public function test_getHospitalExpertList(){
        $page = 1;
        $pageSize = 6;
        $re = $this->hospitalExpertServices->getHospitalExpertList(['isShow'=>HospitalExpert::SHOW], $page, $pageSize,'*','sortOrder DESC , expertId DESC');
        $this->assertTrue(is_array($re['data']));
    }
    /**
     * 获取列表
     */
    public function test_getList(){
        $page = 1;
        $pageSize = 6;
        $re = $this->hospitalExpertServices->getList(['expertName'=>'测试','specialtyId'=>1,'hospitalId'=>2], $page, $pageSize,'*','sortOrder DESC , expertId DESC');
        $this->assertTrue(is_array($re['data']));
    }
    public function test_getList_2(){
        $page = 1;
        $pageSize = 6;
        $re = $this->hospitalExpertServices->getList(['expertName'=>'测试','specialtyId'=>[1,2],'hospitalId'=>2], $page, $pageSize,'*','sortOrder DESC , expertId DESC');
        $this->assertTrue(is_array($re['data']));
    }
    /**
     * 根据医院id获取专家团队
     */
    public function test_getHospitalExpert(){
        $re = $this->hospitalExpertServices->getHospitalExpert(7);
        $this->assertTrue(is_array($re));
    }
    /**
     * 根据专家id获取专家详细信息
     */
    public function test_getHospitalExpertById(){
        $re = $this->hospitalExpertServices->getHospitalExpertById(176);
        $this->assertEquals('0',$re['code']);
    }
    //专家还没发布
    public function test_getHospitalExpertById_noshow(){
        $re = $this->hospitalExpertServices->getHospitalExpertById(195);
        $this->assertEquals('1',$re['code']);
    }
    //异常-空值
    public function test_getHospitalExpertById_no1(){
        $re = $this->hospitalExpertServices->getHospitalExpertById('');
        $this->assertEquals('1',$re['code']);
    }
    //异常-不存在
    public function test_getHospitalExpertById_no2(){
        $re = $this->hospitalExpertServices->getHospitalExpertById(1111111);
        $this->assertEquals('1',$re['code']);
    }
    /**
     * 根据专家id获取专家信息
     */
    public function test_getInfoById(){
        $re = $this->hospitalExpertServices->getInfoById(175);
        $this->assertTrue(is_array($re));
    }
    /**
     * 新增/编辑专家信息
     */
    //编辑
    public function test_saveExpert(){
        $data = [];
        $data['expertId']=175;
        $data['userId']=1;
        $data['hospitalId']=7;
        $data['specialtyId']=1;//擅长领域
        $data['expertName']='专家名字';
        $data['areaId'] = 2;
        $data['honor']='职称';
        $data['position']='职位';
        $data['specialty']='专长';
        $data['expertOtherName']='医生外文名字';
        //执业时间
        $data['practiceTime']='2015|05';
        //本院执业时间
        $data['inOurPracticeTime']='2015|06';
        //医师介绍
        $data['intro']='医师介绍|医师介绍|医师介绍|医师介绍|医师介绍';
        $data['attachmentId']='';
        $data['oldAattachmentId']='1';
        $re = $this->hospitalExpertServices->saveExpert($data);
        $this->assertEquals('0',$re['code']);
    }
    //异常 - 编辑
    public function test_saveExpert_not(){
        $data = [];
        $data['expertId']=175;
        $data['userId']=1;
        $data['hospitalId']=7;
        $data['specialtyId']=1;//擅长领域
        $data['expertName']='专家名字';
        $data['areaId'] = '';
        $data['honor']='职称';
        $data['position']='职位';
        $data['specialty']='专长';
        $data['expertOtherName']='医生外文名字';
        //执业时间
        $data['practiceTime']='2015|05';
        //本院执业时间
        $data['inOurPracticeTime']='2015|06';
        //医师介绍
        $data['intro']='医师介绍|医师介绍|医师介绍|医师介绍|医师介绍';
        $data['attachmentId']='';
        $data['oldAattachmentId']='1';
        $re = $this->hospitalExpertServices->saveExpert($data);
        $this->assertTrue($re['code']!=0);
    }
    //新增
    public function test_saveExpert_add(){
        $data = [];
        $data['expertId']='';
        $data['userId']=1;
        $data['hospitalId']=7;
        $data['specialtyId']=1;//擅长领域
        $data['expertName']='新增';
        $data['areaId'] = 2;
        $data['honor']='职称';
        $data['position']='职位';
        $data['specialty']='专长';
        $data['expertOtherName']='医生外文名字';
        //执业时间
        $data['practiceTime']='2015|05';
        //本院执业时间
        $data['inOurPracticeTime']='2015|06';
        //医师介绍
        $data['intro']='医师介绍|医师介绍|医师介绍|医师介绍|医师介绍';
        $data['attachmentId']=1;
        $data['oldAattachmentId']='';
        $re = $this->hospitalExpertServices->saveExpert($data);
        $this->assertEquals('0',$re['code']);
    }
    /**
     * 删除医疗团队
     */
    public function test_deleteTeam(){
        $re = $this->hospitalExpertServices->deleteTeam(190);
        $this->assertTrue(isset($re['code']));
    }
    //异常
    public function test_deleteTeam_no(){
        $re = $this->hospitalExpertServices->deleteTeam(0);
        $this->assertEquals(1,$re['code']);
    }
    /**
     * 删除多个医疗团队
     */
    public function test_deleteExperts(){
        $re = $this->hospitalExpertServices->deleteExperts([190,191]);
        $this->assertTrue(isset($re['code']));
    }
    //异常
    public function test_deleteExperts_no(){
        $re = $this->hospitalExpertServices->deleteExperts([]);
        $this->assertEquals(1,$re['code']);
    }
    /**
     * 修改医院排序号
     */
    public function test_setSort()
    {
        $re = $this->hospitalExpertServices->setSort(180, 1);
        $this->assertEquals(0, $re['code']);
    }
    //异常-空
    public function test_setSort_no()
    {
        $re = $this->hospitalExpertServices->setSort('', '');
        $this->assertEquals(0, $re['code']);
    }
    /**
     * 设置专家的显示状态
     */
    public function test_setShow(){
        $re = $this->hospitalExpertServices->setShow(195,1);
        $this->assertEquals(0,$re['code']);
    }
    //异常 - 空
    public function test_setShow_null(){
        $re = $this->hospitalExpertServices->setShow('','');
        $this->assertEquals(1,$re['code']);
    }
    //异常 - 状态值异常
    public function test_setShow_no(){
        $re = $this->hospitalExpertServices->setShow(195,100);
        $this->assertEquals(2,$re['code']);
    }
    //异常 - 记录不存在
    public function test_setShow_no2(){
        $re = $this->hospitalExpertServices->setShow(1950000,1);
        $this->assertEquals(3,$re['code']);
    }
    /**
     * 根据专家id获取医院列表
     */
    public function test_getHospitalListByExpertId(){
        $re = $this->hospitalExpertServices->getHospitalListByExpertId(189);
        $this->assertTrue(count($re)>0);
    }
    /**
     * 绑定医院
     */
    public function test_createHospital(){
        $re = $this->hospitalExpertServices->createHospital(210,2);
        $this->assertEquals(0,$re['code']);
    }
    //异常
    public function test_createHospital_no(){
        $re = $this->hospitalExpertServices->createHospital('','');
        $this->assertEquals(1,$re['code']);
    }
}
