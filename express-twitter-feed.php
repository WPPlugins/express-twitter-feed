<?php
/**
 * Plugin Name:   Express Twitter Feed
 * Description:   Quickly and easily embed Twitter feeds on your WordPress site.
 * Version:       0.2.1
 * Author:        Pace Creative
 * Text Domain:   extf
 * Domain Path:   /languages
 */

defined( 'ABSPATH' ) or exit;

define('EXTF_VERSION', '0.2.1');

include 'functions.php';

if( is_admin() ) {
  include 'admin/settings-page.php';
  include 'admin/inc/cb-settings-sections.php';
  include 'admin/inc/cb-settings-fields.php';
  include 'admin/inc/cb-settings-validation.php';
}

if( ! function_exists('twitter_api_get') ){
  require dirname(__FILE__).'/api/twitter/twitter-api.php';
  twitter_api_include('core','utils');
  remove_action('admin_menu', 'twitter_api_admin_menu');
}

function extf_init() {
}
add_action('init', 'extf_init');

function extf_activation() {

  $default_opts = array(
    'extf_options' => array(
      'feed_type' => 'user',
      'extf_num_tweets' => 5,
    ),
    'extf_display_options' => array(
      'extf_display_author_name' => 1,
      'extf_display_author_screenname' => 1,
      'extf_display_tweet_text' => 1,
      'extf_display_avatar_image' => 1,
      'extf_display_date' => 1,
      'extf_display_retweeted_text' => 1,
      'extf_display_tweet_actions' => 1,
      'extf_display_byline' => 0,
    ),
  );

  foreach( $default_opts as $opt_name => $opts ) {

    $opt_exists = get_option( $opt_name );

    if( $opt_exists === false ) {
      foreach( $opts as $opt => $value ) {

        extf_set_opt( $opt, $value, $opt_name );

      }
    }
  }

}
register_activation_hook( __FILE__, 'extf_activation' );

function extf_deactivation() {

  // remove saved config
  // delete_option('extf_options');
  // delete_option('extf_display_options');

}
register_deactivation_hook( __FILE__, 'extf_deactivation' );

function extf_uninstall() {

  // remove saved config
  delete_option('extf_options');
  delete_option('extf_display_options');

}
register_uninstall_hook( __FILE__, 'extf_uninstall' );

function extf_scripts() {

  $inline_styles = '';
  $text_color = extf_get_opt('extf_text_colour', false, 'extf_display_options');
  $link_color = extf_get_opt('extf_link_colour', false, 'extf_display_options');
  $bg_color = extf_get_opt('extf_background_colour', false, 'extf_display_options');

  if( !empty( $text_color ) && sanitize_hex_color( $text_color )) {
    $inline_styles .= "
      .extf-tweet { color: {$text_color}; }
    ";
  }

  if( !empty( $link_color ) && sanitize_hex_color( $link_color )) {
    $inline_styles .= "
      .extf-tweet a, .extf-tweet a:link, .extf-tweet a:visited, .extf-tweet a:hover { color: {$link_color}; }
    ";
  }

  if( !empty( $bg_color ) && sanitize_hex_color( $bg_color )) {
    $inline_styles .= "
      .extf-tweets { background-color: {$bg_color}; }
    ";
  }

  wp_enqueue_style( 'extf-style-min', plugins_url('front/css/style.min.css', __FILE__ ), array(), EXTF_VERSION);
  wp_enqueue_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css' );

  if( !empty( $inline_styles )) {
    wp_add_inline_style( 'extf-style-min', $inline_styles );
  }

  wp_enqueue_script( 'extf-front-scripts', plugins_url('front/js/front.min.js', __FILE__ ),null,EXTF_VERSION,true);
  wp_add_inline_script( 'extf-front-scripts', '
    window.twttr = (function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0],
        t = window.twttr || {};
      if (d.getElementById(id)) return t;
      js = d.createElement(s);
      js.id = id;
      js.src = "https://platform.twitter.com/widgets.js";
      fjs.parentNode.insertBefore(js, fjs);

      t._e = [];
      t.ready = function(f) {
        t._e.push(f);
      };

      return t;
    }(document, "script", "twitter-wjs"));
  ');

}
add_action( 'wp_enqueue_scripts', 'extf_scripts' );

