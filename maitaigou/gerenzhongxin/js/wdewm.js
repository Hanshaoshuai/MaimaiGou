$(function(){
	fn();	
})

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