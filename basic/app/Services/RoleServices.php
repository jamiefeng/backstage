<?php
namespace Joy\Basic\Services;

use Joy\Basic\Models\Role;
/**
 * 角色服务类
 * 2016-9-9
 * @author fengyc
 */
class RoleServices extends BaseServices
{
    /**
     * 获取角色列表
     * @param array $params
     * @param int $page
     * @param unintknown $pageSize
     * @param string $order
     * @param string $columns
     * @return multitype:number multitype: unknown
     */
    public function getList($params, $page, $pageSize, $order = 'id DESC' ,$columns = '*')
    {
        $re = ["total"=>0,"rows"=>[]];
        $conditions = 'delFlag= :delFlag: ';
        $parameters['delFlag'] = Role::NOT_DELETE;
    
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
        $list = Role::find([
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
    
        return Role::count([$conditions, "bind" => $parameters]);
    }
    /**
     * 保存数据
     * @author fengyc 2016-9-9
     * @access public
     * @param  array $roleDate
     * @return bool
     */
    public function saveDate($roleDate){
        $roleObj=false;
        //编辑
        if(isset($roleDate['roleId']) && $roleDate['roleId']) {
            $roleObj  = Role::findFirstByRoleId($roleDate['roleId']);
        }
        //新增
        if($roleObj===false){
            $roleObj  = New Role();
            $roleObj->roleId = $this->unid();
        }
        //赋值
        foreach($roleDate as $key=>$value){
            //系统id不需要更新
            if($key!='roleId'){
                $roleObj->$key = $value;
            }
        }
        if (false == $roleObj->save ()) {
            return $this->returnResult(500,$roleObj->getMessages()[0]->getMessage ());
        }else{
            return $this->returnResult(0,'成功');
        }
    }
    /**
     * 检查姓名唯一性
     * @param string $name
     * @param string $roleId
     */
    public function checkName($name,$roleId){
        $conditions = 'delFlag = :delFlag: AND name =:name:';
        $parameters['delFlag'] = Role::NOT_DELETE;
        $parameters['name']    = $name;
        if($roleId){
            $conditions .= ' AND roleId!=:roleId:';
            $parameters['roleId']    = $roleId;
        }
        $re = Role::count(["conditions" => $conditions,
            "bind"       => $parameters]);
        if($re>0){
            return $this->returnResult(100,'名称存在');
        }else{
            return $this->returnResult(0,'名称不存在');
        }
    }
}
