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
     * Valida conexão.
     * 
     * @param int $nivel Nível mínimo de acesso do método. Se não definido, o controle de acesso não será feito.
     */
    static function validaConexao(int $nivel = 0)
    {
        // Valida SESSION.
        // Escrever código
        return ControllerPrincipal::validaConexao($nivel);
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

    static function vendas($p)
    {
        self::validaConexao(3);
        //var_dump($p);

        // Verifica tipo de documento.
        if($p['subpagina'] == 'comprovante')
        {
            // Recupera vendas
            $venda = new Venda($p['id']);
            $v = $venda->getDados();
            
            if($v === false) {
                header('HTTP/1.1 404'); die;
            }

            // Gera o documento do comprovante.
            $blade = self::bladeStart();
            $documento = $blade->run("documentos.comprovante", array('v' => $v, 'venda' => $venda, 'system' => $venda->system));
            $documentoNome = 'COMPROVANTE '.$v->id;

            if(isset($p['opt3']) && $p['opt3'] == 'download') {
                // Envia para download
                return self::downloadPDF($documento, $documentoNome);
            } else {
                // Envia para exibição em tela
                return self::mostraPDF($documento, $documentoNome);
            }

        } else {
            header('HTTP/1.1 404'); die;
        }
    }

    static function roteiros($p)
    {
        self::validaConexao(2);

        switch($p['subpagina']) {
            case 'listapassageiros':
                $rot = new Roteiro($p['id']);
                $lista_cliente = $rot->getClientesLista();
                $lista_coord = $rot->getCoordenadoresLista();
                
                $blade = self::bladeStart();
                $documento = $blade->run('documentos.listaPassageiros2', array('clientes' => $lista_cliente['clientes'], 'coord' => $lista_coord['coordenadores'], 'roteiro' => $rot->getDados()));

                $documentoNome = 'Lista de Passag. - Roteiro '.$p['id'];
            break;

            case 'lista' :
                //var_dump($p);
                $rot = new Roteiro($p['id']);
                $lista = $rot->getLista($p['opt']);

                $clientes = $rot->getClientesLista()['clientes'];
                $coords = $rot->getCoordenadoresLista()['coordenadores'];

                //var_dump($lista);
                $blade = self::bladeStart();
                if($lista === false) {
                    header('HTTP/1.1 404');
                    die;
                } else {
                    // ARQUIVO PDF GERADO E ARMAZENADO NO BANCO
                    if($lista->bin_pdf != null && $lista->bin_pdf_data > $lista->atualizado_em) {
                        if((isset($p['opt3']) && $p['opt3'] == 'download') || (isset($p['opt2']) && $p['opt2'] == 'download')) {
                            // FAZ DOWNLOAD
                            header("Content-Type: application/pdf");
                            header('Content-Disposition: attachment; filename="'.$lista->nome.'.pdf"');
                            header("Content-Length: $lista->tamanho");
                            print($lista->bin_pdf);
                            die;
                        } else {
                            // MOSTRA NO NAVEGADOR
                            header("Content-type: application/pdf");
                            print($lista->bin_pdf);
                            die;
                        }

                    } else if($lista->tipo == 'hospedagem') { // GERA LISTA DE HOSPEDAGEM
                        $documento = $blade->run('documentos.listaHospedagem', array('clientes' => $clientes, 'coord' => $coords, 'roteiro' => $rot->getDados(), 'lista' => $lista));
                        $documentoNome = $lista->nome;

                        // Salvar arquivo PDF no BD.
                        $pdf = self::outputPDF($documento, $documentoNome);
                        $rot->setListaBinPDF($lista->id, $pdf);
                    } else { // GERA LISTA DE TRANSPORTE
                        $documento = $blade->run('documentos.listaTransporte', array('clientes' => $clientes, 'coord' => $coords, 'roteiro' => $rot->getDados(), 'lista' => $lista));
                        $documentoNome = $lista->nome;

                        // Salvar arquivo PDF no BD.
                        $pdf = self::outputPDF($documento, $documentoNome);
                        $rot->setListaBinPDF($lista->id, $pdf);
                    }

                    // Saída diferenciada. PDF já gerado, faz download ou exibe inline.
                    if((isset($p['opt3']) && $p['opt3'] == 'download') || (isset($p['opt2']) && $p['opt2'] == 'download')) {
                        header("Content-Type: application/pdf");
                        header('Content-Disposition: attachment; filename="'.$lista->nome.'.pdf"');
                        print($pdf);
                    } else {
                        header("Content-type: application/pdf");
                        print($pdf);
                    }

                    die;
                }
                /**
                 * 
                 * 
                 * ATIVAR LINKS DOS PDF DAS LISTAS
                 * 
                 * 
                 */
            break;

            default:die;break;
        }

        
        // Define a saída.
        if((isset($p['opt3']) && $p['opt3'] == 'download') || (isset($p['opt2']) && $p['opt2'] == 'download')) {
            return self::downloadPDF($documento, $documentoNome);
        } else {
            return self::mostraPDF($documento);
        }
    }

    static function listas($p)
    {

    }

    static function mostraPDF(string $conteudo = '', string $titulo = 'Documento')
    {
        // CONFIGURAÇÕES DO PDF
        $options = new Options();
        $options->set([
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true
        ]);
        $dom = new Dompdf($options);
        //$dom->setPaper('A5', 'landscape');
        $dom->setPaper('A4', 'portrait');
        $dom->loadHtml( $conteudo );
        $dom->render();

        
        // Não faz download. Exibe na tela.
        $streamOpt = ["Attachment" => false];

        //var_dump($dom->output());

        $dom->stream($titulo.'.pdf', $streamOpt);
    }

    static function downloadPDF(string $conteudo = '', string $titulo = 'Documento')
    {
        // CONFIGURAÇÕES DO PDF
        $options = new Options();
        $options->set([
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true
        ]);
        $dom = new Dompdf($options);
        //$dom->setPaper('A5', 'landscape');
        $dom->setPaper('A4', 'portrait');
        $dom->loadHtml( $conteudo );
        $dom->render();


        // Faz download do arquivo.
        $streamOpt = ["Attachment" => true];

        //var_dump($dom->output());

        $dom->stream($titulo.'.pdf', $streamOpt);
    }

    static function outputPDF(string $conteudo = '', string $titulo = 'Documento')
    {
        // CONFIGURAÇÕES DO PDF
        $options = new Options();
        $options->set([
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true
        ]);
        $dom = new Dompdf($options);
        //$dom->setPaper('A5', 'landscape');
        $dom->setPaper('A4', 'portrait');
        $dom->loadHtml( $conteudo );
        $dom->render();


        // Não faz download. Exibe na tela.
        $streamOpt = ["Attachment" => false];

        return $dom->output();
    }
}