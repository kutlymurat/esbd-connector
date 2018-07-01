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

//$tf = $connect_esbd->getTFByVINorID('1');
//$tf = $connect_esbd->getTFByVINorID('',10478425);
$tf = $connect_esbd->getTFByNumber('A777AAA');

var_dump($tf);