<?php
namespace SGCTUR;
use SGCTUR\LOG;
use SGCTUR\Erro;

class Coordenador extends Master
{
    private $id = null;
    private $dados;
    protected $dadosNovo = array();
    private $dependentes = array();
    private $deletado_em = null;

    public function __construct(int $coordID)
    {
        parent::__construct();
        if($coordID <= 0) {
            $this->dados = null;
            return false;
        }

        $this->id = $coordID;
        $this->loadInfoBD();
    }

    /**
     * Carrega as informações do BD de dados para o objeto.
     */
    private function loadInfoBD()
    {
        if($this->id  !== 0) {

            $abc = $this->pdo->query('SELECT coordenadores.*, CONCAT(login.nome, " ", login.sobrenome) as usuario FROM coordenadores LEFT JOIN login ON coordenadores.deletado_por = login.id WHERE coordenadores.id = '.$this->id);
            if($abc->rowCount() == 0) {
                $this->dados = null;
                return false;
            } else {
                $x = $abc->fetch(\PDO::FETCH_OBJ);
                
                $this->dados = $x;
                if($x->deletado_em !== null) {
                    $this->deletado_em = new \DateTime($x->deletado_em);
                }

                // Faixa etária    
                try{
                    $nasc = new \DateTime($this->dados->nascimento);
                    $hoje = new \DateTime();
                    $idade = $hoje->diff($nasc);
                    if($idade->y < 6) {
                        $this->dados->faixa_etaria = '0-5';
                    } else if($idade->y <= 12) {
                        $this->dados->faixa_etaria = '6-12';
                    } else if($idade->y < 60) {
                        $this->dados->faixa_etaria = 'ADULTO';
                    } else {
                        $this->dados->faixa_etaria = '60+';
                    }
                }catch(Exception $e) {
                    $this->dados->faixa_etaria = '-';
                }
                
                return true;
                
            }
            
        } else {
            return false;
        }
    }
    
    /**
     * Retorna os dados da ficha do coordenador.
     * 
     * @return mixed Se falha, retorna FALSE; se sucesso, retorna uma instância stdClass.
     */
    public function getDados()
    {
        if($this->dados == null) {
            return false;
        }

        $x = $this->dados;
        
        return $this->dados;
    }

    /**
     * Apaga o coordenador no modo "soft-delete" (o registro é removido automaticamente através de cronjob).
     * 
     * @return bool
     */
    public function apagar()
    {
        if($this->dados == null) {
            return false;
        }

        if($this->deletado_em !== null || $this->id == null) {
            return false;
        }

        $abc = $this->pdo->query('SELECT * FROM coordenadores WHERE id = '.$this->id);
        if($abc->rowCount() == 0) {
            // Coordenador não existe.
            return false;
        } else {
            $reg = $abc->fetch(\PDO::FETCH_OBJ);
            // Apaga (soft-delete).
            $abc = $this->pdo->query('UPDATE coordenadores SET deletado_em = NOW(), deletado_por = '.$_SESSION['auth']['id'].' WHERE id = '.$this->id);
            $this->loadInfoBD();
            /**
             * LOG
             */
            $log = new LOG();
            $log->novo('Excluiu o coordenador <i>'.$reg->nome.'</i> [Cód.: '.$reg->id.'].', $_SESSION['auth']['id'], 4);
            /**
             * ./LOG
             */

            return true;
        }
    }

    /**
     * Restaura o coordenador que está na lixeira (soft-delete).
     * 
     * @return bool
     */
    public function restaurar()
    {
        if($this->dados == null) {
            return false;
        }

        if($this->deletado_em == null || $this->id == null) {
            return false;
        }

        $abc = $this->pdo->query('SELECT * FROM coordenadores WHERE id = '.$this->id);
        if($abc->rowCount() == 0) {
            // Coordenador não existe.
            return false;
        } else {
            $reg = $abc->fetch(\PDO::FETCH_OBJ);
            // Restaura do soft-delete.
            $abc = $this->pdo->query('UPDATE coordenadores SET deletado_em = NULL, deletado_por = 0 WHERE id = '.$this->id);
            $this->loadInfoBD();
            /**
             * LOG
             */
            $log = new LOG();
            $log->novo('Restaurou da lixeira o coordenador <i>'.$reg->nome.'</i> [Cód.: '.$reg->id.'].', $_SESSION['auth']['id'], 3);
            /**
             * ./LOG
             */

            return true;
        }
    }

