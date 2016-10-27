<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>基因160运营管理系统</title>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE9"/>

<link rel="stylesheet" type="text/css" href="<?php echo $globalSetting['staticUrl']; ?>/easyui/themes/default/easyui.css">
<link rel="stylesheet" type="text/css" href="<?php echo $globalSetting['staticUrl']; ?>/easyui/themes/icon.css">
<link rel="stylesheet" type="text/css" href="<?php echo $globalSetting['staticUrl']; ?>/css/global.css">
<script type="text/javascript" src="<?php echo $globalSetting['staticUrl']; ?>/js/jquery-1.8.3.js"></script>
<script type="text/javascript" src="<?php echo $globalSetting['staticUrl']; ?>/easyui/jquery.js"></script>
<script type="text/javascript" src="<?php echo $globalSetting['staticUrl']; ?>/js/base.js"></script>
<script type="text/javascript" src="<?php echo $globalSetting['staticUrl']; ?>/easyui/jquery.easyui.min.js"></script>
<script type="text/javascript" src="<?php echo $globalSetting['staticUrl']; ?>/easyui/locale/easyui-lang-zh_CN.js"></script>

<script type="text/javascript">
	var index_tabs;
	$(function() {
		index_tabs = $('#index_tabs').tabs({
			fit : true,
			onContextMenu : function(e, title) {
				e.preventDefault();
				index_tabsMenu.menu('show', { 
					left : e.pageX,
					top : e.pageY
				}).data('tabTitle', title);
			}
		}); 
	});

	function addTab(title,url){ //创建tabs
	    if ($('#index_tabs').tabs('exists', title)){  
	        $('#index_tabs').tabs('select', title);  
	    } else {  
	        var content = '<iframe scrolling="auto" frameborder="0" src="'+url+'" style="width:100%;height:99.2%;"></iframe>';
	        $('#index_tabs').tabs('add',{  
	            title:title,  
	            content:content,
	            closable:true  
	        });  
	    };
	};
    function openTab(title,url) {
        var TopTab=window.parent.$('#index_tabs');
        TopTab.tabs('close', title);
        TopTab.tabs('add',{title:title,content:'<iframe scrolling="auto" frameborder="0"  src="'+url+'" style="width:100%;height:99.2%;"></iframe>',closable:true });
    }
    //关闭当前tab，打开新窗口
	function closeTab(title,url,opentitle){
		var TopTab=window.parent.$('#index_tabs');
        TopTab.tabs('close', title);
        TopTab.tabs('close', opentitle);
        TopTab.tabs('add',{title:opentitle,content:'<iframe scrolling="auto" frameborder="0"  src="'+url+'" style="width:100%;height:99.2%;"></iframe>',closable:true });
	}
	function errorTips(msg)
    {    
        top.$.messager.confirm('提示', msg);
    }
	//打开弹窗
    function openBox(title,url,w,h){
       if(w==undefined) w = 500;
       if(h==undefined) h = 480;
 	   BombBoxNoBtn(title, w,h, '<iframe src="'+url+'" frameborder="0" width="100%" height="99.4%"></iframe>');
    }
</script>
<script type="text/javascript" src="<?php echo $globalSetting['staticUrl'];?>/js/xheditor/xheditor-1.2.2.min.js?<?php echo $globalSetting['static_version'];?>" ></script>
<script type="text/javascript" src="<?php echo $globalSetting['staticUrl'];?>/js/xheditor/xheditor_lang/zh-cn.js?<?php echo $globalSetting['static_version'];?>"></script>
<link href="<?php echo $globalSetting['staticUrl'];?>/js/xheditor/xheditor_skin/default/ui.css?<?php echo $globalSetting['static_version'];?>" type="text/css" rel="stylesheet">
</head>
<script type="text/javascript">
	var index_tabs;
	$(function() {
		index_tabs = $('#index_tabs').tabs({
			fit : true,
			onContextMenu : function(e, title) {
				e.preventDefault();
				index_tabsMenu.menu('show', { 
					left : e.pageX,
					top : e.pageY
				}).data('tabTitle', title);
			}
		}); 
	});
	$(function(){
		$("#rgtiframe").html('<iframe src="<?php echo $this->url->get(['for' => 'main_index']); ?>" frameborder="0" width="100%" height="99.2%" style="min-width:1120px;"></iframe>')
	});
	
