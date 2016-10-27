<?php
use Joy\Overseasbasic\Services\Attrs\AttachmentServices;
use Joy\Overseasbasic\Services\Attrs\AttrsValueTextServices;
use Joy\Overseasbasic\Models\Attachment;
use Joy\Overseasbasic\Library\Tools;
/**
 * 附件单元测试
 * 2016-4-20
 * @author fengyanchao
 *
 */
class AttachmentTest extends \Codeception\TestCase\Test
{
    private $attachmentServices;
    private $attrsValueTextServices;
    protected function setUp()
    {
        $this->attachmentServices = new AttachmentServices();
        $this->attrsValueTextServices = new AttrsValueTextServices();
    }

    protected function tearDown()
    {

    }

    /**
     * 获取附件
     */
    public function test_getAttachment(){
        $re = $this->attachmentServices->getAttachment(21);
        $this->assertEquals('21',$re['attachmentId']);
    }
    
   //异常获取附件  
   public function test_getAttachByAttachmentIdst(){
        $re = $this->attachmentServices->getAttachByAttachmentIds([]);
        $this->assertTrue(empty($re));
    }
    /**
     * 更新属性关系
     */
    //正常更新
    public function test_editAttachment(){
        $attachmentId = 21;
        $updataData = ["objectId" => 1];
        $re = $this->attachmentServices->editAttachment($attachmentId,$updataData);
        $this->assertTrue($re);
    }
    //附件不存在
    public function test_editAttachment1(){
        $attachmentId = 210000000000;
        $updataData = ["objectId" => 1];
        $re = $this->attachmentServices->editAttachment($attachmentId,$updataData);
        $this->assertTrue(!$re);
    }
    /**
     * 医院环境图片
     */
    public function test_getHospitalPicByObjectTable(){
        $re = $this->attachmentServices->getHospitalPicByObjectTable(9,Attachment::OBJECT_TABLES_HOSPITAL_PIC);
        $this->assertTrue(!empty($re));
    }
    /**
     * 补充图片
     */
    //正常
    public function test_getAttachmentPic(){
        $getarray = [['attachmentId'=>1545],['attachmentId'=>3],['attachmentId'=>7]];
        $re = $this->attachmentServices->getAttachmentPic($getarray);
        $this->assertTrue(isset($re[0]['pic']));
    }
    //参数异常
    public function test_getAttachmentPic2(){
        $getarray = 1;
        $re = $this->attachmentServices->getAttachmentPic($getarray);
        $this->assertEquals('1',$re);
    }
    /**
     * 获取图片列表
     */
    public function test_getList(){
        $params   = ['attachmentId'=>[1545,1546,1547,4,5,6,7]];
        $columns  = ['*'];
        $re = $this->attachmentServices->getList($params,$columns);
        $this->assertTrue(!empty($re));
    }
    /**
     * 获取多个图片路径
     */
    public function test_getAttachmentByIds(){
        $params   = [1545,1546,1547,4,5,6,7];
        $re = $this->attachmentServices->getAttachmentByIds($params);
        $this->assertTrue(!empty($re));
    }
    /**
     * 获取多个图片路径
     */
    public function test_getImageByType(){
        $re = $this->attachmentServices->getImageByType(224,Attachment::OBJECT_TABLES_HOSPITAL_PIC);
        $this->assertTrue(is_array($re['info']));
    }
    /**
     * 删除附件
     */
    public function test_deleteAttachment(){
        $re = $this->attachmentServices->deleteAttachment([1911,1913]);
        $this->assertEquals('0',$re['code']);
    }
    //异常删除
    public function test_deleteAttachment_no(){
        $re = $this->attachmentServices->deleteAttachment([]);
        $this->assertEquals('1',$re['code']);
    }
    /**
     * 上传附件
     * 
     */
    public function test_insertAttachment(){
        $imgRet = [];
        $imgRet['userId'] = 1;
        $imgRet['fileName'] = '1111111111111';
        $imgRet['imgWidth'] = 16;
        $imgRet['imgHeight'] = 16;
        $imgRet['fileExt'] = 'png';
        $imgRet['fileSize'] = 736;
        $imgRet['filePath'] = 'overseas/2016/05/172306635989.png';
        $imgRet['objectTable'] = 7;
        $imgRet['objectId']  = 224;
        $ip = Tools::getClientIp();
        $re = $this->attachmentServices->insertAttachment($imgRet);
        $this->assertTrue($re>0);
    }
    /**
     * 更新附件信息
     */
    public function test_saveAttachment(){
        $hospitalId = 219;
        $aliasArr = [];
        $aliasArr['1922-资质证书']	= '别名1';
        $aliasArr['1923-医院荣誉']	= '别名2';
	    $aliasArr['1924-学术交流']	= '别名3';
        $re = $this->attachmentServices->saveAttachment($hospitalId,$aliasArr,Attachment::OBJECT_TABLES_HONOR_PIC);
        $this->assertEquals('0',$re['code']);
    }
    //异常
    public function test_saveAttachment_no(){
        $hospitalId = 219;
        $aliasArr = [];
        $re = $this->attachmentServices->saveAttachment($hospitalId,$aliasArr,Attachment::OBJECT_TABLES_HONOR_PIC);
        $this->assertEquals('2',$re['code']);
    }
}
