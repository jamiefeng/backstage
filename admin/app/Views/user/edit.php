<?php $this->partial("layouts/headmodule")?>
<center>
	<form id="<?php echo isset($formId)?$formId:'';?>">
		<input type='hidden' name="userId" />
        <div style="margin:20px auto;">
            <table class="form-tb">
            	<tr>
                    <th><em class="cred">*</em>用户名：</th>
                    <td>    
                        <input name="username" type="text" class="ipt easyui-validatebox" data-options="required:true,validType:'unique'" />
                    </td>
                    <th><em class="cred">*</em>性别：</th>
                    <td>    
                        <input name="sex" type="radio" value="1" checked/>男&nbsp;&nbsp;
                        <input name="sex" type="radio" value="2" />女
                    </td>
                </tr>
                <tr>
                    <th><em class="cred">*</em>真实姓名：</th>
                    <td>    
                        <input name="realName" type="text" class="ipt easyui-validatebox" data-options="required:true" />
                    </td>
                    <th>手机：</th>
                    <td>    
                        <input name="mobile" type="text" class="ipt easyui-numberbox" />
                    </td>
                </tr>
                <tr>
                    <th><em class="cred">*</em>邮箱：</th>
                    <td>    
                        <input name="email" class="ipt easyui-validatebox" data-options="required:true,validType:'email'" />
                    </td>
                    <th>传真：</th>
                    <td>    
                        <input name="fax" type="text" class="ipt easyui-textbox" />
                    </td>
                </tr>
                <tr>
                    <th>座机：</th>
                    <td>    
                        <input name="tel" type="text" class="ipt easyui-textbox" />
                    </td>
                    <th><em class="cred">*</em>所属部门：</th>
                    <td>    
                        <select style="width:160px;" name="departmentId" class="ipt easyui-combotree" data-options="url:'<?php echo $this->url->get(['for' => 'user_getDepartmentTree'])?>',required:true,loadFilter:userPageTreeLoadFilter,readonly:true,panelHeight:200"></select>
                    </td>
                </tr>
                <tr>
                    <th>地址：</th>
                    <td>  
                    	<textarea name="address" rows="2" cols="30"></textarea>
                    </td>
                </tr>
            </table>
        </div>      
    </form>
    </center>
<?php $this->partial("layouts/footmodule")?>