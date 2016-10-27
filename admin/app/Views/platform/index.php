<?php $this->partial("layouts/headmodule")?>
<div data-options="region:'north',border:false">
	<div id="toolbar" class="easyui-toolbar">
		<a href="javascript:void(0)" iconCls="icon-refresh" plain="true" onclick="common.refresh()">刷新</a> <a>-</a> 
		<a href="javascript:;" iconCls="icon-add" plain="true" onclick="add()">添加</a> <a>-</a> 
		<a href="javascript:;" iconCls="icon-edit" plain="true" onclick="edit()">修改</a><a>-</a> 
		<a href="javascript:;" iconCls="icon-del" plain="true" onclick="del()">删除</a> <a>-</a>
	</div>

	<div class="search-div">
		<form name="searchFm" id="searchFm" method="post">
			<table class="search-tb">
				<tbody>
					<tr>
						<th>平台名称 ：</th>
						<td><input class="ipt" name="name" /></td>
						<td><a iconCls="icon-search" class="easyui-linkbutton" id="searchBtn" data-options="iconCls:'icon-search'">查询</a></td>
					</tr>
				</tbody>
			</table>
		</form>
	</div>

</div>
<div data-options="region:'center',border:true">
	<table id="dg"></table>
</div>
<script type="text/javascript">
	var sizeObject={width:500,height:300};
	var dataList=null;
	//数据列表
	function parsePage() {
			dataList=$('#dg').datagrid({
			url: '<?php echo $this->url->get(['for' => 'platform_ajaxlist'])?>',
			singleSelect : false,
			fitColumns : true,
			checkOnSelect : true,
			selectOnCheck : true,
			pageSize: 20,
			pageList: [20,50,100],
			columns : [ [ {
				checkbox : true
			}, {
				title : '名称',
				field : 'name',
				width : 50,
				align : 'left',
				sortable : true
			}, {
				title : '排序号',
				field : 'orderNo',
				width : 30,
				sortable : true
			}, {
				title : '备注',
				field : 'note',
				width : 200,
				align : 'left'
			} ] ],
			onLoadSuccess : function() {
			}
		});
	}
	//验证名称的唯一性
	$.extend($.fn.validatebox.defaults.rules, {
	    unique: {
	    validator: function(value, param){
	    	var valid=true;
	    	var formData=$("#formU").form("getData");
	    	$.ajax({
	    		async:false,
	    		url:'<?php echo $this->url->get(['for' => 'platform_checkName'])?>',
	    		data:{name : value,platformId : formData.platformId},
	    		dataType: 'json',
	    		success:function(json){
	    			if(json.code!=0){
	    				valid=false;
	    			}
	    		}
	    	});
	    	return valid;
	    },
	    message: '名称不能重复!'
	    }
	});
	
	function add() {
		$("body").append('<div id="addUI"></div>');
		$('#addUI').dialog({    
		    title: '新增平台',    
		    width: sizeObject.width,    
		    height: sizeObject.height,    
		    closed: false,    
		    cache: false,    
		    href: '<?php echo $this->url->get(['for' => 'platform_edit'],['formId'=>'formA'])?>',    
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
							'<?php echo $this->url->get(['for' => 'platform_doedit'])?>',
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
		//console.log(data);
		$("body").append('<div id="editUI"></div>');
		$('#editUI').dialog({    
		    title: '编辑平台',    
		    width: sizeObject.width,    
		    height: sizeObject.height,    
		    closed: false,    
		    cache: false,    
		    href: '<?php echo $this->url->get(['for' => 'platform_edit'],['formId'=>'formU'])?>',    
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
							'<?php echo $this->url->get(['for' => 'platform_doedit'])?>',
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
					       url: '<?php echo $this->url->get(['for' => 'platform_del'])?>',
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
<?php $this->partial("layouts/footmodule")?>