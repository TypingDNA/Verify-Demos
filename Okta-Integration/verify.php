<?php

    // start php session in order to have access to the username value
    session_start();

    // if username is missing from session redirect the user to login page
    if(!isset($_SESSION['username'])) {
        header('Location: /');
        die();
    }

    // set Verify credentials
    $client_id='your_verify_client_id';
    $secret='your_verify_client_secret';
    $application_id='your_verify_application_id';

    // include TypingDna Verify library
    include('TypingDNAVerifyClient.php');

    // create and store a TypingDNAVerifyClient instance using your Verify credentials
    $typingDNAVerifyClient = new TypingDNAVerifyClient($client_id, $application_id, $secret);

    // if there is opt param in the link it means the Verify flow is completed and we need to validate it
    if( isset($_GET['otp']) ) {

        // validate opt code
        $response = $typingDNAVerifyClient->validateOTP([
            'email' => $_SESSION['username'],
        ], $_GET['otp']);

        // print response
        print_r($response);

        echo '<p><a href="/logout.php">Log Out</a></p>';
        die();
    }

    // create the data object required to generate Verify button
    $typingDNADataAttributes = $typingDNAVerifyClient->getDataAttributes([
        'email' =>  $_SESSION['username'],
        'language' => "en",
        'mode' => "standard"
    ]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Okta - Verify</title>
    <script src ="https://cdn.typingdna.com/verify/typingdna-verify.js"></script>
    <script>
        // crate the callback function that will be invoked by the Verify window
        function callbackFn(payload)
        {
            window.location.href = `verify.php?otp=${payload["otp"]}`;
        }
    </script>
</head>
<body>
    <p>Second factor of authentication</p>
    <button
            class="typingDNA-verify"
            data-typingdna-client-id="<?= $typingDNADataAttributes['clientId']; ?>"
            data-typingdna-application-id="<?= $typingDNADataAttributes['applicationId']; ?>"
            data-typingdna-payload="<?= $typingDNADataAttributes['payload']; ?>"
            data-typingdna-callback-fn="callbackFn"
    >
        Verify with Typingdna
    </button>
    <p><a href="/logout.php">Log Out</a></p>
</body>
</html>