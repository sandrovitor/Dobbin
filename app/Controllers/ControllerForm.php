<?php
require_once('../vendor/autoload.php');
Use eftec\bladeone\BladeOne;
Use Cocur\Slugify\Slugify;
Use SGCTUR\SGCTUR;
Use SGCTUR\Cliente;
Use SGCTUR\Coordenador;
Use SGCTUR\Usuario;
Use SGCTUR\LOG;
Use SGCTUR\Erro;
Use SGCTUR\Parceiro;
Use SGCTUR\Roteiro;

class ControllerForm
{
    /**
     * Inicia roteador dentro do controlador.
     */
    static function router()
    {
        return ControllerPrincipal::router();
    }

    /**
     * Valida conexão.
     * 
     * @param int $nivel Nível mínimo de acesso do método. Se não definido, o controle de acesso não será feito.
     */
    static function validaConexao(int $nivel = 0)
    {
        // Valida SESSION.
        // Escrever código
        return ControllerPrincipal::validaConexao($nivel);
    }

    /**
     * CLIENTES
     */

    static function clientesNovo()
    {
        self::validaConexao(2);

        $sgc = new SGCTUR();
        $ret = $sgc->setClienteNovo($_POST);

        return json_encode($ret);
    }

    static function clientesBuscar()
    {
        self::validaConexao();
        //return json_encode($_POST);

        $sgc = new SGCTUR();

        return json_encode($sgc->getClientesBusca($_POST['busca']));
    }

    static function clientesLista()
    {
        self::validaConexao();

        $sgc = new SGCTUR();
        return json_encode($sgc->getClientesLista($_POST['ini'], $_POST['qtd'], ['nome'], SGCTUR::ORDER_ASC));
    }

    static function clientesApagar($p)
    {
        $retorno = array(
            'success' => false,
            'mensagem' => ''
        );

        self::validaConexao(2);

        $c = new Cliente($p['id']);
        $ret = $c->apagar();

        if($ret == true) {
            $retorno['success'] = true;
        } else {
            $retorno['mensagem'] = Erro::getMessage(106);
        }

        return json_encode($retorno);
    }

    static function clientesRestaurar($p)
    {
        $retorno = array(
            'success' => false,
            'mensagem' => ''
        );

        self::validaConexao(2);

        $c = new Cliente($p['id']);
        $ret = $c->restaurar();

        if($ret == true) {
            $retorno['success'] = true;
        } else {
            $retorno['mensagem'] = Erro::getMessage(107);
        }

        return json_encode($retorno);
    }

    static function clientesSalvar($p)
    {
        self::validaConexao(2);

        $retorno = array(
            'success' => true,
            'mensagem' => ''
        );
        $dados = $_POST;
        if($dados['dependente'] == '' || $dados['dependente'] == '0') {
            $dados['titular'] = 0;
        } else {
            $dados['titular'] = $dados['dependente'];
        }

        $c = new Cliente($_POST['id']);
        $c->setDados($dados);
        $c->salvar();
        
        return json_encode($retorno);
    }

    static function clientesLixeiraApagar($p)
    {
        self::validaConexao(2);
        if($p['id'] == '' || (int)$p['id'] <= 0 ) {
            return Erro::getMessage(100);
        }

        $retorno = [
            'success' => false,
            'mensagem' => ''
        ];

        $c = new Cliente($p['id']);
        $cliente = $c->getDados();
        $ret = $c->apagarLixeira();

        if($ret === true) {
            $retorno['success'] = true;
        } else {
            $retorno['mensagem'] = $ret;
        }

        return json_encode($retorno);
    }

    /**
     * COORDENADORES
     */

    static function coordenadoresNovo($p)
    {
        self::validaConexao(2);

        $sgc = new SGCTUR();
        $ret = $sgc->setCoordenadorNovo($_POST);

        return json_encode($ret);
    }

    static function coordenadoresBuscar($p)
    {
        self::validaConexao(2);
        //return json_encode($_POST);

        $sgc = new SGCTUR();

        return json_encode($sgc->getCoordenadoresBusca($_POST['busca']));
    }

    static function coordenadoresLista($p)
    {
        self::validaConexao(2);

        $sgc = new SGCTUR();
        return json_encode($sgc->getCoordenadoresLista($_POST['ini'], $_POST['qtd'], ['nome'], SGCTUR::ORDER_ASC));
    }

