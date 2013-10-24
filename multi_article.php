<?php

namespace MyApp;
require_once 'vendor/autoload.php';

// relever le point de départ
$timestart = microtime(true);

$client = new \MyApp\App();

$path = '/content/views';
$headers = array(
	'Accept' => 'application/vnd.ez.api.View+json',
    'Content-Type' => 'application/vnd.ez.api.ViewInput+xml'
);
$xml = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<ViewInput>
<identifier>multiArticle</identifier>
<Query>
<Criteria>
    <ContentIdCriterion>70,69,67</ContentIdCriterion>
    <SectionIdentifierCriterion>standard</SectionIdentifierCriterion>
</Criteria>
<SortClauses>
    <SortClause>
        <SortField>CREATED</SortField>
    </SortClause>
</SortClauses>
</Query>
</ViewInput>
EOF;

$json = $client->sendPostRequest( $path, $headers, $xml );
$client->displayViewFields( $json );

//Fin du code PHP
$timeend=microtime(true);
$time=$timeend-$timestart;
 
//Afficher le temps d'éxecution
$page_load_time = number_format($time, 3);
echo "Debut du script: ".date("H:i:s", $timestart);
echo "<br>Fin du script: ".date("H:i:s", $timeend);
echo "<br>Script execute en " . $page_load_time . " sec";
