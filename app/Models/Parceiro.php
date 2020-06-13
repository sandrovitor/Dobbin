<?php
namespace SGCTUR;
use SGCTUR\LOG;
use SGCTUR\Erro;

class Parceiro extends Master
{
    private $id = null;
    private $dados;
    

    public function __construct(int $parceiroID)
    {
        parent::__construct();
        if($parceiroID <= 0) {
            $this->dados = null;
            return false;
        }

        $this->id = $parceiroID;
        $this->loadInfoBD();
    }

    /**
     * Carrega as informações do BD de dados para o objeto.
     */
    private function loadInfoBD() 
    {
        if($this->id != 0) {
            $abc = $this->pdo->query('SELECT * FROM parc_empresa WHERE id = '.$this->id);
            if($abc->rowCount() == 0) {
                $this->dados = null;
                return false;
            } else {
                $reg = $abc->fetch(\PDO::FETCH_OBJ);
                $this->dados = array(
                    'geral' => $reg
                );

                // Financeiro
                $abc = $this->pdo->query('SELECT * FROM  parc_financeiro WHERE empresa_id = '.$this->id);
                if($abc->rowCount() == 0) {
                    $this->dados['financeiro'] = null;
                } else {
                    $this->dados['financeiro'] = $abc->fetchAll(\PDO::FETCH_OBJ);
                }

                // Serviços
                $abc = $this->pdo->query('SELECT * FROM  parc_servico WHERE empresa_id = '.$this->id);
                if($abc->rowCount() == 0) {
                    $this->dados['servico'] = null;
                } else {
                    //$this->dados['servico'] = $abc->fetchAll(\PDO::FETCH_OBJ);
                    $this->dados['servico'] = array();

                    while($reg = $abc->fetch(\PDO::FETCH_OBJ)) {
                        // Remove os slashes.
                        $reg->tarifas = \stripslashes($reg->tarifas);
                        $reg->benef_gratis = \stripslashes($reg->benef_gratis);
                        $reg->benef_pago = \stripslashes($reg->benef_pago);
                        $this->dados['servico'][] = $reg;
                    }
                }

                return true;

            }
        }
        return false;
    }

    /**
     * Retorna os dados da ficha do cliente.
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
     * Retorna serviço do parceiro.
     * 
     * @param int $sid ID do serviço.
     * @return array [success => TRUE|FALSE, mensagem => STRING, servico => OBJECT]
     */
    public function getServico(int $sid)
    {
        $retorno = ['success' => false, 'mensagem' => ''];

        // Busca nos serviços a ID do serviço.
        $key = array_search($sid, array_column($this->dados['servico'], 'id'));

        if($key === FALSE) {
            $retorno['mensagem'] = Erro::getMessage(223);
            return $retorno;
        }

        $retorno['success'] = true;
        $retorno['servico'] = $this->dados['servico'][$key];
        return $retorno;
    }

    /**
     * Retorna dado financeiro do parceiro.
     * 
     * @param int $fid ID do dado financeiro.
     * @return array [success => TRUE|FALSE, mensagem => STRING, financeiro => OBJECT]
     */
    public function getFinanceiro(int $fid)
    {
        $retorno = ['success' => false, 'mensagem' => ''];

        // Busca nos serviços a ID do serviço.
        $key = array_search($fid, array_column($this->dados['financeiro'], 'id'));

        if($key === FALSE) {
            $retorno['mensagem'] = Erro::getMessage(224);
            return $retorno;
        }

        $retorno['success'] = true;
        $retorno['financeiro'] = $this->dados['financeiro'][$key];
        return $retorno;
    }

