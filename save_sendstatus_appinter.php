<?php
header("Content-type: text/html; charset=utf-8"); 
$customer_id = $_POST["customer_id"];
$resultArr = array();
if(empty($customer_id)){
	//echo "{\"result\":\"-3\" , \"msg\":\"缺少参数：customer_id!\" }";
	$resultArr['result'] = -3;
	$resultArr['msg'] = "缺少参数：customer_id";
	echo json_encode($resultArr);
	return;
}
session_start();
$_SESSION["customer_id"] = $customer_id;
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

require('../common/utility_shop.php');
	
//$callback = $configutil->splash_new($_POST["callback"]);
$batchcode =$configutil->splash_new($_POST["batchcode"]);

if(empty($batchcode)){
	//echo "{\"result\":\"-3\" , \"msg\":\"缺少参数：batchcode!\" }";
	$resultArr['result'] = -3;
	$resultArr['msg'] = "缺少参数：batchcode!";
	echo json_encode($resultArr);
	return;
}
$express_id =$configutil->splash_new($_POST["express_id"]);
/*if(empty($express_id)){
	//echo "{\"result\":\"-3\" , \"msg\":\"缺少参数：express_id!\" }";
	$resultArr['result'] = -3;
	$resultArr['msg'] = "缺少参数：express_id!";
	echo json_encode($resultArr);
	return;
}*/
$expressnum =$configutil->splash_new($_POST["expressnum"]);
/*if(empty($expressnum)){
	//echo "{\"result\":\"-3\" , \"msg\":\"缺少参数：expressnum!\" }";
	$resultArr['result'] = -3;
	$resultArr['msg'] = "缺少参数：expressnum!";
	echo json_encode($resultArr);
	return;
}*/
$remarks =$configutil->splash_new($_POST["remarks"]);

$query="select user_id,agent_id,totalprice,agentcont_type,sendstatus from weixin_commonshop_orders where isvalid=true and batchcode='".$batchcode."'";
$result = mysql_query($query) or die('L49 : Query failed: ' . mysql_error());
$user_id=-1;
$agent_id=-1;
$totalprice=0;
$agentcont_type=0;
$sendstatus=0;
while ($row = mysql_fetch_object($result)) {
    $user_id = $row->user_id;
    $agent_id = $row->agent_id; 	//代理商user_id
    $totalprice = $row->totalprice; 	//订单总金额
    $agentcont_type = $row->agentcont_type; 	
    $sendstatus = $row->sendstatus;
	
	if($agent_id>0 and $agentcont_type==1 and $sendstatus==0){
		//购买支付后,扣除代理库存余额 和 代理得到的金额 start
		$query2="select agent_inventory from promoters where status=1 and isvalid=true and user_id=".$agent_id;	//查找代理商代理剩余库存金额
		$agent_inventory = 0;
		$result2 = mysql_query($query2) or die('L66 : Query failed: ' . mysql_error());
		while ($row2 = mysql_fetch_object($result2)) {
		 $agent_inventory = $row2->agent_inventory;
		}
		$query2="select agent_discount from weixin_commonshop_applyagents where status=1 and isvalid=true and user_id=".$agent_id;	//查找代理商代理剩余库存金额
		$result2 = mysql_query($query2) or die('L71 : Query failed: ' . mysql_error());
		$agent_discount =0;
		while ($row2 = mysql_fetch_object($result2)) {
		 $agent_discount = $row2->agent_discount;
		}
		$agent_discount =  $agent_discount/100;
		$agent_cost_inventorymoney = $totalprice*$agent_discount;	//从代理金额扣除成本价
		$agent_cost_inventorymoney = round($agent_cost_inventorymoney,2);
		$agent_inventory = $agent_inventory - $agent_cost_inventorymoney;	
		$agent_cost_inventorymoney = 0-$agent_cost_inventorymoney;	
		// $f = fopen('out1111.txt', 'w'); 

		$sql1 = "insert into weixin_commonshop_agentfee_records(user_id,batchcode,price,detail,type,isvalid,createtime,after_inventory
	) values(".$agent_id.",'".$batchcode."',".$agent_cost_inventorymoney.",'发货(出库)',1,true,now(),".$agent_inventory.")";
		// fwrite($f, "=sql1======".$sql1."\r\n"); 	
		mysql_query($sql1);		//插入扣除成本价
		$sql = "update promoters set agent_inventory=".$agent_inventory." where user_id=".$agent_id;
		mysql_query($sql);
		// fwrite($f, "=sql======".$sql."\r\n");
		// fclose($f);
		//购买支付后,扣除代理库存余额 和 代理得到的金额 end
	}
}

$query="select weixin_fromuser from weixin_users where isvalid=true and id=".$user_id;
$result = mysql_query($query) or die('L96 : Query failed: ' . mysql_error());
$order_fromuser="";
while ($row = mysql_fetch_object($result)) {
    $order_fromuser = $row->weixin_fromuser;
	break;
}


$query3 = 'SELECT id,name,price FROM weixin_expresses where id='.$express_id;
$result3 = mysql_query($query3) or die('L105 : Query failed: ' . mysql_error()); 
$expressname="";
while ($row3 = mysql_fetch_object($result3)) {
  
  $expressname = $row3->name;
}

$sql="update weixin_commonshop_orders set sendstatus = 1,confirm_sendtime=now(),express_id=".$express_id.",expressname='".$expressname."',expressnum='".$expressnum."',send_remarks='".$remarks."' where batchcode='".$batchcode."'"; 

mysql_query($sql);
if($express_id==0){
	$sql2="update weixin_commonshop_orders set sendstatus = 2 where batchcode='".$batchcode."'";
	mysql_query($sql2);
}

if($express_id!=0){
	
	$content = "商家已经确认您的订单号：".$batchcode.",于".date( "Y-m-d H:i:s")."向您发货，快递单号为:<a href='http://m.kuaidi100.com/index_all.html?type=".$expressname."&postid=".$expressnum."'>".$expressnum."(点击查看物流)</a>，快递公司:".$expressname.",请注意查收！";
	if(!empty($remarks)){
		$content=$content."备注:".$remarks;
	}
	$shopmessage= new shopMessage_Utlity();
	$shopmessage->SendMessage($content,$order_fromuser,$customer_id);
}



fclose($f); 

$error =mysql_error();
mysql_close($link);
//echo "{\"result\":\"1\" , \"msg\":\"发货成功!\" }";
$resultArr['result'] = 1;
$resultArr['msg'] = "发货成功!";
echo json_encode($resultArr);
?>