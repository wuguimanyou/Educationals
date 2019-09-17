<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../config.php');
require('../../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD); 
mysql_select_db(DB_NAME) or die('Could not select database');

$keyid = 0;
$pagenum = 1;
$snapup_time = '';
if(!empty($_POST["keyid"])){
	$keyid = $configutil->splash_new($_POST["keyid"]);
}
if(!empty($_POST["snapup_time"])){
	$snapup_time = $configutil->splash_new($_POST["snapup_time"]);
}
if(!empty($_GET["pagenum"])){
	$pagenum = $configutil->splash_new($_GET["pagenum"]);
}

if($keyid > 0){
	$old_snapup_time = $snapup_time;
	$query_time = "select snapup_time from snapup_time_t where isvalid=true and customer_id=".$customer_id." and id=".$keyid;
	$result_time = mysql_query($query_time) or die('$query_time failed'.mysql_error());
	while($row_time = mysql_fetch_object($result_time)){
		$old_snapup_time = $row_time->snapup_time;		//编辑前的抢购时间
	}
	if(strtotime($snapup_time) != strtotime($old_snapup_time)){
		$query = "update snapup_time_t set snapup_time='".$snapup_time."' where isvalid=true and customer_id=".$customer_id." and id=".$keyid;
		mysql_query($query) or die('query failed'.mysql_error());
		
		$query2 = "update snapup_product_t set isvalid=false where isvalid=true and customer_id=".$customer_id." and time_id=".$keyid;
		mysql_query($query2) or die('query failed2'.mysql_error());
	}
}else{
	$query2 = "insert into snapup_time_t(snapup_time,customer_id,createtime,isvalid) values('".$snapup_time."',".$customer_id.",now(),1)";
	mysql_query($query2) or die('query failed2'.mysql_error());
}

mysql_close($link);
echo "<script>location.href='snapup_time.php?customer_id=".$customer_id_en."&pagenum=".$pagenum."';</script>";
?>