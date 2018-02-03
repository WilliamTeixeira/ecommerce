<?php 

require_once("vendor/autoload.php");

use \Slim\Slim;
use \Hcode\Page;
use \Hcode\PageAdmin;

$app = new Slim();

$app->config('debug', true);

$app->get('/', function() {
    
	$Page = new Page();
	
	$Page->setTpl("index");

});

$app->get('/admin', function() {
    
	$Page = new PageAdmin();
	
	$Page->setTpl("index");

});

$app->run();

 ?>