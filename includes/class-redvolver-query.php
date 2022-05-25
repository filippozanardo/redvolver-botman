<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class Redvolver_Query {

  private static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

  public function __construct() {

		$this->init_hooks();

	}

  public function init_hooks() {
    add_action( 'pre_get_posts', array($this ,'rv_search_query'),1000 );
  }

  public function rv_search_query($query){

    if($query->is_search()){
	    if($query->query_vars['s'] == 'rv_search_on' && isset($_GET['rvid'])){

        $formid = absint(str_replace('rvform-','',$_GET['rvid']));

        $post_type = carbon_get_post_meta($formid,'rv_post_type');

        $default_number = get_option('posts_per_page');

        $relation = carbon_get_post_meta($formid,'rv_relation');
        if ( empty($relation) ) $relation = 'OR';

        if ( !empty($_GET['rv_per_page']) ) {
          $number = $_GET['rv_per_page'];
        }else{
          $per_page = carbon_get_post_meta($formid,'rv_per_page');
          if ( !empty($per_page) ) {
            $number = $per_page;
          }else{
            $number = $default_number;
          }
        }

        $query_orderby = null;
        $orderby = carbon_get_post_meta($formid,'rv_orderby');
        if ( !empty($orderby) ) {
          $query_orderby = $orderby;
        }

        $query_order = null;
        $order = carbon_get_post_meta($formid,'rv_order');
        if ( !empty($order) ) {
          $query_order = $order;
        }

        $keyword = !empty($_GET['rvkeyword']) ? sanitize_text_field($_GET['rvkeyword']) : null;

        $paged = ( get_query_var( 'paged') ) ? get_query_var( 'paged' ) : 1;


        $args = array(
          'post_type' => $post_type,
          'post_status' => 'publish',
          //'meta_key'=> $ordermeta,
          'orderby' => $query_orderby,
          'order' => $query_order,
          'paged'=> $paged,
          'posts_per_page' => $number,
          //'meta_query' => $get_meta,
          //'tax_query' => $get_tax,
          's' => esc_html($keyword),
        );

        if ( !empty($_GET['rv-year']) ) {
          if ( is_array( $_GET['rv-year'] ) ) {
            foreach ($_GET['rv-year'] as $yv ) {
              $args['date_query'][] = array( 'year' => $yv );
            }
            if ( !empty($args['date_query']) ) $args['date_query']['relation'] = $relation;
          }else{
            $args['date_query'] = array( 'year' => $_GET['rv-year'] );
          }

        }

        $taxonomies = get_taxonomies( array('public'=> true), 'names' );

        //$args['tax_query'] = null;

        foreach ($_GET as $gkey => $gvalue) {
          $gpieces = explode("-", $gkey);

          if ( isset($gpieces[0]) && $gpieces[0] == 'rvtax' ) {
            if ( isset($gpieces[1]) ) {
              if ( in_array($gpieces[1], $taxonomies)) {
                if (!empty($gvalue)) {
                  $args['tax_query'][] =
                    array(
                        'taxonomy' => $gpieces[1],
                        'field'    => 'term_id',
                        'terms'    => $gvalue,
                    );
                }
              }
            }
          }
          if ( !empty($args['tax_query']) ) $args['tax_query']['relation'] = $relation;
        }

        // echo '<pre>';
        // print_r($_GET);
        // echo '</pre>';
        //
        // echo '<pre>';
        // print_r($args);
        // echo '</pre>';


        foreach($args as $k => $v){
          $query->set( $k, $v );
    		}
    		return $query;


      }
    }


  }


}

Redvolver_Query::instance();
