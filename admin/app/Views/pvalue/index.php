<?php $this->partial("layouts/headmodule")?>
<div data-options="region:'west',title:'系统列表',split:true" style="width:200px;">
    	<ul id="systemMenu"></ul>
    </div>   
    <div data-options="region:'center'">
    	<div class="easyui-layout" data-options="fit:true">   
            <div data-options="region:'north'">
            	<div id="toolbar" class="easyui-toolbar">
					
						<a href="javascript:;" iconCls="icon-add" plain="true" onclick="add()">添加</a>
						<a>-</a>
					
					
						<a href="javascript:;" iconCls="icon-edit" plain="true" onclick="edit()">修改</a>
						<a>-</a>
					
					
						<a href="javascript:;" iconCls="icon-del" plain="true" onclick="del()">删除</a>
						<a>-</a>
					
				</div>
            </div>   
            <div data-options="region:'center'">
    			<table id="dg"></table>
            </div>   
        </div>
    </div>   
</body> 
<script type="text/javascript">
	var sizeObject={width:500,height:280};
	$.parser.parse();
	$('#systemMenu').tree({
		url:'<?php echo $this->url->get(['for' => 'module_getSystems'])?>',
		onSelect: function(node){
			if(node.systemId){
				$('#tg').treegrid("clearSelections");
    			$.post('<?php echo $this->url->get(['for' => 'pvalue_getPvalue'])?>',{systemId : node.systemId},function(data){
    				$('#dg').datagrid("loadData",data);
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
	
	$('#dg').datagrid({
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
			width : 80,
			align : 'left'
		}, {
			title : '标识',
			field : 'sign',
			width : 80,
			align : 'left'
		}, {
			title : '权限位',
			field : 'position',
			width : 50,
			align : 'center',
			formatter: function(value,row,index){
				if(!value){return "0";}return value;
			}
		}, {
			title : '排序号',
			field : 'orderNo',
			width : 30,
			align : 'center'
		}, {
			title : '备注',
			field : 'remark',
			width : 100,
			align : 'left'
		} ] ],
		onLoadSuccess : function() {
		}
	});
	//验证标识的唯一性
	$.extend($.fn.validatebox.defaults.rules, {
	    unique: {
	    validator: function(value, param){
	    	var valid=true;
	    	var formData=$("#formU").form("getData");
	    	var node = $("#systemMenu").tree("getSelected");
	    	$.ajax({
	    		async:false,
	    		url:'<?php echo $this->url->get(['for' => 'pvalue_checkSign'])?>',
	    		data:{sign : value, id:formData.id, systemId : node.systemId},
	    		dataType: 'json',
	    		success:function(json){
	    			if(json.code!=0){
	    				valid=false;
	    			}
	    		}
	    	});
	    	return valid;
	    },
	    message: '系统名称不能重复!'
	    }
	});
	function refreshSelectedNode(){
		var n = $("#systemMenu").tree("getSelected");  
        if(n!=null){  
             $("#systemMenu").tree("select",n.target);
        }
	}
	function add() {
		var node = $("#systemMenu").tree("getSelected");
		$("body").append('<div id="addUI"></div>');
		$('#addUI').dialog({    
		    title: '新增权限值',    
		    width: sizeObject.width,    
		    height: sizeObject.height,    
		    closed: false,    
		    cache: false,    
		    href: "<?php echo $this->url->get(['for' => 'pvalue_edit'],['formId'=>'formA'])?>&systemId="+node.systemId,    
		    modal: false,
		    buttons : [{
				text:'保存',
				iconCls : 'icon-save',
				handler:function(){
					var form=$("#formA");
					if(form.form('validate')){
						$.messager.progress(); 
						var data=form.form("getData");
						$.post(
							'<?php echo $this->url->get(['for' => 'pvalue_doEdit'])?>',
							data,
							function(result){
								$.messager.progress('close');
								if(result.code==0){
									$('#addUI').dialog('close');
									$('#addUI').remove();
									$("#systemMenu").tree("select",node.target);
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
		var data = $('#dg').datagrid("getSelected");
		if(!data){
			$.messager.alert('信息','请选择一条记录！','info');
			return;
		}
		$("body").append('<div id="editUI"></div>');
		$('#editUI').dialog({    
		    title: '编辑权限值',    
		    width: sizeObject.width,    
		    height: sizeObject.height,
		    closed: false,    
		    cache: false,    
		    href: "<?php echo $this->url->get(['for' => 'pvalue_edit'],['formId'=>'formU'])?>&systemId="+data.systemId+"&id="+data.id,
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
							'<?php echo $this->url->get(['for' => 'pvalue_doEdit'])?>',
							fdata,
							function(result){
								$.messager.progress('close');
								if(result.code==0){
									$('#editUI').dialog('close');
									$('#editUI').remove();
									refreshSelectedNode();
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
				var form = $("#formU");
				form.form("load",data);
			}
		});    
	}
	
	function del() {
		var rows=$('#dg').datagrid("getSelections");
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
					       url: '<?php echo $this->url->get(['for' => 'pvalue_del'])?>',
					       dataType: 'json',
					       data: {ids:ids},
					       success: function(result){
					    	   $.messager.progress('close');
								if (result.code == 0) {
									$.messager.progress('close');
									//使用'clearSelections':防止读取到了删除的数据
									$('#dg').datagrid("clearSelections");
									refreshSelectedNode();
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
</script>
<?php $this->partial("layouts/footmodule")?>