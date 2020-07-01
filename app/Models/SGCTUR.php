<?php
namespace SGCTUR;
use SGCTUR\LOG, SGCTUR\Erro, SGCTUR\Roteiro;
use \stdClass as stdClass;

class SGCTUR extends Master 
{
    private $limites = [
        'clientes' => 300000,   // 300 mil
        'parceiros' => 10000,   // 10 mil
        'roteiros' => 50000,    // 50 mil
        'vendas' => 500000,     // 500 mil
        'usuarios' => 250,      // 250
    ];
    const ORDER_ASC = 1;
    const ORDER_DESC = 0;

    public function __construct()
    {
        parent::__construct();

    }

    /**
     * Autentica usuário.
     * 
     * @param string $usuario Pode ser o nome de usuário ou e-mail.
     * @param string $senha Senha para login.
     * 
     * @return array [success => TRUE|FALSE, mensagem => STRING]
     */
    public function loginAuth(string $usuario, string $senha)
    {
        $retorno = array(
            'success' => false,
            'mensagem' => ''
        );

        $sql = 'SELECT * FROM login WHERE ';

        if(\strpos($usuario, '@') !== false) {
            $sql .= 'email = :u';
        } else {
            $sql .= 'usuario = :u';
        }
        
        $abc = $this->pdo->prepare($sql);
        $abc->bindValue(':u', $usuario, \PDO::PARAM_STR);
        
        try{
            $abc->execute();
        }catch(\PDOException $e) {
            $retorno['mensagem'] = Erro::getMessage(70);
            \error_log($e->getMessage(), 1, $this->system->desenvolvedor[0]);
            \error_log($e->getMessage(), 0);
            return $retorno;
        }

        if($abc->rowCount() == 0) {
            $retorno['mensagem'] = Erro::getMessage(4);
            return $retorno;
        } else {
            // Valida usuário.
            $reg = $abc->fetch(\PDO::FETCH_OBJ);

            if($reg->tentativas >= 3) {
                $retorno['mensagem'] = Erro::getMessage(5);
                return $retorno;
            }

            if(\password_verify($senha, $reg->senha)) {
                // Passou
                $_SESSION['auth']['status'] = TRUE;
                $_SESSION['auth']['datetime'] = new \DateTime();
                $_SESSION['auth']['id'] = $reg->id;

                $this->atualizaSession();

                // Reseta as tentativas e o último LOGIN.
                $this->pdo->query('UPDATE login SET tentativas = 0, logado_em = NOW() WHERE id = '.$reg->id);

                $retorno['success'] = TRUE;
                return $retorno;
            } else {
                $this->pdo->query('UPDATE login SET tentativas = (tentativas + 1) WHERE id = '.$reg->id);
                $retorno['mensagem'] = Erro::getMessage(6);
                return $retorno;
            }
        }


    }

    /**
     * Atualiza os dados da sessão
     * 
     * @return bool Em caso de falha, recomendado direcionar para tela de login.
     */
    public function atualizaSession()
    {
        $abc = $this->pdo->query('SELECT * FROM login WHERE id = '.(int)$_SESSION['auth']['id']);
        if($abc->rowCount() == 0) {
            $_SESSION['auth']['status'] = false;
            return false;
        } else {
            $reg = $abc->fetch(\PDO::FETCH_OBJ);

            $_SESSION['auth']['nome'] = $reg->nome;
            $_SESSION['auth']['sobrenome'] = $reg->sobrenome;
            $_SESSION['auth']['email'] = $reg->email;
            $_SESSION['auth']['usuario'] = $reg->usuario;
            $_SESSION['auth']['nivel'] = $reg->nivel;
            $_SESSION['auth']['avatar'] = $reg->avatar;

            // Horário dos dados desta sessão.
            // A cada intervalo, a sessão é atualizada com dados do BD.
            $_SESSION['auth']['hora_dados'] = date('Y-m-d H:i:s');

            unset($reg);

            return true;
        }
    }

    /**
     * Retorna o consumo do sistema e seus limites.
     * 
     * @return array
     */
    public function getSistemaConsumo()
    {
        $retorno = array('consumo' => array(), 'limite' => $this->limites);
        // Clientes
        $abc = $this->pdo->query('SELECT COUNT(id) as total FROM clientes WHERE 1');
        $reg = $abc->fetch(\PDO::FETCH_OBJ);
        $retorno['consumo']['clientes'] = (int)$reg->total;

        // Parceiros
        $abc = $this->pdo->query('SELECT COUNT(id) as total FROM parc_empresa WHERE 1');
        $reg = $abc->fetch(\PDO::FETCH_OBJ);
        $retorno['consumo']['parceiros'] = (int)$reg->total;

        // Roteiros
        $abc = $this->pdo->query('SELECT COUNT(id) as total FROM roteiros WHERE 1');
        $reg = $abc->fetch(\PDO::FETCH_OBJ);
        $retorno['consumo']['roteiros'] = (int)$reg->total;

        // Vendas
        $retorno['consumo']['vendas'] = 0;

        // Usuários
        $abc = $this->pdo->query('SELECT COUNT(id) as total FROM login WHERE 1');
        $reg = $abc->fetch(\PDO::FETCH_OBJ);
        $retorno['consumo']['usuarios'] = (int)$reg->total;

        return $retorno;
    }

    /**
     * Retorna total de clientes na plataforma.
     * 
     * @return int
     */
    public function getClientesTotal()
    {
        $abc = $this->pdo->query('SELECT COUNT(id) as total FROM clientes WHERE deletado_em IS NULL');
        $x = $abc->fetch(\PDO::FETCH_OBJ);
        return (int)$x->total;
    }

    /**
     * Retorna total de coordenadores na plataforma.
     * 
     * @return int
     */
    public function getCoordenadoresTotal()
    {
        $abc = $this->pdo->query('SELECT COUNT(id) as total FROM coordenadores WHERE deletado_em IS NULL');
        $x = $abc->fetch(\PDO::FETCH_OBJ);
        return (int)$x->total;
    }

    /**
     * Retorna total de usuarios na plataforma.
     * 
     * @return int
     */
    public function getUsuariosTotal()
    {
        $abc = $this->pdo->query('SELECT COUNT(id) as total FROM login WHERE 1');
        $x = $abc->fetch(\PDO::FETCH_OBJ);
        return (int)$x->total;
    }

    /**
     * Retorna total de roteiros na plataforma.
     * 
     * @return int
     */
    public function getRoteirosTotal()
    {
        $abc = $this->pdo->query('SELECT COUNT(id) as total FROM roteiros WHERE deletado_em IS NULL');
        $x = $abc->fetch(\PDO::FETCH_OBJ);
        return (int)$x->total;
    }

    /**
     * Retorna lista de clientes na plataforma.
     * 
     * @param int $inicio Onde o ponteiro de busca deve iniciar.
     * @param int $qtd Total de registros que a busca deve retornar.
     * @param array $ordem_por Campo a ser ordenado. Ex.: nome, email, cidade, estado, criado_em, titular...
     * @param const Ordenar ASCENDENTE ou DESCENDENTE.
     * 
     * @return array [success => TRUE|FALSE, mensagem => STRING, clientes => ARRAY]
     */
    public function getClientesLista(int $inicio = 0, int $qtd = 20, array $ordem_por = ['criado_em'], $ordem = SGCTUR::ORDER_ASC)
    {
        $retorno = array(
            'success' => false,
            'mensagem' => ''
        );

        if($qtd == 0) {
            $limit = '';
        } else {
            $limit = 'LIMIT '.$inicio.', '.$qtd;
        }

        $str_ordem = '';
        if(empty($ordem_por)) {
            $retorno['mensagem'] = Erro::getMessage(9);
            return $retorno;
        }
        foreach($ordem_por as $o) {
            switch($o) {
                case 'nome':
                case 'email':
                case 'cidade':
                case 'estado':
                case 'criado_em':
                case 'titular':
                    $str_ordem .= $o;

                    if($ordem == 1) {
                        // ASCENDENTE
                        $str_ordem .= ' ASC, ';
                    } else {
                        // DESCENDENTE
                        $str_ordem .= ' DESC, ';
                    }
                break;
            }

            
        }

        $str_ordem = substr($str_ordem, 0, -2);

        $abc = $this->pdo->prepare('SELECT * FROM clientes WHERE deletado_em IS NULL ORDER BY '.$str_ordem.' '.$limit);
        //$abc->bindValue(':ordem', $ordem == 1 ? 'ASC' : 'DESC', \PDO::PARAM_STR);
        $abc->bindValue(':ini', $inicio, \PDO::PARAM_INT);
        $abc->bindValue(':qtd', $qtd, \PDO::PARAM_INT);

        try {
            $abc->execute();
        } catch(\PDOException $e) {
            $retorno['mensagem'] = Erro::getMessage(70);
            \error_log($e->getMessage(), 1, $this->system->desenvolvedor[0]);
            \error_log($e->getMessage(), 0);

            return $retorno;
        }

        
        $retorno['success'] = true;
        if($abc->rowCount() == 0) {
            $retorno['clientes'] = array();
        } else {
            $retorno['clientes'] = $abc->fetchAll(\PDO::FETCH_OBJ);
        }

        return $retorno;
    }

