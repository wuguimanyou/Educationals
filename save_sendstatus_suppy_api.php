<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
date_default_timezone_set(PRC);
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

require('../common/utility_shop.php');  
	
$batchcode =$configutil->splash_new($_POST["batchcode_id"]);   //订单号
$express_id =$configutil->splash_new($_POST["express_id"]);  //运送方式id
$expressnum =$configutil->splash_new($_POST["express_num"]);   //快递单号
$remarks =$configutil->splash_new($_POST["remarks"]);    //备注

//获取session中customer_id
$customer_id=$_SESSION['supplier_Customer_id'];  //获取商家id

//file_put_contents("log_0626.txt", "batchcode=".$batchcode.PHP_EOL, FILE_APPEND);
//file_put_contents("log_0626.txt", "express_id=".$express_id.PHP_EOL, FILE_APPEND);
//file_put_contents("log_0626.txt", "expressnum=".$expressnum.PHP_EOL, FILE_APPEND);
//file_put_contents("log_0626.txt", "remarks=".$remarks.PHP_EOL, FILE_APPEND);
//file_put_contents("log_0626.txt", "customer_id=".$customer_id.PHP_EOL, FILE_APPEND);

if(!is_numeric($batchcode) or $batchcode<0 or !is_numeric($express_id) or $express_id<0){
	$msg['status'] = 101;
	$msg['info'] = "请填写正确参数";
	echo json_encode($msg);
	return;
} 
//非虚拟发货，验证快递单号
if($express_id!=0){
	if (!preg_match ("/^[A-Za-z0-9]+$/", $expressnum)){   //正则条件  字母加数字
		$msg['status'] = 102;
		$msg['info'] = "请填写订单号";
		echo json_encode($msg);
		return;
	} 
}

$shopmessage= new shopMessage_Utlity();

$query="select user_id,agent_id,totalprice,agentcont_type,sendstatus,pid,is_QR from weixin_commonshop_orders where isvalid=true and batchcode='".$batchcode."'";
$result = mysql_query($query) or die('Query failed26: ' . mysql_error());
$user_id=-1;
$agent_id=-1;
$totalprice=0;
$agentcont_type=0;
$sendstatus=0;
$pid=-1;
$is_QR=0;
while ($row = mysql_fetch_object($result)) {
    $user_id = $row->user_id;
    $agent_id = $row->agent_id; 	//代理商user_id
    $totalprice = $row->totalprice; 	//订单总金额
    $agentcont_type = $row->agentcont_type; 	
    $sendstatus = $row->sendstatus;
	$pid = $row->pid;
	$is_QR = $row->is_QR;
	
	$sql="select agent_discount from weixin_commonshop_products where isvalid=true and id='".$pid."'";
	$result_sql = mysql_query($sql) or die('Query failed: ' . mysql_error());
	$agent_discount=0;
	while ($row_sql = mysql_fetch_object($result_sql)) {
		$agent_discount = $row_sql->agent_discount;
	}
	if($agent_id>0 and $agentcont_type==1 and $sendstatus==0){
		//购买支付后,扣除代理库存余额 和 代理得到的金额 start
		$query2="select agent_inventory from promoters where status=1 and isvalid=true and user_id=".$agent_id;	//查找代理商代理剩余库存金额
		$agent_inventory = 0;
		$result2 = mysql_query($query2) or die('Query failed43: ' . mysql_error());
		while ($row2 = mysql_fetch_object($result2)) {
		 $agent_inventory = $row2->agent_inventory;
		}
		
		if($agent_discount==0){
			$query2="select agent_discount from weixin_commonshop_applyagents where status=1 and isvalid=true and user_id=".$agent_id;	//查找代理商代理剩余库存金额
			$result2 = mysql_query($query2) or die('Query failed48: ' . mysql_error());
			$agent_discount =0;
			while ($row2 = mysql_fetch_object($result2)) {
			 $agent_discount = $row2->agent_discount;
			}
		}
		$agent_discount =  $agent_discount/100;
		$agent_cost_inventorymoney = $totalprice*$agent_discount;	//从代理金额扣除成本价
		$agent_cost_inventorymoney = round($agent_cost_inventorymoney,2);
		$agent_inventory = $agent_inventory - $agent_cost_inventorymoney;	
		$agent_cost_inventorymoney = 0-$agent_cost_inventorymoney;	

		$sql1 = "insert into weixin_commonshop_agentfee_records(user_id,batchcode,price,detail,type,isvalid,createtime,after_inventory) values(".$agent_id.",'".$batchcode."',".$agent_cost_inventorymoney.",'发货(出库)',1,true,now(),".$agent_inventory.")";

		mysql_query($sql1);		//插入扣除成本价
		$sql = "update promoters set agent_inventory=".$agent_inventory." where user_id=".$agent_id;
		mysql_query($sql) or die('Query failed63: ' . mysql_error());
		//购买支付后,扣除代理库存余额 和 代理得到的金额 end
	}
}


