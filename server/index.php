<?php

// Autoload files using composer
require_once __DIR__ . '/vendor/autoload.php';

// Use this namespace
use Steampixel\Route;
use \Firebase\JWT\JWT;

$secretKey = base64_decode("teehazzan");
//use index for indexing :))
$device_table = array();
$refresh_uuid_table = array();


function addToDB($device_id, $reset_uuid)
{
    if (!file_exists("fauxdb.txt")) {

        $device_table = array();
        $refresh_uuid_table = array();

        array_push($device_table, $device_id);
        array_push($refresh_uuid_table, $reset_uuid);

        $output = json_encode(array('device_table' => $device_table, 'refresh_uuid_table' => $refresh_uuid_table));
        file_put_contents("fauxdb.txt", $output);
    } else {
        $raw_text = file_get_contents("fauxdb.txt");
        $fauxDB = json_decode($raw_text, true);
        if (in_array($device_id, $fauxDB['device_table']) == false) {

            array_push($fauxDB['device_table'], $device_id);
            array_push($fauxDB['refresh_uuid_table'], $reset_uuid);

            $output = json_encode($fauxDB);
            file_put_contents("fauxdb.txt", $output);
        } else {
            $index = array_search($device_id, $fauxDB['device_table']);
            $fauxDB['refresh_uuid_table'][$index] = $reset_uuid;

            $output = json_encode($fauxDB);
            file_put_contents("fauxdb.txt", $output);
        }
    }
}
function getDeviceUUID($device_id)
{
    if (file_exists("fauxdb.txt")) {
        $raw_text = file_get_contents("fauxdb.txt");
        $fauxDB = json_decode($raw_text, true);
        $index = array_search($device_id, $fauxDB['device_table']);
        return $fauxDB['refresh_uuid_table'][$index];
    }
}
function deviceExist($device_id)
{
    if (file_exists("fauxdb.txt")) {
        $raw_text = file_get_contents("fauxdb.txt");
        $fauxDB = json_decode($raw_text, true);
        $index = array_search($device_id, $fauxDB['device_table']);
        return $index;
    }
}

Route::add('/generate_key', function () {
    if (isset($_POST['device_id'])) {
        //generate token
        $reset_uuid = uniqid();;
        $token_payload = array(
            "iat" => time(),
            "nbf" => time(),
            "reset_uuid" => $reset_uuid,
            "device_id" => $_POST['device_id']
        );
        $refresh_payload = array(
            "device_id" => $_POST['device_id'],
            "reset_uuid" => $reset_uuid
        );
        //add to dbase
        addToDB($_POST['device_id'], $reset_uuid);

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

        $resp = json_encode(array('access_token' => $access_token, 'refresh_token' => $refresh_token));
        print_r($resp);
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
        // print_r('decoded => \n');
        // print_r($decoded);
        // print_r('\n\n\n\n\n');
        // print_r('\nfile => ' . file_get_contents("fauxdb.txt"));
        if ($decoded->reset_uuid == getDeviceUUID($decoded->device_id)) {

            $reset_uuid = uniqid();
            addToDB($decoded->device_id, $reset_uuid);
            $token_payload = array(
                "device_id" => $decoded->device_id,
                "iat" => time(),
                "nbf" => time(),
                "reset_uuid" => $reset_uuid
            );
            $access_token = JWT::encode(
                $token_payload,
                $GLOBALS['secretKey'],
                'HS512'
            );
            $refresh_payload = array(
                "device_id" => $decoded->device_id,
                "reset_uuid" => $reset_uuid
            );
            $refresh_token = JWT::encode(
                $refresh_payload,
                $GLOBALS['secretKey'],
                'HS512'
            );
            $resp = json_encode(array('access_token' => $access_token, 'refresh_token' => $refresh_token));
            print_r($resp);
        } else {
            header('HTTP/1.0 422 Expired Refresh Token');
            print_r('Error');
        }
    }
}, 'post');

Route::add('/data', function () {
    list($token) = sscanf($_SERVER['HTTP_AUTHORIZATION'], 'Bearer %s');
    if (isset($token)) {
        $decoded = JWT::decode($token, $GLOBALS['secretKey'], array('HS512'));
        //check db for device_id using uuid
        if ((time() - $decoded->iat < (20 * 60)) && ($decoded->reset_uuid == getDeviceUUID($decoded->device_id))) {
            print_r(json_encode(array('status' => 'success', 'data' => json_encode($_POST))));
        } else {
            header('HTTP/1.0 422 Authentication failed');
            print_r('Error');
        }
    }
}, 'post');

// Run the router
Route::run('/');
