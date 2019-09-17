<?php

header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../common/utility.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

//头文件----start
require('../common/common_from.php');
//头文件----end
require('select_skin.php');
/******搜索内容******/

$search_data = '';
if(!empty($_GET["search_data"])){
	$search_data = $configutil->splash_new($_GET["search_data"]);
}

//s_t:1全站搜索2店内搜索
$supplier_id = 0;
if(!empty($_GET["supplier_id"])){
	$supplier_id = $configutil->splash_new($_GET["supplier_id"]);
}  
$placeholder="搜索";
if(0<$supplier_id){
	$placeholder="搜索本店内宝贝";
}


?>
<!DOCTYPE html>
<html>
<head>
    <title>搜索</title>
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
    
    <script type="text/javascript" src="./assets/js/jquery.min.js"></script>    
    <script type="text/javascript" src="./assets/js/amazeui.js"></script>
    <script type="text/javascript" src="./js/global.js"></script>
    <script type="text/javascript" src="./js/loading.js"></script>
    <script src="./js/jquery.ellipsis.js"></script>
    <script src="./js/jquery.ellipsis.unobtrusive.js"></script>
    <script src="./js/jquery-2.1.3.min.js"></script>
    <script src="./js/jquery-cookie.js"></script>
    
</head>

<link type="text/css" rel="stylesheet" href="./css/vic.css" />
<link type = "text/css" rel = "stylesheet" href = "./css/goods/global.css">
<link type = "text/css" rel = "stylesheet" href = "./css/goods/search.css">
<link type="text/css" rel="stylesheet" href="./css/css_<?php echo $skin ?>.css" />
<style type="text/css">
	.am-btn{border:1px solid #DEDBD5;color: #777;background-color: #fff}
    .selected{border-bottom: none;}
</style>
<body data-ctrl=true >
	<!-- <header data-am-widget="header" class="am-header am-header-default header">
		<div class="am-header-left am-header-nav header-btn">
			<img class="am-header-icon-custom" src="./images/center/nav_bar_back.png" /><span>返回</span>
		</div>
	    <h1 class="header-title">男春装</h1>
	    <div class="am-header-right am-header-nav">
			<img class="am-header-icon-custom header-list-btn" src="./images/center/nav_home.png" />
		</div>
	</header>
	
    <div class="topDiv"></div> --><!-- 暂时屏蔽头部 -->
    
    <div class = "content" id="shopTypeContainerDiv">
        <!--<div class = "content-header">
            <div class="am-input-group">
                <input id="tvKeyword" class="am-form-field search" type="text" placeholder="搜索" style="">
                <span class="am-input-group-btn">
                    <button class="am-btn sousuo-btn" type="button" style="margin-left:6px;background-color:white;">搜索</button>
                </span>
            </div>
        </div>
		-->
		<div id="m-type-area">
			<?php if(0<$supplier_id){?>
            <div>
            	<div id="shopType1" class='search_top selected'><span>本店</span></div>
            	<div id="shopType2" class='search_top'><span>全站</span></div>
				<input id="search_from"  type="hidden"  value="2"> <!--1全站2本店-->
            </div>
			<?php }?>
            <div class="am-input-group">
                <input id="tvKeyword" class="am-form-field search" type="text" value="" placeholder="<?php echo $placeholder;?>" style="">
                <span class="am-input-group-btn">
                    <button class="am-btn cancel sousuo-btn" type="button">搜索</button>
                </span>
            </div>
        </div>
		
        <div class="typeDiv1">
            <div class = "type-div-header" >
                <img class="am-header-icon-custom" src="./images/goods_image/icon_search.png"/><span>最近搜索</span>
            </div>
            <div class = "type-content" id="productDiv1">
                
            </div>
			<span id="cookiemore"></span>
			<!--<button class="am-btn small-type-button" id="more1" type="button">更多...</button>-->
        </div>
    	<div class="typeDiv2" id="productDiv">
            <div class = "type-div-header">
                <img style="vertical-align:text-top;padding-top:2px" class="am-header-icon-custom" src="./images/vic/icon_fire.png"/><span>热门搜索</span>
            </div>
			<div class = "type-content" id="productDiv2">
			<?php 
			$query = "select ht.name,ht.relate_type_id from weixin_commonshop_hot_search as ht inner join weixin_commonshop_types as type on ht.relate_type_id=type.id  where ht.isvalid=true and ht.customer_id=".$customer_id." order by ht.asort asc ";
			$ht_name = '';			//关键词
			$ht_relate_type_id = '';//关联分类ID
			$k = 0;
			$result=mysql_query($query)or die('Query failed'.mysql_error());
			while($row=mysql_fetch_object($result)){
				$ht_name 		= $row->name;
				$relate_type_id = $row->relate_type_id;
			
			?>
            
                <button class="am-btn small-type-button cmd-btn2 <?php if($k>5)echo 'productDiv_hide'; ?>" type="button" tid='<?php echo $relate_type_id ;?>' onclick="search_type('<?php echo $relate_type_id;?>','<?php echo $ht_name;?>')"><?php echo $ht_name ;?></button>
               
				
			<?php  $k++; } ?>	
			<!--  <button class="am-btn small-type-button cmd-btn" type="button">运动男装</button>
                <button class="am-btn small-type-button cmd-btn" type="button">韩版连衣裙春装</button>
                <button class="am-btn small-type-button cmd-btn" type="button">面膜</button>
                <button class="am-btn small-type-button cmd-btn" type="button">职业男装&nbsp;春装</button>
                <button class="am-btn small-type-button cmd-btn" type="button">春秋装连衣裙</button> -->
            </div>
			<?php if($k>5){?>
				<button class="am-btn small-type-button" id="more2" type="button">更多...</button>
			<?php }?>
        </div>
    </div>
    
</body>		
<script>
var customer_id = '<?php echo $customer_id_en ;?>';
var supply_id = '<?php echo $supplier_id ;?>';
var user_id = '<?php echo $user_id ;?>';
$("#shopType1").click(function(){ //点击本店，全站
	$("#shopType1").addClass("selected");
	$("#shopType2").removeClass("selected");
	$("#tvKeyword").attr("placeholder","搜索本店内宝贝");
	$("#search_from").attr("value",2);
});
$("#shopType2").click(function(){
	$("#shopType2").addClass("selected");
	$("#shopType1").removeClass("selected");
	$("#tvKeyword").attr("placeholder","搜索全站内宝贝");
	$("#search_from").attr("value",1);
});

</script>
<script src="./js/goods/search.js"></script>

<!--引入微信分享文件----start-->
<?php require('../common/share.php');?>
<!--引入微信分享文件----end-->

</body>
</html>