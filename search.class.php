<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
require('../customer_id_decrypt.php');
require('../common/utility.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../common/own_data.php');
$info = new my_data();//own_data.php my_data类
$op = '';
$type_id=-1;//筛选传过来的分类ID
$tid=-1;//分类页传过来的分类ID
if(!empty($_GET["op"])){
	$op = $configutil->splash_new($_GET["op"]);
}

$start  = 0;
if(!empty($_POST['pageNum'])){
	$start  = $configutil->splash_new($_POST["pageNum"]);
}

	$end  	= $configutil->splash_new($_POST["limit_num"]);

switch ($op){
	case 'search':
	
	
		$query = "select is_cashback from weixin_commonshops where isvalid=true and customer_id=".$customer_id." limit 0,1";
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		$is_cashback = 0;//是否开启消费返现
		//$cashback_perday_old = 0; //每天限制领取返现金额
		$shop_id=-1;
		while ($row = mysql_fetch_object($result)) {
			$is_cashback = $row->is_cashback;//是否开启消费返现
			
		}
	
		$rtn_data = array();		
		$product_data = array();
		$query = "select id,name,default_imgurl,is_supply_id,orgin_price,now_price,isvp,vp_score,cashback,cashback_r,show_sell_count,sell_count,is_free_shipping,need_score,back_currency,is_currency from weixin_commonshop_products where isvalid=true and isout=0 and customer_id=".$customer_id." ";
		
		/*********搜索条件start********/
		//1全站搜索2品牌代理商店内搜索
		if(!empty($_POST['search_from'])){
			$search_from = $configutil->splash_new($_POST['search_from']);
			
		}
		//供应商
		if(!empty($_POST['supply_id'])){
			$supply_id = $configutil->splash_new($_POST['supply_id']);
			
		}
		//品牌供应商分类ID
		if(!empty($_POST['brand_typeid'])){
			$brand_typeid = $configutil->splash_new($_POST['brand_typeid']);
			
		}
		//获取筛选传过来的分类ID
		if(!empty($_POST['type_id'])){ 
			$type_id = $configutil->splash_new($_POST['type_id']);
			$type_son="select id from weixin_commonshop_types where isvalid=true and is_shelves=1 and parent_id=".$type_id." and customer_id=".$customer_id."";
			$result_typeson=mysql_query($type_son) or die ('typeson faild ' .mysql_error());
			while($row=mysql_fetch_object($result_typeson)){
			
				$typeson_id_a[]=$row->id;
			}
			
			if(empty($typeson_id_a)){
				$typeson_id_a=$type_id; 
			}else{
				array_push($typeson_id_a,$type_id);
				$typeson_id_a=implode(',',$typeson_id_a);  //查到一级分类及其子分类
			}
		}
		//分类页分类ID
		if(!empty($_POST['tid']) && $type_id<0){
			$tid = $configutil->splash_new($_POST['tid']);
			$type_son="select id from weixin_commonshop_types where isvalid=true and is_shelves=1 and parent_id=".$tid." and customer_id=".$customer_id."";
			$result_typeson=mysql_query($type_son) or die ('typeson faild ' .mysql_error());
			while($row=mysql_fetch_object($result_typeson)){
			
				$typeson_id[]=$row->id;
			}
			
			if(empty($typeson_id)){
				$typeson_id=$tid; 
			}else{
				array_push($typeson_id,$tid);
				$typeson_id=implode(',',$typeson_id);  //查到一级分类及其子分类
			}			
		}
		if($search_from == 2 && $supply_id>0){ //本地搜索
			$condition .= " and is_supply_id = ".$supply_id."";
		}
		if($search_from == 2 && $brand_typeid>0){ //搜索供应商分类
			$condition .= " and LOCATE(',".$brand_typeid.",', brand_type_ids)>0";
		}
		if($search_from == 2 && $brand_typeid>0){ //搜索分类页产品
			$condition .= " and LOCATE(',".$brand_typeid.",', brand_type_ids)>0";
		}
		if($tid>0 && $type_id<0){ //搜索分类下产品(分类页)
			$condition = $condition." and (";
			$typeson_id_arr = explode(",",$typeson_id);
			$typeson_id_count = count($typeson_id_arr);
			for($j=0;$j<$typeson_id_count;$j++){
				$o_typeid = $typeson_id_arr[$j];
				if($j==0){
					$condition = $condition."( LOCATE(',".$o_typeid.",', type_ids)>0)";
					}else{
					$condition = $condition." or (LOCATE(',".$o_typeid.",', type_ids)>0)";
				}
			}
			$condition = $condition.")";
			unset($typeson_id);
		}
		
		if($type_id>0){ //搜索分类下产品(筛选)
			$condition = $condition." and (";
			$typeson_id_arr_a = explode(",",$typeson_id_a);
			$typeson_id_count = count($typeson_id_arr_a);
			for($j=0;$j<$typeson_id_count;$j++){
				$o_typeid = $typeson_id_arr_a[$j];
				if($j==0){
					$condition = $condition."( LOCATE(',".$o_typeid.",', type_ids)>0)";
					}else{
					$condition = $condition." or (LOCATE(',".$o_typeid.",', type_ids)>0)";
				}
			}
			$condition = $condition.")";
			unset($typeson_id_a);
		}
		
		//新品上市
		if(!empty($_POST['isnew'])){
			$isnew = $configutil->splash_new($_POST['isnew']);
			if($isnew==1){
				$condition .= " and isnew = true "; 
			}
		}
		
		//热卖产品
		if(!empty($_POST['ishot'])){
			$ishot = $configutil->splash_new($_POST['ishot']);
			if($ishot==1){
				$condition .= " and ishot = true "; 
			}
		}
		//VP产品
		if(!empty($_POST['isvp'])){
			$isvp = $configutil->splash_new($_POST['isvp']);
			if($isvp==1){
				$condition .= " and isvp = true "; 
			}
		}
		//积分专区
		if(!empty($_POST['isscore'])){
			$isscore = $configutil->splash_new($_POST['isscore']);
			if($isscore==1){
				$condition .= " and isscore = true "; 
			}
		}
		//最小价格
		if(!empty($_POST['curCostMin'])){
			$curCostMin = $configutil->splash_new($_POST['curCostMin']);
			$condition .= " and now_price >= ".$curCostMin."";
		}
		//最大价格
		if(!empty($_POST['curCostMax'])){
			$curCostMax = $configutil->splash_new($_POST['curCostMax']);
			$condition .= " and now_price <= ".$curCostMax."";
		}
		//最小积分
		if(!empty($_POST['curScoreMin'])){
			$curScoreMin = $configutil->splash_new($_POST['curScoreMin']);
			$condition .= " and need_score >= ".$curScoreMin."";
		}
		//最大积分
		if(!empty($_POST['curScoreMax'])){
			$curScoreMax = $configutil->splash_new($_POST['curScoreMax']);
			$condition .= " and need_score <= ".$curScoreMax."";
		}
		//关键词
		if(!empty($_POST['searchKey'])){
			$searchKey = $configutil->splash_new($_POST['searchKey']);
			$condition .= " and name like '%".$searchKey."%'";
		}
		//猜你喜欢cartlike  morelike
		if(!empty($_POST['like_op'])){
			$like_op = $configutil->splash_new($_POST['like_op']);
			$pid=-1;
			$pidarr=[];

			switch($like_op){
				
				case "cartlike": //购物车猜你喜欢
					$cartlike_query="select pro_id from weixin_commonshop_guess_you_like where isvalid=true and customer_id=".$customer_id." order by asort desc,id desc";
					$cartlike_result=mysql_query($cartlike_query) or die ("cartlike_query faild " .mysql_error());
					while($row=mysql_fetch_object($cartlike_result)){
						$pid=$row->pro_id;
						$pidarr[]=$pid;
					}
				break;
				
				case "morelike":
					
					if(!empty($_POST['like_pid'])){//关联的产品id
						$like_pid = $configutil->splash_new($_POST['like_pid']);
					}
					$prolike_query="select pid from products_relation_t where isvalid=true and parent_pid=".$like_pid." and customer_id=".$customer_id."";
					$prolike_result=mysql_query($prolike_query) or die ("prolike_query faild " .mysql_error());
					while($row=mysql_fetch_object($prolike_result)){
						$pid=$row->pid;
						$pidarr[]=$pid;
					}
				
			}
			$pidstr=implode(",",$pidarr);
			$condition .= " and id in(".$pidstr.")";
			
		}
		
		//大小分类
		
		
		
		$condition .= $condition2;		
		
		/*********搜索条件end********/
		
		/*********排序条件atart********/
		//排序
		if(!empty($_POST['op_sort'])){
			$op_sort = $configutil->splash_new($_POST['op_sort']);
		}		
			switch ($op_sort){
				case 'default_d'://默认降序
					$condition .=  " order by asort_value desc ";
				break;
				case 'default_a'://默认升序
					$condition .=  " order by asort_value asc ";
				break;
				case 'sell_d'://销量高
					$condition .=  " order by show_sell_count+sell_count desc ";	//虚拟销售量
				break;
				case 'sell_a'://销量低
					$condition .=  " order by show_sell_count+sell_count asc ";
				break;
				case 'price_d'://价格高
					$condition .=  " order by now_price desc ";
				break;
				case 'price_a'://价格低
					$condition .=  " order by now_price asc ";
				break;
				case 'score_d'://积分高
					$condition .=  " order by need_score desc ";
				break;
				case 'score_a'://积分低
					$condition .=  " order by need_score asc ";
				break;
				case 'time_d'://时间新
					$condition .=  " order by createtime desc ";
				break;
				case 'time_a'://时间旧
					$condition .=  " order by createtime asc ";
				break;
				default:
					$condition .=  " order by asort_value desc ";
				break;
				
			}
		
		/*********排序条件end********/		
				
		$query .= $condition." ,id desc"." limit ".$start." , ".$end."" ;	//合并
		//echo $query;
		//file_put_contents("1014f.txt", "5.GetMoney_Common=======".$query."\r \n",FILE_APPEND);
				$pro_id				= '';//产品ID
				$pro_name 			= '';//产品名称
				$default_imgurl 	= '';//默认图片
				$pro_supply_id 		= '';//供应商ID
				$orgin_price 		= '';//原价
				$now_price 			= '';//现价
				$isvp 				= '';//是否VP产品
				$isscore			= '';//是否积分专区产品
				$vp_score 			= '';//VP值
				$p_cashback 			= -1;//返现金额
				$p_cashback_r 		= -1;//返现比例
				$show_sell_count	= '';//虚拟销售量
				$is_free_shipping	= '';//是否包邮，1是，0否
				$need_score			=0;  //兑换积分
				$sell_count			=0;  //真实销量
				$back_currency		=0;//返的购物币
				$is_currency		=0;//是否返购物币
				$result=mysql_query($query)or die('L41 Query failed'.mysql_error());
				while($row=mysql_fetch_object($result)){
					$pro_id		  	  = $row->id;
					$pro_name		  = $row->name;
					$default_imgurl   = $row->default_imgurl;
					$pro_supply_id 	  = $row->is_supply_id;
					$orgin_price 	  = $row->orgin_price;
					$now_price 		  = $row->now_price;
					$isvp 			  = $row->isvp;
					$isscore		  = $row->isscore;
					$vp_score 		  = $row->vp_score;
					$p_cashback 	  = $row->cashback;
					$p_cashback_r	  = $row->cashback_r;
					$show_sell_count  = $row->show_sell_count;
					$is_free_shipping = $row->is_free_shipping;
					$need_score 	  = $row->need_score;
					$sell_count 	  = $row->sell_count;  
					$back_currency 	  = $row->back_currency;
					$is_currency 	  = $row->is_currency;
										
					$product_data['pro_id'] 		= $pro_id;
					$product_data['pro_name'] 		= $pro_name;
					$product_data['default_imgurl'] = $default_imgurl;
					$product_data['pro_supply_id'] 	= $pro_supply_id;
					$product_data['orgin_price']    = $orgin_price;
					$product_data['now_price'] 		= $now_price;
					$product_data['isvp'] 			= $isvp;
					$product_data['isscore'] 		= $isscore;
					$product_data['vp_score'] 		= $vp_score;
					/* $product_data['cashback'] 		= $cashback;
					$product_data['cashback_r'] 	= $cashback_r; */
					$product_data['show_sell_count']= $show_sell_count+$sell_count;
					$product_data['cb_condition'] 	= $cb_condition;
					$product_data['is_free_shipping']= $is_free_shipping;
					$product_data['need_score']		 = $need_score;
					$isbrand=-1;//品牌供应商标识
					if($pro_supply_id>0){
						$brand_id=-1;
						$brand_query="select id from weixin_commonshop_brand_supplys where isvalid=true and brand_status=1 and user_id=".$pro_supply_id." limit 0,1";
						$brand_result=mysql_query($brand_query) or die ('brand_query faild:' .mysql_error());
						while($row=mysql_fetch_object($brand_result)){
							$brand_id=$row->id;
						}
						if($brand_id>0){
							$isbrand=1; //是品牌供应商
						}	
					}
					$product_data['isbrand'] 	= $isbrand;
					
					if($orgin_price >0 && $now_price>0){
						$discount=$now_price/$orgin_price;
						$discount=round($discount ,2)*10;	
					}else{
						$discount=0;
					}
					$product_data['discount']= $discount;
					

					//判断产品是否为返购物币
					//不属于返购物币产品的话，返购物币显示为0
					if($is_currency){
						$product_data['back_currency']= $back_currency;
					}else{
						$product_data['back_currency']=0;
					}
					
					
					if($is_cashback == 1){ //开启了消费返现
						$product_data['is_cashback']= 1;
						
					}
					/*返现金额开始*/
					$p_now_price=$now_price;
					$showAndCashback = $info->showCashback($customer_id,$user_id,$p_cashback,$p_cashback_r,$p_now_price);
					$product_data['pro_cash_money']=$showAndCashback['cashback_m'];
					$product_data['display']=$showAndCashback['display'];
				//	$product_data['query']=$brand_query;
					array_push($rtn_data,$product_data);
				
				}
					//var_dump($product_data);
				
			
			$out = json_encode($rtn_data); 
		//	$out = json_encode($query);
			echo $out;
		
	break;
	
	case 'get_type':
		//type_tys 1平台的分类 2品牌代理商分类	
		/*if(!empty($_POST['type_tys'])){
			$type_tys = $configutil->splash_new($_POST['type_tys']);
			
		}*/
		
		$res_type 	  = array();
		$level_1_type = array();	//一级分类
		$level_2_type = array();	//二级分类	
		
		//V7.0分类新排序
		$sort_str="";
		$type_sort="select sort_str from weixin_commonshop_type_sort where customer_id=".$customer_id."";
		$result_type=mysql_query($type_sort) or die ('type_sort faild' .mysql_error());
		while($row=mysql_fetch_object($result_type)){
		   $sort_str=$row->sort_str;									   
		}
		
		
		
		$query = "select id,name,parent_id,sendstyle,is_shelves,create_type from weixin_commonshop_types where isvalid=true and is_shelves=1 and customer_id=".$customer_id." and parent_id=-1 ";
		//echo $query;
		if($sort_str){
			$query =$query.' order by field(id'.$sort_str.')';  
		}
		
		$pt_id 			= -1;
		$pt_name 		= '';
		$pt_parent_id 	= '';
		$pt_sendstyle 	= '';
		$create_type 	= '';
		
		$result=mysql_query($query)or die('Query failed'.mysql_error());
		while($row=mysql_fetch_object($result)){
			   $pt_id 			= $row->id;
			   $pt_name 		= $row->name;
			   $pt_parent_id 	= $row->parent_id;
			   $pt_sendstyle	= $row->sendstyle;
			   $pt_is_shelves	= $row->is_shelves;
			   $create_type 	= $row->create_type;
			
			   
			   $level_1_type['pt_id']   = $pt_id;
			   $level_1_type['pt_name'] = $pt_name;
			   
			   if($pt_id>0){
				   
						$query_child = "select id,name,parent_id,sendstyle,is_shelves,create_type,asort from weixin_commonshop_types where isvalid=true and customer_id=".$customer_id." and parent_id=".$pt_id."";
						$pc_id 			= -1;
						$pc_name 		= '';
						$pc_sort 		= '';
						$pc_parent 		= '';
						$pc_is_shelves 	= '';
						$pc_create_type = '';
						$level_2_type_temp = array();
						
						$result_child = mysql_query($query_child) or die("L279 : Query failed : ".mysql_error());						
						while($row_child = mysql_fetch_object($result_child)){
								$pc_id 				= $row_child->id;
								$pc_name 			= $row_child->name;
								$pc_sort 			= $row_child->asort;
								$pc_parent 			= $row_child->parent_id;
								$pc_is_shelves 		= $row_child->is_shelves;
								$pc_create_type 	= $row_child->create_type;
								
								 $level_2_type['pc_id']   = $pc_id;
								 $level_2_type['pc_name'] = $pc_name;
								 $level_2_type['pc_parent'] = $pc_parent;
								 
								array_push($level_2_type_temp,$level_2_type);
						}
						
						
			   }	
						//组装
						$level_1_type['child_types'] = $level_2_type_temp;
						//var_dump($level_1_type);
						array_push($res_type,$level_1_type);
		}
						
		
		$out = json_encode($res_type);
		echo $out;
		
	break;	
	
	
}



?>