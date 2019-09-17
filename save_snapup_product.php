<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../config.php');
require('../../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD); 
mysql_select_db(DB_NAME) or die('Could not select database');

$keyid = -1;
$product = array();
if(!empty($_POST["keyid"])){
	$keyid = $configutil->splash_new($_POST["keyid"]);
}
if(!empty($_POST["product"])){
	$product = $_POST["product"];
}
$pro_num = count($product);

$sql = "update snapup_product_t set isvalid=false where customer_id=".$customer_id." and time_id=".$keyid;
mysql_query($sql) or die('sql failed'.mysql_error());

for($i=0;$i<$pro_num;$i++){
	$query = "select id from snapup_product_t where customer_id=".$customer_id." and time_id=".$keyid." and pid=".$product[$i];
	$result = mysql_query($query) or die('Query failed'.mysql_error());
	$sp_id = -1;
	while($row = mysql_fetch_object($result)){
		$sp_id = $row->id;
	}
	if($sp_id>0){
		$query2 = "update snapup_product_t set isvalid=true where customer_id=".$customer_id." and time_id=".$keyid." and pid=".$product[$i];
	}else{
		$query2 = "insert into snapup_product_t(pid,customer_id,time_id,isvalid) values(".$product[$i].",".$customer_id.",".$keyid.",true)";
	}
	mysql_query($query2) or die('Query failed2'.mysql_error());
}

mysql_close($link);
echo "<script>location.href='snapup_product.php?keyid=".$keyid."&customer_id=".$customer_id_en."';</script>";
?>