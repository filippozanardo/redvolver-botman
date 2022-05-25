<?php

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\Cache\SymfonyCache;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use BotMan\Drivers\Facebook\FacebookDriver;
use BotMan\BotMan\Messages\Conversations\Conversation;
use Kerox\Messenger\Messenger;
use Kerox\Messenger\Model\Common\Button\PhoneNumber;
use Kerox\Messenger\Model\Common\Button\Postback;
use Kerox\Messenger\Model\Common\Button\WebUrl;
use Kerox\Messenger\Model\Message\Attachment\Template\ButtonTemplate;
use Kerox\Messenger\Model\Message;
use Kerox\Messenger\Model\Message\QuickReply;
use Kerox\Messenger\Api\Send;
use Carbon\Carbon;

add_action( 'admin_post_test1', 'test1' );

function test1 () {


  $rv_chattype = carbon_get_theme_option( 'rv_chattype' );
  if (!$rv_chattype) $rv_chattype = 'web';

  $config = [];

  if ($rv_chattype == 'facebook' ) {

    $rv_facebook_token = carbon_get_theme_option( 'rv_facebook_token' );
    $rv_facebook_verification = carbon_get_theme_option( 'rv_facebook_verification' );
    $rv_facebook_app_secret = carbon_get_theme_option( 'rv_facebook_app_secret' );

    if ( !$rv_facebook_token || !$rv_facebook_verification || !$rv_facebook_app_secret ) {
      echo 'Please configure facebook settings!';
      die();
    }
    $config = [

        'facebook' => [
      	   'token' => $rv_facebook_token,
    	     'app_secret' => $rv_facebook_app_secret,
           'verification'=> $rv_facebook_verification,
           'start_button_payload' => 'GET_STARTED'
        ]
    ];
    //DriverManager::loadDriver(\BotMan\Drivers\Facebook\FacebookDriver::class);
  }else{
    //DriverManager::loadDriver(\BotMan\Drivers\Web\WebDriver::class);
  }


  $messenger = new Messenger($rv_facebook_app_secret, $rv_facebook_verification, $rv_facebook_token);

  $message = new Message('Messagio di test per funzionamento bot');


      $rvuser = RVBOT()->db->table('rv_botman')->get();
      //var_dump($rvuser);
      if ( $rvuser->isEmpty() ) {
      }else{
        foreach ($rvuser as $rvu) {
          echo $rvu->rv_id;
          $messenger->send()->message($rvu->rv_id, $message,[
            'tag' => 'BUSINESS_PRODUCTIVITY'
          ]);

        }
      }

      die();

}

add_action( 'admin_post_test2', 'test2' );

function test2 () {

  $rvuser = RVBOT()->db->table('rv_botman')->where('id','1')->first();

  $randomUser = RVBOT()->db->table('rv_botman_value')->whereDate('date', '=', date("Y-m-d"))->where('rv_id','=',$rvuser->rv_id)->first();
  var_dump($randomUser);

  if (empty($randomUser) ) {

    RVBOT()->db->table('rv_botman_value')->insert(
      [
        'rv_id' => $rvuser->rv_id,
        'keyname' => 'PRANZO',
        'keyvalue' => 'YES',
        'date' => date("Y-m-d H:i:s"),
      ]
    );
  }else{
    RVBOT()->db->table('rv_botman_value')->where('rv_id', $rvuser->rv_id)->update(
      [
        'keyname' => 'PRANZO',
        'keyvalue' => 'YES',
        'date' => date("Y-m-d H:i:s"),
      ]
    );
  }
}

add_action('rvb_broadcast_test', 'rvb_broadcast_test');

