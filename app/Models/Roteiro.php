<?php
namespace SGCTUR;
use SGCTUR\LOG;
use SGCTUR\Erro;

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