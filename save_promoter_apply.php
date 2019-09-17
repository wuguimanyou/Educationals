<?php
header("Content-type: text/html; charset=utf-8");     
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
//require('../back_init.php'); 
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD); 
mysql_select_db(DB_NAME) or die('Could not select database');
mysql_query("SET NAMES UTF8");
require('../common/jssdk.php');
require('../common/utility_shop.php');  
require('../proxy_info.php');
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
$name 				= '';	//姓名
$phone 				= '';	//手机号
$level          	= 1;	//推广员等级
$qrsell_orderothers = '';	//推广员申请自定义
$data           	= array();

$username 			= $configutil->splash_new($_POST['name']);
$phone 				= $configutil->splash_new($_POST['phone']);
$level 				= $configutil->splash_new($_POST['level']);
$qrsell_orderothers = $configutil->splash_new($_POST['qrsell_orderothers']);



if(1==$level){
 $status =0;

 if(!empty($username)){
	 $query="update weixin_users set name='".$username."',phone='".$phone."' where id=".$user_id;
	 mysql_query($query);
 }

    $iscall = 1;
	$sql = "LOCK TABLES weixin_locks_qrs WRITE";
	mysql_query($sql);
	
    $sql="select 0 as iscall from weixin_locks_qrs limit 0,1";
	$result = mysql_query($sql) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_object($result)) {
	    $iscall = $row->iscall;	
	}
	
	if($iscall==0){
		$sql = "UNLOCK TABLES";
        mysql_query($sql); 

		 $qr_info_id =-1;
		 $query="select id,scene_id from weixin_qr_infos where type=1 and user_type=1 and isvalid=true and customer_id=".$customer_id." and foreign_id=".$user_id;
		 $result = mysql_query($query) or die('Query failed: ' . mysql_error());  
		 
		 $scene_id = -1;
		 while ($row = mysql_fetch_object($result)) {
			$scene_id = $row->scene_id;
			$qr_info_id=$row->id;
		 }
		 
		 if($scene_id<0){
			$query="select max(scene_id) as scene_id from weixin_qr_infos where isvalid=true and customer_id=".$customer_id;
			$result = mysql_query($query) or die('Query failed: ' . mysql_error());  
			$scene_id=1;
			while ($row = mysql_fetch_object($result)) {
				$scene_id = $row->scene_id;
				break;
			}
			$scene_id++;
			$sql="insert into weixin_qr_infos(foreign_id,type,scene_id,isvalid,customer_id) values(".$user_id.",1,".$scene_id.",true,".$customer_id.")";
			mysql_query($sql);
			$qr_info_id = mysql_insert_id();
		 }
		 $query="select id,ticket,status from weixin_qrs where customer_id=".$customer_id." and isvalid=true and type=1 and qr_info_id=".$qr_info_id;
		 $qr_id=-1;
		 $status = 0;
		 $status_type = 9;
		 $result = mysql_query($query) or die('Query failed: ' . mysql_error());  
		 while ($row = mysql_fetch_object($result)) {
			 $qr_id = $row->id;
			 $status= $row->status;
			 break;
		 }
		  
		 if($status==0){
			  //是否自动生成推广员
			 $is_autoupgrade=0;
			 $auto_upgrade_money = -1;
			 $query="select is_autoupgrade,auto_upgrade_money from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
			  $result = mysql_query($query) or die('Query failed: ' . mysql_error());  
			 
			  while ($row = mysql_fetch_object($result)) {
				 $is_autoupgrade = $row->is_autoupgrade;
				 $auto_upgrade_money = $row->auto_upgrade_money;
			 }
			  $parent_id=-1;
			  $query="select parent_id from weixin_users where isvalid=true and id=".$user_id;
			  $result = mysql_query($query) or die('Query failed: ' . mysql_error());
			   while ($row = mysql_fetch_object($result)) {
				   $parent_id = $row->parent_id;
			   }
			 if($is_autoupgrade==1){
				 if($auto_upgrade_money==0){
					 $status = 1;  
				 }else{
					 $t_consumemoney = 0;
					 $query="select sum(totalprice) as total_money from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and user_id =".$user_id." and paystatus=1  and sendstatus<3 and return_status in(0,3,9)";
					 $result = mysql_query($query) or die('Query failed: ' . mysql_error());  
					 while ($row = mysql_fetch_object($result)) {
						 $t_consumemoney = $row->total_money;
					 }
					 if($t_consumemoney>=$auto_upgrade_money){
						$status=1;
					 }
					
				 }
				  //增加上级推广员数量
				   $query="update promoters set promoter_count= promoter_count+1 where isvalid=true and status=1 and user_id=".$parent_id;
				   mysql_query($query);
				   
				   $status_type = 10;
			 }
			 if($qr_id<0){
				 $action_name ="QR_LIMIT_SCENE";
				 $query="insert into weixin_qrs(action_name,expire_seconds,qr_info_id,customer_id,isvalid,createtime,type,status) values('".$action_name."',-1,".$qr_info_id.",".$customer_id.",true,now(),1,".$status.")";
				 mysql_query($query);
				 $qr_id = mysql_insert_id();
			 }else{
				 $query="update weixin_qrs set status=".$status." where id=".$qr_id;
				 mysql_query($query);
				 
				 $query="update promoters set status=".$status.",qrsell_orderothers='".$qrsell_orderothers."' where user_id=".$user_id;
				 mysql_query($query);
			 }
			 
			   $query="select id,pwd,customer_id from promoters where  isvalid=true  and user_id=".$user_id;
			   $result = mysql_query($query) or die('Query failed: ' . mysql_error());
			   $pwd = "";
			   $before_customer_id=-1;
			   $promoter_id = -1;
			   while ($row = mysql_fetch_object($result)) {
				   $promoter_id = $row->id;
				   $pwd=$row->pwd;
				   $before_customer_id = $row->customer_id;
			   }
			   $generation=1;
			   if($parent_id>0){
				   $query="select generation from promoters where isvalid=true  and user_id=".$parent_id;
				   $result = mysql_query($query) or die('Query failed10: ' . mysql_error());
				   while ($row = mysql_fetch_object($result)) {
						$generation = $row->generation;
				   }
				   $generation=$generation+1;
			   }
			   if($promoter_id<0){
				   $pwd="888888";
				   $sql ="insert into promoters(user_id,pwd,isvalid,customer_id,parent_id,createtime,status,generation,qrsell_orderothers) values(".$user_id.",'888888',true,".$customer_id.",".$parent_id.",now(),".$status.",".$generation.",'".$qrsell_orderothers."')";
				   mysql_query($sql);
				   $error=mysql_error();
				   //echo $error;   
			   }
			   //生命周期
			   $shopmessage = new shopMessage_Utlity(); 
			   $shopmessage -> ChangeRelation_new($user_id,$parent_id,$parent_id,$customer_id,1,$status_type);

				$qr_Utlity_fun =  new qr_Utlity();
			   $qr_Utlity_fun ->PromoterQr(false,$user_id,$customer_id);
		 }else{
			if($status==-1){
				 $sql="update weixin_qrs set status=0,reason='' where id=".$qr_id;
				 mysql_query($sql);
				 
				 $sql="update promoters set status=0,reason='' where id=".$qr_id;
				 mysql_query($sql);
				 
				 //生命周期
				 $shopmessage = new shopMessage_Utlity(); 
				 $shopmessage -> ChangeRelation_new($user_id,$parent_id,$parent_id,$customer_id,1,$status_type); 
			 }
		 }

		 $error = mysql_error();
		 //echo $error;

		 if($status>0){
			   
			   $parent_id=-1;
			   $query="select parent_id from weixin_users where isvalid=true and id=".$user_id;
			   $result = mysql_query($query) or die('Query failed: ' . mysql_error());
			   while ($row = mysql_fetch_object($result)) {
				   $parent_id = $row->parent_id;
			   }
					   
			   $query="select id,pwd,customer_id from promoters where  isvalid=true and user_id=".$user_id;
			   $result = mysql_query($query) or die('Query failed: ' . mysql_error());
			   $pwd = "";
			   $before_customer_id=-1;
			   $promoter_id = -1;
			   while ($row = mysql_fetch_object($result)) {
				   $promoter_id = $row->id;
				   $pwd=$row->pwd;
				   $before_customer_id = $row->customer_id;
			   }
			   if($promoter_id<0){
				   $pwd="888888";	//密码
				   $sql ="insert into promoters(user_id,pwd,isvalid,customer_id,parent_id,createtime,status,qrsell_orderothers) values(".$user_id.",'888888',true,".$customer_id.",".$parent_id.",now(),0,'".$qrsell_orderothers."')";
				   mysql_query($sql);
				   $error=mysql_error();
				   echo $error;				   
			   }else{
				   //可能出现多次绑定情况
				   //判断推广员发展模式
					$query="select distr_type from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
					$result = mysql_query($query) or die('Query failed: ' . mysql_error());  
					//最后一次为准
					$distr_type=1;
					while ($row = mysql_fetch_object($result)) {
						$distr_type = $row->distr_type;
						break;
					}
					if($distr_type==1){
					   //已最后一次为准
					   if($before_customer_id!=$customer_id){
						   $sql ="update promoters set  pwd='888888' ,status=1, customer_id=".$customer_id.",parent_id=".$parent_id.",createtime=now() where id=".$promoter_id;
						   mysql_query($sql);
					   }else{
						  $sql ="update promoters set parent_id=".$parent_id.",status=1,createtime=now() where id=".$promoter_id;
					   }
				   }
			   }
			    //生命周期
				 $shopmessage = new shopMessage_Utlity(); 
				 $shopmessage -> ChangeRelation_new($user_id,$parent_id,$parent_id,$customer_id,1,$status_type); 
					 
			   $qr_Utlity_fun =  new qr_Utlity();
			   $qr_Utlity_fun ->PromoterQr(false,$user_id,$customer_id);
		 }else{
			if($status==-1){
				   $query="update  promoters set status=0 where  user_id=".$user_id;
				   mysql_query($query);

				  $sql="update promoters set status=0,reason='' where id=".$qr_id;
				   mysql_query($sql);	
				//生命周期
				 $shopmessage = new shopMessage_Utlity(); 
				 $shopmessage -> ChangeRelation_new($user_id,$parent_id,$parent_id,$customer_id,1,$status_type); 
			}
		 }
	}
	
	$query = "select id from promoters where isvalid=true and customer_id=".$customer_id." and user_id=".$user_id." limit 0,1";
	$result = mysql_query($query) or die('query failed'.mysql_error());
	$p_id = -1;
	while($row = mysql_fetch_object($result)){
		$p_id = $row->id;
	}
	if($p_id>0){
		$data['status'] = 1;
	}else{
		$data['status'] = -1;
	}
}else if($level>1){
	$updatetype = 1;	//升级方式 1:手动升级 2:自动升级
	
	$commision = new shopMessage_Utlity();
	$commision->GetMoney_Common_NCommision($customer_id,$user_id,$level,1,$updatetype);
	
	//生命周期
	 $shopmessage = new shopMessage_Utlity(); 
	 $shopmessage -> ChangeRelation_new($user_id,$parent_id,$parent_id,$customer_id,1,11); 
	
	$query = "select id from weixin_commonshop_ncommision_orders where isvalid=true and level=".$level." and customer_id=".$customer_id." and user_id=".$user_id;
	$result = mysql_query($query) or die('query'.mysql_error());
	$o_id = -1;
	while($row = mysql_fetch_object($result)){
		$o_id = $row->id;
	}
	if($o_id>0){
		$data['status'] = 1;
	}else{
		$data['status'] = -1;
	}
}

echo json_encode($data);
?>