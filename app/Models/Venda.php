<?php
namespace SGCTUR;
use SGCTUR\LOG;
use SGCTUR\Erro;

class Venda extends Master
{
    private $id = null;
    private $dados;

    public function __construct(int $vendaID)
    {
        parent::__construct();
        if($vendaID <= 0) {
            $this->dados = null;
            $this->dependentes = null;
            return false;
        } else {
            $this->id = $vendaID;
            $this->loadInfoBD();
        }
    }

    /**
     * Carrega as informações do BD de dados para o objeto.
     */
    private function loadInfoBD()
    {
        if($this->id  !== 0) {
            $abc = $this->pdo->query("SELECT vendas.*, clientes.nome as cliente_nome, roteiros.nome as roteiro_nome, roteiros.data_ini as roteiro_data_ini, ".
            "roteiros.data_fim as roteiro_data_fim, CONCAT(login.nome, ' ', login.sobrenome) as usuario_nome FROM vendas ".
            "LEFT JOIN clientes ON vendas.cliente_id = clientes.id ".
            "LEFT JOIN roteiros ON vendas.roteiro_id = roteiros.id ".
            "LEFT JOIN login ON vendas.usuario_id = login.id ".
            "WHERE vendas.id = $this->id");
            if($abc->rowCount() == 0) {
                $this->dados = null;
                return false;
            } else {
                $this->dados = $abc->fetch(\PDO::FETCH_OBJ);
                return true;
            }
        } else {
            return false;
        }
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
     * Adiciona cliente na lista.
     * 
     * @param int $cid ID do cliente.
     * @return bool
     */
    public function setListaClienteNovo(int $cid)
    {
        if($this->dados == null) {
            return false;
        }

        $lista_clientes = $this->dados->lista_clientes;
        $lista_clientes = json_decode($lista_clientes);

        if($lista_clientes == null) {
            $lista_clientes = array();
        }

        if(empty($lista_clientes)) {
            array_push($lista_clientes, $cid);
        } else if(count($lista_clientes) < (int)$this->dados->clientes_total) {
            array_push($lista_clientes, $cid);
        } else {
            return false;
        }

        try {
            $abc = $this->pdo->prepare("UPDATE vendas SET lista_clientes = :lc WHERE id = $this->id");
            $abc->bindValue(':lc', json_encode($lista_clientes), \PDO::PARAM_STR);
            $abc->execute();
            
            return true;
        } catch(PDOException $e) {
            error_log($e->getMessage(), 0);

            return false;
        }
    }

    /**
     * Define o status da venda.
     * 
     * @param string $situacao Situação da venda: Reserva, Aguardando, Paga, Cancelada, Devolvida.
     * @param string $outro Se situação "PAGA": informar forma de pagamento; se situação "DEVOLVIDA": informar valor devolvido em centavos.
     * 
     * @return bool
     */
    public function setSituacao(string $situacao, string $outro = '')
    {
        if($this->dados == null) {
            return false;
        }

        $atual = $this->dados->status;

        switch($situacao)
        {
            case 'Reserva':
                if($atual == 'Devolvida' || $atual == 'Cancelada' || $atual == 'Paga') {
                    return false;
                } else {
                    try{
                        $abc = $this->pdo->query("UPDATE vendas SET status = '".$situacao."' WHERE id = $this->id");
                        return true;
                    } catch(PDOException $e) {
                        error_log($e->getMessage(), 0);
                        return false;
                    }
                }
            break;


            case 'Aguardando':
                if($atual == 'Devolvida' || $atual == 'Cancelada' || $atual == 'Paga') {
                    return false;
                } else {
                    try{
                        $abc = $this->pdo->query("UPDATE vendas SET status = '".$situacao."', data_venda = NOW() WHERE id = $this->id");
                        return true;
                    } catch(PDOException $e) {
                        error_log($e->getMessage(), 0);
                        return false;
                    }
                }
            break;


            case 'Paga':
                if($atual == 'Devolvida' || $atual == 'Cancelada') {
                    return false;
                } else {
                    switch($outro) {
                        case 'Crédito':
                        case 'Débito':
                        case 'Boleto':
                        case 'Digital':
                        case 'Transferência':
                        case 'Dinheiro':
                        case 'Outro':
                            $f = $outro; break;

                        default: return false; break;
                    }

                    try{
                        $abc = $this->pdo->query("UPDATE vendas SET status = '".$situacao."', forma_pagamento = '".$f."', data_pagamento = NOW(), data_venda = IF(data_venda IS NULL, NOW(), data_venda) WHERE id = $this->id");
                        return true;
                    } catch(PDOException $e) {
                        error_log($e->getMessage(), 0);
                        return false;
                    }
                }
            break;


            case 'Cancelada':
                if($atual == 'Devolvida' || $atual == 'Paga') {
                    return false;
                } else {
                    try{
                        $abc = $this->pdo->query("UPDATE vendas SET status = '".$situacao."', data_cancelado = NOW() WHERE id = $this->id");
                        return true;
                    } catch(PDOException $e) {
                        error_log($e->getMessage(), 0);
                        return false;
                    }
                }
            break;


            case 'Devolvida':
                if($atual == 'Devolvida' || $atual == 'Cancelada' || $atual == 'Reserva' || $atual == 'Aguardando') {
                    return false;
                } else {
                    $valor = (int)$outro;
                    try{
                        $abc = $this->pdo->query("UPDATE vendas SET status = '".$situacao."', data_estorno = NOW(), valor_devolvido = $valor WHERE id = $this->id");
                        return true;
                    } catch(PDOException $e) {
                        error_log($e->getMessage(), 0);
                        return false;
                    }
                }
            break;

            default:
                return false;
            break;
        }
    }
}