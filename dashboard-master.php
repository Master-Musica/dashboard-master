<?php
/*
Plugin Name: Dashboard Master - Clean and Custom Dashboard Multisite
Plugin URI: https://www.mastermusica.com.br
Description: Widget manager. 2 fixed global blocks (Super Admin) and up to 6 local blocks. Fixes for videos and embeds.
Version: 9.3
Author: Master Musica
Author URI: https://www.mastermusica.com.br
License: GPLv2 or later
Text Domain: dashboard-master
Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ==============================================================================
// LOAD TEXT DOMAIN (TRANSLATIONS)
// ==============================================================================
add_action( 'plugins_loaded', 'dashboard_master_load_textdomain' );
function dashboard_master_load_textdomain() {
    load_plugin_textdomain( 
        'dashboard-master', 
        false, 
        dirname( plugin_basename( __FILE__ ) ) . '/languages/' 
    );
}

// ==============================================================================
// YOUTUBE ERROR 153 FIX
// ==============================================================================
add_action( 'admin_head', 'dashboard_master_fix_youtube_error_153', 1 );
function dashboard_master_fix_youtube_error_153() {
    echo '<meta name="referrer" content="strict-origin-when-cross-origin">' . "\n";
}

// ==============================================================================
// 1. VISUAL WIDGET MANAGER
// ==============================================================================

add_action( 'admin_menu', 'dashboard_master_add_menu' );
function dashboard_master_add_menu() {
    add_menu_page(
        __( 'Dashboard Master - Widget Manager', 'dashboard-master' ),
        __( 'Dashboard Master', 'dashboard-master' ),
        'manage_options', 
        'dashboard-widget-manager', 
        'dashboard_master_render_page', 
        'dashicons-screenoptions',
        2
    );
}

add_action( 'admin_init', 'dashboard_master_save_settings' );
function dashboard_master_save_settings() {
    if ( isset( $_POST['dashboard_master_nonce'] ) && wp_verify_nonce( $_POST['dashboard_master_nonce'], 'dashboard_master_save' ) ) {
        
        if ( ! current_user_can( 'manage_options' ) ) return;

        $allowed_html = wp_kses_allowed_html( 'post' );
        $allowed_html['iframe'] = array(
            'src' => true, 'height' => true, 'width' => true, 'frameborder' => true,
            'allowfullscreen' => true, 'allow' => true, 'title' => true, 'style' => true,
            'class' => true, 'id' => true, 'data-secret' => true, 'referrerpolicy' => true,
        );

        // GLOBAL SAVING
        if ( is_super_admin() ) {
            if ( isset( $_POST['widget_welcome'] ) ) {
                $welcome_raw = wp_unslash( $_POST['widget_welcome'] );
                $welcome_clean = current_user_can( 'unfiltered_html' ) ? $welcome_raw : wp_kses( $welcome_raw, $allowed_html );
                update_site_option( 'dashboard_global_welcome', $welcome_clean );
            }
            if ( isset( $_POST['widget_global_secondary'] ) ) {
                $global_2_raw = wp_unslash( $_POST['widget_global_secondary'] );
                $global_2_clean = current_user_can( 'unfiltered_html' ) ? $global_2_raw : wp_kses( $global_2_raw, $allowed_html );
                update_site_option( 'dashboard_global_secondary', $global_2_clean );
            }
        }

        // LOCAL SAVING
        $sanitized_widgets = array();
        if ( isset( $_POST['custom_widgets'] ) && is_array( $_POST['custom_widgets'] ) ) {
            foreach ( $_POST['custom_widgets'] as $widget ) {
                if ( count( $sanitized_widgets ) >= 6 ) break;

                $title = sanitize_text_field( wp_unslash( $widget['title'] ) );
                $content_raw = wp_unslash( $widget['content'] ); 
                $content_clean = current_user_can( 'unfiltered_html' ) ? $content_raw : wp_kses( $content_raw, $allowed_html ); 
                
                $allowed_roles = isset( $widget['allowed_roles'] ) && is_array( $widget['allowed_roles'] ) 
                                    ? array_map( 'sanitize_text_field', $widget['allowed_roles'] ) 
                                    : array();
                
                if ( ! empty( $title ) || ! empty( $content_clean ) ) {
                    $sanitized_widgets[] = array( 
                        'title' => $title, 
                        'content' => $content_clean,
                        'allowed_roles' => $allowed_roles
                    );
                }
            }
        }

        if ( isset( $_POST['add_new_block'] ) && $_POST['add_new_block'] === '1' ) {
            if ( count( $sanitized_widgets ) < 6 ) {
                $sanitized_widgets[] = array( 'title' => '', 'content' => '', 'allowed_roles' => array('all') );
                update_option( 'dashboard_local_widgets', $sanitized_widgets );
                wp_redirect( add_query_arg( 'added', 'true', remove_query_arg( array('saved', 'limit_reached'), wp_get_referer() ) ) );
                exit;
            } else {
                wp_redirect( add_query_arg( 'limit_reached', 'true', remove_query_arg( array('saved', 'added'), wp_get_referer() ) ) );
                exit;
            }
        }

        update_option( 'dashboard_local_widgets', $sanitized_widgets );
        wp_redirect( add_query_arg( 'saved', 'true', remove_query_arg( array('added', 'limit_reached'), wp_get_referer() ) ) );
        exit;
    }
}

function dashboard_master_render_page() {
    $widgets = get_option( 'dashboard_local_widgets', array() );
    $widget_count = is_array( $widgets ) ? count( $widgets ) : 0;
    
    global $wp_roles;
    if ( ! isset( $wp_roles ) ) { $wp_roles = new WP_Roles(); }
    $all_roles = $wp_roles->get_names();
    
    $default_welcome = '<div style="padding: 10px;"><h3>' . __( 'Hello! Need help with your site?', 'dashboard-master' ) . '</h3><p>' . __( 'This is your dashboard.', 'dashboard-master' ) . '</p></div>';
    $default_global_2 = '<div style="padding: 10px;"><h3>' . __( 'Important Notices', 'dashboard-master' ) . '</h3><p>' . __( 'Space reserved for the network.', 'dashboard-master' ) . '</p></div>';

    $welcome_content = get_site_option( 'dashboard_global_welcome', $default_welcome );
    $global_2_content = get_site_option( 'dashboard_global_secondary', $default_global_2 );
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Dashboard Widget Manager', 'dashboard-master' ); ?></h1>
        <p><?php esc_html_e( 'Customize the content displayed on your admin dashboard homepage.', 'dashboard-master' ); ?></p>

        <?php if ( isset( $_GET['saved'] ) ) : ?>
            <div class="updated notice is-dismissible"><p><strong><?php esc_html_e( 'Widgets updated successfully!', 'dashboard-master' ); ?></strong></p></div>
        <?php endif; ?>
        <?php if ( isset( $_GET['added'] ) ) : ?>
            <div class="updated notice is-dismissible" style="border-left-color: #46b450;"><p><strong><?php esc_html_e( 'New block added at the bottom of the page! Fill it out below.', 'dashboard-master' ); ?></strong></p></div>
        <?php endif; ?>
        <?php if ( isset( $_GET['limit_reached'] ) ) : ?>
            <div class="error notice is-dismissible"><p><strong><?php esc_html_e( 'Limit reached:', 'dashboard-master' ); ?></strong> <?php esc_html_e( 'You cannot create more than 6 local blocks.', 'dashboard-master' ); ?></p></div>
        <?php endif; ?>

        <form method="post" action="" id="form-widget-manager">
            <?php wp_nonce_field( 'dashboard_master_save', 'dashboard_master_nonce' ); ?>

            <div style="display: flex; flex-wrap: wrap; gap: 20px; margin-top: 20px;">
                <?php if ( is_super_admin() ) : ?>
                    <div class="card" style="flex: 1; min-width: 300px; padding: 15px 20px; border-left: 4px solid #d63638; margin: 0;">
                        <h2><?php esc_html_e( 'Global Block 1', 'dashboard-master' ); ?> </h2>
                        <?php wp_editor( $welcome_content, 'widget_welcome', array( 'textarea_rows' => 8, 'media_buttons' => true ) ); ?>
                    </div>
                    <div class="card" style="flex: 1; min-width: 300px; padding: 15px 20px; border-left: 4px solid #d63638; margin: 0;">
                        <h2><?php esc_html_e( 'Global Block 2', 'dashboard-master' ); ?> </h2>
                        <?php wp_editor( $global_2_content, 'widget_global_secondary', array( 'textarea_rows' => 8, 'media_buttons' => true ) ); ?>
                    </div>
                <?php else : ?>
                    <div class="card" style="flex: 1; min-width: 300px; padding: 15px 20px; background: #f6f7f7; margin: 0;">
                        <h2><?php esc_html_e( 'Global Block 1', 'dashboard-master' ); ?> </h2>
                        <div style="border: 1px solid #ccc; padding: 15px; background: #fff; border-radius: 4px; pointer-events: none; opacity: 0.8;">
                            <?php echo wp_kses_post( $welcome_content ); ?>
                        </div>
                    </div>
                    <div class="card" style="flex: 1; min-width: 300px; padding: 15px 20px; background: #f6f7f7; margin: 0;">
                        <h2><?php esc_html_e( 'Global Block 2', 'dashboard-master' ); ?> </h2>
                        <div style="border: 1px solid #ccc; padding: 15px; background: #fff; border-radius: 4px; pointer-events: none; opacity: 0.8;">
                            <?php echo wp_kses_post( $global_2_content ); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if ( is_super_admin() ) : ?>
                <p style="color: #d63638;"><strong><?php esc_html_e( 'Super Admin Attention:', 'dashboard-master' ); ?></strong> <?php esc_html_e( 'The content of these two blocks will be fixed and displayed across all subsites in the network.', 'dashboard-master' ); ?></p>
            <?php endif; ?>

            <h2 style="margin-top: 30px;"><?php printf( esc_html__( 'Your Local Blocks (%d/6)', 'dashboard-master' ), $widget_count ); ?></h2>
            <p><strong><?php esc_html_e( 'Tip:', 'dashboard-master' ); ?></strong> <?php esc_html_e( 'These blocks will only appear on this specific site\'s dashboard. Control who can see them below.', 'dashboard-master' ); ?></p>
            
            <div id="list-widgets-container">
                <?php if ( ! empty( $widgets ) ) : ?>
                    <?php foreach ( $widgets as $index => $widget ) : 
                        $saved_roles = isset( $widget['allowed_roles'] ) ? (array) $widget['allowed_roles'] : array( 'all' );
                    ?>
                        <div class="card widget-item" style="max-width: 100%; margin-bottom: 15px; padding: 15px; border-left: 4px solid #2271b1;">
                            
                            <p style="margin-bottom: 15px;">
                                <label><strong><?php esc_html_e( 'Block Title:', 'dashboard-master' ); ?></strong></label><br>
                                <input type="text" name="custom_widgets[<?php echo $index; ?>][title]" value="<?php echo esc_attr( $widget['title'] ); ?>" class="widefat">
                            </p>

                            <div style="margin-bottom: 15px; padding: 10px; background: #f0f0f1; border-radius: 4px; border: 1px solid #c3c4c7;">
                                <label><strong><?php esc_html_e( 'Visibility Permission (Who can see this?):', 'dashboard-master' ); ?></strong></label><br>
                                <div style="margin-top: 8px; display: flex; flex-wrap: wrap; gap: 15px;">
                                    <label>
                                        <input type="checkbox" name="custom_widgets[<?php echo $index; ?>][allowed_roles][]" value="all" <?php checked( in_array( 'all', $saved_roles ) ); ?>>
                                        <strong><?php esc_html_e( 'All Users', 'dashboard-master' ); ?></strong>
                                    </label>
                                    <?php foreach ( $all_roles as $role_slug => $role_name ) : ?>
                                        <label>
                                            <input type="checkbox" name="custom_widgets[<?php echo $index; ?>][allowed_roles][]" value="<?php echo esc_attr( $role_slug ); ?>" <?php checked( in_array( $role_slug, $saved_roles ) ); ?>>
                                            <?php echo translate_user_role( $role_name ); ?>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                                <p class="description" style="margin-top:5px; font-size:12px;"><?php esc_html_e( 'Tip: If "All Users" is checked, the other options will be ignored. If you uncheck all boxes, the widget becomes invisible (Draft Mode).', 'dashboard-master' ); ?></p>
                            </div>
                            
                            <div style="margin-bottom: 15px;">
                                <label><strong><?php esc_html_e( 'Content:', 'dashboard-master' ); ?></strong></label><br>
                                <?php 
                                    $editor_id = 'editor_custom_' . $index;
                                    wp_editor( 
                                        $widget['content'], 
                                        $editor_id, 
                                        array( 'textarea_name' => 'custom_widgets[' . $index . '][content]', 'textarea_rows' => 5, 'media_buttons' => true ) 
                                    ); 
                                ?>
                            </div>
                            <button type="button" class="button button-link-delete remove-widget"><?php esc_html_e( 'Remove this Block', 'dashboard-master' ); ?></button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <p style="margin-top: 20px;">
                <?php if ( $widget_count < 6 ) : ?>
                    <button type="submit" name="add_new_block" value="1" class="button button-secondary"><?php esc_html_e( '+ Add New Local Block', 'dashboard-master' ); ?></button>
                <?php else : ?>
                    <span style="color: #d63638; font-weight: bold;">⚠️ <?php esc_html_e( 'Limit of 6 local blocks reached.', 'dashboard-master' ); ?></span>
                <?php endif; ?>
            </p>

            <hr style="margin-top: 30px;">
            <?php submit_button( __( 'Save Dashboard Changes', 'dashboard-master' ) ); ?>
        </form>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('form-widget-manager');
        if (form) { form.addEventListener('submit', function() { if (typeof tinyMCE !== 'undefined') { tinyMCE.triggerSave(); } }); }

        const container = document.getElementById('list-widgets-container');
        if (container) {
            container.addEventListener('click', function(e) {
                if (e.target && e.target.classList.contains('remove-widget')) {
                    if(confirm('<?php echo esc_js( __( 'Are you sure you want to remove this block? Remember to save your changes to free up space.', 'dashboard-master' ) ); ?>')) { 
                        e.target.closest('.widget-item').remove(); 
                    }
                }
            });
        }
    });
    </script>
    <?php
}

// ==============================================================================
// 2. WIDGET INJECTION AND DEFAULT RENDERING
// ==============================================================================

add_filter('embed_oembed_html', 'dashboard_master_fix_youtube_oembed', 10, 3);
function dashboard_master_fix_youtube_oembed($html, $url, $attr) {
    if ( strpos( $url, 'youtube.com' ) !== false || strpos( $url, 'youtu.be' ) !== false ) {
        if ( strpos( $html, 'referrerpolicy' ) === false ) { $html = str_replace( '<iframe', '<iframe referrerpolicy="strict-origin-when-cross-origin"', $html ); }
    }
    return $html;
}

add_action('wp_dashboard_setup', 'dashboard_master_clear_dashboard', 99999);
function dashboard_master_clear_dashboard() {
    global $wp_meta_boxes;
    $wp_meta_boxes['dashboard'] = array();

    // GLOBAL WIDGETS
    wp_add_dashboard_widget( 
        'widget_global_1', 
        __( 'Network Notices', 'dashboard-master' ), 
        'dashboard_master_content_global_1' 
    );
    wp_add_dashboard_widget( 
        'widget_global_2', 
        __( 'Network Information', 'dashboard-master' ), 
        'dashboard_master_content_global_2' 
    );

    // LOCAL WIDGETS WITH PERMISSION FILTER
    $widgets = get_option( 'dashboard_local_widgets', array() );
    
    $current_user = wp_get_current_user();
    $user_roles = (array) $current_user->roles;

    if ( ! empty( $widgets ) && is_array( $widgets ) ) {
        $widgets = array_slice( $widgets, 0, 6 ); 
        
        foreach ( $widgets as $index => $widget ) {
            
            $allowed_roles = isset( $widget['allowed_roles'] ) ? (array) $widget['allowed_roles'] : array( 'all' );
            
            $can_view = false;
            
            if ( in_array( 'all', $allowed_roles ) ) {
                $can_view = true;
            } elseif ( ! empty( array_intersect( $user_roles, $allowed_roles ) ) ) {
                $can_view = true;
            } elseif ( is_super_admin() ) {
                $can_view = true;
            }

            if ( $can_view ) {
                wp_add_dashboard_widget(
                    'custom_widget_' . $index,
                    esc_html( $widget['title'] ),
                    function() use ( $widget ) {
                        global $wp_embed;
                        $content = $widget['content'];
                        if ( isset( $wp_embed ) ) { $content = $wp_embed->run_shortcode( $content ); $content = $wp_embed->autoembed( $content ); }
                        $content = wpautop( $content );
                        $content = do_shortcode( $content );
                        echo '<div class="custom-widget-content" style="padding: 10px;">' . $content . '</div>';
                    }
                );
            }
        }
    }
}

function dashboard_master_content_global_1() {
    global $wp_embed;
    $default = '<p>' . __( 'Welcome to your dashboard.', 'dashboard-master' ) . '</p>';
    $content = get_site_option( 'dashboard_global_welcome', $default );
    if ( isset( $wp_embed ) ) { $content = $wp_embed->run_shortcode( $content ); $content = $wp_embed->autoembed( $content ); }
    $content = wpautop( $content );
    $content = do_shortcode( $content );
    echo '<div class="custom-widget-content" style="padding: 10px;">' . $content . '</div>';
}

function dashboard_master_content_global_2() {
    global $wp_embed;
    $default = '<p>' . __( 'Reserved space.', 'dashboard-master' ) . '</p>';
    $content = get_site_option( 'dashboard_global_secondary', $default );
    if ( isset( $wp_embed ) ) { $content = $wp_embed->run_shortcode( $content ); $content = $wp_embed->autoembed( $content ); }
    $content = wpautop( $content );
    $content = do_shortcode( $content );
    echo '<div class="custom-widget-content" style="padding: 10px;">' . $content . '</div>';
}

// ==============================================================================
// 3. CLEANING RULES AND NOTICES CONTROL
// ==============================================================================

add_action( 'admin_init', 'dashboard_master_remove_native_welcome_panel' );
function dashboard_master_remove_native_welcome_panel() {
    remove_action( 'welcome_panel', 'wp_welcome_panel' );
}

add_action( 'admin_head-index.php', 'dashboard_master_remove_dashboard_notices', 99 );
function dashboard_master_remove_dashboard_notices() {
    remove_all_actions( 'admin_notices' );
    remove_all_actions( 'all_admin_notices' );
}

add_action( 'admin_init', 'dashboard_master_remove_core_notices_users' );
function dashboard_master_remove_core_notices_users() {
    if ( ! is_super_admin() ) {
        remove_action( 'admin_notices', 'update_nag', 3 );
        remove_action( 'network_admin_notices', 'update_nag', 3 );
        remove_action( 'admin_notices', 'maintenance_nag', 10 );
    }
}

add_action( 'admin_bar_menu', 'dashboard_master_add_xray_button', 999 );
function dashboard_master_add_xray_button( $wp_admin_bar ) {
    if ( current_user_can( 'manage_options' ) || is_super_admin() ) {
        $wp_admin_bar->add_node( array(
            'id'    => 'dm-xray-notices',
            'title' => '<span class="ab-icon dashicons-visibility" style="margin-top:3px;"></span> ' . __( 'Show Notices', 'dashboard-master' ),
            'href'  => '#',
            'meta'  => array( 'class' => 'dm-toggle-notices' )
        ) );
    }
}

// SOLUÇÃO: CSS corrigido. Remoção de curingas ("*=") que quebravam classes do WordPress
add_action( 'admin_head', 'dashboard_master_advanced_adblock_css', 999 );
function dashboard_master_advanced_adblock_css() {
    echo '<style>
        /* Hides banners by default using exact classes, keeping the core WP UI safe */
        body:not(.dm-show-notices) .envo-notice, 
        body:not(.dm-show-notices) .learnpress-notice, 
        body:not(.dm-show-notices) .lp-notice, 
        body:not(.dm-show-notices) div[id*="learn-press"],
        body:not(.dm-show-notices) .pmpro_notice, 
        body:not(.dm-show-notices) .pmpro-notice,
        body:not(.dm-show-notices) .e-notice,
        body:not(.dm-show-notices) .elementor-notice,
        body:not(.dm-show-notices) .fs-notice { 
            display: none !important; 
        }
        
        /* Subtly highlights notices when revealed by the Show Notices button */
        body.dm-show-notices .dm-blocked-notice { display: block !important; border-left-color: #d63638 !important; opacity: 0.95; }
        body:not(.dm-show-notices) .dm-blocked-notice { display: none !important; }

        /* Structure of our custom widgets */
        #dashboard-widgets-wrap { margin-top: 15px !important; }
        .custom-widget-content img { max-width: 100% !important; height: auto; border-radius: 8px; }
        .custom-widget-content iframe { width: 100% !important; aspect-ratio: 16 / 9; border-radius: 8px; min-height: 250px; }
    </style>';
}

