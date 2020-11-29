<?php

Use eftec\bladeone\BladeOne;
Use Cocur\Slugify\Slugify;
Use SGCTUR\SGCTUR, SGCTUR\Cliente, SGCTUR\Coordenador, SGCTUR\Usuario;
Use SGCTUR\LOG, SGCTUR\Erro;
Use SGCTUR\Parceiro, SGCTUR\Roteiro, SGCTUR\Venda;

// Controlador de janelas suspensas / modais dinâmicos.
class ControllerJan
{
    const VIEWS = '../views';
    const CACHE = '../cache';

    /**
     * Inicia roteador dentro do controlador.
     */
    static function router()
    {
        return ControllerPrincipal::router();
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

    static function vendasLoad($p)
    {
        self::validaConexao(3);

        $retorno = array(
            'success' => false,
            'mensagem' => '',
            'page' => '',
        );

        if($p['vid'] == 0) {
            $retorno['mensagem'] = Erro::getMessage(9);
        } else {
            $venda = new Venda($p['vid']);
            $ret = $venda->getDados();

            if($ret === false) {
                $retorno['mensagem'] = Erro::getMessage(302);
            } else {
                $retorno['success'] = true;
                $retorno['venda'] = $ret;

                // Cria as variáveis de data e hora.
                $hoje = new DateTime();
                $data_ini = new DateTime($ret->roteiro_data_ini);
                $data_fim = new DateTime($ret->roteiro_data_fim);
                $data_reserva = new DateTime($ret->data_reserva);
                if($ret->data_venda == NULL) {
                    $data_venda = '-';
                } else {
                    $data_venda = new DateTime($ret->data_venda);
                    $data_venda = $data_venda->format('d/m/Y H:i:s');
                }

                if($ret->data_pagamento == NULL) {
                    $data_pagamento = '-';
                } else {
                    $data_pagamento = new DateTime($ret->data_pagamento);
                    $data_pagamento = $data_pagamento->format('d/m/Y H:i:s');
                }

                if($ret->data_cancelado == NULL) {
                    $data_cancelado = '-';
                } else {
                    $data_cancelado = new DateTime($ret->data_cancelado);
                    $data_cancelado = $data_cancelado->format('d/m/Y H:i:s');
                }

                if($ret->data_estorno == NULL) {
                    $data_estorno = '-';
                } else {
                    $data_estorno = new DateTime($ret->data_estorno);
                    $data_estorno = $data_estorno->format('d/m/Y H:i:s');
                }

                // VALORES DA VENDA
                $valorTotal = $venda->converteCentavoParaReal($ret->valor_total);
                $descontoTotal = $venda->converteCentavoParaReal($ret->desconto_total);
                $valorPago = $venda->converteCentavoParaReal($ret->total_pago);
                $multasTotal = $venda->converteCentavoParaReal($ret->multas);
                $multaPago = $venda->converteCentavoParaReal($ret->multas_pago);
                $totalVenda = $venda->converteCentavoParaReal($ret->multas + $ret->valor_total);
                $totalVendaPago = $venda->converteCentavoParaReal($ret->multas_pago + $ret->total_pago);
                $totalVendaRestante = $venda->converteCentavoParaReal(($ret->multas + $ret->valor_total) - ($ret->multas_pago + $ret->total_pago));

                
                // Situação do pacote e lista de botões
                $btnDescontoEdita = $btnQtdEdita = ''; $info_pagamento = '';
                switch($ret->status) {
                    case 'Reserva':
                        $situacao = 'Reserva';
                        $btnDescontoEdita = '';
                        $btnQtdEdita = '';
                    break;
                    case 'Aguardando':
                        $situacao = 'Aguardando pagamento';
                        $info_pagamento = "<div class=\"d-flex flex-wrap mt-2\"> <div class=\"border px-2 py-1 mb-2 mr-2\"> <strong>Forma de Pagamento:</strong> $ret->forma_pagamento </div>".
                        "<div class=\"border px-2 py-1 mb-2 mr-2\"><strong>Dia Vencimento: </strong> $ret->vencimento</div>".
                        "<div class=\"border px-2 py-1 mb-2 mr-2\"><strong>Parcela(s): </strong> $ret->parcelas"."x</div></div>";
                        
                        $detalhes = json_decode($ret->detalhes_pagamento);
                        if($detalhes == NULL) { $detalhes = [];}

                        $tabParcelas = '<div class="small mt-2 d-flex border border-info p-2 flex-wrap flex-row">';
                        
                        /**
                         * ##### REESCRITA
                         */
                        
                        $contador = 0;
                        for($i = 0; $i < (int)$ret->parcelas; $i++) {
                            if(isset($detalhes[$contador])) {
                                // O detalhe do pagamento está definido.
                                // Verifica se é o pagamento de uma parcela ou outra coisa.
                                //if(isset($detalhes[$contador]->forma) && $detalhes[$contador]->forma == 'CreditoConta') {
                                if($detalhes[$contador]->parcela == 0) {
                                    // Pagamento não é parcela
                                    $i--;
                                    
                                    if($detalhes[$contador]->forma == 'CreditoConta') {
                                        $tituloParcela = '<strong class="small text-primary">CRÉDITO</strong>';
                                    } else {
                                        $tituloParcela = '<strong class="small text-primary">'.$detalhes[$contador]->forma.'</strong>';
                                    }
                                } else {
                                    // Pagamento de parcela
                                    
                                    if((int)$ret->parcelas == 1) {
                                        $tituloParcela = '<strong class="small">À Vista</strong>';
                                    } else {
                                        $tituloParcela = '<strong class="small">'.($i+1).'ª parcela</strong>';
                                    }
                                    
                                }

                                // VALOR E DATA
                                $data = new \DateTime($detalhes[$contador]->data);
                                $data = '<small>'.$data->format('d/m/Y').'</small>';
                                $valorParcela = '<div class="my-2 font-weight-bold bg-info text-white rounded px-1">R$ '.$venda->converteCentavoParaReal($detalhes[$contador]->valor).'</div>';

                            } else {
                                // Não tem detalhes pagamento.
                                // Exibe parcela vazia.
                                
                                $tituloParcela = '<strong class="small">'.($i+1).'ª parcela</strong>';
                                $valorParcela = '<div class="my-2 font-weight-bold text-info small rounded px-1">--</div>';
                                $data = '<small>--/--/----</small>';
                            }

                            $tabParcelas .= '<div class="border text-center shadow-sm p-2 mx-1 mb-2"> '.$tituloParcela.'<br>'.$valorParcela.
                            ''.$data.'</div>';
                            

                            $contador++;
                        }
                        /**
                         * ##### REESCRITA
                         */
                        
                        unset($contador);
                        
                        $tabParcelas .= '</div>';
                        $info_pagamento .= $tabParcelas;
                        unset($tabParcelas);

                    break;
                    case 'Pagando':
                        $situacao = 'Pagando';
                        $info_pagamento = "<div class=\"d-flex flex-wrap mt-2\"> <div class=\"border px-2 py-1 mb-2 mr-2\"> <strong>Forma de Pagamento:</strong> $ret->forma_pagamento </div>".
                        "<div class=\"border px-2 py-1 mb-2 mr-2\"><strong>Valor Pago: </strong> <span class=\"text-info\">R$ ".$totalVendaPago."</span></div>".
                        "<div class=\"border px-2 py-1 mb-2 mr-2\"><strong>Valor Restante: </strong> <span class=\"text-danger\">R$ ".$totalVendaRestante."</span></div>".
                        "<div class=\"border px-2 py-1 mb-2 mr-2\"><strong>Parcela(s): </strong> $ret->parcelas"."x</div></div>";
                        
                        $detalhes = json_decode($ret->detalhes_pagamento);
                        if($detalhes == NULL) { $detalhes = [];}

                        $tabParcelas = '<div class="small mt-2 d-flex border border-info p-2 flex-wrap flex-row">';
                        /**
                         * ##### REESCRITA
                         */
                        
                        $contador = 0;
                        for($i = 0; $i < (int)$ret->parcelas; $i++) {
                            if(isset($detalhes[$contador])) {
                                // O detalhe do pagamento está definido.
                                // Verifica se é o pagamento de uma parcela ou outra coisa.
                                //if(isset($detalhes[$contador]->forma) && $detalhes[$contador]->forma == 'CreditoConta') {
                                if($detalhes[$contador]->parcela == 0) {
                                    // Pagamento não é parcela
                                    $i--;
                                    
                                    if($detalhes[$contador]->forma == 'CreditoConta') {
                                        $tituloParcela = '<strong class="small text-primary">CRÉDITO</strong>';
                                    } else {
                                        $tituloParcela = '<strong class="small text-primary">'.$detalhes[$contador]->forma.'</strong>';
                                    }
                                } else {
                                    // Pagamento de parcela
                                    
                                    if((int)$ret->parcelas == 1) {
                                        $tituloParcela = '<strong class="small">À Vista</strong>';
                                    } else {
                                        $tituloParcela = '<strong class="small">'.($i+1).'ª parcela</strong>';
                                    }
                                    
                                }

                                // VALOR E DATA
                                $data = new \DateTime($detalhes[$contador]->data);
                                $data = '<small>'.$data->format('d/m/Y').'</small>';
                                $valorParcela = '<div class="my-2 font-weight-bold bg-info text-white rounded px-1">R$ '.$venda->converteCentavoParaReal($detalhes[$contador]->valor).'</div>';

                            } else {
                                // Não tem detalhes pagamento.
                                // Exibe parcela vazia.
                                
                                $tituloParcela = '<strong class="small">'.($i+1).'ª parcela</strong>';
                                $valorParcela = '<div class="my-2 font-weight-bold text-info small rounded px-1">--</div>';
                                $data = '<small>--/--/----</small>';
                            }

                            $tabParcelas .= '<div class="border text-center shadow-sm p-2 mx-1 mb-2"> '.$tituloParcela.'<br>'.$valorParcela.
                            ''.$data.'</div>';
                            

                            $contador++;
                        }
                        /**
                         * ##### REESCRITA
                         */
                        
                        unset($contador);
                        $tabParcelas .= '</div>';
                        $info_pagamento .= $tabParcelas;
                        unset($tabParcelas);
                    break;
                    case 'Paga':
                        $situacao = 'Pago';
                        $info_pagamento = "<div class=\"d-flex flex-wrap mt-2\"> <div class=\"border px-2 py-1 mb-2 mr-2\"> <strong>Forma de Pagamento:</strong> $ret->forma_pagamento </div>".
                        "<div class=\"border px-2 py-1 mb-2 mr-2\"><strong>Parcela(s): </strong> $ret->parcelas"."x</div></div>";

                        $detalhes = json_decode($ret->detalhes_pagamento);
                        if($detalhes == NULL) { $detalhes = [];}

                        $tabParcelas = '<div class="small mt-2 d-flex border border-info p-2 flex-wrap flex-row">';


                        /**
                         * ##### REESCRITA
                         */
                        
                        $contador = 0;
                        for($i = 0; $i < (int)$ret->parcelas; $i++) {
                            if(isset($detalhes[$contador])) {
                                // O detalhe do pagamento está definido.
                                // Verifica se é o pagamento de uma parcela ou outra coisa.
                                //if(isset($detalhes[$contador]->forma) && $detalhes[$contador]->forma == 'CreditoConta') {
                                if($detalhes[$contador]->parcela == 0) {
                                    // Pagamento não é parcela
                                    $i--;

                                    if($detalhes[$contador]->forma == 'CreditoConta') {
                                        $tituloParcela = '<strong class="small text-primary">CRÉDITO</strong>';
                                    } else {
                                        $tituloParcela = '<strong class="small text-primary">'.$detalhes[$contador]->forma.'</strong>';
                                    }
                                } else {
                                    // Pagamento de parcela
                                    
                                    if((int)$ret->parcelas == 1) {
                                        $tituloParcela = '<strong class="small">À Vista</strong>';
                                    } else {
                                        $tituloParcela = '<strong class="small">'.($i+1).'ª parcela</strong>';
                                    }
                                    
                                }

                                // VALOR E DATA
                                $data = new \DateTime($detalhes[$contador]->data);
                                $data = '<small>'.$data->format('d/m/Y').'</small>';
                                $valorParcela = '<div class="my-2 font-weight-bold bg-info text-white rounded px-1">R$ '.$venda->converteCentavoParaReal($detalhes[$contador]->valor).'</div>';

                            } else {
                                // Não tem detalhes pagamento.
                                // Exibe parcela vazia.
                                
                                $tituloParcela = '<strong class="small">'.($i+1).'ª parcela</strong>';
                                $valorParcela = '<div class="my-2 font-weight-bold text-info small rounded px-1">--</div>';
                                $data = '<small>--/--/----</small>';
                            }

                            $tabParcelas .= '<div class="border text-center shadow-sm p-2 mx-1 mb-2"> '.$tituloParcela.'<br>'.$valorParcela.
                            ''.$data.'</div>';
                            

                            $contador++;
                        }
                        /**
                         * ##### REESCRITA
                         */


                        unset($contador);
                        $tabParcelas .= '</div>';
                        $info_pagamento .= $tabParcelas;
                        unset($tabParcelas);
                    break;
                    case 'Cancelada':
                        $situacao = 'Cancelado';
                    break;
                    case 'Devolvida':
                        $situacao = 'Estornado';
                    break;
                }


                // Items comprados
                $items_arr = json_decode($ret->items);
                $itemsPage = '';

                foreach($items_arr as $key => $i) {
                    $indice = $key + 1;
                    $valorUNI = $venda->converteCentavoParaReal($i->valorUNI);
                    $desconto = $venda->converteCentavoParaReal($i->desconto);
                    $subtotal = $venda->converteCentavoParaReal($i->subtotal);
                    $itemsPage .= "<tr> <td>$indice</td> <td>$i->tarifa</td>  <td>R$ $valorUNI</td> <td><span>$i->qtd</span> </td> <td>R$ <span>$desconto</span> </td> <td>R$ $subtotal</td> </tr>";
                }
                unset($items_arr, $indice, $valorUNI, $desconto, $subtotal);
                

                // Lista de passageiros (clientes)
                if($ret->lista_clientes !== '') {
                    $lista_cl_arr = json_decode($ret->lista_clientes);
                    if($lista_cl_arr === null) {
                        $lista_cl_arr = array();
                    }
                    $lista_cl = '';
                    $tipo_cl = array('ADULTO' => (int)$ret->adultos, 'CRIANCA' => (int)$ret->criancas, 'IDOSO' => (int)$ret->clientes_total - ((int)$ret->adultos + (int)$ret->criancas) );

                    // Varre clientes adicionados
                    foreach($lista_cl_arr as $l) {
                        $cliente = new Cliente((int)$l->id);
                        $c = $cliente->getDados();

                        $nascimento = new DateTime($c->nascimento);
                        $idade = $hoje->diff($nascimento);
                        $idade = $idade->y.($idade->m > 0 ? ','.$idade->m : '');
                        if($c->cpf != '') {
                            $cpf = substr($c->cpf,0, 3).'.***.***-**';
                        } else {
                            $cpf = '-';
                        }
                        
                        if($l->colo == true) {
                            //$colo = '<span class="text-success"><i class="fas fa-check"></i></span>';
                            $colo = 'checked';
                        } else {
                            $colo = '';
                        }
                        $lista_cl .= '
                        <tr>
                            <td class="small">'.$c->id.'</td>
                            <td><a href="javascript:void(0)" onclick="loadCliente('.$c->id.')">'.$c->nome.'</a></td>
                            <td class="small">'.$idade.' ANO(S)</td>
                            <td class="small">'.$c->faixa_etaria.'</td>
                            <td class="small">'.$cpf.'</td>
                            <td class="text-center">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="colo'.$c->id.'" '.$colo.' onchange="vendaSetPassageiroColo('.$c->id.', this)" data-venda="'.$ret->id.'">
                                    <label class="custom-control-label cursor-pointer" for="colo'.$c->id.'"></label>
                                </div>
                            </td>
                            <td><button type="button" class="btn btn-danger btn-sm" data-venda="'.$ret->id.'" onclick="vendaRemovePassageiroLista('.$c->id.', this)"><i class="fas fa-trash"></i></button></td>
                        </tr>
                        ';

                        if($c->faixa_etaria == '0-5' || $c->faixa_etaria == '6-12') {
                            $tipo_cl['CRIANCA']--;
                        } else if($c->faixa_etaria == '60+') {
                            $tipo_cl['ADULTO']--;
                        } else {
                            $tipo_cl['IDOSO']--;
                        }

                        unset($colo);
                    }

                    unset($cliente, $c);
                    $c = array();
                    foreach($tipo_cl as $key => $l) {
                        if($l > 0) {
                            array_push($c, $key);
                        }
                    }

                    if(!empty($c)) {
                        $tipo_cl = implode(' ', $c);
                    
                        // Botões para adicionar cliente.
                        $btn_cl_add = '
                            <div class="btn-group my-2">
                                <input type="hidden" name="clienteAdd" value="" data-faixa-etaria="'.$tipo_cl.'" data-venda="'.$ret->id.'" onchange="getClienteDados($(this).val(), function(c){vendaAddPassageiroLista(c, $(event.target));})">
                                <button type="button" class="btn-secondary btn btn-sm px-2" data-toggle="tooltip" title="Localizar cliente" onclick="janClienteSelect($(this).prev())"><i class="fas fa-search fa-fw"></i> Buscar e adicionar</button>
                                <a href="#clientes/novo" target="_blank" class="btn-primary btn btn-sm px-2" data-toggle="tooltip" title="Criar novo cliente"><i class="fas fa-plus fa-fw"></i> Criar cliente</a>
                            </div>
                        ';
                    } else {
                        $btn_cl_add = '';
                    }

                    

                } else {
                    // Lista vazia
                    $lista_cl = '';
                    $x = 0;
                    
                    $lista_cl .= '
                    <tr><td colspan="7"></td></tr>
                    ';
                    
                    $tipo_cl = array();

                    // Tipos de clientes necessários.
                    if((int)$ret->adultos > 0) {
                        array_push($tipo_cl, 'ADULTO');
                    }
                    
                    if((int)$ret->criancas > 0) {
                        array_push($tipo_cl, 'CRIANCA');
                    }

                    $tipo_cl = implode(' ', $tipo_cl);

                    // Botões para adicionar cliente.
                    $btn_cl_add = '
                        <div class="btn-group my-2">
                            <input type="hidden" name="clienteAdd" value="" data-faixa-etaria="'.$tipo_cl.'" data-venda="'.$ret->id.'" onchange="getClienteDados($(this).val(), function(c){vendaAddPassageiroLista(c, $(event.target));})">
                            <button type="button" class="btn-secondary btn btn-sm px-2" data-toggle="tooltip" title="Localizar cliente" onclick="janClienteSelect($(this).prev())"><i class="fas fa-search fa-fw"></i> Buscar e adicionar</button>
                            <a href="#clientes/novo" target="_blank" class="btn-primary btn btn-sm px-2" data-toggle="tooltip" title="Criar novo cliente"><i class="fas fa-plus fa-fw"></i> Criar cliente</a>
                        </div>
                    ';
                }
                
                unset($tipo_cl);

                /**
                 * LINK COMPROVANTE
                 * HASH = [bin2hex(hash(sha256, DOBBIN_FRASE_FIXA . DATA_RESERVA(Y-m-d H:i:s), true))]
                 * 
                 * [ID DA VENDA] - [HASH (0, 29)] - [ID DO CLIENTE] - [HASH (30, )]
                 * 
                 */
                
                $hashCompleto = bin2hex( hash('sha256', DOBBIN_FRASE_FIXA . $data_reserva->format('Y-m-d H:i:s'), true) );
                $linkComp = str_pad(bin2hex($ret->id), 8, '0', STR_PAD_LEFT) . '-' . substr($hashCompleto, 0, 30). '-' . str_pad(bin2hex($ret->cliente_id), 8, '0', STR_PAD_LEFT) . '-' .substr($hashCompleto, 30);

                /**
                 * TERMOS/CONTRATO
                 */
                
                $termosLink = 'https://'.DOBBIN_LINK_EXTERNO.'/externo/contrato/'.$linkComp;
                if($ret->termos_ac == NULL) {
                    $termosAc = '<span class="badge badge-secondary py-1 px-2">PENDENTE</span>';
                    $termosData = '';
                } else if((bool)$ret->termos_ac == true) {
                    $termosAc = '<span class="badge badge-success py-1 px-2">CONCORDA</span>';
                    $x = new \DateTime($ret->termos_data);
                    $termosData = '<span class="badge badge-light py-1 px-2 ml-2">'.$x->format('d/m/Y H:i:s').'</span>';
                    unset($x);
                } else {
                    $termosAc = '<span class="badge badge-warning py-1 px-2">DISCORDA</span>';
                    $x = new \DateTime($ret->termos_data);
                    $termosData = '<span class="badge badge-light py-1 px-2 ml-2">'.$x->format('d/m/Y H:i:s').'</span>';
                    unset($x);
                }

                /**
                 * MULTAS
                 */

                $multaSitu = '';
                if((int)$ret->multas == 0) {
                    // Sem multa
                    $multaSitu = "<span class=\"badge badge-light px-2 mr-2\">SEM MULTA</span>";
                } else if((int)$ret->multas > 0 && (int)$ret->multas_pago < (int)$ret->multas) {
                    // Multa pendente de pagamento.
                    $multaSitu = "<span class=\"badge badge-light px-2 font-weight-bold text-danger font-size-1rem\" data-toggle=\"tooltip\" title=\"Multa total (multa paga)\">R$ ".$venda->converteCentavoParaReal($ret->multas).
                    " (R$ ".$venda->converteCentavoParaReal($ret->multas_pago).")</span> ".
                    "<span class=\"badge badge-warning px-2 mr-2\">PENDENTE</span>";
                } else if((int)$ret->multas > 0 && (int)$ret->multas_pago == (int)$ret->multas) {
                    // Multa quitada.
                    $multaSitu = "<span class=\"badge badge-light px-2 font-weight-bold text-success font-size-1rem\" data-toggle=\"tooltip\" title=\"Multa total (multa paga)\">R$ ".$venda->converteCentavoParaReal($ret->multas).
                    " (R$ ".$venda->converteCentavoParaReal($ret->multas_pago).")</span> ".
                    "<span class=\"badge badge-success px-2 mr-2\">QUITADO</span>";
                }

                // Constrói página com HEREDOC
                $page = <<<PAGINA
            <div class="row">
                <div class="col-12">
                <!-- ###-->
                    <div class="row">
                        <div class="col-12">
                        <!-- ###-->
                            <div class="row">
                                <div class="col-12 col-lg-7 col-xl-8">
                                    <h4 class="font-weight-bold">Informações da venda</h4>
                                        <strong>Destino:</strong> <a href="#roteiros/ver/{$ret->roteiro_id}" target="_blank">{$ret->roteiro_nome} ({$data_ini->format('d/m/Y')} a {$data_fim->format('d/m/Y')})</a><br>
                                        <strong>Cliente:</strong> <a href="javascript:void(0)" onclick="loadCliente({$ret->cliente_id})">{$ret->cliente_nome}</a> <small>[Cód: {$ret->cliente_id}]</small><br>
                                        <strong>Vendedor:</strong> {$ret->usuario_nome}<br>
                                        <strong>OBSERVAÇÃO:</strong> <span class="badge badge-pill badge-info mx-2" data-toggle="popover" title="Quer editar?" data-trigger="hover" data-content="Dê um duplo clique no texto para editá-lo."><i class="fas fa-question-circle"></i></span><br>
                                        <div class="font-italic border border-top-0 border-bottom-0 border-right-0 border-info ml-1 py-2 pl-2 cursor-pointer"
                                            dobbin-campo-edita dobbin-campo-tipo="textarea" dobbin-campo-nome="obs" dobbin-url-form="vendas/{$ret->id}/obs/editar">{$ret->obs}</div>

                                </div>
                                <div class="col-12 col-lg-5 col-xl-4">
                                    <div class="border border-dark p-2 mt-3 mt-lg-0">
                                        <h6 class="font-weight-bold text-uppercase">Resumo</h6>

                                        <table class="table table-borderless table-sm small mb-0">
                                            <tbody>
                                                <tr>
                                                    <th>Subtotal</th>
                                                    <td><strong>R$ {$valorTotal}</strong> (R$ $valorPago) 
                                                        <i class="fas fa-question-circle cursor-help" data-toggle="tooltip" title="Valor total ( valor pago )"></i>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Multas</th>
                                                    <td><strong>R$ {$multasTotal}</strong> (R$ $multaPago)
                                                        <i class="fas fa-question-circle cursor-help" data-toggle="tooltip" title="Valor total ( valor pago )"></i>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Total</th>
                                                    <td><strong>R$ {$totalVenda}</strong> (R$ $totalVendaPago)
                                                        <i class="fas fa-question-circle cursor-help" data-toggle="tooltip" title="Valor total ( valor pago )"></i>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            
                            <br>
                            
                            <div class="row">
                                <div class="col-12 col-lg-6 mb-1">
                                    <div class="border border-dark p-2">
                                        <strong>Multas:</strong> {$multaSitu} <br>
                                        <div class="btn-group mt-2">
                                            <a href="javascript:void(0)" class="btn btn-info btn-sm px-2" onclick="vendaGetMultas({$ret->id})" data-toggle="tooltip" title="Definir ou alterar valor da multa.">
                                                <i class="fas fa-pen"></i> Alterar
                                            </a>
                                        </div>
                                        <i class="fas fa-question-circle cursor-help" data-toggle="popover" title="O que é?" data-trigger="hover"
                                        data-content="A <b>multa</b> é definida por você para cada situação. O sistema irá recolher o valor da multa automaticamente nas últimas parcelas do pagamento.
                                        <br><br>OBS.: O valor que JÁ FOI PAGO de multa não pode ser alterado!"></i>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-12 col-lg-6 mb-1">
                                    <div class="border border-dark p-2">
                                        <strong>Contrato:</strong> {$termosAc} {$termosData}<br>
                                        <div class="btn-group mt-2">
                                            <a href="{$termosLink}" class="btn btn-info btn-sm px-2" target="_blank">Página externa do contrato</a>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-info dropdown-toggle" data-toggle="dropdown"></button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item small" href="{$termosLink}/download">Download</a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item small" href="javascript:void(0)" onclick="vendaMudaAceiteContrato({$ret->id}, 1)">Marcar como CONCORDA</a>
                                                    <a class="dropdown-item small" href="javascript:void(0)" onclick="vendaMudaAceiteContrato({$ret->id}, 0)">Marcar como DISCORDA</a>
                                                    <a class="dropdown-item small" href="javascript:void(0)" onclick="vendaMudaAceiteContrato({$ret->id}, 2)">Redefinir</a>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    
                                </div>
                                <div class="col-12 col-lg-6 mb-1">
                                    <div class="border border-dark p-2">
                                        <strong>Situação:</strong> <span class="text-uppercase mr-2">{$ret->status_html}</span><br>

                                        <div class="btn-group mt-2">
                                            <a href="javascript:void(0)" class="btn btn-info btn-sm px-2" onclick="vendaGetSituacao({$ret->id})" data-toggle="tooltip" title="Alterar situação">
                                                <i class="fas fa-pen"></i> Alterar
                                            </a>
                                            <a href="/pdf/venda/{$ret->id}/comprovante" class="btn btn-info btn-sm px-2" target="_blank" data-toggle="tooltip" title="Comprovante da situação atual">Comprovante</a>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-info dropdown-toggle" data-toggle="dropdown"></button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="/pdf/venda/{$ret->id}/comprovante/download">Download</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                            
                            
                            {$info_pagamento}
                        </div>
                    </div>
                    <hr>

                    <div class="card shadow-none rounded-0">
                        <div class="card-header p-2 card-collapse">
                            <h6 class="font-weight-bold text-uppercase mb-0 text-dark">Datas das Operações</h6>
                        </div>
                        <div class="card-body p-2 collapse">
                            <table class="table table-sm table-bordered">
                                <thead class="bg-info text-white">
                                    <tr>
                                        <th>Data da Reserva</th>
                                        <th>Data da Venda</th>
                                        <th>Data do Pagamento</th>
                                        <th>Data do Cancelamento</th>
                                        <th>Data do Estorno</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{$data_reserva->format('d/m/Y H:i:s')}</td>
                                        <td>{$data_venda}</td>
                                        <td>{$data_pagamento}</td>
                                        <td>{$data_cancelado}</td>
                                        <td>{$data_estorno}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card shadow-none rounded-0">
                        <div class="card-header p-2 card-collapse">
                            <h6 class="font-weight-bold text-uppercase mb-0 text-dark">Itens Adquiridos</h6>
                        </div>
                        <div class="card-body p-2 collapse">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Tarifa</th>
                                        <th>Valor UNI</th>
                                        <th>Qtd</th>
                                        <th>Desconto</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    $itemsPage
                                    <tr>
                                        <td colspan="4"><strong>TOTAL:</strong></td>
                                        <td class="table-dark">R$ <span>$descontoTotal</span></td>
                                        <td class="table-dark">R$ <span>$valorTotal</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card shadow-none rounded-0">
                        <div class="card-header p-2 card-collapse">
                            <h6 class="font-weight-bold text-uppercase mb-0 text-dark">Lista de passageiros</h6>
                        </div>
                        <div class="card-body p-2 collapse" style="display:block;">
                            <table class="table table-sm table-borderless">
                                <tbody>
                                    <tr>
                                        <th colspan="3">Qtd. Clientes</th>
                                    </tr>
                                    <tr>
                                        <td>Criança(s):
                                            <span class="badge badge-pill badge-secondary cursor-help" data-toggle="tooltip" title="Quantidade máxima esperada de clientes.">$ret->criancas</span></td>
                                        <td>Adulto(s):
                                            <span class="badge badge-pill badge-secondary cursor-help" data-toggle="tooltip" title="Quantidade máxima esperada de clientes.">$ret->adultos</span></td>
                                        <td>Criança(s) de colo:
                                            <span class="badge badge-pill badge-secondary cursor-help" data-toggle="tooltip" title="Quantidade máxima esperada de clientes.">$ret->criancas_colo</span></td>
                                    </tr>
                                </tbody>
                            </table>
                            <hr>

                            $btn_cl_add
                            <table class="table table-bordered table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Cód.</th>
                                        <th>Nome</th>
                                        <th>Idade</th>
                                        <th>Faixa Etária</th>
                                        <th>CPF</th>
                                        <th><abbr data-toggle="tooltip" title="Criança de colo">Cri. Colo</abbr></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    $lista_cl
                                </tbody>
                            </table>
                        </div>
                    </div>


                </div>
            </div>
PAGINA;

                $retorno['page'] = $page;
            }
        }

        return json_encode($retorno);
        
    }

    static function vendasMudarSituacao($p)
    {
        self::validaConexao(3);

        $retorno = array(
            'success' => false,
            'mensagem' => '',
            'page' => '',
        );

        if($p['vid'] == 0) {
            $retorno['mensagem'] = Erro::getMessage(9);
        } else {
            $venda = new Venda($p['vid']);
            $ret = $venda->getDados();

            if($ret === false) {
                $retorno['mensagem'] = Erro::getMessage(302);
            } else {
                $retorno['success'] = true;
                $retorno['venda'] = $ret;
                $hoje = new DateTime();
                $sgc = new SGCTUR();
                $cl = new Cliente($ret->cliente_id);
                $cliente = $cl->getDados();

                // Se o cliente possuir crédito, exibe opção de pagar com crédito.
                if((int)$cliente->credito > 0) {
                    $btn_situ_credito = '<li class="nav-item d-flex mr-md-2 mb-2 mb-md-0"> <a class="nav-link small align-self-center btn-outline-primary" data-toggle="pill" href="#tabPagCredito">Pagar c/ Crédito '.
                    '<i class="fas fa-exclamation text-danger"></i></a> </li>';
                } else {
                    $btn_situ_credito = '';
                }

                switch($ret->status) {
                    case 'Reserva': // Permitido: Aguardando Pagamento, Pagamento, Cancelar
                        $situacao = 'Reserva';
                        $btn_situ = '
                        <li class="nav-item d-flex mr-md-2 mb-2 mb-md-0"> <a class="nav-link small align-self-center btn-outline-primary" data-toggle="pill" href="#tabAguardando">Aguardando Pagamento</a> </li>
                        <li class="nav-item d-flex mr-md-2 mb-2 mb-md-0"> <a class="nav-link small align-self-center btn-outline-primary" data-toggle="pill" href="#tabPagamento">Pagamento</a> </li>'.
                        $btn_situ_credito.
                        '<li class="nav-item d-flex mr-md-2 mb-2 mb-md-0"> <a class="nav-link small align-self-center btn-outline-primary" data-toggle="pill" href="#tabCancelado">Cancelado</a> </li>
                        <li class="nav-item d-flex mr-md-2 mb-2 mb-md-0"> <a class="nav-link disabled small align-self-center btn-outline-primary" data-toggle="pill" href="#tabEstornado">Estornado</a> </li>
                        ';
                    break;
                    case 'Aguardando': // Permitido: Pagamento, Cancelar
                        $situacao = 'Aguardando pagamento';
                        $btn_situ = '
                        <li class="nav-item d-flex mr-md-2 mb-2 mb-md-0"> <a class="nav-link disabled small align-self-center btn-outline-primary" data-toggle="pill" href="#tabAguardando">Aguardando Pagamento</a> </li>
                        <li class="nav-item d-flex mr-md-2 mb-2 mb-md-0"> <a class="nav-link small align-self-center btn-outline-primary" data-toggle="pill" href="#tabPagamento">Pagamento</a> </li>'.
                        $btn_situ_credito.
                        '<li class="nav-item d-flex mr-md-2 mb-2 mb-md-0"> <a class="nav-link small align-self-center btn-outline-primary" data-toggle="pill" href="#tabCancelado">Cancelado</a> </li>
                        <li class="nav-item d-flex mr-md-2 mb-2 mb-md-0"> <a class="nav-link disabled small align-self-center btn-outline-primary" data-toggle="pill" href="#tabEstornado">Estornado</a> </li>
                        ';
                    break;
                    case 'Pagando': // Permitido: Pagamento, Estornar
                        $situacao = 'Pagando';
                        $btn_situ = '
                        <li class="nav-item d-flex mr-md-2 mb-2 mb-md-0"> <a class="nav-link disabled small align-self-center btn-outline-primary" data-toggle="pill" href="#tabAguardando">Aguardando Pagamento</a> </li>
                        <li class="nav-item d-flex mr-md-2 mb-2 mb-md-0"> <a class="nav-link small align-self-center btn-outline-primary" data-toggle="pill" href="#tabPagamento">Pagamento</a> </li>'.
                        $btn_situ_credito.
                        '<li class="nav-item d-flex mr-md-2 mb-2 mb-md-0"> <a class="nav-link disabled small align-self-center btn-outline-primary" data-toggle="pill" href="#tabCancelado">Cancelado</a> </li>
                        <li class="nav-item d-flex mr-md-2 mb-2 mb-md-0"> <a class="nav-link small align-self-center btn-outline-primary" data-toggle="pill" href="#tabEstornado">Estornado</a> </li>
                        ';
                    break;
                    case 'Paga': // Permitido: Estornar
                        $situacao = 'Pago';
                        $btn_situ = '
                        <li class="nav-item d-flex mr-md-2 mb-2 mb-md-0"> <a class="nav-link disabled small align-self-center btn-outline-primary" data-toggle="pill" href="#tabAguardando">Aguardando Pagamento</a> </li>
                        <li class="nav-item d-flex mr-md-2 mb-2 mb-md-0"> <a class="nav-link disabled small align-self-center btn-outline-primary" data-toggle="pill" href="#tabPagamento">Pagamento</a> </li>
                        <li class="nav-item d-flex mr-md-2 mb-2 mb-md-0"> <a class="nav-link disabled small align-self-center btn-outline-primary" data-toggle="pill" href="#tabCancelado">Cancelado</a> </li>
                        <li class="nav-item d-flex mr-md-2 mb-2 mb-md-0"> <a class="nav-link small align-self-center btn-outline-primary" data-toggle="pill" href="#tabEstornado">Estornado</a> </li>
                        ';
                        $btn_situ_credito = '';
                    break;
                    case 'Cancelada':
                        $situacao = 'Cancelado';
                        $btn_situ = '
                        <li class="nav-item d-flex mr-md-2 mb-2 mb-md-0"> <a class="nav-link disabled small align-self-center btn-outline-primary" data-toggle="pill" href="#tabAguardando">Aguardando Pagamento</a> </li>
                        <li class="nav-item d-flex mr-md-2 mb-2 mb-md-0"> <a class="nav-link disabled small align-self-center btn-outline-primary" data-toggle="pill" href="#tabPagamento">Pagamento</a> </li>
                        <li class="nav-item d-flex mr-md-2 mb-2 mb-md-0"> <a class="nav-link disabled small align-self-center btn-outline-primary" data-toggle="pill" href="#tabCancelado">Cancelado</a> </li>
                        <li class="nav-item d-flex mr-md-2 mb-2 mb-md-0"> <a class="nav-link disabled small align-self-center btn-outline-primary" data-toggle="pill" href="#tabEstornado">Estornado</a> </li>
                        ';
                        $btn_situ_credito = '';
                    break;
                    case 'Devolvida':
                        $situacao = 'Estornado';
                        $btn_situ = '
                        <li class="nav-item d-flex mr-md-2 mb-2 mb-md-0"> <a class="nav-link disabled small align-self-center btn-outline-primary" data-toggle="pill" href="#tabAguardando">Aguardando Pagamento</a> </li>
                        <li class="nav-item d-flex mr-md-2 mb-2 mb-md-0"> <a class="nav-link disabled small align-self-center btn-outline-primary" data-toggle="pill" href="#tabPagamento">Pagamento</a> </li>
                        <li class="nav-item d-flex mr-md-2 mb-2 mb-md-0"> <a class="nav-link disabled small align-self-center btn-outline-primary" data-toggle="pill" href="#tabCancelado">Cancelado</a> </li>
                        <li class="nav-item d-flex mr-md-2 mb-2 mb-md-0"> <a class="nav-link disabled small align-self-center btn-outline-primary" data-toggle="pill" href="#tabEstornado">Estornado</a> </li>
                        ';
                        $btn_situ_credito = '';
                    break;
                }

                // SELECT: QUANTIDADE DE PARCELAS
                $qtdParcelas = '';
                for($i = 2; $i <= \DOBBIN_MAX_PARCELAS; $i++) {
                    $qtdParcelas .= '<option value="'.$i.'">'.$i.'x</option>';
                }

                // SELECT: DIAS VENCIMENTO
                $diasVenc = '';
                for($i = 1; $i <= \DOBBIN_MAX_VENCIMENTO_DIAS; $i++) {
                    if((int)$hoje->format('j') == $i) {
                        $diasVenc .= '<option value="'.$i.'" selected>'.$i.'</option>';
                    } else {
                        $diasVenc .= '<option value="'.$i.'">'.$i.'</option>';
                    }
                    
                }

                // VALOR TOTAL: VALOR DA COMPRA + MULTA
                $valorTotal = $ret->valor_total + $ret->multas;

                // TOTAL PAGO: TOTAL DA COMPRA + MULTA PAGA
                $totalPago = $ret->total_pago + $ret->multas_pago;

                // TAB: AGUARDANDO PAGAMENTO
                $tabAgu = '';
                if($ret->status == 'Pagando' || $ret->status == 'Paga' || $ret->status == 'Cancelada' || $ret->status == 'Devolvida') {
                    $tabAgu .= '<div class="alert alert-info">Esta opção não está disponível para esta venda.</div>';
                } else {
                    $tabAgu = <<<TAB
                        <div class="mb-2">
                            <strong>Valor total: </strong> R$ <span class="font-weight-bold mr-3">{$sgc->converteCentavoParaReal($valorTotal)}</span> 
                            <i class="fas fa-question-circle cursor-help" data-toggle="popover" data-trigger="hover" title="Como calculado?"
                            data-content="R$ <b>{$sgc->converteCentavoParaReal($ret->valor_total)}</b> (venda) + <br>R$ <b>{$sgc->converteCentavoParaReal($ret->multas)}</b> (multas) 
                            <hr class='my-1'>R$ <b>{$sgc->converteCentavoParaReal($valorTotal)}</b> (total)"></i>
                        </div>
                        <div class="d-flex flex-column flex-md-row">

                            <div class="form-group mr-md-3">
                                <label class="font-weight-bold">Forma de pagamento</label>
                                <select class="form-control form-control-sm" name="pagamento" onchange="">
                                    <option value="Crédito">Cartão de crédito</option>
                                    <option value="Débito">Cartão de débito</option>
                                    <option value="Boleto">Boleto Bancário</option>
                                    <option value="Digital">Pagamento Digital</option>
                                    <option value="Transferência">Transferência Bancária</option>
                                    <option value="Dinheiro">Dinheiro</option>
                                    <option value="Outro">Outro</option>
                                </select>
                            </div>
                            <div class="form-group mr-md-3">
                                <label class="font-weight-bold">Parcelas</label>
                                <select class="form-control form-control-sm" name="parcelas" data-valortotal="{$sgc->converteCentavoParaReal($valorTotal)}" onchange="(function(){
                                    let alvo = $(event.target)
                                    let total = Dobbin.converteRealEmCentavo(alvo.attr('data-valortotal'));

                                    alvo.parents('.tab-pane').find('[name=\'valor_parcela\']').val( Math.ceil( total / alvo.find(':selected').val() ));
                                    alvo.parents('.tab-pane').find('[name=\'valor_parcela\']').trigger('change');
                                })()">
                                    <option value="1">1x - À vista</option>
                                    {$qtdParcelas}
                                </select>
                            </div>
                            <div class="form-group mr-md-3">
                                <label class="font-weight-bold">Vencimento</label>
                                <select class="form-control form-control-sm" name="vencimento">
                                    {$diasVenc}
                                </select>
                            </div>
                            <div class="form-group mr-md-3">
                                <label class="font-weight-bold">Valor da Parcela</label>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text ">R$</div>
                                    </div>
                                    <input type="text" class=" form-control form-control-sm" name="valor_parcela" value="{$sgc->converteCentavoParaReal($valorTotal)}" dobbin-mask-money disabled>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-info small px-2 py-1">
                            <i class="fas fa-info-circle mr-2"></i> O valor da parcela é SOMENTE uma prévia e só é válido para compras parceladas (2x ou mais). Caso o cliente pague a menos ou a mais,
                            você poderá informar o valor pago no momento do pagamento.
                            
                        </div>
                        <div class="alert alert-primary small px-2 py-1">
                            <i class="fas fa-info-circle mr-2"></i><strong>Pagamento parcelado no cartão de crédito.</strong><br> As operadoras de cartão de crédito ou bancos emissores, repassam o valor da compra para o estabelecimento,
                            independente da quantidade de parcelas que o cliente vai pagar. <strong>Para esse caso:</strong> 1) Em "Aguardando Pagamento", defina o pagamento com Cartão de Crédito e a quantidade de parcelas desejadas e salve;
                            <br>2) Depois do passo 1, em "Pagamento", defina o pagamento da primeira parcela como o valor integral da compra. A quantidade de parcelas vai ficar salva, e o total da compra será creditado na plataforma.
                        </div>
                        <hr>
                        <button type="button" class="btn btn-sm btn-success" data-venda="{$ret->id}" onclick="(function(){
                            let alvo = $(event.target);
                            let tabpane = alvo.parents('.tab-pane');
                            let outro = {};
                            outro.forma_pagamento = tabpane.find('[name=\'pagamento\']').val();
                            outro.parcelas = tabpane.find('[name=\'parcelas\']').val();
                            outro.vencimento = tabpane.find('[name=\'vencimento\']').val();
                            vendaAlteraSituacao(alvo.data('venda'), 'Aguardando', outro, alvo);
                        })()">Salvar situação</button>
