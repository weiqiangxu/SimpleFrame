/*jQuery placeholder, fix for IE6,7,8,9*/
var JPlaceHolder = {
    _check : function(){
        return 'placeholder' in document.createElement('input');
    },
    init : function(){
        if(!this._check()){
            this.fix();
        }
    },
    fix : function(){
        jQuery(':input[placeholder]').each(function(index, element) {
            var self = $(this), txt = self.attr('placeholder');
            self.nextAll('.placeholder').remove();
			var pos = self.position(), h = self.outerHeight(true), paddingleft = self.css('padding-left'), paddingtop = self.css('padding-top');
            var holder = $('<span class="placeholder"></span>').text(txt).css({position:'absolute', left:pos.left+1, top:pos.top, height:h, lienHeight:h, padding:self.css('padding'), color:'#aaa'}).appendTo(self.parent());
            self.focusin(function(e) {
                holder.hide();
            }).focusout(function(e) {
                if(!self.val()){
                    holder.show();
                }
            });
            holder.click(function(e) {
                holder.hide();
                self.focus();
            });
        });
    }
};
$(document).ready(function(){
	$('.main').attr('h', $(window).height());
	$('.menu_main .menu2 ul').hide();
	//点击大菜单
	$('.menu_main .menu1 li a').click(function(){
		$('.menu_main .menu2 ul').hide();
		$('.menu_main .menu1 li').removeClass('active');
		var pobj = $(this).parent();
		pobj.addClass('active');
		$.cookie('client_last_menu1', pobj.index(),{ expires: 7, path: '/'});
		if(pobj.attr('to') && $('#'+pobj.attr('to')).length > 0)
		{
			$('#'+pobj.attr('To')).show();
		}
		else
		{
			if ($(this).attr('target')=='main_frame') $('#main_frame').attr('src', $(this).attr('href'));
			$.cookie('client_last_menu2', null,{ expires: 7, path: '/'});
			$('.menu_main .menu2 ul li').removeClass('active');
		}
		
	});
	//点击子菜单
	$('.menu_main .menu2 ul li a').click(function(){
		var pobj = $(this).parent();
		$('.menu_main .menu2 ul li').removeClass('active');
		pobj.addClass('active');
		$.cookie('client_last_menu2', pobj.index(),{ expires: 7, path: '/'});
		if ($(this).attr('target')=='main_frame') $('#main_frame').attr('src', $(this).attr('href'));
	});
	
	if($.cookie('client_last_menu2') != null)
	{//上次点击了子菜单
		var toid = $('.menu_main .menu1 li:eq('+$.cookie('client_last_menu1')+')').attr('to');
		$('.menu_main .menu1 li:eq('+$.cookie('client_last_menu1')+') a:eq(0)').click();
		$('#'+toid+' li:eq('+$.cookie('client_last_menu2')+') a:eq(0)').click();
	}
	else if(($.cookie('client_last_menu1') != null))
	{
		$('.menu_main .menu1 li:eq('+$.cookie('client_last_menu1')+') a:eq(0)').click();
	}
	$('#main_frame').height($(window).height()-87);
	$(window).resize(function(){ $('#main_frame').height($(window).height()-87);});
	if(document.title != '') parent.document.title = document.title;
	$('[confirm]').click(function(){ return confirm($(this).attr('confirm'));});
	/*$('[confirm]').click(function(){
		$.messager.confirm('温馨提示', $(this).attr('confirm'), function(r){return r;});
	});*/
	$('.ClickTr tr').click(function(){ $(this).siblings().removeClass('OnlickTr'); $(this).addClass('OnlickTr'); });
	$('.ReFresh').click(function(){ window.location.reload(); });
	$('.tr_click tr').click(function(){$(this).siblings().removeClass('tr_selected');$(this).addClass('tr_selected');});
	$('.close_page').click(function(){window.opener=null;window.close();});
	$('.autocomplete').attr('title', '可以双击选择');

	/*add by Soul 加载帮助文档*/
	/**
	$('[Helper]').each(function (){
		var TempArr = $(this).attr('Helper').split('_');
		if(TempArr.length == 2)
		{
			var Obj = $(this);
			var FileCode = TempArr[0];
			var HelpId = TempArr[1];
			var GetUrl = ADMIN_URI + 'helper/get_page?code=' +FileCode;
			$.get(GetUrl ,function(AllHelpHtml){
				var Title = $.trim($(AllHelpHtml).filter('#Help_'+HelpId+'').children('.HelpTitle').html());
				var ShotHtml = $.trim($(AllHelpHtml).filter('#Help_'+HelpId+'').children('.HelpShortContent').html());
				var Content = $.trim($(AllHelpHtml).filter('#Help_'+HelpId+'').children('.HelpContent').html());
				var HelpUrl = ADMIN_URI+ 'helper/help?code='+FileCode+'&id='+HelpId;
				var More = '<a href="'+HelpUrl+'" target="_blank"><img src="'+ADMIN_URL+'static/img/more.gif" title="更多'+Title+'"/></a>';
				var Notice = '<a href="'+HelpUrl+'" target="_blank"><img src="'+ADMIN_URL+'static/img/notice.gif" title="'+Title+'"/></a>';
				if(ShotHtml == '' && Content != '')
				{
					//短描述为空,长描述不为空，直接显示问号
					ShotHtml = Notice;
				}
				else
				{
					if(Content != '')
					{
						//长描述不为空，显示更多图标
						ShotHtml += More;
					}
				}
				Obj.html(ShotHtml);
			}, "text");
		}
	});
	**/
});


