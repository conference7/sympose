<?php
/**
 * CMB2
 *
 * @link       https://sympose.net
 * @since      1.4.6
 *
 * @package    Sympose
 * @subpackage Sympose/includes
 */

/**
 * Blocks
 *
 * @since      1.4.6
 * @package    Sympose
 * @subpackage Sympose/includes
 * @author     Sympose <info@sympose.io>
 */
class Sympose_CMB2 {

	/**
	 * Construct
	 *
	 * @since    1.4.6
	 */
	public function __construct() {
		add_action( 'cmb2_render_ordered-list', array( $this, 'render_ordered_list' ), 10, 5 );
	}

	/**
	 * Render list.
	 *
	 * @param object $field The CMB2 field object.
	 * @param string $escaped_value The value.
	 * @param int    $object_id The object ID.
	 * @param string $object_type The type of object.
	 * @param object $field_type The Field type object.
	 *
	 * @since 1.4.6
	 */
	public function render_ordered_list( $field, $escaped_value, $object_id, $object_type, $field_type ) {

		$order           = array();
		$comma_separated = $escaped_value;
		$value           = explode( ',', $comma_separated );

		if ( is_array( $value ) && ! empty( $value ) ) {
			$order = $value;
		}

		//phpcs:ignore
		echo $field_type->hidden(
			array(
				'name'  => $field_type->_name(),
				'value' => $escaped_value, //phpcs:ignore
				'desc'  => '',
			)
		);
		if ( is_callable( $field->args['options_cb'] ) ) {
			$options = call_user_func( $field->args['options_cb'], $field );

			if ( $options && is_array( $options ) ) {
				if ( is_array( $order ) ) {
					$options = array_replace( array_flip( $order ), $options );
				}

				echo '<ul class="cmb2-list ' . esc_attr( $field->args['classes'] ) . '">';
				foreach ( $options as $key => $option ) {
					if ( false !== $option && ! empty( $option ) ) {
						//phpcs:ignore
						echo '<li id="' . esc_attr( $key ) . '"><svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000"><g><rect fill="none" height="24" width="24"/></g><g><g><g><path d="M20,9H4v2h16V9z M4,15h16v-2H4V15z"/></g></g></g></svg> ' . $option . '</li>';
					}
				}
				echo '</ul>';
			}
		}
	}

}
