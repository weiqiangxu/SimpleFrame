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

var LOADINGIMG = '<img src="/static/images/loading.gif">';
$(document).ready(function(){
	if(typeof(index_page) != "undefined" && index_page == 1) $('.index_page').removeClass('hide');
	//登录检测
	USERSTATUS.login();
	//自动提示
	JPlaceHolder.init();
	// 加载右侧导航条
	if(typeof(rightBar) != "undefined" && rightBar == 1) RIGHTBAR.ajaxLoad();
	//技术支持与报障
	$('#erviceEmail').html('admin@yiparts.com');
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
			dataType: "jsonp",
			success:function(data){
				return_data = data;
			}
		});
		return return_data;
	}
};

//用户状态
var USERSTATUS = {
	//用户登录状态
	login: function(){
		$.ajax({
			type: 'get',
			url: HOMEURL+'user/checkLogin?isAjax=1',
			dataType: "jsonp",
			success:function(data){
				if(MESSAGE.ajaxError(data)){
					if(data.data && data.data.username){
						var html = '<span>嗨，<a href="'+HOMEURL+'user/index">'+data.data.username+'</a>&nbsp;&nbsp;欢迎来到SimpleFrame商城!</span>'
						+'<a href="'+HOMEURL+'user/message" target="_blank" class="left">消息<b class="msgCount">'+data.data.msgCount+'</b></a>'
						+'<a href="'+HOMEURL+'user/logout" class="left">[退出]</a>';
						$('#top1:eq(0) .left_menu').html(html);
					}
				}
			}
		});
	}
};

//验证码
var IMGCODE = {
	change:function(toid, type){
		$('#'+toid+'').attr('src', HOMEURL+'index/imgcode?type='+type+'&t='+Math.random());
	},
	check:function(val){
		return AJAX.synchronize("post", HOMEURL+"index/checkImgcode", {"val": val});
	}
};

var MOBILECODE = {
	//mobile手机号码, code验证码, act功能(bind|unbind|password)
	check:function(mobile, code, act){
		return AJAX.synchronize("post", HOMEURL+"index/checkMobilecode", {'mobile': mobile, 'code': code, 'act':act});
	}
};
// 加载右侧导航条
var RIGHTBAR = {
    ajaxLoad : function(){
		$.ajax({
			type: 'get',
			url: HOMEURL+'user/refreshRightBar?isAjax=1',
			dataType: "jsonp",
			success:function(res){
				if(MESSAGE.ajaxError(res)){
					$('body').append(res.data);
					// 右侧导航条的js事件
					RIGHTBAR.rightBarJs();
				}
			}
		});
	},
	rightBarJs:function(){
		$(".quick_links li a").mouseover(function(){
			$(this).parents('li').find(".mp_tooltip").css('visibility','visible');
			$(this).parents('li').find(".mp_tooltip").addClass('animated pulse');
		});
		$(".quick_links li a").mouseout(function(){
			$(this).parents('li').find(".mp_tooltip").css('visibility','hidden');
			$(this).parents('li').find(".mp_tooltip").removeClass('animated pulse');
		});
		$(".quick_links_panel .goToTop li a").mouseover(function(){
			$(this).parents('li').find(".mp_tooltip").css('visibility','visible');
			$(this).parents('li').find(".mp_tooltip").addClass('animated pulse');
		});
		$(".quick_links_panel .goToTop li a").mouseout(function(){
			$(this).parents('li').find(".mp_tooltip").css('visibility','hidden');
			$(this).parents('li').find(".mp_tooltip").removeClass('animated pulse');
		});
		//置顶按钮  
		$(window).scroll(function(){
			if($(document).scrollTop()>160){  
		    	$('.goToTop').fadeIn();  
		    }else{  
		        $('.goToTop').fadeOut();  
			}
		});
	}
};

