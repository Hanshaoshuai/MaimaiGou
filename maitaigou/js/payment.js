$(function(){
	payment();
	app();
	nav();
})
 function payment(){
	$("#payment_uls").find("a").on('click',function(){
		$(this).addClass('payment_flag').parent().siblings().children("a").removeClass("payment_flag");
	})
 }

function app() {
	 $('.app').children("a").on('click',function(){
 		$('.app').hide();
 	})
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