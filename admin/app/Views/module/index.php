<?php $this->partial("layouts/headmodule")?> 
<div data-options="region:'west',title:'系统列表',split:true" style="width:200px;">
    	<ul id="systemMenu"></ul>
    </div>   
    <div data-options="region:'center',title:'模块列表'">
    	<div class="easyui-layout" data-options="fit:true">   
            <div data-options="region:'north'">
            	<div id="toolbar" class="easyui-toolbar">
					
						<a href="javascript:;" iconCls="icon-add" plain="true" onclick="add()">添加</a>
						<a>-</a>
					
					
						<a href="javascript:;" iconCls="icon-edit" plain="true" onclick="edit()">修改</a>
						<a>-</a>
					
					
						<a href="javascript:;" iconCls="icon-del" plain="true" onclick="del()">删除</a>
						<a>-</a>
					
					
						<a href="javascript:;" iconCls="icon-add" plain="true" onclick="addfunction()">添加操作权限</a>
						<a>-</a>
					
				</div>
            </div>   
            <div data-options="region:'center'">
    			<table id="tg"></table>
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
			//使用'clearSelections':防止读取到了删除的数据
			if(node.systemId){
    			$('#tg').treegrid("clearSelections");
    			$.post('<?php echo $this->url->get(['for' => 'module_getModule'])?>',{systemId : node.systemId},function(data){
    				$('#tg').treegrid("loadData",data);
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
	function refreshSelectedNode(){
		var n = $("#systemMenu").tree("getSelected");  
        if(n!=null){  
             $("#systemMenu").tree("select",n.target);
        }
	}
	$('#tg').treegrid({
		animate: true,
		rownumbers : true,
		collapsible: true,
		fitColumns:true,
		singleSelect : false,
		checkOnSelect : true,
		selectOnCheck : true,
		method: 'post',
		idField: 'moduleId',
		treeField: 'name',
		loadFilter: pagerFilter,
		pagination: true,
		pageSize: 10,
		pageList: [10,20,50],
		columns:[[
			{checkbox : true},
			{field:'name',title:'名称',align:'left',width:80},
			{field:'url',title:'url',width:140,align:'left'},
			{field:'pvs',title:'操作权限',width:140,align:'left',
				formatter: function(value,row,index){
					var htmltext='';
					//是文件夹不需要操作权限
					if(row.url==""){
						return htmltext;
					}
					for(var i=0,len=value.length;i<len;i++){
						htmltext+='<div style="float:left;over-flow:hidden;height:16px;line-height:16px;">'+
								  '<span style="float:left;">';
						htmltext+=value[i].name+'</span>';
						if(value[i].position!=1)
						htmltext+='<img  title="删除操作权限值" style="float:left;vertical-align:middle;cursor:pointer;"'+
								  'onclick="deletePriVal(\''+row.moduleId+'\',\''+value[i].position+'\',this)" '+
								  'src="<?php echo $globalSetting['staticUrl']; ?>images/delete.gif" />';
						if(i!=len-1){
							htmltext+='&nbsp;|&nbsp;';
						}
						htmltext+='</div>';
					}
					return htmltext;
				}
			},
			{field:'orderNo',title:'排序号',width:30,align:'center'},
			{field:'childNode',title:'操作',width:30,align:'center',
				formatter: function(value,row,index){
					if(row.pid==""){
						return '<img title="添加子类" style="cursor:pointer;" onclick="add(\''+row.moduleId+'\')" src="<?php echo $globalSetting['staticUrl']; ?>resources/assets/images/icons/add.gif" />';
				    }else{
				        return '';
					}
				}
			}
		]],
		onLoadSuccess:function(){
	
		}
	});
	function pagerFilter(data){
        if ($.isArray(data)){    // is array  
            data = {  
                total: data.length,  
                rows: data  
            }; 
        }
        var dg = $(this);  
        var opts = dg.treegrid('options');  
        var pager = dg.treegrid('getPager');  
        pager.pagination({  
            onSelectPage:function(pageNum, pageSize){  
                opts.pageNumber = pageNum;  
                opts.pageSize = pageSize;  
                pager.pagination('refresh',{  
                    pageNumber:pageNum,  
                    pageSize:pageSize  
                });  
                dg.treegrid('loadData',data);  
            }  
        });  
        if (!data.topRows){  
        	data.topRows = [];
        	data.childRows = [];
        	for(var i=0; i<data.rows.length; i++){
        		var row = data.rows[i];
        		row._parentId ? data.childRows.push(row) : data.topRows.push(row);
        	}
			data.total = (data.topRows.length);
        }  
        var start = (opts.pageNumber-1)*parseInt(opts.pageSize);  
        var end = start + parseInt(opts.pageSize);  
		data.rows = $.extend(true,[],data.topRows.slice(start, end).concat(data.childRows));
		return data;
	}
	
	function add(pid) {
		var node = $("#systemMenu").tree("getSelected");
		if(!pid){
			pid="";
		}
		$("body").append('<div id="addUI"></div>');
		$.parser.parse($('#addUI'));
		$('#addUI').dialog({    
		    title: '新增模块',    
		    width: sizeObject.width,    
		    height: sizeObject.height,    
		    closed: false,    
		    cache: false,    
		    href: "<?php echo $this->url->get(['for' => 'module_edit'],['formId'=>'formA'])?>&pid="+pid+"&systemId="+node.systemId,    
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
							'<?php echo $this->url->get(['for' => 'module_doEdit'])?>',
							data,
							function(result){
								$.messager.progress('close');
								if(result.code==0){
									$('#addUI').dialog('destroy');
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
				handler:function(){
					//$('#addUI').dialog('close');
					$('#addUI').dialog('destroy');
					$('#addUI').remove();
				}
			}]
		});    
	}
	function edit() {
		var data = $('#tg').treegrid("getSelected");
		if(!data){
			$.messager.alert('信息','请选择一条记录！','info');
			return;
		}
		$("body").append('<div id="editUI"></div>');
		$.parser.parse($('#editUI'));
		$('#editUI').dialog({    
		    title: '编辑模块',    
		    width: sizeObject.width,    
		    height: sizeObject.height,    
		    closed: false,    
		    cache: false,    
		    href: "<?php echo $this->url->get(['for' => 'module_edit'],['formId'=>'formU'])?>",
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
							'<?php echo $this->url->get(['for' => 'module_doEdit'])?>',
							fdata,
							function(result){
								$.messager.progress('close');
								if(result.code==0){
									$('#editUI').dialog('destroy');
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
				handler:function(){
					//$('#editUI').dialog('close');
					$('#editUI').dialog('destroy');
					$('#editUI').remove();
				}
			}],
			onLoad : function (){
				var form = $("#formU");
				form.form("load",data);
			}
		});    
	}
	
	function del() {
		var rows=$('#tg').treegrid("getSelections");
		if (rows && rows.length > 0) {
			for(var i=0,len=rows.length;i<len;i++){
				if(rows[i].children){
					$.messager.alert('信息','请先删除子模块！','info');
					return;
				}
			}
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
					       url: '<?php echo $this->url->get(['for' => 'module_del'])?>',
					       dataType: 'json',
					       data: {ids:ids},
					       success: function(result){
					    	   $.messager.progress('close');
								if (result.code == 0) {
									$.messager.progress('close');
									//使用'clearSelections':防止读取到了删除的数据
									$('#tg').treegrid("clearSelections");
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
	
	//删除权限值
	function deletePriVal(moduleid,position,imgobj){
		$.messager.confirm("信息", "确定删除吗？", function(r) {
			if (r) {
        		$.ajax({
        			type:'post',
        			url : "<?php echo $this->url->get(['for' => 'module_deletePriVal'])?>",
        			data: {position:position,moduleId: moduleid},		
        			dataType:'json',
        			success : function(result) {
        				if(result.code==0){
        					$(imgobj).parent().remove();
        				}else{
        					$.messager.alert('信息','删除失败！','info');
        				}
        			}
        		});
			}
		});
	}
	
	//添加操作权限
	function addfunction() {
		var node = $("#systemMenu").tree("getSelected"),
		rows = $('#tg').treegrid("getSelections");
		if(rows && rows.length==1){
			$("body").append('<div id="addPValUI"></div>');
			$.parser.parse($('#addPValUI'));
			$('#addPValUI').dialog({    
			    title: '给模块 <font color="red">'+rows[0].name+'</font> 分配权限值',    
			    width: sizeObject.width,    
			    height: 300,    
			    closed: false,    
			    cache: false,    
			    href: "<?php echo $this->url->get(['for' => 'module_insertPriVal'])?>?systemId="+node.systemId+"&moduleId="+rows[0].moduleId,  
			    modal: true,
			    buttons : [{
					text:'保存',
					iconCls : 'icon-save',
					handler:function(){
						$.messager.progress(); 
						var data =[];
						$('input[name="position"]:checked').each(function(){
							data.push($(this).val());
						}); 
						if(data.length==0){
							$.messager.progress('close');
							$.messager.alert('信息','请勾选权限值！','info');
							return false;
						}
						$.ajax({
						       type: 'post',
						       url: '<?php echo $this->url->get(['for' => 'module_doInsertPriVal'])?>?moduleId='+rows[0].moduleId,
						       dataType: 'json',
						       data: {position:data},
						       success: function(result){
						    	   $.messager.progress('close');
									if(result.code==0){
										$('#addPValUI').dialog('destroy');
										$('#addPValUI').remove();
										$("#systemMenu").tree("select",node.target);
									}else{
										$.messager.alert('信息','保存失败！','info');
									}
						       }
						});
					}
				},{
					text:'关闭',
					iconCls : 'icon-cancel',
					handler:function(){
						//$('#addPValUI').dialog('close');
						$('#addPValUI').dialog('destroy');
						$('#addPValUI').remove();
					}
				}],
				onOpen:function(){//页面打开后执行
					setTimeout(function(){
						$(".selectAllBtn").click(function(){
							//debugger;
							if(this.value == '全选'){
								//全选
								$("#formA input:checkbox").attr("checked","true");
							}else if(this.value == '取消'){
								//全不选
								$("#formA input:checkbox").removeAttr("checked");
							}
						});
					},500);
				}
			});    
		}else{
			$.messager.alert('信息','请选择一条记录！','info');
		}
	}
</script>
<?php $this->partial("layouts/footmodule")?>