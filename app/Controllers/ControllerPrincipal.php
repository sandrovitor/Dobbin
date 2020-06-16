<?php

Use eftec\bladeone\BladeOne;
Use Cocur\Slugify\Slugify;
Use SGCTUR\SGCTUR;
Use SGCTUR\Cliente;
Use SGCTUR\Coordenador;
Use SGCTUR\Usuario;
Use SGCTUR\Cryptor;
Use SGCTUR\LOG;
Use SGCTUR\Erro;
Use SGCTUR\Parceiro;
Use SGCTUR\Roteiro;

class ControllerPrincipal 
{
    const VIEWS = '../views';
    const CACHE = '../cache';

    /**
     * Inicia roteador dentro do controlador.
     */
    static function router()
    {
        $router = new \AltoRouter();
        include('../routes/rotas.php');
        return $router;
    }

    /**
     * Inicia o BladeOne.
     */
    static function bladeStart()
    {
        $blade = new BladeOne(\ControllerPrincipal::VIEWS, \ControllerPrincipal::CACHE, BladeOne::MODE_AUTO);
        // Define variáveis globais a serem enviadas para a VIEW.
        $blade->share('router', self::router());

        // Retorna objeto BladeOne já configurado para rodar uma VIEW.
        return $blade;
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
        if(!isset($_SESSION['auth']) || !isset($_SESSION['auth']['status']) || $_SESSION['auth']['status'] != true) {
            http_response_code(511);
            exit(Erro::getMessage(7));
        }
        /**
         * Verifica validade dos dados na sessão.
         * 
         * Tempo mínimo: 5 min.
         * Tempo máximo: 60 min.
         */
        $tempo = 5; // Tempo para atualização, em minutos.
        $data = new DateTime($_SESSION['auth']['hora_dados']);
        if($tempo < 5) {
            $data->add(new DateInterval('PT5M'));
        } else if ($tempo > 60) {
            $data->add(new DateInterval('PT60M'));
        } else {
            $data->add(new DateInterval('PT'.(int)$tempo.'M'));
        }

        $hoje = new DateTime();
        if($hoje > $data) {
            // Atualiza.
            $sgc = new SGCTUR();
            if($sgc->atualizaSession() == false) {
                header('Location: /login');
                exit();
            }

        }

        // Controle de acesso!
        if($nivel > 0) {
            if((int)$_SESSION['auth']['nivel'] < $nivel) {
                // Interrompe. HTTP 403.
                http_response_code(403);
                header('HTTP/1.1 403 Acesso negado!');
                exit();
            }
        }
    }
    
    static function start()
    {
        
        if(!isset($_SESSION['auth']) || !isset($_SESSION['auth']['status']) || $_SESSION['auth']['status'] != true) {
            header('Location: /login');
            exit();
        }
        
        $blade = self::bladeStart();
        $sgc = new SGCTUR();

        return $blade->run("start", array(
            'sistema' => $sgc->system,
        ));
    }

    // Retorna versão do sistema.
    static function checkversion()
    {
        self::validaConexao();

        $sgc = new SGCTUR();
        $retorno = [
            'version' => $sgc->system->version,
        ];
        return json_encode($retorno);
    }

    /**
     * LOGIN
     */
    static function login()
    {
        if(
            isset($_SESSION['auth']) &&
            isset($_SESSION['auth']['status']) &&
            $_SESSION['auth']['status'] == true
        ) {
            header('Location: /');
            exit();
        } else {
            unset($_SESSION['auth']);
        }


        $sgc = new SGCTUR();
        $csrf = $sgc->geraChave(32);

        $_SESSION['csrf'] = $csrf;

        $blade = self::bladeStart();
        return $blade->run("login", array(
            'csrf' => $csrf,
            'sistema' => $sgc->system,
        ));
    }

