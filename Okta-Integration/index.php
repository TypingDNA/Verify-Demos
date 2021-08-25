<?php

    // start php session
    session_start();

    // if there is a username in session, redirect the user to verify page
    if(isset($_SESSION['username'])) {
        header('Location: /verify.php');
        die();
    }

     // helper function to make http requests
     function http($url, $params=false) {

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if($params) curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));

        return json_decode(curl_exec($ch));
    }

    // set Okta credentials
    $client_id = 'Your_client_id';
    $client_secret = 'Your_client_secret-A-LQ6_Dv';
    $metadata_url = 'https://your_domain.okta.com/oauth2/default/.well-known/oauth-authorization-server';

    // update the redirect_uri variable with the ngrok link generated
    $redirect_uri = 'https://your_subdomain.ngrok.io';

    if(isset($_GET['error'])) {
        die('Authorization server returned an error: '.htmlspecialchars($_GET['error']));
    }

    // get OAuth2 authorization, exchange and introspection endpoints
    $metadata = http($metadata_url);

    // if there is code param in the link, it means it's a redirect from Okta and we need to begin the OAuth2 exchange flow
    if(!isset($_SESSION['username']) && isset($_GET['code'])) {

        // exchange OAuth2 code for an access token
        $response = http($metadata->token_endpoint, [
            'grant_type' => 'authorization_code',
            'code' => $_GET['code'],
            'redirect_uri' => $redirect_uri,
            'client_id' => $client_id,
            'client_secret' => $client_secret,
        ]);

        if(!isset($response->access_token)) {
            die('Error fetching access token');
        }

        // make the introspection request and get the username for the logged user
        $token = http($metadata->introspection_endpoint, [
            'token' => $response->access_token,
            'client_id' => $client_id,
            'client_secret' => $client_secret,
        ]);

        // save the username in php session and redirect the user to verify page
        if($token->active == 1) {
            $_SESSION['username'] = $token->username;
            header('Location: /verify.php');
            die();
        }
    }

    // generate Okta authorization link
    $authorize_url = $metadata->authorization_endpoint.'?'.http_build_query([
        'response_type' => 'code',
        'client_id' => $client_id,
        'redirect_uri' => $redirect_uri,
        'state' => time(),
        'scope' => 'openid',
    ]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Okta - Verify</title>
</head>
<body>
    <p>Not logged in</p>
    <p><a href="<?=$authorize_url?>">Log In</a></p>
</body>
</html>
