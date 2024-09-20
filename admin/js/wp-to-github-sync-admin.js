(function($) {
    'use strict';

    $(function() {
        $('.github-push').on('click', function(e) {
            e.preventDefault();
            var postId = $(this).data('post-id');
            var $button = $(this);
            
            $button.text('Pushing...');
            
            $.ajax({
                url: wpToGitHubSync.ajaxurl,
                type: 'POST',
                data: {
                    action: 'push_to_github',
                    post_id: postId,
                    nonce: wpToGitHubSync.nonce
                },
                success: function(response) {
                    if (response.success) {
                        alert('Successfully pushed to GitHub!');
                    } else {
                        alert('Failed to push to GitHub: ' + response.data);
                    }
                },
                error: function() {
                    alert('An error occurred while pushing to GitHub.');
                },
                complete: function() {
                    $button.text('Push to GitHub');
                }
            });
        });
    });

})(jQuery);
