<?php

include 'GoogleElevationRequest.php';

// example - get the elevation of a single point
$request = new GoogleElevationRequest();
$request->addCoordinate(47.12113, -88.56942);
$response = $request->fetchJSON();

$json = json_decode($response);
if ($json->status == 'OK') {
    $elevation = $json->results[0]->elevation;
}

// example - get the elevation of a set of points in SimpleXML
$request = new GoogleElevationRequest();
$request->addCoordinate(44.51916, -88.01983);
$request->addCoordinate(44.26193, -88.41538);
$request->addCoordinate(44.02471, -88.54261);
$response = $request->fetchXML();

$xml = simplexml_load_string($response);
if ($xml->status == 'OK') {
    $elevation_list = array();
    foreach ($xml->result as $result) {
        array_push($elevation_list, (float) $result->elevation);
    }
}