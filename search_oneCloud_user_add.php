<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

$id =$configutil->splash_new($_POST["id"]);
$data=array();
$i=0;
//$query = "SELECT id,name,weixin_name from weixin_users where isvalid=1 and id like '%".$id."%' and customer_id = ".$customer_id; 
$query = 'SELECT pro.user_id,users.name,users.weixin_name from promoters as pro LEFT JOIN weixin_users as users on users.id=pro.user_id  where  pro.isAgent = 0 and pro.isvalid=1 and pro.customer_id = '.$customer_id ." and pro.user_id like '%".$id."%' order by pro.user_id";
$result = mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$user_id = $row->user_id;
	$name = $row->name;
	$weixin_name = $row->weixin_name;
	$names = "[编号". $user_id . "] ".$name;
	if(!empty($weixin_name)){
		$names .= "(" . $weixin_name  . ")";
	}
	$data[$i]['user_id'] = $user_id;
	$data[$i]['names'] = $names;
	$i++;
}
$data=json_encode($data);
echo $data;
?>