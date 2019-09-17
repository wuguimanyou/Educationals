<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../common/utility_shop.php');
$shopMessage= new shopMessage_Utlity(); 
//头文件----start
// require('../common/common_from.php');
//头文件----end
if(!empty($_GET["user_id"])){
    $user_id=$configutil->splash_new($_GET["user_id"]);
    $user_id = passport_decrypt($user_id);
}else{
    if(!empty($_SESSION["user_id_".$customer_id])){
        $user_id=$_SESSION["user_id_".$customer_id];
    }
}

$query="select id,status from weixin_commonshop_applysupplys where isvalid=true and user_id=".$user_id;
$result = mysql_query($query) or die('Query failed: ' . mysql_error());
$is_supply = -1;
$status	   = 0;
while ($row = mysql_fetch_object($result)) {
   $is_supply = $row->id;
   $status 	  = $row->status;
   break;
}

$query = "select id,deposit,supply_detail,not_supply_tip from weixin_commonshop_supplys where isvalid=true and customer_id=".$customer_id;
$result = mysql_query($query) or die('Query failed: ' . mysql_error());
$deposit		= "";	//供应商押金
$supply_detail	= "";	//供应商详情
$not_supply_tip = "";	//非供应商提醒
$supply_id 		= -1;
while ($row = mysql_fetch_object($result)) {
	$supply_id		= $row->id;
    $deposit		= $row->deposit;
	$supply_detail	= $row->supply_detail;
	$not_supply_tip = $row->not_supply_tip;

}

if($is_supply<0){
	$sql="insert into weixin_commonshop_applysupplys(user_id,deposit,supply_money,status,isvalid,createtime) values(".$user_id.",".$deposit.",0,0,true,now())"; 

	 if(!empty($deposit)){
		 mysql_query($sql);
		 $data['status'] = 1;
	 }
}
$parent_id = -1;
$query = "select parent_id from weixin_users where isvalid=true and id=".$user_id;
$result = mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$parent_id = $row->parent_id;
	break;
}
//生命周期
$shopMessage->ChangeRelation_new($user_id,$parent_id,$parent_id,$customer_id,3,2,-1);

echo json_encode($data);
?>