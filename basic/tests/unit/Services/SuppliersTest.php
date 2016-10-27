<?php
use Joy\Overseasbasic\Services\Suppliers\SuppliersServices;
use Joy\Overseasbasic\Models\Suppliers;
/**
 * 供应商单元测试
 * 2016-5-3
 * @author fengyanchao
 *
 */
class SuppliersTest extends \Codeception\TestCase\Test
{
    private $suppliersServices;

    protected function setUp()
    {
        $this->suppliersServices = new SuppliersServices();
    }

    protected function tearDown()
    {

    }
    /**
     * 获取供应商基本信息
     */
    public function test_findFirst(){
        $re = $this->suppliersServices->findFirst(30);
        $this->assertTrue(count($re)>0);
    }
    /**
     * 合作状态
     */
    public function test_getCooperationList(){
        $re = $this->suppliersServices->getCooperationList();
        $this->assertTrue(count($re)>0);
    }
    /**
     * 根据供应商id获取供应商信息
     */
    public function test_getSuppliersById(){
        $re = $this->suppliersServices->getSuppliersById(3);
        $this->assertTrue(count($re['suppliersInfo'])>0);
    }
    /**
     * 根据医院ID获取供应商列表
     */
    public function test_getSuppliersListByHospitalIds(){
        $data = ['hospitalIds' => [196,192,191]];
        $re = $this->suppliersServices->getSuppliersListByHospitalIds($data);
        $this->assertTrue(is_array($re));
    }
    /**
     * 获取供应商列表
     */
    public function test_getSuppliersList(){
        $page = 1;
        $re = $this->suppliersServices->getSuppliersList(['cooperationStatus' => Suppliers::COOPERATION_NORMAL], $page, 5);
        $this->assertTrue(count($re['data'])>0);
    }
    /**
     * 获取供应商医院数目
     */
    public function test_getSuppliersHOspitalNum(){
        $re = $this->suppliersServices->getSuppliersHOspitalNum(6);
        $this->assertTrue($re>0);
    }
    /**
     * 删除供应商
     */
    public function test_deleteSuppliers(){
        $re = $this->suppliersServices->deleteSuppliers([50]);
        $this->assertEquals('0',$re['code']);
    }
    /**
     * 修改供应商状态
     */
    public function test_setStatus(){
        $re = $this->suppliersServices->setStatus(45,20);
        $this->assertEquals('0',$re['code']);
    }
    /**
     * 修改医院排序号
     */
    public function test_setSort(){
        $re = $this->suppliersServices->setSort(44,3);
        $this->assertEquals(0,$re['code']);
    }
    /**
     * 获取供应商信息
     */
    public function test_getSuppliersEditById(){
        $re = $this->suppliersServices->getSuppliersEditById(45);
        $this->assertEquals(45,$re['suppliersId']);
    }
   /**
    * 添加供应商
    */
    public function test_createSuppliers(){
        $postData = [];
        $postData['serviceType']=2;
        $postData['fullName']='单元测试新增供应商';
        $postData['shortName']='';
        $postData['createTime']='2015-01';
        $postData['country']=3;
        $postData['province']='';
        $postData['city']='';
        $postData['intro']='专业供应商';
        $postData['contact']='0755-8445465456';
        $postData['tel']='13756654654';
        $postData['qq']='711521';
        $postData['weixin']='weixin';
        $postData['website']='http://www.91160.com';
        $postData['email']='5644132@qq.com';
        $postData['address']='详细地址';
        $postData['attachmentId']='1910';
        $postData['addUserid']='1';
        $postData['sortOrder']=0;
        $postData['appointmentBase']=0;
        $postData['serviceIds']=[10,11,12];
       
        $re = $this->suppliersServices->createSuppliers($postData);
        $this->assertTrue($re>0);
    }
    /**
     * 编辑供应商
     */
    public function test_updateSuppliers(){
        $postData = [];
        $postData['serviceType']=2;
        $postData['fullName']='修改单元测试新增供应商';
        $postData['shortName']='';
        $postData['createTime']='2015-01';
        $postData['country']=3;
        $postData['province']='';
        $postData['city']='';
        $postData['intro']='专业供应商';
        $postData['contact']='0755-8445465456';
        $postData['tel']='13756654654';
        $postData['qq']='711521';
        $postData['weixin']='weixin';
        $postData['website']='http://www.91160.com';
        $postData['email']='5644132@qq.com';
        $postData['address']='详细地址';
        $postData['attachmentId']='1910';
        $postData['oldAattachmentId']='1909';
        $postData['updateUserid']='1';
        $postData['sortOrder']=0;
        $postData['appointmentBase']=0;
        $postData['serviceIds']=[10,11,12];
    
        $re = $this->suppliersServices->updateSuppliers(53,$postData);
        $this->assertTrue($re>0);
    }
        /**
         * 根据条件获取供应商信息
         */
        public function test_getSuppliersSelectOption(){
            $re = $this->suppliersServices->getSuppliersSelectOption(['cooperationStatus' => 20]);
            $this->assertTrue(count($re)>0);
        }
        /**
         * 根据供应商ID获取医院
         */
        public function test_gethospitalListBySuppliersIds(){
            $re = $this->suppliersServices->gethospitalListBySuppliersIds([42]);
            $this->assertTrue(count($re[42])>0);
        }
        
}
