<?php
function firebase_request($path, $method = 'GET', $data = null) {
    $base_url = "https://inventory-system-fcd54-default-rtdb.asia-southeast1.firebasedatabase.app/";
    $secret_token = "XH4tvntIt0wSeEejw0jpOdbN8KIntmMIWrOJKMOT";
    
    $base = rtrim($base_url, '/') . '/';
    $clean_path = ltrim($path, '/');
    
    $url = $base . $clean_path . ".json?auth=" . $secret_token;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
    
    if ($data !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    }
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch)) {
        throw new Exception('Curl error: ' . curl_error($ch));
    }
    
    curl_close($ch);
    
    if ($http_code >= 400) {
        throw new Exception("Firebase Error (HTTP Status " . $http_code . "): " . $response);
    }
    
    return json_decode($response, true);
}
?>