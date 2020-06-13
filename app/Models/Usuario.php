<?php
namespace SGCTUR;
use SGCTUR\LOG;

class Usuario extends Master
{
    const ORDER_ASC = 1;
    const ORDER_DESC = 0;
    private $dados;
    protected $dadosNovo = array();
    private $id = 0;

    public function __construct(int $id)
    {
        parent::__construct();

        if($id <= 0) {
            $this->dados = null;
            return false;
        }

        $this->id = $id;
        $this->loadInfoBD();
        
    }

    /**
     * Carrega as informações do BD de dados para o objeto.
     */
    private function loadInfoBD()
    {
        if($this->id !== 0) {
            $abc = $this->pdo->query('SELECT * FROM login WHERE id = '.$this->id);
            if($abc->rowCount() == 0) {
                $this->dados = null;
                return false;
            } else {
                $x = $abc->fetch(\PDO::FETCH_OBJ);
                unset($x->senha);
                $this->dados = $x;
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * Retorna dados do usuário.
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
     * Define imagem de perfil do usuário.
     * 
     * @param string $pathIMG Caminho do arquivo temporário. Se for vazio, a imagem default será definida.
     */
    public function setImagemPerfil(string $pathIMG = '')
    {
        if($this->dados == null) {
            return false;
        }
        $caminho = __DIR__.'/../../public_html/media/images/av/';

        if($pathIMG == '') {
            // Imagem default.
            $abc->query('UPDATE login SET avatar = "user00.png" WHERE id ='.$this->id);
            return true;
        } else {
            // Seta imagem.
            if(\file_exists($pathIMG)) {
                // verifica o tamanho do arquivo.
                if(filesize($pathIMG) > 2 * (1024*1024)) {
                    return false;
                }

                $imageInfo = \getimagesize($pathIMG);
                // Carrega imagem
                switch($imageInfo['mime']) {
                    case 'image/png': $raw = \imagecreatefrompng($pathIMG); break;

                    case 'image/bmp': $raw = \imagecreatefrombmp($pathIMG); break;

                    case 'image/jpeg': default: $raw = \imagecreatefromjpeg($pathIMG); break;
                }


                // Trata a imagem.
                $width = 120; // Largura máxima da imagem
                $height = 120; // Altura máxima da imagem

                $wAtual = $imageInfo[0];
                $hAtual = $imageInfo[1];

                $imagemNova = imagecreatetruecolor($width, $height);


                if($wAtual == $hAtual) {
                    // ORIENTAÇÃO: Quadrado perfeito
                    // Reduz o tamanho da imagem.
                    
                    if(\imagecopyresized($imagemNova, $raw, 0, 0, 0, 0, $width, $height, $wAtual, $hAtual) === false) {
                        return false;
                    }
                } else if($wAtual > $hAtual) {
                    // ORIENTAÇÃO: Paisagem
                    // Reduz o tamanho da imagem: altura fixa, largura proporcional.
                    // Corta a imagem.
                    $raw = \imagecrop($raw,['x' => round(($wAtual - $hAtual)/2), 'y' => 0, 'width' => $hAtual, 'height' => $hAtual]);

                    if(\imagecopyresized($imagemNova, $raw, 0, 0, 0, 0, $height, $height, $hAtual, $hAtual) === false) {
                        return false;
                    }

                } else {
                    // ORIENTAÇÃO: Retrato
                    // Reduz o tamanho da image: largura fixa, altura proporcional.
                    // Corta a imagem.
                    $raw = \imagecrop($raw,['x' => 0, 'y' => round(($hAtual - $wAtual)/2), 'width' => $wAtual, 'height' => $wAtual]);

                    if(\imagecopyresized($imagemNova, $raw, 0, 0, 0, 0, $width, $width, $wAtual, $wAtual) === false) {
                        return false;
                    }
                }

                if($imagemNova === null) {
                    return false;
                }

                // Cria um nome aleatório para o arquivo.
                $nome = '';

                do{
                    $nome = strtolower( $this->geraNome(5) );
                    $nome .= '.webp';
                } while(\file_exists($caminho . $nome));

                imagewebp($imagemNova, $caminho . $nome, 85);
                \imagedestroy($imagemNova);
                \imagedestroy($raw);

                $abc = $this->pdo->query('UPDATE login SET avatar = "'.$nome.'", atualizado_em = NOW() WHERE id = '.$this->id);
                if($this->dados->avatar != 'user00.png') {
                    unlink($caminho.$this->dados->avatar);
                }
                
                $this->loadInfoBD();
                /**
                 * LOG
                 */
                $log = new LOG();
                $log->novo('Trocou a foto de perfil.', $_SESSION['auth']['id'], 1);
                /**
                 * ./LOG
                 */
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Remove imagem de perfil do usuário.
     * 
     * @return mixed Retorna TRUE, em caso de sucesso; retorna string em caso de falha.
     */
    public function delImagemPerfil()
    {
        if($this->dados == null) {
            return false;
        }
        $caminho = __DIR__.'/../../public_html/media/images/av/';

        if($this->dados->avatar == 'user00.png') {
            return "Imagem padrão não pode ser removida.";
        } else {
            $abc = $this->pdo->query('UPDATE login SET avatar = "user00.png", atualizado_em = NOW() WHERE id = '.$this->id);
            \unlink($caminho.$this->dados->avatar);
            $this->dados->avatar = "user00.png";

            $this->loadInfoBD();
            /**
             * LOG
             */
            $log = new LOG();
            $log->novo('Removeu a foto de perfil.', $_SESSION['auth']['id'], 1);
            /**
             * ./LOG
             */
            return true;
        }
    }

    /**
     * Define novos dados do usuário.
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

        unset($dados['id']);
        foreach($dados as $key => $val) {
            if(isset($this->dados->$key) && $this->dados->$key != $val) {
                $this->dadosNovo[$key] = $val;
            }
        }
    }

    /**
     * Salva as alterações no cliente, usando método setDados().
     * 
     * @return mixed Em caso de sucesso, retorna TRUE; em caso de falha, retorna string ou NULL.
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
        $sql = 'UPDATE login SET ';
        foreach($this->dadosNovo as $key => $val) {
            $sql .= '`'.$key.'` = "'.$val.'",';

            switch($key)
            {
                case 'nome': break;

                case 'usuario': $campos[] = 'USUÁRIO'; break;
                case 'email': $campos[] = 'EMAIL'; break;
            }
        }

        //$sql = substr($sql, 0, -1);
        $sql .= ' atualizado_em = NOW() WHERE id = '.$this->id;

        // Verifica se haverá conflito de usuário ou e-mail.
        if(isset($this->dadosNovo['usuario']) || isset($this->dadosNovo['email'])) {
            if(isset($this->dadosNovo['usuario'])) {
                $abc = $this->pdo->prepare('SELECT id FROM login WHERE usuario = :usu');
                $abc->bindValue(':usu', $this->dadosNovo['usuario'], \PDO::PARAM_STR);
                $abc->execute();

                if($abc->rowCount() > 0) {
                    return Erro::getMessage(200);
                }
            }

            if(isset($this->dadosNovo['email'])) {
                $abc = $this->pdo->prepare('SELECT id FROM login WHERE email = :email');
                $abc->bindValue(':email', $this->dadosNovo['email'], \PDO::PARAM_STR);
                $abc->execute();

                if($abc->rowCount() > 0) {
                    return Erro::getMessage(201);
                }
            }
        }

        $abc = $this->pdo->query($sql);

        /**
         * LOG
         */
        $log = new LOG();
        if(!empty($campos)) {
            $log->novo('Alterou dados sensíveis (<strong>'.\implode(', ', $campos).'</strong>) do usuário <i>'.$this->dados->nome .' '. $this->dados->sobrenome .'</i> [Cód.: '.$this->id.'].', $_SESSION['auth']['id'], 2);
        } else {
            $log->novo('Alterou dados do usuário <i>'.$this->dados->nome .' '. $this->dados->sobrenome .'</i> [Cód.: '.$this->id.'].', $_SESSION['auth']['id'], 2);
        }

        if(isset($this->dadosNovo['nivel'])) {
            $log->novo('Definiu novo nível (nível '.$this->dados->nivel.' >> nível '.$this->dadosNovo['nivel'].') para o usuário <i>'.$this->dados->nome .' '. $this->dados->sobrenome .'</i> [Cód.: '.$this->id.'].', $_SESSION['auth']['id'], 3);
        }
        /**
         * ./LOG
         */

        $this->dadosNovo = array();

        // Carrega dados novamente.
        $this->loadInfoBD();

        return true;
    }

    /**
     * Define nova senha para o usuário.
     * 
     * @param string $senha Nova senha.
     * @return bool
     */
    public function setSenhaForced(string $senha)
    {
        if($this->dados === null) {
            return false;
        }
        if($senha == '') {
            return false;
        }

        $senhaOptions = array(
            "cost" => $this->system->senha_cost
        );

        $enc = \password_hash($senha, PASSWORD_DEFAULT, $senhaOptions);

        try {
            $abc = $this->pdo->prepare('UPDATE login SET senha = :senha, atualizado_em = NOW() WHERE id = '.$this->id);
            $abc->bindValue(':senha', $enc, \PDO::PARAM_STR);
            $abc->execute();
        } catch(\PDOException $e) {
            error_log('Erro ao definir senha do usuário['.$this->id.']: '. $e->getMessage(), 1);
            error_log($e->getMessage(), 0);
            return false;
        }

        return true;
    }

    /**
     * Verifica se a senha informada é igual a senha atual.
     * 
     * @param string $senha Senha atual
     * @return bool
     */
    public function verificaSenha(string $senha)
    {
        $abc = $this->pdo->query('SELECT senha FROM login WHERE id='.$this->id);
        $reg = $abc->fetch(\PDO::FETCH_OBJ);

        if(\password_verify($senha, $reg->senha)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * EXCLUI O USUÁRIO!
     * 
     * @return bool
     */
    public function apagar()
    {
        if($this->dados == null) {
            return false;
        }

        $abc = $this->pdo->query('DELETE FROM login WHERE login.id =' .$this->id);
        return true;
    }

}