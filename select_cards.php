<?php
header("Content-type: text/html; charset=utf-8");     
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
//require('../back_init.php'); 
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD); 
mysql_select_db(DB_NAME) or die('Could not select database');
//头文件----start
require('../common/common_from.php');
//头文件----end


?>
<!DOCTYPE html>
<html>
<head>
    <title>请选择会员卡</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta content="no" name="apple-touch-fullscreen">
    <meta name="MobileOptimized" content="320"/>
    <meta name="format-detection" content="telephone=no">
    <meta name=apple-mobile-web-app-capable content=yes>
    <meta name=apple-mobile-web-app-status-bar-style content=black>
    <meta http-equiv="pragma" content="nocache">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8">
    
    <link type="text/css" rel="stylesheet" href="./assets/css/amazeui.min.css" />
    <link type="text/css" rel="stylesheet" href="./css/order_css/global.css" />    
    <link type="text/css" rel="stylesheet" href="./css/css_orange.css" />   

    <script type="text/javascript" src="./assets/js/jquery.min.js"></script>    
    <script type="text/javascript" src="./assets/js/amazeui.js"></script>
    <script type="text/javascript" src="./js/global.js"></script>
    <script type="text/javascript" src="./js/loading.js"></script>
    <script src="./js/jquery.ellipsis.js"></script>
    <script src="./js/jquery.ellipsis.unobtrusive.js"></script>
    
    <link rel="stylesheet" id="wp-pagenavi-css" href="./css/list_css/pagenavi-css.css" type="text/css" media="all">
    <link rel="stylesheet" id="twentytwelve-style-css" href="./css/list_css/style.css" type="text/css" media="all">
    <script src="./js/r_global_brain.js" type="text/javascript"></script>
    <script type="text/javascript" src="./js/r_jquery.mobile-1.2.0.min.js"></script>
    <link type="text/css" rel="stylesheet" href="./css/list_css/r_style.css" />
    <!-- <link href="./css/goods_css/mobiscroll/bootstrap.min.css" rel="stylesheet" type="text/css">  -->
    <!-- Mobiscroll JS and CSS Includes -->
    <!-- <link href="./css/goods_css/mobiscroll/mobiscroll.custom-2.17.1.min.css" rel="stylesheet" type="text/css">
    <script src="./js/goods_js/mobiscroll/mobiscroll.custom-2.17.1.min.js" type="text/javascript"></script> -->
	
    
