<?php

class NotificationX_WP_Comments_Extension extends NotificationX_Extension {

    public $type = 'wp_comments';
    public $template = 'comments_template';
    public $themeName = 'comment_theme';
    public $default_data;

    protected $notifications = [];

    public function __construct() {    
        parent::__construct( $this->template );
        $this->notifications = $this->get_notifications( $this->type );

        add_filter( 'nx_notification_link', array( $this, 'notification_link' ), 10, 2 );
    }

    public function fallback_data( $data ){
        $data['anonymous_post'] = __( 'No Post Title', 'notificationx' );
        $data['sometime'] = __( 'Sometimes ago', 'notificationx' );

        return $data;
    }

    /**
     * Main Screen Hooks
     */
    public function init_hooks(){
        add_filter( 'nx_metabox_tabs', array( $this, 'add_fields' ) );
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
        add_action( 'comment_post', array( $this, 'post_comment' ), 10, 2 );
        add_action( 'trash_comment', array( $this, 'delete_comment' ), 10, 2 );
        add_action( 'delete_comment', array( $this, 'delete_comment' ), 10, 2 );
        add_action( 'transition_comment_status', array( $this, 'transition_comment_status' ), 10, 3 );
    }
    /**
     * This function is responsible for the some fields of 
     * wp comments notification in display tab
     *
     * @param array $options
     * @return void
     */
    public function display_tab_section( $options ){
        $options['image']['fields']['show_avatar'] = array(
            'label'       => __( 'Show Gravatar', 'notificationx' ),
            'priority'    => 20,
            'type'        => 'checkbox',
            'default'     => true,
            'description' => __( 'Show the commenter gravatar in notification', 'notificationx' ),
        );

        return $options;
    }

    protected function init_fields(){
        $fields = array();

        $fields['comments_template_new'] = array(
            'type'     => 'template',
            'fields' => array(
                'first_param' => array(
                    'type'     => 'select',
                    'label'    => __('Notification Template' , 'notificationx'),
                    'priority' => 1,
                    'options'  => array(
                        'tag_name' => __('Full Name' , 'notificationx'),
                        'tag_first_name' => __('First Name' , 'notificationx'),
                        'tag_last_name' => __('Last Name' , 'notificationx'),
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
                        'tag_first_name' => array(
                            'fields' => [ 'custom_first_param' ]
                        ),
                        'tag_last_name' => array(
                            'fields' => [ 'custom_first_param' ]
                        ),
                    ),
                    'default' => 'tag_name'
                ),
                'custom_first_param' => array(
                    'type'     => 'text',
                    'priority' => 2,
                    'default' => __('Someone' , 'notificationx')
                ),
                'second_param' => array(
                    'type'     => 'text',
                    'priority' => 3,
                    'default' => __('commented on' , 'notificationx')
                ),
                'third_param' => array(
                    'type'     => 'select',
                    'priority' => 4,
                    'options'  => array(
                        'tag_post_title'       => __('Post Title' , 'notificationx'),
                        'tag_anonymous_post' => __('Anonymous Post' , 'notificationx'),
                    ),
                    'default' => 'tag_post_title'
                ),
                'fourth_param' => array(
                    'type'     => 'select',
                    'priority' => 5,
                    'options'  => array(
                        'tag_time'       => __('Definite Time' , 'notificationx'),
                        'sometime' => __('Sometimes ago' , 'notificationx'),
                    ),
                    'default' => 'tag_time'
                ),
            ),
            'label'    => __('Notification Template' , 'notificationx'),
            'priority' => 80,
        );

        $fields['comments_template_adv'] = array(
            'type'     => 'adv_checkbox',
            'priority' => 81,
            'button_text' => __('Advance Template' , 'notificationx'),
            'side' => 'right',
            'dependency' => array(
                1 => array(
                    'fields' => [ 'comments_template' ]
                )
            ),
        );

        $fields['comments_template'] = array(
            'type'     => 'template',
            'label'    => __('' , 'notificationx'),
            'priority' => 82,
            'defaults' => [
                __('{{name}} commented on', 'notificationx'), '{{post_title}}', '{{time}}'
            ],
            'variables' => [
                '{{name}}', '{{first_name}}', '{{last_name}}', '{{post_title}}', '{{time}}'
            ],
        );

        return $fields;
    }

