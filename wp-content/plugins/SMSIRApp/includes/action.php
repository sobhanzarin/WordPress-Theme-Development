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

require_once "SMSIRAppClass.php";

add_action('gform_after_save_form', 'smsIrGravityCreate', 10, 2);
/**
 * @param $data
 * @param $isNew
 */
function smsIrGravityCreate($data, $isNew)
{
    if ($isNew) {
        if (!get_option("sms_ir_gravity_create_admin_status")) {
            return;
        }

        $adminMobile = get_option("sms_ir_info_admin");
        $text = get_option("sms_ir_gravity_create_admin_text");

        if ($templateID = get_option("sms_ir_gravity_create_admin_template")) {
            $parameters = [
	            (object)[
		            "name"  => "id",
		            "value" => "{$data["id"]}"
	            ],
	            (object)[
		            "name"  => "title",
		            "value" => "{$data["title"]}"
	            ],
	            (object)[
		            "name"  => "description",
		            "value" => "{$data["description"]}"
	            ]
            ];


            foreach (explode(",", get_option("sms_ir_gravity_create_admin_mobile")) as $mobile) {
                SMSIRAppClass::sendVerifySMS($parameters, $templateID, "$mobile");
            }
            if ($adminMobile) {
                SMSIRAppClass::sendVerifySMS($parameters, $templateID, "$adminMobile");
            }
        } else {
            $mobiles = explode(',', get_option("sms_ir_gravity_create_admin_mobile"));
            if ($adminMobile) {
                $mobiles[] = $adminMobile;
            }

            $text = str_replace("#id#", $data["id"], $text);
            $text = str_replace("#title#", $data["title"], $text);
            $text = str_replace("#description#", $data["description"], $text);

            SMSIRAppClass::sendBulkSMS($text, $mobiles);
        }
    } else {
        if (!get_option("sms_ir_gravity_edit_admin_status")) {
            return;
        }

        $adminMobile = get_option("sms_ir_info_admin");
        $text = get_option("sms_ir_gravity_edit_admin_text");

        if ($templateID = get_option("sms_ir_gravity_edit_admin_template")) {
            $parameters = [
	            (object)[
		            "name"  => "id",
		            "value" => "{$data["id"]}"
	            ],
	            (object)[
		            "name"  => "title",
		            "value" => "{$data["title"]}"
	            ],
	            (object)[
		            "name"  => "description",
		            "value" => "{$data["description"]}"
	            ]
            ];


            foreach (explode(",", get_option("sms_ir_gravity_edit_admin_mobile")) as $mobile) {
                SMSIRAppClass::sendVerifySMS($parameters, $templateID, "$mobile");
            }
            if ($adminMobile) {
                SMSIRAppClass::sendVerifySMS($parameters, $templateID, "$adminMobile");
            }
        } else {
            $mobiles = explode(',', get_option("sms_ir_gravity_edit_admin_mobile"));
            if ($adminMobile) {
                $mobiles[] = $adminMobile;
            }

            $text = str_replace("#id#", $data["id"], $text);
            $text = str_replace("#title#", $data["title"], $text);
            $text = str_replace("#description#", $data["description"], $text);

            SMSIRAppClass::sendBulkSMS($text, $mobiles);
        }
    }
}

add_action('gform_post_payment_status', 'smsIrGravityPayment', 10, 2);
/**
 * @param $form
 * @param $data
 */
