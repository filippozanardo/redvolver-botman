<?php

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\Cache\SymfonyCache;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;

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
  DriverManager::loadDriver(\BotMan\Drivers\Facebook\FacebookDriver::class);
}else{
  DriverManager::loadDriver(\BotMan\Drivers\Web\WebDriver::class);
}

// $config = [
//
//     'facebook' => [
//   	   'token' => 'EAAeNzvY6BkMBAKD0sD3VoSByJtVawlogjrRQ5S2Yva46P6nbrNUbgZCTGCjMWdh7ZCD4d71VUy2DgqATfPTK4aTO2KSMuUK9hzNpQBNlwA3Ec8fRBhtMi5SQNDYZCopkDMDXHrOedmQcY6P9kYZCXHjQtRATGmdk693XVZBRZCmgZDZD',
// 	     'app_secret' => '57f7a79a2f381d45859c8bbae6cf04f3',
//        'verification'=>'giancane',
//        'start_button_payload' => 'GET_STARTED'
//     ]
// ];


// Load the driver(s) you want to use
//DriverManager::loadDriver(\BotMan\Drivers\Facebook\FacebookDriver::class);

//$adapter = new FilesystemAdapter('gianni',0,RVB_PLUGIN_DIR);
$adapter = new FilesystemAdapter();
$botman = BotManFactory::create($config, new SymfonyCache($adapter));

$botman->hears('GET_STARTED', function ($bot) {
  $bot->typesAndWaits(1);
  $bot->reply('Giancane');
});

$botman->hears('PRANZOSI', function ($bot) {
    $z = $bot->getMessage()->getPayload();
    $rvuser = RVBOT()->db->table('rv_botman')->where('rv_id', $z['sender']['id'])->first();

    $randomUser = RVBOT()->db->table('rv_botman_value')->whereDate('date', '=', date("Y-m-d"))->where('rv_id','=',$z['sender']['id'])->first();
    if (empty($randomUser) ) {

      RVBOT()->db->table('rv_botman_value')->insert(
        [
          'rv_id' => $z['sender']['id'],
          'keyname' => 'PRANZO',
          'keyvalue' => 'YES',
          'date' => date("Y-m-d H:i:s"),
        ]
      );
    }else{
      RVBOT()->db->table('rv_botman_value')->where('id','=',$randomUser->id)->update(
        [
          'keyname' => 'PRANZO',
          'keyvalue' => 'YES',
          'date' => date("Y-m-d H:i:s"),
        ]
      );
    }

    $bot->reply('Grazie '.$rvuser->first_name);
});

$botman->hears('PRANZONO', function ($bot) {
    $z = $bot->getMessage()->getPayload();
    $rvuser = RVBOT()->db->table('rv_botman')->where('rv_id', $z['sender']['id'])->first();

    $randomUser = RVBOT()->db->table('rv_botman_value')->whereDate('date', '=', date("Y-m-d"))->where('rv_id','=',$z['sender']['id'])->first();
    if (empty($randomUser) ) {

      RVBOT()->db->table('rv_botman_value')->insert(
        [
          'rv_id' => $z['sender']['id'],
          'keyname' => 'PRANZO',
          'keyvalue' => 'NO',
          'date' => date("Y-m-d H:i:s"),
        ]
      );
    }else{
      RVBOT()->db->table('rv_botman_value')->where('id','=',$randomUser->id)->update(
        [
          'keyname' => 'PRANZO',
          'keyvalue' => 'NO',
          'date' => date("Y-m-d H:i:s"),
        ]
      );
    }


    $bot->reply('Ci vediamo domani '.$rvuser->first_name);
});

$botman->hears('PRANZOSCHISCIA', function ($bot) {
    $z = $bot->getMessage()->getPayload();
    $rvuser = RVBOT()->db->table('rv_botman')->where('rv_id', $z['sender']['id'])->first();

    $randomUser = RVBOT()->db->table('rv_botman_value')->whereDate('date', '=', date("Y-m-d"))->where('rv_id','=',$z['sender']['id'])->first();
    if (empty($randomUser) ) {

      RVBOT()->db->table('rv_botman_value')->insert(
        [
          'rv_id' => $z['sender']['id'],
          'keyname' => 'PRANZO',
          'keyvalue' => 'SELF',
          'date' => date("Y-m-d H:i:s"),
        ]
      );
    }else{
      RVBOT()->db->table('rv_botman_value')->where('id','=',$randomUser->id)->update(
        [
          'keyname' => 'PRANZO',
          'keyvalue' => 'SELF',
          'date' => date("Y-m-d H:i:s"),
        ]
      );
    }

    $bot->reply('Buon Appetito '.$rvuser->first_name);
});

