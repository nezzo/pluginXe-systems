<?php
/*
  Plugin Name: Плагин "Личный кабинет"
  Version: 1.0
  Author: Artur Legusha
  Author URI: https://isyms.ru
*/

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
add_action('admin_menu', function(){
	add_menu_page( 'Оставить заявку', 'Оставить заявку', 'subscriber', 'set_orders', 'set_orders_setting', 'dashicons-welcome-write-blog', 3 ); 
} );


//функция по созданию формы и обработки ее "Оставить заявку"
function set_orders_setting(){

#TODO здесь ловить в самом начале $_POST если все данные есть то делаем редирект до вывода echo и тогда должен нормально заработать hedear (редирект на php)
 
 if(!empty($_POST['field1'])){
  $fio = $_POST['field1'];
 }else{
  $fio = "";
 }
 
 if(!empty($_POST['field2'])){
  $email = $_POST['field2'];
 }else{
  $email = "";
 }
 
 if(!empty($_POST['field3'])){
  $phone = $_POST['field3'];
 }else{
  $phone = "";
 }
 
 if(!empty($_POST['field4'])){
  $broker = $_POST['field4'];
 }else{
  $broker = "";
 }
 
 if(!empty($_POST['field5'])){
  $pasport = $_POST['field5'];
 }else{
  $pasport = "";
 }
 
 if(!empty($_POST['field6'])){
  $card = $_POST['field6'];
 }else{
  $card = "";
 }
 
  //дата подачи заявки
  $dat =  date("d.m.Y");
  
  
  //id  текущего пользователя
  $user_id = get_current_user_id();
  
  
  //если данные не пустые то отправляем запрос на добавление мета данных
  if(!empty($fio) && !empty($email) && !empty($phone) && !empty($broker) ){
      add_user_meta( $user_id, '_order_fio', $fio);
      add_user_meta( $user_id, '_order_email', $email);
      add_user_meta( $user_id, '_order_phone', $phone);
      add_user_meta( $user_id, '_order_broker', $broker);
      add_user_meta( $user_id, '_order_pasport', $pasport);
      add_user_meta( $user_id, '_order_card', $card);
      add_user_meta( $user_id, '_order_dat', $dat);
      
      header("Location: ?page=get_orders");
 
  }
  
  
 
//отправляем данные методом POST на эту же страницу и обрабатываем для добавление в базу и редиректа
echo '<form action="?page=set_orders" method="post">
      <ul class="form-style-1">
	  <li><label>ФИО <span class="required">*</span></label>
	    <input type="text" name="field1" class="field-divided" />
	  </li>
	  <li>
	   <label>Email <span class="required">*</span></label>
	   <input type="email" name="field2" class="field-long" />
	  </li>
	  <li>
	   <label>Номер Тел <span class="required">*</span></label>
	   <input type="text" name="field3" class="field-long" />
	  </li>
	  <li>
	   <label>Брокер <span class="required">*</span></label>
	   <input type="text" name="field4" class="field-long" />
	  </li>
	  <li>
	   <label>Паспорт</label>
	   <input type="text" name="field5" class="field-long" />
	  </li>
	 <li>
	      <label>Карты</label>
	      <select name="field6" class="field-select">
	      <option value="visa">visa</option>
	      <option value="mastercard">mastercard</option>
 
	      </select>
	  </li>
 
	  <li>
	      <input type="submit" value="Отправить" />
	  </li>
      </ul>
      </form>
';

}

//Ваши заявки
add_action('admin_menu', function(){
	add_menu_page( 'Ваши заявки', 'Ваши заявки', 'subscriber', 'get_orders', 'get_orders_list', 'dashicons-clipboard', 4 ); 
} );

function get_orders_list(){
  //id  текущего пользователя
  $user_id = get_current_user_id();
  $single = false;
  $getOrdersDat = get_user_meta( $user_id, '_order_dat', $single );
  
   #TODO по дефолту здесь поля в обработке стоят. Админы сами меняют мета данные которые сюда будут подставляться заместь "в обработке".
   #В админке сделать функцию в которой будет выводиться все данные, что указал  клиент в пункте "Оставить заявку". И нужные поля (мета данные)
   #где админы будут прописывать для пользователя статусы, цены и все остальное
  
  ?>
  <table>
    <thead>
       <th>Порядковый номер</th>
       <th>Дата Подачи заявки</th>
       <th>Сумма к оплате</th>
       <th>Статус заявки</th>
       <th>Сроки исполнения</th>
       <th>Статус Оплаты</th>
      </thead>
     <tbody>
     <?php for($i = 0; $i<count($getOrdersDat); $i++) { ?>
       <tr>
	<td><?=$i+1;?></td>
          <td><?=$getOrdersDat[$i];?></td>
          <td>В обработке</td>
          <td>В обработке</td>
          <td>В обработке</td>
          <td>В обработке</td>
       </tr>
      <?php } ?>  
     </tbody>
  </table>
<?php
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
