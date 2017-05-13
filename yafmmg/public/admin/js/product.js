$(function(){
	
	
})


function article_save(){
	var product_obj = $("#form-article-add").serialize();

	x=$("#form-article-add").serializeArray();
    $.each(x, function(i, field){
		if(field.name=="name" && field.value==""){
			layer.msg('产品名称不能为空！',{icon: 5,time:1000});
			return false;
		}
		if(field.name=="weight" && field.value==""){
			layer.msg('重量不能为空！',{icon: 5,time:1000});
			return false;
		}
		if(field.name=="sell_price" && field.value==""){
			layer.msg('销售价格不能为空！',{icon: 5,time:1000});
			return false;
		}
		if(field.name=="cost_price" && field.value==""){
			layer.msg('原价格不能为空！',{icon: 5,time:1000});
			return false;
		}
		
		if(field.name=="catid" && field.value==""){
			layer.msg('请选择分类',{icon: 5,time:1000});
			return false;
		}
		if(field.name=="product_type" && field.value==""){
			layer.msg('请选择商品类型',{icon: 5,time:1000});
			return false;
		}
		console.log(field.name);
    });

}
//保存产品信息时做验证
function article_save_submit(){
	var product_obj = $("#form-article-add").serialize();

	x=$("#form-article-add").serializeArray();
	var err = 0;
    $.each(x, function(i, field){
		if(field.name=="name" && field.value==""){
			layer.msg('产品名称不能为空！',{icon: 5,time:1000});
			err = 1;
			return false;
		}
		if(field.name=="sell_price" && field.value==""){
			layer.msg('销售价格不能为空！',{icon: 5,time:1000});
			err = 1;
			return false;
		}
		if(field.name=="stock" && field.value==""){
			layer.msg('库存不能为空！',{icon: 5,time:1000});
			err = 1;
			return false;
		}
		
		if(field.name=="catid" && field.value==""){
			layer.msg('请选择分类',{icon: 5,time:1000});
			err = 1;
			return false;
		}
		if(field.name=="product_type" && (field.value=="请选择" || field.value==""  || field.value=="0")){
			layer.msg('请选择商品类型',{icon: 5,time:1000});
			err = 1;
			return false;
		}
		console.log(field.name+":"+field.value);
    });
	if(err===0){
		$('#form-article-add').submit();
	}
}