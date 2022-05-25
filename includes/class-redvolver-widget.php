<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class rvsf_widget extends WP_Widget {

  function __construct() {
      $widget_ops = array(
        'classname' => 'rvsf-widget',
        'description' => __( 'Redvolver Search Filter', 'redvolver' )
      );
      $control_ops = array('width' => 400, 'height' => 350, 'id_base' => 'rv-bea-servizi');
      parent::__construct('rvsf_widget', __( 'Redvolver Search', 'redvolver' ), $widget_ops );
  }

  function widget( $args, $instance ) {
    extract($args);

		$title = apply_filters('widget_title', $instance['title']);

		echo $before_widget;

		if ( $title )
		{
			echo $before_title . $title . $after_title;
		}

		$rvsf = $instance['rvsf'];

	  echo do_shortcode('[rvsf id="'.$rvsf.'"]');
    //echo '<h1>'.$rvsf.'</h1>';

		echo $after_widget;
  }

  function update( $new_instance, $old_instance ) {
		// Save widget options
		$instance = $old_instance;

		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['rvsf'] = ( ! empty( $new_instance['rvsf'] ) ) ? strip_tags( $new_instance['rvsf'] ) : '';

		return $instance;

	}

  function form( $instance )
	{

    $defaults = array(
			'title'            => '',
			'rvsf' => 0,
		);

		$instance = wp_parse_args( (array) $instance, $defaults );
    $rvsf = esc_attr($instance[ 'rvsf' ]);
    ?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', 'easy-digital-downloads' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo $instance['title']; ?>"/>
		</p>
    <?php
    $custom_posts = new WP_Query('post_type=rv-search&post_status=publish&posts_per_page=-1');
    ?>
    <p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'rvsf' ) ); ?>"><?php _e( 'Choose a Search Form:', 'easy-digital-downloads' ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'rvsf' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'rvsf' ) ); ?>">
        <option value="0"><?php _e('Please choose','redvolver'); ?></option>
        <?php while ($custom_posts->have_posts()) : $custom_posts->the_post(); ?>
				   <option value="<?php the_ID(); ?>" <?php if($rvsf==get_the_ID()){ echo ' selected="selected"'; } ?>><?php the_title(); ?></option>
        <?php endwhile; ?>
        <?php wp_reset_query(); ?>
			</select>
		</p>

		<?php
	}

}

function rvsf_register_widgets() {
	register_widget( 'rvsf_widget' );
}
add_action( 'widgets_init', 'rvsf_register_widgets' );
