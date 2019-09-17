<?php
require('../logs.php');   
header("Content-type: text/html; charset=utf-8"); 
require('../../../config.php');
require('../../../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../back_init.php');
require('../../../common/utility.php');
require('../../../common/utility_4m.php');
$name =$configutil->splash_new($_POST["name"]);
$keyid =$configutil->splash_new($_POST["keyid"]);

$adminuser_id = $configutil->splash_new($_GET["adminuser_id"]);
$orgin_adminuser_id = $configutil->splash_new($_GET["orgin_adminuser_id"]);
$owner_general 		= $configutil->splash_new($_GET["owner_general"]);

$stock_pidarr="";
if(!empty($_POST["stock_pidarr"])){		
	$stock_pidarr = $configutil->splash_new($_POST["stock_pidarr"]);
}
//echo $stock_pidarr;
$orgin_price = $configutil->splash_new($_POST["orgin_price"]);
$now_price   = $configutil->splash_new($_POST["now_price"]);
$cost_price	 = $configutil->splash_new($_POST["cost_price"]);
$for_price	 = $configutil->splash_new($_POST["for_price"]);
$orgin_price = round($orgin_price, 2);
$cost_price  = round($cost_price, 2);
$now_price 	 = round($now_price, 2);
$for_price 	 = round($for_price, 2);
$unit		 = $configutil->splash_new($_POST["unit"]);
$weight = 0;
if(!empty($_POST["weight"])){
	$weight  = $configutil->splash_new($_POST["weight"]);
}

$foreign_mark 	 		 = $configutil->splash_new($_POST["foreign_mark"]);
$storenum	  	 		 = 0;
if(!empty($_POST["storenum"])){
	$storenum	  	 		 = $configutil->splash_new($_POST["storenum"]);
}
$need_score 			 = 0;
if(!empty($_POST["need_score"])){
	$need_score	  	 		 = $configutil->splash_new($_POST["need_score"]);
}
$show_sell_count 		 = $configutil->splash_new($_POST["show_sell_count"]);
$asort_value	 		 = $configutil->splash_new($_POST["asort_value"]);
$nowprice_title  		 = $configutil->splash_new($_POST["define_price_tag"]);
$type_id		 		 = $configutil->splash_new($_POST["type_id"]);
$pro_price_detail 		 = $configutil->splash_new($_POST["pro_price_detail"]);
$is_QR 			  		 = $configutil->splash_new($_POST["is_QR"]);  //二维码产品
$city_id 		 		 = $configutil->splash_new($_POST["city_id"]);  //产品区域编号
$define_share_image_flag = $configutil->splash_new($_POST["define_share_image_flag"]);
$is_invoice 			 = $configutil->splash_new($_POST["is_invoice"]);//发票开关
$is_currency 			 = $configutil->splash_new($_POST["is_currency"]);//是否购物币
$is_guess_you_like 		 = $configutil->splash_new($_POST["is_guess_you_like"]);//是否猜您喜欢产品
$back_currency 			 = $configutil->splash_new($_POST["back_currency"]);
$is_free_shipping 		 = $configutil->splash_new($_POST["is_free_shipping"]);//是否包邮
//$first_division 		 = $configutil->splash_new($_POST["first_division"]);//一级分佣金额
$isscore 		 		 = $configutil->splash_new($_POST["isscore"]);//是否积分专区

$express_type = 0;
if(!empty($_POST["express_type"])){
	$express_type = $configutil->splash_new($_POST["express_type"]);//邮费计费方式
}
//echo $is_invoice;die;

$is_identity = 0;
if(!empty($_POST["is_identity"])){
	$is_identity = $configutil->splash_new($_POST["is_identity"]);
}
$donation_rate = 0;
if(!empty($_POST["donation_rate"])){
	$donation_rate = $configutil->splash_new($_POST["donation_rate"]);  //慈善比例
}
//$is_identity=$configutil->splash_new($_POST["is_identity"]);//产品是否需要身份证购买开关

$define_share_image='';
$install_price = 0;

if(!empty($_POST["install_price"])){
	$install_price = $configutil->splash_new($_POST["install_price"]);
}
$cashback = '';
if($_POST["cashback"]!=''){
	$cashback = $configutil->splash_new($_POST["cashback"]);
}
$cashback_r = '';
if($_POST["cashback_r"]!=''){
	$cashback_r = $configutil->splash_new($_POST["cashback_r"]);
}
$agent_discount = 0;
if(!empty($_POST["agent_discount"])){
	$agent_discount = $configutil->splash_new($_POST["agent_discount"]);//代理商折扣
}
$pro_card_level_id = -1;
if(!empty($_POST["pro_card_level_id"])){
	$pro_card_level_id = $configutil->splash_new($_POST["pro_card_level_id"]);//购买产品需要的会员卡等级ID
}
if($define_share_image_flag==1){
//file_put_contents('hello.txt','**********'.$_FILES['new_define_share_image']['tmp_name']);
	if(!empty($_FILES['new_define_share_image']['name'])){
	//file_put_contents('hello2.txt','-------');
		$rand1=rand(0,9);
		$rand2=rand(0,9);
		$rand3=rand(0,9);
		$filename=date("Ymdhis").$rand1.$rand2.$rand3;
		$filetype=substr($_FILES['new_define_share_image']['name'], strrpos($_FILES['new_define_share_image']['name'], "."),strlen($_FILES['new_define_share_image']['name'])-strrpos($_FILES['new_define_share_image']['name'], "."));
		$filetype=strtolower($filetype);
		if(($filetype!='.jpg')&&($filetype!='.png')&&($filetype!='.gif')){
				echo "<script>alert('文件类型或地址错误');</script>";
				echo "<script>history.back(-1);</script>";
				exit ;
			}
		$filename=$filename.$filetype;
		$savedir = "../../../".Base_Upload."Product/product/";  
		if(!file_exists($savedir)){
			mkdir($savedir,0777,true);
		}
		//$savedir='../up/common_shop_define/';
		//file_put_contents('hello3.txt',$davedir.'++++'.$filename);
		/*if(!is_dir($savedir)){
			mkdir($savedir,0777);
		}*/
		$savefile=$savedir.$filename;
		if (!move_uploaded_file($_FILES['new_define_share_image']['tmp_name'], $savefile)){
			//echo "<script>文件上传成功！</script>";
			echo "<script>history.back(-1);</script>";
			exit;
		}
		$define_share_image=$savefile;
	}else{
		$define_share_image=$_POST['now_define_share_image'];
	}
	if(strpos($define_share_image,"/weixinpl/") === false){ //不包含才加上/weixinpl/
		$define_share_image = str_replace("../","",$define_share_image);
		$define_share_image = "/weixinpl/".$define_share_image;
	}
	
}
//$f = fopen('out.txt', 'w');  
//fwrite($f, "==pro_price_detail=====".$pro_price_detail."\r\n");  
		
