<?php
/**
 * Plugin Name:       File Upload Field for Formidable Forms (lite)
 * Plugin URI:        https://desolint.com/
 * Description:       Add Upload field in Formidable form
 * Version:           1.1.1
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author URI:        https://desolint.com/
 * Author:            Desol Int
 *
 * @package           Desolint
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

define( 'UFL_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Auto load plugin activation file.
 *
 * @since 3.0
 */
function frmfile_load_formidable_field() {
	spl_autoload_register( 'frmfile_forms_autoloader' );
}
add_action( 'plugins_loaded', 'frmfile_load_formidable_field' );
/**
 * Only load Di classes here.
 *
 * @param string $class_name name of class which load di classes here.
 *
 * @since 3.0
 */
function frmfile_forms_autoloader( $class_name ) {
	if ( ! preg_match( '/^Frm.+$/', $class_name ) ) {
		return;
	}
	$filepath = dirname( __FILE__ );
	$filepath .= '/classes/model/class-' . $class_name . '.php';

	if ( file_exists( $filepath ) ) {
		require $filepath;
	}
}

/**
 * Tell Formidable where to find the field type.
 *
 * @since 3.0
 * @param string $class Tell formidable class name.
 * @param string $field_type Tell formidable field type.
 *
 * @return string $class
 */
function frmfile_get_field_type_class( $class, $field_type ) {
	if ( 'lite-file' === $field_type ) {
		$class = 'FrmLiteUploadField';
	}
	return $class;
}

add_filter( 'frm_get_field_type_class', 'frmfile_get_field_type_class', 10, 2 );
/**
 * Tell Formidable where to find the new field type.
 *
 * @since 3.0
 * @param string $fields Tell formidable where.
 *
 * @return string $fields
 */
function frmfile_add_new_field( $fields ) {
	$fields['lite-file'] = array(
		'name' => 'File Upload',
		'icon' => 'frm_icon_font frm_upload_icon',
	);
	return $fields;
}
add_filter( 'frm_available_fields', 'frmfile_add_new_field' );
