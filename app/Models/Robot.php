<?php
namespace SGCTUR;
Use SGCTUR\LOG;

class Robot extends Master
{
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
}