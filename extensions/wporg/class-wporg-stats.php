<?php

class NotificationXPro_WPOrgStats_Extension extends NotificationX_Extension {
    /**
     *  Type of notification.
     *
     * @var string
     */
    public $type      = 'wp_stats';
    public $template  = 'wp_stats_template';
    public $themeName = 'wpstats_theme';
    public $meta_key  = 'wporg_stats_content';
    public $api_key   = '';
    public $helper    = null;
    /**
     * An array of all notifications
     *
     * @var [type]
     */
    protected $notifications = [];

    public function __construct() {
        parent::__construct( $this->template );

        $this->load_dependencies();

        if( $this->helper === null ) {
            $this->helper = new NotificationXPro_WPOrg_Helper();
        }

        add_action( 'nx_notification_image_action', array( $this, 'image_action' ) ); // Image Action for gravatar
        add_action( 'nx_cron_update_data', array( $this, 'update_data' ), 10, 1 );
        add_action( 'nx_admin_action', array( $this, 'admin_save' ) );
        add_filter( 'cron_schedules', array( $this, 'cache_duration' ) );
    }

    public function template_string_by_theme( $template, $old_template, $posts_data ){
        if( $posts_data['nx_meta_display_type'] === 'download_stats' && $posts_data['nx_meta_stats_source'] === $this->type ) {
            $theme = $posts_data['nx_meta_wpstats_theme'];

            switch( $theme ) {
                case 'theme-one' : 
                    $template = NotificationX_Helper::regenerate_the_theme( $old_template, array( 'br_before' => [ 'second_param', 'fourth_param' ] ) );
                    break;
                case 'theme-two' : 
                    $template = NotificationX_Helper::regenerate_the_theme( $old_template, array( 'br_before' => [ 'second_param', 'fourth_param' ] ) );
                    break;
                case 'actively_using' : 
                    $old_template = $posts_data['nx_meta_actively_using_template_new'];
                    $template = NotificationX_Helper::regenerate_the_theme( $old_template, array( 'br_before' => [ 'third_param' ] ) );
                    break;
                default : 
                    $old_template = $posts_data['nx_meta_wp_stats_template_new'];
                    $template = NotificationX_Helper::regenerate_the_theme( $old_template, array( 'br_before' => [ 'second_param', 'fourth_param' ] ) );
                    break;
            }

            return $template;
        }
        return $template;
    }

    public function settings_by_theme( $data ){
        $data['nx_meta_wp_stats_template_new'] = array(
            'theme-one' => array(
                'first_param' => 'tag_name',
                'third_param' => 'tag_today',
                'fourth_param' => 'tag_today_text',
            ),
            'theme-two' => array(
                'first_param' => 'tag_name',
                'third_param' => 'tag_last_week',
                'fourth_param' => 'tag_last_week_text',
            ),
            'theme-four' => array(
                'first_param' => 'tag_name',
                'third_param' => 'tag_all_time',
                'fourth_param' => 'tag_all_time_text',
            )
        );

        return $data;
    }

    public function fallback_data( $data, $saved_data ){
        unset( $data['name'] );
        $data['today'] = __( $saved_data['today'] . ' times today', 'notificationx' );
        $data['last_week'] = __( $saved_data['last_week'] . ' times in last 7 days', 'notificationx' );
        $data['all_time'] = __( $saved_data['all_time'] . ' times', 'notificationx' );
        
        $data['today_text'] = __( 'Try it out', 'notificationx' );
        $data['last_week_text'] = __( 'Get started free.', 'notificationx' );
        $data['all_time_text'] = __( 'why not you?', 'notificationx' );

        return $data;
    }

    public function load_dependencies(){
        if( ! class_exists( 'NotificationXPro_WPOrg_Helper' ) ) {
            require_once __DIR__ . '/class-wporg-helper.php';
        }
    }

    /**
     * Image Action
     */
    public function image_action(){
        add_filter( 'nx_notification_image', array( $this, 'notification_image' ), 10, 3 );
    }

