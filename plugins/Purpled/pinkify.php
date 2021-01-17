<?php
/**
 * Plugin Name: Pinkify It!
 * Plugin URI: http://suzettefranck.com/pinkify-it
 * Description: Regardless of your theme, overrides the theme's styles to make everything pretty and pink.
 * Version: 2.2
 * Author: Suzette Franck
 * Author URI: http://suzettefranck.com
 * License: GPL2
 */


/*  Copyright 2014  Suzette Franck (email : hello@suzettefranck.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

add_action( 'wp_enqueue_scripts', 'saf_pinkify_styles' );

  function saf_pinkify_styles() {
       wp_register_style( 'saf-pinkify', plugins_url('pinkify.css', __FILE__)  );
       wp_enqueue_style( 'saf-pinkify' );
  }

  function saf_enqueue_custom_admin_theme() {
    wp_enqueue_style( 'saf-enqueue-custom-admin-theme', plugins_url( 'wp-admin.css', __FILE__ ) );
  }

add_action( 'admin_enqueue_scripts', 'saf_enqueue_custom_admin_theme' );
