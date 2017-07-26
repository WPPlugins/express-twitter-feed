<?php

function extf_settings_init() {

  // register a setting for options page
  register_setting( 'extf_quick_setup', 'extf_options', 'extf_options_validate' );
  register_setting( 'extf_display_options', 'extf_display_options', 'extf_display_options_validate' );

  extf_settings_sections();
  extf_settings_fields();

}
add_action( 'admin_init', 'extf_settings_init' );

function extf_handle_notices() {

  $notice = isset( $_GET['message'] ) ? $_GET['message'] : false;
  $class = '';
  $message = '';

  switch( $notice ) {
    case 'extf-auth':
      $class = 'notice notice-success is-dismissible';
      $message = __('You have been successfully authenticated through Twitter. Your Access Token, Access Token Secret, and Screen Name have been saved.', 'extf');
      break;
    case 'extf-no-auth':
      $class = 'notice notice-error is-dismissible';
      $message = __('We were unable to complete the authentication process. Please try again, or enter your Twitter API Credentials manually.', 'extf');
      break;
  }

  if( !empty( $class ) && !empty( $message )) {
    printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
  }

}
add_action( 'admin_notices', 'extf_handle_notices' );

function extf_settings_sections() {

  // register a setting section
  add_settings_section(
    'extf_section_auth_settings',
    __('Authentication', 'extf'),
    'extf_section_auth_settings_cb',
    'extf_quick_setup'
  );

  // register a setting section
  add_settings_section(
    'extf_section_basic_settings',
    __('Basic Feed Settings', 'extf'),
    'extf_section_basic_settings_cb',
    'extf_quick_setup'
  );

  // register a setting section
  add_settings_section(
    'extf_section_feed_styles',
    __('Feed Styles', 'extf'),
    'extf_section_styles_settings_cb',
    'extf_display_options'
  );

  // register a setting section
  add_settings_section(
    'extf_section_tweet_components',
    __('Tweet Layout', 'extf'),
    'extf_section_components_settings_cb',
    'extf_display_options'
  );

}

