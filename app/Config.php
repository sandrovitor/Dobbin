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