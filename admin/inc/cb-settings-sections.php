<?php

function extf_section_basic_settings_cb($args) {}

function extf_section_auth_settings_cb($args) {

  $manual_api = extf_get_opt('manual_api');

  ?>
  <p>
    <a class="button-primary" href="//extf.creativepace.com/auth.php?return=<?php menu_page_url('extf'); ?>"><?php _e('Log in and authenticate automatically via Twitter','extf'); ?></a>
  </p>
  <p>
    <?php echo sprintf( __('Or, if you have your own <a href="%s" target="_blank">Twitter app tokens</a>, you can check this box and enter them manually:','extf'), 'https://dev.twitter.com/oauth/overview/application-owner-access-tokens'); ?> <input name="extf_options[manual_api]" value="on" id="extf-toggle-consumer-fields" type="checkbox" <?php checked( $manual_api, 'on' ); ?> />
  </p>
  <?php
}

function extf_section_styles_settings_cb($args) {
  ?>
  <p>Express Twitter Feed has very few default styles, and instead attempts to display according to your site's CSS. Use the settings below to override some common styles.</p>
  <?php
}
function extf_section_components_settings_cb($args) {
  ?>
  <p>Use the settings below for fine-grained control over which parts of each tweet will display in your feed.</p>
  <?php
}
