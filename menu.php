<?php
#TODO надо создать разные файлы с подключением require ибо очень файл большой сложно искать. Подключение и работа с базой в один файл, выводи и формирование страницы в другой
# тут только подключение и соеденение друг с другом сделать вообщем то один большой модуль + регу сюда прописать. Я боялся, что регу нельзя будет откл, но шорткод или код можно будет
#убрать или в настройках вп запретить новую регу пользователям. ОПисание добавить до этого модуля (прописать все с реги сюда в описание)

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
  
  $order_massiv = [
    'fio'		=>	$fio,
    'email'		=>	$email,
    'phone'		=>	$phone,
    'broker'		=>	$broker,
    'pasport'		=>	$pasport,
    'card'		=>	$card,
    'dat'		=>	$dat,
    'user_id'		=>	$user_id,
    'summa'		=>	"В обработке",
    'status_order'	=>	"В обработке",
    'srok'		=>	"В обработке",
    'status_buy'	=>	"В обработке",
  ];
 
  
    //сериализуем данные, что бы потом распарсить и получить нужные данные без мучений
      add_user_meta( $user_id, '_order_data', $order_massiv);
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
	add_menu_page( 'Список заявок', 'Список заявок', 'administrator', 'get_orders_list_admin', 'get_orders_list_admin_setting', 'dashicons-clipboard'); 
} );


//выводим список всех заявок в админ панели для обработки закаказов
function get_orders_list_admin_setting(){

// принимаем данные с формы по редактированию ордера с админки (если в форме был передан ид пользователя)
if(!empty($_POST['umeta_id'])){

  if(!empty($_POST['field1'])){
    $summa = $_POST['field1'];
  }else{
    $summa = "В обработке";
  }
  
  if(!empty($_POST['field2'])){
    $status_order = $_POST['field2'];
  }else{
    $status_order = "В обработке";
  }
  
  if(!empty($_POST['field3'])){
    $srok = $_POST['field3'];
  }else{
    $srok = "В обработке";
  }
  
  if(!empty($_POST['field4'])){
    $status_buy = $_POST['field4'];
  }else{
    $status_buy = "В обработке";
  }
 
  
  //формируем массив данных с формы
  $data = [
  'summa'		=>	$summa,
  'status_order'	=>	$status_order,
  'srok'		=>	$srok,
  'status_buy'		=>	$status_buy,
  ];
 
  //получаем нужные поля (ид пользоватеял и строку)
  $getNeddInfo =  get_row_order_data((int)$_POST['umeta_id']);
  
   //получаем массив для внесения изменений 
   $massivData = maybe_unserialize($getNeddInfo["meta_value"]);
   
   $order = array_replace($massivData,$data);
 
  //заносим в базу обработанные данные
  update_user_meta( $getNeddInfo["user_id"], '_order_data', $order, $massivData );
 
}
 

//по ид пользователя ловим методом гет для создания user_meta 
if(!empty($_GET["umeta_id"])){
    #TODO (редактированию что бы подтягивало данные нужно еще заюзать ид строки с базы) ордера клиента (обработка user_meta ордера НОВОГО)
 
 
//отправляем данные методом POST на эту же страницу и обрабатываем для добавление в базу и редиректа
echo '<form action="?page=get_orders_list_admin" method="post">
      <ul class="form-style-1">
 	    <input type="text" style="display:none;" name="umeta_id"  value="'.$_GET["umeta_id"].'" class="field-divided" />
  	  <li><label>Сумма к оплате</label>
	    <input type="text" name="field1" class="field-divided" />
	  </li>
  
	   <li>
	      <label>Статус заявки</label>
	      <select name="field2" class="field-select">
	      <option>оплачено</option>
	      <option>ожидает оплаты</option>
	      </select>
	  </li>
	  <li><label>Сроки исполнения</label>
	    <input type="text" name="field3" class="field-divided" />
	  </li>
 
	  <li>
	      <label>Статус Оплаты</label>
	      <select name="field4" class="field-select">
	      <option>ожидает оплаты</option>
	      <option>выполняется</option>
 
	      </select>
	  </li>
 
	  <li>
	      <input type="submit" value="Отправить" />
	  </li>
      </ul>
      </form>
';


}else{
 
 #TODO на будущее если удалять то по 	umeta_id   а создавать юзер мета то  по user_id что бы получать по ид клиента в лк
 
 //массив данных который хранит данные о заявках
 $orderInfo = get_user_order_info();
 
 ?>
  <table>
    <thead>
       <th>ФИО</th>
       <th>email</th>
       <th>Телефон</th>
       <th>Брокер</th>
       <th>Паспорт</th>
       <th>Карта</th>
       <th>Дата подачи заявки</th>
      </thead>
     <tbody>
     <?php 
     $i = 1;
     
     foreach($orderInfo as $order) {
     
      //десериализуем нужный нам массив
      $client = maybe_unserialize($order["meta_value"]);
       
       ?>
       <tr>
 	  <td><a href='<?="?page=get_orders_list_admin&umeta_id=$order[umeta_id]"?>'><?=$client["fio"];?></a></td>
          <td><?=$client["email"];?></td>
          <td><?=$client["phone"];?></td>
          <td><?=$client["broker"];?></td>
          <td><?=$client["pasport"];?></td>
          <td><?=$client["card"];?></td>
          <td><?=$client["dat"];?></td>
       </tr>
      <?php } ?>  
     </tbody>
  </table>
<?php

 }
 }
 
 
 //получаем по id пользователя нужную инфу по ордеру так как get_user_meta не подходит из-за дублей
 function get_user_order_info(){
 global $wpdb;
 
 $getInfo = $wpdb->get_results("SELECT umeta_id,meta_value  FROM $wpdb->usermeta
	WHERE  meta_key = '_order_data' ORDER by meta_value DESC ");
	
 	
   
  $infoList = array();
  foreach ($getInfo as $order){
    $infoList[] = [
      "umeta_id" => $order->umeta_id,
      "meta_value" => $order->meta_value,
      
    ];
  
  }
  
  return $infoList;

}


//получаем одну строку user_meta по umeta_id
function get_row_order_data($umeta_id){

 global $wpdb;
 
 $getInfo = $wpdb->get_row("SELECT user_id,meta_value  FROM $wpdb->usermeta
	WHERE  umeta_id = '$umeta_id'", 'ARRAY_A');
	
 return $getInfo;
}
 
 
//Ваши заявки
add_action('admin_menu', function(){
	add_menu_page( 'Ваши заявки', 'Ваши заявки', 'subscriber', 'get_orders', 'get_orders_list', 'dashicons-clipboard', 4 ); 
} );

function get_orders_list(){
  //id  текущего пользователя
  $user_id = get_current_user_id();
  $single = false;
  $getOrdersDat = get_user_meta( $user_id, '_order_data', $single );
  $i = 1;
 
  
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
     <?php foreach($getOrdersDat as $orders) { 
     //десериализуем нужный нам массив
      $order = maybe_unserialize($orders);
      ?>
       <tr>
	<td><?=$i++;?></td>
          <td><?=$order['dat'];?></td>
          <td><?=$order['summa'];?></td>
          <td><?=$order['status_order'];?></td>
          <td><?=$order['srok'];?></td>
          <td><?=$order['status_buy'];?></td>
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
