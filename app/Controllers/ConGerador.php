<?php

Use eftec\bladeone\BladeOne;
Use Cocur\Slugify\Slugify;
Use SGCTUR\SGCTUR;
Use SGCTUR\Cliente;
Use SGCTUR\Coordenador;
Use SGCTUR\Usuario;
Use SGCTUR\Cryptor;
Use SGCTUR\LOG;
Use SGCTUR\Erro;
Use SGCTUR\Parceiro;
Use SGCTUR\Roteiro;
Use SGCTUR\Venda;
Use Dompdf\Dompdf, Dompdf\Options;

class ConGerador 
{
    const VIEWS = '../views';
    const CACHE = '../cache';
    const VIEWS_ABS = __DIR__.'/../../views/';

    /**
     * Inicia roteador dentro do controlador.
     */
    static function router()
    {
        $router = new \AltoRouter();
        include('../routes/rotas.php');
        return $router;
    }

    /**
     * Inicia o BladeOne.
     */
    static function bladeStart()
    {
        $blade = new BladeOne(\ControllerPrincipal::VIEWS, \ControllerPrincipal::CACHE, BladeOne::MODE_AUTO);
        // Define variáveis globais a serem enviadas para a VIEW.
        $blade->share('router', self::router());

        // Retorna objeto BladeOne já configurado para rodar uma VIEW.
        return $blade;
    }

    static function pdf($p)
    {

        $codigo = explode('-', $p['codigo']);
        if(count($codigo) < 4 || count($codigo) > 4) {
            header('HTTP/1.1 404');
            http_response_code(404);
            exit();
        }

        $hash = $codigo[1].$codigo[3];
        $vendaID = $codigo[0];
        $clienteID = $codigo[2];
        
        // Remove o zerofill
        while(substr($vendaID,0,1) == '0') {
            $vendaID = substr($vendaID, 1);
        }
        $vendaID = (int)hex2bin($vendaID);

        // Remove o zerofill
        while(substr($clienteID,0,1) == '0') {
            $clienteID = substr($clienteID, 1);
        }
        $clienteID = (int)hex2bin($clienteID);

        // Recupera vendas
        $venda = new Venda($vendaID);
        $v = $venda->getDados();

        // Verifica se o cliente ID está correto.
        if((int)$v->cliente_id !== $clienteID)  {
            // Cliente ID incorreto. Erro 404.
            header('HTTP/1.1 404');
            http_response_code(404);
            exit();
        }

        // Verifica se o hash está correto.
        // HASH = [bin2hex(hash(sha256, "DOBBIN".DATA_RESERVA(Y-m-d H:i:s), true))]
        $data_reserva = new \DateTime($v->data_reserva);
        if(bin2hex(hash('sha256', DOBBIN_FRASE_FIXA .$data_reserva->format('Y-m-d H:i:s'), true)) !== $hash) {
            // HASH inválido. Erro 404
            header('HTTP/1.1 404');
            http_response_code(404);
            exit();
        } 

        $blade = self::bladeStart();
        //var_dump($v);die;
        //return $blade->run("documentos.comprovante", array('v' => $v, 'venda' => $venda, 'system' => $venda->system)); die;

        // CONFIGURAÇÕES DO PDF
        $options = new Options();
        $options->set([
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true
        ]);
        $dom = new Dompdf($options);
        //$dom->setPaper('A5', 'landscape');
        $dom->setPaper('A4', 'portrait');
        
        $dom->loadHtml( $blade->run("documentos.comprovante", array('v' => $v, 'venda' => $venda, 'system' => $venda->system)) );
        $dom->render();

        if(!isset($p['opt'])) {
            $streamOpt = ["Attachment" => false];
        } else if($p['opt'] == 'download') {
            $streamOpt = ["Attachment" => true];
        } else {
            $streamOpt = ["Attachment" => false];
        }

        //var_dump($dom->output());

        $dom->stream('comprovante.pdf', $streamOpt);
        

        //var_dump(self::VIEWS_ABS);

    }

}