function smsIrGravityPayment($form, $data)
{
    global $wpdb, $table_prefix;
    if (get_option("sms_ir_gravity_confirm_transaction_admin_status") && $data["payment_status"] == "Paid") {

        $adminMobile = get_option("sms_ir_info_admin");
        $text = get_option("sms_ir_gravity_confirm_transaction_admin_text");

        if ($templateID = get_option("sms_ir_gravity_confirm_transaction_admin_template")) {
	        $amount = number_format($data["payment_amount"]);
	        $parameters = [
		        (object)[
			        "name"  => "id",
			        "value" => "{$data["id"]}"
		        ],
		        (object)[
			        "name"  => "formid",
			        "value" => "{$form["form_id"]}"
		        ],
		        (object)[
			        "name"  => "amount",
			        "value" => "$amount"
		        ],
		        (object)[
			        "name"  => "amount",
			        "value" => "$amount"
		        ],
		        (object)[
			        "name"  => "transaction",
			        "value" => "{$data["transaction_id"]}"
		        ],
		        (object)[
			        "name"  => "gateway",
			        "value" => "{$data["payment_method"]}"
		        ]
	        ];

            foreach (explode(",", get_option("sms_ir_gravity_confirm_transaction_admin_mobile")) as $mobile) {
                SMSIRAppClass::sendVerifySMS($parameters, $templateID, "$mobile");
            }
            if ($adminMobile) {
                SMSIRAppClass::sendVerifySMS($parameters, $templateID, "$adminMobile");
            }
        } else {
            $mobiles = explode(',', get_option("sms_ir_gravity_confirm_transaction_admin_mobile"));
            if ($adminMobile) {
                $mobiles[] = $adminMobile;
            }

            $text = str_replace("#id#", $data["id"], $text);
            $text = str_replace("#formid#", $form["form_id"], $text);
            $text = str_replace("#amount#", number_format($data["payment_amount"]), $text);
            $text = str_replace("#transaction#", $data["transaction_id"], $text);
            $text = str_replace("#gateway#", $data["payment_method"], $text);

            SMSIRAppClass::sendBulkSMS($text, $mobiles);
        }
    }

    if (get_option("sms_ir_gravity_failed_transaction_admin_status") && $data["payment_status"] != "Paid") {

        $adminMobile = get_option("sms_ir_info_admin");
        $text = get_option("sms_ir_gravity_failed_transaction_admin_text");

        if ($templateID = get_option("sms_ir_gravity_failed_transaction_admin_template")) {
	        $amount = number_format($data["payment_amount"]);
            $parameters = [
	            (object)[
		            "name"  => "id",
		            "value" => "{$data["id"]}"
	            ],
	            (object)[
		            "name"  => "formid",
		            "value" => "{$form["form_id"]}"
	            ],
	            (object)[
		            "name"  => "amount",
		            "value" => "$amount"
	            ],
	            (object)[
		            "name"  => "transaction",
		            "value" => "{$data["transaction_id"]}"
	            ],
	            (object)[
		            "name"  => "gateway",
		            "value" => "{$data["payment_method"]}"
	            ]

            ];

            foreach (explode(",", get_option("sms_ir_gravity_failed_transaction_admin_mobile")) as $mobile) {
                SMSIRAppClass::sendVerifySMS($parameters, $templateID, "$mobile");
            }
            if ($adminMobile) {
                SMSIRAppClass::sendVerifySMS($parameters, $templateID, "$adminMobile");
            }
        } else {
            $mobiles = explode(',', get_option("sms_ir_gravity_failed_transaction_admin_mobile"));
            if ($adminMobile) {
                $mobiles[] = $adminMobile;
            }

            $text = str_replace("#id#", $data["id"], $text);
            $text = str_replace("#formid#", $form["form_id"], $text);
            $text = str_replace("#amount#", number_format($data["payment_amount"]), $text);
            $text = str_replace("#transaction#", $data["transaction_id"], $text);
            $text = str_replace("#gateway#", $data["payment_method"], $text);

            SMSIRAppClass::sendBulkSMS($text, $mobiles);
        }
    }

    if (get_option("sms_ir_gravity_confirm_transaction_user_status") && $data["payment_status"] == "Paid") {

        $adminMobile = get_option("sms_ir_info_admin");
        $text = get_option("sms_ir_gravity_confirm_transaction_user_text");

        if ($templateID = get_option("sms_ir_gravity_confirm_transaction_user_template")) {
	        $amount = number_format($data["payment_amount"]);
	        $parameters = [
		        (object)[
			        "name"  => "id",
			        "value" => "{$data["id"]}"
		        ],
		        (object)[
			        "name"  => "formid",
			        "value" => "{$form["form_id"]}"
		        ],
		        (object)[
			        "name"  => "amount",
			        "value" => "$amount"
		        ],
		        (object)[
			        "name"  => "transaction",
			        "value" => "{$data["transaction_id"]}"
		        ],
		        (object)[
			        "name"  => "gateway",
			        "value" => "{$data["payment_method"]}"
		        ]

	        ];

            $displayMeta = $wpdb->get_row("SELECT `display_meta` FROM `{$table_prefix}gf_form_meta` WHERE `form_id` = " . $form["form_id"]);
            $displayMeta = json_decode($displayMeta->display_meta);
            foreach ($displayMeta->fields as $field) {
                $fieldType = str_replace('_', '', $field->type);
                if (get_option("sms_ir_gravity_confirm_transaction_user_mobile") == "#$fieldType$field->id#") {
                    if ($fieldType == "phone") {
						$mobile = preg_replace('/-|\s|\(|\)/', '', $data["$field->id"]);
                    } else {
                        $mobile = $data["$field->id"];
                    }
                }
            }
            if (isset($mobile) && $mobile) {
                SMSIRAppClass::sendVerifySMS($parameters, $templateID, "$mobile");
            }
        } else {
            $text = str_replace("#id#", $data["id"], $text);
            $text = str_replace("#formid#", $form["form_id"], $text);
            $text = str_replace("#amount#", number_format($data["payment_amount"]), $text);
            $text = str_replace("#transaction#", $data["transaction_id"], $text);
            $text = str_replace("#gateway#", $data["payment_method"], $text);

            $displayMeta = $wpdb->get_row("SELECT `display_meta` FROM `{$table_prefix}gf_form_meta` WHERE `form_id` = " . $form["form_id"]);
            $displayMeta = json_decode($displayMeta->display_meta);
            foreach ($displayMeta->fields as $field) {
                $fieldType = str_replace('_', '', $field->type);
                if (get_option("sms_ir_gravity_confirm_transaction_user_mobile") == "#$fieldType$field->id#") {
                    if ($fieldType == "phone") {
	                    $mobile = preg_replace('/-|\s|\(|\)/', '', $data["$field->id"]);
                    } else {
                        $mobile = $data["$field->id"];
                    }
                }
            }
            if (isset($mobile) && $mobile) {
                SMSIRAppClass::sendBulkSMS($text, ["$mobile"]);
            }
        }
    }

    if (get_option("sms_ir_gravity_failed_transaction_user_status") && $data["payment_status"] != "Paid") {

        $adminMobile = get_option("sms_ir_info_admin");
        $text = get_option("sms_ir_gravity_failed_transaction_user_text");

        if ($templateID = get_option("sms_ir_gravity_failed_transaction_user_template")) {
	        $amount = number_format($data["payment_amount"]);
	        $parameters = [
		        (object)[
			        "name"  => "id",
			        "value" => "{$data["id"]}"
		        ],
		        (object)[
			        "name"  => "formid",
			        "value" => "{$form["form_id"]}"
		        ],
		        (object)[
			        "name"  => "amount",
			        "value" => "$amount"
		        ],
		        (object)[
			        "name"  => "transaction",
			        "value" => "{$data["transaction_id"]}"
		        ],
		        (object)[
			        "name"  => "gateway",
			        "value" => "{$data["payment_method"]}"
		        ]

	        ];

            $displayMeta = $wpdb->get_row("SELECT `display_meta` FROM `{$table_prefix}gf_form_meta` WHERE `form_id` = " . $form["form_id"]);
            $displayMeta = json_decode($displayMeta->display_meta);
            foreach ($displayMeta->fields as $field) {
                $fieldType = str_replace('_', '', $field->type);
                if (get_option("sms_ir_gravity_failed_transaction_user_mobile") == "#$fieldType$field->id#") {
                    if ($fieldType == "phone") {
	                    $mobile = preg_replace('/-|\s|\(|\)/', '', $data["$field->id"]);
                    } else {
                        $mobile = $data["$field->id"];
                    }
                }
            }
            if (isset($mobile) && $mobile) {
                SMSIRAppClass::sendVerifySMS($parameters, $templateID, "$mobile");
            }
        } else {
            $text = str_replace("#id#", $data["id"], $text);
            $text = str_replace("#formid#", $form["form_id"], $text);
            $text = str_replace("#amount#", number_format($data["payment_amount"]), $text);
            $text = str_replace("#transaction#", $data["transaction_id"], $text);
            $text = str_replace("#gateway#", $data["payment_method"], $text);

            $displayMeta = $wpdb->get_row("SELECT `display_meta` FROM `{$table_prefix}gf_form_meta` WHERE `form_id` = " . $form["form_id"]);
            $displayMeta = json_decode($displayMeta->display_meta);
            foreach ($displayMeta->fields as $field) {
                $fieldType = str_replace('_', '', $field->type);
                if (get_option("sms_ir_gravity_failed_transaction_user_mobile") == "#$fieldType$field->id#") {
                    if ($fieldType == "phone") {
	                    $mobile = preg_replace('/-|\s|\(|\)/', '', $data["$field->id"]);
                    } else {
                        $mobile = $data["$field->id"];
                    }
                }
            }
            if (isset($mobile) && $mobile) {
                SMSIRAppClass::sendBulkSMS($text, ["$mobile"]);
            }
        }
    }
}

add_action('gform_after_submission', 'smsIrGravitySubmit', 10, 2);
/**
 * @param $data
 * @param $form
 */
function smsIrGravitySubmit($data, $form)
{
    $id = $form["id"];
    if (get_option("sms_ir_gravity_admin_{$id}_status")) {

        $adminMobile = get_option("sms_ir_info_admin");
        $text = get_option("sms_ir_gravity_admin_{$id}_text");

        if ($templateID = get_option("sms_ir_gravity_admin_{$id}_template")) {
            $parameters = smsIrPrepareGravityVerifyData($form, $data, $text);
            foreach (explode(",", get_option("sms_ir_gravity_admin_{$id}_mobile")) as $mobile) {
                SMSIRAppClass::sendVerifySMS($parameters, $templateID, "$mobile");
            }
            if ($adminMobile) {
                SMSIRAppClass::sendVerifySMS($parameters, $templateID, "$adminMobile");
            }
        } else {
            $mobiles = explode(',', get_option("sms_ir_gravity_admin_{$id}_mobile"));
            if ($adminMobile) {
                $mobiles[] = $adminMobile;
            }
            $text = smsIrPrepareGravityBulkData($form, $data, $text);

            SMSIRAppClass::sendBulkSMS($text, $mobiles);
        }
    }

    if (get_option("sms_ir_gravity_user_{$id}_status")) {

        $text = get_option("sms_ir_gravity_user_{$id}_text");

        if ($templateID = get_option("sms_ir_gravity_user_{$id}_template")) {
            $parameters = smsIrPrepareGravityVerifyData($form, $data, $text);
            foreach ($form["fields"] as $field) {
                $fieldType = str_replace('_', '', $field["type"]);
                $fieldID = $field["id"];
                if (get_option("sms_ir_gravity_user_{$id}_mobile") == "#$fieldType$fieldID#") {
                    if ($fieldType == "phone") {
	                    $mobile = preg_replace('/-|\s|\(|\)/', '', $data["$field->id"]);
                    } else {
                        $mobile = $data["$fieldID"];
                    }
                }
            }

            if (isset($mobile) && $mobile) {
                SMSIRAppClass::sendVerifySMS($parameters, $templateID, "$mobile");
            }
        } else {
            $text = smsIrPrepareGravityBulkData($form, $data, $text);
            foreach ($form["fields"] as $field) {
                $fieldType = str_replace('_', '', $field["type"]);
                $fieldID = $field["id"];
                if (get_option("sms_ir_gravity_user_{$id}_mobile") == "#$fieldType$fieldID#") {
                    if ($fieldType == "phone") {
	                    $mobile = preg_replace('/-|\s|\(|\)/', '', $data["$field->id"]);
                    } else {
                        $mobile = $data["$fieldID"];
                    }
                }
            }
            if (isset($mobile) && $mobile) {
                SMSIRAppClass::sendBulkSMS($text, ["$mobile"]);
            }
        }
    }
}