    /**
     * Retorna lista de coordenadores na plataforma.
     * 
     * @param int $inicio Onde o ponteiro de busca deve iniciar.
     * @param int $qtd Total de registros que a busca deve retornar.
     * @param array $ordem_por Campo a ser ordenado. Ex.: nome, email, cidade, estado, criado_em...
     * @param const Ordenar ASCENDENTE ou DESCENDENTE.
     * 
     * @return array [success => TRUE|FALSE, mensagem => STRING, clientes => ARRAY]
     */
    public function getCoordenadoresLista(int $inicio = 0, int $qtd = 20, array $ordem_por = ['criado_em'], $ordem = SGCTUR::ORDER_ASC)
    {
        $retorno = array(
            'success' => false,
            'mensagem' => ''
        );

        if($qtd == 0) {
            $limit = '';
        } else {
            $limit = 'LIMIT '.$inicio.', '.$qtd;
        }

        $str_ordem = '';
        if(empty($ordem_por)) {
            $retorno['mensagem'] = Erro::getMessage(9);
            return $retorno;
        }
        foreach($ordem_por as $o) {
            switch($o) {
                case 'nome':
                case 'email':
                case 'cidade':
                case 'estado':
                case 'criado_em':
                    $str_ordem .= $o;

                    if($ordem == 1) {
                        // ASCENDENTE
                        $str_ordem .= ' ASC, ';
                    } else {
                        // DESCENDENTE
                        $str_ordem .= ' DESC, ';
                    }
                break;
            }

            
        }

        $str_ordem = substr($str_ordem, 0, -2);

        $abc = $this->pdo->prepare('SELECT * FROM coordenadores WHERE deletado_em IS NULL ORDER BY '.$str_ordem.' '.$limit);

        $abc->bindValue(':ini', $inicio, \PDO::PARAM_INT);
        $abc->bindValue(':qtd', $qtd, \PDO::PARAM_INT);

        try {
            $abc->execute();
        } catch(\PDOException $e) {
            $retorno['mensagem'] = Erro::getMessage(70);
            \error_log($e->getMessage(), 1, $this->system->desenvolvedor[0]);
            \error_log($e->getMessage(), 0);

            return $retorno;
        }

        
        $retorno['success'] = true;
        if($abc->rowCount() == 0) {
            $retorno['coordenadores'] = array();
        } else {
            $retorno['coordenadores'] = $abc->fetchAll(\PDO::FETCH_OBJ);
        }

        return $retorno;
    }

    /**
     * Faz a busca de uma string em NOME, EMAIL, CIDADE, ENDEREÇO, COMPLEMENTO,
     * PONTO DE REFERÊNCIA e BAIRRO. (Não precisa enviar ID no construtor).
     * 
     * @param string $busca String para consulta.
     * 
     * @return array [success => TRUE|FALSE, mensagem => STRING, clientes => ARRAY]
     */
    public function getClientesBusca(string $busca = '')
    {
        $retorno = array(
            'success' => false,
            'mensagem' => ''
        );

        if(trim($busca) == '') {
            $abc= $this->pdo->query('SELECT * FROM clientes WHERE deletado_em IS NULL ORDER BY nome ASC');
        } else {
            $abc = $this->pdo->prepare('SELECT * FROM clientes WHERE (nome LIKE :b1 OR email LIKE :b2 OR cidade LIKE :b3 OR endereco LIKE :b4 '.
            'OR complemento LIKE :b5 OR ponto_referencia LIKE :b6 OR bairro LIKE :b7) AND deletado_em IS NULL ORDER BY nome ASC');
            $abc->bindValue(':b1', '%'.trim($busca).'%', \PDO::PARAM_STR);
            $abc->bindValue(':b2', '%'.trim($busca).'%', \PDO::PARAM_STR);
            $abc->bindValue(':b3', '%'.trim($busca).'%', \PDO::PARAM_STR);
            $abc->bindValue(':b4', '%'.trim($busca).'%', \PDO::PARAM_STR);
            $abc->bindValue(':b5', '%'.trim($busca).'%', \PDO::PARAM_STR);
            $abc->bindValue(':b6', '%'.trim($busca).'%', \PDO::PARAM_STR);
            $abc->bindValue(':b7', '%'.trim($busca).'%', \PDO::PARAM_STR);

            try {
                $abc->execute();
            } catch(\PDOException $e) {
                $retorno['mensagem'] = Erro::getMessage(70);
                \error_log($e->getMessage(), 1, $this->system->desenvolvedor[0]);
                \error_log($e->getMessage(), 0);
                return $retorno;
            }
        }

        $retorno['success'] = true;
        if($abc->rowCount() == 0) {
            $retorno['clientes'] = array();
        } else if($abc->rowCount() <= 200)  {
            $retorno['clientes'] = $abc->fetchAll(\PDO::FETCH_OBJ);
        } else {
            $retorno['clientes'] = array();
            $retorno['mensagem'] = 'Total de '.$abc->rowCount().' registros encontrados. Mostrando os primeiros 200 registros. Tente refinar a busca.';
            for($i = 0; $i < 200; $i++) {
                array_push($retorno['clientes'], $abc->fetch(\PDO::FETCH_OBJ));
            }
        }

        return $retorno;
    }

    /**
     * Faz a busca de uma string em NOME, EMAIL, CIDADE, ENDEREÇO, COMPLEMENTO,
     * PONTO DE REFERÊNCIA e BAIRRO. (Não precisa enviar ID no construtor).
     * 
     * @param string $busca String para consulta.
     * 
     * @return array [success => TRUE|FALSE, mensagem => STRING, coordenadores => ARRAY]
     */
    public function getCoordenadoresBusca(string $busca = '')
    {
        $retorno = array(
            'success' => false,
            'mensagem' => ''
        );

        if(trim($busca) == '') {
            $abc= $this->pdo->query('SELECT * FROM coordenadores WHERE deletado_em IS NULL ORDER BY nome ASC');
        } else {
            $abc = $this->pdo->prepare('SELECT * FROM coordenadores WHERE (nome LIKE :b1 OR email LIKE :b2 OR cidade LIKE :b3 OR endereco LIKE :b4 '.
            'OR complemento LIKE :b5 OR ponto_referencia LIKE :b6 OR bairro LIKE :b7) AND deletado_em IS NULL ORDER BY nome ASC');
            $abc->bindValue(':b1', '%'.trim($busca).'%', \PDO::PARAM_STR);
            $abc->bindValue(':b2', '%'.trim($busca).'%', \PDO::PARAM_STR);
            $abc->bindValue(':b3', '%'.trim($busca).'%', \PDO::PARAM_STR);
            $abc->bindValue(':b4', '%'.trim($busca).'%', \PDO::PARAM_STR);
            $abc->bindValue(':b5', '%'.trim($busca).'%', \PDO::PARAM_STR);
            $abc->bindValue(':b6', '%'.trim($busca).'%', \PDO::PARAM_STR);
            $abc->bindValue(':b7', '%'.trim($busca).'%', \PDO::PARAM_STR);

            try {
                $abc->execute();
            } catch(\PDOException $e) {
                $retorno['mensagem'] = Erro::getMessage(70);
                \error_log($e->getMessage(), 1, $this->system->desenvolvedor[0]);
                \error_log($e->getMessage(), 0);
                return $retorno;
            }
        }

        $retorno['success'] = true;
        if($abc->rowCount() == 0) {
            $retorno['coordenadores'] = array();
        } else if($abc->rowCount() <= 200)  {
            $retorno['coordenadores'] = $abc->fetchAll(\PDO::FETCH_OBJ);
        } else {
            $retorno['coordenadores'] = array();
            $retorno['mensagem'] = 'Total de '.$abc->rowCount().' registros encontrados. Mostrando os primeiros 200 registros. Tente refinar a busca.';
            for($i = 0; $i < 200; $i++) {
                array_push($retorno['coordenadores'], $abc->fetch(\PDO::FETCH_OBJ));
            }
        }

        return $retorno;
    }

