<?php
/**
 * The template for displaying a "No posts found" message
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_One
 * @since Twenty Thirteen 1.0
 */
?>

<header class="page-header">
	<h1 class="page-title"><?php _e( 'Nothing Found', 'twentytwentyone' ); ?></h1>
</header>

<div class="page-content">
	<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>

	<p>
		<?php
		/* translators: %s: Post editor URL. */
		printf( __( 'Ready to publish your first post? <a href="%s">Get started here</a>.', 'twentytwentyone' ), admin_url( 'post-new.php' ) );
		?>
	</p>

	<?php elseif ( is_search() ) : ?>

	<p><?php _e( 'Sorry, but nothing matched your search terms. Please try again with different keywords.', 'twentytwentyone' ); ?></p>
		<?php get_search_form(); ?>

	<?php else : ?>

	<p><?php _e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'twentytwentyone' ); ?></p>
		<?php get_search_form(); ?>

	<?php endif; ?>
</div><!-- .page-content -->
