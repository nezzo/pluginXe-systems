<?php

//в этом файле подключаем все возможные подключения файлов, для того что бы заюзать в 1 файле только 1 подключени

//подключаем api Bitrix24
require('apiBitrix24.php');

//юзаем запросы к базе
require('mysql.php');

//подключаем Js и Css файлы
require('JsCss.php');

//подключаем "вид" форм и тд...
require('views.php');