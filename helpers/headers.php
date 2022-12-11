<?php

/*
    Вспомогательные функции для формирования HTTP-статусов 
*/

include_once 'models/Response.php';
include_once 'models/TokenResponse.php';

function setHTTPStatus($statusNum = 200, $responseBody = null) {

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

    /*
    if (!is_null($message) && $statusNum == 200) {
        echo json_encode(['token' => $message]);
    }
    else if (!is_null($errors)){
        echo json_encode([
            'status' => $statusNum,
            'message' => $message,
            'errors' => $errors
        ]);
    }
    else if (!is_null($message) && $statusNum != 200) {
        echo json_encode([
            'status' => $statusNum,
            'message' => $message
        ]);
    }
    */

    if (!is_null($responseBody)) {
        $responseBody->echoContents();
    }

}

?>