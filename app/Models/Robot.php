<?php
namespace SGCTUR;
Use SGCTUR\LOG;
Use SGCTUR\SGCTUR, SGCTUR\Roteiro, SGCTUR\Cliente;

class Robot extends Master
{
    private $valorMaximoU = [ // Valor máximo sem sinal (Unsigned)
        'tinyint' => 255,
        'smallint' => 65535,
        'mediumint' => 16777215,
        'int' => 4294967295,
        'bigint' => 18446744073709551615, // (2^64)-1 => 18.446.744.073.709.551.615
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function deletaClientesLixeira()
    {
        // Verifica se o prazo de 72h expirou e remove.
        $data = new \DateTime();
        $data->add(new \DateInterval('PT72H'));

        $abc = $this->pdo->query('SELECT * FROM clientes WHERE deletado_em IS NOT NULL AND deletado_em <= "'.$data->format('Y-m-d H:i:s').'"');
        if($abc->rowCount() > 0) {
            // DELETA
            $log = new LOG();
            $log->novo('<b>Dobbin Robot:</b> "Excluí '.$abc->rowCount().' cliente(s) que estava(m) na lixeira há mais de 72h."',0,4);
            $abc = $this->pdo->query('DELETE FROM clientes WHERE deletado_em IS NOT NULL AND deletado_em <= "'.$data->format('Y-m-d H:i:s').'"');
        }
    }

    public function deletaCoordenadoresLixeira()
    {
        // Verifica se o prazo de 72h expirou e remove.
        $data = new \DateTime();
        $data->add(new \DateInterval('PT72H'));

        $abc = $this->pdo->query('SELECT * FROM coordenadores WHERE deletado_em IS NOT NULL AND deletado_em <= "'.$data->format('Y-m-d H:i:s').'"');
        if($abc->rowCount() > 0) {
            // DELETA
            $log = new LOG();
            $log->novo('<b>Dobbin Robot:</b> "Excluí '.$abc->rowCount().' coordenador(es) que estava(m) na lixeira há mais de 72h."',0,4);
            $abc = $this->pdo->query('DELETE FROM coordenadores WHERE deletado_em IS NOT NULL AND deletado_em <= "'.$data->format('Y-m-d H:i:s').'"');
        }
    }

    public function deletaRoteirosLixeira()
    {
        // Verifica se o prazo de 7 dias expirou e remove.
        $data = new \DateTime();
        $data->add(new \DateInterval('P7D'));

        $abc = $this->pdo->query('SELECT * FROM roteiros WHERE deletado_em IS NOT NULL AND deletado_em <= "'.$data->format('Y-m-d H:i:s').'"');
        if($abc->rowCount() > 0) {
            // DELETA
            $log = new LOG();
            $log->novo('<b>Dobbin Robot:</b> "Excluí '.$abc->rowCount().' roteiro(s) que estava(m) na lixeira há mais de 7 dias."',0,4);
            $abc = $this->pdo->query('DELETE FROM roteiros WHERE deletado_em IS NOT NULL AND deletado_em <= "'.$data->format('Y-m-d H:i:s').'"');
        }
    }

    public function checaLimiteBancoDados()
    {
        /**
         * Verfica o limite do banco de dados.
         * Ex.: Se um campo tinyint (máximo de 255) chegar em 90%, dispara um alerta. O tipo de campo precisa ser aumentado.
         */
        $dsn = $this->driver.':host='.$this->host.';dbname=information_schema';
        try {
            $pdo_alt = new \PDO($dsn, $this->username, $this->password);
        } catch(PDOException $e) {
            error_log('O Dobbin Robot não conseguiu acessar o banco de dados INFORMATION_SCHEMA. '.$e->getMessage());
            return false;
        }

        
        $sgc = new SGCTUR();
        $limites = $sgc->getSistemaConsumo();
        $alerta_str = '';
        $aviso_str = '';
        
        $abc = $pdo_alt->query("SELECT `TABLES`.`TABLE_NAME`, `TABLES`.`TABLE_ROWS`, `TABLES`.`AUTO_INCREMENT` FROM `TABLES` WHERE `TABLES`.`TABLE_SCHEMA` = '$this->banco'");
        if($abc->rowCount() > 0) {
            $tabelas = $abc->fetchAll(\PDO::FETCH_OBJ);
            //var_dump($tabelas, $limites);

            // Varre cada tabela em busca da chave atual AUTO_INCREMENT, e do seu valor máximo.
            foreach($tabelas as $tab) {
                if($tab->TABLE_NAME == 'login') {  // USUÁRIOS
                    // Verifica se o número de linhas está dentro do limite da plataforma.
                    if((int)$tab->TABLE_ROWS >= $limites['limite']['usuarios']) { // 100% ou mais
                        // Alerta!
                        $alerta_str .= '> TABELA "'.$tab->TABLE_NAME.'" atingiu <strong>100%</strong> ou mais do limite definido na plataforma.<br>';
                    } else if((int)$tab->TABLE_ROWS >= ($limites['limite']['usuarios'] * 0.9)) { // Maior que 90% do limite.
                        // Alerta!
                        $alerta_str .= '> TABELA "'.$tab->TABLE_NAME.'" atingiu <strong>90%</strong> ou mais do limite definido na plataforma.<br>';
                    }

                    // Verifica se o limite do BANCO DE DADOS foi atingido.
                    $def = $pdo_alt->query("SELECT DATA_TYPE FROM `COLUMNS` WHERE `TABLE_SCHEMA` = '$this->banco' AND `TABLE_NAME` = '$tab->TABLE_NAME' AND `EXTRA` = 'auto_increment'");
                    if($def->rowCount() > 0) { // Encontrada uma chave primária com auto_increment.
                        $tipo = $def->fetch(\PDO::FETCH_OBJ);
                        
                        if((int)$tab->AUTO_INCREMENT > $this->valorMaximoU[$tipo->DATA_TYPE] * 0.9) { // Se ultrapassar 90% do limite do banco de dados, gera um alerta
                            // Alerta
                            $alerta_str .= '> TABELA "'.$tab->TABLE_NAME.'" atingiu <strong>90%</strong> ou mais do limite definido PELO BANCO DE DADOS ('.$tipo->DATA_TYPE.').<br>';
                        } else {
                            // relatório básico.
                            $aviso_str .= '> TABELA "'.$tab->TABLE_NAME.'" possui <strong>'.$tab->TABLE_ROWS.' / '.$limites['limite']['usuarios'].'</strong> linhas, '.
                            'AUTO_INCREMENT está em <strong>'.$tab->AUTO_INCREMENT.' / '.$this->valorMaximoU[$tipo->DATA_TYPE].'</strong>.<br>';
                        }
                    }
                    
                } else if($tab->TABLE_NAME == 'parc_empresa') {  // PARCEIROS
                    // Verifica se o número de linhas está dentro do limite da plataforma.
                    if((int)$tab->TABLE_ROWS >= $limites['limite']['parceiros']) { // 100% ou mais
                        // Alerta!
                        $alerta_str .= '> TABELA "'.$tab->TABLE_NAME.'" atingiu <strong>100%</strong> ou mais do limite definido na plataforma.<br>';
                    } else if((int)$tab->TABLE_ROWS >= ($limites['limite']['parceiros'] * 0.9)) { // Maior que 90% do limite.
                        // Alerta!
                        $alerta_str .= '> TABELA "'.$tab->TABLE_NAME.'" atingiu <strong>90%</strong> ou mais do limite definido na plataforma.<br>';
                    }

                    // Verifica se o limite do BANCO DE DADOS foi atingido.
                    $def = $pdo_alt->query("SELECT DATA_TYPE FROM `COLUMNS` WHERE `TABLE_SCHEMA` = '$this->banco' AND `TABLE_NAME` = '$tab->TABLE_NAME' AND `EXTRA` = 'auto_increment'");
                    if($def->rowCount() > 0) { // Encontrada uma chave primária com auto_increment.
                        $tipo = $def->fetch(\PDO::FETCH_OBJ);
                        
                        if((int)$tab->AUTO_INCREMENT > $this->valorMaximoU[$tipo->DATA_TYPE] * 0.9) { // Se ultrapassar 90% do limite do banco de dados, gera um alerta
                            // Alerta
                            $alerta_str .= '> TABELA "'.$tab->TABLE_NAME.'" atingiu <strong>90%</strong> ou mais do limite definido PELO BANCO DE DADOS ('.$tipo->DATA_TYPE.').<br>';
                        } else {
                            // relatório básico.
                            $aviso_str .= '> TABELA "'.$tab->TABLE_NAME.'" possui <strong>'.$tab->TABLE_ROWS.' / '.$limites['limite']['parceiros'].'</strong> linhas, '.
                            'AUTO_INCREMENT está em <strong>'.$tab->AUTO_INCREMENT.' / '.$this->valorMaximoU[$tipo->DATA_TYPE].'</strong>.<br>';
                        }
                    }
                    
                } else if($tab->TABLE_NAME == 'clientes' || $tab->TABLE_NAME == 'parceiros' || $tab->TABLE_NAME == 'roteiros' || $tab->TABLE_NAME == 'vendas') {
                    // Verifica se o número de linhas está dentro do limite da plataforma.
                    if((int)$tab->TABLE_ROWS >= $limites['limite'][$tab->TABLE_NAME]) { // 100% ou mais
                        // Alerta!
                        $alerta_str .= '> TABELA "'.$tab->TABLE_NAME.'" atingiu <strong>100%</strong> ou mais do limite definido na plataforma.<br>';
                    } else if((int)$tab->TABLE_ROWS >= ($limites['limite'][$tab->TABLE_NAME] * 0.9)) { // Maior que 90% do limite.
                        // Alerta!
                        $alerta_str .= '> TABELA "'.$tab->TABLE_NAME.'" atingiu <strong>90%</strong> ou mais do limite definido na plataforma.<br>';
                    } 

                    // Verifica se o limite do BANCO DE DADOS foi atingido.
                    $def = $pdo_alt->query("SELECT DATA_TYPE FROM `COLUMNS` WHERE `TABLE_SCHEMA` = '$this->banco' AND `TABLE_NAME` = '$tab->TABLE_NAME' AND `EXTRA` = 'auto_increment'");
                    if($def->rowCount() > 0) { // Encontrada uma chave primária com auto_increment.
                        $tipo = $def->fetch(\PDO::FETCH_OBJ);
                        
                        if((int)$tab->AUTO_INCREMENT > $this->valorMaximoU[$tipo->DATA_TYPE] * 0.9) { // Se ultrapassar 90% do limite do banco de dados, gera um alerta
                            // Alerta
                            $alerta_str .= '> TABELA "'.$tab->TABLE_NAME.'" atingiu <strong>90%</strong> ou mais do limite definido PELO BANCO DE DADOS ('.$tipo->DATA_TYPE.').<br>';
                        } else {
                            // relatório básico.
                            $aviso_str .= '> TABELA "'.$tab->TABLE_NAME.'" possui <strong>'.$tab->TABLE_ROWS.' / '.$limites['limite'][$tab->TABLE_NAME].'</strong> linhas, '.
                            'AUTO_INCREMENT está em <strong>'.$tab->AUTO_INCREMENT.' / '.$this->valorMaximoU[$tipo->DATA_TYPE].'</strong>.<br>';
                        }
                    }
                } else {
                    // Demais tabelas só faz verificação básica do limite do BANCO DE DADOS.
                    $def = $pdo_alt->query("SELECT DATA_TYPE FROM `COLUMNS` WHERE `TABLE_SCHEMA` = '$this->banco' AND `TABLE_NAME` = '$tab->TABLE_NAME' AND `EXTRA` = 'auto_increment'");
                    if($def->rowCount() > 0) { // Encontrada uma chave primária com auto_increment.
                        $tipo = $def->fetch(\PDO::FETCH_OBJ);
                        
                        if((int)$tab->AUTO_INCREMENT > $this->valorMaximoU[$tipo->DATA_TYPE] * 0.9) { // Se ultrapassar 90% do limite do banco de dados, gera um alerta
                            // Alerta
                            $alerta_str .= '> TABELA "'.$tab->TABLE_NAME.'" atingiu <strong>90%</strong> ou mais do limite definido PELO BANCO DE DADOS ('.$tipo->DATA_TYPE.').<br>';
                        } else {
                            // relatório básico.
                            $aviso_str .= '> TABELA "'.$tab->TABLE_NAME.'" possui <strong>'.$tab->TABLE_ROWS.'</strong> linhas, '.
                            'AUTO_INCREMENT está em <strong>'.$tab->AUTO_INCREMENT.' / '.$this->valorMaximoU[$tipo->DATA_TYPE].'</strong>.<br>';
                        }
                    }
                }
            }

            //echo $alerta_str.'<hr>'.$aviso_str;

            if($alerta_str !== '') {
                \error_log($alerta_str, 1, $this->system->desenvolvedor[0]);
                $log = new LOG();
                $log->novo('<b>Dobbin Robot:</b> "Limites da plataforma estão quase no fim. Consulte/informe o desenvolvedor."',0,4);
            }
            return true;
        }
        
    }

    public function geraListaClientesFixa()
    {
        $hoje = new \DateTime();
        $hoje->sub(new \DateInterval('P2D'));
        $abc = $this->pdo->query("SELECT id FROM roteiros WHERE clientes = '' AND data_fim <= '".$hoje->format('Y-m-d')."'");
        if($abc->rowCount() > 0) {
            $lin = $abc->fetchAll(\PDO::FETCH_OBJ);
            foreach($lin as $l) { // Varre roteiro por roteiro.
                $rot = new Roteiro($l->id);
                $roteiro = $rot->getDados();
                $clientes = $rot->getClientesLista();
                //var_dump($clientes['clientes']);

                if($clientes['tipo'] == 'PROVISORIO') {
                    $fixo = [];
                

                    if($clientes['success'] == true && !empty($clientes['clientes'])) {
                        // Escreve lista de clientes.
                        foreach($clientes['clientes'] as $c) {
                            array_push($fixo, (object)$c);
                        }

                        $x = json_encode($fixo);
                        if(strlen($x) <= 65535) { // Abaixo do limite do tamanho do campo TEXT no BANCO.
                            $abc = $this->pdo->prepare("UPDATE roteiros SET clientes = :fixo WHERE id = $roteiro->id");
                            $abc->bindValue(':fixo', $x, \PDO::PARAM_STR);
                            $abc->execute();

                            $log = new LOG();
                            $log->novo('<b>Dobbin Robot:</b> "Gerei uma lista definitiva de clientes <a href="#roteiros/ver/'.$roteiro->id.'" target="_blank">deste roteiro</a>. Agora não é mais possível alterar."',0, 4);
                        } else {
                            $log = new LOG();
                            $log->novo('<b>Dobbin Robot:</b> "Não foi possível gerar uma lista definitiva de clientes <a href="#roteiros/ver/'.$roteiro->id.'" target="_blank">deste roteiro</a>. O campo no Banco de Dados não suporta tantos clientes."',0,4);
                        }
                        
                    } else if($clientes['success'] == true && empty($clientes['clientes'])) {
                        // Escreve lista de clientes VAZIA.
                        $abc = $this->pdo->query("UPDATE roteiros SET clientes = '[]' WHERE id = $roteiro->id");

                        $log = new LOG();
                        $log->novo('<b>Dobbin Robot:</b> "Gerei uma lista definitiva VAZIA de clientes <a href="#roteiros/ver/'.$roteiro->id.'" target="_blank">deste roteiro</a>. Agora não é mais possível alterar."',0, 4);
                    } else {
                        // Ocorreu algum erro.
                        $log = new LOG();
                        $log->novo('<b>Dobbin Robot:</b> "Não consegui gerar a lista definitiva de clientes <a href="#roteiros/ver/'.$roteiro->id.'" target="_blank">deste roteiro</a>."',0,1);
                    }
                }

            }
        }
        // FIM

    }

    public function enviaEmailAniversario()
    {
        $sgc = new SGCTUR();
        $aniv = $sgc->getClienteAniversario(date('Y-m-d'));
        $ano = date('Y');

        // Teste forçado de execução.
        /*
        $aniv = [
            'success' => true,
            'hoje' => [
                (object)['nome'=> 'Sandro Vitor Mendonça', 'email' => 'sandro_vitor@hotmail.com'],
                (object)['nome'=> 'Vanessa Reis', 'email' => 'tonaestradaviagens@hotmail.com']
            ]
        ];
        */

        if($aniv['success'] == true && !empty($aniv['hoje'])) {
            // Envia e-mails de aniversário para os clientes.
            $lista = $aniv['hoje'];
            $contaEmails = 0;
            foreach($lista as $cliente) {

                // Verifica se ele possui e-mail antes de enviar.
                if(isset($cliente->email) && $cliente->email != '') {
                    $cliente_nome = $cliente->nome;

                    $from = 'nao-responda@'.DOBBIN_LINK_EXTERNO;
                    $to = $cliente->email;
                    $subject = 'Feliz aniversário!';
                    $headers[] = 'MIME-Version: 1.0';
                    $headers[] = 'Content-type: text/html; charset=utf-8';
                    $headers[] = 'To: '.substr($cliente_nome, 0, \strpos($cliente_nome,' ')).' <'.$to.'>';
                    $headers[] = 'From: Relacionamento To Na Estrada Viagens <'.$from.'>';
                    $headers[] = 'Reply-To: '.substr($cliente_nome, 0, \strpos($cliente_nome,' ')).' <'.$to.'>';
                    $headers[] = 'X-Mailer: PHP/'.phpversion();
        
                
                $html = <<<DADOS
                    <html>
                        <head>
                            <title>Feliz aniversário!</title>
                        </head>
                        <body style="background-color: rgb(233,233,233); font-size: 16px; font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;padding-top:1rem;">
                            <div style="width:80%; max-width: 1080px; margin:0 auto;">
                                <a href="https://tonaestradaviagens.com.br/" target="_blank" style="text-decoration:none;margin:0;">
                                <div style="background-color:rgb(143,18,32); width:100%; padding: 2rem 0;">
                                    <h1 style="font-weight:normal;margin:0; padding: 0 1rem 2rem; color: rgb(250, 251, 245);">Prezado(a) <strong style="text-transform:uppercase;">{$cliente_nome}</strong>,</h1>
                                    <img style="text-align:center; max-width:100%;" src="https://tonaestradaviagens.com.br/media/images/AniversarioToNaEstrada.jpg">
                                </div>
                                </a>
                                <div style="background-color: rgb(250, 251, 245); color: rgb(143,18,32); font-size: 1.5rem; padding: 2rem 1rem; box-sizing:border-box; text-align:center;">
                                    Encontre sua nova parada:<br>
                                    <a href="https://tonaestradaviagens.com.br/" style="text-decoration:none; color: rgb(143,18,32)">https://tonaestradaviagens.com.br/</a>
                                </div>
                                <div style="padding: 1rem 0; color: rgb(200,200,200);">
                                    &copy;{$ano} To Na Estrada Viagens e Turismo
                                </div>
                            </div>
                        </body>
                    </html>
DADOS;

                    $contaEmails++;
                    if($to != '') { // Envia e-mail, somente se o destinatário não estiver em branco.
                        $send = mail($to, $subject, $html, implode("\r\n", $headers));
                    }
                    //sleep(2); // Espera 2 segundos para o servidor de e-mails disparar o e-mail e não congestionar.
                    unset($headers);
                }
            }
            /**
             * LOG
             */
            $log = new LOG();
            if($contaEmails == count($aniv['hoje'])) {
                $log->novo('<b>Dobbin Robot:</b> "Enviei '.$contaEmails.' e-mails de aniversário hoje."',0,1);
            } else {
                $log->novo('<b>Dobbin Robot:</b> "Enviei '.$contaEmails.' e-mails de aniversário hoje. Alguns dos clientes não possuem e-mail cadastrado para enviar."',0,1);
            }

        } else if($aniv['success'] == false) {
            /**
             * LOG
             */
            $log = new LOG();
            $log->novo('<b>Dobbin Robot:</b> "Não consegui recuperar os aniversariantes de hoje."',0,1);
        } else if(empty($aniv['hoje'])) {
            /**
             * LOG
             */
            $log = new LOG();
            $log->novo('<b>Dobbin Robot:</b> "Não temos aniversariantes de hoje."',0,1);
        }
        
    }
}