/**
 * @param $form
 * @param $data
 * @param $text
 * @return array|mixed|string|string[]
 */
function smsIrPrepareGravityBulkData($form, $data, $text)
{
    foreach ($form["fields"] as $field) {
        $fieldType = str_replace('_', '', $field["type"]);
        $fieldID = $field["id"];
        if (strpos($text, "#$fieldType$fieldID#") !== false) {
            switch ($fieldType) {
                case "name":
                    $nameValue = "";
                    foreach ($field["inputs"] as $input) {
                        if (isset($data[$input["id"]]) && $data[$input["id"]]) {
                            $nameValue .= $data[$input["id"]] . " ";
                        }
                    }
                    $text = str_replace("#$fieldType$fieldID#", $nameValue, $text);
                    break;
                case "address":
                    $addressValue = "";
                    foreach ($field["inputs"] as $input) {
                        if (isset($data[$input["id"]]) && $data[$input["id"]]) {
                            $addressValue .= $data[$input["id"]] . " ";
                        }
                    }
                    $text = str_replace("#$fieldType$fieldID#", $addressValue, $text);
                    break;
                case "product":
                    $productValue = "";
                    foreach ($field["inputs"] as $input) {
                        if (isset($data[$input["id"]]) && $data[$input["id"]]) {
                            $productValue .= $data[$input["id"]] . " ";
                        }
                    }
                    $text = str_replace("#$fieldType$fieldID#", $productValue, $text);
                    break;
                case "multiselect":
					$multiSelectValue = preg_replace('/\[|\]/', '', $data[$fieldID]);
                    $text = str_replace("#$fieldType$fieldID#", $multiSelectValue, $text);
                    break;
                case "checkbox":
                    foreach ($field["inputs"] as $input) {
                        if (isset($data[$input["id"]]) && $data[$input["id"]]) {
                            if (isset($checkBoxValue) && $checkBoxValue) {
                                $checkBoxValue .= "," . $data[$input["id"]];
                            } else {
                                $checkBoxValue = $data[$input["id"]];
                            }
                        }
                    }
                    if (isset($checkBoxValue) && $checkBoxValue) {
                        $text = str_replace("#$fieldType$fieldID#", $checkBoxValue, $text);
                    }
                    break;
                default:
                    $text = str_replace("#$fieldType$fieldID#", $data[$fieldID], $text);
            }
        }
    }

    return $text;
}

/**
 * @param $form
 * @param $data
 * @param $text
 * @return array
 */
function smsIrPrepareGravityVerifyData($form, $data, $text)
{
    $parameters = [];
    foreach ($form["fields"] as $field) {
        $fieldType = str_replace('_', '', $field["type"]);
        $fieldID = $field["id"];
        if (strpos($text, "#$fieldType$fieldID#") !== false) {
            switch ($fieldType) {
                case "name":
                    $nameValue = "";
                    foreach ($field["inputs"] as $input) {
                        if (isset($data[$input["id"]]) && $data[$input["id"]]) {
                            $nameValue .= $data[$input["id"]] . " ";
                        }
                    }
                    $parameters[] = (object)[
                        "name"  => "$fieldType$fieldID",
                        "value" => "$nameValue"
                    ];
                    break;
                case "address":
                    $addressValue = "";
                    foreach ($field["inputs"] as $input) {
                        if (isset($data[$input["id"]]) && $data[$input["id"]]) {
                            $addressValue .= $data[$input["id"]] . " ";
                        }
                    }
                    $parameters[] = (object)[
                        "name"  => "$fieldType$fieldID",
                        "value" => "$addressValue"
                    ];
                    break;
                case "product":
                    $productValue = "";
                    foreach ($field["inputs"] as $input) {
                        if (isset($data[$input["id"]]) && $data[$input["id"]]) {
                            $productValue .= $data[$input["id"]] . " ";
                        }
                    }
                    $parameters[] = (object)[
                        "name"  => "$fieldType$fieldID",
                        "value" => "$productValue"
                    ];
                    break;
                case "multiselect":
	                $multiSelectValue = preg_replace('/\[|\]/', '', $data[$fieldID]);
                    $parameters[] = (object)[
                        "name"  => "$fieldType$fieldID",
                        "value" => "$multiSelectValue"
                    ];
                    break;
                case "checkbox":
                    foreach ($field["inputs"] as $input) {
                        if (isset($data[$input["id"]]) && $data[$input["id"]]) {
                            if (isset($checkBoxValue) && $checkBoxValue) {
                                $checkBoxValue .= "," . $data[$input["id"]];
                            } else {
                                $checkBoxValue = $data[$input["id"]];
                            }
                        }
                    }
                    if (isset($checkBoxValue) && $checkBoxValue) {
                        $parameters[] = (object)[
                            "name"  => "$fieldType$fieldID",
                            "value" => "$checkBoxValue"
                        ];
                    }
                    break;
                default:
                    $parameters[] = (object)[
                        "name"  => "$fieldType$fieldID",
                        "value" => "{$data[$fieldID]}"
                    ];
            }
        }
    }

    return $parameters;
}

add_action('wpcf7_after_create', 'smsIrContactCreate', 10, 1);
/**
 * @param $data
 */
function smsIrContactCreate($data)
{
    if (!get_option("sms_ir_contact_create_admin_status")) {
        return;
    }

    $adminMobile = get_option("sms_ir_info_admin");
    $text = get_option("sms_ir_contact_create_admin_text");

    if ($templateID = get_option("sms_ir_contact_create_admin_template")) {
        $parameters = [
	        (object)[
		        "name"  => "id",
		        "value" => "$data->id"
	        ],
	        (object)[
		        "name"  => "title",
		        "value" => "$data->title"
	        ]
        ];


        foreach (explode(",", get_option("sms_ir_contact_create_admin_mobile")) as $mobile) {
            SMSIRAppClass::sendVerifySMS($parameters, $templateID, "$mobile");
        }
        if ($adminMobile) {
            SMSIRAppClass::sendVerifySMS($parameters, $templateID, "$adminMobile");
        }
    } else {
        $mobiles = explode(',', get_option("sms_ir_contact_create_admin_mobile"));
        if ($adminMobile) {
            $mobiles[] = $adminMobile;
        }

        $text = str_replace("#id#", $data->id, $text);
        $text = str_replace("#title#", $data->title, $text);

        SMSIRAppClass::sendBulkSMS($text, $mobiles);
    }
}

add_action('wpcf7_after_update', 'smsIrContactUpdate', 10, 1);
/**
 * @param $data
 */
