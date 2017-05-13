

function show(){
	$("#show").mouseenter(function(){
		console.log("jdfjd")
	  $(".show").css({"display":"block"});
	});
	$("#show").mouseleave(function(){
	  $(".show").css({"display":"none"});
	});
}
function qiehuan(){
	$(".img01").children().mouseenter(function(){
		console.log(this.className)
	  $(".img0").attr({src:"detail-img/"+this.className+".png"});
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
function guige(){
	var guige=$(".guige span")[0].innerHTML;
	$(".guige span").click(function(){
		var length=$(".guige span").length;
		for(var i=0;i<length;i++){
//			$(".guige span")[i].style.background="#fafafa";
			$(".guige span").css({"background":"#fff","color":"#000000"})
		}
		this.style.background="#c30d23";
		this.style.color="#fff";
		guige=this.innerHTML;
		console.log(guige)
	});
	console.log(guige)
}
function xiangqing(){
	$(".xiangqing").click(function(){
		this.style.background="#fff";
		this.style.borderColor="#c30d23";
		$(".pingjia").css({"background":"#f2f2f2","borderColor":"#f2f2f2"});
		$(".nav2_1_2").css({"display":"block"});
		$(".nav2_1_3").css({"display":"none"});
	});
	$(".pingjia").click(function(){
		this.style.background="#fff";
		this.style.borderColor="#c30d23";
		$(".xiangqing").css({"background":"#f2f2f2","borderColor":"#f2f2f2"})
		$(".nav2_1_3").css({"display":"block"});
		$(".nav2_1_2").css({"display":"none"});
	});
}








window.onload=function(){
	show();
	qiehuan();
	shuliang();
	guige();
	xiangqing();
	
}