$(function(){
	fn();
	shanchu();
})

//左边
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

//右边点击删除按钮删除
function shanchu () {
	$(".li_i").on('click',function(){
		$(this).parent().remove();
		$.ajax({
			type:"get",
			url:"",
			async:true
		});
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
