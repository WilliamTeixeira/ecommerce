<?php 
session_start();
require_once("vendor/autoload.php");

use \Slim\Slim;
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\User;

$app = new Slim();

$app->config('debug', true);

$app->get('/', function() {
    
	$page = new Page();
	
	$page->setTpl("index");

});

#region Acesso à área admin
$app->get('/admin', function() {
    
    User::verifyLogin();

	$page = new PageAdmin();
	
	$page->setTpl("index");

});
#endregion


#region Login de usuário
$app->get('/admin/login', function() {
    
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);
	
	$page->setTpl("login");

});


$app->post('/admin/login', function() {
    
    User::login($_POST["login"], $_POST["password"]);
	
    header("Location: /admin");
    exit;

});

$app->get('/admin/logout', function() {
    
	User::logout();

	header("Location: /admin/login");
	exit;
});
#endregion


#region menu usuários

//lista de usuários (menu usuários)
$app->get('/admin/users', function(){
	
	User::verifyLogin();
	
	$users = User::listAll();

	$page = new PageAdmin();
	
	$page->setTpl("users", array(
		"users"=>$users
	));
});

//create usuarios admin
$app->get('/admin/users/create', function(){
	
	User::verifyLogin();
	
	$page = new PageAdmin();
	
	$page->setTpl("users-create");
});


//delete deve vir antes por causa do framework
//rota deletar usuário
$app->get("/admin/users/:iduser/delete", function($iduser){
	
	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);

	$user->delete();

	header("Location: /admin/users");
	exit;
	
});

//botão alterar usuário
$app->get("/admin/users/:iduser", function($iduser){
	
	User::verifyLogin();
	
	$user = new User();

	$user->get((int)$iduser);

	$page = new PageAdmin();
	
	$page->setTpl("users-update", array(
		"user"=>$user->getValues()
	));
});

//botão cadastrar usuário
$app->post('/admin/users/create', function(){
	
	User::verifyLogin();

	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;
	$user = new User();

	$user->setData($_POST);

	$user->save();

	header("Location: /admin/users");
	exit;
	
});

//rota altera dados usuário
$app->post("/admin/users/:iduser", function($iduser){
	
	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;
	
	$user->get((int)$iduser);
	
	$user->setData($_POST);

	$user->update();

	header("Location: /admin/users");
	exit;
	
});
#endregion

#region recuperação de email

//rota tela recuperação de email
$app->get("/admin/forgot", function(){
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);
	
	$page->setTpl("forgot");
});

//rota email de recuperação 

$app->post("/admin/forgot", function(){
	
	$user = User::getForgot($_POST["email"]);

	header("Location: /admin/forgot/sent");
	exit;
});

$app->get("/admin/forgot/sent", function(){
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);
	
	$page->setTpl("forgot-sent");
});


$app->get("/admin/forgot/reset", function(){
	
	$user = User::validForgotDecrypt($_GET["code"]);

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);
	
	$page->setTpl("forgot-reset", array(
		"name"=>$user["desperson"],
		"code"=>$_GET["code"]
	));
});

$app->post("/admin/forgot/reset", function(){
	
	$forgot = User::validForgotDecrypt($_POST["code"]);

	User::setForgotUsed($forgot["idrecovery"]);

	$user = new User();

	$user->get((int)$forgot["iduser"]);

	$password = password_hash($_POST["password"], PASSWORD_DEFAULT, [
		"cost"=>12
	]);

	$user->setPassword($password);

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);
	
	$page->setTpl("forgot-reset-success");
});




#region execução de tudo
$app->run();
#endregion
 ?>