<?php $this->partial("layouts/headmodule")?>
<center>
	<form id="<?php echo isset($formId)?$formId:'';?>">
		<input name="platformId" type="hidden" />
        <div style="margin:20px auto;">
            <table class="form-tb">
             	<tr>
                    <th><em class="cred">*</em>名称：</th>
                    <td>    
                        <input id="name" name="name" type="text" class="ipt easyui-validatebox" data-options="required:true,missingMessage:'请输入系统名称',validType:'unique'" />
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
                    	<textarea name="note" rows="2" cols="30"></textarea>
                    </td>
                </tr>
            </table>
        </div>
    </form>
    </center>

<?php $this->partial("layouts/footmodule")?>