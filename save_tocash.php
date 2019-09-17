<?php
header("Content-type: text/html; charset=utf-8");     
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../common/utility_fun.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD); 
mysql_select_db(DB_NAME) or die('Could not select database');
//头文件----start
require('../common/common_from.php');
//头文件----end
// $customer_id = $_SESSION['customer_id'];
// $user_id = $_SESSION['user_id_'.$customer_id];

$json = array();//报错信息
/*判断customer_id是否正常开始*/
if(!isset($customer_id)){
	$json["msg"]	= 10001;
	$json["remark"]	= "登录超时，请重新登录！$customer_id";
	$jsons			= json_encode($json);
	die($jsons);	
}
/*判断customer_id是否正常结束*/

/*判断user_id是否正常开始*/
if( 1 > $user_id ){
	$json["msg"] = 10002;
	$json["remark"] 	= "未知错误！没有获取到个人信息！";
	$jsons=json_encode($json);
	die($jsons);
}
/*判断user_id是否正常结束*/

$to_cash = 0;	//用户申请提现的金额
$type 	 = 0;	//提现方式
$password='';	//支付密码

$to_cash 		= abs($configutil->splash_new($_POST["to_cash"]));	//提现金额
$type 			= abs($configutil->splash_new($_POST["type"]));		//提现账号类型
$password 		= MD5($configutil->splash_new($_POST["pw"]));		//密码
$save_type 		= isset($_POST["save_type"])?$configutil->splash_new($_POST["save_type"]):'';		//处理类型



//判断数字
if(!preg_match("/^([1-9][\d]{0,7}|0)(\.[\d]{1,2})?$/",$to_cash) && !preg_match("/^([1-9][\d]{0,7}|0)(\.[\d]{1,2})?$/",$type)){
	$json["msg"] = 10020;
	$json["remark"]	= "请输入正确的数字";
	$jsons=json_encode($json);
	die($jsons);
}

//先判断用户是否有支付密码
$pay_password = "";
$query = "SELECT paypassword FROM user_paypassword WHERE isvalid=true AND customer_id=".$customer_id." AND user_id=".$user_id." LIMIT 1";
$result= mysql_query($query) or die('Query failed 43: ' . mysql_error());
while( $row = mysql_fetch_object($result) ){
	$pay_password = $row->paypassword;
}
//用户尚未设置支付密码
if($pay_password==''){
	$json["msg"] = 10000;
	$json["remark"]	= "尚未设置支付密码";
	$jsons=json_encode($json);
	die($jsons);
}

if(!empty($save_type) && $save_type == 'check'){
	$json["msg"] = 30066;	//表示首轮验证通过
	$jsons=json_encode($json);
	die($jsons);
}


if($password == ''){		//----密码空
	$json["msg"] 	= 400;
	$json["remark"]	= "密码不能输入为空";
	echo json_encode($json);
	die;
}
if( $to_cash <= 0){			//金额空或0或负数
	$json["msg"] 	= 401;
	$json["remark"]	= "提现金额不能为0或负数";
	echo json_encode($json);
	die;
}

//验证密码---start
if($password !== $pay_password){
	$json["msg"] 	= 402;
	$json["remark"]	= "密码错误";
	echo json_encode($json);
	die;
}


//提现规则
$start_time = -1;			//提现开始时间
$end_time   = -1;			//提现结束时间
$week_time  = -1;			//星期几可以提现
$full_vpscore= 0;			//最低需要多少vp值
$sql = "SELECT start_time,end_time,week_time,full_vpscore FROM moneybag_rule WHERE isvalid=true AND customer_id=".$customer_id." LIMIT 1";
$res = mysql_query($sql) or die('Query failed 92: ' . mysql_error());
while( $row = mysql_fetch_object($res) ){
    $start_time     = $row->start_time;
    $end_time       = $row->end_time;
    $week_time      = $row->week_time;
    $full_vpscore   = $row->full_vpscore;
}
//个人VP总值查询
$my_vpscore = 0;
$sql2 = "SELECT my_vpscore FROM weixin_user_vp WHERE customer_id=".$customer_id." AND user_id=".$user_id." LIMIT 1";
$res2 = mysql_query($sql2) or die('Query failed 34: ' . mysql_error());
while( $row = mysql_fetch_object($res2) ){
    $my_vpscore = $row->my_vpscore;
}
//个人VP总值查询

$data = getdate(time());
$today_m = $data['mday'];//今天是当月中的多少天
$today_w = $data['wday'];//今天是当周中的多少天

$is_allow = 0;
if($today_m >= $start_time && $today_m <= $end_time && $my_vpscore>=$full_vpscore){
    if($week_time==-1){
        $is_allow = 1;
    }elseif( $week_time == $today_w ){
        $is_allow = 1;
    }else{
        $is_allow = 0;
    } 
}

if($is_allow = 0){
	$json["msg"] 	= 399;
	$json["remark"]	= "您尚未满足提现条件";
	echo json_encode($json);
	die;
}
//查询是否还有未完成的
$id=-1;
$query = "SELECT id FROM weixin_cash_being_log WHERE isvalid=true AND status=0 AND user_id=$user_id LIMIT 1";
$result= mysql_query($query) or die('Query failed 130: ' . mysql_error());
while( $row = mysql_fetch_object($result) ){
	$id = $row->id;
}
if($id>0){
	$json["msg"] 	= 388;
	$json["remark"]	= "您尚有一笔提现未通过商家审核";
	echo json_encode($json);
	die;
}
//查询是否还有未完成的