//常用JS验证
var VERIFICATION = {
	email: function(val){ return (/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/).test(val); },
	imgcode: function(val){ return (/^[0-9a-zA-Z]{5}$/).test(val); },
	username: function(val){ return (/^[a-zA-Z][a-zA-Z0-9_]{3,19}$/).test(val); },
	mobile: function(val){ return (/^1\d{10}$/).test(val); },
	mobile_code: function(val){ return (/^\d{6}$/).test(val); },
	//强制保留小数点位数
	floatNum:function(num, count){
		var f = parseFloat(num);
        if (isNaN(f))  return false;
        var f = Math.round(num*100)/100;
        var s = f.toString();
        var rs = s.indexOf('.');
        if (rs < 0) {
            rs = s.length;
            s += '.';
        }
        while (s.length <= rs + count) {
            s += '0';
        }
        return s;
	},
	check: function(){
		$('[check]').blur(function(){
			var obj = $(this);
			var anyCheck = obj.attr('check').split(' ');
			var val = $.trim(obj.val());
			var error = false;
			for(var i in anyCheck)
			{
				var type = anyCheck[i].split(':');
				switch(type[0])
				{
					//price:0.01:9999.99
					case 'price':
						var min = parseFloat(type[1]);
						if(!(/^\d+(\.\d{0,2})?$/).test(val) || val < min){
							val = min;
						}else if(type.length == 3){
							var max = parseFloat(type[2]);
							if(val > max) val = max;
						}
						obj.val(VERIFICATION.floatNum(val, 2));
					break;
					//number:1:99
					case 'number':
						var min = parseInt(type[1]);
						if (!(/^\d+$/).test(val) || val < min){
							val = min;
						}else if(type.length == 3){
							var max = parseInt(type[2]);
							if(val > max) val = max;
						}
						obj.val(val);
					break;
					case 'float':
						var min = parseFloat(type[1]);
						if(!(/^\d+(\.\d+)?$/).test(val) || val < min){
							val = min;
						}else if(type.length == 3){
							var max = parseFloat(type[2]);
							if(val > max) val = max;
						}
						obj.val(val);
					break;
				}
			}
		});
	}
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
	},
	setParam:function (OldUrl, SetParaArr){
		OldUrl = OldUrl == '' ? window.location.href: OldUrl;
		//取描点
		var Target = '';
		if(OldUrl.indexOf('#') != -1)
		{
			Target = OldUrl.substr(OldUrl.indexOf('#'));
			OldUrl = OldUrl.substr(0, OldUrl.indexOf('#'));
		}
		var OldParaStr = '';
		var BaseUrl = OldUrl;
		if(OldUrl.indexOf("?") != -1)
		{//存在
			OldParaStr = OldUrl.substr(OldUrl.indexOf("?") + 1);
			BaseUrl = OldUrl.substr(0, OldUrl.indexOf("?"));
		}
		var NewParaArr = new Array();
		var TempArr = OldParaStr.split('&');
		for(var I in TempArr)
		{
			var Temp = TempArr[I].split('=');
			if(Temp[0] != '')
			{
				NewParaArr[Temp[0]] = Temp.length > 1? Temp[1]: '';
			}
		}

		for(var I in SetParaArr)
		{
			NewParaArr[I] = SetParaArr[I];
		}

		var TempArr = new Array();
		for (var I in NewParaArr)
		{
			if(NewParaArr[I] != '')
			{
				TempArr.push(I+'='+NewParaArr[I]);
			}
		}
		return BaseUrl+'?'+TempArr.join('&')+Target;
	},
	login:function(){
		URL.go(HOMEURL+'user/login?goto='+encodeURIComponent(window.location.href));
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
			swf: HOMEURL+'static/ueditor/Uploader.swf',
			server: HOMEURL+'upload/uploadImg?isAjax=1',
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
				$('#'+params.tarImg).attr('src', HOMEURL+'static/images/loading.gif');
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
	//valType： number || string 【默认】
	unique: function(arr, valType){
		if(!arguments[1]) var valType = 'string';
		if(valType == 'number'){
			arr.sort(function(a,b){return a - b;});
		}else{
			arr.sort();
		}
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
		$('[name="'+toName+'"]:visible').prop('checked', $(obj).prop('checked'));
	},
	getCheckedVal: function(toName){
		var val = new Array();
		$('[name="'+toName+'"]:checked').each(function(){
			val.push($(this).val());
		});
		return ARRAY.unique(val);
	},
	getNoCheckedVal: function(toName){
		var val = new Array();
		$('[name="'+toName+'"]').not('input:checked').each(function(){
			val.push($(this).val());
		});
		return ARRAY.unique(val);
	}
};

/**
  * @method IM 联系
  * @author soul 2017/6/30
  */
var YPIM = {
	//调起IM联系 fromUid, toUid, toShopId可以不填
	//买家联系卖家<a href="javascript:" class="icon icon_contact" onclick="YPIM.contact('111', '222', '1212');return false;" title="点此可以直接和卖家交流噢。">和我联系</a>
	//卖家联系买家<a href="javascript:" class="icon icon_contact" onclick="YPIM.contact('222', '111');return false;" title="点此可以直接和买家交流噢。">和我联系</a>
	contact: function (fromUid, toUid, toShopId){
        var _toShopId = ("undefined" == typeof toShopId) ? 0 : toShopId;
		//alert('some code ....');
        window.open ("/static/im/chat.html?f="+fromUid+'&t='+toUid+'&s='+_toShopId);
	}
}


