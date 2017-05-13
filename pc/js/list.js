









function list(){
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
				$(".list_2").append(
					'<div class="nav_4_1">'+
						'<div class="nav_4_1_img">'+
							'<img src="'+val.goodsListImg+'" alt="" />'+
						'</div>'+
						'<span>'+val.goodsName+'</span>'+
						'<font>'+val.price+'</font>'+
					'</div>'
				)
			})
		}
	});
}













window.onload=function(){
	list();
	
}