//fclose($f);
$specifications = "";
$customer_service = "";
$imgids=$configutil->splash_new($_POST["imgids"]);
$tradeprices=$configutil->splash_new($_POST["tradeprices"]);
$introduce = $configutil->splash_new($_POST["introduce"]);
$description = $configutil->splash_new($_POST["description"]);
$customer_service = $configutil->splash_new($_POST["service"]);
$specifications = $configutil->splash_new($_POST["specifications"]);
$propertyids = $configutil->splash_new($_POST["propertyids"]);

$default_imgurl = $configutil->splash_new($_POST["default_imgurl"]);
$class_imgurl = $configutil->splash_new($_POST["class_imgurl"]);

$isout = $configutil->splash_new($_POST["isout"]);

if($isout==1){
	$isout_status = 0;//0:供应商可以修改产品上下架,1:不可以修改
}else{
	$isout_status = 1; 
}
$isnew     = $configutil->splash_new($_POST["isnew"]);
$ishot     = $configutil->splash_new($_POST["ishot"]);
$issnapup  = $configutil->splash_new($_POST["issnapup"]);
$isvp      = $configutil->splash_new($_POST["isvp"]);      //是否属于vp产品，1：是；0：否
$vp_score  = 0;
$vp_score  = $configutil->splash_new($_POST["vp_score"]);  //vp值,vp产品消费累积满多少vp值可以提现佣金

$buystart_time=0;
$countdown_time=0;
if($issnapup == 1){
	if(!empty($_POST["buystart_time"])){
		$buystart_time = $configutil->splash_new($_POST["buystart_time"]);  //商品抢购开始时间
	}
	if(!empty($_POST["countdown_time"])){
		$countdown_time = $configutil->splash_new($_POST["countdown_time"]);  //商品抢购结束时间
	}
}

$is_Pinformation_b = $configutil->splash_new($_POST["is_Pinformation_b"]);//必填信息大开关1：开 0：关
$is_Pinformation = 0;
if($is_Pinformation_b){
	$is_Pinformation   = $configutil->splash_new($_POST["is_Pinformation"]);  
	//必填信息产品开关1：开 0：关
}

$freight_id = -1;
if(!empty($_POST["freight_id"])){
	$freight_id = $configutil->splash_new($_POST["freight_id"]);  //运费模板ID
}

$is_virtual	= 0;
if(!empty($_POST["is_virtual"])){
	$is_virtual = $configutil->splash_new($_POST["is_virtual"]);  //是否为虚拟产品 0:非虚拟产品,1:虚拟产品
}

$type_ids=$configutil->splash_new($_POST["type_ids"]);
$auth_user_id=$configutil->splash_new($_POST["auth_user_id"]);   //授权用户customer_users表的id	
$asort = -1;
if(!empty($_POST["asort"])){		//排序优先级(已隐藏) 赋值 默认值-1
	$asort = $configutil->splash_new($_POST["asort"]);
}
//echo $imgids;
if(!empty($_POST["pro_discount"])){
   $pro_discount=$configutil->splash_new($_POST["pro_discount"]);
}else{
  $pro_discount=0;
}

if(!empty($_POST["pro_reward"])){
   $pro_reward=$configutil->splash_new($_POST["pro_reward"]);
}else{
  $pro_reward=0;
}
$head = 10;
if(!empty($_GET["head"])){
   $head = $configutil->splash_new($_GET["head"]);
}
$pagenum = 1;
if(!empty($_GET["pagenum"])){
   $pagenum = $configutil->splash_new($_GET["pagenum"]);
}

$product_voice = "";
if(!empty($_POST["product_voice"])){
   $product_voice = $configutil->splash_new($_POST["product_voice"]);//产品宣传语音
}

$product_vedio = "";
if(!empty($_POST["product_vedio"])){
   $product_vedio = $configutil->splash_new($_POST["product_vedio"]);//产品宣传视频
}


$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
 mysql_select_db(DB_NAME) or die('Could not select database');
 
 //add by wenjun
 $customer_ids=$customer_id;
 $u4m = new Utiliy_4m();
 $customer_ids = $customer_ids.",".$u4m->getAllSubCustomers($adminuser_id,$orgin_adminuser_id,$owner_general);
file_put_contents("123ffdd.txt", "5.GetMoney_Common=======". $type_ids."\r\n",FILE_APPEND);