function extf_settings_fields() {

  $consumer_class = 'extf-consumer-hidden';

  if( extf_get_opt('manual_api') === 'on' ) {
    $consumer_class .= ' show';
  }

  $quick_setup = array(
    'extf_section_auth_settings' => array(
      array(
        'field_id' => 'extf_consumer_key',
        'label_name' => __('Consumer Key', 'extf'),
        'cb' => 'extf_text_field_cb',
        'args' => array(
          'size' => 60,
          'class' => $consumer_class,
        ),
      ),
      array(
        'field_id' => 'extf_consumer_secret',
        'label_name' => __('Consumer Secret', 'extf'),
        'cb' => 'extf_text_field_cb',
        'args' => array(
          'size' => 60,
          'class' => $consumer_class,
        ),
      ),
      array(
        'field_id' => 'extf_access_token',
        'label_name' => __('Access Token', 'extf'),
        'cb' => 'extf_text_field_cb',
        'args' => array(
          'size' => 60,
        ),
      ),
      array(
        'field_id' => 'extf_access_token_secret',
        'label_name' => __('Access Token Secret', 'extf'),
        'cb' => 'extf_text_field_cb',
        'args' => array(
          'size' => 60,
        ),
      ),
    ),
    'extf_section_basic_settings' => array(
      array(
        'field_id' => 'extf_feed_type',
        'label_name' => __('Feed Type'),
        'cb' => 'extf_feed_type_cb',
      ),
      array(
        'field_id' => 'extf_num_tweets',
        'label_name' => __('Number of Tweets to display'),
        'cb' => 'extf_number_field_cb',
        'args' => array(
          'default' => 5,
          'input-class' => 'short-number',
        ),
      ),
    ),
  );

  extf_add_settings_fields( $quick_setup, 'extf_quick_setup', 'extf_options' );

  $styles = array(
    'extf_section_feed_styles' => array(
      array(
        'field_id' => 'extf_text_colour',
        'label_name' => __('Text Colour'),
        'cb' => 'extf_colour_picker_cb',
      ),
      array(
        'field_id' => 'extf_link_colour',
        'label_name' => __('Link Colour'),
        'cb' => 'extf_colour_picker_cb',
      ),
      array(
        'field_id' => 'extf_background_colour',
        'label_name' => __('Background Colour'),
        'cb' => 'extf_colour_picker_cb',
      ),
    ),
    'extf_section_tweet_components' => array(
      array(
        'field_id' => 'extf_display_author_name',
        'label_name' => __('Author Name'),
        'cb' => 'extf_checkbox_cb',
      ),
      array(
        'field_id' => 'extf_display_author_screenname',
        'label_name' => __('Author Screen Name'),
        'cb' => 'extf_checkbox_cb',
      ),
      array(
        'field_id' => 'extf_display_tweet_text',
        'label_name' => __('Tweet Text'),
        'cb' => 'extf_checkbox_cb',
      ),
      array(
        'field_id' => 'extf_display_avatar_image',
        'label_name' => __('Avatar Image'),
        'cb' => 'extf_checkbox_cb',
      ),
      array(
        'field_id' => 'extf_display_date',
        'label_name' => __('Tweet Date'),
        'cb' => 'extf_checkbox_cb',
      ),
      array(
        'field_id' => 'extf_display_retweeted_text',
        'label_name' => __('"Retweeted" Text'),
        'cb' => 'extf_checkbox_cb',
      ),
      array(
        'field_id' => 'extf_display_tweet_actions',
        'label_name' => __('Tweet Actions'),
        'cb' => 'extf_checkbox_cb',
      ),
      array(
        'field_id' => 'extf_display_byline',
        'label_name' => __('Plugin Author Credit Link'),
        'cb' => 'extf_checkbox_cb',
      ),
    ),
  );

  extf_add_settings_fields( $styles, 'extf_display_options', 'extf_display_options' );

}

function extf_add_settings_fields( $sections, $page, $option ) {

  foreach( $sections as $section => $fields ) {

    foreach( $fields as $field ) {

      if( !isset( $field['args'] )) {
        $field['args'] = array();
      }

      if( !isset( $field['args']['label_for'] )) {
        $field['args']['label_for'] = $field['field_id'];
      }

      $field['args']['option'] = $option;

      add_settings_field(
        $field['field_id'],
        $field['label_name'],
        $field['cb'],
        $page,
        $section,
        $field['args']
      );

    }
  }
}

