<?php

function extf_get_opt($name, $default = false, $option = 'extf_options') {

  $options = get_option( $option );

  return isset( $options[ $name ] ) ? $options[ $name ] : $default;

}

function extf_set_opt($name, $value, $option = 'extf_options') {

  $options = get_option( $option );

  $options[ $name ] = $value;

  update_option( $option, $options );

}

function extf_get_user_timeline( $args = array() ) {

  $Client = extf_get_twitter_client();

  if( empty( $Client )) {
    return false;
  }

  $defaults = array(
    'include_rts' => 1,
    'screen_name' => extf_get_opt('user_timeline'),
  );

  $args = wp_parse_args( $args, $defaults );

  return $Client->call( 'statuses/user_timeline', $args, 'GET' );

}

function extf_get_hashtag_timeline( $args = array() ) {

  $Client = extf_get_twitter_client();

  if( empty( $Client )) {
    return false;
  }

  $defaults = array(
    'q' => '#' . extf_get_opt('hashtag'),
  );

  $args = wp_parse_args( $args, $defaults );

  return $Client->call( 'search/tweets', $args, 'GET' );

}

function extf_verify_credentials( $Client = false ) {

  if( !$Client ) {
    $Client = extf_get_twitter_client();
  }

  $verified = false;

  try {
    $verified = $Client->call( 'account/verify_credentials', array(), 'GET' );
  }
  catch( Exception $Ex ){
    $error = $Ex->getMessage();
  }

  if( false !== $verified ) {
    return true;
  } else {
    return false;
  }

}

function extf_get_twitter_client() {
  global $ExtfClient;

  if( empty( $ExtfClient )) {
    $ExtfClient = new TwitterApiClient();
  }

  $consumer_key = extf_get_opt('extf_consumer_key');
  $consumer_secret = extf_get_opt('extf_consumer_secret');
  $access_token = extf_get_opt('extf_access_token');
  $access_token_secret = extf_get_opt('extf_access_token_secret');

  $manual_api = extf_get_opt('manual_api');

  $consumer_key = ($manual_api === 'on' && !empty($consumer_key)) ? $consumer_key : 'eP8SU2LvUQbGN0y9fF0lmZ2Qa';
  $consumer_secret = ($manual_api === 'on' && !empty($consumer_secret)) ? $consumer_secret : '4HVblnIAhKAULb3Hd68KbiJNqkfMDcnUu2KYdNzxoGSrUSLtBc';

  if( $consumer_key && $consumer_secret && $access_token && $access_token_secret ) {
    $ExtfClient->set_oauth($consumer_key, $consumer_secret, $access_token, $access_token_secret);

    if(!extf_verify_credentials($ExtfClient)) {
      return false;
    }

  } else {
    return false;
  }

  return $ExtfClient;
}

function extf_relative_date( $strdate ){
    // get universal time now.
    static $t, $y, $m, $d, $h, $i, $s, $o;
    if( ! isset($t) ){
        $t = time();
        sscanf(gmdate('Y m d H i s',$t), '%u %u %u %u %u %u', $y,$m,$d,$h,$i,$s);
    }
    // get universal time of tweet
    $tt = is_int($strdate) ? $strdate : strtotime($strdate);
    if( ! $tt || $tt > $t ){
        // slight difference between our clock and Twitter's clock can cause problem here - just pretend it was zero seconds ago
        $tt = $t;
        $tdiff = 0;
    }
    else {
        sscanf(gmdate('Y m d H i s',$tt), '%u %u %u %u %u %u', $yy,$mm,$dd,$hh,$ii,$ss);
        // Calculate relative date string
        $tdiff = $t - $tt;
    }
    // Less than a minute ago?
    if( $tdiff < 60 ){
        return __('Just now','twitter-api');
    }
    // within last hour? X minutes ago
    if( $tdiff < 3600 ){
        $idiff = (int) floor( $tdiff / 60 );
        return sprintf( _n( '%um', '%um', $idiff, 'twitter-api' ), $idiff );
    }
    // within same day? About X hours ago
    $samey = ($y === $yy) and
    $samem = ($m === $mm) and
    $samed = ($d === $dd);
    if( ! empty($samed) ){
        $hdiff = (int) floor( $tdiff / 3600 );
        return sprintf( _n( '1h', '%uh', $hdiff, 'twitter-api' ), $hdiff );
    }

    // within 1 year?
    if( $tdiff < 31556926 ){
      $df = 'M j';
    } else {
      $df = 'j M Y';
    }
    // else return formatted date, e.g. "Oct 20th 2008 9:27 PM" */

    return date_i18n( $df, $tt );
}
