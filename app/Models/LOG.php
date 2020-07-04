<?php
namespace SGCTUR;

class LOG extends Master
{

    private $total;

    public function __construct()
    {
        parent::__construct();
        $abc = $this->pdo->query('SELECT * FROM log WHERE 1');
        $this->total = $abc->rowCount();
    }

    /**
     * Lança um evento no registro de atividades LOG.
     * 
     * @param string $evento Descreve o evento. Máximo de 400 caracteres.
     * @param int $usuario ID do usuário que disparou o evento. Usuário 0 é o SISTEMA.
     * @param int $grau Nível do registro. 1) Normal ou baixo; 2) Atenção; 3) Arriscado; 4) Perigoso!
     * 
     * @return void
     */
    public function novo(string $evento, int $usuario = 0, int $grau = 1)
    {
        if(\strlen($evento) > 300) {
            $evento = substr($evento, 0, 300);
        }

        if($grau <= 0) {
            $grau = 1;
        }

        $abc = $this->pdo->prepare('INSERT INTO log (id, grau, datahora, usuario, evento) VALUES (NULL, :grau, NOW(), :usuario, :evento)');
        $abc->bindValue(':grau', $grau, \PDO::PARAM_INT);
        $abc->bindValue(':usuario', $usuario, \PDO::PARAM_INT);
        $abc->bindValue(':evento', $evento, \PDO::PARAM_STR);

        $abc->execute();

    }

    /**
     * Retorna os últimso registros.
     * 
     * @param int $qtd Quantidade de registros retornados.
     * 
     * @return array
     */
    public function getRegistros(int $qtd = 100, int $pagina = 1)
    {
        if($qtd < 25) {
            $qtd = 25;
        }
        if($pagina < 1) {
            $pagina = 1;
        }

        // Verifica se a página existe.
        if(($pagina * $qtd) - $qtd < $this->total) {
            // Pagina existe. Cria uma sintaxe pro BD
            $limite = 'LIMIT '.(string)(($pagina * $qtd) - $qtd) .', '.(string)$qtd;
        } else {
            // pagina não existe. Retorna conjunto vazio.
            return array();
        }



        $abc = $this->pdo->query('SELECT log.*, log.usuario as usuid, CONCAT(login.nome, " ", login.sobrenome) as nome, login.avatar, login.usuario, login.nivel FROM log LEFT JOIN login ON log.usuario = login.id WHERE 1 ORDER BY id DESC '.$limite);
        $reg = $abc->fetchAll(\PDO::FETCH_OBJ);
        return $reg;
    }

    /**
     * Retorna quantidade de registros no LOG.
     * 
     * @return int
     */
    public function getTotal()
    {
        return $this->total;

    }
}