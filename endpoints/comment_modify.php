<?php 

    function api_comment_delete($request) {
        $post_id = $request['id'];

        $comments = get_comments([
            'post_id' => $post_id
        ]);

        return rest_ensure_response($comments);
    }
 

    function register_api_comment_delete() {
        register_rest_route( 'api', '/comment/delete', [
            'methods' => WP_REST_Server::DELETABLE,
            'callback' => 'api_comment_delete',
        ]);
    };

    add_action( 'rest_api_init', 'register_api_comment_delete');

    // comment edit

    function api_comment_edit($request) {
        $post_id = $request['id'];

        $comments = get_comments([
            'post_id' => $post_id
        ]);

        return rest_ensure_response($comments);
    }
 

    function register_api_comment_edit() {
        register_rest_route( 'api', '/comment/edit', [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => 'api_comment_edit',
        ]);
    };

    add_action( 'rest_api_init', 'register_api_comment_edit');

?>