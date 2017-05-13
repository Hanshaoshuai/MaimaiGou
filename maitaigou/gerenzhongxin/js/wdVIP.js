$(function(){
	fn();
	fnr();
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

//右边
function fnr () {
	$(".connet_r_div2 ul").children().on('click',function(){
		$(this).addClass("connet_r_div2_li").siblings("li").removeClass("connet_r_div2_li");
	})
}
