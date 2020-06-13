<?php
namespace SGCTUR;
use SGCTUR\LOG;
use SGCTUR\Erro;

class Roteiro extends Master 
{
    const ORDER_ASC = 1;
    const ORDER_DESC = 0;

    private $id = null;
    private $dados;



    public function __construct(int $roteiroID)
    {
        parent::__construct();
        $this->id = $roteiroID;

        if($roteiroID <= 0) {
            $this->dados = null;
            return false;
        } else {
            $this->loadInfoBD();
        }
        

    }

    /**
     * Carrega as informações do BD de dados para o objeto.
     */
    private function loadInfoBD() 
    {
        if($this->id != 0) {
            $abc = $this->pdo->query('SELECT * FROM roteiros WHERE id = '.$this->id);
            if($abc->rowCount() == 0) {
                $this->dados = null;
                return false;
            } else {
                $reg = $abc->fetch(\PDO::FETCH_OBJ);
                $this->dados = $reg;
                
                // Decode os JSONs.
                $this->dados->despesas = json_decode($this->dados->despesas);
                $this->dados->lucro_previsto = json_decode($this->dados->lucro_previsto);
                $tarifa = stripslashes($this->dados->tarifa);
                $tarifa = json_decode($tarifa);
                if($tarifa != null) {
                    $this->dados->tarifa = $tarifa;
                }
                
                return true;
            }
        }
        return false;
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
     * Adiciona uma nova entrada no histórico deste roteiro.
     * 
     * @param int $parcid ID do parceiro.
     * @param string $detalhes Detalhes a serem inseridos.
     * @param string $etapa Etapa da negociação: CONTATO, PEDIDO DE BLOQUEIO, DESISTÊNCIA...
     * 
     * @return bool
     */
    public function setHistoricoNovo(int $parcid, string $detalhes, string $etapa = 'CONTATO')
    {
        if($this->dados == null) {
            return false;
        }

        try{
            $abc = $this->pdo->prepare("INSERT INTO `historico_negoc` (`id`, `empresa_id`, `roteiro_id`, `etapa`, `detalhes`, `criado_por`, `criado_em`) VALUES (NULL, $parcid, $this->id, :etapa, :det, :autor, NOW())");
            $abc->bindValue(':etapa', $etapa, \PDO::PARAM_STR);
            $abc->bindValue(':det', addslashes($detalhes), \PDO::PARAM_STR);
            $abc->bindValue(':autor', $_SESSION['auth']['id'], \PDO::PARAM_INT);

            $abc->execute();
            $d_i = new \DateTime($this->dados->data_ini);
            $d_f = new \DateTime($this->dados->data_fim);
            /**
             * LOG
             */
            $log = new LOG();
            $log->novo('Adicionou um registro no histórico do parceiro no ROTEIRO <i class="text-uppercase">'.$this->dados->nome.' ('. $d_i->format('d/m/Y') .' a '. $d_f->format('d/m/Y') .')</i>.', $_SESSION['auth']['id'], 1);
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
     * Salvar tabela de tarifas no roteiro.
     * 
     * @param string $tarifa Uma string JSON com as tarifas.
     * 
     * @return bool
     */
    public function setTarifa(string $tarifa)
    {
        if(strlen($tarifa) >= 600) {
            return false;
        }

        try {
            $abc = $this->pdo->prepare("UPDATE roteiros SET tarifa = :tarifa, atualizado_em = NOW() WHERE id = $this->id");

            $abc->bindValue(':tarifa', addslashes($tarifa), \PDO::PARAM_STR);

            $abc->execute();

            $d_i = new \DateTime($this->dados->data_ini);
            $d_f = new \DateTime($this->dados->data_fim);
            /**
             * LOG
             */
            $log = new LOG();
            $log->novo('Alterou a tabela de tarifas do ROTEIRO <i class="text-uppercase">'.$this->dados->nome.' ('. $d_i->format('d/m/Y') .' a '. $d_f->format('d/m/Y') .')</i>.', $_SESSION['auth']['id'], 1);
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

}