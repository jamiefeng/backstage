function errorTips(msg)
    {    
        top.$.messager.confirm('提示', msg);
    }
 
//@validate
function _validate(o, arge){
	var arge = arge || {};
	var truenamereg = /^[\u4E00-\u9FA5]{2,6}$|^[A-Za-z]{2,18}$/;
	var passwdreg = /^(?=.*\d)(?=.*[a-zA-Z])[\da-zA-Z\W]{8,20}$/;
	var phonereg = /^0?(13[0-9]|15[012356789]|18[0123456789]|14[57])[0-9]{8}$/;
	var emailreg = /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9_]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
	//var contactreg = /(^(\d{3,4}-)?\d{7,8})$|((\(\d{3}\))|(\d{3}\-))?1[3-8][0-9]\d{8}?$|15[89]\d{8}$/; //联系方式，包括手机、 固话
	var numreg = /^[0-9]*[1-9][0-9]*$/;　　//正整数
	var mymobile = /^0?1[3|4|5|8][0-9]\d{8}$/; //匹配13，14，15，18开头的手机号码
	var myphone = /^(([0\+]\d{2,3}-)?(0\d{2,3})-)(\d{7,8})(-(\d{3,}))?$/ ;  //兼容格式: 国家代码(2到3位)-区号(2到3位)-电话号码(7到8位)-分机号(3位)

	
	switch(o.attr('name')){
		//@登录
		case '_nyusername' :
			if(!o.val()){
				errorTips('请输入登录账号');
			}else{
				return true;
			}
			break;
		case '_password':
			if(!o.val()){
				errorTips('请输入登录密码');
			}else{
				return true;
			}
			break;
		//@公共
		case '_phone_':
			if(!o.val()){
				errorTips("联系方式不能为空");
			}else if(!phonereg.test(o.val())){
				errorTips("联系方式格式错误");
			}else{
				return true;
			}
			break;
		case '_pwd_'://@找回密码/设置密码/注册
			if(o.val() == ''){
				errorTips('密码不能为空');
			}else if(!passwdreg.exec(o.val())){
				errorTips('输入8-20位包含数字、大小写字母的密码');
			}else{
				errorTips('');
				return true;
			}
			break;
		case '_repwd_'://@找回密码/设置密码/注册
			if(o.val() == ''){
				errorTips('请输入重复密码');
			}else if(o.val() != $('#_pwd_').val()){
				errorTips('重复密码不相同');
			}else{
				return true;
			}
			break;
			//新增供应商
		case '_OrganName' ://机构名称
			if(!o.val()){
				errorTips('请输入机构名称');
			}else{
				return true;
			}
			break;
		case 'create_time' ://成立时间
			if(!o.val().length > 0){
				errorTips('请选择成立时间');
			}else{
				return true;
			}
			break;
		case 'elm1' ://机构介绍
			if(!o.val()){
				errorTips('请输入机构介绍');
			}else{
				return true;
			}
			break;
		case '_ContactName' ://联系人
			if(!o.val()){
				errorTips('请输入联系人');
			}else{
				return true;
			}
			break;
		case '_OrganAddress' ://机构地址
			if(!o.val()){
				errorTips('请输入机构地址');
			}else{
				return true;
			}
			break;
		case '_email'://邮箱
			if(!o.val()){
				errorTips("邮箱不能为空");
			}else if(!emailreg.exec(o.val())){
				errorTips("邮箱地址不正确");
			}else{
				return true;
			}
			break;
		case '_phone'://热线
			if(!o.val()){
				errorTips("接待热线不能为空");
			}else if(!mymobile.test(o.val())&& !myphone.test(o.val())){
				errorTips("接待热线号码错误");
			}else{
				return true;
			}
			break;
		case '_qqnum'://QQ
			if(!o.val()){
				errorTips("QQ不能为空");
			}else if(!numreg.test(o.val())){
				errorTips("QQ格式不正确");
			}else{
				return true;
			}
			break;
		}
	return false;
}

$(function(){
	var _nyusername=$("input[name='_nyusername']");//登录用户名
	var _password=$("input[name='_password']");//登录密码
	var _LoginBtn=$("input[name='_LoginBtn']");//登录按钮
	var _phone=$("input[name='_phone']");//热线
	var _email=$("input[name='_email']");//邮箱
	var _qqnum=$("input[name='_qqnum']");//QQ
	var _saveConUs=$("input[name='saveConUs']");//联系方式保存按钮
	var _SaveSupplier=$("#SaveSupplier");//新增供应商按钮
	var elm1=$("textarea[name='elm1']");//编辑器
	var _OrganName=$("input[name='_OrganName']");//机构名称
	var create_time=$("input[name='create_time']");//成立时间
	var _ContactName=$("input[name='_ContactName']");//联系人
	var _OrganAddress=$("input[name='_OrganAddress']");//机构地址
	var _ServiceArea=$("input[name='_ServiceArea']:checked").length;//服务范围
	var _phone_=$("input[name='_phone_']")//联系方式
	_LoginBtn.on('click',function(){//登录页面
		if(_validate(_nyusername) 
		&& _validate(_password) 
		){
			$('#Loginform').submit();
		}else{}
		return false;
	});
	_saveConUs.on('click',function(){//联系方式页面
		if(_validate(_phone) 
		&& _validate(_email) 
		&& _validate(_qqnum) 
		){
			$('#ContactUsform').submit();
		}else{}
		return false;
	});
	_SaveSupplier.on('click',function(){//添加供应商页面
		if(_validate(_OrganName) 
		&&(_validate(create_time))
		&&(_validate(elm1))
		&&(_validate(_ContactName))
		&&(_validate(_phone_))
		&&(_validate(_OrganAddress))
		){
			//alert(111)
			//$('#add-supplier').submit();
		}else{}
		return false;
	});
});