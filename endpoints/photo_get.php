<?php 
    function photo_data($post) {
        $post_meta = get_post_meta($post->ID);
        $src = wp_get_attachment_image_src($post_meta['img'][0], 'large')[0];
        $user = get_userdata($post->post_author);
        $total_comments = get_comments_number($post->ID);

        return [
            'id' => $post->ID,
            'author' => $user->user_login,
            'title' => $post->post_title,
            'date' => $post->post_date,
            'src' => $src,
            'weight' => $post_meta['weight'][0],
            'age' => $post_meta['age'][0],
            'accesses' => $post_meta['accesses'][0],
            'total_comments' => $total_comments

        ];
    }

    function api_photo_get($request) {
        $post_id = $request['id'];
        $post = get_post($post_id);

        if(!isset($post) || empty($post_id)) {
            $response = new WP_Error('error', 'Post não encontrado', ['status' => 404]);
            return rest_ensure_response($response);
        }

        $photo = photo_data($post);
        $photo['accesses'] = (int) $photo['accesses'] + 1;
        update_post_meta($post_id, 'accesses', $photo['accesses']);

        $comments = get_comments([
            'post_id' => $post_id,
            'order' => 'ASC'
        ]);

        $response = [
            'photo' => $photo,
            'comments' => $comments
        ];

        return rest_ensure_response($response);
    }
 

    function register_api_photo_get() {
        register_rest_route( 'api', '/photo/(?P<id>[0-9]+)', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => 'api_photo_get',
        ]);
    };

    add_action( 'rest_api_init', 'register_api_photo_get');

?>