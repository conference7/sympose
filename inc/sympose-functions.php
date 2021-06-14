<?php
/**
 * General functions
 *
 * @link       https://sympose.net
 * @since      1.0.0
 *
 * @package    Sympose
 * @subpackage Sympose/general_functions
 */

/**
 *
 * Get option value
 *
 * @param bool $option Define the option key to retrieve.
 *
 * @return bool|mixed|void return the option if found.
 */
function sympose_get_option( $option = false ) {
	$options = get_option( 'sympose' );
	if ( ! $option ) {
		if ( empty( $options ) ) {
			return false;
		}

		return $options;
	} else {
		if ( isset( $options[ $option ] ) ) {
			return $options[ $option ];
		}

		return false;
	}

	return false;
}

/**
 *
 * Get the set image for a post type
 *
 * @since   1.2.0
 *
 * @param   object|int $post object or Post ID.
 *
 * @return  mixed returns the image.
 */
function sympose_get_image( $post ) {

	if ( is_int( $post ) ) {
		$post = get_post( $post );
	} elseif ( is_string( $post ) ) {
		$post = get_post( intval( $post ) );
	}

	$image = get_post_meta( $post->ID, '_sympose_image_id', true );

	if ( is_wp_error( $image ) || empty( $image ) ) {
		return false;
	}

	// Fallback for featured image.
	if ( empty( $image ) && apply_filters( 'sympose_featured_image_fallback', true ) === true ) {
		$image = get_post_thumbnail_id( $post );
	}

	// If it's string, convert to int.
	if ( ! is_int( $image ) && ! is_int( intval( $image ) ) ) {
		$image = false;
	}

	return $image;
}

/**
 *
 * Get the schedule page for an event
 *
 * @since   1.2.0
 *
 * @param   object|int $term The object term or term ID.
 *
 * @return  string The url to the schedule page
 */
function sympose_get_schedule_page( $term ) {

	if ( is_int( $term ) ) {
		$term = get_term( $term, 'event' );
	}

	if ( ! $term ) {
		return false;
	}

	$page_id = absint( get_term_meta( $term->term_id, '_sympose_schedule_page_id', true ) );

	if ( empty( $page_id ) ) {

		if ( 0 !== $term->parent ) {
			$page_id = absint( get_term_meta( $term->parent, '_sympose_schedule_page_id', true ) );
		}
	}

	if ( ! empty( $page_id ) ) {
		return $page_id;
	}
}