function smsIrContactUpdate($data)
{
    if (!get_option("sms_ir_contact_edit_admin_status")) {
        return;
    }

    $adminMobile = get_option("sms_ir_info_admin");
    $text = get_option("sms_ir_contact_edit_admin_text");

    if ($templateID = get_option("sms_ir_contact_edit_admin_template")) {
	    $parameters = [
		    (object)[
			    "name"  => "id",
			    "value" => "$data->id"
		    ],
		    (object)[
			    "name"  => "title",
			    "value" => "$data->title"
		    ]
	    ];


        foreach (explode(",", get_option("sms_ir_contact_edit_admin_mobile")) as $mobile) {
            SMSIRAppClass::sendVerifySMS($parameters, $templateID, "$mobile");
        }
        if ($adminMobile) {
            SMSIRAppClass::sendVerifySMS($parameters, $templateID, "$adminMobile");
        }
    } else {
        $mobiles = explode(',', get_option("sms_ir_contact_edit_admin_mobile"));
        if ($adminMobile) {
            $mobiles[] = $adminMobile;
        }

        $text = str_replace("#id#", $data->id, $text);
        $text = str_replace("#title#", $data->title, $text);

        SMSIRAppClass::sendBulkSMS($text, $mobiles);
    }
}

/**
 * @param $data
 * @param $text
 * @return array|mixed|string|string[]
 */
function smsIrPrepareContactBulkData($data, $text)
{
    foreach ($data as $dataKey => $dataValue) {
        $dataKey = str_replace("-", "", $dataKey);
        if (strpos($text, "#$dataKey#") !== false) {
            if (is_array($dataValue)) {
                $value = implode(", ", $dataValue);
                $text = str_replace("#$dataKey#", $value, $text);
            } else {
                $text = str_replace("#$dataKey#", $dataValue, $text);
            }
        }
    }

    return $text;
}

/**
 * @param $data
 * @param $text
 * @return array
 */
function smsIrPrepareContactVerifyData($data, $text)
{
    $parameters = [];
    foreach ($data as $dataKey => $dataValue) {
        $dataKey = str_replace("-", "", $dataKey);
            if (is_array($dataValue)) {
                $value = implode(", ", $dataValue);
                $parameters[] = (object)[
                    "name"  => "$dataKey",
                    "value" => "$value"
                ];
            } else {
                $parameters[] = (object)[
                    "name"  => "$dataKey",
                    "value" => "$dataValue"
                ];
            }
    }

    return $parameters;
}

add_action('wpcf7_submit', 'smsIrContactSubmit', 10, 2);
/**
 * @param $form
 * @param $result
 */
function smsIrContactSubmit($form, $result)
{
    if (isset($result["status"]) && $result["status"] == "validation_failed") {
        return;
    }

    $data = $_POST;
    $id = $form->id;
    if (get_option("sms_ir_contact_admin_{$id}_status")) {

        $adminMobile = get_option("sms_ir_info_admin");
        $text = get_option("sms_ir_contact_admin_{$id}_text");

        if ($templateID = get_option("sms_ir_contact_admin_{$id}_template")) {
            $parameters = smsIrPrepareContactVerifyData($data, $text);
            foreach (explode(",", get_option("sms_ir_contact_admin_{$id}_mobile")) as $mobile) {
                SMSIRAppClass::sendVerifySMS($parameters, $templateID, "$mobile");
            }
            if ($adminMobile) {
                SMSIRAppClass::sendVerifySMS($parameters, $templateID, "$adminMobile");
            }
        } else {
            $mobiles = explode(',', get_option("sms_ir_contact_admin_{$id}_mobile"));
            if ($adminMobile) {
                $mobiles[] = $adminMobile;
            }
            $text = smsIrPrepareContactBulkData($data, $text);

            SMSIRAppClass::sendBulkSMS($text, $mobiles);
        }
    }

    if (get_option("sms_ir_contact_user_{$id}_status")) {
        $text = get_option("sms_ir_contact_user_{$id}_text");
        if ($templateID = get_option("sms_ir_contact_user_{$id}_template")) {
            $parameters = smsIrPrepareContactVerifyData($data, $text);
            foreach ($data as $dataKey => $dataValue) {
                $dataKey = str_replace("-", "", $dataKey);
                if (strpos("#$dataKey#", get_option("sms_ir_contact_user_{$id}_mobile")) !== false) {
                    $mobile = $dataValue;
                }
            }

            if (isset($mobile) && $mobile) {
                SMSIRAppClass::sendVerifySMS($parameters, $templateID, "$mobile");
            }
        } else {
            $text = smsIrPrepareContactBulkData($data, $text);
            foreach ($data as $dataKey => $dataValue) {
                $dataKey = str_replace("-", "", $dataKey);
                if (strpos("#$dataKey#", get_option("sms_ir_contact_user_{$id}_mobile")) !== false) {
                    $mobiles = $dataValue;
                }
            }

            if (isset($mobiles) && $mobiles) {
                SMSIRAppClass::sendBulkSMS($text, ["$mobiles"]);
            }
        }
    }
}

add_action("wp_ajax_nopriv_digits_resendotp", "smsIrWordpressAuth", 103);
add_action("wp_ajax_digits_resendotp", "smsIrWordpressAuth", 103);
add_action("wp_ajax_nopriv_digits_check_mob", "smsIrWordpressAuth", 103);
add_action("wp_ajax_digits_check_mob", "smsIrWordpressAuth", 103);
/**
 * params
 */
function smsIrWordpressAuth()
{
    $digitGateway = get_option('digit_custom_gateway');
    if (strtolower($digitGateway['gateway_url']) != "sms.ir") {
        return;
    }

    global $wpdb, $table_prefix;
    if ($_POST["login"] == 1) {
        if (!get_option("sms_ir_wordpress_login_user_status")) {
            return;
        }

        $text = get_option("sms_ir_wordpress_login_user_text");
        $code = dig_get_otp();
        $mobile = $_POST["mobileNo"];
        $wpdb->replace("{$table_prefix}digits_mobile_otp", [
            'countrycode' => $_POST["countrycode"],
            'mobileno'    => $mobile,
            'otp'         => md5($code),
            'time'        => date("Y-m-d H:i:s", strtotime("now"))
        ], [
                '%d',
                '%s',
                '%s',
                '%s'
            ]
        );

        if ($templateID = get_option("sms_ir_wordpress_login_user_template")) {
            $parameters = [
	            (object)[
		            "name"  => "code",
		            "value" => "$code"
	            ],
	            (object)[
		            "name"  => "mobile",
		            "value" => "$mobile"
	            ],
	            (object)[
		            "name"  => "site",
		            "value" => "{$_SERVER["SERVER_NAME"]}"
	            ]
            ];


            SMSIRAppClass::sendVerifySMS($parameters, $templateID, "$mobile");
        } else {
            $text = str_replace("#code#", $code, $text);
            $text = str_replace("#mobile#", $mobile, $text);
            $text = str_replace("#site#", $_SERVER["SERVER_NAME"], $text);

            SMSIRAppClass::sendBulkSMS($text, ["$mobile"]);
        }
    } elseif ($_POST["login"] == 2) {
        if (!get_option("sms_ir_wordpress_register_user_status")) {
            return;
        }

        $text = get_option("sms_ir_wordpress_register_user_text");
        $code = dig_get_otp();
        $mobile = $_POST["mobileNo"];
        $wpdb->replace("{$table_prefix}digits_mobile_otp", [
            'countrycode' => $_POST["countrycode"],
            'mobileno'    => $mobile,
            'otp'         => md5($code),
            'time'        => date("Y-m-d H:i:s", strtotime("now"))
        ], [
                '%d',
                '%s',
                '%s',
                '%s'
            ]
        );

        if ($templateID = get_option("sms_ir_wordpress_register_user_template")) {
	        $parameters = [
		        (object)[
			        "name"  => "code",
			        "value" => "$code"
		        ],
		        (object)[
			        "name"  => "mobile",
			        "value" => "$mobile"
		        ],
		        (object)[
			        "name"  => "site",
			        "value" => "{$_SERVER["SERVER_NAME"]}"
		        ]
	        ];

            SMSIRAppClass::sendVerifySMS($parameters, $templateID, "$mobile");
        } else {
            $text = str_replace("#code#", $code, $text);
            $text = str_replace("#mobile#", $mobile, $text);
            $text = str_replace("#site#", $_SERVER["SERVER_NAME"], $text);

            SMSIRAppClass::sendBulkSMS($text, ["$mobile"]);
        }
    } elseif ($_POST["login"] == 3) {
        if (!get_option("sms_ir_wordpress_password_user_status")) {
            return;
        }

        $text = get_option("sms_ir_wordpress_password_user_text");
        $code = dig_get_otp();
        $mobile = $_POST["mobileNo"];
        $wpdb->replace("{$table_prefix}digits_mobile_otp", [
            'countrycode' => $_POST["countrycode"],
            'mobileno'    => $mobile,
            'otp'         => md5($code),
            'time'        => date("Y-m-d H:i:s", strtotime("now"))
        ], [
                '%d',
                '%s',
                '%s',
                '%s'
            ]
        );

        if ($templateID = get_option("sms_ir_wordpress_password_user_template")) {
	        $parameters = [
		        (object)[
			        "name"  => "code",
			        "value" => "$code"
		        ],
		        (object)[
			        "name"  => "mobile",
			        "value" => "$mobile"
		        ],
		        (object)[
			        "name"  => "site",
			        "value" => "{$_SERVER["SERVER_NAME"]}"
		        ]
	        ];

            SMSIRAppClass::sendVerifySMS($parameters, $templateID, "$mobile");
        } else {
            $text = str_replace("#code#", $code, $text);
            $text = str_replace("#mobile#", $mobile, $text);
            $text = str_replace("#site#", $_SERVER["SERVER_NAME"], $text);

            SMSIRAppClass::sendBulkSMS($text, ["$mobile"]);
        }
    }
}

