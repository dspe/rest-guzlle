<?php

namespace MyApp;
require_once 'vendor/autoload.php';

// relever le point de départ
$timestart = microtime(true);

$client = new \MyApp\App();

if ( isset( $_GET['id'] ) && is_numeric( $_GET['id'] ) )
    $contentId = $_GET['id'];
else
    $contentId = 69;

// Get an article:
$path = '/content/objects/' . $contentId . '/currentversion';
$headers = array(
	'Accept' => 'application/vnd.ez.api.ContentInfo+json',
);

$json = $client->sendGetRequest( $path, $headers );
$client->displayFields( $json );

//Fin du code PHP
$timeend=microtime(true);
$time=$timeend-$timestart;
 
//Afficher le temps d'éxecution
$page_load_time = number_format($time, 3);
echo "Debut du script: ".date("H:i:s", $timestart);
echo "<br>Fin du script: ".date("H:i:s", $timeend);
echo "<br>Script execute en " . $page_load_time . " sec";
