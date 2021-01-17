<?php

/**
 * Template Name: Home Page
 */

get_header(); 

?>
<h1>This is a test</h1>

<div id="primary">
	<div id="content" role="main">

		<?php while ( have_posts() ) : the_post(); ?>

			<h1><?php the_field('custom_title'); ?></h1>

			<img src="<?php the_field('hero_image'); ?>" />

			<p><?php the_content(); ?></p>

		<?php endwhile; // end of the loop. ?>

	</div><!-- #content -->
</div><!-- #primary -->

<?php get_footer(); ?>