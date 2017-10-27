<?php
//приложение работает на основе WebHooks

/*
https://b24-5hx34w.bitrix24.ua/rest/1/f4zphkrjcjmkpr1d/profile/

$url = 'b24-5hx34w.bitrix24.ua';
$code = 'f4zphkrjcjmkpr1d';

/rest/ - указание системе на то, что данный адрес относится в вебхукам;
/1/ - идентификатор пользователя, создавшего вебхук. Под правами этого пользователя будет работать этот вебхук.
/profile/ - метод REST, который вы хотите выполнить, обращаясь к вебхуку. Разработчик должен сам подобрать метод из REST API в зависимости от целей создания вебхука.

*/
 
//начинаем работу с API  bitrix24
function startAPI($url, $code, $data){
//ссылка куда будем слать инфу	
$queryUrl = "$url/rest/1/$code/crm.lead.add";
//массив содержащий данные для добавления нового лида
$queryData = http_build_query(array(
 'fields' => array(
 "TITLE" => $data['first_name'].' '. $data['last_name'],
 "NAME" => $data['first_name'],
 "LAST_NAME" => $data['last_name'],
 "STATUS_ID" => "NEW",
 "OPENED" => "Y",
 "ASSIGNED_BY_ID" => 1,
 "PHONE" => array(array("VALUE" => $data['phone'], "VALUE_TYPE" => "WORK" )),
 "EMAIL" => array(array("VALUE" => $data['email'], "VALUE_TYPE" => "WORK" )),
 "IM" => array(array("VALUE" => $data['skype'], "VALUE_TYPE" => "SKYPE" )),   
 ),
 'params' => array("REGISTER_SONET_EVENT" => "Y"),
 ));

//отправляем нового лида на регистрацию
$curl = curl_init(); 
curl_setopt_array($curl, array( 
CURLOPT_SSL_VERIFYPEER => 0, 
CURLOPT_POST => 1, 
CURLOPT_HEADER => 0, 
CURLOPT_RETURNTRANSFER => 1, 
CURLOPT_URL => $queryUrl, 
CURLOPT_POSTFIELDS => $queryData, 
)); 
$result = curl_exec($curl); 
curl_close($curl);

}
 