    static function loga()
    {
        if (isset($_SESSION['auth']['status']) && $_SESSION['auth']['status'] == true) {
            header('Location: /');
            exit();
        }
        $sgc = new SGCTUR();

        // Valida CSRF;
        if(!isset($_POST['csrf']) || $_POST['csrf'] == '' || !isset($_SESSION['csrf'])) {
            //exit(Erro::getMessage(1));
            $_SESSION['mensagem'] = Erro::getMessage(1);
            header('Location: /login');
            exit();
        } else {
            if($_POST['csrf'] != $_SESSION['csrf']) {
                $_SESSION['csrf'] = $sgc->geraChave(32);
                //exit(Erro::getMessage(2));
                
                $_SESSION['mensagem'] = Erro::getMessage(2);
                header('Location: /login');
                exit();
            }
        }

        if(
            !isset($_POST['usuario']) || $_POST['usuario'] == '' ||
            !isset($_POST['senha']) || $_POST['senha'] == ''
        ) {
            //exit(Erro::getMessage(3));
            $_SESSION['mensagem'] = Erro::getMessage(3);
            header('Location: /login');
            exit();
        }

        // Invalida CSRF
        unset($_SESSION['csrf']);

        $ret = $sgc->loginAuth($_POST['usuario'], $_POST['senha']);
        if($ret['success'] == false) {
            //exit($ret['mensagem']);
            $_SESSION['mensagem'] = $ret['mensagem'];
            header('Location: /login');
            exit();
        } else {
            unset($_SESSION['mensagem']);
            header('Location: /');
            exit();
        }
    }

    static function logout()
    {
        $session = $_SESSION;
        session_unset();
        session_destroy();

        session_start();
        $_SESSION['mensagem'] = "Você saiu. Até mais, ".$session['auth']['nome']."...";
        header('Location: /login');
        exit();
    }

    /**
     * ./LOGIN
     */

    static function inicio()
    {
        self::validaConexao();

        $sgc = new SGCTUR();

        $blade = self::bladeStart();
        $retorno = array(
            'title' => '<i class="fas fa-thumbtack"></i> Início',
            'description' => 'Informações gerais sobre o sistema.',
            'page' => $blade->run("index", array(
                'sistema' => $sgc->getSistemaConsumo(),
            ))
        );

        return json_encode($retorno);
    }


    /**
     * 
     * 
     * ROTEIROS
     * 
     * 
     */

    static function roteiros($p)
    {
        self::validaConexao(2);
        $sgc = new SGCTUR();

        $listaRoteiros = $sgc->getRoteirosLista(0,10);

        $blade = self::bladeStart();
        $retorno = array(
            'title' => '<i class="fas fa-luggage-cart"></i> Roteiros',
            'description' => 'Roteiros definidos e quantidade de vagas.',
            'page' => $blade->run("roteiros.roteiros", array(
                'listaRoteiros' => $listaRoteiros,
            ))
        );

        return json_encode($retorno);
    }

    static function roteirosNovo($p)
    {
        self::validaConexao(2);

        $blade = self::bladeStart();
        $retorno = array(
            'title' => '<i class="fas fa-luggage-cart"></i> Roteiros > Novo',
            'description' => 'Crie um novo roteiro.',
            'page' => $blade->run("roteiros.roteirosNovo", array(
                
            ))
        );

        return json_encode($retorno);
    }

    static function roteirosSimulacao($p)
    {
        self::validaConexao(2);

        $blade = self::bladeStart();
        $retorno = array(
            'title' => '<i class="fas fa-luggage-cart"></i> Roteiros > Simulação',
            'description' => 'Simulação de roteiros, sem alterações.',
            'page' => $blade->run("roteiros.roteiroSimulacao", array(
                
            ))
        );

        return json_encode($retorno);
    }

