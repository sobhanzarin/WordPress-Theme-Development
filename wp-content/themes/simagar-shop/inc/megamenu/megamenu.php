<?php
class Simagar_Megamenu
{
    public function __construct(){
        add_action('wp_nav_menu_item_custom_fields', [$this, 'simagar_add_nav_menu_custom'], 10, 5);
        add_action('wp_update_nav_menu_item', [$this, 'simagar_update_nav_menu_item'], 10, 3);
        add_filter('wp_setup_nav_menu_item', [$this, 'simagar_setup_menu_item']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
        add_filter('walker_nav_menu_start_el', [$this, 'add_menu_icon_to_front'], 10, 4);
    }
    public function simagar_setup_menu_item($menu_item)
    {
        $menu_item->megamenu = get_post_meta($menu_item->ID, '_menu_megamenu', true); 
        $menu_item->submegamenu = get_post_meta($menu_item->ID, '_menu_submegamenu', true); 
        $menu_item->menuisfullwidth = get_post_meta($menu_item->ID, '_menu_is_fullwidth', true);
        return $menu_item;
    }
    public function simagar_update_nav_menu_item($menu_id, $menu_item_db_id)
    {
        if(isset($_POST['menu-megamenu'][$menu_item_db_id])){
            update_post_meta($menu_item_db_id, '_menu_megamenu', 1);
        }else{
            update_post_meta($menu_item_db_id, '_menu_megamenu', 0); 
        }
        if (isset($_POST['menu-item-icon'][$menu_item_db_id])) {
            $icon_id = intval($_POST['menu-item-icon'][$menu_item_db_id]);
            update_post_meta($menu_item_db_id, '_menu_item_icon', $icon_id);
        } else {
            delete_post_meta($menu_item_db_id, '_menu_item_icon');
        }

        if(isset($_POST['menu-submegamenu'][$menu_item_db_id])){
            update_post_meta($menu_item_db_id, '_menu_submegamenu', $_REQUEST['menu-submegamenu'][$menu_item_db_id]);
        }

        if(isset($_POST['menu-is-fullwidth'][$menu_item_db_id])){
            update_post_meta($menu_item_db_id, '_menu_is_fullwidth', 1);
        }else{
            update_post_meta($menu_item_db_id, '_menu_is_fullwidth', 0);
        }
    }
    public function simagar_add_nav_menu_custom($item_id, $item, $depth, $args){
        ?>
            <div>
                <?php $mega_menu_list = $this->get_megamenu(); 
                $icon_id = get_post_meta($item_id, '_menu_item_icon', true);
                $icon_url = $icon_id ? wp_get_attachment_url($icon_id) : '';
                ?>
                <?php if($depth == 0): ?>
                <div class="menu-item-icon-field" style="margin-top:10px;">
                    <label for="edit-menu-item-icon-<?php echo esc_attr($item_id); ?>">
                            آیکن منو:
                            <input type="hidden" class="menu-item-icon-id" 
                                name="menu-item-icon[<?php echo esc_attr($item_id); ?>]" 
                                value="<?php echo esc_attr($icon_id); ?>">
                            <img class="menu-item-icon-preview" 
                                src="<?php echo esc_url($icon_url); ?>" 
                                style="max-width:50px;display:<?php echo $icon_url ? 'inline-block' : 'none'; ?>;margin:5px 0;">
                            <button class="button select-menu-icon">انتخاب آیکن</button>
                            <button class="button remove-menu-icon" style="display:<?php echo $icon_url ? 'inline-block' : 'none'; ?>;">حذف</button>
                        </label>
                    </div>
                    <h4>تنظیمات مگامنو</h4>
                    <div>
                        <label for="edit-menu-megamenu-<?php echo $item_id ?>">فعالسازی مگامنو</label>
                        <input <?php echo checked(!empty($item->megamenu), 1, false); ?> type="checkbox" id="edit-menu-megamenu-<?php echo $item_id ?>" class="edit-menu-megamenu" name="menu-megamenu[<?php echo $item_id?>]" value="1">
                    </div>
                    <div>
                        <label for="">انتخاب مگامنو</label>
                        <select name="menu-submegamenu[<?php echo $item_id ?>]" id="edit-menu-submegamenu-<?php echo $item_id ?>">
                            <?php foreach ($mega_menu_list as $key => $value) : ?>
                                <?php if($key == $item->submegamenu ): ?>
                                     <option selected value="<?php echo $key ?>"><?php echo $value?></option>
                                <?php else: ?>
                                    <option value="<?php echo $key ?>"><?php echo $value?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                     <div>
                        <label for="edit-menu-is-fullwidth-<?php echo $item_id ?>">مگامنو تمام عرض</label>
                        <input <?php echo checked(!empty($item->menuisfullwidth), 1, false); ?> type="checkbox" id="edit-menu-is-fullwidth-<?php echo $item_id ?>" class="edit-menu-is-fullwidth" name="menu-is-fullwidth[<?php echo $item_id?>]" value="1">
                    </div>
                <?php endif; ?>
            </div>
        <?php
    } 
    public function get_megamenu()
    {
        $menus=[];
        $lists = get_posts(['posts_per_page' => -1 , "post_type" => 'simagarmegamenu']);
        foreach ($lists as $menu) {
            $menus[$menu->ID] = $menu->post_title;
        }
        return $menus;
    }
    public function enqueue_admin_scripts($hook)
    {
       // فقط در صفحه‌ی ویرایش منوها لود بشه
    if ($hook !== 'nav-menus.php') {
        return;
    }

    // کتابخانه media uploader وردپرس
    wp_enqueue_media();

    // اسکریپت آیکن منو
    wp_enqueue_script(
        'simagar-megamenu-admin',
        get_template_directory_uri() . '/assets/js/megamenu.js',
        ['jquery'],
        '1.0.0',
        true
    );
    }
    public function add_menu_icon_to_front($item_output, $item, $depth, $args)
    {
    // فقط برای آیتم‌های سطح اول (اصلی)
    if ($depth === 0) {
        $icon_id = get_post_meta($item->ID, '_menu_item_icon', true);
        if ($icon_id) {
            $icon_url = wp_get_attachment_url($icon_id);
            if ($icon_url) {
                // اضافه کردن تصویر قبل از عنوان منو
                $icon_html = '<img class="menu-icon" src="' . esc_url($icon_url) . '" alt="' . esc_attr($item->title) . '">';
                $item_output = preg_replace('/(<a[^>]*>)(.*?)(<\/a>)/', '$1' . $icon_html . '$2$3', $item_output);
            }
        }
    }
    return $item_output;
}

}
new Simagar_Megamenu();