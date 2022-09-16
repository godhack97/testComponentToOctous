<?php



function curlpost($orderData, $method, $url, $AccessToken, $Authorization)
{
  AddMessage2Log('curlpost(' . print_r($orderData, 1) . ', ' . print_r($method, 1) . ', ' . print_r($url, 1) . ', ' . print_r($AccessToken, 1) . ', ' . print_r($Authorization, 1));
  return '';

  $headers = [
    'Content-Type: application/json;charset=UTF-8',
    'Authorization: AccessToken ' . $AccessToken . '',
    'X-User-Authorization: Basic ' . $Authorization . '',
  ];
  if (is_array($orderData))
    $data = json_encode($orderData);

  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
  curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

  $resp = curl_exec($curl);
  curl_close($curl);

  $response = json_decode($resp, 1);

  if ($response['status'] == 'ERROR') {
    if (LANG_CHARSET == 'windows-1251') {
      $response['message'] = iconv(mb_detect_encoding($response['message']), 'windows-1251', $response['message']);
    }
    echo '<pre>';
    echo '<strong style="color:red">' . $response['message'] . '</strong></br>';
    echo '</pre>';
    die();
  }
  return $response;
}