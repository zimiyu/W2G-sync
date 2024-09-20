<?php
class WP_To_GitHub_Sync_API {
    private $github_token;
    private $github_repo;

    public function __construct() {
        $options = get_option('wp_to_github_sync_options');
        $this->github_token = $options['github_token'];
        $this->github_repo = $options['github_repo'];
    }

    public function push_to_github($post) {
    $content = $this->format_post_for_github($post);
    $filename = $this->get_github_filename($post);
    $api_url = "https://api.github.com/repos/{$this->github_repo}/contents/{$filename}";

    $response = wp_remote_request($api_url, array(
        'method' => 'PUT',
        'headers' => array(
            'Authorization' => "token {$this->github_token}",
            'Content-Type' => 'application/json',
        ),
        'body' => json_encode(array(
            'message' => "Update {$post->post_title}",
            'content' => base64_encode($content),
            'sha' => $this->get_file_sha($api_url)
        ))
    ));

    if (is_wp_error($response)) {
        return 'GitHub API Error: ' . $response->get_error_message();
    }

    $response_code = wp_remote_retrieve_response_code($response);
    if ($response_code === 200 || $response_code === 201) {
        return true;
    } else {
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        return isset($data['message']) ? $data['message'] : 'Unknown error occurred';
    }
    }

    private function format_post_for_github($post) {
        $content = "---\n";
        $content .= "title: " . $post->post_title . "\n";
        $content .= "date: " . $post->post_date . "\n";
        $content .= "---\n\n";
        $content .= $post->post_content;
        return $content;
    }

    private function get_github_filename($post) {
        $date = new DateTime($post->post_date);
        return $date->format('Y-m-d') . '-' . sanitize_title($post->post_title) . '.md';
    }

    private function get_file_sha($api_url) {
        $response = wp_remote_get($api_url, array(
            'headers' => array(
                'Authorization' => "token {$this->github_token}",
            )
        ));

        if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
            $body = json_decode(wp_remote_retrieve_body($response), true);
            return $body['sha'];
        }

        return null;
    }
}
