<?php
namespace Joy\Basic\Services;

use Joy\Basic\Models\Module;
use Joy\Basic\Models\Acl;
use Joy\Basic\Services\PvalueServices;
/**
 * 系统模块服务类
 * 2016-9-9
 * @author fengyc
 */
class ModuleServices extends BaseServices
{
    /**
     * 获取单条记录
     * @param String $moduleId
     * @return array
     */
    public function getOneByModuleId($moduleId){
        $re = Module::findFirstByModuleId($moduleId);
        if ($re != false){
            $re = $re->toArray();
        }
        return $re;
    }
    /**
     * 获取系统模块
     * @param String $systemId
     * @return array
     */
    public function getModuleBySystemId($systemId){
        if(empty($systemId)) return [];
        $re = Module::find(["conditions" => "systemId = :systemId: AND delFlag = :delFlag:",
            "bind"       => ['systemId'=>$systemId,'delFlag'=>Module::NOT_DELETE],
            "order" => "orderNo DESC"]);
        if ($re != false){
            $re = $re->toArray();
        }
        return $re;
    }
    /**
     * 获取菜单树数据结构
     * @param array $module
     * @param string $url
     * @param string $sessionId
     * @param string $systemId
     * @param array $aclState 用户菜单权限
     * @return json
     */
    public function getModuleTree($systemId,$url,$sessionId,$aclState){
        //模型列表
        $module = $this->getModuleBySystemId($systemId);
        $re = [];
        foreach($module as $v){
            $tmp = [];
            //主菜单
            if(empty($v['pid'])){
                $tmp['id']      =$v['moduleId'];
                $tmp['text']    =$v['name'];
                $tmp['isLeaf']  =false;
                $tmp['expanded']=true;
            }else{
                //子菜单，应用权限
                if(key_exists($v['moduleId'], $aclState)){
                    $tmp['id']  =$v['moduleId'];
                    $tmp['text']=$v['name'];
                    $tmp['pid'] =$v['pid'];
                    $tmp['url'] =$url.$v['url'].'?sessionId='.$sessionId.'&systemId='.$systemId.'&moduleId='.$v['moduleId'].'&moduleKey='.$this->getModuleKey($systemId,$v['moduleId'],$aclState[$v['moduleId']]);
                }
            }
            if(!empty($tmp)){
                $re[] = $tmp;
            }
        }
        return $re;
    }
    /**
     * 加密key
     * @param unknown $moduleId
     * @param unknown $aclState
     * @return string
     */
    public function getModuleKey($systemId,$moduleId,$aclState){
        return md5($systemId.$moduleId.$aclState);
    }
    /**
     * 获取模块列表树
     * @param array $moduleList
     * @return array
     */
    public function getModuleListTree($systemId){
        //模型列表
        $moduleList = $this->getModuleBySystemId($systemId);
        
        //获取系统操作值
        $aclServices = new AclServices();
        $pvalueServices = new PvalueServices();
        $pvalue = $pvalueServices->getPvalueBySystemId($systemId);

        foreach($moduleList as &$v){
            //赋值easyui父id
            $v['_parentId']=$v['pid'];
            $v['pvs'] = [];
            //处理操作权限
            foreach($pvalue as $pv){
                $isok = $aclServices->getPermission($v['state'],$pv['position']);
                if($isok){
                    $v['pvs'][] = $pv;
                }
            }
        }
        return $moduleList;
    }
    /**
     * 保存数据
     * @author fengyc 2016-9-9
     * @access public
     * @param  array $moduleDate
     * @return bool
     */
    public function saveDate($moduleDate){
        $obj=false;
        //编辑
        if(isset($moduleDate['moduleId']) && $moduleDate['moduleId']) {
            $obj  = Module::findFirstByModuleId($moduleDate['moduleId']);
        }
        //新增
        if($obj===false){
            $obj  = New Module();
            $obj->moduleId = $this->unid();
        }
        //赋值
        foreach($moduleDate as $key=>$value){
            //系统id不需要更新
            if($key!='moduleId'){
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
     * 删除操作权限
     * @param string $moduleId
     * @param int $position
     * @return Ambigous <multitype:, multitype:integer string array >
     */
    public function deletePriVal($moduleId,$position){
        $module = $this->getOneByModuleId($moduleId);
        if($module){
            $aclServices = new AclServices();
            //更新
            $obj = Module::findFirstByModuleId($moduleId);
            $obj->state = $aclServices->setPermission($module['state'],$position, false);//删除权限
            if (false == $obj->save ()) {
                return $this->returnResult(500,$obj->getMessages()[0]->getMessage ());
            }else{
                return $this->returnResult(0,'成功');
            }
        }else{
            return $this->returnResult(0,'记录不存在');
        }
    }
    /**
     * 检查是否拥有模块权限
     * @param string $moduleId
     * @param array $aclState
     */
    public function checkModuleRight($systemId,$moduleId,$aclState){
        if(!array_key_exists($moduleId,$aclState)){
            return $this->returnResult(100,'没有模块权限');
        }
        //获取模块中的操作权限
        $aclServices = new AclServices();
        $pvalueServices = new PvalueServices();
        $pvalue = $pvalueServices->getPvalueBySystemId($systemId);
        $right = [];
        //处理操作权限
        foreach($pvalue as $pv){
            $isok = $aclServices->getPermission($aclState[$moduleId],$pv['position']);
            if($isok){
                $right[] = $pv;
            }
        }
        return $this->returnResult(0,'成功',$right); 
    }
}
