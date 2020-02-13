<?php
/*
Plugin Name: Fenix Firebase
Description: Firebase integratin fenix test
Author: Giancarlo Buono
*/

define('FIREBASE_API_KEY', 'AIzaSyDkqO7hU4ViDkWjaan7VX5yBFOC_UrEVuI');
define('FIREBASE_UID', '3fh7bz9u1QWRrOvS9Hp1IRyWvXx1');
define('FIRESTORE_COLLECTION', 'posts');
// Include mfp-functions.php, use require_once to stop the script if mfp-functions.php is not found
require_once(__DIR__ . '/vendor/autoload.php');
require_once plugin_dir_path(__FILE__) . 'includes/firebase-custom-token.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-firestore-rest.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin-menu.php';

// if you don't add 3 as as 4th argument, this will not work as expected
add_action('save_post_post', 'save_firestore_post', 10, 3);

function save_firestore_post($post_ID, $post, $update)
{

    $restfirestore = new RestFirestore();

    $post_exists = $restfirestore->getPostById($post_ID);
    if ($post->post_status === 'publish' && !$post_exists) {
        $restfirestore->savePost($post_ID, $post->post_title, $post->post_name, $post->post_content, $post->post_excerpt);
    } elseif ($post->post_status === 'publish' && $post_exists) {
        $restfirestore->updatePost($post_ID, $post->post_title, $post->post_name, $post->post_content, $post->post_excerpt, $post_exists);
    }
}
