$(function() {
	handover();
	app();
	nav();
})
//点击切换
function handover() {
	$('#site').on('click', function() {
		$('#wicket').show();
		$('body').addClass('boy');
	})

	$('.delete').on('click', function() {
		$('#wicket').hide();
		$('.switchover').hide();
		$('body').removeClass('boy');
	})
	$('.zhezhao_a_2_btn').on('click', function(event) {
		if($(this).siblings('ul').css("display") === "none") {
			$(this).siblings('ul').show();
			$(this).siblings('ul').children().on('mouseenter', function() {
				$(this).addClass("list_li").siblings('li').removeClass('list_li');
			}).on('click', function() {
				var html = $(this).html();
				$(this).parent().hide().children().removeClass("list_li");
				$(this).parent().siblings('div').children("p").html(html);
			})
		} else {
			$(this).siblings('ul').hide().children("li").removeClass("list_li");
		}
		$(this).parent().siblings('div').find("ul").hide();
		cancel_Bubble(event);
	})
	$('body').on('click', function() {
		$('.zhezhao_a_2_btn').siblings('ul').hide().children("li").removeClass("list_li");
	});
	//点击切换按钮后  发送请求
	$(".b_flag").on('click', function() {
		$('.switchover').show();
		$('body').addClass('boy');
		$.ajax({
			type: "get",
			url: "",
			async: true
		});
	})
	//请求完成后  才能执行这里的代码
	$('#site_ul').children("li").on('click', function() {
		$(this).addClass('sitebtn').siblings("li").removeClass("sitebtn");
		$(this).children("button").removeAttr("disabled");
		$(this).siblings("li").children("button").attr("disabled", "disabled");
	})
	$('.btn').on('click', function(event) {
		cancel_Bubble(event);
		var arr = [];
		var spans = $(this).siblings("p").children('span');
		for(var i = 0; i < spans.length; i++) {
			arr.push($(spans[i]).html());
		}
		var siteSpan = $("#order_site_span").find('span');
		for(var i = 0; i < siteSpan.length; i++) {
			$(siteSpan[i]).html(arr[i]);
		}
		$('.switchover').hide();
		$('body').removeClass('boy');
	})
}
//app下载
function app() {
	 $('.app').children("a").on('click',function(){
 		$('.app').hide();
 	})
}
//取消冒泡
function cancel_Bubble(event) {
	//如果事件对象存在
	var event = event || window.event;
	// w3c的方法
	if(event && event.stopPropagation) {
		event.stopPropagation();
	} else {
		// ie的方法
		event.cancelBubble = true;
	}
	return false;
}

//导航下的轮播图
function nav () {
	$('.header_3_a1').on('mouseenter',function(){
		$('.show').stop().animate({
			"top":'188px'
		},500);
	})
	$('.header_30').on('mouseleave',function(){
		$('.show').stop().animate({
			"top":'28px'
		},500);
	})
	
	$('.show_ul').children("li").on('mouseenter',function () {
		$(this).children('span').stop().animate({
			'height':'100%',
			'line-height':'140px'
		},500)
	})
	$('.show_ul').children("li").on('mouseleave',function () {
		$(this).children('span').stop().animate({
			'height':'40px',
			'line-height':'40px'
		},500)
	})
	var index = 0;
	var sum = -182;
	var num = $('.show_ul').children("li")
	$(".next").on('click',function(event){
		index++;
		if(index > num.length - 6){
			index = num.length - 6;
			alert("已经是最后一张了");
			return;
		}
		$('.show_ul').stop().animate({
			"left":sum * index + "px"
		},400)
		cancel_Bubble(event)
	})
	
	$(".prev").on('click',function(event){
		index--;
		if(index < 0){
			index = 0;
			alert("已经是第一张了");
			return;
		}
		$('.show_ul').stop().animate({
			"left":sum * index + "px"
		},400)
		cancel_Bubble(event)
	})
}

//取消冒泡
function cancel_Bubble(event) {
	//如果事件对象存在
	var event = event || window.event;
	// w3c的方法
	if(event && event.stopPropagation) {
		event.stopPropagation();
	} else {
		// ie的方法
		event.cancelBubble = true;
	}
	return false;
}