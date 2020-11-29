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
            array_push($lista_clientes, (object)['id' => $cid, 'colo' => false]);
        } else if(count($lista_clientes) < (int)$this->dados->clientes_total) {
            array_push($lista_clientes, (object)['id' => $cid, 'colo' => false]);
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

            /*
            if(is_int($lista_clientes[0])) { // Busca em array com inteiros. OBSOLETO
                $key = array_search($cid, $lista_clientes);
            } else { // Busca em array com objeto.
                $key = array_search($cid, array_column($lista_clientes,'id'));
            }*/

            $key = array_search($cid, array_column($lista_clientes,'id'));
            // Remove cliente
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
     * Define passageiro como criança de colo. Se ele já for criança de colo, remove a definição.
     * @param int $cid ID do cliente
     * @return bool|string
     */
    public function alternaListaClienteColo(int $cid)
    {
        if($this->dados == null) {
            return false;
        }

        $lista_clientes = $this->dados->lista_clientes;
        $lista_clientes = json_decode($lista_clientes);

        if($lista_clientes == null) { // Lista vazia. Não tem o que definir.
            $lista_clientes = array();
            return false;
        }

        // Conta o número de crianças de colo nesta lista.
        $criancaColoArr = [];
        foreach($lista_clientes as $key => $val) {
            if($val->colo == true) {
                array_push($criancaColoArr, $key);
            }
        }
        unset($key, $val);

        // Busca o cliente na lista
        $key = array_search($cid, array_column((array)$lista_clientes, 'id'));

        if($key === FALSE) {
            // Não encontrado
            return false;
        } else {
            // Encontrado
            $l = $lista_clientes[$key];


            if($l->colo === true) { // Define como FALSE
                $lista_clientes[$key]->colo = false;
            } else { // Define como TRUE

                // Verifica se a quantidade de crianças de colo está dentro do limite.
                if(count($criancaColoArr) >= (int)$this->dados->criancas_colo) {
                    // Necessário remover uma criança de colo, para definir a nova criança de colo.
                    if(!empty($criancaColoArr)) {
                        $lista_clientes[$criancaColoArr[0]]->colo = false;
                    } else {
                        return false;
                    }
                }

                $lista_clientes[$key]->colo = true;
            }

            $temp1 = [];
            foreach($lista_clientes as $l) {
                array_push($temp1, $l);
            }
            $lista_clientes = $temp1;

            try {
                $abc = $this->pdo->prepare("UPDATE vendas SET lista_clientes = :lc WHERE id = $this->id");
                $abc->bindValue(':lc', json_encode($lista_clientes), \PDO::PARAM_STR);
                $abc->execute();
                return true;
            } catch(PDOException $e) {
                error_log($e->getMessage(), 0);
    
                return false;
            }
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

                    // ############################################ reescrita

                    // Verifica a quantidade de parcelas
                    if((int)$this->dados->parcelas - (int)$this->dados->parcelas_pagas <= 0) { // TODA PARCELAS PAGAS
                        // ERRO: A quantidade de parcelas extrapolou.
                        return 'A quantidade de parcelas já extrapolou. Não é possível mais fazer pagamentos. Solicite que o desenvolvedor '.
                        'aumente a quantidade de parcelas manualmente no Banco de Dados.';
                    } else if((int)$this->dados->parcelas - (int)$this->dados->parcelas_pagas == 1) { // ÚLTIMA PARCELA
                        if((int)$this->dados->total_pago + (int)$outro['valor_parcela'] < ( (int)$this->dados->valor_total + (int)$this->dados->multas )) {
                            // O valor não pode ser menor.
                            return 'Valor da parcela é baixo. Na última (ou única) parcela, o valor da parcela precisa cobrir o valor restante do total da compra (e possíveis multas).';
                        }
                    } else { // DEMAIS PARCELAS: PRIMEIRA ATÉ PENÚLTIMA
                        // Continua. Nada de diferente
                    }

                    /**
                     * Fluxo de pagamento:
                     * O valor total da compra deve ser QUITADO; em seguida, o valor da multa deve ser QUITADO.
                     * Por último, o excedente deve ser transformado em créditos.
                     * 
                     * Variáveis:
                     *      $vParcela (Valor da parcela recebida),
                     *      $vCompra (destinado a cobrir o valor da compra),
                     *      $vMulta (destinado a cobrir o valor da multa), 
                     *      $vExc (destinado ao crédito do cliente).
                     * 
                     * Ordem de verificação:
                     * 1) VALOR TOTAL == VALOR PAGO
                     *      Não há nada a cobrar. Valor destinado à multa e crédito.
                     * 2) VALOR TOTAL - VALOR PAGO >= VALOR DA PARCELA:
                     *      Paga o valor integral da parcela. Multa Paga = 0; Credito = 0.
                     * 3) VALOR TOTAL - VALOR PAGO < VALOR DA PARCELA: 
                     *      Separa o valor da parcela.
                     *      
                     * 
                     * 4) VALOR DA PARCELA > 0 && MULTAS > MULTAS PAGO:
                     *      Há multas para pagar
                     *      4.1) VALOR DA PARCELA > ( MULTAS - MULTAS PAGO )
                     *          Separa o valor da multa e deixa o excedente.
                     *      3.2) VALOR DA PARCELA <= ( MULTAS - MULTAS PAGO )
                     *          Separa o valor da multa. Excedente em 0.
                     * 
                     */
                    
                    $vParcela = (int)$outro['valor_parcela'];
                    $vMulta = 0;
                    $vExc = 0;

                    if( (int)$this->dados->valor_total == (int)$this->dados->total_pago ) { // 1)
                        $vCompra = 0;
                    } else if((int)$this->dados->valor_total - (int)$this->dados->total_pago >= $vParcela) { // 2)
                        $vCompra = (int)$outro['valor_parcela'];
                        $vParcela = 0;
                    } else if((int)$this->dados->valor_total - (int)$this->dados->total_pago < $vParcela) { // 3)
                        $vCompra = ( (int)$this->dados->valor_total - (int)$this->dados->total_pago );
                        $vParcela = $vParcela - $vCompra;
                    }
                    
                    if($vParcela > 0 && (int)$this->dados->multas > (int)$this->dados->multas_pago) { // 4)
                        if($vParcela > ( (int)$this->dados->multas - (int)$this->dados->multas_pago )) { // 4.1)
                            $vMulta = (int)$this->dados->multas - (int)$this->dados->multas_pago;
                            $vExc = $vParcela - $vMulta; // Excedente.
                        } else if($vParcela <= ( (int)$this->dados->multas - (int)$this->dados->multas_pago )) { // 4.2)
                            $vMulta = $vParcela;
                            $vParcela = 0; // Sem excedente. Parcela esgotou.
                        }
                    }


                    // Organiza os dados para persistir no banco O ÚLTIMO PAGAMENTO.
                    $detalhes = json_decode($this->dados->detalhes_pagamento);
                    if($detalhes == NULL) {
                        $detalhes = [];
                    }

                    array_push($detalhes, [
                        'valor' => (int)$outro['valor_parcela'], // Informa o valor integral da parcela paga.
                        'parcela' => (int)$this->dados->parcelas_pagas+1,
                        'data' => date('Y-m-d H:i:s')
                    ]);

                    /**
                     * Verifica se esse pagamento cobre todas as outras parcelas.
                     * Se SIM, adiciona informação zerada nas parcelas seguintes.
                     */

                    if( ((int)$this->dados->total_pago + (int)$this->dados->multas_pago) + (int)$outro['valor_parcela'] >= ((int)$this->dados->valor_total + (int)$this->dados->multas)) {
                        // COBRINDO TODAS AS PARCELAS
                        for($i = (int)$this->dados->parcelas_pagas+2; $i <= (int)$this->dados->parcelas; $i++) {
                            array_push($detalhes, [
                                'valor' => 0,
                                'parcela' => $i,
                                'data' => date('Y-m-d H:i:s')
                            ]);
                        }

                        $parcPagas = (int)$this->dados->parcelas; // PARCELAS_PAGAS
                        $situ = 'Paga'; // STATUS
                        $dataPagamento = 'data_pagamento = NOW(), ';
                        $logStr = 'Alterou a situação da venda <a href="javascript:void(0)" onclick="getVenda('.$this->id.')">#'. $this->id.'.</a>: <b>'.\strtoupper($atual).'</b> para <b>Paga</b>.'; // MENSAGEM DO LOG
                    } else {
                        $parcPagas = (int)$this->dados->parcelas_pagas+1; // PARCELAS_PAGAS
                        $situ = 'Pagando'; // STATUS
                        $dataPagamento = '';
                        $logStr = 'Lançou pagamento de parcela (<b>'.$parcPagas.'</b> de <b>'.$this->dados->parcelas.'</b>) da venda <a href="javascript:void(0)" onclick="getVenda('.$this->id.')">#'. $this->id.'.</a>: <b>'.\strtoupper($atual).'</b>.'; // MENSAGEM DO LOG
                    }
                    

                    // Persiste
                    $this->pdo->beginTransaction();

                    try{
                        /**  
                         * Persiste a parcela paga, multa paga e excedente, se houver.
                         * 
                        */
                        
                        $abc = $this->pdo->prepare("UPDATE vendas SET status = :status, parcelas_pagas = :parc, ".
                        "total_pago = :t_pag, detalhes_pagamento = :d_pag, $dataPagamento".
                        "multas_pago = :m_pag WHERE id = :id");
                        $abc->bindValue(':id', $this->id, \PDO::PARAM_INT);
                        $abc->bindValue(':status', $situ, \PDO::PARAM_STR); // Situação da venda: Pagando ou Paga
                        $abc->bindValue(':parc', $parcPagas, \PDO::PARAM_INT);
                        //$abc->bindValue(':t_pag', (int)$this->dados->total_pago + (int)$outro['valor_parcela'], \PDO::PARAM_INT);
                        $abc->bindValue(':t_pag', (int)$this->dados->total_pago + $vCompra, \PDO::PARAM_INT); // TOTAL PAGO
                        $abc->bindValue(':m_pag', (int)$this->dados->multas_pago + $vMulta, \PDO::PARAM_INT); // MULTA PAGO
                        $abc->bindValue(':d_pag', json_encode($detalhes), \PDO::PARAM_STR);
                        $abc->execute();
                        /**
                         * LOG
                         */
                        $log = new LOG();
                        /**
                         * ./LOG
                         */


                        // Adiciona créditos ao cliente, se houver.
                        if($vExc > 0) {
                            // Há créditos para adicionar ao cliente.
                            $abc = $this->pdo->query("SELECT * FROM clientes WHERE id = ".$this->dados->cliente_id);
                            if($abc->rowCount() == 0) {
                                // Cliente não existe. Faz um RollBack no banco de dados e retorna erro.
                                $this->pdo->rollBack();

                                $log->novo('O cliente de ID: '.$this->dados->cliente_id.' não foi encontrado, por isso não pode receber créditos. '.
                                'A venda <a href="javascript:void(0)" onclick="getVenda('.$this->id.')">#'. $this->id.'.</a> não poderá receber valor a mais.',$_SESSION['auth']['id'], 2);

                                return false;
                            } else {
                                // Cliente existe. Lança créditos no cliente.
                                $abc = $this->pdo->query("UPDATE clientes SET credito = credito + $vExc WHERE id = ".$this->dados->cliente_id);

                            }
                        }

                        

                         
                        $log->novo($logStr, $_SESSION['auth']['id'], 2); // LOG
                        $this->pdo->commit();
                        return true;
                    } catch(\PDOException $e) {
                        error_log('Erro ao definir situação da venda (#'.$this->id.'): '.$e->getMessage(), 0);
                        $this->pdo->rollBack();
                        return false;
                    }

                    // ############################################ ./reescrita

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
                        !isset($outro['valor_estorno']) || $outro['valor_estorno'] === ''
                    ) {
                        return false;
                    }

                    if((int)$outro['valor_estorno'] == 0 && ( (int)$this->dados->total_pago > 0 || (int)$this->dados->valor_total > 0 ))  {
                        // Valor de estorno só pode 0,00, se o valor total e total_pago for igual a zero.
                        return false;
                    }
                    
                    $valor = (int)$outro['valor_estorno'];
                    try{
                        $parcelas = $this->dados->parcelas;
                        /*
                        ob_start();
                        var_dump($parcelas);
                        error_log('- Informações da parcela:'.\ob_get_clean());
                        */
                        $abc = $this->pdo->query("UPDATE vendas SET status = '".$situacao."', parcelas_pagas = ".$parcelas.", data_estorno = NOW(), valor_devolvido = $valor WHERE id = $this->id");
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

            /**
             * caso especial
             */
            case 'PagarCredito':
                if($atual == 'Cancelada' || $atual == 'Devolvida' || $atual == 'Paga') {
                    return false;
                } else {
                    // Faz o pagamento com o crédito do cliente.
                    $ret = $this->pagarComCredito();

                    if($ret === true) {
                        // Recarrega os dados da venda e verifica se é necessário definir a venda como PAGA.
                        $this->loadInfoBD();

                        $valorRestante = (int)$this->dados->valor_total - (int)$this->dados->total_pago;
                        $valorRestante += ((int)$this->dados->multas - (int)$this->dados->multas_pago);

                        if($valorRestante > 0) {
                            // Ainda há valor pendente. Altera situação "Pagando".
                            $abc = $this->pdo->query("UPDATE vendas SET status = 'Pagando' WHERE id = $this->id");
                        } else {
                            // Não há pendências. Altera situação para "Paga".
                            $abc = $this->pdo->query("UPDATE vendas SET status = 'Paga' WHERE id = $this->id");
                        }

                        return true;
                    } else {
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

    /**
     * Define o aceite do contrato.
     * 
     * @param bool $aceito
     * @return bool|null Para resetar, envie NULL; para concordar, envie TRUE; para discordar, envie FALSE.
     */
    public function setContratoAceite($aceito)
    {
        if($this->dados == null) {
            return false;
        }

        if(!is_null($aceito) && !is_bool($aceito)) {
            return false;
        }

        if($aceito === null) {
            $abc = $this->pdo->query("UPDATE vendas SET termos_ac = NULL, termos_data = NULL WHERE id = $this->id");
        } else if($aceito === true) {
            $abc = $this->pdo->query("UPDATE vendas SET termos_ac = 1, termos_data = NOW() WHERE id = $this->id");
        } else {
            $abc = $this->pdo->query("UPDATE vendas SET termos_ac = 0, termos_data = NOW() WHERE id = $this->id");
        }

        return true;
    }

    /**
     * Define o valor da multa para a venda.
     * 
     * @param int $valor Valor em centavos da multa.
     * 
     * @return bool
     */
    public function setMulta(int $valor = 0)
    {
        if($this->dados == null) {
            return false;
        }

        if($valor < 0) {
            return false;
        } else {
            try {
                $abc = $this->pdo->query("UPDATE vendas SET multas = $valor WHERE id = $this->id");
                return true;
            } catch(\PDOException $e) {
                error_log('Tentativa de definir multa em venda, retorno erro: '.$e->getMessage());
                return false;
            }
        }
    }

    /**
     * Paga venda com crédito do cliente.
     * 
     * @return bool Se o pagamento foi realizado com sucesso, retorna TRUE; caso contrário, retorna FALSE.
     */
    protected function pagarComCredito()
    {
        if($this->dados == null) {
            return false;
        }

        $cl = new Cliente($this->dados->cliente_id);
        $cliente = $cl->getDados();

        if((int)$cliente->credito == 0) {
            // Cliente não tem crédito.
            return false;
        } else {
            // Cliente tem crédito
            $credito = (int)$cliente->credito;

            // Inicia transação no banco de dados.
            $this->pdo->beginTransaction();
            try {
                $detAtual = [
                    'valor' => 0,
                    'parcela' => 0,
                    'data' => date('Y-m-d'),
                    'forma' => 'CreditoConta'
                ];

                // Verifica o valor restante da venda para ser quitado com o crédito.
                $valorRestante = (int)$this->dados->valor_total - (int)$this->dados->total_pago;
                if($valorRestante == 0) {
                    // Não há nada para pagar.
                } else {
                    // Há valor pendente.
                    if($credito >= $valorRestante) {
                        // O crédito cobre o valor restante.
                        $abc = $this->pdo->prepare("UPDATE vendas SET total_pago = :tPago WHERE id = $this->id");
                        $abc->bindValue(':tPago', (int)$this->dados->total_pago + $valorRestante, \PDO::PARAM_INT);
                        $abc->execute();

                        $credito -= $valorRestante;
                        $detAtual['valor'] += $valorRestante;

                    } else {
                        // O crédito não cobre todo o valor restante.
                        $abc = $this->pdo->prepare("UPDATE vendas SET total_pago = :tPago WHERE id = $this->id");
                        $abc->bindValue(':tPago', (int)$this->dados->total_pago + $credito, \PDO::PARAM_INT);
                        $abc->execute();

                        $detAtual['valor'] += $credito;
                        $credito = 0;
                    }
                }


                // Verifica se há multas e se há crédito para quitar a divida.
                if($credito > 0 && (int)$this->dados->multas - (int)$this->dados->multas_pago > 0) {
                    // Há crédito disponível e há multa pendente de quitação.
                    $valorRestante = (int)$this->dados->multas - (int)$this->dados->multas_pago;

                    if($credito >= $valorRestante) {
                        // O crédito cobre o valor restante.
                        $abc = $this->pdo->prepare("UPDATE vendas SET multas_pago = :mPago WHERE id = $this->id");
                        $abc->bindValue(':mPago', (int)$this->dados->multas_pago + $valorRestante, \PDO::PARAM_INT);
                        $abc->execute();

                        $credito -= $valorRestante;
                        $detAtual['valor'] += $valorRestante;
                    } else {
                        // O crédito não cobre todo o valor restante.
                        $abc = $this->pdo->prepare("UPDATE vendas SET multas_pago = :mPago WHERE id = $this->id");
                        $abc->bindValue(':mPago', (int)$this->dados->multas_pago + $credito, \PDO::PARAM_INT);
                        $abc->execute();

                        $detAtual['valor'] += $credito;
                        $credito = 0;
                    }
                }

                // Salva o detalhes do pagamento, informação de crédito.
                $detalhes = json_decode($this->dados->detalhes_pagamento);
                if($detalhes == NULL) {
                    $detalhes = [];
                }

                array_push($detalhes, $detAtual);
                $abc = $this->pdo->prepare("UPDATE vendas SET detalhes_pagamento = :det WHERE id = $this->id");
                $abc->bindValue(':det', json_encode($detalhes), \PDO::PARAM_STR);
                $abc->execute();

                $abc = $this->pdo->query("UPDATE clientes SET credito = $credito WHERE id = ".$this->dados->cliente_id);

                $log = new LOG();
                $log->novo('A venda <a href="javascript:void(0)" onclick="getVenda('.$this->id.')">(#'.$this->id.')</a> recebeu um pagamento com o crédito do cliente.', $_SESSION['auth']['id'], 2);


                $this->pdo->commit();
                return true;
            } catch(\PDOException $e) {
                error_log("Não foi possível pagar venda (#$this->id) com crédito: $e->getMessage()");
                $this->pdo->rollBack();
                return false;
            }
            
        }
    }
}