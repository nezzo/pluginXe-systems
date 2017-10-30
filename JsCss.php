<?php
//В этом файле подключаем все js и css файлы нужные для плагина
function set_data_plugin_js() {
	$tooltip = plugins_url('/css/tooltip.css', __FILE__);
	$plugin = plugins_url('/js/plugin.js', __FILE__);
	 
	 //подключаем стили, скрипты плагина dataPicker  в админке
    wp_enqueue_script( 'jquery-ui-datepicker' );
    wp_enqueue_style( 'jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css' );
	 
	 //подключаем скрипт плагина
	 wp_enqueue_script( 'plugin',$plugin, array('jquery') );
	 
	 wp_enqueue_script( 'plugin',$plugin, array('jquery') );
	 
	 
 	 
}

function set_data_plugin_css() {
	$tooltip = plugins_url('/css/tooltip.css', __FILE__);
 
	  wp_enqueue_style( 'tooltip', $tooltip);
	 
 	 
}