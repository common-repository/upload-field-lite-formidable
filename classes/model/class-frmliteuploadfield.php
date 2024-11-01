<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * FrmLiteUploadField File Doc Comment.
 *
 * @category FrmLiteUploadField
 * @package   formidable
 * @author    Desolint
 */
class FrmLiteUploadField extends FrmFieldType {

	/**
	 * Create new input type in Formidable input feilds
	 *
	 * @var string
	 * @since 3.0
	 */
	protected $type = 'lite-file';
	/**
	 * Does the field include a input box to type into?
	 *
	 * @var bool
	 * @since 3.0
	 */
	protected $has_input = true;
	/**
	 * Does the field include a input box to type into?
	 *
	 * @var bool
	 * @since 3.0
	 */
	public $file_url = true;
	/**
	 * Which Formidable settings should be hidden or displayed?
	 */
	protected function field_settings_for_type() {
		$settings              = parent::field_settings_for_type();
		$settings['read_only'] = true;
		return $settings;
	}

	/**
	 * Need custom options too? Add them here or remove this function.
	 */
	protected function extra_field_opts() {
		return array(
			'ftypes' => 'jpg,png,pdf',
			'fsize'  => 1,
		);
	}
	/**
	 * This function validate file size.
	 *
	 * @param int $mb_limit get file size.
	 * @return int
	 */
	public static function get_max_file_size( $mb_limit = 256 ) {
		if ( ! $mb_limit || ! is_numeric( $mb_limit ) ) {
			$mb_limit = 516;
		}
		$mb_limit   = (float) $mb_limit;
		$upload_max = wp_max_upload_size() / 1000000;
		return round( min( $upload_max, $mb_limit ), 3 );
	}
	/**
	 * Update the options in a field
	 */
	protected function include_form_builder_file() {
		return UFL_PLUGIN_DIR_PATH . 'classes/view/back-end\builder-field.php';
	}
	/**
	 * This is required to add a settings
	 * section just for this field. show_extra_field_choices will not be triggered
	 * without it.
	 *
	 * @param bool $field Get the type of field being displayed.
	 * @return array
	 */
	public function displayed_field_type( $field ) {
		return array(
			$this->type => true,
		);
	}

	/**
	 * Posted file assiated array data convert to simple array data.
	 *
	 * @param array $file posted file array.
	 * @param int   $id posted entry id.
	 * @return array
	 */
	public static function get_file_data_array( $file, $id ) {
		$array = array(
			'name'     => $file['item_meta']['name'][$id],
			'type'     => $file['item_meta']['type'][$id],
			'tmp_name' => $file['item_meta']['tmp_name'][$id],
			'error'    => 0,
			'size'     => $file['item_meta']['size'][$id],
		);
		return $array;
	}

	/**
	 * Posted file assiated array data convert to simple array data.
	 *
	 * @param array $explode_comma posted file array.
	 * @return array
	 */
	public static function create_explode_array( $explode_comma ) {
		$exploded_array = array();
		$array          = explode( ',', $explode_comma );
		foreach ( $array as $value ) {
			$exploded_array[] = preg_replace( '/\s+/', '', $value );
		}
		return $exploded_array;
	}
	/**
	 * Get the file name for a single media ID
	 *
	 * @since 3.0
	 * @param int $id
	 * @return boolean|string $filename
	 */
	private function get_single_file_name( $id ) {
		$filepath = get_attached_file( $id, true );
		if ( ! is_string( $filepath ) ) {
			return false;
		}
		return basename( $filepath );
	}

