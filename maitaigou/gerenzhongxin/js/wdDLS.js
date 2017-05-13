$(function(){
	fn();
	fnr();
})

//左侧
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

function fnr () {
	//下拉框
	$('.conbtn').on('click', function() {
		if($(this).siblings('ul').css("display") === "none") {
			$(this).siblings('ul').show();
			$(this).siblings('ul').children().on('mouseenter', function() {
				$(this).addClass("contdiv_a_li").siblings('li').removeClass('contdiv_a_li');
			}).on('click', function() {
				var html = $(this).html();
				$(this).parent().hide().children().removeClass("contdiv_a_li");
				$(this).parent().siblings('div').children("p").html(html);
			})
		} else {
			$(this).siblings('ul').hide().children("li").removeClass("contdiv_a_li");
		}
		cancel_Bubble()
	})
	$('body').on('click', function() {
		$('.conbtn').siblings('ul').hide().children("li").removeClass("contdiv_a_li");
	});
}

//取消冒泡
function cancel_Bubble() {
	//如果事件对象存在
	var event = event || window.event;
	// w3c的方法
	if(event && event.stopPropagation) {
		event.stopPropagation();
	} else {
		// ie的方法
		event.cancelBubble = true;
	}
}
