<?php
class WP_To_GitHub_Sync_Settings {
    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function setup_plugin_options_menu() {
        add_options_page(
            'WP to GitHub Sync Settings',
            'WP to GitHub Sync',
            'manage_options',
            'wp-to-github-sync',
            array($this, 'render_settings_page_content')
        );
    }

    public function initialize_settings() {
        register_setting(
            'wp_to_github_sync_options',
            'wp_to_github_sync_options',
            array($this, 'validate_options')
        );

        add_settings_section(
            'api_settings',
            'GitHub API Settings',
            array($this, 'api_settings_callback'),
            'wp-to-github-sync'
        );

        add_settings_field(
            'github_token',
            'GitHub Personal Access Token',
            array($this, 'github_token_callback'),
            'wp-to-github-sync',
            'api_settings'
        );

        add_settings_field(
            'github_repo',
            'GitHub Repository',
            array($this, 'github_repo_callback'),
            'wp-to-github-sync',
            'api_settings'
        );
    }

    public function render_settings_page_content($active_tab = '') {
        ?>
        <div class="wrap">
            <h2>WP to GitHub Sync Settings</h2>
            <?php settings_errors(); ?>
            <form method="post" action="options.php">
                <?php
                settings_fields('wp_to_github_sync_options');
                do_settings_sections('wp-to-github-sync');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function api_settings_callback() {
        echo '<p>Enter your GitHub API settings below:</p>';
    }

    public function github_token_callback() {
        $options = get_option('wp_to_github_sync_options');
        echo '<input type="text" id="github_token" name="wp_to_github_sync_options[github_token]" value="' . esc_attr($options['github_token']) . '" />';
    }

    public function github_repo_callback() {
        $options = get_option('wp_to_github_sync_options');
        echo '<input type="text" id="github_repo" name="wp_to_github_sync_options[github_repo]" value="' . esc_attr($options['github_repo']) . '" />';
    }

    public function validate_options($input) {
        $new_input = array();
        if (isset($input['github_token']))
            $new_input['github_token'] = sanitize_text_field($input['github_token']);
        if (isset($input['github_repo']))
            $new_input['github_repo'] = sanitize_text_field($input['github_repo']);
        return $new_input;
    }
}
