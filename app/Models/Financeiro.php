<?php
namespace SGCTUR;

use DateTime;
use PDOException;
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
     * @return bool
     */
    public function novo(\stdClass $balanco)
    {
        // Busca o mes e ano do balanço.
        $mes = $balanco->mes;
        $ano = $balanco->ano;

        // Valores.
        $despesa = $balanco->saida;
        $receita = $balanco->entrada;
        $saldo = $balanco->saldo;

        do {
            $nomeArq = $this->geraChave(10) . ".json";

            // Verifica se o nome do arquivo já está sendo usado.
            $abc = $this->pdo->query("SELECT id FROM fin_lista_arquivos WHERE nomearq = \"$nomeArq\"");
            $res = $abc->fetchAll();
        } while(count($res) > 0);

        // Persiste folha no banco e no sistema de arquivos.
        $usu_id = $_SESSION['auth']['id'];
        

        // Escreve arquivo.
        $hand = fopen(__DIR__. "/../../storage/financeiro/" . $nomeArq, "w");
        fwrite($hand,json_encode($balanco));
        fclose($hand);

        $hash = md5_file(__DIR__. "/../../storage/financeiro/" . $nomeArq);

        $sql = "INSERT INTO fin_lista_arquivos
                (id, nomearq, ano, mes, despesas, receitas, saldo, pdf, hash_json, criado_por, criado_data)
                VALUES
                (null, '{$nomeArq}', '{$ano}', '{$mes}', $despesa, $receita, $saldo, '', '{$hash}', $usu_id, NOW())";
        


        try {
            $this->pdo->beginTransaction();

            $abc = $this->pdo->query($sql);

            $this->pdo->commit();
            return true;
        } catch(\PDOException $e) {
            @\error_log($e->getMessage(), 1, $this->system->desenvolvedor[0]);
            \error_log($e->getMessage(), 0);
            $this->pdo->rollBack();
            return false;
        }
    }

    /**
     * Verifica se o balanço já existe.
     * 
     * @param int $ano
     * @param int $mes
     * 
     * @return false|string Se existir, retorna o nome do arquivo.
     */
    public function checkExiste(int $ano, int $mes)
    {
        if($mes < 10) {
            $mes = '0'.$mes;
        }

        $sql = "SELECT FLA.*
                FROM fin_lista_arquivos FLA
                WHERE fla.ano = '{$ano}' AND fla.mes = '{$mes}'";
        
        $abc = $this->pdo->query($sql);
        if($abc->rowCount() == 0) {
            return false;
        } else {
            $reg = $abc->fetch(\PDO::FETCH_OBJ);
            return substr($reg->nomearq, 0, strrpos($reg->nomearq, '.'));
        }
    }

    /**
     * Retorna uma lista com todas as folhas de todos os anos.
     * 
     * @return array
     */
    public function getAllFolhas()
    {
        $sql = "SELECT  FLA.id, FLA.nomearq, FLA.ano, FLA.mes, FLA.criado_data,
                        FLA.fechada,
                (SELECT CONCAT(login.nome, ' ', login.sobrenome)
                FROM login WHERE login.id = FLA.criado_por) as criado_por_nome,

                IF (FLA.fechada = 1,
                    (SELECT CONCAT(login.nome, ' ', login.sobrenome) 
                    FROM login WHERE login.id = FLA.fechada_por),
                null) as fechada_por_nome,

                IF (FLA.alterado_por IS NOT NULL,
                    (SELECT CONCAT(login.nome, ' ', login.sobrenome) 
                    FROM login WHERE login.id = FLA.alterado_por),
                null) as alterado_por_nome
        
                FROM fin_lista_arquivos FLA
                LEFT JOIN login ON FLA.criado_por = login.id
                WHERE 1
                ORDER BY FLA.ano DESC, FLA.mes DESC, FLA.criado_data DESC";
        
        $abc = $this->pdo->query($sql);
        $ret = $abc->fetchAll(\PDO::FETCH_OBJ);
        foreach($ret as $key => $r) {
            $data = new \DateTime($r->criado_data);
            $ret[$key]->criado_data_str = $data->format("d/m/Y H:i");
        }

        return $ret;
    }

    /**
     * Retorna dados de uma folha.
     * 
     * @param int $ano
     * @param int $mes
     * @param string $nomeArq
     * 
     * @return stdClass|false
     */
    public function getFolha(int $ano, int $mes, string $nomeArq)
    {
        $sql = "SELECT FLA.*,
                DATE_FORMAT(FLA.criado_data, '%d/%m/%Y %H:%i') as criado_data,

                IF(FLA.alterado_data IS NOT NULL,
                    DATE_FORMAT(FLA.criado_data, '%d/%m/%Y %H:%i'),
                    alterado_data) as alterado_data,
                IF(FLA.fechada_data IS NOT NULL,
                    DATE_FORMAT(FLA.fechada_data, '%d/%m/%Y %H:%i'),
                    fechada_data) as fechada_data,

                (SELECT CONCAT(login.nome, ' ', login.sobrenome)
                FROM login WHERE login.id = FLA.criado_por) as criado_por_nome,

                IF (FLA.fechada = 1,
                    (SELECT CONCAT(login.nome, ' ', login.sobrenome) 
                    FROM login WHERE login.id = FLA.fechada_por),
                null) as fechada_por_nome,

                IF (FLA.alterado_por IS NOT NULL,
                    (SELECT CONCAT(login.nome, ' ', login.sobrenome) 
                    FROM login WHERE login.id = FLA.alterado_por),
                null) as alterado_por_nome

                FROM fin_lista_arquivos FLA
                LEFT JOIN login ON FLA.criado_por = login.id
                WHERE fla.ano = '{$ano}' AND fla.mes LIKE '%{$mes}' AND nomearq LIKE '{$nomeArq}%'";

        $abc = $this->pdo->query($sql);
        if($abc->rowCount() > 0) {
            $ret = $abc->fetch(\PDO::FETCH_OBJ);
            return $ret;
        } else {
            return false;
        }
    }

    /**
     * Retorna dados de uma folha pela ID.
     * 
     * @param int $id
     * 
     * @return stdClass|false
     */
    public function getFolhaById(int $id)
    {
        $sql = "SELECT fla.*, CONCAT(login.nome, ' ', login.sobrenome) as nome
                FROM fin_lista_arquivos FLA
                LEFT JOIN login ON fla.criado_por = login.id
                WHERE fla.id = {$id}";

        $abc = $this->pdo->query($sql);
        if($abc->rowCount() > 0) {
            $ret = $abc->fetch(\PDO::FETCH_OBJ);
            return $ret;
        } else {
            return false;
        }
    }

    /**
     * Salva alterações em uma folha.
     * 
     * @param int $id
     * @param \stdClass $folha
     * 
     * @return true|string
     */
    public function salvaFolha(int $id, \stdClass $folha) {
        // Busca no banco a folha correspondente.
        $folhaOld = $this->getFolhaById($id);

        if($folhaOld === false) {
            return "Folha não encontrada. Ela pode ter sido apagada.";
        }

        // Verifica se a folha está fechada.
        if($folhaOld->fechada == true) {
            return "Esta folha está fechada, não permitindo alteração.";
        }

        $ano = $folha->ano;
        $mes = $folha->mes;
        $despesa = $folha->saida;
        $receita = $folha->entrada;
        $saldo = $folha->saldo;
        $usu_id = $_SESSION['auth']['id'];

        // Salva as alterações da folha.
        $sql = "UPDATE fin_lista_arquivos
                SET ano = \"$ano\", mes = \"$mes\", despesas = $despesa, receitas = $receita, saldo = $saldo,
                    alterado_data = NOW(), alterado_por = $usu_id
                WHERE id = $id";
        
        try {
            $this->pdo->beginTransaction();

            $abc = $this->pdo->query($sql);

            // Escreve a folha no arquivo.
            $hand = fopen(__DIR__. "/../../storage/financeiro/" . $folhaOld->nomearq, "w");
            fwrite($hand,json_encode($folha));
            fclose($hand);

            // Calcula o hash do arquivo.
            $hash = md5_file(__DIR__. "/../../storage/financeiro/" . $folhaOld->nomearq);

            $sql = "UPDATE fin_lista_arquivos
                    SET hash_json = '$hash'
                    WHERE id = $id";

                    
            $abc = $this->pdo->query($sql);

            $this->pdo->commit();

            $log = new LOG();
            $log->novo("Atualizou o balanço (ID: $id) de ".$folha->mes."/".$folha->ano.".", $usu_id, 3);
            return true;
        } catch(\PDOException $e) {
            \error_log($e->getMessage(), 1, $this->system->desenvolvedor[0]);
            \error_log($e->getMessage(), 0);
            $this->pdo->rollBack();
            return "Não foi possível salvar os dados no banco.";
        }
    }

    /**
     * Fecha o balanço\folha.
     * 
     * @param int $id
     * 
     * @return bool
     */
    public function fecharFolha(int $id) {
        $usu_id = $_SESSION['auth']['id'];

        $abc = $this->pdo->query("SELECT fechada FROM fin_lista_arquivos WHERE id = $id");
        $reg = $abc->fetch(\PDO::FETCH_OBJ);
        if($reg->fechada == '1') {
            return true;
        }

        $sql = "UPDATE fin_lista_arquivos
                SET fechada_data = NOW(), fechada = '1', fechada_por = $usu_id
                WHERE id = $id";


        try {
            $this->pdo->beginTransaction();

            $abc = $this->pdo->query($sql);

            $this->pdo->commit();
            $log = new LOG();
            $log->novo("Fechou o balanço (ID: $id) de ".$folha->mes."/".$folha->ano.
            ". Não é mais possivel fazer alterações neste balanço.", $usu_id, 3);
            return true;
        } catch(\PDOException $e) {
            \error_log($e->getMessage(), 1, $this->system->desenvolvedor[0]);
            \error_log($e->getMessage(), 0);
            $this->pdo->rollBack();
            return false;
        }
    }

    /**
     * Apaga o arquivo com base no nome do arquivo JSON.
     * 
     * @param string $arquivo Nome do arquivo.
     * @return bool
     */
    public function apagarByArquivo(string $arquivo) {
        if($arquivo == '') {
            return false;
        }

        if(strrpos($arquivo, '.') === false) {
            // O ponto (.) não foi encontrado.
            $arquivo .= ".json";
        }

        $sql = "SELECT * FROM fin_lista_arquivos WHERE nomearq = '{$arquivo}'";
        $abc = $this->pdo->query($sql);

        if($abc->rowCount() == 0) {
            // Registro não encontrado.
            return true;
        } else {
            $reg = $abc->fetch(\PDO::FETCH_OBJ);


            // Exclui o registro do banco e remove o arquivo.
            try {
                $sql = "DELETE FROM fin_lista_arquivos WHERE nomearq = '{$arquivo}'";
                $abc= $this->pdo->query($sql);

                unlink(__DIR__. "/../../storage/financeiro/" . $arquivo);

                $log = new Log();
                $log->novo('Excluiu o balanço do mês '.$reg->mes.'/'.$reg->ano.'.', $_SESSION['auth']['id'], 4);
                return true;
            } catch(\PDOException $e) {
                @\error_log($e->getMessage(), 1, $this->system->desenvolvedor[0]);
                \error_log($e->getMessage(), 0);
                $this->pdo->rollBack();
                return false;
            }
        
        }
    }

    /**
     * Gera um relatório financeiro no período informado.
     * 
     * @param array     $inicio Ex.: ['2020', '01']
     * @param array     $fim    Ex.: ['2020', '04']
     * 
     * @return array
     */
    public function relatorio(array $inicio, array $fim) {
        // Valida o inicio e o fim do periodo.
        $data_ini = new \DateTime();    $data_ini->setDate((int)$inicio[0], (int)$inicio[1], 1);
        $data_variavel = new \DateTime();    $data_variavel->setDate((int)$inicio[0], (int)$inicio[1], 1);
        $data_fim = new \DateTime();    $data_fim->setDate((int)$fim[0], (int)$fim[1], 28);

        if($data_ini->diff($data_fim)->invert == 1) {
            $retorno = 'Período inválido. O FIM deve ser maior que o INICIO.';
            return $retorno;
        }

        // verifica o limite de meses por balanço.
        if($data_ini->diff($data_fim)->m + 1 > DOBBIN_LIM_QTD_MES_BALANCO_TOTAL) {
            $retorno = 'A quantidade máxima de meses foi excedida. O limite é '.DOBBIN_LIM_QTD_MES_BALANCO_TOTAL.' meses.';
            return $retorno;
        }



        // Retorna todos os balanços do período.
        $sql = "SELECT FLA.*,
                    (SELECT count(*)
                    FROM `vendas`
                    WHERE DATE_FORMAT(`data_venda`, '%Y-%m') = CONCAT(FLA.ano,'-', FLA.mes)) as totalVendas,
                    (SELECT count(*)
                    FROM `vendas`
                    WHERE DATE_FORMAT(`data_reserva`, '%Y-%m') = CONCAT(FLA.ano,'-', FLA.mes)) as totalReservas
                FROM fin_lista_arquivos FLA
                WHERE FLA.ano >= '".$inicio[0]."' AND FLA.ano <= '".$fim[0]."' AND FLA.mes >= '".$inicio[1]."' AND FLA.mes <= '".$fim[1]."'
                ORDER BY FLA.ano ASC, FLA.mes ASC";
        
        $abc = $this->pdo->query($sql);
        if($abc->rowCount() == 0) {
            return (object)['erro' => "Não há balanços neste período informado."];
        } else {
            $balancos = $abc->fetchAll(\PDO::FETCH_OBJ);
        }

        // Ao invés de depender dos balanços lançados no sistema, ele gera dados com base no período informado
        // e os balanços que não existirem, retornarão vazios.
        

        if($data_variavel->format('Y-m') == $data_fim->format('Y-m')) {
            // Relatorio de um único mês.

        } else {
            // Relatório de vários meses.
            $relatorio = [];
            $relatorio['saldoTotal'] = 0;
            $relatorio['receitaTotal'] = 0;
            $relatorio['despesaTotal'] = 0;
            $relatorio['vendasTotal'] = 0;
            $relatorio['reservasTotal'] = 0;
            $relatorio['qtdMes'] = count($balancos);
            $relatorio['meses'] = [];


            // Se mudar para 1, o periodo variavel ultrapassou o periodo final. Interrompe.
            while($data_variavel->diff($data_fim)->invert == 0) { 

                // Verifica se há um balanço para o mês.
                $balMes = null;

                foreach($balancos as $b) {
                    if($b->ano == $data_variavel->format('Y') && $b->mes == $data_variavel->format('m')) {
                        $balMes = $b;  // Adiciona o balanço do mês à variável.
                        break;
                    }
                }

                if($balMes == null) {
                    // Não há balanço para o mês.
                    // Só retorna as informações existentes.

                    $despesaCategorias = ['FIXA' => 0, 'OCASIONAL' => 0, 'NOVO_ITINERÁRIO' => 0, 'PAGAMENTOS' => 0];

                    
                    $relatorio['vendasTotal'] += $b->totalVendas;
                    $relatorio['reservasTotal'] += $b->totalReservas;

                    $relatorio['meses'][] = [
                        'temBalanco' => false, // Variável de controle, para o JS.
                        'mes' => $data_variavel->format('m/Y'),
                        'despesas' => '-',
                        'receitas' => '-',
                        'saldo' => '-',
                        'vendas' => $b->totalVendas,
                        'reservas' => $b->totalReservas,
                        'link' => '-',
                        'fechada' => 0,
                        'despesasCat' => $despesaCategorias
                    ];
                } else {
                    // Balanço para o mês encontrado.
                    $b = $balMes;

                    $relatorio['saldoTotal'] += $b->saldo;
                    $relatorio['receitaTotal'] += $b->receitas;
                    $relatorio['despesaTotal'] += $b->despesas;
                    $relatorio['vendasTotal'] += $b->totalVendas;
                    $relatorio['reservasTotal'] += $b->totalReservas;
        
                    // Abre o arquivo da folha e busca dados das despesas.
                    if(file_exists(__DIR__ . '/../../storage/financeiro/' . $b->nomearq)) {
                        $hand = fopen(__DIR__ . '/../../storage/financeiro/' . $b->nomearq,"r");
                        $content = fread($hand, filesize(__DIR__ . '/../../storage/financeiro/' . $b->nomearq));
                        fclose($hand);
            
                        $folhaContent = json_decode($content);
                    } else {
                        $folhaContent = (object)[];
                    }
        
                    $despesaCategorias = ['FIXA' => 0, 'OCASIONAL' => 0, 'NOVO_ITINERÁRIO' => 0, 'PAGAMENTOS' => 0];
        
                    // Processa alguns dados das despesas.
                    foreach($folhaContent->folha as $f) {
                        if($f->tipo == "SAIDA") {
                            $cat = str_replace(' ', '_', $f->categoria);
                            if($despesaCategorias[$cat] == null) {
                                $despesaCategorias[$cat] = (int)str_replace(',', '', $f->valor);
                            } else {
                                $despesaCategorias[$cat] += (int)str_replace(',', '', $f->valor);
                            }
                        }
                    }
        
                    $relatorio['meses'][] = [
                        'temBalanco' => true, // Variável de controle, para o JS.
                        'mes' => $b->mes.'/'.$b->ano,
                        'despesas' => $b->despesas,
                        'receitas' => $b->receitas,
                        'saldo' => $b->saldo,
                        'vendas' => $b->totalVendas,
                        'reservas' => $b->totalReservas,
                        'link' => '#financeiro/ver/'.$b->ano.'/'.$b->mes.'/'.substr($b->nomearq, 0, strrpos($b->nomearq, '.')),
                        'fechada' => $b->fechada,
                        'despesasCat' => $despesaCategorias
                    ];
                }


                // Incrementa a data variavel.
                $data_variavel->add(new \DateInterval('P1M'));
                $data_variavel->setTime(0,0,0,0);
            }
        }
        
        $log = new LOG();
        $log->novo("Gerou um relatório financeiro do período ".$data_ini->format('m/Y')." a ".$data_fim->format('m/Y').".", $_SESSION['auth']['id'], 1);

        

        $relatorio['mediaSaldoMes'] = $relatorio['saldoTotal'] / $relatorio['qtdMes'];
        $relatorio['mediaReceitaMes'] = $relatorio['receitaTotal'] / $relatorio['qtdMes'];
        $relatorio['mediaDespesaMes'] = $relatorio['despesaTotal'] / $relatorio['qtdMes'];
        $relatorio['mediaVendaMes'] = $relatorio['vendasTotal'] / count($relatorio['meses']);
        $relatorio['mediaReservaMes'] = $relatorio['reservasTotal'] / count($relatorio['meses']);


        return $relatorio;
    }

    /**
     * Busca no banco de dados uma lista com receitas/recebidos no período.
     * 
     * @param   \DateTime   $periodo Mês e ano da busca.
     * 
     * @return array
     */
    public function getListaReceitas(\DateTime $periodo) {
        $mesAno = $periodo->format('Y-m');

        $sql = "
        SELECT v.*,
               c.nome as cliente_nome,
               CONCAT(r.nome, ' (', DATE_FORMAT(r.data_ini, '%d/%m/%Y'), ' a ', DATE_FORMAT(r.data_ini, '%d/%m/%Y'), ')') as roteiro_nome
         FROM `vendas` v
         LEFT JOIN clientes c
           ON v.cliente_id = c.id
         LEFT JOIN roteiros r
           ON v.roteiro_id = r.id
        WHERE v.detalhes_pagamento
         LIKE '%\"data\":\"$mesAno%'
          AND v.valor_devolvido IS NULL";
        $abc = $this->pdo->query($sql);
        if($abc->rowCount() > 0) {
            $reg = $abc->fetchAll(\PDO::FETCH_OBJ);
            // Varre as receitas. Remove os itens com valor zerado.
            $retorno = [];
            foreach($reg as $key => $r) {
                $dp = json_decode($r->detalhes_pagamento);
                $DPok = []; // Armazena os detalhes de pagamento válidos (diferentes de zero).

                foreach($dp as $d) {
                    // verifica se a data da informação é do período e se o valor não está zerado.
                    $dataDP = new DateTime($d->data); // Data do pagamento.

                    if($dataDP->format('Y-m') == $mesAno && $d->valor > 0) {
                        // Pagamento válido.
                        $DPok[] = $d;
                    }
                }

                // Verifica se há conteúdo na variável.
                if(count($DPok) > 0) {
                    // Envia essa receita para o array de retorno.
                    $total = 0;
                    foreach($DPok as $d) {$total += $d->valor;}


                    $retorno[] = [
                        'cliente_nome'      => $r->cliente_nome,
                        'cliente_id'        => $r->cliente_id,
                        'detalhes_pagamento'=> $r->detalhes_pagamento,
                        'id'                => $r->id,
                        'roteiro_nome'      => $r->roteiro_nome,
                        'vencimento'        => $r->vencimento,
                        'valor'             => $total,
                        'forma_pagamento'   => $r->forma_pagamento,
                        'valor_total'       => $this->converteCentavoParaReal($r->valor_total),
                        'valor_recebido_mes'=> $this->converteCentavoParaReal($total),
                        'data_venda'        => (new \DateTime($r->data_venda))->format('d/m/Y H:i:s'),
                    ];
                }
            }

            return $retorno;
        } else {
            return [];
        }

    }

    /**
     * Busca no balanço anterior (se existir) uma lista com despesas/pagamentos.
     * 
     * @param   \DateTime   $periodo Mês e ano da busca.
     * 
     * @return array|string
     */
    public function getListaDespesas(\DateTime $periodo) {
        $periodo->sub(new \DateInterval('P1M'));

        $sql = "SELECT * FROM fin_lista_arquivos FLA
                WHERE FLA.ano = '".$periodo->format('Y')."' AND FLA.mes = '".$periodo->format('m')."'";

        $abc = $this->pdo->query($sql);
        if($abc->rowCount() == 0) {
            return 'Não foi encontrado balanço para o mês '.$periodo->format('m/Y');
        } else {
            $b = $abc->fetch(\PDO::FETCH_OBJ);

            // Abre o arquivo da folha e busca dados das despesas.
            if(file_exists(__DIR__ . '/../../storage/financeiro/' . $b->nomearq)) {
                $hand = fopen(__DIR__ . '/../../storage/financeiro/' . $b->nomearq,"r");
                $content = fread($hand, filesize(__DIR__ . '/../../storage/financeiro/' . $b->nomearq));
                fclose($hand);
    
                $folhaContent = json_decode($content);
                $despesas = [];
                foreach($folhaContent->folha as $f) {
                    if($f->tipo == "SAIDA") {
                        $despesas[] = $f;
                    }
                }

                return $despesas;
            } else {
                return 'O arquivo do balanço para o mês '.$periodo->format('m/Y').' não foi localizado no servidor.';
            }
        }
    }

    /**
     * Retorna dados de uma determinada venda .
     * 
     * @param   int         $id ID da venda.
     * @param   \DateTime   $periodo Mês e ano da busca.
     * 
     * @return array
     */
    public function getDadosVenda(int $id, \DateTime $periodo) {
        $mesAno = $periodo->format('Y-m');

        $sql = "
        SELECT v.*,
               c.nome as cliente_nome,
               CONCAT(r.nome, ' (', DATE_FORMAT(r.data_ini, '%d/%m/%Y'), ' a ', DATE_FORMAT(r.data_ini, '%d/%m/%Y'), ')') as roteiro_nome
         FROM `vendas` v
         LEFT JOIN clientes c
           ON v.cliente_id = c.id
         LEFT JOIN roteiros r
           ON v.roteiro_id = r.id
        WHERE v.id = $id";
        $abc = $this->pdo->query($sql);
        if($abc->rowCount() > 0) {
            $reg = $abc->fetchAll(\PDO::FETCH_OBJ);
            // Varre as receitas. Remove os itens com valor zerado.
            $retorno = [];
            foreach($reg as $key => $r) {
                $dp = json_decode($r->detalhes_pagamento);
                $DPok = []; // Armazena os detalhes de pagamento válidos (diferentes de zero).

                if(!empty($dp)) {
                    foreach($dp as $d) {
                        // verifica se a data da informação é do período e se o valor não está zerado.
                        $dataDP = new DateTime($d->data); // Data do pagamento.

                        if($dataDP->format('Y-m') == $mesAno && $d->valor > 0) {
                            // Pagamento válido.
                            $DPok[] = $d;
                        }
                    }
                }

                

                // Verifica se há conteúdo na variável.
                if(count($DPok) > 0) {
                    // Envia essa receita para o array de retorno.
                    $total = 0;
                    foreach($DPok as $d) {$total += $d->valor;}


                    $retorno[] = [
                        'cliente_nome'      => $r->cliente_nome,
                        'cliente_id'        => $r->cliente_id,
                        'detalhes_pagamento'=> $r->detalhes_pagamento,
                        'id'                => $r->id,
                        'roteiro_nome'      => $r->roteiro_nome,
                        'vencimento'        => $r->vencimento,
                        'valor'             => $total,
                        'forma_pagamento'   => $r->forma_pagamento,
                        'valor_total'       => $this->converteCentavoParaReal($r->valor_total),
                        'valor_recebido_mes'=> $this->converteCentavoParaReal($total),
                        'data_venda'        => (new \DateTime($r->data_venda))->format('d/m/Y H:i:s'),
                    ];
                }
            }

            return $retorno;
        } else {
            return [];
        }

    }

}