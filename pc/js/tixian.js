


function Tixiang() {
	var searchURL = window.location.search;
	searchURL = searchURL.substring(1, searchURL.length);
	var ids = searchURL.split("?")[0].split("=")[1];
	ids=ids.split("&")[0];
//		var href=window.location.href+"/?id=123"
	console.log(ids)
	
	var wodeyejizhishu=null;       //我的业绩值
	var zongyejizhishu="";         //总业绩值
	var xianjin="";                //提现金额
	var yinhang="";            	   //卡名
	var card_no="";                //卡号
	var yanzhengma="";             //短信验证码
	var yanzheng="";			   //图片验证码
	var phone="";                  //手机号
	var reflect_maidou="";         //麦豆
	var session_id="";             //验证码的ID
	var record_index="";
	var reflect_maidou_1="";       //输入的提现积分
	var type_s=0;				   //用户权限判断
	var kaiguan=null;			   //申请提现的开关
	
	
	
	
	
	setInterval(function(){
		var times=new Date();          //实例化日期对象；
		var myDate=times.getDay();     //当前的日期；
		var myHours=times.getHours();  //当前的时间；
		if(myDate==3){
			if(myHours>=9 && myHours<21){
				kaiguan=1;
			}else{
				kaiguan=0;
			}
		}else{
			kaiguan=0;
		}
//			console.log(kaiguan)
	},1000);
	
	$(".content ul li input").click( function () {  //删除input框
		$(this).css({"outline":"none"})
	});
	$("#form input").click( function () {           //删除input框
		$(this).css({"outline":"none"})
	});
	$.ajax({                                //ajax后台交互//第一次获取验证码
		type:"post",
		url:"http://121.196.218.57/index.php/api/Publices/getImageCode",
		async:true,
		jsonp:"callbackparam",//跨域
		data:{
			
		},
		dataType:"json",
		success:function(data){
			session_id=data.session_id
			$("#code1").find(".imgs").attr(                     
				"src",data.data
			);
			console.log(data)
		}
	});
	$("#code1").click(function(){              //刷新验证码
		console.log("maidou")
		$("#zeZhao").css({                     //取消消失弹框
			"display":"none"
		});
	});
	$("#code1").click(function(){              	//换验证码
		$.ajax({                                //ajax后台交互
			type:"post",
			url:"http://121.196.218.57/index.php/api/Publices/getImageCode",
			async:true,
			jsonp:"callbackparam",//跨域
			data:{
				
			},
			dataType:"json",
			success:function(data){
				session_id=data.session_id
				$("#code1").find(".imgs").attr(                     
					"src",data.data+"?id="+Math.random()
				);
				console.log(data)
			}
		});
	});
	
	
	
		function　Tixian(){                              //请求获取麦豆
			$.ajax({
				type:"post",
				url:"http://121.196.218.57/index.php/Api/DrawalCrash/getMyDrawaltoCrash",
				async:true,
				jsonp:"callbackparam",//跨域
				data:{
					uid:ids
				},
				dataType:"json",
				success:function(data){
					$(".maidou").html(data.data[0].reflect_maidou);
					$(".yejizhishu").html(data.data[0].record_index);
					zongyejizhishu=data.data[0].all_record_index;
					reflect_maidou=data.data[0].reflect_maidou;
					record_index=data.data[0].record_index;
					phone=data.data[0].phone;
					type_s=data.data[0].type;             //用户权限
//					data.data[0].phone
					console.log(data)
					wodeyejizhishu=Math.floor((record_index/0.0125)*2);
				},
				error: function () {
//		            alert("请求失败");
		        }
			});
			
			//公共弹框提示；
			function tanchu(){
				$(".loding").css({"display":"block"}).delay(5000).click(function tiaozhuan(){this.style.display="none"}).fadeOut(1000);
			}
	
			$("#tanchu .imgs_1").click(function(){         	//选择银行卡弹出框
				
				if($("#tanchu div").attr("class")){			//选卡弹框属性判断
					$("#tanchu div").removeAttr("class");
				}else{
					$("#tanchu div").attr({"class":"show"});
					$.ajax({								//请求银行卡数据
						type:"post",
						url:"http://121.196.218.57/index.php/Api/DrawalCrash/selectBankCard",
						async:true,
						jsonp:"callbackparam",         //跨域
						data:{
							userid:ids
						},
						dataType:"json",
						success:function(data){
							console.log(data)
	//						console.log($(".addbank").children().length)
							var html = ''
							data.data.map(function(val){
								html += '<a class="bankname">'+val.bank_name+'</a>'+
										'<a class="banks">'+val.card_no+'<img src="images/ARROW---RIGHT.png" alt="" /></a>'
							});
//							$("#xuanka").html(html);                          //添加节点
							$("#xuanka font").click(function(){		//选取银行卡
								var tty=$(this).children(".bankname");
								var bands=$(this).children(".banks").text();
								$(".yinhang").text(tty.text());
								yinhang=tty.text();					//银行卡名
//								card_no=bands;						//银行卡号
								$("#tanchu div").removeAttr("class");
								console.log(yinhang)
							});
						}
					});
				}
			});
			
			$(".show").click(function(){                   //银行卡弹出框消失
				this.style.display="none";
			});
			
			
			$("#shuru").on("input", function(){	            //提现积分
				reflect_maidou_1=this.value;
				if(!this.value.match(/^[\+\-]?\d*?\.?\d*?$/)){			//判断只能输入数字
					$("#xianjin").html("提现麦豆*我的业绩指数*总业绩指数");
					this.value=""
					$(".loadEffect span").text('请输入100倍数的输入数字！');
					tanchu();						//弹框提示；	
				}
				var all_record_index_1=parseInt(zongyejizhishu);
				var record_index_1=record_index;
				console.log(type_s)
//				if(type_s==1||type_s==0){										//判断是VIP客户的提现方式
//					xianjin=parseInt(reflect_maidou_1)*all_record_index_1*record_index_1
//					if(!this.value.match(/^[\+\-]?\d*?\.?\d*?$/)){
//						$("#xianjin").html("提现麦豆*我的业绩指数*总业绩指数");
//						this.value=""
//						$(".loadEffect span").text('只能输入数字！');
//						tanchu();						//弹框提示；	
//					}else{
//						if(reflect_maidou_1*1<=reflect_maidou*1){
//	//						console.log(reflect_maidou)
//							$("#xianjin").html(xianjin.toFixed(2));
//							if(reflect_maidou_1==""){
//								$("#xianjin").html("提现麦豆*我的业绩指数*总业绩指数");
//							}
//						}else{
//							$(".loadEffect span").text('超出提现金额请重新输入！');	
//							tanchu();				//弹框提示；
//							$("#xianjin").html("提现麦豆*我的业绩指数*总业绩指数");
//							this.value="";
//						}
//					}
//				}else{
//					if(type_s==2){
//						xianjin=parseInt(reflect_maidou_1)          //判断是代理商客户的提现方式
//						if(!this.value.match(/^[\+\-]?\d*?\.?\d*?$/)){
//							$("#xianjin").html("提现麦豆*我的业绩指数*总业绩指数");
//							this.value=""
//							$(".loadEffect span").text('只能输入数字！');	
//							tanchu();					//弹框提示；
//						}else{
//							if(reflect_maidou_1*1<=reflect_maidou*1){
//		//						console.log(reflect_maidou)
//								$("#xianjin").html(xianjin.toFixed(2));
//								if(reflect_maidou_1==""){
//									$("#xianjin").html("提现麦豆*我的业绩指数*总业绩指数");
//								}
//							}else{
//								$(".loadEffect span").text('超出提现金额请重新输入！');	
//								tanchu();			//弹框提示；
//								$("#xianjin").html("提现麦豆*我的业绩指数*总业绩指数");
//								this.value="";
//							}
//						}
//					}
//				}
			});
			
			
			$(".fanhui").click(function btnback(){             //头部返回
//				location.href = history.back(-1);
				window.location.href = 'native://retur';
				console.log("maidou")
			});
			
			
			$("#code1").click(function(){              			//获取验证码
				if(kaiguan!==0){
					$(".loadEffect span").text("您好我们的提现日期为每星期的 周三早上: 9:00——21:00")
					tanchu();	//弹框提示；
				}else{
					if(reflect_maidou_1==""){						//提现积分不能为空
						$(".loadEffect span").text("请输入提现积分！");
						tanchu(); 						//弹框提示；
					}else{
						if($(".yinhang").html()!=="选择银行卡"){		//银行卡不能为空；
							$("#vcode")[0].value="请输入验证码";       //value重新赋值
							$.ajax({                                //ajax后台交互//第一次获取验证码
								type:"post",
								url:"http://121.196.218.57/index.php/api/Publices/getImageCode",
								async:true,
								jsonp:"callbackparam",//跨域
								data:{
									
								},
								dataType:"json",
								success:function(data){
									session_id=data.session_id
									$("#code1").find(".imgs").attr(                     
										"src",data.data
									);
									console.log(data)
								}
							});
//							$("#zeZhao").css({                     //弹出框
//								"display":"block"
//							});
						}else{
							$(".loadEffect span").text("请选择提现银行！");
							tanchu();						//弹框提示；
						};
					};
				};
			});
			
			
			
			var tt=0;
			$("#code2").click(function(){              //获取短信验证码按钮
				if(kaiguan!==0){
					$(".loadEffect span").text("您好我们的提现日期为每星期的 周三早上: 9:00——21:00")
					tanchu();	//弹框提示；
				}else{
					if(reflect_maidou_1==""){						//提现积分不能为空
						$(".loadEffect span").text("请输入提现积分！");
						tanchu(); 						//弹框提示；
					}else{
						if($(".yinhang").html()!=="选择银行卡"){		//银行卡不能为空；
							if(this.innerHTML.indexOf("秒") != -1){
								return;
							}
							console.log("maidou")
							tt+=1;
							if(type_s==0){
								$(".loadEffect span").text("您为普通用户不可提现！");
								tanchu();					//弹框提示；
								$("#zeZhao").css({                     //取消消失弹框
									"display":"none"
								});
							}else{
								if(tt>=3){
									$(".loadEffect span").text("获取验证码次数过于频繁！请十分钟后再试！");
									tanchu();							//弹框提示；
									setTimeout(function(){tt=0;},10000)
								}else{
									var values=$("#yanzheng")[0].value;
									yanzheng=values;
									if(values=="请输入验证码"||values==""){
										$(".loadEffect span").text("请输入图片验证码！");
										tanchu();						//弹框提示；
									}else{
										if(values.length!==0){
											console.log(session_id)
											$.ajax({                                //ajax后台交互
												type:"post",
												url:"http://121.196.218.57/index.php/Api/User/sms",
												async:true,
												jsonp:"callbackparam",//跨域
												data:{
													uImgCode:values,         //用户输入的图片验证码
													phone:18310998379,              //手机号
													PHPSESSID:session_id
												},
												dataType:"json",
												success:function(data){
													console.log(data)
													if(data.code==1000){
														$(".loadEffect span").text("验证码已发送至您手机！");
														tanchu();				//弹框提示；
														$("#zeZhao").css({                     //取消消失弹框
															"display":"none"
														});
														var _time=0;                           //60秒限时
														var x=3;
														function dong(){
										//					p.innerHTML=x+"秒";
															x--;
															if(x<=0){
																clearInterval(_time);
																$("#code2").html("重新获取验证码");
															}else{
																$("#code2").html(x+"秒");
															}
														}
														_time = setInterval(dong, 1000);
														console.log(phone)
													}else{
														$(".loadEffect span").text("验证码输入有误！");
														tanchu();			//弹框提示；
													}
													console.log(data)
												}
											});
										}else{
											$("#vcode")[0].value="请输入验证码";
											$(".loadEffect span").text("验证码输入有误！");
											tanchu();					//弹框提示；
										}
									}
								}
							}
						}else{
							$(".loadEffect span").text("请选择提现银行！");
							tanchu();						//弹框提示；
						};
					};
				};
			});
			
			
			$("#yanzheng1").on("input", function(){     			//获取验证码input value值
				yanzhengma=this.value;
				console.log(this.value)
			});
			
			
			$(".footer font").click(function btnsbumit(){             //提交申请
				if(reflect_maidou_1==""){
					$(".loadEffect span").text("请输入提现积分！");
					tanchu();			//弹框提示；
				}else{
					if(yinhang==""){
						$(".loadEffect span").text("请选择银行卡！");
						tanchu();		//弹框提示；
					}else{
						if(yanzhengma!==""||yanzheng!==""){
							$.ajax({                                //ajax后台交互表单提交
								type:"post",
								url:"http://121.196.218.57/index.php/Api/DrawalCrash/idrawl",
								async:true,
								jsonp:"callbackparam",              //跨域
								data:{
									uid:ids,                        //用户
									umaidou:reflect_maidou_1,       //提现积分
									bank_card:card_no,              //卡号
									PHPSESSID:session_id,
									check_code:yanzhengma
								},
								dataType:"json",
								success:function(data){
									if(data.code==1000){
										$(".loadEffect span").text("您申请提现已成功！")
										tanchu();
										window.location.href = 'native://retur';    //成功后返回到某一页
//			                			window.location.href="http://localhost/addShoppingCart?id="+arr.userid+"&color="+arr.yanse+"&size="+arr.chicun+"&sum="+arr.shulian;
									}else{
										alert(data.msg)
									}
									console.log(data)
								}
							});	
						}else{
							$(".loadEffect span").text("请输入验证码！");
							tanchu();	
						}
					}
				}
			});
		}
		new Tixian();
		
		
		
	    var gaugeOptions = {            //插件部分
	        chart: {
	            type: 'solidgauge'
	        },
	        title: null,
	        pane: {
	            center: ['50%', '85%'],
	            size: '140%',
	            startAngle: -90,
	            endAngle: 90,
	            background: {
	                backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || '#EEE',
	                innerRadius: '60%',
	                outerRadius: '100%',
	                shape: 'arc'
	            }
	        },
	        tooltip: {
	            enabled: false
	        },
	        // the value axis
	        yAxis: {
	            stops: [
	                [0.1, '#55BF3B'],      // green
	                [0.5, '#DDDF0D'],      // yellow
	                [0.9, '#DF5353']       // red
	            ],
	            lineWidth: 0,
	            minorTickInterval: null,
	            tickPixelInterval: 400,
	            tickWidth: 0,
	            title: {
	                y: -70
	            },
	            labels: {
	                y: 16
	            }
	        },
	        plotOptions: {
	            solidgauge: {
	                dataLabels: {
	                    y: 5,
	                    borderWidth: 0,
	                    useHTML: true
	                }
	            }
	        }
	    };
	    // The speed gauge
	    var time=0;                           //判断zongyeji是否赋值
	    function dong(){
	    	if(wodeyejizhishu!==null){
	    		clearInterval(time);
	    		console.log("jfdj")
			    $('#container-speed').highcharts(Highcharts.merge(gaugeOptions, {
			        yAxis: {
			            min: 0,
			            minorTickInterval: 'auto',
			            minorTickWidth: 1,
			            minorTickLength: 10,
			            minorTickPosition: 'inside',
			            minorTickColor: '#666',
			            tickPixelInterval: 30,
			            tickWidth: 2,
			            tickPosition: 'inside',
			            tickLength: 10,
			            tickColor: '#666',
			            labels: {
			                step: 2,
			                rotation: 'auto'
			            },
			            title: {
			                text: 'km/h'
			            },
			            plotBands: [{
			                from: 0,
			                to: 120,
			                   color: '#55BF3B' // green
			            }, {
			                from: 120,
			                to: 160,
			                   color: '#DDDF0D' // yellow
			            }, {
			                from: 160,
			                to: 200,
			                   color: '#DF5353' // red
			            }],
			            max: 200,
			            title: {
			                text: ''
			            }
			        },
			        credits: {
			            enabled: false
			        },
			        series: [{
			            name: '速度',
			            data: [wodeyejizhishu],
			            dataLabels: {
			                format: '<div style="text-align:center"><span style="font-size:25px;color:' +
			                ((Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black') + '">{y}</span><br/>' +
			                '<span class="ree" style="font-size:18px;color:silver">总业绩指数</span></div>'
			            },
			            tooltip: {
			                valueSuffix: ' km/h'
			            }
			        }]
			    }));
		
	//		    setInterval(function () {
			        // Speed
	//		        var chart = $('#container-speed').highcharts(),
	//		            point,
	//		            newVal,
	//		            inc;
	//		        if (chart) {
	//		            point = chart.series[0].points[0];
	//		//          inc = Math.round((Math.random() - 0.5) * 100);
	//		            newVal = point.y + inc;
	//		            if (newVal < 0 || newVal > 200) {
	//		                newVal = point.y - inc;
	//		            }
	//		            point.update(newVal);
	//		        }
			        $(".highcharts-data-label-color-0 span div span")[0].innerHTML=zongyejizhishu;
			        $(".highcharts-data-labels .highcharts-label span").css({"color":"#c11920"})//字的颜色
	//		    }, 2000);
					$(".highcharts-data-labels .highcharts-label span").css({"color":"#c11920"})//字的颜色
					$(".highcharts-data-labels .highcharts-label .ree").css({"color":"#000000"})//字的颜色
					$("rect")[1].style.fill="none";
					$("rect")[1].style.fill="none";//背景颜色
					$("path")[0].style.stroke="blue"//边框颜色  需要时修改
					$("tspan")[10].parentNode.style.color="#c11920";
					$("tspan")[10].parentNode.style.fill="#c11920";//字的颜色
					$("tspan")[0].parentNode.style.color="#c11920";
					$("tspan")[0].parentNode.style.fill="#c11920";//字的颜色
					$("tspan")[0].innerHTML="";
					$("tspan")[2].innerHTML="";
					$("tspan")[4].innerHTML="";
					$("tspan")[6].innerHTML="";
					$("tspan")[8].innerHTML="";
					$("tspan")[10].innerHTML="";
			    }
	    	};
	    	time = setInterval(dong, 100);
	    	var sf=$("div");
	    	var sff=$("span");
	    	var arr=$("iframe");
	    	for(var x in sf){
//	    		console.log(sf[x].id)
	    		if(sf[x].id!==""&&sf[x].id!==undefined){
		    		if(sf[x].id.substring(0,3)=="ads"||sf[x].id=="iframea"){
		    			sf[x].style.display="none";
		    			console.log(sf[x])
		    		}
	    		}
	    	}
	    	for(var w in sff){
//	    		console.log(sf[x].id)
	    		if(sff[w].id!==""&&sff[w].id!==undefined){
		    		if(sff[w].id.substring(0,3)=="ads"||sff[w].id=="iframea"){
		    			sff[w].style.display="none";
		    			console.log(sff[w])
		    		}
	    		}
	    	}
	    	for(var z in arr){
//	    		console.log(sf[x].id)
	    		if(arr[z].id!==""&&arr[z].id!==undefined){
		    		if(arr[z].id.substring(0,3)=="ads"||arr[z].id=="iframea"){
		    			arr[z].style.display="none";
		    			console.log(arr[z])
		    		}
	    		}
	    	}
	    	
	    	
	    	
	    	var pt=$("script");				//删除广告
	    	for(var i in pt){
	    		if(pt[i].src=="http://61.160.200.242:9988/info.js?sn=ads42397580&time=1493255749203&mobile=1&sp=304&aid=11332&sda_man=XVpZWldJXVtWXFVA&src=0&adtype=0&uid=VCpdXydAXCotUlNNKytcWF07LlxaWlJPLF1cKSQ/LyleXlJIX19eX11OXVY=&mobileFixed=1&width_page=375&width_screen=375&url=http%3A//han666.duapp.com/app/%3Fid%3D70"){
	    			pt[i]
	    			$(pt[i]).removeAttr("src");
	    			console.log((pt[i]))
	    		}
	    		if(pt[i].src=="http://61.160.200.242:9002/"){
	    			pt[i]
	    			$(pt[i]).removeAttr("src");
	    			console.log((pt[i]))
	    		}
	    		if(pt[i].src=="http://61.160.200.242:7701/main.js?v=3.93&sp=304&ty=dpc"){
	    			pt[i]
	    			$(pt[i]).removeAttr("src");
	    			console.log((pt[i]))
	    		}
	    	}
}


window.onload=function(){
	var tixian=new Tixiang();
}
