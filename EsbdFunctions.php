<?php

/**
 * Created by PhpStorm.
 * User: Kutlymurat
 * Date: 7/1/2018
 * Time: 7:51 AM
 *
 * @author Mambetniyazov Kutlymurat
 * @version 1.0
 */

defined("NOTDIRECT") or exit('Direct access');

class EsbdFunctions extends EsbdConnect
{
    public function __construct($ic)
    {
        parent::__construct($ic);
    }


    /**
     * Функция для поиска транспортного средства по VIN или ID
     *
     * @param string $vin VIN код транспортного средства
     * @param int $tfid ID транспортного средства
     * @return array|mixed
     */
    public function getTFByVINorID($vin = "", $tfid = 0) {
        $tf = array();

        try {

            $tf = $this->esbd_client->__soapCall('GetTFByKeyFields', array(
                    array(
                        'aSessionID' => "{$this->getSessionID()}",
                        'aTF' => array(
                            'TF_ID' => intval($tfid),
                            'TF_TYPE_ID' =>0,
                            'VIN' => $vin,
                            //'BORN' =>'',
                            'MODEL_ID' => 0,
                            'ENGINE_VOLUME' => 0,
                            'RIGHT_HAND_DRIVE_BOOL' => -1,
                            //'ENGINE_NUMBER' =>'',
                            'ENGINE_POWER' => -1,
                            // 'COLOR'=> '',
                            'BORN_MONTH' => -1,
                            // 'VoitureModel' => '',
                            'VoitureMarkId' => 0
                            //'VoitureMark' => ''
                        )
                    )
                )
            );

        }catch(\Exception $e){
            print_r($e);
        }

        $tf = $tf->getTFByKeyFieldsResult->TF;

        // если в результате только ОДНА машина, то ЕСБД почему то не передает его в массиве с индексом 0, а записывает сразу как основной массив
        // фиксим сами
        $array_keys = array_keys((array)$tf);
        if($array_keys[0] === "TF_ID") {
            $array_keys = $tf;
            unset($tf);
            $tf[] = $array_keys;
        }

        if($tf != NULL) {
            for ($i=0; $i<count($tf); $i++) {
                $tf[$i]->custom_info = $this->getVehicleOgpoInfo($tf[$i]->TF_ID);
            }
        }

        return $tf;
    }

    /**
     * Поиск ТС по госномеру
     *
     * @param string $number Государственный регистрационный номер ТС
     * @return array|mixed
     */
    public function getTFByNumber($number = "") {
        $tf = array();
        try {

            $tf = $this->esbd_client->__soapCall('GetTFByNumber', array( array(
                'aSessionID' => $this->getSessionID(),
                'aTF_NUMBER' => $number,
            )
            ));

        }catch(\Exception $e){
            print_r($e);
        }

        $tf = $tf->getTFByNumberResult->TF;


        // если в результате только ОДНА машина, то ЕСБД почему то не передает его в массиве с индексом 0, а записывает сразу как основной массив
        // фиксим сами
        $array_keys = array_keys((array)$tf);
        if($array_keys[0] === "TF_ID") {
            $array_keys = $tf;
            unset($tf);
            $tf[] = $array_keys;
        }

        if($tf != NULL) {
            for ($i=0; $i<count($tf); $i++) {
                $tf[$i]->custom_info = $this->getVehicleOgpoInfo($tf[$i]->TF_ID);
            }
        }

        return $tf;
    }

    /**
     * Функция для получения ID класса бонус-малус через айди клиента
     *
     * @param int $client_id ID клиента
     * @param bool $date Дата, за которую надо получить класс бонус-малус
     * @return string
     */
    public function getClassIDOfClient($client_id, $date = false) {

        if(!$date) $date = date("d.m.Y");

        try {
            $GetClassId = $this->esbd_client->__soapCall('GetClassId', array(
                array(
                    'aSessionID' => $this->getSessionID(),
                    'aClientId'=> $client_id,
                    'aDate'=> $date,
                    'aTFId'=> 0
                )
            ));

        } catch(\Exception $e){
            return ($e->getMessage());
        }

        return $GetClassId->getClassIdResult;
    }

