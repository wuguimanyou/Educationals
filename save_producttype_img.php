<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../back_init.php');
require('product_type_utlity.php');
require('../common/utility_4m.php');
 
$index_catnum=-1;
$name =$configutil->splash_new($_POST["name"]);
$keyid =$configutil->splash_new($_POST["keyid"]);

$parent_id = $configutil->splash_new($_POST["parent_id"]);
$sendstyle=$configutil->splash_new($_POST["sendstyle"]);
$type_imgurl=$configutil->splash_new($_POST["type_imgurl"]);
$index_catnum=$configutil->splash_new($_POST["index_catnum"]);
$adminuser_id = $configutil->splash_new($_GET["adminuser_id"]);
$orgin_adminuser_id = $configutil->splash_new($_GET["orgin_adminuser_id"]);
$owner_general = $configutil->splash_new($_GET["owner_general"]);



if($_POST["temp47"]){
	$temp47 = $configutil->splash_new($_POST["temp47"]);		
}



$product_detail_id_cat =-1;

$type_id_5 = $configutil->splash_new($_POST["type_id_5"]);
$product_detail_id_cat = $configutil->splash_new($_POST["product_detail_id_cat"]);

//echo $type_id_5."===type_id_5===".$product_detail_id_cat."===product_detail_id_cat";

//echo "+++type_imgurl==".$type_imgurl;
$f = fopen('out_savetype.txt', 'w'); 
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
 mysql_select_db(DB_NAME) or die('Could not select database');

$customer_ids="";


			
$url="-1";
if($type_id_5>0){
	$typestrarr= explode("_",$type_id_5);
	$type_id_5 = $typestrarr[0];
	$linktype=$typestrarr[1];
	if($linktype==1){
		 $product_detail_id_cat = $configutil->splash_new($_POST["product_detail_id_cat"]);        
		 if($product_detail_id_cat>0){
			
			 $url="detail.php?customer_id=".$customer_id_en."&pid=".$product_detail_id_cat;
		 
		 }else{					
			$query3="select name from weixin_commonshop_types where isvalid=true and id=".$type_id_5;
			$result3 = mysql_query($query3) or die('Query failed: ' . mysql_error());
			$typename="";
			while ($row3 = mysql_fetch_object($result3)) {
			   $typename = $row3->name;
			}
			$url="list.php?customer_id=".$customer_id_en."&tid=".$type_id_5."&tname=".$typename;
		}
	}else if($linktype==2){
	   //图文
		$query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$type_id_5;
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		while ($row = mysql_fetch_object($result)) {
		   $website_url = $row->website_url;
		}
		$pos = strpos($website_url,"?"); 
		if($pos>0){
		   $website_url = $website_url."&C_id=".$customer_id_en;
		}else{
		   $website_url = $website_url."?C_id=".$customer_id_en;
		}
		$url = $website_url;
	}
}else{
   switch($type_id_5){
	   case -6:
		  $url="list.php?customer_id=".$customer_id_en;
		  break;
	   case -2:
		  $url="list.php?isnew=1&customer_id=".$customer_id_en;
		  break;
	   case -3:
		  $url="list.php?ishot=1&customer_id=".$customer_id_en;
		  break;
	   case -4:
		  $url="order_cart.php?customer_id=".$customer_id_en;
		  break;
	   case -7:
		  $url="class_page.php?customer_id=".$customer_id_en;
		  break;
	   case -8:
		  $url="order_list.php?customer_id=".$customer_id_en;
		  break;
   }
}
	
	$foreign_id=$type_id_5;
	
	//echo "UPDATE weixin_commonshop_types set cat_detail_id='".$product_detail_id_cat."',index_imgurl='".$type_imgurl."',index_catnum='".$index_catnum."',cat_adurl='".$url."',cat_foreign_id='".$foreign_id."' where id=".$keyid."";
	if($temp47){
		//echo "UPDATE weixin_commonshop_types set cat_detail_id='".$product_detail_id_cat."',cat_index_imgurl='".$type_imgurl."',index_catnum='".$index_catnum."',cat_adurl='".$url."',cat_foreign_id='".$foreign_id."' where id=".$keyid."";return;
		mysql_query("UPDATE weixin_commonshop_types set cat_detail_id='".$product_detail_id_cat."',cat_index_imgurl='".$type_imgurl."',index_catnum='".$index_catnum."',cat_adurl='".$url."',cat_foreign_id='".$foreign_id."' where id=".$keyid."");		
	}else{
		//echo "UPDATE weixin_commonshop_types set cat_detail_id='".$product_detail_id_cat."',index_imgurl='".$type_imgurl."',index_catnum='".$index_catnum."',cat_adurl='".$url."',cat_foreign_id='".$foreign_id."' where id=".$keyid."";return;
		mysql_query("UPDATE weixin_commonshop_types set cat_detail_id='".$product_detail_id_cat."',index_imgurl='".$type_imgurl."',index_catnum='".$index_catnum."',cat_adurl='".$url."',cat_foreign_id='".$foreign_id."' where id=".$keyid."");
	}
	//mysql_query("UPDATE weixin_commonshop_types set index_imgurl='".$type_imgurl."',index_catnum='".$index_catnum."' where id=".$keyid);
	//return;
	
	//echo "update weixin_commonshop_types set cat_detail_id=".$product_detail_id_cat.",index_imgurl='".$type_imgurl."',index_catnum='".$index_catnum."',cat_adurl='".$url."',cat_foreign_id='".$foreign_id."' where id=".$keyid."";
	/*if($url){	
		   
	mysql_query("update weixin_commonshop_types set cat_detail_id=".$product_detail_id_cat.",index_imgurl='".$type_imgurl."',index_catnum='".$index_catnum."',cat_adurl='".$url."',cat_foreign_id='".$foreign_id."' where id=".$keyid."");
	   
	}
	elseif($type_imgurl&&$url){	
	
	mysql_query("update weixin_commonshop_types set index_imgurl='".$type_imgurl."',index_catnum='".$index_catnum."' where id=".$keyid."");
	   
	}*/

//return;


/*if($type_id_5){
	echo mysql_query("update weixin_commonshop_types set index_imgurl='".$type_imgurl."',index_catnum='".$index_catnum."',cat_adurl='".$type_id_5."' where id=".$keyid);
	}
*/    
		
//mysql_query("update weixin_commonshop_types set index_imgurl='".$type_imgurl."',index_catnum='".$index_catnum."' where id=".$keyid);
	
 $error =mysql_error();
 mysql_close($link);
 echo $error; 

 echo "<script>location.href='defaultset.php?default_set=1&customer_id=".$customer_id_en."';</script>"
?>