$botman->hears('PRANZOOSPITE', function ($bot) {
  $z = $bot->getMessage()->getPayload();
  $rvuser = RVBOT()->db->table('rv_botman')->where('rv_id', $z['sender']['id'])->first();

  $randomUser = RVBOT()->db->table('rv_botman_value')->whereDate('date', '=', date("Y-m-d"))->where('rv_id','=',$z['sender']['id'])->first();
  if (empty($randomUser) ) {

    RVBOT()->db->table('rv_botman_value')->insert(
      [
        'rv_id' => $z['sender']['id'],
        'keyname' => 'PRANZO',
        'keyvalue' => 'OSPITE',
        'date' => date("Y-m-d H:i:s"),
      ]
    );
  }else{
    RVBOT()->db->table('rv_botman_value')->where('id','=',$randomUser->id)->update(
      [
        'keyname' => 'PRANZO',
        'keyvalue' => 'OSPITE',
        'date' => date("Y-m-d H:i:s"),
      ]
    );
  }

  $bot->reply('Aggiungo un ospite '.$rvuser->first_name);
});

$botman->hears('lava', function ($bot) {
  $bot->typesAndWaits(2);
  $z = $bot->getMessage()->getPayload();


  $randomUser = RVBOT()->db->table('rv_botman_value')->whereDate('date', '=', date("Y-m-d"))->where('keyvalue','=','YES')->orderByRaw('RAND()')->first();

  if ( empty($randomUser) ) {
    $bot->reply('Nessuno si è registrato oggi');
  }else{
    $randomUser3 = RVBOT()->db->table('rv_botman')->where('rv_id', '=', $randomUser->rv_id)->first();
    if ( empty($randomUser3) ) {
      $bot->reply('Nessuno si è registrato oggi');
    }else{
      $bot->reply($randomUser3->first_name.' '.$randomUser3->last_name);
    }

  }

});

$botman->hears('tavolo', function ($bot) {
  $bot->typesAndWaits(2);

  $randomUser = RVBOT()->db->table('rv_botman_value')->whereDate('date', '=', date("Y-m-d"))->where('keyvalue','!=','NO')->orderByRaw('RAND()')->first();

  if ( empty($randomUser) ) {
    $bot->reply('Nessuno si è registrato oggi');
  }else{
    $randomUser3 = RVBOT()->db->table('rv_botman')->where('rv_id', '=', $randomUser->rv_id)->first();
    if ( empty($randomUser3) ) {
      $bot->reply('Nessuno si è registrato oggi');
    }else{
      $bot->reply($randomUser3->first_name.' '.$randomUser3->last_name);
    }

  }
});

$botman->fallback(function($bot) {

  $z = $bot->getMessage()->getPayload();

  try {
    $user = $bot->getUser();
    $firstname = $user->getFirstName();
    $lastname = $user->getLastName();
  } catch (Exception $e) {
    $firstname = 'Gianni';
    $lastname = 'Sperti';
  }


  // $user = $bot->getUser();
  // if ( $user ) {
  //   $firstname = $user->getFirstName();
  //   $lastname = $user->getLastName();
  // }else{
  //   $firstname = 'Gianni';
  //   $lastname = 'Sperti';
  // }

  //$fp = fopen('botman.txt', 'w');
  // $info = $user->getInfo();
  // fwrite($fp, 'i'.$z['sender']['id'].PHP_EOL );
  // fwrite($fp, 'u'.serialize($user).PHP_EOL );
  // fwrite($fp, 'd'.serialize($bot->getDriver()).PHP_EOL );
  // fwrite($fp, 'f'.$firstname.PHP_EOL);
  // fwrite($fp, 'info'.json_encode($info).PHP_EOL);
  // fclose($fp);
    $rvuser = RVBOT()->db->table('rv_botman')->where('rv_id', $z['sender']['id'])->get();


    if ( $rvuser->isEmpty() ) {

      if (!empty($z['sender']['id'])) {
        RVBOT()->db->table('rv_botman')->insert(
          [
            'rv_id' => $z['sender']['id'],
            'username' => 'd',
            'first_name' => $firstname,
            'last_name' => $lastname,
            'date' => date("Y-m-d H:i:s"),
          ]
        );
      }
    }

    $bot->typesAndWaits(2);
    $bot->reply('Non scrivere direttamente a questo bot');

    //fwrite($fp, 'Cats chase mice');

});

// Start listening
$botman->listen();
die();
