<?php 
    function api_user_post($request) {
        $response = [
            'ID' => '2',
            'user_login' => 'meu_usuario',
        ];
        return rest_ensure_response($response);
    };  

    function register_api_user_post() {
        register_rest_route( 'api', 'user', [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => 'api_user_post',
        ]);
    };

    add_action( 'rest_api_init', 'register_api_user_post');

?>