    public function notification_image( $image_data, $data, $settings ){
        if( $settings->display_type != 'download_stats' || $settings->stats_source != $this->type ) { 
            return $image_data;
        }
    
        $avatar = '';
        $alt_title = isset( $data['name'] ) ? $data['name'] : $data['title'];

        if( isset( $data['icons'] ) && $settings->wp_stats_product_type === 'plugin' ) {
            $avatar = $data['icons']['2x'];
        }

        if( isset( $data['screenshot_url'] ) && $settings->wp_stats_product_type === 'theme' ) {
            $avatar = $data['screenshot_url'];
        }

        $image_data['url'] = $avatar;
        $image_data['alt'] = $alt_title;

        return $image_data;
    }

    public function admin_save(){
        add_action( 'save_post', array( $this, 'save_post' ), 10, 1 );
    }

    public function save_post( $post_id ) {
        $this->update_data( $post_id );
		NotificationX_Cron::set_cron( $post_id );
    }

    public function update_data( $post_id ){
        if ( empty( $post_id ) ) {
            return;
        }

        $plugins_data = $this->get_plugins_data( $post_id );
        NotificationX_Admin::update_post_meta( $post_id, $this->meta_key, $plugins_data );
    }

    /**
     * This functions is hooked
     * 
     * @hooked nx_public_action
     *
     * @return void
     */
    public function public_actions(){
        if( ! $this->is_created( $this->type ) ) {
            return;
        }
        
        add_filter( 'nx_fields_data', array( $this, 'conversion_data' ), 10, 2 );
    }

    public function conversion_data( $data, $id ){
        if( ! $id ) {
            return $data;
        }
        $data[ $this->type ] = NotificationX_Admin::get_post_meta( intval( $id ), $this->meta_key, true );
        return $data;
    }

    private function member( $data, $title ){
        $member['link'] = '';
        
        return $member;
    }

    public function get_plugins_data( $post_id ) {
        if( ! $post_id ) {
            return;
        }

        $product_type = NotificationX_Admin::get_post_meta( intval( $post_id ), 'wp_stats_product_type', true );
        $plugin_slug = NotificationX_Admin::get_post_meta( intval( $post_id ), 'wp_stats_slug', true );

        if( ! $plugin_slug ) {
            return;
        }

        if( $product_type == 'plugin' ) {
            $raw_stats              = $this->helper->get_plugin_stats( $plugin_slug );
            $raw_historical_summary = $this->remote_get('https://api.wordpress.org/stats/plugin/1.0/downloads.php?slug='. $plugin_slug .'&historical_summary=1');
            $historical_summary     = json_decode( json_encode( $raw_historical_summary ), true );
            $total_stats            = array_merge( $raw_stats, $historical_summary );
            $total_stats['link'] = "https://wordpress.org/plugins/" . $total_stats['slug'];
        }

        if( $product_type == 'theme' ) {
            $stats              = $this->helper->get_theme_stats( $plugin_slug );
            $raw_historical_summary = $this->remote_get('https://api.wordpress.org/stats/themes/1.0/downloads.php?slug='. $plugin_slug .'&historical_summary=1');
            $historical_summary     = json_decode( json_encode( $raw_historical_summary ), true );
            $total_stats            = array_merge( $stats, $historical_summary );
        }

        return array( $total_stats );
    }

    public function cache_duration( $schedules ) {
        $custom_duration = NotificationX_DB::get_settings( 'wporg_cache_duration' );

        if ( ! $custom_duration || empty( $custom_duration ) ) {
            $custom_duration = 45;
        }

        if ( $custom_duration < 3 ) {
            $custom_duration = 3;
        }

        $schedules['nx_cache_interval'] = array(
            'interval'	=> $custom_duration * 60,
            'display'	=> sprintf( __('Every %s minutes', 'notificationx'), $custom_duration )
        );

        return $schedules;
    }