add_action('woocommerce_order_status_changed', 'smsIrWoocommerceOrder', 10, 3);
/**
 * @param $orderID
 * @param $oldStatus
 * @param $newStatus
 * @return void
 */
function smsIrWoocommerceOrder($orderID, $oldStatus, $newStatus)
{
    if (get_option("sms_ir_woocommerce_order_admin_{$newStatus}_status")) {
        $order = wc_get_order($orderID);
        $orderItems = $order->get_items();
        $adminMobile = get_option("sms_ir_info_admin");
        $text = get_option("sms_ir_woocommerce_order_admin_{$newStatus}_text");

        if ($templateID = get_option("sms_ir_woocommerce_order_admin_{$newStatus}_template")) {
	        $price = number_format($order->get_total());

	        $shipping = number_format($order->get_shipping_total());

	        $discount = number_format($order->get_discount_total());

	        $items = "";
	        foreach ($orderItems as $item) {
		        $items .= $item->get_name() . " * " . $item->get_quantity() . PHP_EOL;
	        }

	        $itemsCount = count($orderItems);

            $parameters = [
	            (object)[
		            "name"  => "orderid",
		            "value" => "$orderID"
	            ],
	            (object)[
		            "name"  => "trackingcode",
		            "value" => "{$order->get_order_key()}"
	            ],
	            (object)[
		            "name"  => "description",
		            "value" => "{$order->get_customer_note()}"
	            ],
	            (object)[
		            "name"  => "paymentmethod",
		            "value" => "{$order->get_payment_method_title()}"
	            ],

	            (object)[
		            "name"  => "oldstatus",
		            "value" => "$oldStatus"
	            ],
	            (object)[
		            "name"  => "newstatus",
		            "value" => "$newStatus"
	            ],
			    (object)[
			             "name"  => "price",
			             "value" => "$price"
			    ],
	            (object)[
		            "name"  => "shipping",
		            "value" => "$shipping"
	           ],
	            (object)[
			            "name"  => "discount",
			            "value" => "$discount"
	            ],
	            (object)[
		            "name"  => "items",
		            "value" => "$items"
	            ],
	            (object)[
		            "name"  => "itemscount",
		            "value" => "$itemsCount"
	            ],
	            (object)[
			              "name"  => "productscount",
			              "value" => "{$order->get_item_count()}"
	            ],
	            (object)[
		            "name"  => "firstname",
		            "value" => "{$order->get_billing_first_name()}"
	            ],
	            (object)[
		            "name"  => "lastname",
		            "value" => "{$order->get_billing_last_name()}"
	            ],
	            (object)[
		            "name"  => "company",
		            "value" => "{$order->get_billing_company()}"
	            ],
	            (object)[
		            "name"  => "address1",
		            "value" => "{$order->get_billing_address_1()}"
	            ],
	            (object)[
		            "name"  => "address2",
		            "value" => "{$order->get_billing_address_2()}"
	            ],
	            (object)[
		            "name"  => "city",
		            "value" => "{$order->get_billing_city()}"
	            ],
	            (object)[
		            "name"  => "state",
		            "value" => "{$order->get_billing_state()}"
	            ],
	            (object)[
		            "name"  => "postcode",
		            "value" => "{$order->get_billing_postcode()}"
	            ],
	            (object)[
		            "name"  => "country",
		            "value" => "{$order->get_billing_country()}"
	            ],
	            (object)[
		            "name"  => "email",
		            "value" => "{$order->get_billing_email()}"
	            ],
	            (object)[
		            "name"  => "phone",
		            "value" => "{$order->get_billing_phone()}"
	            ],
            ];

            foreach (explode(",", get_option("sms_ir_woocommerce_order_admin_{$newStatus}_mobile")) as $mobile) {
                SMSIRAppClass::sendVerifySMS($parameters, $templateID, "$mobile");
            }
            if ($adminMobile) {
                SMSIRAppClass::sendVerifySMS($parameters, $templateID, "$adminMobile");
            }
        } else {
            $mobiles = explode(',', get_option("sms_ir_woocommerce_order_admin_{$newStatus}_mobile"));
            if ($adminMobile) {
                $mobiles[] = $adminMobile;
            }

            $text = str_replace("#orderid#", $orderID, $text);
            $text = str_replace("#newstatus#", $newStatus, $text);
            $text = str_replace("#oldstatus#", $oldStatus, $text);
            $text = str_replace("#trackingcode#", $order->get_order_key(), $text);
            $text = str_replace("#description#", $order->get_customer_note(), $text);
            $text = str_replace("#paymentmethod#", $order->get_payment_method_title(), $text);
            $text = str_replace("#price#", number_format($order->get_total()), $text);
            $text = str_replace("#shipping#", number_format($order->get_shipping_total()), $text);
            $text = str_replace("#discount#", number_format($order->get_discount_total()), $text);
            $text = str_replace("#productscount#", $order->get_item_count(), $text);
            $text = str_replace("#firstname#", $order->get_billing_first_name(), $text);
            $text = str_replace("#lastname#", $order->get_billing_last_name(), $text);
            $text = str_replace("#company#", $order->get_billing_company(), $text);
            $text = str_replace("#address1#", $order->get_billing_address_1(), $text);
            $text = str_replace("#address2#", $order->get_billing_address_2(), $text);
            $text = str_replace("#city#", $order->get_billing_city(), $text);
            $text = str_replace("#state#", $order->get_billing_state(), $text);
            $text = str_replace("#postcode#", $order->get_billing_postcode(), $text);
            $text = str_replace("#country#", $order->get_billing_country(), $text);
            $text = str_replace("#email#", $order->get_billing_email(), $text);
            $text = str_replace("#phone#", $order->get_billing_phone(), $text);

            if (strpos($text, "#items#") !== false) {
                $items = "";
                foreach ($orderItems as $item) {
                    $items .= $item->get_name() . " * " . $item->get_quantity() . PHP_EOL;
                }
                $text = str_replace("#items#", $items, $text);
            }
            if (strpos($text, "#itemscount#") !== false) {
                $text = str_replace("#itemscount#", count($orderItems), $text);
            }

            SMSIRAppClass::sendBulkSMS($text, $mobiles);
        }
    }

    if (get_option("sms_ir_woocommerce_order_user_{$newStatus}_status")) {
        $order = wc_get_order($orderID);
        $orderItems = $order->get_items();
        $adminMobile = get_option("sms_ir_info_admin");
        $text = get_option("sms_ir_woocommerce_order_user_{$newStatus}_text");

        if ($templateID = get_option("sms_ir_woocommerce_order_user_{$newStatus}_template")) {
	        $price = number_format($order->get_total());

	        $shipping = number_format($order->get_shipping_total());

	        $discount = number_format($order->get_discount_total());

	        $items = "";
	        foreach ($orderItems as $item) {
		        $items .= $item->get_name() . " * " . $item->get_quantity() . PHP_EOL;
	        }

	        $itemsCount = count($orderItems);

	        $parameters = [
		        (object)[
			        "name"  => "orderid",
			        "value" => "$orderID"
		        ],
		        (object)[
			        "name"  => "trackingcode",
			        "value" => "{$order->get_order_key()}"
		        ],
		        (object)[
			        "name"  => "description",
			        "value" => "{$order->get_customer_note()}"
		        ],
		        (object)[
			        "name"  => "paymentmethod",
			        "value" => "{$order->get_payment_method_title()}"
		        ],

		        (object)[
			        "name"  => "oldstatus",
			        "value" => "$oldStatus"
		        ],
		        (object)[
			        "name"  => "newstatus",
			        "value" => "$newStatus"
		        ],
		        (object)[
			        "name"  => "price",
			        "value" => "$price"
		        ],
		        (object)[
			        "name"  => "shipping",
			        "value" => "$shipping"
		        ],
		        (object)[
			        "name"  => "discount",
			        "value" => "$discount"
		        ],
		        (object)[
			        "name"  => "items",
			        "value" => "$items"
		        ],
		        (object)[
			        "name"  => "itemscount",
			        "value" => "$itemsCount"
		        ],
		        (object)[
			        "name"  => "productscount",
			        "value" => "{$order->get_item_count()}"
		        ],
		        (object)[
			        "name"  => "firstname",
			        "value" => "{$order->get_billing_first_name()}"
		        ],
		        (object)[
			        "name"  => "lastname",
			        "value" => "{$order->get_billing_last_name()}"
		        ],
		        (object)[
			        "name"  => "company",
			        "value" => "{$order->get_billing_company()}"
		        ],
		        (object)[
			        "name"  => "address1",
			        "value" => "{$order->get_billing_address_1()}"
		        ],
		        (object)[
			        "name"  => "address2",
			        "value" => "{$order->get_billing_address_2()}"
		        ],
		        (object)[
			        "name"  => "city",
			        "value" => "{$order->get_billing_city()}"
		        ],
		        (object)[
			        "name"  => "state",
			        "value" => "{$order->get_billing_state()}"
		        ],
		        (object)[
			        "name"  => "postcode",
			        "value" => "{$order->get_billing_postcode()}"
		        ],
		        (object)[
			        "name"  => "country",
			        "value" => "{$order->get_billing_country()}"
		        ],
		        (object)[
			        "name"  => "email",
			        "value" => "{$order->get_billing_email()}"
		        ],
		        (object)[
			        "name"  => "phone",
			        "value" => "{$order->get_billing_phone()}"
		        ],
	        ];

            SMSIRAppClass::sendVerifySMS($parameters, $templateID, "{$order->get_billing_phone()}");
        } else {
            $text = str_replace("#orderid#", $orderID, $text);
            $text = str_replace("#newstatus#", $newStatus, $text);
            $text = str_replace("#oldstatus#", $oldStatus, $text);
            $text = str_replace("#trackingcode#", $order->get_order_key(), $text);
            $text = str_replace("#description#", $order->get_customer_note(), $text);
            $text = str_replace("#paymentmethod#", $order->get_payment_method_title(), $text);
            $text = str_replace("#price#", number_format($order->get_total()), $text);
            $text = str_replace("#shipping#", number_format($order->get_shipping_total()), $text);
            $text = str_replace("#discount#", number_format($order->get_discount_total()), $text);
            $text = str_replace("#productscount#", $order->get_item_count(), $text);
            $text = str_replace("#firstname#", $order->get_billing_first_name(), $text);
            $text = str_replace("#lastname#", $order->get_billing_last_name(), $text);
            $text = str_replace("#company#", $order->get_billing_company(), $text);
            $text = str_replace("#address1#", $order->get_billing_address_1(), $text);
            $text = str_replace("#address2#", $order->get_billing_address_2(), $text);
            $text = str_replace("#city#", $order->get_billing_city(), $text);
            $text = str_replace("#state#", $order->get_billing_state(), $text);
            $text = str_replace("#postcode#", $order->get_billing_postcode(), $text);
            $text = str_replace("#country#", $order->get_billing_country(), $text);
            $text = str_replace("#email#", $order->get_billing_email(), $text);
            $text = str_replace("#phone#", $order->get_billing_phone(), $text);

            if (strpos($text, "#items#") !== false) {
                $items = "";
                foreach ($orderItems as $item) {
                    $items .= $item->get_name() . " * " . $item->get_quantity() . PHP_EOL;
                }
                $text = str_replace("#items#", $items, $text);
            }
            if (strpos($text, "#itemscount#") !== false) {
                $text = str_replace("#itemscount#", count($orderItems), $text);
            }

            SMSIRAppClass::sendBulkSMS($text, ["{$order->get_billing_phone()}"]);
        }
    }
}

