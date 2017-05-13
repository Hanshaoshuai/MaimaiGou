


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
//	产品动态加载
	var cont=null;
	$.ajax({
		type:"get",
		url:"http://datainfo.duapp.com/shopdata/getGoods.php",
		data:{
//			classID:classID
		},
		dataType:"JSONP",
		beforeSend:function(){
//			未请求到数据前函数再次添加loding
//	获取元素	that.refs.btn.setAttribute("disabled","disabled")
		},
		success:function(res){
			console.log(res)
			res.map(function(val){
				cont='<div class="cart_lsit_00">'+
						'<span class="img_0"></span>'+
						'<span class="cart_lsit_2_img"><img src="../images/u1086.png" alt="" /></span>'+
						'<span class="cart_lsit_2_1">'+
							'<span>100年传世珐琅锅 马卡龙系列</span>'+
							'<font>净含量：750ml</font>'+
						'</span>'+
						'<span class="cart_lsit_2_2">'+
							'<font>359麦</font>'+
						'</span>'+
						'<span class="cart_lsit_2_3">'+
							'<font class="jian">-</font>'+
							'<font class="he">1</font>'+
							'<font class="jia">+</font>'+
						'</span>'+
						'<span class="cart_lsit_2_4">'+
							'<span>359麦</span>'+
							'<font class="img"></font>'+
							'<font class="maijuan">10麦券</font>'+
						'</span>'+
						'<span class="cart_lsit_2_5"><img src="../images/u5066.png" alt="" /></span>'+
						'</div>'
				//插入到父标签
				$(cont).appendTo('.cart_lsit_2');
			});
			
			var zongjia=null;			//所有已选中产品的总价
			var zongjia_dom=$(".heji_1 font")	//所有已选中产品的总价的DOM
			var xuanzhong_de_jiage=null;		//已选中某产品的价格
			var geshu=1;				//个数
			var danjia=null;			//单价
			var danzongjia=null;		//某个产品价格合计的DOM
			var danzongjia_1=null;		//某个产品价格合计/转换成数字
			//单个产品价格计算
			function jisuan(x,y,q,w){
				geshu=$(w).parent().children(".he").text();			//个数
				danjia=$(w).parent().prev().children("font").text()	//单价
				geshu=parseInt(geshu);								//个数/转换成数字
				danjia=parseInt(danjia);							//单价/转换成数字
				danzongjia=$(w).parent().next().children("span");	//某个产品价格合计的DOM
				danzongjia_1=parseInt(danzongjia.text());			//某个产品价格合计/转换成数字
				$(danzongjia).text(danjia*geshu+"麦豆");				//计算并重新赋值
				zongjia=parseInt($(".heji_1 font").text());			//获取总价
				if($(w).parent().parent().children(".img_1")[0]!==undefined){
					if($(w).text()=="+"){								//判断是加是减
						zongjia+=danjia;
						$(".heji_1 font").text(zongjia+"麦豆");			//获取总价重新赋值
					}else{
						zongjia-=danjia;
						$(".heji_1 font").text(zongjia+"麦豆");			//获取总价重新赋值
					}
					console.log(zongjia)
				}else{
//					zongjia_dom.text("0麦豆");
				}
				console.log(w);
//				console.log($(w).parent().next().children("span").text())
			}
			
			//购买数量加减
			var i=0;
			var jian=$(".jian");		//减号按键集合
			var jia=$(".jia");			//加好按键集合
			var he=$(".he");			//数量DOM集合
			var shanchu=$(".cart_lsit_2_5 img");		//删除按钮DOM的集合
			var chanpin=$(".cart_lsit_00");				//所有产品的DOM的集合
			var xuanzhong=$(".img_0");					//所有选中图标的DOM的集合
			var yixuanzhong=null;						//所有已选中图标的DOM的集合
			var index=null;
			console.log(jian)
			for(i; i<he.length; i++){
				jian[i].setAttribute("index",i);		//加,减的DOM添加自定义属性
				jia[i].setAttribute("index",i);
				shanchu[i].setAttribute("index",i);		//删除按钮DOM添加自定义属性
				xuanzhong[i].setAttribute("index",i);	//选中图标按钮DOM添加自定义属性
//				console.log(jian[i])
				$(jian[i]).click(function(){
//					console.log(this)
					index=parseInt(this.getAttribute("index"));
					var zong=parseInt(he[index].innerText);			//通过this的自定义属性获取当前he的text的数量
//					console.log(parseInt(he[index].innerText));
					if(zong>1){
						zong--;
						$(he[index]).text(zong);
						jisuan(zong,index,chanpin,this)
					}
				});
				$(jia[i]).click(function(){
//					console.log(this.getAttribute("index"))
					index=parseInt(this.getAttribute("index"));
					var zong=parseInt(he[index].innerText);			//通过this的自定义属性获取当前he的text的数量
//					console.log(parseInt(he[index].innerText));
					if(zong<12){
						zong++;
						$(he[index]).text(zong);
						jisuan(zong,index,chanpin,this)
					}
				});
				
				//删除产品部分
				$(shanchu[i]).click(function(){						//删除某一种产品
					var k=0;
					var sume=null;									//已选中产品的价格累计
					var sume_1=null;								//已选中产品的价格
					index=parseInt(this.getAttribute("index"));
					$(chanpin[index]).remove();
					yixuanzhong=$(".img_1");						//所有已选中图标的DOM的集合
					if(yixuanzhong.length>0){
						for(k; k<yixuanzhong.length; k++){
							sume_1=$(yixuanzhong[k]).parent().children(".cart_lsit_2_4").children("span").text();
							sume+=parseInt(sume_1);
						}
						console.log(sume)
//						zongjia_dom.text("");
						zongjia_dom.text(sume+"麦豆");
					}else{
						zongjia_dom.text("0麦豆");
					}
					xuanzhong=$(".img_0");							//所有选中图标的DOM的集合从新赋值
				});
				
				//选中某一种产品
				var ids=0;
				$(xuanzhong[i]).click(function(){					//选中某一种产品
					xuanzhong_de_jiage=parseInt($(this).parent().children(".cart_lsit_2_4").children("span").text());	//已选中某产品的价格
					zongjia=parseInt($(".heji_1 font").text());		//所有已选产品的总价格
					if(ids==xuanzhong.length){
						$(".img_2").attr({"id":"img_1"});
					}
					if($(this).attr("add")){
						$(this).removeAttr("id");					//移除属性
						$(this).removeAttr("add");
						$(this).attr({"class":"img_0"});
						zongjia_dom.text(zongjia-xuanzhong_de_jiage+"麦豆")//取消选中后计算总价
						console.log(zongjia)
					}else{
						this.setAttribute("id","img_1");			//添加属性
						$(this).attr({"add":"add"});
						$(this).attr({"class":"img_0 img_1"});
						zongjia_dom.text(zongjia+xuanzhong_de_jiage+"麦豆")//选中后计算总价
						console.log(zongjia)
					}
					ids=$(".img_1").length;							//选中的产品个数判断是否全选
					if(ids==xuanzhong.length){
						$(".img_2").attr({"id":"quanxuan"});
					}else{
						$(".img_2").removeAttr("id");
					}
					yixuanzhong=$(".img_1");						//所有已选中图标的DOM的集合
//					console.log(yixuanzhong.length)
				});
			}
			
			//选中产品部分
			$(".quanxuan").click(function(){							//全选产品
				chanpin=$(".cart_lsit_00");								//所有产品的DOM的集合
				if(this.children[0].getAttribute("id")){
					var q=0;
					$(this.children[0]).removeAttr("id");				//移除属性
					xuanzhong=$(".img_0");								//所有选中图标的DOM的集合
					for(q; q<xuanzhong.length; q++){
						$(xuanzhong[q]).removeAttr("id");
						$(xuanzhong[q]).removeAttr("add");
						$(xuanzhong[q]).attr({"class":"img_0"});
					}
					$(".heji_1 font").text("0麦豆");
					zongjia=0;											//总价为0
				}else{
					var q=0;
					this.children[0].setAttribute("id","quanxuan");		//添加属性
					xuanzhong=$(".img_0");								//所有选中图标的DOM的集合
					for(q; q<xuanzhong.length; q++){
						$(xuanzhong[q]).attr({"id":"img_1"});
						$(xuanzhong[q]).attr({"add":"add"});
						$(xuanzhong[q]).attr({"class":"img_0 img_1","add":"add"});
					}
					zongji();											//调用计算总价函数
				}
			});
			
			//所有产品的总价
			function zongji(){											//全选时计算总价函数
				var e=0;
				var zongjia_0=null;
				for(e; e<chanpin.length; e++){
					var shuji=$(".cart_lsit_2_4 span")[e];
					zongjia_0+=parseInt($(shuji).text());
					$(".heji_1 font").text(zongjia_0+"麦豆");
					console.log(zongjia_0);
				}
			}
			
			//批量删除
			$(".piliang").click(function(){
				if($(this).children("font").attr("class")){
					$(this).children("font").removeClass();
				}else{
					$(this).children("font").attr({"class":"imgurl"});
				}
			});
			$(".piliang_1").click(function(){
				var v=0;
				xuanzhong=$(".img_1");					//所有选中图标的DOM的集合
				if(xuanzhong.length>0 && $(".piliang").children("font").attr("class")){
					for(v; v<xuanzhong.length; v++){
						$(xuanzhong[v]).parent().remove();
					}
					$(".heji_1 font").text("0麦豆");		//所有产品总价为0
				}
			});
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
//	获取元素	that.refs.btn.setAttribute("disabled","disabled")
			
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
//导航下的轮播图
function nav () {
	$('.header_3_a1').on('mouseenter',function(){
		$('.show').stop().animate({
			"top":'188px'
		},500);
	})
	$('.header_30').on('mouseleave',function(){
		$('.show').stop().animate({
			"top":'28px'
		},500);
	})
	
	$('.show_ul').children("li").on('mouseenter',function () {
		$(this).children('span').stop().animate({
			'height':'100%',
			'line-height':'140px'
		},500)
	})
	$('.show_ul').children("li").on('mouseleave',function () {
		$(this).children('span').stop().animate({
			'height':'40px',
			'line-height':'40px'
		},500)
	})
	var index = 0;
	var sum = -182;
	var num = $('.show_ul').children("li")
	$(".next").on('click',function(event){
		index++;
		if(index > num.length - 6){
			index = num.length - 6;
			alert("已经是最后一张了");
			return;
		}
		$('.show_ul').stop().animate({
			"left":sum * index + "px"
		},400)
		cancel_Bubble(event)
	})
	
	$(".prev").on('click',function(event){
		index--;
		if(index < 0){
			index = 0;
			alert("已经是第一张了");
			return;
		}
		$('.show_ul').stop().animate({
			"left":sum * index + "px"
		},400)
		cancel_Bubble(event)
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

//app下载
function app() {
	 $('.app').children("a").on('click',function(){
 		$('.app').hide();
 	})
}

window.onload=function(){
	show();
	shuliang();
	xihuan();
	nav();
	app();
}