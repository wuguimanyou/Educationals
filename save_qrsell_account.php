<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../config.php');
require('../../../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../common/utility_shop.php');


$account_type =$configutil->splash_new($_POST["account_type"]);
$account =$configutil->splash_new($_POST["account"]);
$remark =$configutil->splash_new($_POST["remark"]);
$bank_open =$configutil->splash_new($_POST["bank_open"]);
$isAgent =$configutil->splash_new($_POST["isAgent"]);
$status =$configutil->splash_new($_POST["status"]);
$u_parent_id =$configutil->splash_new($_GET["u_parent_id"]);

$parent_id = -1;
if(!empty($_POST["parent_id"])){
  $parent_id = $configutil->splash_new($_POST["parent_id"]);
}
$pre_parent_id = -1;
if(!empty($_POST["pre_parent_id"])){
  $pre_parent_id = $configutil->splash_new($_POST["pre_parent_id"]);
}
$pagenum = $configutil->splash_new($_GET["pagenum"]);
//echo 'content='.$keyword;
$user_id =$configutil->splash_new($_POST["user_id"]);

$card_member_id =$configutil->splash_new($_POST["card_member_id"]);

 $link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
 mysql_select_db(DB_NAME) or die('Could not select database');
 
 if($status>0 and $isAgent==0){
	if(in_array($parent_id,$count_arr)){
		echo "<script>alert('不能选择自己的下线成为上线');window.history.go(-1)</script>"; 	
		return;	
	}
	if($parent_id == $user_id){
		echo "<script>alert('不能选择自己的下线成为上线');window.history.go(-1)</script>"; 	
		return;	
	} 
}
if($card_member_id>0){
	

/*修改账户信息开始*/
   $sql="update weixin_card_members set account='".$account."',account_type=".$account_type.",bank_open='".$bank_open."' where id=".$card_member_id;
mysql_query($sql)or die('Query failed1:'.mysql_error());
/*修改账户信息结束*/
 
 }
$p_generation=0;//上级推广员代数
$p_gflag = "";//上级推广员基因
$my_generation=1;//自己的新代数
$my_gflag = ",-1,"; //自己新基因
/*查找上级信息开始*/
if($parent_id>0){
	 $query="select weixin_fromuser,generation,gflag from weixin_users where id=".$parent_id;
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_object($result)) {
		 $parent_fromuser= $row->weixin_fromuser;
		 $p_generation= $row->generation;
		 $p_gflag= $row->gflag;
		 break;
	}
	$my_generation=$p_generation+1;
	$my_gflag = $p_gflag.$parent_id.",";
} 
/*查找上级信息结束*/

/*查找自己的代数开始*/
$old_flag = "";//自己的基因
$old_generation = 1;//自己的代数
 $query="select weixin_fromuser,generation,gflag from weixin_users where id=".$user_id;
$result = mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	 $old_generation= $row->generation;
	 $old_flag= $row->gflag;
	 break;
}
/*查找自己的代数结束*/

if($parent_id>0){
	$shopmessage= new shopMessage_Utlity();
	//$shopmessage->ChangeRelation($user_id,$parent_id,$customer_id,1);	//1:商家后台手动改动关系 2:通过分享建立关系 3:推广二维码扫描建立关系;
	$shopmessage->ChangeRelation_new($user_id,$parent_id,$u_parent_id,$customer_id,1,4);
	//为上级增加粉丝
	$shopmessage->AddFanPromoter_count($user_id,$parent_id,$id);
	if($pre_parent_id>0){		//扫描者 不是 本人的上一级,则减粉丝数
		$shopmessage->ReduceFanPromoter_count($user_id,$pre_parent_id,$id);
	}
	//修改推广记录
		$query="select id from weixin_qr_scans where isvalid=true and user_id=".$user_id." limit 0,1";
		 $result = mysql_query($query) or die('Query failed: ' . mysql_error());
		 $qr_scan_id=-1;
		 while ($row = mysql_fetch_object($result)) {
			 $qr_scan_id = $row->id;
			 break;
		 }
		 if($qr_scan_id>0){
			$sql="update weixin_qr_scans set  scene_id=".$parent_id.",fromw=4 where id=".$qr_scan_id;
			mysql_query($sql);
		}else{
			$sql="insert into weixin_qr_scans(user_id,scene_id,type,customer_id,isvalid,createtime,fromw) values(".$user_id.",".$parent_id.",2,".$customer_id.",true,now(),4)";
			mysql_query($sql);
		}
}


