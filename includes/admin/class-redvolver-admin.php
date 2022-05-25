<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class RVB_Admin {

	private static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
			self::$_instance->hooks();
		}
		return self::$_instance;
	}

	public function hooks() {
		//add_action( 'admin_menu', array( $this, 'admin_menu' ) );

    //add_action( 'admin_head', array($this, 'rv_admin_transient') );
		//add_filter( 'manage_rv-search_posts_columns', array( $this,'set_rv_columns' ) );
		//add_action( 'manage_rv-search_posts_custom_column' , array( $this,'rv_columns'), 10, 2 );
  }

	function admin_menu() {
        add_options_page(
            __( 'Redvolver Botman', 'redvolver' ),
            __( 'Redvolver Botman', 'redvolver' ),
            'manage_options',
            'options_rv_botman',
            array(
                $this,
                'settings_page'
            )
        );
    }

    /**
     * Settings page display callback.
     */
    function settings_page() {
        echo __( 'This is the page content', 'textdomain' );
    }

	public function set_rv_columns($columns){
		$columns['rv_shortcode'] = __( 'Shortcode', 'redvolver' );

		return $columns;
	}

	public function rv_columns( $column, $post_id ) {
	    switch ( $column ) {

	        case 'rv_shortcode' :

	            echo '[rvsf id="'.$post_id.'"]';
	            break;

	    }
	}
}

RVB_Admin::instance();
