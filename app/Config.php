<?php
/**
 * Arquivos de configuração do PHP, para plataforma.
 * Este arquivo deve ser carregado antes de qualquer outra saída.
 */

// Variáveis


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