<?php 

defined("ABSPATH") || exit("No Access ...");

class WSS_Sms_ir extends WSS_SMS
{
    public function data()
    {
    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://api.sms.ir/v1/send/verify',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS =>'{
    "mobile": {$this->phone},
    "templateId": YourTemplateID,
    "parameters": [
            {
    "name":"PARAMETER2",
    "value":"000000"
            }
            ]
        }',

        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Accept: text/plain',
            'x-api-key: YOURAPIKEY'
            ),
        ));

    $response = curl_exec($curl);

    curl_close($curl);
    echo $response;
        }
}