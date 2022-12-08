<?php
    include_once 'router.php';

    global $dbLink;

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

    $adress = getAdress();
    $data = getData();

    route(
        $_SERVER['REQUEST_METHOD'],
        $adress,
        $data
    );

?>