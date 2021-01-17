<?php

/* enqueue scripts and style from parent theme */
   
function twentytwentyone_styles() {
	wp_enqueue_style( 'child-style', get_stylesheet_uri(),
	array( 'twenty-twenty-one-style' ), wp_get_theme()->get('Version') );
	wp_enqueue_style( 'child-style', get_template_directory_uri() .'/css/style-editor.css', false, '1.0', 'all' );
} 

// add_action( 'wp_enqueue_scripts', 'enqueue_load_fa' );
// function enqueue_load_fa() {
// wp_enqueue_style( 'load-fa', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' );
// }

add_action( 'wp_enqueue_scripts', 'enqueue_load_fa' );
function enqueue_load_fa() {
wp_enqueue_style( 'load-fa', 'https://use.fontawesome.com/releases/v5.15.1/css/all.css' );
}


add_action( 'wp_enqueue_scripts', 'twentytwentyone_styles');

	/**
	 * Print the next and previous posts navigation.
	 *
	 * @since Twenty Twenty-One 1.0
	 *
	 * @return void
	 */
	function twenty_twenty_one_the_posts_navigation() {
		the_posts_pagination(
			array(
				'before_page_number' => esc_html__( 'Page', 'twentytwentyone' ) . ' ',
				'mid_size'           => 0,
				'prev_text'          => sprintf(
					'<span class="nav-prev-text">&laquo </span>',
					// is_rtl() ? twenty_twenty_one_get_icon_svg( 'ui', 'arrow_right' ) : twenty_twenty_one_get_icon_svg( 'ui', 'arrow_left' ),
					// wp_kses(
					// 	__( ' Newer <span class="nav-short">posts</span>', 'twentytwentyone' ) ,
					// 	array(
					// 		'span' => array(
					// 			'class' => array(),
					// 		),
					// 	)
					// )
				),
				'next_text'          => sprintf(
					'<span class="nav-next-text">&raquo</span>',
					// wp_kses(
					// 	__( 'Older <span class="nav-short">posts</span>', 'twentytwentyone' ),
					// 	array(
					// 		'span' => array(
					// 			'class' => array(),
					// 		),
					// 	)
					// ),
					// is_rtl() ? twenty_twenty_one_get_icon_svg( 'ui', 'arrow_left' ) : twenty_twenty_one_get_icon_svg( 'ui', 'arrow_right' )
				),
			)
		);
	}


		function twenty_twenty_one_posted_by() {
			remove_filter('pre_user_description', 'wp_filter-kses');
		}