TAB;
                }

                // TAB: PAGAMENTO
                $tabPag = '';
                if($ret->status == 'Reserva') { // Pula o status de Aguardando.
                    $tabPag = <<<TAB

                        <div class="mb-2">
                            <strong>Valor total: </strong> R$ <span class="font-weight-bold">{$sgc->converteCentavoParaReal($valorTotal)}</span> 
                            <i class="fas fa-question-circle cursor-help" data-toggle="popover" data-trigger="hover" title="Como calculado?"
                            data-content="R$ <b>{$sgc->converteCentavoParaReal($ret->valor_total)}</b> (venda) + <br>R$ <b>{$sgc->converteCentavoParaReal($ret->multas)}</b> (multas) 
                            <hr class='my-1'>R$ <b>{$sgc->converteCentavoParaReal($valorTotal)}</b> (total)"></i> 
                            <span class="ml-2 text-muted">(À vista)</span>
                        </div>
                        <div class="alert alert-info small px-2 py-1">
                            <i class="fas fa-info-circle"></i> <strong>Somente pagamentos à vista</strong>
                            <br>Para definir um pagamento parcelado, vá em <a href="javascript:void(0)" onclick="$(this).parents('.modal').find('ul [href=\'#tabAguardando\']').click()">Aguardando Pagamento</a>
                            e altere a Reserva para <strong>Aguardando Pagamento</strong>.
                        </div>
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">

                            <div class="form-group mr-md-3">
                                <label class="font-weight-bold">Forma de pagamento</label>
                                <select class="form-control form-control-sm" name="pagamento" onchange="">
                                    <option value="Crédito">Cartão de crédito</option>
                                    <option value="Débito">Cartão de débito</option>
                                    <option value="Boleto">Boleto Bancário</option>
                                    <option value="Digital">Pagamento Digital</option>
                                    <option value="Transferência">Transferência Bancária</option>
                                    <option value="Dinheiro">Dinheiro</option>
                                    <option value="Outro">Outro</option>
                                </select>
                            </div>
                            <button type="button" class="btn btn-sm btn-success" data-venda="{$ret->id}" onclick="(function(){
                                let alvo = $(event.target);
                                let tabpane = alvo.parents('.tab-pane');
                                let outro = {};
                                outro.forma_pagamento = tabpane.find('[name=\'pagamento\']').val();
                                vendaAlteraSituacao(alvo.data('venda'), 'Paga', outro, alvo);
                            })()">Salvar situação</button>

                        </div>
