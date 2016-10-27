<?php $this->partial("layouts/headmodule")?>
<center>
	<form id="<?php echo isset($formId)?$formId:'';?>">
		<input name="roleId" type="hidden" />
        <div style="margin:20px auto;">
            <table class="form-tb">
               	<tr>
                    <th><em class="cred">*</em>角色名称：</th>
                    <td>    
                        <input name="name" type="text" class="ipt easyui-validatebox" data-options="required:true,missingMessage:'请输入角色名称',validType:'unique'" />
                    </td>
                </tr>
                <tr>
                    <th>备注：</th>
                    <td>  
                    	<textarea name="note" rows="2" cols="30"></textarea>
                    </td>
                </tr>
            </table>
        </div>
    </form>
</center>
<?php $this->partial("layouts/footmodule")?>