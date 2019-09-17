<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
require('../customer_id_decrypt.php');
require('../common/utility.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');


//查询的类别
if(!empty($_GET["op"])){
	$op = $configutil->splash_new($_GET["op"]);
}

// 返回码
$result = Array(
	'code'=>10000,
	'msg'=>'',	
	'data'=>''	
);

switch ($op){
	
	
	case 'getdiyarea':
	
			$diy_area_arr = array();		//会员卡数据数组
						
			//---查询限制数目
			$page = 0;	
			if(!empty($_POST["page"])){
				$page = $configutil->splash_new($_POST["page"]);
			}
			$end = 10;
			//---查询限制数目
			
			//-----查询商家自定义区域 start-----//
			
			$all_areaname = '';
			if(!empty($_POST["all_areaname"])){               
			  $all_areaname = $configutil->splash_new($_POST["all_areaname"]); 
			
				
				$temp_arr = array();
				$Query_diyArea = "SELECT id,areaname from weixin_commonshop_team_area WHERE isvalid = true and customer_id = ".$customer_id." and grade = 3 and all_areaname like '" . $all_areaname. "%' limit ".$page." , ".$end."";
				//echo $Query_diyArea;
				$diy_area_id 	= -1;
				$areaname 		= '';
				$Result_diyArea = mysql_query($Query_diyArea) or die (" w867 : Query_diyArea failed : ".mysql_error());
				while ($row_diyArea = mysql_fetch_object($Result_diyArea)) {	
					$diy_area_id 	= $row_diyArea->id;
					$areaname 		= $row_diyArea->areaname;
					
					 $temp_arr = array(
						'diy_area_id'		=>$diy_area_id,
						'areaname'  		=>$areaname
					 );
					 array_push($diy_area_arr,$temp_arr);				
				}
			
			}	 
			 
				if(count($diy_area_arr)>0){
					$result = Array(
						'code'=>10002,
						'msg'=>'success',	
						'data'=>$diy_area_arr,	
					);
				}else{
					$result = Array(
						'code'=>10004,
						'msg'=>'no_data'	
							
					);
				}
										
			//-----查询商家自定义区域 end-----//
		$out = json_encode($result);
		echo $out;
	break;	
	
	
}



?>