$(function() {
	fn();
	site();
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
function site () {
	var lists = $('#list').children("li");
	lists.on('change',function(){
		if(this.length != 0){
			$('#list').addClass('list');
		}else{
			$('#list').removeClass('list');
		}
	});
	//点击添加地址出现弹层
	$('#site').on('click',function () {
		arise();
	})
	//点击x关闭弹层
	$('#delete').on('click',function(){
		close();
	})
	//点击取消关闭弹层
	$('#cancel').on('click',function () {
		close();
	})
	//删除
	remove()
	//点击编辑，
	compile();
	//下拉搜索
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
	//点击确定 关闭弹层
	$('#confirm').on('click',function () {
		var obj = {};
		obj["name"] = $('#inp1').val();
		obj["tel"] = $('#inp2').val();
		obj["select1"] = $('#select1').html();
		obj["select2"] = $('#select2').html();
		obj["select3"] = $('#select3').html();
		obj["text"] = $('#text').val();
		console.log(obj.select1);
		console.log(obj.select2);
		console.log(obj.select3);
		var html = '<li>'+
					'<div class="addressS_text">'+
					'<div class="addressS_txt1 clearfix">'+
					'<span class="addressS_txt1_span1">'+obj.name+'</span>'+
					'<span class="addressS_txt1_span2">'+obj.tel+'</span>'+
					'<span class="addressS_txt1_span3">'+obj.select1+'&nbsp;'+ obj.select2+'&nbsp;'+ obj.select3+'&nbsp;'+ obj.text +'</span>'+
					'</div>'+
					'</div>'+
					'<div class="addressS_txt clearfix">'+
						'<a href="javascript:;" class="addressS_txt_a1">默认地址</a>'+
						'<a class="compile" href="javascript:;">编辑</a>'+
						'<a class="remove" href="javascript:;">删除</a>'+
					'</div>'+
					'</li>'
		$('#list').append(html);
		remove();
		compile();
		$.ajax({
			type:"get",
			url:"",
			async:true
		});	
	})
	
}

//出现弹层
function arise() {
	$('#wicket').show();
	$('body').addClass('boy');
}
//关闭弹层
function close() {
	$('#wicket').hide();
	$('body').removeClass('boy');
}
//点击删除地址
function remove() {
	$('.remove').on('click',function(){
		$(this).parent().parent().remove();
		$.ajax({
			type:"get",
			url:"",
			async:true
		});
	})
}

//点击编辑地址
function compile() {
	$('.compile').on('click',function(){
		arise();
		$.ajax({
			type:"get",
			url:"",
			async:true
		})
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