<?php
/**
 * Custom functions that act independently of the theme templates
 *
 * @package strtr
 */

/**
 * Get a post's featured image at a specified size.
 *
 * @see strtr_get_attachment_image_at_size()
 *
 * @param int $post_id ID of post.
 * @param str $size
 * @return str|null
 */
function strtr_get_featured_image_at_size( $post_id, $size = null ) {
	$img_id = get_post_thumbnail_id( $post_id );
	return strtr_get_attachment_image_at_size( $img_id, $size );
}

/**
 * Get an attachment image URL at a specified size.
 *
 * @param int $attachment_id ID of attachment.
 * @param str $size
 * @return str|null
 */
function strtr_get_attachment_image_at_size( $attachment_id, $size = null ) {

	if ( ! $attachment_id || ! is_numeric( $attachment_id ) ) {
		return null;
	}

	$size = ( $size ) ? $size : 'large'; // default to WP's 'large' if no size specified.

	$img_url = null;
	$img = wp_get_attachment_image_src( $attachment_id, $size );
	if ( $img && ! empty( $img[0] ) ) {
		$img_url = $img[0];
	}
	return $img_url;
}

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function strtr_body_classes( $classes ) {
	// Adds a class of group-blog to blogs with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	return $classes;
}
add_filter( 'body_class', 'strtr_body_classes' );

if ( version_compare( $GLOBALS['wp_version'], '4.1', '<' ) ) :
	/**
	 * Filters wp_title to print a neat <title> tag based on what is being viewed.
	 *
	 * @param string $title Default title text for current view.
	 * @param string $sep Optional separator.
	 * @return string The filtered title.
	 */
	function strtr_wp_title( $title, $sep ) {
		if ( is_feed() ) {
			return $title;
		}

		global $page, $paged;

		// Add the blog name.
		$title .= get_bloginfo( 'name', 'display' );

		// Add the blog description for the home/front page.
		$site_description = get_bloginfo( 'description', 'display' );
		if ( $site_description && ( is_home() || is_front_page() ) ) {
			$title .= " $sep $site_description";
		}

		// Add a page number if necessary.
		if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() ) {
			$title .= " $sep " . sprintf( esc_html__( 'Page %s', 'strtr' ), max( $paged, $page ) );
		}

		return $title;
	}
	add_filter( 'wp_title', 'strtr_wp_title', 10, 2 );

	/**
	 * Title shim for sites older than WordPress 4.1.
	 *
	 * @link https://make.wordpress.org/core/2014/10/29/title-tags-in-4-1/
	 * @todo Remove this function when WordPress 4.3 is released.
	 */
	function strtr_render_title() {
		?>
		<title><?php wp_title( '|', true, 'right' ); ?></title>
		<?php
	}
	add_action( 'wp_head', 'strtr_render_title' );
endif;