    static function roteirosVer($p)
    {
        self::validaConexao(2);

        $rot = new Roteiro($p['id']);
        $roteiro = $rot->getDados();
        
        if($roteiro === false || $roteiro->deletado_em !== null) {
            http_response_code(404);
            header('HTTP/1.1 404 Não encontrado!');
            exit();
        }

        // Carrega parceiros
        if($roteiro->parceiros == '') {
            $roteiro->parceiros = array();
        } else {
            $parceiros = json_decode($roteiro->parceiros);
            $parc_array = array();

            if(is_array($parceiros)) {
                foreach($parceiros as $a) {
                    $par = new Parceiro($a);
                    $x = $par->getDados();
                    unset($x['financeiro'], $x['servico']);
                    $x['geral']->historico = $par->getHistorico(50,0,$p['id']);
                    array_push($parc_array, $x['geral']);
                }
    
            }
            
            $roteiro->parceiros = $parc_array;
        }

        // Criado por
        $u = new Usuario($roteiro->criado_por);
        $u = $u->getDados();
        if($u === false) {
            $criado_por = '<img src="/media/images/av/user00.png" height="25" style="border-radius:50%" class="mr-1"> <i>Usuário removido</i>';
        } else {
            $criado_por = '<img src="/media/images/av/'.$u->avatar.'" height="25" style="border-radius:50%" class="mr-1"> <i>'.$u->nome .' '. $u->sobrenome.'</i>';
        }

        $blade = self::bladeStart();
        $retorno = array(
            'title' => '<i class="fas fa-luggage-cart"></i> Roteiros > <span id="roteiroTitle"></span>',
            'description' => 'Detalhes do roteiro.',
            'page' => $blade->run("roteiros.roteirosVer", array(
                'roteiro' => $roteiro,
                'sgc' => new SGCTUR(),
                'criado_por' => $criado_por
            ))
        );

        return json_encode($retorno);
    }

    static function roteirosEditar($p)
    {
        self::validaConexao(2);

        $rot = new Roteiro($p['id']);
        $roteiro = $rot->getDados();
        if($roteiro === false) {
            http_response_code(404);
            header('HTTP/1.1 404 Não encontrado!');
            exit();
        }

        // Carrega parceiros
        if($roteiro->parceiros == '') {
            $roteiro->parceiros = array();
        } else {
            $parceiros = json_decode($roteiro->parceiros);
            $parc_array = array();
            foreach($parceiros as $a) {
                $par = new Parceiro($a);
                $x = $par->getDados();
                unset($x['financeiro'], $x['servico']);
                $x['geral']->historico = $par->getHistorico(50,0,$p['id']);
                array_push($parc_array, $x['geral']);
            }

            $roteiro->parceiros = $parc_array;
        }

        $blade = self::bladeStart();
        $retorno = array(
            'title' => '<i class="fas fa-luggage-cart"></i> Roteiros > Editar',
            'description' => 'Faça alterações no roteiro.',
            'page' => $blade->run("roteiros.roteirosEditar", array(
                'roteiro' => $roteiro,
                'sgc' => new SGCTUR(),
            ))
        );

        return json_encode($retorno);
    }

    static function roteirosLixeira($p)
    {
        self::validaConexao(2);
        $sgc = new SGCTUR();

        $listaRoteiros = $sgc->getRoteirosLixeira();

        $blade = self::bladeStart();
        $retorno = array(
            'title' => '<i class="fas fa-luggage-cart"></i> Roteiros > Lixeira',
            'description' => 'Roteiros excluídos e estão aguardando para serem removidos por completo.',
            'page' => $blade->run("roteiros.roteirosLixeira", array(
                'roteiros' => $listaRoteiros,
            ))
        );

        return json_encode($retorno);
    }

