$(function(){
	nav();
	pinjia();
	showMessage();
	app();
})

//商品详情+评价
function pinjia (){
	$("#li1").on('click',function(){
		$(this).addClass('introduce_li').siblings("li").removeClass('introduce_li');
		$(".details_introduce").removeClass("details_flag");
		$('.details_div').show();
		$('.introduce_div').hide();
	})
	$("#li2").on('click',function(){
		$(this).addClass('introduce_li').siblings("li").removeClass('introduce_li');
		$(".details_introduce").addClass("details_flag");
		$('.details_div').hide();
		$('.introduce_div').show();
	})
	
	
	$(".intU").children("li").on('click',function(){
		$(this).addClass("introduce_divs_lis").siblings('li').removeClass('introduce_divs_lis');
		var div = $(this).parent().parent().next();
		console.log(div);
		var img = $(this).children("img").attr('src');
		var html = '<img src="'+img+'" alt="" />';
		$(div).find('li').html(html);
		$(div).show();
		$(div).find('.introduce_divs_b_5_5b').on('click',function(){
			$(div).hide();
		})

	})
}

//商品信息
function showMessage () {
	$('#img_ul').children("li").on('mouseenter',function(){
		var img = $(this).children("img").attr('src');
		$(".commodity_show_print").children("img").attr('src',img);
	})
	
	$(".commodity_show_b_5").find("span").on("click",function () {
		var disabled = $(this).attr('disabled');
		if(disabled){
			alert('对不起，这种规格的商品无货了');
			return
		}
		$(this).addClass("span1").siblings("span").removeClass("span1");
	})
	
	var index = $("#inp_txt").val();
	console.log(index);
	$('.commodity_show_b_6_li1').on('click',function(){
		index --;
		if(index < 1){
			index = 1;
			return;
		}
		$("#inp_txt").val(index);
	})
	$('.commodity_show_b_6_li2').on('click',function(){
		index ++;
		if(index > 100){
			index = 100;
			return;
		}
		$("#inp_txt").val(index);
	})
	
	
	$('.show_a').on('click',function () {
		var flag = $(this).attr("id");
		if(flag){
			$(this).removeAttr('id');
		}else{
			$(this).attr('id',"a1");
		}
	})
	
	
	$('.join').on('click',function(){
		var num = $('#font').html() - 0;
		num ++;
		$('#font').html(num);
		console.log(num);
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