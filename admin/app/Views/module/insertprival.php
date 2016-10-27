<?php $this->partial("layouts/headmodule")?>
<center>
	<div class="tb_class">
		<input class="btn btnplan selectAllBtn" type="button" value="全选" style="cursor: pointer;"/>&nbsp;&nbsp;
		<input class="btn btnplan selectAllBtn" type="button" value="取消" style="cursor: pointer;"/>
	</div>
	<form id="formA" method="post">
        <div style="margin:20px auto;">
	        <?php foreach($pvalue as $v):?>
	        	&nbsp;
	        	<label><input type="checkbox" name="position" value="<?php echo $v['position']?>" />&nbsp;<?php echo $v['name']?></label>
	        <?php endforeach;?>
        </div>
    </form>
    </center>
<?php $this->partial("layouts/footmodule")?>