$query="update promoters set parent_id=".$parent_id." where isvalid=true and user_id=".$user_id;
mysql_query($query);	

$add_generation = $my_generation-$old_generation;//更改关系增加的代数 

$query="update  weixin_users set parent_id=".$parent_id.",generation=".$my_generation.",gflag='".$my_gflag."' where id=".$user_id;
mysql_query($query);

$query="update  weixin_users set gflag=replace(gflag,'".$old_flag."','".$my_gflag."') ,generation=generation+".$add_generation." where match(gflag) against (',".$user_id.",')";
mysql_query($query);


/* $count_arr=array();	
$is_alreadyRelat=0;
$p_user_id=$user_id;
$curr_num=0;
while(true){
$query="select user_id from promoters where isvalid=true and status=1 and parent_id=".$p_user_id;
$result = mysql_query($query) or die('Query failed: ' . mysql_error());
$p_parent_id=-1;
while ($row = mysql_fetch_object($result)) {			//查找下线
  $p_parent_id = $row->user_id;
  break;
}
	array_push($count_arr,$p_parent_id);
	if($p_parent_id==-1){
	   $is_alreadyRelat=1;
	   break;
	}
	$p_user_id = $p_parent_id;
	$curr_num++;
	if($curr_num>20){
	  //查找20层关系
	  break;
	}
} 
if($status>0 and $isAgent==0){
	if(in_array($parent_id,$count_arr)){
		echo "<script>alert('不能选择自己的下线成为上线');window.history.go(-1)</script>"; 	
		return;	
	}
	if($parent_id == $user_id){
		echo "<script>alert('不能选择自己的下线成为上线');window.history.go(-1)</script>"; 	
		return;	
	} 
}

	$sql="update weixin_card_members set account='".$account."',account_type=".$account_type.",bank_open='".$bank_open."' where id=".$card_member_id;
	 mysql_query($sql);
	 
	 if($parent_id>0){
		$shopmessage= new shopMessage_Utlity();
		$shopmessage->ChangeRelation($user_id,$parent_id,$customer_id,1);	//1:商家后台手动改动关系 2:通过分享建立关系 3:推广二维码扫描建立关系;
		
		$sql="update weixin_users set parent_id=".$parent_id." where isvalid=true and id=".$user_id;
		mysql_query($sql);
		$sql="update promoters set parent_id=".$parent_id." where isvalid=true and user_id=".$user_id;
		mysql_query($sql);
		//为上级增加粉丝
		$shopmessage->AddFanPromoter_count($user_id,$parent_id,$id);
		if($pre_parent_id>0){		//扫描者 不是 本人的上一级,则减粉丝数
			$shopmessage->ReduceFanPromoter_count($user_id,$pre_parent_id,$id);
		}
		//修改推广记录
		$query="select id from weixin_qr_scans where isvalid=true and user_id=".$user_id." limit 0,1";
		 $result = mysql_query($query) or die('Query failed: ' . mysql_error());
		 $qr_scan_id=-1;
		 while ($row = mysql_fetch_object($result)) {
			 $qr_scan_id = $row->id;
			 break;
		 }
		 if($qr_scan_id>0){
			$sql="update weixin_qr_scans set  scene_id=".$parent_id.",fromw=4 where id=".$qr_scan_id;
			mysql_query($sql);
		}else{
			$sql="insert into weixin_qr_scans(user_id,scene_id,type,customer_id,isvalid,createtime,fromw) values(".$user_id.",".$parent_id.",2,".$customer_id.",true,now(),4)";
			mysql_query($sql);
		}
		
	 } */
 
 $error =mysql_error();
 mysql_close($link);
 echo $error; 
echo "<script>location.href='promoter.php?customer_id=".$customer_id_en."&pagenum=".$pagenum."';</script>"
?>