<?php
namespace SGCTUR;

/**
 * Classe de controle de erros.
 * 
 * Cada erro na execução de um código, pode gerar um código de erro
 * que será convertido em texto informativo para o usuário com esta classe.
 */

class Erro 
{
    private $lista = [
        /** 
         * 0xx: GERAL
         */
        1   => 'Campo CSRF inválido.',
        2   => 'Verificação CSRF falhou.',
        3   => 'Informe seu usuário e senha corretamente. Campos em branco não são permitidos.',
        4   => 'Usuário não encontrado.',
        5   => 'Número de tentativas excedido.',
        6   => 'Senha incorreta.',
        7   => 'Faça login!',
        8   => 'Não foi possível concluir essa solicitação no momento.',
        9   => 'Falha nos parâmetros internos.',
        10  => 'O servidor não entendeu os dados enviados. Sugerimos que a página seja atualizada.',

        // Sistema
        20  => 'Dados incompletos. Por favor, preencha todos os campos.',
        21  => 'A senha e a confirmação da senha não são iguais.',
        22  => 'Endereço de e-mail já em uso. Informe outro.',
        23  => 'Nome de usuário já em uso. Informe outro.',
        24  => 'Seu nível de acesso não permite essa operação. Interrompido!',
        25  => 'Não há alterações para serem salvas.',
        26  => 'Nova senha não pode ser igual a senha atual.',

        //Banco de dados
        70  => 'Houve um erro interno na comunicação com a base de dados. Comunique desenvolvedor...',

        /**
         * 1xx: Clientes
         */
        100 => 'Dados enviados são inválidos.',
        101 => 'Taxa extra de casal não é um número válido.',
        102 => 'Taxa extra de casal inválida. Só é permitido uma vírgula.',
        103 => 'Taxa extra não é um número válido. Só use números e vírgula para separar real e centavos.',
        104 => 'O TITULAR precisa ser um código de um cliente (somente números).',
        105 => 'Cliente não encontrado.',
        106 => 'Não foi possível apagar o cliente.',
        107 => 'Não foi possível restaurar o cliente.',
        108 => 'Cliente não se encontra na lixeira.',


        
        /**
         * 2xx: Usuários e Parceiros e Roteiros
         */
        200 => 'Nome de usuário já está em uso.',
        201 => 'Endereço de e-mail já está em uso.',
        205 => 'Usuário não encontrado.',

        // Parceiros
        221 => 'O serviço não está vinculado ao parceiro. Não é possivel excluir.',
        222 => 'Este serviço não está vinculado ao parceiro.',
        223 => 'Serviço não encontrado.',
        224 => 'Dado financeiro não encontrado.',
        225 => 'Registro histórico não encontrado.',

        // Roteiros
        240 => 'Não foi possível lançar esse roteiro na plataforma. Ocorreu algum erro ao salvar. Informe desenvolvedor.',
    ];

    public static function getMessage(int $code)
    {
        $self = new self();

        if(isset($self->getLista()[$code])) {
            return $self->getLista()[$code]." [$code]";
        } else {
            return 'Erro desconhecido. Código: '.$code;
        }
    }

    private function getLista()
    {
        return $this->lista;
    }

    public static function log($mensagem)
    {

    }
}