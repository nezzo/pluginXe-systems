<?php
ob_start();

 
//подключаем 1 файл со множеству подключений файлов
require_once('connector.php');
 
function custom_registration_function() {
 
    if ( isset($_POST['submit'] ) ) {
        registration_validation(
        $_POST['username'],
        $_POST['first_name'],
        $_POST['last_name'],
        $_POST['email'],
        $_POST['phone'],
        $_POST['skype'],
        $_POST['password'],
        $_POST['passwordSecond']
       );
          
        // sanitize user form input
        global $username, $first_name, $last_name, $email, $phone, $skype, $password, $passwordSecond;
        $username         =   sanitize_user( $_POST['username'] );
        $first_name       =   sanitize_user( $_POST['first_name'] );
        $last_name        =   sanitize_user( $_POST['last_name'] );
        $email            =   sanitize_email( $_POST['email'] );
        $phone            =   esc_attr( $_POST['phone'] );
        $skype            =   sanitize_text_field( $_POST['skype'] );
        $password         =   esc_attr( $_POST['password'] );
        $passwordSecond   =   esc_attr( $_POST['passwordSecond'] );
        
        // call @function complete_registration to create the user
        // only when no WP_error is found
        complete_registration(
        $username,
        $first_name,
        $last_name,
        $email,
        $phone,
        $skype,
        $password
        );
    }
  
    registration_form(
        (isset( $_POST['username'] ) ? $username : null),
        (isset( $_POST['first_name'] ) ? $first_name : null),
        (isset( $_POST['last_name'] ) ? $last_name : null),
	(isset( $_POST['email'] ) ? $email : null),
        (isset( $_POST['phone'] ) ? $phone : null),
        (isset( $_POST['skype'] ) ? $skype : null),
        (isset( $_POST['password'] ) ? $password : null),
        (isset( $_POST['passwordSecond'] ) ? $passwordSecond : null)
 
        );
}


 function registration_form( $username,$first_name, $last_name, $email, $phone, $skype, $password, $passwordSecond ) {
    echo '
    <style>
    div {
        margin-bottom:2px;
    }
      
    input{
        margin-bottom:4px;
    }
    </style>
    ';
    
    //вызываем форму регистрации
    echo viewReg();
}
 
function registration_validation( $username, $first_name, $last_name, $email, $phone, $skype, $password, $passwordSecond )  {
  global $reg_errors;
  $reg_errors = new WP_Error;
  if ( empty( $username ) || empty( $first_name) || empty( $last_name ) || empty( $password ) || empty( $email ) || empty( $passwordSecond ) || empty( $phone ) ) {
      $reg_errors->add('field', 'Заполните обязательные поля');
  }
  
  if ( 10 > strlen( $phone ) ) {
    $reg_errors->add( 'phone_length', 'Телефон пользователя не должен быть меньше 10 символов' );
  }
  
  if ( 4 > strlen( $username ) ) {
    $reg_errors->add( 'username_length', 'Логин пользователя не должен быть меньше 4 символов' );
  }
  
  if ( username_exists( $username ) )
    $reg_errors->add('user_name', 'Данный логин уже используется');
   
  if ( ! validate_username( $username ) ) {
    $reg_errors->add( 'username_invalid', 'Данный логин недопустим' );
  } 
  
  if ( 5 > strlen( $password ) ) {
        $reg_errors->add( 'password', 'Пароль должен иметь более 5 символов' );
    }
   
   //делаем проверку (пароль и подтвердить пароль одинаковые)
   if ( $password != $passwordSecond ) {
        $reg_errors->add( 'password', 'Поле "Пароль" и "Подтвердить пароль" не совпадает' );
    }
    
    if ( !is_email( $email ) ) {
    $reg_errors->add( 'email_invalid', 'Неправильно введен email' );
    }
    
    if ( email_exists( $email ) ) {
    $reg_errors->add( 'email', 'Данный email уже зарегистрирован' );
    }
    
    if ( is_wp_error( $reg_errors ) ) {
  
        foreach ( $reg_errors->get_error_messages() as $error ) {
          
	   echo '<div class="error_reg">'; 
	    echo '<center>';
            echo '<strong>ERROR</strong>:';
            echo $error . '<br/>';
            echo '</center>';
            echo '</div>';
              
        }
    }
  
}

function complete_registration() {
    global $reg_errors, $username, $first_name, $last_name, $email, $phone, $skype, $password;
    if ( 1 > count( $reg_errors->get_error_messages() ) ) {
        $userdata = array(
        'user_login'    =>   $username,
        'user_email'    =>   $email,
	'user_pass'     =>   $password,
	'first_name'    =>   $first_name,
	'last_name'     =>   $last_name,
	'role'          => 'subscriber',
      );
        $user = wp_insert_user( $userdata );
        
        
	  //после добавление нового пользователя авторизуемся
	  if(! is_wp_error( $user )){
	  
	  //после регистрации отправляем письмо пользователю
	  $mess = "
	    Данные по регестрации:
	    Ваш ник: $username
	    Ваш пароль: $password
	  ";
	  set_mail($email, 'Регистрация', $mess);
	  
	  //добавляем нового лида в Bitrix24
	   startAPI('https://chargeback24.bitrix24.ua', 'n1db38kgl7do8d0g', $data = ['first_name'=>$first_name,'last_name'=>$last_name,'phone'=>$phone,'email'=>$email,'skype'=>$skype]);
	  
	      //добавляем  отдельные данные в таблицу wp_usermeta (номер телефона и скайп)
	      add_user_meta( $user, 'phone', $phone);
	      add_user_meta( $user, 'skype', $skype);
	      
	      // Авторизуем
		    $creds = array(
		    'user_login'    => $userdata['user_login'],
		    'user_password' => $userdata['user_pass'],
		    'remember'      => true,
		    );
		    
		    $auth = wp_signon($creds,false);

		    //в случае удачной авторизации делаем редирект в личный кабинет, если что то пошло не так выводим сообщение об ошибке
		    if (! is_wp_error($auth) ) {
			
		      #TODO тут надо будет сделать редирект на новый кабинет, не в админку вордпресса
		       wp_redirect( home_url(). '/wp-admin/profile.php' ); 
			    
		    }else{
		      echo $auth->get_error_message();
		    }

	    }else{
	      return $user->get_error_message();
	    
	    } 
    }
}

// Register a new shortcode: [cr_custom_registration]
add_shortcode( 'cr_custom_registration', 'custom_registration_shortcode' );
  
// The callback function that will replace [book]
function custom_registration_shortcode() {
    custom_registration_function();
    return ob_get_clean();
}


//делаем отправку письма
function set_mail($email, $title, $mess){


//делаем проверку если все данные есть то отправляем
if(!empty($email) && !empty($title) && !empty($mess)){
  wp_mail($email, $title, $mess);
}else{
  return false;
}


}


