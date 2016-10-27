<?php $this->partial("layouts/headmodule")?>
<script>
$(function(){
	//查询方法
	var doSearch = function doSearch(){
		var myForm = $('#searchFm');
		var data = myForm.serializeJson();
		$.each(data,function(index,dom){ //去空格
			if(index && dom){
				data[index]=$.trim(dom);
			}
		});
		$('#dg').datagrid('load', data);
	};
	//查询按钮
	$('#searchBtn').click(function() {
		doSearch();
	});
	//回车事件
	$('#searchFm').on('keydown', function(event){
		if(event.keyCode == 13){
			doSearch();
		}
	});
});
</script>
<div data-options="region:'north',border:false">
	<div id="toolbar" class="easyui-toolbar">
	<a href="javascript:void(0)" iconCls="icon-refresh" plain="true" onclick="common.refresh()">刷新</a>
	<a>-</a>
				<a href="javascript:;" iconCls="icon-add" plain="true" onclick="add()">添加</a>
				<a>-</a>
				<a href="javascript:;" iconCls="icon-edit" plain="true" onclick="edit()">修改</a>
				<a>-</a>
				<a href="javascript:;" iconCls="icon-del" plain="true" onclick="del()">删除</a>
				<a>-</a>
				<a href="javascript:;" iconCls="icon-aduit" plain="true" onclick="roleModuleUI()">操作授权</a>
				<a>-</a>
		</div>
		<div style="padding:10px;">
		<form name="searchFm" id="searchFm" method="post">
                  <table class="search-tb">
                     <tbody>
                        <tr>
                        	<td>角色名称：
                        		<input class="ipt" name="name"/>
                        	</td>
                            <td>
								<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-search" id="searchBtn">查询</a>
                            </td>
						</tr>
					</tbody>
				</table>
		</form>
		</div>
	</div>
	<div data-options="region:'center',border:true">
		<table id="dg">
		</table>
	</div>
