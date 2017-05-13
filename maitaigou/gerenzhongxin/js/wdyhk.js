$(function() {
	fn();
	bankCard();
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
function bankCard () {
	//点击添加出现弹层
	$('#addCard').on('click',function(){
		arise();
	})
	//点击X和取消隐藏弹层
	$('.crossFlip').on('click',function () {
		close();
	})
	//点击确定隐藏弹层
	$('makeSure').on('click',function () {
		close();
		$.ajax({
			type:"get",
			url:"",
			async:true
		});
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
			$(this).siblings('ul').hide().children("li").remove("list_li");
		}
		$(this).parent().siblings('div').find("ul").hide();
		cancel_Bubble(event);
	})
	$('body').on('click', function() {
		$('.zhezhao_a_2_btn').siblings('ul').hide();
	});
	
}

//出现弹层
function arise() {
	$('#flip').show();
	$('body').addClass('boy');
}
//关闭弹层
function close() {
	$('#flip').hide();
	$('body').removeClass('boy');
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