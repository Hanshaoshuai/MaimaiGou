$(function() {
	fn();
	fnr();
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
function fnr() {
	//导航部分
	$("#ulc").children("li").on('click', function() {
		$(this).addClass("li1").siblings('li').removeClass('li1');
		$.ajax({
			type: "get",
			url: "",
			async: true
		});
	})

	//下拉框
	$('.conbtn').on('click', function(event) {
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
			$(this).siblings('ul').hide().children("li").remove("contdiv_a_li");
		}
		$(this).parent().parent().siblings('div').find("ul").hide();
		cancel_Bubble(event);
	})
	$('body').on('click', function() {
		$('.conbtn').siblings('ul').hide();
	});
	
	var txt = "输入订单号或商品名称";
	$('#txtinp').on('blur',function(){
		if($(this).val() == ""){
			$(this).val(txt);
			$(this).css("color","#888888");
		}
	})
	
	$('#txtinp').on('focus',function(){
		if($(this).val() == txt){
			$(this).val("");
			$(this).css("color","#000000")
		}
	})
	
	//搜索按钮
	$('#divbtn').on("click", function() {
		var arr = [];
		var inp = $('.inp1');
		for(var i = 0; i < inp.length; i++) {
			arr.push(inp[i].value)
		}
		$.ajax({
			type: "get",
			url: "",
			async: true
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