    private function init_fields(){
        $fields = [];

        $fields['wp_stats_product_type'] = array(
            'type'     => 'select',
            'label'    => __('Product Type' , 'notificationx'),
            'priority' => 79,
            'options' => array(
                'plugin' => __('Plugin' , 'notificationx'),
                'theme' => __('Theme' , 'notificationx'),
            )
        );
        
        $fields['wp_stats_slug'] = array(
            'type'     => 'text',
            'label'    => __('Slug' , 'notificationx'),
            'priority' => 80,
        );

        $fields['wp_stats_template_new'] = array(
            'type'     => 'template',
            'fields' => array(
                'first_param' => array(
                    'type'     => 'select',
                    'label'    => __('Notification Template' , 'notificationx'),
                    'priority' => 1,
                    'options'  => array(
                        'tag_name' => __('Plugin/Theme Name' , 'notificationx'),
                        'tag_custom' => __('Custom' , 'notificationx'),
                    ),
                    'dependency' => array(
                        'tag_custom' => array(
                            'fields' => [ 'custom_first_param' ]
                        )
                    ),
                    'hide' => array(
                        'tag_name' => array(
                            'fields' => [ 'custom_first_param' ]
                        ),
                    ),
                    'default' => 'tag_name'
                ),
                'custom_first_param' => array(
                    'type'     => 'text',
                    'priority' => 2,
                ),
                'second_param' => array(
                    'type'     => 'text',
                    'priority' => 4,
                    'default' => __('has been downloaded' , 'notificationx')
                ),
                'third_param' => array(
                    'type'     => 'select',
                    'priority' => 5,
                    'options'  => array(
                        'tag_today'           => __('Today' , 'notificationx'),
                        'tag_last_week'       => __('In last 7 days' , 'notificationx'),
                        'tag_all_time'        => __('Total' , 'notificationx'),
                        'tag_active_installs' => __('Total Active Install' , 'notificationx'),
                        'tag_custom_stats'    => __('Custom' , 'notificationx'),
                    ),
                    'dependency' => array(
                        'tag_custom_stats' => array(
                            'fields' => [ 'custom_third_param' ]
                        )
                    ),
                    'hide' => array(
                        'tag_today' => array(
                            'fields' => [ 'custom_third_param' ]
                        ),
                        'tag_last_week' => array(
                            'fields' => [ 'custom_third_param' ]
                        ),
                        'tag_all_time' => array(
                            'fields' => [ 'custom_third_param' ]
                        ),
                    ),
                    'default' => 'tag_all_time'
                ),
                'custom_third_param' => array(
                    'type'     => 'text',
                    'priority' => 6,
                ),
                'fourth_param' => array(
                    'type'     => 'select',
                    'priority' => 7,
                    'options'  => array(
                        'tag_today_text'           => __('today. Try it out' , 'notificationx'),
                        'tag_last_week_text'       => __('in last 7 days' , 'notificationx'),
                        'tag_all_time_text'        => __('in total' , 'notificationx'),
                        'tag_active_installs_text' => __('in total active' , 'notificationx'),
                    ),
                    'default' => 'tag_today_text'
                ),
            ),
            'label'    => __('Notification Template' , 'notificationx'),
            'priority' => 83,
        );

        $fields['actively_using_template_new'] = array(
            'type'     => 'template',
            'fields' => array(
                'first_param' => array(
                    'type'     => 'select',
                    'label'    => __('Notification Template' , 'notificationx'),
                    'priority' => 1,
                    'options'  => array(
                        'tag_today'           => __('Today' , 'notificationx'),
                        'tag_last_week'       => __('In last 7 days' , 'notificationx'),
                        'tag_all_time'        => __('Total' , 'notificationx'),
                        'tag_active_installs' => __('Total Active Install' , 'notificationx'),
                        'tag_custom'    => __('Custom' , 'notificationx'),
                    ),
                    'dependency' => array(
                        'tag_custom' => array(
                            'fields' => [ 'custom_first_param' ]
                        )
                    ),
                    'hide' => array(
                        'tag_today' => array(
                            'fields' => [ 'custom_first_param' ]
                        ),
                        'tag_last_week' => array(
                            'fields' => [ 'custom_first_param' ]
                        ),
                        'tag_all_time' => array(
                            'fields' => [ 'custom_first_param' ]
                        ),
                        'tag_active_installs' => array(
                            'fields' => [ 'custom_first_param' ]
                        ),
                    ),
                    'default' => 'tag_active_installs'
                ),
                'custom_first_param' => array(
                    'type'     => 'text',
                    'priority' => 2,
                ),
                'second_param' => array(
                    'type'     => 'text',
                    'priority' => 4,
                    'default' => __('people are actively using' , 'notificationx')
                ),
                'third_param' => array(
                    'type'     => 'select',
                    'priority' => 5,
                    'options'  => array(
                        'tag_name' => __('Plugin/Theme Name' , 'notificationx'),
                        'tag_custom_stats' => __('Custom' , 'notificationx'),
                    ),
                    'dependency' => array(
                        'tag_custom_stats' => array(
                            'fields' => [ 'custom_third_param' ]
                        )
                    ),
                    'hide' => array(
                        'tag_name' => array(
                            'fields' => [ 'custom_third_param' ]
                        ),
                    ),
                    'default' => 'tag_name'
                ),
                'custom_third_param' => array(
                    'type'     => 'text',
                    'priority' => 6,
                ),
            ),
            'label'    => __('Notification Template' , 'notificationx'),
            'priority' => 83,
        );

        $fields['wp_stats_template_adv'] = array(
            'type'        => 'adv_checkbox',
            'priority'    => 84,
            'button_text' => __('Advance Template' , 'notificationx'),
            'side'        => 'right',
            'dependency'  => array(
                1 => array(
                    'fields' => [ 'wp_stats_template' ]
                )
            ),
        );

        $fields['wp_stats_template'] = array(
            'type'     => 'template',
            'priority' => 85,
            'defaults' => [
                __('{{name}}', 'notificationx'), 'has been downloaded {{all_time}} times', 'Why not you?'
            ],
            'variables' => [
                '{{name}}', '{{today}}', '{{last_week}}', '{{yesterday}}', '{{all_time}}', '{{active_installs}}'
            ],
        );
        
        return $fields;
    }
    private function init_sections(){
        $sections = [];

        $sections['wpstats_themes'] = array(
            'title'      => __('Themes', 'notificationx'),
            'priority' => 14,
            'fields'   => array(
                'wpstats_theme' => array(
                    'type'      => 'theme',
                    'priority'	=> 3,
                    'default'	=> 'theme-one',
                    'options'   => $this->themes(),
                    'hide' => [
                        'theme-two' => [
                            'fields' => ['actively_using_template_new']
                        ],
                        'theme-four' => [
                            'fields' => ['actively_using_template_new']
                        ],
                        'theme-one' => [
                            'fields' => ['actively_using_template_new']
                        ],
                        'actively_using' => [
                            'fields' => ['wp_stats_template_new']
                        ],
                    ],
                    'dependency' => [
                        'actively_using' => [
                            'fields' => ['actively_using_template_new']
                        ],
                        'theme-two' => [
                            'fields' => ['wp_stats_template_new']
                        ],
                        'theme-four' => [
                            'fields' => ['wp_stats_template_new']
                        ],
                        'theme-one' => [
                            'fields' => ['wp_stats_template_new']
                        ],
                    ],
                ),
                'wpstats_advance_edit' => array(
                    'type'      => 'adv_checkbox',
                    'priority'	=> 10,
                    'dependency' => [
                        1 => [
                            'sections' => ['wpstats_theme_design', 'wpstats_theme_typography']
                        ]
                    ],
                ),
            )
        );

        $sections['wpstats_theme_design'] = array(
            'title'    => __('Design', 'notificationx'),
            'priority' => 15,
            'reset'    => true,
            'fields'   => array(
                'wpstats_bg_color' => array(
                    'type'      => 'colorpicker',
                    'label'     => __('Background Color' , 'notificationx'),
                    'priority'	=> 5,
                    'default'	=> ''
                ),
                'wpstats_text_color' => array(
                    'type'      => 'colorpicker',
                    'label'     => __('Text Color' , 'notificationx'),
                    'priority'	=> 10,
                    'default'	=> ''
                ),
                'wpstats_border' => array(
                    'type'      => 'checkbox',
                    'label'     => __('Want Border?' , 'notificationx'),
                    'priority'	=> 15,
                    'default'	=> 0,
                    'dependency'	=> [
                        1 => [
                            'fields' => [ 'wpstats_border_size', 'wpstats_border_style', 'wpstats_border_color' ]
                        ]
                    ],
                ),
                'wpstats_border_size' => array(
                    'type'      => 'number',
                    'label'     => __('Border Size' , 'notificationx'),
                    'priority'	=> 20,
                    'default'	=> '1',
                    'description'	=> 'px',
                ),
                'wpstats_border_style' => array(
                    'type'      => 'select',
                    'label'     => __('Border Style' , 'notificationx'),
                    'priority'	=> 25,
                    'default'	=> 'solid',
                    'options'	=> [
                        'solid' => __('Solid', 'notificationx'),
                        'dashed' => __('Dashed', 'notificationx'),
                        'dotted' => __('Dotted', 'notificationx'),
                    ],
                ),
                'wpstats_border_color' => array(
                    'type'      => 'colorpicker',
                    'label'     => __('Border Color' , 'notificationx'),
                    'priority'	=> 30,
                    'default'	=> ''
                ),
            )
        );

        $sections['wpstats_theme_typography'] = array(
            'title'      => __('Typography', 'notificationx'),
            'priority' => 16,
            'reset'    => true,
            'fields'   => array(
                'wpstats_first_font_size' => array(
                    'type'      => 'number',
                    'label'     => __('Font Size' , 'notificationx'),
                    'priority'	=> 5,
                    'default'	=> '13',
                    'description'	=> 'px',
                    'help'	=> __( 'This font size will be applied for <mark>first</mark> row', 'notificationx' ),
                ),
                'wpstats_second_font_size' => array(
                    'type'      => 'number',
                    'label'     => __('Font Size' , 'notificationx'),
                    'priority'	=> 10,
                    'default'	=> '14',
                    'description'	=> 'px',
                    'help'	=> __( 'This font size will be applied for <mark>second</mark> row', 'notificationx' ),
                ),
                'wpstats_third_font_size' => array(
                    'type'      => 'number',
                    'label'     => __('Font Size' , 'notificationx'),
                    'priority'	=> 15,
                    'default'	=> '11',
                    'description'	=> 'px',
                    'help'	=> __( 'This font size will be applied for <mark>third</mark> row', 'notificationx' ),
                ),
            )
        );
        
        return $sections;
    }

