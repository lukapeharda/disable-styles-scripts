<?php
/*
Plugin Name: Disable Styles & Scripts
Plugin URI: http://enfinita.com
Description: Disables selected plugin (and/or theme) styles and scripts. Conditions can be set which will be evaluated.
Version: 1.0.0
Author: Luka Peharda
Author URI: http://enfinita.com
*/

class Disable_Styles_Scripts
{
    /**
     * @var Disable_Styles_Scripts
     */
    protected static $instance;

    /**
     * Registering actions and filters
     */
    protected function __construct()
    {
        add_action('admin_menu', array($this, 'registerAdminPage'));
        add_action('init', array($this, 'loadTextdomain'));
        add_action('wp_print_scripts', array($this, 'removeScripts'));
        add_action('wp_print_styles', array($this, 'removeStyles'));
    }

    /**
     * Singleton
     * @return Disable_Styles_Scripts
     */
    public static function getInstance()
    {
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Loads plugin translation files
     * @return void
     */
    public function loadTextdomain()
    {
        $locale = apply_filters('plugin_locale', get_locale(), 'disable-styles-scripts');

        load_textdomain('disable-styles-scripts', WP_LANG_DIR . '/plugins/disable-styles-scripts-' . $locale . '.mo');
        load_plugin_textdomain('disable-styles-scripts', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    /**
     * Adds menu page, in network admin only
     * @return void
     */
    public function registerAdminPage()
    {
        add_options_page(__('Disable Styles & Scripts', 'disable-styles-scripts'), __('Disable S&S', 'disable-styles-scripts'), 'manage_options', 'disable-styles-scripts', array($this, 'displayAdminPage'));
    }

    /**
     * Site cloner page logic
     * @return void
     */
    public function displayAdminPage()
    {
        /*
         * Lets go ahead and create a new blog
         */
        if (isset($_POST['save-changes'])) {
            check_admin_referer('disable-styles-scripts', '_wpnonce_disable-styles-scripts');

            /*
             * Saving plugin CSS list
             */
            if (isset($_POST['e6n_disable_plugin_css'])) {
                update_option('e6n_disable_plugin_css', $_POST['e6n_disable_plugin_css']);
            } else {
                delete_option('e6n_disable_plugin_css');
            }

            /*
             * Saving plugin JS list
             */
            if (isset($_POST['e6n_disable_plugin_js'])) {
                update_option('e6n_disable_plugin_js', $_POST['e6n_disable_plugin_js']);
            } else {
                delete_option('e6n_disable_plugin_js');
            }

            /*
             * Saving theme CSS list
             */
            if (isset($_POST['e6n_disable_theme_css'])) {
                update_option('e6n_disable_theme_css', $_POST['e6n_disable_theme_css']);
            } else {
                delete_option('e6n_disable_theme_css');
            }

            /*
             * Saving theme JS list
             */
            if (isset($_POST['e6n_disable_theme_js'])) {
                update_option('e6n_disable_theme_js', $_POST['e6n_disable_theme_js']);
            } else {
                delete_option('e6n_disable_theme_js');
            }

            /*
             * Saving conditional logic
             */
            if (isset($_POST['e6n_conditional_logic'])) {
                update_option('e6n_conditional_logic', $_POST['e6n_conditional_logic']);
            } else {
                delete_option('e6n_conditional_logic');
            }

            /*
             * Setting success message
             */
            $messages = array(
                __('Changes saved.', 'disable-styles-scripts'),
            );
        }

        require_once plugin_dir_path(__FILE__) . 'views/admin.php';
    }

    /**
     * Deregister plugins and theme scripts
     * @return void
     */
    public function removeScripts()
    {
        if (is_admin()) {
            return;
        }

        $conditionalLogic = get_option('e6n_conditional_logic');
        /*
         * Check conditional logic
         */
        if (!empty($conditionalLogic) && !eval('return (' . $conditionalLogic . ');')) {
            return;
        }

        $js         = get_option('e6n_disable_plugin_js');
        $themeJs    = (int) get_option('e6n_disable_theme_js');
        if (is_array($js) || $themeJs) {
            global $wp_scripts;
            /*
             * Traversing through collection of all registered scripts
             */
            foreach ($wp_scripts->registered as $key => $dependency) {
                if (is_array($js) && count($js) > 0) {
                    /*
                     * Traversing through collection of "unwanted" plugins
                     */
                    foreach ($js as $script) {
                        if (strpos($dependency->src, 'plugins/' . $script . '/')) {
                            wp_deregister_script($key);
                        }
                    }
                }
                /*
                 * Deregistering theme JS files if we are using plugin and if necessary checkbox is checked
                 */
                if ($themeJs === 1 && strpos($dependency->src, 'themes')) {
                    // wp_deregister_script($key);
                }
            }
        }
    }

    /**
     * Deregister plugins and theme styles
     * @return void
     */
    public function removeStyles()
    {
        if (is_admin()) {
            return;
        }

        $conditionalLogic = get_option('e6n_conditional_logic');
        /*
         * Check conditional logic
         */
        if (!empty($conditionalLogic) && !eval('return (' . $conditionalLogic . ');')) {
            return;
        }

        $css        = get_option('e6n_disable_plugin_css');
        $themeCss   = (int) get_option('e6n_disable_theme_css');
        if (is_array($css) || $themeCss) {
            global $wp_styles;
            /*
             * Traversing through collection of all registered styles
             */
            foreach ($wp_styles->registered as $key => $dependency) {
                if (is_array($css) && count($css) > 0) {
                    /*
                     * Traversing through collection of "unwanted" plugins
                     */
                    foreach ($css as $style) {
                        if (strpos($dependency->src, 'plugins/' . $style . '/')) {
                            wp_deregister_style($key);
                        }
                    }
                }
                /*
                 * Deregistering theme CSS files if we are using plugin and if necessary checkbox is checked
                 */
                if ($themeCss === 1 && strpos($dependency->src, '/themes/')) {
                    wp_deregister_style($key);
                }
            }
        }
    }
}
add_action('plugins_loaded', array('Disable_Styles_Scripts', 'getInstance'));