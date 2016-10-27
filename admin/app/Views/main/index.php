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
			<span class="hd-tt"><i></i><b><?php echo $userInfo['realName']?>，
			        欢迎使用<?php echo $globalSetting['companyName'];?><?php echo $globalSetting['platformName'];?>
                <a title="退出" href="<?php echo $this->url->get(['for' => 'login_login'])?>">[退出]</a>
			</span>
            	
        </div>
		<div class="bd">
			<div style="width500px;left: 50%;margin-left: -500px;padding-right:150px;margin-top: -140px;position: absolute;top: 50%;">
			<ul>
			<?php foreach($platform as $v):?>
			 <a href="<?php echo $this->url->get(['for' => 'main_system'],['platformId'=>$v['platformId']])?>">
			 <li style="float:left;margin:10px;padding:30px;background-color:#DBEAF9;font-size:20px;-moz-border-radius: 15px;-webkit-border-radius: 15px;border-radius:15px;">
			 <?php echo $v['name'];?>
			 </li></a>
			<?php endforeach;?>
			</ul>
			</div>
		</div>
		<div class="ft">
			<?php echo $globalSetting['companyName'];?> <span class="arial">&copy; 2015-2016</span>
		</div>
	</div>
	
</body>
</html>