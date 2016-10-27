<?php $this->partial("layouts/headmodule")?>
<center>
	<form id="<?php echo isset($formId)?$formId:'';?>">
	    <input name="pid" type="hidden" value="<?php echo (isset($pid)?$pid:'')?>" />
		<input name="departmentId" type="hidden" />
        <div style="margin:20px auto;">
            <table class="form-tb">
             	<tr>
                    <th><em class="cred">*</em>名称：</th>
                    <td>    
                        <input name="name" type="text" class="ipt easyui-validatebox" data-options="required:true,missingMessage:'请输入部门名称'" />
                    </td>
                </tr>
                <tr>
                    <th>描述：</th>
                    <td>  
                    	<textarea name="note" rows="2" cols="30"></textarea>
                    </td>
                </tr>
            </table>
        </div>
    </form>
    </center>
<?php $this->partial("layouts/footmodule")?>