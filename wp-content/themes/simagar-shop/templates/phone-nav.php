<?php
    $menu = wp_nav_menu([
        'theme_location' => 'phone-menu',
        'echo' => false,
    ]);
    
?>
<div class="phone-menu" > 
    <div class="body-menu">
        <?php echo $menu ?>
    </div>
</div>