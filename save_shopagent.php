<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../back_init.php');
$is_showdiscount=0;
$sendstatus=0;
$agent_price =$configutil->splash_new($_POST["agent_price"]);
$agent_price=str_replace(" ","",$agent_price);
//echo $agent_price;return;
$agent_detail =$configutil->splash_new($_POST["agent_detail"]);
$sendstatus =$configutil->splash_new($_POST["sendstatus"]);
$is_showdiscount =$configutil->splash_new($_POST["is_showdiscount"]);
//echo $sendstatus;return;
$not_agent_tip ="对不起,你仍未成为代理商";
if(!empty($_POST["not_agent_tip"])){
$not_agent_tip =$configutil->splash_new($_POST["not_agent_tip"]);
}

$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
 

$query="select id from weixin_commonshop_agents where isvalid=true and customer_id=".$customer_id;
$result = mysql_query($query) or die('Query failed: ' . mysql_error());
$agent_id=-1;
while ($row = mysql_fetch_object($result)) {
	$agent_id = $row->id;
}
if($agent_id>0){
	$sql="update weixin_commonshop_agents set agent_price='".$agent_price."',not_agent_tip='".$not_agent_tip."',agent_detail='".$agent_detail."',sendstatus=".$sendstatus.",is_showdiscount=".$is_showdiscount." where id=".$agent_id;
	 //echo $sql."<br/>";return;
	mysql_query($sql);
	
}else{
	$sql = "insert into weixin_commonshop_agents(customer_id,agent_price,agent_detail,isvalid,createtime,not_agent_tip,sendstatus,is_showdiscount) values (".$customer_id.",'".$agent_price."','".$agent_detail."',true,now(),'".$not_agent_tip."',".$sendstatus.",".$is_showdiscount.")";
	// echo $sql."<br/>";
	mysql_query($sql);
 }
 $error =mysql_error();
 mysql_close($link);
//echo $error; 
 echo "<script>location.href='shop_agent.php?customer_id=".$customer_id_en."';</script>"
?>