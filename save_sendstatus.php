<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require_once('./sf/lib/orderService.php'); // 顺丰接口
date_default_timezone_set('PRC');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../proxy_info.php');
require('../common/utility_shop.php');
	
$callback = $configutil->splash_new($_GET["callback"]);
$batchcode =$configutil->splash_new($_GET["batchcode"]);
$express_id =$configutil->splash_new($_GET["express_id"]);
$expressnum =$configutil->splash_new($_GET["expressnum"]);
$remarks =$configutil->splash_new($_GET["remarks"]);

//更改库存
/*$query="select pid,rcount,prvalues from weixin_commonshop_orders where isvalid=true and batchcode='".$batchcode."'"; 
$result = mysql_query($query) or die('Query failed: ' . mysql_error());

$pid=-1;
$rcount = 0;
$prvalues="";
	$f = fopen('out.txt', 'w'); 
fwrite($f, "=query======".$query."\r\n"); 	
 while ($row = mysql_fetch_object($result)) {
    $pid = $row->pid;
	$rcount = $row->rcount;
	$prvalues= $row->prvalues;
	
	$prvalues= rtrim($prvalues,"_");
	if(!empty($prvalues)){
		$sql="update weixin_commonshop_product_prices set storenum= storenum-".$rcount." where product_id=".$pid." and proids='".$prvalues."'";
		mysql_query($sql);
	}else{
	    $sql="update weixin_commonshop_products set storenum= storenum-".$rcount." where id=".$pid;
		mysql_query($sql);
	}


fwrite($f, "=sql======".$sql."\r\n");  
		

}*/
//订单所需扣代理商库存金额----start
$query_ACM="select AgentCostMoney from weixin_commonshop_order_prices where isvalid=true and batchcode='".$batchcode."'";
$result_ACM = mysql_query($query_ACM) or die('Query failed: ' . mysql_error());
$all_agent_cost_inventorymoney=0;  
while ($row_ACM = mysql_fetch_object($result_ACM)) {
    $all_agent_cost_inventorymoney = $row_ACM->AgentCostMoney;	//新版本 用AgentCostMoney金额,以免再计算一次
}
//订单所需扣代理商库存金额----end



$query="select user_id,agent_id,totalprice,agentcont_type,sendstatus,pid,rcount,address_id,is_QR from weixin_commonshop_orders where isvalid=true and batchcode='".$batchcode."'";
$result = mysql_query($query) or die('Query failed: ' . mysql_error());
$user_id=-1;
$agent_id=-1;
$totalprice=0;
$agentcont_type=0;
$sendstatus=0;
$address_id=0;
$is_QR=0;  //券类订单
$pro_name_one=""; //产品名称
$pro_name=""; //产品名称
while ($row = mysql_fetch_object($result)) {
    $user_id = $row->user_id;
    $agent_id = $row->agent_id; 	//代理商user_id
    $totalprice = $row->totalprice; 	//订单总金额
    $agentcont_type = $row->agentcont_type; 	
    $sendstatus = $row->sendstatus;
    $pid = $row->pid;
	$address_id = $row->address_id;
	$is_QR = $row->is_QR;
	$rcount = $row->rcount;
	$sql="select agent_discount,name,foreign_mark,now_price from weixin_commonshop_products where isvalid=true and id='".$pid."'";
	$result_sql = mysql_query($sql) or die('Query failed: ' . mysql_error());
	$agent_discount=0;
	while ($row_sql = mysql_fetch_object($result_sql)) {
		$agent_discount = $row_sql->agent_discount;
		$product_name = $row_sql->name;	
		$pro_name_one = "<".$product_name.">";
		$product_now_price = $row_sql->now_price;	
		$foreign_mark = $row_sql->foreign_mark;
	} 
	$pro_name = $pro_name.$pro_name_one;
	//旧版本 如果all_agent_cost_inventorymoney金额不存在,需再计算一次
	if($all_agent_cost_inventorymoney==0){
		//购买支付后,计算 扣除代理库存余额 所需总额 start
		if($agent_id>0 and $agentcont_type==1 and $sendstatus==0){
			if($agent_discount==0){
				$query2="select agent_discount from weixin_commonshop_applyagents where status=1 and isvalid=true and user_id=".$agent_id;	//查找代理商代理剩余库存金额
				$result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());
				$agent_discount =0;
				while ($row2 = mysql_fetch_object($result2)) {
				 $agent_discount = $row2->agent_discount;
				}
			}
			$agent_discount =  $agent_discount/100;
			$agent_cost_inventorymoney = $totalprice*$agent_discount;	//从代理金额扣除成本价
			$agent_cost_inventorymoney = round($agent_cost_inventorymoney,2);
			
			$all_agent_cost_inventorymoney = $all_agent_cost_inventorymoney + $agent_cost_inventorymoney; //代理商所扣库存的总额
			$all_agent_cost_inventorymoney = round($all_agent_cost_inventorymoney,2);
			
		}
		//购买支付后,计算 扣除代理库存余额 所需总额 end
	}
	 
}


