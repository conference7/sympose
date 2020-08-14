<?php
/**
 * Sympose list
 *
 * @link       https://sympose.net
 * @since      1.0.0
 *
 * @package    Sympose
 * @subpackage Sympose/public/partials
 */

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<?php
if ( $term && $posts ) :
	?>
	<h1><?php echo esc_html( $term->name ); ?></h1>
	<?php
endif;
?>
<div class="sympose-list">
	<?php

	foreach ( $posts as $post_item ) :
		$sym              = array( 'classes' => array() );
		$sym['classes'][] = $post_item->post_type;
		$sym['classes'][] = 'square';

		$classes = implode( ' ', $sym['classes'] );

		// Using featured image.
		$img_id = sympose_get_image( $post_item );

		// Using post meta.
		$img = wp_get_attachment_image( $img_id, 'medium' );

		$desc = get_post_meta( $post_item->ID, $this->prefix . 'description', true );

		?>
		<a href="<?php echo esc_url( get_permalink( $post_item->ID ) ); ?>" class="sym <?php echo esc_attr( $classes ); ?>">
			<disv class="image">
				<?php
                // phpcs:disable
                echo wp_filter_content_tags( $img );
				// phpcs:enable
				?>
			</disv>
			<div class="information">
				<h3 class="title">
					<?php echo esc_html( $post_item->post_title ); ?>
				</h3>
				<?php if ( $desc ) : ?>
					<p class="description">
						<?php echo esc_html( $desc ); ?>
					</p>
				<?php endif; ?>
			</div>
		</a>
		<?php
	endforeach;
	?>
</div>
