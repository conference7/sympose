<?php

class Sympose_Divi {

	public function __construct( $sympose = '', $version = '', $prefix = '_sympose_' ) {
		$this->sympose = $sympose;
		$this->version = $version;
		$this->prefix  = $prefix;
		$this->hooks();
	}

	public function hooks() {
		add_filter( 'et_builder_custom_dynamic_content_fields', array( $this, 'add_custom_fields' ), 20, 3 );
		add_filter( 'et_builder_dynamic_content_meta_value', array( $this, 'maybe_filter_dynamic_content_meta_value' ), 10, 2 );
	}

	public function maybe_filter_dynamic_content_meta_value( $meta_value, $meta_key ) {
		if ( $meta_key === '_sympose_event_date' ) {
			$timestamp = get_term_meta( get_queried_object_id(), $this->prefix . 'event_date', true );
			$date      = new Datetime();
			$date->setTimestamp( $timestamp );
			$meta_value = $date->format( get_option( 'date_format' ) );
		}
		return $meta_value;
	}

	public function add_custom_fields( $custom_fields, $post_id, $raw_custom_fields ) {
		$custom_fields['custom_meta__sympose_event_date'] = array(
			'label'    => 'Event Date',
			'type'     => 'any',
			'fields'   =>
			array(
				'before' =>
				array(
					'label'   => 'Vóór',
					'type'    => 'text',
					'default' => '',
					'show_on' => 'text',
				),
			),
			'meta_key' => '_sympose_event_date',
			'custom'   => true,
			'group'    => 'Sympose',
		);
		return $custom_fields;

	}
}
