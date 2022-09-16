<?php
require 'vendor/autoload.php';

$accessKey = "AD91D85E18408AD5";
$userId = "BOJGUA.API";
$password = "Thursday,Nov";


$address = new \Ups\Entity\Address();
$address->setAttentionName('Test Test');
$address->setBuildingName('Test');
$address->setAddressLine1('Address Line 1');
$address->setAddressLine2('Address Line 2');
$address->setAddressLine3('Address Line 3');
$address->setStateProvinceCode('NY');
$address->setCity('New York');
$address->setCountryCode('US');
$address->setPostalCode('10000');

$xav = new \Ups\AddressValidation($accessKey, $userId, $password, true);
$xav->activateReturnObjectOnValidate(); //This is optional
try {
  $response = $xav->validate($address, $requestOption = \Ups\AddressValidation::REQUEST_OPTION_ADDRESS_VALIDATION, $maxSuggestion = 15);
} catch (Exception $e) {
  echo "<pre>";
  print_r($e);
}


////Testing: https://wwwcie.ups.com/rest/AV
////Production: https://onlinetools.ups.com/rest/AV
//
//// Configuration
//$accessLicenseNumber = "FD8CB91F5571AF7D";
//$userId = "BOJGUA.API";
//$password = "Thursday,Nov";
//
//$endpointurl = 'https://wwwcie.ups.com/rest/AV';
//$outputFileName = "XOLTResult.xml";
//
//echo 123;
//try {
//  // Create AccessRequest XMl
//  $accessRequestXML = new SimpleXMLElement ( "<AccessRequest></AccessRequest>" );
//  $accessRequestXML->addChild ( "AccessLicenseNumber", $accessLicenseNumber );
//  $accessRequestXML->addChild ( "UserId", $userId );
//  $accessRequestXML->addChild ( "Password", $password );
//
//  // Create AddressValidationRequest XMl
//  $avRequestXML = new SimpleXMLElement ( "<AddressValidationRequest ></AddressValidationRequest >" );
//  $request = $avRequestXML->addChild ( 'Request' );
//  $request->addChild ( "RequestAction", "AV" );
//
//  $address = $avRequestXML->addChild ( 'Address' );
//  $address->addChild ( "City", "ALPHARETTA" );
//  $address->addChild ( "PostalCode", "300053778" );
//  $requestXML = $accessRequestXML->asXML () . $avRequestXML->asXML ();
//
//  $form = array (
//    'http' => array (
//      'method' => 'POST',
//      'header' => 'Content-type: application/x-www-form-urlencoded',
//      'content' => "$requestXML"
//    )
//  );
//
//  $ch = curl_init();
//  curl_setopt($ch, CURLOPT_URL, $endpointurl);
//  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//  curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: application/x-www-form-urlencoded']);
//  curl_setopt($ch, CURLOPT_POST, 1);
//  curl_setopt($ch, CURLOPT_POSTFIELDS, array(
//    'content' =>  "$requestXML"
//  ));
//  $response = curl_exec($ch);
//
//  echo $response;
//
////  if ($response == false) {
////    throw new Exception ( "Bad data." );
////  } else {
////    // save request and response to file
////    $fw = fopen ( $outputFileName, 'w' );
////    fwrite ( $fw, "Request: \n" . $requestXML . "\n" );
////    fwrite ( $fw, "Response: \n" . $response . "\n" );
////    fclose ( $fw );
////
////    // get response status
////    $resp = new SimpleXMLElement ( $response );
////    echo $resp->Response->ResponseStatusDescription . "\n";
////  }
////
////  Header ( 'Content-type: text/xml' );
//
//} catch ( Exception $ex ) {
//  echo $ex;
//}

?>