	/**
	 * Add settings in the builder here.
	 *
	 * @since 4.0
	 * @param array $args - Includes 'field', 'display', and 'values'.
	 */
	public function show_extra_field_choices( $args ) {
		$field = $args['field'];
		include UFL_PLUGIN_DIR_PATH . 'classes/model/builder-settings.php';
	}
	/**
	 * Set input Type.
	 */
	protected function html5_input_type() {
		return 'file';
	}
	/**
	 * Posted form validate
	 *
	 * @param array $args all data of posted form.
	 * @return array|null If there is an error, return an array.
	 */
	public function validate( $args ) {
		$post_id          = $args['id'];
		$errors           = array();
		$file             = self::get_file_data_array( $_FILES, $post_id );
		$type             = $file['type'];
		$file_info        = wp_check_filetype( basename( $file['name'] ) );
		$mb_limit         = FrmField::get_option( $this->field, 'fsize' );
		$ftypes           = FrmField::get_option( $this->field, 'ftypes' );
		$allow_file_types = self::create_explode_array( $ftypes );
		$size_limit       = self::get_max_file_size( $mb_limit );
		$this_file_size   = $file['size'];
		$this_file_size   = $this_file_size / 1000000; // compare in MB.

		if ( $this_file_size > $size_limit ) {
			// translators: %sMB: File size limit (Megabytes).
			$errors['field' . $args['id']] .= sprintf( __( ' This file is too big. It must be less than %sMB.<br>', 'formidable-pro' ), $size_limit );
		}
		if ( empty( $allow_file_types ) ) {
			$allow_file_types = 'jpg';
		}
		if ( ! in_array( $file_info['ext'], $allow_file_types, true ) && ! empty( $file['name'] ) ) {
			// translators: %s: File type.
			$errors['field' . $args['id']] .= sprintf( __( ' This file type %s Not allow.<br>', 'formidable-pro' ), $file_info['ext'] );
		}
		return $errors;
	}
	/**
	 * Upload file data save
	 *
	 * @since 3.0
	 * @param array|string $value (the posted value).
	 * @param array        $atts the posted form feild attributes.
	 *
	 * @return array|string $value
	 */
	public function get_value_to_save( $value, $atts ) {
		$field_id = $atts['field_id'];
		// Uploaded File array Data.
		$file = self::get_file_data_array( $_FILES, $field_id );

		if ( '' !== $file['name'] ) {

			if ( ! function_exists( 'media_handle_sideload' ) ) {
				require_once ABSPATH . 'wp-admin/includes/image.php';
				require_once ABSPATH . 'wp-admin/includes/file.php';
				require_once ABSPATH . 'wp-admin/includes/media.php';
			}

			$move_file_id        = media_handle_sideload( $file, $post_id = 0, $desc = null );
			$move_file_url       = wp_get_attachment_url( $move_file_id );
			$file_base_name      = basename( $move_file_url );
			$file_type_info      = pathinfo( $move_file_url );
			$ftypes              = FrmField::get_option( $this->field, 'ftypes' );
			$allow_file_types    = self::create_explode_array( $ftypes );
			$file_type_extention = $allow_file_types;
			if ( $move_file_id && ! isset( $move_file_id['error'] ) ) {
				// Single image upload in gallery.
				if ( in_array( $file_type_info['extension'], $file_type_extention, true ) ) {
					$value = '<a href=' . $move_file_url . "><img src='" . $move_file_url . "' width='100'><br>" . $file_base_name . '</a>';
				} else {
					$value = '<a href=' . $move_file_url . ' >' . $file_base_name . '</a>';
				}
			} else {
				/*
				 * Error generated by _wp_handle_upload().
				 * @see _wp_handle_upload() in wp-admin/includes/file.php
				 */
				return $move_file_id['error'];
			}

			return $value;
		}
	}

	/**
	 * Customize the way the value is displayed in emails and views.
	 *
	 * @param array $value all posted values view in entries.
	 * @param array $atts all attributes view in entries.
	 * @return string
	 */
	protected function prepare_display_value( $value, $atts ) {
		$field_value = isset( $atts['entry']->metas ) ? $atts['entry']->metas : $value;
		return $field_value;
	}

	/**
	 * String Whatever shows in the front end goes here.
	 *
	 * @param array $args all posted values.
	 * @param array $shortcode_atts all attributes.
	 * @return sting html input
	 */
	public function front_field_input( $args, $shortcode_atts ) {
		$field_name    = $args['field_name'];
		$html_id       = $args['html_id'];
		$default_value = $this->field['default_value'];
		ob_start();
		include UFL_PLUGIN_DIR_PATH . 'classes/view/front-end/front-end-field.php';
		$input_html = ob_get_contents();
		ob_end_clean();
		return $input_html;
	}
}
