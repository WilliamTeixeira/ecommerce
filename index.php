<?php 

require_once("vendor/autoload.php");

use \Slim\Slim;
use \Hcode\Page;

$app = new Slim();

$app->config('debug', true);

$app->get('/', function() {
    
	$Page = new Page();
	
	$Page->setTpl("index");

});

$app->run();

 ?>