    /**
     * Faz a busca de uma string em NOME, DATA_INI, DATA_FIM. (Não precisa enviar ID no construtor).
     * 
     * @param string $busca String para consulta.
     * 
     * @return array [success => TRUE|FALSE, mensagem => STRING, roteiros => ARRAY]
     */
    public function getRoteirosBusca(string $busca = '')
    {
        $retorno = array(
            'success' => false,
            'mensagem' => ''
        );

        if(trim($busca) == '') {
            $abc= $this->pdo->query('SELECT id FROM roteiros WHERE deletado_em IS NULL ORDER BY nome ASC, data_ini DESC, data_fim DESC');
        } else {
            $abc = $this->pdo->prepare('SELECT id FROM roteiros WHERE (nome LIKE :b1 OR data_ini LIKE :b2 OR data_fim LIKE :b3) AND deletado_em IS NULL ORDER BY nome ASC, data_ini DESC, data_fim DESC');
            $abc->bindValue(':b1', '%'.trim($busca).'%', \PDO::PARAM_STR);
            $abc->bindValue(':b2', '%'.trim($busca).'%', \PDO::PARAM_STR);
            $abc->bindValue(':b3', '%'.trim($busca).'%', \PDO::PARAM_STR);

            try {
                $abc->execute();
            } catch(\PDOException $e) {
                $retorno['mensagem'] = Erro::getMessage(70);
                \error_log($e->getMessage(), 1, $this->system->desenvolvedor[0]);
                \error_log($e->getMessage(), 0);
                return $retorno;
            }
        }

        $retorno['success'] = true;
        $retorno['roteiros'] = array();

        if($abc->rowCount() == 0) {
            $retorno['roteiros'] = array();
        } else if($abc->rowCount() <= 200)  {
            $tmp = $abc->fetchAll(\PDO::FETCH_OBJ);
            // Captura informações dos roteiros no BANCO DE DADOS
            foreach($tmp as $t) {
                $roteiro = new Roteiro($t->id);
                array_push($retorno['roteiros'], $roteiro->getDados());
            }

            unset($tmp, $t);
            
        } else {
            $retorno['roteiros'] = array();
            $retorno['mensagem'] = 'Total de '.$abc->rowCount().' registros encontrados. Mostrando os primeiros 200 registros. Tente refinar a busca.';
            for($i = 0; $i < 200; $i++) {
                $tmp = $abc->fetch(\PDO::FETCH_OBJ);
                $roteiro = new Roteiro($tmp->id);
                array_push($retorno['roteiros'], $roteiro->getDados());

                unset($tmp, $roteiro);
            }
        }

        return $retorno;
    }
    
    /**
     * Retorna clientes apagados (soft-delete).
     * 
     * @return array [success => TRUE|FALSE, mensagem => STRING, clientes => ARRAY]
     */
    public function getClientesLixeira()
    {
        $retorno = array(
            'success' => false,
            'mensagem' => '',
        );

        $abc = $this->pdo->query('SELECT clientes.id, clientes.nome, clientes.email, clientes.cidade, clientes.estado, clientes.deletado_em, clientes.deletado_por, '.
        'CONCAT(login.nome," ", login.sobrenome) as usuario FROM clientes LEFT JOIN login ON clientes.deletado_por = login.id WHERE clientes.deletado_em IS NOT NULL');
        if($abc->rowCount() == 0) {
            $retorno['clientes'] = array();
            $retorno['success'] = true;
        } else {
            $retorno['clientes'] = $abc->fetchAll(\PDO::FETCH_OBJ);
            $retorno['success'] = true;
        }

        return $retorno;
    }

    /**
     * Retorna coordenadores apagados (soft-delete).
     * 
     * @return array [success => TRUE|FALSE, mensagem => STRING, coordenadores => ARRAY]
     */
    public function getCoordenadoresLixeira()
    {
        $retorno = array(
            'success' => false,
            'mensagem' => '',
        );

        $abc = $this->pdo->query('SELECT coordenadores.id, coordenadores.nome, coordenadores.email, coordenadores.cidade, coordenadores.estado, coordenadores.deletado_em, coordenadores.deletado_por, '.
        'CONCAT(login.nome," ", login.sobrenome) as usuario FROM coordenadores LEFT JOIN login ON coordenadores.deletado_por = login.id WHERE coordenadores.deletado_em IS NOT NULL');
        if($abc->rowCount() == 0) {
            $retorno['coordenadores'] = array();
            $retorno['success'] = true;
        } else {
            $retorno['coordenadores'] = $abc->fetchAll(\PDO::FETCH_OBJ);
            $retorno['success'] = true;
        }

        return $retorno;
    }

    /**
     * Retorna roteiros apagados (soft-delete).
     * 
     * @return array [success => TRUE|FALSE, mensagem => STRING, roteiros => ARRAY]
     */
    public function getRoteirosLixeira()
    {
        $retorno = array(
            'success' => false,
            'mensagem' => '',
        );

        $abc = $this->pdo->query('SELECT roteiros.id, roteiros.nome, roteiros.data_ini, roteiros.data_fim, roteiros.deletado_em, roteiros.deletado_por, '.
        'CONCAT(login.nome," ", login.sobrenome) as usuario FROM roteiros LEFT JOIN login ON roteiros.deletado_por = login.id WHERE roteiros.deletado_em IS NOT NULL');
        if($abc->rowCount() == 0) {
            $retorno['roteiros'] = array();
            $retorno['success'] = true;
        } else {
            $retorno['roteiros'] = $abc->fetchAll(\PDO::FETCH_OBJ);
            $retorno['success'] = true;
        }

        return $retorno;
    }

    /**
     * Cria novo cliente. (Não precisa enviar ID no construtor).
     * 
     * @param array  $dados => [$nome, $nascimento, $rg, $cpf
     *      $email, $telefone, $endereco, $complemento,
     *      $ponto_referencia, $bairro, $cidade, $estado,
     *      $cep, $estado_civil, $alergia, $emergencia_nome,
     *      $emergencia_tel, $taxa_extra_casal, $dependente, $sangue]
     * 
     * @return array [success => TRUE|FALSE, mensagem => STRING]
     */
    public function setClienteNovo(array $dados)
    {
        $retorno = array(
            'success' => false,
            'mensagem' => ''
        );

        if(empty($dados) || !isset($dados['nome']) || $dados['nome'] == '') {
            $retorno['mensagem'] = Erro::getMessage(100);
            return $retorno;
        }

        /**
         * VALIDA OS DADOS
         */

        if(trim($dados['taxa_extra_casal']) === '') {
            $taxaExtraCasal = 0;
        } else if(\strrpos($dados['taxa_extra_casal'], ',') === false) {
            // trata valor como real (adiciona dois zeros no final para converter em centavos).
            $taxaExtraCasal = $dados['taxa_extra_casal'].'00';
            $taxaExtraCasal = filter_var($taxaExtraCasal, \FILTER_VALIDATE_INT);
            if($taxaExtraCasal === FALSE) {
                $retorno['mensagem'] = Erro::getMessage(101);
                return $retorno;
            }
        } else if (\strpos($dados['taxa_extra_casal'], ',') == \strrpos($dados['taxa_extra_casal'], ',')) {
            // Há mais de uma vírgula.
            $retorno['mensagem'] = Erro::getMessage(102);
            return $retorno;
        } else {
            // Converte para reais e centavos.
            //$taxaExtraCasal = str_replace(',', '', $dados['taxa_extra_casal']);
            $x = explode(',', $dados['taxa_extra_casal']);
            $taxaExtraCasal = $x[0].substr($x[1],0,2);

            // Passa pelo filtro de validação.
            $taxaExtraCasal = filter_var($taxaExtraCasal, \FILTER_VALIDATE_INT);
            if($taxaExtraCasal === FALSE) {
                $retorno['mensagem'] = Erro::getMessage(103);
                return $retorno;
            }
        }
            
        

        $titular = trim($dados['dependente']);
        if($titular === '') {
            $titular = 0;
        } else if(filter_var($titular, \FILTER_VALIDATE_INT) === FALSE) {
            $retorno['mensagem'] = Erro::getMessage(104);
            return $retorno;
        }

        // Lança cliente no Banco de Dados.
        $sql = "INSERT INTO clientes (id, nome, email, telefone, rg, cpf, nascimento, estado_civil, ".
            "endereco, complemento, ponto_referencia, bairro, cep, cidade, estado, sangue, alergia, emergencia_nome, ".
            "emergencia_tel, taxa_extra_casal, titular, criado_em, atualizado_em) ".
        "VALUES (null, :nome, :email, :tel, :rg, :cpf, :nascimento, :estado_civil, :endereco, ".
            ":complemento, :ponto_referencia, :bairro, :cep, :cidade, :estado, :sangue, :alergia, ".
            ":em_nome, :em_tel, :taxa_extra, :titular, NOW(), NOW())";
        $abc = $this->pdo->prepare($sql);
        
        // Faz o TRIM nos dados
        foreach($dados as $key => $val) {
            $dados[$key] = trim($val);
        }

        $abc->bindValue(':nome', $dados['nome'], \PDO::PARAM_STR);
        $abc->bindValue(':email', $dados['email'], \PDO::PARAM_STR);
        $abc->bindValue(':tel', $dados['telefone'], \PDO::PARAM_STR);
        $abc->bindValue(':rg', $dados['rg'], \PDO::PARAM_STR);
        $abc->bindValue(':cpf', $dados['cpf'], \PDO::PARAM_STR);
        $abc->bindValue(':nascimento', $dados['nascimento'], \PDO::PARAM_STR);
        $abc->bindValue(':estado_civil', $dados['estado_civil'], \PDO::PARAM_STR);
        $abc->bindValue(':endereco', $dados['endereco'], \PDO::PARAM_STR);
        $abc->bindValue(':complemento', $dados['complemento'], \PDO::PARAM_STR);
        $abc->bindValue(':ponto_referencia', $dados['ponto_referencia'], \PDO::PARAM_STR);
        $abc->bindValue(':bairro', $dados['bairro'], \PDO::PARAM_STR);
        $abc->bindValue(':cep', $dados['cep'], \PDO::PARAM_STR);
        $abc->bindValue(':cidade', $dados['cidade'], \PDO::PARAM_STR);
        $abc->bindValue(':estado', $dados['estado'], \PDO::PARAM_STR);
        $abc->bindValue(':sangue', $dados['sangue'], \PDO::PARAM_STR);
        $abc->bindValue(':alergia', $dados['alergia'], \PDO::PARAM_STR);
        $abc->bindValue(':em_nome', $dados['emergencia_nome'], \PDO::PARAM_STR);
        $abc->bindValue(':em_tel', $dados['emergencia_tel'], \PDO::PARAM_STR);
        $abc->bindValue(':taxa_extra', $taxaExtraCasal, \PDO::PARAM_INT);
        $abc->bindValue(':titular', $titular, \PDO::PARAM_INT);

        try {
            $abc->execute();
            $retorno['success'] = true;
            /**
             * LOG
             */
            $log = new LOG();
            $log->novo('Criou um novo cliente <i>'.$dados['nome'].'</i>.', $_SESSION['auth']['id'], 1);
            /**
             * ./LOG
             */
        } catch(\PDOException $e) {
            $retorno['mensagem'] = Erro::getMessage(70);
            \error_log($e->getMessage(), 1, $this->system->desenvolvedor[0]);
            \error_log($e->getMessage(), 0);
        }
        
        return $retorno;
        
    }

