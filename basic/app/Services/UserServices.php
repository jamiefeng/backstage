<?php
namespace Joy\Basic\Services;

use Joy\Basic\Models\User;
use Joy\Basic\Models\Role;
use Joy\Basic\Models\UserRole;
/**
 * 用户服务类
 * 2016-9-9
 * @author fengyc
 */
class UserServices extends BaseServices
{
    /**
     * 判断用户登录
     * @param User $userModel
     * @return Json
     */
    public function doLogin($userModel){
        //获取用户信息
        $conditions = 'delFlag= :delFlag: AND username = :username:';
        $parameters['delFlag']  = User::NOT_DELETE;
        $parameters['username'] = $userModel->username;
        $userInfo = User::findFirst([$conditions,
            'bind'      => $parameters,]);
        if ($userInfo != false){
            $userInfo = $userInfo->toArray();
        }
        if(empty($userInfo)){
            return $this->returnResult(100,'用户名错误');
        }
        if($userInfo['status']==User::DISABLE){
            return $this->returnResult(100,'用户已被禁用');
        }
        if(md5(md5($this->globalSetting['passwordPrefix'].$userModel->password)) != $userInfo['password']){
            return $this->returnResult(200,'密码错误');
        }
        return $this->returnResult(0,'成功',$userInfo);
    }
    /**
     * 获取用户列表
     * @param array $params
     * @param int $page
     * @param unintknown $pageSize
     * @param string $order
     * @param string $columns
     * @return multitype:number multitype: unknown
     */
    public function getList($params, $page, $pageSize, $order = 'id DESC' ,$columns = '*')
    {
        $departmentServices = new DepartmentServices();
        $re = ["total"=>0,"rows"=>[]];
        $conditions = 'delFlag= :delFlag: ';
        $parameters['delFlag'] = User::NOT_DELETE;
        if (is_array($params) && !empty($params)) {
    
            foreach ($params as $key => $row) {
    
                if (isset($params[$key]) && !empty($params[$key])) {
                    //模糊查询
                    if (in_array($key, ['realName','username','mobile','email'])) {
                        $conditions .= " AND $key like :{$key}:";
                        $parameters[$key] = '%' . $params[$key] . '%';
                    }elseif ($key=='departmentId') {//部门查询，需要查询所有子部门
                        $departmentIds = "'".$params[$key]."'";
                        $arrayTemp = [];
                        $departmentServices->getSonDepartment($params[$key],$arrayTemp);
                        foreach($arrayTemp as $v){
                            $departmentIds .=",'".$v['departmentId']."'";
                        }
                        $conditions .= " AND departmentId IN ($departmentIds)";
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
        $list = User::find([
            $conditions,
            'bind'      => $parameters,
            'columns'   => $columns,
            'order'     => $order,
            'limit'     => [$pageSize, $offset]
        ]);
        if ($list != false) {
            $list = $list->toArray();
            
            foreach($list as &$v){ 
                //查询用户角色
                $roles = $this->getUserRole($v['userId']);
                $rolesStr = '';
                foreach($roles as $k=>$rv){
                    if($k==0){
                        $rolesStr = $rv['name'];
                    }else{
                        $rolesStr .= ','.$rv['name'];
                    }
                }
                $v['roles'] = $rolesStr;
                //查询部门
                $re = $departmentServices->getOneByDepartmentId($v['departmentId']);
                $v['deptName'] = isset($re['name'])?$re['name']:'';
            }
            $re["total"] = $count;
            $re["rows"]  = $list;
        }
        // 结果集
        return $re;
    }
    /**
     * 获取用户角色
     * @param string $userId
     */
    public function getUserRole($userId){
        //筛选条件
        $conditions = UserRole::MP.".userId = :userId: AND t2.delFlag = :delFlag:";
        $parameters['userId'] = $userId;
        $parameters['delFlag'] = Role::NOT_DELETE;
        //开始查询
        $dbquery = UserRole::query();
        $dbquery->columns(['t2.roleId','t2.name']);
        $dbquery->where($conditions);
        $dbquery->leftJoin(Role::MP, UserRole::MP.".roleId = t2.roleId", 't2');
        $dbquery->bind($parameters);
        $re = $dbquery->execute();
        
        if($re===false){
            return [];
        }
        $list = $re->toArray();
        
        return $list;
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
    
        return User::count([$conditions, "bind" => $parameters]);
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
        if(isset($date['userId']) && $date['userId']) {
            $obj  = User::findFirstByUserId($date['userId']);
        }
        //新增
        if($obj===false){
            $obj  = New User();
            $obj->userId = $this->unid();
            //默认用户名为密码
            $obj->password = md5(md5($this->globalSetting['passwordPrefix'].$date['username']));
        }
        //赋值
        foreach($date as $key=>$value){
            //id不需要更新
            if($key!='userId'){
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
     * 检查姓名唯一性
     * @param string $username
     * @param string $userId
     */
    public function checkName($username,$userId){
        $conditions = 'delFlag = :delFlag: AND username =:username:';
        $parameters['delFlag'] = Role::NOT_DELETE;
        $parameters['username']    = $username;
        if($userId){
            $conditions .= ' AND userId!=:userId:';
            $parameters['userId']    = $userId;
        }
        $re = User::count(["conditions" => $conditions,
            "bind"       => $parameters]);
        if($re>0){
            return $this->returnResult(100,'用户名存在');
        }else{
            return $this->returnResult(0,'用户名不存在');
        }
    }
    /**
     * 更新用户角色
     * @param string $userId
     * @param array $roleIds
     */
    public function updateUserRole($userId,$roleIds){
        if($userId){
            $msg = '';
            $re = $this->delRealByUserId($userId);
            if($re['code']==0 && !empty($roleIds)){
                foreach ($roleIds as $v){
                    $userRole = new UserRole();
                    $userRole->userId = $userId;
                    $userRole->roleId = $v;
                    if (false == $userRole->save ()) {
                        $re['code'] = 100;
                        $re['msg'] = $this->returnResult(500,$obj->getMessages()[0]->getMessage ());
                    }
                }
            }
            return $this->returnResult($re['code'],$re['msg']);
        }
        return $this->returnResult(100,'参数错误');
    }
    /**
     * 物理删除记录
     * @param array $userId
     */
    public function delRealByUserId($userId){
        if (!empty($userId)) {
            $data = 0;
            $obj = UserRole::findByUserId($userId);
            if ($obj != false) {
               foreach ($obj as $v) {
                    $ret = $v->delete();
                    if ($ret) $data++;
                }
            }
            return $this->returnResult(0,'删除成功');
        } else {
            return $this->returnResult(100,'id为空');
        }
    }
}