//购买支付后,扣除代理库存余额 和 代理得到的金额 start 
if($agent_id>0 and $agentcont_type==1 and $sendstatus==0){
	
	$query2="select agent_inventory from promoters where status=1 and isvalid=true and user_id=".$agent_id;	//查找代理商代理剩余库存金额
	$agent_inventory = 0;
	$result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());
	while ($row2 = mysql_fetch_object($result2)) {
	 $agent_inventory = $row2->agent_inventory;
	}
	$agent_inventory = $agent_inventory - $all_agent_cost_inventorymoney;
	$agent_inventory = round($agent_inventory,2);	
	if($agent_inventory<0){
		echo $callback."([{status:2,msg:'代理商库存不足,无法发货'}]);";
		echo $callback;			
	}
	$all_agent_cost_inventorymoney = 0-$all_agent_cost_inventorymoney;	
	$sql1 = "insert into weixin_commonshop_agentfee_records(user_id,batchcode,price,detail,type,isvalid,createtime,after_inventory
	) values(".$agent_id.",'".$batchcode."',".$all_agent_cost_inventorymoney.",'发货(出库)',1,true,now(),".$agent_inventory.")";
	mysql_query($sql1);		//插入扣除成本价
	$sql = "update promoters set agent_inventory=".$agent_inventory." where user_id=".$agent_id;
	mysql_query($sql);
}
//购买支付后,扣除代理库存余额 和 代理得到的金额 end

$query="select weixin_fromuser from weixin_users where isvalid=true and id=".$user_id." limit 0,1";
$result = mysql_query($query) or die('Query failed: ' . mysql_error());
$order_fromuser="";
while ($row = mysql_fetch_object($result)) {
    $order_fromuser = $row->weixin_fromuser;
}


$query3 = 'SELECT id,name,price FROM weixin_expresses where id='.$express_id;
$result3 = mysql_query($query3) or die('Query failed: ' . mysql_error()); 
$expressname="";
$expressprice="";
while ($row3 = mysql_fetch_object($result3)) {
 
  $expressname = $row3->name;
}


$express_id==0?$expressname="虚拟发货":$expressname; 
if($express_id==-2){
	$expressname="顺丰进口业务";
}