function extf_options_page_html() {

  // check user capabilities
  if (!current_user_can('manage_options')) {
    return;
  }

  if (isset($_GET['settings-updated'])) {
    // add settings saved message with the class of "updated"
    add_settings_error('wporg_messages', 'wporg_message', __('Settings Saved', 'extf'), 'updated');
  }

  $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'extf_quick_setup';

  ?>

  <h2 class="nav-tab-wrapper">
    <a href="?page=extf&tab=extf_quick_setup" class="nav-tab <?php echo $active_tab == 'extf_quick_setup' ? 'nav-tab-active' : ''; ?>"><?php _e('Quick Setup','extf'); ?></a>
    <a href="?page=extf&tab=extf_display_options" class="nav-tab <?php echo $active_tab == 'extf_display_options' ? 'nav-tab-active' : ''; ?>"><?php _e('Display Options','extf'); ?></a>
    <!-- <a href="#" class="nav-tab">Advanced</a> -->
  </h2>

  <div class="wrap extf-wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <form action="options.php" method="post">
      <?php

        settings_fields( $active_tab );
        do_settings_sections( $active_tab );

        submit_button('Save Settings');
      ?>
    </form>

    <h2><?php _e('Display Feed','extf'); ?></h2>
    <?php

      $Client = extf_get_twitter_client();

      if( $Client === false ) {
        ?>

        <p><?php _e("You will need to complete the authentication step above before your feed will display.",'extf'); ?></p>

        <?php
          $manual_api = extf_get_opt('manual_api');
          if( $manual_api ) {

            $consumer_key = extf_get_opt('extf_consumer_key');
            $consumer_secret = extf_get_opt('extf_consumer_secret');
            $access_token = extf_get_opt('extf_access_token');
            $access_token_secret = extf_get_opt('extf_access_token_secret');

            if( empty($consumer_key) ||
                empty($consumer_secret) ||
                empty($access_token) ||
                empty($access_token_secret)) {
              ?>
              <div class="notice notice-warning">
                <p>Please fill out all Consumer Key and Access Token fields in order to enable manual authentication.</p>
              </div><!-- /.notice notice-error -->
            <?php
            } else {
              ?>
              <div class="notice notice-warning">
                <p>We were unable to connet to the Twitter API using your supplied credentials. Please double check your info, or try automatic authentication.</p>
              </div><!-- /.notice notice-error -->
              <?php
            }

          } else {

            $access_token = extf_get_opt('extf_access_token');
            $access_token_secret = extf_get_opt('extf_access_token_secret');

            if( !empty( $access_token ) && !empty( $access_token_secret )) {
              ?>
                <div class="notice notice-warning">
                  <p>There appears to be something wrong with your Access Token details. Please try running the automatic authentication process again.</p>
                </div><!-- /.notice notice-error -->
              <?php
            }
          }
        ?>

        <?php
      } else {

        ?>
        <p><?php _e("Your feed is ready to go! Just copy the shortcode below into any of your pages or posts.",'extf'); ?> </p>
        <input type="text" readonly="readonly" value="[express-twitter-feed]" />

        <p><?php echo sprintf( __('Check out the <a href="%s">Display Options</a> tab to make changes to the look of the feed.', 'extf'), '?page=extf&tab=extf_display_options'); ?></p>
        <?php

      }
      ?>
    </div><!-- /.wrap -->
  <?php
}

function extf_options_page() {
  add_submenu_page(
    'options-general.php',
    __('Express Twitter Feed', 'extf'),
    __('Express Twitter Feed', 'extf'),
    'manage_options',
    'extf',
    'extf_options_page_html'
  );
}
add_action('admin_menu', 'extf_options_page');

function extf_admin_enqueue_scripts( $hook_suffix ) {
  wp_enqueue_style( 'wp-color-picker' );
  wp_enqueue_style( 'extf-style-min', plugins_url('css/style.min.css', __FILE__ ), array(), EXTF_VERSION);

  wp_enqueue_script( 'extf_admin_scripts', plugins_url('js/admin.min.js', __FILE__ ), array( 'wp-color-picker' ), EXTF_VERSION, true );
}
add_action( 'admin_enqueue_scripts', 'extf_admin_enqueue_scripts' );

function extf_check_activation() {

  $screen = get_current_screen();

  if( $screen->id == 'settings_page_extf' ) {

    // check if access token is coming in from URL
    $access_token = isset( $_REQUEST['oauth_token'] ) ? $_REQUEST['oauth_token'] : false;
    $access_token_secret = isset( $_REQUEST['oauth_token_secret'] ) ? $_REQUEST['oauth_token_secret'] : false;
    $screen_name = isset( $_REQUEST['screen_name'] ) ? $_REQUEST['screen_name'] : false;

    if( $access_token && $access_token_secret ) {
      if( isset( $screen_name )) {
        extf_set_opt( 'user_timeline', $screen_name );
      }

      extf_set_opt( 'extf_access_token', $access_token );
      extf_set_opt( 'extf_access_token_secret', $access_token_secret );

      $plugin_url = menu_page_url('extf', 0);
      $plugin_url = add_query_arg('message', 'extf-auth', $plugin_url);

      wp_redirect($plugin_url);
      exit;
    }
  }
}
add_action( 'current_screen', 'extf_check_activation' );
