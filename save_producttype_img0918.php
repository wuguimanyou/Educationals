<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
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


echo $type_id_5 = $configutil->splash_new($_POST["type_id_5"])."<br>";
echo $product_detail_id_cat = $configutil->splash_new($_POST["product_detail_id_cat"])."<br>";

//echo "+++type_imgurl==".$type_imgurl;
$f = fopen('out_savetype.txt', 'w'); 
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
 mysql_select_db(DB_NAME) or die('Could not select database');
$customer_ids="";

			
$url="";
if($type_id_5>0){
	$typestrarr= explode("_",$type_id_5);
	$type_id_5 = $typestrarr[0];
	$linktype=$typestrarr[1];
	if($linktype==1){
		 $product_detail_id_2 = $configutil->splash_new($_POST["product_detail_id_2"]);        
		 if($product_detail_id_2>0){
			
			 $url="detail.php?customer_id=".$customer_id."&pid=".$product_detail_id_2;
		 
		 }else{					
			$query3="select name from weixin_commonshop_types where isvalid=true and id=".$type_id_5;
			$result3 = mysql_query($query3) or die('Query failed: ' . mysql_error());
			$typename="";
			while ($row3 = mysql_fetch_object($result3)) {
			   $typename = $row3->name;
			}
			$url="list.php?customer_id=".$customer_id."&tid=".$type_id_5."&tname=".$typename;
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
		   $website_url = $website_url."&C_id=".$customer_id;
		}else{
		   $website_url = $website_url."?C_id=".$customer_id;
		}
		$url = $website_url;
	}
}else{
   switch($type_id_5){
	   case -6:
		  $url="list.php?customer_id=".$customer_id;
		  break;
	   case -2:
		  $url="list.php?isnew=1&customer_id=".$customer_id;
		  break;
	   case -3:
		  $url="list.php?ishot=1&customer_id=".$customer_id;
		  break;
	   case -4:
		  $url="order_cart.php?customer_id=".$customer_id;
		  break;
	   case -7:
		  $url="class_page.php?customer_id=".$customer_id;
		  break;
	   case -8:
		  $url="order_list.php?customer_id=".$customer_id;
		  break;
   }
}
	echo $url;
	//echo "update weixin_commonshop_types set index_imgurl='".$type_imgurl."',index_catnum='".$index_catnum."',cat_adurl='".$url."' where id=".$keyid."";
	if($url){	
	echo 222;		
	mysql_query("update weixin_commonshop_types set index_imgurl='".$type_imgurl."',index_catnum='".$index_catnum."',cat_adurl='".$url."' where id=".$keyid."");
	   
	}
	elseif($type_imgurl&&$url){	
	echo 111;		
	mysql_query("update weixin_commonshop_types set index_imgurl='".$type_imgurl."',index_catnum='".$index_catnum."' where id=".$keyid."");
	   
	}
	return;



/*if($type_id_5){
	echo mysql_query("update weixin_commonshop_types set index_imgurl='".$type_imgurl."',index_catnum='".$index_catnum."',cat_adurl='".$type_id_5."' where id=".$keyid);
	}
*/    
		
//mysql_query("update weixin_commonshop_types set index_imgurl='".$type_imgurl."',index_catnum='".$index_catnum."' where id=".$keyid);
	
 $error =mysql_error();
 mysql_close($link);
 echo $error; 
 echo "<script>location.href='defaultset.php?default_set=1&customer_id=".$customer_id."';</script>"
?>