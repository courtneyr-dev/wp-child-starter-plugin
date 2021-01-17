<?php
/**
 * The template for displaying author info below posts.
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_One
 * @since Twenty Twenty-One 1.0
 */

?>
<?php if ( (bool) get_the_author_meta( 'description' ) && post_type_supports( get_post_type(), 'author' ) ) : ?>
	<div class="author-bio <?php echo get_option( 'show_avatars' ) ? 'show-avatars' : ''; ?>">
		<?php echo get_avatar( get_the_author_meta( 'ID' ), '85' ); ?>
		<div class="author-bio-content">
			<h2 class="author-title"><?php printf( /* translators: 1: Author name. */ esc_html__( 'By %s', 'twentytwentyone' ), get_the_author() ); ?></h2>
			<p class="author-description"> <?php the_author_meta( 'description' ); ?></p><!-- .author-description -->

			<?php 
				$twitter = get_the_author_meta( 'twitter', $post->post_author );
				$facebook = get_the_author_meta( 'facebook', $post->post_author );
				$instagram = get_the_author_meta( 'instagram', $post->post_author );
				$linkedin = get_the_author_meta( 'linkedin', $post->post_author );
				$pinterest = get_the_author_meta( 'pinterest', $post->post_author );
				$youtube = get_the_author_meta( 'linkedin', $post->post_author );
				echo '<a href="https://twitter.com/' . $twitter .' " rel="nofollow" target="_blank"><span class="dashicons dashicons-twitter"></span></a> &nbsp;<a href="'. $instagram .'" rel="nofollow" target="_blank"><span class="dashicons dashicons-instagram"></span></a>   &nbsp; 
				<a href="'. $facebook .'" rel="nofollow" target="_blank"><span class="dashicons dashicons-facebook"></span></a>  &nbsp; <a href="'. $linkedin .'" rel="nofollow" target="_blank"><span class="dashicons dashicons-linkedin"></span></a>   &nbsp;<a href="'. $youtube .'" rel="nofollow" target="_blank"><span class="dashicons dashicons-youtube"></span></a> &nbsp;   <a href="'. $pinterest .'" rel="nofollow" target="_blank"><span class="dashicons dashicons-pinterest"></span></a>  &nbsp; <a href="https://github.com/courane01" rel="nofollow" target=_blank"> <span class="fab fa-github-square"></span></a>  &nbsp; <a href="https://profiles.wordpress.org/courane01" rel="nofollow" target=_blank"><span class="dashicons dashicons-wordpress"></span></a>'
				?>  
		</div><!-- .author-bio-content -->
	</div><!-- .author-bio -->
<?php endif; ?>

