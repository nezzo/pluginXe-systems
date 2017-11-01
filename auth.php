<?php


//Авторизация клиентов
function authClient(){
  
    //вызываем форму для авторизации
    echo viewLogin();
    
  if(!empty($_POST['submit'])){
     
     //делаем проверку, что бы все поля были заполнены
     if(!empty($_POST['email']) && !empty($_POST['pass'])){
 
	if(!empty(FindLogin($_POST['email']))){
	  // Авторизуем
	  $creds = array(
	      'user_login'    => FindLogin($_POST['email']),
	      'user_password' => $_POST['pass'],
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
	    echo '<div class="error_reg">'; 
	    echo '<center>';
            echo '<strong>ERROR</strong>:';
            echo 'Неправильный логин или пароль' . '<br/>';
            echo '</center>';
            echo '</div>';
	}

	 
     }
  
 
  }
}

//делаем поиск по email, что бы получить логин пользователя
function FindLogin($email){
  $login = get_user_by( 'email', $email );
  
  if(!empty($login->data->user_login)){
    return $login->data->user_login;
  }else{
    return false;
  }
  
  
}

