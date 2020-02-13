<?php

// Requires: composer require
use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\ClientException;


// Get service account's email address and private key from the JSON key file
$service_account_email = "firebase-adminsdk-gw5je@fenix-test-f76e0.iam.gserviceaccount.com";
$private_key = "-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQC7U464JHutgT+o\nGrVQQf9yMRProGTaNE4UsZUXDQ+oMJ9mqe3sAPGjDQuZ8ei+lpg3COKxyz222kA8\nURsut6aBhAP6QCLzwg1j1CZRYiRfj29eO93nWPUwl53XdRft0bYNbjg8scpouAaM\n5admqX/hl5ibBVP1WhNfcSO53aEOeaytIkV7PE9ScxOygMtLEdB9lyf1X8Cffni1\nYJGA/uj61vt6dS5UBUdRvdQ0tkIHkC1QuDVNUESrlOOzjdOrWlpNiNpAPWzGInR1\nP8WFmR0kRIP3VXeD54BQJ511bOsNUI8m3O1DEcxyIzIqaesCTUo1XXjw0N8jV0Rf\nUk2pieqVAgMBAAECggEABl+kRPv/de/7sFFhpuk/8uzcOSb8F3fdH5N1N6i68NA1\nXuMG6dc/DCWsUItsYwOxCnpTwD8339SoMoUt8BZ75KnkZWuGOkCkKlKJuo4kylg7\n2VB0eePLH+FLaQ0eXc9GAEn7ltu+ana3HlcirXm0lK05mi4BunYwn42JbNBaI6m4\nNQ8iBMPaS/K5lLnxFS6wHMIlLgp6DhNlHqE8/qdsopb5Fk9J4iDRZkvGlL8hcE8H\nVewcjsBKl9drSAEmCkJCJtxul8kd0y3gVI+hle0t4tKVpsxs4hj3VuqEyKVMQJOb\nfH9+yef/peQv5ODT9zCow7TJ5tUx77XNbBfVAU8WEQKBgQDtPELwM/lkmsl02Xja\ngUVkQmFAfmNPSLcNKPVilKTxawpeSaeTTiMVjrRSm66bevdrrzsrdlyJO8F38K/x\noxMe3YISzCznweyCbAP2y4+4Yt+RK+9EgZLsoW6nMJkMnUONInIlt48PWIqpZmfe\nF+6BpjtQRju7OK3boM+ie0WUfQKBgQDKJLKC4zPyVjBrYbS1XIxE9FN5V/1K8v+d\nXy6W2tpJr2qHkxKoqAGDqdYHjjueHizqxonv92vlNhOfQsUAxd3UpxX6J2NIWGrj\nsUH9gPxO2KVlcUlEJqVP+qoSUfuVqiPY4pvOrJ5c0/cfFBk4mPBTTKd5tLtb9AeE\nMethXOgB+QKBgHWXakKwMI1qoDMYXOxKKYBB2vzh6Q3yqDGQvNlJftzfxvrnnXXJ\nxGA6JS3RV2JgOGYbLMQlXkbz5Lk0B7JJt/+Topb4t8WE/VvEeM6LJkkqUVEfKvGV\nHKPIfRXIZAS5qzM5AMWrT/E2XT0Msq2GxUVkhYAL4C20MZFtC56pZdrtAoGBAKEM\nt8ScK+jpTk5fW9Aa2g6d8lt9BOrPy2OgT8gUuVF3lpJJVNZWdE5n44PFiC4jsHmk\nkIVFRQsrGFsMoVRKUMB6FoJlGy6qcw7RTNgiCeLvrGaz6UGI4LBl71YFR0IoecL0\nWFpDITCoF/0E0EtNXjufmNbYbWWHeO2TsAq0MIxRAoGAUzZSDW8mLItZpxD5u6dR\nrMPl8f2ZNkCL5Bup/MPyA1GxepM851CZOidJv3G13IceIEJCZSaHDC6baGTP4a2l\nhrdXjK8+V7aGb9AO/GOkAfzGzWHUSAV9Rkm/pclaFbqnioukREJeaBh0vBCLm9cd\nQdv6QZGYt5VR2w6E5kfqlao=\n-----END PRIVATE KEY-----\n";

function create_custom_token($uid, $is_premium_account)
{
    global $service_account_email, $private_key;

    $now_seconds = time();
    $payload = array(
        "iss" => $service_account_email,
        "sub" => $service_account_email,
        "aud" => "https://identitytoolkit.googleapis.com/google.identity.identitytoolkit.v1.IdentityToolkit",
        "iat" => $now_seconds,
        "exp" => $now_seconds + (60 * 60),  // Maximum expiration time is one hour
        "uid" => $uid,
        "claims" => array(
            "premium_account" => $is_premium_account
        )
    );
    return JWT::encode($payload, $private_key, "RS256");
}

// Exchange custom token for an ID and refresh token
function verifyCustomToken()
{
    $client = new Client();
    $headers = [
        'Content-Type'        => 'application/json',
    ];
    $query = [
        'key' => FIREBASE_API_KEY,
    ];
    $json = ['token' => create_custom_token(FIREBASE_UID, false), 'returnSecureToken' => true];

    try {
        $response = $client->request('POST', 'https://identitytoolkit.googleapis.com/v1/accounts:signInWithCustomToken', [
            'query' => $query,
            'headers' => $headers,
            'json' => $json
        ]);
    } catch (ClientException $e) {
        echo Psr7\str($e->getRequest());
        echo Psr7\str($e->getResponse());
    }

    return json_decode($response->getBody()->getContents());
}
