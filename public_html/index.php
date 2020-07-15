<?php
/**
 * SISTEMA DE GESTÃO DE CLIENTES PARA AGÊNCIAS DE TURISMO - SGCTur
 * 
 * Desenvolvido por: Sandro Vitor
 * Contato: sandro_vitor@hotmail.com
 * 
 * Arquivo principal do sistema. Carrega arquivos do autoloader, rotas e lógica do roteador.
 */ 

require_once('../app/Config.php');
require_once('../vendor/autoload.php');
require_once('../app/Controllers/ControllerPrincipal.php');
require_once('../app/Controllers/ControllerForm.php');
require_once('../app/Controllers/ControllerJan.php');
require_once('../app/Controllers/ConGerador.php');
@session_start(); // Inicia sessão em todas as páginas/requisições.

date_default_timezone_set('America/Bahia');

// Inicia o roteador
$router = new \AltoRouter();
/*
    Ajuda: http://altorouter.com/usage/mapping-routes.html

 */
/*
 |------------------------------
 |  Lista de rotas
 |------------------------------
 */


require_once('../routes/rotas.php');

/*
 |------------------------------
 |  FIM da Lista de rotas
 |------------------------------
 */

$match = $router->match();

// Verifica se a URI bate com alguma rota salva
if(is_array($match) && is_callable($match['target'])) {
    // Rota encontrada, sem CONTROLLER

    //var_dump($match['params']);
    $writePage = call_user_func_array($match['target'], array($match['params']));
    //var_dump($writePage);
    echo $writePage;
} else if(is_array($match) && strrpos($match['target'],'#') > 0) {
    // Rota encontrada, com CONTROLLER

    $x = explode('#', $match['target']);
    $writePage = call_user_func_array($x, array($match['params']));
    echo $writePage;
} else {
    // Nenhuma rota encontrada

    // Exibe erro 404
    header( $_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
}