<?php // @partial
/**
 * Color Control custom class
 *
 * The color control uses Spectrum as a Javascript Plugin which offers more
 * features comparing to Iris, the default one used by WordPress.
 * We basically whitelist the Spectrum options that developers are allowed to
 * define setting them as class protected properties which are then put in the
 * JSON params of the control object, ready to be used in the javascript
 * implementation.
 *
 * @since  1.0.0
 *
 * @package    Customize_Plus
 * @subpackage Customize\Controls
 * @author     KnitKode <dev@knitkode.com> (https://knitkode.com)
 * @copyright  2017 KnitKode
 * @license    GPLv3
 * @version    Release: pkgVersion
 * @link       https://knitkode.com/products/customize-plus
 */
class KKcp_Customize_Control_Color extends KKcp_Customize_Control_Base {

	/**
	 * Control type.
	 *
	 * {@inheritdoc}
	 * @since 1.0.0
	 * @var string
	 */
	public $type = 'kkcp_color';

	/**
	 * Colors format supported
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private static $colorsFormatsSupported = array( 'hex', 'rgb', 'rgba', /*'hsl', 'hsla', 'keyword'*/ );

	/**
	 * Allow alpha channel modification (rgba colors)
	 *
	 * @since 1.0.0
	 * @var boolean
	 */
	protected $allowAlpha = false;

	/**
	 * Disallow transparent color
	 *
	 * @since 1.0.0
	 * @var boolean
	 */
	protected $disallowTransparent = false;

	/**
	 * Palette
	 *
	 * {@link(https://bgrins.github.io/spectrum/#options-selectionPalette, js docs)}
	 * @since 1.0.0
	 * @var boolean|array
	 */
	protected $palette = array();

	/**
	 * Show palette only in color control
	 *
	 * {@link(https://bgrins.github.io/spectrum/#options-showPaletteOnly, js docs)}
	 * @since 1.0.0
	 * @var boolean
	 */
	protected $showPaletteOnly = false;

	/**
	 * Toggle palette only in color control
	 *
	 * {@link(https://bgrins.github.io/spectrum/#options-togglePaletteOnly, js docs)}
	 * @since 1.0.0
	 * @var boolean
	 */
	protected $togglePaletteOnly = false;


	/**
	 * Get l10n
	 *
	 * {@inheritdoc}
	 * @since  1.0.0
	 * @override
	 */
	public function get_constants() {
		return array(
			'colorsKeyword' => KKcp_Utils::COLORS_KEYWORDS,
			'colorsFormatsSupported' => self::$colorsFormatsSupported
		);
	}

	/**
	 * Get l10n
	 *
	 * {@inheritdoc}
	 * @since  1.0.0
	 * @override
	 */
	public function get_l10n() {
		return array(
			'cancelText' => esc_html__( 'Cancel' ),
			'chooseText' => esc_html__( 'Choose' ),
			'clearText' => esc_html__( 'Clear selection' ),
			'noColorSelectedText' => esc_html__( 'No color selected' ),
			'togglePaletteMoreText' => esc_html__( 'Show color picker' ),
			'togglePaletteLessText' => esc_html__( 'Hide color picker' ),
			'vNotInPalette' => esc_html__( 'Color not in the allowed palette.' ),
			'vNoTransparent', esc_html__( 'Transparent is not allowed for this setting.' ),
			'vNoRGBA', esc_html__( 'RGBA color is not allowed for this setting.' ),
			'vNoColor' => esc_html__( 'Not a valid color.' ),
		);
	}

	/**
	 * Add to JSON
	 *
	 * {@inheritdoc}
	 * @since 1.0.0
	 * @override
	 */
	protected function add_to_json() {
		$value = $this->value();

		$this->add_booleans_params_to_json( array(
			'allowAlpha',
			'disallowTransparent',
			'showPaletteOnly',
			'togglePaletteOnly',
		) );

		if ( $this->palette ) {
			$this->json['palette'] = $this->palette;
		}

		$this->json['mode'] = 'custom';
		$this->json['valueCSS'] = $value;
	}

	/**
	 * JS template
	 *
	 * {@inheritdoc}
	 * @since 1.0.0
	 * @override
	 */
	protected function js_tpl() {
		?>
		<?php $this->js_tpl_header(); ?>
		<span class="kkcpcolor-current kkcpcolor-current-bg"></span>
		<span class="kkcpcolor-current kkcpcolor-current-overlay" style="background:{{data.valueCSS}}"></span>
		<button class="kkcpui-toggle kkcpcolor-toggle"><?php esc_html_e( 'Select Color' ) ?></button>
		<div class="kkcp-expander">
			<input class="kkcpcolor-input" type="text">
		</div>
		<?php
	}

	/**
	 * Sanitize
	 *
	 * {@inheritdoc}
	 * @since 1.0.0
	 * @override
	 * @param string               $value   The value to sanitize.
 	 * @param WP_Customize_Setting $setting Setting instance.
 	 * @param WP_Customize_Control $control Control instance.
 	 * @return string The sanitized value.
 	 */
	protected static function sanitize( $value, $setting, $control ) {
		$value = preg_replace( '/\s+/', '', $value );

		// @@doubt here there might be arace condition when the developer defines a palette
		// that have rgba colors without setting `allowAlpha` to `true`. \\
		if ( KKcp_Validate::is_rgba( $value ) && ! $control->allowAlpha ) {
			$value = KKcp_Sanitize::rgba_to_rgb( $value );
		} else {
			$value = KKcp_Sanitize::hex( $value );
		}

		return $value;
	}

	/**
	 * Validate
	 *
	 * @since 1.0.0
	 * @override
	 * @param WP_Error 						 $validity
	 * @param mixed 							 $value    The value to validate.
 	 * @param WP_Customize_Setting $setting  Setting instance.
 	 * @param WP_Customize_Control $control  Control instance.
	 * @return mixed
 	 */
	protected static function validate( $validity, $value, $setting, $control ) {
		$value = self::sanitize( $value, $setting, $control );

		if ( $control->showPaletteOnly &&
			! $control->togglePaletteOnly &&
			is_array( $control->palette )
		) {
			$palette_flatten = KKcp_Sanitize::array_flatten( $control->palette, 1 );
			$palette_normalized = array_map( 'KKcp_Sanitize::hex_to_rgb', $palette_flatten );
			$value_normalized = KKcp_Sanitize::hex_to_rgb( $value );
			if ( ! KKcp_Sanitize::in_array_r( $value_normalized, $palette_normalized ) ) {
				$validity->add( 'vNotInPalette', esc_html__( 'Color not in the allowed palette.' ) );
			}
		}

		if ( 'transparent' === $value && $control->disallowTransparent ) {
			$validity->add( 'vNoTransparent', esc_html__( 'Transparent is not allowed for this setting.' ) );
		}

		if ( KKcp_Validate::is_rgba( $value ) && ! $control->allowAlpha ) {
			$validity->add( 'vNoRGBA', esc_html__( 'RGBA color is not allowed for this setting.' ) );
		}

		if ( !KKcp_Validate::is_hex( $value ) &&
			!KKcp_Validate::is_rgb( $value ) &&
			!KKcp_Validate::is_rgba( $value ) &&
			$value !== 'transparent'
		) {
			$validity->add( 'vNoColor', esc_html__( 'Not a valid color.' ) );
		}

		return $validity;
	}
}

/**
 * Register on WordPress Customize global object
 */
$wp_customize->register_control_type( 'KKcp_Customize_Control_Color' );