    static function roteirosLixeiraVer($p)
    {
        self::validaConexao(2);

        $rot = new Roteiro($p['id']);
        $roteiro = $rot->getDados();
        
        if($roteiro === false || $roteiro->deletado_em === null) {
            http_response_code(404);
            header('HTTP/1.1 404 Não encontrado!');
            exit();
        }

        // Carrega parceiros
        if($roteiro->parceiros == '') {
            $roteiro->parceiros = array();
        } else {
            $parceiros = json_decode($roteiro->parceiros);
            $parc_array = array();
            foreach($parceiros as $a) {
                $par = new Parceiro($a);
                $x = $par->getDados();
                unset($x['financeiro'], $x['servico']);
                $x['geral']->historico = $par->getHistorico(50,0,$p['id']);
                array_push($parc_array, $x['geral']);
            }

            $roteiro->parceiros = $parc_array;
        }

        // Criado por
        $u = new Usuario($roteiro->criado_por);
        $u = $u->getDados();
        if($u === false) {
            $criado_por = '<img src="/media/images/av/user00.png" height="25" style="border-radius:50%" class="mr-1"> <i>Usuário removido</i>';
        } else {
            $criado_por = '<img src="/media/images/av/'.$u->avatar.'" height="25" style="border-radius:50%" class="mr-1"> <i>'.$u->nome .' '. $u->sobrenome.'</i>';
        }

        // Deletado por
        $u = new Usuario($roteiro->criado_por);
        $u = $u->getDados();
        if($u === false) {
            $deletado_por = '<img src="/media/images/av/user00.png" height="25" style="border-radius:50%" class="mr-1"> <i>Usuário removido</i>';
        } else {
            $deletado_por = '<img src="/media/images/av/'.$u->avatar.'" height="25" style="border-radius:50%" class="mr-1"> <i>'.$u->nome .' '. $u->sobrenome.'</i>';
        }

        $blade = self::bladeStart();
        $retorno = array(
            'title' => '<i class="fas fa-luggage-cart"></i> Roteiros > <span id="roteiroTitle"></span>',
            'description' => 'Detalhes do roteiro.',
            'page' => $blade->run("roteiros.roteirosLixeiraVer", array(
                'roteiro' => $roteiro,
                'sgc' => new SGCTUR(),
                'criado_por' => $criado_por,
                'deletado_por' => $deletado_por
            ))
        );

        return json_encode($retorno);
    }

    /**
     * 
     * 
     * CLIENTES
     * 
     * 
     */

    static function clientes($p)
    {
        self::validaConexao();
        $sgc = new SGCTUR();

        $blade = self::bladeStart();
        $retorno = array(
            'title' => '<i class="fas fa-users"></i> Clientes',
            'description' => 'Gerencie seus clientes.',
            'page' => $blade->run("clientes", array(
                'clientes' => $sgc->getClientesLista(0, 20, ['criado_em'], SGCTUR::ORDER_DESC),
            ))
        );

        return json_encode($retorno);
    }

    static function clientesNovo($p)
    {
        self::validaConexao(2);

        $blade = self::bladeStart();
        $retorno = array(
            'title' => '<i class="fas fa-users"></i> Clientes > Novo',
            'description' => 'Salve seus novos clientes.',
            'page' => $blade->run("clientesNovo", array(
                
            ))
        );

        return json_encode($retorno);
    }

    static function clientesBuscar($p)
    {
        self::validaConexao();

        $sgc = new SGCTUR();

        $blade = self::bladeStart();
        $retorno = array(
            'title' => '<i class="fas fa-users"></i> Clientes > Buscar',
            'description' => 'Localize seus clientes.',
            'page' => $blade->run("clientesBuscar", array(
                'clientes' => $sgc->getClientesLista(0, 20, ['criado_em'], SGCTUR::ORDER_DESC),
                'totalClientes' => $sgc->getClientesTotal()
            ))
        );

        return json_encode($retorno);
    }

    static function clientesDatabase($p)
    {
        self::validaConexao();

        $sgc = new SGCTUR();
        $blade = self::bladeStart();
        $retorno = array(
            'title' => '<i class="fas fa-users"></i> Clientes > Base de Dados',
            'description' => 'Uma interface para sua base de dados dos clientes.',
            'page' => $blade->run("clientesDatabase", array(
                'totalClientes' => $sgc->getClientesTotal()
            ))
        );

        return json_encode($retorno);
    }

    static function clientesLixeira($p)
    {
        self::validaConexao(2);

        $sgc = new SGCTUR();
        $x = $sgc->getClientesLixeira();
        $blade = self::bladeStart();
        $retorno = array(
            'title' => '<i class="fas fa-users"></i> Clientes > Lixeira',
            'description' => 'Clientes que foram excluídos e estão aguardando para serem removidos por completo.',
            'page' => $blade->run("clientesLixeira", array(
                'clientes' => $x['clientes'],
            ))
        );

        return json_encode($retorno);
    }

