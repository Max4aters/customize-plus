<?php // @partial
/**
 * Textarea Control custom class
 *
 * @since  1.0.0
 *
 * @package    Customize_Plus
 * @subpackage Customize\Controls
 * @author     KnitKode <dev@knitkode.com> (https://knitkode.com)
 * @copyright  2018 KnitKode
 * @license    GPLv3
 * @version    Release: pkgVersion
 * @link       https://knitkode.com/products/customize-plus
 */
class KKcp_Customize_Control_Textarea extends KKcp_Customize_Control_Base {

	/**
	 * @since 1.0.0
	 * @inheritDoc
	 */
	public $type = 'kkcp_textarea';

	/**
	 * Allow HTML inside textarea (default = `false`)
	 *
	 * @since 1.0.0
	 * @var boolean
	 */
	public $allowHTML = false;

	/**
	 * Enable TinyMCE textarea (default = `false`)
	 *
	 * @since 1.0.0
	 * @var boolean|array
	 */
	public $wp_editor = false;

	/**
	 * WP editor allowed options
	 *
	 * Sanitize methods must be class methods of `KKcp_Sanitize` or global
	 * functions
	 *
	 * The commented options are not allowed to be changed and some of theme
	 * are always overriden in js to the indicated default value.
	 *
	 * @see the following docs:
	 * - https://codex.wordpress.org/Function_Reference/wp_editor
	 * - https://codex.wordpress.org/Quicktags_API#Default_Quicktags
	 * - https://codex.wordpress.org/TinyMCE
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public static $wp_editor_allowed_options = array(
		'wpautop' => array( 'sanitizer' => 'bool' ),
		// Default: false (instead of WP core's true)

		'mediaButtons' => array( 'sanitizer' => 'bool' ),
		// Default: false (instead of WP core's true)

		// 'textareaName' => 'string',
		// Default: $editor_id

		'textareaRows' => array( 'sanitizer' => 'int' ),
		// Default: 5 (instead of WP core's get_option('default_post_edit_rows', 10))

		// 'tabindex' => array( 'sanitizer' => 'int' ),
		// Default: None

		// 'editorCss' => array( 'sanitizer' => 'string' ),
		// Default: None

		'editorClass' => array( 'sanitizer' => 'string' ),
		// Default: Empty string

		'editorHeight' => array( 'sanitizer' => 'int' ),
		// Default: None

		// 'teeny' => array( 'sanitizer' => 'bool' ),
		// Default: true (instead of WP core's false)

		// 'dfw' => array( 'sanitizer' => 'bool' ),
		// Default: false

		'tinymce' => array( 'sanitizer' => 'bool_object', 'permissive_object' => true ),
		// Default: true (we don't sanitize each option here @@todo)

		'quicktags' => array( 'sanitizer' => 'bool_object', 'values' => array(
			'buttons' => array( 'sanitizer' => 'string' )
		) ),
		// Default: true

		'dragDropUpload' => array( 'sanitizer' => 'bool' ),
		// Default: false
	);

	/**
	 * @since 1.0.0
	 * @inheritDoc
	 */
	protected function add_to_json() {
		$this->json['attrs'] = $this->input_attrs;

		$this->json['allowHTML'] = KKcp_SanitizeJS::bool( $this->allowHTML );

		// @@doubt Should we check for capability here? move this to the constructor \\
		if ( $this->wp_editor && user_can_richedit() ) {
			if ( is_array( $this->wp_editor ) ) {
				$this->json['wp_editor'] = KKcp_SanitizeJS::options( $this->wp_editor, self::$wp_editor_allowed_options );
			} else {
				$this->json['wp_editor'] = KKcp_SanitizeJS::bool( $this->wp_editor );
			}
			wp_enqueue_editor();
		}
	}

	/**
	 * @since 1.0.0
	 * @inheritDoc
	 */
	protected function js_tpl() {
		?>
		<label>
			<?php $this->js_tpl_header(); ?><# var a = data.attrs; #>
			<textarea class="kkcpui-textarea<# if (data.wp_editor && data.wp_editor.editorClass) { #> {{ data.wp_editor.editorClass }}<# } #>" <# for (var key in a) { if (a.hasOwnProperty(key)) { #>{{ key }}="{{ a[key] }}" <# } } #> rows="<# if (data.wp_editor && data.wp_editor.textareaRows) { #>{{ data.wp_editor.textareaRows }}<# } else if (a.rows) { #>{{ a.rows }}<# } else { #>4<# } #>"<# if (data.wp_editor && data.wp_editor.editorHeight) { #> style="height:{{ data.wp_editor.editorHeight }}px"<# } #>><?php // filled through js ?></textarea>
		</label>
		<?php
	}

	/**
	 * @since 1.0.0
	 * @inheritDoc
	 */
	protected static function sanitize( $value, $setting, $control ) {
		return KKcp_Sanitize::textarea( $value, $setting, $control );
	}

	/**
	 * @since 1.0.0
	 * @inheritDoc
	 */
	protected static function validate( $validity, $value, $setting, $control ) {
		return KKcp_Validate::textarea( $validity, $value, $setting, $control );
	}
}

/**
 * Register on WordPress Customize global object
 */
$wp_customize->register_control_type( 'KKcp_Customize_Control_Textarea' );