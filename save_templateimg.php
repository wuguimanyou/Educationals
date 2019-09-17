<?php
 header("Content-type: text/html; charset=utf-8"); 

 require('../config.php');
 require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
 require('../back_init.php');
 
 
 
 $link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
 mysql_select_db(DB_NAME) or die('Could not select database');
//$new_baseurl = BaseURL."back_commonshop/";  //11.13隐藏
  require('../proxy_info.php');  /*fenxiao下链接出错 11.13 by cdr*/
$new_baseurl ="http://". $http_host."/weixinpl/back_commonshop/"; 
   
$uptypes=array('image/jpg', //上传文件类型列表
'image/jpeg',
'image/png',
'image/pjpeg',
'image/gif',   
'image/bmp',
'image/x-png');
$max_file_size=1000000; //上传文件大小限制, 单位BYTE
  
//$customer_id = $configutil->splash_new($_GET["customer_id"]); // config 里面已经有customer_id了 ,直接获取为加密后的，注释掉
$template_id = $configutil->splash_new($_GET["template_id"]);
$position=$configutil->splash_new($_POST["position"]);
$contenttype=$configutil->splash_new($_POST["contenttype"]);

$type_id_1_1=-1;
$type_id_1_2=-1;
$type_id_1_3=-1;
$type_id_1_4=-1;
$type_id_1_5=-1;
$type_id_1_6=-1;
$type_id_1_7=-1;
$type_id_1_8=-1;
$type_id_1_9=-1;
$type_id_1_10=-1;


$product_detail_id_1 =-1;
$product_detail_id_2 =-1;
$product_detail_id_3 =-1;
$product_detail_id_4 =-1;
$product_detail_id_5 =-1;
$product_detail_id_6 =-1;
$product_detail_id_7 =-1;
$product_detail_id_8 =-1;
$product_detail_id_9 =-1;
$product_detail_id_10 =-1;

$type_id_2=-1;
$product_detail_id_2 =-1;

$type_id_3=-1;
$product_detail_id_3=-1;


$foreign_id_1_1=-1;
$foreign_id_1_2=-1;
$foreign_id_1_3=-1;
$foreign_id_1_4=-1;
$foreign_id_1_5=-1;

$foreign_id_1_6=-1;
$foreign_id_1_7=-1;
$foreign_id_1_8=-1;
$foreign_id_1_9=-1;
$foreign_id_1_10=-1;

$linktype_id_1_1=-1;
$linktype_id_1_2=-1;
$linktype_id_1_3=-1;
$linktype_id_1_4=-1;
$linktype_id_1_5=-1;
$linktype_id_1_6=-1;
$linktype_id_1_7=-1;
$linktype_id_1_8=-1;
$linktype_id_1_9=-1;
$linktype_id_1_10=-1;



$path_parts=pathinfo($_SERVER['PHP_SELF']); //取得当前路径
$destination_folder="up/template/"; //上传文件路径
if(!file_exists($destination_folder))
  mkdir($destination_folder);
$destination_folder = $destination_folder.$customer_id."/";  
if(!file_exists($destination_folder))
  mkdir($destination_folder);

//$watermark=1; //是否附加水印(1为加水印,0为不加水印);
//$watertype=1; //水印类型(1为文字,2为图片)
//$waterposition=2; //水印位置(1为左下角,2为右下角,3为左上角,4为右上角,5为居中);
//$waterstring="www.tt365.org"; //水印字符串
//$waterimg="xplore.gif"; //水印图片
$imgpreview=1; //是否生成预览图(1为生成,0为不生成);
$imgpreviewsize=1/1; //缩略图比例
$destination = "";
//覆盖
$overwrite = true;
$destination1 = "";
$destination2 = "";
$destination3 = "";
$destination4 = "";
$destination5 = "";

$destination6 = "";
$destination7 = "";
$destination8 = "";
$destination9 = "";
$destination10 = "";