    static function clientesVer($p)
    {
        self::validaConexao();


        $retorno = array(
            'success' => false,
            'mensagem' => '',
        );
        if(!isset($p['id']) || $p['id'] == '' || $p['id'] == 0) {
            http_response_code(404);
            header('HTTP/1.1 404 Não encontrado');
            exit();
        } else {
            $cliente = new Cliente((int)$p['id']);
            $dados = $cliente->getDados();

            if($dados === false) {
                http_response_code(404);
                $retorno['mensagem'] = Erro::getMessage(105);
                return json_encode($retorno);
            } else {
                $retorno['success'] = true;
                $retorno['cliente'] = $dados;

                if($retorno['cliente']->titular == 0) {
                    $retorno['cliente']->dependentes = array();
                    $x = $cliente->getDependentes();
                    
                    if(!empty($x)) {
                        foreach($x as $key => $valor) {
                            array_push($retorno['cliente']->dependentes, array('id'=> $key, 'nome' => $valor));
                        }
                    }
                } else {
                    $titular = new Cliente($retorno['cliente']->titular);
                    $x = $titular->getDados();

                    $retorno['cliente']->titular_nome = $x->nome;
                }

                

                return json_encode($retorno);
            }
        }
    }

    /**
     * 
     * 
     * COORDENADORES
     * 
     * 
     */
    static function coordenadores($p)
    {
        self::validaConexao(2);
        $sgc = new SGCTUR();

        $blade = self::bladeStart();
        $retorno = array(
            'title' => '<i class="fas fa-user-tie"></i> Coordenadores',
            'description' => 'Gerencie seus coordenadores.',
            'page' => $blade->run("coordenadores.coordenador", array(
                'coordenadores' => $sgc->getCoordenadoresLista(0, 20, ['criado_em'], SGCTUR::ORDER_DESC),
            ))
        );

        return json_encode($retorno);
    }

    static function coordenadorNovo($p)
    {
        self::validaConexao(2);

        $sgc = new SGCTUR();

        $blade = self::bladeStart();
        $retorno = array(
            'title' => '<i class="fas fa-user-tie"></i> Coordenadores > Novo',
            'description' => 'Salve os dados dos seus coordenadores.',
            'page' => $blade->run("coordenadores.coordenadorNovo", array(
                
            ))
        );

        return json_encode($retorno);
    }

    static function coordenadorBuscar($p)
    {
        self::validaConexao();

        $sgc = new SGCTUR();

        $blade = self::bladeStart();
        $retorno = array(
            'title' => '<i class="fas fa-user-tie"></i> Coordenadores > Buscar',
            'description' => 'Localize seus coordenadores.',
            'page' => $blade->run("coordenadores.coordenadorBuscar", array(
                'coordenadores' => $sgc->getCoordenadoresLista(0, 20, ['criado_em'], SGCTUR::ORDER_DESC),
                'totalCoordenadores' => $sgc->getCoordenadoresTotal()
            ))
        );

        return json_encode($retorno);
    }

    static function coordenadorDatabase($p)
    {
        self::validaConexao();

        $sgc = new SGCTUR();
        $blade = self::bladeStart();
        $retorno = array(
            'title' => '<i class="fas fa-user-tie"></i> Coordenadores > Base de Dados',
            'description' => 'Uma interface para sua base de dados dos seus coordenadores.',
            'page' => $blade->run("coordenadores.coordenadorDatabase", array(
                'totalCoordenadores' => $sgc->getCoordenadoresTotal()
            ))
        );

        return json_encode($retorno);
    }

    static function coordenadorLixeira($p)
    {
        self::validaConexao(2);

        $sgc = new SGCTUR();
        $x = $sgc->getCoordenadoresLixeira();
        $blade = self::bladeStart();
        $retorno = array(
            'title' => '<i class="fas fa-user-tie"></i> Coordenadores > Lixeira',
            'description' => 'Coordenadores que foram excluídos e estão aguardando para serem removidos por completo.',
            'page' => $blade->run("coordenadores.coordenadorLixeira", array(
                'coordenadores' => $x['coordenadores'],
            ))
        );

        return json_encode($retorno);
    }

