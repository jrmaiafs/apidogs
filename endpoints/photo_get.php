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
            'total_comments' => $total_comments,
            'curtidas' => $post_meta['curtidas'][0],
            'user_ID' => $user->ID
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

    // photo list

    function api_photos_get($request) {
    $user = wp_get_current_user();
       $_total = sanitize_text_field($request['_total']) ?: 6;
       $_page = sanitize_text_field($request['_page']) ?: 1;
       $_user = sanitize_text_field($request['_user']) ?: 0;

       if(!is_numeric($_user)) {
           $user = get_user_by('login', $_user);
           if(!$user) {
            $response = new WP_Error('error', 'Usuário não encontrado', ['status' => 404]);
            return rest_ensure_response($response);
           }
           $_user = $user->ID;
       }

       $args = [
           'post_type' => 'post',
           'author' => $_user,
           'post_per_page' => $_total,
           'paged' => $_page
       ];

       $query = new WP_Query($args);
       $posts = $query->posts;


       $photos = [];
       if($posts) {
           foreach($posts as $post) {
                $photos[] = photo_data($post);
           };
       };

       return rest_ensure_response($photos);
   
    }
 

    function register_api_photos_get() {
        register_rest_route( 'api', '/photo', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => 'api_photos_get',
        ]);
    };

    add_action( 'rest_api_init', 'register_api_photos_get');

    
    // photo curtir 
    
    function api_photo_curtir($request) {
        $user = wp_get_current_user();

        $post_id = $request['id'];
        $post = get_post($post_id);


        if(!isset($post) || empty($post_id)) {
            $response = new WP_Error('error', 'Post não encontrado', ['status' => 404]);
            return rest_ensure_response($response);
        }
        add_post_meta($post_id, 'ids_curtidas', $user->ID);
        $photo = photo_data($post);
        $photo['curtidas'] = (int) $photo['curtidas'] + 1;
        update_post_meta($post_id, 'curtidas', $photo['curtidas']);
        
        $post_meta_curtidas = get_post_meta($post->ID);

        $response = [
            'curtidas' => $photo['curtidas'],
            'ids_photo_likes' =>  $post_meta_curtidas['ids_curtidas']

        ];
        return rest_ensure_response($response);
    }
 

    function register_api_photo_curtir() {
        register_rest_route( 'api', '/photo/curtir/(?P<id>[0-9]+)', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => 'api_photo_curtir',
        ]);
    };

    add_action( 'rest_api_init', 'register_api_photo_curtir');

        // photo curtir list 
    
        function api_photo_curtidas($request) {
            $user = wp_get_current_user();

            $post_id = $request['id'];
            $post = get_post($post_id);
    
    
            if(!isset($post) || empty($post_id)) {
                $response = new WP_Error('error', 'Post não encontrado', ['status' => 404]);
                return rest_ensure_response($response);
            }
            $photo = photo_data($post);
            $post_meta_curtidas = get_post_meta($post->ID);
    
            $response = [
                'curtidas' => $photo['curtidas'],
                'ids_photo_likes' =>  $post_meta_curtidas['ids_curtidas']
    
            ];
            return rest_ensure_response($response);
        }
     
    
        function register_api_photo_curtidas() {
            register_rest_route( 'api', '/photo/likes/(?P<id>[0-9]+)', [
                'methods' => WP_REST_Server::READABLE,
                'callback' => 'api_photo_curtidas',
            ]);
        };
    
        add_action( 'rest_api_init', 'register_api_photo_curtidas');

?>