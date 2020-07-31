<?php
namespace SGCTUR;
use SGCTUR\LOG;
use SGCTUR\Erro;

class Financeiro extends Master
{
    const DIR = __DIR__.'/../../storage/financeiro/';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Cria um novo balanço do mês.
     * @param \stdClass $balanco Um objeto gerado a partir de uma string JSON.
     */
    public function novo(\stdClass $balanco)
    {
        // Busca o mes e ano do balanço.
        $mes = $balanco->mes;
        $ano = $balanco->ano;
    }
}