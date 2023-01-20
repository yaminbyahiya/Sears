<?php

global $wpdb;

// if uninstall.php is not called by WordPress, die
if(!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

// remove_menu_page('edit.php?post_type=acf');
// add_shortcode('pluginshortcode', '__return_false');

$options = $wpdb->get_results("SELECT option_name FROM {$wpdb->prefix}options WHERE option_name LIKE '%eac_option%'");
$updates = $wpdb->get_results("SELECT option_name FROM {$wpdb->prefix}options WHERE option_name LIKE '%eac_up%'");
$nominatims = $wpdb->get_results("SELECT option_name FROM {$wpdb->prefix}options WHERE option_name LIKE '%eac_nominatim_%'");
$menu_item_ids = $wpdb->get_results("SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key LIKE '_eac_custom_nav_%'");

/** Nettoie les options */
if($options && !empty($options)) {
	foreach($options as $option) {
		delete_option($option->option_name);
	}
}

/** Nettoie les options de mise à jour et des transients */
if($updates && !empty($updates)) {
	foreach($updates as $update) {
		delete_option($update->option_name);
	}
}

/** Nettoie les options instagram nominatim du plugin et des transients */
if($nominatims && !empty($nominatims)) {
	foreach($nominatims as $nominatim) {
		delete_option($nominatim->option_name);
	}
}

/** Nettoie les metas données des items de menu */
if($menu_item_ids && !empty($menu_item_ids)) {
	foreach($menu_item_ids as $menu_item_id) {
		delete_post_meta($menu_item_id->post_id, '_eac_custom_nav_menu_item');
	}
}

/** Suppression des capacités editor et shop_manager */
$role = get_role('editor');
if($role->has_cap('eac_manage_options') === true) {
	wp_roles()->remove_cap('editor', 'eac_manage_options');
}
				
$role = get_role('shop_manager');
if(!is_null($role) && $role->has_cap('eac_manage_options') === true) {
	wp_roles()->remove_cap('shop_manager', 'eac_manage_options');
}