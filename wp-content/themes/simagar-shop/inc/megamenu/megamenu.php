<?php
class Simagar_Megamenu
{
    public function __construct(){
        add_action('wp_nav_menu_item_custom_fields', [$this, 'simagar_add_nav_menu_custom'], 10, 5);
        add_action('wp_update_nav_menu_item', [$this, 'simagar_update_nav_menu_item'], 10, 3);
        add_filter('wp_setup_nav_menu_item', [$this, 'simagar_setup_menu_item']);
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
                <?php $mega_menu_list = $this->get_megamenu(); ?>
                <?php if($depth == 0): ?>
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
}
new Simagar_Megamenu();