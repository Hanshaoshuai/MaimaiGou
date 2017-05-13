/**
 * Created by Administrator on 2017/3/28.
 */
//window.onLoad = function () {}

//表单验证
var  session_id="";
var state;
var phone_1;
var tuiphone;
//alert($.session.get('user_id'));
//设置2秒后提示层消失
setTimeout(autoplay,2000);
function autoplay() {
    $('.tishi').hide();
}
$(function(){
    //匹配手机号正则
    var phone1 = /^0?1[3|4|5|8][0-9]\d{8}$/;
    $(document).ready(function(){  $("input[name=phone]").focus();});
    $("#telphone").blur( function blur_phone() {
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
    $("#pwd").blur( function blur_pwd() {
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

    $('#btn').click(function (event) {
                    var inps = $(".txt-input");
                    //console.log(inps);
                    //console.log(inps[0].value);
                    if (!inps[0].value){
                        $('.tishi').show();
                        console.log('请输入原密码!');
                        $('#tishi').html('请输入原密码!');
                        setTimeout(autoplay,2000);
                        inps[0].focus();
                        return;
                    }else if(!password.test($("#old_pwd").val())){
                        $('.tishi').show();
                        console.log('密码格式不正确,请重新输入!');
                        $('#tishi').html('密码格式不正确,请重新输入!');
                        setTimeout(autoplay,2000);
                        return;
                    }else if(!inps[1].value){
                        $('.tishi').show();
                        console.log('请设置6-20位新密码!');
                        $('#tishi').html('请设置6-20位新密码!');
                        setTimeout(autoplay,2000);
                        inps[1].focus();
                        return;
                    }else if(!password.test($("#new_pwd ").val())){
                        $('.tishi').show();
                        console.log('密码格式不正确,请重新输入!');
                        $('#tishi').html('密码格式不正确,请重新输入!');
                        setTimeout(autoplay,2000);
                        return;
                    }else if(!inps[2].value){
                        $('.tishi').show();
                        console.log('请确认新密码!');
                        $('#tishi').html('请确认新密码!');
                        setTimeout(autoplay,2000);
                        inps[2].focus();
                        return;
                    }else if(!password.test($("#verify_pwd ").val())){
                        $('.tishi').show();
                        console.log('密码格式不正确,请重新输入!');
                        $('#tishi').html('密码格式不正确,请重新输入!');
                        setTimeout(autoplay,2000);
                        return;
                    }else if(!password.test($("#verify_pwd ").val())){
                        $('.tishi').show();
                        console.log('密码格式不正确,请重新输入!');
                        $('#tishi').html('密码格式不正确,请重新输入!');
                        setTimeout(autoplay,2000);
                        return;
                    }else if($("#new_pwd").val()!=$("#verify_pwd").val()){
                        $('.tishi').show();
                        console.log('两次输入不一致，请重新输入!');
                        $('#tishi').html('两次输入不一致，请重新输入!');
                        setTimeout(autoplay,2000);
                        return;
                    }

        var jsonObj={}
        var inps = $('.txt-input');
        for(var i = 0; i < inps.length; i++){
            jsonObj[$(inps[i]).attr('name')] = inps[i].value;
        }
        console.log(jsonObj);
        console.log($('#joinphone').val());

                    $.ajax({
                        type:"post",    //请求方式
                        async:true,    //是否异步
                        url :"http://192.168.10.100/index.php/Api/User/upPwd",
                        dataType:"json",    //跨域json请求一定是jsonp
                        jsonp: "callbackparam",    //跨域请求的参数名，默认是callback
                        //jsonpCallback:"successCallback",    //自定义跨域参数值，回调函数名也是一样，默认为jQuery自动生成的字符串
                        data:"password="+jsonObj.password+"&npw="+jsonObj.npw+"&npwd="+jsonObj.npwd+"&user_id="+'307',    //请求参数
                        success: function(data,event) {
                            if(data.code == 1000){
                                $('#tishi').html('修改密码成功');
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
                                return;
                            } else if(data.code == 1004){
                                $('#tishi').html(data.msg);
                                $('.tishi').show();
                                setTimeout(autoplay,2000);
                                console.log(data.msg);
                                //alert(data.msg);
                                return;
                            }
                        },
                        error : function () {
                            $('#tishi').html('抱歉，注册失败!');
                            //console.log("抱歉，注册失败");
                        }
                    });
                    $('#Dform').off('tap');
                    return false;
                    //阻止事件冒泡
                    cancel_Bubble(event);
                    event.preventDefault();

                });
    //实现回车键触发事件
    $(document).keyup(function(event){
        if(event.keyCode ==13){
            $("#btn").trigger("click");
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
