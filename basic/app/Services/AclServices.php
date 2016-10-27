<?php
namespace Joy\Basic\Services;

use Joy\Basic\Services\ModuleServices;
use Joy\Basic\Models\Acl;
use Joy\Basic\Models\Pfsystem;
use Joy\Basic\Models\UserRole;
/**
 * 模块授权服务类
 * 2016-9-9
 * @author fengyc
 */
class AclServices extends BaseServices
{
    
    /**
     * 更加用户id获取系统权限
     * @param string $userId
     * @param string $platformId
     * @return Ambigous <multitype:, multitype:integer string array >
     */
    public function getSystemsByUserId($userId,$platformId){
        
        $systemArray = $this->getUserAcl($userId, $platformId);
        $systemIds = '';
        foreach($systemArray as $k=>$v){
            if($k==0){
                $systemIds = "'".$v['systemId']."'";
            }else{
                $systemIds .= ",'".$v['systemId']."'";
            }
        }
        
        $re = [];
        if($systemIds){
            $conditions = "delFlag = :delFlag: AND systemId IN ($systemIds)";
            $parameters['delFlag'] = Pfsystem::NOT_DELETE;
            $reobj = Pfsystem::find(["conditions" => $conditions,"bind" => $parameters,"order" => "orderNo DESC"]);
            if($reobj!=false){
                $re = $reobj->toArray();
            }
        }
        return $re;
    }
    /**
     * 获取用户拥有的系统权限
     * @param string $userId
     * @param string $platformId
     */
    public function getUserAcl($userId,$platformId){
        $roleAcl = [];
        $userAcl = [];
        //查询用户角色
        $roleList = UserRole::findByUserId($userId);
        if ($roleList != false){
            $roleList = $roleList->toArray();
            foreach($roleList as $role){
                //通过角色获取权限
                if(!empty($role['roleId'])){
                    $acl = Acl::find(["conditions" => " platformId = :platformId: AND releaseSn = 'role' AND releaseId =:releaseId:",
                        "bind"       => ['platformId'=>$platformId,'releaseId'=>$role['roleId']],
                        "group"      => "systemId"
                    ]);
                    if ($acl != false){
                        $roleAcl = array_merge($roleAcl,$acl->toArray());//多个角色权限累加
                    }
                }
            }
        }
        //获取用户特殊权限
        $acl = Acl::find(["conditions" => " platformId = :platformId: AND releaseSn = 'user' AND releaseId =:releaseId:",
            "bind"       => ['platformId'=>$platformId,'releaseId'=>$userId],
            "group"      => "systemId"
        ]);
        if ($acl != false){
            $userAcl = $acl->toArray();
        }
        return array_merge($roleAcl,$userAcl);
    }
    /**
     * 获取用户模块权限，只有有一个权限都显示
     * @param String $userId
     * @return array
     */
    public function getModuleAclByUserId($userId){
        $re = [];$roleAcl = [];
        //获取用户的角色
        $roleList = UserRole::findByUserId($userId);
        if ($roleList != false){
            $roleList = $roleList->toArray();
            foreach($roleList as $role){
                //通过角色获取权限
                if(!empty($role['roleId'])){
                    $acl = Acl::find(["conditions" => " releaseSn = 'role' AND releaseId =:releaseId:",
                        "bind"       => ['releaseId'=>$role['roleId']],
                        "group"      => "moduleId"
                    ]);
                    if ($acl != false){
                        $roleAcl = array_merge($roleAcl,$acl->toArray());//多个角色权限累加
                    }
                }
            }
        }
        //获取用户特殊权限
        $userAcl = Acl::find(["conditions" => " releaseSn = 'user' AND releaseId =:releaseId:",
            "bind"       => ['releaseId'=>$userId],
            "group"      => "moduleId"
        ]);
        if ($userAcl != false){
            $userAcl = $userAcl->toArray();
        }
        if(is_array($roleAcl)){
            foreach($roleAcl as $v){
                $re[$v['moduleId']] = $v['aclState'];//decbin($v['aclState']).'';//转字符串
            }
        }
        if(is_array($userAcl)){
            foreach($userAcl as $v){
                $re[$v['moduleId']] = $v['aclState'];
            }
        }
        return $re;
    }
    /**
     * 设置权限
     * @param int permission 位数
     * @param bool yes 获得权限或者删除权限
     */
    public function setPermission($aclState,$permission,$yes){
        $temp = 1;
        $temp = $temp << $permission;
        if($yes){
            $aclState |= $temp;
        }else{
            $aclState &= ~$temp;
        }
        return $aclState;
    }
    /**
     * 得到权限
     * @param int permission 位数
     * @return
     */
    public function getPermission($aclState,$permission){
        $temp = 1;
        $temp = $temp << $permission;
        $temp &= $aclState;
        if($temp != 0){
            return true;
        }
        return false;
    }
    /**
     * 根据标示和id获取权限值
     * @param string $releaseSn
     * @param string $releaseId
     */
    public function getListByRelease($releaseSn,$releaseId){
        $acl = Acl::find(["conditions" => " releaseSn = :releaseSn: AND releaseId =:releaseId:",
            "bind"       => ['releaseSn'=>$releaseSn,'releaseId'=>$releaseId]
        ]);
        if ($acl != false){
            $acl = $acl->toArray();
        }
        return $acl;
    }
    /**
     * 根据系统id添加权限
     * @param string $platformId
     * @param string $systemId
     * @param string $releaseSn
     * @param string $releaseId
     */
    public function addAclBySystemId($platformId,$systemId, $releaseSn, $releaseId){
        $moduleServices = new ModuleServices();
        $module = $moduleServices->getModuleBySystemId($systemId);
        if($module){
            //先删除后添加
            $re = $this->delRealById(['systemId'=>$systemId,'releaseSn'=>$releaseSn,'releaseId'=>$releaseId]);
            if($re['code']==0){ 
                foreach($module as $v){
                    $obj  = New Acl();
                    $obj->releaseSn = $releaseSn;
                    $obj->releaseId = $releaseId;
                    $obj->platformId  = $platformId;
                    $obj->systemId  = $systemId;
                    $obj->moduleId  = $v['moduleId'];
                    $obj->aclState  = $v['state'];
                    $re = $obj->save (); 
                }
                if($re!=false){
                    return $this->returnResult(0,'成功');
                }
            }
        }
        return $this->returnResult(100,'失败');
    }
    /**
     * 根据模块id添加权限
     * @param string $moduleId
     * @param string $releaseSn
     * @param string $releaseId
     */
    public function addAclByModuleId($platformId,$moduleId, $releaseSn, $releaseId){
        $moduleServices = new ModuleServices();
        $module = $moduleServices->getOneByModuleId($moduleId);
        
        if($module){
            //先删除后添加
            $re = $this->delRealById(['systemId'=>$module['systemId'],'releaseSn'=>$releaseSn,'releaseId'=>$releaseId,'moduleId'=>$moduleId]);
            
            if($re['code']==0){
                $obj  = New Acl();
                $obj->releaseSn = $releaseSn;
                $obj->releaseId = $releaseId;
                $obj->platformId  = $platformId;
                $obj->systemId  = $module['systemId'];
                $obj->moduleId  = $moduleId;
                $obj->aclState  = $module['state'];
                $re = $obj->save ();
                if($re!=false){
                    return $this->returnResult(0,'成功');
                }
            }
        }
        return $this->returnResult(100,'失败');
    }
    /**
     * 物理删除记录
     * @param array $ids
     */
    public function delRealById($params){
        $ret = false;
        if (!empty($params) && is_array($params)) {
            $conditions = ' 1';
            $parameters = [];
            if (is_array($params) && !empty($params)) {
                foreach ($params as $key => $row) {
                    if (isset($params[$key]) && !empty($params[$key])) {
                        $conditions .= " AND $key = :{$key}:";
                        $parameters[$key] = $params[$key];
                    }
                }
            }
            if($conditions && $parameters){
                $acl = Acl::find(["conditions" => $conditions,
                    "bind" => $parameters
                ]);
                if ($acl != false){
                    foreach ($acl as $aclobj){
                        $aclobj->delete();
                    }
                    $ret = true;
                }
            }
            
        }
        if($ret){
            return $this->returnResult(0,'删除成功');
        }else {
            return $this->returnResult(100,'删除失败');
        }
    }
    /**
     * 更新权限值
     * @author fengyc 2016-9-9
     * @access public
     * @param  array $params
     * @return bool
     * 'systemId'=>$systemId,'releaseSn'=>$releaseSn,'releaseId'=>$releaseId,'moduleId'=>$moduleId,'aclState'
     */
    public function updateState($params){
        $aclState = 0;
        $acl = Acl::findFirst(["conditions" => " systemId =:systemId: AND releaseSn = :releaseSn: AND releaseId =:releaseId: AND moduleId=:moduleId:",
            "bind"       => ['systemId'=>$params['systemId'],'releaseSn'=>$params['releaseSn'],'releaseId'=>$params['releaseId'],'moduleId'=>$params['moduleId']]
        ]);
        
        if($acl==false){
            //计算后的权限值
            $aclState = $this->setPermission(0, $params['position'], $params['yes']);
            //新增
            $acl = new Acl();
            $acl->platformId = $params['platformId'];
            $acl->systemId   = $params['systemId'];
            $acl->moduleId   = $params['moduleId'];
            $acl->releaseSn  = $params['releaseSn'];
            $acl->releaseId  = $params['releaseId'];  
            
        }else{
            //计算后的权限值
            $aclState = $this->setPermission($acl->aclState, $params['position'], $params['yes']);
        }
        if($acl!=false){
            $acl->aclState   = $aclState;
            if (false == $acl->save ()) {
                return $this->returnResult(500,$acl->getMessages()[0]->getMessage ());
            }else{
                return $this->returnResult(0,'成功'.$params['aclState']);
            }
        }
        return $this->returnResult(400,'失败');
    }
}
