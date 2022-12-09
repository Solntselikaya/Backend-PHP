<?php
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
        'exp' => $currDateTime->getTimeStamp() + 1800,
        'iat' => $currDateTime->getTimeStamp()
    ]);

    $secret = 'nvrgnngvyup';
    
    $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
    $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
    
    $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

    $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

    //echo $jwt;

    return $jwt;
}

?>