/**
  * @method 车型库选择
  * @author soul 2017/6/30
  */
var MODELBOX = {
	show: function(){
		$('#mask_layer').show();
		if($('#MODELBOX').length < 1)
		{
			$('body').append('<div id="MODELBOX"><div class="mainBox">'+LOADINGIMG+'</div><a class="closeBtn" onclick="MODELBOX.hide()" title="关闭"><i class="icon2 icon2_close"></i></a></div>');
		}
		$('#MODELBOX').show();
		var lastSelect = {};
		if($.cookie('MODELBOX_VAL'))
		{
			var temp = $.cookie('MODELBOX_VAL').split(';');
			for(var i in temp)
			{
				var temp2 = temp.split(':');
				lastSelect[temp2[0]] = temp2[1];
			}
		}
		if(lastSelect['mod2Id'] && lastSelect['mod2Id'] > 0){
			MODELBOX.showModel(3, lastSelect['mod2Id']);
		}else if(lastSelect['mod1Id'] && lastSelect['mod1Id'] > 0){
			MODELBOX.showModel(2, lastSelect['mod1Id']);
		}else if(lastSelect['brandId'] && lastSelect['brandId'] > 0){
			MODELBOX.showModel(1, lastSelect['brandId']);
		}else{
			MODELBOX.showBrand();
		}
	},
	showBrand:function(){
		if($('#MODELBOX .modelBrand').length > 0){
			$('#MODELBOX .modelBrand').show();
			return false;
		}
		$('#MODELBOX .mainBox').html(LOADINGIMG);
		$.ajax({
			type: 'get',
			url: HOMEURL+'model/getModelBoxBrand?isAjax=1',
			dataType: "jsonp",
			success:function(data){
				if(MESSAGE.ajaxError(data)){
					$('#MODELBOX .mainBox').html(data.data);
					var brandObj = $('#MODELBOX .modelBrand');
					brandObj.find('li[initial] a').unbind('click').click(function(){
						brandObj.find('.initialList .selected').removeClass('selected');
						$(this).addClass('selected');
						var initial = $(this).parent('li').attr('initial');
						switch(initial)
						{
							case 'all': brandObj.find('.selectOption li').show();break;
							default:
								brandObj.find('.selectOption li').hide();
								brandObj.find('.selectOption li[initial="'+initial+'"]').show();
							break;
						}
					});

					brandObj.find('.searchTxt').unbind('click').bind("keyup click", function (){
						brandObj.find('.initialList .selected').removeClass('selected');
						var keyword = $.trim($(this).val()).toUpperCase();
						if(keyword == '')
						{
							brandObj.find('.selectOption li').show();
							brandObj.find('.initialList .all a').addClass('selected');
						}
						else
						{
							brandObj.find('.selectOption li').hide();
							brandObj.find('.selectOption li').each(function(){
								if($(this).attr('initial').indexOf(keyword) != -1 || $(this).attr('word').toUpperCase().indexOf(keyword) != -1)
								{
									$(this).show();
								}
							});
						}
					});

					brandObj.find('.selectOption li a').unbind('click').click(function (){
						MODELBOX.showModel(1, $(this).parent().attr('valid'));
					});
				}
			}
		});
	},
	showModel:function(level, pId){
		if($('#MODELBOX .model'+level).length > 0){
			$('#MODELBOX .model'+level).show();
			return false;
		}
		if(level == 3){
			$('#MODELBOX .mainBox .M3List').html(LOADINGIMG);
		}else{
			$('#MODELBOX .mainBox').html(LOADINGIMG);
		}
		$.ajax({
			type: 'get',
			url: HOMEURL+'model/getModelBox?isAjax=1&level='+level+'&pId='+pId,
			dataType: "jsonp",
			success:function(data){
				if(MESSAGE.ajaxError(data)){
					switch(level)
					{
						case 1:
							$('#MODELBOX .mainBox').html(data.data);
							$('.makeList li[mid]').unbind('click').click(function(){
								$('.makeList .active').removeClass('active');
								$(this).addClass('active');
								var mid = $(this).attr('mid');
								if(mid == 'all'){
									$('[tomid]').show();
								}else{
									$('[tomid]').hide();
									$('[tomid="'+mid+'"]').show();
								}
							});
							if($('.makeList .active').length < 1) $('.makeList li[mid]:eq(0)').click();
							$('.M1List li[m1id]').unbind('click').click(function(){
								MODELBOX.showModel(2, $(this).attr('m1id'));
							});
						break;
						case 2:
							$('#MODELBOX .mainBox').html(data.data);
							$('.M2List li[m2id]').unbind('click').click(function(){
								$('.M2List .active').removeClass('active');
								$(this).addClass('active');
								MODELBOX.showModel(3, $(this).attr('m2id'));
							});

							//默认
							if($('.M2List .active').length < 1) $('.M2List li[m2id]:eq(0)').click();
						break;
						case 3:
							$('#MODELBOX .mainBox .M3List').html(data.data);
						break;
					}
				}
			}
		});
	},
	hide:function(){
		$('#MODELBOX').hide();
		$('#mask_layer').hide();
	}
};



