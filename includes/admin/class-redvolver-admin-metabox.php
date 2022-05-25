<?php

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class RVB_Admin_Metabox {

		private $prefix = 'rv_';
		private static $_instance = null;


    public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
				self::$_instance->hooks();
			}
			return self::$_instance;
		}

		public function hooks() {

        add_action( 'carbon_fields_register_fields', array( $this, 'register_carbonfields' ) );
    }

    public function register_carbonfields() {

			$basic_options_container = Container::make( 'theme_options', __( 'Redvolver Botman', 'redvolver' ) )
			->add_tab( __( 'General' ), array(
				Field::make( 'select', 'rv_chatpage', __( 'Chat Page' ) )
					->set_options($this->getPages()),
				Field::make( 'select', 'rv_chattype', __( 'Chat Type' ) )
					->set_options( array(
							'' => '',
							'web' => 'Web',
							'facebook' => 'Facebook',
					) )
			) )
			->add_tab( __( 'Web' ), array(
			 	Field::make( 'text', 'rv_chatserver', __( 'Chat Server' ) , 'redvolver' ),
			 	Field::make( 'text', 'rv_frameendpoint', __( 'Frame Endpoint' ), 'redvolver' ),
			 	Field::make( 'text', 'rv_timeformat', __( 'Time Format' ), 'redvolver' )
				->set_default_value( 'HH:MM' ),
				Field::make( 'text', 'rv_datetimeformat', __( 'Date Time Format' ), 'redvolver' ),
				Field::make( 'text', 'rv_title', __( 'Title' ), 'redvolver' ),
				// introMessage
				// placeholderText
				// displayMessageTime
				// mainColor
				// bubbleBackground
				// bubbleAvatarUrl
				// desktopHeight
				// desktopWidth
				// mobileHeight
				// mobileWidth
				// videoHeight
				// aboutLink
				// aboutText
				// userId
			) )
			->add_tab( __( 'Facebook' ), array(
					Field::make( 'html', 'crb_html', __( 'Section Description' ) )
						->set_html( '<a href="https://developers.facebook.com/docs/messenger-platform/getting-started/quick-start" target="_blank">Official Guide</a>' ),
					Field::make( 'html', 'crb_html2', __( 'Section Description2' ) )
						->set_html( '<form action="'.admin_url( 'admin-post.php' ).'"><input type="hidden" name="action" value="test1"><input type="text" name="test" value=""><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"  /></form>' ),
					Field::make( 'html', 'crb_html3', __( 'Section Description2' ) )
						->set_html( '<form action="'.admin_url( 'admin-post.php' ).'"><input type="hidden" name="action" value="test2"><input type="text" name="test" value=""><input type="submit" name="submit" id="submit2" class="button button-primary" value="Save Changes"  /></form>' ),
					Field::make( 'text', 'rv_facebook_token', __( 'FACEBOOK TOKEN' ) ),
					Field::make( 'text', 'rv_facebook_verification', __( 'FACEBOOK VERIFICATION' ) ),
					Field::make( 'text', 'rv_facebook_app_secret', __( 'FACEBOOK APP SECRET' ) ),
			) );

				Container::make( 'theme_options', __( 'Chat Builder', 'redvolver' ) )
		    ->set_page_parent( $basic_options_container ) // reference to a top level container
		    ->add_fields( array(



		    ) );

				Container::make( 'theme_options', __( 'Broadcast Message', 'redvolver' ) )
		    ->set_page_parent( $basic_options_container ) // reference to a top level container
		    ->add_fields( array(
		        Field::make( 'text', 'crb_facebook_link', __( 'Facebook Link' ) ),
		        Field::make( 'text', 'crb_twitter_link', __( 'Twitter Link' ) ),
		    ) );


    }

    public function get_post_types() {

        $post_types = apply_filters( 'redvolver_post_types', get_post_types( array( 'public' => true ), 'objects' ) );

        foreach ( $post_types as $post_type ) {
            if ( $post_type->name  == 'attachment' ) continue;
            $types[ $post_type->name ] = $post_type->labels->name;
        }

        return $types;

    }

		public function getPages() {
			// The Query
			$args = array(
				'post_type' => 'page'
			);
			$the_query = new WP_Query( $args );
			$return = array();
			$return[0] = '';

			// The Loop
			if ( $the_query->have_posts() ) {
				while($the_query->have_posts()):$the_query->the_post();
					$return[ $the_query->post->ID ] = $the_query->post->post_title;
			  endwhile;
				wp_reset_postdata();
			}

			return $return;
		}

}

RVB_Admin_Metabox::instance();