function express_twitter_feed() {

  $feed_type = extf_get_opt('feed_type');
  $num_tweets = extf_get_opt('extf_num_tweets');
  $user_timeline = extf_get_opt('user_timeline');

  $args = array();

  $tweets = false;

  switch( $feed_type ) {
    case 'user':
      $tweets = extf_get_user_timeline( $args );
      break;
    case 'hashtag':
      $result = extf_get_hashtag_timeline( $args );
      $tweets = $result['statuses'];
      break;
  }

  // pvar_dump($tweets[1]);

  if( !empty( $tweets )) {

    echo '<div class="extf-tweets">
      <hr class="extf-tweet__divider" />';
    $count = 1;

    // get display flags
    $display_author_name = extf_get_opt('extf_display_author_name', false, 'extf_display_options');
    $display_author_screenname = extf_get_opt('extf_display_author_screenname', false, 'extf_display_options');
    $display_tweet_text = extf_get_opt('extf_display_tweet_text', false, 'extf_display_options');
    $display_avatar_image = extf_get_opt('extf_display_avatar_image', false, 'extf_display_options');
    $display_date = extf_get_opt('extf_display_date', false, 'extf_display_options');
    $display_retweeted_text = extf_get_opt('extf_display_retweeted_text', false, 'extf_display_options');
    $display_tweet_actions = extf_get_opt('extf_display_tweet_actions', false, 'extf_display_options');
    $display_byline = extf_get_opt('extf_display_byline', false, 'extf_display_options');

    foreach( $tweets as $tweet ) {

      $rt_name = '';
      $rt_user = '';
      $display_tweet = $tweet;

      if( isset( $tweet['retweeted_status'] )) {
        $rt_name = $tweet['user']['name'];
        $rt_user = $tweet['user']['screen_name'];
        $display_tweet = $tweet['retweeted_status'];
      }

      $name = $display_tweet['user']['name'];
      $user = $display_tweet['user']['screen_name'];
      $date = extf_relative_date($display_tweet['created_at']);
      $text = twitter_api_html_with_entities( $display_tweet['text'], $display_tweet['entities'] );
      $avatar = $display_tweet['user']['profile_image_url_https'];
      $rt_count = $display_tweet['retweet_count'];
      $favorite_count = $display_tweet['favorite_count'];
      $tweet_id = $tweet['id_str'];

      ?>
      <div class="extf-tweet <?php echo !$display_avatar_image ? 'no-avatar' : ''; ?>">

        <?php if( $display_retweeted_text && !empty( $rt_name )): ?>
          <div class="extf-tweet__retweet">
            <a target="_blank" href="https://twitter.com/<?php echo $rt_user; ?>"><?php echo $rt_name; ?> <?php _e('Retweeted','extf'); ?></a>
          </div><!-- /.extf-tweet__retweet -->
        <?php endif; ?>

        <div class="extf-tweet__meta">
          <?php if( $display_avatar_image ): ?>
            <img src="<?php echo $avatar; ?>" alt="" class="extf-tweet__avatar" />
          <?php endif; ?>

          <?php if( $display_author_name ): ?>
            <a target="_blank" href="https://twitter.com/<?php echo $user; ?>" class="extf-tweet__name"><?php echo $name; ?></a>
          <?php endif; ?>

          <?php if( $display_author_screenname ): ?>
            <a target="_blank" href="https://twitter.com/<?php echo $user; ?>" class="extf-tweet__handle">@<?php echo $user; ?></a>
          <?php endif; ?>

          <?php if( $display_date && ($display_author_name || $display_author_screenname)): ?>
            &bull;
          <?php endif; ?>

          <?php if( $display_date ): ?>
           <a target="_blank" href="https://twitter.com/statuses/<?php echo $tweet_id; ?>" class="extf-tweet__date"><?php echo $date; ?></a>
          <?php endif; ?>
        </div><!-- /.extf-tweet__meta -->

        <?php if( $display_tweet_text ): ?>
          <div class="extf-tweet__content">
            <p class="extf-tweet__text"><?php echo $text; ?></p>
          </div><!-- /.extf-tweet__content -->
        <?php endif; ?>

        <?php if( $display_tweet_actions ): ?>
          <div class="extf-tweet__actions">
            <a class="extf-tweet__action extf-tweet__action--reply" target="_blank" href="https://twitter.com/intent/tweet?in_reply_to=<?php echo $tweet_id; ?>&amp;related=<?php echo $user; ?>">
              <i class="fa fa-reply"></i>
              <span class="screen-reader-text"><?php _e('Reply','extf'); ?></span>
            </a>

            <a class="extf-tweet__action extf-tweet__action--retweet" target="_blank" href="https://twitter.com/intent/retweet?tweet_id=<?php echo $tweet_id; ?>&amp;related=<?php echo $user; ?>">
              <i class="fa fa-retweet"></i>
              <span class="screen-reader-text"><?php _e('Retweet','extf'); ?></span> <span aria-hidden="true"><?php echo $rt_count; ?></span>
            </a>

            <a class="extf-tweet__action extf-tweet__action--like" target="_blank" href="https://twitter.com/intent/like?tweet_id=<?php echo $tweet_id; ?>&amp;related=<?php echo $user; ?>">
              <i class="fa fa-heart"></i>
              <span class="screen-reader-text"><?php _e('Like','extf'); ?></span> <span aria-hidden="true"><?php echo $favorite_count; ?></span>
            </a>
          </div><!-- /.extf-tweet-actions -->
        <?php endif; ?>

      </div><!-- /.extf-tweet -->

      <hr class="extf-tweet__divider" />

      <?php
      if( $count++ >= $num_tweets ) {
        break;
      }
    }

    if( $display_byline ) {
      echo '<p style="font-size:12px;">Express Twitter Feed by <a href="http://www.creativepace.com/" target="_blank">Pace Creative</a></p>';
    }

    echo '</div>';
  }

}
add_shortcode('express-twitter-feed', 'express_twitter_feed');