/*   echo 'owner_general='.($owner_general).'<br>';   
echo 'customer_ids='.($customer_ids).'<br>';  
echo 'type_ids='.$type_ids.'<br>';  */
 //产品更新
 if($keyid>0){
   $create_parent_id=-1;
   if(($owner_general and !empty($customer_ids)) or (!$owner_general)){
	  $carr = explode(",",$customer_ids);
	  foreach($carr as $keys=>$values){ 
		    $c_id = $values;
		
			$pid = -1;
			if(empty($c_id)){
				continue;
			}
			if($c_id==$customer_id){
			   //厂家的产品更新,自己添加的产品也为-1
			   $create_type=-1;
			   if(!$owner_general){
				   //商家自己创建的是3；
				   $create_type=3;
			   }
			  // echo '------------------------'.'<br>';
			  if($foreign_mark != ""){
				  $fm_num = 0;
				  $query_fm_num ="select count(1) as num from weixin_commonshop_products where isvalid =true and customer_id=$c_id and foreign_mark='".$foreign_mark."' and id not in ($keyid)";
				  //echo $query_fm_num;
				  $result_fm_num = mysql_query($query_fm_num) or die('L231: Query_fm_num failed: ' . mysql_error());   
				  while ($row = mysql_fetch_object($result_fm_num)) {
					$fm_num= $row->num;
				  }				  
				  if(0 < $fm_num){
					echo "<script>alert('已存在的外部标识,请重新编辑');window.history.go(-1)</script>";
					return;	
				  }				  
			  }

			  
			  $sql="update weixin_commonshop_products set 
			  donation_rate='".$donation_rate."',
			  asort_value=".$asort_value.",
			  default_imgurl='".$default_imgurl."',
			  class_imgurl='".$class_imgurl."',
			  unit='".$unit."', 
			  type_id='".$type_id."',
			  type_ids='".$type_ids."',
			  pro_discount=".$pro_discount.",
			  pro_reward=".$pro_reward.", 
			  name='".$name."',
			  orgin_price=".$orgin_price.",
			  now_price=".$now_price.",
			  storenum=".$storenum.",
			  need_score=".$need_score.",
			  cost_price=".$cost_price.",
			  for_price=".$for_price.",
			  foreign_mark='".$foreign_mark."',
			  introduce='".$introduce."',
			  description='".$description."', 
			  specifications='".$specifications."', 
			  customer_service='".$customer_service."',
			  asort=".$asort.",
			  isout=".$isout.",
			  isnew=".$isnew.",
			  ishot=".$ishot.",
			  issnapup=".$issnapup.",
			  isvp=".$isvp.",
			  vp_score='".$vp_score."',
			  tradeprices='".$tradeprices."',
			  propertyids='".$propertyids."',
			  show_sell_count=".$show_sell_count.",
			  define_share_image='".$define_share_image."',
			  isout_status=".$isout_status.",
			  create_type=".$create_type.",
			  install_price = '".$install_price."',
			  weight = '".$weight."',
			  is_QR = ".$is_QR.",
			  pro_area = ".$city_id.",
			  agent_discount = ".$agent_discount.",
			  pro_card_level_id=".$pro_card_level_id.",
			  cashback='".$cashback."',
			  cashback_r='".$cashback_r."',
			  buystart_time='".$buystart_time."',
			  countdown_time='".$countdown_time."',
			  is_identity=".$is_identity.",
			  is_Pinformation=".$is_Pinformation.",
			  is_virtual=".$is_virtual.",
			  freight_id=".$freight_id.",
			  is_invoice=".$is_invoice.",
			  is_currency=".$is_currency.", 
			  is_guess_you_like=".$is_guess_you_like.", 
			  is_free_shipping=".$is_free_shipping.", 
			  isscore=".$isscore.",
			  product_voice='".$product_voice."', 
			  product_vedio='".$product_vedio."',
			  back_currency='".$back_currency."',
			  express_type=".$express_type."
			   where id=".$keyid;
				//echo "sql  : ".$sql." <br/>";
			//	echo $sql;return;
			  mysql_query($sql)or die('L177: Query failed1: ' . mysql_error());
			  $Pkid = $keyid;
			  $pid = $keyid;
			  // echo $sql;return;
			  // echo '---------------------'.'<br>'; */
			}else{
				//4M模式
				//查询自己所有下级产品
				//$query="select id from weixin_commonshop_products where isvalid=true and create_type=".$owner_general." and customer_id=".$c_id." and create_parent_id=".$keyid;
				//解决渠道修改总店身份后，以前的产品不同步问题--不加create_type查询
				$query="select id from weixin_commonshop_products where isvalid=true  and customer_id=".$c_id." and create_parent_id=".$keyid;
				//echo $query.'<br>';
				$result = mysql_query($query) or die('L183: Query failed: ' . mysql_error());   
		        while ($row = mysql_fetch_object($result)) {
					$pid= $row->id;
					break;
				}
				
				//$type_id已经不用
				/* $query="select id from weixin_commonshop_types where customer_id=".$c_id." and isvalid=true and  create_parent_id=".$type_id;
				echo $query.'<br>';
				$result = mysql_query($query) or die('L191 : Query failed: ' . mysql_error());
				$sub_type_id=-1;
				while ($row = mysql_fetch_object($result)) {
					$sub_type_id= $row->id;
					break;
				} */
				
				$type_id_arr = explode(",",$type_ids);				
				$sub_type_ids = "";
				for($i=0;$i<count($type_id_arr);$i++){
					if($type_id_arr[$i] == ''){
						continue;
					}
					$type_id_f = $type_id_arr[$i];
					$query="select id from weixin_commonshop_types where customer_id=".$c_id." and isvalid=true and  create_parent_id=".$type_id_f;
					//echo $query.'<br>';
					$result = mysql_query($query) or die('L203 :Query failed: ' . mysql_error());
					//$type_id_s=-1;
					$type_id_s='';
					while ($row = mysql_fetch_object($result)) {
						$type_id_s= $row->id;
						break;
					}
				
				/* 	if($type_id_s==-1){
						//查找厂家的属性；
						$query="select create_parent_id from weixin_commonshop_types where customer_id=".$customer_id." and isvalid=true and create_type=1 and  id=".$type_id_f;
						echo $query.'<br>';
						$result = mysql_query($query) or die('L213 :Query failed: ' . mysql_error());
						$t_create_parent_id=-1;
						while ($row = mysql_fetch_object($result)) {
							$t_create_parent_id= $row->create_parent_id;
							break;
						}
						
						$query="select id from weixin_commonshop_types where customer_id=".$c_id." and isvalid=true and  create_parent_id=".$t_create_parent_id;
						echo $query.'<br>';
						$result = mysql_query($query) or die('L221 :Query failed: ' . mysql_error());
						while ($row = mysql_fetch_object($result)) {
							$type_id_s= $row->id;
							break;
						}
					} */
					//echo 'type_id_s='.$type_id_s.'<br>';
					if(empty($sub_type_ids)){
						$sub_type_ids=$type_id_s;
					}else{
						$sub_type_ids=$sub_type_ids.",".$type_id_s;
					}
				}
			 /* echo "===========sub_type_ids=".$sub_type_ids."<br/>";  */
				$pro_id_arr = explode("_",$propertyids);
				$pro_type_ids = "";
				for($i=0;$i<count($pro_id_arr);$i++){
					$pro_id = $pro_id_arr[$i];
					if(empty($pro_id)){
						continue;
					}
					$query="select id from weixin_commonshop_pros where customer_id=".$c_id." and isvalid=true and  create_parent_id=".$pro_id;
					$result = mysql_query($query) or die('L243 :Query failed: ' . mysql_error());
					//$type_id_s=-1;
					$type_id_s='';
					while ($row = mysql_fetch_object($result)) {
						$type_id_s= $row->id;
						break;
					}
					
				/* 	if($type_id_s==-1){
						//查找厂家的属性；
						$query="select create_parent_id from weixin_commonshop_pros where customer_id=".$customer_id." and isvalid=true and create_type=1 and  id=".$pro_id;
						$result = mysql_query($query) or die('L253 :Query failed: ' . mysql_error());
						$t_create_parent_id=-1;
						while ($row = mysql_fetch_object($result)) {
							$t_create_parent_id= $row->create_parent_id;
							break;
						}
						
						$query="select id from weixin_commonshop_pros where customer_id=".$c_id." and isvalid=true and  create_parent_id=".$t_create_parent_id;
						$result = mysql_query($query) or die('L261 :Query failed: ' . mysql_error());
						while ($row = mysql_fetch_object($result)) {
							$type_id_s= $row->id;
							break;
						}
						if($sub_type_id<0){
							$sub_type_id= $type_id_s;
						}
					} */
					if(empty($pro_type_ids)){
						$pro_type_ids=$type_id_s;
					}else{
						$pro_type_ids=$pro_type_ids."_".$type_id_s;
					}
				}
				
				$sql="update weixin_commonshop_products set 
				donation_rate='".$donation_rate."',
				asort_value=".$asort_value.",
				default_imgurl='".$default_imgurl."',
				class_imgurl='".$class_imgurl."',
				unit='".$unit."', 
				type_ids='".$sub_type_ids."',
				pro_discount=".$pro_discount.",
				pro_reward=".$pro_reward.", 
				name='".$name."',
				orgin_price=".$orgin_price.",
				now_price=".$now_price.",
				storenum=".$storenum.",
				need_score=".$need_score.",
				cost_price=".$cost_price.",
				for_price=".$for_price.",
				foreign_mark='".$foreign_mark."',
				introduce='".$introduce."',
				description='".$description."',
				specifications='".$specifications."',
				customer_service='".$customer_service."',
				asort=".$asort.",
				isout=".$isout.",
				isnew=".$isnew.",
				isvp=".$isvp.",
				vp_score='".$vp_score."',
				ishot=".$ishot.",
				issnapup=".$issnapup.",
				tradeprices='".$tradeprices."',
				propertyids='".$pro_type_ids."',
				show_sell_count=".$show_sell_count.",
				define_share_image='".$define_share_image."',
				isout_status=".$isout_status.",
				install_price = '".$install_price."',
				weight = ".$weight.",
				is_QR = ".$is_QR.",
				pro_area = ".$city_id.",
				agent_discount = ".$agent_discount.",
				pro_card_level_id=".$pro_card_level_id.",
				cashback='".$cashback."',
				cashback_r='".$cashback_r."',
				buystart_time='".$buystart_time."',
				countdown_time='".$countdown_time."',
				is_identity=".$is_identity.",
				is_Pinformation=".$is_Pinformation.",
			    is_virtual=".$is_virtual.",
			    freight_id=".$freight_id.",
				is_invoice=".$is_invoice.",
				is_currency=".$is_currency.",
				is_guess_you_like=".$is_guess_you_like.",
				is_free_shipping=".$is_free_shipping.",
				isscore=".$isscore.",
				product_voice='".$product_voice."',
				product_vedio='".$product_vedio."',
				back_currency=".$back_currency.",
				express_type=".$express_type."
			     where id=".$pid;
				
				//echo $sql.'<br>';
				
				/*echo "============1end================<br/>";  */
			}
			if($pid==-1){
				//没有找到4M 下级商家，往下继续寻找
				continue;
			}
			/* echo $sql;
			return; */
			
			mysql_query($sql)or die('Query failed1: ' . mysql_error());
			$Pkid = $pid;
			//清除图片
			
			//插入修改运费模板日志 start
			$query="select id,express_id from weixin_commonshop_product_express_logs where isvalid=true and customer_id=".$customer_id." and pid=".$pid." order by id desc limit 0,1";
			$e_log_id 		  = -1;
			$e_log_express_id = -1;
		    $result = mysql_query($query) or die('Query failed_express_logs2: ' . mysql_error());
		    while ($row = mysql_fetch_object($result)) {
			   $e_log_id 		  =	$row->id;
			   $e_log_express_id  = $row->express_id;
		    }
			// 如果有修改,则插入修改记录
			if($e_log_express_id != $freight_id){
				$sql_elog="insert into weixin_commonshop_product_express_logs(pid,express_id,isvalid,createtime,customer_id,operation,operation_user) values(".$pid.",".$freight_id.",true,now(),".$customer_id.",1,'".$_SESSION['username']."')";
				mysql_query($sql_elog)or die('W945 : Query failed1-2: ' . mysql_error());
			}
			//插入修改运费模板日志 end
			
			if($imgids!=""){
				
				//插入图片
				$imgidarr = explode(";",$imgids);
				
				//$sql_update = "update weixin_commonshop_product_imgs set isvalid = false where product_id = ".$pid." and isvalid = true ";
				//mysql_query($sql_update)or die('L294 : Query failed1: ' . mysql_error());
				for($i=0;$i<count($imgidarr);$i++){
				   $imgid = $imgidarr[$i];
				   
				    //上传时不再添加到数据库，现改为在保存时统一提交
				   $sql="insert into weixin_commonshop_product_imgs(product_id,imgurl,isvalid,customer_id) values(".$pid.",'".$imgid."',true,".$c_id.")";
				   mysql_query($sql)or die('L297 : Query failed1: ' . mysql_error());
				   
				   /*
				   $sql = "update weixin_commonshop_product_imgs set product_id=".$pid.",isvalid=true where id=".$imgid;
				   mysql_query($sql)or die('Query failed1: ' . mysql_error());*/
				}
			}
			//echo $pro_price_detail."<br/>";
			$pro_price_details = explode("-",$pro_price_detail);
			$plen = count($pro_price_details);
			if( $plen > 0 ){
				$query= "select proids from weixin_commonshop_product_prices where product_id=".$pid;
				$result = mysql_query($query) or die('L308 :Query failed: ' . mysql_error());
				$rpocount = 0;
				$ppLst = new ArrayList();
				while ($row = mysql_fetch_object($result)) {
					$proids = $row->proids;
					$ppLst->Add($proids);
				}
				$query=" delete from weixin_commonshop_product_prices where product_id=".$pid;
				mysql_query($query)or die('L318 :Query failed1: ' . mysql_error());
			}
			
			$fm_num2 = 0;
			for($i=0;$i<$plen;$i++){
				$pro_price_item = $pro_price_details[$i];
				if(empty($pro_price_item)){
				   continue;
				}
				$pro_price_items = explode(",",$pro_price_item);
				$pilen = count($pro_price_items);
				$proids =$pro_price_items[0];
				//echo "c_id== ".$c_id."<br/>";
				//echo "customer_id== ".$customer_id."<br/>";
				if($c_id!=$customer_id){
					//查找下级的真实的属性编号
					$pro_id_arr = explode("_",$proids);
					$proids = "";
					for($j=0;$j<count($pro_id_arr);$j++){
						$pro_id = $pro_id_arr[$j];
						if(empty($pro_id)){
							continue;
						}
						$query="select id from weixin_commonshop_pros where customer_id=".$c_id." and isvalid=true and  create_parent_id=".$pro_id;
						$result = mysql_query($query) or die('L340 : Query failed: ' . mysql_error());
						$pro_id_s=-1;
						while ($row = mysql_fetch_object($result)) {
							$pro_id_s= $row->id;
							break;
						}
						if(empty($proids)){
							$proids=$pro_id_s;
						}else{
							$proids=$proids."_".$pro_id_s;
						}
					}
				}
				 $is_added = false;
				if($ppLst->Contains($proids)){
					$is_added =true;
				}
				
				$proprices 	   	= $pro_price_items[1];
							
				$p_orgin_price  = $orgin_price;
				$p_unit         = $unit;
				$p_weight       = $weight;
				$p_now_price    = $now_price;
				$p_storenum     = $storenum;
				$p_cost_price   = $cost_price;
				$p_for_price   	= $for_price;
				$p_foreign_mark = $foreign_mark;
				$p_need_score   = $need_score;
				
				$pprices 		= explode("_",$proprices);
				$pplen 			= count($pprices);
				
				$p_o_price 		  = $pprices[0];
				$p_n_price 		  = $pprices[1];
				$p_n_storenum 	  = $pprices[2];
				$p_n_need_score   = $pprices[3];
				$p_n_cost_price   = $pprices[4];
				$p_n_foreign_mark = $pprices[5];
				$p_n_unit 		  = $pprices[6];
				$p_n_weight 	  = $pprices[7];
				$p_n_for_price 	  = $pprices[8];
				
				$p_o_price 		  = round($p_o_price, 2);
				$p_n_price 		  = round($p_n_price, 2);
				$p_n_cost_price   = round($p_n_cost_price, 2);
				$p_n_for_price    = round($p_n_for_price, 2);
				
				if($p_n_unit!=""){
				  $p_unit = $p_n_unit;
				}
				if($p_n_weight!="" or ($p_n_weight=="0" and $is_added)){
				  $p_weight = $p_n_weight;
				}
				
				if($p_o_price!="" or ($p_o_price=="0" and $is_added)){
				  $p_orgin_price = $p_o_price;
				}
				if($p_n_price!="" or ($p_n_price=="0" and $is_added )){
				  $p_now_price = $p_n_price;
				}				
				
				if($p_n_storenum!="" or ($p_n_storenum=="0" and $is_added)){
				  $p_storenum = $p_n_storenum;
				}
				
				if($p_n_need_score!="" or ($p_n_need_score=="0" and $is_added )){
				  $p_need_score = $p_n_need_score;
				}
				if($p_n_cost_price!="" or ($p_n_cost_price=="0" and $is_added )){
				  $p_cost_price = $p_n_cost_price;
				}
				if($p_n_for_price!="" or ($p_n_for_price=="0" and $is_added )){
					$p_for_price = $p_n_for_price;
				}
				 if($p_n_foreign_mark!=""){
				  $p_foreign_mark = $p_n_foreign_mark;
				}

				if($p_foreign_mark != ""){
					$query_fm_num2 ="select count(1) as num2 from weixin_commonshop_product_prices as pri 
									 LEFT JOIN weixin_commonshop_products as pro on pri.product_id=pro.id
									 where pri.foreign_mark='" . $p_foreign_mark . "' and pro.customer_id = ".$c_id;
					//echo $query_fm_num2;
					$result_fm_num2 = mysql_query($query_fm_num2) or die('L616: Query_fm_num2 failed: ' . mysql_error());   
						while ($row = mysql_fetch_object($result_fm_num2)) {
						$fm_num_2= $row->num2;
						$fm_num2 += $fm_num_2;
					}				  				  
				}				
				
				$query="insert into weixin_commonshop_product_prices(
				product_id,
				proids,
				orgin_price,
				now_price,
				storenum,
				need_score,
				cost_price,
				unit,
				foreign_mark,
				weight,for_price
				) value(
				".$pid.",
				'".$proids."',
				".$p_orgin_price.",
				".$p_now_price.",
				".$p_storenum.",
				".$p_need_score.",
				".$p_cost_price.",
				'".$p_unit."',
				'".$p_foreign_mark."',
				".$p_weight.",
				".$p_for_price.")";
				mysql_query($query)or die('L420 : Query failed128: ' . mysql_error());
			}
			if(0 < $fm_num2){
				echo "<script>alert('属性价格存在已使用的外部标识，请检查确保正确！');</script>";
			}						
	  }	
	}

 }else{
	 $create_parent_id=-1;
	 if(($owner_general and !empty($customer_ids)) or (!$owner_general)){
		$carr = explode(",",$customer_ids);
		
		foreach($carr as $keys=>$values){ 
		    $c_id = $values;
			if(empty($c_id)){
				continue;
			}
			$p_id=-1;
		    if($c_id==$customer_id){
				//厂家上传的
				$create_type=-1;
			    if(!$owner_general){
				   //商家自己创建的是3；
				   $create_type=3;
			    }
				
				if($foreign_mark != ""){
					$fm_num = 0;
					$query_fm_num ="select count(1) as num from weixin_commonshop_products where isvalid =true and customer_id=$c_id and foreign_mark='".$foreign_mark."'";
					// echo $query_fm_num;
					$result_fm_num = mysql_query($query_fm_num) or die('L231: Query_fm_num failed: ' . mysql_error());   
						while ($row = mysql_fetch_object($result_fm_num)) {
						$fm_num= $row->num;
					}				  
					if(0 < $fm_num){
						echo "<script>alert('已存在的外部标识,请重新编辑');window.history.go(-1)</script>";
						return;	
					}				  
				}				
				
				$sql="insert into weixin_commonshop_products(
				donation_rate,
				name,
				unit,
				type_id,
				orgin_price,
				now_price,
				storenum,
				need_score,
				introduce,
				description,
				specifications,
				customer_service,
				asort,
				isout,
				isnew,
				ishot,
				issnapup,
				isvp,
				vp_score,
				customer_id,
				isvalid,
				createtime,
				tradeprices,
				propertyids,
				pro_discount,
				pro_reward,
				type_ids,
				default_imgurl,
				class_imgurl,
				cost_price,
				for_price,
				foreign_mark,
				asort_value,
				show_sell_count,
				define_share_image,
				isout_status,
				create_type,
				create_parent_id,
				install_price,
				weight,
				agent_discount,
				auth_users,
				nowprice_title,
				is_QR,
				pro_card_level_id,
				cashback,
				cashback_r,
				pro_area,
				buystart_time,
				countdown_time,
				is_identity,
				is_Pinformation,
				is_virtual,
				freight_id,
				is_invoice,
				is_currency,
				is_guess_you_like,
				is_free_shipping,
				isscore,
				product_voice,
				product_vedio,
				back_currency,
				express_type
				)";
				$sql=$sql." values(
				'".$donation_rate."',
				'".$name."',
				'".$unit."',
				".$type_id.",
				".$orgin_price.",
				".$now_price.",
				".$storenum.",
				".$need_score.",
				'".$introduce."',
				'".$description."',
				'".$specifications."',
				'".$customer_service."',
				".$asort.",
				".$isout.",
				".$isnew.",
				".$ishot.",
				".$issnapup.",
				".$isvp.",
				'".$vp_score."',
				".$c_id.",
				true,
				now(),
				'".$tradeprices."',
				'".$propertyids."',
				".$pro_discount.",
				".$pro_reward.",
				'".$type_ids."',
				'".$default_imgurl."',
				'".$class_imgurl."',
				".$cost_price.",
				".$for_price.",
				'".$foreign_mark."',
				".$asort_value.",
				".$show_sell_count.",
				'".$define_share_image."',
				".$isout_status.",
				".$create_type.",
				-1,
				'".$install_price."',
				".$weight.",
				".$agent_discount.",
				".$auth_user_id.",
				'".$nowprice_title."',
				".$is_QR.",
				".$pro_card_level_id.",
				'".$cashback."',
				'".$cashback_r."',
				".$city_id.",
				'".$buystart_time."',
				'".$countdown_time."',
				".$is_identity.",
				".$is_Pinformation.",
				".$is_virtual.",
				".$freight_id.",
				".$is_invoice.",
				".$is_currency.",
				".$is_guess_you_like.",
				".$is_free_shipping.",
				".$isscore.",
				'".$product_voice."',
				'".$product_vedio."',
				".$back_currency.",
				".$express_type."
				)";
				//echo $sql."1";die;
				mysql_query($sql)or die('L445 : Query failed321: ' . mysql_error());
				$error =mysql_error();
				
				$p_id = mysql_insert_id();
				$Pkid = $p_id;
				$create_parent_id = $p_id;
				//echo $sql.'<br>';
			}else{	
				
				//找到下级对应的类型编号
				/* $query="select id from weixin_commonshop_types where customer_id=".$c_id." and isvalid=true and  create_parent_id=".$type_id;
				
				$result = mysql_query($query) or die('L454 : Query failed: ' . mysql_error());
				$sub_type_id=-1;
				while ($row = mysql_fetch_object($result)) {
					$sub_type_id= $row->id;
					break;
				} */
				
				$type_id_arr = explode(",",$type_ids);	
					
				$sub_type_ids = "";
				for($i=0;$i<count($type_id_arr);$i++){
					
					if(empty($type_id_arr[$i] )){
						continue;
					}
					$type_id = $type_id_arr[$i];
					$query="select id from weixin_commonshop_types where customer_id=".$c_id." and isvalid=true and  create_parent_id=".$type_id;
					
					$result = mysql_query($query) or die('L466 : Query failed8: ' . mysql_error());
					//$type_id_s=-1;
					$type_id_s='';
					while ($row = mysql_fetch_object($result)) {
						$type_id_s= $row->id;
						break;
					}
					/* if($type_id_s==-1){
						//查找厂家的属性；
						$query="select create_parent_id from weixin_commonshop_types where customer_id=".$customer_id." and isvalid=true and create_type=1 and  id=".$type_id;
						
						$result = mysql_query($query) or die('Query failed7: ' . mysql_error());
						$t_create_parent_id=-1;
						while ($row = mysql_fetch_object($result)) {
							$t_create_parent_id= $row->create_parent_id;
							break;
						}
						
						$query="select id from weixin_commonshop_types where customer_id=".$c_id." and isvalid=true and  create_parent_id=".$t_create_parent_id;
					
						$result = mysql_query($query) or die('L483 : Query failed6: ' . mysql_error());
						while ($row = mysql_fetch_object($result)) {
							$type_id_s= $row->id;
							break;
						}
						if($sub_type_id<0){
							$sub_type_id= $type_id_s;
						}
					} */
					if(empty($sub_type_ids)){
						$sub_type_ids=$type_id_s;
					}else{
						$sub_type_ids=$sub_type_ids.",".$type_id_s;
					}
					
					
				}
				
				$pro_id_arr = explode("_",$propertyids);
				$pro_type_ids = "";
				for($i=0;$i<count($pro_id_arr);$i++){
					$pro_id = $pro_id_arr[$i];
					if(empty($pro_id)){
						continue;
					}
					$query="select id from weixin_commonshop_pros where customer_id=".$c_id." and isvalid=true and  create_parent_id=".$pro_id;
					$result = mysql_query($query) or die('L507 : Query failed5: ' . mysql_error());
					//$type_id_s=-1;
					$type_id_s='';
					while ($row = mysql_fetch_object($result)) {
						$type_id_s= $row->id;
						break;
					}
				/* 	if($type_id_s==-1){
						//查找厂家的属性；
						$query="select create_parent_id from weixin_commonshop_pros where customer_id=".$customer_id." and isvalid=true and create_type=1 and  id=".$pro_id;
						$result = mysql_query($query) or die('L516 : Query failed4: ' . mysql_error());
						$t_create_parent_id=-1;
						while ($row = mysql_fetch_object($result)) {
							$t_create_parent_id= $row->create_parent_id;
							break;
						}
						
						$query="select id from weixin_commonshop_pros where customer_id=".$c_id." and isvalid=true and  create_parent_id=".$t_create_parent_id;
						$result = mysql_query($query) or die('L524 : Query failed3: ' . mysql_error());
						while ($row = mysql_fetch_object($result)) {
							$type_id_s= $row->id;
							break;
						}
					} */
					if(empty($pro_type_ids)){
						$pro_type_ids=$type_id_s;
					}else{
						$pro_type_ids=$pro_type_ids."_".$type_id_s;
					}
				}
				
				$sql="insert into weixin_commonshop_products(
				donation_rate,
				name,
				unit,
				type_id,
				orgin_price,
				now_price,
				storenum,
				need_score,
				introduce,
				description,
				specifications,
				customer_service,
				asort,
				isout,
				isnew,
				ishot,
				issnapup,
				isvp,
				vp_score,
				customer_id,
				isvalid,
				createtime,
				tradeprices,
				propertyids,
				pro_discount,
				pro_reward,
				type_ids,
				default_imgurl,
				class_imgurl,
				cost_price,
				for_price,
				foreign_mark,
				asort_value,
				show_sell_count,
				define_share_image,
				isout_status,
				create_type,
				create_parent_id,
				install_price,
				weight,
				agent_discount,
				auth_users,
				nowprice_title,
				is_QR,
				pro_card_level_id,
				cashback,
				cashback_r,
				pro_area,
				buystart_time,
				countdown_time,
				is_identity,
				is_Pinformation,
				is_virtual,
				freight_id,
				is_invoice,
				is_currency,
				is_guess_you_like,
				is_free_shipping,
				isscore,
				product_voice,
				product_vedio,
				back_currency,
				express_type
				)";
				$sql=$sql." values(
				'".$donation_rate."',
				'".$name."',
				'".$unit."',
				".$type_id.",
				".$orgin_price.",
				".$now_price.",
				".$storenum.",
				".$need_score.",
				'".$introduce."',
				'".$description."',
				'".$specifications."',
				'".$customer_service."',
				".$asort.",
				".$isout.",
				".$isnew.",
				".$ishot.",
				".$issnapup.",
				".$isvp.",
				'".$vp_score."',".$c_id.",
				true,
				now(),
				'".$tradeprices."',
				'".$pro_type_ids."',
				".$pro_discount.",
				".$pro_reward.",
				'".$sub_type_ids."',
				'".$default_imgurl."',
				'".$class_imgurl."',
				".$cost_price.",
				".$for_price.",
				'".$foreign_mark."',
				".$asort_value.",
				".$show_sell_count.",
				'".$define_share_image."',
				".$isout_status.",
				".$owner_general.",
				".$create_parent_id.",
				'".$install_price."',
				".$weight.",
				".$agent_discount.",
				".$auth_user_id.",
				'".$nowprice_title."',
				".$is_QR.",
				".$pro_card_level_id.",
				'".$cashback."',
				'".$cashback_r."',
				".$city_id.",
				'".$buystart_time."',
				'".$countdown_time."',
				".$is_identity.",
				".$is_Pinformation.",
				".$is_virtual.",
				".$freight_id.",
				".$is_invoice.",
				".$is_currency.",
				".$is_guess_you_like.",
				".$is_free_shipping.",
				".$isscore.",
				'".$product_voice."',
				'".$product_vedio."'
				".$back_currency.",
				".$express_type."
				)";
				//echo $sql."2";die;
				//echo $sql.'<br>';
				mysql_query($sql)or die('L540 : Query failed123: ' . mysql_error());
				$error =mysql_error();
				$p_id = mysql_insert_id();
				$Pkid = $p_id;
				
			}
			
			//插入修改运费模板日志 start
			$query="select id,express_id from weixin_commonshop_product_express_logs where isvalid=true and customer_id=".$customer_id." and pid=".$p_id." order by id desc limit 0,1";
			$e_log_id 		  = -1;
			$e_log_express_id = -1;
		    $result = mysql_query($query) or die('Query failed_express_logs2: ' . mysql_error());
		    while ($row = mysql_fetch_object($result)) {
			   $e_log_id 		  =	$row->id;
			   $e_log_express_id  = $row->express_id;
		    }
			// 如果有修改,则插入修改记录
			if($e_log_express_id != $freight_id){
				$sql_elog="insert into weixin_commonshop_product_express_logs(pid,express_id,isvalid,createtime,customer_id,operation,operation_user) values(".$p_id.",".$freight_id.",true,now(),".$customer_id.",1,'".$_SESSION['username']."')";
				mysql_query($sql_elog)or die('W945 : Query failed1-2: ' . mysql_error());
			}
			//插入修改运费模板日志 end
			
			//插入图片
			$imgidarr = explode(";",$imgids);
			
			//$sql_update = "update weixin_commonshop_product_imgs set isvalid = false where product_id = ".$p_id." and isvalid = true ";
			//mysql_query($sql_update)or die('L294 : Query failed1: ' . mysql_error());
			for($i=0;$i<count($imgidarr);$i++){
			   $imgid = $imgidarr[$i]; //新改过后，imgid为实际图片路径 
			   if(empty($imgid)){ 
				   continue;
			   }
			   //上传时不再添加到数据库，现改为在保存时统一提交
			   $sql="insert into weixin_commonshop_product_imgs(product_id,imgurl,isvalid,customer_id) values(".$p_id.",'".$imgid."',true,".$c_id.")";
				mysql_query($sql)or die('L555 : Query failed1: ' . mysql_error());
			   
			  /* if($c_id==$customer_id){
				   //如果是厂家上传的图片，则更新产品编号
				   $sql = "update weixin_commonshop_product_imgs set product_id=".$p_id." where id=".$imgid;
				   mysql_query($sql);
			   }else{
				   //赋值给商家，要重新再生成一条记录
				   $query="select imgurl from weixin_commonshop_product_imgs where id=".$imgid;
				   $result = mysql_query($query) or die('Query failed2: ' . mysql_error());
				   $imgurl="";
				   while ($row = mysql_fetch_object($result)) {
					   $imgurl = $row->imgurl;
					   break;
				   }
				   $sql="insert into weixin_commonshop_product_imgs(product_id,imgurl,isvalid,customer_id) values(".$p_id.",'".$imgurl."',true,".$c_id.")";
				   mysql_query($sql)or die('Query failed1: ' . mysql_error());
			   }*/
			}
			
			//新增产品的时候，添加属性价格、库存
			$pro_price_details = explode("-",$pro_price_detail);
			
			$plen = count($pro_price_details);
			if( $plen > 0 ){
				$query= "select proids from weixin_commonshop_product_prices where product_id=".$p_id;
				$result = mysql_query($query) or die('L308 :Query failed: ' . mysql_error());
				$rpocount = 0;
				$ppLst = new ArrayList();
				while ($row = mysql_fetch_object($result)) {
					$proids = $row->proids;
					$ppLst->Add($proids);
				}
				$query=" delete from weixin_commonshop_product_prices where product_id=".$p_id;
				mysql_query($query)or die('L318 :Query failed1: ' . mysql_error());
			}
			
			$fm_num2 = 0;
			for($i=0;$i<$plen;$i++){
				$pro_price_item = $pro_price_details[$i];
				if(empty($pro_price_item)){
				   continue;
				}
				$pro_price_items = explode(",",$pro_price_item);
				$pilen = count($pro_price_items);
				$proids =$pro_price_items[0];
				if($c_id!=$customer_id){
					//查找下级的真实的属性编号
					$pro_id_arr = explode("_",$proids);
					$proids = "";
					for($j=0;$j<count($pro_id_arr);$j++){
						$pro_id = $pro_id_arr[$j];
						if(empty($pro_id)){
							continue;
						}
						$query="select id from weixin_commonshop_pros where customer_id=".$c_id." and isvalid=true and  create_parent_id=".$pro_id;
						$result = mysql_query($query) or die('L597 : Query failed1: ' . mysql_error());
						$pro_id_s=-1;
						while ($row = mysql_fetch_object($result)) {
							$pro_id_s= $row->id;
							break;
						}
						if(empty($proids)){
							$proids=$pro_id_s;
						}else{
							$proids=$proids."_".$pro_id_s;
						}
					}
				}
				
				$is_added = false;
				if($ppLst->Contains($proids)){
					$is_added =true;
				}
				
				$proprices      = $pro_price_items[1];
				$p_orgin_price  = $orgin_price;
				$p_unit         = $unit;
				$p_weight       = $weight;
				$p_now_price    = $now_price;
				$p_storenum     = $storenum;
				$p_cost_price   = $cost_price;
				$p_for_price   	= $for_price;
				$p_foreign_mark = $foreign_mark;
				$p_need_score   = $need_score;
				
				$p_orgin_price = round($p_orgin_price, 2);
				$p_now_price = round($p_now_price, 2);
				$p_for_price = round($p_for_price, 2);
				$p_cost_price = round($p_cost_price, 2);
				
				
				
				$pprices = explode("_",$proprices);
				$pplen = count($pprices);
				
				$p_o_price        = $pprices[0];
				$p_n_price        = $pprices[1];
				$p_n_storenum     = $pprices[2];
				$p_n_need_score   = $pprices[3];
				$p_n_cost_price   = $pprices[4];
				$p_n_foreign_mark = $pprices[5];
				$p_n_unit 		  = $pprices[6];
				$p_n_weight 	  = $pprices[7];
				$p_n_for_price 	  = $pprices[8];
				
				$p_o_price 		= round($p_o_price, 2);
				$p_n_price 		= round($p_n_price, 2);
				$p_n_cost_price = round($p_n_cost_price, 2);
				$p_n_for_price = round($p_n_for_price, 2);
				
				if($p_n_unit!=""){  //单位
					$p_unit = $p_n_unit;
				}
				
				if($p_n_weight!=""){  //重量
					$p_weight = $p_n_weight;
				}
				
				if($p_o_price!=""){  //原价
				  $p_orgin_price = $p_o_price;
				}
				
				if($p_n_price!=""){  //现价  
				  $p_now_price = $p_n_price;
				}
				
				 if($p_n_storenum!=""){  //库存
				  $p_storenum = $p_n_storenum;
				}
				
				 if($p_n_need_score!=""){  //需要积分
				  $p_need_score = $p_n_need_score; 
				}
				
				if($p_n_cost_price!=""){    //供货价
				  $p_cost_price = $p_n_cost_price;
				}
				if($p_n_for_price!=""){    //成本价
				  $p_for_price = $p_n_for_price;
				}
				
				 if($p_n_foreign_mark!=""){    //外部标识
				  $p_foreign_mark = $p_n_foreign_mark;
				}
				
				if($p_foreign_mark != ""){
					$query_fm_num2 ="select count(1) as num2 from weixin_commonshop_product_prices as pri 
									 LEFT JOIN weixin_commonshop_products as pro on pri.product_id=pro.id
									 where pri.foreign_mark='" . $p_foreign_mark . "' and pro.customer_id = ".$c_id;
					//echo $query_fm_num2;
					$result_fm_num2 = mysql_query($query_fm_num2) or die('L616: Query_fm_num2 failed: ' . mysql_error());   
						while ($row = mysql_fetch_object($result_fm_num2)) {
						$fm_num_2 = $row->num2;
						$fm_num2 += $fm_num_2;
					}				  				  
				}				
				
				$query="insert into weixin_commonshop_product_prices(product_id,proids,orgin_price,now_price,for_price,storenum,need_score,cost_price,unit,foreign_mark,weight) value(".$p_id.",'".$proids."',".$p_orgin_price.",".$p_now_price.",".$p_for_price.",".$p_storenum.",".$p_need_score.",".$p_cost_price.",'".$p_unit."','".$p_foreign_mark."',".$p_weight.")";
			
				mysql_query($query)or die('L1160 : Query failed1: ' . mysql_error()); 
			}
			if(0 < $fm_num2){
				//echo "<script>alert('属性价格存在已使用的外部标识，请检查确保正确！');</script>";
			}			
			
		}
	}
	//4M模式 End
 }