    static function coordenadorVer($p)
    {
        self::validaConexao(2);

        $retorno = array(
            'success' => false,
            'mensagem' => '',
        );
        if(!isset($p['id']) || $p['id'] == '' || $p['id'] == 0) {
            http_response_code(404);
            header('HTTP/1.1 404 Não encontrado');
            exit();
        } else {
            $coord = new Coordenador((int)$p['id']);
            $dados = $coord->getDados();

            if($dados === false) {
                http_response_code(404);
                $retorno['mensagem'] = Erro::getMessage(105);
                return json_encode($retorno);
            } else {
                $retorno['success'] = true;
                $retorno['coordenador'] = $dados;
                return json_encode($retorno);
            }
        }
    }


    /**
     * 
     * 
     * PARCEIROS
     * 
     * 
     */
    static function parceiros()
    {
        self::validaConexao(4);

        $sgc = new SGCTUR();

        $blade = self::bladeStart();
        $retorno = array(
            'title' => '<i class="far fa-handshake"></i> Parceiros',
            'description' => 'Gerencie seus parceiros de negócios.',
            'page' => $blade->run("parceiros", array(
                'parceiros' => $sgc->getParceirosLista(),
            ))
        );

        return json_encode($retorno);
    }

    static function parceirosNovo()
    {
        self::validaConexao(4);
        $sgc = new SGCTUR();

        $bancos = $sgc->getListaBancos();

        $blade = self::bladeStart();
        $retorno = array(
            'title' => '<i class="far fa-handshake"></i> Parceiros > Novo',
            'description' => 'Cadastre seus parceiros de negócios.',
            'page' => $blade->run("parceirosNovo", array(
                'bancos' => $bancos['bancos'],
            ))
        );

        return json_encode($retorno);
    }

    static function parceirosDatabase($p)
    {
        self::validaConexao(4);

        $sgc = new SGCTUR();
        $blade = self::bladeStart();
        $retorno = array(
            'title' => '<i class="far fa-handshake"></i> Parceiros > Base de Dados',
            'description' => 'Uma interface para sua base de dados de parceiros.',
            'page' => $blade->run("parceirosDatabase", array(
                'totalClientes' => $sgc->getClientesTotal()
            ))
        );

        return json_encode($retorno);
    }

    static function parceirosVer($p)
    {
        self::validaConexao(4);
        $sgc = new SGCTUR();
        $par = new Parceiro($p['id']);
        $parceiro = $par->getDados();

        if($parceiro == null) {
            return self::parceiros();
        }

        $bancos = $sgc->getListaBancos();

        $blade = self::bladeStart();
        $retorno = array(
            'title' => '<i class="far fa-handshake"></i> Parceiros > <small id="parceiroTitle" class="text-uppercase"></small>',
            'description' => 'Veja detalhes dos seus parceiros de negócio.',
            'page' => $blade->run("parceirosVer", array(
                'bancos' => $bancos['bancos'],
                'parceiro' => $parceiro,
                'par' => $par,
            ))
        );

        return json_encode($retorno);
    }

    static function parceirosGetServico($p)
    {
        self::validaConexao(4);

        if((int)$p['id'] == 0 || (int)$p['sid'] == 0 || $p['id'] == '' || $p['sid'] == '') {
            http_response_code(404);
            header('Location: HTTP/1.1 404 Not Found');
            exit();
        }

        $par = new Parceiro($p['id']);
        return json_encode($par->getServico((int)$p['sid']));
    }

    static function parceirosGetFinanceiro($p)
    {
        self::validaConexao(4);

        if((int)$p['id'] == 0 || (int)$p['fid'] == 0 || $p['id'] == '' || $p['fid'] == '') {
            http_response_code(404);
            header('Location: HTTP/1.1 404 Not Found');
            exit();
        }

        $par = new Parceiro($p['id']);
        return json_encode($par->getFinanceiro((int)$p['fid']));
    }

    /**
     * 
     * 
     * USUÁRIOS
     * 
     * 
     */