<style>
	#middle-tab .area-one {float:left;width:25%!important;padding-bottom: 0px !important; padding-top: 0px !important;border-bottom:none!important;}
	#middle-tab .area-one .item{ height:50px;line-height:50px;margin-top:0px !important;font-size:15px!important;margin-top: 0px!important;}
	#middle-tab .area-one div{margin-top: 0px!important;}
	.menu_selected{position:absolute;font-size:15px;top:47px;left:45%;}
	.my_info{width:100%;height:65px;line-height:65px;background-color:white;padding-left:10px;border-bottom:1px solid #d1d1d1;}
	.content-base-size{float:left; width: 60px; height: 35px; line-height: 33px; margin-left:3px;margin-top:5px;  border : 1px solid #c4c4c4;background-color:white;color:black;}
	.content-base-size-selected{background:url('images/goods_image/2016042705.png'); background-size:100%;}
	.info_left{width:40%;float:left;}
	.info_left .up{width:100%;float:left;text-align:left;line-height: 30px;color:#1c1f20;}
	.info_left .down{width:100%;float:left;text-align:left;line-height: 15px;color:#a1a1a1;}
	.info_middle{width:20%;float:left;color:rgb(183, 183, 183);font-size:15px;}
	.info_right{width:40%;float:right;color:black;text-align:right;padding-right:10px;font-size:23px;}
    .area-line{height:40px;width:1%;float:left;margin-top: 25px;padding-top: 20px;border-left:1px solid #fff;}
    .area-one{width:49%;float:left;color:white;padding-top: 10px;}
     #detail-count{height:90px; width:100%;text-align: center;}
    .big_number{font-size:30px;color:white;}
    .big_txt{font-size:15px;color:white;margin-top: 10px;}
    .period{width:100%;height:50px;text-align: center;}
    .period_left{width: 55%;height: 40px;float: left;line-height: 40px;text-align: left;padding-left: 10px;}
    .period_right{width:45%;height:50px;float:right;padding:10px;text-align:right;}
    .period_left img{margin-left: 5px;width: 17px;vertical-align: middle;}
    .period_right img{height:14px;vertical-align:middle;float: right;padding-left: 10px;}
    .tis{text-align: center;font-family: "微软雅黑";font-size: 26px;color:#ccc;margin-top: 10%;display: none}
	.down span{margin-top: 5px;display: inherit;}
	.width_90{width:90%}
	#end_down{text-align: center;}
	#end_down span{line-height: 58px;font-size: 18px;}
    
</style>

</head>
<!-- Loading Screen -->
<div id='loading' class='loadingPop'style="display: none;"><img src='./images/loading.gif' style="width:40px;"/><p class=""></p></div>

<body data-ctrl=true style="background:#fff;">
	<!-- <header data-am-widget="header" class="am-header am-header-default">
		<div class="am-header-left am-header-nav" onclick="goBack();">
			<img class="am-header-icon-custom" src="./images/center/nav_bar_back.png" style="vertical-align:middle;"/><span style="margin-left:5px;">返回</span>
		</div>
	    <h1 class="am-header-title" style="font-size:18px;padding-top: 15px;">累积收益</h1>
	</header>
	<div class="topDiv"></div> --><!-- 暂时隐藏头部导航栏 -->

    <div class="contentlist"><!--异步加载数据-->	
    </div>

    <div class="tis" style="padding-bottom:30px;">---暂无数据---</div>
      
    <input type="hidden" id="IsEnd" value="0">
    <input type="hidden" id="Search" value="1">
</body>		

<script type="text/javascript">
    var winWidth = $(window).width();
    var winheight = $(window).height();
	var page = 0;		//页数
	var op = 'getcards'	//
	var user_id = '<?php echo $user_id ;?>';
	
	
   //选择会员卡并跳转回确认订单页面
    function select_card(id,obj){
		var thiss = $(obj);
		//console.log(thiss);
		
		var rtn_card_array = new Array();
		var card_remain  = thiss.attr('card_remain');		//余额
		var remain_score = thiss.attr('remain_score');		//积分
		var discount     = thiss.attr('discount');			//折扣	 
		var cardname     = thiss.attr('cardname');			//会员卡名称	 
		rtn_card_array.push(card_remain,remain_score,discount,cardname);
		console.log(rtn_card_array);
		/* 将GET方法改为POST ----start---*/
		var strurl = "order_form.php?customer_id=<?php echo $customer_id_en;?>";
		
		var objform = document.createElement('form');
		document.body.appendChild(objform);
		
		//选择的会员卡ID		
		var obj_p = document.createElement("input");
		obj_p.type = "hidden";
		objform.appendChild(obj_p);
		obj_p.value = id;
		obj_p.name = 'select_card_id';
		
		//会员卡信息
		var obj_p = document.createElement("input");
		obj_p.type = "hidden";
		objform.appendChild(obj_p);
		obj_p.value = rtn_card_array;
		obj_p.name = 'rtn_card_array';
		
		objform.action = strurl;
		objform.method = "POST"
		objform.submit();
		/* 将GET方法改为POST ----end---*/
    }
    

    var i = 1;
    $(window).scroll(function() {		//下拉滑动加载
        var pageH = $(document.body).height();
        var scrollT = $(window).scrollTop(); //滚动条top
        var aa = (pageH-winheight-scrollT)/winheight;
		console.log(pageH);
		console.log(scrollT);
		     		
		if(aa<0.3){ 
		
            popClose();  				//自动加载
        }

    });
   
    function popClose(){
   
        $.ajax({
            url     :   'select_cards.class.php?op='+op,
            dataType:   'json',
            type    :   "post",
            data    :{  
                        'user_id':user_id,        //查询该类型的具体类型
                        'page':page,        //页数
                    },
            success:function(res){
				 var res=eval(res);
				//console.log(res);
				var data = res.data;
                var html="";
               console.log(data);
                if(data==''){         
                    $(".tis").show();
                }else{
                    for(i in data){
						var card_member_id 	= data[i]['card_member_id'];	//会员卡ID
						var card_remain 	= data[i]['card_remain'];		//余额
						var remain_score 	= data[i]['remain_score'];		//积分
						var discount 		= data[i]['discount'];			//折扣						
						var cardname     	= data[i]['cardname'];			//会员卡名称
						
                        html += '<div class="my_info" onclick="select_card('+card_member_id+',this);" card_member_id='+card_member_id+' card_remain="'+card_remain+'" remain_score="'+remain_score+'" discount="'+discount+'" cardname="'+cardname+'">';
                        html += '   <div class="info_left width_90" style="">';
                        html += '       <div class="up" ><span>'+cardname+'</span></div>';
                        html += '       <div class="down" ><span>'+'余额：'+card_remain+'元，积分：'+remain_score+'，享受折扣：'+discount+'%'+'</span></div>';
                        html += '   </div>';
                        html += '</div>';
						
						
                    }	
						var html_end = '';
						//添加不选择任何折扣
						html_end += '<div class="contentlist">'	;
						html_end += '<div class="my_info" onclick="select_card(-1,this);" card_member_id="" card_remain="0" html += remain_score="0" discount="0" cardname="">';
						html_end += '<div class="info_left width_90" style="">';

						html_end += '<div class="down end_down" id="end_down"><span>不选择会员卡</span></div>';
						html_end += '</div>';
						html_end += '</div>';
						//添加不选择任何折扣
						
                    $(".contentlist").append(html+html_end);			//加载数据
                    $(".tis").hide();						//暂无数据
                } 
				
                page++;   
            }

        });

    }

  	
</script>

<script>
function showtime(){
        $(".am-share").show();
    }

</script>   
</html>