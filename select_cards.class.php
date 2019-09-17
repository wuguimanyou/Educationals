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
	
	
	case 'getcards':
	
			$card_arr = array();		//会员卡数据数组
			$user_id = 0;	
			if(!empty($_POST["user_id"])){
				$user_id = $configutil->splash_new($_POST["user_id"]);
			}			
			//---查询限制数目
			$page = 0;	
			if(!empty($_POST["page"])){
				$page = $configutil->splash_new($_POST["page"]);
			}
			$end = 10;
			//---查询限制数目
			
			//-----查询用户所拥有的会员卡详细 start-----//
			$card_member_id=-1;			
			$query_card = "SELECT distinct(wcm.id),wc.name,wcm.level_id from weixin_card_members as wcm inner join weixin_cards as wc on wcm.card_id=wc.id and wc.customer_id=".$customer_id." and wcm.isvalid=true and wc.isvalid=true and  wcm.user_id=".$user_id." limit ".$page." , ".$end."";	
			//echo $query_card;
			$result = mysql_query($query_card) or die('w786 Query failed: ' . mysql_error());
			while ($row = mysql_fetch_object($result)) {
				$card_member_id=$row->id;
				$cardname = $row->name;
				$level_id = $row->level_id; 
				
				if($card_member_id>0){				
					//-----会员卡剩余金额
					$query2 = "SELECT id,remain_consume from weixin_card_member_consumes where isvalid=true and  card_member_id=".$card_member_id;
					$result2 = mysql_query($query2) or die('w661 Query failed: ' . mysql_error());
					while ($row2 = mysql_fetch_object($result2)) {
						$card_remain = $row2->remain_consume;	
						break;
					}
					$card_remain = round($card_remain, 2);
					//-----会员卡剩余金额
					
					//-----会员卡剩余积分
					$query2 = "SELECT id,remain_score from weixin_card_member_scores where isvalid=true and  card_member_id=".$card_member_id;
					$result2 = mysql_query($query2) or die('w669 Query failed: ' . mysql_error());
					$remain_score=0;
					while ($row2 = mysql_fetch_object($result2)) {
						$remain_score = $row2->remain_score;	//会员卡剩余积分
						break;
					}
					$remain_score = round($remain_score, 2);
					//-----会员卡剩余积分
					
					//----查找会员卡等级折扣
					$query2 = "SELECT discount,title from weixin_card_levels where isvalid=true and  id=".$level_id;
					$result2 = mysql_query($query2) or die('w678 Query failed: ' . mysql_error());
					$level_name = "";
					$discount = 0;
					while ($row2 = mysql_fetch_object($result2)) {
						$discount = $row2->discount;		//会员卡折扣
						$level_name = $row2->title;			//会员卡等级名称
						break;
					}
					if(!empty($level_name)){
					  $cardname = $cardname."(".$level_name.")";
					}
					//----查找会员卡等级折扣
					
					//会员卡数据
					$card_data = array(
						'card_member_id'	=>$card_member_id,		//会员卡ID
						'cardname'			=>$cardname,			//会员卡名称
						'card_remain'		=>$card_remain,			//余额
						'remain_score'		=>$remain_score,		//积分	
						'level_name'		=>$level_name,			//会员卡等级名称
						'discount'			=>$discount				//会员卡折扣					
					);
					
					
					array_push($card_arr,$card_data);				//把会员卡数据重组在一起
	
				
				}
			}

				if(count($card_arr)>0){
					$result = Array(
						'code'=>10002,
						'msg'=>'success',	
						'data'=>$card_arr,	
					);
				}else{
					$result = Array(
						'code'=>10004,
						'msg'=>'no_data'	
							
					);
				}
										
			//-----查询会员卡详细 end-----//
		$out = json_encode($result);
		echo $out;
	break;	
	
	
	case 'check_remain':									//检查会员卡余额是否与支付产品金额不足
					$check_card_id = $_POST['check_card_id'];
					$check_money  = $_POST['check_money'];
					//-----会员卡剩余金额
					$query2 = "SELECT id,remain_consume from weixin_card_member_consumes where isvalid=true and  card_member_id=".$check_card_id;
					$result2 = mysql_query($query2) or die('w661 Query failed: ' . mysql_error());
					while ($row2 = mysql_fetch_object($result2)) {
						$card_remain = $row2->remain_consume;	
						break;
					}
					$card_remain = round($card_remain, 2);
					//-----会员卡剩余金额
					
					//-----会员卡剩余积分
					/*$query2 = "SELECT id,remain_score from weixin_card_member_scores where isvalid=true and  card_member_id=".$check_card_id;
					$result2 = mysql_query($query2) or die('w669 Query failed: ' . mysql_error());
					$remain_score=0;
					while ($row2 = mysql_fetch_object($result2)) {
						$remain_score = $row2->remain_score;	//会员卡剩余积分
						break;
					}
					$remain_score = round($remain_score, 2);*/
					if(abs($check_money)> $card_remain){				//产品金额超出会员卡余额
						
						$result = Array(
							'code'=>10005,
							'msg'=>'over_remain'	
							
						);
						
					}
					$out = json_encode($result);
					echo $out;
	break;
	
}



?>