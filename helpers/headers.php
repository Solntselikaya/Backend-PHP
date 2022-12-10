<?php

/*
    Вспомогательные функции для формирования HTTP-статусов 
*/

//include_once 'models/TokenResponse.php';
//include_once 'models/Response.php';

function setHTTPStatus($statusNum = 200, $message = null, $errors = null) {

    switch ($statusNum) {
        default:
        case 200:
            $status = "HTTP/1.0 200 OK";
            break;
        case 400:
            $status = "HTTP/1.0 400 Bad Request";
            break;
        case 401:
            $status = "HTTP/1.0 401 Unauthorized";
            break;
        case 403: 
            $status = "HTTP/1.0 403 Forbidden";
            break;
        case 404:
            $status = "HTTP/1.0 404 Not Found";
            break;
        case 500:
            $status = "HTTP/1.0 500 Internal Server Error";
            break;        
    }

    header($status);

    if (!is_null($message) && $statusNum == 200) {
        //$responseBody = new TokenResponse($message);
        echo json_encode(['token' => $message]);
    }
    else if (!is_null($message) && $statusNum != 200) {
        echo json_encode([
            'status' => $statusNum,
            'message' => $message
        ]);
    }
    else if (!is_null($errors)){
        /* $responseBody = new Response([
            'status' => $statusNum,
            'message' => $message,
            'errors' => $errors
        ]); */

        echo json_encode([
            'status' => $statusNum,
            'message' => $message,
            'errors' => $errors
        ]);
    }

    //var_dump($responseBody);
}

?>