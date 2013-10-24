<?php

namespace MyApp;

use Guzzle\Http\Client;
use \DomDocument;
use \XSLTProcessor;

class App {
	
	const urlHttp = 'http://admin.ez.local';
	const extHttp = '/api/ezp/v2';
	const userRest = 'admin';
	const passwordRest = 'publish';
	
	protected $_client;
	
	/**
	 * Initialize client
	 **/
	function __construct()
	{
		$this->_client = new Client( self::urlHttp );
	}
	
	/**
	 * Send a Request with Get Verbs
	 **/
	function sendGetRequest( $route, $headers )
	{
		$request = $this->_client->get( self::extHttp . $route, array(), array(
		    'headers' => $headers
		));
		$request->setAuth( self::userRest, self::passwordRest );
		$response = $request->send();
		
		echo $response->getStatusCode() . " " .$response->getHeader('Content-Type') . "<br />";
		
		return $response->json();
	}
	
	function sendPostRequest( $route, $headers, $xml )
	{
		$request = $this->_client->post( self::extHttp . $route, null, $xml, array( 'headers' => $headers ));
		$request->setAuth( self::userRest, self::passwordRest );
		$response = $request->send();
		
		echo $response->getStatusCode() . " " .$response->getHeader('Content-Type') . "<br />";
		
		return $response->json();
	}
	
	/**
	 * Display Fields only
	 * This code is totaly custom..
	 **/
	function displayFields( $data ) 
	{
		$versionInfo = $data['Version']['VersionInfo'];
		$fields = $data['Version']['Fields']['field'];
		
		if ( $fields )
		{
		    $textField = array( 'title', 'short_title' );
		    $xmlField = array( 'intro', 'body', 'caption' );

		    $firstLanguage = $fields[0]['languageCode'];

		    foreach ( $fields as $field ) 
		    {
		        if ( $field['languageCode'] != $firstLanguage )
		        {
		            echo "<hr />";
		            $firstLanguage = $field['languageCode'];
		        }
		
				switch( $field['fieldDefinitionIdentifier'] )
				{
					case 'title':
					case 'short_title':
						echo "<strong>" . $field['fieldDefinitionIdentifier'] . ": </strong>" . $field['fieldValue'] . "<br />";
						break;
					case 'intro':
					case 'body':
					case 'caption':
						$this->displayXmlToHtml( $field['fieldValue']['xml'], $field['fieldDefinitionIdentifier'] );
						break;
					case 'author':
						echo "<strong>" . $field['fieldDefinitionIdentifier'] . ": </strong>";
			            foreach ( $field['fieldValue'] as $author )
			            {
			               echo $author['name'] . " ";
			            }
			            echo "<br />";
						break;
					case 'image':
						echo "<strong>" . $field['fieldDefinitionIdentifier'] . ": </strong>";
			            echo "<img src='http://admin.ez.local" . $field['fieldValue']['path'] . "' /><br />";
						break;
					case 'url':
						echo "<strong>" . $field['fieldDefinitionIdentifier'] . ": </strong>";
			            var_dump( $field['fieldValue'] );
						break;
					default: 
						continue;
				}
		    }
		}
	}
	
	function displayViewFields( $data )
	{
		if ( isset( $data['View']['Result'] ) )
		{
		    foreach ( $data['View']['Result']['searchHits']['searchHit'] as $searchHit )
		    {
				echo "<hr/><hr/>";
				echo $searchHit['value']['Content']['Name'];
				$href = $searchHit['value']['Content']['_href'];

		        $path = str_replace( '/api/ezp/v2', '', $href ) . '/currentversion' ;
				// @TODO: remove
				$path = str_replace('/ez52test/web/index.php', '', $path );
				
				$headers = array(
			    	'Accept' => 'application/vnd.ez.api.ContentInfo+json',
		        );
		
				$client = new self();
				$json = $client->sendGetRequest( $path, $headers );
				$client->displayFields( $json );
			}
		}
	}
	
	function displayXmlToHtml( $xml, $identifier )
	{
        $xslDoc = new DOMDocument;
        $xslDoc->load( './Resources/stylesheets/eZXml2Html5.xsl' );
        $xsl = new XSLTProcessor();
        $xsl->importStyleSheet( $xslDoc );
        echo "<strong>" . $identifier. ": </strong>" . $xsl->transformToXML( simplexml_load_string( $xml ) ) . "<br />";
	}
}
