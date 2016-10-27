<?php
namespace Joy\Basic\Services;
/**
 * 服务通用基类
 */
class BaseServices
{
    /**
     * 通用公共设置
     *
     * @var array
     */
    public $globalSetting;
    
    /**
     * 初始化服务
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        // 通用配置
        $this->globalSetting = \Joy::$config ['globalSetting'];
    }
    
    /**
     * 返回结果
     *
     * @param integer $statuCode
     * @param string $message
     * @param array $data
     * @return array
     */
    public function returnResult($statuCode=0, $messages = '', $data=array())
    {
        $re = ["code" => $statuCode,"msg"  => $messages];
        if($data){
            $re['data'] = $data;
        }
        return $re;
    }
    /**
     * 返回一个错误
     *
     * @param integer $statuCode
     * @param string $message
     * @param array $messages
     * @return Response
     */
    public function sendError($statuCode, $message, $messages = [])
    {
        return [
            "code" => $statuCode,
            "msg" => $message.'['.implode(";", $messages).']',
            "msgstr"=>implode(";", $messages),
            "msgJson"=>json_encode($messages)
        ];
    }
    
    /**
     * 捕获数据模型的错误
     * @param \Phalcon\Mvc\ModelInterface $model
     * @return Response
     */
    public function catchModelMessage($model)
    {
        $messages = [];
        foreach ($model->getMessages() as $message) {
            $messages [] = $message->getMessage();
        }
        return $this->sendError(500, 'Data saving error', $messages);
    }
    
    /**
	 * 产生唯一编号 当天的年月日小时分钟+4位随机数
	 * @return string
	 */
	public function unid(){
	    return md5(uniqid(mt_rand()).microtime());
	}
	
	/**
	 * 删除记录
	 * @param array $ids
	 */
	public function delById($models,$ids){
	    if (!empty($ids) && is_array($ids)) {
	        $data = [];
	        foreach ($ids as $id) {
	            $obj = $models::findFirst($id);
	            if ($obj != false) {
	                $ret = $obj->save(['delFlag' => $models::DELETE]);
	                if ($ret) $data[] = $id;
	            }
	        }
	        if (empty($data)) return $this->returnResult(400,'删除失败');
	        return $this->returnResult(0,'删除成功');
	    } else {
	        return $this->returnResult(100,'id为空');
	    }
	}
}