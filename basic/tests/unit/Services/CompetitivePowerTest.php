<?php
use Joy\Overseasbasic\Services\CompetitivePowerServices;
use Joy\Overseasbasic\Services\Hospital\HospitalServices;
/**
 * 竞争力单元测试
 * 2016-5-5
 * @author fengyanchao
 *
 */
class CompetitivePowerTest extends \Codeception\TestCase\Test
{
    private $competitivePowerServices;

    protected function setUp()
    {
        $this->competitivePowerServices = new CompetitivePowerServices();
        $this->hospitalServices = new HospitalServices();
    }

    protected function tearDown()
    {

    }
    /**
     * 获取医院信息，项目名称 和 项目介绍，发展历程 年-月-历史沿革
     */
    public function test_getHospitalDepartmentCourse(){
        $re = $this->competitivePowerServices->getHospitalDepartmentCourse(194);
        $this->assertEquals('194',$re['hospitalId']);
    }
    public function test_getCompetitivePowerList(){
        $re = $this->competitivePowerServices->getCompetitivePowerList(194,1,2);
        $this->assertTrue(is_array($re));
    }
    /**
     * 添加/更新医院竞争力
     */
    //编辑
    public function test_saveCompetitivePower(){
        $data = [];
        $data['id']=1342;
        $data['userId']=1;
        $data['hospitalId']=194;
        //医院历史
        $data['hospitalHistory']=25;
        //已手术人数
        $data['surgicalNum']=5000;
        //恢复床位数量
        $data['restoreBedsNum']=500;
        //手术室数量
        $data['operatingRoomNum']=100;
        //医院竞争力
        $data['competitivenes']='132|133|147|149|156';
        //科目名称和科目介绍
        $data['accountName']=['test1','test2','test3'];
        $data['accountInfo']=['testinfo1','testinfo2','testinfo3'];
        //发展历程 年-月-历史沿革
        $data['year']=['2015','2016','2015','2016','2015','2016'];
        $data['month']=['02','03','02','03','02','03'];
        $data['historyInfo']=['发展很好','竞争力强','发展很好','竞争力强','发展很好','竞争力强'];
        $re = $this->competitivePowerServices->saveCompetitivePower($data);
        $this->assertEquals('0',$re['code']);
    }
    //编辑 - 异常
    public function test_saveCompetitivePower_no(){
        $data = [];
        $data['id']=1342;
        $data['userId']=1;
        $data['hospitalId']='';
        //医院历史
        $data['hospitalHistory']=25;
        //已手术人数
        $data['surgicalNum']=5000;
        //恢复床位数量
        $data['restoreBedsNum']=500;
        //手术室数量
        $data['operatingRoomNum']=100;
        //医院竞争力
        $data['competitivenes']='132|133|147|149|156';
        //科目名称和科目介绍
        $data['accountName']=['test1','test2','test3'];
        $data['accountInfo']=['testinfo1','testinfo2','testinfo3'];
        //发展历程 年-月-历史沿革
        $data['year']=['2015','2016','2015','2016','2015','2016'];
        $data['month']=['02','03','02','03','02','03'];
        $data['historyInfo']=['发展很好','竞争力强','发展很好','竞争力强','发展很好','竞争力强'];
        $re = $this->competitivePowerServices->saveCompetitivePower($data);
        $this->assertEquals('500',$re['code']);
    }
    //新增
    public function test_saveCompetitivePower_add(){
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
        $data['hospitalId']=$re['data'];//医院id
        //医院历史
        $data['hospitalHistory']=25;
        //已手术人数
        $data['surgicalNum']=5000;
        //恢复床位数量
        $data['restoreBedsNum']=500;
        //手术室数量
        $data['operatingRoomNum']=100;
        //医院竞争力
        $data['competitivenes']='132|133|147|149|156';
        //科目名称和科目介绍
        $data['accountName']=['test1','test2','test3'];
        $data['accountInfo']=['testinfo1','testinfo2','testinfo3'];
        //发展历程 年-月-历史沿革
        $data['year']=['2015','2016','2015','2016','2015','2016'];
        $data['month']=['02','03','02','03','02','03'];
        $data['historyInfo']=['发展很好','竞争力强','发展很好','竞争力强','发展很好','竞争力强'];
        $re = $this->competitivePowerServices->saveCompetitivePower($data);
        $this->assertEquals('0',$re['code']);
    }
    /**
     * 添加竞争力
     */
    public function test_addCompetitivePowerItem(){
        $data = [];
        $data['hospitalId']=196;
        //自定义竞争力名称
        $data['competitivePowerName']='여권&비자 신청 |办理签证护照';
        //竞争力类型
        $data['competitivePowerType'] = 1;
        //竞争力排序
        $data['competitivePowerSort'] = 1;
        $re = $this->competitivePowerServices->addCompetitivePowerItem($data);
        $this->assertEquals('0',$re['code']);
    }
    //异常
    public function test_addCompetitivePowerItem_no(){
        $data = [];
        $data['hospitalId']=194;
        //自定义竞争力名称
        $data['competitivePowerName']='';
        //竞争力类型
        $data['competitivePowerType'] = 1;
        //竞争力排序
        $data['competitivePowerSort'] = 1;
        $re = $this->competitivePowerServices->addCompetitivePowerItem($data);
        $this->assertEquals('500',$re['code']);
    }
    /**
     * 删除竞争力
     */
    public function test_deleteCompetitivePowerItem(){
        $data = [];
        $data['hospitalId']=196;
        //自定义竞争力名称
        $data['competitivePowerName']='여권&비자 신청 |办理签证护照';
        //竞争力类型
        $data['competitivePowerType'] = 1;
        //竞争力排序
        $data['competitivePowerSort'] = 1;
        $res = $this->competitivePowerServices->addCompetitivePowerItem($data);
        $re = $this->competitivePowerServices->deleteCompetitivePowerItem($res['data']);
        $this->assertEquals('0',$re['code']);
    }
    //失败
    public function test_deleteCompetitivePowerItem_no(){
        $re = $this->competitivePowerServices->deleteCompetitivePowerItem(131);
        $this->assertEquals('1',$re['code']);
    }
}
