





function show(){
	$("#show").mouseenter(function(){
		console.log("jdfjd")
	  $(".show").css({"display":"block"});
	});
	$("#show").mouseleave(function(){
	  $(".show").css({"display":"none"});
	});
}
function shuliang(){
	$(".jian").click(function(){
		var he=parseInt($(".he").text())
		console.log(parseInt($(".he").text()))
		if(he>1){
			he--;
			$(".he").text(he)
		}		
	});
	$(".jia").click(function(){
		var he=parseInt($(".he").text())
		console.log(parseInt($(".he").text()))
		if(he<10){
			he++;
			$(".he").text(he)
		}
	});
}




window.onload=function(){
	show();
	shuliang();
	
}