add_action( 'admin_footer', 'dashboard_master_internal_adblock_js', 999 );
function dashboard_master_internal_adblock_js() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var blockedWords = [
            'upgrade', 'premium', 'limited time offer', 'discount', 'sale',
            'unlock all features', 'learnpress lms is ready', 'envothemes', 'addons',   
            'superb addons', 'simple nova', 'envo one', 'free woocommerce',
            'pmpro update manager', 'secure updates for pmpro', 'license server',
            'seja pro', 'atualizar agora', 'crie sem limites', 'versão pro', 'obter suporte'
        ];
        
        function annihilateAds() {
            var notices = document.querySelectorAll('.wrap > div, .notice, .updated, .error, div[class*="notice"], div[class*="message"]');
            
            notices.forEach(function(notice) {
                // SHIELD: Ignores completely any WordPress Media UI or Popups
                if (notice.closest('.media-modal') || notice.closest('.media-frame')) {
                    return;
                }

                var innerHTML = notice.innerHTML.toLowerCase();
                if (innerHTML.includes('alternar de volta') || innerHTML.includes('user-switching')) {
                    return; 
                }
                
                var text = (notice.innerText || notice.textContent).toLowerCase();
                for (var i = 0; i < blockedWords.length; i++) {
                    if (text.includes(blockedWords[i])) {
                        notice.classList.add('dm-blocked-notice');
                        break;
                    }
                }
            });
        }

        annihilateAds();
        setTimeout(annihilateAds, 1000);
        setTimeout(annihilateAds, 3000);

        var btnToggle = document.querySelector('.dm-toggle-notices a');
        if ( btnToggle ) {
            btnToggle.addEventListener('click', function(e) {
                e.preventDefault();
                document.body.classList.toggle('dm-show-notices');
                if ( document.body.classList.contains('dm-show-notices') ) {
                    btnToggle.innerHTML = '<span class="ab-icon dashicons-hidden" style="margin-top:3px;"></span> <?php esc_html_e( 'Hide Notices', 'dashboard-master' ); ?>';
                } else {
                    btnToggle.innerHTML = '<span class="ab-icon dashicons-visibility" style="margin-top:3px;"></span> <?php esc_html_e( 'Show Notices', 'dashboard-master' ); ?>';
                }
            });
        }
    });
    </script>
    <?php
}

add_filter( 'screen_options_show_screen', 'dashboard_master_hide_screen_options' );
function dashboard_master_hide_screen_options( $show_screen ) {
    $screen = get_current_screen();
    if ( $screen && $screen->id === 'dashboard' ) {
        return false;
    }
    return $show_screen;
}

add_action( 'admin_head', 'dashboard_master_remove_help_tabs' );
function dashboard_master_remove_help_tabs() {
    $screen = get_current_screen();
    if ( $screen && $screen->id === 'dashboard' ) { 
        $screen->remove_help_tabs(); 
    }
}
// ==============================================================================
// 4. FOOTER CUSTOMIZATION
// ==============================================================================

add_filter( 'admin_footer_text', 'dashboard_master_custom_footer' );
function dashboard_master_custom_footer() {
    printf( 
        __( 'Managed by %s.', 'dashboard-master' ), 
        '<a href="https://www.mastermusica.com.br" target="_blank">Master Musica</a>' 
    );
}

add_filter( 'update_footer', '__return_empty_string', 9999 );