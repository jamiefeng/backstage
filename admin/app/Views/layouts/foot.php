<div data-options="region:'south',split:true,border:false" style="height:36px;text-align:center;line-height:36px;">
    	<?php echo $globalSetting['companyName'];?><?php echo $globalSetting['platformName'];?> <span class="arial">&copy; 2015-2016</span>
    </div>
    <div id="leftMenuPanel" data-options="region:'west',title:'<?php echo $loadSystemName;?>菜单',split:true,border:true" style="width:150px;">
    	<ul id="leftMenu" class="" url="<?php echo $this->url->get(['for' => 'main_lefttree'],['systemId'=>$loadSystemId,'url'=>$loadSystemUrl]); ?>" data-options="loadFilter : indexTreeLoadFilter" style="width:100%;height:100%;">
        </ul>
    </div>
    <div data-options="region:'center',border:true" style="background:#eee;">
    	<div id="mainTabs" class="easyui-tabs" data-options="border:false,fit:true,tools:'#tabsButtons'" >
            <div name="first" title="系统桌面" iconCls="icon-home" fit="true">
            	<iframe id="deskFrame" style="border:none;width:100%;height:100%;" src="<?php echo $this->url->get(['for' => 'main_desktop'])?>" frameborder="0" scrolling="none"></iframe>
            </div>
        </div>
    </div>
    <div id="tabsButtons" style="margin-top:1px;">
    	<a id="tabToolsFullScreen" class="easyui-linkbutton" iconCls="icon-window-max" plain="true" onclick="tabToolsFullScreenOnClick()">全屏</a>
	</div>
<script>
$.parser.parse();
function resetpassword(){
	$("body").append('<div id="resetpwd"></div>');
	$('#resetpwd').dialog({    
	    title: '重置密码',    
	    width: 500,    
	    height: 220,    
	    closed: false,    
	    cache: false,    
	    href: '<?php echo $this->url->get(['for' => 'main_setpassword'])?>',    
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
						'<?php echo $this->url->get(['for' => 'main_setpassword'])?>',
						data,
						function(result){
							$.messager.progress('close');
							if(result.code==0){
								$('#resetpwd').dialog('close');
								$('#resetpwd').remove();
								$.messager.alert('成功','重置密码成功','info');
							}else{
								$.messager.alert('信息','保存失败:'+result.msg,'error');
							}
						},'json'
					);
				}
			}
		},{
			text:'关闭',
			iconCls : 'icon-cancel',
			handler:function(){$('#resetpwd').dialog('close');$('#resetpwd').remove();}
		}]
	});    
}

function indexTreeLoadFilter(data){
	return common.treeLoadFilter(data,{source:1});
}
</script>
</body>
</html>