<?php
/**
 *
 * @category  PLugins
 * @package   Wordpress
 * @author    IPdev.ir
 * @copyright 2022 The Ideh Pardazan (ipe.ir). All rights reserved.
 * @license   https://sms.ir/ ipe license
 * @version   IPE: 1.0.19
 * @link      https://app.sms.ir
 *
 */

if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}


/**
 * @class SMSIRAppClass
 */
class SMSIRAppClass
{
    /**
     * @type string
     */
    const API_URL = 'https://api.sms.ir/v1/';
    /**
     * @type array
     */
    const STATES = [
        1 => "رسیده به گوشی",
        2 => "نرسیده به گوشی",
        3 => "پردازش در مخابرات",
        4 => "نرسیده به مخابرات",
        5 => "رسیده به مخابرات",
        6 => "خطا",
        7 => "لیست سیاه",
    ];
	private static final function saveLog($data){
		date_default_timezone_set('Asia/Tehran');
		global $wpdb;
		$table_name = $wpdb->prefix . 'sms_ir_app_log';
		if ($data->status == 1) return;

		switch ($data->status){
			case 0:
			case 12:
			case 20:
				$title = 'خطای سرور';
				break;
			case 13:
			case 14:
			case 102:
				$title = 'خطای حساب کاربری';
				break;
			default:
				$title = 'خطای اطلاعات';
				break;
		}

		$data = [
			'id'=> NULL,
			'title' => $title,
			'message' => $data->message,
			'status' => $data->status,
			'created_at' => date("Y/m/d H:i:s"),
		];

		if ($wpdb->insert(
			$table_name,
			$data,
			[
				'%d',
				'%s',
				'%s',
				'%d',
				'%s'
			]
		)) return;

	}
    /**
     * @param $text
     * @param $mobiles
     * @return false|mixed
     */
    public static final function sendBulkSMS($text, $mobiles)
    {
        $postFields = [
            "messageText" => $text,
            "lineNumber"  => get_option('sms_ir_info_number'),
            "mobiles"     => $mobiles
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => self::API_URL . "send/bulk",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 5,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => json_encode($postFields)
            ,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'x-api-key:' . get_option('sms_ir_info_api_key'),
            ],
        ]);

        try {
            $response = json_decode(curl_exec($curl));
	        self::saveLog($response);

            curl_close($curl);
            return $response;
        } catch (Exeption $e) {
            return false;
        }
    }

    /**
     * @param $parameters
     * @param $templateId
     * @param $mobile
     * @return false|mixed
     */
    public static final function sendVerifySMS($parameters, $templateId, $mobile)
    {
        $postFields = json_encode([
            "mobile"     => $mobile,
            "templateId" => $templateId,
            "parameters" => $parameters
        ]);

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL            => self::API_URL . "send/verify",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 5,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => $postFields,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'x-api-key:' . get_option('sms_ir_info_api_key'),
            ],
        ]);

        try {
            $response = json_decode(curl_exec($curl));
            curl_close($curl);
            return $response;
        } catch (Exeption $e) {
            return false;
        }
    }

    /**
     * @return false|mixed
     */
    public static final function getLine()
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => self::API_URL . "line",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 5,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'GET',
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'x-api-key:' . get_option('sms_ir_info_api_key'),
            ],
        ]);

        try {
            $response = json_decode(curl_exec($curl));
            curl_close($curl);
            return $response;
        } catch (Exeption $e) {
            return false;
        }
    }

    /**
     * @return false|mixed
     */
    public static final function getCredit()
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => self::API_URL . "credit",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 5,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'GET',
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'x-api-key:' . get_option('sms_ir_info_api_key'),
            ],
        ]);

        try {
            $response = json_decode(curl_exec($curl));
            curl_close($curl);
            return $response;
        } catch (Exeption $e) {
            return false;
        }
    }

    /**
     * @return false|mixed
     */
    public static final function getSendReport()
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => self::API_URL . "send/live",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 5,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'GET',
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'x-api-key:' . get_option('sms_ir_info_api_key'),
            ],
        ]);

        try {
            $response = json_decode(curl_exec($curl));
            curl_close($curl);
            return $response;
        } catch (Exeption $e) {
            return false;
        }
    }

    /**
     * @return false|mixed
     */
    public static final function getReceiveReport()
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => self::API_URL . "receive/live",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 5,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'GET',
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'x-api-key:' . get_option('sms_ir_info_api_key'),
            ],
        ]);

        try {
            $response = json_decode(curl_exec($curl));
            curl_close($curl);
            return $response;
        } catch (Exeption $e) {
            return false;
        }
    }
	public static final function getLog(){
		global $wpdb;
		$table_name = $wpdb->prefix . 'sms_ir_app_log';
		$result = $wpdb->get_results("SELECT * FROM `$table_name` ORDER BY `id` ASC ");

		return $result;
	}

	public static final function getInventoryLog(){
		global $wpdb;
		$table_name = $wpdb->prefix . 'sms_ir_app_notifications';
		$table_products = $wpdb->prefix . 'wp_posts';
		$result = $wpdb->get_results("SELECT * FROM `wp_sms_ir_app_notifications` AS notify
								    LEFT JOIN wp_posts as p ON p.ID = notify.product_id AND p.post_type = 'product'
									WHERE type = 'inventory' ORDER BY notify.id ASC ;");

		return $result;
	}

	public static final function getPromotionLog(){
		global $wpdb;
		$table_name = $wpdb->prefix . 'sms_ir_app_notifications';
		$table_products = $wpdb->prefix . 'wp_posts';
		$result = $wpdb->get_results("SELECT * FROM `wp_sms_ir_app_notifications` AS notify
								    LEFT JOIN wp_posts as p ON p.ID = notify.product_id AND p.post_type = 'product'
									WHERE type = 'promotion' ORDER BY notify.id ASC ;");

		return $result;
	}

}