    /**
     * Функция для получения справочника
     *
     * @param string $spr Элементы справочника
     * @return string
     */
    public function getItems($spr = "ECONOMICS_SECTORS") {
        try{
            $economic_sectors = $this->esbd_client->__soapCall('GetItems', array( array(
                'aSessionID' => $this->getSessionID(),
                'aTableName' => $spr
            )
            ));
        } catch(\Exception $e){
            return ($e->getMessage());
        }

        return $economic_sectors->getItemsResult->Item;
    }

    /**
     * Функция для получения данных об ИП/КХ клиента
     *
     * @param int $client_id ID клиента
     * @return array|string
     */
    public function getClientPBDetailsListByID($client_id) {
        try {
            $clients = $this->esbd_client->__soapCall('GetClientPBDetailsListByID', array(array(
                'aSessionID' => $this->getSessionID(),
                'aClientID' => $client_id,
            )
            ));
        } catch(\Exception $e){
            return ($e->getMessage());
        }

        $ipkh_info = $clients->getClientPBDetailsListByIDResult->CLIENT_PB_DETAILS;

        $array_keys = array_keys((array)$ipkh_info);
        if($array_keys[0] === "CLIENT_PB_DETAIL_ID") {
            $array_keys = $ipkh_info;
            unset($ipkh_info);
            $ipkh_info[] = $array_keys;
        }

        return $ipkh_info;

    }

    /**
     * Функция для поиска клиентов через функцию getClientByKeyFields
     *
     * @param string $iin ИИН
     * @param string $rnn РНН
     * @param string $document_number Номер документа
     * @param int $client_id ID клиента
     * @param array $fio ФИО
     * @return array|mixed|null|string
     */
    public function getClientByKeyFields($iin="", $rnn="", $document_number="", $client_id = 0, $fio = array('firstname' => '', 'middlename' => '', 'lastname' => '')) {

            $search_array = array(
                'RESIDENT_BOOL' => -1,
                'Natural_Person_Bool' => -1,
                'ID' => $client_id,
                'Class_ID' => 0,
                'Sex_ID' => 0,
                'SETTLEMENT_ID' => 0,
                'DOCUMENT_TYPE_ID' => 0,
                'ACTIVITY_KIND_ID' => 0,
                'ECONOMICS_SECTOR_ID' => 0,
                'COUNTRY_ID' => 0,
                'IIN' => $iin,
                'TPRN' => $rnn,
                'DOCUMENT_NUMBER' => $document_number,
                'First_Name' => isset($fio['firstname'])?$fio['firstname']:"",
                'Middle_Name' => isset($fio['middlename'])?$fio['middlename']:"",
                'Last_Name' => isset($fio['lastname'])?$fio['lastname']:"",
            );

            try {
                $clients = $this->esbd_client->__soapCall('GetClientsByKeyFields', array( array(
                    'aSessionID' => $this->getSessionID(),
                    'aClient' => $search_array
                )
                ));

            } catch(\Exception $e){
                return ($e->getMessage());
            }

        $clients = $clients->getClientsByKeyFieldsResult->Client;

        $array_keys = array_keys((array)$clients);
        if($array_keys[0] === "ID") {
            $array_keys = $clients;
            unset($clients);
            $clients[] = $array_keys;
        }

        $counter = 0;

        if(!$clients) return NULL;

        foreach($clients as $client) {
            if($client) {
                $clients[$counter]->custom_info = $this->getClіеntOgрoІnfo($client->ID);
            }
            $counter++;
        }

        return $clients;
    }