var AJAX = {
	//同步执行，一般在表单验证时使用
	synchronize: function(atype, aurl, adata){
		var return_data = {};
		adata['isAjax'] = 1;
		$.ajax({
			type: atype,
			url: aurl,
			async: false,
			data: adata,
			dataType: "json",
			success:function(data){
				return_data = data;
			}
		});
		return return_data;
	}
};

function ChangeCheckCode(ToId)
{
	$('#'+ToId+'').attr('src', ADMIN_URI+'/user/checkcode?rand='+Math.random());
}

/*
*分页使用
*/
function SetCookiePage(value)
{
	$.cookie('Count',value,{ expires: 7 }); 
}


/*
*全选/全不选
*/
function SelectAll(Obj, Name)
{
	$("input[name='"+Name+"']:not(:disabled)").prop('checked', Obj.checked);
}

/*
*反选
*/
function Inverse(Name)
{
	$("input[name='"+Name+"']:not(:disabled)").each(function(){
		$(this).prop('checked', !$(this).prop('checked'));
	});
}

/*
*监听复选框并给选中的表格添加选中样式
*/
function ListenCheckbox(Name)
{
	$("input[name='"+Name+"']").change(function(){
		if($(this).prop('checked')){
			$(this).parent().parent().addClass('selectbg');
		}else{
			$(this).parent().parent().removeClass('selectbg');
		}
	});
}

/*
*给选中的值
array
*/
function GetCheckboxVal(Name)
{
	var Val = new Array();
	$("input[name='"+Name+"']:checked").each(function(){
		Val.push(this.value);
	});
	return Val;
}

/*
*检查值是否在json中，如果存在则返回ID
*只适合一维
*/
function InJson(SearchVal, Json)
{
	SearchVal = $.trim(SearchVal.toUpperCase());
	for(var Key in Json)
	{
		if($.trim(Json[Key].toUpperCase()) == SearchVal) return Key;
	}
	return false;
}

//验证码
var IMGCODE = {
	change:function(toid, type){
		$('#'+toid+'').attr('src', '/index/imgcode?type='+type+'&t='+Math.random());
	},
	check:function(val){
		return AJAX.synchronize("post", "/index/checkImgcode", {"val": val});
	}
};

var MOBILECODE = {
	//mobile手机号码, code验证码, act功能(bind|unbind|password)
	check:function(mobile, code, act){
		return AJAX.synchronize("post", "/index/checkMobilecode", {'mobile': mobile, 'code': code, 'act':act});
	}
};

//常用JS验证
var VERIFICATION = {
	email: function(val){ return (/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/).test(val); },
	imgcode: function(val){ return (/^[0-9a-zA-Z]{5}$/).test(val); },
	username: function(val){ return (/^[a-zA-Z][a-zA-Z0-9_]{3,19}$/).test(val); },
	mobile: function(val){ return (/^1\d{10}$/).test(val); },
	mobile_code: function(val){ return (/^\d{6}$/).test(val); }
};

/**
  * @method 消息弹窗
  * @author soul 2017/6/16
  * @example MESSAGE.alert('aa');
			 MESSAGE.confirm('确定要删除此记录吗', "test(1,2)"); callback为回调方法的字符串
			 MESSAGE.show('SimpleFrame商城用户注册协议', '协议内容', '<button type="button" class="btn btn-primary">确定</button><button type="button" class="btn btn-default message_close">取消</button>');
  */
