﻿<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
require('../back_init.php');
$name =$configutil->splash_new($_POST["name"]);
$keyid =$configutil->splash_new($_POST["keyid"]);

$orgin_price = $configutil->splash_new($_POST["orgin_price"]);
$now_price=$configutil->splash_new($_POST["now_price"]);
$storenum=$configutil->splash_new($_POST["storenum"]);

$type_id=$configutil->splash_new($_POST["type_id"]);
$pro_price_detail = $configutil->splash_new($_POST["pro_price_detail"]);

$imgids=$configutil->splash_new($_POST["imgids"]);
$tradeprices=$configutil->splash_new($_POST["tradeprices"]);
$introduce = $configutil->splash_new($_POST["introduce"]);
$description = $configutil->splash_new($_POST["description"]);
$propertyids = $configutil->splash_new($_POST["propertyids"]);

$isout = $configutil->splash_new($_POST["isout"]);
$isnew = $configutil->splash_new($_POST["isnew"]);
$ishot = $configutil->splash_new($_POST["ishot"]);

$type_ids=$configutil->splash_new($_POST["type_ids"]);
$asort = $configutil->splash_new($_POST["asort"]);
//echo $imgids;
if(!empty($_POST["pro_discount"])){
   $pro_discount=$configutil->splash_new($_POST["pro_discount"]);
}else{
  $pro_discount=0;
}

if(!empty($_POST["pro_reward"])){
   $pro_reward=$configutil->splash_new($_POST["pro_reward"]);
}else{
  $pro_reward=0;
}

$pagenum = 1;
if(!empty($_GET["pagenum"])){
   $pagenum = $_GET["pagenum"];
}


$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
 mysql_select_db(DB_NAME) or die('Could not select database');

 if($keyid>0){
    $sql="update weixin_commonshop_products set type_ids='".$type_ids."',pro_discount=".$pro_discount.",pro_reward=".$pro_reward.", name='".$name."',type_id=".$type_id.",orgin_price=".$orgin_price.",now_price=".$now_price.",storenum=".$storenum.",introduce='".$introduce."',description='".$description."',asort=".$asort.",isout=".$isout.",isnew=".$isnew.",ishot=".$ishot.",tradeprices='".$tradeprices."',propertyids='".$propertyids."' where id=".$keyid;
	//echo $sql;
	mysql_query($sql);
	//清除图片
	
	if($imgids!=""){
		//$query="update  weixin_commonshop_product_imgs set isvalid=false where product_id=".$keyid;
		//mysql_query($query);
		//插入图片
		$imgidarr = explode("_",$imgids);

		for($i=0;$i<count($imgidarr);$i++){
		   $imgid = $imgidarr[$i];
		   
		   $sql = "update weixin_commonshop_product_imgs set product_id=".$keyid.",isvalid=true where id=".$imgid;
		   mysql_query($sql);
		}
	}
	//echo $pro_price_detail."<br/>";
	$pro_price_details = explode("-",$pro_price_detail);
	$plen = count($pro_price_details);
	
	$query=" delete from weixin_commonshop_product_prices where product_id=".$keyid;
	mysql_query($query);
	
	for($i=0;$i<$plen;$i++){
	    $pro_price_item = $pro_price_details[$i];
	    if(empty($pro_price_item)){
		   continue;
		}
		$pro_price_items = explode(",",$pro_price_item);
		$pilen = count($pro_price_items);
		$proids =$pro_price_items[0];
		$proprices = $pro_price_items[1];
		$p_orgin_price=$orgin_price;
		$p_now_price=$now_price;
		$p_storenum=$storenum;
		$pprices = explode("_",$proprices);
		$pplen = count($pprices);
		
	    $p_o_price = $pprices[0];
	    $p_n_price = $pprices[1];
		$p_n_storenum = $pprices[2];
	    if(!empty($p_o_price) or $p_o_price=="0"){
		  $p_orgin_price = $p_o_price;
	    }
	    if(!empty($p_n_price) or $p_n_price=="0"){
		  $p_now_price = $p_n_price;
	    }
		
		 if(!empty($p_n_storenum) or $p_n_storenum=="0"){
		  $p_storenum = $p_n_storenum;
	    }
		
		
	    $query="insert into weixin_commonshop_product_prices(product_id,proids,orgin_price,now_price,storenum) value(".$keyid.",'".$proids."',".$p_orgin_price.",".$p_now_price.",".$p_storenum.")";	
	    mysql_query($query);
	}
 }else{
    $sql="insert into weixin_commonshop_products(name,type_id,orgin_price,now_price,storenum,introduce,description,asort,isout,isnew,ishot,customer_id,isvalid,createtime,tradeprices,propertyids,pro_discount,pro_reward,type_ids)";
	$sql=$sql." values('".$name."',".$type_id.",".$orgin_price.",".$now_price.",".$storenum.",'".$introduce."','".$description."',".$asort.",".$isout.",".$isnew.",".$ishot.",".$customer_id.",true,now(),'".$tradeprices."','".$propertyids."',".$pro_discount.",".$pro_reward.",'".$type_ids."')";
	//echo $sql;
	mysql_query($sql);
	$error =mysql_error();
    
	$p_id = mysql_insert_id();
	
	//插入图片
	$imgidarr = explode("_",$imgids);
	for($i=0;$i<count($imgidarr);$i++){
	   $imgid = $imgidarr[$i];
	   $sql = "update weixin_commonshop_product_imgs set product_id=".$p_id." where id=".$imgid;
	   mysql_query($sql);
	}
	
	//新增产品的时候，添加属性价格、库存
	$pro_price_details = explode("-",$pro_price_detail);
	$plen = count($pro_price_details);
	
	for($i=0;$i<$plen;$i++){
	    $pro_price_item = $pro_price_details[$i];
	    if(empty($pro_price_item)){
		   continue;
		}
		$pro_price_items = explode(",",$pro_price_item);
		$pilen = count($pro_price_items);
		$proids =$pro_price_items[0];
		$proprices = $pro_price_items[1];
		$p_orgin_price=$orgin_price;
		$p_now_price=$now_price;
		$p_storenum=$storenum;
		$pprices = explode("_",$proprices);
		$pplen = count($pprices);
		
	    $p_o_price = $pprices[0];
	    $p_n_price = $pprices[1];
		$p_n_storenum = $pprices[2];
	    if(!empty($p_o_price) or $p_o_price=="0"){
		  $p_orgin_price = $p_o_price;
	    }
	    if(!empty($p_n_price) or $p_n_price=="0"){
		  $p_now_price = $p_n_price;
	    }
		
		 if(!empty($p_n_storenum) or $p_n_storenum=="0"){
		  $p_storenum = $p_n_storenum;
	    }
	    $query="insert into weixin_commonshop_product_prices(product_id,proids,orgin_price,now_price,storenum) value(".$p_id.",'".$proids."',".$p_orgin_price.",".$p_now_price.",".$p_storenum.")";	
	    mysql_query($query);
	}
 }
 
 $error =mysql_error();
 mysql_close($link);
//echo $error; 
 echo "<script>location.href='product.php?customer_id=".$customer_id."&pagenum=".$pagenum."';</script>"
?>