    static function coordenadoresSalvar($p)
    {
        self::validaConexao(2);

        $retorno = array(
            'success' => true,
            'mensagem' => ''
        );
        $dados = $_POST;

        $c = new Coordenador($_POST['id']);
        $c->setDados($dados);
        $c->salvar();
        
        return json_encode($retorno);
    }

    static function coordenadoresApagar($p)
    {
        $retorno = array(
            'success' => false,
            'mensagem' => ''
        );

        self::validaConexao(2);

        $c = new Coordenador($p['id']);
        $ret = $c->apagar();

        if($ret == true) {
            $retorno['success'] = true;
        } else {
            $retorno['mensagem'] = Erro::getMessage(106);
        }

        return json_encode($retorno);
    }

    static function coordenadoresRestaurar($p)
    {
        $retorno = array(
            'success' => false,
            'mensagem' => ''
        );

        self::validaConexao(2);

        $c = new Coordenador($p['id']);
        $ret = $c->restaurar();

        if($ret == true) {
            $retorno['success'] = true;
        } else {
            $retorno['mensagem'] = Erro::getMessage(107);
        }

        return json_encode($retorno);
    }

    static function coordenadoresLixeiraApagar($p)
    {
        self::validaConexao(2);
        if($p['id'] == '' || (int)$p['id'] <= 0 ) {
            return Erro::getMessage(100);
        }

        $retorno = [
            'success' => false,
            'mensagem' => ''
        ];

        $c = new Coordenador($p['id']);
        $cliente = $c->getDados();
        $ret = $c->apagarLixeira();

        if($ret === true) {
            $retorno['success'] = true;
        } else {
            $retorno['mensagem'] = $ret;
        }

        return json_encode($retorno);
    }

    /**
     * USUÁRIOS
     */
    static function usuariosNovo()
    {
        
        self::validaConexao(6);

        $sgc = new SGCTUR();
        $ret = $sgc->setUsuarioNovo($_POST);

        return json_encode($ret);
    }

    static function usuariosBuscar()
    {
        self::validaConexao(6);

        $sgc = new SGCTUR();

        return json_encode($sgc->getUsuariosBusca($_POST['busca']));
    }

    static function usuariosLista()
    {
        self::validaConexao(6);

        $sgc = new SGCTUR();
        return json_encode($sgc->getUsuariosLista($_POST['ini'], $_POST['qtd'], ['nome', 'sobrenome'], SGCTUR::ORDER_ASC));
    }

    static function usuariosSalvar()
    {
        // Método multinivel.
        self::validaConexao();


        $retorno = array(
            'success' => false,
            'mensagem' => ''
        );
        $dados = $_POST;

        if(!isset($dados['id'])) {
            http_response_code(404);
            header('HTTP/1.1 404 Não encontrado');
            exit();
        }

        // Verifica se ele vai alterar outros usuários.
        if((int)$_SESSION['auth']['id'] != (int)$dados['id']) {
            // Verifica se ele tem nível para alterar outros usuários.
            if((int)$_SESSION['auth']['nivel']  < 6){
                // Interrompe!
                $retorno ['mensagem'] = Erro::getMessage(24);
                return json_encode($retorno);
            } else {
                // Libera.
                $u = new Usuario($dados['id']);
            }
        } else {
            // Garante que a alteração é somente no perfil dele.
            $u = new Usuario($_SESSION['auth']['id']);
        }
        
        $u->setDados($dados);
        $ret = $u->salvar();
        if($ret === true) {
            $retorno['success'] = true;
        } else {
            switch($ret) {
                case null:
                    $retorno['mensagem'] = Erro::getMessage(205);
                    break;
                
                default:
                    $retorno['mensagem'] = $ret;
                break;
            }
        }

        // Antes de encerrar a operação, verifica se a alteração foi no próprio usuário.
        if((int)$u->getDados()->id === (int)$_SESSION['auth']['id']) {
            // Atualiza a sessão.
            $reg = $u->getDados();
            $_SESSION['auth']['nome'] = $reg->nome;
            $_SESSION['auth']['sobrenome'] = $reg->sobrenome;
            $_SESSION['auth']['email'] = $reg->email;
            $_SESSION['auth']['usuario'] = $reg->usuario;
            $_SESSION['auth']['nivel'] = $reg->nivel;
        }


        return json_encode($retorno);

    }    