    /**
     * Define novo serviço do parceiro.
     * 
     * @param array $dados Dados do serviço.
     * 
     * @return bool
     */
    public function setNovoServico(array $dados)
    {
        $tarifas = array();
        if(!empty($dados['tarifario'])) {
            foreach($dados['tarifario'] as $t) {
                $valor = $t->valor;
                if(\strpos($valor, ',') === FALSE) {
                    $valor .= '00';
                } else {
                    $valor = \str_replace(',', '', $valor);
                }

                $tarifas[] = (object)['idade' => $t->idade, 'valor' => $valor];
            }
        }

        $benPagos = array();
        if(!empty($dados['beneficiosPagos'])) {
            foreach($dados['beneficiosPagos'] as $t) {
                $valor = $t->valor;
                if(\strpos($valor, ',') === FALSE) {
                    $valor .= '00';
                } else {
                    $valor = \str_replace(',', '', $valor);
                }

                $benPagos[] = (object)['nome' => $t->nome, 'valor' => $valor];
            }
        }

        

        try{
            $abc = $this->pdo->prepare("INSERT INTO `parc_servico` (`id`, `empresa_id`, `categoria`, `tipo`, `passageiros`, `cidade`, `estado`, `tarifas`, `benef_gratis`, `benef_pago`, `obs_servico`) VALUES (NULL, $this->id, :cat, :tipo, :passageiros, :cidade, :estado, :tarifas, :benef_gratis, :benef_pago, :obs)");
            $abc->bindValue(':cat', $dados['categoria'], \PDO::PARAM_STR);
            
            switch($dados['categoria'])
            {
                case 'Transporte':
                    $abc->bindValue(':tipo', $dados['tipo'], \PDO::PARAM_STR);
                    $abc->bindValue(':passageiros', $dados['passageiros'], \PDO::PARAM_INT);
                    break;

                case 'Hospedagem':
                    $abc->bindValue(':tipo', $dados['tipo'], \PDO::PARAM_STR);
                    $abc->bindValue(':passageiros', NULL, \PDO::PARAM_NULL);
                    break;

                default:
                    $abc->bindValue(':tipo', NULL, \PDO::PARAM_NULL);
                    $abc->bindValue(':passageiros', NULL, \PDO::PARAM_NULL);
            }
            
            
            $abc->bindValue(':cidade', $dados['cidade'], \PDO::PARAM_STR);
            $abc->bindValue(':estado', $dados['estado'], \PDO::PARAM_STR);
            $abc->bindValue(':tarifas', addslashes(json_encode($tarifas)), \PDO::PARAM_STR);
            $abc->bindValue(':benef_gratis', addslashes(json_encode($dados['beneficiosGratis'])), \PDO::PARAM_STR);
            $abc->bindValue(':benef_pago', addslashes(json_encode($benPagos)), \PDO::PARAM_STR);
            $abc->bindValue(':obs', $dados['obs'], \PDO::PARAM_STR);

            $abc->execute();

            /**
             * LOG
             */
            $log = new LOG();
            $log->novo('Adicionou um novo serviço <b class="text-uppercase">'.$dados['categoria'].'</b> ao parceiro <i class="text-uppercase">'.$this->dados['geral']->razao_social.'</i>.', $_SESSION['auth']['id'], 1);
            /**
             * ./LOG
             */
            return true;
        } catch(\PDOException $e) {
            \error_log($e->getMessage(), 1, $this->system->desenvolvedor[0]);
            \error_log($e->getMessage(), 0);
            return false;
        }
    }

