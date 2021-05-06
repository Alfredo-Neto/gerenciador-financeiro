<?php 

$method = $_SERVER["REQUEST_METHOD"];
$uri = $_SERVER["REQUEST_URI"];


$uri = str_replace("/", "", $uri, $count = 1);

$rotas = [];
$rotas["POST"]["login"] = "postLogin";
$rotas["POST"]["register"] = "postRegister";

echo '<pre>';
var_export($rotas);
echo '</pre>';

var_export($rotas[$method]);

echo($method);
echo($uri);

if (isset($rotas[$method]))
{
    if (isset($rotas[$method][$uri]))
    {
        $rotas[$method][$uri]();
    }
}

function postLogin (){
    echo($_POST["username"]);
    echo($_POST["password"]);
}

// A função acima vai exibir o que eu enviar no input com o name == username e com name == password
// utilizar objeto form do JavaScript (olhar MDN/W3Schools)