    /**
     * Define novos dados do coordenador.
     * 
     * @param array $dados Array associativo de parametros atualizados.
     * 
     * @return void
     */
    public function setDados(array $dados)
    {
        if($this->dados == null) {
            return null;
        }

        unset($dados['id'], $dados['criado_em'], $dados['atualizado_em'], $dados['dependente'], $dados['titular'], $dados['deletado_em'], $dados['deletado_por']);
        foreach($dados as $key => $val) {
            if(isset($this->dados->$key) && $this->dados->$key != $val) {
                $this->dadosNovo[$key] = $val;
            }
        }
    }

    /**
     * Salva as alterações no coordenador, usando o método setDados().
     * 
     * @return void
     */
    public function salvar()
    {
        if($this->dados == null) {
            return null;
        }

        if(empty($this->dadosNovo)) {
            return Erro::getMessage(25);
        }

        $campos = array();
        $sql = 'UPDATE coordenadores SET ';
        foreach($this->dadosNovo as $key => $val) {
            $sql .= '`'.$key.'` = "'.$val.'",';

            switch($key)
            {
                case 'nome': break;

                case 'email': case 'telefone': case 'rg': case 'cpf': case 'complemento':
                case 'bairro': case 'cep': case 'cidade': case 'estado': case 'alergia':
                    $campos[] = strtoupper($key); break;

                case 'endereco': $campos[] = 'ENDEREÇO'; break;
                case 'ponto_referencia': $campos[] = 'PONTO DE REFERÊNCIA'; break;
                case 'sangue': $campos[] = 'TIPO SANGUÍNEO'; break;
                case 'nascimento': $campos[] = 'DATA DE NASCIMENTO'; break;
                case 'estado_civil': $campos[] = 'ESTADO CIVIL'; break;
                case 'emergencia_nome': $campos[] = 'CONTATO EMERGÊNCIA'; break;
                case 'emergencia_tel': $campos[] = 'TELEFONE EMERGÊNCIA'; break;
                case 'taxa_extra_casal': if($this->dados->taxa_extra_casal != (int)$val) {$campos[] = 'TAXA EXTRA (CASAL)';} break;
            }
        }

        $sql = substr($sql, 0, -1);
        $sql .= ' WHERE id = '.$this->id;

        $abc = $this->pdo->query($sql);

        /**
         * LOG
         */
        $log = new LOG();
        if(!empty($campos)) {
            $log->novo('Alterou os campos <strong>'.\implode(', ', $campos).'</strong> do cliente <i>'.$this->dados->nome.'</i> [Cód.: '.$this->id.'].', $_SESSION['auth']['id'], 2);
        }

        if(isset($this->dadosNovo['titular']) && 0 == (int)$this->dadosNovo['titular'] ) {
            $log->novo('Definiu cliente <i>'.$this->dados->nome.'</i> como TITULAR.', $_SESSION['auth']['id'], 2);
        } else if (isset($this->dadosNovo['titular']) && 0 > (int)$this->dadosNovo['titular']) {
            $log->novo('Definiu cliente <i>'.$this->dados->nome.'</i> como DEPENDENTE.', $_SESSION['auth']['id'], 2);
        }

        if(isset($this->dadosNovo['nome'])) {
            $log->novo('Alterou o nome do cliente: <i>'.$this->dados->nome.'</i> >> <i>'.$this->dadosNovo['nome'].'</i> [Cód.: '.$this->id.'].', $_SESSION['auth']['id'], 3);
        }
        /**
         * ./LOG
         */
        
        
        $this->dadosNovo = array();

        // Carrega dados novamente.
        $this->loadInfoBD();
        
    }

    /**
     * EXCLUI O COORDENADOR (DEFINITIVAMENTE) DA LIXEIRA.
     * 
     * @return mixed Em caso de sucesso, retorna TRUE; em caso de falha, retorna string.
     */
    public function apagarLixeira()
    {
        if($this->dados == null) {
            return Erro::getMessage(105);
        }

        if($this->deletado_em == null) {
            return Erro::getMessage(108);
        }

        $abc=$this->pdo->query('DELETE FROM coordenadores WHERE coordenadores.id = '.$this->id);
        /**
         * LOG
         */
        $log = new LOG();
        $log->novo('Excluiu o coordenador <i>'.$this->dados->nome.'</i> [Cód. '.$this->id.'] definitivamente.', $_SESSION['auth']['id'], 4);
        /**
         * ./LOG
         */
        return true;
    }
}