var MESSAGE = {
	alert: function(msg){
		MESSAGE.allType('alert', '消息提示', msg, '', '');
	},
	//tarId【缺省】指定加载的标签ID
	show:function(title, msg, foot, tarId){
		if(!arguments[3]) var tarId = '';
		MESSAGE.allType('show', title, msg, foot, tarId);
	},
	confirm: function(msg, callback){
		MESSAGE.allType('confirm', '温馨提醒', msg, callback, '');
	},
	ajaxError: function(json){
		var errorMsg = '';
		if(json.hasOwnProperty("status") && json.status){
		}else if(json.hasOwnProperty("code")){
			if(json.code == 'noLogin'){
				URL.login();
				return false;
			}
			errorMsg = json.data;
		}else{
			errorMsg = '网络异常，请稍后重试！';
		}

		if(errorMsg != ''){
			if($('.message_box:visible').length > 0){
				alert(json.data);
			}else{
				MESSAGE.allType('alert', '消息提示', json.data, '', '');
			}
			return false;
		}
		return true;
	},
	//可以移动的
	moveBox: function(title, msg, foot, tarId){
		if(!arguments[3]) var tarId = '';
		MESSAGE.allType('move', title, msg, foot, tarId);
	},
	allType: function(type, title, msg, foot, tarId){
		var randClass = '';
		if(tarId == '')
		{
			randClass = ' message_rand';
			tarId = 'massage_'+ (new Date().getTime() + Math.ceil(Math.random()*1000000));
		}

		$('.message_box:not("#'+tarId+'")').hide();
		if($('#'+tarId).length < 1)
		{
			if(type == 'confirm'){
				var footHtml = '<div class="massage_foot"><button type="button" class="btn btn-primary confirmOk">确定</button><button type="button" class="btn btn-default message_close">取消</button></div>';
			}else{
				var footHtml = foot == ''? '': '<div class="massage_foot">'+foot+'</div>';
			}
			var tplHtml = '<div class="message_box message_'+type+randClass+'" id="'+tarId+'">'
				+'<div class="message_head"><h4 class="message_title">'+title+'</h4><button type="button" class="close message_close"><span aria-hidden="true">×</span></button><div class="clear"></div></div>'
				+'<div class="massage_body">'+msg+'</div>'+footHtml+'</div>';
		   $('body').append(tplHtml);
		}
		if(type != 'move') $('#mask_layer').show();
		$('#'+tarId).show();
		MESSAGE.resize(tarId);
		$(window).resize(function(){MESSAGE.resize(tarId);});
		$('#'+tarId+' .message_close').click(function(){MESSAGE.hide()});
		if(type != 'move') $('#mask_layer').click(function(){MESSAGE.hide()});
		if(type == 'confirm')
		{
			$('.confirmOk').click(function(){
				MESSAGE.hide();
				eval(foot);
			});
		}
	},
	hide: function(){
		$('.message_box.message_rand').remove();
		$('.message_box').hide();
		$('#mask_layer').hide();
	},
	resize: function(tarId){
		var top = ( $(window).height() - $('#'+tarId).height() )/2;
		var left = ( $(window).width() - $('#'+tarId).width() )/2;
		$('#'+tarId).css({"top": top,"left": left });
	}
};

/**
  * @method URL 跳转、获取其参数值....
  * @author soul 2017/6/16
  */
var URL = {
	go:function(url){
		url = url == ''? URL.getParam('goto'): url;
		url = url == null || url == ''? '/': url;
		window.location.href = url;
	},
	getParam: function(name){
		var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i"); 
		var r = window.location.search.substr(1).match(reg);
		if (r != null) return unescape(r[2]); 
		return null; 
	}
};

/**
  * @method 分页处理
  * @author soul 2017/6/16
  */
var PAGE = {
	jump:function(){
		window.location.href = $('.to_page_txt:eq(0)').attr('base').replace('-_-page_-_', $('.to_page_txt:eq(0)').val());
	},
	perPage:function(perPage){
		$.cookie('perPage', perPage, {expires:7});
		location.reload();
	}
}

/**
  * @method 可收拢容器
  * @author soul 2017/6/16
  */
var EXTENDBOX = {
	//初始化关闭打开事件....
	init: function(){
		$('.extend_closed .extend_body').hide();
		$('.extend_box .open_close').click(function(){
			var obj = $(this).closest('.extend_box');
			if(obj.hasClass('extend_closed'))
			{
				obj.removeClass('extend_closed').addClass('extend_opened');
				obj.find('.extend_body').show();
			}
			else
			{
				obj.removeClass('extend_opened').addClass('extend_closed');
				obj.find('.extend_body').hide();
			}
		});
	}
};


