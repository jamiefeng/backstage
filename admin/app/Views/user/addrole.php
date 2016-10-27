<?php $this->partial("layouts/headmodule")?>
<div data-options="region:'north',border:true">
		<form name="searchFmRole" id="searchFmRole" method="post" onsubmit="return false;">
            <table class="search-tb">
                 <tbody>
                    <tr>
                    	<td>角色名称：
                    		<input class="ipt" name="name"/>
                    	</td>
                        <td>
							<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-search" id="searchBtnRole">查询</a>
                        </td>
					</tr>
				</tbody>
			</table>
		</form>
	</div>
	<div data-options="region:'center',border:true" style="height:93%">
		<table id="roledg">
		</table>
	</div>
<?php $this->partial("layouts/footmodule")?>