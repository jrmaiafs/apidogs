<?php

$dirbase = get_template_directory();

require_once $dirbase . '/endpoints/user_post.php';
require_once $dirbase . '/endpoints/user_get.php';

function change_api() {
    return 'json';
}

add_filter( 'rest_url_prefix', 'change_api');

function expire_token() {
    return time() + (60 * 60 * 24);
}

add_filter( 'jwt_auth_expire', 'expire_token');

?>

