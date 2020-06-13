<?php
namespace SGCTUR;

class Cryptor
{
    protected $caracteres = 'abcdefghijlkmnopqrstuvxyzwABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    protected $metodo = 'aes-256-ctr';
    private $key = 'JW.ORG é demais.. Acesse o site.'; // Chave crua;
    private $iv = '68e5873ebd7caa1945c86ddc8ae77d9f'; // Vetor de inicialização em hexadecimal.
    private $enc_key; // Chave encriptada em binário.
    private $enc_iv; // Vetor de inicialização em binário.
    private $separador = ':|~|:';

    public function __construct()
    {
        if($this->key !== '') {
            $this->enc_key = openssl_digest($this->key, 'SHA256', false);
        }

        /*
        if($this->iv !== '') {
            $this->enc_iv = \hex2bin($this->iv);
        }
        */
    }

    public function getMetodosLista()
    {
        return \openssl_get_cipher_methods();
    }

    public function getMetodo()
    {
        return $this->metodo;
    }

    public function setKey(string $key)
    {
        $this->key = $key;
        $this->enc_key = openssl_digest($key, 'SHA256', false);
        return true;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function generateKey(int $tamanho = 16)
    {
        if($tamanho < 16) {
            return false;
        }

        $str = '';
        for($i = 0; $i < $tamanho; $i++) {
            $str .= $this->caracteres[mt_rand(0, 61)];
        }

        $this->setKey($str);
        return $str;
    }

    /**
     * Cria um vetor de inicialização automaticamente, do tamanho necessário para o método.
     * 
     * @return void
     */
    private function generateIV()
    {
        $iv = openssl_random_pseudo_bytes(\openssl_cipher_iv_length($this->metodo));
        $this->enc_iv = $iv;
        $this->iv = bin2hex($iv);
    }

    /**
     * Encripta um texto e retorna os dados criptografados e o vetor de inicialização.
     * 
     * @param string $texto Conteúdo a ser criptografado.
     * 
     * @return mixed Texto criptografado e vetor de inicialização ou FALSE caso falhe.
     */
    public function encrypt(string $texto)
    {
        $this->generateIV();

        $encrypted = openssl_encrypt($texto, $this->metodo, $this->enc_key, 0, $this->enc_iv);
        return $encrypted.$this->separador.$this->iv;
    }

    /**
     * Descriptografa um texto que foi encriptado com esta classe. Nenhum outro texto será decriptado.
     * 
     * @param string $textoCodificado Conteúdo a ser descriptografado.
     * 
     * @return mixed Texto descriptografado ou FALSE caso falhe.
     */
    public function decrypt(string $textoCodificado)
    {
        if(\strpos($textoCodificado, $this->separador) === false) {
            return false;
        }
        $texto = substr($textoCodificado, 0, \strpos($textoCodificado, $this->separador));
        $iv = substr($textoCodificado, \strpos($textoCodificado, $this->separador)+strlen($this->separador));
        $iv = \hex2bin($iv);

        $decrypted = openssl_decrypt($texto, $this->metodo, $this->enc_key, 0, $iv);
        return $decrypted;
    }

}