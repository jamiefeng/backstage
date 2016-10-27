<!DOCTYPE>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="chrome=1,IE=edge">
<meta name="renderer" content="webkit">
<meta charset="UTF-8" />
<title><?php echo $globalSetting['companyName'];?><?php echo $globalSetting['platformName'];?></title>
<script src="<?php echo $globalSetting['staticUrl']; ?>assets/js/check_browser.js"></script>
<link rel="shortcut icon" href="<?php echo $globalSetting['staticUrl']; ?>images/favicon.ico" type="image/x-icon" />
<link href="<?php echo $globalSetting['staticUrl']; ?>assets/css/base.css" rel="stylesheet" />
<link href="<?php echo $globalSetting['staticUrl']; ?>assets/css/login.css" rel="stylesheet" />
</head>
<body class="login-body">
	<div class="login-div">
		<div class="hd">
			<span class="hd-tt"><i></i>欢迎使用<?php echo $globalSetting['companyName'];?><?php echo $globalSetting['platformName'];?></span>
		</div>
		<div class="bd">
			<div class="login-box">
				<form id="myForm" method="post" action="<?php echo $this->url->get(['for' => 'login_login']); ?>">
					<dl class="dl-lgn-form">
						<dd class="dd-user">
							<i></i> <input id="username" name="username" type="text"
								class="lgn-ipt" placeholder="请输入用户名" datatype="*4-16"
								value="<?php echo $username;?>" nullmsg="用户名不能为空" errormsg="用户名必须为四位以上" />
						</dd>
						<dd class="mt20">
							<i></i> <input id="password" name="password" type="password"
								class="lgn-ipt" placeholder="请输入密码" datatype="*4-16"
								value="" nullmsg="密码不能为空" errormsg="密码必须大于四位以上" />
						</dd>
						<dd class="mt20">
							<a href="javascript:;" class="btn-nrml btn-lgn fl"
								id="loginSubmit"><b>登录</b></a> <span class="fl ml10"
								style="margin-top: 8px;"> <input type="checkbox"
								<?php echo (isset($username)?'checked':'');?>
								id="reminedPwd" name="selectFlag" value="1"/> <label
								for="reminedPwd">记住用户名</label> <span id="loginMsg" class="ml10"></span>
							</span>
						</dd>
					</dl>
				</form>
			</div>
		</div>
		<div class="ft">
			<?php echo $globalSetting['companyName'];?> <span class="arial">&copy; 2015-2016</span>
		</div>
	</div>
	<!--[if lt IE 7]>
<script src="<?php echo $globalSetting['staticUrl']; ?>assets/js/libs/pngfixed.js"></script>
<script type="text/javascript">
    DD_belatedPNG.fix('.login-box,.login-div .bd');
</script>
<![endif]-->
	<script src="<?php echo $globalSetting['staticUrl']; ?>assets/js/libs/jquery-1.6.2.min.js"></script>
	<script src="<?php echo $globalSetting['staticUrl']; ?>assets/js/libs/validform5.3.2.js"></script>
	<!--[if lt IE 9]>
<script src="<?php echo $globalSetting['staticUrl']; ?>assets/js/libs/placeholder.js"></script>
<![endif]-->
	<script>
		if (document.addEventListener) {
			//如果是Firefox  
			document.addEventListener("keypress", enterEvent, true);
		} else {
			//如果是IE
			document.attachEvent("onkeypress", enterEvent);
		}
		function enterEvent(evt) {
			if (evt.keyCode == 13) {
				$('#loginSubmit').click();
			}
		}
		$("#myForm").Validform(
			{
				ajaxPost : true,
				callback : function(data) {
					//请返回登录状态信息
					if (data.code != null && data.code == '0') {
						$("#loginMsg").addClass("Validform_right").text(data.msg);
						location.href = '<?php echo $this->url->get(['for' => 'main_index']); ?>';
					} else {
						$("#loginMsg").addClass("Validform_wrong").text(data.msg);
						return;
					}
				}
			});
		$('#loginSubmit').click(function() {
			$("#myForm").submit();
		});
	</script>
</body>
</html>