function rvb_broadcast_test() {

  $dt = Carbon::now();

  // if ( $dt->isThursday() ) {
  //   exit();
  // }

  if ( $dt->isSaturday() ) {
    exit();
  }
  if ( $dt->isSunday() ) {
    exit();
  }

  $rv_chattype = carbon_get_theme_option( 'rv_chattype' );
  if (!$rv_chattype) $rv_chattype = 'web';

  $config = [];

  if ($rv_chattype == 'facebook' ) {

    $rv_facebook_token = carbon_get_theme_option( 'rv_facebook_token' );
    $rv_facebook_verification = carbon_get_theme_option( 'rv_facebook_verification' );
    $rv_facebook_app_secret = carbon_get_theme_option( 'rv_facebook_app_secret' );

    if ( !$rv_facebook_token || !$rv_facebook_verification || !$rv_facebook_app_secret ) {
      echo 'Please configure facebook settings!';
      die();
    }
    $config = [

        'facebook' => [
      	   'token' => $rv_facebook_token,
    	     'app_secret' => $rv_facebook_app_secret,
           'verification'=> $rv_facebook_verification,
           'start_button_payload' => 'GET_STARTED'
        ]
    ];
    //DriverManager::loadDriver(\BotMan\Drivers\Facebook\FacebookDriver::class);
  }else{
    //DriverManager::loadDriver(\BotMan\Drivers\Web\WebDriver::class);
  }


  $messenger = new Messenger($rv_facebook_app_secret, $rv_facebook_verification, $rv_facebook_token);

  $message = new Message('Ci sei a pranzo?');
  $message
      ->setQuickReplies([
          QuickReply::create(QuickReply::CONTENT_TYPE_TEXT)
              ->setTitle('SI')
              ->setPayload('PRANZOSI')
              ->setImageUrl(RVB_PLUGIN_URL.'img/si.png'),
          QuickReply::create(QuickReply::CONTENT_TYPE_TEXT)
              ->setTitle('NO')
              ->setPayload('PRANZONO')
              ->setImageUrl(RVB_PLUGIN_URL.'img/no.png'),
          QuickReply::create(QuickReply::CONTENT_TYPE_TEXT)
              ->setTitle('SCHISCIA')
              ->setPayload('PRANZOSCHISCIA')
              ->setImageUrl(RVB_PLUGIN_URL.'img/schiscia.png'),
          QuickReply::create(QuickReply::CONTENT_TYPE_TEXT)
              ->setTitle('OSPITE')
              ->setPayload('PRANZOOSPITE')
              ->setImageUrl(RVB_PLUGIN_URL.'img/ospite.png'),
      ]);

        // $randomUser = RVBOT()->db->table('rv_botman_value')->whereDate('date', '=', date("Y-m-d"))->where('rv_id','=','1883557555076496')->first();
        // var_dump($randomUser);
        // die();
      // $randomUser = RVBOT()->db->table('rv_botman_value')->whereDate('date', '=', date("Y-m-d"))->where('keyvalue','=','YES')->orderByRaw('RAND()')->first();
      // var_dump($randomUser);
      // if ( empty($randomUser) ) {
      //   echo 'STO CAZZO!';
      // }else{
      //   $randomUser3 = RVBOT()->db->table('rv_botman')->where('rv_id', '=', $randomUser->rv_id)->first();
      //   if ( empty($randomUser3) ) {
      //     echo 'STO CAZZO!';
      //   }else{
      //     echo $randomUser3->first_name.' '.$randomUser3->last_name;
      //   }
      //
      // }
      // die();

      $rvuser = RVBOT()->db->table('rv_botman')->get();
      //var_dump($rvuser);
      if ( $rvuser->isEmpty() ) {
      }else{
        foreach ($rvuser as $rvu) {
          //echo $rvu->rv_id;
          $messenger->send()->message($rvu->rv_id, $message,[
            'tag' => 'BUSINESS_PRODUCTIVITY'
          ]);

        }
      }

      die();
}

add_action( 'wp_ajax_nopriv_rv_send_test', 'rv_send_test' );
add_action( 'wp_ajax_rv_send_test', 'rv_send_test' );

function rv_send_test() {



  if (!empty($_POST['id']) ) {

    $rv_facebook_token = carbon_get_theme_option( 'rv_facebook_token' );
    $rv_facebook_verification = carbon_get_theme_option( 'rv_facebook_verification' );
    $rv_facebook_app_secret = carbon_get_theme_option( 'rv_facebook_app_secret' );

    $messenger = new Messenger($rv_facebook_app_secret, $rv_facebook_verification, $rv_facebook_token);

    $message = new Message('Se ti arriva questo messaggio è un test per favore comunica a Filippo Zanardo questo numero '.$_POST['id']);

    $messenger->send()->message($_POST['id'], $message,[
      'tag' => 'BUSINESS_PRODUCTIVITY'
    ]);
    wp_send_json_success();
  }else{
    wp_send_json_error();
  }

}

add_action( 'wp_ajax_nopriv_rv_send_pranzo', 'rv_send_pranzo' );
add_action( 'wp_ajax_rv_send_pranzo', 'rv_send_pranzo' );

