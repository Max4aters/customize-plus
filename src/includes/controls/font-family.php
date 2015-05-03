<?php // @partial
/**
 * Font Family Control custom class
 *
 * @since  0.0.1
 */
class PWPcp_Customize_Control_Font_Family extends PWPcp_Customize_Control_Base {

	public $type = 'pwpcp_font_family';

	/**
	 * [enqueue description]
	 * @return [type] [description]
	 */
	public function enqueue() {
		wp_enqueue_script( 'jquery-ui-sortable' );
	}

	/**
	 * Refresh the parameters passed to the JavaScript via JSON.
	 *
	 * @since 0.0.1
	 */
	protected function add_to_json() {
		$this->json['value'] = pwpcp_sanitize_font_families( $this->value() );
	}

	/**
	 * Render a JS template for the content of the control.
	 *
	 * @since 0.0.1
	 */
	protected function js_tpl() {
		?>
		<label>
			<?php $this->js_tpl_header(); ?>
		</label>
		<!-- <label>
			<input class="pwpcp-font-google-toggle" type="checkbox" value="0">
		 	<?php _e( 'Enable Google fonts', 'pkgTextdomain' ); ?>
		</label> -->
		<input class="pwpcp-selectize" type="text" value="{{ data.value }}" required>
		<?php
	}
}

/**
 * Register on WordPress Customize global object
 */
$wp_customize->register_control_type( 'PWPcp_Customize_Control_Font_Family' );