    static function usuariosApagar($p)
    {
        self::validaConexao(6);

        $retorno = array(
            'success' => false,
            'mensagem' => ''
        );

        $u = new Usuario($p['id']);
        $usuario = $u->getDados();

        $ret = $u->apagar();
        if($ret === false) {
            $retorno['mensagem'] = 'Ocorreu um erro do lado de cá. Não foi possível excluir o usuário.';
        } else {
            $retorno['success'] = true;
            /**
             * LOG
             */
            $log = new LOG();
            $log->novo('Apagou o usuário '.$usuario->nome.' '.$usuario->sobrenome . ' ('.$usuario->usuario.').', $_SESSION['auth']['id']);
            /**
             * ./LOG
             */
        }

        return json_encode($retorno);
    }

    /**
     * PARCEIROS
     */
    static function parceirosNovo($p)
    {
        self::validaConexao(4);

        $sgc = new SGCTUR();
        $ret = $sgc->setParceirosNovo($_POST);
        return json_encode($ret);
    }

    static function parceirosLista($p)
    {
        self::validaConexao(4);

        $sgc = new SGCTUR();
        return json_encode($sgc->getParceirosLista($_POST['ini'], $_POST['qtd'], ['nome_fantasia', 'razao_social'], SGCTUR::ORDER_ASC));
    }

    static function parceirosNovoServico($p)
    {
        self::validaConexao(4);

        $retorno = [
            'success' => false,
            'mensagem' => ''
        ];
        if($_POST['servico'] == '') {
            $retorno['mensagem'] = Erro::getMessage(100);
            return json_encode($retorno);
        }

        $servico = json_decode($_POST['servico']);
        if($servico == null) {
            $retorno['mensagem'] = Erro::getMessage(100);
            return json_encode($retorno);
        }

        
        $parc = new Parceiro($p['id']);
        $ret = $parc->setNovoServico((array)$servico);
        if($ret === true) {
            $retorno['success'] = true;
        } else {
            $retorno['mensagem'] = Erro::getMessage(70);
        }

        return json_encode($retorno);
    }

    static function parceirosApagarServico($p)
    {
        self::validaConexao(4);

        $par = new Parceiro($p['id']);
        $ret = $par->setDeleteServico($p['sid']);
        
        return json_encode($ret);
    }

    static function parceirosSalvarServico($p)
    {
        self::validaConexao(4);

        $retorno = [
            'success' => false,
            'mensagem' => ''
        ];
        if($_POST['servico'] == '') {
            $retorno['mensagem'] = Erro::getMessage(100);
            return json_encode($retorno);
        }

        $servico = json_decode($_POST['servico']);
        if($servico == null) {
            $retorno['mensagem'] = Erro::getMessage(100);
            return json_encode($retorno);
        }

        $par = new Parceiro($p['id']);
        $ret = $par->setSalvarServico((array)$servico, $p['sid']);

        return json_encode($ret);
    }

    static function parceirosNovoFinanceiro($p)
    {
        self::validaConexao(4);

        $retorno = [
            'success' => false,
            'mensagem' => ''
        ];

        if($_POST['financeiro'] == '') {
            $retorno['mensagem'] = Erro::getMessage(100);
            return json_encode($retorno);
        }

        $financeiro = json_decode($_POST['financeiro']);
        if($financeiro == null) {
            $retorno['mensagem'] = Erro::getMessage(100);
            return json_encode($retorno);
        }

        $par = new Parceiro($p['id']);
        $ret = $par->setNovoFinanceiro((array)$financeiro);

        if($ret === true) {
            $retorno['success'] = true;
        } else {
            $retorno['mensagem'] = Erro::getMessage(70);
        }
        return json_encode($retorno);
    }

    static function parceirosSalvarFinanceiro($p)
    {
        self::validaConexao(4);

        $retorno = [
            'success' => false,
            'mensagem' => ''
        ];
        if($_POST['financeiro'] == '') {
            $retorno['mensagem'] = Erro::getMessage(100);
            return json_encode($retorno);
        }

        $financeiro = json_decode($_POST['financeiro']);
        if($financeiro == null) {
            $retorno['mensagem'] = Erro::getMessage(100);
            return json_encode($retorno);
        }

        $par = new Parceiro($p['id']);
        $ret = $par->setSalvarFinanceiro((array)$financeiro, $p['fid']);

        return json_encode($ret);
    }

