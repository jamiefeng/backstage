<?php
use Joy\Overseasbasic\Services\Hospital\HospitalServices;
use Joy\Overseasbasic\Models\Hospital;

/**
 * 医院单元测试
 * 2016-4-20
 * 
 * @author fengyanchao
 *        
 */
class HospitalTest extends \Codeception\TestCase\Test
{

    private $hospitalServices;

    protected function setUp()
    {
        $this->hospitalServices = new HospitalServices();
    }

    protected function tearDown()
    {}

    /**
     * 获取医院基本信息
     */
    public function test_findFirst()
    {
        $re = $this->hospitalServices->findFirst(7);
        $this->assertTrue(is_array($re));
    }

    /**
     * 获取医院列表
     */
    public function test_getHospitalList()
    {
        $page = 1;
        $pageSize = 6;
        $re = $this->hospitalServices->getHospitalList([
            'status' => Hospital::RELEASE
        ], $page, $pageSize);
        $this->assertTrue(is_array($re['data']));
    }
    // 搜索条件为空
    public function test_getHospitalList_null()
    {
        $page = 1;
        $pageSize = 6;
        $re = $this->hospitalServices->getHospitalList([
            'hospitalName' => ''
        ], $page, $pageSize);
        $this->assertTrue(is_array($re['data']));
    }
    // 覆盖搜索条件
    public function test_getHospitalList_params()
    {
        $page = 1;
        $pageSize = 6;
        $params = [
            'hospitalName' => 'test',
            'hospitalId' => [
                7,
                2
            ],
            'startTime' => '2015-5-1',
            'endTime' => '2016-5-1',
            'country' => '1',
            'areaIds' => [
                1,
                2
            ]
        ];
        $re = $this->hospitalServices->getHospitalList($params, $page, $pageSize);
        $this->assertTrue(is_array($re['data']));
    }

    /**
     * 获取擅长领域的医院id
     */
    public function test_getHosiptalIdsBySpecialty()
    {
        $re = $this->hospitalServices->getHosiptalIdsBySpecialty(7);
        $this->assertTrue(is_array($re));
    }

    /**
     * 合作医院 该供应商下的医院
     */
    public function test_getHospitalBySuppliersId()
    {
        $re = $this->hospitalServices->getHospitalBySuppliersId(3);
        $this->assertTrue(count($re) > 1);
    }

    /**
     * 选择供应商，绑定供应商
     */
    public function test_createSuppliers()
    {
        $re = $this->hospitalServices->createSuppliers(195, '49,50');
        $this->assertEquals(0, $re['code']);
    }

    /**
     * 医院管理后台-完成度
     */
    public function test_completerate()
    {
        $re = $this->hospitalServices->completerate(194);
        $this->assertTrue(is_numeric($re));
    }

    /**
     * 根据医院ID获取医院信息
     */
    public function test_getHospitalInfo()
    {
        $re = $this->hospitalServices->getHospitalInfo(192);
        $this->assertEquals(192, $re['hospitalId']);
    }

    /**
     * 根据医院ID获取医院详细信息
     */
    public function test_getHospitalById()
    {
        $re = $this->hospitalServices->getHospitalById(192);
        $this->assertEquals(192, $re['hospitalId']);
    }

    /**
     * 编辑或者新增医院
     */
    // 编辑医院
    public function test_saveHospital()
    {
        // 医院数据
        $data['hospitalId'] = 196;
        $data['userId'] = 1;
        // 医院名称
        $data['hospitalName'] = '单元测试，修改医院';
        // 外文医院名称
        $data['foreignName'] = '单元测试，修改医院';
        // 医院概况
        $data['hospitalIntro'] = '单元测试，修改医院';
        // 地区ID
        $data['areaId'] = 2;
        // 医院性质
        $data['nature'] = 1;
        // 招揽外国患者医疗机构许可证
        $data['attractMedical'] = 0;
        // 医院创建时间
        $data['createTime'] = '2016-5';
        
        // 医院口号
        $subject = [
            '环境好',
            '单元测试'
        ];
        $data['subject'] = $subject ? implode("|", $subject) : "";
        
        // logo图片ID
        $data['attachmentId'] = 10;
        $data['oldAattachmentId'] = 0;
        /*
         * 医院，擅长关联表数据
         */
        $data['specialtyIds'] = [];
        $re = $this->hospitalServices->saveHospital($data);
        $this->assertEquals(0, $re['code']);
    }
    // 新增医院
    public function test_saveHospital_add()
    {
        // 医院数据
        $data['hospitalId'] = '';
        $data['userId'] = 1;
        // 医院名称
        $data['hospitalName'] = '单元测试，新增医院';
        // 外文医院名称
        $data['foreignName'] = '单元测试，新增医院';
        // 医院概况
        $data['hospitalIntro'] = '单元测试，新增医院';
        // 地区ID
        $data['areaId'] = 2;
        // 医院性质
        $data['nature'] = 1;
        // 招揽外国患者医疗机构许可证
        $data['attractMedical'] = 0;
        // 医院创建时间
        $data['createTime'] = '2016-5';
        
        // 医院口号
        $subject = [
            '环境好',
            '单元测试'
        ];
        $data['subject'] = $subject ? implode("|", $subject) : "";
        
        // logo图片ID
        $data['attachmentId'] = 10;
        $data['oldAattachmentId'] = 0;
        /*
         * 医院，擅长关联表数据
         */
        $data['specialtyIds'] = [];
        $re = $this->hospitalServices->saveHospital($data);
        $this->assertEquals(0, $re['code']);
    }

    /**
     * 删除医院
     */
    public function test_deleteHospital()
    {
        $re = $this->hospitalServices->deleteHospital([
            197,
            198
        ]);
        $this->assertEquals(0, $re['code']);
    }

    /**
     * 修改发布状态
     */
    public function test_setStatus()
    {
        $re = $this->hospitalServices->setStatus(196, 2);
        $this->assertEquals(0, $re['code']);
    }

    /**
     * 修改医院排序号
     */
    public function test_setSort()
    {
        $re = $this->hospitalServices->setSort(203, 3);
        $this->assertEquals(0, $re['code']);
    }
    /**
     * 获取排列靠前的医院
     */
    public function test_getTopHospital()
    {
        $re = $this->hospitalServices->getTopHospital(4, 8);
        $this->assertTrue(count($re)>0);
    }
    /**
     * 根据搜索关键字获取供应商或者医院ID集合
     */
    public function test_getHospitalIdOrsuppliersId(){
        $re = $this->hospitalServices->getHospitalIdOrsuppliersId('测试');
        $this->assertTrue(is_array($re));
    }
}
