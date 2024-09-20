<?php
/**
 * Plugin Name: WP 2 GitHub Sync
 * Plugin URI: http://www.baad.in/wp-to-github-sync/
 * Description: An easy plugin to automatically sync WordPress posts to GitHub. 
 * Version: 1.0.1
 * Author: baadin
 * Author URI: http://baad.in/
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: wp-to-github-sync
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('WP_TO_GITHUB_SYNC_VERSION', '1.0.0');
define('WP_TO_GITHUB_SYNC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WP_TO_GITHUB_SYNC_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include the main plugin class
require_once plugin_dir_path(__FILE__) . 'includes/class-wp-to-github-sync.php';

// Begin execution of the plugin
function run_wp_to_github_sync() {
    $plugin = new WP_To_GitHub_Sync();
    $plugin->run();
}
run_wp_to_github_sync();

// add onekey sync

add_action('admin_enqueue_scripts', 'wp_to_github_sync_enqueue_admin_scripts');

function wp_to_github_sync_enqueue_admin_scripts($hook) {
    if ('edit.php' != $hook) {
        return;
    }
    wp_enqueue_script('wp-to-github-sync-admin-js', plugin_dir_url(__FILE__) . 'admin/js/wp-to-github-sync-admin.js', array('jquery'), WP_TO_GITHUB_SYNC_VERSION, true);
    wp_localize_script('wp-to-github-sync-admin-js', 'wpToGitHubSync', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('wp_to_github_sync_nonce'),
    ));
}

add_action('wp_ajax_push_to_github', 'wp_to_github_sync_push_to_github');

function wp_to_github_sync_push_to_github() {
    check_ajax_referer('wp_to_github_sync_nonce', 'nonce');
    
    if (!current_user_can('edit_posts')) {
        wp_send_json_error('Permission denied');
    }
    
    $post_id = intval($_POST['post_id']);
    $post = get_post($post_id);
    
    if (!$post) {
        wp_send_json_error('Post not found');
    }
    
    $api = new WP_To_GitHub_Sync_API();
    $result = $api->push_to_github($post);
    
    if ($result === true) {
        wp_send_json_success('Successfully pushed to GitHub');
    } else {
        wp_send_json_error('Failed to push to GitHub: ' . $result);
    }
}