    /**
     * Salva alterações no serviço.
     * 
     * @param array $dados Dados do serviço.
     * @param int $servicoID ID do serviço.
     * 
     * @return array [success => TRUE|FALSE, mensagem => STRING]
     */
    public function setSalvarServico(array $dados, int $servicoID)
    {
        $retorno = ['success' => false, 'mensagem' => ''];

        // Busca nos serviços a ID do serviço.
        $key = array_search($servicoID, array_column($this->dados['servico'], 'id'));

        if($key === FALSE) {
            $retorno['mensagem'] = Erro::getMessage(223);
            return $retorno;
        }

        // Faz a alteração no serviço.
        $tarifas = array();
        if(!empty($dados['tarifario'])) {
            foreach($dados['tarifario'] as $t) {
                $valor = $t->valor;
                if(\strpos($valor, ',') === FALSE) {
                    $valor .= '00';
                } else {
                    $valor = \str_replace(',', '', $valor);
                }

                $tarifas[] = (object)['idade' => $t->idade, 'valor' => $valor];
            }
        }

        $benPagos = array();
        if(!empty($dados['beneficiosPagos'])) {
            foreach($dados['beneficiosPagos'] as $t) {
                $valor = $t->valor;
                if(\strpos($valor, ',') === FALSE) {
                    $valor .= '00';
                } else {
                    $valor = \str_replace(',', '', $valor);
                }

                $benPagos[] = (object)['nome' => $t->nome, 'valor' => $valor];
            }
        }

        

        try{
            $abc = $this->pdo->prepare("UPDATE `parc_servico` SET categoria = :cat, tipo = :tipo, passageiros = :passageiros, ".
            "cidade = :cidade, estado = :estado, tarifas = :tarifas, benef_gratis = :benef_gratis, benef_pago = :benef_pago, ".
            "obs_servico = :obs WHERE id = ".$servicoID);
            $abc->bindValue(':cat', $dados['categoria'], \PDO::PARAM_STR);
            
            switch($dados['categoria'])
            {
                case 'Transporte':
                    $abc->bindValue(':tipo', $dados['tipo'], \PDO::PARAM_STR);
                    $abc->bindValue(':passageiros', $dados['passageiros'], \PDO::PARAM_INT);
                    break;

                case 'Hospedagem':
                    $abc->bindValue(':tipo', $dados['tipo'], \PDO::PARAM_STR);
                    $abc->bindValue(':passageiros', NULL, \PDO::PARAM_NULL);
                    break;

                default:
                    $abc->bindValue(':tipo', NULL, \PDO::PARAM_NULL);
                    $abc->bindValue(':passageiros', NULL, \PDO::PARAM_NULL);
            }
            
            
            $abc->bindValue(':cidade', $dados['cidade'], \PDO::PARAM_STR);
            $abc->bindValue(':estado', $dados['estado'], \PDO::PARAM_STR);
            $abc->bindValue(':tarifas', addslashes(json_encode($tarifas)), \PDO::PARAM_STR);
            $abc->bindValue(':benef_gratis', addslashes(json_encode($dados['beneficiosGratis'])), \PDO::PARAM_STR);
            $abc->bindValue(':benef_pago', addslashes(json_encode($benPagos)), \PDO::PARAM_STR);
            $abc->bindValue(':obs', $dados['obs'], \PDO::PARAM_STR);

            $abc->execute();

            /**
             * LOG
             */
            $log = new LOG();
            $log->novo('Alterou dados do serviço <b class="text-uppercase">'.$dados['categoria'].'</b> do parceiro <i class="text-uppercase">'.$this->dados['geral']->razao_social.'</i>.', $_SESSION['auth']['id'], 2);
            /**
             * ./LOG
             */

            $retorno['success'] = true;
            return $retorno;
        } catch(\PDOException $e) {
            \error_log($e->getMessage(), 1, $this->system->desenvolvedor[0]);
            \error_log($e->getMessage(), 0);
            
            $retorno['mensagem'] = Erro::getMessage(70);
            return $retorno;
        }
    }

    /**
     * Define novo financeiro do parceiro.
     * 
     * @param array $dados Dados do financeiro.
     * 
     * @return bool
     */
    public function setNovoFinanceiro(array $dados)
    {
        try {
            $abc = $this->pdo->prepare('INSERT INTO `parc_financeiro` (`id`, `empresa_id`, `banco`, `agencia`, `agencia_dv`, `conta`, `conta_dv`, `tipo_conta`, `favorecido`, `obs_financeiro`) '.
            'VALUES (NULL, '.$this->id.', :banco, :agencia, :agenciadv, :conta, :contadv, :tipo_conta, :favorecido, :obs)');

            $abc->bindValue(':banco', $dados['banco'], \PDO::PARAM_STR);
            $abc->bindValue(':agencia', $dados['agencia'], \PDO::PARAM_STR);
            $abc->bindValue(':agenciadv', $dados['agencia_dv'], \PDO::PARAM_STR);
            $abc->bindValue(':conta', $dados['conta'], \PDO::PARAM_STR);
            $abc->bindValue(':contadv', $dados['conta_dv'], \PDO::PARAM_STR);
            $abc->bindValue(':tipo_conta', $dados['tipo_conta'], \PDO::PARAM_STR);
            $abc->bindValue(':favorecido', $dados['favorecido'], \PDO::PARAM_STR);
            $abc->bindValue(':obs', \addslashes($dados['obs_financeiro']), \PDO::PARAM_STR);

            $abc->execute();
            /**
             * LOG
             */
            $log = new LOG();
            $log->novo('Adicionou um novo dado financeiro ao parceiro <i class="text-uppercase">'.$this->dados['geral']->razao_social.'</i>.', $_SESSION['auth']['id'], 2);
            /**
             * ./LOG
             */

            return true;
        } catch(\PDOException $e)
        {
            \error_log($e->getMessage(), 1, $this->system->desenvolvedor[0]);
            \error_log($e->getMessage(), 0);
            return false;
        }
    }


