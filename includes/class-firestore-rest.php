<?php

use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\ClientException;

class RestFirestore
{

    function getPostById($id)
    {
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
                        'collectionId' => FIRESTORE_COLLECTION,
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
                                        'integerValue' => $id,
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
        if (count($body[0]->document) > 0) {
            return $body[0]->document->name;
        } else {
            return false;
        };
    }

    function savePost($id, $title, $slug, $content, $excerpt)
    {

        $client = new GuzzleHttp\Client(['base_uri' => 'https://firestore.googleapis.com/v1/']);
        $headers = [
            'Authorization' => 'Bearer ' . verifyCustomToken()->idToken,
            'Accept'        => 'application/json',
        ];

        $json = [
            "fields" => [
                "wpid" => ["integerValue" => $id],
                "title" => ["stringValue" => $title],
                "slug" => ["stringValue" => $slug],
                "body" => ["stringValue" => $content],
                "excerpt" => ["stringValue" => $excerpt]
            ]
        ];

        $response = $client->request('POST', 'projects/fenix-test-f76e0/databases/(default)/documents/'.FIRESTORE_COLLECTION, [
            'headers' => $headers,
            'json' => $json
        ]);
    }

    function updatePost($id, $title, $slug, $content, $excerpt, $document)
    {

        $client = new GuzzleHttp\Client(['base_uri' => 'https://firestore.googleapis.com/v1/']);
        $headers = [
            'Authorization' => 'Bearer ' . verifyCustomToken()->idToken,
            'Accept'        => 'application/json',
        ];

        $json = [
            "fields" => [
                "wpid" => ["integerValue" => $id],
                "title" => ["stringValue" => $title],
                "slug" => ["stringValue" => $slug],
                "body" => ["stringValue" => $content],
                "excerpt" => ["stringValue" => $excerpt]
            ]
        ];

        $response = $client->request('PATCH', $document, [
            'headers' => $headers,
            'json' => $json
        ]);
    }
}