    static function usuarios($p)
    {
        self::validaConexao(6);
        $sgc = new SGCTUR();

        $blade = self::bladeStart();
        $retorno = array(
            'title' => '<i class="fas fa-user-shield"></i> Usuários',
            'description' => 'Esses são os que têm acesso à sua plataforma de gestão.',
            'page' => $blade->run("usuarios", array(
                'usuarios' => $sgc->getUsuariosLista(),
            ))
        );

        return json_encode($retorno);
    }

    static function usuariosNovo($p)
    {
        self::validaConexao(6);

        $blade = self::bladeStart();
        $retorno = array(
            'title' => '<i class="fas fa-user-shield"></i> Usuários > Novo',
            'description' => 'Crie novos logins de acesso à plataforma.',
            'page' => $blade->run("usuariosNovo", array(
                
            ))
        );

        return json_encode($retorno);
    }

    static function usuariosBuscar($p)
    {
        self::validaConexao(6);

        $sgc = new SGCTUR();

        $blade = self::bladeStart();
        $retorno = array(
            'title' => '<i class="fas fa-user-shield"></i> Usuários > Buscar',
            'description' => 'Localize os usuários da plataforma.',
            'page' => $blade->run("usuariosBuscar", array(
                'usuarios' => $sgc->getUsuariosLista(),
                'totalUsuarios' => $sgc->getUsuariosTotal()
            ))
        );

        return json_encode($retorno);
    }

    static function usuariosVer($p)
    {
        self::validaConexao();

        $retorno = array(
            'success' => false,
            'mensagem' => '',
        );
        if(!isset($p['id']) || $p['id'] == '' || $p['id'] == 0) {
            http_response_code(404);
            header('HTTP/1.1 404 Não encontrado');
            exit();
        } else {
            $u = new Usuario($p['id']);
            $dados = $u->getDados();

            if($dados === false) {
                http_response_code(404);
                $retorno['mensagem'] = Erro::getMessage(205);
                return json_encode($retorno);
            } else {
                $retorno['success'] = true;
                $retorno['usuario'] = $dados;
                return json_encode($retorno);
            }
        }
    }

    static function usuariosDatabase($p)
    {
        self::validaConexao(6);

        $sgc = new SGCTUR();

        $blade = self::bladeStart();
        $retorno = array(
            'title' => '<i class="fas fa-user-shield"></i> Usuários > Base de Dados',
            'description' => 'Uma interface para sua base de dados dos clientes.',
            'page' => $blade->run("usuariosDatabase", array(
                'totalUsuarios' => $sgc->getUsuariosTotal()
            ))
        );

        return json_encode($retorno);
    }

    /**
     * 
     * LOG
     * 
     */
    static function log($p)
    {
        self::validaConexao();

        $blade = self::bladeStart();
        $log = new LOG();

        // Quantidade
        if(!isset($p['qtd'])) {
            $qtd = 25;
        } else if(isset($p['qtd']) && is_int((int)$p['qtd'])) {
            $qtd = (int)$p['qtd'];
        } else {
            http_response_code(403);
            header('HTTP/1.1 403 Proibido!');
            return false;
        }

        // Página
        if(!isset($p['pagina'])) {
            $pagina = 1;
        } else if(isset($p['qtd']) && is_int((int)$p['qtd'])) {
            $pagina = (int)$p['pagina'];
        } else {
            http_response_code(403);
            header('HTTP/1.1 403 Proibido!');
            return false;
        }

        $retorno = array(
            'title' => '<i class="fas fa-info-circle"></i> LOG',
            'description' => 'Registro de atividades dos usuários.',
            'page' => $blade->run("log", array(
                'logs' => $log->getRegistros($qtd, $pagina),
                'qtd' => $qtd,
                'pagina' => $pagina,
                'total' => $log->getTotal()
            ))
        );

        return json_encode($retorno);
    }

