<?php

function extf_feed_type_cb($args) {

  $feed_type = extf_get_opt('feed_type');
  $user_timeline = extf_get_opt('user_timeline');
  $hashtag = extf_get_opt('hashtag');

  ?>

  <table cellspacing="0" cellpadding="0">
    <tr>
      <td>
        <input type="radio" id="extf_feed_type_user" name="<?php echo $args['option']; ?>[feed_type]" value="user" <?php checked( $feed_type, 'user' ); ?> />
        <label for="extf_feed_type_user"><?php _e('User timeline','extf'); ?>:</label>
      </td>
      <td>
        <input type="text" name="<?php echo $args['option']; ?>[user_timeline]" value="<?php echo $user_timeline; ?>" />
      </td>
    </tr>
    <tr>
      <td>
        <input type="radio" id="extf_feed_type_hashtag" name="<?php echo $args['option']; ?>[feed_type]" value="hashtag" <?php checked( $feed_type, 'hashtag' ); ?> />
        <label for="extf_feed_type_hashtag"><?php _e('Hashtag','extf'); ?>:</label>
      </td>
      <td>
        <input type="text" name="<?php echo $args['option']; ?>[hashtag]" value="<?php echo $hashtag; ?>" />
      </td>
    </tr>
  </table>

  <?php
}

function extf_text_field_cb($args) {

  $args['type'] = 'text';
  extf_input_field_cb($args);

}

function extf_number_field_cb($args) {

  $args['type'] = 'number';
  extf_input_field_cb($args);

}

function extf_input_field_cb($args) {

  $default  = isset( $args['default'] )     ? $args['default']      : null;
  $class    = isset( $args['input-class'] ) ? $args['input-class']  : '';
  $size     = isset( $args['size'] )        ? $args['size']         : 20;
  $type     = isset( $args['type'] )        ? $args['type']         : 'text';

  $value = extf_get_opt($args['label_for'], $default, $args['option']);

  ?>

  <input type="<?php echo $type; ?>" value="<?php echo $value; ?>" size="<?php echo $size; ?>" class="<?php echo $class; ?>" name="<?php echo $args['option']; ?>[<?php echo $args['label_for']; ?>]" id="<?php echo $args['label_for']; ?>" />

  <?php

}

function extf_colour_picker_cb($args) {

  $args['input-class'] = 'extf-colour-picker';

  extf_text_field_cb( $args );

}

function extf_checkbox_cb($args) {

  $value = extf_get_opt($args['label_for'], false, $args['option']);

  ?>
  <input type="checkbox" name="<?php echo $args['option']; ?>[<?php echo $args['label_for']; ?>]" value="1" <?php checked( 1, $value ); ?> />
  <?php

}
