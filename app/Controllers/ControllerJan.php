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
                        $info_pagamento = "<div class=\"d-flex mt-2\"> <div class=\"border px-2 py-1 mr-2\"> <strong>Forma de Pagamento:</strong> $ret->forma_pagamento </div>".
                        "<div class=\"border px-2 py-1 mr-2\"><strong>Dia Vencimento: </strong> $ret->vencimento</div>".
                        "<div class=\"border px-2 py-1 mr-2\"><strong>Parcela(s): </strong> $ret->parcelas"."x</div></div>";
                        
                        $detalhes = json_decode($ret->detalhes_pagamento);
                        if($detalhes == NULL) { $detalhes = [];}

                        $tabParcelas = '<div class="small mt-2 d-flex border border-info p-2 flex-wrap flex-row">';
                        
                        for($i = 0; $i < (int)$ret->parcelas; $i++) {
                            if(isset($detalhes[$i])) {
                                $valorParcela = '<div class="my-2 font-weight-bold bg-info text-white rounded px-1">R$ '.$venda->converteCentavoParaReal($detalhes[$i]->valor).'</div>';

                                $data = new \DateTime($detalhes[$i]->data);
                                $data = '<small>'.$data->format('d/m/Y').'</small>';
                            } else {
                                $valorParcela = '<div class="my-2 font-weight-bold text-info small rounded px-1">--</div>';
                                $data = '<small>--/--/----</small>';
                            }

                            if((int)$ret->parcelas == 1) {
                                $tabParcelas .= '<div class="border text-center shadow-sm p-2 mx-1 mb-2"> <strong>À Vista</strong><br>'.$valorParcela.
                                ''.$data.'</div>';
                            } else {
                                $tabParcelas .= '<div class="border text-center shadow-sm p-2 mx-1 mb-2"> <strong>'.($i+1).'&ordf; parcela</strong><br>'.$valorParcela.
                                ''.$data.'</div>';
                            }

                            unset($valorParcela, $data);
                        }
                        
                        $tabParcelas .= '</div>';
                        $info_pagamento .= $tabParcelas;
                        unset($tabParcelas);

                    break;
                    case 'Pagando':
                        $situacao = 'Pagando';
                        $info_pagamento = "<div class=\"d-flex mt-2\"> <div class=\"border px-2 py-1 mr-2\"> <strong>Forma de Pagamento:</strong> $ret->forma_pagamento </div>".
                        "<div class=\"border px-2 py-1 mr-2\"><strong>Valor Pago: </strong> <span class=\"text-info\">R$ ".$venda->converteCentavoParaReal($ret->total_pago)."</span></div>".
                        "<div class=\"border px-2 py-1 mr-2\"><strong>Valor Restante: </strong> <span class=\"text-danger\">R$ ".$venda->converteCentavoParaReal($ret->valor_total - $ret->total_pago)."</span></div>".
                        "<div class=\"border px-2 py-1 mr-2\"><strong>Parcela(s): </strong> $ret->parcelas"."x</div></div>";
                        
                        $detalhes = json_decode($ret->detalhes_pagamento);
                        if($detalhes == NULL) { $detalhes = [];}

                        $tabParcelas = '<div class="small mt-2 d-flex border border-info p-2 flex-wrap flex-row">';
                        for($i = 0; $i < (int)$ret->parcelas; $i++) {
                            if(isset($detalhes[$i])) {
                                $valorParcela = '<div class="my-2 font-weight-bold bg-info text-white rounded px-1">R$ '.$venda->converteCentavoParaReal($detalhes[$i]->valor).'</div>';

                                $data = new \DateTime($detalhes[$i]->data);
                                $data = '<small>'.$data->format('d/m/Y').'</small>';
                            } else {
                                $valorParcela = '<div class="my-2 font-weight-bold text-info small rounded px-1">--</div>';
                                $data = '<small>--/--/----</small>';
                            }

                            if((int)$ret->parcelas == 1) {
                                $tabParcelas .= '<div class="border text-center shadow-sm p-2 mx-1 mb-2"> <strong>À Vista</strong><br>'.$valorParcela.
                                ''.$data.'</div>';
                            } else {
                                $tabParcelas .= '<div class="border text-center shadow-sm p-2 mx-1 mb-2"> <strong>'.($i+1).'&ordf; parcela</strong><br>'.$valorParcela.
                                ''.$data.'</div>';
                            }

                            unset($valorParcela, $data);
                        }
                        
                        $tabParcelas .= '</div>';
                        $info_pagamento .= $tabParcelas;
                        unset($tabParcelas);
                    break;
                    case 'Paga':
                        $situacao = 'Pago';
                        $info_pagamento = "<div class=\"d-flex mt-2\"> <div class=\"border px-2 py-1 mr-2\"> <strong>Forma de Pagamento:</strong> $ret->forma_pagamento </div>".
                        "<div class=\"border px-2 py-1 mr-2\"><strong>Parcela(s): </strong> $ret->parcelas"."x</div></div>";

                        $detalhes = json_decode($ret->detalhes_pagamento);
                        if($detalhes == NULL) { $detalhes = [];}

                        $tabParcelas = '<div class="small mt-2 d-flex border border-info p-2 flex-wrap flex-row">';
                        for($i = 0; $i < (int)$ret->parcelas; $i++) {
                            if(isset($detalhes[$i])) {
                                $valorParcela = '<div class="my-2 font-weight-bold bg-info text-white rounded px-1">R$ '.$venda->converteCentavoParaReal($detalhes[$i]->valor).'</div>';

                                $data = new \DateTime($detalhes[$i]->data);
                                $data = '<small>'.$data->format('d/m/Y').'</small>';
                            } else {
                                $valorParcela = '<div class="my-2 font-weight-bold text-info small rounded px-1">--</div>';
                                $data = '<small>--/--/----</small>';
                            }

                            if((int)$ret->parcelas == 1) {
                                $tabParcelas .= '<div class="border text-center shadow-sm p-2 mx-1 mb-2"> <strong>À Vista</strong><br>'.$valorParcela.
                                ''.$data.'</div>';
                            } else {
                                $tabParcelas .= '<div class="border text-center shadow-sm p-2 mx-1 mb-2"> <strong>'.($i+1).'&ordf; parcela</strong><br>'.$valorParcela.
                                ''.$data.'</div>';
                            }

                            unset($valorParcela, $data);
                        }
                        
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
                $valorTotal = $venda->converteCentavoParaReal($ret->valor_total);
                $descontoTotal = $venda->converteCentavoParaReal($ret->desconto_total);

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
                        $cliente = new Cliente((int)$l);
                        $c = $cliente->getDados();

                        $nascimento = new DateTime($c->nascimento);
                        $idade = $hoje->diff($nascimento);
                        $idade = $idade->y.($idade->m > 0 ? ','.$idade->m : '');
                        if($c->cpf != '') {
                            $cpf = substr($c->cpf,0, 3).'.***.***-**';
                        } else {
                            $cpf = '-';
                        }
                        $lista_cl .= '
                        <tr>
                            <td>'.$c->id.'</td>
                            <td><a href="javascript:void(0)" onclick="loadCliente('.$c->id.')">'.$c->nome.'</a></td>
                            <td>'.$idade.' ANO(S)</td>
                            <td>'.$c->faixa_etaria.'</td>
                            <td>'.$cpf.'</td>
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
                    <tr><td colspan="6"></td></tr>
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

                // Constrói página com HEREDOC
                $page = <<<PAGINA
            <div class="row">
                <div class="col-12">
                    <h4 class="font-weight-bold">Informações da venda</h4>
                    <div class="row">
                        <div class="col-12">
                            <strong>Destino:</strong> <a href="#roteiros/ver/{$ret->roteiro_id}" target="_blank">{$ret->roteiro_nome} ({$data_ini->format('d/m/Y')} a {$data_fim->format('d/m/Y')})</a><br>
                            <strong>Cliente:</strong> {$ret->cliente_nome} <small>[Cód: {$ret->cliente_id}]</small><br>
                            <strong>Vendedor:</strong> {$ret->usuario_nome}<br>
                            <strong>OBSERVAÇÃO:</strong> <span class="badge badge-pill badge-info mx-2" data-toggle="popover" title="Quer editar?" data-trigger="hover" data-content="Dê um duplo clique no texto para editá-lo."><i class="fas fa-question-circle"></i></span><br>
                            <div class="font-italic border border-top-0 border-bottom-0 border-right-0 border-info ml-1 py-2 pl-2 cursor-pointer"
                                dobbin-campo-edita dobbin-campo-tipo="textarea" dobbin-campo-nome="obs" dobbin-url-form="vendas/{$ret->id}/obs/editar">{$ret->obs}</div>

                            <br>
                            <strong>Situação:</strong> <span class="text-uppercase">{$ret->status_html}</span> <a href="javascript:void(0)" class="ml-2" onclick="vendaGetSituacao({$ret->id})">Alterar situação</a>
                            {$info_pagamento}
                        </div>
                    </div>
                    <hr>
                    
                    <h4 class="font-weight-bold">Datas das Operações</h4>
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
                    <hr>

                    <h4 class="font-weight-bold">Itens Adquiridos</h4>
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
                    <hr>
                    <h4 class="font-weight-bold">Lista de passageiros <small>[Criança(s): $ret->criancas | Adulto(s): $ret->adultos]</small></h4>
                    $btn_cl_add
                    <table class="table table-bordered table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Cód.</th>
                                <th>Nome</th>
                                <th>Idade</th>
                                <th>Faixa Etária</th>
                                <th>CPF</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            $lista_cl
                        </tbody>
                    </table>
                    
                    <strong></strong> <br>
                    <strong></strong> <br>
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

                switch($ret->status) {
                    case 'Reserva': // Permitido: Aguardando Pagamento, Pagamento, Cancelar
                        $situacao = 'Reserva';
                        $btn_situ = '
                        <li class="nav-item d-flex mr-md-2 mb-2 mb-md-0"> <a class="nav-link small align-self-center btn-outline-primary" data-toggle="pill" href="#tabAguardando">Aguardando Pagamento</a> </li>
                        <li class="nav-item d-flex mr-md-2 mb-2 mb-md-0"> <a class="nav-link small align-self-center btn-outline-primary" data-toggle="pill" href="#tabPagamento">Pagamento</a> </li>
                        <li class="nav-item d-flex mr-md-2 mb-2 mb-md-0"> <a class="nav-link small align-self-center btn-outline-primary" data-toggle="pill" href="#tabCancelado">Cancelado</a> </li>
                        <li class="nav-item d-flex mr-md-2 mb-2 mb-md-0"> <a class="nav-link disabled small align-self-center btn-outline-primary" data-toggle="pill" href="#tabEstornado">Estornado</a> </li>
                        ';
                    break;
                    case 'Aguardando': // Permitido: Pagamento, Cancelar
                        $situacao = 'Aguardando pagamento';
                        $btn_situ = '
                        <li class="nav-item d-flex mr-md-2 mb-2 mb-md-0"> <a class="nav-link disabled small align-self-center btn-outline-primary" data-toggle="pill" href="#tabAguardando">Aguardando Pagamento</a> </li>
                        <li class="nav-item d-flex mr-md-2 mb-2 mb-md-0"> <a class="nav-link small align-self-center btn-outline-primary" data-toggle="pill" href="#tabPagamento">Pagamento</a> </li>
                        <li class="nav-item d-flex mr-md-2 mb-2 mb-md-0"> <a class="nav-link small align-self-center btn-outline-primary" data-toggle="pill" href="#tabCancelado">Cancelado</a> </li>
                        <li class="nav-item d-flex mr-md-2 mb-2 mb-md-0"> <a class="nav-link disabled small align-self-center btn-outline-primary" data-toggle="pill" href="#tabEstornado">Estornado</a> </li>
                        ';
                    break;
                    case 'Pagando': // Permitido: Pagamento, Estornar
                        $situacao = 'Pagando';
                        $btn_situ = '
                        <li class="nav-item d-flex mr-md-2 mb-2 mb-md-0"> <a class="nav-link disabled small align-self-center btn-outline-primary" data-toggle="pill" href="#tabAguardando">Aguardando Pagamento</a> </li>
                        <li class="nav-item d-flex mr-md-2 mb-2 mb-md-0"> <a class="nav-link small align-self-center btn-outline-primary" data-toggle="pill" href="#tabPagamento">Pagamento</a> </li>
                        <li class="nav-item d-flex mr-md-2 mb-2 mb-md-0"> <a class="nav-link disabled small align-self-center btn-outline-primary" data-toggle="pill" href="#tabCancelado">Cancelado</a> </li>
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
                    break;
                    case 'Cancelada':
                        $situacao = 'Cancelado';
                        $btn_situ = '
                        <li class="nav-item d-flex mr-md-2 mb-2 mb-md-0"> <a class="nav-link disabled small align-self-center btn-outline-primary" data-toggle="pill" href="#tabAguardando">Aguardando Pagamento</a> </li>
                        <li class="nav-item d-flex mr-md-2 mb-2 mb-md-0"> <a class="nav-link disabled small align-self-center btn-outline-primary" data-toggle="pill" href="#tabPagamento">Pagamento</a> </li>
                        <li class="nav-item d-flex mr-md-2 mb-2 mb-md-0"> <a class="nav-link disabled small align-self-center btn-outline-primary" data-toggle="pill" href="#tabCancelado">Cancelado</a> </li>
                        <li class="nav-item d-flex mr-md-2 mb-2 mb-md-0"> <a class="nav-link disabled small align-self-center btn-outline-primary" data-toggle="pill" href="#tabEstornado">Estornado</a> </li>
                        ';
                    break;
                    case 'Devolvida':
                        $situacao = 'Estornado';
                        $btn_situ = '
                        <li class="nav-item d-flex mr-md-2 mb-2 mb-md-0"> <a class="nav-link disabled small align-self-center btn-outline-primary" data-toggle="pill" href="#tabAguardando">Aguardando Pagamento</a> </li>
                        <li class="nav-item d-flex mr-md-2 mb-2 mb-md-0"> <a class="nav-link disabled small align-self-center btn-outline-primary" data-toggle="pill" href="#tabPagamento">Pagamento</a> </li>
                        <li class="nav-item d-flex mr-md-2 mb-2 mb-md-0"> <a class="nav-link disabled small align-self-center btn-outline-primary" data-toggle="pill" href="#tabCancelado">Cancelado</a> </li>
                        <li class="nav-item d-flex mr-md-2 mb-2 mb-md-0"> <a class="nav-link disabled small align-self-center btn-outline-primary" data-toggle="pill" href="#tabEstornado">Estornado</a> </li>
                        ';
                    break;
                }

                // SELECT: QUANTIDADE DE PARCELAS
                $qtdParcelas = '';
                for($i = 2; $i <= \DOBBIN_MAX_PARCELAS; $i++) {
                    $qtdParcelas .= '<option value="'.$i.'">'.$i.'x</option>';
                }

                // SELECT: DIAS VENCIMENTO
                $diasVenc = '';
                for($i = 1; $i <= 28; $i++) {
                    if((int)$hoje->format('j') == $i) {
                        $diasVenc .= '<option value="'.$i.'" selected>'.$i.'</option>';
                    } else {
                        $diasVenc .= '<option value="'.$i.'">'.$i.'</option>';
                    }
                    
                }

                // TAB: AGUARDANDO PAGAMENTO
                $tabAgu = '';
                if($ret->status == 'Pagando' || $ret->status == 'Paga' || $ret->status == 'Cancelada' || $ret->status == 'Devolvida') {
                    $tabAgu .= '<div class="alert alert-info">Esta opção não está disponível para esta venda.</div>';
                } else {
                    $tabAgu = <<<TAB
                        <div class="mb-2">
                            <strong>Valor da compra: </strong> R$ <span>{$sgc->converteCentavoParaReal($ret->valor_total)}</span>
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
                                <select class="form-control form-control-sm" name="parcelas" data-valortotal="{$sgc->converteCentavoParaReal($ret->valor_total)}" onchange="(function(){
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
                                    <input type="text" class=" form-control form-control-sm" name="valor_parcela" value="{$sgc->converteCentavoParaReal($ret->valor_total)}" dobbin-mask-money disabled>
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
                            <strong>Valor da compra: </strong> R$ <span>{$sgc->converteCentavoParaReal($ret->valor_total)}</span> <span class="ml-2 text-muted">(À vista)</span>
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
                    $valorParcSugerido = ceil( ( (int)$ret->valor_total - (int)$ret->total_pago ) / ( (int)$ret->parcelas - (int)$ret->parcelas_pagas ) );

                    $tabPag = <<<TAB

                        {$tabParcelas}
                        <div class="mb-2 d-flex flex-column flex-md-row justify-content-md-between">
                            <div>
                                <strong>Valor total da compra: </strong> R$ <span>{$sgc->converteCentavoParaReal($ret->valor_total)}</span>
                            </div>
                            <div>
                                <strong>Valor total pago: </strong> R$ <span>{$sgc->converteCentavoParaReal($ret->total_pago)}</span>
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
                                <strong>Valor total da compra: </strong> R$ <span>{$sgc->converteCentavoParaReal($ret->valor_total)}</span>
                            </div>
                            <div class="mb-2 border shadow-sm p-2">
                                <strong>Valor pago: </strong> R$ <span>{$sgc->converteCentavoParaReal($ret->total_pago)}</span>
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
                                if(outro.valor_estorno == 0) {
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