    static function parceirosApagarFinanceiro($p)
    {
        self::validaConexao(4);

        $par = new Parceiro($p['id']);
        $ret = $par->setDeleteFinanceiro($p['fid']);
        
        return json_encode($ret);
    }

    static function parceirosHistoricoAdd($p)
    {
        self::validaConexao(4);
        $retorno = [
            'success' => false,
            'mensagem' => ''
        ];

        if(!isset($_POST['detalhes']) || !isset($_POST['etapa'])) {
            $retorno['mensagem'] = Erro::getMessage(20);
            return json_encode($retorno);
        }

        if($_POST['detalhes'] == '' || $_POST['etapa'] == '') {
            $retorno['mensagem'] = Erro::getMessage(100);
            return json_encode($retorno);
        }

        $par = new Parceiro($p['id']);
        $ret = $par->setHistoricoNovo($_POST['detalhes'], $_POST['etapa']);

        if($ret == false) {
            $retorno['mensagem'] = Erro::getMessage(70);
        } else {
            $retorno['success'] = true;
        }

        return json_encode($retorno);
    }

    static function parceirosHistoricoEditar($p)
    {
        self::validaConexao(4);
        $retorno = [
            'success' => false,
            'mensagem' => ''
        ];

        if(!isset($_POST['detalhes']) || !isset($_POST['etapa'])) {
            $retorno['mensagem'] = Erro::getMessage(20);
            return json_encode($retorno);
        }

        if($_POST['detalhes'] == '' || $_POST['etapa'] == '') {
            $retorno['mensagem'] = Erro::getMessage(100);
            return json_encode($retorno);
        }

        $par = new Parceiro($p['id']);
        return json_encode($par->setHistoricoEdita($p['hid'], $_POST['etapa'], $_POST['detalhes']));

    }

    static function parceirosHistoricoLista($p)
    {
        self::validaConexao(4);
        $retorno = [
            'success' => false,
            'mensagem' => ''
        ];

        $par = new Parceiro($p['id']);

        $res = $par->getHistorico($p['qtd'], $p['start']);
        $retorno['historico'] = $res;

        return json_encode($retorno);
    }

    static function parceirosApagarHistorico($p)
    {
        self::validaConexao(4);
        $par = new Parceiro($p['id']);
        
        return json_encode($par->setDeleteHistorico($p['hid']));

    }

    static function parceirosBuscar()
    {
        self::validaConexao(4);
        //return json_encode($_POST);

        $sgc = new SGCTUR();

        return json_encode($sgc->getParceirosBusca($_POST['busca']));
    }

    /**
     * ROTEIRO
     */
    static function roteirosNovo($p)
    {
        self::validaConexao(2);
        $retorno = [
            'success' => false,
            'mensagem' => ''
        ];

        $sgc = new SGCTUR();
        

        $ret = $sgc->setRoteiroNovo((array)json_decode($_POST['dados']));
        if($ret === true) {
            $retorno['success'] = true;
        } else {
            $retorno['mensagem'] = Erro::getMessage(240);
        }

        return json_encode($retorno);
    }

    static function roteirosLista($p)
    {
        self::validaConexao(2);

        $sgc = new SGCTUR();
        return json_encode($sgc->getRoteirosLista($_POST['ini'], $_POST['qtd'], ['ano', 'mes', 'nome'], [SGCTUR::ORDER_DESC, SGCTUR::ORDER_DESC, SGCTUR::ORDER_ASC]));
    }

    static function roteirosHistoricoNovo($p)
    {
        self::validaConexao(2);
        $retorno = [
            'success' => false,
            'mensagem' => ''
        ];


        if(!isset($_POST['detalhes']) || !isset($_POST['etapa'])) {
            $retorno['mensagem'] = Erro::getMessage(20);
            return json_encode($retorno);
        }

        if($_POST['detalhes'] == '' || $_POST['etapa'] == '') {
            $retorno['mensagem'] = Erro::getMessage(100);
            return json_encode($retorno);
        }

        $sgc = new SGCTUR();
        $roteiro = new Roteiro($p['id']);
        $ret = $roteiro->setHistoricoNovo($p['parcid'], $_POST['detalhes'], $_POST['etapa']);

        if($ret == false) {
            $retorno['mensagem'] = Erro::getMessage(70);
        } else {
            $retorno['success'] = true;
        }

        return json_encode($retorno);
    }

