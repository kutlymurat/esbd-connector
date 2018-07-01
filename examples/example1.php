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
$age_experience = $connect_esbd->getItems('AGE_EXPERIENCE');

var_dump($age_experience);