    private function get_fields(){
        return $this->init_fields();
    }

    private function get_sections(){
        return $this->init_sections();
    }

    public function init_hooks(){
        add_filter( 'nx_metabox_tabs', array( $this, 'add_fields' ) );
        add_filter( 'nx_display_types_hide_data', array( $this, 'hide_fields' ) );
        add_filter( 'nx_stats_source', array( $this, 'toggle_fields' ) );
    }

    public function init_builder_hooks(){
        add_filter( 'nx_builder_tabs', array( $this, 'add_builder_fields' ) );
        add_filter( 'nx_display_types_hide_data', array( $this, 'hide_builder_fields' ) );
        add_filter( 'nx_builder_tabs', array( $this, 'builder_toggle_fields' ) );
    }

    /**
     * This function is responsible for adding fields to helper files.
     *
     * @param array $options
     * @return void
     */
    public function add_fields( $options ){
        $fields = $this->get_fields();

        foreach ( $fields as $name => $field ) {
            $options[ 'content_tab' ]['sections']['content_config']['fields'][ $name ] = $field;
        }

        $sections = $this->get_sections();
        foreach ( $sections as $parent_key => $section ) {
            $options[ 'design_tab' ]['sections'][ $parent_key ] = $section;
        }

        return $options;
    }

    public function add_builder_fields( $options ){
        $fields = $this->get_fields();
        $sections = $this->get_sections();
        unset( $fields[ $this->template ] );
        unset( $fields[ $this->template . '_new' ] );
        unset( $fields[ $this->template . '_adv' ] );
        unset( $fields['actively_using_template_new'] );
        unset( $sections['wpstats_theme_design'] );
        unset( $sections['wpstats_themes']['fields']['wpstats_advance_edit'] );
        unset( $sections['wpstats_theme_typography'] );
        
        foreach ( $fields as $name => $field ) {
            $options['source_tab']['sections']['config']['fields'][ $name ] = $field;
        }
        foreach ( $sections as $sec_name => $section ) {
            $options['design_tab']['sections'][ $sec_name ] = $section;
        }

        return $options;
    }