    public function add_fields( $options ){
        $fields = $this->init_fields();

        foreach ( $fields as $name => $field ) {
            $options[ 'content_tab' ]['sections']['content_config']['fields'][ $name ] = $field;
        }

        return $options;
    }
    /**
     * This function responsible for making ready the notifications for the first time
     * we have made a notification.
     *
     * @param string $type
     * @param array $data
     * @return void
     */
    public function get_notification_ready( $type, $data = array() ){
        if( $this->type === $type ) {
            if( ! is_null( $comments = $this->get_comments( $data ) ) ) {
                $this->update_notifications( $this->type, $comments );
            }
        }
    }
    /**
     * This function is responsible for getting the comments from wp_comments data table.
     *
     * @param array $data
     * @return void
     */
    public function get_comments( $data ) {
        if( empty( $data ) ) return null;

        $from = isset( $data[ '_nx_meta_display_from' ] ) ? intval( $data[ '_nx_meta_display_from' ] ) : 0;
        $needed = isset( $data[ '_nx_meta_display_last' ] ) ? intval( $data[ '_nx_meta_display_last' ] ) : 0;

        $comments = get_comments([
            'status' => 'approve',
                'number'=> $needed,
                'date_query' => [
                    'after' => $from .' days ago',
                    'inclusive' => true,
                ]
        ]);

        if( empty( $comments ) ) return null;
        $new_comments = [];
        foreach( $comments as $comment ) {
            $new_comments[ $comment->comment_ID ] = $this->add( $comment );;
        }
        return $new_comments;
    }
    /**
     * This function is responsible for transition comment status 
     * from approved to unapproved or unapproved to approved
     *
     * @param string $new_status
     * @param string $old_status
     * @param WP_Comment $comment
     * @return void
     */
    public function transition_comment_status( $new_status, $old_status, $comment ){
        if( 'unapproved' === $new_status ) {
            $this->delete_comment( $comment->comment_ID, $comment );
        }
        if( 'approved' === $new_status ) {
            $this->post_comment( $comment->comment_ID, 1 );
        }
    }
    /**
     * This function is responsible for making comment notifications ready if comments is approved.
     *
     * @param int $comment_ID
     * @param bool $comment_approved
     * @return void
     */
    public function post_comment( $comment_ID, $comment_approved ){

        if( count( $this->notifications ) === $this->cache_limit ) {
            $sorted_data = NotificationX_Helper::sorter( $this->notifications, 'key' );
            array_pop( $sorted_data );
            $this->notifications = $sorted_data;
        }
        
        if( 1 === $comment_approved ){
            $this->notifications[ $comment_ID ] = $this->add( $comment_ID );
            /**
             * Save the data to 
             * notificationx_data ( options DB. )
             */
            $this->save( $this->type, $this->add( $comment_ID ), $comment_ID );
        }
        return;
    }
    /**
     * This function is responsible for making ready the comments data!
     *
     * @param int|WP_Comment $comment
     * @return void
     */
    public function add( $comment ){
        $comment_data = [];

        if( ! $comment instanceof WP_Comment ) {
            $comment = get_comment( intval( $comment ), 'OBJECT' );  
        }
        
        $comment_data['id']         = $comment->comment_ID;
        $comment_data['link']       = get_comment_link( $comment->comment_ID );
        $comment_data['post_title'] = get_the_title( $comment->comment_post_ID );
        $comment_data['post_link']  = get_permalink( $comment->comment_post_ID );
        $comment_data['timestamp']  = strtotime( $comment->comment_date );
        $comment_data['ip']  = $comment->comment_author_IP;



        $user_ip_data = $this->remote_get('http://ip-api.com/json/' . $comment->comment_author_IP );
        if( $user_ip_data ) {
            $comment_data['country'] = $user_ip_data->country;
            $comment_data['city']    = $user_ip_data->city;
        }

        if( $comment->user_id )  {
            $comment_data['user_id']    = $comment->user_id;
            $user                       = get_userdata( $comment->user_id );
            $comment_data['first_name'] = $user->first_name;
            $comment_data['last_name']  = $user->last_name;
            $comment_data['name']       = $user->first_name . ' ' . substr( $user->last_name, 0, 1 );
        } else {
            $comment_data['name'] = get_comment_author( $comment->comment_ID );
        }
        $comment_data['email'] = get_comment_author_email( $comment->comment_ID );
        return $comment_data;
    }
    /**
     * If a comment delete, than the notifications data set has to be updated as well.
     * this function is responsible for doing this.
     *
     * @param int $comment_ID
     * @param WP_Comment $comment
     * @return void
     */
    public function delete_comment( $comment_ID, $comment ){
        if( isset( $this->notifications[ $comment_ID ] ) ) {
            unset( $this->notifications[ $comment_ID ] );
            /**
             * Delete the data from 
             * notificationx_data ( options DB. )
             */
            $this->update_notifications( $this->type, $this->notifications );
        }
    }

    public function notification_link( $link, $settings ){
        if( $settings->display_type == 'comments' && $settings->comments_source == 'wp_comments' && $settings->comments_url == 'none' ) {
            return '';
        }
        return $link;
    }
}