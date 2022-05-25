<?php

if ( !empty($rv_param) ) {
  if ($rv_param->id != 0 ) {

    $nonce = wp_create_nonce  ('rvsfsearch');
    $post_type = carbon_get_post_meta($rv_param->id,'rv_post_type');
    $per_page = carbon_get_post_meta($rv_param->id,'rv_per_page');
    $orderby = carbon_get_post_meta($rv_param->id,'rv_orderby');
    $order = carbon_get_post_meta($rv_param->id,'rv_order');
    $button_text = carbon_get_post_meta($rv_param->id,'rv_button_text');
    $template = carbon_get_post_meta($rv_param->id,'rv_template');
    $method = 'method="get" action="'.home_url( '/' ).'"';

    $oldvalue = (isset($_GET['rvkeyword'])) ? $_GET['rvkeyword'] : '';


    if ( $post_type )  {
      $years = get_posts_years_array($post_type);
  ?>
    <div id="rvsf-<?php echo $rv_param->id; ?>">
      <form id="rvform-<?php echo $rv_param->id; ?>" <?php echo $method; ?>>

        <?php if($formtitle) { ?>
          <div class="rv-title"><?php echo get_the_title($rv_param->id); ?></div>
        <?php } ?>

        <div class="rv-field">
          <label class="rv-label rv-keyword"><?php echo $button_text; ?></label>
          <input id="rv-key-<?php echo $rv_param->id; ?>" type="text" name="rvkeyword" class="rv-text-input" value="<?php echo $oldvalue; ?>" />
        </div>

        <?php
          $fields = carbon_get_post_meta($rv_param->id,'rv_search-field');

          if ( $fields) {

            $args = array(
      			  'public'   => true,
      			);
      			$output = 'names';
      			$taxonomies = get_taxonomies( $args, $output );

            $i = 1;
            foreach ($fields as $field) {
              //var_dump($field);
              ?>

              <?php if ( in_array($field['_type'], $taxonomies)) { ?>
                  <?php
                  $args = array();

                  if ( $field['rv_hide_empty'] == 'yes' ) {
                    $args['hide_empty'] = true;
                  }else{
                    $args['hide_empty'] = false;
                  }

                  if ( $field['rv_taxsel'] ) {
                    $args[$field['rv_filter']] = $field['rv_taxsel'];
                  }

                  $terms = get_terms( $field['_type'], $args );
                  if ( $terms) {
                    $meold = false;
                    if ( isset($_GET['rvtax-'.$field['_type'].'-'.$i]) && !empty($_GET['rvtax-'.$field['_type'].'-'.$i]) ) {
                      $meold = $_GET['rvtax-'.$field['_type'].'-'.$i];
                    }
                  ?>
                <div class="rv-form-group rv-year">
                  <?php if ( $field['rv_label'] ) { ?>
                    <span class="rv-label"><?php echo $field['rv_label']; ?></span>
                  <?php } ?>

                  <?php if ( $field['rv_display'] == 'dropdown' ) { ?>
                    <?php
                      $dlabel = 'Select';
                      if ( !empty($field['rv_drop_label']) ) {
                        $dlabel = $field['rv_drop_label'];
                      }
                    ?>
                    <select id="rv-<?php echo $field['_type']; ?>-<?php echo $i; ?>" class="rv-select" name="rvtax-<?php echo $field['_type']; ?>-<?php echo $i; ?>">
                      <?php
                      $selected = '';
                      if (!$meold && !is_array($meold)) {
                          $selected = 'selected="true"';
                      }
                      ?>
                      <option value="" <?php echo $selected; ?>><?php echo $dlabel; ?></option>

                      <?php foreach ($terms as $term) { ?>
                        <?php
                        $selected = '';
                        if ($meold && !is_array($meold)) {
                          if ( $meold == $term->term_id ) {
                            $selected = 'selected="true"';
                          }
                        }
                        ?>

                        <option value="<?php echo $term->term_id; ?>" <?php echo $selected; ?>><?php echo $term->name; ?></option>
                      <?php } ?>

                    </select>
                  <?php }elseif ( $field['rv_display'] == 'radio' ) { ?>

                    <?php foreach ($terms as $term) { ?>
                      <?php
                      $checked = '';
                      if ($meold && is_array($meold)) {
                        if ( in_array($term->term_id,$meold) ) {
                          $checked = 'checked';
                        }
                      }
                      ?>
                      <input type="radio" name="rvtax-<?php echo $field['_type']; ?>-<?php echo $i; ?>[]" value="<?php echo $term->term_id; ?>" <?php echo $checked; ?>> <?php echo $term->name; ?> <br/>
                    <?php } ?>

                  <?php }elseif ( $field['rv_display'] == 'checkbox' ) { ?>

                    <?php foreach ($terms as $term) { ?>
                      <?php
                      $checked = '';
                      if ($meold && is_array($meold)) {
                        if ( in_array($term->term_id,$meold) ) {
                          $checked = 'checked';
                        }
                      }
                      ?>
                      <input type="checkbox" name="rvtax-<?php echo $field['_type']; ?>-<?php echo $i; ?>[]" value="<?php echo $term->term_id; ?>" <?php echo $checked; ?>> <?php echo $term->name; ?> <br/>
                    <?php } ?>

                  <?php } ?>

                </div>
                <?php } ?>
              <?php } ?>

              <?php if ( $field['_type'] == 'year') { ?>

              <?php $oldyear = (isset($_GET['rv-year'])) ? $_GET['rv-year'] : ''; ?>

              <div class="rv-form-group rv-year">
                <?php if ( $years ) { ?>

                  <?php if ( $field['rv_label'] ) { ?>
                    <span class="rv-label"><?php echo $field['rv_label']; ?></span>
                  <?php } ?>


                  <?php if ( $field['rv_display'] == 'dropdown' ) { ?>
                    <?php
                      $dlabel = 'Select';
                      if ( !empty($field['rv_drop_label']) ) {
                        $dlabel = $field['rv_drop_label'];
                      }
                    ?>
                    <select id="rv-year-<?php echo $i; ?>" class="rv-select" name="rv-year">
                      <option <?php if ($oldyear == '' ) echo 'selected="true"'; ?> value=""><?php echo $dlabel; ?></option>
                      <?php foreach ($years as $year) { ?>
                        <?php if ( $year > 0 ) { ?>
                          <option value="<?php echo $year; ?>" <?php if ($oldyear == $year ) echo 'selected="true"'; ?>><?php echo $year; ?></option>
                        <?php } ?>
                      <?php } ?>
                    </select>

                  <?php }elseif ( $field['rv_display'] == 'radio' ) { ?>
                    <?php foreach ($years as $year) { ?>

                      <?php if ( $year > 0 ) { ?>
                        <?php
                        $checked = false;
                        if ( is_array($oldyear) ) {
                          if ( in_array($year,$oldyear) ) {
                            $checked = true;
                          }
                        }
                        ?>
                        <input type="radio" name="rv-year[]" value="<?php echo $year; ?>" <?php if ($checked) echo 'checked'; ?>> <?php echo $year; ?> <br/>
                      <?php } ?>
                    <?php } ?>
                  <?php }elseif ( $field['rv_display'] == 'checkbox' ) { ?>
                    <?php foreach ($years as $year) { ?>
                      <?php if ( $year > 0 ) { ?>
                        <?php
                        $checked = false;
                        if ( is_array($oldyear) ) {
                          if ( in_array($year,$oldyear) ) {
                            $checked = true;
                          }
                        }
                        ?>
                        <input type="checkbox" name="rv-year[]" value="<?php echo $year; ?>" <?php if ($checked) echo 'checked'; ?>> <?php echo $year; ?> <br/>
                      <?php } ?>
                    <?php } ?>
                  <?php } ?>
                <?php } ?>

              </div>

              <?php }

              if ( $field['_type'] == 'meta') {
                //echo 'meta';
              }


              $i++;

            }
          }
        ?>


        <input type="hidden" name="rvnonce" value="<?php echo $nonce; ?>" />
        <input type="hidden" name="rvid" value="rvform-<?php echo $rv_param->id; ?>">
        <input type="hidden" name="rv_per_page" value="<?php echo $per_page; ?>" />
        <input type="hidden" name="s" value="rv_search_on" />

        <div class="rv-form-group" id="rv_submit">
          <input type="submit" id="rv_submit_btn" value="Search" alt="Search" class="rv-button" />
        </div>

      </form>
    </div>
  <?php
    }else{
      echo 'Please select a post type';
    }
  }else{
    echo 'No Form Selected';
  }
}else{
  echo 'No Form Selected';
}
