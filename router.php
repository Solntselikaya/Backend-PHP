<?php
    function route($method, $url, $data) {
        switch ($method) {
            case 'GET': 
                echo 'GET';
                break;
            case 'PUT': 
                echo 'PUT';
                break;
            case 'POST': 
                echo 'POST';
                break;
            case 'DELETE': 
                echo 'DELETE';
                break;
            default:
                echo 'no such method baka';
                break;
        }
    }
?>