    /**
     * Cria novo coordenador. (Não precisa enviar ID no construtor).
     * 
     * @param array  $dados => [$nome, $nascimento, $rg, $cpf
     *      $email, $telefone, $endereco, $complemento,
     *      $ponto_referencia, $bairro, $cidade, $estado,
     *      $cep, $estado_civil, $alergia, $emergencia_nome,
     *      $emergencia_tel, $sangue]
     * 
     * @return array [success => TRUE|FALSE, mensagem => STRING]
     */
    public function setCoordenadorNovo(array $dados)
    {
        $retorno = array(
            'success' => false,
            'mensagem' => ''
        );

        if(empty($dados) || !isset($dados['nome']) || $dados['nome'] == '') {
            $retorno['mensagem'] = Erro::getMessage(100);
            return $retorno;
        }

        /**
         * VALIDA OS DADOS
         */

        // Lança cliente no Banco de Dados.
        $sql = "INSERT INTO coordenadores (id, nome, email, telefone, rg, cpf, nascimento, estado_civil, ".
            "endereco, complemento, ponto_referencia, bairro, cep, cidade, estado, sangue, alergia, emergencia_nome, ".
            "emergencia_tel, criado_em, atualizado_em) ".
        "VALUES (null, :nome, :email, :tel, :rg, :cpf, :nascimento, :estado_civil, :endereco, ".
            ":complemento, :ponto_referencia, :bairro, :cep, :cidade, :estado, :sangue, :alergia, ".
            ":em_nome, :em_tel, NOW(), NOW())";
        $abc = $this->pdo->prepare($sql);

        // Faz o TRIM nos dados
        foreach($dados as $key => $val) {
            $dados[$key] = trim($val);
        }

        $abc->bindValue(':nome', $dados['nome'], \PDO::PARAM_STR);
        $abc->bindValue(':email', $dados['email'], \PDO::PARAM_STR);
        $abc->bindValue(':tel', $dados['telefone'], \PDO::PARAM_STR);
        $abc->bindValue(':rg', $dados['rg'], \PDO::PARAM_STR);
        $abc->bindValue(':cpf', $dados['cpf'], \PDO::PARAM_STR);
        $abc->bindValue(':nascimento', $dados['nascimento'], \PDO::PARAM_STR);
        $abc->bindValue(':estado_civil', $dados['estado_civil'], \PDO::PARAM_STR);
        $abc->bindValue(':endereco', $dados['endereco'], \PDO::PARAM_STR);
        $abc->bindValue(':complemento', $dados['complemento'], \PDO::PARAM_STR);
        $abc->bindValue(':ponto_referencia', $dados['ponto_referencia'], \PDO::PARAM_STR);
        $abc->bindValue(':bairro', $dados['bairro'], \PDO::PARAM_STR);
        $abc->bindValue(':cep', $dados['cep'], \PDO::PARAM_STR);
        $abc->bindValue(':cidade', $dados['cidade'], \PDO::PARAM_STR);
        $abc->bindValue(':estado', $dados['estado'], \PDO::PARAM_STR);
        $abc->bindValue(':sangue', $dados['sangue'], \PDO::PARAM_STR);
        $abc->bindValue(':alergia', $dados['alergia'], \PDO::PARAM_STR);
        $abc->bindValue(':em_nome', $dados['emergencia_nome'], \PDO::PARAM_STR);
        $abc->bindValue(':em_tel', $dados['emergencia_tel'], \PDO::PARAM_STR);

        try {
            $abc->execute();
            $retorno['success'] = true;
            /**
             * LOG
             */
            $log = new LOG();
            $log->novo('Criou um novo coordenador <i>'.$dados['nome'].'</i>.', $_SESSION['auth']['id'], 1);
            /**
             * ./LOG
             */
        } catch(\PDOException $e) {
            $retorno['mensagem'] = Erro::getMessage(70);
            \error_log($e->getMessage(), 1, $this->system->desenvolvedor[0]);
            \error_log($e->getMessage(), 0);
        }
        
        return $retorno;
        
    }

    /**
     * Cria novo usuário.
     * 
     * @param array  $dados => [$nome,
     *      $sobrenome,
     *      $email,
     *      $usuario
     *      $senha1,
     *      $senha2]
     * 
     * @return array [success => TRUE|FALSE, mensagem => STRING]
     */
    public function setUsuarioNovo(array $dados)
    {
        $retorno = array(
            'success' => false,
            'mensagem' => ''
        );

        if(empty($dados) ||
            !isset($dados['nome']) || $dados['nome'] == '' ||
            !isset($dados['sobrenome']) || $dados['sobrenome'] == '' ||
            !isset($dados['email']) || $dados['email'] == '' ||
            !isset($dados['usuario']) || $dados['usuario'] == '' ||
            !isset($dados['senha1']) || $dados['senha1'] == '' ||
            !isset($dados['senha2']) || $dados['senha2'] == '') {

            $retorno['mensagem'] = Erro::getMessage(20);
            return $retorno;
        }

        // Verifica senha.
        if($dados['senha1'] !== $dados['senha2']) {
            $retorno['mensagem'] = Erro::getMessage(21);
            return $retorno;
        }

        //Verifica se o email já existe no BD.
        $abc=$this->pdo->prepare('SELECT * FROM login WHERE email = :email');
        $abc->bindValue(':email', $dados['email'], \PDO::PARAM_STR);
        try {
            $abc->execute();
        } catch(\PDOException $e) {
            $retorno['mensagem'] = Erro::getMessage(70);
            \error_log($e->getMessage(), 1, $this->system->desenvolvedor[0]);
            \error_log($e->getMessage(), 0);
            return $retorno;
        }

        if($abc->rowCount() > 0) {
            $retorno['mensagem'] = Erro::getMessage(22);
            return $retorno;
        }


        //Verifica se o usuário já existe no BD.
        $abc=$this->pdo->prepare('SELECT * FROM login WHERE usuario = :usuario');
        $abc->bindValue(':usuario', $dados['usuario'], \PDO::PARAM_STR);
        try {
            $abc->execute();
        } catch(\PDOException $e) {
            $retorno['mensagem'] = Erro::getMessage(70);
            \error_log($e->getMessage(), 1, $this->system->desenvolvedor[0]);
            \error_log($e->getMessage(), 0);
            return $retorno;
        }

        if($abc->rowCount() > 0) {
            $retorno['mensagem'] = Erro::getMessage(23);
            return $retorno;
        }

        // Lança usuário no Banco de Dados.
        $sql = "INSERT INTO login (id, nome, sobrenome, email, usuario, senha, nivel, criado_em, atualizado_em) ".
        "VALUES (null, :nome, :sobrenome, :email, :usuario, :senha, :nivel, NOW(), NOW())";
        $abc = $this->pdo->prepare($sql);

        $senhaOptions = array(
            "cost" => $this->system->senha_cost
        );
        // Faz o TRIM nos dados
        foreach($dados as $key => $val) {
            if($key == 'senha1' || $key == 'senha2') {

            } else {
                $dados[$key] = trim($val);
            }
        }

        $abc->bindValue(':nome', $dados['nome'], \PDO::PARAM_STR);
        $abc->bindValue(':sobrenome', $dados['sobrenome'], \PDO::PARAM_STR);
        $abc->bindValue(':email', $dados['email'], \PDO::PARAM_STR);
        $abc->bindValue(':usuario', $dados['usuario'], \PDO::PARAM_STR);
        $abc->bindValue(':senha', password_hash($dados['senha1'],PASSWORD_DEFAULT, $senhaOptions), \PDO::PARAM_STR);
        $abc->bindValue(':nivel', $dados['nivel'], \PDO::PARAM_INT);

        try {
            $abc->execute();
            $retorno['success'] = true;
            /**
             * LOG
             */
            $log = new LOG();
            $log->novo('Criou um novo usuário: <b>'.$dados['nome'].' '.$dados['sobrenome'].'</b> [<i>@'.$dados['usuario'].'</i> | Nível: '.$dados['nivel'].'].', $_SESSION['auth']['id'],2);
            /**
             * ./LOG
            */
        } catch(\PDOException $e) {
            $retorno['mensagem'] = Erro::getMessage(70);
            \error_log($e->getMessage(), 1, $this->system->desenvolvedor[0]);
            \error_log($e->getMessage(), 0);
        }
        
        return $retorno;
    }

