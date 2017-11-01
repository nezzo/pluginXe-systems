<?php
//В этом файле юзаем запросы к базе данных


//получаем одну строку user_meta по umeta_id
function get_row_order_data($umeta_id){

 global $wpdb;
 
 $getInfo = $wpdb->get_row($wpdb->prepare("SELECT user_id,meta_value  FROM $wpdb->usermeta
	WHERE  umeta_id = %d", $umeta_id), 'ARRAY_A');
	
 return $getInfo;
}

 //получаем по meta_key  нужные нам поля 
 function get_user_order_info(){
 global $wpdb;
 
 $getInfo = $wpdb->get_results("SELECT umeta_id,user_id,meta_value  FROM $wpdb->usermeta
	WHERE  meta_key = '_order_data' ORDER by meta_value DESC ");
	
 	
   
  $infoList = array();
  foreach ($getInfo as $order){
    $infoList[] = [
      "umeta_id" 	=> 	$order->umeta_id,
      "meta_value" 	=> 	$order->meta_value,
      'user_id'		=>	$order->user_id
      
    ];
  
  }
  
  return $infoList;

}
 