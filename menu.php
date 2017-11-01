<?php
#TODO тут надо сделать по другому то есть. Сделать перенаправление на шаблон где сделать проверку, что это авторизирован юзер, если нет ошибку мол 404.php а если авторизирован и 
#подписчик то выводим свой шаблон все делать как в isyms. Ну и разделить файлы как задумывалось ранее.

/*
  Plugin Name: Плагин "Личный кабинет"
  Version: 1.0
  Author: Artur Legusha
  Author URI: https://isyms.ru
*/

//подключаем 1 файл со множеству подключений файлов
require_once('connector.php');

//подключаем скрипты плагина
add_action( 'admin_enqueue_scripts', 'set_data_plugin_js' );

//подключаем стилей плагина
add_action( 'admin_enqueue_scripts', 'set_data_plugin_css' );

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
  $born = $_POST['field2'];
 }else{
  $born = "";
 }
 
 if(!empty($_POST['field3'])){
  $vudan = $_POST['field3'];
 }else{
  $vudan = "";
 }
 
 if(!empty($_POST['field4'])){
  $broker = $_POST['field4'];
 }else{
  $broker = "";
 }
  
 if(!empty($_POST['field5'])){
  $summa_poter = $_POST['field5'];
 }else{
  $summa_poter = "";
 }
 
 if(!empty($_POST['field6'])){
  $pasport = $_POST['field6'];
 }else{
  $pasport = "";
 }
 
 if(!empty($_POST['field7'])){
  $card = $_POST['field7'];
 }else{
  $card = "";
 }
 
 if(!empty($_POST['field8'])){
  $comment = $_POST['field8'];
 }else{
  $comment = "";
 }
 
  //дата подачи заявки
  $dat =  date("d.m.Y H:i");
  
  
  //id  текущего пользователя
  $user_id = get_current_user_id();
 
  //если данные не пустые то отправляем запрос на добавление мета данных
  if(!empty($fio) && !empty($born) && !empty($vudan) && !empty($broker) ){
  
  $order_massiv = [
    'fio'		=>	$fio,
    'born'		=>	$born,
    'vudan'		=>	$vudan,
    'broker'		=>	$broker,
    'summa_poter'	=>	$summa_poter,
    'pasport'		=>	$pasport,
    'card'		=>	$card,
    'dat'		=>	$dat,
    'user_id'		=>	$user_id,
    'comment'		=>	$comment,
    'summa'		=>	"В обработке",
    'status_order'	=>	"В обработке",
    'srok'		=>	"В обработке",
    'status_buy'	=>	"В обработке",
  ];
 
  
    //сериализуем данные, что бы потом распарсить и получить нужные данные без мучений
      $id = add_user_meta( $user_id, '_order_data', $order_massiv);
      
      //получаем ид добавленной записи и апдейтим (заносим ид записи в базу, что бы после юзать порядковый номер)
      if(!empty($id)){
      $id_order = [
	'id_order'	=> $id	
      ];
      
      $meta_value = array_replace($order_massiv,$id_order);
      $update = update_user_meta( $user_id, '_order_data', $meta_value, $order_massiv );
      
      //после удачных обновлений данных делаем редирект
      if(!empty($update)){
	header("Location: ?page=get_orders");
      }
      
      }
      
 
  }
  

//отправляем данные методом POST на эту же страницу и обрабатываем для добавление в базу и редиректа
echo viewSet_orders_setting();


}
 
//Ваши заявки(админ панель)
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
 
  //получаем нужные поля (ид пользователя и строку)
  $getNeddInfo =  get_row_order_data((int)$_POST['umeta_id']);
  $umeta_id = (int)$_POST['umeta_id'];
  
   //получаем массив для внесения изменений 
   $massivData = maybe_unserialize($getNeddInfo["meta_value"]);
   
   $order = array_replace($massivData,$data);
 
  //заносим в базу обработанные данные
  $update_data = update_user_meta( $getNeddInfo["user_id"], '_order_data', $order, $massivData );
  
  //получаем объект с данными о пользователе
  $infoClient = get_userdata($getNeddInfo["user_id"]);
  
 
  //если обновили данные то отправляем уведомление пользователю
  if(!empty($update_data)){
    $mess = "Статус заявки №$umeta_id был изменен";
    
    //отправляем сообщение об изменении статусы заявки
    set_mail($infoClient->user_email, "Изменение заявки", $mess);
  }

}
 

