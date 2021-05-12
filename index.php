<?php
//  phpinfo();
// exit();
require_once 'controller/AuthController.php';
require_once 'lib/JsonResponse.php';
require_once 'database/DbConnectionFactory.php';

function instanciaClasse( $nomeDaClasse )
{
   return new $nomeDaClasse();
}

function executaMetodo($objeto, $nomeDoMetodo, $parametros = []) {
    return $objeto->{$nomeDoMetodo}(...$parametros); //meuController->{login}($request)
}

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

$method = $_SERVER["REQUEST_METHOD"];
$uri = $_SERVER["REQUEST_URI"];
$count = 1;

$rotas = [];
$rotas["POST"]["/login"] = ['AuthController', "login"];
$rotas["POST"]["/register"] = ['AuthController', "register"];
$rotas["POST"]["/pudim"] = ['AuthController', "pudim"];

// $rotas = {
//     'POST' : {
//         'login' : {
//             0 : 'AuthController',
//             1 : "login"
//         },
//         'register' : {
//             0 : 'AuthController',
//             1 : "register"
//         }
//     }
// };


$request = json_decode(file_get_contents('php://input')); //raw body

$response = null;
if (isset($rotas[$method][$uri])) // se método que vier do server existir e se método e o recurso que vier do server existirem
{
    $meuController = instanciaClasse($rotas[$method][$uri][0]); //instanciei controller
    $response = executaMetodo($meuController, $rotas[$method][$uri][1], [$request]); //chamei funcao (passo o array global request que contem dados GET e POST)
} else {
    $response = new JsonResponse([],404); //not found
}

echo $response->process();
// A função acima vai exibir o que eu enviar no input com o name == username e com name == password



// Perguntas:
// Os dados que vêm em $_SERVER["REQUEST_METHOD"] e $_SERVER["REQUEST_URI"] são do usuário ou do servidor?

