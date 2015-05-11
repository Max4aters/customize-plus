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
class PWPcp_Customize_Control_Color extends PWPcp_Customize_Control_Base {

	/**
	 * Control type identifier
	 *
	 * Also used in the js on the `defaultConstructor`
	 * property of the WordPress Customize API.
	 *
	 * @override
	 * @since 0.0.1
	 * @var string
	 */
	public $type = 'pwpcp_color';

	/**
	 * Palette
	 *
	 * @since 0.0.1
	 * @var boolean
	 */
	protected $palette = array();

	/**
	 * Allow alpha channel modification (rgba colors)
	 *
	 * @since 0.0.1
	 * @var boolean
	 */
	protected $allowAlpha = false;

	/**
	 * Disallow transparent color
	 *
	 * @since 0.0.1
	 * @var boolean
	 */
	protected $disallowTransparent = false;

	/**
	 * Show palette only in color control
	 *
	 * @link(https://bgrins.github.io/spectrum/#options-showPaletteOnly, Javascript plugin docs)
	 * @since 0.0.1
	 * @var boolean
	 */
	protected $showPaletteOnly = false;

	/**
	 * Toggle palette only in color control
	 *
	 * @link(https://bgrins.github.io/spectrum/#options-togglePaletteOnly, Javascript plugin docs)
	 * @since 0.0.1
	 * @var boolean
	 */
	protected $togglePaletteOnly = false;

	/**
	 * Enqueue scripts/styles for the color picker.
	 *
	 * @since 0.0.1
	 */
	public function enqueue() {
		// PWPcp_Customize::add_js_l10n( array(
		// 	'cancelText' => __( 'annulla', 'pkgTextdomain' ),
		// 	'chooseText' => __( 'scegli', 'pkgTextdomain' ),
		// 	'clearText' => __( 'Annulla selezione colore', 'pkgTextdomain' ),
		// 	'noColorSelectedText' => __( 'Nessun colore selezionato', 'pkgTextdomain' ),
		// 	'togglePaletteMoreText' => 'more',
		//  'togglePaletteLessText' => 'less',
		// ) );

		// var localization = $.spectrum.localization["it"] = {
		// 	cancelText: "annulla",
		// 	chooseText: "scegli",
		// 	clearText: "Annulla selezione colore",
		// 	noColorSelectedText: "Nessun colore selezionato"
		// };

		// $.extend($.fn.spectrum.defaults, localization);

		// $serialized = json_encode( $exported_data );
		// $data = sprintf( 'window.%s = %s;', $exported_name, $serialized );
		// $wp_scripts->add_data( $handle, 'data', $data );

		// wp_enqueue_script( 'wp-color-picker' );
		// wp_enqueue_style( 'wp-color-picker' );
	}

	/**
	 * Refresh the parameters passed to the JavaScript via JSON.
	 * Let's use early returns here. Not the cleanest anyway.
	 *
	 * @since 0.0.1
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

		// Custom Color
		$this->json['mode'] = 'custom';

		// check for transparent color
		if ( 'transparent' === $value ) {
			$this->json['valueCSS'] = $value;
			return;
		}

		// check for a hex color string
		$custom_color_hex = pwpcp_sanitize_hex_color( $value );
		if ( $custom_color_hex ) {
			// hex color is valid, so we have a Custom Color
			$this->json['valueCSS'] = $custom_color_hex;
			return;
		}

		// check for a rgba color string
		$custom_color_rgba = pwpcp_sanitize_alpha_color( $value );
		if ( $custom_color_rgba ) {
			// hex color is valid, so we have a Custom Color
			$this->json['valueCSS'] = $custom_color_rgba;
			return;
		}
	}

	/**
	 * Render a JS template for the content of the control.
	 *
	 * @since 0.0.1
	 */
	protected function js_tpl() {
		?>
		<?php $this->js_tpl_header(); ?>
		<span class="pwpcpcolor-current pwpcpcolor-current-bg"></span>
		<span class="pwpcpcolor-current pwpcpcolor-current-overlay" style="background:{{data.valueCSS}}"></span>
		<a href="javascript:void(0)" class="pwpcpui-toggle pwpcpcolor-toggle" data-pwpcp-expandtoggle="custom"><?php _e( 'Select Color', 'pkgTextdomain' ) ?></a>
		<div class="pwpcp-expander" data-pwpcp-expander="custom">
			<input class="pwpcpcolor-input" type="text">
		</div>
		<?php
	}
}

/**
 * Register on WordPress Customize global object
 */
$wp_customize->register_control_type( 'PWPcp_Customize_Control_Color' );