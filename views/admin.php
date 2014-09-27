<div class="wrap">
    <h2 id="add-new-site"><?php echo esc_html(get_admin_page_title()); ?></h2>
    <?php
    if (!empty($messages)) {
        foreach ($messages as $msg) {
            echo '<div id="message" class="updated"><p>' . $msg . '</p></div>';
        }
    }
    $activeTheme    = wp_get_theme();
    $plugins        = get_plugins();
    ?>
    <form method="post" action="<?php echo admin_url('options-general.php?page=disable-styles-scripts&action=save-changes'); ?>">
    <?php wp_nonce_field('disable-styles-scripts', '_wpnonce_disable-styles-scripts') ?>
    <table class="form-table">
        <tr class="form-required">
            <th scope="row"><?php _e('Active plugins', 'disable-styles-scripts'); ?></th>
            <td>
                <table class="wp-list-table widefat fixed">
                    <thead>
                        <tr>
                            <th scope="col" style="padding-left: 10px;"><?php _e('Plugin name', 'disable-styles-scripts'); ?></th>
                            <th scope="col" style="padding-left: 10px; width: 5%; text-align: center;"><?php _e('CSS', 'disable-styles-scripts'); ?></th>
                            <th scope="col" style="padding-left: 10px; width: 5%; text-align: center;"><?php _e('JS', 'disable-styles-scripts'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $alternate = true; foreach ($plugins as $pluginId => $plugin) : if (!is_plugin_active($pluginId)) { continue; } ?>
                        <tr<?php if ($alternate) echo ' class="alternate"'; ?>>
                            <?php $pluginId = substr($pluginId, 0, strpos($pluginId, '/')); ?>
                            <td><?php echo $plugin['Name']; ?></td>
                            <td scope="row" style="text-align: center;"><input type="checkbox" name="e6n_disable_plugin_css[]" value="<?php echo esc_attr($pluginId); ?>"<?php checked(true, is_array(get_option('e6n_disable_plugin_css')) && in_array($pluginId, get_option('e6n_disable_plugin_css'))); ?> /></td>
                            <td scope="row" style="text-align: center;"><input type="checkbox" name="e6n_disable_plugin_js[]" value="<?php echo esc_attr($pluginId); ?>"<?php checked(true, is_array(get_option('e6n_disable_plugin_js')) && in_array($pluginId, get_option('e6n_disable_plugin_js'))); ?> /></td>
                        </tr>
                    <?php $alternate = !$alternate; endforeach; ?>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr class="form-required">
            <th scope="row"><?php _e('Active theme', 'disable-styles-scripts') ?></th>
            <td>
                <table class="wp-list-table widefat fixed">
                    <thead>
                        <tr>
                            <th scope="col" style="padding-left: 10px;"><?php _e('Theme name', 'disable-styles-scripts'); ?></th>
                            <th scope="col" style="padding-left: 10px; width: 5%; text-align: center;"><?php _e('CSS', 'disable-styles-scripts'); ?></th>
                            <th scope="col" style="padding-left: 10px; width: 5%; text-align: center;"><?php _e('JS', 'disable-styles-scripts'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="alternate">
                            <td><?php echo $activeTheme->Name; ?></td>
                            <td scope="row" style="text-align: center;"><input type="hidden" name="e6n_disable_theme_css" value="0" /><input type="checkbox" name="e6n_disable_theme_css" value="1"<?php checked(1, get_option('e6n_disable_theme_css')); ?> /></td>
                            <td scope="row" style="text-align: center;"><input type="hidden" name="e6n_disable_theme_js" value="0" /><input type="checkbox" name="e6n_disable_theme_js" value="1"<?php checked(1, get_option('e6n_disable_theme_js')); ?> /></td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr class="form-required">
            <th scope="row"><?php _e('Conditional logic', 'disable-styles-scripts'); ?></th>
            <td>
                <p><input type="text" class="widefat" name="e6n_conditional_logic" value="<?php echo get_option('e6n_conditional_logic'); ?>" /></p>
                <p><?php _e('Enter some WordPress (or your custom) conditional logic, like <code>is_single() || is_page()</code> or <code>is_single() && in_category(X)</code>.', 'disable-styles-scripts'); ?></p>
            </td>
        </tr>
    </table>
    <?php submit_button(__('Save changes'), 'primary', 'save-changes'); ?>
    </form>
</div>