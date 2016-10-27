// 动态添加dialog对话框----按钮提交
function BombBox(tit, BoxWidth, BoxHeight, ContentURL){
	var dialogBox = top.$("<div></div>").dialog({
		title: tit,
		width: BoxWidth,
		height: BoxHeight,
		content: ContentURL,
	    closed: true,
	    modal: true,
	    buttons: [{
			text:'提交',
			iconCls:'icon-ok',
			handler: function(){
				alert('ok');
			}
		},{
			text:'取消',
			iconCls: 'icon-cancel',
			handler: function(){
				dialogBox.dialog('destroy');
			}
		}]
	}).dialog('open');
};

function PrintBox(tit, BoxWidth, BoxHeight, ContentURL){
	var dialogBox = top.$("<div></div>").dialog({
		title: tit,
		width: BoxWidth,
		height: BoxHeight,
		content: ContentURL,
	    closed: true,
	    modal: true
	    
	}).dialog('open');
};

// 动态添加dialog对话框----没有提交按钮
function BombBoxNoBtn(tit, BoxWidth, BoxHeight, ContentURL){
	var dialogBox = top.$("#win").dialog({
		title: tit,
		width: BoxWidth,
		height: BoxHeight,
		content: ContentURL,
	    closed: true,
	    modal: true
	}).dialog('open')
};

function parentReload(){
	parent.window.location.reload(true);
}

function getObjectKey(source) {
    var result=[],
        key,
        _length=0;
    for(key in source){
       if(source.hasOwnProperty(key)){
          result[_length++] = key;
       }
    }
    return result;
}
$(function(){
	$(".inputexts").focus(function(){//input text文字提示语
		if($(this).val()==this.defaultValue){
			$(this).val("");
		}
	});
	$(".inputexts").blur(function(){
		if($(this).val()==''){
			$(this).val(this.defaultValue);
		}
	});

	$('#CheckedAll').click(function(){//全选医院列表
        $('input[name=h-list]').prop('checked',$('#CheckedAll').prop('checked'));
    });
    $('input[name=h-list]').click(function(){//一个没选中，全选就不被选中
        $('#CheckedAll').attr('checked',$('input[name=h-list]:checked').length == $('input[name=h-list]').length);
    });


	$("input[name=choose-supp]").click(function(){//checkbox选中之后插入到choosesupped中
		var supp='i_'+$(this).attr("id");
        var id = supp.substr(supp.lastIndexOf('_') + 1);
        var name = $(this).val();
        var supplier = {};
        supplier = window.parent.$('#index_tabs').data("supplier");
		if($(this).prop("checked") ==1){
			$(".choosesupped").append("<i id='"+supp+"'>"+$(this).val()+"</i>");
            
            if(supplier == undefined || supplier == 'undefined') {
                var supplier = {};
            }
            supplier[id] = {'id' : id, 'name' : name};
		}else{
			$("#"+supp).remove();
            delete supplier[id];
		}
        supplier['html'] = $(".choosesupped").html();
        window.parent.$('#index_tabs').data("supplier", supplier);
	});

	$(".cancelclose").on('click',function(){//取消弹出窗
		parent.$('#win').window('close');
	});

	$(".unfold").on('click',function(){//就诊疗效展开更多
		var othertext=$(this).parent().find(".othertext").text();
		if($(this).hasClass("more2")){
			$(this).removeClass("more2").html("▼展开");
			$(this).parent().find("i").empty();
		}else{
			$(this).addClass("more2").html("▲收起");
			$(this).parent().find("i").append(othertext);
		};
	});
	if($(".othertext").text().length>0){
		$(".unfold").show();
	}else{
		$(".unfold").hide();
	}

	$(".showmore").hover(function(){
		$(this).find("em").show();
	},function(){
		$(this).find("em").hide();
	});

	$(".upfilebtn").click(function(){
		$(this).next("input").click();
	});

});



