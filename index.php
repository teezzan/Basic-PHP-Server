<?php

// Autoload files using composer
require_once __DIR__ . '/vendor/autoload.php';

// Use this namespace
use Steampixel\Route;
use \Firebase\JWT\JWT;
// use Laminas\Http\Request;

// $request = new Request();
$secretKey = base64_decode("teehazzan");


Route::add('/generate_key', function () {
    if (isset($_POST['device_id'])) {
        //generate token
        $token_payload = array(
            "device_id" => $_POST['device_id'],
            "iat" => time() + 1000000,
            "reset_uuid" => 1357000000
        );
        $refresh_payload = array(
            "device_id" => $_POST['device_id'],
            "reset_uuid" => 1357000000
        );
        //add to dbase
        $access_token = JWT::encode(
            $token_payload,
            $GLOBALS['secretKey'],
            'HS512'
        );
        $refresh_token = JWT::encode(
            $refresh_payload,
            $GLOBALS['secretKey'],
            'HS512'
        );

        print_r(array('access_token' => $access_token, 'refresh_token' => $refresh_token));
    } else {
        header('HTTP/1.0 422 Method Not Allowed');
        print_r('Error');
    }
}, 'post');

Route::add('/refresh_key', function () {
    list($refresh_payload_token) = sscanf($_SERVER['HTTP_AUTHORIZATION'], 'Bearer %s');
    if (isset($refresh_payload_token)) {
        $decoded = JWT::decode($refresh_payload_token, $GLOBALS['secretKey'], array('HS512'));
        //check db for uuid
        //generate another, save , hash and send
        $token_payload = array(
            "device_id" => $_POST['device_id'],
            "iat" => time() + 1000000,
            "reset_uuid" => 1357000000
        );
        $access_token = JWT::encode(
            $token_payload,
            $GLOBALS['secretKey'],
            'HS512'
        );
        $refresh_payload = array(
            "device_id" => $_POST['device_id'],
            "reset_uuid" => 1357000000
        );
        $refresh_token = JWT::encode(
            $refresh_payload,
            $GLOBALS['secretKey'],
            'HS512'
        );

        print_r(array('access_token' => $access_token, 'refresh_token' => $refresh_token));
    }
    // $secretKey = base64_decode("teehazzan");
    // $decoded = JWT::decode($jwt, $secretKey, array('HS512'));
    // print_r($decoded);
}, 'post');

Route::add('/data_sink', function () {
    list($jwt) = sscanf($_SERVER['HTTP_AUTHORIZATION'], 'Bearer %s');
    if (isset($jwt)) {
        $decoded = JWT::decode($jwt, $GLOBALS['secretKey'], array('HS512'));
        //add to db
        print_r($_POST);
    } else {
        header('HTTP/1.0 422 Method Not Allowed');
        print_r('Error');
    }
}, 'post');


// Run the router
Route::run('/');
