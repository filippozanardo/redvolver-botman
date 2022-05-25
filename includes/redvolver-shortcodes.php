<?php

function rvsf_shortcode( $atts, $content = null ) {
  ob_start();
  RVSF()->template_loader->set_template_data( $atts ,'rv_param' )->get_template_part( 'shortcodes/rvsf' ,'shortcode',true );
  //RVSF()->template_loader->get_template_part( 'shortcodes/rvsf' ,'shortcode',true );
  $output = ob_get_clean();
	return $output;
}
add_shortcode( 'rvsf', 'rvsf_shortcode' );