</script>

<body class="easyui-layout">
<!-- north区域 -->
<div data-options="region:'north'" class="header">
	<img src="<?php echo $globalSetting['staticUrl']; ?>/images/logo.png">
	<p>您好！<?php echo $username;?> | <a href="#">退出</a></p>
</div>
<!-- /north区域 -->

<!-- west区域 -->
<div data-options="region:'west',iconCls:'',width:'150'" title="欢迎您登录！" class="Aside">
	<!-- 左侧center -->
	<div data-options="region:'center'" border="false">
		<div id="firstpane" class="menu_list">
			<p class="menu_head">商品管理</p>
			<div class="menu_body">
				<a href="javascript:void(0)" onclick="addTab('商品列表','<?php echo $this->url->get(['for' => 'main_index']); ?>')" >商品列表</a>
				<a href="javascript:void(0)" onclick="addTab('新增商品','<?php echo $this->url->get(['for' => 'main_index']); ?>')" >新增商品</a>
			</div>

            <p class="menu_head">订单管理</p>
			<div class="menu_body">
				<a href="javascript:void(0)" onclick="addTab('订单列表','<?php echo $this->url->get(['for' => 'main_index']); ?>',1)" >订单列表</a>
				<a href="javascript:void(0)" onclick="addTab('订单详情','<?php echo $this->url->get(['for' => 'main_index']); ?>')" >订单详情</a>
				<a href="javascript:void(0)" onclick="addTab('报告详情','<?php echo $this->url->get(['for' => 'main_index']); ?>')" >报告详情</a>
			</div>
            
            <p class="menu_head">资讯管理</p>
			<div class="menu_body">
				<a href="javascript:void(0)" onclick="addTab('资讯列表','<?php echo $this->url->get(['for' => 'main_index']); ?>',1)" >资讯列表</a>
				<a href="javascript:void(0)" onclick="addTab('新增资讯','<?php echo $this->url->get(['for' => 'main_index']); ?>')" >新增资讯</a>
			</div>
			
			<p class="menu_head">会员管理</p>
			<div class="menu_body">
				<a href="javascript:void(0)" onclick="addTab('会员列表','<?php echo $this->url->get(['for' => 'main_index']); ?>',1)" >会员列表</a>
			</div>
            <p class="menu_head">财务管理</p>
			<div class="menu_body">
				<a href="javascript:void(0)" onclick="addTab('交易流水','<?php echo $this->url->get(['for' => 'main_index']); ?>',1)" >交易流水</a>
			</div>
		</div>
		<script type="text/javascript">
		$(function() {
			$("#firstpane .menu_body:eq(0)").show();
			$("#firstpane p.menu_head").click(function(){
				$(this).toggleClass("current");
				$(this).next(".menu_body").slideToggle();
			});
		});
		</script>
	</div>	
	<!-- /左侧center -->
</div>
<!-- /west区域 -->

<!-- center区域 -->
<div data-options="region:'center',bodyCls:'Section'">
	<div class="easyui-layout" fit="true">
		<!-- center -->
		<div data-options="region:'center'" style="padding:5px 5px 0;">
			<div id="index_tabs" class="easyui-tabs">
				<div title="商品列表" id="rgtiframe">
					
				</div>
			</div>
		</div>
		<!-- /center -->
	</div>
</div>
<!-- /center区域 -->


<div id='win' data-options="modal:true"></div>


</body>
</html>
<?php exit();?>