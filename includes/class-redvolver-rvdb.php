<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RVBOT_RVDB {

	private static $_instance = null;

	public $tables = array();

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Construct class.
	 */
	public function __construct() {
	}

	public function rvbotmanvalue_table() {
		global $wpdb;
		$rv_botman_value = $wpdb->prefix . 'rv_botman_value';

		// @codingStandardsIgnoreLine
		$this->tables[] = "CREATE TABLE " . $rv_botman_value . " (
			id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			rv_id bigint(20) NOT NULL,
			keyname varchar(255) NOT NULL,
			keyvalue varchar(255) NOT NULL,
			date datetime NOT NULL default '0000-00-00 00:00:00',
			PRIMARY KEY  (id)
		)" . $this->charset_collate . ";";
	}

	public function rvbotman_table() {
		global $wpdb;
		$rv_botman = $wpdb->prefix . 'rv_botman';

		// @codingStandardsIgnoreLine
		$this->tables[] = "CREATE TABLE " . $rv_botman . " (
			id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			rv_id bigint(20) NOT NULL,
			username varchar(255) NOT NULL,
			first_name varchar(255) NOT NULL,
			last_name varchar(255) NOT NULL,
			date datetime NOT NULL default '0000-00-00 00:00:00',
			PRIMARY KEY  (id)
		)" . $this->charset_collate . ";";
	}

	public function insert_tables() {
		global $wpdb;
		$this->charset_collate = ! empty( $wpdb->charset ) ? 'DEFAULT CHARACTER SET ' . $wpdb->charset : '';

		$this->rvbotman_table();
		$this->rvbotmanvalue_table();

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		if ( count( $this->tables ) > 0 ) {
			foreach ( $this->tables as $table ) {
				dbDelta( $table );
			}
		}
	}

}