if($express_id==-2){
	
	$query3="select name,phone,address,location_p,location_c,location_a,identity from weixin_commonshop_order_addresses where  batchcode=".$batchcode;
					$result3 = mysql_query($query3) or die('Query failed47: ' . mysql_error());
					$order_username = "";
                    $order_userphone ="";
                    $order_address="";					
					while ($row3 = mysql_fetch_object($result3)) {
					    $order_username = $row3->name;
						$order_userphone = $row3->phone;
						$identity = $row3->identity;
						$order_address = $row3->address;
						$location_p=$row3->location_p;
						$location_c=$row3->location_c;
						$location_a=$row3->location_a;
						
					}
					if(empty($order_username)){
					   $query3="select name,phone,address,location_p,location_c,location_a from weixin_commonshop_addresses where  id=".$address_id;
						$result3 = mysql_query($query3) or die('Query failed48: ' . mysql_error());
						$order_username = "";
						$order_userphone ="";
						$order_address="";					
						while ($row3 = mysql_fetch_object($result3)) {
							$order_username = $row3->name;
							$order_userphone = $row3->phone;
							$order_address = $row3->address;
							$location_p=$row3->location_p;
							$location_c=$row3->location_c;
							$location_a=$row3->location_a;
						}
					}
	
	
	
	
	
	
	
	
	
	
	
	$sql="select * from sf_import where customer_id=$customer_id and ison=1";
	$re_sf=mysql_query($sql) or die("查询顺丰进口业数据表务表失败!");
	$l_sf=mysql_num_rows($re_sf);
	if(!$l_sf){
		die("没有配置顺丰进口参数!");
	}else{
		$row_sf=mysql_fetch_object($re_sf);
		$head=$row_sf->head;
		$token=$row_sf->token;
		$authToken=$row_sf->authToken;
		$businessLogo=$row_sf->businessLogo;
		$Sendcompany=$row_sf->Sendcompany;
		$Sendconcact=$row_sf->Sendconcact;
		$Sendtelphone=$row_sf->Sendtelphone;
		$Sendmobile=$row_sf->Sendmobile;
		$Sendcountry=$row_sf->Sendcountry;
		$Sendprovinoce=$row_sf->Sendprovinoce;
		$Sendcitycode=$row_sf->Sendcitycode;
		$Sendcity=$row_sf->Sendcity;
		$Sendzipcode=$row_sf->Sendzipcode;
		$Sendaddress=$row_sf->Sendaddress;
		$Sendcounty=$row_sf->Sendcounty;
		
		$monthlyAccount=$row_sf->monthlyAccount;
		
		$customsBatchNumber=$row_sf->customsBatchNumber;
		$taxSetAccounts=$row_sf->taxSetAccounts;
		$checkWord=$row_sf->checkWord;
		
			$xmlArray = array(
			'@attributes' => array(
				'service' => 'otherOrderService',
				'lang' => 'zh_cn',
				'printType'=>'2',
			),
			'Head' => "$head",
			   'Body' => array(
					"Order" => array(
						'@attributes' => array(
								'orderSourceSystem'=>'3',
								 'businessLogo'=>'Wsy',
								 'customerOrderNo'=>$batchcode,
								 'Sendcompany'=>$Sendcompany,
								 'Sendconcact'=>$Sendconcact,
								 'Sendtelphone'=>$Sendtelphone,
								 'Sendmobile'=>$Sendmobile,
								 'Sendcountry'=>$Sendcountry,
								 'Sendprovinoce'=>$Sendprovinoce,
								 'Sendcitycode'=>$Sendcitycode,
								 'Sendcity'=>$Sendcity,
								 'Sendcounty'=>$Sendcounty,
								 'Sendzipcode'=>$Sendzipcode,
								 'Sendaddress'=>$Sendaddress,
								 'monthlyAccount'=>$monthlyAccount,
								 'customsBatchNumber'=>$customsBatchNumber,
								 'taxSetAccounts'=>$taxSetAccounts,
								 'expressType'=>'全球顺',
								 'taxPayType'=>'寄付',
								 'payType'=>'寄方付',
								 'recCompany'=>'*',
								 'recConcact'=>$order_username,
								 'recTelphone'=>$order_userphone,
								 'recMobile'=>$order_userphone,
								 'recCountry'=>'中国',
								 'recProvinoce'=>$location_p,
								 'recCityCode'=>'cn',
								 'recCity'=>$location_c,
								 'recCounty'=>$location_a,
								 'recZipcode'=>'100000',
								 'recAddress'=>$order_address,
								 'turnover'=>$totalprice,
								 'freight'=>0,
								 'freightCurrency'=>'CNY',
								 'buyersNickname'=>$order_username,
								 'ordersName'=>$order_username,
								 'ordersDocumentType'=>'身份证',
								 'orderDocumentNumber'=>strtoupper($identity)
						),
						'Goods' =>array(
							array(
								'@attributes' => array(
										'code'=>$product_name,
										'name'=>$product_name,
										'unit'=>'个', 
										'model'=>$product_name,
										'brand'=>$product_name, 
										'unitPrice'=>$product_now_price, 
										'count'=>$rcount,
										'currencyType'=>'CNY', 
										'sourceArea'=>'芬兰'
										
							))
						)
					))
			);

			$xml = array2xml($xmlArray,"Request"); // 生成XML
		//echo $xml;
		//	exit;
			$arr = array(

					"verifyCode"=>$checkWord, // verifyCode
					
					'Servicefun' => 'issuedOrder', // webserver 方法
					
					'server' => 'http://cbti.sfb2c.com:8003/CBTA/ws/orderService?wsdl', // webserver 服务
					
					'authtoken' =>$token, // authtoken
					
					'headerNamespace' => 'http://cbti.sfb2c.com:8003/CBTA/' // SoapHeader命名空间
			);
			$Api = new orderService;

			$re_xml=$Api->getOrderData($xml,$arr);
			
			$xml_obj = simplexml_load_string($re_xml,'SimpleXMLElement', LIBXML_NOCDATA);
			$errorCode = $xml_obj->errorCode;
			$errorDesc = $xml_obj->errorDesc;
			$mailNo = $xml_obj->mailNo;
			$printUrl = $xml_obj->printUrl;
			if($errorCode=="001"){
				$expressnum=$mailNo;	
				
			}else{
				if($errorCode=="009"){
					
					echo $callback."([{status:2,msg:'此订单已经发货，不能重复提交'}";     
					echo "]);";
					echo $callback;
					
				}else{
					echo $callback."([{status:2,msg:'顺接口返回错误,错误码:$errorCode,错误信息:$errorDesc'}";     
					echo "]);";
					echo $callback;
					
				}

					mysql_close($link);
					exit;				
			}

	
		
	}
	
}

