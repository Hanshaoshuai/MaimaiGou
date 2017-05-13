


function show(){
	//导航下的轮播图
	$('.header_3_a1').on('mouseenter',function(){
		$('.show').stop(true,false).animate({
			"top":'188px'
		},500);
	})
	$('.header_30').on('mouseleave',function(){
		$('.show').stop(true,false).animate({
			"top":'28px'
		},500);
	})
	
	$('.show_ul').children("li").on('mouseenter',function () {
		$(this).children('span').stop(true,false).animate({
			'height':'100%',
			'line-height':'140px'
		},500)
	})
	$('.show_ul').children("li").on('mouseleave',function () {
		$(this).children('span').stop(true,false).animate({
			'height':'40px',
			'line-height':'40px'
		},500)
	})
	var index = 0;
	var sum = -182;
	var num = $('.show_ul').children("li")
	$(".next").on('click',function(){
		index++;
		if(index > num.length - 6){
			index = num.length - 6;
			alert("已经是最后一张了");
			return;
		}
		$('.show_ul').animate({
			"left":sum * index + "px"
		},400)
	})
	
	$(".prev").on('click',function(){
		index--;
		if(index < 0){
			index = 0;
			alert("已经是第一张了");
			return;
		}
		$('.show_ul').animate({
			"left":sum * index + "px"
		},400)
	})

	
	//关闭注册页按钮
	$('.close1').click(function() {
		$('.menu').hide();
		//alert(000);
	});
	
	
	//用户注册协议
	$("#agreement").click(function() {
		$(".mask").show();
		$(".agreement").show();
	});
	$(".close").click(function() {
		$(".mask").hide();
		$(".agreement").hide();
	});
	
	
	//轮播
	var focusBanner=function(){
		var $focusBanner=$("#focus-banner"),
		$bannerList=$("#focus-banner-list li"),
		$focusHandle=$(".focus-handle"),
		$bannerImg=$(".focus-banner-img"),
		$nextBnt=$("#next-img"),
		$prevBnt=$("#prev-img"),
		$focusBubble=$("#focus-bubble"),
		bannerLength=$bannerList.length,
		_index=0,
		_timer="";
		
		var _height=$(".focus-banner-img").find("img").height();
		$focusBanner.height(_height);
		$bannerImg.height(_height);

		$(window).resize(function(){
			window.location.reload()
		});

		for(var i=0; i<bannerLength; i++){
			$bannerList.eq(i).css("zIndex",bannerLength-i);
			$focusBubble.append("<li></li>");
		}
		$focusBubble.find("li").eq(0).addClass("current");
		var bubbleLength=$focusBubble.find("li").length;
		$focusBubble.css({
			"width":bubbleLength*28,
			"marginLeft":-bubbleLength*11
		});//初始化

		$focusBubble.on("click","li",function(){
			$(this).addClass("current").siblings().removeClass("current");
			_index=$(this).index();
			changeImg(_index);
		});//点击轮换

		$nextBnt.on("click",function(){
			_index++
			if(_index>bannerLength-1){
				_index=0;
			}
			changeImg(_index);
		});//下一张

		$prevBnt.on("click",function(){
			_index--
			if(_index<0){
				_index=bannerLength-1;
			}
			changeImg(_index);
		});//上一张
		$(".banner").css({"background":"url(images/banner0.jpg)"})
		function changeImg(_index){
			$(".banner").css({"background":"url(images/banner"+_index+".jpg)"});
			console.log(_index)
			$bannerList.eq(_index).fadeIn(250);
			$bannerList.eq(_index).siblings().fadeOut(200);
			$focusBubble.find("li").removeClass("current");
			$focusBubble.find("li").eq(_index).addClass("current");
			clearInterval(_timer);
			_timer=setInterval(function(){$nextBnt.click()},3000)
		}//切换主函数
		_timer=setInterval(function(){$nextBnt.click()},3000)
	}();
	
	
	//app下载
	function app() {
		 $('.app').children("a").on('click',function(){
	 		$('.app').hide();
	 	})
	}
}


