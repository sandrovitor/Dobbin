<?php

/**
 * Cronjob Diário
 * 
 * - Exclui clientes que estão na lixeira há 72h ou mais.
 */

@session_start(); // Inicia sessão em todas as páginas/requisições.
Use SGCTUR\Robot;

// Carrega dados para o robô.
$hand = fopen(__DIR__.'/robot.json','r');
$dado = fread($hand, filesize(__DIR__.'/robot.json'));
fclose($hand);

$config = json_decode($dado);
//var_dump($config);
unset($dado, $hand);
// Verifica a última hora de execução. Menos de 23h interrompe.
$hoje = new DateTime();
$ue = new DateTime($config->executaBot->diario);
//var_dump($hoje->diff($ue));
if($hoje->diff($ue)->h < 23 && $hoje->diff($ue)->days == 0) {
    exit();
}
// Salva o horário dessa execução.
$config->executaBot->diario = $hoje->format('Y-m-d H:i:s');
$config->ultimaExec = $hoje->format('Y-m-d H:i:s');
$hand = fopen(__DIR__.'/robot.json','w+');
fwrite($hand, json_encode($config));
fclose($hand);
// #########################################################################
// Iniciando o CRON DIARIO

$robot = new Robot();
// Exclui clientes da lixeira
$robot->deletaClientesLixeira();

// Verifica limites do banco de dados diariamente.
$robot->checaLimiteBancoDados();

// Gera lista de clientes definitiva no roteiro.
$robot->geraListaClientesFixa();

// Envia e-mails de aniversário.
$robot->enviaEmailAniversario();

exit();