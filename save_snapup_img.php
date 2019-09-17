<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../config.php');
require('../../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD); 
mysql_select_db(DB_NAME) or die('Could not select database');

$keyid = -1;
$pid = -1;
$snapup_img = "";
$pagenum = 1;

$snapup_img = $configutil->splash_new($_POST["snapup_img"]);

if(!empty($_POST["keyid"])){
	   $keyid = $configutil->splash_new($_POST["keyid"]);
}
if(!empty($_POST["pid"])){
	   $pid = $configutil->splash_new($_POST["pid"]);
}
if(!empty($_GET["pagenum"])){
	   $pagenum = $configutil->splash_new($_GET["pagenum"]);
}

/*图片上传*/	
 $uptypes=array('image/jpg', //上传文件类型列表
'image/jpeg',
'image/png',
'image/pjpeg',
'image/gif',
'image/bmp',
'image/x-png');
$max_file_size=1000000; //上传文件大小限制, 单位BYTE
$path_parts=pathinfo($_SERVER['PHP_SELF']); //取得当前路径
$destination_folder="../../".Base_Upload."snapup_img/"; 									//上传文件路径

$imgpreview=1; //是否生成预览图(1为生成,0为不生成);
$imgpreviewsize=1/1; //缩略图比例
$destination = "";
$website_default= "http://".CLIENT_HOST."/weixin/plat/app/html/";

 if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if (!is_uploaded_file($_FILES["upfile"]["tmp_name"]))	//判断是否上传文件，是则不上传文件，使用旧文件
	{	
	   $destination = $_POST["snapup_img"];
	   //echo  $destination;
	}else{
		$file = $_FILES["upfile"];
		if($max_file_size < $file["size"])
		//检查文件大小
		{
			echo "<font color='red'>文件太大！</font>";
			exit;
		}
		if(!in_array($file["type"], $uptypes))
		//检查文件类型
		{
		  echo "<font color='red'>不能上传此类型文件！</font>";
		  exit;
		}
		if(!file_exists($destination_folder))
		   mkdir($destination_folder,0777,true);

		  $filename=$file["tmp_name"];

		  $image_size = getimagesize($filename);

		  $pinfo=pathinfo($file["name"]);
		
		  $ftype=$pinfo["extension"];
		  $destination = $destination_folder.time().".".$ftype;
		  
		  $overwrite=true;
		  if (file_exists($destination) && $overwrite != true)
		  {
			 echo "<font color='red'>同名文件已经存在了！</a>";
			 exit;
		   }
		  if(!move_uploaded_file ($filename, $destination))
		  {
			 echo "<font color='red'>移动文件出错！</a>";
			 exit;
		  }
	}
}  
/*图片上传end*/	
$destination = str_replace("../","",$destination);
$destination = "/weixinpl/".$destination;

$query = "update snapup_product_t set snapup_img='".$destination."' where isvalid=true and pid=".$pid." and time_id=".$keyid;
mysql_query($query) or die('Query failed'.mysql_error());

mysql_close($link);
echo "<script>location.href='snapup_product.php?keyid=".$keyid."&customer_id=".passport_encrypt((string)$customer_id)."&pagenum=".$pagenum."';</script>";
?>