<?php
namespace Joy\Basic\Services;

use Joy\Basic\Models\UserRole;
use Joy\Basic\Models\Platform;
use Joy\Basic\Models\Acl;
/**
 * 平台服务类
 * 2016-9-9
 * @author fengyc
 */
class PlatformServices extends BaseServices
{
    /**
     * 获取单条记录
     * @param String $moduleId
     * @return array
     */
    public function getOneByPlatformId($platformId){
        $re = Platform::findFirstByPlatformId($platformId);
        if ($re != false){
            $re = $re->toArray();
        }
        return $re;
    }
    /**
     * 获取用户拥有的平台权限
     * @param string $userId
     */
    public function getUserPlatform($userId){
        
        $roleAcl = [];
        $userAcl = [];
        //查询用户角色
        $roleList = UserRole::findByUserId($userId);
        if ($roleList != false){
            $roleList = $roleList->toArray();
            foreach($roleList as $role){
                //通过角色获取权限
                if(!empty($role['roleId'])){
                    $acl = Acl::find(["conditions" => " releaseSn = 'role' AND releaseId =:releaseId:",
                        "bind"       => ['releaseId'=>$role['roleId']],
                        "group"      => "platformId"
                    ]);
                    if ($acl != false){
                        $roleAcl = array_merge($roleAcl,$acl->toArray());//多个角色权限累加
                    }
                }
            }
        }
        //获取用户特殊权限
        $acl = Acl::find(["conditions" => " releaseSn = 'user' AND releaseId =:releaseId:",
            "bind"       => ['releaseId'=>$userId],
            "group"      => "platformId"
        ]);
        if ($acl != false){
            $userAcl = $acl->toArray();
        }
        $userPlatform = array_merge($roleAcl,$userAcl);
        
        
        if ($userPlatform){
            $pfids = '';
            foreach($userPlatform as $k=>$v){
                if($k==0){
                    $pfids = "'".$v['platformId']."'";
                }else{
                    $pfids .= ",'".$v['platformId']."'";
                }
            }
            if($pfids){
                $platform = Platform::find("platformId IN ({$pfids})");
                if ($platform != false){
                    $list = $platform->toArray();
                }
            }
        }
        return $list;
    }
    /**
     * 获取平台列表
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
        $parameters['delFlag'] = Platform::NOT_DELETE;
    
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
        $list = Platform::find([
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
    
        return Platform::count([$conditions, "bind" => $parameters]);
    }
    /**
     * 检查姓名唯一性
     * @param string $name
     * @param string $systemId
     */
    public function checkName($name,$platformId){
        $conditions = 'delFlag = :delFlag: AND name =:name:';
        $parameters['delFlag'] = Platform::NOT_DELETE;
        $parameters['name']    = $name;
        if($platformId){
            $conditions .= ' AND platformId!=:platformId:';
            $parameters['platformId']    = $platformId;
        }
        $re = Platform::count(["conditions" => $conditions,
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
     * @param  array $platformDate
     * @return bool
     */
    public function saveDate($platformDate){
        $platformObj=false;
        //编辑
        if(isset($platformDate['platformId']) && $platformDate['platformId']) {
            $platformObj  = Platform::findFirstByPlatformId($platformDate['platformId']);
        }
        //新增
        if($platformObj===false){
            $platformObj  = New Platform();
            $platformObj->platformId = $this->unid();
        }
        //赋值
        foreach($platformDate as $key=>$value){
            //id不需要更新
            if($key!='platformId'){
                $platformObj->$key = $value;
            }
        }
        if (false == $platformObj->save ()) {
            return $this->returnResult(500,$platformObj->getMessages()[0]->getMessage ());
        }else{
            return $this->returnResult(0,'成功');
        }
    }
}
