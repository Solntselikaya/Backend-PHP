<?php

include_once 'router.php';
include_once 'helpers/headers.php';

global $dbLink;
global $UUID_REGEX;
$UUID_REGEX = "/[0-9a-f]{8}-[0-9a-f]{4}-[0-5][0-9a-f]{3}-[089ab][0-9a-f]{3}-[0-9a-f]{12}/";

date_default_timezone_set('Asia/Novosibirsk');

function getAdress() {
    $url = rtrim(isset($_GET['q']) ? $_GET['q']: '');
    $str = explode('/', $url);
    return $str;
}

function getData() {
    $data = new stdClass();
    $data->body = json_decode(file_get_contents('php://input'));
    $params = explode('&',$_SERVER['QUERY_STRING']);
    $data->params = [];

    $uwu = [];
    foreach ($params as $key => $value) {
        $dev = explode('=', $value);
        if (!isset($uwu[$dev[0]])) {
            $uwu[$dev[0]] = [];
        }
        array_push($uwu[$dev[0]], $dev[1]);
    }
    foreach ($uwu as $key => $value) {
        if (count($value) == 1) {
            $data->params[$key] = $value[0];
            continue;
        }
        $data->params[$key] = $value;
    }
    return $data;
}

header('Content-type: application/json');

$dbLink = new mysqli("127.0.0.1", "back_guy", "password", "backend");
if (!$dbLink) {
    $response = new Response(404, "DB Connection error: ".mysqli_connect_error());
    setHTTPStatus(500, $response);
    exit;
}

$adress = getAdress();
$data = getData();

route(
    $_SERVER['REQUEST_METHOD'],
    $adress,
    $data
);

?>