    static function roteirosTarifaEdita($p)
    {
        self::validaConexao(2);
        $retorno = [
            'success' => false,
            'mensagem' => ''
        ];

        // Verifica o JSON.
        if(json_decode($_POST['tarifas']) === NULL) {
            $retorno['mensagem'] = Erro::getMessage(10);
            return $retorno;
        }

        $roteiro = new Roteiro($p['id']);
        $ret = $roteiro->setTarifa($_POST['tarifas']);

        if($ret === true) {
            $retorno['success'] = true;
        } else {
            $retorno['mensagem'] = Erro::getMessage(70);
        }

        return json_encode($retorno);
    }

    static function roteirosSalvar($p)
    {
        self::validaConexao(2);
        $retorno = [
            'success' => false,
            'mensagem' => ''
        ];

        //$retorno['mensagem'] = print_r((array)json_decode($_POST['dados']));

        $rot = new Roteiro($p['id']);
        $rot->setDados((array)json_decode($_POST['dados']));
        $ret = $rot->salvar();

        if($ret === true) {
            $retorno['success'] = true;
        } else if($ret === false) {
            $retorno['mensagem'] = Erro::getMessage(241);
        } else {
            $retorno['mensagem'] = $ret;
            print_r($ret);
        }

        return json_encode($retorno);
    }

    static function roteirosApagar($p)
    {
        self::validaConexao(2);
        $retorno = [
            'success' => false,
            'mensagem' => ''
        ];

        $roteiro = new Roteiro($p['id']);
        if($roteiro->getDados() === false) {
            $retorno['mensagem'] = Erro::getMessage(243);
            return json_encode($retorno);
        }

        if($roteiro->apagar()) {
            $retorno['success'] = true;
        } else {
            $retorno['mensagem'] = Erro::getMessage(242);
        }

        return json_encode($retorno);
    }

    static function roteirosRestaurar($p)
    {
        self::validaConexao(2);
        $retorno = [
            'success' => false,
            'mensagem' => ''
        ];

        $roteiro = new Roteiro($p['id']);
        if($roteiro->getDados() === false) {
            $retorno['mensagem'] = Erro::getMessage(243);
            return json_encode($retorno);
        }

        if($roteiro->restaurar()) {
            $retorno['success'] = true;
        } else {
            $retorno['mensagem'] = Erro::getMessage(244);
        }

        return json_encode($retorno);
    }

    static function roteirosApagarLixeira($p)
    {
        self::validaConexao(2);
        $retorno = [
            'success' => false,
            'mensagem' => ''
        ];

        $roteiro = new Roteiro($p['id']);
        if($roteiro->getDados() === false) {
            $retorno['mensagem'] = Erro::getMessage(243);
            return json_encode($retorno);
        }

        if($roteiro->apagarLixeira()) {
            $retorno['success'] = true;
        } else {
            $retorno['mensagem'] = Erro::getMessage(245);
        }

        return json_encode($retorno);
    }

    static function roteiroCriarCopia($p)
    {
        self::validaConexao(2);
        $retorno = [
            'success' => false,
            'mensagem' => ''
        ];

        $cop = json_decode($_POST['roteiro']);

        $roteiro = new Roteiro($cop->id);
        $roteiroDados = $roteiro->getDados();

        if($roteiroDados === false) {
            // Retorna erro, pq roteiro não existe ou foi apagado.
            $retorno['mensagem'] = Erro::getMessage(243);
            return json_encode($retorno);
        }

        $roteiroDados->data_ini = $cop->data_ini;
        $roteiroDados->data_fim = $cop->data_fim;
        $roteiroDados->parceiros = json_decode($roteiroDados->parceiros);

        
        $sgc = new SGCTUR();
       
        $ret = $sgc->setRoteiroNovo((array)$roteiroDados);
        if($ret === false) {
            // Retorna erro, pq não conseguiu salvar o novo roteiro.
            $retorno['mensagem'] = Erro::getMessage(240);
            return json_encode($retorno);
        }
        

        // Busca o último roteiro lançado numa lista de últimos roteiros.
        $lista = $sgc->getRoteirosLista();
        $novoID = '';
        foreach($lista['roteiros'] as $l) {
            if($l->nome == $roteiroDados->nome && $l->data_ini == $roteiroDados->data_ini && $l->data_fim == $roteiroDados->data_fim) {
                $novoID = $l->id;
                break;
            }
        }

        $retorno['success'] = true;
        $retorno['roteiro']['id'] = $novoID;
        return json_encode($retorno);
    }

