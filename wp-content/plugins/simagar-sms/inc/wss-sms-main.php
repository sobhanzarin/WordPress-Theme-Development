<?php
defined("ABSPATH") || exit("No Access ...");

class WSS_SMS 
{
    public $username;
    public $password;
    public $phone;
    public $message;
    public $code;

    public function __construct($phone, $message, $code) {
        $this->username = wss_simagar('sms-username');
        $this->password = wss_simagar('sms-password');
        $this->phone = $phone;
        $this->message = $message;
        $this->code = $code;
    }
}