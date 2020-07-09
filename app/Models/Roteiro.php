<?php
namespace SGCTUR;
use SGCTUR\LOG, SGCTUR\Erro, SGCTUR\Cliente;

class Roteiro extends Master 
{
    const ORDER_ASC = 1;
    const ORDER_DESC = 0;

    private $id = null;
    private $dados;
    private $dadosNovo = array();



    public function __construct(int $roteiroID)
    {
        parent::__construct();
        $this->id = $roteiroID;

        if($roteiroID <= 0) {
            $this->dados = null;
            return false;
        } else {
            $this->loadInfoBD();
        }
        

    }

    /**
     * Carrega as informações do BD de dados para o objeto.
     */
    private function loadInfoBD() 
    {
        if($this->id != 0) {
            $abc = $this->pdo->query('SELECT * FROM roteiros WHERE id = '.$this->id);
            if($abc->rowCount() == 0) {
                $this->dados = null;
                return false;
            } else {
                $reg = $abc->fetch(\PDO::FETCH_OBJ);
                $this->dados = $reg;
                
                // Decode os JSONs.
                $this->dados->despesas = json_decode($this->dados->despesas);
                $this->dados->lucro_previsto = json_decode($this->dados->lucro_previsto);
                $tarifa = stripslashes($this->dados->tarifa);
                $tarifa = json_decode($tarifa);
                if($tarifa != null) {
                    $this->dados->tarifa = $tarifa;
                }

                // Situação da viagem, com base na data.
                $hoje = new \DateTime();
                $partida = new \DateTime($this->dados->data_ini);
                $retorno = new \DateTime($this->dados->data_fim);
                $diffPart = $hoje->diff($partida);
                $diffRet = $hoje->diff($retorno);


                if($diffPart->invert == 1 && $diffRet->invert == 1) { // Datas já passaram.
                    $situacao = '<span class="badge badge-dark py-1 px-2 ml-3" title="A data da viagem já passou." data-toggle="tooltip">CONCLUIDO</span>';
                    $this->dados->situacao = 'CONCLUIDO';
                } else if($diffPart->invert == 1 && $diffRet->invert == 0) { // Durante a viagem
                    $situacao = '<span class="badge badge-primary py-1 px-2 ml-3" title="Período da viagem em andamento." data-toggle="tooltip">EM VIAGEM</span>';
                    $this->dados->situacao = 'ANDAMENTO';
                } else { // Data no futuro.
                    $this->dados->situacao = 'ABERTO';
                    if($diffPart->days > 7) { // Falta mais de uma semana
                        $situacao = '<span class="badge badge-success py-1 px-2 ml-3" title="Falta alguns dias para esta viagem." data-toggle="tooltip">PROGRAMADO</span>';
                    } else if($diffPart->days > 1) { // Falta uma semana ou menos.
                        $situacao = '<span class="badge badge-info py-1 px-2 ml-3" title="Falta uma semana ou menos para esta viagem." data-toggle="tooltip">MENOS DE UMA SEMANA</span>';
                    } else { // Breve. Menos de 2 dias
                        $situacao = '<span class="badge badge-info py-1 px-2 ml-3" title="Falta menos de 2 dias para esta viagem." data-toggle="tooltip">BREVE</span>';
                    }
                }

                $this->dados->situacao_html = $situacao;

                // Informações sobre vendas do roteiro
                if(!isset($this->dados->estoque)) {
                    $this->dados->estoque = array();
                }
        
                $abc = $this->pdo->query("SELECT SUM(clientes_total) as vendidos FROM vendas WHERE roteiro_id = $this->id AND status <> 'Cancelada' AND status <> 'Devolvida' AND status <> 'Reserva'");
                $reg = $abc->fetch(\PDO::FETCH_OBJ);
                if($reg->vendidos === NULL) { $reg->vendidos = 0;}
        
                $this->dados->estoque['total'] = (int)$this->dados->passagens;
                $this->dados->estoque['vendidos'] = (int)$reg->vendidos;
        
                $abc = $this->pdo->query("SELECT SUM(clientes_total) reservados FROM vendas WHERE roteiro_id = $this->id AND status = 'Reserva'");
                $reg = $abc->fetch(\PDO::FETCH_OBJ);
                if($reg->reservados === NULL) { $reg->reservados = 0;}
                $this->dados->estoque['reservados'] = (int)$reg->reservados;
                $this->dados->estoque['livre'] = $this->dados->estoque['total'] - ($this->dados->estoque['vendidos'] + $this->dados->estoque['reservados']);
                $this->dados->estoque['vendidos_perc'] = round( ($this->dados->estoque['vendidos']  * 100) / $this->dados->estoque['total'], 2);
                $this->dados->estoque['reservados_perc'] = round( ($this->dados->estoque['reservados'] * 100) / $this->dados->estoque['total'], 2);
                $this->dados->estoque['livre_perc'] = round( ($this->dados->estoque['livre'] * 100) / $this->dados->estoque['total'], 2);
                
                return true;
            }
        }
        return false;
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
     * Retorna lista de vendas desse roteiro.
     * 
     * @return array
     */
    public function getVendidosLista()
    {
        if($this->dados == null) {
            return array();
        }

        //$abc = $this->pdo->query("SELECT vendas.*, clientes.nome as cliente_nome FROM vendas LEFT JOIN clientes ON vendas.cliente_id = clientes.id WHERE vendas.roteiro_id = $this->id AND vendas.status <> 'Devolvida' AND vendas.status <> 'Cancelada' ORDER BY vendas.id ASC");
        $abc = $this->pdo->query("SELECT vendas.*, clientes.nome as cliente_nome FROM vendas LEFT JOIN clientes ON vendas.cliente_id = clientes.id WHERE vendas.roteiro_id = $this->id AND vendas.status <> 'Cancelada' ORDER BY vendas.id ASC");
        if($abc->rowCount() == 0) {
            $reg = array();
        } else {
            $reg = $abc->fetchAll(\PDO::FETCH_OBJ);
        }

        return $reg;
    }

    /**
     * Retorna lista de clientes/passageiros do roteiro.
     * 
     * @return array [success => TRUE|FALSE, mensagem => string, clientes => array]
     */
    public function getClientesLista()
    {
        if($this->dados == null) {
            return [
                'success' => false,
                'mensagem' => 'Roteiro não encontrado ou não existe.',
                'clientes' => []
            ];
        }

        $retorno = [
            'success' => true,
            'mensagem' => '',
            'clientes' => []
        ];

        // Primeiro verifica se já existe lista de clientes DEFINITIVA no roteiro.
        if($this->dados->clientes != '') {
            // Definitiva
            $retorno['tipo'] = 'DEFINITIVO';
            $retorno['clientes'] = json_decode($this->dados->clientes);
        } else {
            // Provisória
            $lista_vendas = $this->getVendidosLista();
            $temp1 = array(); // Array temporário com todos os clientes desordenados.
            foreach($lista_vendas as $l) {
                if($l->lista_clientes == '') {
                    $temp2 = array();
                } else {
                    $temp2 = json_decode($l->lista_clientes);
                }

                if(!empty($temp2)) {
                    foreach($temp2 as $temp3) {
                        // Busca cliente no Banco de dados.
                        $cliente = new Cliente($temp3);
                        $c = $cliente->getDados();
                        if($c->cpf == ''){$cpf = '-';} else {$cpf = $c->cpf;}
                        array_push($temp1, [
                            'id' => $c->id,
                            'nome' => $c->nome,
                            'faixa_etaria' => $c->faixa_etaria,
                            'cpf' => $cpf,
                            'cidade' => $c->cidade,
                            'estado' => $c->estado,
                            'titular' => $c->titular, // Código do titular (caso seja dependente)
                            'venda' => $l->id // ID da venda
                        ]);
                    }

                    unset($cliente, $c);

                    // organiza array pelo nome
                    usort($temp1, function($a, $b){
                        return strcmp($a["nome"], $b["nome"]);
                    });
                    
                }
            }

            $retorno['tipo'] = 'PROVISORIO';
            $retorno['clientes'] = $temp1;
        }

        return $retorno;
    }



    /**
     * Adiciona uma nova entrada no histórico deste roteiro.
     * 
     * @param int $parcid ID do parceiro.
     * @param string $detalhes Detalhes a serem inseridos.
     * @param string $etapa Etapa da negociação: CONTATO, PEDIDO DE BLOQUEIO, DESISTÊNCIA...
     * 
     * @return bool
     */
    public function setHistoricoNovo(int $parcid, string $detalhes, string $etapa = 'CONTATO')
    {
        if($this->dados == null) {
            return false;
        }

        try{
            $abc = $this->pdo->prepare("INSERT INTO `historico_negoc` (`id`, `empresa_id`, `roteiro_id`, `etapa`, `detalhes`, `criado_por`, `criado_em`) VALUES (NULL, $parcid, $this->id, :etapa, :det, :autor, NOW())");
            $abc->bindValue(':etapa', $etapa, \PDO::PARAM_STR);
            $abc->bindValue(':det', addslashes($detalhes), \PDO::PARAM_STR);
            $abc->bindValue(':autor', $_SESSION['auth']['id'], \PDO::PARAM_INT);

            $abc->execute();
            $d_i = new \DateTime($this->dados->data_ini);
            $d_f = new \DateTime($this->dados->data_fim);
            /**
             * LOG
             */
            $log = new LOG();
            $log->novo('Adicionou um registro no histórico do parceiro no ROTEIRO <i class="text-uppercase">'.$this->dados->nome.' ('. $d_i->format('d/m/Y') .' a '. $d_f->format('d/m/Y') .')</i>.', $_SESSION['auth']['id'], 1);
            /**
             * ./LOG
             */

            return true;
        } catch(PDOException $e) {
            \error_log($e->getMessage(), 1, $this->system->desenvolvedor[0]);
            \error_log($e->getMessage(), 0);
            return false;
        }
    }

    /**
     * Salvar tabela de tarifas no roteiro.
     * 
     * @param string $tarifa Uma string JSON com as tarifas.
     * 
     * @return bool
     */
    public function setTarifa(string $tarifa)
    {
        if(strlen($tarifa) >= 600) {
            return false;
        }

        try {
            $abc = $this->pdo->prepare("UPDATE roteiros SET tarifa = :tarifa, atualizado_em = NOW() WHERE id = $this->id");

            $abc->bindValue(':tarifa', addslashes($tarifa), \PDO::PARAM_STR);

            $abc->execute();

            $d_i = new \DateTime($this->dados->data_ini);
            $d_f = new \DateTime($this->dados->data_fim);
            /**
             * LOG
             */
            $log = new LOG();
            $log->novo('Alterou a tabela de tarifas do ROTEIRO <i class="text-uppercase">'.$this->dados->nome.' ('. $d_i->format('d/m/Y') .' a '. $d_f->format('d/m/Y') .')</i>.', $_SESSION['auth']['id'], 1);
            /**
             * ./LOG
             */

            return true;
        } catch(PDOException $e) {
            \error_log($e->getMessage(), 1, $this->system->desenvolvedor[0]);
            \error_log($e->getMessage(), 0);
            return false;
        }
    }

    /**
     * Define novos dados do roteiro.
     * 
     * @param array $dados Array de dados a serem alterados do roteiro. Exemplo: ['nome' => 'Novo Nome'].
     * 
     * @return void
     */
    public function setDados(array $dados)
    {
        // Valida os dados.

        foreach($dados as $key => $val) {
            switch($key) {
                case 'nome':
                    if($val == '') {$dados['nome'] = '[Sem nome]';}
                    break;

                case 'data_ini':
                    if($val == '') {unset($dados['data_ini']);}
                    break;

                case 'data_fim':
                    if($val == '') {unset($dados['data_fim']);}
                    break;

                case 'passagens':
                    if($val == '' || (int)$val == 0) {unset($dados['passagens']);}
                    break;

                case 'data_ini':
                    if($val == '') {unset($dados['data_ini']);}
                    break;

                case 'criado_em':
                case 'criado_por':
                case 'atualizado_em':
                case 'deletado_em':
                case 'deletado_por':
                case 'id':
                    unset($dados[$key]);
                break;

                default:
                    // verifica o tipo da variável. Faz validação diferente para ARRAYs.
                    if(is_array($val)) {
                        // Encoda as variáveis em JSON e compara strings.

                        $x = (array)$this->dados;
                        $y = \json_encode($val);
                        $z = \json_encode($x[$key]);

                        if($y == $z) { // Não atualiza.
                            unset($dados[$key]);
                        }

                        unset($x, $y, $z);
                    } else {
                        // Se não houver alterações nos dados, então não precisa atualizar.
                        $x = (array)$this->dados;
                        if($x[$key] == $val) {unset($dados[$key]);}
                        unset($x);
                    }
                break;

            }
        }
        
        $this->dadosNovo = $dados;
    }

    /**
     * Adiciona coordenador ao roteiro.
     * 
     * @param int $coord ID do coordenador que será adicionado.
     * 
     * @return bool
     */
    public function setCoordenadorAdd(int $coord)
    {
        if($this->dados->coordenador == '') {
            $coordenadores = array();
        } else {
            $coordenadores = json_decode($this->dados->coordenador);
        }

        // verifica se o coordenador já foi adicionado.
        if(array_search($coord, $coordenadores) === false) {
            // Não encontrado, adiciona coordenador.
            if(count($coordenadores) < $this->dados->qtd_coordenador) {
                array_push($coordenadores, $coord);
                $this->dados->coordenador = json_encode($coordenadores);

                $abc = $this->pdo->prepare("UPDATE roteiros SET coordenador = :c WHERE id = $this->id");
                $abc->bindValue(':c', $this->dados->coordenador, \PDO::PARAM_STR);
                $abc->execute();

                return true;
            } else {
                // Limite de coordenadores alcançado.
                return false;
            }
        } else {
            // Encontrado.
            return true;
        }
    }

    /**
     * Remove coordenador do roteiro.
     * 
     * @param int $coord ID do coordenador que será removido.
     * 
     * @return bool
     */
    public function setCoordenadorRemove(int $coord)
    {
        if($this->dados->coordenador == '') {
            $coordenadores = array();
        } else {
            $coordenadores = json_decode($this->dados->coordenador);
        }

        // verifica se o coordenador já consta.
        $key = array_search($coord, $coordenadores);
        if($key === false) {
            // Não encontrado.
            return true;
        } else {
            // Encontrado. Remove da lista.
            unset($coordenadores[$key]);
            if(empty($coordenadores)) {
                $this->dados->coordenador = '';
            } else {
                $this->dados->coordenador = json_encode($coordenadores);
            }
            

            $abc = $this->pdo->prepare("UPDATE roteiros SET coordenador = :c WHERE id = $this->id");
            $abc->bindValue(':c', $this->dados->coordenador, \PDO::PARAM_STR);
            $abc->execute();

            return true;
        }
    }

    /**
     * Persiste os dados no banco.
     * 
     * @return bool
     */
    public function salvar()
    {
        $recalcularLucro = false;

        // Varre os dados.
        if(empty($this->dadosNovo)) {
            return true;
        } else {
            $sql = '';
            $x = (array)$this->dados; // Converte objeto em array.

            foreach($this->dadosNovo as $key => $val) {
                
                if($x[$key] != $val) {
                    switch($key) {
                        // Array, converte em JSON.
                        case 'despesas':
                        case 'parceiros':
                            $sql .= $key.' = "'.addslashes(\json_encode($val)).'", ';
                        break;

                        case 'obs':
                            $sql .= $key.' = "'.addslashes(trim($val)).'", ';
                        break;


                        // String ou booleano ou inteiro, armazena normal.
                        default:
                            $sql .= $key.' = "'.trim($val).'", ';
                        break;
                    }
                    

                    // Verifica se alguma variável de cálculo de lucro foi alterada. Se sim, calculo do lucro precisa ser refeito.
                    switch($key) {
                        case 'despesas':
                        case 'qtd_rateio':
                        case 'taxa_lucro':
                        case 'qtd_coordenador':
                        case 'passagens':
                            $recalcularLucro = true;
                            break;
                    }
                }
            }
            unset($x);

            $sql = \substr($sql, 0, -2);
        }

        // Refaz cálculo do lucro?
        if($recalcularLucro === true) {
            // Necessário recalcular lucro.
            // Faz o cálculo do lucro previsto, com base nas despesas e taxa de lucro.
            $despesas = 0;
            $dados = array();
            if(!isset($this->dadosNovo['despesas'])) { $dados['despesas'] = $this->dados->despesas; } else { $dados['despesas'] = $this->dadosNovo['despesas']; }
            if(!isset($this->dadosNovo['passagens'])) { $dados['passagens'] = $this->dados->passagens; } else { $dados['passagens'] = $this->dadosNovo['passagens']; }
            if(!isset($this->dadosNovo['qtd_coordenador'])) { $dados['qtd_coordenador'] = $this->dados->qtd_coordenador; } else { $dados['qtd_coordenador'] = $this->dadosNovo['qtd_coordenador']; }
            if(!isset($this->dadosNovo['taxa_lucro'])) { $dados['taxa_lucro'] = $this->dados->taxa_lucro; } else { $dados['taxa_lucro'] = $this->dadosNovo['taxa_lucro']; }
            if(!isset($this->dadosNovo['qtd_rateio'])) { $dados['qtd_rateio'] = $this->dados->qtd_rateio; } else { $dados['qtd_rateio'] = $this->dadosNovo['qtd_rateio']; }
            
            foreach($dados['despesas'] as $d) {
                switch($d->tipo_valor) {
                    case 'total':
                        $despesas = $despesas + (int)$d->valor;
                    break;

                    case 'pessoa':
                        $despesas = $despesas + ( (int)$d->valor * ( (int)$dados['passagens'] + (int)$dados['qtd_coordenador']) );
                    break;

                    case 'dia':
                        $despesas = $despesas + ( (int)$d->valor * (int)$d->dias);
                    break;

                    case 'pessoa_dia':
                        $despesas = $despesas + ( (int)$d->valor * (int)$d->dias * ( (int)$dados['passagens'] + (int)$dados['qtd_coordenador']) );
                    break;
                }
            }
            $lucro = [
                'lucroRateio' => 0,
                'lucroPoltronaLivre' => 0,
                'lucroTotal' => 0
            ];

            // Cálculo dos lucros sobre rateio.
            $lucro['lucroRateio'] = $despesas * ($dados['taxa_lucro']/100); // Ex.: 30% x 2000 = 600.

            // Cálculo do lucro sobre poltronas livres.
            $x = \ceil(($despesas + $lucro['lucroRateio']) / $dados['qtd_rateio']); // Valor p/ cliente = Valor total (despesa + lucro) / quantidade de clientes do rateio.
            $lucro['lucroPoltronaLivre'] = $x * ($dados['passagens'] - $dados['qtd_rateio']); // [valor p/ cliente] * [poltronas livres] = [lucro poltronas livres]

            // Cálculo do lucro total previsto.
            $lucro['lucroTotal'] = $lucro['lucroRateio'] + $lucro['lucroPoltronaLivre'];

            // Adiciona o lucro na query SQL
            $sql .= ', lucro_previsto = "'.addslashes(\json_encode($lucro)).'"';

            unset($dados);
        }


        // Query de atualização
        $sql .= ', atualizado_em = NOW()';
        $query = "UPDATE roteiros SET $sql WHERE id = $this->id";

        try {
            $abc = $this->pdo->query($query);

            $abc->execute();

            $d_i = new \DateTime($this->dados->data_ini);
            $d_f = new \DateTime($this->dados->data_fim);
            /**
             * LOG
             */
            $log = new LOG();
            $log->novo('Atualizou alguns dados do ROTEIRO <i class="text-uppercase">'.$this->dados->nome.' ('. $d_i->format('d/m/Y') .' a '. $d_f->format('d/m/Y') .')</i>.', $_SESSION['auth']['id'], 1);
            /**
             * ./LOG
             */

            return true;
        } catch(PDOException $e) {
            \error_log($e->getMessage(), 1, $this->system->desenvolvedor[0]);
            \error_log($e->getMessage(), 0);
            return false;
        }

    }

    /**
     * Apaga roteiro via soft-delete.
     * 
     * @return bool
     */
    public function apagar()
    {
        if($this->dados->deletado_em == null) {
            try {
                $abc = $this->pdo->query('UPDATE roteiros SET deletado_em = NOW(), deletado_por = '.$_SESSION['auth']['id'].' WHERE id = '.$this->id);

                $d_i = new \DateTime($this->dados->data_ini);
                $d_f = new \DateTime($this->dados->data_fim);
                /**
                 * LOG
                 */
                $log = new LOG();
                $log->novo('Enviou para lixeira o ROTEIRO <i class="text-uppercase">'.$this->dados->nome.' ('. $d_i->format('d/m/Y') .' a '. $d_f->format('d/m/Y') .')</i>.', $_SESSION['auth']['id'], 1);
                /**
                 * ./LOG
                 */
                return true;
            } catch(PDOException $e) {
                \error_log($e->getMessage(), 1, $this->system->desenvolvedor[0]);
                \error_log($e->getMessage(), 0);
                return false;
            }
        } else {
            return true;
        }
    }

    /**
     * Restaura roteiro apagado via soft-delete.
     * 
     * @return bool
     */
    public function restaurar()
    {
        if($this->dados->deletado_em != null) {
            try {
                $abc = $this->pdo->query('UPDATE roteiros SET deletado_em = NULL, deletado_por = NULL WHERE id = '.$this->id);

                $d_i = new \DateTime($this->dados->data_ini);
                $d_f = new \DateTime($this->dados->data_fim);
                /**
                 * LOG
                 */
                $log = new LOG();
                $log->novo('Restaurou da lixeira o ROTEIRO <i class="text-uppercase">'.$this->dados->nome.' ('. $d_i->format('d/m/Y') .' a '. $d_f->format('d/m/Y') .')</i>.', $_SESSION['auth']['id'], 1);
                /**
                 * ./LOG
                 */
                return true;
            } catch(PDOException $e) {
                \error_log($e->getMessage(), 1, $this->system->desenvolvedor[0]);
                \error_log($e->getMessage(), 0);
                return false;
            }
        } else {
            return true;
        }
    }

    /**
     * Apaga roteiro da lixeira. Não é possível excluir o roteiro direto, sem passar pela lixeira!
     * 
     * @return bool
     */
    public function apagarLixeira()
    {
        if($this->dados->deletado_em != null) {
            try {
                $abc = $this->pdo->query('DELETE FROM roteiros WHERE id = '.$this->id);

                $d_i = new \DateTime($this->dados->data_ini);
                $d_f = new \DateTime($this->dados->data_fim);
                /**
                 * LOG
                 */
                $log = new LOG();
                $log->novo('Excluiu definitivament o ROTEIRO <i class="text-uppercase">'.$this->dados->nome.' ('. $d_i->format('d/m/Y') .' a '. $d_f->format('d/m/Y') .')</i>.', $_SESSION['auth']['id'], 1);
                /**
                 * ./LOG
                 */
                return true;
            } catch(PDOException $e) {
                \error_log($e->getMessage(), 1, $this->system->desenvolvedor[0]);
                \error_log($e->getMessage(), 0);
                return false;
            }
        } else {
            return true;
        }
    }

}