function rv_send_pranzo() {



  if (!empty($_POST['id']) ) {
    $dt = Carbon::now();
    $rv_facebook_token = carbon_get_theme_option( 'rv_facebook_token' );
    $rv_facebook_verification = carbon_get_theme_option( 'rv_facebook_verification' );
    $rv_facebook_app_secret = carbon_get_theme_option( 'rv_facebook_app_secret' );

    $messenger = new Messenger($rv_facebook_app_secret, $rv_facebook_verification, $rv_facebook_token);

    //$message = new Message('Ci sei a pranzo?');

    $menu = RVBOT()->db->table('rv_daily_menu')->whereDate('menu_date', $dt->toDateString() )->first();
    if ( !empty( $menu ) ) {
      $message = new Message('Ci sei a pranzo? OGGI SI MANGIA: '.$menu->menu_name);
    }else{
      $message = new Message('Ci sei a pranzo?');
    }

    $message
      ->setQuickReplies([
          QuickReply::create(QuickReply::CONTENT_TYPE_TEXT)
              ->setTitle('SI')
              ->setPayload('PRANZOSI'),
              //->setImageUrl(RVB_PLUGIN_URL.'img/si.png'),
          QuickReply::create(QuickReply::CONTENT_TYPE_TEXT)
              ->setTitle('NO')
              ->setPayload('PRANZONO'),
              //->setImageUrl(RVB_PLUGIN_URL.'img/no.png'),
          QuickReply::create(QuickReply::CONTENT_TYPE_TEXT)
              ->setTitle('SCHISCIA')
              ->setPayload('PRANZOSCHISCIA'),
              //->setImageUrl(RVB_PLUGIN_URL.'img/schiscia.png'),
          QuickReply::create(QuickReply::CONTENT_TYPE_TEXT)
              ->setTitle('OSPITE')
              ->setPayload('PRANZOOSPITE'),
              //->setImageUrl(RVB_PLUGIN_URL.'img/ospite.png'),
      ]);

    $messenger->send()->message($_POST['id'], $message,[
      'tag' => 'BUSINESS_PRODUCTIVITY'
    ]);
    wp_send_json_success();
  }else{
    wp_send_json_error();
  }

}


add_action( 'wp_ajax_nopriv_rv_spacca_tutto', 'rv_spacca_tutto' );
add_action( 'wp_ajax_rv_spacca_tutto', 'rv_spacca_tutto' );

function rv_spacca_tutto() {

  $dt = Carbon::now();

  // if ( $dt->isSaturday() ) {
  //   exit();
  // }
  // if ( $dt->isSunday() ) {
  //   exit();
  // }
  //
  $rv_facebook_token = carbon_get_theme_option( 'rv_facebook_token' );
  $rv_facebook_verification = carbon_get_theme_option( 'rv_facebook_verification' );
  $rv_facebook_app_secret = carbon_get_theme_option( 'rv_facebook_app_secret' );

  $messenger = new Messenger($rv_facebook_app_secret, $rv_facebook_verification, $rv_facebook_token);

  $menu = RVBOT()->db->table('rv_daily_menu')->whereDate('menu_date', $dt->toDateString() )->first();
  if ( !empty( $menu ) ) {
    $message = new Message('Ci sei a pranzo? OGGI SI MANGIA: '.$menu->menu_name);
  }else{
    $message = new Message('Ci sei a pranzo?');
  }

  $message
    ->setQuickReplies([
        QuickReply::create(QuickReply::CONTENT_TYPE_TEXT)
            ->setTitle('SI')
            ->setPayload('PRANZOSI'),
            //->setImageUrl(RVB_PLUGIN_URL.'img/si.png'),
        QuickReply::create(QuickReply::CONTENT_TYPE_TEXT)
            ->setTitle('NO')
            ->setPayload('PRANZONO'),
            //->setImageUrl(RVB_PLUGIN_URL.'img/no.png'),
        QuickReply::create(QuickReply::CONTENT_TYPE_TEXT)
            ->setTitle('SCHISCIA')
            ->setPayload('PRANZOSCHISCIA'),
            //->setImageUrl(RVB_PLUGIN_URL.'img/schiscia.png'),
        QuickReply::create(QuickReply::CONTENT_TYPE_TEXT)
            ->setTitle('OSPITE')
            ->setPayload('PRANZOOSPITE'),
            //->setImageUrl(RVB_PLUGIN_URL.'img/ospite.png'),
    ]);

      $rvuser = RVBOT()->db->table('rv_botman')->get();
      //$rvuser = RVBOT()->db->table('rv_botman')->where('id','=',1)->get();
      //var_dump($rvuser);
      if ( $rvuser->isEmpty() ) {
      }else{
        foreach ($rvuser as $rvu) {
          $messenger->send()->message($rvu->rv_id, $message,[
            'tag' => 'BUSINESS_PRODUCTIVITY'
          ]);

        }
      }

      wp_send_json_success(array('j'=>$rvu->rv_id));

}
