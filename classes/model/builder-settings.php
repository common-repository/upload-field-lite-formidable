<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<p class="frm_form_field">
	<label for="fsize_<?php echo esc_attr( $field['id'] ); ?>">
		<?php esc_html_e( 'Max file size (MB)', 'formidable' ); ?>
	</label>
	<input type="text" name="field_options[fsize_<?php echo esc_attr( $field['id'] ); ?>]" id="fsize_<?php echo esc_attr( $field['id'] ); ?>" value="<?php echo esc_attr( $field['fsize'] ); ?>" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"/>
</p>

<p class="frm_form_field">
	<label for="ftypes_<?php echo esc_attr( $field['id'] ); ?>">
		<strong><?php esc_html_e( 'Allowed file types', 'formidable' ); ?></strong>
	</label>
	<input type="text" name="field_options[ftypes_<?php echo esc_attr( $field['id'] ); ?>]" id="ftypes_<?php echo esc_attr( $field['id'] ); ?>" value="<?php echo esc_attr( $field['ftypes'] ); ?>"/>
</p>
