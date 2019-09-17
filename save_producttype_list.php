<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
 mysql_select_db(DB_NAME) or die('Could not select database');
$sendstyle2 =$configutil->splash_new($_POST["sendstyle2"]);

$sql="update weixin_commonshops set list_type=".$sendstyle2." where customer_id=".$customer_id;
mysql_query($sql) or die('Query failed: ' . mysql_error());   

 $error =mysql_error();
 mysql_close($link);
 echo $error; 
 echo "<script>location.href='product_type.php?customer_id=".$customer_id_en."';</script>"
?>