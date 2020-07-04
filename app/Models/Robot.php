<?php
namespace SGCTUR;
Use SGCTUR\LOG;
Use SGCTUR\SGCTUR;

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
}