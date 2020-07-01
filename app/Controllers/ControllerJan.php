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
                $btnDescontoEdita = $btnQtdEdita = '';
                switch($ret->status) {
                    case 'Reserva':
                        $situacao = 'Reserva';
                        $btn_situ = '
                        <button type="button" class="btn btn-sm btn-primary mr-md-2" onclick="vendaAlteraSituacao('.$ret->id.', \'Aguardando\', \'\', $(this))">Aguardando pagamento</button>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-primary mr-md-2 dropdown-toggle" data-toggle="dropdown">Pago</button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="javascript:void(0)" onclick="vendaAlteraSituacao('.$ret->id.', \'Paga\', \'Crédito\', $(this))">Crédito</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="vendaAlteraSituacao('.$ret->id.', \'Paga\', \'Débito\', $(this))">Débito</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="vendaAlteraSituacao('.$ret->id.', \'Paga\', \'Boleto\', $(this))">Boleto</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="vendaAlteraSituacao('.$ret->id.', \'Paga\', \'Digital\', $(this))">Digital</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="vendaAlteraSituacao('.$ret->id.', \'Paga\', \'Transferência\', $(this))">Transferência</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="vendaAlteraSituacao('.$ret->id.', \'Paga\', \'Dinheiro\', $(this))">Dinheiro</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="vendaAlteraSituacao('.$ret->id.', \'Paga\', \'Outro\', $(this))">Outro</a>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary mr-md-2" onclick="vendaAlteraSituacao('.$ret->id.', \'Cancelada\', \'\', $(this))">Cancelado</button>
                        <button type="button" class="btn btn-sm btn-primary mr-md-2" disabled>Estornado</button>
                        ';
                        $btnDescontoEdita = '';
                        $btnQtdEdita = '';
                    break;
                    case 'Aguardando':
                        $situacao = 'Aguardando pagamento';
                        $btn_situ = '
                        <button type="button" class="btn btn-sm btn-primary mr-md-2" disabled>Aguardando pagamento</button>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-primary mr-md-2 dropdown-toggle" data-toggle="dropdown">Pago</button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="javascript:void(0)" onclick="vendaAlteraSituacao('.$ret->id.', \'Paga\', \'Crédito\', $(this))">Crédito</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="vendaAlteraSituacao('.$ret->id.', \'Paga\', \'Débito\', $(this))">Débito</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="vendaAlteraSituacao('.$ret->id.', \'Paga\', \'Boleto\', $(this))">Boleto</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="vendaAlteraSituacao('.$ret->id.', \'Paga\', \'Digital\', $(this))">Digital</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="vendaAlteraSituacao('.$ret->id.', \'Paga\', \'Transferência\', $(this))">Transferência</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="vendaAlteraSituacao('.$ret->id.', \'Paga\', \'Dinheiro\', $(this))">Dinheiro</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="vendaAlteraSituacao('.$ret->id.', \'Paga\', \'Outro\', $(this))">Outro</a>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary mr-md-2" onclick="vendaAlteraSituacao('.$ret->id.', \'Cancelada\', \'\', $(this))">Cancelado</button>
                        <button type="button" class="btn btn-sm btn-primary mr-md-2" disabled>Estornado</button>
                        ';
                    break;
                    case 'Paga':
                        $situacao = 'Pago';
                        $btn_situ = '
                        <button type="button" class="btn btn-sm btn-primary mr-md-2" disabled>Aguardando pagamento</button>
                        <button type="button" class="btn btn-sm btn-primary mr-md-2" disabled>Pago</button>
                        <button type="button" class="btn btn-sm btn-primary mr-md-2" disabled>Cancelado</button>
                        <button type="button" class="btn btn-sm btn-primary mr-md-2" onclick="alert(\'Ainda indisponível...\')">Estornado</button>
                        ';
                    break;
                    case 'Cancelada':
                        $situacao = 'Cancelado';
                        $btn_situ = '
                        <button type="button" class="btn btn-sm btn-primary mr-md-2" disabled>Aguardando pagamento</button>
                        <button type="button" class="btn btn-sm btn-primary mr-md-2" disabled>Pago</button>
                        <button type="button" class="btn btn-sm btn-primary mr-md-2" disabled>Cancelado</button>
                        <button type="button" class="btn btn-sm btn-primary mr-md-2" disabled>Estornado</button>
                        ';
                    break;
                    case 'Devolvida':
                        $situacao = 'Estornado';
                        $btn_situ = '
                        <button type="button" class="btn btn-sm btn-primary mr-md-2" disabled>Aguardando pagamento</button>
                        <button type="button" class="btn btn-sm btn-primary mr-md-2" disabled>Pago</button>
                        <button type="button" class="btn btn-sm btn-primary mr-md-2" disabled>Cancelado</button>
                        <button type="button" class="btn btn-sm btn-primary mr-md-2" disabled>Estornado</button>
                        ';
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
                            <td></td>
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
                <strong>Destino:</strong> {$ret->roteiro_nome} ({$data_ini->format('d/m/Y')} a {$data_fim->format('d/m/Y')})<br>
                <strong>Cliente:</strong> {$ret->cliente_nome} <small>[Cód: {$ret->cliente_id}]</small><br>
                <strong>Vendedor:</strong> {$ret->usuario_nome}<br>
                <strong>OBSERVAÇÃO:</strong> <i>{$ret->obs}</i><br><br>
                <strong>Situação:</strong> <span class="badge badge-dark text-uppercase px-2">{$situacao}</span> <a href="javascript:void(0)" class="ml-2" onclick="$(this).next().fadeToggle();">Alterar situação</a>
                
                <div class="border rounded-sm p-2 mb-3 mt-1 border-primary mb-0" style="display:none">
                    <div class="d-flex flex-md-row flex-column">
                        $btn_situ
                    </div>
                </div>
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

}