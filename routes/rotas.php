<?php
/*
 |------------------------------
 |  Lista de rotas
 |------------------------------
 */
$router->map('GET', '/', 'ControllerPrincipal#start', 'start');
$router->map('GET', '/[a:page]', function($p){
    if($p['page'] == 'login') {
        return ControllerPrincipal::login();
    } else if($p['page'] == 'logout' || $p['page'] == 'sair') {
        return ControllerPrincipal::logout();
    } else if($p['page'] == 'offlineclient') {
        return ControllerPrincipal::offlineDownloadFile($p);
    } else if($p['page'] == 'lQ37zclzOy4dCR8Gxn1JpzLkiRQSBZIowpcrondiario'){ // ROBOT
        include(__DIR__.'/../app/cronjob/wpcrondiario.php');
    }else {
        header('Location: /');
    }
});

// CHECA ATUALIZAÇÃO DO SISTEMA
$router->map('POST', '/checkversion', 'ControllerPrincipal#checkversion');


// LANDING PAGES via POST
$router->map('POST', '/login', 'ControllerPrincipal#loga');
$router->map('POST', '/inicio', 'ControllerPrincipal#inicio');
$router->map('POST', '/clientes', 'ControllerPrincipal#clientes');
$router->map('POST', '/clientes/novo', 'ControllerPrincipal#clientesNovo');
$router->map('POST', '/clientes/buscar', 'ControllerPrincipal#clientesBuscar');
$router->map('POST', '/clientes/database', 'ControllerPrincipal#clientesDatabase');
$router->map('POST', '/clientes/lixeira', 'ControllerPrincipal#clientesLixeira');
$router->map('POST', '/clientes/ver/[i:id]', 'ControllerPrincipal#clientesVer');

$router->map('POST', '/roteiros', 'ControllerPrincipal#roteiros');
$router->map('POST', '/roteiros/novo', 'ControllerPrincipal#roteirosNovo');
$router->map('POST', '/roteiros/simulacao', 'ControllerPrincipal#roteirosSimulacao');
$router->map('POST', '/roteiros/ver/[i:id]', 'ControllerPrincipal#roteirosVer');


$router->map('POST', '/parceiros', 'ControllerPrincipal#parceiros');
$router->map('POST', '/parceiros/novo', 'ControllerPrincipal#parceirosNovo');
$router->map('POST', '/parceiros/database', 'ControllerPrincipal#parceirosDatabase');
$router->map('POST', '/parceiros/ver/[i:id]', 'ControllerPrincipal#parceirosVer');
$router->map('POST', '/parceiros/ver/[i:id]/servico/[i:sid]', 'ControllerPrincipal#parceirosGetServico');
$router->map('POST', '/parceiros/ver/[i:id]/financeiro/[i:fid]', 'ControllerPrincipal#parceirosGetFinanceiro');

$router->map('POST', '/usuarios', 'ControllerPrincipal#usuarios');
$router->map('POST', '/usuarios/novo', 'ControllerPrincipal#usuariosNovo');
$router->map('POST', '/usuarios/buscar', 'ControllerPrincipal#usuariosBuscar');
$router->map('POST', '/usuarios/ver/[i:id]', 'ControllerPrincipal#usuariosVer');
$router->map('POST', '/usuarios/database', 'ControllerPrincipal#usuariosDatabase');

$router->map('POST', '/log', 'ControllerPrincipal#log');
$router->map('POST', '/log/[i:qtd]', 'ControllerPrincipal#log');
$router->map('POST', '/log/[i:qtd]/p/[i:pagina]', 'ControllerPrincipal#log');


$router->map('POST', '/offline', 'ControllerPrincipal#offline');





