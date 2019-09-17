<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../back_init.php');
$link =mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

$qrsell_orderothers=$configutil->splash_new($_POST['qrsell_orderothers']);
$sql="update weixin_commonshops set qrsell_orderothers='".$qrsell_orderothers."' where isvalid=1 and customer_id=".$customer_id;
mysql_query($sql)or die('W_update Query failed: ' . mysql_error()); 	
mysql_close($link);
echo "<script>location.href='qrsell_orderothers.php?customer_id=".$customer_id_en."';</script>"
?>