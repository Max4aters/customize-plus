<?php // @partial
/**
 * Text Control custom class
 *
 * @since  0.0.1
 *
 * @package    Customize_Plus
 * @subpackage Customize\Controls
 * @author     PlusWP <dev@pluswp.com> (http://pluswp.com)
 * @copyright  2015 PlusWP (kunderi kuus)
 * @license    GPL-2.0+
 * @version    Release: pkgVersion
 * @link       http://pluswp.com/customize-plus
 */
class PWPcp_Customize_Control_Text extends PWPcp_Customize_Control_Base_Input {

	/**
	 * Control type.
	 *
	 * @since 0.0.1
	 * @var string
	 */
	public $type = 'pwpcp_text';

	/**
	 * Get localized strings
	 *
	 * @override
	 * @since  0.0.1
	 * @return array
	 */
	public function get_l10n() {
		return array(
			'vNotEmpty' => __( 'This field cannot be empty.', 'pkgTextdomain' ),
			'vTooLong' => __( 'The value is too long.', 'pkgTextdomain' ),
			'vInvalidUrl' => __( 'Invalid url.', 'pkgTextdomain' ),
			'vInvalidEmail' => __( 'Invalid email.', 'pkgTextdomain' ),
		);
	}

	/**
	 * Sanitization callback
	 *
	 * @since 0.0.1
	 * @override
	 * @param string               $value   The value to sanitize.
 	 * @param WP_Customize_Setting $setting Setting instance.
 	 * @return string The sanitized value.
 	 */
	public static function sanitize_callback( $value, $setting ) {
    // if value is required and is empty return default
    // also be sure it's a string value
    if ( ( isset( $input_attrs['required'] ) && ! $value ) || ! is_string( $value ) ) {
      return $setting->default;
    } else {
  		$control = $setting->manager->get_control( $setting->id );
  		$input_attrs = $control->input_attrs;

  		$input_type = isset( $input_attrs['type'] ) ? $input_attrs['type'] : 'text';

      // url
      if ( 'url' === $input_type ) {
        $value = esc_url_raw( $value );
      }
      // email
      else if ( 'email' === $input_type ) {
        $value = sanitize_email( $value );
      }
      // text
      else {
      	$value = wp_strip_all_tags( $value );
      }
      // max length
			if ( isset( $input_attrs['maxlength'] ) && strlen( $value ) > $input_attrs['maxlength'] ) {
        return $setting->default;
      }

      return $value;
    }
	}
}

/**
 * Register on WordPress Customize global object
 */
$wp_customize->register_control_type( 'PWPcp_Customize_Control_Text' );