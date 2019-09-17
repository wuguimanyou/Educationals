<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../back_init.php');
require('product_type_utlity.php');
require('../common/utility_4m.php');

$name =$configutil->splash_new($_POST["name"]);
$keyid =$configutil->splash_new($_POST["keyid"]);

$parent_id = $configutil->splash_new($_POST["parent_id"]);
$sendstyle=$configutil->splash_new($_POST["sendstyle"]);
$type_imgurl=$configutil->splash_new($_POST["type_imgurl"]);

$adminuser_id = $configutil->splash_new($_GET["adminuser_id"]);
$orgin_adminuser_id = $configutil->splash_new($_GET["orgin_adminuser_id"]);
$owner_general = $configutil->splash_new($_GET["owner_general"]);
//echo "+++type_imgurl==".$type_imgurl;
$f = fopen('out_savetype.txt', 'w'); 
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
 mysql_select_db(DB_NAME) or die('Could not select database');
 $customer_ids="";
 if($owner_general>0){
	//是厂家总店
	$u4m = new Utiliy_4m();
	$customer_ids = $u4m->getAllSubCustomers($adminuser_id,$orgin_adminuser_id,$owner_general);
}
		
if($keyid>0){
	//查找新上级的上级是否自己，防止交叉、超过3级
	$product_type_utlity = new Product_Type_Utlity();
	$re=$product_type_utlity->search_up($keyid,$parent_id);
	//file_put_contents ( "11111111.txt", "num=".$re."\r\n", FILE_APPEND);
	if($re<0){
		echo"<script>alert('隶属关系不正确');window.history.go(-1)</script>"; 	
		return;		
	}else if($re>3){
		echo"<script>alert('隶属关系最大为3层！');window.history.go(-1)</script>"; 	
		return;
	}else{
		mysql_query("update weixin_commonshop_types set name='".$name."',imgurl='".$type_imgurl."',parent_id=".$parent_id.",sendstyle=".$sendstyle." where id=".$keyid);
		//是否总店编辑
		
		if($owner_general and !empty($customer_ids)){
			
			$carr = explode(",",$customer_ids);
			foreach($carr as $keys=>$values){ 
				//是厂家\代理商总店
				 $c_id = $values;
				//更新类型名称，所有的下级商家，并且类型为厂家、代理商类型
				$sub_parent_id=-1;
				if($owner_general==1){
					//厂家创建
					$query="select id from weixin_commonshop_types where isvalid=true and customer_id=".$c_id." and create_parent_id=".$parent_id." and create_type=1";
					fwrite($f, "=query======".$query."\r\n"); 
					$result = mysql_query($query) or die('Query failed: ' . mysql_error());   
					while ($row = mysql_fetch_object($result)) {
						$sub_parent_id = $row->id;
					}
				}else{
					//代理商创建
					if($parent_id==-1){
						//如果是代理商创建的顶级类型
						//$sub_parent_id = $create_parent_id;				
					}else{
						$query="select create_parent_id from weixin_commonshop_types where isvalid=true and customer_id=".$customer_id." and   id=".$parent_id;
						fwrite($f, "=query======".$query."\r\n"); 
						$result = mysql_query($query) or die('Query failed: ' . mysql_error());   
						$create_parent_id_tmp=-1;
						while ($row = mysql_fetch_object($result)) {
							$create_parent_id_tmp = $row->create_parent_id;
						}
						fwrite($f, "=create_parent_id_tmp======".$create_parent_id_tmp."\r\n"); 
						fwrite($f, "=parent_id======".$parent_id."\r\n"); 
						if($create_parent_id_tmp==-1){
							//如果没有创建者
						   $create_parent_id_tmp = $parent_id;
						}
							
						
						$query="select id from weixin_commonshop_types where isvalid=true and customer_id=".$c_id." and create_parent_id=".$create_parent_id_tmp;
					     fwrite($f, "=query2======".$query."\r\n"); 	
						$result = mysql_query($query) or die('Query failed: ' . mysql_error());   
						while ($row = mysql_fetch_object($result)) {
							$sub_parent_id = $row->id;
						}
					}
					
				}
				
				$sql="update weixin_commonshop_types set name='".$name."',imgurl='".$type_imgurl."',parent_id=".$sub_parent_id.",sendstyle=".$sendstyle." where customer_id=".$c_id." and create_type=".$owner_general." and create_parent_id=".$keyid;
				
				fwrite($f, "=sql======".$sql."\r\n"); 
				mysql_query($sql);
			}
        }
	}	
}else{
	//默认是商家
	$create_type =3;
	if($owner_general>0){
		$create_type = $owner_general;
	}
	mysql_query("insert into weixin_commonshop_types(name,imgurl,parent_id,sendstyle,customer_id,isvalid,create_type) values ('".$name."','".$type_imgurl."',".$parent_id.",".$sendstyle.",".$customer_id.",true,".$create_type.")");
	$create_parent_id = mysql_insert_id();
 
 
    fwrite($f, "=customer_ids======".$customer_ids."\r\n");  
	
	if($owner_general and !empty($customer_ids)){
		$carr = explode(",",$customer_ids);
		foreach($carr as $keys=>$values){ 
		       $c_id = $values;
		   //给厂家下面所有的商家创建一个分类
		    $sub_parent_id=-1;
		    if($owner_general==1){
				//厂家创建
				$query="select id from weixin_commonshop_types where isvalid=true and customer_id=".$c_id." and create_parent_id=".$parent_id." and create_type=1";
				fwrite($f, "=query======".$query."\r\n"); 
				$result = mysql_query($query) or die('Query failed: ' . mysql_error());   
				while ($row = mysql_fetch_object($result)) {
					$sub_parent_id = $row->id;
				}
			}else{
				//代理商创建
				if($parent_id==-1){
					//如果是代理商创建的顶级类型
					//$sub_parent_id = $create_parent_id;				
				}else{
					$query="select create_parent_id from weixin_commonshop_types where isvalid=true and customer_id=".$customer_id." and   id=".$parent_id;
					fwrite($f, "=query======".$query."\r\n"); 
					$result = mysql_query($query) or die('Query failed: ' . mysql_error());   
					$create_parent_id_tmp=-1;
					while ($row = mysql_fetch_object($result)) {
						$create_parent_id_tmp = $row->create_parent_id;
					}
					fwrite($f, "=create_parent_id_tmp======".$create_parent_id_tmp."\r\n"); 
					fwrite($f, "=parent_id======".$parent_id."\r\n"); 
					if($create_parent_id_tmp==-1){
						//如果没有创建者
					   $create_parent_id_tmp = $parent_id;
					}
						
					
					$query="select id from weixin_commonshop_types where isvalid=true and customer_id=".$c_id." and create_parent_id=".$create_parent_id_tmp;
					 fwrite($f, "=query2======".$query."\r\n"); 	
					$result = mysql_query($query) or die('Query failed: ' . mysql_error());   
					while ($row = mysql_fetch_object($result)) {
						$sub_parent_id = $row->id;
					}
				}
		   }
			
		    $sql="insert into weixin_commonshop_types(name,imgurl,parent_id,sendstyle,customer_id,isvalid,create_type,create_parent_id) values ('".$name."','".$type_imgurl."',".$sub_parent_id.",".$sendstyle.",".$c_id.",true,".$owner_general.",".$create_parent_id.")";
		    mysql_query($sql);
		} 
	}
}
	fclose($f);
 $error =mysql_error();
 mysql_close($link);
 echo $error; 
 echo "<script>location.href='product_type.php?customer_id=".$customer_id_en."';</script>"
?>