    /**
     * 
     * OFFLINE
     * 
     */
    static function offline($p)
    {
        self::validaConexao(3);
        $u = new Usuario($_SESSION['auth']['id']);

        $blade = self::bladeStart();
        $retorno = array(
            'title' => '<i class="fas fa-cloud-download-alt"></i> Offline',
            'description' => 'Acesse o banco de dados da plataforma offline.',
            'page' => $blade->run("offline", array(
                
            ))
        );

        return json_encode($retorno);
    }

    static function offlineDownloadFile($p)
    {
        self::validaConexao(3);

        
        if(!isset($_SESSION['auth']) || !isset($_SESSION['auth']['status']) || $_SESSION['auth']['status'] != true) {
            header('HTTP/1.1 403 Proibido!');
            exit();
        }

        // Insere os scripts, o token e e-mail dentro do arquivo. Lança o arquivo para download.
        $path = __DIR__.'/../../storage/others/';
        if(!file_exists($path.'SGCTUR.html')) {
            header('HTTP/1.1 404 Não encontrado!');
            exit();
        }

        $u = new Usuario($_SESSION['auth']['id']);
        $token = $u->getTokenAuth(TRUE);
        
        

        $html_arq = $path.'SGCTUR.html';
        $scripts_arq = $path.'scripts.txt';
        $scriptloc_arq = $path.'geral.min.js';
        $styles_arq = $path.'styles.txt';

        // HTML
        $hand = fopen($html_arq, 'r');
        $arquivo = fread($hand, filesize($html_arq));
        fclose($hand);

        // SCRIPTS
        $hand = fopen($scripts_arq, 'r');
        $scripts = fread($hand, filesize($scripts_arq));
        fclose($hand);

        // SCRIPT LOCAL
        $hand = fopen($scriptloc_arq, 'r');
        $script_local = fread($hand, filesize($scriptloc_arq));
        fclose($hand);

        // STYLES
        $hand = fopen($styles_arq, 'r');
        $styles = fread($hand, filesize($styles_arq));
        fclose($hand);

        // Manipula os arquivos
        $script_local = str_replace([
            '{{$tokenauth}}',
            '{{$mailauth}}',
            '{{$server}}',
        ],[
            $token,
            $_SESSION['auth']['email'],
            'https://'.$_SERVER['HTTP_HOST'].'/api/getclientes/'
        ],$script_local);


        $arquivo = str_replace([
            '{{scripts.txt}}',
            '{{localscript}}',
            '{{styles.txt}}'
        ], [
            $scripts,
            $script_local,
            $styles
        ], $arquivo);

        // Gera arquivo temporário
        $temporario = 'temp-'. bin2hex(openssl_random_pseudo_bytes(8)) .'.html';
        $hand = fopen($path.'temp/'.$temporario, 'a'); // Abre para escrita e posiciona ponteiro no fim do arquivo.
        fwrite($hand, $arquivo);

        unset($arquivo);

        /**
         * Resgata dados do BD
         */
        
        fwrite($hand, '<script>var rawClientes = [');
        $sgc = new SGCTUR();
        $clientes = $sgc->getClientesLista(0, $sgc->getClientesTotal())['clientes']; // Lista de clientes

        $itens = 0; // Conta total itens no BD
        $reg = 0; // Total de registros em uma página.
        $x = ''; // variável temporária.

        
        while($itens < count($clientes)) {
            $x = array();
            for($reg = 0; $reg < 1000 && $itens < count($clientes); $reg++) {
                array_push($x, $clientes[$itens]);
                $itens++;
            }

            // Escreve no arquivo.
            if($itens < count($clientes)) {
                fwrite($hand, "'".json_encode($x)."',");
            } else {
                fwrite($hand, "'".json_encode($x)."'];"); // Fecha arquivo.
            }
            $x = '';
        }
        
        /**
         * ./Resgata dados do BD
         */
        fwrite($hand, '</script>');
        fclose($hand);


        // Download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="SGCTUR-offline.html"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path.'temp/'.$temporario));
        flush(); // Empurra todo o fluxo do buffer para o browser.
        readfile($path.'temp/'.$temporario);
        //echo $arquivo;

        unlink($path.'temp/'.$temporario);
        exit();
        
    }
}