$index_bg = $configutil->splash_new($_POST["index_bg"]);

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{	
	if($contenttype==4){
			//保存视频
	    $video_link = $configutil->splash_new($_POST["video_link"]);
	      
	   //$query="update weixin_commonshop_template_imgs set imgurl='".$destination."',url='".$url."',linktype=".$linktype.",foreign_id=".$foreign_id." where template_id=".$template_id." and position='".$position."'";
		$query="select id from weixin_commonshop_template_item_imgs where isvalid=true and template_id=".$template_id." and position=".$position." and customer_id=".$customer_id;	
		//echo $query."<br/>";
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		$ti_id=-1;
		while ($row = mysql_fetch_object($result)) {
		   $ti_id = $row->id;
		}
		/*$query_font="select id from weixin_commonshop_type_font where isvalid=true and template_id=".$template_id." and font_id=".$ti_id." and customer_id=".$customer_id;	
		$result_font = mysql_query($query_font) or die('Query failed query_font: ' . mysql_error());
		$co_id=-1;
		while ($row_font = mysql_fetch_object($result_font)) {
		   $co_id = $row_font->id;
		}*/
		
		$type_id_3 = $configutil->splash_new($_POST["type_id_3"]);
		$product_detail_id_3 = -1;
		$url="";

		
		$linktype=1;			
		$foreign_id=$type_id_3;
		if($ti_id>0){
			$query="update weixin_commonshop_template_item_imgs set detail_id=".$product_detail_id_3.", video_link='".$video_link."',foreign_id=".$foreign_id."  where id=".$ti_id;
			//echo $query;
			mysql_query($query);
			
		}else{
			$query="insert into weixin_commonshop_template_item_imgs(template_id,imgurl,position,url,linktype,foreign_id,isvalid,createtime,customer_id,video_link,detail_id) values(".$template_id.",'','".$position."','".$url."','".$linktype."','".$foreign_id."',true,now(),".$customer_id.",'".$video_link."','".$product_detail_id_3."')";
			mysql_query($query);
		}
		}


    //保存单页图片
	if($contenttype==3){
	   //保存文字
	    $title = $configutil->splash_new($_POST["title"]);
	    $font_color = $configutil->splash_new($_POST["font_bg"]);
		$ti_id=-1;
	   //$query="update weixin_commonshop_template_imgs set imgurl='".$destination."',url='".$url."',linktype=".$linktype.",foreign_id=".$foreign_id." where template_id=".$template_id." and position='".$position."'";
	   
		$query="select id from weixin_commonshop_template_item_imgs where isvalid=true and template_id=".$template_id." and position=".$position." and customer_id=".$customer_id;	
		//echo $query."<br/>";
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		
		while ($row = mysql_fetch_object($result)) {
		  $ti_id = $row->id;
		}
		
		
		$query_font="select id from weixin_commonshop_type_font where isvalid=true and template_id=".$template_id." and font_id=".$ti_id." and customer_id=".$customer_id;	
		$result_font = mysql_query($query_font) or die('Query failed query_font: ' . mysql_error());
		$co_id=-1;
		while ($row_font = mysql_fetch_object($result_font)) {
		   $co_id = $row_font->id;
		}
		
		$type_id_3 = $configutil->splash_new($_POST["type_id_3"]);
		$product_detail_id_3 = -1;
		$url="";
		if($type_id_3>0){   
			$typestrarr= explode("_",$type_id_3);
			$type_id_3 = $typestrarr[0];
			$linktype=$typestrarr[1];
			if($linktype==1){
			    $product_detail_id_3 = $configutil->splash_new($_POST["product_detail_id_3"]);
				
				if($product_detail_id_3>0){
					    
					$url="detail.php?customer_id=".$customer_id_en."&pid=".$product_detail_id_3;
					 
				 }else{					
					$query3="select name from weixin_commonshop_types where isvalid=true and id=".$type_id_3;
					$result3 = mysql_query($query3) or die('Query failed: ' . mysql_error());
					$typename="";
					while ($row3 = mysql_fetch_object($result3)) {
					   $typename = $row3->name;
					}
					$url="list.php?customer_id=".$customer_id_en."&tid=".$type_id_3."&tname=".$typename;
				}
			}else if($linktype==2){
			   //图文
				$query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$type_id_3;
				$result = mysql_query($query) or die('Query failed: ' . mysql_error());
				while ($row = mysql_fetch_object($result)) {
				   $website_url = $row->website_url;
				}
				$pos = strpos($website_url,"?"); 
				if($pos>0){
				   $website_url = $website_url."&C_id=".$customer_id_en;
				}else{
				   $website_url = $website_url."?C_id=".$customer_id_en;
				}
				$url = $website_url;
			}
		}else{
		   switch($type_id_3){
			   case -6:
				  $url="list.php?customer_id=".$customer_id_en;
				  break;
			   case -2:
				  $url="list.php?isnew=1&customer_id=".$customer_id_en;
				  break;
			   case -3:
				  $url="list.php?ishot=1&customer_id=".$customer_id_en;
				  break;
			   case -4:
				  $url="order_cart.php?customer_id=".$customer_id_en;
				  break;
			   case -7:
				  $url="class_page.php?customer_id=".$customer_id_en;
				  break;
			   case -8:
				  $url="order_list.php?customer_id=".$customer_id_en;
				  break;
			   case -9:
				  $url="index.php?customer_id=".$customer_id_en;
			   case -5:
				  $url="countdown.php?customer_id=".$customer_id_en; 	  
				  break;
		   }
		}
		
		$linktype=1;			
		$foreign_id=$type_id_3;
		
		if($ti_id>0){
			$query="update weixin_commonshop_template_item_imgs set detail_id=".$product_detail_id_3.", title='".$title."',linktype=".$linktype.",url='".$url."',foreign_id=".$foreign_id."  where id=".$ti_id;
			
			//echo $query;
			mysql_query($query);
			
			if($co_id>0){
				$query_color="update weixin_commonshop_type_font set font_color='".$font_color."' where font_id=".$ti_id." and template_id=".$template_id;
				
				mysql_query($query_color) or die('Query failed query_color1: ' . mysql_error());
				//echo $query_color;return;
			}else{
				$query_color="insert into weixin_commonshop_type_font(customer_id,template_id,font_id,font_color,isvalid) values(".$customer_id.",".$template_id.",".$ti_id.",'".$font_color."',1)";
				//echo $query_color;
				
				mysql_query($query_color) or die('Query failed query_color2: ' . mysql_error());
				//return;
			}
			
		}else{
			
			
			$query="INSERT into weixin_commonshop_template_item_imgs(template_id,imgurl,position,url,linktype,foreign_id,isvalid,createtime,customer_id,title,detail_id) values(".$template_id.",'','".$position."','".$url."','".$linktype."','".$foreign_id."',true,now(),".$customer_id.",'".$title."','".$product_detail_id_3."')";
			$result2=mysql_query($query) or die ('query' .mysql_error());
			mysql_query($query);
			$getID=mysql_insert_id();
			//echo $getID;

			$query_color1="INSERT into weixin_commonshop_type_font(customer_id,template_id,font_id,font_color,isvalid) values(".$customer_id.",".$template_id.",".$getID.",'".$font_color."',1)";
			$result=mysql_query($query_color1) or die ('query_color1' .mysql_error());
			
		}   
		
	}else if($contenttype==2){
		if (!is_uploaded_file($_FILES["upfile2"]["tmp_name"]))
		//是否存在文件
		{
	
		    $type_id_2 = $configutil->splash_new($_POST["type_id_2"]);
			
		    $url="";
            if($type_id_2>0){
			    $typestrarr= explode("_",$type_id_2);
				$type_id_2 = $typestrarr[0];
				$linktype=$typestrarr[1];
				if($linktype==1){
				     $product_detail_id_2 = $configutil->splash_new($_POST["product_detail_id_2"]);        
                     if($product_detail_id_2>0){
					    
						 $url="detail.php?customer_id=".$customer_id_en."&pid=".$product_detail_id_2;
					 
					 }else{					
						$query3="select name from weixin_commonshop_types where isvalid=true and id=".$type_id_2;
						$result3 = mysql_query($query3) or die('Query failed: ' . mysql_error());
						$typename="";
						while ($row3 = mysql_fetch_object($result3)) {
						   $typename = $row3->name;
						}
						$url="list.php?customer_id=".$customer_id_en."&tid=".$type_id_2."&tname=".$typename;
					}
				}else if($linktype==2){
				   //图文
				    $query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$type_id_2;
				    $result = mysql_query($query) or die('Query failed: ' . mysql_error());
					while ($row = mysql_fetch_object($result)) {
					   $website_url = $row->website_url;
					}
					$pos = strpos($website_url,"?"); 
					if($pos>0){
					   $website_url = $website_url."&C_id=".$customer_id_en;
					}else{
					   $website_url = $website_url."?C_id=".$customer_id_en;
					}
					$url = $website_url;
				}
			}else{
			   switch($type_id_2){
			       case -6:
				      $url="list.php?customer_id=".$customer_id_en;
				      break;
				   case -2:
				      $url="list.php?isnew=1&customer_id=".$customer_id_en;
				      break;
				   case -3:
				      $url="list.php?ishot=1&customer_id=".$customer_id_en;
				      break;
				   case -4:
				      $url="order_cart.php?customer_id=".$customer_id_en;
				      break;
				   case -7:
					  $url="class_page.php?customer_id=".$customer_id_en;
					  break;
				   case -8:
					  $url="order_list.php?customer_id=".$customer_id_en;
				      break;
				   case -9:
					  $url="index.php?customer_id=".$customer_id_en;
					   break;
				   case -5:
					  $url="countdown.php?customer_id=".$customer_id_en; 		
					  break;
			   }
			}
			
		    $destination = $configutil->splash_new($_POST["imgurl2"]);
			
		    $linktype=1;			
			$foreign_id=$type_id_2;
			
			//$query="update weixin_commonshop_template_imgs set imgurl='".$destination."',url='".$url."',linktype=".$linktype.",foreign_id=".$foreign_id." where template_id=".$template_id." and position='".$position."'";
		    $query="select id from weixin_commonshop_template_item_imgs where  template_id=".$template_id." and isvalid=true and position=".$position." and customer_id=".$customer_id;	
			//echo $query."<br/>";
			$result = mysql_query($query) or die('Query failed: ' . mysql_error());
			$ti_id=-1;
			while ($row = mysql_fetch_object($result)) {
			   $ti_id = $row->id;
			}
			if($ti_id>0){
			    $query="update weixin_commonshop_template_item_imgs set detail_id=".$product_detail_id_2.", imgurl='".$destination."',url='".$url."',linktype='".$linktype."',foreign_id='".$foreign_id."' where id=".$ti_id;
				//echo $query;
				mysql_query($query);
			}else{
				$query="insert into weixin_commonshop_template_item_imgs(template_id,imgurl,position,url,linktype,foreign_id,isvalid,createtime,customer_id,detail_id) values(".$template_id.",'".$destination."','".$position."','".$url."','".$linktype."','".$foreign_id."',true,now(),".$customer_id.",".$product_detail_id_2.")";
				mysql_query($query);
			}
		}else{
			$file = $_FILES["upfile2"];
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
			   mkdir($destination_folder);

			  $filename=$file["tmp_name"];

			  $image_size = getimagesize($filename);

			  $pinfo=pathinfo($file["name"]);

			  $ftype=$pinfo["extension"];
			  $destination = $destination_folder.time().".".$ftype;
			  if (file_exists($destination) && $overwrite != true)
			  {
				 echo "<font color='red'>同名文件已经存在了！</font>";
				 exit;
			   }
			  if(!move_uploaded_file ($filename, $destination))
			  {
				 echo "<font color='red'>移动文件出错！</font>";
				 exit;
			  }
			  //$pinfo=pathinfo($destination);
			  //$fname=$pinfo["basename"];
		//   echo " <font color=red>成功上传,鼠标移动到地址栏自动复制</font><br><table width=\"348\" cellspacing=\"0\" cellpadding=\"5\" border=\"0\" class=\"table_decoration\" align=\"center\"><tr><td><input type=\"checkbox\" id=\"fmt\" onclick=\"select_format()\"/>图片UBB代码<br/><div id=\"site\"><table border=\"0\"><tr><td valign=\"top\">文件地址:</td><td><input type=\"text\" onclick=\"sendtof(this.value)\" onmouseover=\"oCopy(this)\" style=font-size=9pt;color:blue size=\"44\" value=\"http://".$_SERVER['SERVER_NAME'].$path_parts["dirname"]."/".$destination_folder.$fname."\"/>
		  //  </td></tr></table></div><div id=\"sited\" style=\"display:none\"><table border=\"0\"><tr><td valign=\"top\">文件地址:</td><td><input type=\"text\" onclick=\"sendtof(this.value)\" onmouseover=\"oCopy(this)\" style=font-size=9pt;color:blue size=\"44\" value=\"[img]http://".$_SERVER['SERVER_NAME'].$path_parts["dirname"]."/".$destination_folder.$fname."[/img]\"/></td></tr></table></div></td></tr></table>";
			  //echo " 宽度:".$image_size[0];
			 // echo " 长度:".$image_size[1];
			
            $type_id_2 = $configutil->splash_new($_POST["type_id_2"]);
            $url="";
			$linktype=1;
            if($type_id_2>0){
			    $typestrarr= explode("_",$type_id_2);
				$type_id_2 = $typestrarr[0];
				$linktype=$typestrarr[1];
				if($linktype==1){
					
					 $product_detail_id_2 = $configutil->splash_new($_POST["product_detail_id_2"]);        
                     if($product_detail_id_2>0){
					    
						 $url="detail.php?customer_id=".$customer_id_en."&pid=".$product_detail_id_2;
					 
					 }else{					
						$query3="select name from weixin_commonshop_types where isvalid=true and id=".$type_id_2;
						$result3 = mysql_query($query3) or die('Query failed: ' . mysql_error());
						$typename="";
						while ($row3 = mysql_fetch_object($result3)) {
						   $typename = $row3->name;
						}
						$url="list.php?customer_id=".$customer_id_en."&tid=".$type_id_2."&tname=".$typename;
					}
				}else if($linktype==2){
				   //图文
				    $query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$type_id_2;
				    $result = mysql_query($query) or die('Query failed: ' . mysql_error());
					while ($row = mysql_fetch_object($result)) {
					   $website_url = $row->website_url;
					}
					$pos = strpos($website_url,"?"); 
					if($pos>0){
					   $website_url = $website_url."&C_id=".$customer_id_en;
					}else{
					   $website_url = $website_url."?C_id=".$customer_id_en;
					}
					$url = $website_url;
				}
			}else{
			   switch($type_id_2){
			        case -6:
				      $url="list.php?customer_id=".$customer_id_en;
				      break;
				   case -2:
				      $url="list.php?isnew=1&customer_id=".$customer_id_en;
				      break;
				   case -3:
				      $url="list.php?ishot=1&customer_id=".$customer_id_en;
				      break;
				   case -4:
				      $url="order_cart.php?customer_id=".$customer_id_en;
				      break;
				   case -7:
					  $url="class_page.php?customer_id=".$customer_id_en;
					  break;
				   case -8:
					  $url="order_list.php?customer_id=".$customer_id_en;
				      break;
				   case -9:
					  $url="index.php?customer_id=".$customer_id_en;
					  break;
				   case -5:
					  $url="countdown.php?customer_id=".$customer_id_en; 	  
					  break;
			   }
			}
			//默认为链接到类型。以后可以链接到其他的功能模块
           // echo $url;		
			$foreign_id=$type_id_2;
			
			$query="select id from weixin_commonshop_template_item_imgs where isvalid=true and template_id=".$template_id." and position=".$position." and customer_id=".$customer_id;	
			//echo $query."<br/>";
			$result = mysql_query($query) or die('Query failed: ' . mysql_error());
			$ti_id=-1;
			while ($row = mysql_fetch_object($result)) {
			   $ti_id = $row->id;
			}
			if($ti_id>0){
			    $query="update weixin_commonshop_template_item_imgs set detail_id=".$product_detail_id_2.", imgurl='".$new_baseurl.$destination."',url='".$url."',linktype=".$linktype.",foreign_id=".$foreign_id." where id=".$ti_id;
				//echo $query;
				mysql_query($query);
			}else{
				$query="insert into weixin_commonshop_template_item_imgs(template_id,imgurl,position,url,linktype,foreign_id,isvalid,createtime,customer_id,detail_id) values(".$template_id.",'".$new_baseurl.$destination."','".$position."','".$url."',".$linktype.",".$foreign_id.",true,now(),".$customer_id.",".$product_detail_id_2.")";
				mysql_query($query);
			}
	  }
  }else{
    //轮播图片
	$destination_folder = $destination_folder."banner/";  
    if(!file_exists($destination_folder))
        mkdir($destination_folder);
	 $lun_imgurls="";
	 $lun_urls="";
	 $lun_linktypes="";
	 $lun_foreign_ids="";
	 $lun_detail_ids="";
	 if (!is_uploaded_file($_FILES["upfile1_1"]["tmp_name"]))
	 {
	      $destination1 = $configutil->splash_new($_POST["imgids_1_1"]);
		  $type_id_1_1 = $configutil->splash_new($_POST["type_id_1_1"]);
		  $url="";
		  $linktype=1;
		  $product_detail_id_1_1 =-1;
		  if($type_id_1_1>0){
			
			$typestrarr= explode("_",$type_id_1_1);
			$type_id_1_1 = $typestrarr[0];
			$linktype=$typestrarr[1];
			if($linktype==1){
			     $product_detail_id_1_1 = $configutil->splash_new($_POST["product_detail_id_1_1"]);        
				 if($product_detail_id_1_1>0){
					
					 $url="detail.php?customer_id=".$customer_id_en."&pid=".$product_detail_id_1_1;
				 
				 }else{					
					$query3="select name from weixin_commonshop_types where isvalid=true and id=".$type_id_1_1;
					$result3 = mysql_query($query3) or die('Query failed: ' . mysql_error());
					$typename="";
					while ($row3 = mysql_fetch_object($result3)) {
					   $typename = $row3->name;
					}
					$url="list.php?customer_id=".$customer_id_en."&tid=".$type_id_1_1."&tname=".$typename;
				}
				
				
			}else if($linktype==2){
			   //图文
				$query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$type_id_1_1;
				$result = mysql_query($query) or die('Query failed: ' . mysql_error());
				$website_url="";
				while ($row = mysql_fetch_object($result)) {
				   $website_url = $row->website_url;
				}
				$pos = strpos($website_url,"?"); 
				if($pos>0){
				   $website_url = $website_url."&C_id=".$customer_id_en;
				}else{
				   $website_url = $website_url."?C_id=".$customer_id_en;
				}
				$url = $website_url;
			}
		  }else{
			   switch($type_id_1_1){
			        case -6:
				      $url="list.php?customer_id=".$customer_id_en;
				      break;
				   case -2:
				      $url="list.php?isnew=1&customer_id=".$customer_id_en;
				      break;
				   case -3:
				      $url="list.php?ishot=1&customer_id=".$customer_id_en;
				      break;
				   case -4:
				      $url="order_cart.php?customer_id=".$customer_id_en;
				      break;
				   case -7:
					  $url="class_page.php?customer_id=".$customer_id_en;
					  break;
				   case -8:
					  $url="order_list.php?customer_id=".$customer_id_en;
					  break;
				   case -9:
					  $url="index.php?customer_id=".$customer_id_en;
					  break;
				   case -5:
					  $url="countdown.php?customer_id=".$customer_id_en;   
					  break;					
			   }
			}
		  			
		  $foreign_id=$type_id_1_1;
			 
		  $lun_urls=$lun_urls.$url."|*|";
		  $lun_linktypes=$lun_linktypes.$linktype."|*|";
		  $lun_foreign_ids=$lun_foreign_ids.$foreign_id."|*|";
		  $lun_imgurls = $lun_imgurls.$destination1."|*|";
		  
		  $lun_detail_ids=$lun_detail_ids.$product_detail_id_1_1."|*|";
	 }else{
			$file = $_FILES["upfile1_1"];
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
			   mkdir($destination_folder);

			  $filename=$file["tmp_name"];

			  $image_size = getimagesize($filename);

			  $pinfo=pathinfo($file["name"]);

			  $ftype=$pinfo["extension"];
			  $destination1 = $destination_folder.time()."1.".$ftype;
			  if (file_exists($destination1) && $overwrite != true)
			  {
				 echo "<font color='red'>同名文件已经存在了！</font>";
				 exit;
			   }
			  if(!move_uploaded_file ($filename, $destination1))
			  {
				 echo "<font color='red'>移动文件出错！</font>";
				 exit;
			  }
			 
			  $linktype=1;
			  $type_id_1_1 = $configutil->splash_new($_POST["type_id_1_1"]);
			  $product_detail_id_1_1 =-1;
              $url="";
              if($type_id_1_1>0){
			    
				$typestrarr= explode("_",$type_id_1_1);
				$type_id_1_1 = $typestrarr[0];
				$linktype=$typestrarr[1];
				if($linktype==1){
					$product_detail_id_1_1 = $configutil->splash_new($_POST["product_detail_id_1_1"]);        
					 if($product_detail_id_1_1>0){
						
						 $url="detail.php?customer_id=".$customer_id_en."&pid=".$product_detail_id_1_1;
					 
					 }else{					
						$query3="select name from weixin_commonshop_types where isvalid=true and id=".$type_id_1_1;
						$result3 = mysql_query($query3) or die('Query failed: ' . mysql_error());
						$typename="";
						while ($row3 = mysql_fetch_object($result3)) {
						   $typename = $row3->name;
						}
						$url="list.php?customer_id=".$customer_id_en."&tid=".$type_id_1_1."&tname=".$typename;
					}
				}else if($linktype==2){
				   //图文
					$query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$type_id_1_1;
					$result = mysql_query($query) or die('Query failed: ' . mysql_error());
					$website_url="";
					while ($row = mysql_fetch_object($result)) {
					   $website_url = $row->website_url;
					}
					$pos = strpos($website_url,"?"); 
					if($pos>0){
					   $website_url = $website_url."&C_id=".$customer_id_en;
					}else{
					   $website_url = $website_url."?C_id=".$customer_id_en;
					}
					$url = $website_url;
				}		 
			 }else{
			   switch($type_id_1_1){
			        case -6:
				      $url="list.php?customer_id=".$customer_id_en;
				      break;
				   case -2:
				      $url="list.php?isnew=1&customer_id=".$customer_id_en;
				      break;
				   case -3:
				      $url="list.php?ishot=1&customer_id=".$customer_id_en;
				      break;
				   case -4:
				      $url="order_cart.php?customer_id=".$customer_id_en;
				      break;
				   case -7:
					  $url="class_page.php?customer_id=".$customer_id_en;
					  break;
				   case -8:
					  $url="order_list.php?customer_id=".$customer_id_en;
				      break;
				   case -9:
					  $url="index.php?customer_id=".$customer_id_en;
					  break;
				   case -5:
					  $url="countdown.php?customer_id=".$customer_id_en;   
					  break;					  
			   }
			}
			 
			 $foreign_id=$type_id_1_1;
			 
			 $lun_urls=$lun_urls.$url."|*|";
			 $lun_linktypes=$lun_linktypes.$linktype."|*|";
			 $lun_foreign_ids=$lun_foreign_ids.$foreign_id."|*|";
			//默认为链接到类型。以后可以链接到其他的功能模块
			
			 $lun_imgurls = $lun_imgurls.$new_baseurl.$destination1."|*|";
			 $lun_detail_ids=$lun_detail_ids.$product_detail_id_1_1."|*|";
	  }
	  if (!is_uploaded_file($_FILES["upfile1_2"]["tmp_name"]))
	  {
	  
	      $destination2 = $configutil->splash_new($_POST["imgids_1_2"]);
		  $type_id_1_2 = $configutil->splash_new($_POST["type_id_1_2"]);
		  $url="";
		  $linktype=1;
		  $product_detail_id_1_2 = -1;
		  if($type_id_1_2>0){
			$typestrarr= explode("_",$type_id_1_2);
			$type_id_1_2 = $typestrarr[0];
			$linktype=$typestrarr[1];
			if($linktype==1){
				 $product_detail_id_1_2 = $configutil->splash_new($_POST["product_detail_id_1_2"]);        
				 if($product_detail_id_1_2>0){
					
					 $url="detail.php?customer_id=".$customer_id_en."&pid=".$product_detail_id_1_2;
				 
				 }else{					
					$query3="select name from weixin_commonshop_types where isvalid=true and id=".$type_id_1_2;
					$result3 = mysql_query($query3) or die('Query failed: ' . mysql_error());
					$typename="";
					while ($row3 = mysql_fetch_object($result3)) {
					   $typename = $row3->name;
					}
					$url="list.php?customer_id=".$customer_id."&tid=".$type_id_1_2."&tname=".$typename;
				}
			}else if($linktype==2){
			   //图文
				$query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$type_id_1_2;
				$result = mysql_query($query) or die('Query failed: ' . mysql_error());
				$website_url="";
				while ($row = mysql_fetch_object($result)) {
				   $website_url = $row->website_url;
				}
				$pos = strpos($website_url,"?"); 
				if($pos>0){
				   $website_url = $website_url."&C_id=".$customer_id_en;
				}else{
				   $website_url = $website_url."?C_id=".$customer_id_en;
				}
				$url = $website_url;
			}
		  }else{
			   switch($type_id_1_2){
			        case -6:
				      $url="list.php?customer_id=".$customer_id_en;
				      break;
				   case -2:
				      $url="list.php?isnew=1&customer_id=".$customer_id_en;
				      break;
				   case -3:
				      $url="list.php?ishot=1&customer_id=".$customer_id_en;
				      break;
				   case -4:
				      $url="order_cart.php?customer_id=".$customer_id_en;
				      break;
				   case -7:
					  $url="class_page.php?customer_id=".$customer_id_en;
					  break;
				   case -8:
					  $url="order_list.php?customer_id=".$customer_id_en;
				      break;
				   case -9:
					  $url="index.php?customer_id=".$customer_id_en;
					  break;
				   case -5:
					  $url="countdown.php?customer_id=".$customer_id_en; 	  
					  break;					  
			   }
			}
		  			
		  $foreign_id=$type_id_1_2;
			 
		  $lun_urls=$lun_urls.$url."|*|";
		  $lun_linktypes=$lun_linktypes.$linktype."|*|";
		  $lun_foreign_ids=$lun_foreign_ids.$foreign_id."|*|";
		//默认为链接到类型。以后可以链接到其他的功能模块
		
		  $lun_imgurls = $lun_imgurls.$destination2."|*|";
		  $lun_detail_ids=$lun_detail_ids.$product_detail_id_1_2."|*|";
		  
	  }else{
			$file = $_FILES["upfile1_2"];
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
			   mkdir($destination_folder);

			  $filename=$file["tmp_name"];

			  $image_size = getimagesize($filename);

			  $pinfo=pathinfo($file["name"]);

			  $ftype=$pinfo["extension"];
			  $destination2 = $destination_folder.time()."2.".$ftype;
			  if (file_exists($destination2) && $overwrite != true)
			  {
				 echo "<font color='red'>同名文件已经存在了！</font>";
				 exit;
			   }
			  if(!move_uploaded_file ($filename, $destination2))
			  {
				 echo "<font color='red'>移动文件出错！</font>";
				 exit;
			  }
			 // $pinfo=pathinfo($destination1);
			  //$fname=$pinfo["basename"];
		//   echo " <font color=red>成功上传,鼠标移动到地址栏自动复制</font><br><table width=\"348\" cellspacing=\"0\" cellpadding=\"5\" border=\"0\" class=\"table_decoration\" align=\"center\"><tr><td><input type=\"checkbox\" id=\"fmt\" onclick=\"select_format()\"/>图片UBB代码<br/><div id=\"site\"><table border=\"0\"><tr><td valign=\"top\">文件地址:</td><td><input type=\"text\" onclick=\"sendtof(this.value)\" onmouseover=\"oCopy(this)\" style=font-size=9pt;color:blue size=\"44\" value=\"http://".$_SERVER['SERVER_NAME'].$path_parts["dirname"]."/".$destination_folder.$fname."\"/>
		  //  </td></tr></table></div><div id=\"sited\" style=\"display:none\"><table border=\"0\"><tr><td valign=\"top\">文件地址:</td><td><input type=\"text\" onclick=\"sendtof(this.value)\" onmouseover=\"oCopy(this)\" style=font-size=9pt;color:blue size=\"44\" value=\"[img]http://".$_SERVER['SERVER_NAME'].$path_parts["dirname"]."/".$destination_folder.$fname."[/img]\"/></td></tr></table></div></td></tr></table>";
			  //echo " 宽度:".$image_size[0];
			 // echo " 长度:".$image_size[1];
			 $linktype=1;
			 $type_id_1_2 = $configutil->splash_new($_POST["type_id_1_2"]);
              $url="";
			  $product_detail_id_1_2=-1;
              if($type_id_1_2>0){
			    $typestrarr= explode("_",$type_id_1_2);
				$type_id_1_2 = $typestrarr[0];
				$linktype=$typestrarr[1];
				if($linktype==1){
					$product_detail_id_1_2 = $configutil->splash_new($_POST["product_detail_id_1_2"]);        
					 if($product_detail_id_1_2>0){
						
						 $url="detail.php?customer_id=".$customer_id_en."&pid=".$product_detail_id_1_2;
					 
					 }else{					
						$query3="select name from weixin_commonshop_types where isvalid=true and id=".$type_id_1_2;
						$result3 = mysql_query($query3) or die('Query failed: ' . mysql_error());
						$typename="";
						while ($row3 = mysql_fetch_object($result3)) {
						   $typename = $row3->name;
						}
						$url="list.php?customer_id=".$customer_id_en."&tid=".$type_id_1_2."&tname=".$typename;
					}
				}else if($linktype==2){
				   //图文
					$query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$type_id_1_2;
					$result = mysql_query($query) or die('Query failed: ' . mysql_error());
					$website_url="";
					while ($row = mysql_fetch_object($result)) {
					   $website_url = $row->website_url;
					}
					$pos = strpos($website_url,"?"); 
					if($pos>0){
					   $website_url = $website_url."&C_id=".$customer_id_en;
					}else{
					   $website_url = $website_url."?C_id=".$customer_id_en;
					}
					$url = $website_url;
				}
			 }else{
			   switch($type_id_1_2){
			        case -6:
				      $url="list.php?customer_id=".$customer_id_en;
				      break;
				   case -2:
				      $url="list.php?isnew=1&customer_id=".$customer_id_en;
				      break;
				   case -3:
				      $url="list.php?ishot=1&customer_id=".$customer_id_en;
				      break;
				   case -4:
				      $url="order_cart.php?customer_id=".$customer_id_en;
				      break;
				   case -7:
					  $url="class_page.php?customer_id=".$customer_id_en;
					  break;
				   case -8:
					  $url="order_list.php?customer_id=".$customer_id_en;
				      break;
				   case -9:
					  $url="index.php?customer_id=".$customer_id_en;
					  break;
				   case -5:
					  $url="countdown.php?customer_id=".$customer_id_en; 	  
					  break;					  
			   }
			}
			 			
			 $foreign_id=$type_id_1_2;
			 $lun_urls=$lun_urls.$url."|*|";
		
			 $lun_linktypes=$lun_linktypes.$linktype."|*|";
			 $lun_foreign_ids=$lun_foreign_ids.$foreign_id."|*|";
			//默认为链接到类型。以后可以链接到其他的功能模块
			 $lun_imgurls = $lun_imgurls.$new_baseurl.$destination2."|*|";
			 $lun_detail_ids=$lun_detail_ids.$product_detail_id_1_1."|*|";
			 
	  }
	  if (!is_uploaded_file($_FILES["upfile1_3"]["tmp_name"]))
	  {
	      $destination3 = $configutil->splash_new($_POST["imgids_1_3"]);
		  $type_id_1_3 = $configutil->splash_new($_POST["type_id_1_3"]);
		  
          $product_detail_id_1_3=-1;
		  $url="";
		  $linktype=1;
		  if($type_id_1_3>0){
			$typestrarr= explode("_",$type_id_1_3);
			$type_id_1_3 = $typestrarr[0];
			$linktype=$typestrarr[1];
			

			if($linktype==1){
				 $product_detail_id_1_3 = $configutil->splash_new($_POST["product_detail_id_1_3"]);        
				 if($product_detail_id_1_3>0){
					
					 $url="detail.php?customer_id=".$customer_id_en."&pid=".$product_detail_id_1_3;
				 
				 }else{					
					$query3="select name from weixin_commonshop_types where isvalid=true and id=".$type_id_1_3;
					$result3 = mysql_query($query3) or die('Query failed: ' . mysql_error());
					$typename="";
					while ($row3 = mysql_fetch_object($result3)) {
					   $typename = $row3->name;
					}
					$url="list.php?customer_id=".$customer_id_en."&tid=".$type_id_1_3."&tname=".$typename;
				}
			}else if($linktype==2){
			   //图文
				$query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$type_id_1_3;
				$result = mysql_query($query) or die('Query failed: ' . mysql_error());
				$website_url="";
				while ($row = mysql_fetch_object($result)) {
				   $website_url = $row->website_url;
				}
				$pos = strpos($website_url,"?"); 
				if($pos>0){
				   $website_url = $website_url."&C_id=".$customer_id_en;
				}else{
				   $website_url = $website_url."?C_id=".$customer_id_en;
				}
				$url = $website_url;
			}
		  }else{
			   switch($type_id_1_3){
			        case -6:
				      $url="list.php?customer_id=".$customer_id_en;
				      break;
				   case -2:
				      $url="list.php?isnew=1&customer_id=".$customer_id_en;
				      break;
				   case -3:
				      $url="list.php?ishot=1&customer_id=".$customer_id_en;
				      break;
				   case -4:
				      $url="order_cart.php?customer_id=".$customer_id_en;
				      break;
				   case -7:
					  $url="class_page.php?customer_id=".$customer_id_en;
					  break;
				   case -8:
					  $url="order_list.php?customer_id=".$customer_id_en;
				      break;
				   case -9:
					  $url="index.php?customer_id=".$customer_id_en;
					  break;
				   case -5:
					  $url="countdown.php?customer_id=".$customer_id_en; 	  
					  break;					  
			   }
			}
		  			
		  $foreign_id=$type_id_1_3;
			 
		  $lun_urls=$lun_urls.$url."|*|";
		  $lun_linktypes=$lun_linktypes.$linktype."|*|";
		  $lun_foreign_ids=$lun_foreign_ids.$foreign_id."|*|";
		//默认为链接到类型。以后可以链接到其他的功能模块
		
		  $lun_imgurls = $lun_imgurls.$destination3."|*|";
		  $lun_detail_ids=$lun_detail_ids.$product_detail_id_1_3."|*|";
		   
  	  }else{
			$file = $_FILES["upfile1_3"];
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
			   mkdir($destination_folder);

			  $filename=$file["tmp_name"];

			  $image_size = getimagesize($filename);

			  $pinfo=pathinfo($file["name"]);

			  $ftype=$pinfo["extension"];
			  $destination3 = $destination_folder.time()."3.".$ftype;
			  if (file_exists($destination3) && $overwrite != true)
			  {
				 echo "<font color='red'>同名文件已经存在了！</font>";
				 exit;
			   }
			  if(!move_uploaded_file ($filename, $destination3))
			  {
				 echo "<font color='red'>移动文件出错！</font>";
				 exit;
			  }
			 // $pinfo=pathinfo($destination1);
			  //$fname=$pinfo["basename"];
		//   echo " <font color=red>成功上传,鼠标移动到地址栏自动复制</font><br><table width=\"348\" cellspacing=\"0\" cellpadding=\"5\" border=\"0\" class=\"table_decoration\" align=\"center\"><tr><td><input type=\"checkbox\" id=\"fmt\" onclick=\"select_format()\"/>图片UBB代码<br/><div id=\"site\"><table border=\"0\"><tr><td valign=\"top\">文件地址:</td><td><input type=\"text\" onclick=\"sendtof(this.value)\" onmouseover=\"oCopy(this)\" style=font-size=9pt;color:blue size=\"44\" value=\"http://".$_SERVER['SERVER_NAME'].$path_parts["dirname"]."/".$destination_folder.$fname."\"/>
		  //  </td></tr></table></div><div id=\"sited\" style=\"display:none\"><table border=\"0\"><tr><td valign=\"top\">文件地址:</td><td><input type=\"text\" onclick=\"sendtof(this.value)\" onmouseover=\"oCopy(this)\" style=font-size=9pt;color:blue size=\"44\" value=\"[img]http://".$_SERVER['SERVER_NAME'].$path_parts["dirname"]."/".$destination_folder.$fname."[/img]\"/></td></tr></table></div></td></tr></table>";
			  //echo " 宽度:".$image_size[0];
			 // echo " 长度:".$image_size[1];
			  $type_id_1_3 = $configutil->splash_new($_POST["type_id_1_3"]);
			  $product_detail_id_1_3=-1;
              $url="";
			  $linktype=1;	
              if($type_id_1_3>0){
			    $typestrarr= explode("_",$type_id_1_3);
				$type_id_1_3 = $typestrarr[0];
				$linktype=$typestrarr[1];
				if($linktype==1){
					$product_detail_id_1_3 = $configutil->splash_new($_POST["product_detail_id_1_3"]);        
					 if($product_detail_id_1_3>0){
						
						 $url="detail.php?customer_id=".$customer_id_en."&pid=".$product_detail_id_1_3;
					 
					 }else{					
						$query3="select name from weixin_commonshop_types where isvalid=true and id=".$type_id_1_3;
						$result3 = mysql_query($query3) or die('Query failed: ' . mysql_error());
						$typename="";
						while ($row3 = mysql_fetch_object($result3)) {
						   $typename = $row3->name;
						}
						$url="list.php?customer_id=".$customer_id_en."&tid=".$type_id_1_3."&tname=".$typename;
					}
				}else if($linktype==2){
				   //图文
					$query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$type_id_1_3;
					$result = mysql_query($query) or die('Query failed: ' . mysql_error());
					$website_url="";
					while ($row = mysql_fetch_object($result)) {
					   $website_url = $row->website_url;
					}
					$pos = strpos($website_url,"?"); 
					if($pos>0){
					   $website_url = $website_url."&C_id=".$customer_id_en;
					}else{
					   $website_url = $website_url."?C_id=".$customer_id_en;
					}
					$url = $website_url;
				}
			 }else{
			   switch($type_id_1_3){
			        case -6:
				      $url="list.php?customer_id=".$customer_id_en;
				      break;
				   case -2:
				      $url="list.php?isnew=1&customer_id=".$customer_id_en;
				      break;
				   case -3:
				      $url="list.php?ishot=1&customer_id=".$customer_id_en;
				      break;
				   case -4:
				      $url="order_cart.php?customer_id=".$customer_id_en;
				      break;
				   case -7:
					  $url="class_page.php?customer_id=".$customer_id_en;
					  break;
				   case -8:
					  $url="order_list.php?customer_id=".$customer_id_en;
				      break;
				   case -9:
					  $url="index.php?customer_id=".$customer_id_en;
					  break;
				   case -5:
					  $url="countdown.php?customer_id=".$customer_id_en; 	  
					  break;					  
			   }
			}
			 		
			 $foreign_id=$type_id_1_3;
			 
			 $lun_urls=$lun_urls.$url."|*|";
			 $lun_linktypes=$lun_linktypes.$linktype."|*|";
			 $lun_foreign_ids=$lun_foreign_ids.$foreign_id."|*|";
			 
			 $lun_imgurls = $lun_imgurls.$new_baseurl.$destination3."|*|";
			 $lun_detail_ids=$lun_detail_ids.$product_detail_id_1_3."|*|";
	  }
	  if (!is_uploaded_file($_FILES["upfile1_4"]["tmp_name"]))
	  {
	      $destination4 = $configutil->splash_new($_POST["imgids_1_4"]);
		  $type_id_1_4 = $configutil->splash_new($_POST["type_id_1_4"]);
		  $product_detail_id_1_4=-1;
		  $url="";
		  $linktype=1;
		  if($type_id_1_4>0){
			$typestrarr= explode("_",$type_id_1_4);
			$type_id_1_4 = $typestrarr[0];
			$linktype=$typestrarr[1];
			if($linktype==1){
				$product_detail_id_1_4 = $configutil->splash_new($_POST["product_detail_id_1_4"]);        
				 if($product_detail_id_1_4>0){
					
					 $url="detail.php?customer_id=".$customer_id_en."&pid=".$product_detail_id_1_4;
				 
				 }else{					
					$query3="select name from weixin_commonshop_types where isvalid=true and id=".$type_id_1_4;
					$result3 = mysql_query($query3) or die('Query failed: ' . mysql_error());
					$typename="";
					while ($row3 = mysql_fetch_object($result3)) {
					   $typename = $row3->name;
					}
					$url="list.php?customer_id=".$customer_id_en."&tid=".$type_id_1_4."&tname=".$typename;
				}
			}else if($linktype==2){
			   //图文
				$query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$type_id_1_4;
				$result = mysql_query($query) or die('Query failed: ' . mysql_error());
				$website_url="";
				while ($row = mysql_fetch_object($result)) {
				   $website_url = $row->website_url;
				}
				$pos = strpos($website_url,"?"); 
				if($pos>0){
				   $website_url = $website_url."&C_id=".$customer_id_en;
				}else{
				   $website_url = $website_url."?C_id=".$customer_id_en;
				}
				$url = $website_url;
			}
		  }else{
			   switch($type_id_1_4){
			        case -6:
				      $url="list.php?customer_id=".$customer_id_en;
				      break;
				   case -2:
				      $url="list.php?isnew=1&customer_id=".$customer_id_en;
				      break;
				   case -3:
				      $url="list.php?ishot=1&customer_id=".$customer_id_en;
				      break;
				   case -4:
				      $url="order_cart.php?customer_id=".$customer_id_en;
				      break;
				   case -7:
					  $url="class_page.php?customer_id=".$customer_id_en;
					  break;
				   case -8:
					  $url="order_list.php?customer_id=".$customer_id_en;
				      break;
				   case -9:
					  $url="index.php?customer_id=".$customer_id_en;
					  break;
				   case -5:
					  $url="countdown.php?customer_id=".$customer_id_en; 	  
					  break;					  
			   }
			}
		  			
		  $foreign_id=$type_id_1_4;
			 
		  $lun_urls=$lun_urls.$url."|*|";
		  $lun_linktypes=$lun_linktypes.$linktype."|*|";
		  $lun_foreign_ids=$lun_foreign_ids.$foreign_id."|*|";
		//默认为链接到类型。以后可以链接到其他的功能模块
		
		  $lun_imgurls = $lun_imgurls.$destination4."|*|";
		  $lun_detail_ids=$lun_detail_ids.$product_detail_id_1_4."|*|";
		   
	  }else{
			$file = $_FILES["upfile1_4"];
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
			   mkdir($destination_folder);

			  $filename=$file["tmp_name"];

			  $image_size = getimagesize($filename);

			  $pinfo=pathinfo($file["name"]);

			  $ftype=$pinfo["extension"];
			  $destination4 = $destination_folder.time()."4.".$ftype;
			  if (file_exists($destination4) && $overwrite != true)
			  {
				 echo "<font color='red'>同名文件已经存在了！</font>";
				 exit;
			   }
			  if(!move_uploaded_file ($filename, $destination4))
			  {
				 echo "<font color='red'>移动文件出错！</font>";
				 exit;
			  }
			 // $pinfo=pathinfo($destination1);
			  //$fname=$pinfo["basename"];
		//   echo " <font color=red>成功上传,鼠标移动到地址栏自动复制</font><br><table width=\"348\" cellspacing=\"0\" cellpadding=\"5\" border=\"0\" class=\"table_decoration\" align=\"center\"><tr><td><input type=\"checkbox\" id=\"fmt\" onclick=\"select_format()\"/>图片UBB代码<br/><div id=\"site\"><table border=\"0\"><tr><td valign=\"top\">文件地址:</td><td><input type=\"text\" onclick=\"sendtof(this.value)\" onmouseover=\"oCopy(this)\" style=font-size=9pt;color:blue size=\"44\" value=\"http://".$_SERVER['SERVER_NAME'].$path_parts["dirname"]."/".$destination_folder.$fname."\"/>
		  //  </td></tr></table></div><div id=\"sited\" style=\"display:none\"><table border=\"0\"><tr><td valign=\"top\">文件地址:</td><td><input type=\"text\" onclick=\"sendtof(this.value)\" onmouseover=\"oCopy(this)\" style=font-size=9pt;color:blue size=\"44\" value=\"[img]http://".$_SERVER['SERVER_NAME'].$path_parts["dirname"]."/".$destination_folder.$fname."[/img]\"/></td></tr></table></div></td></tr></table>";
			  //echo " 宽度:".$image_size[0];
			 // echo " 长度:".$image_size[1];
			 
			 $type_id_1_4 = $configutil->splash_new($_POST["type_id_1_4"]);
			 $product_detail_id_1_4=-1;
              $url="";
			  $linktype=1;
              if($type_id_1_4>0){
			    $typestrarr= explode("_",$type_id_1_4);
				$type_id_1_4 = $typestrarr[0];
				$linktype=$typestrarr[1];
				if($linktype==1){
					 $product_detail_id_1_4 = $configutil->splash_new($_POST["product_detail_id_1_4"]);        
					 if($product_detail_id_1_4>0){
						
						 $url="detail.php?customer_id=".$customer_id_en."&pid=".$product_detail_id_1_4;
					 
					 }else{					
						$query3="select name from weixin_commonshop_types where isvalid=true and id=".$type_id_1_4;
						$result3 = mysql_query($query3) or die('Query failed: ' . mysql_error());
						$typename="";
						while ($row3 = mysql_fetch_object($result3)) {
						   $typename = $row3->name;
						}
						$url="list.php?customer_id=".$customer_id_en."&tid=".$type_id_1_4."&tname=".$typename;
					}
				}else if($linktype==2){
				   //图文
					$query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$type_id_1_4;
					$result = mysql_query($query) or die('Query failed: ' . mysql_error());
					$website_url="";
					while ($row = mysql_fetch_object($result)) {
					   $website_url = $row->website_url;
					}
					$pos = strpos($website_url,"?"); 
					if($pos>0){
					   $website_url = $website_url."&C_id=".$customer_id_en;
					}else{
					   $website_url = $website_url."?C_id=".$customer_id_en;
					}
					$url = $website_url;
				}
			 }else{
			   switch($type_id_1_4){
			        case -6:
				      $url="list.php?customer_id=".$customer_id_en;
				      break;
				   case -2:
				      $url="list.php?isnew=1&customer_id=".$customer_id_en;
				      break;
				   case -3:
				      $url="list.php?ishot=1&customer_id=".$customer_id_en;
				      break;
				   case -4:
				      $url="order_cart.php?customer_id=".$customer_id_en;
				      break;
				   case -7:
					  $url="class_page.php?customer_id=".$customer_id_en;
					  break;
				   case -8:
					  $url="order_list.php?customer_id=".$customer_id_en;
				      break;
				   case -9:
					  $url="index.php?customer_id=".$customer_id_en;
					  break;
				  case -5:
					  $url="countdown.php?customer_id=".$customer_id_en; 	  
					  break;					  
			   }
			}
			 			
			 $foreign_id=$type_id_1_4;
			 
			 $lun_urls=$lun_urls.$url."|*|";
			 $lun_linktypes=$lun_linktypes.$linktype."|*|";
			 $lun_foreign_ids=$lun_foreign_ids.$foreign_id."|*|";
			 
			 $lun_imgurls = $lun_imgurls.$new_baseurl.$destination4."|*|";
			 $lun_detail_ids=$lun_detail_ids.$product_detail_id_1_4."|*|";
	  }
	  
	  if (!is_uploaded_file($_FILES["upfile1_5"]["tmp_name"]))
	  {
	      $destination5 = $configutil->splash_new($_POST["imgids_1_5"]);
		  $type_id_1_5 = $configutil->splash_new($_POST["type_id_1_5"]);
		  $product_detail_id_1_5=-1;
		  $url="";
		  $linktype=1;
		  if($type_id_1_5>0){
			$typestrarr= explode("_",$type_id_1_5);
			$type_id_1_5 = $typestrarr[0];
			$linktype=$typestrarr[1];
			if($linktype==1){
				 $product_detail_id_1_5 = $configutil->splash_new($_POST["product_detail_id_1_5"]);        
				 if($product_detail_id_1_5>0){
					
					 $url="detail.php?customer_id=".$customer_id_en."&pid=".$product_detail_id_1_5;
				 
				 }else{					
					$query3="select name from weixin_commonshop_types where isvalid=true and id=".$type_id_1_5;
					$result3 = mysql_query($query3) or die('Query failed: ' . mysql_error());
					$typename="";
					while ($row3 = mysql_fetch_object($result3)) {
					   $typename = $row3->name;
					}
					$url="list.php?customer_id=".$customer_id_en."&tid=".$type_id_1_5."&tname=".$typename;
				 }
			}else if($linktype==2){
			   //图文
				$query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$type_id_1_5;
				$result = mysql_query($query) or die('Query failed: ' . mysql_error());
				$website_url="";
				while ($row = mysql_fetch_object($result)) {
				   $website_url = $row->website_url;
				}
				$pos = strpos($website_url,"?"); 
				if($pos>0){
				   $website_url = $website_url."&C_id=".$customer_id_en;
				}else{
				   $website_url = $website_url."?C_id=".$customer_id_en;
				}
				$url = $website_url;
			}
		  }else{
			   switch($type_id_1_5){
			        case -6:
				      $url="list.php?customer_id=".$customer_id_en;
				      break;
				   case -2:
				      $url="list.php?isnew=1&customer_id=".$customer_id_en;
				      break;
				   case -3:
				      $url="list.php?ishot=1&customer_id=".$customer_id_en;
				      break;
				   case -4:
				      $url="order_cart.php?customer_id=".$customer_id_en;
				      break;
				   case -7:
					  $url="class_page.php?customer_id=".$customer_id_en;
					  break;
				   case -8:
					  $url="order_list.php?customer_id=".$customer_id_en;
				      break;
				   case -9:
					  $url="index.php?customer_id=".$customer_id_en;
					  break;
				   case -5:
					  $url="countdown.php?customer_id=".$customer_id_en; 	  
					  break;					  
			   }
			}
		  			
		  $foreign_id=$type_id_1_5;
			 
		  $lun_urls=$lun_urls.$url."|*|";
		  $lun_linktypes=$lun_linktypes.$linktype."|*|";
		  $lun_foreign_ids=$lun_foreign_ids.$foreign_id."|*|";
		//默认为链接到类型。以后可以链接到其他的功能模块
		
		  $lun_imgurls = $lun_imgurls.$destination5."|*|";
		  $lun_detail_ids=$lun_detail_ids.$product_detail_id_1_5."|*|";
		   
	  }else{
			$file = $_FILES["upfile1_5"];
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
			   mkdir($destination_folder);

			  $filename=$file["tmp_name"];

			  $image_size = getimagesize($filename);

			  $pinfo=pathinfo($file["name"]);

			  $ftype=$pinfo["extension"];
			  $destination5 = $destination_folder.time()."5.".$ftype;
			  if (file_exists($destination5) && $overwrite != true)
			  {
				 echo "<font color='red'>同名文件已经存在了！</font>";
				 exit;
			   }
			  if(!move_uploaded_file ($filename, $destination5))
			  {
				 echo "<font color='red'>移动文件出错！</font>";
				 exit;
			  }
			 // $pinfo=pathinfo($destination1);
			  //$fname=$pinfo["basename"];
		//   echo " <font color=red>成功上传,鼠标移动到地址栏自动复制</font><br><table width=\"348\" cellspacing=\"0\" cellpadding=\"5\" border=\"0\" class=\"table_decoration\" align=\"center\"><tr><td><input type=\"checkbox\" id=\"fmt\" onclick=\"select_format()\"/>图片UBB代码<br/><div id=\"site\"><table border=\"0\"><tr><td valign=\"top\">文件地址:</td><td><input type=\"text\" onclick=\"sendtof(this.value)\" onmouseover=\"oCopy(this)\" style=font-size=9pt;color:blue size=\"44\" value=\"http://".$_SERVER['SERVER_NAME'].$path_parts["dirname"]."/".$destination_folder.$fname."\"/>
		  //  </td></tr></table></div><div id=\"sited\" style=\"display:none\"><table border=\"0\"><tr><td valign=\"top\">文件地址:</td><td><input type=\"text\" onclick=\"sendtof(this.value)\" onmouseover=\"oCopy(this)\" style=font-size=9pt;color:blue size=\"44\" value=\"[img]http://".$_SERVER['SERVER_NAME'].$path_parts["dirname"]."/".$destination_folder.$fname."[/img]\"/></td></tr></table></div></td></tr></table>";
			  //echo " 宽度:".$image_size[0];
			 // echo " 长度:".$image_size[1];
			 
			 $type_id_1_5 = $configutil->splash_new($_POST["type_id_1_5"]);
			 $product_detail_id_1_5=-1;
              $url="";
			  $linktype=1;
              if($type_id_1_5>0){
			    $typestrarr= explode("_",$type_id_1_5);
				$type_id_1_5 = $typestrarr[0];
				$linktype=$typestrarr[1];
				if($linktype==1){
					$product_detail_id_1_5 = $configutil->splash_new($_POST["product_detail_id_1_5"]);        
					 if($product_detail_id_1_5>0){
						
						 $url="detail.php?customer_id=".$customer_id_en."&pid=".$product_detail_id_1_5;
					 
					 }else{					
						$query3="select name from weixin_commonshop_types where isvalid=true and id=".$type_id_1_5;
						$result3 = mysql_query($query3) or die('Query failed: ' . mysql_error());
						$typename="";
						while ($row3 = mysql_fetch_object($result3)) {
						   $typename = $row3->name;
						}
						$url="list.php?customer_id=".$customer_id_en."&tid=".$type_id_1_5."&tname=".$typename;
					 }
				}else if($linktype==2){
				   //图文
					$query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$type_id_1_5;
					$result = mysql_query($query) or die('Query failed: ' . mysql_error());
					$website_url="";
					while ($row = mysql_fetch_object($result)) {
					   $website_url = $row->website_url;
					}
					$pos = strpos($website_url,"?"); 
					if($pos>0){
					   $website_url = $website_url."&C_id=".$customer_id_en;
					}else{
					   $website_url = $website_url."?C_id=".$customer_id_en;
					}
					$url = $website_url;
				}
			 }else{
			   switch($type_id_1_5){
			        case -6:
				      $url="list.php?customer_id=".$customer_id_en;
				      break;
				   case -2:
				      $url="list.php?isnew=1&customer_id=".$customer_id_en;
				      break;
				   case -3:
				      $url="list.php?ishot=1&customer_id=".$customer_id_en;
				      break;
				   case -4:
				      $url="order_cart.php?customer_id=".$customer_id_en;
				      break;
				   case -7:
					  $url="class_page.php?customer_id=".$customer_id_en;
					  break;
				   case -8:
					  $url="order_list.php?customer_id=".$customer_id_en;
				      break;
				   case -9:
					  $url="index.php?customer_id=".$customer_id_en;
					  break;
				   case -5:
					  $url="countdown.php?customer_id=".$customer_id_en; 	  
					  break;					  
			   }
			}
			 			
			 $foreign_id=$type_id_1_5;
			 
			 $lun_urls=$lun_urls.$url."|*|";
			 $lun_linktypes=$lun_linktypes.$linktype."|*|";
			 $lun_foreign_ids=$lun_foreign_ids.$foreign_id."|*|";
			 
			 $lun_imgurls = $lun_imgurls.$new_baseurl.$destination5."|*|";
			 $lun_detail_ids=$lun_detail_ids.$product_detail_id_1_5."|*|";
	  }
	  if (!empty($_POST["type_id_1_6"]))
	  {
		  $type_id_1_6 = $configutil->splash_new($_POST["type_id_1_6"]);
		  $product_detail_id_1_6=-1;
		  $url="";
		  $linktype=1;
		  if($type_id_1_6>0){
			$typestrarr= explode("_",$type_id_1_6);
			$type_id_1_6 = $typestrarr[0];
			$linktype=$typestrarr[1];
			
			if($linktype==1){
				$product_detail_id_1_6 = $configutil->splash_new($_POST["product_detail_id_1_6"]);        
				 if($product_detail_id_1_6>0){
					
					 $url="detail.php?customer_id=".$customer_id_en."&pid=".$product_detail_id_1_6;
				 
				 }else{					
					$query3="select name from weixin_commonshop_types where isvalid=true and id=".$type_id_1_6;
					$result3 = mysql_query($query3) or die('Query failed: ' . mysql_error());
					$typename="";
					while ($row3 = mysql_fetch_object($result3)) {
					   $typename = $row3->name;
					}
					$url="list.php?customer_id=".$customer_id_en."&tid=".$type_id_1_6."&tname=".$typename;
				 }
			}else if($linktype==2){
			   //图文
				$query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$type_id_1_6;
				$result = mysql_query($query) or die('Query failed: ' . mysql_error());
				$website_url="";
				while ($row = mysql_fetch_object($result)) {
				   $website_url = $row->website_url;
				}
				$pos = strpos($website_url,"?"); 
				if($pos>0){
				   $website_url = $website_url."&C_id=".$customer_id_en;
				}else{
				   $website_url = $website_url."?C_id=".$customer_id_en;
				}
				$url = $website_url;
			}
		  }else{
			   switch($type_id_1_6){
			        case -6:
				      $url="list.php?customer_id=".$customer_id_en;
				      break;
				   case -2:
				      $url="list.php?isnew=1&customer_id=".$customer_id_en;
				      break;
				   case -3:
				      $url="list.php?ishot=1&customer_id=".$customer_id_en;
				      break;
				   case -4:
				      $url="order_cart.php?customer_id=".$customer_id_en;
				      break;
				   case -7:
					  $url="class_page.php?customer_id=".$customer_id_en;
					  break;
				   case -8:
					  $url="order_list.php?customer_id=".$customer_id_en;
				      break;
				   case -9:
					  $url="index.php?customer_id=".$customer_id_en;
					   break;
				   case -5:
					  $url="countdown.php?customer_id=".$customer_id_en; 	  
					  break;					  
			   }
			}
		  			
		  $foreign_id=$type_id_1_6;
			 
		  $lun_urls=$lun_urls.$url."|*|";
		  $lun_linktypes=$lun_linktypes.$linktype."|*|";
		  $lun_foreign_ids=$lun_foreign_ids.$foreign_id."|*|";
		  
		  $lun_detail_ids=$lun_detail_ids.$product_detail_id_1_6."|*|";
		//默认为链接到类型。以后可以链接到其他的功能模块
	  }else{
	         $lun_urls=$lun_urls."|*|";
			 $lun_linktypes=$lun_linktypes."|*|";
			 $lun_foreign_ids=$lun_foreign_ids."|*|";
			 $lun_detail_ids=$lun_detail_ids."|*|";
	  }
	  
	  
	  if (!empty($_POST["type_id_1_7"]))
	  {
	      $type_id_1_7 = $configutil->splash_new($_POST["type_id_1_7"]);
		  
          $product_detail_id_1_7=-1;
		  
		  $url="";
		  $linktype=1;
		  if($type_id_1_7>0){
			$typestrarr= explode("_",$type_id_1_7);
			$type_id_1_7 = $typestrarr[0];
			$linktype=$typestrarr[1];
			
			if($linktype==1){
				$product_detail_id_1_7 = $configutil->splash_new($_POST["product_detail_id_1_7"]);        
				 if($product_detail_id_1_7>0){
					
					 $url="detail.php?customer_id=".$customer_id_en."&pid=".$product_detail_id_1_7;
				 
				 }else{					
					$query3="select name from weixin_commonshop_types where isvalid=true and id=".$type_id_1_7;
					$result3 = mysql_query($query3) or die('Query failed: ' . mysql_error());
					$typename="";
					while ($row3 = mysql_fetch_object($result3)) {
					   $typename = $row3->name;
					}
					$url="list.php?customer_id=".$customer_id_en."&tid=".$type_id_1_7."&tname=".$typename;
				 }
			}else if($linktype==2){
			   //图文
				$query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$type_id_1_7;
				$result = mysql_query($query) or die('Query failed: ' . mysql_error());
				$website_url="";
				while ($row = mysql_fetch_object($result)) {
				   $website_url = $row->website_url;
				}
				$pos = strpos($website_url,"?"); 
				if($pos>0){
				   $website_url = $website_url."&C_id=".$customer_id_en;
				}else{
				   $website_url = $website_url."?C_id=".$customer_id_en;
				}
				$url = $website_url;
			}
		  }else{
			   switch($type_id_1_7){
			        case -6:
				      $url="list.php?customer_id=".$customer_id_en;
				      break;
				   case -2:
				      $url="list.php?isnew=1&customer_id=".$customer_id_en;
				      break;
				   case -3:
				      $url="list.php?ishot=1&customer_id=".$customer_id_en;
				      break;
				   case -4:
				      $url="order_cart.php?customer_id=".$customer_id_en;
				      break;
				   case -7:
					  $url="class_page.php?customer_id=".$customer_id_en;
					  break;
				   case -8:
					  $url="order_list.php?customer_id=".$customer_id_en;
				      break;
				   case -9:
					  $url="index.php?customer_id=".$customer_id_en;
					   break;
				   case -5:
					  $url="countdown.php?customer_id=".$customer_id_en;   
					  break;					  
			   }
			}
		  			
		  $foreign_id=$type_id_1_7;
			 
		  $lun_urls=$lun_urls.$url."|*|";
		  $lun_linktypes=$lun_linktypes.$linktype."|*|";
		  $lun_foreign_ids=$lun_foreign_ids.$foreign_id."|*|";
		//默认为链接到类型。以后可以链接到其他的功能模块
		$lun_detail_ids=$lun_detail_ids.$product_detail_id_1_7."|*|";
	  }else{
	         $lun_urls=$lun_urls."|*|";
			 $lun_linktypes=$lun_linktypes."|*|";
			 $lun_foreign_ids=$lun_foreign_ids."|*|";
			  $lun_detail_ids=$lun_detail_ids."|*|";
	  }
	  
	  
	  
	  if (!empty($_POST["type_id_1_8"]))
	  {
		  $type_id_1_8 = $configutil->splash_new($_POST["type_id_1_8"]);
		  $product_detail_id_1_8=-1;
		  $url="";
		  $linktype=1;
		  if($type_id_1_8>0){
			$typestrarr= explode("_",$type_id_1_8);
			$type_id_1_8 = $typestrarr[0];
			$linktype=$typestrarr[1];
			
			if($linktype==1){
				$product_detail_id_1_8 = $configutil->splash_new($_POST["product_detail_id_1_8"]);        
				 if($product_detail_id_1_8>0){
					
					 $url="detail.php?customer_id=".$customer_id_en."&pid=".$product_detail_id_1_8;
				 
				 }else{					
					$query3="select name from weixin_commonshop_types where isvalid=true and id=".$type_id_1_8;
					$result3 = mysql_query($query3) or die('Query failed: ' . mysql_error());
					$typename="";
					while ($row3 = mysql_fetch_object($result3)) {
					   $typename = $row3->name;
					}
					$url="list.php?customer_id=".$customer_id_en."&tid=".$type_id_1_8."&tname=".$typename;
				 }
			}else if($linktype==2){
			   //图文
				$query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$type_id_1_8;
				$result = mysql_query($query) or die('Query failed: ' . mysql_error());
				$website_url="";
				while ($row = mysql_fetch_object($result)) {
				   $website_url = $row->website_url;
				}
				$pos = strpos($website_url,"?"); 
				if($pos>0){
				   $website_url = $website_url."&C_id=".$customer_id_en;
				}else{
				   $website_url = $website_url."?C_id=".$customer_id_en;
				}
				$url = $website_url;
			}
		  }else{
			   switch($type_id_1_8){
			        case -6:
				      $url="list.php?customer_id=".$customer_id_en;
				      break;
				   case -2:
				      $url="list.php?isnew=1&customer_id=".$customer_id_en;
				      break;
				   case -3:
				      $url="list.php?ishot=1&customer_id=".$customer_id_en;
				      break;
				   case -4:
				      $url="order_cart.php?customer_id=".$customer_id_en;
				      break;
				   case -7:
					  $url="class_page.php?customer_id=".$customer_id_en;
					  break;
				   case -8:
					  $url="order_list.php?customer_id=".$customer_id_en;
				      break;
				   case -9:
					  $url="index.php?customer_id=".$customer_id_en;
					   break;
				   case -5:
					  $url="countdown.php?customer_id=".$customer_id_en; 	  
					  break;					  
			   }
			}
		  			
		  $foreign_id=$type_id_1_8;
			 
		  $lun_urls=$lun_urls.$url."|*|";
		  $lun_linktypes=$lun_linktypes.$linktype."|*|";
		  $lun_foreign_ids=$lun_foreign_ids.$foreign_id."|*|";
		//默认为链接到类型。以后可以链接到其他的功能模块
		 $lun_detail_ids=$lun_detail_ids.$product_detail_id_1_8."|*|";
	  }else{
	         $lun_urls=$lun_urls."|*|";
			 $lun_linktypes=$lun_linktypes."|*|";
			 $lun_foreign_ids=$lun_foreign_ids."|*|";
			  $lun_detail_ids=$lun_detail_ids."|*|";
	  }
	  
	  if (!empty($_POST["type_id_1_9"]))
	  {
		  $type_id_1_9 = $configutil->splash_new($_POST["type_id_1_9"]);
		  
          $product_detail_id_1_9=-1;
		  $url="";
		  $linktype=1;
		  if($type_id_1_9>0){
			$typestrarr= explode("_",$type_id_1_9);
			$type_id_1_9 = $typestrarr[0];
			$linktype=$typestrarr[1];
			
			if($linktype==1){
				 $product_detail_id_1_9 = $configutil->splash_new($_POST["product_detail_id_1_9"]);        
				 if($product_detail_id_1_9>0){
					
					 $url="detail.php?customer_id=".$customer_id_en."&pid=".$product_detail_id_1_9;
				 
				 }else{					
					$query3="select name from weixin_commonshop_types where isvalid=true and id=".$type_id_1_9;
					$result3 = mysql_query($query3) or die('Query failed: ' . mysql_error());
					$typename="";
					while ($row3 = mysql_fetch_object($result3)) {
					   $typename = $row3->name;
					}
					$url="list.php?customer_id=".$customer_id_en."&tid=".$type_id_1_9."&tname=".$typename;
				 }
			}else if($linktype==2){
			   //图文
				$query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$type_id_1_9;
				$result = mysql_query($query) or die('Query failed: ' . mysql_error());
				$website_url="";
				while ($row = mysql_fetch_object($result)) {
				   $website_url = $row->website_url;
				}
				$pos = strpos($website_url,"?"); 
				if($pos>0){
				   $website_url = $website_url."&C_id=".$customer_id_en;
				}else{
				   $website_url = $website_url."?C_id=".$customer_id_en;
				}
				$url = $website_url;
			}
		  }else{
			   switch($type_id_1_9){
			        case -6:
				      $url="list.php?customer_id=".$customer_id_en;
				      break;
				   case -2:
				      $url="list.php?isnew=1&customer_id=".$customer_id_en;
				      break;
				   case -3:
				      $url="list.php?ishot=1&customer_id=".$customer_id_en;
				      break;
				   case -4:
				      $url="order_cart.php?customer_id=".$customer_id_en;
				      break;
				   case -7:
					  $url="class_page.php?customer_id=".$customer_id_en;
					  break;
				   case -8:
					  $url="order_list.php?customer_id=".$customer_id_en;
				      break;
				   case -9:
					  $url="index.php?customer_id=".$customer_id_en;
					   break;
				   case -5:
					  $url="countdown.php?customer_id=".$customer_id_en; 	  
					  break;					  
			   }
			}
		  			
		  $foreign_id=$type_id_1_9;
			 
		  $lun_urls=$lun_urls.$url."|*|";
		  $lun_linktypes=$lun_linktypes.$linktype."|*|";
		  $lun_foreign_ids=$lun_foreign_ids.$foreign_id."|*|";
		//默认为链接到类型。以后可以链接到其他的功能模块
		  $lun_detail_ids=$lun_detail_ids.$product_detail_id_1_9."|*|";
		   
	  }else{
	         $lun_urls=$lun_urls."|*|";
			 $lun_linktypes=$lun_linktypes."|*|";
			 $lun_foreign_ids=$lun_foreign_ids."|*|";
			 $lun_detail_ids=$lun_detail_ids."|*|";
	  }
	  
	  
	  if (!empty($_POST["type_id_1_10"]))
	  {
	      $type_id_1_10 = $configutil->splash_new($_POST["type_id_1_10"]);
		  $product_detail_id_1_10=-1;
		  $url="";
		  $linktype=1;
		  if($type_id_1_10>0){
			$typestrarr= explode("_",$type_id_1_10);
			$type_id_1_10 = $typestrarr[0];
			$linktype=$typestrarr[1];
			
			if($linktype==1){
				$product_detail_id_1_10 = $configutil->splash_new($_POST["product_detail_id_1_10"]);        
				 if($product_detail_id_1_10>0){
					
					 $url="detail.php?customer_id=".$customer_id_en."&pid=".$product_detail_id_1_10;
				 
				 }else{					
					$query3="select name from weixin_commonshop_types where isvalid=true and id=".$type_id_1_10;
					$result3 = mysql_query($query3) or die('Query failed: ' . mysql_error());
					$typename="";
					while ($row3 = mysql_fetch_object($result3)) {
					   $typename = $row3->name;
					}
					$url="list.php?customer_id=".$customer_id_en."&tid=".$type_id_1_10."&tname=".$typename;
				 }
			}else if($linktype==2){
			   //图文
				$query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$type_id_1_10;
				$result = mysql_query($query) or die('Query failed: ' . mysql_error());
				$website_url="";
				while ($row = mysql_fetch_object($result)) {
				   $website_url = $row->website_url;
				}
				$pos = strpos($website_url,"?"); 
				if($pos>0){
				   $website_url = $website_url."&C_id=".$customer_id_en;
				}else{
				   $website_url = $website_url."?C_id=".$customer_id_en;
				}
				$url = $website_url;
			}
		  }else{
			   switch($type_id_1_10){
			        case -6:
				      $url="list.php?customer_id=".$customer_id_en;
				      break;
				   case -2:
				      $url="list.php?isnew=1&customer_id=".$customer_id_en;
				      break;
				   case -3:
				      $url="list.php?ishot=1&customer_id=".$customer_id_en;
				      break;
				   case -4:
				      $url="order_cart.php?customer_id=".$customer_id_en;
				      break;
				   case -7:
					  $url="class_page.php?customer_id=".$customer_id_en;
					  break;
				   case -8:
					  $url="order_list.php?customer_id=".$customer_id_en;
				      break;
				   case -9:
					  $url="index.php?customer_id=".$customer_id_en;
					   break;
				   case -5:
					  $url="countdown.php?customer_id=".$customer_id_en; 	  
					  break;					  
			   }
			}
		  			
		  $foreign_id=$type_id_1_10;
			 
		  $lun_urls=$lun_urls.$url."|*|";
		  $lun_linktypes=$lun_linktypes.$linktype."|*|";
		  $lun_foreign_ids=$lun_foreign_ids.$foreign_id."|*|";
		//默认为链接到类型。以后可以链接到其他的功能模块
		  $lun_detail_ids=$lun_detail_ids.$product_detail_id_1_1."|*|";
	  }else{
	         $lun_urls=$lun_urls."|*|";
			 $lun_linktypes=$lun_linktypes."|*|";
			 $lun_foreign_ids=$lun_foreign_ids."|*|";
			  $lun_detail_ids=$lun_detail_ids."|*|";
	  }
	  
	  
	  if($lun_imgurls!=""){
		  
		  $lun_imgurls = substr($lun_imgurls,0,count($lun_imgurls)-4);
		  $lun_urls = substr($lun_urls,0,count($lun_urls)-4);
		  
		  $lun_foreign_ids = substr($lun_foreign_ids,0,count($lun_foreign_ids)-4);
		  $lun_linktypes = substr($lun_linktypes,0,count($lun_linktypes)-4);
		  //$query="update weixin_commonshop_template_imgs set imgurl='".$lun_imgurls."',url='".$lun_urls."',linktype='".$lun_linktypes."',foreign_id='".$lun_foreign_ids."' where template_id=".$template_id." and position='".$position."'";
		  $query="select id from weixin_commonshop_template_item_imgs where isvalid=true and template_id=".$template_id." and position=".$position." and customer_id=".$customer_id;	
		  $result = mysql_query($query) or die('Query failed: ' . mysql_error());
		  $ti_id=-1;
		  while ($row = mysql_fetch_object($result)) {
		     $ti_id = $row->id;
		  }
		  if($ti_id>0){
			 $query="update weixin_commonshop_template_item_imgs set imgurl='".$lun_imgurls."',url='".$lun_urls."',linktype='".$lun_linktypes."',foreign_id='".$lun_foreign_ids."',detail_id='".$lun_detail_ids."' where id=".$ti_id;
			 
			 mysql_query($query);
		  }else{
			$query="insert into weixin_commonshop_template_item_imgs(template_id,imgurl,position,url,linktype,foreign_id,isvalid,createtime,customer_id,detail_id) values(".$template_id.",'".$lun_imgurls."','".$position."','".$lun_urls."','".$lun_linktypes."','".$lun_foreign_ids."',true,now(),".$customer_id.",'".$lun_detail_ids."')";
			//echo $query;
			mysql_query($query);
		  }
	  }
  }
 
 $sql="update weixin_commonshops set index_bg='".$index_bg."' where isvalid=true and customer_id=".$customer_id;
 mysql_query($sql);
 $error =mysql_error();
 mysql_close($link);
 //echo $error;
 //echo $parent_id;
 
 echo "<script>location.href='defaultset.php?customer_id=".$customer_id_en."';</script>";
 }
?>