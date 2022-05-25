<?php
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class Redvolver_Ajax {

  private static $_instance = null;

  public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
			self::$_instance->hooks();
		}
		return self::$_instance;
	}


	public function hooks() {
    add_action( 'wp_ajax_nopriv_rv_fetch_event', array($this, 'rv_fetch_event') );
    add_action( 'wp_ajax_rv_fetch_event', array($this, 'rv_fetch_event') );

    add_action( 'wp_ajax_nopriv_rv_fetch_event_all', array($this, 'rv_fetch_event_all') );
    add_action( 'wp_ajax_rv_fetch_event_all', array($this, 'rv_fetch_event_all') );

    add_action( 'wp_ajax_nopriv_rv_edit_event', array($this, 'rv_edit_event') );
    add_action( 'wp_ajax_rv_edit_event', array($this, 'rv_edit_event') );

    add_action( 'wp_ajax_nopriv_rv_del_event', array($this, 'rv_del_event') );
    add_action( 'wp_ajax_rv_del_event', array($this, 'rv_del_event') );

    add_action( 'wp_ajax_nopriv_rv_del_client', array($this, 'rv_del_client') );
    add_action( 'wp_ajax_rv_del_client', array($this, 'rv_del_client') );

    add_action( 'wp_ajax_nopriv_rv_del_tag', array($this, 'rv_del_tag') );
    add_action( 'wp_ajax_rv_del_tag', array($this, 'rv_del_tag') );

    add_action( 'wp_ajax_nopriv_rv_project_report', array($this, 'rv_project_report') );
    add_action( 'wp_ajax_rv_project_report', array($this, 'rv_project_report') );

    add_action( 'wp_ajax_nopriv_rv_project_report_tax', array($this, 'rv_project_report_tax') );
    add_action( 'wp_ajax_rv_project_report_tax', array($this, 'rv_project_report_tax') );

    add_action( 'wp_ajax_nopriv_rv_user_report', array($this, 'rv_user_report') );
    add_action( 'wp_ajax_rv_user_report', array($this, 'rv_user_report') );

    add_action( 'wp_ajax_nopriv_rv_add_asana', array($this, 'rv_add_asana') );
    add_action( 'wp_ajax_rv_add_asana', array($this, 'rv_add_asana') );

    add_action( 'wp_ajax_nopriv_rv_login', array($this, 'rv_login') );
    add_action( 'wp_ajax_rv_login', array($this, 'rv_login') );

  }

  public function rv_login(){
    $return = array(); //What we send back

    parse_str($_REQUEST['form'], $output);

    //wp_send_json_error( $output );

		if( !empty($output['rvuser']) && !empty($output['rvpassword']) && trim($output['rvuser']) != '' && trim($output['rvpassword'] != '') ){

      $credentials = array(
        'user_login' => $output['rvuser'],
        'user_password'=> $output['rvpassword'],
        'remember' => !empty($output['remember'])
      );
			$loginResult = wp_signon($credentials);
      if(!is_wp_error($loginResult)){
        $redirect = wp_sanitize_redirect( get_site_url() );
        $return['redirect'] = $redirect;
        wp_send_json_success($return);
      }else{
  			$return['error'] = __('Please supply your username and password.', 'login-with-ajax');
        wp_send_json_error($return);
      }
    }else{

			$return['error'] = __('Please supply your username and password.', 'login-with-ajax');
      wp_send_json_error($return);
    }
    wp_send_json_error($return);
  }

  public function rv_add_asana(){

    //wp_send_json_success($_POST);

    $current_user = wp_get_current_user();
    $start = $_POST['asanadate'].' '.$_POST['asanastart'].':00';
    $end = $_POST['asanadate'].' '.$_POST['asanaend'].':00';

    $rv_post = array(
        'post_title'    => $_POST['asananame'],
        'post_content'  => '',
        'post_status'   => 'publish',
        'post_author'   => $current_user->ID,
        'post_date' => $start,
        'post_type' => 'rvc',
        'meta_input' => array(
          'start' => $start,
          'end' => $end,
        )
    );

    // Insert the post into the database.
    $post_id = wp_insert_post($rv_post);
    if(!is_wp_error($post_id)){

      $term = get_term_by( 'name', $_POST['asanaproject'], 'rvc-tag'  );

      if ( $term ) {
        wp_set_post_terms( $post_id, array($term->term_id), 'rvc-tag', false);
      }else{
        $newterm = wp_insert_term(
            $_POST['asanaproject'],
            'rvc-tag'
        );
        wp_set_post_terms( $post_id, array($newterm['term_id']), 'rvc-tag', false);
      }

      $response = array(
          'id'        => $post_id
      );
      wp_send_json_success($response);
    }else{
      wp_send_json_error();
    }

  }

  public function rv_project_report_tax() {
    $tax = $_POST['tax'];

    if ( $tax ) {
      $args=array(
        'post_status'=>array('future','publish'),
        'post_type'=>'rvc',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'rvc-tag',
                'field'    => 'term_id',
                'terms'    => $tax,
            ),
        ),
      );


      $reportarray = array();
      $series = array();
      $cat = array();
      $p_query= null;
      $p_query = new WP_Query();

      $p_query->query($args);
      if ( $p_query->have_posts() ) {
        while($p_query->have_posts()):$p_query->the_post();

          $start = get_field('start');
          $end = get_field('end');

          if ( $start ) {
            $ds = new Carbon($start);
          }

          if ( $end ) {
            $de = new Carbon($end);
          }

          $difference = $ds->diffInRealHours($de);
          if ( $difference > 0 ) {
            $hours = $ds->diffInRealHours($de);
            //echo 'ore'.$ds->diffInRealHours($de).'<br/>';
          }else{
            $hours = 0.5;
          }

          if (isset($reportarray[$p_query->post->post_author]) ) {
            $old = $reportarray[$p_query->post->post_author]['hours'];
            $reportarray[$p_query->post->post_author]['hours'] = $old + $hours;
          }else{
            $reportarray[$p_query->post->post_author]['hours'] = $hours;
          }

          $cat[$ds->weekOfYear] = $ds->weekOfYear;


          //$series[$p_query->post->post_author]['name'] = get_userdata($p_query->post->post_author)->display_name;
          //$series[$p_query->post->post_author]['data'][$ds->weekOfYear] = $hours;

          if (isset($series[$p_query->post->post_author][$ds->weekOfYear]) ) {

            $old = $series[$p_query->post->post_author][$ds->weekOfYear];
            $series[$p_query->post->post_author][$ds->weekOfYear] = $old + $hours;

          }else{
            $series[$p_query->post->post_author][$ds->weekOfYear] = $hours;
          }
        endwhile;
        wp_reset_query();

        ob_start();
        ?>
        <table class="table table-bordered">
					<thead>
				    <tr>
				      <th scope="col">Utenti</th>
							<th scope="col">Ore</th>
				    </tr>
				  </thead>
					<tbody>
            <?php $total = 0; ?>
						<?php foreach ($reportarray as $kk => $vv) { ?>
							<?php $user_info = get_userdata($kk); ?>
							<?php if ( $user_info ) { ?>
              <?php $total += $vv['hours']; ?>
							<tr>
								<td><?php echo $user_info->display_name; ?></td>
								<td><?php echo $vv['hours']; ?></td>
							</tr>
							<?php } ?>
						<?php } ?>
            <tr>
              <td></td>
              <td><?php echo $total; ?></td>
            </tr>
					</tbody>
				</table>
        <?php
        $content = ob_get_clean();
        asort($cat);

        /*
        series: [{
          name: 'John',
          data: [5, 3, 4, 7, 2]
        }, {
          name: 'Jane',
          data: [2, 2, 3, 2, 1]
        }, {
          name: 'Joe',
          data: [3, 4, 4, 2, 5]
        }]
        */

        $newseries = array();
        foreach ($cat as $c) {
          foreach ($series as $u2 => $v2) {
            if (!isset($newseries['name'] ) ) {
              $newseries[$u2]['name'] = get_userdata($u2)->display_name;
            }
            if (isset($v2[$c]) ) {
              $newseries[$u2]['data'][$c] = $v2[$c];
            }else{
              $newseries[$u2]['data'][$c] = 0;
            }
          }
        }

        foreach ($series as $u2 => $v2) {
          $newseries[$u2]['data'] = array_values($newseries[$u2]['data']);
        }

        $response = array(
          'table' => $content,
          'cat' => array_values($cat),
          'series' => array_values($newseries)
        );
        wp_send_json_success($response);

      }else{
        wp_send_json_error();
      }
    }else{
      wp_send_json_error();
    }
  }

  public function rv_project_report() {
    $title = $_POST['title'];

    if ( $title ) {
      $args=array(
        'post_status'=>array('future','publish'),
        'post_type'=>'rvc',
        'posts_per_page' => -1,
        's' => $title
      );


      $reportarray = array();
      $series = array();
      $cat = array();
      $p_query= null;
      $p_query = new WP_Query();

      $p_query->query($args);
      if ( $p_query->have_posts() ) {
        while($p_query->have_posts()):$p_query->the_post();

          $start = get_field('start');
          $end = get_field('end');

          if ( $start ) {
            $ds = new Carbon($start);
          }

          if ( $end ) {
            $de = new Carbon($end);
          }

          $difference = $ds->diffInRealHours($de);
          if ( $difference > 0 ) {
            $hours = $ds->diffInRealHours($de);
            //echo 'ore'.$ds->diffInRealHours($de).'<br/>';
          }else{
            $hours = 0.5;
          }

          if (isset($reportarray[$p_query->post->post_author]) ) {
            $old = $reportarray[$p_query->post->post_author]['hours'];
            $reportarray[$p_query->post->post_author]['hours'] = $old + $hours;
          }else{
            $reportarray[$p_query->post->post_author]['hours'] = $hours;
          }

          $cat[$ds->weekOfYear] = $ds->weekOfYear;


          //$series[$p_query->post->post_author]['name'] = get_userdata($p_query->post->post_author)->display_name;
          //$series[$p_query->post->post_author]['data'][$ds->weekOfYear] = $hours;

          if (isset($series[$p_query->post->post_author][$ds->weekOfYear]) ) {

            $old = $series[$p_query->post->post_author][$ds->weekOfYear];
            $series[$p_query->post->post_author][$ds->weekOfYear] = $old + $hours;

          }else{
            $series[$p_query->post->post_author][$ds->weekOfYear] = $hours;
          }
        endwhile;
        wp_reset_query();

        ob_start();
        ?>
        <table class="table table-bordered">
					<thead>
				    <tr>
				      <th scope="col">Utenti</th>
							<th scope="col">Ore</th>
				    </tr>
				  </thead>
					<tbody>
            <?php $total = 0; ?>
						<?php foreach ($reportarray as $kk => $vv) { ?>
							<?php $user_info = get_userdata($kk); ?>
							<?php if ( $user_info ) { ?>
              <?php $total += $vv['hours']; ?>
							<tr>
								<td><?php echo $user_info->display_name; ?></td>
								<td><?php echo $vv['hours']; ?></td>
							</tr>
							<?php } ?>
						<?php } ?>
            <tr>
              <td></td>
              <td><?php echo $total; ?></td>
            </tr>
					</tbody>
				</table>
        <?php
        $content = ob_get_clean();
        asort($cat);

        $newseries = array();
        foreach ($cat as $c) {
          foreach ($series as $u2 => $v2) {
            if (!isset($newseries['name'] ) ) {
              $newseries[$u2]['name'] = get_userdata($u2)->display_name;
            }
            if (isset($v2[$c]) ) {
              $newseries[$u2]['data'][$c] = $v2[$c];
            }else{
              $newseries[$u2]['data'][$c] = 0;
            }
          }
        }

        foreach ($series as $u2 => $v2) {
          $newseries[$u2]['data'] = array_values($newseries[$u2]['data']);
        }

        $response = array(
          'table' => $content,
          'cat' => array_values($cat),
          'series' => array_values($newseries)
        );
        wp_send_json_success($response);

      }else{
        wp_send_json_error();
      }
    }else{
      wp_send_json_error();
    }
  }


  public function rv_user_report() {
    $uid = $_POST['uid'];
    $range = $_POST['range'];

    //if ( $uid ) {

      $reportarray = array();

      if ( empty($range) ) {
        $datestart = Carbon::now();
        $startOfWeek = $datestart->startOfWeek()->subDay();
        $weekDays = array();

        for ($i = 0; $i < Carbon::DAYS_PER_WEEK; $i++) {
            $weekDays[] = $startOfWeek->addDay()->startOfDay()->copy();
        }
      }else{
        $pieces = explode(" / ", $range);

        $datestart = $pieces[0];
        $dateend = $pieces[1];

        $period = CarbonPeriod::create($datestart, $dateend);
        $weekDays = $period->toArray();

      }


      foreach ($weekDays as $day) {
        $d1 = $day;
        $d2 = $day->copy()->addDay();

        $args = array (
          'post_type' => 'rvc',
          'post_status' => 'any',
          'meta_query' => array(
              'relation' => 'AND',
              array(
                  'key'		=> 'start',
                  'compare'	=> '>=',
                  'value'     => $d1,
                  'type'			=> 'DATETIME'
              ),
              array(
                    'key'		=> 'end',
                    'compare'	=> '<=',
                    'value'     => $d2,
                    'type'			=> 'DATETIME'
              ),
           ),
           //'author' => $uid
        );
        if ($uid) $args['author'] = $uid;



        $p_query= null;
        $p_query = new WP_Query();

        $p_query->query($args);
        if ( $p_query->have_posts() ) {
          while($p_query->have_posts()):$p_query->the_post();

            $start = get_field('start');
            $end = get_field('end');

            if ( $start ) {
              $ds = new Carbon($start);
            }

            if ( $end ) {
              $de = new Carbon($end);
            }

            $difference = $ds->diffInRealHours($de);

            if ( $difference > 0 ) {
              $hours = $ds->diffInRealHours($de);
              //echo 'ore'.$ds->diffInRealHours($de).'<br/>';
            }else{
              $hours = 0.5;
            }

            // if (isset($reportarray[$uid]) ) {
            // 	$old = $reportarray[$uid]['hours'];
            // 	$reportarray[$uid]['hours'] = $old + $hours;
            // }else{
              $tt = 'noproject';
              $terms = get_the_terms( get_the_ID(), 'rvc-tag' );
              if ($terms) $tt = $terms[0]->name;

              if ( isset($reportarray[$tt]) ) {
                $reportarray[$tt] = $reportarray[$tt] + $hours;
              }else{
                $reportarray[$tt] =  $hours;
              }



              //$reportarray[get_the_ID()]['term'] = $tt;
            //}



          endwhile;
          wp_reset_query();
        }

      }

      if ( $reportarray ) {
        ob_start();
        ?>
        <table class="table table-striped- table-bordered table-hover" id="remaketable">
					<thead>
				    <tr>
							<th scope="col">Progetto</th>
              <th scope="col">Ore</th>
				    </tr>
				  </thead>
					<tbody>
            <?php $total = 0; ?>
						<?php foreach ($reportarray as $kk => $vv) { ?>
              <?php $total += $vv; ?>
							<tr>
								<td><?php echo $kk; ?></td>
								<td><?php echo $vv; ?></td>
							</tr>
						<?php } ?>
            <tr>
              <td></td>
              <td><?php echo $total; ?></td>
            </tr>
					</tbody>
				</table>
        <?php
        $content = ob_get_clean();
        $response = array(
          'table' => $content,
        );
        wp_send_json_success($response);
      }else{
        wp_die();
      }

    //}else{
      //wp_die();
    //}
  }

  public function rv_fetch_event() {
    $current_user = wp_get_current_user();
    $start = $_POST['start'];
    $end = $_POST['end'];

    //$start = '2017-02-26';
    //$end = '2017-04-09';

    if ($start && $end) {

      $startp = explode("-", $start);
      $endp = explode("-", $end);

      $start = str_replace('-', '', $start);
      $end = str_replace('-', '', $end);

      $args=array(
        'post_status'=>array('future','publish'),
        'post_type'=>'rvc',
        'author' => $current_user->ID,
        'date_query' => array(
          array(
            'before'     => array(
              'year'  => $endp[0],
              'month' => $endp[1],
              'day'   => $endp[2],
            ),
            'after'    => array(
              'year'  => $startp[0],
              'month' => $startp[1],
              'day'   => $startp[2],
            ),
            'inclusive' => true,
          ),
        ),


        'posts_per_page' => -1,
      );

      $arrayeventi = array();
      $p_query= null;
      $p_query = new WP_Query();

      $p_query->query($args);
    	if ( $p_query->have_posts() ) {
    		while($p_query->have_posts()):$p_query->the_post();

    			$start = get_field('start');
          $end = get_field('end');

          if (!$start) $start = get_the_time( 'Y-m-d H:i:s');
          if (!$end) $end = get_the_time( 'Y-m-d H:i:s');

          $terms = get_the_terms( $p_query->post->ID ,  'rvc-tag' );
          if ( $terms) {
            $tag = $terms[0]->term_id;
          }else{
            $tag = false;
          }
  				$arrayeventi[] = array(
              'id' => $p_query->post->ID,
  						'title' => $p_query->post->post_title,
  						'start' => $start,
              'end' => $end,
  						'tag' => $tag,
  				);


    		endwhile;
    	}
      wp_reset_query();

    	$out = json_encode($arrayeventi,JSON_UNESCAPED_UNICODE);

      wp_die($out);
    }else{
      wp_die();
    }

  }

  public function rv_fetch_event_all() {
    $current_user = wp_get_current_user();
    $start = $_POST['start'];
    $end = $_POST['end'];

    if ($start && $end) {

      $startp = explode("-", $start);
      $endp = explode("-", $end);

      $start = str_replace('-', '', $start);
      $end = str_replace('-', '', $end);

      $args=array(
        'post_status'=>array('future','publish'),
        'post_type'=>'rvc',
        'date_query' => array(
          array(
            'before'     => array(
              'year'  => $endp[0],
              'month' => $endp[1],
              'day'   => $endp[2],
            ),
            'after'    => array(
              'year'  => $startp[0],
              'month' => $startp[1],
              'day'   => $startp[2],
            ),
            'inclusive' => true,
          ),
        ),


        'posts_per_page' => -1,
      );

      $arrayeventi = array();
      $p_query= null;
      $p_query = new WP_Query();

      $p_query->query($args);
    	if ( $p_query->have_posts() ) {
    		while($p_query->have_posts()):$p_query->the_post();

    			$start = get_field('start');
          $end = get_field('end');

          if (!$start) $start = get_the_time( 'Y-m-d H:i:s');
          if (!$end) $end = get_the_time( 'Y-m-d H:i:s');

          $terms = get_the_terms( $p_query->post->ID ,  'rvc-tag' );
          if ( $terms) {
            $tag = $terms[0]->term_id;
          }else{
            $tag = false;
          }
  				$arrayeventi[] = array(
              'id' => $p_query->post->ID,
  						'title' => $p_query->post->post_title,
  						'start' => $start,
              'end' => $end,
  						'tag' => $tag,
              'color' => 'violet'
  				);



    		endwhile;
    	}
      wp_reset_query();
    	$out = json_encode($arrayeventi);


      wp_die($out);
    }else{
      wp_die();
    }

  }

  public function rv_edit_event() {

    if ( $_POST['mode'] == 'add' ) {
      if ($_POST['asana']) {


        $data = RVC()->db->table('rv_asana')->where('id', $_POST['asana'] )->get();

        if ( $data->isEmpty() ) {

          wp_send_json_error();
        }else{


          $current_user = wp_get_current_user();
          $st = new DateTime( $_POST['start'] );
          $en = new DateTime( $_POST['end'] );
          $rv_post = array(
              'post_title'    => $data[0]->asana_title,
              'post_content'  => '',
              'post_status'   => 'publish',
              'post_author'   => $current_user->ID,
              'post_date' => $st->format( 'Y-m-d H:i:s' ),
              'post_type' => 'rvc',
              'meta_input' => array(
                'start' => $st->format( 'Y-m-d H:i:s' ),
                'end' => $en->format( 'Y-m-d H:i:s' ),
              )
          );
          $post_id = wp_insert_post($rv_post);
          if(!is_wp_error($post_id)){
            $term = get_term_by( 'name', $data[0]->asana_project, 'rvc-tag'  );

            if ( $term ) {
              wp_set_post_terms( $post_id, array($term->term_id), 'rvc-tag', false);
            }else{
              $newterm = wp_insert_term(
                  $data[0]->asana_project,
                  'rvc-tag'
              );
              wp_set_post_terms( $post_id, array($newterm['term_id']), 'rvc-tag', false);
            }
            $response = array(
                'mode'   => 'add',
                'id'        => $post_id,
                'title' => $data[0]->asana_title,
            );
            wp_send_json_success($response);
          }else{
            wp_send_json_error();
          }

        }

      }elseif ($_POST['title']) {
        $current_user = wp_get_current_user();
        $st = new DateTime( $_POST['start'] );
        $en = new DateTime( $_POST['end'] );
        //error_log( $d->format( 'Ymd' ) );

        $rv_post = array(
            'post_title'    => $_POST['title'],
            'post_content'  => '',
            'post_status'   => 'publish',
            'post_author'   => $current_user->ID,
            'post_date' => $st->format( 'Y-m-d H:i:s' ),
            'post_type' => 'rvc',
            'meta_input' => array(
              'start' => $st->format( 'Y-m-d H:i:s' ),
              'end' => $en->format( 'Y-m-d H:i:s' ),
            )
        );

        // Insert the post into the database.
        $post_id = wp_insert_post($rv_post);
        if(!is_wp_error($post_id)){
          if ( $_POST['tag'] != '' ) {
            wp_set_post_terms( $post_id, $_POST['tag'], 'rvc-tag', false);
          }
          $response = array(
              'mode'   => 'add',
              'id'        => $post_id,
              'title' => $_POST['title'],
          );
          wp_send_json_success($response);
        }else{
          wp_send_json_error();
        }

      }else{
        wp_send_json_error();
      }

    }elseif ( $_POST['mode'] == 'edit' ) {

      if ($_POST['cid']) {
        $st = new DateTime( $_POST['start'] );
        $en = new DateTime( $_POST['end'] );

        $rv_post = array(
            'ID' => $_POST['cid'],
            'post_title'    => $_POST['title'],
            'post_content'  => '',
            'post_status'   => 'publish',
            'post_author'   => $current_user->ID,
            'post_date' => $st->format( 'Y-m-d H:i:s' ),
            'post_type' => 'rvc',
            'meta_input' => array(
              'start' => $st->format( 'Y-m-d H:i:s' ),
              'end' => $en->format( 'Y-m-d H:i:s' ),
            )
        );

        // Insert the post into the database.
        $post_id = wp_update_post($rv_post);
        if ( $_POST['tag'] != '' ) {
          wp_set_post_terms( $post_id, $_POST['tag'], 'rvc-tag', false);
        }
        $response = array(
            'mode'   => 'edit',
            'id'        => $post_id,
            'title'    => $_POST['title'],
        );
        wp_send_json_success($response);
      }else{
        wp_send_json_error();
      }
    }else{
      wp_send_json_error();
    }

  }

  public function rv_del_event() {
    if ($_POST['cid']) {
      wp_delete_post( $_POST['cid'], true );
      wp_send_json_success();
    }else{
      wp_send_json_error();
    }
  }

  public function rv_del_client() {
    if ($_POST['cid']) {
      wp_delete_term( $_POST['cid'], 'rvc-client');
      wp_send_json_success();
    }else{
      wp_send_json_error();
    }
  }

  public function rv_del_tag() {
    if ($_POST['cid']) {
      wp_delete_term( $_POST['cid'], 'rvc-tag');
      wp_send_json_success();
    }else{
      wp_send_json_error();
    }
  }

}

Redvolver_Ajax::instance();
