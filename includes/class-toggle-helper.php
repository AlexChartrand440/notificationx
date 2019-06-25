<?php
/**
 * This class will provide all kind of helper methods.
 */
class NotificationX_ToggleFields {

    public static function common_fields(){
        $common_fields = apply_filters( 'nx_meta_common_fields', array(
            'conversion_position',
            'delay_before',
            'display_last',
            'display_from',
            'display_for',
            'delay_between',
            'loop',
            'notification_preview',
            'conversion_size'
        ) );
        return $common_fields;
    }

    public static function common_sections(){
        $common_sections = apply_filters( 'nx_meta_common_fields', array(
            'image',
        ) );
        return $common_sections;
    }

    public static function woocommerce(){

        $fields = self::common_fields();
        $fields[] = 'conversion_from';
        $sections = self::common_sections();
        $sections[] = 'themes';
        $sections[] = 'conversion_link_options';

        return apply_filters( 'nx_woocommerce_toggle_fields', array(
            'fields' => $fields,
            'sections' => $sections,
        ));
    }

    public static function woocommerce_hide(){
        return apply_filters( 'nx_woocommerce_toggle_fields', array(
            'fields' => [ 'comments_template' ],
            'sections' => [],
        ));
    }

    public static function edd(){
        $fields = self::common_fields();
        $fields[] = 'conversion_from';

        $sections = self::common_sections();
        $sections[] = 'themes';
        $sections[] = 'conversion_link_options';

        return apply_filters( 'nx_edd_toggle_fields', array(
            'fields' => $fields,
            'sections' => $sections,
        ));
    }

    public static function edd_hide(){
        return apply_filters( 'nx_edd_toggle_fields', array(
            'fields' => [ 'comments_template' ],
            'sections' => [],
        ));
    }

    public static function reviews(){
        $fields = self::common_fields();

        return apply_filters( 'nx_reviews_toggle_fields', array(
            'fields' => array_merge( $fields, array( 'reviews_source' ) ),
            'sections' => array( 'image' )
        ));
    }

    public static function stats(){
        $fields = self::common_fields();

        return apply_filters( 'nx_stats_toggle_fields', array(
            'fields' => array_merge( $fields, array( 'stats_source' ) ),
            'sections' => array( 'image' )
        ));
    }

}