TAB;
                } else if($ret->status == 'Aguardando' || $ret->status == 'Pagando') { // Migra de Aguardando Pagamento para Pagamento.
                    
                    $tabParcelas = '';

                    if($ret->detalhes_pagamento == '' || $ret->detalhes_pagamento == '[]') { // Primeira parcela.
                        $tabParcelas = '<div class="d-flex border border-info p-2 flex-wrap flex-column flex-md-row">';
                        for($i = 0; $i < (int)$ret->parcelas; $i++) {
                            $tabParcelas .= '<div class="border text-center shadow-sm p-2 mx-1 mb-2"> <strong>'.($i+1).'&ordf; parcela</strong><br> - <br></div>';
                        }
                        
                        $tabParcelas .= '</div><hr>';
                    } else {
                        $detalhes = json_decode($ret->detalhes_pagamento);
                        
                        $tabParcelas = '<div class="d-flex border border-info p-2 flex-wrap flex-column flex-md-row">';
                        for($i = 0; $i < (int)$ret->parcelas; $i++) {
                            if(isset($detalhes[$i])) {
                                $valorParcela = '<div class="my-2 font-weight-bold bg-info text-white rounded">R$ '.$sgc->converteCentavoParaReal($detalhes[$i]->valor).'</div>';

                                $data = new \DateTime($detalhes[$i]->data);
                                $data = '<small>'.$data->format('d/m/Y').'</small>';
                            } else {
                                $valorParcela = '<div class="my-2 font-weight-bold text-info small rounded">--</div>';
                                $data = '<small>--/--/----</small>';
                            }
                            $tabParcelas .= '<div class="border text-center shadow-sm p-2 mx-1 mb-2"> <strong>'.($i+1).'&ordf; parcela</strong><br>'.$valorParcela.
                            ''.$data.'</div>';

                            unset($valorParcela, $data);
                        }
                        
                        $tabParcelas .= '</div><hr>';
                    }
                    $valorParcSugerido = ceil( ( (int)$valorTotal - (int)$totalPago ) / ( (int)$ret->parcelas - (int)$ret->parcelas_pagas ) );

                    $tabPag = <<<TAB

                        {$tabParcelas}
                        <div class="mb-2 d-flex flex-column flex-md-row justify-content-md-between">
                            <div>
                                <strong>Valor total: </strong> R$ <span>{$sgc->converteCentavoParaReal($valorTotal)}</span>
                                <i class="fas fa-question-circle cursor-help" data-toggle="popover" data-trigger="hover" title="Como calculado?"
                                data-content="R$ <b>{$sgc->converteCentavoParaReal($ret->valor_total)}</b> (venda) + <br>R$ <b>{$sgc->converteCentavoParaReal($ret->multas)}</b> (multas) 
                                <hr class='my-1'>R$ <b>{$sgc->converteCentavoParaReal($valorTotal)}</b> (total)"></i>
                            </div>
                            <div>
                                <strong>Valor total pago: </strong> R$ <span>{$sgc->converteCentavoParaReal($totalPago)}</span>
                                <i class="fas fa-question-circle cursor-help" data-toggle="popover" data-trigger="hover" title="Como calculado?"
                                data-content="R$ <b>{$sgc->converteCentavoParaReal($ret->total_pago)}</b> (venda) + <br>R$ <b>{$sgc->converteCentavoParaReal($ret->multas_pago)}</b> (multas) 
                                <hr class='my-1'>R$ <b>{$sgc->converteCentavoParaReal($totalPago)}</b> (total)"></i>
                            </div>
                            
                        </div><hr>
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">

                            <div class="form-group mr-md-3">
                                <label class="font-weight-bold">Valor da parcela</label>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text ">R$</div>
                                    </div>
                                    <input type="text" class=" form-control form-control-sm" name="valor" value="0,00" dobbin-mask-money>
                                </div>
                                <span class="text-muted small">[<strong>Sugerido:</strong> R$ {$sgc->converteCentavoParaReal($valorParcSugerido)} ]</span>
                            </div>
                            <button type="button" class="btn btn-sm btn-success" data-venda="{$ret->id}" onclick="(function(){
                                let alvo = $(event.target);
                                let tabpane = alvo.parents('.tab-pane');
                                let outro = {};
                                outro.valor_parcela = Dobbin.converteRealEmCentavo(tabpane.find('[name=\'valor\']').val());
                                vendaAlteraSituacao(alvo.data('venda'), 'Pagando', outro, alvo);
                            })()">Confirmar pagamento da parcela</button>

                        </div>
