<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../back_init.php');

$commission = 0;
$limit_day = -1;
$limit_money = -1;

//$commission =$configutil->splash_new($_POST["commission"]);
//$commission = round($commission,2);
$deposit =$configutil->splash_new($_POST["deposit"]);
$deposit = round($deposit,2);
$supply_detail =$configutil->splash_new($_POST["supply_detail"]);
$limit_day =$configutil->splash_new($_POST["limit_day"]);  //限制时间
$limit_money =$configutil->splash_new($_POST["limit_money"]);  //限制金额
$not_supply_tip ="对不起,你仍未成为供应商";
if(!empty($_POST["not_supply_tip"])){
$not_supply_tip =$configutil->splash_new($_POST["not_supply_tip"]);
}

$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
mysql_query("SET NAMES UTF8");

$query="select id from weixin_commonshop_supplys where isvalid=true and customer_id=".$customer_id;
$result = mysql_query($query) or die('Query failed: ' . mysql_error());
$supply_id=-1;
while ($row = mysql_fetch_object($result)) {
	$supply_id = $row->id;
}
if(!empty($deposit)){
	if($supply_id>0){
		
		$sql="update weixin_commonshop_supplys set deposit=".$deposit.",commission=".$commission.",limit_day=".$limit_day.",limit_money=".$limit_money.",supply_detail='".$supply_detail."',not_supply_tip='".$not_supply_tip."' where id=".$supply_id;
	
	}else{
		
		$sql = "insert into weixin_commonshop_supplys(customer_id,deposit,commission,supply_detail,isvalid,createtime,not_supply_tip,limit_day,limit_money) values (".$customer_id.",".$deposit.",".$commission.",'".$supply_detail."',true,now(),'".$not_supply_tip."',".$limit_day.",".$limit_money.")";
		// echo $sql."<br/>";
		
	}
	mysql_query($sql);
}

 $error =mysql_error();
 mysql_close($link);
//echo $error; 
 echo "<script>location.href='shop_supply.php?customer_id=".$customer_id_en."';</script>"
?>