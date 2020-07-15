<?php
namespace SGCTUR;

abstract class Master
{
    protected $pdo;
    protected $driver = "";
    protected $host = "";
    protected $banco = "";
    protected $tabela = "";
    protected $username = "";
    protected $password = "";
    protected $prefix = "";
    protected $charset = "";
    public $system;

    protected $caracteres = 'abcdefghijlkmnopqrstuvxyzwABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    


    public function __construct()
    {
        $this->system = new \StdClass();

        $this->loadEnv();
        
        if($this->charset == '') {
            $dsn = $this->driver.':host='.$this->host.';dbname='.$this->banco;
        } else {
            $dsn = $this->driver.':host='.$this->host.';dbname='.$this->banco.';charset='.$this->charset;
        }
        

        try {
            $this->pdo = new \PDO($dsn, $this->username, $this->password);
        } catch(\PDOException $e) {
            echo '<div><h3>Impossível continuar: Erro no Banco de Dados.</h3></div>';
            die();
        }
        $this->pdo->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );

        
    }

    /**
     * Carrega arquivo de configuração do sistema. Arquivo ".env" e ".config".
     * Caso arquivo não seja encontrado, a execução é interrompida bruscamente.
     * 
     * @return void
     */
    private function loadEnv()
    {
        $caminhos = [__DIR__.'/../../.env', __DIR__.'/../../.config'];

        foreach($caminhos as $caminho) {
            if(file_exists($caminho)) {
                $handler = fopen($caminho, 'r');
                $arquivo = fread($handler, filesize($caminho));
                $config = explode("\n", $arquivo);
                foreach($config as $c) {
                    $a = explode('=', $c);
                    switch($a[0]) {
                        case 'DB_CONNECTION':
                            $this->driver = trim($a[1]);
                            break;

                        case 'DB_HOST':
                            $this->host = trim($a[1]);
                            break;

                        case 'DB_DATABASE':
                            $this->banco = trim($a[1]);
                            break;
                            
                        case 'DB_USERNAME':
                            $this->username = trim($a[1]);
                            break;

                        case 'DB_PASSWORD':
                            $this->password = trim($a[1]);
                            break;
                            
                        case 'DB_CHARSET':
                            $this->charset = trim($a[1]);
                            break;

                        case 'NAME':
                            $this->system->name = trim($a[1]);
                            break;

                        case 'DESCRIPTION':
                            $this->system->description = trim($a[1]);
                            break;

                        case 'VERSION':
                            $this->system->version = trim($a[1]);
                            break;

                        case 'SENHA_COST':
                            $this->system->senha_cost = (int)trim($a[1]);
                            break;

                        case 'DESENVOLVEDOR':
                            if($a[1] != '') {
                                $n = explode(';', trim($a[1]));
                                $this->system->desenvolvedor = array();
                                foreach($n as $b) {
                                    if($b != '') {
                                        array_push($this->system->desenvolvedor, trim($b));
                                    }
                                }
                            }
                            break;

                        case 'EMPRESA_NOME':
                            $this->system->empresa_nome = trim($a[1]);
                            break;

                        case 'EMPRESA_CNPJ':
                            $this->system->empresa_cnpj = trim($a[1]);
                            break;

                        
                    }
                }
            } else {
                exit('Arquivo de configuração não encontrado. Plataforma interrompeu sua operação até a correção do erro.');
            }
        }
    }

    /** 
     * Define um valor de configuração.
     * 
     * @param string $chave Item da configuração a ser alterado.
     * @param string $valor Novo valor do item.
     * 
     * @return bool Em caso de sucesso, retorna TRUE; Em caso de falha, retorna FALSE.
     */
    public function setEnv(string $chave, string $valor)
    {
        $caminhos = [__DIR__.'/../../.env', __DIR__.'/../../.config'];


        foreach($caminhos as $caminho) {
            if(file_exists($caminho)) {
                $handler = fopen($caminho, 'r');
                $arquivo = fread($handler, filesize($caminho));
                fclose($handler);
                $novoarq = array();
                $config = explode("\n", $arquivo);


                foreach($config as $lin) {
                    $c = explode('=',$lin);

                    if($c[0] === $chave) {
                        $c[1] = $valor;
                    }

                    $c = implode('=', $c);

                    array_push($novoarq, $c);
                }

                $novoarq = implode("\n", $novoarq);

                // Compara para confirmar se houve alterações
                if($novoarq !== $arquivo) {
                    // Salva o arquivo novo
                    $handler = fopen($caminho, 'w');
                    fwrite($handler,$novoarq);
                    fclose($handler);
                    break;

                }

                
            } else {
                // Arquivo não encontrado. Operação abortada.
                return false;
            }
        }
        
        return true;
    }

    /**
     * Gera uma string aleatória com letras maiúsculas, minúsculas e números.
     * 
     * @param int $qtdLetras Quantidade de caracteres.
     * 
     * @return string String aleatória.
     */
    protected function geraNome($qtdLetras = 5)
    {
        // Gera nome da imagem
        $nomeArq = '';
        for($i=0; $i < $qtdLetras; $i++) {
            $nomeArq .= $this->caracteres[mt_rand(0, 61)];
        }

        return $nomeArq;
    }

    /**
     * Alias do método geraNome().
     * Gera uma string aleatória com letras maiúsculas, minúsculas e números.
     * 
     * @param int $qtdLetras Quantidade de caracteres.
     * 
     * @return string String aleatória.
     */
    public function geraChave(int $qtdLetras = 5)
    {
        return $this->geraNome($qtdLetras);
    }

    /**
     * Remove acentos de uma string.
     * 
     * @param string $string String a ser modificada.
     * 
     * @return string String sem acentos.
     */
    protected function tirarAcentos($string)
    {
        return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/"),explode(" ","a A e E i I o O u U n N"),$string);
    }

    /**
     * Remove acentos e caracteres de uma string. Caracteres especiais serão substituidos por um hífen (-).
     * 
     * @param string $nome Nome ou string a ser modificada.
     * 
     * @return string String sem acentos e caracteres especiais.
     */
    protected function escapaNome($nome)
    {
        // Escapa nome
        $comAcentos = array('à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ü', 'ú', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'O', 'Ù', 'Ü', 'Ú');
        $semAcentos = array('a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'y', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', '0', 'U', 'U', 'U');
        
        $nomeNovo = str_replace($comAcentos, $semAcentos, $nome);
        
        $char_not = array('\\','/','|','?','+','=','§',':','~','^','!','@','#','$', '%','&','*');
        
        // Remove caracteres especiais.
        $nomeNovo = str_replace(' ', '-', addslashes(str_replace($char_not, '-', $nomeNovo)));

        return $nomeNovo;
    }

    /**
     * Retorna o nome do mês em PT.
     * 
     * @param mixed Número do mês (string|int).
     * 
     * @return string Nome do mês correspondente em PT.
     */
    protected function converteMes($mes) 
    {
        switch($mes) {
            case 1:
            case '1':
                return 'Janeiro';
                break;
                
            case 2:
            case '2':
                return 'Fevereiro';
                break;
                
            case 3:
            case '3':
                return 'Março';
                break;
                
            case 4:
            case '4':
                return 'Abril';
                break;
                
            case 5:
            case '5':
                return 'Maio';
                break;
                
            case 6:
            case '6':
                return 'Junho';
                break;
                
            case 7:
            case '7':
                return 'Julho';
                break;
                
            case 8:
            case '8':
                return 'Agosto';
                break;
                
            case 9:
            case'9':
                return 'Setembro';
                break;
                
            case 10:
            case '10':
                return 'Outubro';
                break;
                
            case 11:
            case '11':
                return 'Novembro';
                break;
                
            case 12:
            case '12':
                return 'Dezembro';
                break;
                
            default:
                return 'Inválido';
                break;
        }
    }

    /**
     * Formata número de telefone no formato: +55 99 9 9999-9999.
     * 
     * @param string Número de telefone, somente números.
     * 
     * @return string Número de telefone formatado.
     */
    protected function formataTelefone(string $numero)
    {
        return '+'.substr($numero, 0, 2) .' '. substr($numero, 2, 2) .' '. substr($numero, 4, 1) .' '. substr($numero, 5, 4) .'-'. substr($numero, 9);
    }

    /**
     * Transforma centavos em reais.
     * @param string $valor Valor em centavos, somente números.
     * 
     * @return string Valor em reais (vírgula separando casas decimais). Ex.: 1234,00.
     */
    public function converteCentavoParaReal(string $valor)
    {
        if((int)$valor < 10) {
            return '0,0'.(int)$valor;
        } else if((int)$valor < 100) {
            return '0,'.(int)$valor;
        } else {
            $valor = (int)$valor;
            $valor = (string)$valor;
            return substr($valor, 0, -2).','.substr($valor, -2, 2);
        }
    }
}