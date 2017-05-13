/**
 * Created by Administrator on 2017/3/28.
 */
//window.onLoad = function () {}

//表单验证
var session_id="";
var state;
var phone ='';
$(function(){
    $.mvalidateExtend({
            phone:{
                required : true,
                pattern : /^0?1[3|4|5|8][0-9]\d{8}$/,

                each:function(){

                },

                descriptions:{
                    valid : '<div class="field-validmsg">正确</div>'
                }
            },
            password:{
                required : true,
                pattern : /^[a-zA-Z0-9]{6,22}$/,

                each:function(){

                },

                descriptions:{
                    //required : '<div class="field-invalidmsg">密码输入格式错误</div>',
                    //pattern : '<div class="field-invalidmsg">您输入的手机号码格式不正确</div>',
                    valid : '<div class="field-validmsg">正确</div>'
                }
            },
            tuiphone:{
            required : true,
           // pattern : /^0?1[3|4|5|8][0-9]\d{8}$/,

            each:function(){

            },

            descriptions:{
                required : '<div class="field-invalidmsg">请输入推荐人手机号码</div>',
               // pattern : '<div class="field-invalidmsg">您输入的手机号码格式不正确</div>',
                valid : '<div class="field-validmsg">正确</div>'
            }
        },

        });

    //判断手机号输入
    //var phoneYZM;
    var Code1;
    //var code1;
    var sName;
    var time = 0;
    $("#btn-3").on("touchend", function(event) {
    	if(time>1){
    		return;
    	}else{
	        if(state>0 && state<60){
	            return;
	        }
	        //阻止事件冒泡
	        cancel_Bubble(event);
	        event.preventDefault();
	        //$(this).attr("disabled","disabled").css("color",'#0f0');
	        phone = $.trim($("#telphone").val());
	        //console.log(phone);
	        if (!phone) {
	            $.mvalidateTip("请输入手机号码！");
	            return;
	        } else if (!/^0?1[3|4|5|8]\d{9}$/.test(phone)) {
	            $.mvalidateTip("你输入的手机号码不正确！");
	            return;
	        } else if(/^0?1[3|4|5|8]\d{9}$/.test(phone)){
	                $('#zeZhao').show();
	                $.ajax({
	                    url: "http://121.196.218.57/index.php/api/Publices/getImageCode",
	                    type: "post",
	                    async: true,
	                    dataType:"json",    //跨域json请求一定是jsonp
	                    jsonp: "callbackparam",    //跨域请求的参数名，默认是callback
	                    data: {
	
	                    },
	                    success: function(data,event) {
	                        //阻止事件冒泡
	                        cancel_Bubble(event);
	                        //console.log(data.session_id);
	                        session_id = data.session_id;
	                        console.log(data.session_id);
	
	                        if(data.code == 1000){
	                            console.log(data.data);
	                            $(".D_Img img").attr("src", data.data);
	                            //阻止事件冒泡
	                            cancel_Bubble(event);
	                        }else {
	                            console.log("服务器返回参数出错");
	                        }
	                        //阻止事件冒泡
	                        cancel_Bubble(event);
	                    },
	                    error: function () {
	                      alert("请求失败");
	                    }
	                });
	            //阻止事件冒泡
	            cancel_Bubble(event);
	            event.preventDefault();
	            //return false;
	        }
	        //阻止事件冒泡
	        cancel_Bubble(event);
	        event.preventDefault();
	        return false;
        }
    });
    //阻止事件冒泡
    //cancel_Bubble(event);
    //当点击刷新图片验证码时再次请求图片验证码接口
    var Code1;
    $('#code').on('touchend',function (event) {
        $.ajax({
            url: "http://121.196.218.57/index.php/api/Publices/getImageCode",
            type: "post",
            async: true,
            dataType:"json",    //跨域json请求一定是jsonp
            jsonp: "callbackparam",    //跨域请求的参数名，默认是callback
            data: {

            },
            success: function(data,event) {
                //阻止事件冒泡
                cancel_Bubble(event);
                session_id = data.session_id;
                console.log(data.session_id);
                var timestamp = Date.parse(new Date())+parseInt(500*Math.random());
                if(data.code == 1000){
                    console.log(data.data);
                    $(".D_Img img").attr("src", data.data+"?v="+timestamp);
                    //阻止事件冒泡
                    cancel_Bubble(event);
                }else {
                    console.log("服务器返回参数出错");
                }
            },
            error: function () {
                alert("请求失败");
            }
        });
        //阻止事件冒泡
        cancel_Bubble(event);
        event.preventDefault();
        return false;
    });

    //点击遮罩层的确定按钮触发的事件
    $('#verify').on('touchend',function (event) {

        if(state>0 && state<60){
            return;
        }
        var ulmgCode1=$('#vcode').val();
        console.log(ulmgCode1);
        $.ajax({
            //http://192.168.10.100/index.php/Api/User/sms
            url: "http://121.196.218.57/index.php/Api/User/sms",
            type: "post",
            async: true,
            dataType:"json",    //跨域json请求一定是jsonp
            jsonp: "callbackparam",    //跨域请求的参数名，默认是callback
            data: {
                uImgCode: ulmgCode1,
                phone:phone,
                //ulmgCode1:ulmgCode1
                PHPSESSID:session_id
            },
            success: function(data) {
                //阻止事件冒泡
                cancel_Bubble(event);
                event.preventDefault();
                console.log(data)
                if(data.code == 1000){
                    $('#zeZhao').hide();
                    time = 60;
                    var set=setInterval(function(){
                    	time-=1
                    	if(time==1){
                    		clearInterval(set)
                    	}
                        $("#btn-3").text("重新发送"+time+"(s)");
                        //state = time;
                        //console.log(state);
                    }, 1000);/*等待时间*/

                    setTimeout(function(){
//						clearInterval(set)
                        $("#btn-3").text("重新获取验证码");/*倒计时*/
//                      clearInterval(set);
                    },60000);
                    //阻止事件冒泡
                    cancel_Bubble(event);
                    event.preventDefault();
                }
                if(data.code == 1009){
                    alert(data.msg);
                    //阻止事件冒泡
                    cancel_Bubble(event);
                    event.preventDefault();
                }
            },
            error: function () {
                alert("请求失败");
            }
        });
        //阻止事件冒泡
        cancel_Bubble(event);
        event.preventDefault();
        return false;
    });
    //点击遮罩层的取消按钮触发的事件
    $('#cancel').on('touchend',function () {
        //遮罩层消失
        $('#zeZhao').hide();
    });
    //阻止事件冒泡
    cancel_Bubble(event);
    event.preventDefault();
    $("#form1").mvalidate({
            type:1,
            onKeyup:true,
            sendForm:false,
            firstInvalidFocus:false,
            valid:function(event,options){
                //点击提交按钮时,表单通过验证触发函数
//                alert("验证通过！接下来可以做你想做的事情啦！");
                //阻止事件冒泡
                cancel_Bubble(event);
                event.preventDefault();
                //通过ajax实现提交表单数据到后台
                $('#btn').on('touchend',function (event) {
                    //阻止事件冒泡
//                  cancel_Bubble(event);
//                  event.preventDefault();
                    console.log(1111)
                    //表单序列化
                    var jsonObj={}
                    var jsonData=$('#Dform form').serializeArray();
                    $.each(jsonData,function(index,item){
                        jsonObj[item['name']]=item['value'];
                    });
//                    console.log(data);  //name=zhangsan&sex=1&age=20
                    console.log(jsonData);  //[Object, Object, Object]
//                  console.log(jsonObj);
                    $.ajax({
                        type:"post",    //请求方式
                        async:true,    //是否异步
                        url :"http://121.196.218.57/index.php/Api/User/register",
                        dataType:"json",    //跨域json请求一定是jsonp
                        jsonp: "callbackparam",    //跨域请求的参数名，默认是callback
                        //jsonpCallback:"successCallback",    //自定义跨域参数值，回调函数名也是一样，默认为jQuery自动生成的字符串
                        data:"phone="+jsonObj.phone+"&password="+jsonObj.password+"&tuiphone="+jsonObj.tuiphone +'&check_code='+jsonObj.check_code+'&PHPSESSID='+session_id,    //请求参数
                        success: function(data,event) {
                        	console.log(jsonObj);
                        	console.log(data);
                            if(data.code == 1000){
                                alert('恭喜您注册成功！');
                                setTimeout(function(){
                                    window.location = 'http://121.196.218.57/app/appdown/'
                                },2000);
                                //阻止事件冒泡
//                              cancel_Bubble(event);
//                              event.preventDefault();
                                return false;
                                //阻止事件冒泡
//                              cancel_Bubble(event);
//                              event.preventDefault();
                            }
                            //如果数据库中已经存在该用户，则注册失败
                            if(data.code == 1001){
                            //$('.field-validmsg').innerHTML = '手机已经被注册!';
                                alert('手机已经被注册!');
                                //alert(0);
                                //setTimeout(function(){
                                //    window.location = 'http://www.baidu.com'
                                //},1000);
                                return;
                                //阻止事件冒泡
//                              cancel_Bubble(event);
//                              event.preventDefault();
                            }
                            if(data.code == 1010){
                                //$('.field-validmsg').innerHTML = '电话或密码不正确!';
//                              alert(data.msg);
								alert('验证码错误！');
                                //阻止事件冒泡
//                              cancel_Bubble(event);
//                              event.preventDefault();
                                return;
                            }
                            if(data.code == 1007){
                                //$('.field-validmsg').innerHTML = '电话或密码不正确!';
//                              alert(data.msg);
								alert('电话或密码不正确!');
                                return;
                                //阻止事件冒泡
//                              cancel_Bubble(event);
//                              event.preventDefault();
                            }
//                          console.log('恭喜您注册成功');
                            //阻止事件冒泡
//                          cancel_Bubble(event);
//                          event.preventDefault();
                            return false;
                        },
                        error : function () {
                            console.log("抱歉，注册失败");
                            //阻止事件冒泡
                            cancel_Bubble(event);
                            event.preventDefault();
                        }
                    });
                    $('#Dform').off('touchend');
                    return false;
                    //阻止事件冒泡
//                  cancel_Bubble(event);
//                  event.preventDefault();

                    //alert(0);
                    //setTimeout(function(){
                    //    window.location = 'http://www.baidu.com'
                    //},1000);
                });
            },
            invalid:function(event, status, options){
                //点击提交按钮时,表单未通过验证触发函数
            },
            eachField:function(event,status,options){
                //点击提交按钮时,表单每个输入域触发这个函数 this 执向当前表单输入域，是jquery对象
            },
            eachValidField:function(val){},
            eachInvalidField:function(event, status, options){},
            conditional:{
                //phoneyzm: function() {
                //    return $('#phoneyzm').val() == code1;
                //}
            },
            descriptions:{
                telphone: {
                    required: '请输入手机号码',
                    pattern: '你输入的手机号码格式不正确'
                },
                phoneyzm: {
                    required: '请输入短信验证码',
                    //conditional: '您输入的手机短信验证码不正确',
                    valid : '<div class="field-validmsg">正确</div>'
                },
                passwords:{
                    required : '请输入密码',
                    pattern : '你输入的密码格式不正确',
                },
                confirmpassword:{
                    required : '请再次输入密码',
                    conditional : '两次密码不一样'
                }
            }
        });
    });
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