add_action('woocommerce_new_order', 'smsIrWoocommerceNewOrder', 10, 2);
/**
 * @param $orderID
 * @return void
 */
function smsIrWoocommerceNewOrder($orderID, $order)
{
    if (get_option("sms_ir_woocommerce_new_order_admin_status")) {
        $orderItems = $order->get_items();
        $adminMobile = get_option("sms_ir_info_admin");
        $text = get_option("sms_ir_woocommerce_new_order_admin_text");

        if ($templateID = get_option("sms_ir_woocommerce_new_order_admin_template")) {

	        $price = number_format($order->get_total());

	        $shipping = number_format($order->get_shipping_total());

	        $discount = number_format($order->get_discount_total());

	        $items = "";
	        foreach ($orderItems as $item) {
		        $items .= $item->get_name() . " * " . $item->get_quantity() . PHP_EOL;
	        }

	        $itemsCount = count($orderItems);


            $parameters = [
	            (object)[
		            "name"  => "orderid",
		            "value" => "$orderID"
	            ],
	            (object)[
		            "name"  => "trackingcode",
		            "value" => "{$order->get_order_key()}"
	            ],
	            (object)[
		            "name"  => "description",
		            "value" => "{$order->get_customer_note()}"
	            ],
	            (object)[
		            "name"  => "paymentmethod",
		            "value" => "{$order->get_payment_method_title()}"
	            ],
	            (object)[
		            "name"  => "price",
		            "value" => "$price"
	            ],
	            (object)[
		            "name"  => "shipping",
		            "value" => "$shipping"
	            ],
	            (object)[
		            "name"  => "discount",
		            "value" => "$discount"
	            ],
	            (object)[
		            "name"  => "items",
		            "value" => "$items"
	            ],
	            (object)[
		            "name"  => "itemscount",
		            "value" => "$itemsCount"
	            ],
	            (object)[
		            "name"  => "productscount",
		            "value" => "{$order->get_item_count()}"
	            ],
	            (object)[
		            "name"  => "firstname",
		            "value" => "{$order->get_billing_first_name()}"
	            ],
	            (object)[
		            "name"  => "lastname",
		            "value" => "{$order->get_billing_last_name()}"
	            ],
	            (object)[
		            "name"  => "company",
		            "value" => "{$order->get_billing_company()}"
	            ],
	            (object)[
		            "name"  => "address1",
		            "value" => "{$order->get_billing_address_1()}"
	            ],
	            (object)[
		            "name"  => "address2",
		            "value" => "{$order->get_billing_address_2()}"
	            ],
	            (object)[
		            "name"  => "city",
		            "value" => "{$order->get_billing_city()}"
	            ],
	            (object)[
		            "name"  => "state",
		            "value" => "{$order->get_billing_state()}"
	            ],
	            (object)[
		            "name"  => "postcode",
		            "value" => "{$order->get_billing_postcode()}"
	            ],
	            (object)[
		            "name"  => "country",
		            "value" => "{$order->get_billing_country()}"
	            ],
	            (object)[
		            "name"  => "email",
		            "value" => "{$order->get_billing_email()}"
	            ],
	            (object)[
		            "name"  => "phone",
		            "value" => "{$order->get_billing_phone()}"
	            ]
            ];

            foreach (explode(",", get_option("sms_ir_woocommerce_new_order_admin_mobile")) as $mobile) {
                SMSIRAppClass::sendVerifySMS($parameters, $templateID, "$mobile");
            }
            if ($adminMobile) {
                SMSIRAppClass::sendVerifySMS($parameters, $templateID, "$adminMobile");
            }
        } else {
            $mobiles = explode(',', get_option("sms_ir_woocommerce_new_order_admin_mobile"));
            if ($adminMobile) {
                $mobiles[] = $adminMobile;
            }

            $text = str_replace("#orderid#", $orderID, $text);
            $text = str_replace("#trackingcode#", $order->get_order_key(), $text);
            $text = str_replace("#description#", $order->get_customer_note(), $text);
            $text = str_replace("#paymentmethod#", $order->get_payment_method_title(), $text);
            $text = str_replace("#price#", number_format($order->get_total()), $text);
            $text = str_replace("#shipping#", number_format($order->get_shipping_total()), $text);
            $text = str_replace("#discount#", number_format($order->get_discount_total()), $text);
            $text = str_replace("#productscount#", $order->get_item_count(), $text);
            $text = str_replace("#firstname#", $order->get_billing_first_name(), $text);
            $text = str_replace("#lastname#", $order->get_billing_last_name(), $text);
            $text = str_replace("#company#", $order->get_billing_company(), $text);
            $text = str_replace("#address1#", $order->get_billing_address_1(), $text);
            $text = str_replace("#address2#", $order->get_billing_address_2(), $text);
            $text = str_replace("#city#", $order->get_billing_city(), $text);
            $text = str_replace("#state#", $order->get_billing_state(), $text);
            $text = str_replace("#postcode#", $order->get_billing_postcode(), $text);
            $text = str_replace("#country#", $order->get_billing_country(), $text);
            $text = str_replace("#email#", $order->get_billing_email(), $text);
            $text = str_replace("#phone#", $order->get_billing_phone(), $text);

            if (strpos($text, "#items#") !== false) {
                $items = "";
                foreach ($orderItems as $item) {
                    $items .= $item->get_name() . " * " . $item->get_quantity() . PHP_EOL;
                }
                $text = str_replace("#items#", $items, $text);
            }
            if (strpos($text, "#itemscount#") !== false) {
                $text = str_replace("#itemscount#", count($orderItems), $text);
            }

            SMSIRAppClass::sendBulkSMS($text, $mobiles);
        }
    }

    if (get_option("sms_ir_woocommerce_new_order_user_status")) {
        $orderItems = $order->get_items();
        $adminMobile = get_option("sms_ir_info_admin");
        $text = get_option("sms_ir_woocommerce_new_order_user_text");

        if ($templateID = get_option("sms_ir_woocommerce_new_order_user_template")) {
	        $price = number_format($order->get_total());

	        $shipping = number_format($order->get_shipping_total());

	        $discount = number_format($order->get_discount_total());

	        $items = "";
	        foreach ($orderItems as $item) {
		        $items .= $item->get_name() . " * " . $item->get_quantity() . PHP_EOL;
	        }

	        $itemsCount = count($orderItems);

	        $parameters = [
		        (object)[
			        "name"  => "orderid",
			        "value" => "$orderID"
		        ],
		        (object)[
			        "name"  => "trackingcode",
			        "value" => "{$order->get_order_key()}"
		        ],
		        (object)[
			        "name"  => "description",
			        "value" => "{$order->get_customer_note()}"
		        ],
		        (object)[
			        "name"  => "paymentmethod",
			        "value" => "{$order->get_payment_method_title()}"
		        ],
		        (object)[
			        "name"  => "price",
			        "value" => "$price"
		        ],
		        (object)[
			        "name"  => "shipping",
			        "value" => "$shipping"
		        ],
		        (object)[
			        "name"  => "discount",
			        "value" => "$discount"
		        ],
		        (object)[
			        "name"  => "items",
			        "value" => "$items"
		        ],
		        (object)[
			        "name"  => "itemscount",
			        "value" => "$itemsCount"
		        ],
		        (object)[
			        "name"  => "productscount",
			        "value" => "{$order->get_item_count()}"
		        ],
		        (object)[
			        "name"  => "firstname",
			        "value" => "{$order->get_billing_first_name()}"
		        ],
		        (object)[
			        "name"  => "lastname",
			        "value" => "{$order->get_billing_last_name()}"
		        ],
		        (object)[
			        "name"  => "company",
			        "value" => "{$order->get_billing_company()}"
		        ],
		        (object)[
			        "name"  => "address1",
			        "value" => "{$order->get_billing_address_1()}"
		        ],
		        (object)[
			        "name"  => "address2",
			        "value" => "{$order->get_billing_address_2()}"
		        ],
		        (object)[
			        "name"  => "city",
			        "value" => "{$order->get_billing_city()}"
		        ],
		        (object)[
			        "name"  => "state",
			        "value" => "{$order->get_billing_state()}"
		        ],
		        (object)[
			        "name"  => "postcode",
			        "value" => "{$order->get_billing_postcode()}"
		        ],
		        (object)[
			        "name"  => "country",
			        "value" => "{$order->get_billing_country()}"
		        ],
		        (object)[
			        "name"  => "email",
			        "value" => "{$order->get_billing_email()}"
		        ],
		        (object)[
			        "name"  => "phone",
			        "value" => "{$order->get_billing_phone()}"
		        ]
	        ];


            SMSIRAppClass::sendVerifySMS($parameters, $templateID, "{$order->get_billing_phone()}");
        } else {
            $text = str_replace("#orderid#", $orderID, $text);
            $text = str_replace("#trackingcode#", $order->get_order_key(), $text);
            $text = str_replace("#description#", $order->get_customer_note(), $text);
            $text = str_replace("#paymentmethod#", $order->get_payment_method_title(), $text);
            $text = str_replace("#price#", number_format($order->get_total()), $text);
            $text = str_replace("#shipping#", number_format($order->get_shipping_total()), $text);
            $text = str_replace("#discount#", number_format($order->get_discount_total()), $text);
            $text = str_replace("#productscount#", $order->get_item_count(), $text);
            $text = str_replace("#firstname#", $order->get_billing_first_name(), $text);
            $text = str_replace("#lastname#", $order->get_billing_last_name(), $text);
            $text = str_replace("#company#", $order->get_billing_company(), $text);
            $text = str_replace("#address1#", $order->get_billing_address_1(), $text);
            $text = str_replace("#address2#", $order->get_billing_address_2(), $text);
            $text = str_replace("#city#", $order->get_billing_city(), $text);
            $text = str_replace("#state#", $order->get_billing_state(), $text);
            $text = str_replace("#postcode#", $order->get_billing_postcode(), $text);
            $text = str_replace("#country#", $order->get_billing_country(), $text);
            $text = str_replace("#email#", $order->get_billing_email(), $text);
            $text = str_replace("#phone#", $order->get_billing_phone(), $text);

            if (strpos($text, "#items#") !== false) {
                $items = "";
                foreach ($orderItems as $item) {
                    $items .= $item->get_name() . " * " . $item->get_quantity() . PHP_EOL;
                }
                $text = str_replace("#items#", $items, $text);
            }
            if (strpos($text, "#itemscount#") !== false) {
                $text = str_replace("#itemscount#", count($orderItems), $text);
            }

            SMSIRAppClass::sendBulkSMS($text, ["{$order->get_billing_phone()}"]);
        }
    }
}