    /**
     * Busca usuário através do e-mail.
     * 
     * @param string $email E-mail a buscar.
     * 
     * @return mixed Em caso de sucesso, retorna stdClass; em caso de falha, retorna FALSE.
     */
    public function getUsuarioBuscarEmail(string $email)
    {
        if($email == '')
        {
            return false;
        } else {
            $email = \filter_var($email, \FILTER_VALIDATE_EMAIL);
            if($email === false) {
                return false;
            }
        }

        $abc = $this->pdo->prepare('SELECT * FROM login WHERE email = :email');
        $abc->bindValue(':email', $email, \PDO::PARAM_STR);
        $abc->execute();

        if($abc->rowCount() == 0) {
            return false;
        } else {
            $reg = $abc->fetch(\PDO::FETCH_OBJ);
            unset($reg->senha);
            return $reg;
        }
    }

    /**
     * Listar usuários. (Não precisa enviar ID no construtor).
     * 
     * @param int $inicio Onde o ponteiro de busca deve iniciar.
     * @param int $qtd Total de registros que a busca deve retornar.
     * @param array $ordem_por Campo a ser ordenado. Ex.: nome, sobrenome, email, nivel, criado_em, logado_em...
     * @param const Ordenar ASCENDENTE ou DESCENDENTE.
     * 
     * @return array [success => TRUE|FALSE, mensagem => STRING, usuarios => ARRAY]
     */
    public function getUsuariosLista(int $inicio = 0, int $qtd = 20, array $ordem_por = ['criado_em'], $ordem = SGCTUR::ORDER_DESC)
    {
        $retorno = array(
            'success' => false,
            'mensagem' => ''
        );
        
        if($qtd == 0) {
            $limit = '';
        } else {
            $limit = 'LIMIT '.$inicio.', '.$qtd;
        }

        $str_ordem = '';
        if(empty($ordem_por)) {
            $retorno['mensagem'] = Erro::getMessage(9);
            return $retorno;
        }
        foreach($ordem_por as $o) {
            switch($o) {
                case 'nome':
                case 'sobrenome':
                case 'email':
                case 'nivel':
                case 'criado_em':
                case 'logado_em':
                    $str_ordem .= $o;
                    if($ordem == 1) {
                        // ASCENDENTE
                        $str_ordem .= ' ASC, ';
                    } else {
                        // DESCENDENTE
                        $str_ordem .= ' DESC, ';
                    }
                break;
            }

            
        }

        $str_ordem = substr($str_ordem, 0, -2);

        $abc = $this->pdo->prepare('SELECT id, nome, sobrenome, email, avatar, nivel, usuario, criado_em, logado_em FROM login WHERE 1 ORDER BY '.$str_ordem.' '.$limit);
        $abc->bindValue(':ini', $inicio, \PDO::PARAM_INT);
        $abc->bindValue(':qtd', $qtd, \PDO::PARAM_INT);

        try {
            $abc->execute();
        } catch(\PDOException $e) {
            $retorno['mensagem'] = Erro::getMessage(70);
            \error_log($e->getMessage(), 1, $this->system->desenvolvedor[0]);
            \error_log($e->getMessage(), 0);

            return $retorno;
        }

        
        $retorno['success'] = true;
        if($abc->rowCount() == 0) {
            $retorno['usuarios'] = array();
        } else {
            $retorno['usuarios'] = $abc->fetchAll(\PDO::FETCH_OBJ);
        }

        return $retorno;
    }

    /**
     * Faz a busca de uma string em NOME, SOBRENOME, EMAIL, USUÁRIO.
     * (Não precisa enviar ID no construtor).
     * 
     * @param string $busca String para consulta.
     * 
     * @return array [success => TRUE|FALSE, mensagem => STRING, usuarios => ARRAY]
     */
    public function getUsuariosBusca(string $busca = '')
    {
        $retorno = array(
            'success' => false,
            'mensagem' => ''
        );
        $busca = trim($busca);

        if($busca == '') {
            $abc = $this->pdo->query('SELECT * FROM login WHERE 1 ORDER BY nome ASC, sobrenome ASC');
        } else {
            $abc = $this->pdo->prepare('SELECT * FROM login WHERE nome LIKE :b1 OR sobrenome LIKE :b2 OR '.
            'email LIKE :b3 OR usuario LIKE :b4 ORDER BY nome ASC, sobrenome ASC');

            $abc->bindValue(':b1', '%'.$busca.'%', \PDO::PARAM_STR);
            $abc->bindValue(':b2', '%'.$busca.'%', \PDO::PARAM_STR);
            $abc->bindValue(':b3', '%'.$busca.'%', \PDO::PARAM_STR);
            $abc->bindValue(':b4', '%'.$busca.'%', \PDO::PARAM_STR);

            try {
                $abc->execute();
            } catch(\PDOException $e) {
                $retorno['mensagem'] = Erro::getMessage(70);
                \error_log($e->getMessage(), 1, $this->system->desenvolvedor[0]);
                \error_log($e->getMessage(), 0);
                return $retorno;
            }
            
        }

        $retorno['success'] = true;
        if($abc->rowCount() == 0) {
            $retorno['usuarios'] = array();
        } else if($abc->rowCount() <= 200)  {
            $retorno['usuarios'] = $abc->fetchAll(\PDO::FETCH_OBJ);
            foreach($retorno['usuarios'] as $key => $u) {
                unset($retorno['usuarios'][$key]->senha);
            }
        } else {
            $retorno['usuarios'] = array();
            $retorno['mensagem'] = 'Total de '.$abc->rowCount().' registros encontrados. Mostrando os primeiros 200 registros. Tente refinar a busca.';
            for($i = 0; $i < 200; $i++) {
                $reg = $abc->fetch(\PDO::FETCH_OBJ);
                unset($reg->senha);
                array_push($retorno['usuarios'], $reg);
            }
        }

        return $retorno;
    }

    /**
     * Retorna lista de bancos na plataforma.
     * 
     * @return array [success => TRUE|FALSE, mensagem => STRING, bancos => ARRAY]
     */
    public function getListaBancos()
    {
        $retorno = array(
            'success' => false,
            'mensagem' => ''
        );

        $abc = $this->pdo->query('SELECT codigo, banco FROM lista_bancos ORDER BY codigo ASC');
        $retorno['success'] = true;
        $retorno['bancos'] = $abc->fetchAll(\PDO::FETCH_OBJ);

        return $retorno;

    }