if(!empty($stock_pidarr)){
	$stock_pidarr_new = explode("_",$stock_pidarr);
	$key = array_search($keyid, $stock_pidarr_new);
	if ($key !== false){
		array_splice($stock_pidarr_new, $key, 1);
		//var_dump($stock_pidarr_new);
	}
	$stock_pidarrlst=implode("_",$stock_pidarr_new);
	//echo $stock_pidarrlst;
	//return;
}

//必填信息入数据库
$mess_num = 0;//获取自定义名称个数
if(!empty($_POST["mess_num"])){
	$mess_num = $configutil->splash_new($_POST["mess_num"]); //添加信息个数

}

if( $is_Pinformation == 1 ){

	for( $i = 1; $i <= $mess_num; $i++){
		$singletext_con = "";
		$name_id        = -1;
		if(!empty($_POST["name_id".$i])){
			$name_id = $configutil->splash_new($_POST["name_id".$i]); //判断数据是否存在表里
		}
		if(!empty($_POST["singletext_con_".$i])){
			$singletext_con = $configutil->splash_new($_POST["singletext_con_".$i]); //自定义名称
		}
		
		$query = "select id from weixin_commonshop_product_information_t where name='".$singletext_con."' and p_id=".$Pkid." and customer_id=".$customer_id;
		$res = mysql_query($query) or die('t Query failed: ' . mysql_error());
		if(mysql_num_rows($res)>0){
			$name_id = -1;
			$singletext_con = "";
		}//判断信息是否已存在
		
		if( 1 > $name_id ){
			if($singletext_con != ""){			
				$sql_ins="insert into weixin_commonshop_product_information_t(customer_id,isvalid,name,p_id,createtime) values(".$customer_id.",true,'". $singletext_con ."',".$Pkid.",now())";
				$result = mysql_query($sql_ins) or die('L1163 Query failed: ' . mysql_error());
			}
		}else{
			if($singletext_con != ""){
				$sql_ins="update weixin_commonshop_product_information_t set name='". $singletext_con ."'  where id = ".$name_id." and customer_id=".$customer_id;
			}else{
				$sql_ins="update weixin_commonshop_product_information_t set isvalid=flase  where id = ".$name_id." and customer_id=".$customer_id;
			}
			$result = mysql_query($sql_ins) or die('L1163 Query failed: ' . mysql_error());
		}
			// echo $sql_ins;
			// $result = mysql_query($sql_ins) or die('L1163 Query failed: ' . mysql_error());
	}

}
//return;
$error =mysql_error();
mysql_close($link);
echo $error; 

 if(!empty($stock_pidarrlst)){	
	 echo "<script>location.href='stock_product.php?customer_id=".$customer_id_en."&pagenum=".$pagenum."&stock_pidarr=".$stock_pidarrlst."';</script>";
 }else{
	
	 if($head == 11){
		 echo "<script>location.href='sale.php?customer_id=".$customer_id_en."&pagenum=".$pagenum."';</script>";
	 }elseif($head == 12){
		 echo "<script>location.href='store.php?customer_id=".$customer_id_en."&pagenum=".$pagenum."';</script>";
	 }elseif($head == 13){
		 echo "<script>location.href='saleout.php?customer_id=".$customer_id_en."&pagenum=".$pagenum."';</script>";
	 }elseif($head == 14){
		 echo "<script>location.href='warn.php?customer_id=".$customer_id_en."&pagenum=".$pagenum."';</script>";
	 }else{
		 echo "<script>location.href='sale.php?customer_id=".$customer_id_en."&pagenum=".$pagenum."';</script>";
	 }	
 }
/*  echo "<script>location.href='product.php?customer_id=".$customer_id."&pagenum=".$pagenum."';</script>" */
?>