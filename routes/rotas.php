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
$router->map('POST', '/clientes/ver/[i:id]/vendas', 'ControllerPrincipal#clientesVerVendas'); // JSON


$router->map('POST', '/coordenadores', 'ControllerPrincipal#coordenadores');
$router->map('POST', '/coordenadores/novo', 'ControllerPrincipal#coordenadorNovo');
$router->map('POST', '/coordenadores/buscar', 'ControllerPrincipal#coordenadorBuscar');
$router->map('POST', '/coordenadores/database', 'ControllerPrincipal#coordenadorDatabase');
$router->map('POST', '/coordenadores/lixeira', 'ControllerPrincipal#coordenadorLixeira');
$router->map('POST', '/coordenadores/ver/[i:id]', 'ControllerPrincipal#coordenadorVer');

$router->map('POST', '/roteiros', 'ControllerPrincipal#roteiros');
$router->map('POST', '/roteiros/novo', 'ControllerPrincipal#roteirosNovo');
$router->map('POST', '/roteiros/database', 'ControllerPrincipal#roteirosDatabase');
$router->map('POST', '/roteiros/simulacao', 'ControllerPrincipal#roteirosSimulacao');
$router->map('POST', '/roteiros/ver/[i:id]', 'ControllerPrincipal#roteirosVer');
$router->map('POST', '/roteiros/ver/[i:id]/clientes', 'ControllerPrincipal#roteirosVerClientes'); // JSON
$router->map('POST', '/roteiros/ver/[i:id]/estoque', 'ControllerPrincipal#roteirosVerEstoque'); // JSON
$router->map('POST', '/roteiros/load/[i:id]', 'ControllerPrincipal#roteirosLoad');
$router->map('POST', '/roteiros/editar/[i:id]', 'ControllerPrincipal#roteirosEditar');
$router->map('POST', '/roteiros/lixeira', 'ControllerPrincipal#roteirosLixeira');
$router->map('POST', '/roteiros/lixeira/ver/[i:id]', 'ControllerPrincipal#roteirosLixeiraVer');

// VENDAS
$router->map('POST', '/vendas', 'ControllerPrincipal#vendas');
$router->map('POST', '/vendas/novo', 'ControllerPrincipal#vendasNovo');
$router->map('POST', '/vendas/buscar', 'ControllerPrincipal#vendasBuscar');
$router->map('POST', '/vendas/database', 'ControllerPrincipal#vendasDatabase');
$router->map('POST', '/vendas/canceladas', 'ControllerPrincipal#vendasCanceladas');
$router->map('POST', '/vendas/estornadas', 'ControllerPrincipal#vendasEstornadas');
$router->map('POST', '/vendas/database/get/reservas', 'ControllerPrincipal#vendasDatabaseReservas'); // JSON
$router->map('POST', '/vendas/database/get/aguardando', 'ControllerPrincipal#vendasDatabaseAguardando'); // JSON
$router->map('POST', '/vendas/database/get/pagas', 'ControllerPrincipal#vendasDatabasePagas'); // JSON
$router->map('POST', '/vendas/database/get/canceladas', 'ControllerPrincipal#vendasDatabaseCanceladas'); // JSON
$router->map('POST', '/vendas/database/get/devolvidas', 'ControllerPrincipal#vendasDatabaseDevolvidas'); // JSON
$router->map('POST', '/vendas/database/get/estornadas', 'ControllerPrincipal#vendasDatabaseDevolvidas'); // JSON
$router->map('POST', '/vendas/database/load/venda/[i:vid]', 'ControllerJan#vendasLoad');


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

    array('POST', $prefix.'coordenadores/novo', 'ControllerForm#coordenadoresNovo'),
    array('POST', $prefix.'coordenadores/buscar', 'ControllerForm#coordenadoresBuscar'),
    array('POST', $prefix.'coordenadores/database', 'ControllerForm#coordenadoresLista'),
    array('POST', $prefix.'coordenadores/salvar', 'ControllerForm#coordenadoresSalvar'),
    array('POST', $prefix.'coordenadores/apagar/[i:id]', 'ControllerForm#coordenadoresApagar'),
    array('POST', $prefix.'coordenadores/restaurar/[i:id]', 'ControllerForm#coordenadoresRestaurar'),
    array('POST', $prefix.'coordenadores/apagarlixeira/[i:id]', 'ControllerForm#coordenadoresLixeiraApagar'), // IMPLANTANDO

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
    array('POST', $prefix.'roteiros/database', 'ControllerForm#roteirosLista'),
    array('POST', $prefix.'roteiros/buscar', 'ControllerForm#roteirosBuscar'),
    array('POST', $prefix.'roteiros/[i:id]/addhistorico/[i:parcid]', 'ControllerForm#roteirosHistoricoNovo'),
    array('POST', $prefix.'roteiros/[i:id]/tarifa/editar', 'ControllerForm#roteirosTarifaEdita'),
    array('POST', $prefix.'roteiros/[i:id]/addcoordenador/[i:coord]', 'ControllerForm#roteiroAddCoordenador'),
    array('POST', $prefix.'roteiros/[i:id]/delcoordenador/[i:coord]', 'ControllerForm#roteiroRemoveCoordenador'),
    array('POST', $prefix.'roteiros/salvar/[i:id]', 'ControllerForm#roteirosSalvar'),
    array('POST', $prefix.'roteiros/apagar/[i:id]', 'ControllerForm#roteirosApagar'),
    array('POST', $prefix.'roteiros/restaurar/[i:id]', 'ControllerForm#roteirosRestaurar'),
    array('POST', $prefix.'roteiros/apagarlixeira/[i:id]', 'ControllerForm#roteirosApagarLixeira'),
    array('POST', $prefix.'roteiros/[i:id]/copiar', 'ControllerForm#roteiroCriarCopia'),

    // VENDAS!
    array('POST', $prefix.'vendas/novo', 'ControllerForm#vendasNovo'),
    array('POST', $prefix.'vendas/buscar', 'ControllerForm#vendasBuscar'),
    array('POST', $prefix.'vendas/[i:id]/clientes/add/[i:cid]', 'ControllerForm#vendasAddCliente'),
    array('POST', $prefix.'vendas/[i:id]/clientes/del/[i:cid]', 'ControllerForm#vendasDelCliente'),
    array('POST', $prefix.'vendas/[i:id]/situacao/editar', 'ControllerForm#vendasAlterarSituacao'),

    

));