//个人钱包余额
$balance = 0;
$query = "SELECT balance FROM moneybag_t WHERE isvalid=true AND customer_id=".$customer_id." AND user_id=".$user_id." LIMIT 1";
$result= mysql_query($query) or die('Query failed 135: ' . mysql_error());
while( $row = mysql_fetch_object($result) ){
    $balance = cut_num($row->balance,2);
}

//查询钱包提现规则------------------------------------------------start
$isOpen_callback    = 0;//是否开启零钱提现
$start_time         = 1;//每月提现开始日期
$end_time           = 30;//每月提现结束日期
$week_time          = 0;//提现可设置按每周几提现 0：周日；1-6；周一-周六
$mini_callback      = 0;//最低提现金额
$max_callback       = 0;//不能提现金额
$callback_currency  = 0;//提现反购物币 0：不返 大于0则反千分之几
$callback_fee       = 0;//提现手续费 0：不收，大于0则收千分之几手续费
$full_vpscore       = 0;//提现vp值限制

$query = "SELECT isOpen_callback,
                 start_time,
                 end_time,
                 week_time,
                 mini_callback,
                 max_callback,
                 callback_currency,
                 callback_fee,
                 full_vpscore 
          FROM moneybag_rule 
          WHERE customer_id=".$customer_id." AND isvalid=true LIMIT 1";

$query = mysql_query($query) or die('Query failed 85: ' . mysql_error());
while( $row = mysql_fetch_object($query) ){
    $isOpen_callback    = $row->isOpen_callback;
    $start_time         = $row->start_time;
    $end_time           = $row->end_time;
    $week_time          = $row->week_time;
    $mini_callback      = $row->mini_callback;
    $max_callback       = $row->max_callback;
    $callback_currency  = $row->callback_currency;
    $callback_fee       = $row->callback_fee;
    $full_vpscore       = $row->full_vpscore;
}
if( $to_cash < $mini_callback){
	$json["msg"] 	= 403;
	$json["remark"]	= "提现不得少于".$mini_callback."元";
	echo json_encode($json);
	die;
} 

$allow_cash = $balance-$max_callback;
if( $allow_cash < $to_cash ){
	$json["msg"] 	= 404;
	$json["remark"]	= "可提现余额小于当前提现金额";
	echo json_encode($json);
	die;
}

//计算到账后金额
$ok_tocash = 0;		//扣取的手续费或返佣的购物币
$real_cash = 0;		//实际会到账的金额
if( $callback_currency > 0 ){

	$ok_tocash = $to_cash*$callback_currency/1000;
	$real_cash = $to_cash - $ok_tocash;

}elseif( $callback_fee > 0 ){

	$ok_tocash = $to_cash*$callback_fee/1000;
	$real_cash = $to_cash - $ok_tocash;

}elseif( $callback_currency == 0 && $callback_fee == 0 ){
	$ok_tocash = $to_cash;
	$real_cash = $ok_tocash;
}

if( $callback_currency > 0 || $callback_fee > 0){
	if( $real_cash < 1 ){
		$json["msg"] 	= 398;
		$json["remark"]	= "提现金额过少！";
		echo json_encode($json);
		die;
	}	
}


//echo $to_cash."==".$type."==".$password;die;
//--------------------保存提现记录开始
$batchcode = $user_id.time();
$remark = "申请提现".$to_cash."元";
//扣除钱包余额
$query = "UPDATE moneybag_t SET balance=balance-$to_cash WHERE isvalid=true AND customer_id=$customer_id AND user_id=".$user_id." LIMIT 1";
mysql_query($query) or die('Query failed 119: ' . mysql_error());

//写钱包进出账日志
$query = "INSERT INTO moneybag_log(isvalid,customer_id,user_id,money,type,batchcode,pay_style,remark,createtime) VALUES(true,$customer_id,$user_id,'$to_cash',1,'$batchcode',5,'$remark',now())";
mysql_query($query) or die('Query failed 240: ' . mysql_error());


if( $callback_currency > 0 ){ //返佣购物币
	$query = "INSERT INTO weixin_cash_being_log(isvalid,customer_id,user_id,remain_money,getmoney,status,batchcode,remark,cash_type,percentage,surplus_type,createtime) VALUES(true,$customer_id,$user_id,$balance,$to_cash,0,'$batchcode','$remark',$type,$callback_currency,2,now())";
	mysql_query($query) or die('Query failed 119: ' . mysql_error());
	$json["msg"] 	= 405;
	$json["remark"]	= "提现申请提交成功，等待商家审核";
	echo json_encode($json);
	die;

}elseif( $callback_fee > 0 ){ //直接收取手续费
	$query = "INSERT INTO weixin_cash_being_log(isvalid,customer_id,user_id,remain_money,getmoney,status,batchcode,remark,cash_type,percentage,surplus_type,createtime) VALUES(true,$customer_id,$user_id,$balance,$to_cash,0,'$batchcode','$remark',$type,$callback_fee,1,now())";
	mysql_query($query) or die('Query failed 122: ' . mysql_error());
	$json["msg"] 	= 406;
	$json["remark"]	= "提现申请提交成功，等待商家审核";
	echo json_encode($json);
	die;
}else{
	$query = "INSERT INTO weixin_cash_being_log(isvalid,customer_id,user_id,remain_money,getmoney,status,batchcode,remark,cash_type,percentage,surplus_type,createtime) VALUES(true,$customer_id,$user_id,$balance,$to_cash,0,'$batchcode','$remark',$type,0,0,now())";
	mysql_query($query) or die('Query failed 125: ' . mysql_error());
	$json["msg"] 	= 407;
	$json["remark"]	= "提现申请提交成功，等待商家审核";
	echo json_encode($json);
	die;
}

//echo $query;die;


?>