TAB;
                }

                // TAB: PAGAR CRÉDITO
                $tabPagCredito = '';
                if($btn_situ_credito != '') {
                    
                    $tabPagCredito = <<<TAB

                        <div class="row">
                            <div class="col-12 d-flex justify-content-between">
                                <div class="px-2 mb-2">
                                    <strong>Valor total: </strong> R$ <span>{$sgc->converteCentavoParaReal($valorTotal)}</span>
                                </div>
                                <div class="px-2 mb-2">
                                    <strong>Crédito: </strong> R$ <span>{$sgc->converteCentavoParaReal($cliente->credito)}</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="alert alert-info small px-2">
                                    1) Se o valor em crédito for inferior ao total da compra, o valor restante pode ser pago no método escolhido pelo cliente;<br>
                                    2) Se o valor em crédito for maior ou igual ao total da compra, o valor será coberto e a venda recebe a situação de PAGA automaticamente;<br>

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 text-right">
                                <button type="button" class="btn btn-sm btn-success" data-venda="{$ret->id}" onclick="(function(){
                                    let alvo = $(event.target);
                                    let tabpane = alvo.parents('.tab-pane');
                                    let outro = {};
                                    vendaAlteraSituacao(alvo.data('venda'), 'PagarCredito', outro, alvo);
                                })()">Pagar venda com crédito</button>
                            </div>
                        </div>
