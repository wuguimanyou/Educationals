<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$expressname =$configutil->splash_new($_POST["expressname"]);
$expressnum =$configutil->splash_new($_POST["expressnum"]);

//echo 'content='.$keyword;
$order_id =$configutil->splash_new($_POST["order_id"]);
$imgurl = "";
 $link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
 mysql_select_db(DB_NAME) or die('Could not select database');


 mysql_query("update weixin_commonshop_orders set expressname='".$expressname."',expressnum='".$expressnum."' where id=".$order_id);

 
 $error =mysql_error();
 mysql_close($link);
 //echo $error; 
 echo "<script>location.href='order.php?customer_id=".$customer_id_en."';</script>"
?>