    /**
     * This function is responsible for hide fields when others type selected.
     *
     * @param array $options
     * @return void
     */
    public function hide_fields( $options ) {
        $fields = $this->get_fields();
        $sections = $this->get_sections();

        foreach ( $fields as $name => $field ) {
            foreach( $options as $opt_key => $opt_value ) {
                if( $opt_key != $this->type ) {
                    $options[ $opt_key ][ 'fields' ][] = $name;
                }
            }
        }

        foreach ( $sections as $section_name => $section ) {
            foreach( $options as $opt_key => $opt_value ) {
                if( $opt_key != $this->type ) { 
                    $options[ $opt_key ][ 'sections' ][] = $section_name;
                }
            }
        }

        return $options;
    }

    public function hide_builder_fields( $options ) {
        $fields = array_merge( $this->get_fields(), [] );
        $sections = $this->get_sections();
        unset( $sections['wporg_design'] );
        unset( $sections['wporg_typography'] );

        // Hide fields from other field types.
        foreach( $fields as $field_key => $field_value ) {
            foreach( $options as $opt_key => $opt_value ) {
                $options[ $opt_key ][ 'fields' ][] = $field_key;
            }
        }

        foreach( $sections as $sec_key => $sec_value ) {
            foreach( $options as $opt_key => $opt_value ) {
                $options[ $opt_key ][ 'sections' ][] = $sec_key;
            }
        }
        
        return $options;
    }
    /**
     * This function is responsible for render toggle data for conversion
     *
     * @param array $options
     * @return void
     */
    public function toggle_fields( $options ) {

        $fields = $this->init_fields();
        $sections = $this->init_sections();
        $sections = array_keys( $sections );
        $fields = array_keys( $fields );

        $options['dependency'][ $this->type ]['fields'] = $fields;
        $options['dependency'][ $this->type ]['sections'] = $sections;

        return $options;
    }

    /**
     * This function is responsible for builder fields
     *
     * @param array $options
     * @return void
     */
    public function builder_toggle_fields( $options ) {
        $fields = $this->init_fields();
        $sections = $this->init_sections();
        unset( $fields[ $this->template ] );
        $old_fields = [];
        $options['source_tab']['sections']['config']['fields']['stats_source']['dependency'][ $this->type ]['fields'] = array_keys( $fields );
        return $options;
    }

    public function themes(){
        return apply_filters('nxpro_stats_themes', array(
            'theme-one' => NOTIFICATIONX_ADMIN_URL . 'assets/img/themes/wporg/today-download.png',
            'theme-two' => NOTIFICATIONX_ADMIN_URL . 'assets/img/themes/wporg/7day-download.png',
            'actively_using' => NOTIFICATIONX_ADMIN_URL . 'assets/img/themes/wporg/actively-using.png',
            'theme-four' => NOTIFICATIONX_ADMIN_URL . 'assets/img/themes/wporg/total-download.png',
        ));
    }

    public function frontend_html( $data = [], $settings = false, $args = [] ){
        return parent::frontend_html( $data, $settings, $args );
    }

}