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
require_once dirname(__FILE__) . "/../SMSIRAppClass.php";

$url = plugins_url('/assets/', __FILE__);
wp_enqueue_style('smsIr', $url . 'css/smsir.css', true, 103);
wp_enqueue_style('dataTable', $url . 'css/jquery.dataTables.min.css', true, 103);
wp_enqueue_script('smsIr', $url . 'js/smsir.js', true, 103);
wp_enqueue_script('dataTable', $url . 'js/jquery.dataTables.min.js', true, 103);

$data = SMSIRAppClass::getLog();
?>

<div class="sms-ir-header-div">
	<h1>
		<img width="100px" src="<?= plugin_dir_url(__FILE__) . 'assets/img/logo.png' ?>">
		لاگ های خطا
	</h1>
</div>
<?php if (!get_option('sms_ir_info_api_key')) { ?>
	<div class="error">
		<p>متاسفانه تنظیمات مربوط به حساب پیامکی شما انجام نشده است.</p>
	</div>
<?php } ?>
<div class="row sms-ir-main-div sms-ir-list-div" id="smsIrTabContent">
    <p>لیست زیر نمایش دهنده تمامی لاگ های موجود در سیستم می باشد.
    <hr>
	<div class="table-responsive">
		<table id="dataTableLog" class="table table-bordered table-hover">
			<thead>
			<tr>
				<th>ردیف</th>
				<th>کد</th>
				<th>عنوان</th>
				<th>زمان</th>
				<th>پیام</th>
			</tr>
			</thead>
			<tbody>
			<?php foreach ($data as $key => $d) { ?>
				<tr>
                    <td><?= $key + 1 ?></td>
                    <td><?= $d->status?></td>
                    <td><?= $d->title?></td>
                    <td><?= $d->created_at?></td>
                    <td><?= $d->message?></td>
				</tr>
			<?php } ?>
			</tbody>
			<tfoot>
			<tr>
				<th>ردیف</th>
				<th>کد</th>
				<th>عنوان</th>
				<th>زمان</th>
				<th>پیام</th>
			</tr>
			</tfoot>
		</table>
	</div>
    <script>
        jQuery(document).ready(function () {
            jQuery("#dataTableLog").DataTable({
            aaSorting: [[0, "asc"]],
            language: {
                emptyTable: "هیچ داده‌ای در جدول وجود ندارد",
                info: "نمایش _START_ تا _END_ از _TOTAL_ ردیف",
                infoEmpty: "نمایش 0 تا 0 از 0 ردیف",
                infoFiltered: "(فیلتر شده از _MAX_ ردیف)",
                infoThousands: ",",
                lengthMenu: "نمایش _MENU_ ردیف",
                processing: "در حال پردازش...",
                search: "جستجو:",
                zeroRecords: "رکوردی با این مشخصات پیدا نشد",
                paginate: { first: "برگه‌ی نخست", last: "برگه‌ی آخر", next: "بعدی", previous: "قبلی" },
                aria: { sortAscending: ": فعال سازی نمایش به صورت صعودی", sortDescending: ": فعال سازی نمایش به صورت نزولی" },
                loadingRecords: "در حال بارگذاری...",
            },
        });
        })
    </script>
    <style>
        td {
            text-align: center;
        }
    </style>
</div>