    /**
     * Cria nova parceria.
     * 
     * @param array $dados Dados para serem inseridos no banco.
     * @return array [success => TRUE|FALSE, mensagem => STRING, parceiro => ARRAY]
     */
    public function setParceirosNovo(array $dados)
    {
        $retorno = [
            'success' => false,
            'mensagem' => ''
        ];

        // Validando entrada.
        if(!isset($dados['razao_social']) || $dados['razao_social'] == '') {
            $retorno['mensagem'] = 'A razão social/nome completo não pode ser vazio.';
            return $retorno;
        }

        if(!isset($dados['doc_tipo']) || $dados['doc_tipo'] == '') {
            $retorno['mensagem'] = 'O tipo de documento (CPF ou CNPJ) não foi informado.';
            return $retorno;
        }

        if(!isset($dados['doc_numero']) || $dados['doc_numero'] == '') {
            $retorno['mensagem'] = 'O número do documento '.$dados['doc_tipo'].' não foi informado.';
            return $retorno;
        }

        if(
            ($dados['doc_tipo'] == 'CPF' && strlen($dados['doc_numero']) != 11) ||
            ($dados['doc_tipo'] == 'CNPJ' && strlen($dados['doc_numero']) != 14)
        ) {
            $retorno['mensagem'] = 'Quantidade de dígitos incorreto: CPF (11 números) ou CNPJ (14 números).';
            return $retorno;
        }

        if(!is_numeric($dados['doc_numero'])) {
            $retorno['mensagem'] = 'Em número do documento SOMENTE é aceito números!';
            return $retorno;
        }

        try {
            $abc = $this->pdo->prepare('INSERT INTO `parc_empresa` (`id`, `razao_social`, `nome_fantasia`, `doc_tipo`, `doc_numero`, `endereco`, `cidade`, `estado`, `responsavel`, `criado_em`) '.
            'VALUES (NULL, :rs, :nf, :doctipo, :docnum, :endereco, :cidade, :estado, :resp, NOW())');

            // Faz o TRIM nos dados
            foreach($dados as $key => $val) {
                $dados[$key] = trim($val);
            }

            $abc->bindValue(':rs', $dados['razao_social'], \PDO::PARAM_STR);
            $abc->bindValue(':nf', $dados['fantasia'], \PDO::PARAM_STR);
            $abc->bindValue(':doctipo', $dados['doc_tipo'], \PDO::PARAM_STR);
            $abc->bindValue(':docnum', $dados['doc_numero'], \PDO::PARAM_STR);
            $abc->bindValue(':endereco', $dados['endereco'], \PDO::PARAM_STR);
            $abc->bindValue(':cidade', $dados['cidade'], \PDO::PARAM_STR);
            $abc->bindValue(':estado', $dados['estado'], \PDO::PARAM_STR);
            $abc->bindValue(':resp', $dados['responsavel'], \PDO::PARAM_STR);

            $abc->execute();

            $abc = $this->pdo->prepare('SELECT id FROM `parc_empresa` WHERE doc_tipo = :doctipo AND doc_numero = :docnum AND razao_social = :rs ORDER BY id DESC');
            
            $abc->bindValue(':rs', $dados['razao_social'], \PDO::PARAM_STR);
            $abc->bindValue(':doctipo', $dados['doc_tipo'], \PDO::PARAM_STR);
            $abc->bindValue(':docnum', $dados['doc_numero'], \PDO::PARAM_STR);

            $abc->execute();
            if($abc->rowCount() == 0) {
                $retorno['mensagem'] = 'Não foi possível localizar  o registro recém-inserido. Faça uma busca manual, ou salve novamente.';
                return $retorno;
            }

            $reg = $abc->fetch(\PDO::FETCH_OBJ);

            $abc = $this->pdo->prepare('INSERT INTO `parc_financeiro` (`id`, `empresa_id`, `banco`, `agencia`, `agencia_dv`, `conta`, `conta_dv`, `tipo_conta`, `favorecido`, `obs_financeiro`) '.
            'VALUES (NULL, '.$reg->id.', :banco, :agencia, :agenciadv, :conta, :contadv, :tipo_conta, :favorecido, :obs)');

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
            $log->novo('Criou um novo parceiro: <b class="text-uppercase">'.$dados['razao_social'].'</b>.', $_SESSION['auth']['id'], 1);
            /**
             * ./LOG
             */

            $retorno['success'] = true;
            $retorno['parceiro'] = $reg;
            return $retorno;


        } catch(PDOException $e) {
            $retorno['mensagem'] = Erro::getMessage(70);
            \error_log($e->getMessage(), 1, $this->system->desenvolvedor[0]);
            \error_log($e->getMessage(), 0);
            return $retorno;
        }
    }

    /**
     * Listar parceiros. (Não precisa enviar ID no construtor).
     * 
     * @param int $inicio Onde o ponteiro de busca deve iniciar.
     * @param int $qtd Total de registros que a busca deve retornar.
     * @param array $ordem_por Campo a ser ordenado. Ex.: razao_social, nome_fantasia, cidade, estado, responsavel, criado_em...
     * @param const Ordenar ASCENDENTE ou DESCENDENTE.
     * 
     * @return array [success => TRUE|FALSE, mensagem => STRING, parceiros => ARRAY]
     */
    public function getParceirosLista(int $inicio = 0, int $qtd = 20, array $ordem_por = ['criado_em'], $ordem = SGCTUR::ORDER_DESC)
    {
        $retorno = array(
            'success' => false,
            'mensagem' => ''
        );
        
        if($qtd == 0) {
            $limit = '';
        } else {
            $limit = 'LIMIT '.$inicio.', '.$qtd;
        }

        $str_ordem = '';
        if(empty($ordem_por)) {
            $retorno['mensagem'] = Erro::getMessage(9);
            return $retorno;
        }
        foreach($ordem_por as $o) {
            switch($o) {
                case 'razao_social':
                case 'nome_fantasia':
                case 'cidade':
                case 'estado':
                case 'criado_em':
                case 'responsavel':
                    $str_ordem .= $o;
                    if($ordem == 1) {
                        // ASCENDENTE
                        $str_ordem .= ' ASC, ';
                    } else {
                        // DESCENDENTE
                        $str_ordem .= ' DESC, ';
                    }
                break;
            }

            
        }

        $str_ordem = substr($str_ordem, 0, -2);

        $abc = $this->pdo->prepare('SELECT id, razao_social, nome_fantasia, cidade, estado, responsavel, criado_em FROM parc_empresa WHERE 1 ORDER BY '.$str_ordem.' '.$limit);
        $abc->bindValue(':ini', $inicio, \PDO::PARAM_INT);
        $abc->bindValue(':qtd', $qtd, \PDO::PARAM_INT);

        try {
            $abc->execute();
        } catch(\PDOException $e) {
            $retorno['mensagem'] = Erro::getMessage(70);
            \error_log($e->getMessage(), 1, $this->system->desenvolvedor[0]);
            \error_log($e->getMessage(), 0);

            return $retorno;
        }

        
        $retorno['success'] = true;
        if($abc->rowCount() == 0) {
            $retorno['parceiros'] = array();
        } else {
            $retorno['parceiros'] = $abc->fetchAll(\PDO::FETCH_OBJ);
        }

        return $retorno;
    }

    /**
     * Faz a busca de uma string em RAZAO_SOCIAL, NOME_FANTASIA, CIDADE (Não precisa enviar ID no construtor).
     * 
     * @param string $busca String para consulta.
     * 
     * @return array [success => TRUE|FALSE, mensagem => STRING, clientes => ARRAY]
     */
    public function getParceirosBusca(string $busca = '')
    {
        $retorno = array(
            'success' => false,
            'mensagem' => ''
        );

        if(trim($busca) == '') {
            $abc= $this->pdo->query('SELECT * FROM parc_empresa WHERE 1 ORDER BY razao_social ASC, nome_fantasia ASC');
        } else {
            $abc = $this->pdo->prepare('SELECT * FROM parc_empresa WHERE razao_social LIKE :b1 OR nome_fantasia LIKE :b2 OR cidade LIKE :b3 '.
            'ORDER BY razao_social ASC, nome_fantasia ASC');
            $abc->bindValue(':b1', '%'.trim($busca).'%', \PDO::PARAM_STR);
            $abc->bindValue(':b2', '%'.trim($busca).'%', \PDO::PARAM_STR);
            $abc->bindValue(':b3', '%'.trim($busca).'%', \PDO::PARAM_STR);

            try {
                $abc->execute();
            } catch(\PDOException $e) {
                $retorno['mensagem'] = Erro::getMessage(70);
                \error_log($e->getMessage(), 1, $this->system->desenvolvedor[0]);
                \error_log($e->getMessage(), 0);
                return $retorno;
            }
        }

        $retorno['success'] = true;
        if($abc->rowCount() == 0) {
            $retorno['parceiros'] = array();
        } else if($abc->rowCount() <= 200)  {
            $retorno['parceiros'] = array();
            
            while($parceiro = $abc->fetch(\PDO::FETCH_OBJ)) {
                // Pesquisa a lista de serviços
                $def = $this->pdo->query('SELECT DISTINCT categoria FROM parc_servico WHERE empresa_id = '.$parceiro->id);
                if($def->rowCount() == 0) {
                    $parceiro->servicos = array();
                } else {
                    $parceiro->servicos = $def->fetchAll(\PDO::FETCH_OBJ);
                }
                array_push($retorno['parceiros'], $parceiro);
            }
            //$retorno['parceiros'] = $abc->fetchAll(\PDO::FETCH_OBJ);
        } else {
            $retorno['parceiros'] = array();
            $retorno['mensagem'] = 'Total de '.$abc->rowCount().' registros encontrados. Mostrando os primeiros 200 registros. Tente refinar a busca.';
            for($i = 0; $i < 200; $i++) {
                $parceiro = $abc->fetch(\PDO::FETCH_OBJ);
                // Pesquisa a lista de serviços
                $def = $this->pdo->query('SELECT DISTINCT categoria FROM parc_servico WHERE empresa_id = '.$parceiro->id);
                if($def->rowCount() == 0) {
                    $parceiro->servicos = array();
                } else {
                    $parceiro->servicos = $def->fetchAll(\PDO::FETCH_OBJ);
                }
                array_push($retorno['parceiros'], $parceiro);
            }
        }

        return $retorno;
    }

