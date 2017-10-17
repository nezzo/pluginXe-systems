<?php
/*
  Plugin Name: Плагин "Личный кабинет"
  Version: 1.0
  Author: Artur Legusha
  Author URI: https://isyms.ru
*/


#TODO подать заявку делать все горизонтально, когда заявка успешно создана делаем редирект в "Ваши заявки",а там где подавали заявки, там обнуляем поля для того, что бы заново их 
#подать. В "Ваши заявки" тут выводим походу обычный текст в табличной верстке со всеми данными. Надо будет спросить, после подачи заявки обнулять поля или не надо?


//Start Install plugin carbon
use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action( 'after_setup_theme', 'crb_load' );
function crb_load() {
    require_once( 'vendor/autoload.php' );
    \Carbon_Fields\Carbon_Fields::boot();
}

//откл по дефолту, что бы заработали права ролей (выводить настройки опции по ролям (в админке одно в подписчиках другое) )
add_filter( 'carbon_fields_theme_options_container_admin_only_access', '__return_false' );


//END Install plugin carbon



//Оставить заявку
add_action( 'carbon_fields_register_fields', 'crb_attach_theme_options_set_orders' );

function crb_attach_theme_options_set_orders() {
   Container::make( 'theme_options', __( ' ', 'my-textdomain' ))
   ->set_page_menu_position( 3 )
   ->set_icon( 'dashicons-welcome-write-blog' )
   ->set_page_file( 'set_orders' )
   ->set_page_menu_title( 'Оставить заявку' )
   ->where( 'current_user_role', '=', 'subscriber' )
    ->add_fields( array(
        Field::make( 'text', 'crb_order_fio', 'ФИО')->set_required( true ) ,
        Field::make( 'text', 'crb_order_email', 'Email')->set_required( true ) ,
        Field::make( 'text', 'crb_order_phone', 'Номер Телефона')->set_required( true ) ,
        Field::make( 'text', 'crb_order_broker', 'Брокер')->set_required( true ) ,
        Field::make( 'text', 'crb_order_pasport', 'Паспорт') ,
         
        Field::make( 'radio', 'crb_subtitle_styling', ' ' )
	  ->add_options( array(
	      'visa' => 'Visa',
	      'mastercard' => 'MasterCard'
	  ) )
    ) );
    
    
}


//Ваши заявки
add_action( 'carbon_fields_register_fields', 'crb_attach_theme_options_get_orders' );

function crb_attach_theme_options_get_orders() {
   Container::make( 'theme_options', __( ' ', 'my-textdomain' ))
   ->set_page_menu_position( 4 )
   ->set_icon( 'dashicons-clipboard' )
   ->set_page_file( 'get_orders' )
   ->set_page_menu_title( 'Ваши заявки' )
   ->where( 'current_user_role', '=', 'subscriber' )
    ->add_fields( array(
        Field::make( 'text', 'crb_facebook_url') ,
        Field::make( 'textarea', 'crb_footer_text' )
    ) );
}


//Мои Документы
add_action( 'carbon_fields_register_fields', 'crb_attach_theme_options_my_document' );

function crb_attach_theme_options_my_document() {
   Container::make( 'theme_options', __( ' ', 'my-textdomain' ))
   ->set_icon( 'dashicons-book-alt' )
   ->set_page_file( 'my_document' )
   ->set_page_menu_title( 'Мои Документы' )
   ->where( 'current_user_role', '=', 'subscriber' )
    ->add_fields( array(
        Field::make( 'text', 'crb_facebook_url') ,
        Field::make( 'textarea', 'crb_footer_text' )
    ) );
}
 

//добавляем в профиль два поля Скайп и телефон
add_filter('user_contactmethods', 'set_new_fields_profile');
 
function set_new_fields_profile($user_contactmethods){
    $user_contactmethods['phone'] 	= 'Телефон';
    $user_contactmethods['skype']	= 'Skype';
  
    return $user_contactmethods;
}

//скрываем ненужные поля
function remove_profile_fields_selectors() {

    if( current_user_can('subscriber')) {
    
	  //удаляем консоль в личном кабинете. Ссылка доступна просто нету пункта меню
	  remove_menu_page('index.php');
    
    
	  $delete = array(
 
	    // Цветовая схема
	    "tr.user-admin-color-wrap",
	
	    // Горячие клавиши
	    "tr.user-comment-shortcuts-wrap",
	
	    // Основной язык сайта
	    "tr.user-language-wrap",
	    
	    // Верхняя панель
	    "tr.user-admin-bar-front-wrap",
	    
	    // h2
	    "h2",
	    
	    // h1
	    "h1.wp-heading-inline",
	    
	    // Фамилия
	    "tr.user-last-name-wrap",
	    
	    // Отображать как
	    "tr.user-display-name-wrap",
	    
	    // Сайт
	    "tr.user-url-wrap",
	    
	    // Биография
	    "tr.user-description-wrap",
	    
	    // Аватар
	    "tr.user-profile-picture",
	    
	    // Имя пользователя
	    "tr.user-user-login-wrap",
	
	    );
	
	    $selectors = implode(", ", $delete);
	
	    echo "<style>{$selectors}{display:none !important;}</style>";
     
    }
  
}
add_action('admin_head','remove_profile_fields_selectors');
