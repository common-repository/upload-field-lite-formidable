<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

?>
<?php /* translators: %s: Maximum upload file size in MB*/ ?>
<label>	<?php echo esc_html( sprintf( __( 'Maximum upload size: %sMB', 'formidable-pro' ), FrmField::get_option( $this->field, 'fsize' ) ) ); ?><br>
<input type="file" id="<?php echo esc_attr( $html_id ); ?>" name="<?php echo esc_attr( $field_name ); ?>" value="<?php echo esc_attr( $field['default_value'] ); ?>" class="dyn_default_value" disabled /></label>
