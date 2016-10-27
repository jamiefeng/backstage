<?php $this->partial("layouts/head")?>
	<div class="header" data-options="region:'north',split:true,border:true" style="height:105px;">
		<div class="header_nav_index">
            <a class="logo" href="">logo</a> 
            <div class="bd" style="text-align:right;padding-top: 30px;padding-right: 10px;">
            	<span><b><?php echo $platform['name']?></b> - 欢迎您，<font color="#1808dd"><?php echo $userInfo['realName']?></font></span>
                <a href="<?php echo $this->url->get(['for' => 'main_index'])?>">平台桌面</a> | 
                <a onclick="resetpassword()" href="javascript:void(0);">重置密码</a> | 
                <a href="<?php echo $this->url->get(['for' => 'login_login'])?>">[退出]</a>
            </div>
        </div>
        <div id="navMenu" class="nav-menu" style="position:relative;">
            <ul>
            	<?php foreach($systems as $k=>$v):?>
					<li <?php echo ($k==0?'class="curr"':'')?> data-deskurl="<?php echo $this->url->get(['for' => 'main_desktop'])?>" data-sessionid="<?php echo $sessionId?>" data-url="<?php echo $this->url->get(['for' => 'main_lefttree'],['systemId'=>$v['systemId'],'url'=>$v['url']]); ?>" >
						<a href="javascript:;"><span><?php echo $v['name']?></span></a>
					</li>
				<?php endforeach;?>
            </ul>
            <span id="moreMenu1" style="display:none;position:absolute;right:20px;top:2px;color:white;font-weight:bold;cursor:pointer;z-index:999;">&lt;&lt;</span>
            <span id="moreMenu" style="display:none;position:absolute;right:0;top:2px;color:white;font-weight:bold;cursor:pointer;z-index:999;">&gt;&gt;</span>
        </div>
	</div>   
<?php $this->partial("layouts/foot")?>