$query="select weixin_fromuser from weixin_users where isvalid=true and id=".$user_id;
$result = mysql_query($query) or die('Query failed70: ' . mysql_error());
$order_fromuser="";
while ($row = mysql_fetch_object($result)) {
	$order_fromuser = $row->weixin_fromuser;
	break;
}


$query3 = 'SELECT id,expresses_name FROM weixin_expresses_company where id='.$express_id;
$result3 = mysql_query($query3) or die('Query failed79: ' . mysql_error()); 
$expressname="";
while ($row3 = mysql_fetch_object($result3)) { 
  $expressname = $row3->expresses_name;
}

$express_id==0?$expressname="虚拟发货":$expressname; 

$query_cus = " select auto_cus_time from weixin_commonshops where isvalid = true and customer_id = ".$customer_id;
$result_cus = mysql_query($query_cus);
$auto_cus_time = mysql_result($result_cus,0,0);
if(empty($auto_cus_time) || $auto_cus_time <= 0){ //如没有设置时间默认为7天
	$auto_cus_time = 7;
}
$sql="update weixin_commonshop_orders set sendstatus = 1,confirm_sendtime=now(),auto_receivetime = DATE_ADD( now(), INTERVAL ".$auto_cus_time." DAY ),send_express_id=".$express_id.",expressname='".$expressname."',expressnum='".$expressnum."',send_remarks='".$remarks."' where batchcode='".$batchcode."'";
mysql_query($sql) or die('Query failed103: ' . mysql_error());

//虚拟发货直接改为已收货   
if($express_id==0){
	$sql2="update weixin_commonshop_orders set sendstatus = 2,confirm_receivetime=now(),send_express_id=".$express_id.",expressnum='".$expressnum."',expressname='".$expressname.",'send_remarks='".$remarks."' where batchcode='".$batchcode."'";
	mysql_query($sql2) or die('Query failed107: ' . mysql_error());
}

//券类订单发放二维码
if($is_QR==1){
	$shopmessage->GetQR($batchcode,$order_fromuser,$customer_id);
}

$error =mysql_error();
if($error!=""){
	$msg['status'] = 102;
	$msg['info'] = $error;
	echo json_encode($msg);	
	return;
}


if($express_id!=0){
	$content = "商家已经确认您的订单号：".$batchcode.",于".date( "Y-m-d H:i:s")."向您发货，快递单号为:<a href='http://m.kuaidi100.com/result.jsp?nu=".$expressnum."'>".$expressnum."(点击查看物流)</a>，快递公司:".$expressname.",请注意查收！";
	if(!empty($remarks)){
		$content=$content."备注:".$remarks;
	}
	
	$shopmessage->SendMessage($content,$order_fromuser,$customer_id);
	
	//添加发货日志
	$username = $_SESSION['supplier_Acount'];
	$roletype = $_SESSION['user_roletype']; //1 ：代理商 ； 3 ：供应商
	if($roletype == 1){
		$roletypeStr = "代理商";
	}else if($roletype == 3){
		$roletypeStr = "供应商";
	}
	
}
$query_logs = "insert into weixin_commonshop_order_logs(batchcode,operation,descript,operation_user,createtime,isvalid) 
	values('".$batchcode."',4,'".$roletypeStr."发货[物流：".$expressname.",单号：".$expressnum."]','".$username."',now(),1)";
	mysql_query($query_logs) or die("L365 query error  : ".mysql_error());


//$error =mysql_error();
mysql_close($link);


if($error!=""){
	$msg['status'] = 102;
	$msg['info'] = $error;
}else{
	$msg['status'] = 0;
	$msg['info'] = "成功";		
}

echo json_encode($msg); 

?>