</body>
<script type="text/javascript">
	var sizeObject={width:500,height:280};
	var dataList=null;
	function parsePage() {
			dataList=$('#dg').datagrid({
			url: '<?php echo $this->url->get(['for' => 'role_ajaxlist'])?>',
			singleSelect : false,
			fitColumns : true,
			checkOnSelect : true,
			selectOnCheck : true,
			pageSize: 20,
			pageList: [20,50,100],
			columns : [ [ {
				checkbox : true
			}, {
				title : '角色名称',
				field : 'name',
				width : 100,
				align : 'left'
			}, {
				title : '备注',
				field : 'note',
				width : 100,
				align : 'left'
			} ] ],
			onLoadSuccess : function() {
			}
		});
	}
	
	//验证标识的唯一性
	$.extend($.fn.validatebox.defaults.rules, {
	    unique: {
	    validator: function(value, param){
	    	var valid=true;
	    	var formData=$("#formU").form("getData");
	    	$.ajax({
	    		async:false,
	    		url:'<?php echo $this->url->get(['for' => 'role_checkName'])?>',
	    		data:{name : value,roleId : formData.roleId},
	    		dataType: 'json',
	    		success:function(result){
	    			if(result.code!=0){
	    				valid=false;
	    			}
	    		}
	    	});
	    	return valid;
	    },
	    message: '角色名称不能重复!'
	    }
	});
	
	function add() {
		$("body").append('<div id="addUI"></div>');
		$('#addUI').dialog({    
		    title: '新增角色',    
		    width: sizeObject.width,    
		    height: sizeObject.height, 
		    closed: false,    
		    cache: false,    
		    href: '<?php echo $this->url->get(['for' => 'role_edit'],['formId'=>'formA'])?>',    
		    modal: true,
		    buttons : [{
				text:'保存',
				iconCls : 'icon-save',
				handler:function(){
					var form=$("#formA");
					if(form.form('validate')){
						$.messager.progress(); 
						var data=form.form("getData");
						$.post(
							'<?php echo $this->url->get(['for' => 'role_doedit'])?>',
							data,
							function(result){
								$.messager.progress('close');
								if(result.code==0){
									$('#addUI').dialog('close');
									$('#addUI').remove();
									dataList.datagrid("reload");
								}else{
									$.messager.alert('信息','保存失败！','info');
								}
							},'json'
						);
					}
				}
			},{
				text:'关闭',
				iconCls : 'icon-cancel',
				handler:function(){$('#addUI').dialog('close');$('#addUI').remove();}
			}]
		});    
	}
	
	function edit() {
		var data=dataList.datagrid("getSelected");
		if(!data){
			$.messager.alert('信息','请选择一条记录！','info');
			return;
		}
		$("body").append('<div id="editUI"></div>');
		$('#editUI').dialog({    
		    title: '编辑角色',    
		    width: sizeObject.width,    
		    height: sizeObject.height, 
		    closed: false,    
		    cache: false,    
		    href: '<?php echo $this->url->get(['for' => 'role_edit'],['formId'=>'formU'])?>',    
		    modal: true,
		    buttons : [{
				text:'保存',
				iconCls : 'icon-save',
				handler:function(){
					var form=$("#formU");
					if(form.form('validate')){
						$.messager.progress(); 
						var fdata=form.form("getData");
						$.post(
							'<?php echo $this->url->get(['for' => 'role_doedit'])?>',
							fdata,
							function(result){
								$.messager.progress('close');
								if(result.code==0){
									$('#editUI').dialog('close');
									$('#editUI').remove();
									dataList.datagrid("reload");
								}else{
									$.messager.alert('信息','保存失败！','info');
								}
							},'json'
						);
					}
				}
			},{
				text:'关闭',
				iconCls : 'icon-cancel',
				handler:function(){$('#editUI').dialog('close');$('#editUI').remove();}
			}],
			onLoad : function (){
				var form=$("#formU");
				form.form("load",data);
			}
		});    
	}
	
	function del() {
		var rows=dataList.datagrid("getSelections");
		if (rows && rows.length > 0) {
			$.messager.confirm("信息", "确定删除选中记录？", function(r) {
				if (r) {
					$.messager.progress(); 
					var ids = [];
					for ( var i = 0, l = rows.length; i < l; i++) {
						var r = rows[i];
						ids.push(r.id);
					}
					$.ajax({
					       type: 'post',
					       url: '<?php echo $this->url->get(['for' => 'role_del'])?>',
					       dataType: 'json',
					       data: {ids:ids},
					       success: function(result){
					    	   $.messager.progress('close');
								if (result.code == 0) {
									dataList.datagrid("clearSelections");
									dataList.datagrid("reload");
								}else{
									$.messager.alert(result.msg);
								}
					       }
					   });
				}
			});
		} else {
			$.messager.alert('信息','请选择要删除的记录！','info');
		}
	}
	function roleModuleUI() {
		var row=dataList.datagrid("getSelected");
		if(!row){
			$.messager.alert('信息','请选择一条记录！','info');
			return;
		}
		$("body").append('<div id="roleModuleUI"></div>');
		$('#roleModuleUI').dialog({    
		    title: '给 <font color="red">'+row.name+' </font>授权',    
		    width: 800,    
		    height: 450,    
		    closed: false,    
		    cache: false,    
		    href: "<?php echo $this->url->get(['for' => 'role_rolemodule'],['releaseSn'=>'role'])?>&releaseId=" + row.id,
		    modal: true,
		    buttons : [{
				text:'关闭',
				iconCls : 'icon-cancel',
				handler:function(){$('#roleModuleUI').dialog('close');$('#roleModuleUI').remove();}
			}],
			onLoad : function (){
				//系统菜单
				$('#systemMenu').tree({
					url:'<?php echo $this->url->get(['for' => 'module_getSystems'])?>',
					onSelect: function(node){
						if(node.systemId){
    						//取消绑定的click事件
    						$("#selectAllBtn .btn,.mbtn").unbind("click"); 
    						//重新加载treegrid的数据
    						$.post("<?php echo $this->url->get(['for' => 'role_getSystemModulePvalue'])?>",{systemId : node.systemId, releaseSn: 'role', releaseId : row.roleId},function(data){
    							$('#rmtg').treegrid("loadData",data);
    						},'json');
						}
					},
					onLoadSuccess:function(node,data){  
				        $("#systemMenu li:eq(1)").find("div").addClass("tree-node-selected");   //设置第一个节点高亮  
				        var n = $("#systemMenu").tree("getSelected");  
				        if(n!=null){  
				             $("#systemMenu").tree("select",n.target);    //相当于默认点击了一下第一个节点，执行onSelect方法  
				        }
				    }  
				});
				//权限值
				$('#rmtg').treegrid({
					rownumbers : true,
					fit:true,
					fitColumns : true,
					pagination : false,
					idField:'moduleId',    
				    treeField:'name',
					columns : [ [ {
						title : '名称',
						field : 'name',
						width : 60,
						align : 'left'
					}, {
						title : ' 权限值',
						field : 'pvs',
						width : 150,
						align : 'left',
						formatter: function(value,row,index){
							var list=value,
								btnValue='全选',
								htmltext='',
								vflag=true;
							for(var i=0,len=list.length;i<len;i++){
								htmltext+='<label class="function-item">'+
								'<input class="jsrmtg" type="checkbox" name="'+row.moduleId+'" value="'+list[i].position+'"';
								if(list[i].flag==true){
									htmltext+=' checked="checked" ';
								}else{
									vflag=false;
								}
								htmltext+=' />&nbsp;'+list[i].name+'</label>';
								/* if((i+1)%6==0){//6个换一行
									htmltext+='<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
								}  */
							}
							if(vflag){
								btnValue='取消';
							}
							var btnhtml='<input class="mbtn btn btnplan white" id="'+row.moduleId+'" type="button" value="'+btnValue+'" style="cursor:pointer;"/>';
							//自动换行div
							var _autoHuanhangDivStart='<div style="width:370px;white-space: normal;">';
							var _autoHuanhangDivEnd='</div>';
							return _autoHuanhangDivStart+btnhtml+htmltext+_autoHuanhangDivEnd;
						}
					}] ],
					onLoadSuccess : function() {
						var node=$('#systemMenu').tree("getSelected");
						$(":checkbox").click(function(){
							var name=this.name,
								moduleSn=$(this).attr('sn'),
								value=this.value,
								yes=false;
							if(this.checked){
								yes=true;
							}
							$.ajax({
								type:'post',
								url:'<?php echo $this->url->get(['for' => 'role_setacl'])?>',
								data:{releaseId:row.roleId,releaseSn:'role',systemId : node.systemId,moduleId:name,position:value,yes:yes}
							});
						});
						
						$(".mbtn").click(function(){
							var chks=document.getElementsByName(this.id),
								yes=false;
							if(this.value == '全选'){
								this.value='取消',yes=true;
								for(var i=0,len=chks.length;i<len;i++){
									chks[i].checked = true;
								}
							}else{
								this.value='全选';
								for(var i=0,len=chks.length;i<len;i++){
									chks[i].checked = false;
								}
							}
							$.ajax({
								type:'post',
								url:'<?php echo $this->url->get(['for' => 'role_setacl'])?>',
								data:{releaseId:row.roleId,releaseSn:'role',platformId : node.platformId,systemId : node.systemId,moduleId:this.id,yes:yes}
							});
						});
						
						$("#selectAllBtn .btn").click(function(){
							var yes=true;
							if(this.value == '全选'){
								//全选
								$(".jsrmtg:checkbox").attr("checked","true");
								$(".mbtn").val("取消");
							}else{
								//全不选
								yes=false;
								$(".jsrmtg:checkbox").removeAttr("checked");
								$(".mbtn").val("全选");
							}
							$.ajax({
								type:'post',
								url:'<?php echo $this->url->get(['for' => 'role_setacl'])?>',
								data:{releaseId:row.roleId,releaseSn:'role',platformId : node.platformId,systemId : node.systemId,yes:yes}
							});
						});
					}
				});
			}
		});    
	}
</script>
<?php $this->partial("layouts/footmodule")?>