add_action('woocommerce_low_stock_notification', 'smsIrWoocommerceStock', 10, 1);
/**
 * @param $product
 * @return void
 */
function smsIrWoocommerceStock($product)
{
    if (get_option("sms_ir_woocommerce_stock_admin_status")) {
        $adminMobile = get_option("sms_ir_info_admin");
        $text = get_option("sms_ir_woocommerce_stock_admin_text");

        if ($templateID = get_option("sms_ir_woocommerce_stock_admin_template")) {
            $parameters = [
	            (object)[
		            "name"  => "id",
		            "value" => "{$product->get_id()}"
	            ],
	            (object)[
		            "name"  => "name",
		            "value" => "{$product->get_name()}"
	            ],
	            (object)[
		            "name"  => "quantity",
		            "value" => "{$product->get_stock_quantity()}"
	            ],
	            (object)[
		            "name"  => "quantity",
		            "value" => "{$product->get_stock_quantity()}"
	            ],
	            (object)[
		            "name"  => "lowstock",
		            "value" => "{$product->get_low_stock_amount()}"
	            ]
            ];
            foreach (explode(",", get_option("sms_ir_woocommerce_stock_admin_mobile")) as $mobile) {
                SMSIRAppClass::sendVerifySMS($parameters, $templateID, "$mobile");
            }
            if ($adminMobile) {
                SMSIRAppClass::sendVerifySMS($parameters, $templateID, "$adminMobile");
            }
        } else {
            $mobiles = explode(',', get_option("sms_ir_woocommerce_stock_admin_mobile"));
            if ($adminMobile) {
                $mobiles[] = $adminMobile;
            }

            $text = str_replace("#id#", $product->get_id(), $text);
            $text = str_replace("#name#", $product->get_name(), $text);
            $text = str_replace("#quantity#", $product->get_stock_quantity(), $text);
            $text = str_replace("#lowstock#", $product->get_low_stock_amount(), $text);

            SMSIRAppClass::sendBulkSMS($text, $mobiles);
        }
    }
}

