<?php 

    function api_comment_delete($request) {
        $comment_id = $request['id'];
        $user = wp_get_current_user();
        $user_id = $user->ID;

        if($user_id === 0) {
            $response = new WP_Error('error', 'Usuário não possuí permissão', ['status' => 401]);
            return rest_ensure_response($response);
        }



        $response = wp_delete_comment( $comment_id, $force_delete = true );

        return rest_ensure_response($response);

    }

 

    function register_api_comment_delete() {
        register_rest_route( 'api', '/comment/delete/(?P<id>[0-9]+)', [
            'methods' => WP_REST_Server::DELETABLE,
            'callback' => 'api_comment_delete',
        ]);
    };

    add_action( 'rest_api_init', 'register_api_comment_delete');

?>