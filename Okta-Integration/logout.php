<?php

    // start php session
    session_start();

    // remove the username from session
    unset($_SESSION['username']);

    // redirect the user to login page
    header('Location: /');
?>