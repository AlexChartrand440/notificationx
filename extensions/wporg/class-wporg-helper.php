<?php

class NotificationXPro_WPOrg_Helper {

    public $plugin_information;
    public $theme_information;

    protected function get_links( $html, $strip_tags = false ) {

		$links = array();

		$doc       = new DOMDocument();
		$doc->loadHTML( $html );
		$linkTags  = $doc->getElementsByTagName( 'a' );

		foreach ( $linkTags as $tag ) {
			if ( $strip_tags ) {
				$links[] = trim( strip_tags( $tag->ownerDocument->saveXML( $tag ) ) );
			} else {
				$links[] = $tag->ownerDocument->saveXML( $tag );
			}
		}

		return $links;

    }
    
    protected function get_link_href( $html ) {

		$doc       = new DOMDocument();
		$doc->loadHTML( $html );
		$linkhrefs = array();
		$linkTags  = $doc->getElementsByTagName( 'a' );

		foreach ( $linkTags as $tag ) {
			$linkhrefs[] = $tag->getAttribute( 'href' );
		}

		if ( ! empty( $linkhrefs ) ) {
			return $linkhrefs[0];
		} else{
			return '';
		}

    }
    
	protected function get_image_src( $html ) {

		$doc        = new DOMDocument();
		$doc->loadHTML( $html );
		$imagepaths = array();
		$imageTags  = $doc->getElementsByTagName( 'img' );

		foreach ( $imageTags as $tag ) {
			$imagepaths[] = $tag->getAttribute( 'src' );
		}

		if ( ! empty( $imagepaths ) ) {
			return $imagepaths[0];
		} else{
			return '';
		}

    }
    
	protected function get_node_content( $html, $class ) {

		$dom     = new DOMDocument();
		$dom->loadHTML( $html );
		$finder  = new DomXPath( $dom );
		$nodes   = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $class ')]");
		$content = '';

		foreach ( $nodes as $element ) {
			$content = $element->ownerDocument->saveXML( $element );
		}

		return trim( strip_tags( $content ) );

    }
    
	protected function get_tag_content( $html, $search ) {

		$doc        = new DOMDocument();
		$doc->loadHTML( $html );
		$titlepaths = array();
		$titleTags  = $doc->getElementsByTagName( $search );

		foreach ( $titleTags as $tag ) {
			$titlepaths[] = $tag->ownerDocument->saveXML( $tag );
		}

		if ( ! empty( $titlepaths ) ) {
			return trim( strip_tags( $titlepaths[0] ) );
		} else{
			return '';
		}

	}

	protected function get_rating_content( $html, $class ) {

		$dom     = new DOMDocument();
		$dom->loadHTML( $html );
		$finder  = new DomXPath( $dom );
		$nodes   = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $class ')]");

		$content = '';
		foreach ( $nodes as $element ) {
			$content = $element->getAttribute('data-rating');
		}

		return trim( strip_tags( $content ) );

	}

    protected function extract_review_data( $review ){
        $data  = array();
		$links = $this->get_links( $review, true );

		$data['username']         = isset( $links[1] ) ? $links[1] : '';
		// $data['username']['text'] = isset( $links[1] ) ? $links[1] : '';
		// $data['username']['href'] = $this->get_link_href( $review );
		$data['avatar']['src']    = $this->get_image_src( $review );
		$data['content']          = iconv("UTF-8", 'ISO-8859-1', $this->get_node_content( $review, 'review-body' ));
		$data['plugin_name']      = $this->plugin_information->name;
		$data['title']            = iconv("UTF-8", 'ISO-8859-1', $this->get_tag_content( $review, 'h4' ));
		$data['timestamp']        = strtotime( iconv("UTF-8", 'ISO-8859-1', $this->get_node_content( $review, 'review-date' )) );
		$data['rating']           = $this->get_rating_content( $review, 'wporg-ratings' );

		return $data;
    }

    public function extract_reviews_from_html( $reviews ){
        $extracted_reviews = array();

        $dom           = new DOMDocument();
        $dom->loadHTML('<?xml encoding="UTF-8">' . $reviews);
        
        $finder        = new DomXPath( $dom );
        $nodes         = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' review ')]");
        
        foreach ( $nodes as $node ) {
            $raw_review = $node->ownerDocument->saveXML( $node );
			$review     = $this->extract_review_data( $raw_review );
            $extracted_reviews[] = $review;
		}

		return $extracted_reviews;
    }

    public function get_plugin_reviews( $plugin_slug ){
        if( ! function_exists('plugins_api') ) {
            require_once ABSPATH . '/wp-admin/includes/plugin-install.php';
        }

        $this->plugin_information = plugins_api( 'plugin_information', array( 'slug' => $plugin_slug, 'fields' => array( 'reviews' => true ) ) );

        return $this->plugin_information->sections['reviews'];
	}
	
	public function get_theme_reviews( $theme_slug ){
        if( ! function_exists('themes_api') ) {
            require_once ABSPATH . '/wp-admin/includes/themes.php';
        }

        // $this->theme_information = themes_api( 'theme_information', array( 'slug' => $theme_slug, 'fields' => array( 'reviews' => true ) ) );

        return [];
    }

    public function get_plugin_stats( $plugin_slug ){
        if( ! function_exists('plugins_api') ) {
            require_once ABSPATH . '/wp-admin/includes/plugin-install.php';
        }

		$this->plugin_information = plugins_api( 'plugin_information', array( 'slug' => $plugin_slug, 'fields' => array( 'downloaded' => true, 'icons' => true, 'historical_summary' => true ) ) );
		
		$needed_key = array(
			'name', 'slug', 'num_ratings', 'rating', 'homepage', 'version', 'downloaded', 'icons', 'active_installs', 'author_profile', 'author'
		);
		$new_data = [];

		foreach( $needed_key as $key => $value ) {
			if( isset( $this->plugin_information->$value ) ) {
				$new_data[ $value ] = $this->plugin_information->$value;
			}
		}

		if( isset( $new_data['homepage'] ) ) {
			$new_data['link'] = $new_data['homepage'];
			unset( $new_data['homepage'] );
		}

        return $new_data;
	}

    public function get_theme_stats( $theme_slug ){
        if( ! function_exists('themes_api') ) {
            require_once ABSPATH . '/wp-admin/includes/theme.php';
        }

		$this->theme_information = themes_api( 'theme_information', array( 'slug' => $theme_slug, 'fields' => array( 'downloaded' => true, 'sections' => true, 'theme_url' => true, 'photon_screenshots' => true, 'screenshot_url' => true ) ) );

		$needed_key = array(
			'name', 'slug', 'num_ratings', 'rating', 'homepage', 'version', 'downloaded', 'screenshot_url', 
		);
		$new_data = [];

		foreach( $needed_key as $key => $value ) {
			if( isset( $this->theme_information->$value ) ) {
				$new_data[ $value ] = $this->theme_information->$value;
			}
		}

		if( isset( $new_data['homepage'] ) ) {
			$new_data['link'] = $new_data['homepage'];
			unset( $new_data['homepage'] );
		}

		return $new_data;
	}
}