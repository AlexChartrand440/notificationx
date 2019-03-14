<?php

function notificationx_metabox_args(){
    return array(
        'id'           => 'notificationx_metabox_wrapper',
        'title'        => __('NotificationX', 'notificationx'),
        'object_types' => array( 'notificationx' ),
        'context'      => 'normal',
        'priority'     => 'high',
        'show_header'  => false,
        'tabnumber'    => true,
        'layout'       => 'horizontal',
        'tabs'         => apply_filters('nx_metabox_tabs', array(
            'source_tab' => array(
                'title'         => __('Source', 'notificationx'),
                'icon'          => 'database.svg',
                'sections'      => apply_filters('nx_source_tab_sections', array(
                    'config'        => array(
                        'title'             => __('Select Source', 'notificationx'),
                        'fields'            => array(
                            'display_type'  => apply_filters( 'nx_display_type', array(
                                'type'      => 'select',
                                'label'     => __('I would like to display' , 'notificationx'),
                                'default'   => 'press_bar',
                                'options'   => NotificationX_Helper::notification_types(),
                                'toggle'   => [
                                    'comments'    => NotificationX_Helper::comments_toggle_data(),
                                    'press_bar'   => NotificationX_Helper::press_bar_toggle_data(),
                                    'conversions' => NotificationX_Helper::conversions_toggle_data(),
                                ],
                                'hide' => NotificationX_Helper::hide_data( 'display_types' ),                            
                                'priority' => 50
                            ) ),
                            'conversion_from'  => apply_filters('nx_conversion_from', array(
                                'type'      => 'select',
                                'label'     => __('From' , 'notificationx'),
                                'default'   => 'custom',
                                'options'   => NotificationX_Helper::conversion_from(),
                                'priority'	=> 60,
                                'toggle'   => NotificationX_Helper::conversion_toggle(),
                            ))
                        ),
                    ),
                ))
            ),
            'content_tab' => array(
                'title'         => __('Content', 'notificationx'),
                'icon'          => 'pencil.svg',
                'sections'      => apply_filters('nx_content_tab_sections', array(
                    'content_config'        => array(
                        'title'             => __('Content', 'notificationx'),
                        'fields'            => array(
                            'press_content' => array(
                                'type'     => 'editor',
                                'label'    => __('Content' , 'notificationx'),
                                'priority' => 50,
                            ),
                            'button_text' => array(
                                'type'     => 'text',
                                'label'    => __('Button Text' , 'notificationx'),
                                'priority' => 60,
                            ),
                            'button_url' => array(
                                'type'     => 'text',
                                'label'    => __('Button URL' , 'notificationx'),
                                'priority' => 70,
                            ),
                        ),
                    ),
                    'countdown_timer' => array(
                        'title'  => __('Countdown Timer', 'notificationx'),
                        'fields' => array(
                            'enable_countdown' => array(
                                'label' => __('Enable Countdown', 'notificationx'),
                                'type'  => 'checkbox',
                                'toggle'  => [
                                    '1' => [
                                        'fields' => ['countdown_text', 'countdown_time']
                                    ]
                                ],
                            ),
                            'countdown_text' => array(
                                'label' => __('Countdown Text', 'notificationx'),
                                'type'  => 'text',
                            ),
                            'countdown_time' => array(
                                'label' => __('Countdown Time', 'notificationx'),
                                'type'  => 'time',
                            )
                        )
                    )
                ))
            ),
            'design_tab' => array(
                'title'      => __('Design', 'notificationx'),
                'icon'       => 'magic-wand.svg',
                'sections'   => apply_filters('nx_design_tab_sections', array(
                    'bar_themes' => array(
                        'title'      => __('Themes', 'notificationx'),
                        'priority' => 3,
                        'fields'   => array(
                            'bar_theme' => array(
                                'type'      => 'theme',
                                'priority'	=> 5,
                                'default'	=> 'theme-one',
                                'options'   => NotificationX_Helper::bar_colored_themes(),
                            ),
                            'bar_advance_edit' => array(
                                'type'      => 'adv_checkbox',
                                'priority'	=> 10,
                                'default'	=> 0,
                                'toggle' => [
                                    1 => [
                                        'sections' => ['bar_design', 'bar_typography']
                                    ]
                                ]
                            ),
                        )
                    ),
                    'comment_themes' => array(
                        'title'      => __('Themes', 'notificationx'),
                        'priority' => 4,
                        'fields'   => array(
                            'comment_theme' => array(
                                'type'      => 'theme',
                                'priority'	=> 5,
                                'default'	=> 'theme-one',
                                'options'   => NotificationX_Helper::comment_colored_themes(),
                            ),
                            'comment_advance_edit' => array(
                                'type'      => 'adv_checkbox',
                                'priority'	=> 10,
                                'default'	=> 0,
                                'toggle' => [
                                    1 => [
                                        'sections' => ['comment_design', 'comment_image_design', 'comment_typography']
                                    ]
                                ]
                            ),
                        )
                    ),
                    'themes' => array(
                        'title'      => __('Themes', 'notificationx'),
                        'priority' => 5,
                        'fields'   => array(
                            'theme' => array(
                                'type'      => 'theme',
                                'priority'	=> 5,
                                'default'	=> 'theme-one',
                                'options'   => NotificationX_Helper::colored_themes(),
                            ),
                            'advance_edit' => array(
                                'type'      => 'adv_checkbox',
                                'priority'	=> 10,
                                'default'	=> 0,
                                'toggle' => [
                                    1 => [
                                        'sections' => ['design', 'image_design', 'typography']
                                    ]
                                ]
                            ),
                        )
                    ),
                    'design' => array(
                        'title'    => __('Design', 'notificationx'),
                        'priority' => 6,
                        'reset'    => true,
                        'fields'   => array(
                            'bg_color' => array(
                                'type'      => 'colorpicker',
                                'label'     => __('Background Color' , 'notificationx'),
                                'priority'	=> 5,
                                'default'	=> ''
                            ),
                            'text_color' => array(
                                'type'      => 'colorpicker',
                                'label'     => __('Text Color' , 'notificationx'),
                                'priority'	=> 10,
                                'default'	=> ''
                            ),
                            'border' => array(
                                'type'      => 'checkbox',
                                'label'     => __('Want Border?' , 'notificationx'),
                                'priority'	=> 15,
                                'default'	=> 0,
                                'toggle'	=> [
                                    '1' => [
                                        'fields' => [ 'border_size', 'border_style', 'border_color' ]
                                    ]
                                ],
                            ),
                            'border_size' => array(
                                'type'      => 'number',
                                'label'     => __('Border Size' , 'notificationx'),
                                'priority'	=> 20,
                                'default'	=> '1',
                                'description'	=> 'px',
                            ),
                            'border_style' => array(
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
                            'border_color' => array(
                                'type'      => 'colorpicker',
                                'label'     => __('Border Color' , 'notificationx'),
                                'priority'	=> 30,
                                'default'	=> ''
                            ),
                        )
                    ),
                    'comment_design' => array(
                        'title'    => __('Design', 'notificationx'),
                        'priority' => 7,
                        'reset'    => true,
                        'fields'   => array(
                            'comment_bg_color' => array(
                                'type'      => 'colorpicker',
                                'label'     => __('Background Color' , 'notificationx'),
                                'priority'	=> 5,
                                'default'	=> ''
                            ),
                            'comment_text_color' => array(
                                'type'      => 'colorpicker',
                                'label'     => __('Text Color' , 'notificationx'),
                                'priority'	=> 10,
                                'default'	=> ''
                            ),
                            'comment_border' => array(
                                'type'      => 'checkbox',
                                'label'     => __('Want Border?' , 'notificationx'),
                                'priority'	=> 15,
                                'default'	=> 0,
                                'toggle'	=> [
                                    '1' => [
                                        'fields' => [ 'comment_border_size', 'comment_border_style', 'comment_border_color' ]
                                    ]
                                ],
                            ),
                            'comment_border_size' => array(
                                'type'      => 'number',
                                'label'     => __('Border Size' , 'notificationx'),
                                'priority'	=> 20,
                                'default'	=> '1',
                                'description'	=> 'px',
                            ),
                            'comment_border_style' => array(
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
                            'comment_border_color' => array(
                                'type'      => 'colorpicker',
                                'label'     => __('Border Color' , 'notificationx'),
                                'priority'	=> 30,
                                'default'	=> ''
                            ),
                        )
                    ),
                    'image_design' => array(
                        'title'      => __('Image Appearance', 'notificationx'),
                        'priority' => 8,
                        'reset'    => true,
                        'fields'   => array(
                            'image_shape' => array(
                                'type'      => 'select',
                                'label'     => __('Image Shape' , 'notificationx'),
                                'priority'	=> 5,
                                'default'	=> 'circle',
                                'options'	=> [
                                    'circle' => __('Circle', 'notificationx'),
                                    'rounded' => __('Rounded', 'notificationx'),
                                    'square' => __('Square', 'notificationx'),
                                ],
                            ),
                            'image_position' => array(
                                'type'      => 'select',
                                'label'     => __('Position' , 'notificationx'),
                                'priority'	=> 10,
                                'default'	=> 'left',
                                'options'	=> [
                                    'left' => __('Left', 'notificationx'),
                                    'right' => __('Right', 'notificationx'),
                                ],
                            ),
                        )
                    ),
                    'comment_image_design' => array(
                        'title'      => __('Image Appearance', 'notificationx'),
                        'priority' => 9,
                        'reset'    => true,
                        'fields'   => array(
                            'comment_image_shape' => array(
                                'type'      => 'select',
                                'label'     => __('Image Shape' , 'notificationx'),
                                'priority'	=> 5,
                                'default'	=> 'circle',
                                'options'	=> [
                                    'circle' => __('Circle', 'notificationx'),
                                    'rounded' => __('Rounded', 'notificationx'),
                                    'square' => __('Square', 'notificationx'),
                                ],
                            ),
                            'comment_image_position' => array(
                                'type'      => 'select',
                                'label'     => __('Position' , 'notificationx'),
                                'priority'	=> 10,
                                'default'	=> 'left',
                                'options'	=> [
                                    'left' => __('Left', 'notificationx'),
                                    'right' => __('Right', 'notificationx'),
                                ],
                            ),
                        )
                    ),
                    'typography' => array(
                        'title'      => __('Typography', 'notificationx'),
                        'priority' => 10,
                        'reset'    => true,
                        'fields'   => array(
                            'first_font_size' => array(
                                'type'      => 'number',
                                'label'     => __('Font Size' , 'notificationx'),
                                'priority'	=> 5,
                                'default'	=> '13',
                                'description'	=> 'px',
                                'help'	=> __( 'This font size will be applied for <mark>first</mark> row', 'notificationx' ),
                            ),
                            'second_font_size' => array(
                                'type'      => 'number',
                                'label'     => __('Font Size' , 'notificationx'),
                                'priority'	=> 10,
                                'default'	=> '14',
                                'description'	=> 'px',
                                'help'	=> __( 'This font size will be applied for <mark>second</mark> row', 'notificationx' ),
                            ),
                            'third_font_size' => array(
                                'type'      => 'number',
                                'label'     => __('Font Size' , 'notificationx'),
                                'priority'	=> 15,
                                'default'	=> '11',
                                'description'	=> 'px',
                                'help'	=> __( 'This font size will be applied for <mark>third</mark> row', 'notificationx' ),
                            ),
                        )
                    ),
                    'comment_typography' => array(
                        'title'      => __('Typography', 'notificationx'),
                        'priority' => 11,
                        'reset'    => true,
                        'fields'   => array(
                            'comment_first_font_size' => array(
                                'type'      => 'number',
                                'label'     => __('Font Size' , 'notificationx'),
                                'priority'	=> 5,
                                'default'	=> '13',
                                'description'	=> 'px',
                                'help'	=> __( 'This font size will be applied for <mark>first</mark> row', 'notificationx' ),
                            ),
                            'comment_second_font_size' => array(
                                'type'      => 'number',
                                'label'     => __('Font Size' , 'notificationx'),
                                'priority'	=> 10,
                                'default'	=> '14',
                                'description'	=> 'px',
                                'help'	=> __( 'This font size will be applied for <mark>second</mark> row', 'notificationx' ),
                            ),
                            'comment_third_font_size' => array(
                                'type'      => 'number',
                                'label'     => __('Font Size' , 'notificationx'),
                                'priority'	=> 15,
                                'default'	=> '11',
                                'description'	=> 'px',
                                'help'	=> __( 'This font size will be applied for <mark>third</mark> row', 'notificationx' ),
                            ),
                        )
                    ),
                    'bar_design' => array(
                        'title'      => __('Design', 'notificationx'),
                        'priority' => 12,
                        'reset'    => true,
                        'fields'   => array(
                            'bar_bg_color' => array(
                                'type'      => 'colorpicker',
                                'label'     => __('Background Color' , 'notificationx'),
                                'priority'	=> 5,
                                'default'	=> ''
                            ),
                            'bar_text_color' => array(
                                'type'      => 'colorpicker',
                                'label'     => __('Text Color' , 'notificationx'),
                                'priority'	=> 10,
                                'default'	=> ''
                            ),
                        )
                    ),
                    'bar_typography' => array(
                        'title'      => __('Typography', 'notificationx'),
                        'priority' => 13,
                        'reset'    => true,
                        'fields'   => array(
                            'bar_font_size' => array(
                                'type'      => 'number',
                                'label'     => __('Font Size' , 'notificationx'),
                                'priority'	=> 5,
                                'default'	=> '13',
                                'description'	=> 'px',
                                'help'	=> __( 'This font size will be applied for <mark>first</mark> row', 'notificationx' ),
                            ),
                        )
                    ),
                ))
            ),
            'display_tab' => array(
                'title'         => __('Display', 'notificationx'),
                'icon'          => 'screen.svg',
                'sections'      => apply_filters('nx_display_tab_sections', array(
                    'image' => array(
                        'title'    => __('Image', 'notificationx'),
                        'priority' => 100,
                        'fields'   => array(
                            'show_default_image'  => array(
                                'type'      => 'checkbox',
                                'label'     => __('Show Default Image' , 'notificationx'),
                                'priority'	=> 5,
                                'toggle'	=> [
                                    '1' => [
                                        'fields' => [ 'image_url' ]
                                    ]
                                ],
                                'description' => __('If checked, this will show in notifications.', 'notificationx'),
                            ),
                            'image_url'  => array(
                                'type'      => 'media',
                                'label'     => __('Default Image' , 'notificationx'),
                                'priority'	=> 10,
                            ),
                            'show_product_image' => array(
                                'label'       => __( 'Show Product Image', 'notificationx' ),
                                'priority'    => 15,
                                'type'        => 'checkbox',
                                'default'     => true,
                                'description' => __( 'Show the product image in notification', 'notificationx' ),
                            )
                        )
                    ),
                    'visibility'        => array(
                        'title'    => __('Visibility', 'notificationx'),
                        'priority' => 1000,
                        'fields'   => array(
                            'show_on'  => array(
                                'type'      => 'select',
                                'label'     => __('Show On' , 'notificationx'),
                                'priority'	=> 10,
                                'options'   => [
                                    'everywhere'       => __('Show Everywhere' , 'notificationx'),
                                    'on_selected'      => __('Show On Selected' , 'notificationx'),
                                    'hide_on_selected' => __('Hide On Selected' , 'notificationx'),
                                ],
                                'toggle' => [
                                    'on_selected' => [ 
                                        'fields' => [ 'all_locations' ]
                                    ],
                                    'hide_on_selected' => [ 
                                        'fields' => [ 'all_locations' ]
                                    ]
                                ],
                                'hide' => [
                                    'everywhere' => [ 
                                        'fields' => [ 'all_locations' ]
                                    ],
                                ],
                                'default'	=> 'everywhere'
                            ),
                            'all_locations'  => array(
                                'type'      => 'select',
                                'label'     => __('Locations' , 'notificationx'),
                                'priority'	=> 20,
                                'options'   => NotificationX_Locations::locations(),
                            ),
                            'show_on_display'  => array(
                                'type'      => 'select',
                                'label'     => __('Display' , 'notificationx'),
                                'priority'	=> 200,
                                'options'   => [
                                    'always'          => __('Always' , 'notificationx'),
                                    'logged_out_user' => __('Logged Out User' , 'notificationx'),
                                    'logged_in_user'  => __('Logged In User' , 'notificationx'),
                                ],
                            )
                        ),
                    ),
                ))
            ),
            'customize_tab' => array(
                'title'         => __('Customize', 'notificationx'),
                'icon'          => 'cog.svg',
                'sections'      => apply_filters('nx_customize_tab_sections', array(
                    'appearance'        => array(
                        'title'    => __('Appearance', 'notificationx'),
                        'priority' => 100,
                        'fields'   => array(
                            'pressbar_position'  => array(
                                'type'      => 'select',
                                'label'     => __('Position' , 'notificationx'),
                                'priority'	=> 40,
                                'options'   => [
                                    'top'       => __('Top' , 'notificationx'),
                                    'bottom'      => __('Bottom' , 'notificationx'),
                                ],
                            ),
                            'conversion_position'  => array(
                                'type'      => 'select',
                                'label'     => __('Position' , 'notificationx'),
                                'priority'	=> 50,
                                'options'   => [
                                    'bottom_left'       => __('Bottom Left' , 'notificationx'),
                                    'bottom_right'      => __('Bottom Right' , 'notificationx'),
                                ],
                            ),
                            'sticky_bar'  => array(
                                'type'        => 'checkbox',
                                'label'       => __('Sticky Bar?' , 'notificationx'),
                                'priority'    => 60,
                                'default'     => 0,
                                'description' => __('If checked, this will fixed Notification Bar at top or bottom.', 'notificationx'),
                            ),
                            'close_button'  => array(
                                'type'        => 'checkbox',
                                'label'       => __('Show Close Button' , 'notificationx'),
                                'default'     => true,
                                'priority'    => 70,
                                'description' => __('It will display the close button at the top right corner.', 'notificationx'),
                            ),
                            'hide_on_mobile'  => array(
                                'type'        => 'checkbox',
                                'label'       => __('Hide On Mobile' , 'notificationx'),
                                'priority'    => 200,
                                'default'     => 0,
                                'description' => __(' It will hide the notification on mobile devices.', 'notificationx'),
                            ),
                        ),
                    ),
                    'timing'        => array(
                        'title'       => __('Timing', 'notificationx'),
                        'priority'    => 200,
                        'collapsable' => true,
                        'fields'      => array(
                            'delay_before'  => array(
                                'type'        => 'number',
                                'label'       => __('Delay before 1st notification' , 'notificationx'),
                                'description' => __('seconds', 'notificationx'),
                                'help'        => __('Initial Delay', 'notificationx'),
                                'priority'    => 40,
                                'default'     => 5,
                            ),
                            'initial_delay'  => array(
                                'type'        => 'number',
                                'label'       => __('Initial Delay' , 'notificationx'),
                                'description' => __('seconds', 'notificationx'),
                                'help'        => __('Initial Delay', 'notificationx'),
                                'priority'    => 45,
                                'default'     => 5,
                            ),
                            'auto_hide'  => array(
                                'type'        => 'checkbox',
                                'label'       => __('Auto Hide' , 'notificationx'),
                                'description' => __('If checked, notification bar will be hidden after the time set below.', 'notificationx'),
                                'priority'    => 50,
                                'toggle'	=> [
                                    '1' => [
                                        'fields' => [ 'hide_after' ]
                                    ]
                                ],
                                'default'     => false,
                            ),
                            'hide_after'  => array(
                                'type'        => 'number',
                                'label'       => __('Hide After' , 'notificationx'),
                                'description' => __('seconds', 'notificationx'),
                                'help'        => __('Hide after 60 seconds', 'notificationx'),
                                'priority'    => 55,
                                'default'     => 60,
                            ),
                            'display_for'  => array(
                                'type'        => 'number',
                                'label'       => __('Display For' , 'notificationx'),
                                'description' => __('seconds', 'notificationx'),
                                'help'        => __('Display each notification for * seconds', 'notificationx'),
                                'priority'    => 60,
                                'default'     => 5,
                            ),
                            'delay_between'  => array(
                                'type'        => 'number',
                                'label'       => __('Delay Between' , 'notificationx'),
                                'description' => __('seconds', 'notificationx'),
                                'help'        => __('Delay between each notification', 'notificationx'),
                                'priority'    => 70,
                                'default'     => 5,
                            ),
                        ),
                    ),
                    'behaviour'        => array(
                        'title'       => __('Behaviour', 'notificationx'),
                        'priority'    => 300,
                        'collapsable' => true,
                        'fields'      => array(
                            'display_last'  => array(
                                'type'        => 'number',
                                'label'       => __('Display the last' , 'notificationx'),
                                'description' => 'conversions',
                                'default'     => 20,
                                'priority'    => 40,
                                'max'         => 20,
                            ),
                            'display_from'  => array(
                                'type'        => 'number',
                                'label'       => __('Display From The Last' , 'notificationx'),
                                'priority'    => 45,
                                'default'     => 2,
                                'description' => 'days',
                            ),
                            'loop'  => array(
                                'type'        => 'checkbox',
                                'label'       => __('Loop notification' , 'notificationx'),
                                'priority'    => 50,
                                'default'     => true,
                            ),
                            'link_open'  => array(
                                'type'        => 'checkbox',
                                'label'       => __('Open link in new tab' , 'notificationx'),
                                'priority'    => 60,
                                'default'     => false,
                            ),
                        ),
                    ),
                ))
            ),
        ))
    );
}