<?php
header("Content-type: text/html; charset=utf-8");     
require('../../../config.php');
require('../../../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../back_init.php'); 
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD); 
mysql_select_db(DB_NAME) or die('Could not select database');
require('../../../common/utility_shop.php');
require('../../../proxy_info.php');
require('../../../auth_user.php');
mysql_query("SET NAMES UTF8");
$shopmessage= new shopMessage_Utlity(); 

$user_id	 = 0;
$balance 	 = 0;
$remark 	 = '';
$real_name 	 = '';
$customer_id = passport_decrypt($customer_id);
$user_id 	 = $configutil->splash_new($_GET["user_id"]);
$balance 	 = $configutil->splash_new($_POST["balance"]);
$real_name 	 = $configutil->splash_new($_POST["real_name"]);
$remark 	 = $configutil->splash_new($_POST["remark"]);
$batchcode   = $user_id.time();
$id=0;
$sql = "SELECT count(id) as id,balance FROM moneybag_t where user_id=".$user_id." AND customer_id=".$customer_id." limit 1";
$res = mysql_query($sql)or die( 'Query failed in 22: ' . mysql_error() );
while($row=mysql_fetch_object($res)){
	$id = $row->id;
	$before_balance = $row->balance;

	$after_balance = $before_balance+$balance;
	if($after_balance<0){
		echo '<script>history.go(-2);</script>';
		return false;
	}
	if($remark == ''){
		if( $balance > 0 ){
			
			$remark = "后台充值 ".$balance." 零钱 | 零钱订单号：".$batchcode;
			$msg_content =  "亲，你的零钱增加了".$balance."元\r\n".
							"来源【后台充值】\r\n".	
							"时间：<".date( "Y-m-d H:i:s").">";
		}elseif( $balance < 0 ){		
			$remark = "商家后台扣取您 ".abs($balance)." 零钱";
			$msg_content =  "亲，你的零钱扣取了".abs($balance)."元\r\n".
							"来源【后台扣取】\r\n".	
							"时间：<".date( "Y-m-d H:i:s").">";
		}
		
	}

}
if( $balance > 0 ){
	$type = 0;
}elseif( $balance < 0 ){
	$type = 1;
}

	if($id == 0){

		$query = "INSERT INTO moneybag_t(isvalid,customer_id,user_id,balance,createtime,real_name) VALUES(true,".$customer_id.",".$user_id.",".$balance.",now(),'".$real_name."')";
		mysql_query($query)or die( 'Query failed in 28: ' . mysql_error() );
		
		$sql = "INSERT INTO moneybag_log(isvalid,customer_id,user_id,money,batchcode,remark,createtime,type,pay_style)
		VALUES(true,".$customer_id.",".$user_id.",".abs($balance).",".$batchcode.",'".$remark."',now(),$type,4)";

		mysql_query($sql)or die( 'Query failed in 34: ' . mysql_error() );
	}else{

		$query = "UPDATE moneybag_t SET balance=balance+".$balance.",createtime=now() WHERE user_id=".$user_id." AND customer_id=".$customer_id." ";
		mysql_query($query)or die( 'Query failed in 40: ' . mysql_error() );

		$sql = "INSERT INTO moneybag_log(isvalid,customer_id,user_id,money,batchcode,remark,createtime,type,pay_style)
		VALUES(true,".$customer_id.",".$user_id.",".abs($balance).",".$batchcode.",'".$remark."',now(),$type,4)";
		mysql_query($sql)or die( 'Query failed in 44: ' . mysql_error() );
		
	}
$weixin_fromuser = '';
$query = "SELECT weixin_fromuser FROM weixin_users WHERE isvalid=true AND id = $user_id LIMIT 1";
$result= mysql_query($query)or die( 'Query failed in 74: ' . mysql_error() );
while( $row = mysql_fetch_object($result) ){
	$weixin_fromuser = $row->weixin_fromuser;
}

$shopmessage->SendMessage($msg_content,$weixin_fromuser,$customer_id);

echo '<script>history.go(-2);</script>';


//echo $day; 

?>