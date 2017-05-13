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
    var txt = "请输入手机号";
    var txt_l = "请输入密码";
    $('#telphone_1').on('blur',function(){
        if($(this).val() == ""){
            $(this).val(txt);
            $(this).css("color","#888888");
        }
    })

    $('#telphone_1').on('focus',function(){
        if($(this).val() == txt){
            $(this).val("");
            $(this).css("color","#000000")
        }
    })
    $('#pwd_1').on('blur',function(){
        if($(this).val() == ""){
            $(this).attr('type','text')
            $(this).val(txt_l);
            $(this).css("color","#888888");
        }
    })

    $('#pwd_1').on('focus',function(){
        if($(this).val() == txt_l){
            $(this).val("");
            $(this).css("color","#000000")
        }
        $(this).attr('type','password')
    })
    //匹配手机号正则
    var phone1 = /^0?1[3|4|5|8][0-9]\d{8}$/;
    $(document).ready(function(){  $("input[name=phone]").focus();});
    $("#telphone_1").blur( function blur_phone() {
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
    $("#pwd_1").blur( function blur_pwd() {
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

    $('#btn_1').click(function (event) {
        var inps_1 = $(".txt-input_1");
        //console.log(inps);
        //console.log(inps[0].value);
        if(!inps_1[0].value || inps_1[0].value =="请输入手机号"){
            $('.tishi').show();
            console.log('手机号不能为空!');
            $('#tishi_1').html('手机号不能为空!');
            setTimeout(autoplay,2000);
            inps_1[0].focus();
            return;
        }else if(!phone1.test($("#telphone_1").val())) {
            $('.tishi').show();
            console.log('手机号格式不正确,请重新输入!');
            $('#tishi_1').html('手机号格式不正确,请重新输入!');
            setTimeout(autoplay,2000);
            return;
        }else if (!inps_1[1].value || inps_1[1].value == "请输入密码"){
            $('.tishi').show();
            console.log('密码不能为空!');
            $('#tishi_1').html('密码不能为空!');
            setTimeout(autoplay,2000);
            inps_1[1].focus();
            inps_1[1].value = '';
            return;
        }else if(!password.test($("#pwd_1").val())){
            $('.tishi').show();
            console.log('密码格式不正确,请重新输入!');
            $('#tishi_1').html('密码格式不正确,请重新输入!');
            $("#pwd_1").val('');
            setTimeout(autoplay,2000);
            $("#pwd_1").focus();
            return;
        }

        var jsonObj={}
        var inps_1 = $('.txt-input_1');
        for(var i = 0; i < inps_1.length; i++){
            jsonObj[$(inps_1[i]).attr('name')] = inps_1[i].value;
        }
        console.log(jsonObj);


        $.ajax({
            type:"post",    //请求方式
            async:true,    //是否异步
            url :"http://192.168.10.100/index.php/Api/User/login",
            dataType:"json",    //跨域json请求一定是jsonp
            jsonp: "callbackparam",    //跨域请求的参数名，默认是callback
            //jsonpCallback:"successCallback",    //自定义跨域参数值，回调函数名也是一样，默认为jQuery自动生成的字符串
            data:"phone="+jsonObj.phone+"&password="+jsonObj.password+"&mobid="+"PC_"+Math.random(),    //请求参数
            success: function(data,event) {
                if(data.code == 1000){
                    //获取user_id
                    //  var user_id = data.data[0].user_id;
                    //  $.session.set('user_id',user_id);
                    //alert($.session.get('user_id')) ;

                    $('#tishi_1').html(data.msg);
                    $('.tishi').show();
                    setTimeout(autoplay,2000);

                    //设置3秒后关闭登录遮罩层，并刷新原页面
                    setTimeout(login_zezhao,1000);
                    function login_zezhao() {
                        $('.menu').hide();
                    }
                    console.log(data.msg);
                    //alert(data.msg);
                    return;

                    //$('#tishi').html('登录成功!');
                    //alert('登录成功,2秒刷新原页面');
                    //setTimeout(function(){
                    //    window.location = 'http://192.168.10.100/app/appdown/';
                    //},2000);
                }
                //如果数据库中已经存在该用户，则注册失败
                else if(data.code == 1003){
                    $('#tishi_1').html(data.msg);
                    $('.tishi').show();
                    setTimeout(autoplay,2000);
                    console.log(data.msg);
                    //alert(data.msg);
                    return;
                }
            },
            error : function () {
                $('#tishi_1').html('抱歉，注册失败!');
                //console.log("抱歉，注册失败");
            }
        });
        $('#Dform_1').off('tap');
        return false;
        //阻止事件冒泡
        // cancel_Bubble(event);
        // event.preventDefault();

    });

    //实现回车键触发事件
    $(document).keyup(function(event){
        if($("#menu").css("display") == "block" && event.keyCode ==13){
            $("#btn_1").trigger("click");
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


$('#Dform_1').click(function (url_1,time_1) {
    setTimeout("top.location.href = '" + url_1 + "'",time_1);
});