//по ид пользователя ловим методом гет для создания user_meta 
if(!empty($_GET["umeta_id"])){
    #TODO (редактированию что бы подтягивало данные нужно еще заюзать ид строки с базы) ордера клиента (обработка user_meta ордера НОВОГО)
 
 
//отправляем данные методом POST на эту же страницу и обрабатываем для добавление в базу и редиректа
echo viewGet_orders_list_admin_setting();

//выводим поля на страницу где редактируются статус заявки
$umeta_id = get_row_order_data($_GET["umeta_id"]);
 
if(!empty($umeta_id)){
//десериализуем нужный нам массив
$client = maybe_unserialize($umeta_id["meta_value"]);
 ?>
  <table>
    <thead>
       <th>№ заявки</th>
       <th>Ник</th>
       <th>ФИО</th>
       <th>Дата рождения</th>
       <th>Кем выдан</th>
       <th>Брокер</th>
       <th>Сумма потеряных инвестиций</th>
       <th>Паспорт</th>
       <th>Карта</th>
       <th>Дата подачи заявки</th>
       <th>Комментарий</th>
      </thead>
     <tbody>
      <tr>
	  <td><?=$_GET["umeta_id"];?></td>
	  <td><?=get_userdata($client['user_id'])->user_login;?></td>
 	  <td><?=$client["fio"];?></td>
          <td><?=(!empty($client["born"])) ? $client["born"] : "";?></td>
          <td><?=(!empty($client["vudan"])) ? $client["vudan"] : "";?></td>
          <td><?=(!empty($client["broker"])) ? $client["broker"] : "";?></td>
          <td><?=(!empty($client["summa_poter"])) ? $client["summa_poter"] : "";?></td>
          <td><?=(!empty($client["pasport"])) ? $client["pasport"] : "";?></td>
          <td><?=(!empty($client["card"])) ? $client["card"] : "";?></td>
          <td><?=(!empty($client["dat"])) ? $client["dat"] : "";?></td>
          <td><?=(!empty($client["comment"])) ? $client["comment"] : "";?></td>
       </tr>
      </tbody>
  </table>
<?php
}

}else{
 
 #TODO на будущее если удалять то по 	umeta_id   а создавать юзер мета то  по user_id что бы получать по ид клиента в лк
 
 //массив данных который хранит данные о заявках
 $orderInfo = get_user_order_info();
 
 ?>
   
  <table>
    <thead>
       <th>№ заявки</th>
       <th>Ник</th>
       <th>ФИО</th>
       <th>Дата рождения</th>
       <th>Кем выдан</th>
       <th>Брокер</th>
       <th>Сумма потеряных инвестиций</th>
       <th>Паспорт</th>
       <th>Карта</th>
       <th>Дата подачи заявки</th>
       <th>Комментарий</th>
      </thead>
     <tbody>
     <?php 
   
     foreach($orderInfo as $order) {
     
      //десериализуем нужный нам массив
      $client = maybe_unserialize($order["meta_value"]);
       
       ?>
       <tr>
	  <td><?=$order['umeta_id'];?></td>
	  <td><?=get_userdata($order['user_id'])->user_login;?></td>
 	  <td><a href='<?="?page=get_orders_list_admin&umeta_id=$order[umeta_id]"?>'><?=$client["fio"];?></a></td>
          <td><?=(!empty($client["born"])) ? $client["born"] : "";?></td>
          <td><?=(!empty($client["vudan"])) ? $client["vudan"] : "";?></td>
          <td><?=(!empty($client["broker"])) ? $client["broker"] : "";?></td>
          <td><?=(!empty($client["summa_poter"])) ? $client["summa_poter"] : "";?></td>
          <td><?=(!empty($client["pasport"])) ? $client["pasport"] : "";?></td>
          <td><?=(!empty($client["card"])) ? $client["card"] : "";?></td>
          <td><?=(!empty($client["dat"])) ? $client["dat"] : "";?></td>
          <td><?=(!empty($client["comment"])) ? $client["comment"] : "";?></td>
       </tr>
      <?php } ?>  
     </tbody>
  </table>
<?php

 }
 }
 

//Ваши заявки(пользователь)
add_action('admin_menu', function(){
	add_menu_page( 'Ваши заявки', 'Ваши заявки', 'subscriber', 'get_orders', 'get_orders_list', 'dashicons-clipboard', 4 ); 
} );

//Ваши заявки(пользователь)
 function get_orders_list(){
  //id  текущего пользователя
  $user_id = get_current_user_id();
  $single = false;
  $getOrdersDat = get_user_meta( $user_id, '_order_data', $single );
  $i = 1;
 
  if(!empty($getOrdersDat)){
  ?>
  <table>
    <thead>
       <th>Порядковый номер</th>
       <th>Дата Подачи заявки</th>
       <th>Сумма к оплате</th>
       <th>Статус заявки</th>
       <th>Возврат средств до</th>
       </thead>
     <tbody>
     <?php foreach($getOrdersDat as $orders) { 
     //десериализуем нужный нам массив
      $order = maybe_unserialize($orders);
      ?>
       <tr>
	  <td><?=(!empty($order['id_order'])) ? $order['id_order'] : "";?></td>
          <td><?=(!empty($order['dat'])) ? $order['dat'] : "";?></td>
          <td><?=(!empty($order['summa'])) ? $order['summa'] : "";?></td>
          <td><?=(!empty($order['status_order'])) ? $order['status_order'] : "";?></td>
          <td><?=(!empty($order['srok'])) ? $order['srok'] : "";?></td>
        </tr>
      <?php } ?>  
     </tbody>
  </table>
<?php
}
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
	    //"tr.user-last-name-wrap",
	    
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
 