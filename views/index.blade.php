<div class="row mb-3">
    <div class="col-6 col-lg-3 mb-3">
        <a href="#vendas/novo" class="btn btn-block btn-info rounded-0 shadow-sm font-weight-bold py-2"><i class="fas fa-shopping-cart fa-fw"></i> Nova Venda</a>
    </div>
    <div class="col-6 col-lg-3 mb-3">
        <a href="#roteiros/novo" class="btn btn-block btn-info rounded-0 shadow-sm font-weight-bold py-2"><i class="fas fa-luggage-cart fa-fw"></i> Novo Roteiro</a>
    </div>
</div>

<div class="row">
    <div class="col-12 col-md-9 col-lg-5 col-xl-6 mx-auto">
        <div class="card">
            <div class="card-header">
                INFORMAÇÕES DO SISTEMA
            </div>
            <div class="card-body">
                Dados relevantes sobre o funcionamento da plataforma.
                @php
                    $consumo = $sistema['consumo'];
                    $limite = $sistema['limite'];
                    $perc = array();
                    $status = array();

                    foreach($consumo as $key => $val) {
                        $perc[$key] = ($val * 100) / $limite[$key];
                        if($perc[$key] < 50) {
                            $status[$key] = 'primary';
                        } else if($perc[$key] < 80) {
                            $status[$key] = 'success';
                        } else if($perc[$key] < 95) {
                            $status[$key] = 'warning';
                        } else {
                            $status[$key] = 'danger';
                        }
                    }
                @endphp
                <div class="row mt-3">
                    <div class="col-12 col-md-6 col-lg-6 mb-3">
                        <div class="border border-{{$status['clientes']}} shadow-sm p-2">
                            <strong class="text-primary">Clientes</strong><br>
                            <span>{{$consumo['clientes']}}</span> / <span>{{$limite['clientes']}}</span> <span class="ml-2">({{round($perc['clientes'], 2)}}%)</span>
                            <div class="progress">
                                <div class="progress-bar bg-{{$status['clientes']}}" style="width:{{round($perc['clientes'], 2)}}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6 mb-3">
                        <div class="border border-{{$status['parceiros']}} shadow-sm p-2">
                            <strong class="text-primary">Parceiros</strong><br>
                            <span>{{$consumo['parceiros']}}</span> / <span>{{$limite['parceiros']}}</span> <span class="ml-2">({{round($perc['parceiros'], 2)}}%)</span>
                            <div class="progress">
                                <div class="progress-bar bg-{{$status['parceiros']}}" style="width:{{round($perc['parceiros'], 2)}}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6 mb-3">
                        <div class="border border-{{$status['roteiros']}} shadow-sm p-2">
                            <strong class="text-primary">Roteiros</strong><br>
                            <span>{{$consumo['roteiros']}}</span> / <span>{{$limite['roteiros']}}</span> <span class="ml-2">({{round($perc['roteiros'], 2)}}%)</span>
                            <div class="progress">
                                <div class="progress-bar bg-{{$status['roteiros']}}" style="width:{{round($perc['roteiros'], 2)}}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6 mb-3">
                        <div class="border border-{{$status['vendas']}} shadow-sm p-2">
                            <strong class="text-primary">Vendas</strong><br>
                            <span>{{$consumo['vendas']}}</span> / <span>{{$limite['vendas']}}</span> <span class="ml-2">({{round($perc['vendas'], 2)}}%)</span>
                            <div class="progress">
                                <div class="progress-bar bg-{{$status['vendas']}}" style="width:{{round($perc['vendas'], 2)}}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6 mb-3">
                        <div class="border border-{{$status['usuarios']}} shadow-sm p-2">
                            <strong class="text-primary">Usuários</strong><br>
                            <span>{{$consumo['usuarios']}}</span> / <span>{{$limite['usuarios']}}</span> <span class="ml-2">({{round($perc['usuarios'], 2)}}%)</span>
                            <div class="progress">
                                <div class="progress-bar bg-{{$status['usuarios']}}" style="width:{{round($perc['usuarios'], 2)}}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    
    <div class="col-12 col-md-11 col-lg-7 col-xl-6 mx-auto">
        <div class="card">
            <div class="card-header">
                Aniversariantes
            </div>
            <div class="card-body">
            
            @if($aniversarios['success'] == true)
                <h5 class="font-weight-bold"><i class="fas fa-gift"></i> HOJE</h5>
                <table class="table table-hover table-sm mb-4">
                    <tbody>
                    @if(empty($aniversarios['hoje']))
                    <tr><td class="text-center">Nenhum aniversário hoje</td></tr>
                    @else
                    @foreach($aniversarios['hoje'] as $h)
                    <tr onclick="loadCliente({{$h->id}})" class="cursor-pointer">
                        <td class="">
                            <div class="d-flex justify-content-between">
                                <strong>{{$h->nome}}</strong> <span>({{$h->cidade == '' ? '-' : $h->cidade}}/{{$h->estado}})</span>
                            </div>
                            
                            <div class="d-flex mt-1 small justify-content-between">
                                <span>{{$h->nascimento_str}}</span> <span>{{$h->idade}} {{$h->idade > 1 ? 'anos' : 'ano'}}</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @endif
                    </tbody>
                </table><br>

                <h5 class="font-weight-bold">PRÓXIMOS ANIVERSÁRIOS</h5>
                <table class="table table-sm table-hover">
                    <tbody>
                    @if(empty($aniversarios['amanha']))
                    <tr><td class="text-center">Nenhum aniversário nos próximos dias</td></tr>
                    @else
                    @foreach($aniversarios['amanha'] as $h)
                    <tr onclick="loadCliente({{$h->id}})" class="cursor-pointer">
                        <td class="">
                            <div class="d-flex justify-content-between">
                                <strong>{{$h->nome}}</strong> <span>({{$h->cidade == '' ? '-' : $h->cidade}}/{{$h->estado}})</span>
                            </div>
                            
                            <div class="d-flex mt-1 small justify-content-between">
                                <span>{{$h->nascimento_str}}</span> <span>{{$h->idade}} {{$h->idade > 1 ? 'anos' : 'ano'}}</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @endif
                    </tbody>
                </table>
            @else
                <div class="alert alert-info small px-2 py-1">
                    Houve um erro ao retornar os aniversariantes: {{$retorno['mensagem']}}
                </div>
            @endif
            </div>
        </div>
    </div>

    <div class="col-12 col-md-12 col-lg-12 col-xl-8 mx-auto">
        <div class="card">
            <div class="card-header">
                Vencimentos
            </div>
            <div class="card-body">
                <h5 class="font-weight-bold">VENCIMENTOS DE HOJE</h5>
                <table class="table table-hover table-sm mb-4">
                    <thead>
                        <tr>
                            <th>Venda</th>
                            <th>Situação</th>
                            <th><abbr title="Esta é a parcela esperada para receber o pagamento hoje." data-toggle="tooltip">Parcela</abbr></th>
                            <th><abbr title="Data do vencimento da venda." data-toggle="tooltip">Data</abbr></th>
                        </tr>
                    </thead>
                    <tbody>
                    @if(empty($vencimentos['hoje']))
                    <tr>
                        <td colspan="4" class="text-center"> Nenhum vencimento hoje </td>
                    </tr>
                    @else
                    @foreach($vencimentos['hoje'] as $h)
                    @php
                        $data_ini = new \DateTime($h->roteiro_data_ini);
                        $data_fim = new \DateTime($h->roteiro_data_fim);
                        $data_reserva = new \DateTime($h->data_reserva);
                    @endphp
                    <tr class="cursor-pointer" onclick="getVenda({{$h->id}})" data-toggle="popover" data-placement="top" data-trigger="hover"
                    data-content="<strong>Roteiro: </strong> {{$h->roteiro_nome}} ({{$data_ini->format('d/m/Y')}} a {{$data_fim->format('d/m/Y')}})<br>
                    <strong>Cliente: </strong> {{$h->cliente_nome}}<br> <strong>Data da reserva: </strong> {{$data_reserva->format('d/m/Y')}}<br><br>
                    <strong>Valor Total: </strong> R$ {{$sgc->converteCentavoParaReal($h->valor_total)}}<br> <strong>Parcelas Pagas:</strong> {{$h->parcelas_pagas}}<br>
                    <strong>Total Pago: </strong> R$ {{$sgc->converteCentavoParaReal($h->total_pago)}}"
                    title="<strong class='text-info'>Resumo da venda</strong>">
                        <td><small>{{$h->id}}</small></td>
                        <td>{!!$h->status_html!!} ({{$h->forma_pagamento}})</td>
                        <td><b>{{$h->parcelas_pagas + 1}}</b>/{{$h->parcelas}}</td>
                        <td>{{$h->vencimento}}</td>
                    </tr>
                    @endforeach
                    @endif
                    </tbody>
                </table><br>

                <h5 class="font-weight-bold">PRÓXIMOS VENCIMENTOS</h5>
                <table class="table table-hover table-sm mb-4">
                    <thead>
                        <tr>
                            <th>Venda</th>
                            <th>Situação</th>
                            <th><abbr title="Esta é a parcela esperada para receber o pagamento nos respectivos vencimentos." data-toggle="tooltip">Parcela</abbr></th>
                            <th><abbr title="Data do vencimento da venda." data-toggle="tooltip">Data</abbr></th>
                        </tr>
                    </thead>
                    <tbody>
                    @if(empty($vencimentos['proximos']))
                    <tr>
                        <td colspan="4" class="text-center"> Nenhum vencimento para os próximos 7 dias </td>
                    </tr>
                    @else
                    @foreach($vencimentos['proximos'] as $h)
                    <tr class="cursor-pointer" onclick="getVenda({{$h->id}})" data-toggle="popover" data-placement="top" data-trigger="hover"
                    data-content="<strong>Roteiro: </strong> {{$h->roteiro_nome}} ({{$data_ini->format('d/m/Y')}} a {{$data_fim->format('d/m/Y')}})<br>
                    <strong>Cliente: </strong> {{$h->cliente_nome}}<br> <strong>Data da reserva: </strong> {{$data_reserva->format('d/m/Y')}}<br><br>
                    <strong>Valor Total: </strong> R$ {{$sgc->converteCentavoParaReal($h->valor_total)}}<br> <strong>Parcelas Pagas:</strong> {{$h->parcelas_pagas}}<br>
                    <strong>Total Pago: </strong> R$ {{$sgc->converteCentavoParaReal($h->total_pago)}}"
                    title="<strong class='text-info'>Resumo da venda</strong>">
                        <td><small>{{$h->id}}</small></td>
                        <td>{!!$h->status_html!!} ({{$h->forma_pagamento}})</td>
                        <td><b>{{$h->parcelas_pagas + 1}}</b>/{{$h->parcelas}}</td>
                        <td>{{$h->vencimento}}</td>
                    </tr>
                    @endforeach
                    @endif
                    </tbody>
                </table>
                
            </div>
        </div>
    </div>
</div>