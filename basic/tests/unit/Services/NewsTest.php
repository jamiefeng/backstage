<?php
use Joy\Overseasbasic\Services\News\NewsServices;
use Joy\Overseasbasic\Services\News\NewsCategoryServices;

/**
 * 资讯单元测试
 * 2016-4-20
 * @author fengyanchao
 *
 */
class NewsTest extends \Codeception\TestCase\Test
{
    private $newsServices;
    private $newsCategoryServices;
    protected function setUp()
    {
        $this->newsServices = new NewsServices();
        $this->newsCategoryServices = new NewsCategoryServices();
    }

    protected function tearDown()
    {

    }
    /**
     * 获取前几条资讯内容
     */
    public function test_getTopNews(){
        $re = $this->newsServices->getTopNews(1,8,'sortOrder DESC');
        $this->assertEquals('1',$re[0]['categoryId']);
    }
    
    /**
     * 根据资讯id获取激活的资讯内容
     */
    //正常获得资讯
    public function test_getNewsByNewsId(){
        $re = $this->newsServices->getNewsByNewsId(21,4);
        $this->assertEquals('0',$re['code']);
    }
    //还没发布资讯
    public function test_getNewsByNewsId1(){
        $re = $this->newsServices->getNewsByNewsId(19,4);
        $this->assertEquals('1',$re['code']);
    }
    //不存在的资讯
    public function test_getNewsByNewsId2(){
        $re = $this->newsServices->getNewsByNewsId(175555,4);
        $this->assertEquals('1',$re['code']);
    }
    /**
     * 根据资讯id获取上一篇资讯
     */
    public function test_getLastOneNews(){
        $re = $this->newsServices->getLastOneNews(21,1);
        $this->assertEquals('1',$re['categoryId']);
    }
    /**
     * 根据资讯id获取下一篇资讯
     */
    public function test_getNextOneNews(){
        $re = $this->newsServices->getNextOneNews(21,1);
        $this->assertEquals('1',$re['categoryId']);
    }
    /**
     * 获取资讯列表
     */
    public function test_getNews() {
        $re = $this->newsServices->getNews(['title'=>'测试','startTime'=>'2016-1-1','endTime'=>'2016-5-1','status'=>2], 1, 3);
        $this->assertEquals('3',count($re));
    }
    /**
     * 根据id获取资讯
     */
    public function test_getNewsById(){
        $re = $this->newsServices->getNewsById(22);
        $this->assertEquals('22',$re['newsId']);
    }
    /**
     * 最大排序值
     */
    public function test_getMaxSortOrder() {
        $re = $this->newsServices->getMaxSortOrder();
    }
    /**
     * 添加或者更新资讯
     */
    //正常添加资讯
    public function test_newsSave(){
        $data = [];
        $data['newsId'] = 10;
        $data['userId'] = 1;
        $data['categoryId'] =1;
        $data['sortOrder']  =1;
        $data['status'] = 2;
        $data['title'] = '单元测试';
        $data['subhead']= '添加';
        $data['keywords']= '单元测试';
        $data['url'] = 'http://www.91160.com';
        $data['attachmentId'] = 130; 
        $data['oldAattachmentId'] = 1;
        $re = $this->newsServices->newsSave($data);
        $this->assertEquals('0',$re['code']);
    }
    //输入空值
    public function test_newsSave1(){
        $data = [];
        $re = $this->newsServices->newsSave($data);
        $this->assertEquals('1',$re['code']);
    }
    //异常插入资讯
    public function test_newsSave2(){
        $data = [];
        $data['newsId'] = '';
        $data['userId'] = 1;
        $data['categoryId'] =1;
        $data['sortOrder']  =1;
        $data['status'] = 2;
        $data['title'] = '';
        $data['subhead']= '添加';
        $data['keywords']= '太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长
            太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长
            太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长
            太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长
            太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长
            太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长
            太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长太长';
        $data['url'] = "http://www.91160.com";
        $data['attachmentId'] = 0; 
        $data['oldAattachmentId'] = 1;
        $re = $this->newsServices->newsSave($data);
        $this->assertEquals('500',$re['code']);
    }
    /**
     * 更新资讯排序号
     */
    //正常更新排序
    public function test_setSort(){
            $re = $this->newsServices->setSort(21,1);
            $this->assertEquals('0',$re['code']);
    }
    //资讯不存在
    public function test_setSort1(){
        $re = $this->newsServices->setSort(21000,1);
        $this->assertEquals('1',$re['code']);
    }
    //异常更新排序
    public function test_setSort2(){
        $re = $this->newsServices->setSort('','1');
        $this->assertEquals('参数错误',$re['msg']);
    }
    /**
     * 逻辑删除资讯
     */
    //正常删除
    public function test_delNews(){
        $re = $this->newsServices->delNews(['10']);
        $this->assertEquals('0',$re['code']);
    }
    //不存在的资讯
    public function test_delNews1(){
        $re = $this->newsServices->delNews(['3100']);
        $this->assertEquals('1',$re['code']);
    }
    //输入空值
    public function test_delNews2(){
        $re = $this->newsServices->delNews([]);
        $this->assertEquals('1',$re['code']);
    }
    /**
     * 权威医院排行
     */
    public function test_getOrderList() {
        $newsCategory = [
            ['categoryId' => 3, 'name' => '美国权威医院'],
            ['categoryId' => 4, 'name' => '日本权威医院'],
            ['categoryId' => 5, 'name' => '韩国权威医院'],
            ['categoryId' => 6, 'name' => '泰国权威医院']
        ];
        $newsServices = new NewsServices();
        $re = $newsServices->getNewsOrderList($newsCategory);
        $this->assertEquals('4',count($re));
    }
    /**
     * 补充资讯描述和图片
     * @access array
     */
    public function test_fillPicContent(){
        $list['data'][] = [
            'newsId'=>1,
            'attachmentId'=>1
        ]; 
        $re = $this->newsServices->fillPicContent($list,4);
        $this->assertEquals('1',$re['data'][0]['newsId']);
    }
    /**
     * 获取资讯分类
     */
    public function test_getNewsCategoryList(){
        $re = $this->newsCategoryServices->getNewsCategoryList();
        $this->assertEquals('海外医讯',$re[1]);
    }
    /**
     * 获取资讯分类列表
     */
    public function test_getNewsCategory() {
        $re = $this->newsCategoryServices->getNewsCategory(3);
        $this->assertEquals('3',count($re));
    }
}