//<!--内容第三部分  品牌特惠-->
function pinpai(){
	$.ajax({
		type:"get",
		url:"http://datainfo.duapp.com/shopdata/getGoods.php",
		data:{
//			classID:classID
		},
		dataType:"JSONP",
		beforeSend:function(){
//			未请求到数据前函数再次添加loding
//			获取元素	that.refs.btn.setAttribute("disabled","disabled")
		},
		success:function(res){
			console.log(res)
			res.map(function(val){
				console.log(val.goodsListImg)
				$("#nav_3").append('<div class="nav_3_1"><div id="nav_3_1_hidden"><ul><li class="nav_3_1_hidden1"></li><li class="nav_3_1_hidden2"></li></ul></div><img src="'+val.goodsListImg+'"/></div>')
					
			})
			$(".nav_3_1").mouseenter(function(){
			console.log("jdfjd")
			$("#nav_3_1_hidden").css({"display":"block"});
			$.ajax({
				type:"get",
				url:"http://datainfo.duapp.com/shopdata/getGoods.php",
				data:{
		//			classID:classID
				},
				dataType:"JSONP",
				beforeSend:function(){
		//			未请求到数据前函数再次添加loding
		//			获取元素	that.refs.btn.setAttribute("disabled","disabled")
				},
				success:function(res){
					console.log(res)
					res.map(function(val){
						console.log(val.goodsListImg)
						$(".nav_3_1_hidden1").append('<font><img src="'+val.goodsListImg+'" alt="" /><span>'+val.price+'</span></font>')
						$(".nav_3_1_hidden2").append('<a href="#">查看更多</a>')
					})
				}
			});
		});
		$(".nav_3_1").mouseleave(function(){
		  $("#nav_3_1_hidden").css({"display":"none"});
		});
			
			
		}
	});
	
}


//<!--内容第四部分   热卖商品-->
function koubei(){
	$.ajax({
		type:"get",
		url:"http://datainfo.duapp.com/shopdata/getGoods.php",
		data:{
//			classID:classID
		},
		dataType:"JSONP",
		beforeSend:function(){
//			未请求到数据前函数再次添加loding
//			获取元素	that.refs.btn.setAttribute("disabled","disabled")
		},
		success:function(res){
			console.log(res)
			res.map(function(val){
				console.log(val.goodsListImg)
				$("#nav_4").append('<div class="nav_4_1"><div class="nav_4_1_img"><img src="'+val.goodsListImg+'" alt="" /></div><div class="nav_4_1_text"><span>'+val.goodsName+'</span><font>'+val.price+'麦</font></div></div>')
			})
		}
	});
}


//<!--内容第五部分     分类-->
function fenlei(){
	$.ajax({
		type:"get",
		url:"http://datainfo.duapp.com/shopdata/getGoods.php",
		data:{
//			classID:classID
		},
		dataType:"JSONP",
		beforeSend:function(){
//			未请求到数据前函数再次添加loding
//			获取元素	that.refs.btn.setAttribute("disabled","disabled")
		},
		success:function(res){
			console.log(res)
			res.map(function(val){
				console.log(val.goodsListImg)
				$("#nav_5").append('<div class="nav_4_1"><div class="nav_4_1_img"><img src="'+val.goodsListImg+'" alt="" /></div><div class="nav_4_1_text"><span>'+val.goodsName+'</span><font>'+val.price+'麦</font></div></div>')
			})
		}
	});
}


//<!--内容第六部分   猜你喜欢-->
function xihuan(){
	$.ajax({
		type:"get",
		url:"http://datainfo.duapp.com/shopdata/getGoods.php",
		data:{
//			classID:classID
		},
		dataType:"JSONP",
		beforeSend:function(){
//			未请求到数据前函数再次添加loding
//			获取元素	that.refs.btn.setAttribute("disabled","disabled")
		},
		success:function(res){
			console.log(res)
			res.map(function(val){
				console.log(val.goodsListImg)
				$("#nav_6").append('<div class="nav_4_1"><div class="nav_4_1_img"><img src="'+val.goodsListImg+'" alt="" /></div><div class="nav_4_1_text"><span>'+val.goodsName+'</span><font>'+val.price+'麦</font></div></div>')
			})
		}
	});
}


window.onload=function(){
	show();
	pinpai();
	koubei();
	fenlei();
	xihuan();
}