TAB;
                }

                // TAB: CANCELADO
                $tabCancel = '';
                if($ret->status == 'Pagando' || $ret->status == 'Paga' || $ret->status == 'Devolvida') {
                    $tabCancel .= '<div class="alert alert-info">Esta opção não está disponível para esta venda.</div>';
                } else {
                    $tabCancel = <<<TAB
                        <div class="alert alert-info small px-2 py-1">
                            <i class="fas fa-info-circle mr-1"></i> Só é possível <b>Cancelar</b> vendas que ainda não foram pagas ou reservas.<br>
                            A poltrona/ingresso reservado ficará livre para venda novamente e a operação NÃO poderá ser desfeita.
                        </div>
                        <div class="alert alert-success small px-2 py-1">
                            Esta venda pode ser <b>CANCELADA</b>.
                        </div>
                        <button type="button" class="btn btn-sm btn-warning" data-venda="{$ret->id}" onclick="(function(){
                            let alvo = $(event.target);
                            let tabpane = alvo.parents('.tab-pane');
                            let outro = {};
                            vendaAlteraSituacao(alvo.data('venda'), 'Cancelada', outro, alvo);
                        })()">Cancelar venda</button>

TAB;
                }

                // TAB: ESTORNADO
                $tabEst = '';
                if($ret->status == 'Pagando' || $ret->status == 'Paga') {
                    $tabEst = <<<TAB
                        <div class="d-flex flex-column flex-md-row justify-content-md-between">
                            <div class="mb-2 border shadow-sm p-2">
                                <strong>Valor total: </strong> R$ <span>{$sgc->converteCentavoParaReal($valorTotal)}</span>
                                <i class="fas fa-question-circle cursor-help" data-toggle="popover" data-trigger="hover" title="Como calculado?"
                                data-content="R$ <b>{$sgc->converteCentavoParaReal($ret->valor_total)}</b> (venda) + <br>R$ <b>{$sgc->converteCentavoParaReal($ret->multas)}</b> (multas) 
                                <hr class='my-1'>R$ <b>{$sgc->converteCentavoParaReal($valorTotal)}</b> (total)"></i>
                            </div>
                            <div class="mb-2 border shadow-sm p-2">
                                <strong>Valor pago: </strong> R$ <span>{$sgc->converteCentavoParaReal($totalPago)}</span>
                                <i class="fas fa-question-circle cursor-help" data-toggle="popover" data-trigger="hover" title="Como calculado?"
                                data-content="R$ <b>{$sgc->converteCentavoParaReal($ret->total_pago)}</b> (venda) + <br>R$ <b>{$sgc->converteCentavoParaReal($ret->multas_pago)}</b> (multas) 
                                <hr class='my-1'>R$ <b>{$sgc->converteCentavoParaReal($totalPago)}</b> (total)"></i>
                            </div>
                        </div>
                        <div class="alert alert-info small py-1 px-2">
                            <strong>Como estornar?</strong>
                            <ul>
                                <li>O valor a ser devolvido/estornado precisa ser igual ou menor que o <b>valor pago</b>.</li>
                                <li>Se há alguma cobrança (valor que não será devolvido), preencha SOMENTE o que vai ser devolvido.
                                (Ex.: Total = R$ 1000,00; Valor a ser devolvido = R$ 750,00.)</li>
                                <li>O valor do estorno deve ser igual ou maior que R$ 0,01.</li>
                                <li>A operação de devolução só pode ser realizada uma única vez! Mesmo que você devolva em parcelas, você deve lançar o VALOR TOTAL DEVOLVIDO/ESTORNADO.</li>
                            </ul>
                        </div>
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">

                            <div class="form-group mr-md-3">
                                <label class="font-weight-bold">Valor do estorno</label>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text ">R$</div>
                                    </div>
                                    <input type="text" class=" form-control form-control-sm" name="valor" value="0,00" max="{$ret->total_pago}" dobbin-mask-money>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-success" data-venda="{$ret->id}" onclick="(function(){
                                let alvo = $(event.target);
                                let tabpane = alvo.parents('.tab-pane');
                                let outro = {};
                                outro.valor_estorno = Dobbin.converteRealEmCentavo(tabpane.find('[name=\'valor\']').val());
                                
                                if(outro.valor_estorno == parseInt(tabpane.find('[name=\'valor\']').prop('max'))) {
                                    vendaAlteraSituacao(alvo.data('venda'), 'Devolvida', outro, alvo);
                                } else if(outro.valor_estorno == 0) {
                                    alerta('O valor mínimo é <b>R$ 0,01</b>! Não é possível devolver R$ 0,00.','', 'danger');
                                } else {
                                    vendaAlteraSituacao(alvo.data('venda'), 'Devolvida', outro, alvo);
                                }
                            })()">Estornar</button>

                        </div>
TAB;
                } else {
                    $tabEst .= '<div class="alert alert-info">Esta opção não está disponível para esta venda.</div>';
                }

                $pagina = <<<PAGINA
                    <div class="row">
                        <div class="col-12">
                            <ul class="nav nav-pills border rounded-sm border-primary flex-md-row flex-column flex-nowrap p-2 justify-content-md-between text-center">
                                $btn_situ
                            </ul>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="tab-content">
                                <div class="tab-pane container fade border rounded rounded-sm p-2 active show" id="">
                                    <div class="text-center py-2">
                                        Se nenhuma opção acima estiver disponível, é porque não há mais o que fazer.
                                    </div>
                                </div>
                                <div class="tab-pane container fade border rounded rounded-sm p-2" id="tabAguardando">
                                    <h5>Aguardando pagamento</h5>
                                    <hr>
                                    {$tabAgu}
                                    
                                </div>
                                <div class="tab-pane container fade border rounded rounded-sm p-2" id="tabPagamento">
                                    <h5>Pagamento</h5>
                                    <hr>
                                    {$tabPag}
                                    
                                </div>
                                <div class="tab-pane container fade border rounded rounded-sm p-2" id="tabCancelado">
                                    <h5>Cancelar venda</h5>
                                    <hr>
                                    {$tabCancel}
                                </div>
                                <div class="tab-pane container fade border rounded rounded-sm p-2" id="tabEstornado">
                                    <h5>Devolvido / Estornado</h5>
                                    <hr>
                                    {$tabEst}
                                </div>
                                <div class="tab-pane container fade border rounded rounded-sm p-2" id="tabPagCredito">
                                    <h5>Pagar com Crédito</h5>
                                    <hr>
                                    {$tabPagCredito}
                                </div>
                            </div>
                        </div>
                    </div>