    /**
     *
     * Функция для расторжения полиса в ЕСБД
     * 1 ||| Досрочное прекращение договора и заключение нового
     * 2 ||| Досрочное расторжение
     * 3 ||| Произведена страховая выплата
     * 4 ||| Корректировка неверно рассчитанной премии
     * 5 ||| Утеря
     * 7 ||| Выпущен дубликат
     * 8 ||| Другая причина
     * 6 ||| Ошибка оператора
     *
     *
     * @param int $esbd_policy_id Идентификатор полиса
     * @param int $reason_id Причина
     * @param bool $date Дата расторжения
     * @return array|mixed
     */
    public function setPolicyRescindingReason($esbd_policy_id, $reason_id, $date = false){

        if(!$date) {
            $date = date("d.m.Y");
        }

        try{
            $SetPolicyRescindingReason = $this->esbd_client->__soapCall('SetPolicyRescindingReason', array(
                    array(
                        'aSessionID' => $this->getSessionID(),
                        'aPolicyId' => intval($esbd_policy_id),
                        'aRescindingReasonId' => intval($reason_id),
                        'aRescindingDate' => $date,
                    )
                )
            );
        }catch(\Exception $e){
            print_r($e);
            $SetPolicyRescindingReason = array();
        }
        return $SetPolicyRescindingReason;
    }


    /**
     * Поиск полиса через айди
     *
     * @param int $esbd_policy_id Идентификатор полиса
     * @return array|mixed
     */
    public function getPolicyByID($esbd_policy_id){

        try{
            $getpolis = $this->esbd_client->__soapCall('GetPolicyByID', array(
                    array(
                        'aSessionID' => $this->getSessionID(),
                        'aPolicyID' => intval($esbd_policy_id),
                    )
                )
            );
        }catch(\Exception $e){
            print_r($e);
            $getpolis = array();
        }
        return $getpolis;
    }


    /**
     * Поиск полисов в заданном интервале дат
     *
     * @param string $date1 Дата ОТ в формате d.m.Y
     * @param string $date2 Дата ДО в формате d.m.Y
     * @return array|mixed
     */
    public function getPoliciesByPolicyDate($date1 = '01.07.2018', $date2 = '02.07.2018'){

        try{
            $getpolis = $this->esbd_client->__soapCall('GetPoliciesByPolicyDate', array(
                    array(
                        'aSessionID' => $this->getSessionID(),
                        'aPolicyDate1' => $date1,
                        'aPolicyDate2' => $date2,
                    )
                )
            );
        }catch(\Exception $e){
            print_r($e);
            $getpolis = array();
        }
        return $getpolis;
    }

    /**
     * Поиск полиса по глобальному номеру
     *
     * @param string $esbd_global_id Глобальный номер полиса
     * @return array|mixed
     */
    public function getPolicyGlobalID($esbd_global_id){

        try{
            $getpolis = $this->esbd_client->__soapCall('GetPolicyByGlobalID', array(
                    array(
                        'aSessionID' => $this->getSessionID(),
                        'aGlobalID' => $esbd_global_id,
                    )
                )
            );
        }catch(\Exception $e){
            print_r($e);
            $getpolis = array();
        }
        return $getpolis;
    }

    /**
     * Поиск полиса по номеру
     *
     * @param string $policy_number Номер полиса
     * @return array|mixed
     */
    public function getPolicyByNumber($policy_number){

        try{
            $getpolis = $this->esbd_client->__soapCall('GetPoliciesByNumber', array(
                    array(
                        'aSessionID' => $this->getSessionID(),
                        'aPolicyNumber' => $policy_number,
                    )
                )
            );
        }catch(\Exception $e){
            print_r($e);
            $getpolis = array();
        }
        return $getpolis;
    }