$query_cus = " select auto_cus_time from weixin_commonshops where isvalid = true and customer_id = ".$customer_id;
$result_cus = mysql_query($query_cus);
$auto_cus_time = mysql_result($result_cus,0,0);
if(empty($auto_cus_time) || $auto_cus_time <= 0){ //如没有设置时间默认为7天
	$auto_cus_time = 7;
}

if($express_id==-2 && $errorCode==001){ //修改默认的自动收货时间
	//如果是顺丰进口业务
	$sql="update weixin_commonshop_orders set sendway=1,sendstatus = 1,confirm_sendtime=now(),auto_receivetime = DATE_ADD( now(), INTERVAL ".$auto_cus_time." DAY ),send_express_id=".$express_id.",expressnum='".$expressnum."',send_remarks='".$remarks."',printUrl='".$printUrl."' where batchcode='".$batchcode."'"; 

}else{
	$sql="update weixin_commonshop_orders set sendway=1,sendstatus = 1,confirm_sendtime=now(),auto_receivetime = DATE_ADD( now(), INTERVAL ".$auto_cus_time." DAY ),send_express_id=".$express_id.",expressnum='".$expressnum."',send_remarks='".$remarks."' where batchcode='".$batchcode."'"; 
	
}

mysql_query($sql);
 

if($express_id==0){
	$sql2="update weixin_commonshop_orders set sendway=1,sendstatus = 2 where batchcode='".$batchcode."'";
	mysql_query($sql2);
}

if($express_id!=0){	
	//$content = "商家已经确认您的订单号：".$batchcode.",于".date( "Y-m-d H:i:s")."向您发货，快递单号为:<a href='http://m.kuaidi100.com/result.jsp?nu=".$expressnum."'>".$expressnum."(点击查看物流)</a>，快递公司:".$expressname.",请注意查收！";
	
	$content = "亲，您有一笔订单【已发货】\n\n商品：".$pro_name."\n时间：".date( "Y-m-d H:i:s")."\n快递：".$expressname."";
	if(!empty($remarks)){
		$content=$content."\n备注:".$remarks;
	}
	$content=$content."\n\n<a href='http://m.kuaidi100.com/result.jsp?nu=".$expressnum."'>【物流进度】</a>\n\n<a href='http://".$http_host."/weixinpl/common_shop/jiushop/order_detail.php?batchcode=".$batchcode."&customer_id=".passport_encrypt((string)$customer_id)."&fromuser=".$order_fromuser."'>【订单详情】</a>";
	$shopmessage= new shopMessage_Utlity();
	$shopmessage->SendMessage($content,$order_fromuser,$customer_id);
	
} 

//添加发货日志
$username = $_SESSION['username'];
$query_logs = "insert into weixin_commonshop_order_logs(batchcode,operation,descript,operation_user,createtime,isvalid) 
values('".$batchcode."',4,'平台发货[物流：".$expressname.",单号：".$expressnum."]','".$username."',now(),1)";
mysql_query($query_logs) or die("L365 query error  : ".mysql_error());


//券类订单发放二维码
if($is_QR==1){
	$shopUtility_get =  new shopMessage_Utlity();
	$shopUtility_get->GetQR($batchcode,$order_fromuser,$customer_id);
	//非虚拟发货
	if($express_id != 0){
		$sql2="update weixin_commonshop_orders set sendway=1,sendstatus = 2 where batchcode='".$batchcode."'";   
		mysql_query($sql2);
	}	
	
}
    
//fclose($f); 
//status状态信息   1发货成功  2发货失败，顺丰进口返回客户单号已存在
$error =mysql_error();
mysql_close($link);

echo $callback."([{status:1,confirmtime:'".date( "Y-m-d H:i:s")."'}";
echo "]);";
echo $callback;

?>