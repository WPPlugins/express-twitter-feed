<?php
function extf_options_validate($input) {

  $input = array_map( 'trim', $input );
  $options = get_option( 'extf_options' );

  if( $input['feed_type'] === 'hashtag' && !preg_match('/^#/', $input['hashtag']) ) {
    $input['hashtag'] = '#' . $input['hashtag'];
  }

  if( $input['extf_num_tweets'] < 0 ) {
    $input['extf_num_tweets'] = 0;
  }

  return $input;
}

function extf_display_options_validate($input) {

  $input = array_map( 'trim', $input );

  return $input;
}