    /**
     * Cria um novo roteiro na plataforma.
     * 
     * @param array $dados [nome, data_ini, data_fim, ARRAY parceiros, passagens, qtd_coordenador, qtd_rateio, taxa_lucro, ARRAY despesas]
     * 
     * @return bool
     */
    public function setRoteiroNovo(array $dados)
    {
        if(empty($dados)) {
            return false;
        }

        // Faz o cálculo do lucro previsto, com base nas despesas e taxa de lucro.
        $despesas = 0;
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
        
        //---

        // Verifica se alguns outros dados foram enviados e estão no formato certo.
        // Tarifa
        if(isset($dados['tarifa'])) {
            if(is_array($dados['tarifa']) && !empty($dados['tarifa'])) {
                $dados['tarifa'] = json_encode($dados['tarifa']);
            }
        } else {
            $dados['tarifa'] = '';
        }

        // Observações
        if(!isset($dados['obs'])) {
            $dados['obs'] = '';
        }
        

        // Persiste dados.
        try {
            $abc = $this->pdo->prepare("INSERT INTO `roteiros` (`id`, `nome`, `data_ini`, `data_fim`, `passagens`, `qtd_coordenador`, `coordenador`, `clientes`, `despesas`, `parceiros`, `qtd_rateio`, `taxa_lucro`, `lucro_previsto`, `tarifa`, `reserva_qtd`, `reserva_obs`, `obs`, `criado_em`, `criado_por`, `atualizado_em`, `deletado_em`, `deletado_por`) VALUES ".
            "(NULL, :nome, :datai, :dataf, :pass, :qtd_coord, '', '', :despesas, :parceiros, :qtd_rateio, :taxa_lucro, :lucro_previsto, :tarifa, '', '', :obs, current_timestamp(), ".$_SESSION['auth']['id'].", current_timestamp(), NULL, NULL)");

            // Faz o TRIM nas string dos dados
            foreach($dados as $key => $val) {
                if(is_string($val)) {
                    $dados[$key] = trim($val);
                }
            }

            $abc->bindValue(':nome', $dados['nome'], \PDO::PARAM_STR);
            $abc->bindValue(':datai', $dados['data_ini'], \PDO::PARAM_STR);
            $abc->bindValue(':dataf', $dados['data_fim'], \PDO::PARAM_STR);
            $abc->bindValue(':pass', $dados['passagens'], \PDO::PARAM_INT);
            $abc->bindValue(':qtd_coord', $dados['qtd_coordenador'], \PDO::PARAM_INT);

            $abc->bindValue(':despesas', json_encode($dados['despesas']), \PDO::PARAM_STR);
            $abc->bindValue(':parceiros', json_encode($dados['parceiros']), \PDO::PARAM_STR);
            $abc->bindValue(':qtd_rateio', $dados['qtd_rateio'], \PDO::PARAM_INT);
            $abc->bindValue(':taxa_lucro', $dados['taxa_lucro'], \PDO::PARAM_INT);
            $abc->bindValue(':lucro_previsto', json_encode($lucro), \PDO::PARAM_STR);
            $abc->bindValue(':tarifa', addslashes($dados['tarifa']), \PDO::PARAM_STR);
            $abc->bindValue(':obs', addslashes($dados['obs']), \PDO::PARAM_STR);

            $abc->execute();
            /**
             * LOG
             */
            $d_i = new \DateTime($dados['data_ini']);
            $d_f = new \DateTime($dados['data_fim']);
            $log = new LOG();
            $log->novo('Criou um novo roteiro: <b>'.$dados['nome'].' ('.$d_i->format('d/m/Y').' a '.$d_f->format('d/m/Y').')</b>.', $_SESSION['auth']['id'], 1);
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
     * Listar roteiros. (Não precisa enviar ID no construtor).
     * 
     * @param int $inicio Onde o ponteiro de busca deve iniciar.
     * @param int $qtd Total de registros que a busca deve retornar.
     * @param array $ordem_por Campo a ser ordenado. Ex.: nome, data_ini, data_fim, mes, ano criado_em, atualizado_em...
     * @param const Ordenar ASCENDENTE ou DESCENDENTE.
     * 
     * @return array [success => TRUE|FALSE, mensagem => STRING, roteiros => ARRAY]
     */
    public function getRoteirosLista(int $inicio = 0, int $qtd = 20, array $ordem_por = ['criado_em'], array $ordem = [SGCTUR::ORDER_DESC])
    {
        $retorno = array(
            'success' => false,
            'mensagem' => ''
        );
        
        // Quantidade de registros.
        if($qtd == 0) {
            $limit = '';
        } else {
            $limit = 'LIMIT '.$inicio.', '.$qtd;
        }

        // Clausula ORDER BY
        $str_ordem = '';
        if(empty($ordem_por)) {
            $retorno['mensagem'] = Erro::getMessage(9);
            return $retorno;
        }
        foreach($ordem_por as $key => $o) {
            switch($o) {
                case 'nome':
                case 'data_ini':
                case 'data_fim':
                case 'criado_em':
                case 'atualizado_em':
                case 'mes':
                case 'ano':
                    $str_ordem .= $o;
                    
                    if(!isset($ordem[$key]) || $ordem[$key] == '') {
                        // Se este índice não estiver setado, usa o primeiro indice.
                        if($ordem[0] == 1) {
                            // ASCENDENTE
                            $str_ordem .= ' ASC, ';
                        } else {
                            // DESCENDENTE
                            $str_ordem .= ' DESC, ';
                        }
                    } else {
                        // Indice setado.
                        if($ordem[$key] == 1) {
                            // ASCENDENTE
                            $str_ordem .= ' ASC, ';
                        } else {
                            // DESCENDENTE
                            $str_ordem .= ' DESC, ';
                        }

                    }
                    
                break;
            }

            
        }

        $str_ordem = substr($str_ordem, 0, -2);

        $abc = $this->pdo->prepare('SELECT id, nome, data_ini, data_fim, MONTH(data_ini) as mes, YEAR(data_ini) as ano, passagens, qtd_coordenador, criado_em FROM roteiros WHERE deletado_em IS NULL ORDER BY '.$str_ordem.' '.$limit);
        $abc->bindValue(':ini', $inicio, \PDO::PARAM_INT);
        $abc->bindValue(':qtd', $qtd, \PDO::PARAM_INT);

        try {
            $abc->execute();
        } catch(\PDOException $e) {
            $retorno['mensagem'] = Erro::getMessage(70);
            \error_log($e->getMessage(), 1, $this->system->desenvolvedor[0]);
            \error_log($e->getMessage(), 0);

            return $retorno;
        }

        
        $retorno['success'] = true;
        if($abc->rowCount() == 0) {
            $retorno['roteiros'] = array();
        } else {
            $retorno['roteiros'] = $abc->fetchAll(\PDO::FETCH_OBJ);
        }

        return $retorno;
    }

    /**
     * Lança venda na plataforma.
     * 
     * @param \stdClass $venda Um objeto gerado com o JSON_DECODE ou não.
     * @return array [success => TRUE|FALSE, mensagem => STRING]
     */
    public function setVendaNovo(\stdClass $venda)
    {
        //print_r($venda);

        $retorno = [
            'success' => false,
            'mensagem' => ''
        ];

        // Define variáveis importantes.
        $vendedor = $_SESSION['auth']['id'];
        $cliente = $venda->clienteID;
        $obs = $venda->obs;
        $formaPagamento = $venda->formaPagamento;

        // Verifica os itens da venda e checa se são do mesmo roteiro.
        $listaRoteiros = array();
        foreach($venda->items as $key => $val) {
            if(\array_search($val->roteiroID, $listaRoteiros) === false) {
                array_push($listaRoteiros, $val->roteiroID);
            }
        }

        // Inicia transação no banco de dados.
        $this->pdo->beginTransaction();


        // Lança venda para cada roteiro
        foreach($listaRoteiros as $r) {
            // Carrega roteiro.
            $roteiroClasse = new Roteiro($r);
            $roteiro = $roteiroClasse->getDados();
            //print_r($roteiro->tarifa);
            
            $v = new stdClass();
            $v->roteiroID = $r;
            $v->items = array();
            $v->criancas = 0;
            $v->adultos = 0;
            $v->clientes_total = 0;
            $v->valor_total = 0;
            $v->desconto_total = 0;

            // Busca itens desse roteiro.
            foreach($venda->items as $items) {
                if($items->roteiroID === $r) {
                    unset($items->roteiroID);
                    array_push($v->items, $items);
                    
                    // Busca a tarifa correspondente.
                    foreach($roteiro->tarifa as $key => $y) {
                        if($y->nome == $items->tarifa) {
                            $x = $key;
                            break;
                        }
                    }
                    // Conta clientes.
                    $v->criancas += ( (int)$roteiro->tarifa[$x]->distr->criancas * (int)$items->qtd );
                    $v->adultos += ( (int)$roteiro->tarifa[$x]->distr->adultos * (int)$items->qtd );

                    // Valor total e desconto total.
                    $v->valor_total += (int)$items->subtotal;
                    $v->desconto_total += (int)$items->desconto;

                }
            }
    
            // Conta total de clientes nessa venda.
            $v->clientes_total = $v->criancas + $v->adultos;
            //print_r($v);

            // Verifica se há estoque suficiente para efetuar a compra.
            if($roteiro->estoque['total'] - $roteiro->estoque['vendidos'] >= $v->clientes_total) {
            
                // Lança venda no sistema.
                $abc = $this->pdo->prepare("INSERT INTO `vendas` (`id`, `cliente_id`, `roteiro_id`, `items`, `adultos`, `criancas`, `clientes_total`, `lista_clientes`, `desconto_total`, `valor_total`, `status`, `obs`, `forma_pagamento`, `usuario_id`, `data_reserva`, `data_venda`, `data_pagamento`, `data_cancelado`, `data_estorno`) VALUES ".
                "(NULL, :cliente, :roteiro, :items, :adultos, :criancas, :clientesTotal, '', :descontoTotal, :valorTotal, :status, :obs, :formaPagamento, $vendedor, NOW(), NULL, NULL, NULL, NULL)");

                $abc->bindValue(':cliente', $cliente, \PDO::PARAM_INT);
                $abc->bindValue(':roteiro', $v->roteiroID, \PDO::PARAM_INT);
                $abc->bindValue(':items', json_encode($v->items), \PDO::PARAM_STR);
                $abc->bindValue(':adultos', $v->adultos, \PDO::PARAM_INT);
                $abc->bindValue(':criancas', $v->criancas, \PDO::PARAM_INT);
                $abc->bindValue(':clientesTotal', $v->clientes_total, \PDO::PARAM_INT);
                $abc->bindValue(':descontoTotal', $v->desconto_total, \PDO::PARAM_INT);
                $abc->bindValue(':valorTotal', $v->valor_total, \PDO::PARAM_INT);
                $abc->bindValue(':obs', $obs, \PDO::PARAM_STR);
                $abc->bindValue(':status', '', \PDO::PARAM_STR);

                if($formaPagamento == 'Reserva') {
                    $abc->bindValue(':status', 'Reserva', \PDO::PARAM_STR);
                    $abc->bindValue(':formaPagamento', '', \PDO::PARAM_STR);
                } else {
                    $abc->bindValue(':status', 'Aguardando', \PDO::PARAM_STR);
                    $abc->bindValue(':formaPagamento', $formaPagamento, \PDO::PARAM_STR);
                }

                try{
                    $abc->execute();
                } catch(\PDOException $e) {
                    $this->pdo->rollBack();
                    $retorno['mensagem'] = Erro::getMessage(70) . ' ERRO: '.$e->getMessage();
                    \error_log($e->getMessage(), 1, $this->system->desenvolvedor[0]);
                    \error_log($e->getMessage(), 0);
                    return $retorno;
                }

            } else {
                // Não há vagas suficientes para compra deste roteiro.
                $retorno['mensagem'] .= '- O roteiro <i>'.$roteiro->nome.' (Cód.: '.$roteiro->id.')</i> não possui vagas disponíveis suficientes. [Disponível: '.
                ($roteiro->estoque['total'] - $roteiro->estoque['vendidos']) .'; Necessárias: '.$v->clientes_total.'].<br>';
            }
            
        }

        // Commit no banco
        $this->pdo->commit();

        $retorno['success'] = true;
        return $retorno;
    }

    /**
     * Listar vendas. (Não precisa enviar ID no construtor).
     * 
     * @param int $inicio Onde o ponteiro de busca deve iniciar.
     * @param int $qtd Total de registros que a busca deve retornar.
     * @param array $ordem_por Campo a ser ordenado. Ex.: cliente_id, roteiro_id, roteiroNome, clienteNome, data_reserva...
     * @param const Ordenar ASCENDENTE ou DESCENDENTE.
     * @param array $filtro Filtro a ser aplicado (array de arrays). Se array vazio ([]) é usado "WHERE 1" na consulta.
     * Modelo deve ser: [ [CAMPO (nome do campo no BD), OPERADOR (=, <>, <, >, IS NOT, IS), VALOR (NULL, string, int)], [...] ].
     * Ex.: [ ['status', '=', 'Reserva'] ].
     * 
     * @return array [success => TRUE|FALSE, mensagem => STRING, vendas => ARRAY]
     */
    public function getVendasLista(int $inicio = 0, int $qtd = 20, array $ordem_por = ['data_reserva'], array $ordem = [SGCTUR::ORDER_DESC], array $filtro = [])
    {
        $retorno = array(
            'success' => false,
            'mensagem' => ''
        );
        
        // Cláusula LIMIT
        if($qtd == 0) {
            $limit = '';
        } else {
            $limit = 'LIMIT '.$inicio.', '.$qtd;
        }

        // Cláusula ORDER BY
        $str_ordem = '';
        if(empty($ordem_por)) {
            $retorno['mensagem'] = Erro::getMessage(9);
            return $retorno;
        }
        foreach($ordem_por as $key => $o) {
            switch($o) {
                case 'cliente_id':
                case 'roteiro_id':
                case 'data_reserva':
                case 'roteiroNome':
                case 'clienteNome':
                    $str_ordem .= $o;
                    
                    if(!isset($ordem[$key]) || $ordem[$key] == '') {
                        // Se este índice não estiver setado, usa o primeiro indice.
                        if($ordem[0] == 1) {
                            // ASCENDENTE
                            $str_ordem .= ' ASC, ';
                        } else {
                            // DESCENDENTE
                            $str_ordem .= ' DESC, ';
                        }
                    } else {
                        // Indice setado.
                        if($ordem[$key] == 1) {
                            // ASCENDENTE
                            $str_ordem .= ' ASC, ';
                        } else {
                            // DESCENDENTE
                            $str_ordem .= ' DESC, ';
                        }

                    }
                    
                break;
            }

            
        }

        $str_ordem = substr($str_ordem, 0, -2);

        
        // Cláusula WHERE
        $where = '';
        if(empty($filtro)) {
            $where = '1';
        } else {
            foreach($filtro as $f) {
                switch(\gettype($f[2])) {
                    case 'boolean':
                    case 'integer':
                    case 'NULL':
                        $where .= $f[0]. ' '. $f[1] .' '.$f[2].' AND ';
                    break;
                    
                    case 'string':
                        $where .= $f[0]. ' '. $f[1] .' "'.$f[2].'" AND ';
                    break;
                }
            }

            $where = substr($where, 0, -5);
        }

        try{
            $abc = $this->pdo->query('SELECT vendas.*, roteiros.nome as roteiro_nome, roteiros.data_ini as roteiro_data_ini, roteiros.data_fim as roteiro_data_fim, '.
            'clientes.nome as cliente_nome '.
            'FROM vendas '.
            'LEFT JOIN roteiros ON vendas.roteiro_id = roteiros.id '.
            'LEFT JOIN clientes ON vendas.cliente_id = clientes.id '.
            'WHERE '.$where.' ORDER BY '.$str_ordem.' '.$limit);
            $retorno['vendas'] = $abc->fetchAll(\PDO::FETCH_OBJ);
            $retorno['success'] = true;

            return $retorno;
        } catch(\PDOException $e) {
            $retorno['mensagem'] = Erro::getMessage(70) . ' ERRO: '.$e->getMessage();
            \error_log($e->getMessage(), 1, $this->system->desenvolvedor[0]);
            \error_log($e->getMessage(), 0);
            return $retorno;
        }
        

    }
}