/**
  * @method 店铺顶部搜索
  * @author soul 2017/9/13
  */
 function submitShopSearch(url)
{
	if($('#shopTopSearch input[name="keyword"').val() == '')
	{
		$('#shopTopSearch input[name="keyword"').focus();
		return false;
	}
	$('form[name="shopTopSearch"]').attr('action', url);
	shopTopSearch.submit();
}

/**
  * @method 初始化主要搜索表单
  * @author soul 2017/9/13
  */
function InitMainSearchFrom()
{
	var placeholder = {"number":"可输入配件的OE编码或者参考编码", "vin":"请输入行驶证上的17位车架号", "item":"例如：本田雅阁刹车片", "shop":"可输入店铺名称、掌柜会员名等"};
	var formObj = $('form[name="mainSearchForm"]');
	formObj.find('.searchTab li').click(function(){
		var obj = $(this);
		var type = obj.attr('stype');
		obj.siblings('.searchTabselect').removeClass('searchTabselect');
		obj.addClass('searchTabselect');
		formObj.attr('class', 'search-color-'+type);
		formObj.find('input[name="type"]').val(type);
		formObj.find('input[name="keyword"]').prop('placeholder', placeholder[type]);
		switch(type)
		{
			case 'shop':
				formObj.attr('action', "/search/shop");
			break;
			default:
				formObj.attr('action', "/search/index");
			break;
		}
		if($.cookie('search'+type)){
			formObj.find('input[name="keyword"]').val($.cookie('search'+type));
		}else{
			formObj.find('input[name="keyword"]').val('');
		}
	});

	if($.cookie('searchType') && $.cookie('searchType') != ''){
		formObj.find('.searchTab li[stype="'+$.cookie('searchType')+'"]').click();
	}else{
		formObj.find('.searchTab li:eq(0)').click();
	}
}

function mainSearchFormSubmit()
{
	var formObj = $('form[name="mainSearchForm"]');
	if($.trim(formObj.find('input[name="keyword"]').val()) == ''){
		formObj.find('input[name="keyword"]').focus();	
		return false;
	}
	mainSearchForm.submit();
}


/**
 * @method 卖家退款
 * @author xu
 * @copyright 2017-10-25
 */
var AFTERSALEUPLODER = {
	submit:function(formId){
		
	},
	initImg: function(){
		// 给点击按钮添加点击上传图片的事件
		// 设置参数
		var params = {
			// 点击按钮ID值
			pickId: 'aftSaluploader',
			// 上传完成后图片显示容易的ID值
			tarImg: 'aftSaluploader',
			// post到传递数据的PHP接口
			postData: {cate: 'product'},
			// 上传成功回调动作
			okFun: AFTERSALEUPLODER.uploadOk,
			// 上传失败的回调动作
			errFun: AFTERSALEUPLODER.uploadErr,
			// 上传之前的动作
			beforeFun: AFTERSALEUPLODER.uploadBefore
		};
		// 参数传递给common.js的UPLOAD对象实现绑定上传图片 事件
		UPLOAD.img(params);
	},
	// 上传之前
	uploadBefore:function(file, params){
	},
	//上传成功
	uploadOk: function(file, data, params){
	},
	//上传失败
	uploadErr:function(file, reason, params){
		MESSAGE.alert('图片上传失败，请稍后重试！'+reason);
	}
}


/**
 * @method 切换特卖场显示广告（前五个后五个）
 * @author xu
 * @copyright 2017-11-16
 */
function toggleAdsSuper(type)
{
    if(type=='left')
    {
        $(".ads-super-left").css('display','block');
        $(".ads-super-right").css('display','none');
    }
    if(type=='right')
    {
        $(".ads-super-left").css('display','none');
        $(".ads-super-right").css('display','block');
    }
}