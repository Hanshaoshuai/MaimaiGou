$(function(){
	handover();
	app();
	nav();
//	focusBanner();
})
//点击切换
function handover(){
	$("#recli").children('li').on('click',function(){
		$(this).addClass("active").siblings().removeClass("active");
	})
	
	$("#fyul").children('li').on('click',function(){
		$(this).addClass("active").siblings().removeClass("active");
		if($(this).attr("data-id") && $(this).attr('class')){
			if($(this).attr("data-id") == "1"){
				$(this).children(".reclassify_b_up").addClass("up").siblings(".reclassify_b_down").removeClass("down");
				$(this).attr("data-id",'2');
			}else if($(this).attr("data-id") == "2"){
				$(this).children(".reclassify_b_down").addClass("down").siblings(".reclassify_b_up").removeClass("up");
				$(this).attr("data-id",'1');
			}
		}else{
			$(this).siblings().children(".reclassify_b_up").removeClass("up").siblings(".reclassify_b_down").removeClass("down");
		}
	})
	
	
}
//app下载
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

	//轮播
	var focusBanner=function(){
		var $focusBanner=$("#focus-banner"),
		$bannerList=$("#focus-banner-list li"),
		$focusHandle=$(".focus-handle"),
		$bannerImg=$(".focus-banner-img"),
		$nextBnt=$("#next-img"),
		$prevBnt=$("#prev-img"),
		$focusBubble=$("#focus-bubble"),
		bannerLength=$bannerList.length,
		_index=0,
		_timer="";

		var _height=$(".focus-banner-img").find("img").height();
		$focusBanner.height(_height);
		$bannerImg.height(_height);

		$(window).resize(function(){
			window.location.reload()
		});

		for(var i=0; i<bannerLength; i++){
			$bannerList.eq(i).css("zIndex",bannerLength-i);
			$focusBubble.append("<li></li>");
		}
		$focusBubble.find("li").eq(0).addClass("current");
		var bubbleLength=$focusBubble.find("li").length;
		$focusBubble.css({
			"width":bubbleLength*28,
			"marginLeft":-bubbleLength*11
		});//初始化

		$focusBubble.on("click","li",function(){
			$(this).addClass("current").siblings().removeClass("current");
			_index=$(this).index();
			changeImg(_index);
		});//点击轮换

		function changeImg(_index){
			$bannerList.eq(_index).fadeIn(250);
			$bannerList.eq(_index).siblings().fadeOut(200);
			$focusBubble.find("li").removeClass("current");
			$focusBubble.find("li").eq(_index).addClass("current");
			clearInterval(_timer);
			_timer=setInterval(function(){
				_index++
				if(_index>bannerLength-1){
					_index=0;
				}	
				changeImg(_index);
			},3000)
		}//切换主函数
		_timer=setInterval(function(){
			_index++
			if(_index>bannerLength-1){
				_index=0;
			}
			changeImg(_index);
		},3000)
	}();