$('#Dform').click(function (url_1,time_1) {
    setTimeout("top.location.href = '" + url_1 + "'",time_1);
});


//遮罩层验证码
//$(function () {
//
//    var code;//声明一个变量用于存储生成的验证码
//    document.getElementById("code").onclick=changeImg;
//    function changeImg(){
//        //alert("换图片");
//        var arrays=new Array(
//            '1','2','3','4','5','6','7','8','9','0',
//            'a','b','c','d','e','f','g','h','i','j',
//            'k','l','m','n','o','p','q','r','s','t',
//            'u','v','w','x','y','z',
//            'A','B','C','D','E','F','G','H','I','J',
//            'K','L','M','N','O','P','Q','R','S','T',
//            'U','V','W','X','Y','Z'
//        );
//        code='';//重新初始化验证码
//        //alert(arrays.length);
//        //随机从数组中获取四个元素组成验证码
//        for(var i=0;i<4;i++){
//            //随机获取一个数组的下标
//            var r=parseInt(Math.random()*arrays.length);
//            code+=arrays[r];
//            //alert(arrays[r]);
//        }
//        //alert(code);
//        document.getElementById('code1').innerHTML=code;//将验证码写入指定区域
//    }
//
////效验验证码(表单被提交时触发)
//    function check(){
//        //获取用户输入的验证码
//        var input_code=document.getElementById('vcode').value;
//        //alert(input_code+"----"+code);
//        if(input_code.toLowerCase()==code.toLowerCase())
//        {
//            //验证码正确(表单提交)
//            return true;
//        }
//        alert("请输入正确的验证码!");
//        //验证码不正确,表单不允许提交
//        return false;
//    }
//
//});


