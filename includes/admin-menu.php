<?php

use \Firebase\JWT\JWT;

/*
 * Add my new menu to the Admin Control Panel
 */

// Hook the 'admin_menu' action hook, run the function named 'mfp_Add_My_Admin_Link()'
add_action( 'admin_menu', 'test_plugin_setup_menu' );
 
// Add a new top level menu link to the ACP
function test_plugin_setup_menu(){
    add_menu_page( 'Test Plugin Page', 'Test Plugin', 'manage_options', 'test-plugin', 'fenix_debug_page' );
}

function fenix_debug_page() {
    echo '<div class="wrap">';
    $client = new GuzzleHttp\Client(['base_uri' => 'https://firestore.googleapis.com/v1/']);
        $headers = [
            'Authorization' => 'Bearer ' . verifyCustomToken()->idToken,
            'Accept'        => 'application/json',
        ];

        $json = array(
            'structuredQuery' =>
            array(
                'from' =>
                array(
                    0 =>
                    array(
                        'collectionId' => 'posts',
                    ),
                ),
                'select' =>
                array(
                    'fields' =>
                    array(
                        0 =>
                        array(
                            'fieldPath' => 'wpid',
                        )
                    ),
                ),
                'where' =>
                array(
                    'compositeFilter' =>
                    array(
                        'filters' =>
                        array(
                            0 =>
                            array(
                                'fieldFilter' =>
                                array(
                                    'field' =>
                                    array(
                                        'fieldPath' => 'wpid',
                                    ),
                                    'op' => 'EQUAL',
                                    'value' =>
                                    array(
                                        'integerValue' => 15,
                                    ),
                                ),
                            ),
                        ),
                        'op' => 'AND',
                    ),
                ),
                'limit' => 1,
            ),
        );

        $response = $client->request('POST', 'projects/fenix-test-f76e0/databases/(default)/documents:runQuery', [
            'headers' => $headers,
            'json' => $json
        ]);

        $body = json_decode($response->getBody()->getContents());
            print_r($body[0]->document->name);
    echo '</div>';
}