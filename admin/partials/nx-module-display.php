<?php 
    $module_on = isset( $active_modules[ $module_key ] ) ? $active_modules[ $module_key ] : false;
    if( is_array( $module ) ) {
        if( isset( $module['link'] ) && ! empty( $module['link'] ) ) {
            $module_title = sprintf('%2$s<a rel="nofollow" target="_blank" href="%1$s"><img width="6px" src="%3$s" alt="%2$s"/></a>', $module['link'], $module['title'], NOTIFICATIONX_ADMIN_URL . 'assets/img/icons/question.svg');
        } else {
            $module_title = $module['title'];
        }
    } else {
        $module_title = $module;
    }
    if( ! is_array( $module ) ) :
        ?>
            <div class="nx-checkbox" data-id="<?php echo $module_key; ?>">
                <input type="checkbox" <?php checked( $module_on, true ); ?> id="<?php echo $module_key; ?>" name="<?php echo $module_key; ?>">
                <label for="<?php echo $module_key; ?>"></label>
                <p class="nx-module-title"><?php echo $module_title; ?></p>
            </div>
        <?php
    else :
        if( ! empty( $module['title'] ) ) : 
            $is_pro_module = isset( $module['is_pro'] ) && $module['is_pro'] == true ? true : false;
        ?>
            <div class="nx-checkbox <?php echo $is_pro_module ? 'nx-pro-checkbox' : ''; ?>" data-id="<?php echo $module_key; ?>">
                <input <?php echo $is_pro_module == true ? 'disabled' : ''; ?> type="checkbox" <?php checked( $module_on, true ); ?> id="<?php echo $module_key; ?>" name="<?php echo $module_key; ?>">
                <label for="<?php echo $module_key; ?>"></label>
                <p class="nx-module-title"><?php echo $module_title; ?><?php echo $is_pro_module ? '<sup class="pro-label">Pro</sup>' : ''; ?></p>
            </div>
        <?php
        endif;
    endif;
?>