//发送手机验证码
//function clickButton(obj){
//    var obj = $(obj);
//    alert('nihao');
//    obj.attr("disabled","disabled");/*按钮倒计时*/
//    var time = 60;
//    var set=setInterval(function(){
//        obj.val("重新发送"+ --time+"(s)");
//    }, 1000);/*等待时间*/
//    setTimeout(function(){
//        obj.attr("disabled",false).val("重新获取验证码");/*倒计时*/
//        clearInterval(set);
//    }, 60000);
//}


//ajax跨域问题
//var ulmgCode=document.getElementById('vcode').value;
//var jsontext={}
//var jsonDatas=$('#Dform form').serializeArray();
//$.each(jsonDatas,function(index,item){
//    jsontext[item['name']]=item['value'];
//});
//var verifyCode = code1;
//$.ajax({
//    type:"post",    //请求方式
//    async:true,    //是否异步
//    url :"http://192.168.10.100/index.php/Api/User/sms",
//    dataType:"json",    //跨域json请求一定是jsonp
//    jsonp: "callbackparam",    //跨域请求的参数名，默认是callback
//    //jsonpCallback:"successCallback",    //自定义跨域参数值，回调函数名也是一样，默认为jQuery自动生成的字符串
//    data:"&uImgCode="+jsontext.ulmgCode + "phone="+jsontext.phone + "&verifyCode	="+jsontext.verifyCode,    //请求参数
//    success: function(data) {
//        //请求成功处理，和本地回调完全一样
//        //alert("注册成功");
//
//        //如果数据库中已经存在该用户，则注册失败-------------------------------------此功能未解决
//        //if (!ulmgCode == imgCode){
//        //    alert('注册成功');
//        //}
//        //alert('注册失败')
//
//        console.log('发送短信验证码成功');
//    },
//    error : function () {
//
//        console.log("抱歉，发送短信验证码失败");
//    }
//});