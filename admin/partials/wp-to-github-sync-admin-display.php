<div class="wrap wp-to-github-sync-admin">
    <h2><?php echo esc_html(get_admin_page_title()); ?></h2>
    <form action="options.php" method="post">
        <?php
        settings_fields('wp_to_github_sync_options');
        do_settings_sections('wp-to-github-sync');
        submit_button('Save Settings');
        ?>
    </form>
</div>
