<?php
namespace Joy\Basic\Services;

use Joy\Basic\Models\Department;
/**
 * 部门服务类
 * 2016-9-9
 * @author fengyc
 */
class DepartmentServices extends BaseServices
{
    /**
     * 获取单条记录
     * @param String $moduleId
     * @return array
     */
    public function getOneByDepartmentId($departmentId){
        $re = Department::findFirstByDepartmentId($departmentId);
        if ($re != false){
            $re = $re->toArray();
        }
        return $re;
    }
    /**
     * 递归获取所有子部门
     * @param string $departmentId
     */
    public function getSonDepartment($departmentId,&$list){
        $re = $this->getSonDepartmentByPid($departmentId);
        if ($re){
            foreach ($re as $v){
                $list[] = $v;
                $this->getSonDepartment($v['departmentId'],$list);
            }
        }else{
            return $list;
        }
    }
    /**
     * 根据父id获取子部门
     * @param string $pid
     * @return array
     */
    public function getSonDepartmentByPid($pid){
        $re = Department::find(["conditions" => "delFlag = :delFlag: AND pid =:pid:",
            "bind"       => ['delFlag'=>Department::NOT_DELETE,'pid'=>$pid]]);
        if ($re != false){
            $list = $re->toArray();
        }
        return $list;
    }
    /**
     * 获取部门列表树
     * @param 
     * @return array
     */
    public function getDepartmentTree(){
        //模型列表
        $re = Department::find(["conditions" => "delFlag = :delFlag:",
            "bind"       => ['delFlag'=>Department::NOT_DELETE]]);
        if ($re == false){
            return [];
        }
        $list = $re->toArray();
        foreach($list as &$v){
            //赋值easyui父id
            $v['_parentId']=$v['pid'];
        }
        return $list;
    }
    /**
     * 获取用户管理部门列表树
     * @param
     * @return array
     */
    public function getUserDepartmentTree(){
        //模型列表
        $re = Department::find(["conditions" => "delFlag = :delFlag:",
            "bind"       => ['delFlag'=>Department::NOT_DELETE]]);
        if ($re == false){
            return [];
        }
        $list = $re->toArray();
        foreach($list as &$v){
            $v['id'] = $v['departmentId'];
            //赋值easyui父id
            if($v['pid']){
                $v['_parentId']=$v['pid'];
            }
        }
        return $list;
    }
    /**
     * 保存数据
     * @author fengyc 2016-9-9
     * @access public
     * @param  array $date
     * @return bool
     */
    public function saveDate($date){
        $obj=false;
        //编辑
        if(isset($date['departmentId']) && $date['departmentId']) {
            $obj  = Department::findFirstByDepartmentId($date['departmentId']);
        }
        //新增
        if($obj===false){
            $obj  = New Department();
            $obj->departmentId = $this->unid();
        }
        //赋值
        foreach($date as $key=>$value){
            //id不需要更新
            if($key!='departmentId'){
                $obj->$key = $value;
            }
        }
        if (false == $obj->save ()) {
            return $this->returnResult(500,$obj->getMessages()[0]->getMessage ());
        }else{
            return $this->returnResult(0,'成功');
        }
    }
}
