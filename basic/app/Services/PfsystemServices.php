<?php
namespace Joy\Basic\Services;

use Joy\Basic\Models\Platform;
use Joy\Basic\Models\Pfsystem;
/**
 * 系统管理服务类
 * 2016-9-9
 * @author fengyc
 */
class PfsystemServices extends BaseServices
{
    /**
     * 获取系统列表
     * @param array $params
     * @param int $page
     * @param unintknown $pageSize
     * @param string $order
     * @param string $columns
     * @return multitype:number multitype: unknown
     */
    public function getList($params, $page, $pageSize, $order = 'orderNo DESC' ,$columns = '*')
    {
        $re = ["total"=>0,"rows"=>[]];
        $conditions = 'delFlag= :delFlag: ';
        $parameters['delFlag'] = Pfsystem::NOT_DELETE;
        
        if (is_array($params) && !empty($params)) {
        
            foreach ($params as $key => $row) {
        
                if (isset($params[$key]) && !empty($params[$key])) {
                    //模糊查询
                    if ($key == 'name') {
                        $conditions .= " AND $key like :{$key}:";
                        $parameters[$key] = '%' . $params[$key] . '%';
                    } else {
                        $conditions .= " AND $key = :{$key}:";
                        $parameters[$key] = $params[$key];
                    }
                }
            }
        }
        $count = $this->getCount($conditions, $parameters);
        $total = ceil($count / $pageSize);
        $offset = ($page - 1) * $pageSize;
        $list = Pfsystem::find([
            $conditions,
            'bind'      => $parameters,
            'columns'   => $columns,
            'order'     => $order,
            'limit'     => [$pageSize, $offset]
        ]);
        if ($list != false) {
            $list = $list->toArray();
            $re["total"] = $count;
            $re["rows"]  = $list;
        }
        // 结果集
        return $re;
    }
    /**
     * 总数
     *
     * @access  public
     * @param   string  $conditions 条件
     * @param   string  $parameters 条件绑定的参数值
     *
     * @return  int     条数
     */
    public function getCount($conditions, $parameters) {
    
        return Pfsystem::count([$conditions, "bind" => $parameters]);
    }
    /**
     * 检查姓名唯一性
     * @param string $name
     * @param string $systemId
     */
    public function checkName($name,$systemId){
        $conditions = 'delFlag = :delFlag: AND name =:name:';
        $parameters['delFlag'] = Pfsystem::NOT_DELETE;
        $parameters['name']    = $name;
        if($systemId){
            $conditions .= ' AND systemId!=:systemId:';
            $parameters['systemId']    = $systemId;
        }
        $re = Pfsystem::count(["conditions" => $conditions,
                               "bind"       => $parameters]);
        if($re>0){
            return $this->returnResult(100,'系统名称存在');
        }else{
            return $this->returnResult(0,'系统名称不存在');
        }
    }
    /**
     * 保存数据
     * @author fengyc 2016-9-9
     * @access public
     * @param  array $pfsystemDate  
     * @return bool
     */
    public function saveDate($pfsystemDate){
        $pfsystemObj=false;
        //编辑
        if(isset($pfsystemDate['systemId']) && $pfsystemDate['systemId']) {
            $pfsystemObj  = Pfsystem::findFirstBySystemId($pfsystemDate['systemId']);
        }
        //新增
        if($pfsystemObj===false){
            $pfsystemObj  = New Pfsystem();
            $pfsystemObj->systemId = $this->unid();
        }
        //赋值
        foreach($pfsystemDate as $key=>$value){
            //系统id不需要更新
            if($key!='systemId'){
                $pfsystemObj->$key = $value;
            }
        }
        if (false == $pfsystemObj->save ()) {
            return $this->returnResult(500,$pfsystemObj->getMessages()[0]->getMessage ());
        }else{
            return $this->returnResult(0,'成功');
        }
    }
    /**
     * 获取平台-系统树形结构if($re['rows']){
            foreach($re['rows'] as $v){
                $v['text'] = $v['name'];
                $array[] = $v;
            }
        }
     */
    public function getPfsystemTree(){
        $re = [];
        $platform = Platform::find(["conditions" => " delFlag =:delFlag:",
            "bind"       => ['delFlag'=>Platform::NOT_DELETE],
            'columns'   => "platformId,name,name AS text,note,orderNo",
            'order'     => "orderNo DESC",
        ]);
        if($platform!=false){
            $platform = $platform->toArray();
            foreach($platform as $v){
                $v['children'] = [];
                $pfsystem = Pfsystem::find(["conditions" => " platformId=:platformId: AND delFlag =:delFlag:",
                    "bind"       => ['platformId'=>$v['platformId'],'delFlag'=>Platform::NOT_DELETE],
                    'columns'   => "systemId,platformId,name,name AS text,url,note,orderNo",
                    'order'     => "orderNo DESC",
                ]);
                if($pfsystem!=false){
                    $v['children'] = $pfsystem->toArray();
                }
                $re[] = $v;
            }
        }
        return $re;
    }
}