    static function roteiroAddCoordenador($p)
    {
        self::validaConexao(2);
        $retorno = [
            'success' => false,
            'mensagem' => ''
        ];

        $roteiro = new Roteiro($p['id']);
        if($roteiro->getDados() === false) {
            $retorno['mensagem'] = Erro::getMessage(243);
            return json_encode($retorno);
        }

        // Adiciona coordenador ao roteiro.
        $ret = $roteiro->setCoordenadorAdd((int)$p['coord']);
        if($ret === true) {
            $retorno['success'] = true;
        } else {
            $retorno['mensagem'] = Erro::getMessage(246);
        }

        return json_encode($retorno);
    }

    static function roteiroRemoveCoordenador($p)
    {
        self::validaConexao(2);
        $retorno = [
            'success' => false,
            'mensagem' => ''
        ];

        $roteiro = new Roteiro($p['id']);
        if($roteiro->getDados() === false) {
            $retorno['mensagem'] = Erro::getMessage(243);
            return json_encode($retorno);
        }

        // Remove coordenador do roteiro.
        $ret = $roteiro->setCoordenadorRemove((int)$p['coord']);
        if($ret === true) {
            $retorno['success'] = true;
        } else {
            $retorno['mensagem'] = Erro::getMessage(246);
        }

        return json_encode($retorno);
    }


    /**
     * MINHA CONTA
     */
    static function minhaContaSalvar()
    {
        self::validaConexao();

        $_POST['id'] = $_SESSION['auth']['id'];
        
        return self::usuariosSalvar();
        
    }

    static function minhaContaAlterarSenha()
    {
        self::validaConexao();

        $retorno = [
            'success' => false,
            'mensagem' => ''
        ];

        $u = new Usuario($_SESSION['auth']['id']);

        if($_POST['senha_atual'] == '') {
            $retorno['mensagem'] = Erro::getMessage(20);
        } else if($_POST['senha_atual'] == $_POST['senha1']) {
            $retorno['mensagem'] = Erro::getMessage(26);
        } else if($_POST['senha1'] !== $_POST['senha2']) {
            $retorno['mensagem'] = Erro::getMessage(21);
        } else {
            if($u->verificaSenha($_POST['senha_atual'])) {
                // Autoriza troca de senha
                if($u->setSenhaForced($_POST['senha1'])){
                    $retorno['success'] = true;
                    /**
                     * LOG
                     */
                    $log = new LOG();
                    $log->novo('Alterou sua senha de acesso.', $_SESSION['auth']['id'], 1);
                    /**
                     * ./LOG
                     */
                } else {
                    $retorno['mensagem'] = Erro::getMessage(8);

                }
            } else {
                $retorno['mensagem'] = Erro::getMessage(6);
            }
            
        }

        return json_encode($retorno);
    }

    static function minhaConta()
    {
        self::validaConexao();

        $u = new Usuario($_SESSION['auth']['id']);
        $ret = $u->getDados();
        return json_encode($ret);
    }

    static function minhaContaFotoNovo()
    {
        self::validaConexao();
        
        $u = new Usuario($_SESSION['auth']['id']);
        $ret['success'] = $u->setImagemPerfil($_FILES['avatar']['tmp_name']);
        if($ret['success'] == false) {
            $ret['mensagem'] = 'Aconteceu algum erro no servidor...';
        } else {
            $dados = $u->getDados();
            $_SESSION['auth']['avatar'] = $dados->avatar;
        }

        return json_encode($ret);
    }

    static function minhaContaFotoApagar()
    {
        $retorno = array(
            'success' => false,
            'mensagem' => ''
        );
        self::validaConexao();

        $u = new Usuario($_SESSION['auth']['id']);
        $res = $u->delImagemPerfil();

        if($res === true) {
            $retorno['success'] = true;
            $dados = $u->getDados();
            $_SESSION['auth']['avatar'] = $dados->avatar;
        } else {
            $retorno['mensagem'] = $res;
        }

        return json_encode($retorno);
    }

}