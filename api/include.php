<?php

require_once 'sql.php';
require_once 'json.php';

$ROOT 	= "http://letakol.free.fr/fiboot/";

$input  = file_get_contents("php://input");
$post   = json_decode($input);

$sql 	= new Sql;

$data	= $post->data;
$json   = array(
	"request"	=> $post->action,
	"status" 	=> false,
	"message" 	=> "Une erreur inconnue est survenue"
);

?>
