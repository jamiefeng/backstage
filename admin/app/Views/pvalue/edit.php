<?php $this->partial("layouts/headmodule")?>
<center>
	<form id="<?php echo isset($formId)?$formId:'';?>">
        <div style="margin:20px auto;">
        <input name="id" type="hidden" value="<?php echo isset($id)?$id:'';?>"/>
		<input name="systemId" type="hidden" value="<?php echo isset($systemId)?$systemId:'';?>"/>
            <table class="form-tb">
           		<tr>
                    <th><em class="cred">*</em>权限值名称：</th>
                    <td>
                    	<input name="name" type="text" class="ipt easyui-validatebox" data-options="required:true,missingMessage:'请输入权限值名称'" />
                    </td>
                </tr>
                <tr>
                    <th><em class="cred">*</em>标识：</th>
                    <td>
                    	<input name="sign" type="text" class="ipt easyui-validatebox" data-options="required:true,missingMessage:'请输入标识',validType:'unique'" />
                    </td>
                </tr>
                <tr>
                    <th><em class="cred">*</em>权限位：</th>
                    <td>
                    	<input name="position" value="<?php echo (isset($maxPvalue['position'])?($maxPvalue['position']+1):'')?>" class="ipt easyui-numberbox numberbox-f validatebox-text" data-options="required:true,missingMessage:'请输入整型的位'" type="text">
                    </td>
                </tr>
             	<tr>
                    <th>排序号：</th>
                    <td>  
                    	<input name="orderNo" type="text" class="ipt easyui-numberbox" data-options="min:0"></input>
                    </td>
                </tr>
                <tr>
                    <th>描述：</th>
                    <td>  
                    	<textarea name="remark" rows="2" cols="30"></textarea>
                    </td>
                </tr>
            </table>
        </div>
    </form>
    </center>
<?php $this->partial("layouts/footmodule")?>