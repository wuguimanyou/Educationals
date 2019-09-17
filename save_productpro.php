<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../back_init.php');
require('../common/utility.php');
require('../common/utility_4m.php');
$name =$configutil->splash_new($_POST["name"]);
$keyid =$configutil->splash_new($_POST["keyid"]);

$parent_id = -1;
$subpros = $configutil->splash_new($_POST["subpro"]);
//echo $subpros;

$adminuser_id = $configutil->splash_new($_GET["adminuser_id"]);
$orgin_adminuser_id = $configutil->splash_new($_GET["orgin_adminuser_id"]);
$owner_general = $configutil->splash_new($_GET["owner_general"]);


$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
 mysql_select_db(DB_NAME) or die('Could not select database');

 $customer_ids="";
 $sub_parent_id=$keyid;
 if($owner_general){
	//是厂家总店
	$u4m = new Utiliy_4m();
	$customer_ids = $u4m->getAllSubCustomers($adminuser_id,$orgin_adminuser_id,$owner_general);
}

 if($keyid>0){
    mysql_query("update weixin_commonshop_pros set name='".$name."',parent_id=".$parent_id." where id=".$keyid);
    if($owner_general and !empty($customer_ids)){
		$carr = explode(",",$customer_ids);
		foreach($carr as $keys=>$values){ 
		   $c_id = $values;
		   //更新4M下 属性名称
		   $sql="update weixin_commonshop_pros set name='".$name."' where customer_id=".$c_id." and create_parent_id=".$sub_parent_id;
		   mysql_query($sql);
	   }
	}
	
	
	$query2="select id,name from weixin_commonshop_pros where parent_id=".$keyid;
    $result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());
	
	$arrLst = new ArrayList();
	
	$arrLst_id = new ArrayList();
	while ($row2 = mysql_fetch_object($result2)) {
	    $rname = $row2->name;
		$rid = $row2->id;
	    $arrLst->Add($rname);
	    $arrLst_id->Add($rid);	
    }							 
	$arrLst2 = new ArrayList();
	
	$subparr = explode("_",$subpros);
	for($i=0;$i<count($subparr);$i++){
	    $sname = $subparr[$i];
		$arrLst2->Add($sname);
		if(!$arrLst->Contains($sname)){
           mysql_query("insert into weixin_commonshop_pros(name,parent_id,customer_id,isvalid) values ('".$sname."',".$keyid.",".$customer_id.",true)");
		   $create_parent_id = mysql_insert_id();
		   
		   if($owner_general and !empty($customer_ids)){
				$carr = explode(",",$customer_ids);
				foreach($carr as $keys=>$values){ 
				   $c_id = $values;
				   //查找商家对应的， 相同属性名称的 父类编号
				   $query="select id from weixin_commonshop_pros where customer_id=".$c_id." and create_parent_id=".$keyid;
				   $result = mysql_query($query) or die('Query failed: ' . mysql_error());   
				   $sub_parent_id=-1;
		           while ($row = mysql_fetch_object($result)) {
					   $sub_parent_id = $row->id;
				   }
				   //属性的下面的属性，create_parent_id=-1
				   $sql="insert into weixin_commonshop_pros(name,parent_id,customer_id,isvalid,create_type,create_parent_id) values ('".$sname."',".$sub_parent_id.",".$c_id.",true,".$owner_general.",".$create_parent_id.")";
				   mysql_query($sql);
			   }
		    }
		}
	}
	
	$f = fopen('out_savepro.txt', 'w');  
	//删除没用的属性
	for($i=0;$i<$arrLst->Size();$i++){
	   $oname = $arrLst->Get($i);
	   
fwrite($f, "=oname======".$oname."\r\n");  
		

	   if(!empty($oname)){
		   if(!$arrLst2->Contains($oname)){
			  //不存在的要删除
			  $rid = $arrLst_id->Get($i);
			  mysql_query("delete from weixin_commonshop_pros where id=".$rid);
			  fwrite($f, "=rid======".$rid."\r\n");  
			  
			  if($owner_general and !empty($customer_ids)){
					$carr = explode(",",$customer_ids);
					foreach($carr as $keys=>$values){ 
					   $c_id = $values;
					   
					   $sql="delete from weixin_commonshop_pros where customer_id=".$c_id." and create_type=".$owner_general." and create_parent_id=".$rid;
					   
					   fwrite($f, "=sql======".$sql."\r\n");  
					   mysql_query($sql);
				   }
				}
		   }
	   }
	}
	fclose($f);
	
}else{
    mysql_query("insert into weixin_commonshop_pros(name,parent_id,customer_id,isvalid) values ('".$name."',".$parent_id.",".$customer_id.",true)");
	$p_id= mysql_insert_id();
	
	$subparr = explode("_",$subpros);
	$subid_arr = Array();
	for($i=0;$i<count($subparr);$i++){
	    $sname = $subparr[$i];
        mysql_query("insert into weixin_commonshop_pros(name,parent_id,customer_id,isvalid) values ('".$sname."',".$p_id.",".$customer_id.",true)");
		$sub_id = mysql_insert_id();
		$subid_arr[$i] = $sub_id;
	}
	
	if($owner_general and !empty($customer_ids)){
		$carr = explode(",",$customer_ids);
		foreach($carr as $keys=>$values){ 
		   $c_id = $values;
		   //给厂家下面所有的商家创建一个分类
		  mysql_query("insert into weixin_commonshop_pros(name,parent_id,customer_id,isvalid,create_type,create_parent_id) values ('".$name."',".$parent_id.",".$c_id.",true,".$owner_general.",".$p_id.")");
		  $p_p_id= mysql_insert_id(); 
		  
		  for($i=0;$i<count($subparr);$i++){
			  $sname = $subparr[$i];
			  $sub_id = $subid_arr[$i];
			  mysql_query("insert into weixin_commonshop_pros(name,parent_id,customer_id,isvalid,create_type,create_parent_id) values ('".$sname."',".$p_p_id.",".$c_id.",true,".$owner_general.",".$sub_id.")");	
		  }
		} 
	}
 }
 
 $error =mysql_error();
 mysql_close($link);
// echo $error; 
 echo "<script>location.href='product_pro.php?customer_id=".$customer_id_en."';</script>"
?>