//上传PC图片
$(function () {

    var showimg = $('#showimg');
    var btn = $(".upfilebtn span");
    if(($("#fileupload").length>0)){
    	$(".upfilebtn").wrap("<form id='myupload' action='"+uploadUrl+"' method='post' enctype='multipart/form-data'></form>");
    }
    $("#fileupload").change(function(){
        $("#myupload").ajaxSubmit({
            dataType:  'json',
            beforeSend: function() {
                btn.html("上传中...");
            },
            success: function(data) { 
				console.log(data);
				if(data.code == -1){                            
					parentReload();
					return false;
				}else if(data.code == 0){
                    var data =data.data;
                    $("input[name='pcImage']").val(data.imgPath);                        
                    showimg.html("<img height='50px' width='50px' src='"+data.filePath+"'></div>");
                    btn.html("添加图片");

                }else{					
					top.$.messager.alert('提示', data.msg);
					btn.html("添加图片");
                }
            },
            error:function(xhr){
				top.$.messager.alert('提示', '上传失败');
				btn.html("添加图片");
            }
        });
    });
    
    //上传h5图片
    var showimgH5 = $('#showimgH5');
    var btnH5 = $(".upfilebtnH5 span");
    if(($("#fileuploadH5").length>0)){
    	$(".upfilebtnH5").wrap("<form id='myuploadH5' action='"+uploadUrl+"' method='post' enctype='multipart/form-data'></form>");
    }
    $("#fileuploadH5").change(function(){
        $("#myuploadH5").ajaxSubmit({
            dataType:  'json',
            beforeSend: function() {
            	btnH5.html("上传中...");
            },
            success: function(data) { 
				console.log(data);
				if(data.code == -1){                            
					parentReload();
					return false;
				}else if(data.code == 0){
                    var data =data.data;
                    $("input[name='h5Image']").val(data.imgPath);                        
                    showimgH5.html("<img height='50px' width='50px' src='"+data.filePath+"'></div>");
                    btnH5.html("添加图片");

                }else{					
					top.$.messager.alert('提示', data.msg);
					btnH5.html("添加图片");
                }
            },
            error:function(xhr){
				top.$.messager.alert('提示', '上传失败');
				btnH5.html("添加图片");
                files.html(xhr.responseText);
            }
        });
    });
    
    $(".delimg").live('click',function(){
        var attachmentId = $(this).attr("rel");
        $.post(delImgUrl,{attachmentId:attachmentId},function(data){
            if(data.code == -1){                            
				parentReload();
				return false;
			}else if(data.code == 0){
                $("input[name='attachmentId']").val();
                files.html(data.msg);
                showimg.empty();
            }else{
                alert(data.msg);
            }
        });
    });
});

//上传报告
$(function () {

    var showimg = $('#showimg');
    var filesReport = $(".filesReport");
    var btn = $(".upfilebtn span");

    if(($("#fileuploadReport").length>0)){
    	
    	if($("#fileuploadReport").data('url').length>0){
    		uploadUrl = $("#fileuploadReport").data('url');
    	}
    	$(".upfilebtn").wrap("<form id='myuploadReport' action='"+uploadUrl+"' method='post' enctype='multipart/form-data'></form>");
    }
    
    
    $("#fileuploadReport").change(function(){
  
        $("#myuploadReport").ajaxSubmit({
            dataType:  'json',
            beforeSend: function() {
                showimg.empty();
                btn.html("上传中...");
            },
            success: function(data) { 
                if(data.code == -1){                            
					parentReload();
					return false;
				}else if(data.code == 0){
                    var data =data.data;
                    $("input[name='newReportWebUrl']").val(data.filePath);                        
                    filesReport.html("<a href='javascript:;' class='delReport'>删除</a>");
                    btn.html("添加附件");

                }else{					
					top.$.messager.alert('提示', data.msg);
					btn.html("添加附件");
                }
            },
            error:function(xhr){
				top.$.messager.alert('提示', '上传失败');
				btn.html("添加附件");
				filesReport.html(xhr.responseText);
            }
        });
    });
    
    $(".delReport").live('click',function(){
        $("input[name='newReportWebUrl']").val('');
        filesReport.html('');
    });
});
// 验证
var validate = {
    
    pattern : function(str, mode) {
        var reg = {
            'email'  : /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9_]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/,
            'integer': /^[0-9]*[1-9][0-9]*$/,
            'mobile' : /^0?1[3|4|5|7|8][0-9]\d{8}$/,
            'phone'  : /^(([0\+]\d{2,3}-)?(0\d{2,3})-)(\d{7,8})(-(\d{3,}))?$/,
            'empty'  : /&nbsp;| |<.*?>/g,
            'age'    : /^[0-9]{1,3}$/,
            'url'    : /^http(s)?:\/\/(([a-zA-Z0-9_-])+(\.)?)*(:\d+)?(\/((\.)?(\?)?=?&?[a-zA-Z0-9_-](\?)?)*)*$/i
        };


        var result;
        switch(mode) {
            case 'email'    :
            case 'integer'  :
            case 'mobile'   :
            case 'phone'    :
            case 'age'      :
			case 'url'      :
                result = reg[mode].test(str);
                break;
            case 'empty'    :
                result = str.replace(reg.empty, '') == '' ? true : false;
                break;
        }
        return result;
    },
    isEmpty : function(str) {
        return this.pattern(str, 'empty');
    },
    isEmail : function(str) {
        return this.pattern(str, 'email');
    },
    isAge : function(str) {
        return this.pattern(str, 'age');
    },
    isInt : function(str) {
        return this.pattern(str, 'integer');
    },
    isMobileNum : function(str) {
        return this.pattern(str, 'mobile');
    },
    isPhoneNum : function(str) {
        return this.pattern(str, 'phone');
    },
    isUrl : function(str) {
        return this.pattern(str, 'url');
    }
};