    /**
     * Salva alterações no financeiro.
     * 
     * @param array $dados Dados financeiros.
     * @param int $finID ID do registo financeiro.
     * 
     * @return array [success => TRUE|FALSE, mensagem => STRING]
     */
    public function setSalvarFinanceiro(array $dados, int $finID)
    {
        $retorno = ['success' => false, 'mensagem' => ''];

        // Busca no financeiro a ID do registro financeiro.
        $key = array_search($finID, array_column($this->dados['financeiro'], 'id'));

        if($key === FALSE) {
            $retorno['mensagem'] = Erro::getMessage(224);
            return $retorno;
        }

        try {
            $abc = $this->pdo->prepare('INSERT INTO `parc_financeiro` (`id`, `empresa_id`, `banco`, `agencia`, `agencia_dv`, `conta`, `conta_dv`, `tipo_conta`, `favorecido`, `obs_financeiro`) '.
            'VALUES (NULL, '.$this->id.', :banco, :agencia, :agenciadv, :conta, :contadv, :tipo_conta, :favorecido, :obs)');

            $abc = $this->pdo->prepare("UPDATE parc_financeiro SET banco = :banco, agencia = :agencia, agencia_dv = :agenciadv, conta = :conta, conta_dv = :contadv, tipo_conta = :tipo_conta, ".
            "favorecido = :favorecido, obs_financeiro = :obs WHERE id = $finID AND empresa_id = $this->id");

            $abc->bindValue(':banco', $dados['banco'], \PDO::PARAM_STR);
            $abc->bindValue(':agencia', $dados['agencia'], \PDO::PARAM_STR);
            $abc->bindValue(':agenciadv', $dados['agencia_dv'], \PDO::PARAM_STR);
            $abc->bindValue(':conta', $dados['conta'], \PDO::PARAM_STR);
            $abc->bindValue(':contadv', $dados['conta_dv'], \PDO::PARAM_STR);
            $abc->bindValue(':tipo_conta', $dados['tipo_conta'], \PDO::PARAM_STR);
            $abc->bindValue(':favorecido', $dados['favorecido'], \PDO::PARAM_STR);
            $abc->bindValue(':obs', \addslashes($dados['obs_financeiro']), \PDO::PARAM_STR);

            $abc->execute();
            /**
             * LOG
             */
            $log = new LOG();
            $log->novo('Alterou dados financeiros do parceiro <i class="text-uppercase">'.$this->dados['geral']->razao_social.'</i>.', $_SESSION['auth']['id'], 2);
            /**
             * ./LOG
             */

            $retorno['success'] = true;
            return $retorno;
        } catch(\PDOException $e)
        {
            \error_log($e->getMessage(), 1, $this->system->desenvolvedor[0]);
            \error_log($e->getMessage(), 0);
            $retorno['mensagem'] = Erro::getMessage(70);
            return $retorno;
        }
    }

    /**
     * Exclui serviço da base de dados.
     * 
     * @param int $sid ID do serviço
     * 
     * @return array [success => TRUE|FALSE, mensagem => STRING]
     */
    public function setDeleteServico(int $sid)
    {
        $retorno = ['success' => false, 'mensagem' => ''];

        // Busca a ID do serviço no registro de serviços.
        $key = array_search($sid, array_column($this->dados['servico'], 'id'));

        if($key === FALSE) {
            $retorno['mensagem'] = Erro::getMessage(224);
            return $retorno;
        }

        $serv = $this->dados['servico'][$key];
        // Exclui
        try {
            $abc = $this->pdo->query('DELETE FROM parc_servico WHERE parc_servico.empresa_id = '.$this->id.' AND parc_servico.id = '.$sid);
            $retorno['success'] = true;

            /**
             * LOG
             */
            $log = new LOG();
            $log->novo('Removeu serviço <b class="text-uppercase">'.$serv->categoria. ($serv->tipo == null ? '' : " [$serv->tipo] ").'</b> do parceiro <i class="text-uppercase">'.$this->dados['geral']->razao_social.'</i>.', $_SESSION['auth']['id'], 3);
            /**
             * ./LOG
             */

            return $retorno;
        } catch(\PDOException $e) {
            \error_log($e->getMessage(), 1, $this->system->desenvolvedor[0]);
            \error_log($e->getMessage(), 0);

            $retorno['mensagem'] = Erro::getMessage(70);
            return $retorno;
        }
        
    }