PAGINA;
                $retorno['page'] = $pagina;
            }
        }

        return json_encode($retorno);
    }

    static function vendasMudarMultas($p)
    {
        self::validaConexao(3);

        $retorno = array(
            'success' => false,
            'mensagem' => '',
            'page' => '',
        );

        if($p['vid'] == 0) {
            $retorno['mensagem'] = Erro::getMessage(9);
        } else {
            $venda = new Venda($p['vid']);
            $ret = $venda->getDados();

            if($ret === false) {
                $retorno['mensagem'] = Erro::getMessage(302);
            } else {
                $retorno['success'] = true;
                $retorno['venda'] = $ret;
                $hoje = new DateTime();
                $sgc = new SGCTUR();


                // VALORES
                $valorTotal = $venda->converteCentavoParaReal($ret->valor_total);
                $valorTotalPago = $venda->converteCentavoParaReal($ret->total_pago);
                $valorMultas = $venda->converteCentavoParaReal($ret->multas);
                $valorMultasPago = $venda->converteCentavoParaReal($ret->multas_pago);


                /** 
                 * 
                 * PAGINA DE MULTAS
                 * 
                 */
                $pagina = <<<PAGINA
                
                    <div class="row">
                        <div class="col-12">
                            <div class="border rounded rounded-sm p-2">
                                
                                <div class="row">
                                    <div class="col-12 col-lg-6">
                                        <h5 class="font-weight-bold">Defina/altere o valor da multa</h5>
                                        <form action="vendas/{$ret->id}/setmulta" method="POST" data-aftersubmit="VendaMultaDefinir" data-venda="{$ret->id}">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Valor da multa</label>
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            R$
                                                        </div>
                                                    </div>
                                                    <input type="text" name="valor_real" class="form-control form-control-sm"
                                                    onchange="$(this).next().val(Dobbin.converteRealEmCentavo($(this).val()))" dobbin-mask-money value="{$valorMultas}">
                                                    <input type="hidden" name="valor" class="form-control form-control-sm" dobbin-mask-money value="{$valorMultas}">
                                                </div>
                                            </div>
                                            <div class="alert alert-info small py-1 px-2">
                                                <strong>Como funciona?</strong>
                                                O valor total da venda não é modificada. A plataforma irá somar o valor total da venda com o valor da multa
                                                (aqui definida), para mostrar o que o cliente deve.
                                            </div>
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-success btn-sm">Salvar</button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-12 col-lg-6">

                                            <div class="border border-dark p-2">
                                                <h6 class="text-uppercase font-weight-bold">RESUMO</h6>
                                                <table class="table table-sm table-borderless mb-0 small">
                                                    <tbody>
                                                        <tr>
                                                            <th>Valor da Venda</th>
                                                            <td>R$ {$valorTotal}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Valor Pago da Venda</th>
                                                            <td>R$ {$valorTotalPago}</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2">
                                                            <hr class="my-0">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>Valor da Multa</th>
                                                            <td>R$ {$valorMultas}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Valor Pago da Multa</th>
                                                            <td>R$ {$valorMultasPago}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
PAGINA;
                $retorno['page'] = $pagina;

            }
        }

        return json_encode($retorno);
    }







}