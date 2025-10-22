<?php 

defined("ABSPATH") || exit("No Access ...");

class WSS_Melipayamak extends WSS_SMS
{
    public function send()
    {
        $data = array('username' => $this->username, 'password' => $this->password,'text' =>implode(";", $this->message),'to' =>$this->phone ,"bodyId"=> $this->code);
        var_dump($data);
        $post_data = http_build_query($data);
        $handle = curl_init('https://rest.payamak-panel.com/api/SendSMS/BaseServiceNumber');
        curl_setopt($handle, CURLOPT_HTTPHEADER, array(
            'content-type' => 'application/x-www-form-urlencoded'
        ));
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $post_data);
        $response = curl_exec($handle);
        return $response;

        }
}