    /**
     * Exclui dado financeiro da da base de dados.
     * 
     * @param int $sid ID do dado financeiro
     * 
     * @return array [success => TRUE|FALSE, mensagem => STRING]
     */
    public function setDeleteFinanceiro(int $finID)
    {
        $retorno = ['success' => false, 'mensagem' => ''];

        // Busca no financeiro a ID do registro financeiro.
        $key = array_search($finID, array_column($this->dados['financeiro'], 'id'));

        if($key === FALSE) {
            $retorno['mensagem'] = Erro::getMessage(224);
            return $retorno;
        }

        $finan = $this->dados['financeiro'][$key];

        // Exclui
        try {
            $abc = $this->pdo->query('DELETE FROM parc_financeiro WHERE parc_financeiro.empresa_id = '.$this->id.' AND parc_financeiro.id = '.$finID);
            $retorno['success'] = true;

            /**
             * LOG
             */
            $log = new LOG();
            $log->novo('Removeu dado financeiro do parceiro <i class="text-uppercase">'.$this->dados['geral']->razao_social.'</i>.', $_SESSION['auth']['id'], 3);
            /**
             * ./LOG
             */
            
            return $retorno;
        } catch(\PDOException $e) {
            \error_log($e->getMessage(), 1, $this->system->desenvolvedor[0]);
            \error_log($e->getMessage(), 0);

            $retorno['mensagem'] = Erro::getMessage(70);
            return $retorno;
        }
    }

