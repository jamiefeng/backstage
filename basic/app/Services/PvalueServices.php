<?php
namespace Joy\Basic\Services;

use Joy\Basic\Models\Pvalue;
/**
 * 操作权限值服务类
 * 2016-9-9
 * @author fengyc
 */
class PvalueServices extends BaseServices
{
    /**
     * 根据系统id获取整数位最大的操作权限值
     * @param strung $systemId
     * @return array
     */
    public function getMaxPvalueBySystemId($systemId){
        if(empty($systemId)) return [];
        //获取用户信息
        $re = Pvalue::findFirst(["conditions" => "systemId = :systemId:",
            "bind"       => ['systemId'=>$systemId],
            "order" => "position DESC"]);
        if ($re != false){
            $re = $re->toArray();
        }
        return $re;
    }
    /**
     * 根据系统id获取操作权限值
     * @param strung $systemId
     * @return array
     */
    public function getPvalueBySystemId($systemId){
        if(empty($systemId)) return [];
        //获取用户信息
        $re = Pvalue::find(["conditions" => "systemId = :systemId:",
            "bind"       => ['systemId'=>$systemId],
            "order" => "orderNo DESC"]);
        if ($re != false){
            $re = $re->toArray();
        }
        return $re;
    }
    /**
     * 保存数据
     * @author fengyc 2016-9-9
     * @access public
     * @param  array $pvalueDate
     * @return bool
     */
    public function saveDate($pvalueDate){
        $obj=false;
        //编辑
        if(isset($pvalueDate['id']) && $pvalueDate['id']) {
            $obj  = Pvalue::findFirst($pvalueDate['id']);
        }
        //新增
        if($obj===false){
            $obj  = New Pvalue();
        }
        //赋值
        foreach($pvalueDate as $key=>$value){
            //系统id不需要更新
            if($key!='id'){
                $obj->$key = $value;
            }
        }
        if (false == $obj->save ()) {
            return $this->returnResult(500,$obj->getMessages()[0]->getMessage ());
        }else{
            return $this->returnResult(0,'成功');
        }
    }
    /**
     * 检查标识唯一性
     * @param int $id
     * @param string $sign
     * @param string $systemId
     */
    public function checkSign($id,$sign,$systemId){
        $conditions = ' sign = :sign: AND systemId =:systemId:';
        $parameters['sign'] = $sign;
        $parameters['systemId'] = $systemId;
        if($id){
            $conditions .= ' AND id != :id:';
            $parameters['id']   = $id;
        }
        $re = Pvalue::count(["conditions" => $conditions,
            "bind"       => $parameters]);
        if($re>0){
            return $this->returnResult(100,'标识存在');
        }else{
            return $this->returnResult(0,'标识不存在');
        }
    }
    /**
     * 物理删除记录
     * @param array $ids
     */
    public function delRealById($ids){
        if (!empty($ids) && is_array($ids)) {
            $data = [];
            foreach ($ids as $id) {
                $obj = Pvalue::findFirst($id);
                if ($obj != false) {
                    $ret = $obj->delete();
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
