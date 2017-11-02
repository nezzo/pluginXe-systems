<?php
//тут выводим "вид" форм 


//форма "Оставить заявку"
function viewSet_orders_setting(){
  $view = '<form action="?page=set_orders" method="post" class="order_zayavki">
    <div class="col-lg-6 col-md-6">
      <div class="form-group">
        <input type="text" name="field1" class="form-control" placeholder="ФИО">
      </div>
      <div class="form-group">
        <input type="text" name="field2" class="form-control" placeholder="Дата рождения">
      </div>
      <div class="form-group">
        <input type="text" name="field6" class="form-control" placeholder="Серия, номер паспорта">
      </div>
      <div class="form-group">
        <input type="text" name="field3" class="form-control" placeholder="Кем выдан">
      </div>
      <div class="form-group">
        <input type="text" name="field4" class="form-control" placeholder="Брокер">
      </div>
      <div class="form-group">
        <input type="text" name="field5" class="form-control" placeholder="Сумма потеряных инвестиций">
      </div>
      <div class="form-group">
        <label>Карты</label>
	<select name="field7" class="field-select">
	<option value="visa">visa</option>
	<option value="mastercard">mastercard</option>
	</select>
      </div>
      <div class="form-group">
       <label>Комментарий</label>
 	   <textarea rows="5" cols="50" name="field8" class="field-long"></textarea>
      </div>
      <div class="form-group">
        <input type="submit" class="form-control btn btn-success" value="Оформить заявку">
      </div>
    </div> 
  </form>
  <form action="/account/" method="post" class="order_zayavki">
    <div class="form-group">
         <input type="submit" style="background: #b41d1d;" class="form-control btn btn-danger" value="Вернуться в кабинет">
      </div>
  </form>
';

return $view;
}

//форма редактирования заявки
function viewGet_orders_list_admin_setting(){
  $view = '<form action="?page=get_orders_list_admin" method="post">
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
	  <li><label>Возврат средств до</label>
	    <input type="text" name="field3" class="field-divided" />
	  </li>
	  <li>
	      <input type="submit" value="Отправить" />
	  </li>
      </ul>
      </form>
';

return $view;

}

//форма регистрации
function viewReg(){

$view = '
    <form action="' . $_SERVER['REQUEST_URI'] . '" method="post" class="login-form register-form">
    <div class="form-group">
     <input type="text" name="username" value="' . ( isset( $_POST['username'] ) ? $_POST['username'] : null ) . '" class="form-control" placeholder="Логин">
    </div>
    
    <div class="form-group">
     <input type="text" name="first_name" value="' . ( isset( $_POST['first_name'] ) ? $_POST['first_name'] : null ) . '" class="form-control" placeholder="Имя">
    </div>
    
    <div class="form-group">
     <input type="text" name="last_name" value="' . ( isset( $_POST['last_name'] ) ? $_POST['last_name'] : null ) . '" class="form-control" placeholder="Фамилия">
    </div>
    
    <div class="form-group">
     <input type="text" name="email" value="' . ( isset( $_POST['email']) ? $_POST['email'] : null ) . '" class="form-control" placeholder="Email">
    </div>
    
    <div class="form-group">
     <input type="text" name="phone" value="' . ( isset( $_POST['phone']) ? $_POST['phone'] : null ) . '" class="form-control" placeholder="Телефон">
    </div>
    
    <div class="form-group">
     <input type="text" name="skype" value="' . ( isset( $_POST['phone']) ? $_POST['phone'] : null ) . '" class="form-control" placeholder="Skype">
    </div>
      
    <div class="form-group">
     <input type="password" name="password" value="' . ( isset( $_POST['password'] ) ? $_POST['password'] : null ) . '" class="form-control" placeholder="Пароль">
    </div>
    
    <div class="form-group">
     <input type="password" name="passwordSecond" value="' . ( isset( $_POST['passwordSecond'] ) ? $_POST['passwordSecond'] : null ) . '" class="form-control" placeholder="Подтвердите Пароль">
    </div>
      
    <div class="form-group">
      <input type="submit" name="submit" class="form-control login" value="Зарегистрироваться">
    </div>
    </form>
    ';

    return $view;
}

//форма для авторизации
function viewLogin(){

  $view = '
     <form action="' . $_SERVER['REQUEST_URI'] . '" method="post" class="login-form">
  	<div class="form-group opacity">
  		<input type="email" name="email" class="form-control" placeholder="Email">
  	</div>
  	<div class="form-group opacity">
  		<input type="password" name="pass" class="form-control" placeholder="Пароль">
  	</div>
  	<div class="form-group">
  		<input type="submit" name="submit" class="form-control login" value="Войти">
  	</div>
    </form>
  
  ';
  
  return $view;

}