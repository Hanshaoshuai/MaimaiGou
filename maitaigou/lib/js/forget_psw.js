/**
 * Created by Administrator on 2017/3/28.
 */
//window.onLoad = function () {}

//表单验证
var  session_id="";
var state;
var phone_1;
var tuiphone;
//设置2秒后提示层消失
setTimeout(autoplay,2000);
function autoplay() {
    $('.tishi').hide();
}
$(function(){
    var txt_1_1 = "手机号码";
    var txt_l_2 = "请输入图片验证码";
    var txt_l_3 = "请输入短信验证码";
    var txt_l_4 = "请设置6-20位新密码";
    $('#telphone_2').on('blur',function(){
        if($(this).val() == ""){
            $(this).val(txt_1_1);
            $(this).css("color","#888888");
        }
    })

    $('#telphone_2').on('focus',function(){
        if($(this).val() == txt_1_1){
            $(this).val("");
            $(this).css("color","#000000")
        }
    })
    $('#tupianyzm_2').on('blur',function(){
        if($(this).val() == ""){
            $(this).attr('type','text')
            $(this).val(txt_l_2);
            $(this).css("color","#888888");
        }
    })

    $('#tupianyzm_2').on('focus',function(){
        if($(this).val() == txt_l_2){
            $(this).val("");
            $(this).css("color","#000000")
        }
    })
    $('#phoneyzm_2').on('blur',function(){
        if($(this).val() == ""){
            $(this).attr('type','text')
            $(this).val(txt_l_3);
            $(this).css("color","#888888");
        }
    })

    $('#phoneyzm_2').on('focus',function(){
        if($(this).val() == txt_l_3){
            $(this).val("");
            $(this).css("color","#000000")
        }
    })
    $('#pwd_2').on('blur',function(){
        if($(this).val() == ""){
            $(this).attr('type','text')
            $(this).val(txt_l_4);
            $(this).css("color","#888888");
        }
    })

    $('#pwd_2').on('focus',function(){
        if($(this).val() == txt_l_4){
            $(this).val("");
            $(this).css("color","#000000")
        }
        $(this).attr('type','password')
    })

    //				页面加载时发送图片验证码请求
    $.ajax({
        url: "http://192.168.10.100/index.php/api/Publices/getImageCode",
        type: "post",
        async: true,
        dataType:"json",    //跨域json请求一定是jsonp
        jsonp: "callbackparam",    //跨域请求的参数名，默认是callback
        data: {

        },
        crossDomain: true == !(document.all),
        success: function(data,event) {
            //阻止事件冒泡
            cancel_Bubble(event);
            //console.log(data.session_id);
            session_id = data.session_id;
            console.log(data.session_id);
            var timestamp = Date.parse(new Date())+parseInt(500*Math.random());
            if(data.code == 1000){
                console.log(data.data);
                $("#btn_3_2 img").attr("src", data.data+"?v="+timestamp);
                //阻止事件冒泡
                cancel_Bubble(event);
            }else {
                console.log("服务器返回参数出错");
            }

            //阻止事件冒泡
            cancel_Bubble(event);

        },
        error: function () {
            // alert("请求失败");
        }
    });

    //匹配手机号正则
    var phone1 = /^0?1[3|4|5|8][0-9]\d{8}$/;
    $(document).ready(function(){  $("input[name=phone]").focus();});
    $("#telphone_2").blur( function blur_phone() {
        //console.log($("#telphone").val());

        //if (phone.test($("#telphone").val())){
        //    console.log($("#telphone").val());
        //    console.log('格式正确');
        //}else{
        //    console.log('手机号格式不正确,请重新输入');
        //}
    } );
    //匹配密码正则
    var password = /^[a-zA-Z0-9]{6,22}$/;
    $("#pwd_2").blur( function blur_pwd() {
        //console.log($("#telphone").val());
        //if (password.test($("#pwd").val())){
        //    console.log($("#pwd").val());
        //    if($("#pwd").val()!=$("#confirmpwd").val()){
        //        console.log('两次输入不一致，请重新输入!');
        //    }
        //    console.log('密码正确');
        //}else{
        //    console.log('密码格式不正确,请重新输入');
        //    $(this).focus();
        //}
    } );

    var Code1;
    //当点击刷新图片验证码时再次请求图片验证码接口
    var Code1;
    $('#btn_3_2').click(function (event) {

        $.ajax({
            url: "http://192.168.10.100/index.php/api/Publices/getImageCode",
            type: "post",
            async: true,
            dataType:"json",    //跨域json请求一定是jsonp
            jsonp: "callbackparam",    //跨域请求的参数名，默认是callback
            data: {

            },
            crossDomain: true == !(document.all),
            success: function(data,event) {
                //阻止事件冒泡
                cancel_Bubble(event);

                session_id = data.session_id;
                console.log(data.session_id);
                var timestamp = Date.parse(new Date())+parseInt(500*Math.random());
                if(data.code == 1000){
                    console.log(data.data);
                    $("#btn_3_2 img").attr("src", data.data+"?v="+timestamp);
                    //Dat = data.data;
                    //阻止事件冒泡
                    cancel_Bubble(event);

                }else {
                    console.log("服务器返回参数出错");
                }

            },
            error: function () {
                // alert("请求失败");
            }
        });
        //阻止事件冒泡
        cancel_Bubble(event);
        event.preventDefault();
        return false;
    });


    //点击获取短信验证码按钮触发的事件
    $('#btn-3_2').click(function (event) {
        phone_1 = $('#telphone_2').val();
        console.log(phone_1);
        if(state>0 && state<60){
            return;
        }
        var ulmgCode1=$('#tupianyzm_2').val();
        console.log(ulmgCode1);

        if(!$('#tupianyzm_2').val()){
            $('.tishi').show();
            console.log('图片验证码输入有误!');
            $('#tishi_2').html('图片验证码输入有误!');
            setTimeout(autoplay,2000);
            return;
        }else{
            $.ajax({
                //http://192.168.10.100/index.php/Api/User/sms
                url: "http://192.168.10.100/index.php/Api/User/sms",
                type: "post",
                async: true,
                dataType:"json",    //跨域json请求一定是jsonp
                jsonp: "callbackparam",    //跨域请求的参数名，默认是callback
                data: {
                    uImgCode: ulmgCode1,
                    phone:phone_1,
                    //ulmgCode1:ulmgCode1
                    PHPSESSID:session_id
                },
                crossDomain: true == !(document.all),
                success: function(data) {

                    //阻止事件冒泡
                    cancel_Bubble(event);
                    event.preventDefault();

                    console.log(data)

                    if(data.code == 1000){

                        $('#zeZhao').hide();

                        var time = 60;
                        var set=setInterval(function(){
                            $("#btn-3_2").text("重新发送"+ --time+"(s)");

                            //state = time;
                            //console.log(state);
                        }, 1000);/*等待时间*/

                        setTimeout(function(){

                            $("#btn-3_2").text("重新获取验证码");/*倒计时*/
                            clearInterval(set);

                        }, 60000);

                        //阻止事件冒泡
                        cancel_Bubble(event);
                        event.preventDefault();

                    }
                    if(data.code == 1009){
                        $('.tishi').show();
                        console.log('图片验证码有误!');
                        $('#tishi_2').html('图片验证码有误!');
                        setTimeout(autoplay,2000);
                        return;
                    }

                },
                error: function () {
                    // alert("请求失败");
                }
            });
        }
        return;
    });


    $('#btn_2').click(function (event) {
        var inps_2 = $(".txt-input_3");
        //console.log(inps);
        //console.log(inps[0].value);
        if(!inps_2[0].value || inps_2[0].value == "手机号码"){
            // console.log(inps_2[0]);
            $('.tishi').show();
            console.log('手机号不能为空!');
            $('#tishi_2').html('手机号不能为空!');
            setTimeout(autoplay,2000);
            inps_2[0].focus();
            return;
        }else if(!phone1.test($("#telphone_2").val())) {
            $('.tishi').show();
            console.log('手机号格式不正确,请重新输入!');
            $('#tishi_2').html('手机号格式不正确,请重新输入!');
            setTimeout(autoplay,2000);
            return;
        }else if(!inps_2[1].value || inps_2[1].value =="请输入图片验证码"){
            $('.tishi').show();
            console.log('图片验证码不能为空');
            $('#tishi_2').html('图片验证码不能为空');
            setTimeout(autoplay,2000);
            console.log($('#tupianyzm_2').val());
            inps_2[1].focus();
            return;
        }else if(!inps_2[2].value || inps_2[2].value =="请输入短信验证码"){
            $('.tishi').show();
            console.log('短信验证码不能为空');
            $('#tishi_2').html('短信验证码不能为空');
            setTimeout(autoplay,2000);
            console.log($('#phoneyzm_2').val());
            inps_2[2].focus();
            return;
        }else if (!inps_2[3].value || inps_2[3].value =="请设置6-20位新密码"){
            $('.tishi').show();
            console.log('密码不能为空!');
            $('#tishi_2').html('密码不能为空!');
            setTimeout(autoplay,2000);
            inps_2[3].focus();
            return;
        }else if(!password.test($("#pwd_2").val())){
            $('.tishi').show();
            console.log('密码格式不正确,请重新输入!');
            $('#tishi_2').html('密码格式不正确,请重新输入!');
            setTimeout(autoplay,2000);
            return;
        }
        var jsonObj={}
        var inps_2 = $('.txt-input_3');
        for(var i = 0; i < inps_2.length; i++){
            jsonObj[$(inps_2[i]).attr('name')] = inps_2[i].value;
        }
        console.log(jsonObj);
        console.log($('#joinphone').val());

        $.ajax({
            type:"post",    //请求方式
            async:true,    //是否异步
            url :"http://192.168.10.100/index.php/Api/User/resetPassword",
            dataType:"json",    //跨域json请求一定是jsonp
            jsonp: "callbackparam",    //跨域请求的参数名，默认是callback
            //jsonpCallback:"successCallback",    //自定义跨域参数值，回调函数名也是一样，默认为jQuery自动生成的字符串
            data:"phone="+jsonObj.phone_2 +'&check_code='+jsonObj.check_code_2_1+"&password="+jsonObj.password_2,    //请求参数
            crossDomain: true == !(document.all),
            success: function(data,event) {
                if(data.code == 1000){
                    $('#tishi_2').html('修改成功，请登录!');
                    //$('#tishi').html(data.msg);
                    $('.tishi').show();
                    setTimeout(autoplay,2000);
                    //设置3秒后关闭登录遮罩层，并刷新原页面
                    setTimeout(login_zezhao,1000);
                    function login_zezhao() {
                        $('.menu').hide();
                    }

                    //alert('注册成功,2秒刷新原页面');
                    //setTimeout(function(){
                    //    window.location = 'http://192.168.10.100/app/appdown/';
                    //},2000);

                    $('.meni').hide();

                    return;
                }
                //如果数据库中已经存在该用户，则注册失败
                else if(data.code == 1004){
                    $('#tishi_2').html(data.msg);
                    $('.tishi').show();
                    setTimeout(autoplay,2000);
                    console.log(data.msg);
                    //alert(data.msg);
                    return;
                }
                else if(data.code == 1006){
                    $('#tishi_2').html(data.msg);
                    $('.tishi').show();
                    setTimeout(autoplay,2000);
                    console.log(data.msg);
                    //alert(data.msg);
                    return;
                }
            },
            error : function () {
                $('#tishi_2').html('抱歉，修改失败!');
                $('.tishi').show();
                setTimeout(autoplay,2000);
                //console.log("抱歉，注册失败");
            }
        });
        $('#Dform_2').off('tap');
        return false;
        //阻止事件冒泡
        // cancel_Bubble(event);
        // event.preventDefault();

    });

    //实现回车键触发事件
    $(document).keyup(function(event){
        if($("#menu_2").css("display") == "block" && event.keyCode ==13){
            $("#btn_2").trigger("click");
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


$('#Dform_2').click(function (url_1,time_1) {
    setTimeout("top.location.href = '" + url_1 + "'",time_1);
});
