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

//$policy = $connect_esbd->getPoliciesByPolicyDate();
//$policy = $connect_esbd->getPolicyByID('47289624');
//$policy = $connect_esbd->getPolicyGlobalID('25871490864V');
//$policy = $connect_esbd->getPolicyByNumber('ОС 668');

$drivers = array (
    0 =>
        array (
            'DRIVER_ID' => 0,
            'POLICY_ID' => 0,
            'CLIENT_ID' => 28783085,
            'HOUSEHOLD_POSITION_ID' => 2,
            'AGE_EXPERIENCE_ID' => 2,
            'EXPERIENCE' => 5,
            'DRIVER_CERTIFICATE' => '54654654',
            'DRIVER_CERTIFICATE_DATE' => '15.03.2015',
            'PRIVELEGER_BOOL' => 0,
            'WOW_BOOL' => 0,
            'PENSIONER_BOOL' => 0,
            'INVALID_BOOL' => 0,
            'RECORD_CHANGED_AT' => date('d.m.Y'),
            'CREATED_BY_USER_ID' => 0,
            'INPUT_DATE' => date('d.m.Y'),
            'CHANGED_BY_USER_ID' => 0,
            'SYSTEM_DELIMITER_ID' => 25,
            'ClassId' => 5,
        ),
);

$tfs = array (
    0 =>
        array (
            'POLICY_TF_ID' => 0,
            'POLICY_ID' => 0,
            'TF_ID' => 10478425,
            'TF_TYPE_ID' => 4,
            'TF_AGE_ID' => 1,
            'REGION_ID' => 15,
            'BIG_CITY_BOOL' => 1,
            'RECORD_CHANGED_AT' => date('d.m.Y'),
            'CREATED_BY_USER_ID' => 0,
            'INPUT_DATE' => date('d.m.Y'),
            'CHANGED_BY_USER_ID' => 0,
            'SYSTEM_DELIMITER_ID' => 25,
        ),
);

$policy = $connect_esbd->calculate_ogpo_premium($drivers, $tfs, '02.01.2018', '01.01.2019');

var_dump($policy);