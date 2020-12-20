<?php
/**
 * Arquivos de configuração do PHP, para plataforma.
 * Este arquivo deve ser carregado antes de qualquer outra saída.
 */

// CONSTANTES
/**
 * Máximo de parcelas das compras.
 */
define('DOBBIN_MAX_PARCELAS', 12);
/**
 * Dia máximo do vencimento.
 */
define('DOBBIN_MAX_VENCIMENTO_DIAS', 30);
/**
 * Domínio ou endereço para clientes externos acessarem comprovantes.
 */
define('DOBBIN_LINK_EXTERNO', 'tonaestradaviagens.com.br');
/**
 * Frase fixa usada em alguns cálculos de hash para links externos.
 */
define('DOBBIN_FRASE_FIXA', 'DOBBIN');


// Definições do sistema.
error_reporting( E_ALL & (~E_NOTICE | ~E_USER_NOTICE) );
ini_set( 'error_log', 'php_erros.log' );
ini_set( 'ignore_repeated_source', true );    
ini_set( 'ignore_repeated_errors', true );
ini_set( 'display_errors', TRUE);
ini_set( 'log_errors', true );


// Definindo UTF-8 como padrão
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

// LIMITES
define('DOBBIN_LIM_QTD_MES_BALANCO_TOTAL', 6); // Inicial 6 meses.
define('DOBBIN_LIM_QTD_MES_BALANCO_TOTAL_MAX', 25);
define('DOBBIN_LIM_BALANCOS_EXPORTAR', true);


define('DOBBIN_LIM_CLIENTES', 2000);            // Inicial: 2 mil
define('DOBBIN_LIM_PARCEIROS', 100);            // Inicial: 100
define('DOBBIN_LIM_ROTEIROS', 200);             // Inicial: 200
define('DOBBIN_LIM_VENDAS', 5000);            // Inicial: 5 mil
define('DOBBIN_LIM_USUARIOS', 10);             // Inicial: 10

define('DOBBIN_LIM_CLIENTES_MAX', 300000);      // 300 MIL
define('DOBBIN_LIM_PARCEIROS_MAX', 10000);      // 10 MIL
define('DOBBIN_LIM_ROTEIROS_MAX', 50000);       // 50 MIL
define('DOBBIN_LIM_VENDAS_MAX', 500000);        // 500 MIL
define('DOBBIN_LIM_USUARIOS_MAX', 250);         // 250