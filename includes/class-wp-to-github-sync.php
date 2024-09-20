<?php
class WP_To_GitHub_Sync {
    protected $loader;
    protected $plugin_name;
    protected $version;

    public function __construct() {
        $this->plugin_name = 'wp-to-github-sync';
        $this->version = WP_TO_GITHUB_SYNC_VERSION;
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    private function load_dependencies() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-wp-to-github-sync-settings.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-wp-to-github-sync-api.php';
    }

    private function set_locale() {
        add_action('plugins_loaded', array($this, 'load_plugin_textdomain'));
    }

    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'wp-to-github-sync',
            false,
            dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );
    }

    private function define_admin_hooks() {
        $plugin_settings = new WP_To_GitHub_Sync_Settings($this->get_plugin_name(), $this->get_version());
        add_action('admin_menu', array($plugin_settings, 'setup_plugin_options_menu'));
        add_action('admin_init', array($plugin_settings, 'initialize_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_filter('post_row_actions', array($this, 'add_github_push_action'), 10, 2);
        add_action('post_submitbox_misc_actions', array($this, 'add_github_push_button_to_publish_box'));
    }

    private function define_public_hooks() {
        add_action('publish_post', array($this, 'sync_post_to_github'), 10, 2);
    }

    public function enqueue_admin_styles() {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'admin/css/wp-to-github-sync-admin.css', array(), $this->version, 'all');
    }

    public function enqueue_admin_scripts() {
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'admin/js/wp-to-github-sync-admin.js', array('jquery'), $this->version, false);
    }

    public function sync_post_to_github($post_id, $post) {
        $api = new WP_To_GitHub_Sync_API();
        $api->push_to_github($post);
    }

    public function get_plugin_name() {
        return $this->plugin_name;
    }

    public function get_version() {
        return $this->version;
    }

    public function run() {
//        $this->define_admin_hooks();
        $this->define_public_hooks();
    }
    
    public function add_github_push_action($actions, $post) {
        if (current_user_can('edit_post', $post->ID)) {
        $actions['github_push'] = '<a href="#" class="github-push" data-post-id="' . $post->ID . '">Push to GitHub</a>';
        }
    return $actions;
    }

    public function add_github_push_button_to_publish_box() {
        global $post;
        echo '<div class="misc-pub-section">';
        echo '<a href="#" class="button github-push" data-post-id="' . $post->ID . '">Push to GitHub</a>';
        echo '</div>';
    }
}
