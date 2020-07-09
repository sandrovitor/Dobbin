<?php
namespace SGCTUR;
use SGCTUR\LOG;
use SGCTUR\Erro;

class Venda extends Master
{
    private $id = null;
    private $dados;

    public function __construct(int $vendaID)
    {
        parent::__construct();
        if($vendaID <= 0) {
            $this->dados = null;
            $this->dependentes = null;
            return false;
        } else {
            $this->id = $vendaID;
            $this->loadInfoBD();
        }
    }

    /**
     * Carrega as informações do BD de dados para o objeto.
     */
    private function loadInfoBD()
    {
        if($this->id  !== 0) {
            $abc = $this->pdo->query("SELECT vendas.*, clientes.nome as cliente_nome, roteiros.nome as roteiro_nome, roteiros.data_ini as roteiro_data_ini, ".
            "roteiros.data_fim as roteiro_data_fim, CONCAT(login.nome, ' ', login.sobrenome) as usuario_nome FROM vendas ".
            "LEFT JOIN clientes ON vendas.cliente_id = clientes.id ".
            "LEFT JOIN roteiros ON vendas.roteiro_id = roteiros.id ".
            "LEFT JOIN login ON vendas.usuario_id = login.id ".
            "WHERE vendas.id = $this->id");
            if($abc->rowCount() == 0) {
                $this->dados = null;
                return false;
            } else {
                $this->dados = $abc->fetch(\PDO::FETCH_OBJ);

                // Formata alguns valores para ficar mais amigável.
                switch($this->dados->status) {
                    case 'Reserva': $this->dados->status_html = '<span class="badge badge-info py-1 px-2">Reserva</span>'; break;
                    case 'Aguardando': $this->dados->status_html = '<span class="badge badge-primary py-1 px-2">Aguardando Pagamento</span>'; break;
                    case 'Pagando': $this->dados->status_html = '<span class="badge badge-success py-1 px-2">Em Pagamento</span>'; break;
                    case 'Paga': $this->dados->status_html = '<span class="badge badge-success py-1 px-2">Paga</span>'; break;
                    case 'Cancelada': $this->dados->status_html = '<span class="badge badge-secondary py-1 px-2">Cancelada</span>'; break;
                    case 'Devolvida': $this->dados->status_html = '<span class="badge badge-dark py-1 px-2">Devolvida</span>'; break;
                }
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * Retorna os dados do roteiro.
     * 
     * @return mixed Se falha, retorna FALSE; se sucesso, retorna um ARRAY.
     */
    public function getDados()
    {
        if($this->dados == null) {
            return false;
        }

        return $this->dados;
    }

    /**
     * Adiciona cliente na lista.
     * 
     * @param int $cid ID do cliente.
     * @return bool
     */
    public function setListaClienteNovo(int $cid)
    {
        if($this->dados == null) {
            return false;
        }

        $lista_clientes = $this->dados->lista_clientes;
        $lista_clientes = json_decode($lista_clientes);

        if($lista_clientes == null) {
            $lista_clientes = array();
        }

        if(empty($lista_clientes)) {
            array_push($lista_clientes, $cid);
        } else if(count($lista_clientes) < (int)$this->dados->clientes_total) {
            array_push($lista_clientes, $cid);
        } else {
            return false;
        }

        try {
            $abc = $this->pdo->prepare("UPDATE vendas SET lista_clientes = :lc WHERE id = $this->id");
            $abc->bindValue(':lc', json_encode($lista_clientes), \PDO::PARAM_STR);
            $abc->execute();

            /**
             * LOG
             */
            $log = new LOG();
            $log->novo('Adicionou cliente na lista de passageiros da venda <a href="javascript:void(0)" onclick="getVenda('.$this->id.')">#'. $this->id.'.</a>', $_SESSION['auth']['id'], 1);
            /**
             * ./LOG
             */
            
            return true;
        } catch(PDOException $e) {
            error_log($e->getMessage(), 0);

            return false;
        }
    }

    /**
     * Remove cliente da lista.
     * @param int $cid ID do cliente.
     */
    public function setListaClienteRemove(int $cid)
    {
        if($this->dados == null) {
            return false;
        }

        $lista_clientes = $this->dados->lista_clientes;
        $lista_clientes = json_decode($lista_clientes);

        if($lista_clientes == null) { // Lista vazia. Não tem o que remover.
            $lista_clientes = array();
            return true;
        }

        if(!empty($lista_clientes)) {
            // Remove cliente
            $key = array_search($cid, $lista_clientes);
            if($key === FALSE) {
                return true; // cliente não encontrado. Não remove nada
            } else {
                unset($lista_clientes[$key]);
                $x = $lista_clientes;
                $lista_clientes = [];
                
                foreach($x as $y) {
                    array_push($lista_clientes, $y);
                }
            }
        } else {
            return true; // Não há o que remover.
        }

        try {
            $abc = $this->pdo->prepare("UPDATE vendas SET lista_clientes = :lc WHERE id = $this->id");
            $abc->bindValue(':lc', json_encode($lista_clientes), \PDO::PARAM_STR);
            $abc->execute();

            /**
             * LOG
             */
            $log = new LOG();
            $log->novo('Removeu cliente na lista de passageiros da venda <a href="javascript:void(0)" onclick="getVenda('.$this->id.')">#'. $this->id.'.</a>', $_SESSION['auth']['id'], 1);
            /**
             * ./LOG
             */
            
            return true;
        } catch(PDOException $e) {
            error_log($e->getMessage(), 0);

            return false;
        }
    }

    /**
     * Define o status da venda.
     * 
     * @param string $situacao Situação da venda: Reserva, Aguardando, Paga, Cancelada, Devolvida.
     * @param array $outro Pode conter alguns desses valores: ['forma_pagamento' = STRING, 'vencimento' => INT, 'parcelas' => INT, 'valor_parcela' => INT]
     * 
     * @return bool
     */
    public function setSituacao(string $situacao, array $outro = [])
    {
        if($this->dados == null) {
            return false;
        }

        $atual = $this->dados->status;  

        switch($situacao)
        {
            case 'Reserva':
                if($atual == 'Devolvida' || $atual == 'Cancelada' || $atual == 'Paga') {
                    return false;
                } else {
                    try{
                        $abc = $this->pdo->query("UPDATE vendas SET status = '".$situacao."' WHERE id = $this->id");
                        /**
                         * LOG
                         */
                        $log = new LOG();
                        $log->novo('Alterou a situação da venda <a href="javascript:void(0)" onclick="getVenda('.$this->id.')">#'. $this->id.'.</a>: <b>'.\strtoupper($atual).'</b> para <b>'.strtoupper($situacao).'</b>.', $_SESSION['auth']['id'], 2);
                        /**
                         * ./LOG
                         */
                        return true;
                    } catch(\PDOException $e) {
                        error_log($e->getMessage(), 0);
                        return false;
                    }
                }
            break;


            case 'Aguardando':

                if($atual == 'Devolvida' || $atual == 'Cancelada' || $atual == 'Paga') {
                    return false;
                } else {
                    // Verifica se todas as informações necessárias foram enviadas.
                    if(
                        !isset($outro['forma_pagamento']) || $outro['forma_pagamento'] == '' ||
                        !isset($outro['parcelas']) || $outro['parcelas'] == '' ||
                        !isset($outro['vencimento']) || $outro['vencimento'] == ''
                    ) {
                        return false;
                    }

                    try{
                        $abc = $this->pdo->prepare("UPDATE vendas SET status = :status, forma_pagamento = :f_p, parcelas = :parc, vencimento = :venc, data_venda = NOW() WHERE id = $this->id");
                        $abc->bindValue(':status', $situacao, \PDO::PARAM_STR);
                        $abc->bindValue(':f_p', $outro['forma_pagamento'], \PDO::PARAM_STR);
                        $abc->bindValue(':parc', $outro['parcelas'], \PDO::PARAM_INT);
                        $abc->bindValue(':venc', $outro['vencimento'], \PDO::PARAM_INT);
                        $abc->execute();
                        /**
                         * LOG
                         */
                        $log = new LOG();
                        $log->novo('Alterou a situação da venda <a href="javascript:void(0)" onclick="getVenda('.$this->id.')">#'. $this->id.'.</a>: <b>'.\strtoupper($atual).'</b> para <b>'.strtoupper($situacao).'</b>.', $_SESSION['auth']['id'], 2);
                        /**
                         * ./LOG
                         */
                        return true;
                    } catch(\PDOException $e) {
                        error_log($e->getMessage(), 0);
                        return false;
                    }
                }
            break;


            case 'Pagando':
                if($atual == 'Devolvida' || $atual == 'Cancelada' || $atual == 'Paga') {
                    return false;
                } else {
                    // Verifica se todas as informações necessárias foram enviadas.
                    if(
                        !isset($outro['valor_parcela']) || $outro['valor_parcela'] == '' || (int)$outro['valor_parcela'] == 0
                    ) {
                        return false;
                    }

                    // Verifica quantidade de parcelas.
                    // Se for última parcela, o TOTAL_PAGO + VALOR_PARCELA precisa ser igual ao VALOR_TOTAL
                    if((int)$this->dados->parcelas - (int)$this->dados->parcelas_pagas == 1) {
                        if((int)$this->dados->total_pago + (int)$outro['valor_parcela'] < (int)$this->dados->valor_total) {
                            // O valor não pode ser menor.
                            return 'Valor da parcela é baixo. Na última (ou única) parcela, o valor da parcela precisa cobrir o valor restante do total da compra.';
                        } else if((int)$this->dados->total_pago + (int)$outro['valor_parcela'] > (int)$this->dados->valor_total) {
                            // O valor pago é maior que o esperado.
                            return 'Valor da parcela é alto. O valor da parcela extrapolou o valor da compra. Não permitido.';
                        }

                        // Organiza os dados para persistir no banco O ÚLTIMO PAGAMENTO.
                        $detalhes = json_decode($this->dados->detalhes_pagamento);
                        if($detalhes == NULL) {
                            $detalhes = [];
                        }

                        array_push($detalhes, [
                            'valor' => (int)$outro['valor_parcela'],
                            'parcela' => (int)$this->dados->parcelas_pagas+1,
                            'data' => date('Y-m-d')
                        ]);

                        // Persiste
                        try{
                            $abc = $this->pdo->prepare("UPDATE vendas SET status = :status, parcelas_pagas = :parc, ".
                            "total_pago = :t_pag, detalhes_pagamento = :d_pag, data_pagamento = NOW() WHERE id = $this->id");
                            $abc->bindValue(':status', 'Paga', \PDO::PARAM_STR); // Concluiu o pagamento.
                            $abc->bindValue(':parc', (int)$this->dados->parcelas_pagas+1, \PDO::PARAM_INT);
                            $abc->bindValue(':t_pag', (int)$this->dados->total_pago + (int)$outro['valor_parcela'], \PDO::PARAM_INT);
                            $abc->bindValue(':d_pag', json_encode($detalhes), \PDO::PARAM_STR);
                            $abc->execute();
                            /**
                             * LOG
                             */
                            $log = new LOG();
                            $log->novo('Alterou a situação da venda <a href="javascript:void(0)" onclick="getVenda('.$this->id.')">#'. $this->id.'.</a>: <b>'.\strtoupper($atual).'</b> para <b>Paga</b>.', $_SESSION['auth']['id'], 2);
                            /**
                             * ./LOG
                             */
                            return true;
                        } catch(\PDOException $e) {
                            error_log($e->getMessage(), 0);
                            return false;
                        }

                    } else if((int)$this->dados->parcelas - (int)$this->dados->parcelas_pagas <= 0) {
                        // ERRO: A quantidade de parcelas extrapolou.
                        return 'A quantidade de parcelas já extrapolou. Não é possível mais fazer pagamentos. Solicite que o desenvolvedor '.
                        'aumente a quantidade de parcelas manualmente no Banco de Dados.';
                    } else {
                        // Ainda restam parcelas.

                        $detalhes = json_decode($this->dados->detalhes_pagamento);
                        if($detalhes == NULL) {
                            $detalhes = [];
                        }

                        // Verifica se a parcela paga cobre todo o valor da compra:
                        // TOTAL_PAGO + VALOR_PARCELA é maior ao VALOR_TOTAL
                        if((int)$this->dados->total_pago + (int)$outro['valor_parcela'] > (int)$this->dados->valor_total) {
                            // O valor pago é maior que o esperado.
                            return 'Valor da parcela é alto. O valor da parcela extrapolou o valor da compra. Não permitido.';
                        } else if((int)$this->dados->total_pago + (int)$outro['valor_parcela'] == (int)$this->dados->valor_total) {
                            // O valor da parcela cobriu todo o valor.
                            // A venda deve ser definida como PAGA;
                            // O campo PARCELAS_PAGAS deve ser igual a PARCELAS;

                            // Organiza dados para persistir no banco.
                            

                            array_push($detalhes, [
                                'valor' => (int)$outro['valor_parcela'],
                                'parcela' => (int)$this->dados->parcelas_pagas+1,
                                'data' => date('Y-m-d')
                            ]);

                            // Adiciona informação zerada nas parcelas seguintes.
                            for($i = (int)$this->dados->parcelas_pagas+2; $i <= (int)$this->dados->parcelas; $i++) {
                                array_push($detalhes, [
                                    'valor' => 0,
                                    'parcela' => $i,
                                    'data' => date('Y-m-d')
                                ]);
                            }

                            $parcPagas = (int)$this->dados->parcelas; // PARCELAS_PAGAS
                            $situ = 'Paga'; // STATUS
                            $dataPagamento = ', data_pagamento = NOW() ';
                            $logStr = 'Alterou a situação da venda <a href="javascript:void(0)" onclick="getVenda('.$this->id.')">#'. $this->id.'.</a>: <b>'.\strtoupper($atual).'</b> para <b>Paga</b>.'; // MENSAGEM DO LOG
                        } else {
                            // Pagamento de uma parcela normalmente.
                            // Valor mínimo: R$ 0,01 ou (1 centavo).

                            array_push($detalhes, [
                                'valor' => (int)$outro['valor_parcela'],
                                'parcela' => (int)$this->dados->parcelas_pagas+1,
                                'data' => date('Y-m-d')
                            ]);

                            $parcPagas = (int)$this->dados->parcelas_pagas+1; // PARCELAS_PAGAS
                            $situ = 'Pagando'; // STATUS
                            $dataPagamento = '';
                            $logStr = 'Lançou pagamento de parcela (<b>'.$parcPagas.'</b> de <b>'.$this->dados->parcelas.'</b>) da venda <a href="javascript:void(0)" onclick="getVenda('.$this->id.')">#'. $this->id.'.</a>: <b>'.\strtoupper($atual).'</b>.'; // MENSAGEM DO LOG
                        }

                        // Persiste
                        try{
                            $abc = $this->pdo->prepare("UPDATE vendas SET status = :status, parcelas_pagas = :parc, ".
                            "total_pago = :t_pag, detalhes_pagamento = :d_pag $dataPagamento WHERE id = $this->id");
                            $abc->bindValue(':status', $situ, \PDO::PARAM_STR); // Concluiu o pagamento.
                            $abc->bindValue(':parc', $parcPagas, \PDO::PARAM_INT);
                            $abc->bindValue(':t_pag', (int)$this->dados->total_pago + (int)$outro['valor_parcela'], \PDO::PARAM_INT);
                            $abc->bindValue(':d_pag', json_encode($detalhes), \PDO::PARAM_STR);
                            $abc->execute();
                            /**
                             * LOG
                             */
                            $log = new LOG();
                            $log->novo('', $_SESSION['auth']['id'], 2);
                            /**
                             * ./LOG
                             */
                            return true;
                        } catch(\PDOException $e) {
                            error_log($e->getMessage(), 0);
                            return false;
                        }
                    }
                }
            break;


            case 'Paga':
                if($atual == 'Devolvida' || $atual == 'Cancelada') {
                    return false;
                } else {
                    // Verifica se todas as informações necessárias foram enviadas.
                    if(
                        !isset($outro['forma_pagamento']) || $outro['forma_pagamento'] == ''
                    ) {
                        return false;
                    }

                    switch($outro['forma_pagamento']) {
                        case 'Crédito':
                        case 'Débito':
                        case 'Boleto':
                        case 'Digital':
                        case 'Transferência':
                        case 'Dinheiro':
                        case 'Outro':
                            $f = $outro['forma_pagamento']; break;

                        default: return false; break;
                    }

                    if($this->dados->data_venda == NULL) {
                        $dataVenda = 'data_venda = NOW(), ';
                    } else {
                        $dataVenda = '';
                    }
                    if((int)date('j') > 28){$venc = 1;} else {$venc = (int)date('j');}

                    try{
                        //$abc = $this->pdo->query("UPDATE vendas SET status = '".$situacao."', forma_pagamento = '".$f."', data_pagamento = NOW(), data_venda = IF(data_venda IS NULL, NOW(), data_venda) WHERE id = $this->id");
                        $abc = $this->pdo->prepare("UPDATE vendas SET status = :status, forma_pagamento = :f_p, parcelas_pagas = :parc, vencimento = :venc, $dataVenda total_pago = :t_pago, ".
                        "data_pagamento = NOW(), detalhes_pagamento = :det_pag WHERE id = $this->id");
                        $abc->bindValue(':status', $situacao, \PDO::PARAM_STR);
                        $abc->bindValue(':f_p', $f, \PDO::PARAM_STR);
                        $abc->bindValue(':parc', $this->dados->parcelas, \PDO::PARAM_INT);
                        $abc->bindValue(':venc', $venc, \PDO::PARAM_INT); // Dia de hoje
                        $abc->bindValue(':t_pago', $this->dados->valor_total, \PDO::PARAM_INT);
                        $abc->bindValue(':det_pag', json_encode([
                            'valor' => $this->dados->valor_total,
                            'parcela' => 1,
                            'data' => date('Y-m-d')
                        ]), \PDO::PARAM_STR);

                        $abc->execute();

                        /**
                         * LOG
                         */
                        $log = new LOG();
                        $log->novo('Alterou a situação da venda <a href="javascript:void(0)" onclick="getVenda('.$this->id.')">#'. $this->id.'.</a>: <b>'.\strtoupper($atual).'</b> para <b>'.strtoupper($situacao).'</b>.', $_SESSION['auth']['id'], 2);
                        /**
                         * ./LOG
                         */
                        return true;
                    } catch(\PDOException $e) {
                        error_log($e->getMessage(), 0);
                        return false;
                    }
                }
            break;


            case 'Cancelada':
                if($atual == 'Devolvida' || $atual == 'Pagando' || $atual == 'Paga') {
                    return false;
                } else {
                    try{
                        $abc = $this->pdo->query("UPDATE vendas SET status = '".$situacao."', data_cancelado = NOW() WHERE id = $this->id");
                        /**
                         * LOG
                         */
                        $log = new LOG();
                        $log->novo('Alterou a situação da venda <a href="javascript:void(0)" onclick="getVenda('.$this->id.')">#'. $this->id.'.</a>: <b>'.\strtoupper($atual).'</b> para <b>'.strtoupper($situacao).'</b>.', $_SESSION['auth']['id'], 3);
                        /**
                         * ./LOG
                         */
                        return true;
                    } catch(\PDOException $e) {
                        error_log($e->getMessage(), 0);
                        return false;
                    }
                }
            break;


            case 'Devolvida':
                if($atual == 'Devolvida' || $atual == 'Cancelada' || $atual == 'Reserva' || $atual == 'Aguardando') {
                    return false;
                } else {
                    // Verifica se todas as informações necessárias foram enviadas.
                    if(
                        !isset($outro['valor_estorno']) || $outro['valor_estorno'] == '' || (int)$outro['valor_estorno'] == 0
                    ) {
                        return false;
                    }
                    $valor = (int)$outro['valor_estorno'];
                    try{
                        $abc = $this->pdo->query("UPDATE vendas SET status = '".$situacao."', parcelas_pagas = $this->parcelas, data_estorno = NOW(), valor_devolvido = $valor WHERE id = $this->id");
                        /**
                         * LOG
                         */
                        $log = new LOG();
                        $log->novo('Alterou a situação da venda <a href="javascript:void(0)" onclick="getVenda('.$this->id.')">#'. $this->id.'.</a>: <b>'.\strtoupper($atual).'</b> para <b>'.strtoupper($situacao).'</b>.', $_SESSION['auth']['id'], 3);
                        /**
                         * ./LOG
                         */
                        return true;
                    } catch(\PDOException $e) {
                        error_log($e->getMessage(), 0);
                        return false;
                    }
                }
            break;

            default:
                return false;
            break;
        }
    }

    /**
     * Define OBSERVAÇÃO DA VENDA.
     */
    public function setObservacao(string $obs)
    {
        if($this->dados == null) {
            return false;
        }
        try{
            $abc = $this->pdo->prepare("UPDATE vendas SET vendas.obs = :obs WHERE id = $this->id");
            $abc->bindValue(':obs', $obs, \PDO::PARAM_STR);

            $abc->execute();
            return true;
        } catch(\PDOException $e) {
            error_log($e->getMessage(), 0);
            return false;
        }
    }
}