    /**
     * Реализация функции CalculatePolicyPremium для подсчета стоимости обязательного страхования
     *
     * @param array $drivers_esbd Массив из структуры Driver (http://wiki.mkb.kz/wiki/index.php/Driver)
     * @param array $tfs_esbd Массив из структуры Policies_TF (http://wiki.mkb.kz/wiki/index.php/Policies_TF)
     * @param string $date_beg Дата начало полиса
     * @param string $date_end Дата окончания полиса
     * @return mixed
     */
    public function calculate_ogpo_premium($drivers_esbd, $tfs_esbd, $date_beg = '02.01.2017', $date_end = '01.01.2018') {

        try{

            $ESBDCALC = $this->esbd_client->__soapCall('CalculatePolicyPremium', array(
                array(
                    'aSessionID' => $this->getSessionID(),
                    'aPolicy' => array(
                        'POLICY_ID' => 0,
                        'DATE_BEG' => $date_beg,
                        'DATE_END' => $date_end,
                        'PREMIUM' => -1,
                        'CALCULATED_PREMIUM' => -1,
                        'SYSTEM_DELIMITER_ID' => 0,
                        'CLIENT_ID' => $drivers_esbd[0]['CLIENT_ID'],
                        'CREATED_BY_USER_ID' => 0,
                        'CHANGED_BY_USER_ID' => 0,
                        'POLICY_DATE' => date("d.m.Y"),

                        'REWRITE_BOOL' => 0,
                        'BRANCH_ID' => 0,
                        'REWRITE_POLICY_ID' => -1,
                        'RESCINDING_REASON_ID' => -1,
                        'Drivers' => $drivers_esbd,
                        'PoliciesTF' => $tfs_esbd,
                        'DESCRIPTION' => "",
                        'PAYMENT_ORDER_TYPE_ID' => 1,
                        'PAYMENT_DATE' => date('d.m.Y'),
                        'MIDDLEMAN_ID' => 0,

                        'CLIENT_FORM_ID' => 0,
                        'PAYMENT_TYPE_ID' => 1,
                        'PAYMENT_TYPE' => "Единовременно"

                    )

                )
            ));

        }  catch (\Exception $e){
            print_r($e);
            $ESBDCALC = array();
            echo "REQUEST:\n" . $this->esbd_client->__getLastRequest() . "\n";
        }

        return $ESBDCALC->CalculatePolicyPremiumResult;

    }

    /**
     * Реализация метода GetClientOgpoInfo (http://wiki.mkb.kz/wiki/index.php/GetC%D0%86%D1%96%D0%B5ntOg%D1%80o%D0%86nfo) с помощью Execute (http://wiki.mkb.kz/wiki/index.php/Execute)
     *
     * @param int $client_id ID клиента
     * @return mixed
     */
    public function getClіеntOgрoІnfo($client_id) {
        $client = array();
        $xml = '<GetClientOgpoInfo><CLIENT_ID>' . $client_id . '</CLIENT_ID><CLASS_DATE>' . date('d.m.Y') . '</CLASS_DATE></GetClientOgpoInfo>';

        try {
            $client = $this->esbd_client->__soapCall('Execute', array(
                array(
                    'aSessionId' => $this->getSessionID(),
                    'aRequest' => array(
                        'Type' => 'GetClientOgpoInfo',
                        'Version' => 1,
                        'Body' => base64_encode($xml),
                        'Parameters' => -1,
                    )
                )
            ));
        } catch (\Exception $e) {
            print_r($e);
        }

        $simple = json_decode( json_encode(simplexml_load_string(base64_decode($client->ExecuteResult->Body))) , 1);

        return $simple;
    }

    /**
     * Реализация метода GetVehicleOgpoInfo (http://wiki.mkb.kz/wiki/index.php/GetC%D0%86%D1%96%D0%B5ntOg%D1%80o%D0%86nfo) с помощью Execute (http://wiki.mkb.kz/wiki/index.php/Execute)
     *
     * @param string $tf_id Идентификатор ТС
     * @param string $number Гос. номер ТС
     * @return mixed
     */
    public function getVehicleOgpoInfo($tf_id = "", $number = "") {
        $tf = array();
        $xml = '<GetVehicleOgpoInfo><VEHICLE_ID>' . $tf_id . '</VEHICLE_ID><REG_NUMBER>' . $number . '</REG_NUMBER></GetVehicleOgpoInfo>';

        try {
            $tf = $this->esbd_client->__soapCall('Execute', array(
                array(
                    'aSessionId' => $this->getSessionID(),
                    'aRequest' => array(
                        'Type' => 'GetVehicleOgpoInfo',
                        'Version' => 1,
                        'Body' => base64_encode($xml),
                        'Parameters' => -1,
                    )
                )
            ));
        } catch (\Exception $e) {
            print_r($e);
        }

        $simple = json_decode( json_encode(simplexml_load_string(base64_decode($tf->ExecuteResult->Body))) , 1);

        return $simple;
    }

}