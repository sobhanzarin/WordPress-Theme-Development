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
date_default_timezone_set('Asia/Tehran');

require_once dirname(__FILE__) . "/../SMSIRAppClass.php";

$url = plugins_url('/assets/', __FILE__);
wp_enqueue_style('smsIr', $url . 'css/smsir.css', true, 103);
wp_enqueue_style('dataTable', $url . 'css/jquery.dataTables.min.css', true, 103);
wp_enqueue_script('smsIr', $url . 'js/smsir.js', true, 103);
wp_enqueue_script('dataTable', $url . 'js/jquery.dataTables.min.js', true, 103);

$promotion_data = SMSIRAppClass::getPromotionLog();
?>

<div class="sms-ir-header-div">
	<h1>
		<img width="100px" src="<?= plugin_dir_url(__FILE__) . 'assets/img/logo.png' ?>">
		لیست تخفیف دار شد خبرم کن
	</h1>
</div>

<div class="row sms-ir-main-div sms-ir-list-div" id="smsIrTabContent">
	<p>لیست زیر نمایش دهنده اطلاعات تمامی کاربرانی است که در فرم "تخفیف دار شد خبرم کن" ثبت نام کرده اند.</p>
	<hr>
	<div class="table-responsive">
		<table id="dataTable" class="table table-bordered table-hover">
			<thead>
			<tr>
                <th>ردیف</th>
				<th>شناسه</th>
				<th>
                    <object data="<?= plugin_dir_url(__FILE__) ?>assets/img/svg/info.svg"></object>
                    شناسه محصول
                    <p style="display: none">شما میتوانید با کلیک بر روی شناسه محصول جزئیات محصول را مشاهده نمایید.</p>
                </th>
				<th>
                    <object data="<?= plugin_dir_url(__FILE__) ?>assets/img/svg/info.svg"></object>
                    نام محصول
                    <p style="display: none">شما میتوانید با کلیک بر روی نام محصول جزئیات محصول را مشاهده نمایید.</p>
                </th>
				<th>نام کاربر</th>
				<th>شماره کاربر</th>
			</tr>
			</thead>
			<tbody>
			<?php
                $i = 1;
                foreach ($promotion_data as $item) { ?>
                    <tr>
                        <td><?= $i ?></td>
                        <td><?= $item->id ?></td>
                        <td><a href="/wp-admin/post.php?post=17&action=edit" target="_blank"><?= $item->ID ?></a></td>
                        <td><a href="/wp-admin/post.php?post=17&action=edit" target="_blank"><?= $item->post_title ?></a></td>
                        <td><?= $item->name ?></td>
                        <td><?= $item->mobile ?></td>
                    </tr>
			<?php
                    $i++;
                }
            ?>
			</tbody>
			<tfoot>
			<tr>
				<th>ردیف</th>
				<th>شناسه</th>
				<th>شناسه محصول</th>
				<th>نام محصول</th>
				<th>نام کاربر</th>
				<th>شماره کاربر</th>
			</tr>
			</tfoot>
		</table>
	</div>
</div>