add_action('woocommerce_product_meta_start', 'smsIrProductInit', 103);
/**
 * @return void
 */
function smsIrProductInit()
{
    include dirname(__FILE__) . "/templates/form.php";
}

add_action('woocommerce_product_set_stock', 'smsIrWoocommerceInventory', 10, 1);
add_action('woocommerce_product_set_stock_status', 'smsIrWoocommerceInventory', 10, 2);
/**
 * @param $product
 * @param $newStock
 * @return void
 */
function smsIrWoocommerceInventory($product, $newStock = null)
{
    if (get_option("sms_ir_woocommerce_inventory_user_status")) {
        if (is_int($product)) {
            $product = wc_get_product($product);
        }

        if ($newStock == "instock") {
            global $wpdb, $table_prefix;
            $notifications = $wpdb->get_results("SELECT `name`, `mobile` FROM `{$table_prefix}sms_ir_app_notifications` WHERE `type` = 'inventory' AND `product_id` = " . $product->get_id());

            if ($templateID = get_option("sms_ir_woocommerce_inventory_user_template")) {
                foreach ($notifications as $notification) {
                    $parameters = [
                        (object)[
                            "name"  => "clientname",
                            "value" => "$notification->name"
                        ],
                        (object)[
                            "name"  => "productid",
                            "value" => "{$product->get_id()}"
                        ],
                        (object)[
                            "name"  => "productname",
                            "value" => "{$product->get_name()}"
                        ],
                        (object)[
                            "name"  => "productsku",
                            "value" => "{$product->get_sku()}"
                        ]
                    ];
                    SMSIRAppClass::sendVerifySMS($parameters, $templateID, "$notification->mobile");
                }
            } else {
                foreach ($notifications as $notification) {
                    $text = get_option("sms_ir_woocommerce_inventory_user_text");
                    $text = str_replace("#clientname#", $notification->name, $text);
                    $text = str_replace("#productid#", $product->get_id(), $text);
                    $text = str_replace("#productname#", $product->get_name(), $text);
                    $text = str_replace("#productsku#", $product->get_sku(), $text);

                    SMSIRAppClass::sendBulkSMS($text, ["$notification->mobile"]);
                }
            }
        }
    }
}

add_action('woocommerce_update_product', 'smsIrWoocommercePromotion', 10, 1);
/**
 * @param $productID
 * @return void
 */
function smsIrWoocommercePromotion($productID)
{
    if (get_option("sms_ir_woocommerce_promotion_user_status")) {
        $product = wc_get_product($productID);

        if ($product->get_price() != $product->get_regular_price()) {
            global $wpdb, $table_prefix;
            $notifications = $wpdb->get_results("SELECT `name`, `mobile` FROM `{$table_prefix}sms_ir_app_notifications` WHERE `type` = 'promotion' AND `product_id` = " . $product->get_id());

            if ($templateID = get_option("sms_ir_woocommerce_promotion_user_template")) {
                foreach ($notifications as $notification) {
                    $parameters = [
                        (object)[
                            "name"  => "clientname",
                            "value" => "$notification->name"
                        ],
                        (object)[
                            "name"  => "productid",
                            "value" => "{$product->get_id()}"
                        ],
                        (object)[
                            "name"  => "productname",
                            "value" => "{$product->get_name()}"
                        ],
                        (object)[
                            "name"  => "productsku",
                            "value" => "{$product->get_sku()}"
                        ],
                        (object)[
                            "name"  => "productprice",
                            "value" => "{$product->get_price()}"
                        ]
                    ];
                    SMSIRAppClass::sendVerifySMS($parameters, $templateID, "$notification->mobile");
                }
            } else {
                foreach ($notifications as $notification) {
                    $text = get_option("sms_ir_woocommerce_promotion_user_text");
                    $text = str_replace("#clientname#", $notification->name, $text);
                    $text = str_replace("#productid#", $product->get_id(), $text);
                    $text = str_replace("#productname#", $product->get_name(), $text);
                    $text = str_replace("#productsku#", $product->get_sku(), $text);
                    $text = str_replace("#productprice#", $product->get_price(), $text);
                    SMSIRAppClass::sendBulkSMS($text, ["$notification->mobile"]);
                }
            }
        }
    }
}