/**
  * @method 文件上传
  * @author soul 2017/6/16
  * @example var params = {
				pickId: 'btnid',//按钮
				tarImg: 'imgid',//图片
				tarVal: 'valid',//接收返回值
				postData: {site:'yp|b2c',cate: "product"....},site可以缺省，默认为b2c;
				okFun: uploadOk,
				errFun: uploadErr,
			};
			UPLOAD.img(params);
  */
var UPLOAD = {
	//params {}
	img: function(params){
		if(typeof(WebUploader) == "undefined") return false;
		params.postData = params.hasOwnProperty("postData")? params.postData: {};
		uploader = WebUploader.create({
			auto: true,
			compress: false,
			duplicate: true,
			swf: ADMIN_URI+'static/ueditor/Uploader.swf',
			server: ADMIN_URI+'upload/uploadImg?isAjax=1',
			pick: '#'+params.pickId,
			formData: params.postData,
			accept: {
				title: '图片上传',
				extensions: 'gif,jpg,jpeg,bmp,png',
				mimeTypes: 'image/jpg,image/jpeg,image/png'
			},
			fileVal : "file",
			fileSingleSizeLimit: 3145728
		});
		//上传前loading
		uploader.on( 'fileQueued', function( file) {
			if(params.hasOwnProperty("beforeFun")){
				params.beforeFun(file, params);
			}else if(params.hasOwnProperty("tarImg")){
				$('#'+params.tarImg).attr('src', ADMIN_URI+'static/images/loading.gif');
			}
		});
		// 文件上传成功，给item添加成功class, 用样式标记上传成功。
		uploader.on( 'uploadSuccess', function( file , data) {
			if(params.hasOwnProperty("okFun")){
				params.okFun(file, data, params);
			}else if(data.status){
				if(params.hasOwnProperty("tarImg")){
					$('#'+params.tarImg).attr('src', data.data.url);
				}
				if(params.hasOwnProperty("tarVal")){
					$('#'+params.tarVal).val(MYJSON.toSting(data.data));
				}
			}else{
				MESSAGE.alert('图片上传失败，请稍后重试！'+data.data);
			}
		});
		//上传前出错
		uploader.on('error', function( type ){
			if (type=="Q_TYPE_DENIED"){
				MESSAGE.alert("请选择 gif,jpg,jpeg,bmp,png 格式的图片");
			}else if(type=="Q_EXCEED_SIZE_LIMIT"){
				MESSAGE.alert("图片大小不能超过3M");
			}
		});
		//上传出错
		uploader.on( 'uploadError', function( file, reason) {
			if(params.hasOwnProperty("errFun")){
				params.errFun(file, reason, params);
			}else{
				MESSAGE.alert('图片上传失败，请稍后重试！'+reason);
			}
		});
	}
};

/**
  * @method JSON 字符串 和 对象的互转
  * @author soul 2017/6/16
  */
var MYJSON = {
	toSting: function(json){
		return JSON.stringify(json);
	},
	toJson: function(string){
		return JSON.parse(string);
	}
};

/**
  * @method 字符串处理
  * @author soul 2017/6/30
  */
var STRING = {
	formatNum: function(str){
		return str.replace(/[^0-9a-zA-Z]/ig, '');
	},
	simple: function(str){
		return str.replace(/[^0-9a-zA-Z\u4e00-\u9fa5]/ig, '');
	},
	toEn: function(str){
		return str.replace(/[^0-9a-zA-Z]/ig, '');
	},
	toCnEn: function(str){
		return str.replace(/[^0-9a-zA-Z\u4e00-\u9fa5]/ig, '');
	}
};

var ARRAY = {
	unique: function(arr){
		arr.sort();
		var resarr =new Array();
		var temp = '';
		for(var i in arr)
		{
			if(arr[i] != temp && arr[i] != '')
			{
				resarr.push(arr[i]);
				temp = arr[i];
			}
		}
		return resarr;
	},
	merge: function(arr1, arr2){
		return $.merge(arr1, arr2);
	},
	has:function(val, arr){
		return $.inArray(val, arr);
	}
};

/**
  * @method checkbox  select  radio 的操作
  * @author soul 2017/6/30
  */
var INPUT = {
	checkAll: function(obj, toName){
		$('[name="'+toName+'"]').prop('checked', $(obj).prop('checked'));
	},
	getCheckedVal: function(toName){
		var val = new Array(); 
		$('[name="'+toName+'"]:checked').each(function(){ 
			val.push($(this).val()); 
		}); 
		return val;
	}
};