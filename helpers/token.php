<?php

/*
    Вспомогательные функции для работы с токенами 
*/

function createToken($userEmail): string {

    $header = json_encode([
        'alg' => 'HS256',
        'typ' => 'JWT'
    ]);

    $currDateTime = new DateTime();
    $payload = json_encode([
        'email' => $userEmail,
        'iss' => "http://localhost/",
        'aud' => "http://localhost/",
        'nbf' => $currDateTime->getTimeStamp(),
        'exp' => $currDateTime->getTimeStamp() + 3600,
        'iat' => $currDateTime->getTimeStamp()
    ]);

    $secret = "nvrgnngvyup";

    $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
    $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
    
    $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

    $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

    return $jwt;
}

function isTokenExist($jwt): bool {

    $token = explode('.', $jwt);
    if (!isset($token[1]) && !isset($token[2])) {
        return false;
    }

    $userHeader = $token[0];
    $userPayload = $token[1];
    $userSignature = $token[2];

    $decodedPayload = json_decode(base64_decode($userPayload));
    if (!isset($decodedPayload->email)) {
        return false;
    }

    $userEmail = $decodedPayload->email;
    $emailExists = $GLOBALS['dbLink']->query("SELECT email FROM users WHERE email = '$userEmail'")->fetch_assoc();
    if (is_null($emailExists)) {
        return false;
    }

    $signature = hash_hmac('sha256', $userHeader . "." . $userPayload, "nvrgnngvyup", true);
    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

    return ($base64UrlSignature === $userSignature);
}

function getEmailFromToken($token): string {
    $tokenList = explode('.', $token);

    $userPayload = $tokenList[1];
    $decodedPayload = json_decode(base64_decode($userPayload));
    return $decodedPayload->email;
}

function isTokenAlive($token): bool {

    $tokenList = explode('.', $token);
    if (count($tokenList) != 3) {
        return false;
    }

    $userPayload = json_decode(base64_decode($tokenList[1]));

    $currDateTime = new DateTime();
    return ($currDateTime->getTimeStamp() < $userPayload->exp);
}

function isTokenInBlackList($token): bool {

    $userEmail = getEmailFromToken($token);

    $tokenExists = $GLOBALS['dbLink']->query("SELECT token FROM blackList WHERE email = '$userEmail'")->fetch_assoc();
    if (is_null($tokenExists)) {
        return false;
    }

    return true;
}

function getToken($userEmail): string {

    $tokenExists = $GLOBALS['dbLink']->query("SELECT token FROM blackList WHERE email = '$userEmail'")->fetch_assoc();
    if (is_null($tokenExists)) {
        return createToken($userEmail);
    }

    $token = $tokenExists['token'];
    if (isTokenAlive($token)) {
        $GLOBALS['dbLink']->query("DELETE FROM blackList WHERE token = '$token'");
        return $token;
    }

    $GLOBALS['dbLink']->query("DELETE FROM blackList WHERE token = '$token'");
    return createToken($userEmail);
}

function addTokenToBlackList($token) {

    $userEmail = getEmailFromToken($token);

    $GLOBALS['dbLink']->query("INSERT blackList (email, token) values ('$userEmail', '$token')");
}

function checkBearerToken($token) {

    if (!isTokenExist($token)) {
        $response = new Response(401, "Invalid token");
        setHTTPStatus(401, $response);
        exit;
    }

    if (isTokenInBlackList($token)) {
        $response = new Response(401, "User is unauthorized");
        setHTTPStatus(401, $response);
        exit;
    }

    if(!isTokenAlive($token)) {
        $response = new Response(401, "Token expired");
        setHTTPStatus(401, $response);
        addTokenToBlackList($token);
        exit;
    }
}

?>