<?php
/**
 * Plugin Name: Redvolver Botman
 * Plugin URI: http://redvolver.it/
 * Description: Redvolver Botman Rocks!
 * Version: 0.0.1
 * Author: Redvolver
 * Author URI: https://redvolver.com/
 * Requires at least: 4.1
 * Tested up to: 4.9
 * Text Domain: redvolver
 * Domain Path: /languages/
 * License: GPL3+
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

include_once( __DIR__ . '/vendor/autoload.php' );

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class RV_Botman {
	/**
	 * The single instance of the class.
	 */
	private static $_instance = null;

	public $asana;

	public $roles;

	/**
	 * Main Instance.
	 *
	 * Ensures only one instance is loaded or can be loaded.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();

		}
		return self::$_instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->setup_constants();
		$this->includes();
		$this->init_hooks();
		$this->db = \WeDevs\ORM\Eloquent\Database::instance();

		do_action( 'rv_botman_loaded' );
	}

	public function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'redvolver' ), '1' );
	}

	/**
	 * Disable unserializing of the class.
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'redvolver' ), '1' );
	}

	private function setup_constants() {

		if ( ! defined( 'RVB_VERSION' ) ) {
			define( 'RVB_VERSION', '0.0.1' );
		}

		// Plugin Folder Path.
		if ( ! defined( 'RVB_PLUGIN_DIR' ) ) {
			define( 'RVB_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		//var_dump(RVC_PLUGIN_DIR);

		// Plugin Folder URL.
		if ( ! defined( 'RVB_PLUGIN_URL' ) ) {
			define( 'RVB_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

		//var_dump(RVC_PLUGIN_URL);
	}

	private function init_hooks() {

		// Activation - works with symlinks
		register_activation_hook( basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ), array( $this, 'activate' ) );
		register_uninstall_hook(basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ), array( 'RV_Search_Filter', 'deactivate' ));

		add_action( 'after_setup_theme', array( $this, 'carbon_boot' ) );
		add_action( 'after_setup_theme', array( $this, 'load_textdomain' ) );

		add_filter( 'template_include' , array($this, 'template_include') );

		add_action( 'wp_enqueue_scripts', array($this, 'rv_enqueue_scripts') , 99);
	}

	public function carbon_boot() {
		\Carbon_Fields\Carbon_Fields::boot();
	}

	public function activate() {
		//error_log('cron'.time());
		//$this->post_types->register_post_types();
		$this->cron_setup();
		$this->rvdb->insert_tables();
		flush_rewrite_rules();
	}

	public function deactivate() {
		$this->cron_stop();
	}

	public function cron_setup( ) {
		if (! wp_next_scheduled ( 'rvb_broadcast_test' )) {
			//wp_schedule_event(strtotime('09:57:00'), 'daily', 'rvb_broadcast_test');
    }

	}

	public function cron_stop( ) {
		wp_clear_scheduled_hook('rvb_broadcast_test');
	}

	public function rv_enqueue_scripts() {
		wp_enqueue_style( 'rvbotman', RVB_PLUGIN_URL . 'css/styles.css"', false );
	}

	public function includes() {


		include_once( 'includes/class-redvolver-post-types.php' );
		include_once( 'includes/class-redvolver-rvdb.php' );
		include_once( 'includes/class-redvolver-template-loader.php' );
		include_once( 'includes/admin/class-redvolver-admin-metabox.php' );
		//include_once( 'includes/class-redvolver-widget.php' );
		//include_once( 'includes/redvolver-shortcodes.php' );

		include_once( 'includes/functions.php' );

		if ( is_admin() ) {
			include_once( 'includes/admin/class-redvolver-admin.php' );
		}

		$this->post_types = RVBOT_Post_Types::instance();
		$this->rvdb = RVBOT_RVDB::instance();

		$this->template_loader = new RVB_Template_Loader;
	}

	public function load_textdomain() {

	}

	public function template_include( $template ) {

		$rv_chatpage = carbon_get_theme_option( 'rv_chatpage' );

		if ( $rv_chatpage ) {
			if ( is_page( $rv_chatpage ) ) {
				$template = $this->template_loader->get_template_part( 'page' ,'chat',true );
				//exit();
				// var_dump($rv_chatpage);
				// die();
			}
		}

		return $template;

	}

}


function RVBOT() {
	return RV_Botman::instance();
}

$GLOBALS['rv_botman'] = RVBOT();
