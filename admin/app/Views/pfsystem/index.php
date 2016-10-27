<?php $this->partial("layouts/headmodule")?>
<div data-options="region:'west',title:'平台列表',split:true" style="width:200px;">
    	<ul id="systemMenu"></ul>
    </div> 
<div data-options="region:'center',title:'系统列表'">
    	<div class="easyui-layout" data-options="fit:true">   
            <div data-options="region:'north'">
            	<div id="toolbar" class="easyui-toolbar">
					<a href="javascript:;" iconCls="icon-add" plain="true" onclick="add()">添加</a> <a>-</a> 
            		<a href="javascript:;" iconCls="icon-edit" plain="true" onclick="edit()">修改</a><a>-</a> 
            		<a href="javascript:;" iconCls="icon-del" plain="true" onclick="del()">删除</a> <a>-</a>
					
				</div>
            </div>   
            <div data-options="region:'center'">
    			<table id="tg"></table>
            </div>   
        </div>
    </div>

<script type="text/javascript">
    var sizeObject={width:500,height:280};
    $.parser.parse();
    $('#systemMenu').tree({
    	url:'<?php echo $this->url->get(['for' => 'pfsystem_getPlatform'])?>',
    	onSelect: function(node){
    		//使用'clearSelections':防止读取到了删除的数据
    		$('#tg').treegrid("clearSelections");
    		$.post('<?php echo $this->url->get(['for' => 'pfsystem_ajaxlist'])?>',{platformId : node.platformId},function(data){
    			$('#tg').treegrid("loadData",data);
    		},'json');
    	},
    	onLoadSuccess:function(node,data){  
            $("#systemMenu li:eq(0)").find("div").addClass("tree-node-selected");   //设置第一个节点高亮  
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
		idField: 'id',
		treeField: 'name',
		loadFilter: pagerFilter,
		pagination: true,
		pageSize: 20,
		pageList: [20,50,100],
		columns:[[
			{checkbox : true},
			{field:'name',title:'名称',align:'left',width:80},
			{field:'url',title:'url前缀',align:'left',width:100},
			{field:'orderNo',title:'排序号',width:30,align:'center'},
			{field:'note',title:'备注',width:30,align:'center'}
			
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
	//验证名称的唯一性
	$.extend($.fn.validatebox.defaults.rules, {
	    unique: {
	    validator: function(value, param){
	    	var valid=true;
	    	var formData=$("#formU").form("getData");
	    	$.ajax({
	    		async:false,
	    		url:'<?php echo $this->url->get(['for' => 'pfsystem_checkName'])?>',
	    		data:{name : value,systemId : formData.systemId},
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
	
	function add() {
		var node = $("#systemMenu").tree("getSelected");
		$("body").append('<div id="addUI"></div>');
		$('#addUI').dialog({    
		    title: '新增系统',    
		    width: sizeObject.width,    
		    height: sizeObject.height,    
		    closed: false,    
		    cache: false,    
		    href: '<?php echo $this->url->get(['for' => 'pfsystem_edit'],['formId'=>'formA'])?>&platformId='+node.platformId,    
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
							'<?php echo $this->url->get(['for' => 'pfsystem_doedit'])?>',
							data,
							function(result){
								$.messager.progress('close');
								if(result.code==0){
									$('#addUI').dialog('close');
									$('#addUI').remove();
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
				handler:function(){$('#addUI').dialog('close');$('#addUI').remove();}
			}]
		});    
	}
	
	function edit() {
		var data=$('#tg').treegrid("getSelected");
		if(!data){
			$.messager.alert('信息','请选择一条记录！','info');
			return;
		}
		//console.log(data);
		$("body").append('<div id="editUI"></div>');
		$('#editUI').dialog({    
		    title: '编辑系统',    
		    width: sizeObject.width,    
		    height: sizeObject.height,    
		    closed: false,    
		    cache: false,    
		    href: '<?php echo $this->url->get(['for' => 'pfsystem_edit'],['formId'=>'formU'])?>',    
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
							'<?php echo $this->url->get(['for' => 'pfsystem_doedit'])?>',
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
				var form=$("#formU");
				form.form("load",data);
			}
		});    
	}
	
	function del() {
		var rows=$('#tg').treegrid("getSelections");
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
					       url: '<?php echo $this->url->get(['for' => 'pfsystem_del'])?>',
					       dataType: 'json',
					       data: {ids:ids},
					       success: function(result){
					    	   $.messager.progress('close');
								if (result.code == 0) {
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
</script>
<?php $this->partial("layouts/footmodule")?>