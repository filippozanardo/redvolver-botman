<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class Redvolver_Roles {

	/**
	 * Get things going
	 *
	 * @since 1.4.4
	 */
	public function __construct() {

		add_filter( 'map_meta_cap', array( $this, 'meta_caps' ), 10, 4 );
	}

	/**
	 * Add new roles with default WP caps
	 */
	public function add_roles() {
		add_role( 'rv_administrator', __( 'Calendar Administrator', 'redvolver' ), array(
			'read'                   => true,
			'edit_posts'             => true,
			'delete_posts'           => true,
			'unfiltered_html'        => true,
			'upload_files'           => true,
			'export'                 => true,
			'import'                 => true,
			'delete_others_pages'    => true,
			'delete_others_posts'    => true,
			'delete_pages'           => true,
			'delete_private_pages'   => true,
			'delete_private_posts'   => true,
			'delete_published_pages' => true,
			'delete_published_posts' => true,
			'edit_others_pages'      => true,
			'edit_others_posts'      => true,
			'edit_pages'             => true,
			'edit_private_pages'     => true,
			'edit_private_posts'     => true,
			'edit_published_pages'   => true,
			'edit_published_posts'   => true,
			'manage_categories'      => true,
			'manage_links'           => true,
			'moderate_comments'      => true,
			'publish_pages'          => true,
			'publish_posts'          => true,
			'read_private_pages'     => true,
			'read_private_posts'     => true
		) );

		add_role( 'rv_accountant', __( 'Calendar Accountant', 'redvolver' ), array(
		    'read'                   => true,
		    'edit_posts'             => false,
		    'delete_posts'           => false
		) );

		add_role( 'rv_worker', __( 'Calendar Worker', 'redvolver' ), array(
			'read'                   => true,
			'edit_posts'             => false,
			'upload_files'           => true,
			'delete_posts'           => false
		) );

	}

	/**
	 * Add new capabilities
	 */
	public function add_caps() {
		global $wp_roles;

		if ( class_exists('WP_Roles') ) {
			if ( ! isset( $wp_roles ) ) {
				$wp_roles = new WP_Roles();
			}
		}

		if ( is_object( $wp_roles ) ) {
			$wp_roles->add_cap( 'rv_administrator', 'view_rv_reports' );
			$wp_roles->add_cap( 'rv_administrator', 'edit_rv_tax' );
			$wp_roles->add_cap( 'rv_administrator', 'view_rv_settings' );

			$wp_roles->add_cap( 'administrator', 'view_rv_reports' );
			$wp_roles->add_cap( 'administrator', 'edit_rv_tax' );
			$wp_roles->add_cap( 'administrator', 'view_rv_settings' );

			// Add the main post type capabilities
			$capabilities = $this->get_core_caps();
			foreach ( $capabilities as $cap_group ) {
				foreach ( $cap_group as $cap ) {
					$wp_roles->add_cap( 'rv_administrator', $cap );
					$wp_roles->add_cap( 'administrator', $cap );
					$wp_roles->add_cap( 'rv_worker', $cap );
				}
			}

			$wp_roles->add_cap( 'rv_accountant', 'view_rv_reports' );
			$wp_roles->add_cap( 'rv_accountant', 'edit_rv_tax' );

		}
	}

	/**
	 * Gets the core post type capabilities
	 *
	 * @since  1.4.4
	 * @return array $capabilities Core post type capabilities
	 */
	public function get_core_caps() {
		$capabilities = array();

		$capability_types = array( 'rvc', 'rvs' );

		foreach ( $capability_types as $capability_type ) {
			$capabilities[ $capability_type ] = array(
				// Post type
				"edit_{$capability_type}",
				"read_{$capability_type}",
				"delete_{$capability_type}",
				"edit_{$capability_type}s",
				"edit_others_{$capability_type}s",
				"publish_{$capability_type}s",
				"read_private_{$capability_type}s",
				"delete_{$capability_type}s",
				"delete_private_{$capability_type}s",
				"delete_published_{$capability_type}s",
				"delete_others_{$capability_type}s",
				"edit_private_{$capability_type}s",
				"edit_published_{$capability_type}s",

				// Terms
				"manage_{$capability_type}_terms",
				"edit_{$capability_type}_terms",
				"delete_{$capability_type}_terms",
				"assign_{$capability_type}_terms",

				// Custom
				"view_{$capability_type}_stats",
				"import_{$capability_type}s",
			);
		}

		return $capabilities;
	}

	/**
	 * Map meta caps to primitive caps
	 */
	public function meta_caps( $caps, $cap, $user_id, $args ) {

		return $caps;

	}

	/**
	 * Remove core post type capabilities (called on uninstall)
	 */
	public function remove_caps() {

		global $wp_roles;

		if ( class_exists( 'WP_Roles' ) ) {
			if ( ! isset( $wp_roles ) ) {
				$wp_roles = new WP_Roles();
			}
		}

		if ( is_object( $wp_roles ) ) {
			$wp_roles->remove_cap( 'rv_administrator', 'view_rv_reports' );
			$wp_roles->remove_cap( 'rv_administrator', 'edit_rv_tax' );
			$wp_roles->remove_cap( 'rv_administrator', 'view_rv_settings' );

			$wp_roles->remove_cap( 'administrator', 'view_rv_reports' );
			$wp_roles->remove_cap( 'administrator', 'edit_rv_tax' );
			$wp_roles->remove_cap( 'administrator', 'view_rv_settings' );

			/** Remove the Main Post Type Capabilities */
			$capabilities = $this->get_core_caps();

			foreach ( $capabilities as $cap_group ) {
				foreach ( $cap_group as $cap ) {
					$wp_roles->remove_cap( 'rv_administrator', $cap );
					$wp_roles->remove_cap( 'administrator', $cap );
					$wp_roles->remove_cap( 'rv_worker', $cap );
				}
			}

			/** Shop Accountant Capabilities */
			$wp_roles->remove_cap( 'rv_accountant', 'view_rv_reports' );
			$wp_roles->remove_cap( 'rv_accountant', 'edit_rv_tax' );

		}
	}
}
