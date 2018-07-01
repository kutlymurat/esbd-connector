<?php

/**
 * Created by PhpStorm.
 * User: Kutlymurat
 * Date: 7/1/2018
 * Time: 7:55 AM
 */
define("NOTDIRECT", true);
include '../EsbdConnect.php';
include '../EsbdFunctions.php';

$connect_esbd = new EsbdFunctions("test");

//$client = $connect_esbd->getClassIDOfClient(28783085);
//$client = $connect_esbd->getClientByKeyFields("","",0,28783085); // поиск по ID. Номер документа должен быть равен нулю.
//$client = $connect_esbd->getClientByKeyFields("","",'123456'); // поиск по номеру документа
//$client = $connect_esbd->getClientByKeyFields("961124300432"); // поиск по ИИН
$client = $connect_esbd->getClientByKeyFields("","111111111111"); // поиск по РНН

var_dump($client);