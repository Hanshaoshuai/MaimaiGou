$(function() {
	fn();
	property();
})
//个人中心左侧
function fn() {
	$("#ul").children().on('click', function() {
		$(this).addClass("active").siblings('li').removeClass('active');
		$.ajax({
			type: "get",
			url: "",
			async: true
		});
	})
}

//个人中心右侧
function property (){
	$("#record").children().on('click',function(){
		$(this).addClass('actives').siblings('li').removeClass('actives');
		$.ajax({
			type:"get",
			url:"",
			async:true
		});
	})
	
	
	//图片轮播

	//上一张
	var list = $('#ulli').children('li')
	if(list.length > 5){
		$('#carousel').on('mouseenter',function(){
			$('#box').fadeIn();
		})
		$('#carousel').on('mouseleave',function(){
			$('#box').fadeOut();
		})
	}
	var num = 0
	$('.prev').on('click',function(event){
		num++;
		if(num > list.length-5){
			alert('這已經是最後一張了')
			return;
		}
		var sum = -180* num  + "px";
		$('#ulli').animate({
			"left":sum
		},500)
		cancel_Bubble(event)
	})
	$('.next').on('click',function(event){
		num--;
		if(num < 0){
			alert('這已經是最後一張了')
			return;
		}
		var sum = -180 * num  + "px"; 
		$('#ulli').animate({
			"left":sum
		},500)
		cancel_Bubble(event)
	})
}

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