    /**
     * Adiciona uma nova entrada no registro.
     * 
     * @param string $detalhes Detalhes a serem inseridos.
     * @param string $etapa Etapa da negociação: CONTATO, PEDIDO DE BLOQUEIO, DESISTÊNCIA...
     * 
     * @return bool
     */
    public function setHistoricoNovo(string $detalhes, string $etapa = 'CONTATO')
    {
        try{
            $abc = $this->pdo->prepare("INSERT INTO `historico_negoc` (`id`, `empresa_id`, `roteiro_id`, `etapa`, `detalhes`, `criado_por`, `criado_em`) VALUES (NULL, $this->id, 0, :etapa, :det, :autor, NOW())");
            $abc->bindValue(':etapa', $etapa, \PDO::PARAM_STR);
            $abc->bindValue(':det', addslashes($detalhes), \PDO::PARAM_STR);
            $abc->bindValue(':autor', $_SESSION['auth']['id'], \PDO::PARAM_INT);

            $abc->execute();
            /**
             * LOG
             */
            $log = new LOG();
            $log->novo('Adicionou um registro no HISTÓRICO DE NEGOCIAÇÕES do parceiro <i class="text-uppercase">'.$this->dados['geral']->razao_social.'</i>.', $_SESSION['auth']['id'], 1);
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
     * Recupera entradas no histórico de negociações.
     * @param int $qtd Limite de linhas retornadas.
     * @param int $start Linha de inicio.
     * @param int $roteiroID ID do roteiro. Padrão é 0 (todos os históricos do parceiro).
     * 
     * @return array
     */
    public function getHistorico(int $qtd = 20, int $start = 0, int $roteiroID = 0)
    {
        if($qtd <= 0) {
            $qtd = 1;
        }

        if($start < 0) {
            $start = 0;
        }

        if($roteiroID !== 0) {
            $pesq_roteiro = ' AND historico_negoc.roteiro_id = '.$roteiroID;
        } else {
            $pesq_roteiro = '';
        }

        

        $limite = "LIMIT $start, $qtd";


        $abc = $this->pdo->query("SELECT historico_negoc.*, CONCAT(login.nome, ' ', login.sobrenome) as usuario_nome, roteiros.nome as roteiro_nome, roteiros.data_ini, roteiros.data_fim FROM `historico_negoc` LEFT JOIN login ON historico_negoc.criado_por = login.id LEFT JOIN roteiros ON historico_negoc.roteiro_id = roteiros.id ".
        "WHERE historico_negoc.empresa_id = $this->id $pesq_roteiro ORDER BY historico_negoc.criado_em DESC $limite ");
        if($abc->rowCount() == 0) {
            return array();
        } else {
            $registros = array();
            while($reg = $abc->fetch(\PDO::FETCH_OBJ)) {
                $reg->detalhes = \stripslashes($reg->detalhes);
                
                // Se registro sofreu atualização, retorna usuário.
                if($reg->atualizado_por != null) {
                    $def = $this->pdo->query("SELECT nome, sobrenome FROM login WHERE id = $reg->atualizado_por");
                    if($def->rowCount() == 0) {
                        $reg->atualizado_por_nome = 'Usuário removido';
                    } else {
                        $lin = $def->fetch(\PDO::FETCH_OBJ);
                        $reg->atualizado_por_nome = $lin->nome .' '. $lin->sobrenome;
                    }
                }

                array_push($registros, $reg);
            }

            return $registros;
        }
    }

    /**
     * Edita entrada do histórico de negociações.
     * @param int $historicoID ID do registro no histórico.
     * @param string $etapa Etapa no histórico.
     * @param string $detalhes Detalhes do histórico de negociações.
     * 
     * @return array [success => TRUE|FALSE, mensagem => STRING]
     */
    public function setHistoricoEdita(int $historicoID, string $etapa, string $detalhes)
    {
        $retorno = ['success' => false, 'mensagem' => ''];
        if($historicoID <= 0) {
            $retorno['mensagem'] = Erro::getMessage(100);
            return $retorno;
        }

        $abc = $this->pdo->query("SELECT * FROM historico_negoc WHERE empresa_id = $this->id AND id = ".$historicoID);
        if($abc->rowCount() == 0) {
            $retorno['mensagem'] = Erro::getMessage(225);
            return $retorno;
        }

        // Atualiza registro.
        try {
            $abc = $this->pdo->prepare("UPDATE historico_negoc SET etapa = :etapa, detalhes = :det, atualizado_por = ".$_SESSION['auth']['id'].", atualizado_em = NOW() WHERE id = :id AND empresa_id = $this->id");
            $abc->bindValue(':etapa', $etapa, \PDO::PARAM_STR);
            $abc->bindValue(':det', $detalhes, \PDO::PARAM_STR);
            $abc->bindValue(':id', $historicoID, \PDO::PARAM_INT);

            $abc->execute();

            /**
             * LOG
             */
            $log = new LOG();
            $log->novo('editou um registro no HISTÓRICO DE NEGOCIAÇÕES do parceiro <i class="text-uppercase">'.$this->dados['geral']->razao_social.'</i>.', $_SESSION['auth']['id'], 1);
            /**
             * ./LOG
             */
            $retorno['success'] = true;
            return $retorno;
        } catch(PDOException $e) {
            \error_log($e->getMessage(), 1, $this->system->desenvolvedor[0]);
            \error_log($e->getMessage(), 0);
            $retorno['mensagem'] = Erro::getMessage(70);
            return $retorno;
        }
    }

    /**
     * Remove entrada do histórico de negociações.
     * @param int $historicoID ID do registro no histórico.
     * 
     * @return array [success => TRUE|FALSE, mensagem => STRING]
     */
    public function setDeleteHistorico(int $historicoID)
    {
        $retorno = ['success' => false, 'mensagem' => ''];
        $abc = $this->pdo->query("SELECT * FROM historico_negoc WHERE empresa_id = $this->id AND id = $historicoID");
        if($abc->rowCount() == 0) {
            $retorno['success'] = true;
            return $retorno;
        } else {
            try {
                // Exclui registro.
                $abc = $this->pdo->query("DELETE FROM historico_negoc WHERE empresa_id = $this->id AND id = $historicoID");
                $retorno['success'] = true;
                /**
                 * LOG
                 */
                $log = new LOG();
                $log->novo('Removeu um registro no HISTÓRICO DE NEGOCIAÇÕES do parceiro <i class="text-uppercase">'.$this->dados['geral']->razao_social.'</i>.', $_SESSION['auth']['id'], 1);
                /**
                 * ./LOG
                 */

                return $retorno;
            } catch(PDOException $e) {
                \error_log($e->getMessage(), 1, $this->system->desenvolvedor[0]);
                \error_log($e->getMessage(), 0);
                $retorno['mensagem'] = Erro::getMessage(70);
                return $retorno;
            }
        }
    }
}