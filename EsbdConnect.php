<?php

/**
 * Created by PhpStorm.
 * User: Kutlymurat
 * Date: 7/1/2018
 * Time: 7:47 AM
 *
 * @author Mambetniyazov Kutlymurat
 * @version 1.0
 */

defined("NOTDIRECT") or exit('Direct access');

class EsbdConnect
{
    private static $soapURL = "https://testesbd.mkb.kz:8077/IICWebService.asmx?WSDL";
    private $soapClientOptions;
    private $soapLogin;

    private $certificates;
    private $certificate_usernames;

    public $esbd_client;
    public $AuthenticateUser;

    public function __construct($ic) {

        $this->certificates = array(
            'test' => array(
                'local_cert' => 'C:\path\to\key.pem',
                'passphrase' => 'PASSWORD_TO_CERTIFICATE',
                'connection_timeout' => 25,
                'location' => self::$soapURL,
                'trace' => 1,
                'exceptions' => 1,
                'encoding' => 'utf-8',
                'stream_context' => stream_context_create(array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                ))
            ),
        );


        $this->certificate_usernames = array(
            'test' => array(
                'aName' => 'USERNAME',
                'aPassword' => 'PASSWORD'
            ),
        );

        $this->soapLogin = $this->certificate_usernames[$ic];
        $this->soapClientOptions = $this->certificates[$ic];

        $this->esbd_client = new \SoapClient(self::$soapURL, $this->soapClientOptions);

        $this->AuthenticateUser = $this->esbd_client->__soapCall('AuthenticateUser', array($this->soapLogin));

    }

    public function getSessionID() {
        return $this->AuthenticateUser->AuthenticateUserResult->SessionID;
    }

    public function getUserID() {
        return intval($this->AuthenticateUser->AuthenticateUserResult->id);
    }

    public function getSystemDelimiterID() {
        return intval($this->AuthenticateUser->AuthenticateUserResult->SYSTEM_DELIMITER_ID);
    }

    public function getBranchID() {
        return intval($this->AuthenticateUser->AuthenticateUserResult->Branch_ID);
    }
}