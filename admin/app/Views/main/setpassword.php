<?php $this->partial("layouts/headmodule")?>
<center>
	<form id="formA">
        <div style="margin:20px auto;">
            <table class="form-tb">
            	<tr>
                    <th><em class="cred">*</em>原密码：</th>
                    <td>    
                        <input name="oldpwd" type="password" class="ipt easyui-validatebox" data-options="required:true,missingMessage:'请输入原密码'" />
                    </td>
                </tr>
            	<tr>
                    <th><em class="cred">*</em>新密码：</th>
                    <td>    
                        <input id="newpwd" name="newpwd" type="password" class="ipt easyui-validatebox" data-options="required:true,missingMessage:'请输入新密码'" />
                    </td>
                </tr>
            	<tr>
                    <th><em class="cred">*</em>重复新密码：</th>
                    <td>    
                        <input name="renewpwd" type="password" class="ipt easyui-validatebox" data-options="required:true,validType:'checkpwd',missingMessage:'请再次输入新密码'" />
                    </td>
                </tr>
            </table>
        </div>
    </form>
    </center>
	<script type="text/javascript">
	//验证标识的唯一性
	$.extend($.fn.validatebox.defaults.rules, {
		checkpwd: {
	    validator: function(value, param){
	    	var valid=true;
	    	var newpwd=$("#newpwd").val();
	    	if(newpwd!=value){
	    		valid=false;
	    	}
	    	return valid;
	    },
	    message: '两次输入密码不一致!'
	    }
	});
	</script>
<?php $this->partial("layouts/footmodule")?>