// POST DE FORMULARIOS
$prefix = '/form/';
$router->addRoutes(array(
    array('POST', $prefix.'clientes/novo', 'ControllerForm#clientesNovo'),
    array('POST', $prefix.'clientes/buscar', 'ControllerForm#clientesBuscar'),
    array('POST', $prefix.'clientes/database', 'ControllerForm#clientesLista'),
    array('POST', $prefix.'clientes/salvar', 'ControllerForm#clientesSalvar'),
    array('POST', $prefix.'clientes/apagar/[i:id]', 'ControllerForm#clientesApagar'),
    array('POST', $prefix.'clientes/restaurar/[i:id]', 'ControllerForm#clientesRestaurar'),
    array('POST', $prefix.'clientes/apagarlixeira/[i:id]', 'ControllerForm#clientesLixeiraApagar'),

    array('POST', $prefix.'usuarios/novo', 'ControllerForm#usuariosNovo'),
    array('POST', $prefix.'usuarios/buscar', 'ControllerForm#usuariosBuscar'),
    array('POST', $prefix.'usuarios/database', 'ControllerForm#usuariosLista'),
    array('POST', $prefix.'usuarios/salvar', 'ControllerForm#usuariosSalvar'),
    array('POST', $prefix.'usuarios/apagar/[i:id]', 'ControllerForm#usuariosApagar'),

    array('POST', $prefix.'parceiros/novo', 'ControllerForm#parceirosNovo'),
    array('POST', $prefix.'parceiros/database', 'ControllerForm#parceirosLista'),
    array('POST', $prefix.'parceiros/[i:id]/novofinanceiro', 'ControllerForm#parceirosNovoFinanceiro'),
    array('POST', $prefix.'parceiros/[i:id]/salvarfinanceiro/[i:fid]', 'ControllerForm#parceirosSalvarFinanceiro'),
    array('POST', $prefix.'parceiros/[i:id]/apagarfinanceiro/[i:fid]', 'ControllerForm#parceirosApagarFinanceiro'),
    array('POST', $prefix.'parceiros/[i:id]/novoservico', 'ControllerForm#parceirosNovoServico'),
    array('POST', $prefix.'parceiros/[i:id]/salvarservico/[i:sid]', 'ControllerForm#parceirosSalvarServico'),
    array('POST', $prefix.'parceiros/[i:id]/apagarservico/[i:sid]', 'ControllerForm#parceirosApagarServico'),
    array('POST', $prefix.'parceiros/[i:id]/addhistorico', 'ControllerForm#parceirosHistoricoAdd'),
    array('POST', $prefix.'parceiros/[i:id]/editarhistorico/[i:hid]', 'ControllerForm#parceirosHistoricoEditar'),
    array('POST', $prefix.'parceiros/[i:id]/listahistorico/[i:qtd]/[i:start]', 'ControllerForm#parceirosHistoricoLista'),
    array('POST', $prefix.'parceiros/[i:id]/apagarhistorico/[i:hid]', 'ControllerForm#parceirosApagarHistorico'),
    array('POST', $prefix.'parceiros/buscar', 'ControllerForm#parceirosBuscar'),


    array('POST', $prefix.'minhaconta', 'ControllerForm#minhaConta'),
    array('POST', $prefix.'minhaconta/salvar', 'ControllerForm#minhaContaSalvar'),
    array('POST', $prefix.'minhaconta/alterarsenha', 'ControllerForm#minhaContaAlterarSenha'),
    array('POST', $prefix.'minhaconta/avatar', 'ControllerForm#minhaContaFotoNovo'),
    array('POST', $prefix.'minhaconta/avatar/apagar', 'ControllerForm#minhaContaFotoApagar'),


    array('POST', $prefix.'roteiros/novo', 'ControllerForm#roteirosNovo'),
    array('POST', $prefix.'roteiros/[i:id]/addhistorico/[i:parcid]', 'ControllerForm#roteirosHistoricoNovo'),
    array('POST', $prefix.'roteiros/[i:id]/tarifa/editar', 'ControllerForm#roteirosTarifaEdita'),


    

));