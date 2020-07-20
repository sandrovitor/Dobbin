<html>
    <head>
        <style>
            @page {
                margin: 15px 15px 25px;
                font-size: 14px;
            }
            
            @import url('https://fonts.googleapis.com/css2?family=Metrophobic:wght@400;700&display=swap');
            h1, h2, h3, h4, h5, h6 {
                font-family: 'Metrophobic', sans-serif;
            }

            .table-bordered.border-dark td, .table-bordered.border-dark th {
                border-color: #343a40!important;
            }

            footer {
                position:fixed;
                bottom: 10px;
                left:0;
                right:0;
                width:100%;
            }
        </style>
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    </head>
    @php
    $data_reserva = new \DateTime($v->data_reserva);
    $data_ini = new \DateTime($v->roteiro_data_ini);
    $data_fim = new \DateTime($v->roteiro_data_fim);

    if($data_ini == $data_fim) {
        $periodo = $data_ini->format('d/m/Y');
    } else {
        $periodo = $data_ini->format('d/m/Y') . ' a ' . $data_fim->format('d/m/Y');;
    }
    if($v->data_venda == NULL) {
        $data_venda = '-';
    } else {
        $data_venda = new DateTime($v->data_venda);
        $data_venda = $data_venda->format('d/m/Y H:i:s');
    }

    if($v->data_pagamento == NULL) {
        $data_pagamento = '-';
    } else {
        $data_pagamento = new DateTime($v->data_pagamento);
        $data_pagamento = $data_pagamento->format('d/m/Y H:i:s');
    }

    if($v->data_cancelado == NULL) {
        $data_cancelado = '-';
    } else {
        $data_cancelado = new DateTime($v->data_cancelado);
        $data_cancelado = $data_cancelado->format('d/m/Y H:i:s');
    }

    if($v->data_estorno == NULL) {
        $data_estorno = '-';
    } else {
        $data_estorno = new DateTime($v->data_estorno);
        $data_estorno = $data_estorno->format('d/m/Y H:i:s');
    }

    $items = json_decode($v->items);
    if($items == NULL) {
        $items = [];
    }
    
    @endphp
    <body>
        <table class="table table-bordered border-dark table-sm mb-1">
            <tr>
                <td><img src="https://tonaestradaviagens.com.br/media/images/logo-wide64.png" height="40"></td>
                <td class="text-center" style="vertical-align:middle"><strong>COMPROVANTE DE RESERVA E PAGAMENTO</strong></td>
                <td class="text-center" style="vertical-align:middle"><small class="font-weight-bold">RESERVA</small> <br><small class="text-muted">{{$v->id}}</small></td>
            </tr>
        </table>

        <table class="table table-bordered border-dark table-sm mt-0 mb-4">
            <tr>
                <td><strong>EMPRESA:</strong></td>
                <td>{{$system->empresa_nome}}</td>
            </tr>
            <tr>
                <td><strong>CNPJ:</strong></td>
                <td>{{$system->empresa_cnpj}}</td>
            </tr>
        </table>

        <table class="table table-bordered border-dark table-sm my-0">
            <tr>
                <td><strong>Roteiro:</strong></td>
                <td colspan="3">{{$v->roteiro_nome}} ({{$periodo}}) <small class="text-muted">[Cód. {{$v->roteiro_id}}]</small></td>
            </tr>
            <tr>
                <td><strong>Cliente:</strong></td>
                <td colspan="3">{{$v->cliente_nome}} <small class="text-muted">[Cód. {{$v->cliente_id}}]</small></td>
            </tr>
            <tr>
                <td style="width:160px;"><strong>Qtd. Passagens:</strong></td>
                <td>{{$v->clientes_total}}</td>
                <td style="width:160px;"><strong>Data da Reserva:</strong></td>
                <td>{{$data_reserva->format('d/m/Y H:i:s')}}</td>
            </tr>
            <tr>
                <td class="text-center">ADULTOS: <strong>{{$v->adultos}}</strong></td>
                <td class="text-center">CRIANÇAS: <strong>{{$v->criancas}}</strong></td>
                <td colspan="2">-</td>
            </tr>
            
        </table>
        
        <table class="table table-bordered border-dark table-sm mt-1 mb-4 small">
            <tr>
                <th>#</th>
                <th>Tarifa</th>
                <th>Qtd</th>
                <th>Subtotal</th>
            </tr>
            @if(empty($items))

            @else
            @foreach($items as $chave => $i)
            <tr>
                <td>{{$chave+1}}</td>
                <td>{{$i->tarifa}}</td>
                <td>{{$i->qtd}}</td>
                <td>R$ {{$venda->converteCentavoParaReal($i->subtotal)}}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="3"><strong>TOTAL</strong></td>
                <td class="text-primary font-weight-bold">R$ {{$venda->converteCentavoParaReal($v->valor_total)}}</td>
            </tr>
            @endif
        </table>

        @if($v->status == 'Reserva')
        <table class="table table-bordered border-dark table-sm mt-0">
            <tr>
                <td style="width:160px;"><strong>Situação:</strong></td>
                <td colspan="3" class="text-uppercase text-center font-weight-bold text-info">{{$v->status}}</td>
            </tr>
        </table>
        @elseif($v->status == 'Aguardando')
        <table class="table table-bordered border-dark table-sm mt-0">
            <tr>
                <td style="width:160px;"><strong>Situação:</strong></td>
                <td colspan="3" class="text-uppercase text-center font-weight-bold text-primary">Aguardando Pagamento</td>
            </tr>
            <tr>
                <td><strong>Data da Venda:</strong></td>
                <td class="text-uppercase text-center">{{$data_venda}}</td>
                <td><strong>Valor:</strong></td>
                <td>R$ {{$venda->converteCentavoParaReal($v->valor_total)}}</td>
            </tr>
            <tr>
                <td colspan="4">
                    <table class="table table-sm table-borderless my-0">
                        <tr>
                            <td class="text-center">
                                <strong>Forma de Pagamento</strong><br>
                                {{$v->forma_pagamento}}
                            </td>
                            <td class="text-center border border-bottom-0 border-top-0">
                                <strong>Qtd. de Parcelas</strong><br>
                                {{$v->parcelas}}
                            </td>
                            <td class="text-center">
                                <strong>Vencimento</strong><br>
                                {{$v->vencimento}}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        @elseif($v->status == 'Pagando')
        @php
        $detalhes = json_decode($v->detalhes_pagamento);
        $ultParc = $detalhes[count($detalhes) - 1];
        $data_parcela = new \DateTime($ultParc->data);
        $data_parcela = $data_parcela->format('d/m/Y H:i:s');
        @endphp
        <table class="table table-bordered border-dark table-sm mt-0">
            <tr>
                <td style="width:170px;"><strong>Situação:</strong></td>
                <td colspan="5" class="text-uppercase text-center font-weight-bold text-success">Em Pagamento</td>
            </tr>
            <tr>
                <td><strong>Data do Pagamento:</strong></td>
                <td colspan="5" class="text-uppercase text-center">{{$data_parcela}}</td>
            </tr>
            <tr>
                <td><strong>Parcelas:</strong></td>
                <td>{{$v->parcelas_pagas}}/{{$v->parcelas}}</td>
                <td><strong>Valor:</strong></td>
                <td>R$ {{$venda->converteCentavoParaReal($ultParc->valor)}}</td>
                <td><strong>Quitado:</strong></td>
                <td>R$ {{$venda->converteCentavoParaReal($v->total_pago)}}</td>
            </tr>
            <tr>
                <td colspan="6">
                    <table class="table table-borderless my-0">
                        <tr>
                            <td class="text-center">
                                <strong>Forma de Pagamento</strong><br>
                                {{$v->forma_pagamento}}
                            </td>
                            <td class="text-center border border-bottom-0 border-top-0">
                                <strong>Qtd. de Parcelas</strong><br>
                                {{$v->parcelas}}
                            </td>
                            <td class="text-center">
                                <strong>Vencimento</strong><br>
                                {{$v->vencimento}}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        @elseif($v->status == 'Paga')
        <table class="table table-bordered border-dark table-sm mt-0">
            <tr>
                <td style="width:160px;"><strong>Situação:</strong></td>
                <td colspan="3" class="text-uppercase text-center font-weight-bold text-success">{{$v->status}}</td>
            </tr>
            <tr>
                <td><strong>Data do Pagamento:</strong></td>
                <td class="text-center">{{$data_pagamento}}</td>
                <td><strong>Valor:</strong></td>
                <td>R$ {{$venda->converteCentavoParaReal($v->total_pago)}}</td>
            </tr>
            <tr>
                <td colspan="4">
                    <table class="table table-borderless my-0">
                        <tr>
                            <td class="text-center">
                                <strong>Forma de Pagamento</strong><br>
                                {{$v->forma_pagamento}}
                            </td>
                            <td class="text-center border border-bottom-0 border-top-0">
                                <strong>Qtd. de Parcelas</strong><br>
                                {{$v->parcelas}}
                            </td>
                            <td class="text-center">
                                <strong>Vencimento</strong><br>
                                {{$v->vencimento}}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        @elseif($v->status == 'Cancelada')
        <table class="table table-bordered border-dark table-sm mt-0">
            <tr>
                <td style="width:190px;"><strong>Situação:</strong></td>
                <td colspan="3" class="text-uppercase text-center font-weight-bold text-dark">{{$v->status}}</td>
            </tr>
            <tr>
                <td><strong>Data do Cancelamento:</strong></td>
                <td colspan="3" class="text-center">{{$data_cancelado}}</td>
            </tr>
        </table>
        @elseif($v->status == 'Devolvida')
        <table class="table table-bordered border-dark table-sm mt-0">
            <tr>
                <td style="width:160px;"><strong>Situação:</strong></td>
                <td colspan="3" class="text-uppercase text-center font-weight-bold text-dark">{{$v->status}}</td>
            </tr>
            <tr>
                <td><strong>Data do Estorno:</strong></td>
                <td colspan="3" class="text-center">{{$data_estorno}}</td>
            </tr>
            <tr>
                <td><strong>Valor Pago:</strong></td>
                <td>R$ {{$venda->converteCentavoParaReal($v->total_pago)}}</td>
                <td><strong>Valor Devolvido:</strong></td>
                <td class="font-weight-bold text-danger">R$ {{$venda->converteCentavoParaReal($v->valor_devolvido)}}</td>
            </tr>
            <tr>
                <td colspan="4">
                    <table class="table table-sm table-borderless my-0 small">
                        <tr>
                            <td class="text-center">
                                <strong>Forma de Pagamento</strong><br>
                                {{$v->forma_pagamento}}
                            </td>
                            <td class="text-center border border-bottom-0 border-top-0">
                                <strong>Qtd. de Parcelas</strong><br>
                                {{$v->parcelas}}
                            </td>
                            <td class="text-center">
                                <strong>Vencimento</strong><br>
                                {{$v->vencimento}}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        @endif
        <footer class="text-center">
            <small class="font-italic text-muted">Comprovante gerado em: {{date('d/m/Y H:i:s')}}</small>
        </footer>
        
    </body>
</html>