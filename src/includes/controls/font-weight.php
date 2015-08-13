<?php // @partial
/**
 * Select Control custom class
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
class PWPcp_Customize_Control_Font_Weight extends PWPcp_Customize_Control_Select {

	/**
	 * Control type.
	 *
	 * @since 0.0.1
	 * @var string
	 */
	public $type = 'pwpcp_font_weight';

	/**
	 * Selectize disabled (`false`) or enabled (just `true` or array of options)
	 *
	 * @since 0.0.1
	 * @var boolean|array
	 */
	public $choices = array(
		'normal' => array(
			'label' => 'Normal',
			'sublabel' => 'Defines a normal text. This is default',
		),
		'bold' => array(
			'label' => 'Bold',
			'sublabel' => 'Defines thick characters',
		),
		'bolder' => array(
			'label' => 'Bolder',
			'sublabel' => 'Defines thicker characters',
		),
		'lighter' => array(
			'label' => 'Lighter',
			'sublabel' => 'Defines lighter characters',
		),
		'100' => '100',
		'200' => '200',
		'300' => '300',
		'400' => '400 (Same as normal)',
		'500' => '500',
		'600' => '600',
		'700' => '700 (Same as bold)',
		'800' => '800',
		'900' => '900',
		'initial' => array(
			'label' => 'Initial',
			'sublabel' => 'Sets this property to its default value',
		),
		'inherit' => array(
			'label' => 'Inherit',
			'sublabel' => 'Inherits this property from its parent element',
		),
	);
}

/**
 * Register on WordPress Customize global object
 */
$wp_customize->register_control_type( 'PWPcp_Customize_Control_Font_Weight' );