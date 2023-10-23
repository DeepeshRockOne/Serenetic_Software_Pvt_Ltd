<?php

$selCred = "SELECT * FROM api_info";
$resCred = $pdo->selectOne($selCred);

$API_USERNAME = trim(checkIsset($resCred["username"]));
$API_PASSWORD = trim(checkIsset($resCred["password"]));

$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api-endpointsalvasen.benefitportal.com/api/product/",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "Authorization: Basic ". base64_encode("$API_USERNAME:$API_PASSWORD"),
    "cache-control: no-cache",
    "content-type: application/json",
  ),
));
$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}