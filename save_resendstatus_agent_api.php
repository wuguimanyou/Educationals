<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
date_default_timezone_set(PRC);
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
mysql_query("SET NAMES UTF8");

require('../common/utility_shop.php');
include_once("../log_.php");
$log_ = new Log_();
$log_name="./notify_url_order.log";//log文件路径	
	
$batchcode =$configutil->splash_new($_POST["batchcode_id"]);    //订单编号

//获取session中customer_id
$customer_id=$_SESSION['customer_id'];  //获取商家id

if(!is_numeric($batchcode) or $batchcode<0){
	$msg['status'] = 101;
	$msg['info'] = "请填写正确参数";
	echo json_encode($msg);
	return;
}

	$query="select pid,sendstatus,paystyle,totalprice,prvalues,rcount from weixin_commonshop_orders where isvalid=true and batchcode='".$batchcode."' limit 0,1"; 
	$result = mysql_query($query) or die('Query failed1: ' . mysql_error());
	$pid=-1;
	$card_member_id=-1;
	$rcount = 0;
	$prvalues="";
	$totalprice=0;
	 while ($row = mysql_fetch_object($result)) {
		$pid = $row->pid;         //商品id
		$sendstatus= $row->sendstatus;  //发货状态 0:未发货;1:已发货;2:已收货;3.申请退货;4.已退货
		$paystyle= $row->paystyle;         //支付类型
		$totalprice= $row->totalprice;     //总价
		$prvalues= $row->prvalues;        
		$rcount = $row->rcount;        //数量
	 }

	if($sendstatus !=4){
	//退货完成
	$sql = "update weixin_commonshop_orders set sendstatus=4 where batchcode='".$batchcode."'";
	mysql_query($sql);

	$log_->log_result($log_name,"退货开始 sql======".$sql);

	$shopUtility_back =  new shopMessage_Utlity();
	
	$log_->log_result($log_name,"退货进入card_member_id-------".$card_member_id);
	$log_->log_result($log_name,"退货进入totalprice-------".$totalprice);
	$log_->log_result($log_name,"退货进入customer_id-------".$customer_id);
	$log_->log_result($log_name,"退货进入paystyle-------".$paystyle);
	$log_->log_result($log_name,"退货进入2-------".$batchcode);
	
	$shopUtility_back->Back_GetMoney($batchcode,$card_member_id,$totalprice,$customer_id,$paystyle);
	}	 

	$prvalues= rtrim($prvalues,"_");
	if(!empty($prvalues)){
		$sql="update weixin_commonshop_product_prices set storenum= storenum+".$rcount." where product_id=".$pid." and proids='".$prvalues."'";
		mysql_query($sql);
	}else{
		$sql="update weixin_commonshop_products set storenum= storenum+".$rcount." where id=".$pid;
		mysql_query($sql);
	}	
	
$error =mysql_error();
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