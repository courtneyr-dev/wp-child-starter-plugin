<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_One
 * @since Twenty Twenty-One 1.0
 */

get_header();
?>

	<header class="page-header alignwide">
		<h1 class="page-title"><?php esc_html_e( 'OOOPS 🦸🏻‍♀️', 'twentytwentyone' ); ?></h1>
	</header><!-- .page-header -->

	<div class="error-404 not-found default-max-width">
	
	<div class="page-content">
			<div class="search" style="width:100%"> 
				<?php get_search_form(); ?>	
			</div>

			<div class="giphy" style="width:76%;padding:0 0 0 0;float:left;">
				<div style="width:100%;height:0;padding-bottom:75%;position:relative;"><iframe src="https://giphy.com/embed/8p9O3TyoTaNlXDwmSj" width="100%" height="100%" style="position:absolute" frameBorder="0" class="giphy-embed" allowFullScreen></iframe></div><p><a href="https://giphy.com/gifs/BTTF-back-to-the-future-bttf-one-8p9O3TyoTaNlXDwmSj">via GIPHY</a></p>
					<img src = 
			</div>

			<div class="404content" style="width:24%;padding:0 0 0 30px;float:left;">
				<?php esc_html_e( 'What are you looking for? Because it isn\'t here. Maybe searching will help.', 'twentytwentyone' ); ?>
			</div>
		</div>
		<div style="clear:both;"></div>
		<!-- .page-content -->
	</div>	<!-- .error-404 -->


<?php
get_footer();
