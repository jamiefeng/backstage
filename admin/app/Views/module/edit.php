<?php $this->partial("layouts/headmodule")?>
<center>
	<form id="<?php echo isset($formId)?$formId:'';?>">
		<input name="moduleId" type="hidden" />
		<input name="pid" type="hidden" value="<?php echo isset($pid)?$pid:'';?>"/>
		<input name="systemId" type="hidden" value="<?php echo isset($systemId)?$systemId:'';?>"/>
        <div style="margin:20px auto;">
            <table class="form-tb">
             	<tr>
                    <th><em class="cred">*</em>模块名称：</th>
                    <td>
                    	<input style="width:200px;" name="name" type="text" class="ipt easyui-validatebox" data-options="required:true,missingMessage:'请输入模块名称'" />
                    </td>
                </tr>
                <tr>
                    <th>url：</th>
                    <td>    
                    	<input style="width:200px;" name="url" type="text" class="ipt easyui-validatebox" data-options="missingMessage:'请输入url'" />
                    </td>
                </tr>
                <tr>
                    <th>排序号：</th>
                    <td>  
                    	<input style="width:200px;" name="orderNo" type="text" class="ipt easyui-numberbox" data-options="min:0"></input>
                    </td>
                </tr>
            </table>
        </div>
    </form>
    </center>
<?php $this->partial("layouts/footmodule")?>