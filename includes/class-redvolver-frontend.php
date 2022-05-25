<?php

class Redvolver_Frontend {

	private $prefix = 'rv_';
	private static $_instance = null;
  private $currentID = 0;

  public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
			self::$_instance->hooks();
		}
		return self::$_instance;
	}

    public function isEnabled() {

        $calendar_page = get_field('calendar_page','option');
				$calendar_all_page = get_field('calendar_all_page','option');
				$report_page = get_field('report_page','option');
				$project_report_page = get_field('project_report_page','option');
				$estimate_report_page = get_field('estimate_report_page','option');
				$user_report_page = get_field('user_report_page','option');
				$tag_list_page = get_field('tag_list_page','option');
				$tag_add_page = get_field('tag_add_page','option');
				$client_list_page = get_field('client_list_page','option');
				$client_add_page = get_field('client_add_page','option');
				$dashboard_page = get_field('dashboard_page','option');
        $queried_object = get_queried_object();

        if ( is_singular( 'jrr-spotlight' ) ) {
            return true;
        }
        if ( is_singular( 'jrr-recruitment' ) ) {
            return true;
        }
        if ( $calendar_page ) {
            if ( is_page( $calendar_page->ID ) ) {
                return true;
            }
        }
				if ( $calendar_all_page ) {
            if ( is_page( $calendar_all_page->ID ) ) {
                return true;
            }
        }
				if ( $report_page ) {
            if ( is_page( $report_page->ID ) ) {
                return true;
            }
        }
				if ( $project_report_page ) {
            if ( is_page( $project_report_page->ID ) ) {
                return true;
            }
        }
				if ( $estimate_report_page ) {
            if ( is_page( $estimate_report_page->ID ) ) {
                return true;
            }
        }
				if ( $user_report_page ) {
            if ( is_page( $user_report_page->ID ) ) {
                return true;
            }
        }
				if ( $tag_list_page ) {
            if ( is_page( $tag_list_page->ID ) ) {
                return true;
            }
        }
				if ( $tag_add_page ) {
            if ( is_page( $tag_add_page->ID ) ) {
                return true;
            }
        }
				if ( $client_list_page ) {
            if ( is_page( $client_list_page->ID ) ) {
                return true;
            }
        }
				if ( $client_add_page ) {
            if ( is_page( $client_add_page->ID ) ) {
                return true;
            }
        }

				if ( $dashboard_page ) {
            if ( is_page( $dashboard_page->ID ) ) {
                return true;
            }
        }

				if ( is_page() && is_page_template('rv-schedule.php') ) {
					return true;
				}

        return false;
    }

		public function hooks() {
				add_action( 'wp_head', array($this, 'rvb_head') );
        add_action( 'wp_enqueue_scripts', array($this, 'rvb_enqueue_scripts') , 99);
        add_action( 'wp_footer', array($this, 'rvb_footer') );

        add_filter( 'body_class', array($this, 'rvb_body_class' ) );
    }

    public function rvb_enqueue_scripts() {

				$estimate_report_page = get_field('estimate_report_page','option');
        $enabled = $this->isEnabled();

        if($enabled) {

						global $wp_styles;
						$wp_styles->queue = array();

						global $wp_scripts;
            $wp_scripts->queue = array();

						wp_enqueue_style('dashicons');
						wp_enqueue_style('admin-bar');


            //wp_enqueue_style('fonta','https://use.fontawesome.com/releases/v5.1.0/css/all.css',false);
						wp_enqueue_style( 'fontaw', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css', array(), null );
            //wp_enqueue_style( 'fullcalendar', RVC_PLUGIN_URL . 'ext/fullcalendar/fullcalendar.min.css', false );
						//wp_enqueue_style( 'flatpickr', RVC_PLUGIN_URL . 'ext/flatpickr/flatpickr.min.css', false );

						//wp_enqueue_style( 'fullcalendarprint', RVC_PLUGIN_URL . 'ext/fullcalendar/fullcalendar.print.min.css', false );
						//wp_enqueue_style( 'izitoast', RVC_PLUGIN_URL . 'ext/izitoast/css/iziToast.min.css', false );
						//wp_enqueue_style( 'select2', RVC_PLUGIN_URL . 'ext/select2/select2.min.css', false );

						// if ( $estimate_report_page ) {
		        //     if ( is_page( $estimate_report_page->ID ) ) {
						//
						// 			wp_enqueue_style( 'datatable', RVC_PLUGIN_URL . 'ext/datatables/datatables.min.css', false );
		        //     }
		        // }
						//wp_enqueue_style( 'metroniccss', RVC_PLUGIN_URL . 'css/metronic.min.css"', false );
            wp_enqueue_style( 'rvcss', RVC_PLUGIN_URL . 'css/styles.css"', false );

            // wp_register_script( 'modernizr', RVC_PLUGIN_URL . 'ext/modernizr.custom.js', null, null, false );
            // wp_enqueue_script( 'modernizr' );
						//

            // wp_register_script( 'popper', RVC_PLUGIN_URL . 'ext/popper.min.js', array('jquery'), null, true );
            // wp_enqueue_script( 'popper' );
						// //
            // wp_register_script( 'bootstrap', RVC_PLUGIN_URL . 'ext/bootstrap.min.js', array('jquery'), null, true );
            // wp_enqueue_script( 'bootstrap' );

						// wp_register_script( 'moment', RVC_PLUGIN_URL . 'ext/moment.min.js', array('jquery'), null, true );
            // wp_enqueue_script( 'moment' );
						//
            // wp_register_script( 'fullcalendar', RVC_PLUGIN_URL . 'ext/fullcalendar/fullcalendar.min.js', array('jquery'), null, true );
            // wp_enqueue_script( 'fullcalendar' );
						//
						// wp_register_script( 'fullcalendarit', RVC_PLUGIN_URL . 'ext/fullcalendar/locale/it.js', array('jquery'), null, true );
            // wp_enqueue_script( 'fullcalendarit' );
						//
						// wp_register_script( 'pikaday', RVC_PLUGIN_URL . 'ext/pikaday.js', array('jquery'), null, true );
            // wp_enqueue_script( 'pikaday' );
						//
						// wp_register_script( 'izitoast', RVC_PLUGIN_URL . 'ext/izitoast/js/iziToast.min.js', array('jquery'), null, true );
            // wp_enqueue_script( 'izitoast' );
						//
						// wp_register_script( 'select2', RVC_PLUGIN_URL . 'ext/select2/select2.min.js', array('jquery'), null, true );
            // wp_enqueue_script( 'select2' );
						//
						// wp_register_script( 'highcharts', RVC_PLUGIN_URL . 'ext/highcharts/highcharts.js', array('jquery'), null, true );
            // wp_enqueue_script( 'highcharts' );
						//
						// wp_register_script( 'highchartsexp', RVC_PLUGIN_URL . 'ext/highcharts/modules/exporting.js', array('jquery'), null, true );
            // wp_enqueue_script( 'highchartsexp' );
						//
						// wp_register_script( 'highchartsexpd', RVC_PLUGIN_URL . 'ext/highcharts/modules/export-data.js', array('jquery'), null, true );
            // wp_enqueue_script( 'highchartsexpd' );

						//wp_register_script( 'extjs', RVC_PLUGIN_URL . 'js/ext.js', array('jquery'), null, true );
						wp_register_script( 'extjs', RVC_PLUGIN_URL . 'js/ext.js', null, null, true );
            wp_enqueue_script( 'extjs' );

						// if ( $estimate_report_page ) {
		        //     if ( is_page( $estimate_report_page->ID ) ) {
						//
						// 			wp_register_script( 'datatable', RVC_PLUGIN_URL . 'ext/datatables/datatables.min.js', array('jquery'), null, true );
						// 			wp_enqueue_script( 'datatable' );
		        //     }
		        // }

            //wp_register_script( 'rvjs', RVC_PLUGIN_URL . 'js/main.js', array('jquery','jquery-ui-core'), null, true );
						wp_register_script( 'rvjs', RVC_PLUGIN_URL . 'js/main.js', null, null, true );
            wp_localize_script( 'rvjs', 'rvlocalize', array(
                'dataurl' => RVC_PLUGIN_URL,
								'ajaxurl' => admin_url( 'admin-ajax.php' )
            ));
            wp_enqueue_script( 'rvjs' );

        }
    }



    public function rvb_footer() {

        $enabled = $this->isEnabled();

        if($enabled) {

        }
    }

		public function rvb_head() {

        $enabled = $this->isEnabled();
				$data = array();

        if($enabled) {
					$terms = get_terms( 'rvc-tag', array(
					    'orderby' => 'name',
							'order' => 'ASC',
					    'hide_empty' => false
					) );
					if ( $terms ) {
						foreach ($terms as $term) {
							$data[] = array(
								'id' => $term->term_id,
								'text' => $term->name,
							);
						}
						?>
						<script>
							var rvdata = <?php echo json_encode( array_values($data) ); ?>;
						</script>
						<?php
					}

					$terms = get_terms( 'rvc-client', array(
					    'orderby' => 'name',
							'order' => 'ASC',
					    'hide_empty' => false
					) );
					if ( $terms ) {
						foreach ($terms as $term) {
							$clientdata[] = array(
								'id' => $term->term_id,
								'text' => $term->name,
							);
						}
						?>
						<script>
							var rvclientdata = <?php echo json_encode( array_values($clientdata) ); ?>;
						</script>
						<?php
					}

					$enable_asana = get_field('enable_asana','option');
					$asana_token = get_field('asana_token','option');

					if ( $enable_asana && $asana_token ) {
						$current_user = wp_get_current_user();
	          $data = RVC()->db->table('rv_asana')->where('asana_email', $current_user->user_email )->get();
						$asanadata = array();
						foreach ($data as $da) {
							$asanadata[] = array(
								'id' => $da->id,
								'text' => $da->asana_title.' - '.$da->asana_project,
							);
						}
						?>
						<script>
							var rvasanadata = <?php echo json_encode( array_values($asanadata) ); ?>;
						</script>
						<?php
					}

        }
    }

    public function rvb_body_class() {
        $enabled = $this->isEnabled();

        if($enabled) {
    	   //$classes[] = 'rvc';
				 	$classes[] = 'm--skin-';
				 	$classes[] = 'm-page--loading-enabled';
					$classes[] = 'm-page--loading';
					$classes[] = 'm-content--skin-light';
					$classes[] = 'm-header--fixed';
					$classes[] = 'm-header--fixed-mobile';
					$classes[] = 'm-aside-left--offcanvas-default';
					$classes[] = 'm-aside-left--enabled';
					$classes[] = 'm-aside-left--fixed';
					$classes[] = 'm-aside-left--skin-dark';
					$classes[] = 'm-aside--offcanvas-default';

        	return $classes;
        }

    }

}

Redvolver_Frontend::instance();
