<?php

namespace PW\PWSMS;

defined( 'ABSPATH' ) || exit;

class About {

    public function __construct() {
        add_action( 'admin_init', [ $this, 'admin_init' ] );
        add_filter( 'pwoosms_settings_sections', [ $this, 'add_section' ], 9999, 1 );
        add_action( 'pwoosms_settings_form_bottom_sms_about', [ $this, 'about_page' ] );
        add_action( 'wp_ajax_pwoosms_hide_about_page', [ $this, 'ajax_callback' ] );
    }

    public static function about_page() { ?>

        <div class="wrap about-wrap">

            <h1>افزونه حرفه ای پیامک ووکامرس</h1>

            <div class="about-text">

                <p>
                    این افزونه به صورت رایگان از سوی
                    <a target="_blank" href="https://woosupport.ir">ووکامرس فارسی</a>
                    ارائه شده است.
                </p>
                <p>با کمک افزونه پیامک ووکامرس میتوانید هر گونه پیامک مربوط به وضعیت سفارش را به مشتریان خود ارسال
                    نمایید.</p>

                <p><img src="<?php echo PWSMS_URL . '/assets/images/tick.png'; ?>"/> طراح افزونه: محمد مجیدی <a
                        href="https://hits.ir/minsta" target="_blank"><img
                            src="<?php echo PWSMS_URL . '/assets/images/instagram-48.png'; ?>"/></a></p>
            </div>

            <div class="wp-badge"
                 style="background: #fff url('<?php echo PWSMS_URL . '/assets/images/logo.png'; ?>'); width: 128px !important; height: 10px !important;"></div>

            <h2 class="nav-tab-wrapper">
                <a href="<?php echo remove_query_arg( [] ); ?>" class="nav-tab nav-tab-active">برخی ویژگی
                    های افزونه</a>
                <a target="_blank" href="https://woosupport.ir" class="nav-tab">سایت ووکامرس فارسی</a>

            </h2>

            <br><br>
            <div class="feature-section two-col">
                <div class="col">
                    <img src="<?php echo PWSMS_URL . '/assets/images/mobile-phone.png'; ?>"/>
                </div>
                <div class="col">
                    <h3>ارسال پیامک بعد از ثبت و یا تغییر وضعیت سفارشات</h3>
                    <p>
                    <ul>
                        <li>بعد از ثبت و یا تغییر وضعیت سفارش به مدیر کل، مشتری (خریدار) و مدیر محصول (فروشنده) به صورت
                            خودکار پیامک
                            ارسال کنید.
                        </li>
                        <li>قابلیت انتخاب وضعیت های سفارش دلخواه برای دریافت پیامک</li>
                        <li>قابلیت انتخاب توسط مشتری برای دریافت و یا عدم دریافت پیامک برای وضعیت های مورد نظر</li>
                        <li>قابلیت شخصی سازی متن های پیامک برای هر وضعیت سفارش</li>
                        <li>قابلیت شخصی سازی متن های پیامک برای مدیر کل، خریدار و فروشنده</li>
                        <li>قابلیت استفاده از شورتکد های متعدد داخل متن پیامک برای ارائه جزییات دقیق تر سفارش</li>
                        <li>قابلیت دریافت پیامک در صورت کم شدن یا ناموجود شدن موجودی انبار هر محصول نیز در این افزونه
                            اضافه شده
                            است.
                        </li>
                    </ul>
                    </p>
                </div>
            </div>


            <div class="feature-section two-col">
                <div class="col">
                    <h3>سیستم خبرنامه پیشرفته محصولات</h3>
                    <p>توسط این امکان میتوانید کاربران سایت خود را در حین موجود شدن محصولات، تخفیف و سایر رویداد های
                        متنوع، از
                        طریق پیامک با خبر
                        نمایید.</p>
                    <p>همچنین قادر خواهید بود که گزینه های دلخواه و مورد نظر خود را بسازید و کاربران را در آن گزینه ها
                        به اشتراک
                        در بیاورید.</p>
                    <br>
                    <p>در نسخه جدید قابلیت مشاهده، افزودن و ویرایش مشترکین خبرنامه نیز اضافه شده است.</p>
                    <br>
                    <p>نحوه نمایش این گزینه ها در صفحه محصولات به سه روش؛ خودکار، شورتکد و ابزارک صورت خواهد گرفت.</p>
                </div>
                <div class="col">
                    <img src="<?php echo PWSMS_URL . '/assets/images/newsletter.jpg'; ?>"/>
                </div>
            </div>


            <div class="feature-section two-col">

                <div class="col">
                    <img src="<?php echo PWSMS_URL . '/assets/images/admin-panel.jpg'; ?>"/>
                </div>

                <div class="col">
                    <h3>پنل تنظیمات ساده</h3>
                    <p>در نسخه جدید پیامک ووکامرس جهت دسترسی سریع به پیامک ووکامرس و یکپارچگی کامل آن با فروشگاه ساز
                        ووکامرس،
                        پنل تنظیمات آن به صورت زیر منوی افزونه ووکامرس در آمده است.</p>
                    <p>همچنین جهت دسترسی سریع تر میتوانید گزینه "پیامک ووکامرس" را در ادمین بار فعال نمایید.</p>
                </div>
            </div>


            <hr>
            <div class="changelog">
                <h3>سایر امکانات افزونه 😊</h3>

                <div class="two-col">
                    <div class="col">
                        <h3>پشتیبانی از وبسرویس های متنوع</h3>
                        <p>افزونه پیامک ووکامرس تا کنون بیش از ۵۰ سامانه پیامکی را تحت پوشش خود قرار داده است. و از اکثر
                            سامانه
                            های پیامکی محبوب پشتیبانی میکند.</p>
                    </div>
                    <div class="col">
                        <h3>ارسال پیامک دسته جمعی</h3>
                        <p>شما از طریق منوی ووکامرس >> سفارشات، میتوانید سفارشات مورد نظر خود را مارک نموده و سپس از
                            طریق ابزار
                            اقدامات دسته جمعی، گزینه ارسال پیامک را انتخاب نمایید و به سفارشات مورد نظر به صورت دسته
                            جمعی پیامک
                            ارسال نمایید.</p>
                    </div>
                    <div class="col">
                        <h3>متاباکس ارسال پیامک</h3>
                        <p>داخل صفحه سفارشات و محصولات (پست تایپ های ووکامرس)، متاباکس ارسال پیامک اضافه خواهد شد که از
                            طریق آن
                            میتوانید به خریدار و یا مشترکین محصولات پیامک ارسال کنید.</p>
                    </div>

                    <div class="col">
                        <h3>مشاهده آرشیو پیامک‌های ارسالی</h3>
                        <p>تمام پیامک‌های در قسمت آرشیو پیامک‌های ارسالی قابل مشاهده خواهند بود تا در صورتی که پیامک‌ها
                            بنا به
                            هر دلیلی با خطا مواجه شدند، جزییات مشکل قابل مشاهده باشند.</p>
                    </div>

                </div>

            </div>


            <div class="changelog under-the-hood feature-list">

                <div class="rating">

                    <h3> و امکانات بی نظیر دیگر ....</h3>

                    <a href="https://wordpress.org/support/plugin/persian-woocommerce-sms/reviews/?rate=5#new-post"
                       target="_blank">
                        <div class="wporg-ratings" data-rating="5" style="color:#ffb900;">
                            <span style="color:#0073aa;">با دادن امتیاز ۵ ستاره به این افزونه، انگیزه ما را در بهبود
                                امکانات بعدی، چند برابر کنید.</span>
                            <span class="dashicons dashicons-star-filled"></span>
                            <span class="dashicons dashicons-star-filled"></span>
                            <span class="dashicons dashicons-star-filled"></span>
                            <span class="dashicons dashicons-star-filled"></span>
                            <span class="dashicons dashicons-star-filled"></span>
                        </div>
                    </a>
                </div>
            </div>
            <hr>

            <div class="changelog under-the-hood feature-list">
                <div class="last-feature">


                    <div class="return-to-dashboard">
                        <a href="<?php echo admin_url( 'admin.php?page=persian-woocommerce-sms-pro' ); ?>">رفتن به
                            پیکربندی
                            &larr; پیامک ووکامرس</a>
                    </div>
                    <br><br>
                    <hr>
                    <p>
                        برای اضافه شدن وبسرویس شرکت خود با ما تماس بگیرید: 05191004490
                    </p>
                </div>
            </div>


            <div class="clear"></div>
        </div>
        <style type="text/css">
            a {
                text-decoration: none !important;
            }

            p {
                line-height: 28px !important;
                text-align: justify;
            }
        </style>
        <script type="text/javascript">
            jQuery(document).on('change', '#pwoosms_hide_about_page', function () {
                jQuery.ajax({
                    url: ajaxurl,
                    data: {
                        action: 'pwoosms_hide_about_page'
                    }
                }).done(function () {
                    window.location = "<?php echo admin_url( 'admin.php?page=persian-woocommerce-sms-pro' ); ?>";
                });
            })
        </script>
    <?php }

    public function add_section( $sections ) {

        if ( ! get_option( 'pwoosms_hide_about_page' ) ) {
            $sections[] = [
                'id'       => 'sms_about',
                'title'    => 'درباره',
                'form_tag' => false,
            ];
        }

        return $sections;
    }

    public function admin_init() {
        if ( ! get_option( 'pwoosms_redirect_about_page' ) ) {

            delete_option( 'pwoosms_hide_about_page' );
            update_option( 'pwoosms_redirect_about_page', '10' );

            if ( ! headers_sent() ) {
                wp_redirect( admin_url( 'admin.php?page=persian-woocommerce-sms-pro&tab=about' ) );
                exit();
            }
        }
    }

    public function ajax_callback() {
        update_option( 'pwoosms_hide_about_page', '0' );
        die();
    }
}

