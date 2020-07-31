<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-12">
                        <button type="button" class="btn btn-sm btn-primary" onclick="$('.card-header.card-collapse').siblings('.card-body').not(':visible').slideDown('fast');">Mostrar tudo</button>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="$('.card-header.card-collapse').siblings('.card-body:visible').slideUp('fast');">Esconder tudo</button>

                        <button type="button" class="btn btn-sm btn-light mr-2 disabled" disabled>|</button>
                        <button type="button" class="btn btn-sm btn-dark mr-2" onclick="$('.modal.show').modal('hide'); $('#janCriarCopiaRoteiro').modal('show')" data-id="{{$roteiro->id}}">Criar cópia do roteiro</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8">
                        <!-- COLUNA 1 -->
                        <div class="card rounded-0">
                            <div class="card-header card-collapse text-dark px-2 py-1">
                                GERAL
                            </div>
                            <div class="card-body pt-3 pb-0 px-2" >
                                <div class="mb-3">
                                    <a href="#roteiros/editar/{{$roteiro->id}}" class="btn btn-sm btn-primary mr-2">Editar roteiro</a>
                                    <button type="button" class="btn btn-sm btn-danger mr-2" onclick="roteiroApagar(this)" data-id="{{$roteiro->id}}">Apagar roteiro</button>
                                </div>
                            @php
                                $hoje = new DateTime();
                                $partida = new DateTime($roteiro->data_ini);
                                $retorno = new DateTime($roteiro->data_fim);
                                $criado = new DateTime($roteiro->criado_em);
                                $atualizado = new DateTime($roteiro->atualizado_em);

                                $receita = 0; // Soma de valores pagos até agora em todas as vendas. [TOTAL REAL]
                                $receita_esperada = 0; // Valor total esperado com todas as vendas. [PROJETADA]

                                

                            @endphp
                                <table class="table table-bordered table-sm">
                                    <tbody>
                                        <tr>
                                            <td class="px-3 py-1" colspan="2"><strong>Roteiro:</strong> <span class="ml-2">{{$roteiro->nome}}</span> {!!$roteiro->situacao_html!!}</td>
                                        </tr>
                                        <tr>
                                            <td class="px-3 py-1"><strong>Partida:</strong> <span class="ml-2">{{$partida->format('d/m/Y')}}</span></td>
                                            <td class="px-3 py-1"><strong>Retorno:</strong> <span class="ml-2">{{$retorno->format('d/m/Y')}}</span></td>
                                        </tr>
                                        <tr>
                                            <td class="px-3 py-2" colspan="2"></td>
                                        </tr>
                                        <tr>
                                            <td class="px-3 py-1"><strong>Total de poltronas:</strong> <span class="ml-2">{{(int)$roteiro->passagens + (int)$roteiro->qtd_coordenador}}</span></td>
                                            <td class="px-3 py-1"><strong>Poltronas livre:</strong> <span class="ml-2" data-poltrona-livre>{{(int)$roteiro->estoque['livre']}}</span></td>
                                        </tr>
                                        <tr>
                                            <td class="px-3 py-1"><strong>Clientes (pagantes e cortesias):</strong> <span class="ml-2">{{(int)$roteiro->passagens}}</span></td>
                                            <td class="px-3 py-1"><strong>Coordenadores (isentos):</strong> <span class="ml-2">{{(int)$roteiro->qtd_coordenador}}</span></td>
                                        </tr>
                                        <tr>
                                            <td class="px-3 py-1" colspan="2"><strong>Observação:</strong><br> <span class="">"{{$roteiro->obs == '' ? '-' : $roteiro->obs}}"</span></td>
                                        </tr>
                                    </tbody>
                                </table>

                                <table class="table table-bordered table-sm">
                                    <tbody>
                                        <tr>
                                            <td class="px-3 py-1"><strong>Criado em:</strong> <span class="ml-2">{{$criado->format('d/m/Y H:i:s')}}</span></td>
                                            <td class="px-3 py-1"><strong>Atualizado em:</strong> <span class="ml-2">{{$atualizado->format('d/m/Y H:i:s')}}</span></td>
                                        </tr>
                                        <tr>
                                            <td class="px-3 py-2" colspan="2"><strong>Criado por:</strong> <span class="ml-2">{!!$criado_por!!}</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card rounded-0">
                            <div class="card-header card-collapse text-dark px-2 py-1">
                                VENDAS
                            </div>
                            <div class="card-body pt-3 pb-2 px-2">
                                <div class="row">
                                    <div class="col-12">
                                        <button type="button" class="btn btn-sm btn-info" onclick="$('.modal').modal('hide'); setTimeout(function(){$('#modalVerTarifas').modal('show');}, 200);"> Ver tarifas </button>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="modalTarifaRoteiro()">Alterar tarifas do roteiro</button>
                                    </div>
                                </div>
                                <hr>
                                @php
                                $estoque = $roteiro->estoque;
                                $estoque['vendidos_perc'] = round( ($estoque['vendidos']  * 100) / $estoque['total'], 2);
                                $estoque['reservados_perc'] = round( ($estoque['reservados'] * 100) / $estoque['total'], 2);
                                $estoque['livre_perc'] = round( ($estoque['livre'] * 100) / $estoque['total'], 2);
                                
                                //var_dump($estoque);
                                @endphp
                                <div class="row">
                                    <div class="col-12">
                                        <div class="d-flex flex-wrap mb-3">
                                            <div class="border bg-light shadow-sm p-2 mr-2">
                                                <strong>LEGENDA</strong>
                                                <ul class="list-group list-group-horizontal-lg">
                                                    <li class="list-group-item">
                                                        <i class="fas fa-circle text-primary"></i>
                                                        Poltronas vendidas
                                                        <span class="badge badge-primary badge-pill ml-2 py-1">{{$estoque['vendidos']}}</span>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <i class="fas fa-circle text-secondary"></i>
                                                        Poltronas reservadas
                                                        <span class="badge badge-secondary badge-pill ml-2 py-1">{{$estoque['reservados']}}</span>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <i class="fas fa-circle text-success"></i>
                                                        Poltronas vagas
                                                        <span class="badge badge-success badge-pill ml-2 py-1">{{$estoque['livre']}}</span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex" id="graficoVendas">
                                    
                                        <!-- Poltronas vendidas -->
                                        <div class="progress mr-1 rounded rounded-lg" title="Poltronas vendidas: {{$estoque['vendidos']}}" data-toggle="tooltip" style="height:10px; width:{{$estoque['vendidos_perc']}}%;">
                                            <div class="progress-bar" style="height:10px; width:100%;"></div>
                                        </div>
                                        <!-- Poltronas reservadas -->
                                        <div class="progress mr-1 rounded rounded-lg" title="Poltronas reservadas: {{$estoque['reservados']}}" data-toggle="tooltip" style="height:10px; width:{{$estoque['reservados_perc']}}%;">
                                            <div class="progress-bar bg-secondary" style="height:10px; width:100%;"></div>
                                        </div>
                                        <!-- Poltronas vagas -->
                                        <div class="progress rounded rounded-lg" title="Poltronas livres para venda: {{$estoque['livre']}}" data-toggle="tooltip" style="height:10px; width:{{$estoque['livre_perc']}}%;">
                                            <div class="progress-bar bg-success" style="height:10px; width:100%;"></div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="mt-3" style="overflow-x:auto;">
                                    <table class="table table-sm table-bordered small table-hover" id="tabelaVendas">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>Venda</th>
                                                <th>Cliente</th>
                                                <th>Pessoas</th>
                                                <th>Crianças</th>
                                                <th>Cri. Colo</th>
                                                <th>Situação</th>
                                                <th>Data Reserva/Venda</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                            @if(empty($lista_vendas))
                                            <tr>
                                                <td colspan="6" class="text-center py-2 font-italic">Sem vendas</td>
                                            </tr>
                                            @else
                                            @foreach($lista_vendas as $l)
                                            @php
                                                if($l->status == 'Reserva') {
                                                    $temp = new DateTime($l->data_reserva);
                                                    $bgtr = 'table-info';
                                                    $situ = '<span class="badge badge-info py-1 px-2">'.$l->status.'</span>';
                                                } else if($l->status == 'Devolvida') {
                                                    $temp = new DateTime($l->data_reserva);
                                                    $bgtr = '';
                                                    $situ = '<span class="badge badge-secondary py-1 px-2">'.$l->status.'</span>';
                                                } else {
                                                    $temp = new DateTime($l->data_venda);
                                                    $bgtr = '';
                                                    $situ = '<span class="badge badge-success py-1 px-2">'.$l->status.'</span> ('.$l->forma_pagamento.')';
                                                }

                                                //$receita = 0; // Soma de valores pagos até agora em todas as vendas. [TOTAL REAL]
                                                //$receita_esperada = 0; // Valor total esperado com todas as vendas. [PROJETADA]


                                                // Incrementa valor da receita.
                                                if($l->status == 'Devolvida') { // Contabiliza somente o valor que não foi devolvido.
                                                    $receita += ( (int)$l->total_pago - (int)$l->valor_devolvido );
                                                } else { // Contabiliza o valor pago.
                                                    $receita += (int)$l->total_pago; // VALOR REAL. Valor pago até o momento.
                                                    $receita_esperada += (int)$l->valor_total; // Receita espera (mas que ainda foi quitado totalmente);
                                                }
                                                //$receita += (int)$l->valor_total;
                                            @endphp

                                            @if($l->status != 'Devolvida' && $l->status != 'Cancelada')
                                            <tr class="{{$bgtr}}">
                                                <td><a href="javascript:void(0)" onclick="getVenda({{$l->id}})">{{$l->id}}</a></td>
                                                <td><a href="javascript:void(0)" onclick="loadCliente({{$l->cliente_id}})">{{$l->cliente_nome == NULL ? 'Cliente desconhecido' : $l->cliente_nome}}</a></td>
                                                <td>{{$l->clientes_total}}</td>
                                                <td>{{$l->criancas}}</td>
                                                <td>{{$l->criancas_colo}}</td>
                                                <td>{!!$situ!!}</td>
                                                <td>{{$temp->format('d/m/Y H:i:s')}}</td>
                                            </tr>
                                            @endif

                                            @endforeach
                                            @php
                                                unset($temp, $bgtr);
                                            @endphp
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-3" style="overflow-x:auto;">
                                    <div class="border bloco-acord">
                                        <div class="acord-header bg-light p-2 d-flex justify-content-between cursor-pointer">
                                            <h6 class="font-weight-bold text-uppercase my-1 text-primary">VENDAS COM CORTESIAS <span class="ml-2 badge badge-primary badge-pill">{{count($lista_cortesias)}}</span></h6>
                                        </div>
                                        <div class="acord-body p-2 py-3 pt-0 border border-secondary border-bottom-0 border-left-0 border-right-0" style="display:none">
                                            <div class="alert alert-info px-2 py-1 small">
                                                <i class="fas fa-info-circle fa-fw"></i> As cortesias aqui listadas são: <strong>1)</strong> Venda onde o valor total é igual R$0,00; <strong>2)</strong> Venda que tenha um item com valor em R$0,00.   
                                            </div>
                                            <table class="table table-sm table-bordered small table-hover" id="tabelaVendasCortesia">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th>Venda</th>
                                                        <th>Cliente</th>
                                                        <th>Pessoas</th>
                                                        <th>Crianças</th>
                                                        <th>Situação</th>
                                                        <th>Data Reserva/Venda</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                @if(empty($lista_cortesias))
                                                <tr>
                                                    <td colspan="6" class="text-center py-2 font-italic">Sem cortesias</td>
                                                </tr>
                                                @else
                                                @foreach($lista_cortesias as $l)
                                                
                                                @php
                                                $temp = new DateTime($l->data_reserva);
                                                @endphp
                                                <tr>
                                                    <td><a href="javascript:void(0)" onclick="getVenda({{$l->id}})">{{$l->id}}</a></td>
                                                    <td><a href="javascript:void(0)" onclick="loadCliente({{$l->cliente_id}})">{{$l->cliente_nome == NULL ? 'Cliente desconhecido' : $l->cliente_nome}}</a></td>
                                                    <td>{{$l->clientes_total}}</td>
                                                    <td>{{$l->criancas}}</td>
                                                    <td><span class="badge badge-secondary py-1 px-2">{{$l->status}}</span></td>
                                                    <td>{{$temp->format('d/m/Y H:i:s')}}</td>
                                                </tr>
                                                    @php
                                                    unset($temp);
                                                    @endphp
                                                @endforeach
                                                @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="border bloco-acord">
                                        <div class="acord-header bg-light p-2 d-flex justify-content-between cursor-pointer">
                                            <h6 class="font-weight-bold text-uppercase my-1">Canceladas/Estornadas</h6>
                                        </div>
                                        <div class="acord-body p-2 py-3 pt-0 border border-secondary border-bottom-0 border-left-0 border-right-0" style="display:none">
                                            <table class="table table-sm table-bordered small table-hover" id="tabelaVendasCanceladas">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th>Venda</th>
                                                        <th>Cliente</th>
                                                        <th>Pessoas</th>
                                                        <th>Crianças</th>
                                                        <th>Situação</th>
                                                        <th>Data Reserva/Venda</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                @if(empty($lista_vendas))
                                                <tr>
                                                    <td colspan="6" class="text-center py-2 font-italic">Sem vendas</td>
                                                </tr>
                                                @else
                                                @foreach($lista_vendas as $l)
                                                @if($l->status == 'Devolvida' || $l->status == 'Cancelada')
                                                    @php
                                                    $temp = new DateTime($l->data_reserva);
                                                    @endphp
                                                <tr>
                                                    <td><a href="javascript:void(0)" onclick="getVenda({{$l->id}})">{{$l->id}}</a></td>
                                                    <td><a href="javascript:void(0)" onclick="loadCliente({{$l->cliente_id}})">{{$l->cliente_nome == NULL ? 'Cliente desconhecido' : $l->cliente_nome}}</a></td>
                                                    <td>{{$l->clientes_total}}</td>
                                                    <td>{{$l->criancas}}</td>
                                                    <td><span class="badge badge-secondary py-1 px-2">{{$l->status}}</span></td>
                                                    <td>{{$temp->format('d/m/Y H:i:s')}}</td>
                                                </tr>
                                                    @php
                                                    unset($temp);
                                                    @endphp
                                                @endif
                                                @endforeach
                                                @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-lg-12">
                                <div class="card rounded-0">
                                    <div class="card-header card-collapse text-dark px-2 py-1">
                                        CLIENTES/PASSAGEIROS
                                    </div>
                                    <div class="card-body pt-3 pb-2 px-2" >
                                        <div class="row">
                                            <div class="col-12" id="passagDiv">
                                                
                                                <div class="text-center mx-2">
                                                    Carregando...<br>
                                                    <div class="spinner-grow text-primary"></div><br>
                                                    <small class="text-muted">[Se essa seção demorar a carregar, ela pode estar indisponível ou em desenvolvimento.]</small>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-12">
                                
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <!-- COLUNA 2 -->
                        <div class="card rounded-0">
                            <div class="card-header card-collapse text-dark px-2 py-1">
                                DESPESAS
                            </div>
                            <div class="card-body pt-3 pb-2 px-2" style="overflow-x:auto;">
                                <table class="table table-bordered table-sm">
                                    <thead class="thead-dark small">
                                        <tr>
                                            <th>Item</th>
                                            <th>Valor</th>
                                            <th>Operação</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @php
                                        $despesasTotal = 0;
                                        @endphp
                                        @foreach($roteiro->despesas as $d)
                                        <tr>
                                            <td>
                                            {{$d->tipo}} - {{$d->nome}}
                                            </td>
                                            <td>
                                            @switch($d->tipo_valor)
                                                @case('total')
                                                <strong>R$ {{$sgc->converteCentavoParaReal($d->valor)}}</strong>
                                                @php
                                                    $despesasTotal += $d->valor;
                                                @endphp
                                                @break

                                                @case('pessoa')
                                                R$ {{$sgc->converteCentavoParaReal($d->valor)}} (x {{((int)$roteiro->passagens + (int)$roteiro->qtd_coordenador )}} pessoas) =<br>
                                                <strong>R$ {{$sgc->converteCentavoParaReal( $d->valor * ((int)$roteiro->passagens + (int)$roteiro->qtd_coordenador ))}}</strong>
                                                @php
                                                    $despesasTotal += ( $d->valor * ((int)$roteiro->passagens + (int)$roteiro->qtd_coordenador ));
                                                @endphp
                                                @break

                                                @case('dia')
                                                R$ {{$sgc->converteCentavoParaReal($d->valor)}} (x {{$d->dias}} dias) =<br>
                                                <strong>R$ {{$sgc->converteCentavoParaReal( $d->valor * $d->dias )}}</strong>
                                                @php
                                                    $despesasTotal += ( $d->valor * $d->dias );
                                                @endphp
                                                @break

                                                @case('pessoa_dia')
                                                R$ {{$sgc->converteCentavoParaReal($d->valor)}} (x {{$d->dias}} dias)
                                                (x {{((int)$roteiro->passagens + (int)$roteiro->qtd_coordenador )}} pessoas) =<br>

                                                <strong>R$ {{$sgc->converteCentavoParaReal($d->valor * ($d->dias) * ((int)$roteiro->passagens + (int)$roteiro->qtd_coordenador ) )}}</strong>
                                                @php
                                                    $despesasTotal += ($d->valor * ($d->dias) * ((int)$roteiro->passagens + (int)$roteiro->qtd_coordenador ) );
                                                @endphp
                                                @break
                                            @endswitch
                                            </td>
                                            <td class="text-primary">+</td>
                                        </tr>
                                        @endforeach
                                        <tr>
                                            <td colspan="3"></td>
                                        </tr>
                                        <tr>
                                            <td>TOTAL</td>
                                            <td><strong>R$ {{$sgc->converteCentavoParaReal($despesasTotal)}}</strong></td>
                                            <td class="text-primary">=</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3"></td>
                                        </tr>
                                        <tr>
                                            <td>Valor Integral p/ cliente <br>(incluso lucro)</td>
                                            <td><strong>R$ {{$sgc->converteCentavoParaReal( ceil(($despesasTotal + $roteiro->lucro_previsto->lucroRateio) / $roteiro->qtd_rateio) )}}</strong></td>
                                            <td class="text-primary"></td>
                                        </tr>
                                    </tbody>
                                </table>

                                <table class="table table-bordered table-sm">
                                    <thead class="thead-dark small">
                                        <tr>
                                            <th colspan="3" class="text-center">LUCRO PREVISTO</th>
                                        </tr>
                                        <tr>
                                            <th>Detalhe</th>
                                            <th>Valor</th>
                                            <th>Operação</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        <tr>
                                            <td colspan="3">Poltronas para rateio: <strong>{{$roteiro->qtd_rateio}} clientes/poltronas.</strong></td>
                                        </tr>
                                        <tr>
                                            <td>Lucro do Rateio ({{$roteiro->qtd_rateio}} clientes)</td>
                                            <td class="text-success"><strong>R$ {{$sgc->converteCentavoParaReal( $roteiro->lucro_previsto->lucroRateio )}}</strong></td>
                                            <td class="text-success">+</td>
                                        </tr>
                                        <tr>
                                            <td>Lucro das Poltronas Livres ({{$roteiro->passagens - $roteiro->qtd_rateio}} poltronas)</td>
                                            <td class="text-success"><strong>R$ {{$sgc->converteCentavoParaReal( $roteiro->lucro_previsto->lucroPoltronaLivre )}}</strong></td>
                                            <td class="text-success">+</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3"></td>
                                        </tr>
                                        <tr>
                                            <td>Lucro Total</td>
                                            <td class="text-primary"><strong>R$ {{$sgc->converteCentavoParaReal( $roteiro->lucro_previsto->lucroTotal )}}</strong></td>
                                            <td class="text-primary">=</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card rounded-0">
                            <div class="card-header card-collapse text-dark px-2 py-1">
                                FATURAMENTO
                            </div>
                            <div class="card-body pt-3 pb-2 px-2" style="overflow-x:auto;">
                                @php
                                    $coberturaDespesa = round(($receita *100)/$despesasTotal, 2);
                                    $coberturaDiff = $receita - $despesasTotal;
                                    
                                    if($coberturaDiff < 0) {
                                        $bgCobertura = 'danger';
                                    } else {
                                        if($coberturaDespesa < 20) {
                                            $bgCobertura = 'danger';
                                        } else if($coberturaDespesa < 50) {
                                            $bgCobertura = 'warning';
                                        } else if($coberturaDespesa < 100) {
                                            $bgCobertura = 'primary';
                                        } else {
                                            $bgCobertura = 'success';
                                            $coberturaDespesa = 100;
                                        }
                                    }

                                    if($receita > $despesasTotal) {
                                        $lucroTotal = $receita - $despesasTotal;
                                    } else {
                                        $lucroTotal = 0;
                                    }
                                @endphp

                                <h6><strong>Cobertura das despesas:</strong>
                                <span class="badge badge-{{$bgCobertura}} py-1 px-2">R$ {{$coberturaDiff < 0 ? '-'.$sgc->converteCentavoParaReal($coberturaDiff*(-1)) : $sgc->converteCentavoParaReal($coberturaDiff)}}</span></h6>
                                <div class="progress mb-3" style="height:10px;">
                                    <div class="progress-bar bg-{{$bgCobertura}} progress-bar-striped progress-bar-animated"
                                        title="Valor faturado até o momento: R$ {{$sgc->converteCentavoParaReal($receita)}}."
                                        data-toggle="tooltip" style="width:{{$coberturaDespesa}}%; height: 10px;"></div>
                                </div>
                                <table class="table table-bordered table-sm">
                                    <thead class="thead-dark small">
                                        <tr>
                                            <th colspan="3" class="text-center">DETALHES</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        <tr>
                                            <td><strong>Despesas:</strong> R$ {{$sgc->converteCentavoParaReal($despesasTotal)}}</td>
                                            <td><strong>Lucro real:</strong> R$ {{$sgc->converteCentavoParaReal($lucroTotal)}}</td>
                                            
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <strong>Receita (REAL):</strong>
                                                R$ {{$sgc->converteCentavoParaReal($receita)}}<br>
                                                <span class="badge badge-pill badge-info mx-2" data-toggle="popover" title="Como é calculado?" data-trigger="hover" data-content="A <b>Receita (REAL)</b> corresponde aos valores das vendas 
                                                que já entrou em caixa. Por exemplo, <br>
                                                <ul> <li><strong>Reservas e Aguardando pagamento:</strong> valor ainda não foi pago. Não é contado como Receita (REAL) até a dívida do cliente ser quitada.</li>
                                                <li><strong>Pagando ou Em Pagamento:</strong> Somente o valor pago até o momento é calculado na Receita (REAL). O valor total dessa venda é contabilizado na Receita (PROJETADA).</li> 
                                                <li><strong>Paga:</strong> O valor total dessa venda é contabilizada.</li>
                                                <li><strong>Cancelada:</strong> Essa venda não é contabilizada, pois nenhum valor foi pago pelo cliente. </li> 
                                                <li><strong>Devolvida:</strong> A diferença entre o TOTAL PAGO (pelo cliente) e o VALOR DEVOLVIDO (para o cliente) é calculado na Receita (REAL), visto que se trata de um faturamento.
                                                Esta venda é desconsiderada na Receita (PROJETADA).</li> </ul>">
                                                    <i class="fas fa-question-circle"></i>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <strong>Receita (PROJETADA):</strong>
                                                R$ {{$sgc->converteCentavoParaReal($receita_esperada)}}<br>
                                                <span class="badge badge-pill badge-info mx-2" data-toggle="popover" title="Como é calculado?" data-trigger="hover" data-content="A <b>Receita (PROJETADA)</b> corresponde aos valores TOTAIS
                                                das vendas realizadas, mas que ainda não entraram em caixa. Por exemplo, <br>
                                                <ul> <li><strong>Reservas e Aguardando pagamento:</strong> valor ainda não foi pago, mas o valor já é contabilizado como Receita (PROJETADA), somente aguardando
                                                a dívida do cliente ser quitada.</li>
                                                <li><strong>Pagando (ou Em Pagamento) e Paga:</strong> O valor total dessa venda é contabilizado na Receita (PROJETADA), mesmo se a dívida ainda estiver sendo quitada.</li>
                                                <li><strong>Cancelada:</strong> Essa venda não é contabilizada, pois nenhum valor foi pago pelo cliente. </li> 
                                                <li><strong>Devolvida:</strong> Esta venda é desconsiderada na Receita (PROJETADA).</li> </ul>
                                                <br> <b>OBS.: <i>Se o valor da Receita (REAL) superar a Receita (PROJETADA), o motivo são as vendas DEVOLVIDAS/ESTORNADAS, que não foram devolvidas integralmente. </i></b>">
                                                    <i class="fas fa-question-circle"></i>
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <small class="mt-3">Atualizado em: <i>{{$hoje->format('d/m/Y H:i:s')}}</i></small></small>
                            </div>
                        </div>

                        <div class="card rounded-0">
                            <div class="card-header card-collapse text-dark px-2 py-1">
                                PARCEIROS DESTE ROTEIRO
                            </div>
                            <div class="card-body pt-3 pb-2 px-2">
                                @if(empty($roteiro->parceiros))
                                    <i>Não há parceiros neste roteiro...</i>
                                @else
                                    @php
                                    $conta = 0;
                                    @endphp
                                    @foreach($roteiro->parceiros as $p)
                                    <div class="border bloco-acord">
                                        <div class="acord-header bg-light p-2 d-flex justify-content-between" style="cursor:pointer;">
                                            <h6 class="font-weight-bold text-uppercase my-1 text-primary">
                                                {{$p->nome_fantasia == '' ? $p->razao_social : $p->nome_fantasia}}
                                                <small class="ml-2 text-dark">{{$p->nome_fantasia == '' ? '' : '('. $p->razao_social .')'}}</small>
                                            </h6>
                                            <button class="btn btn-transparent btn-sm text-dark"><i class="fas fa-angle-down"></i></button>
                                        </div>
                                        <div class="acord-body p-2 py-3 pt-0 border border-secondary border-bottom-0 border-left-0 border-right-0" style="display:none;">
                                            <table class="table table-sm table-bordered">
                                                <tr>
                                                    <td colspan="2"><strong>Razão Social/Nome Completo:</strong> <br><span class="ml-2">{{$p->razao_social}}</span></td>
                                                </tr>
                                                @if($p->nome_fantasia != '')
                                                <tr>
                                                    <td colspan="2"><strong>Nome Fantasia:</strong> <br><span class="ml-2">{{$p->nome_fantasia}}</span></td>
                                                </tr>
                                                @endif
                                                <tr>
                                                    <td><strong>{{$p->doc_tipo}}</strong></td>
                                                    <td>
                                                    @switch($p->doc_tipo)
                                                        @case('CNPJ')
                                                        {{substr($p->doc_numero,0,2). '.' .substr($p->doc_numero,2,1). '**.***/****-'. substr($p->doc_numero,-2,2)}}
                                                        @break

                                                        @case('CPF')
                                                        {{substr($p->doc_numero,0,3). '.***.***-**'}}
                                                        @break
                                                    @endswitch
                                                    </td>
                                                </tr>
                                                <tr>
                                                <td colspan="2"><strong>Cidade/Estado:</strong> <br><span class="ml-2">{{$p->cidade .'/'. $p->estado}}</span></td>
                                                </tr>
                                            </table>

                                            <div class="border border-secondary mb-3 p-2">
                                                <h5 class="mb-3 pb-2 font-weight-bold border border-top-0 border-left-0 border-right-0 d-flex justify-content-between">
                                                    <span><i class="far fa-folder-open mr-2"></i> HISTÓRICO DE NEGOCIAÇÕES</span>
                                                    <span><button type="button" class="toggleMinMax btn-light rounded-0 border" data-target="#historico_{{$conta}}"><i class="fas fa-minus"></i></button></span>
                                                </h5>
                                                <div class="row" id="historico_{{$conta}}">
                                                    <div class="col-12 mb-2">
                                                        <div class="border py-2 px-2 mb-3">
                                                            <button type="button" class="py-0 btn btn-sm btn-primary" onclick="$('#modalParceiroHistoricoAdd').modal('show'); $('#modalParceiroHistoricoAdd').find('[name=\'parcid\']').val({{$p->id}})">
                                                                <i class="fas fa-plus"></i> Adicionar entrada no registro
                                                            </button>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-12">
                                                        <table class="table table-sm table-bordered table-responsive-md">
                                                            <thead class="thead-dark small text-uppercase">
                                                                <tr>
                                                                    <th style="width:7em;">Data</th>
                                                                    <th>Etapa</th>
                                                                    <th>Detalhes</th>
                                                                    <th style="width: 4em;"></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="small">
                                                                @if(empty($p->historico))
                                                                    <tr><td colspan="4" class="text-center py-2">Nenhum registro no histórico.</td></tr>
                                                                @else
                                                                @foreach($p->historico as $h)

                                                                @php
                                                                    $data = new DateTime($h->criado_em);
                                                                    if($h->data_ini != null) {
                                                                        $data_ini = new DateTime($h->data_ini);
                                                                    }

                                                                    if($h->atualizado_em != null) {
                                                                        $atualizado = new DateTime($h->atualizado_em);
                                                                    } else {
                                                                        $atualizado = null;
                                                                    }
                                                                @endphp
                                                                <tr data-detalhes="{{$h->detalhes}}" data-etapa="{{$h->etapa}}">
                                                                    <td rowspan="2" class="font-monospace ">{{$data->format('d/m/Y')}}<br>{{$data->format('H:i:s')}}</td>
                                                                    <td class="font-weight-bold">{{$h->etapa}}</td>
                                                                    <td>
                                                                        {{$h->detalhes}}
                                                                    </td>
                                                                    <td rowspan="2">
                                                                        
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="2">
                                                                        <strong>AUTOR:</strong>
                                                                        @if($h->usuario_nome == null)
                                                                            <i>Usuário removido</i>
                                                                        @else
                                                                            {{$h->usuario_nome}}
                                                                        @endif
                                                                        @if($h->roteiro_nome != null)
                                                                        &nbsp;|&nbsp;
                                                                        <strong>ROTEIRO:</strong> {{$h->roteiro_nome}} {{"(".$data_ini->format('d/m/Y').")"}}
                                                                        @elseif($h->roteiro_nome == null && $h->roteiro_id > 0)
                                                                        &nbsp;|&nbsp;
                                                                        <strong>ROTEIRO:</strong> <i>Roteiro removido.</i>
                                                                        @endif

                                                                        @if($h->atualizado_por != null && $h->atualizado_em != null)
                                                                            <br><strong>[Atualizado por: {{$h->atualizado_por_nome}} em {{$atualizado->format('d/m/Y H:i:s')}}]</strong>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                                @endforeach
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        
                                        </div>
                                    </div>

                                    @php
                                    $conta++;
                                    @endphp
                                    @endforeach
                                        
                                @endif
                            </div>
                        </div>

                        <div class="card rounded-0">
                            <div class="card-header card-collapse text-dark px-2 py-1">
                                COORDENADORES
                            </div>
                            <div class="card-body pt-3 pb-2 px-2" style="overflow-x:auto;">
                                <div class="row">
                                    <div class="col-12">
                                        <button type="button" class="btn btn-sm btn-primary mr-2" onclick="janCoordenadorSelect(this)" data-id="{{$roteiro->id}}">Adicionar coordenador</button>
                                        <div id="listaCoord" class="mt-3">
                                            <ul class="list-group">
                                            @foreach($roteiro->coordenador as $coord)
                                                <li class="list-group-item d-flex justify-content-between align-items-center py-2 pl-3 pr-2">
                                                <a href="javascript:void(0)" onclick="loadCoordenador({{$coord['id']}})">{{$coord['nome']}}</a>
                                                <button type="button" class="btn btn-sm btn-light" data-id="{{$coord['id']}}" data-rid="{{$roteiro->id}}" onclick="roteiroRemoveCoordenador(this)"><i class="fas fa-times fa-fw"></i></button>
                                                </li>
                                            @endforeach
                                            </ul>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card rounded-0">
                            <div class="card-header card-collapse text-dark px-2 py-1">
                                LISTAS
                            </div>
                            <div class="card-body pt-3 pb-2 px-2" style="overflow-x:auto;">
                                <div class="row">
                                    <div class="col-12">
                                        <button type="button" class="btn btn-sm btn-primary mr-2 mb-1" onclick="janNovaListaTransporte(this)" data-id="{{$roteiro->id}}">Nova lista de transporte</button>
                                        <button type="button" class="btn btn-sm btn-primary mr-2 mb-1" onclick="janNovaListaHospede(this)" data-id="{{$roteiro->id}}">Nova lista de hóspedes</button>
                                        <div id="listaListas" class="mt-3 d-flex flex-column">
                                            
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

<!-- MODAL -->
<div class="modal fade" id="modalParceiroHistoricoAdd">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-2 px-3 font-weight-bold">
                Nova Entrada Registro de Negociações
                <button type="button" class="btn btn-sm btn-danger fechar" data-dismiss="modal"><strong>&times;</strong></button>
            </div>
            <form onsubmit="return false;">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Etapa da negociação</label>
                        <select class="form-control form-control-sm form-control-solid" name="etapa">
                            <option value="CONTATO">CONTATO</option>
                            <option value="PEDIDO BLOQUEIO">PEDIDO BLOQUEIO</option>
                            <option value="PAGAMENTO">PAGAMENTO</option>
                            <option value="DESISTÊNCIA">DESISTÊNCIA</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Detalhes</label>
                        <textarea rows="3" class="form-control form-control-sm form-control-solid" name="detalhes" maxlength="300"></textarea>
                        <input type="hidden" name="parcid" value="">
                        <input type="hidden" name="roteiroid" value="{{$roteiro->id}}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-success" onclick="roteiroHistoricoNovo(this)">Salvar</button>
                    <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Fechar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalRoteiroTarifas">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-2 px-3 font-weight-bold">
                Alterar Tarifas do Roteiro
                <button type="button" class="btn btn-sm btn-danger fechar" data-dismiss="modal"><strong>&times;</strong></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        Valor Integral mínimo p/ cliente: <strong>R$ {{$sgc->converteCentavoParaReal( ceil(($despesasTotal + $roteiro->lucro_previsto->lucroRateio) / $roteiro->qtd_rateio) )}}</strong>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-12 mb-3">
                        <button type="button" class="btn btn-sm btn-info px-3" data-target="#ajudaModalRoteiroTarifas" data-toggle="collapse">Ajuda <i class="fas fa-question-circle"></i></button>
                        <div id="ajudaModalRoteiroTarifas" class="collapse border px-2 py-1 border-info bg-light">
                            <strong>O que cada campo da tabela significa?</strong><br>
                            <ul>
                                <li><strong>Nome da tarifa:</strong> Especifique o nome da tarifa que irá aparecer na página de <a href="#vendas/novo" target="_blank">Vendas > Novo</a>. Não repita nomes;</li>
                                <li><strong>Valor:</strong> Valor dessa tarifa. Os valores, é você quem define. Eles irão aparecer na página de <a href="#vendas/novo" target="_blank">Vendas > Novo</a>.</li>
                                <li><strong>Quantidade de Clientes:</strong> Em cada tarifa, quantos clientes estão cobertos por ela. Veja exemplos: <br><br><b>Ex. 1:</b> Espera-se que uma tarifa "Meia"
                                seja só preenchida por CRIANÇAS e não ADULTOS, por isso preencha com 1 Criança e 0 Adultos;
                                <br><b>Ex. 2:</b> Uma tarifa "Pacote Família" talvez inclua 2 ADULTOS (os pais) e 1 CRIANÇA, por isso preencha com 1 Criança e 2 Adultos;
                                <br><b>Ex. 3:</b> Espera-se que uma tarifa "CASADINHA" seja preenchida por 2 ADULTOS, por isso preencha com 0 Crianças e 2 Adultos.</li>
                            </ul>
                            <div class="alert alert-info small">
                                <b class="font-weight-bold">OBSERVAÇÃO: </b><br>
                                A plataforma entende que CRIANÇA (0-5 ANOS e 6-12 ANOS) ou IDOSO (60+) possuem tarifação diferenciada (desconto). Por isso, NENHUM ADULTO pode ocupar a vaga deles;<br>
                                No entanto, uma CRIANÇA ou IDOSO (60+) pode ocupar a vaga de um ADULTO (pagar o mesmo valor de um ADULTO e ocupar a VAGA de um ADULTO na tarifa).
                            </div>
                            Ex.:
                            <table class="table table-bordered table-sm">
                                <tbody>
                                    <tr>
                                        <td>
                                            <label class="small">Nome da tarifa</label><br>
                                            Integral
                                        </td>
                                        <td>
                                            <label class="small">Valor</label><br>
                                            R$ 250,00
                                        </td>
                                        <td>
                                            <label class="small">Quantidade de Clientes</label><br>
                                            ADULTOS: 1<br>
                                            CRIANÇAS: 0
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label class="small">Nome da tarifa</label><br>
                                            Meia
                                        </td>
                                        <td>
                                            <label class="small">Valor</label><br>
                                            R$ 125,00
                                        </td>
                                        <td>
                                            <label class="small">Quantidade de Clientes</label><br>
                                            ADULTOS: 0<br>
                                            CRIANÇAS: 1
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label class="small">Nome da tarifa</label><br>
                                            Pacote Família (tri)
                                        </td>
                                        <td>
                                            <label class="small">Valor</label><br>
                                            R$ 350,00
                                        </td>
                                        <td>
                                            <label class="small">Quantidade de Clientes</label><br>
                                            ADULTOS: 2<br>
                                            CRIANÇAS: 1
                                        </td>
                                    </tr>
                                </tbody>
                            </table><br>
                            <strong>Problemas</strong>
                            <ul>
                                <li>Se você não definir nenhuma quantidade de clientes, depois de efetuar a venda e lançar o passageiro na lista, <strong>a plataforma vai recusar a ação</strong> porque
                                a tarifa foi configurada incorretamente. A plataforma vai receber a informação de que AQUELA TARIFA tem permissão de adicionar <b>0</b> ADULTOS e <b>0</b> CRIANÇAS ou seja, ninguém.<br><br>
                                Não há como corrigir a venda, por isso 1) ela deverá ser <strong>CANCELADA/ESTORNADA</strong>, 2) a correção na tarifa
                                deve ser feita e 3) uma nova venda correspondente terá que ser criada.</li>
                                <li>Se você pretende remover ou suspender uma tarifa depois de um tempo, edite as tarifas e a remova. As vendas anteriores com aquela tarifa não serão afetadas, SOMENTE
                                as novas vendas que não terão mais essa tarifa listada.</li>
                            </ul><br>
                            <a href="javascript:void(0)" data-target="#ajudaModalRoteiroTarifas" data-toggle="collapse"><i class="fas fa-angle-up mr-2"></i> Fechar ajuda</a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <form method="post">
                            <table class="table table-bordered table-sm table-hover table-condensed">
                                <tbody>
                                    <tr style="display:none;" data-example>
                                        <td>
                                            <label class="small">Nome da tarifa</label>
                                            <input type="text" class="form-control form-control-sm form-control-solid" name="nome_tarifa" maxlength="15" placeholder="Ex.: CASADINHA">
                                        </td>
                                        <td>
                                            <label class="small">Valor</label>
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text form-control-solid">R$</span>
                                                </div>
                                                <input type="text" class="form-control form-control-sm form-control-solid" name="valor" placeholder="1234,99" dobbin-validate-valor>
                                                
                                            </div>
                                        </td>
                                        <td>
                                            <label class="small">Quantidade de Clientes</label>
                                            <div class="input-group input-group-sm mb-1">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text form-control-solid">ADULTOS</span>
                                                </div>
                                                <input type="number" class="form-control form-control-sm form-control-solid" name="qtd_adultos" value="0" min="0" max="9" >
                                                <div class="invalid-feedback">Só permitido valores entre 0 e 9. </div>
                                            </div>
                                            
                                            <div class="input-group input-group-sm mb-1">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text form-control-solid">CRIANÇAS</span>
                                                </div>
                                                <input type="number" class="form-control form-control-sm form-control-solid" name="qtd_criancas" value="0" min="0" max="9" >
                                                <div class="invalid-feedback">Só permitido valores entre 0 e 9. </div>
                                            </div>

                                            <div class="input-group input-group-sm mb-1">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text form-control-solid">CRIANÇAS DE COLO</span>
                                                </div>
                                                <input type="number" class="form-control form-control-sm form-control-solid" name="qtd_colo" value="0" min="0" max="9" >
                                                <div class="invalid-feedback">Só permitido valores entre 0 e 9. </div>
                                            </div>
                                        </td>
                                        <td style="vertical-align:middle;">
                                            <button type="button" class="btn btn-block btn-sm btn-danger" onclick="$(this).parents('tr').remove();"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                    @if($roteiro->tarifa == '')
                                    <tr>
                                        <td>
                                            <label class="small">Nome da tarifa</label>
                                            <input type="text" class="form-control form-control-sm form-control-solid" name="nome_tarifa" maxlength="15" value="Integral" placeholder="Ex.: CASADINHA" required>
                                        </td>
                                        <td>
                                            <label class="small">Valor</label>
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text form-control-solid">R$</span>
                                                </div>
                                                <input type="text" class="form-control form-control-sm form-control-solid" name="valor" placeholder="1234,99" value="{{$sgc->converteCentavoParaReal( ceil(($despesasTotal + $roteiro->lucro_previsto->lucroRateio) / $roteiro->qtd_rateio) )}}" dobbin-validate-valor required>
                                                
                                            </div>
                                        </td>
                                        <td>
                                            <label class="small">Quantidade de Clientes</label>
                                            <div class="input-group input-group-sm mb-1">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text form-control-solid">ADULTOS</span>
                                                </div>
                                                <input type="number" class="form-control form-control-sm form-control-solid" name="qtd_adultos" value="1" min="0" max="9" required>
                                                <div class="invalid-feedback">Só permitido valores entre 0 e 9. </div>
                                            </div>
                                            
                                            <div class="input-group input-group-sm mb-1">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text form-control-solid">CRIANÇAS</span>
                                                </div>
                                                <input type="number" class="form-control form-control-sm form-control-solid" name="qtd_criancas" value="0" min="0" max="9" required>
                                                <div class="invalid-feedback">Só permitido valores entre 0 e 9. </div>
                                            </div>

                                            <div class="input-group input-group-sm mb-1">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text form-control-solid">CRIANÇAS DE COLO</span>
                                                </div>
                                                <input type="number" class="form-control form-control-sm form-control-solid" name="qtd_colo" value="0" min="0" max="9" >
                                                <div class="invalid-feedback">Só permitido valores entre 0 e 9. </div>
                                            </div>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label class="small">Nome da tarifa</label>
                                            <input type="text" class="form-control form-control-sm form-control-solid" name="nome_tarifa" maxlength="15" value="Criança de Colo" placeholder="Ex.: CASADINHA" required>
                                        </td>
                                        <td>
                                            <label class="small">Valor</label>
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text form-control-solid">R$</span>
                                                </div>
                                                <input type="text" class="form-control form-control-sm form-control-solid" name="valor" placeholder="1234,99" value="{{$sgc->converteCentavoParaReal( ceil(($despesasTotal + $roteiro->lucro_previsto->lucroRateio) / $roteiro->qtd_rateio) )}}" dobbin-validate-valor required>
                                                
                                            </div>
                                        </td>
                                        <td>
                                            <label class="small">Quantidade de Clientes</label>
                                            <div class="input-group input-group-sm mb-1">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text form-control-solid">ADULTOS</span>
                                                </div>
                                                <input type="number" class="form-control form-control-sm form-control-solid" name="qtd_adultos" value="0" min="0" max="9" required>
                                                <div class="invalid-feedback">Só permitido valores entre 0 e 9. </div>
                                            </div>
                                            
                                            <div class="input-group input-group-sm mb-1">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text form-control-solid">CRIANÇAS</span>
                                                </div>
                                                <input type="number" class="form-control form-control-sm form-control-solid" name="qtd_criancas" value="0" min="0" max="9" required>
                                                <div class="invalid-feedback">Só permitido valores entre 0 e 9. </div>
                                            </div>

                                            <div class="input-group input-group-sm mb-1">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text form-control-solid">CRIANÇAS DE COLO</span>
                                                </div>
                                                <input type="number" class="form-control form-control-sm form-control-solid" name="qtd_colo" value="1" min="0" max="9" >
                                                <div class="invalid-feedback">Só permitido valores entre 0 e 9. </div>
                                            </div>
                                        </td>
                                        <td style="vertical-align:middle;">
                                            <button type="button" class="btn btn-block btn-sm btn-danger" onclick="$(this).parents('tr').remove();"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                    @else
                                    @foreach($roteiro->tarifa as $t)
                                    <tr>
                                        <td>
                                            <label class="small">Nome da tarifa</label>
                                            <input type="text" class="form-control form-control-sm form-control-solid" name="nome_tarifa" maxlength="15" value="{{$t->nome}}" placeholder="Ex.: CASADINHA" required>
                                        </td>
                                        <td>
                                            <label class="small">Valor</label>
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text form-control-solid">R$</span>
                                                </div>
                                                <input type="text" class="form-control form-control-sm form-control-solid" name="valor" value="{{$sgc->converteCentavoParaReal($t->valor)}}"  placeholder="1234,99" dobbin-validate-valor required>
                                                
                                            </div>
                                        </td>
                                        <td>
                                            <label class="small">Quantidade de Clientes</label>
                                            <div class="input-group input-group-sm mb-1">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text form-control-solid">ADULTOS</span>
                                                </div>
                                                <input type="number" class="form-control form-control-sm form-control-solid" name="qtd_adultos" value="{{$t->distr->adultos}}" min="0" max="9" required>
                                                <div class="invalid-feedback">Só permitido valores entre 0 e 9. </div>
                                            </div>
                                            
                                            <div class="input-group input-group-sm mb-1">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text form-control-solid">CRIANÇAS</span>
                                                </div>
                                                <input type="number" class="form-control form-control-sm form-control-solid" name="qtd_criancas" value="{{$t->distr->criancas}}" min="0" max="9" required>
                                                <div class="invalid-feedback">Só permitido valores entre 0 e 9. </div>
                                            </div>

                                            <div class="input-group input-group-sm mb-1">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text form-control-solid">CRIANÇAS DE COLO</span>
                                                </div>
                                                <input type="number" class="form-control form-control-sm form-control-solid" name="qtd_colo" value="{{isset($t->distr->colo) ? $t->distr->colo : '0'}}" min="0" max="9" >
                                                <div class="invalid-feedback">Só permitido valores entre 0 e 9. </div>
                                            </div>
                                        </td>
                                        <td style="vertical-align:middle;">
                                            <button type="button" class="btn btn-block btn-sm btn-danger" onclick="$(this).parents('tr').remove();"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-sm btn-primary" onclick="addTarifaRoteiro(this)">Adicionar tarifa</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-sm" onclick="salvarTarifasRoteiro(this)">Salvar</button>
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalVerTarifas">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-2 px-3 font-weight-bold">
                Tarifas deste roteiro
                <button type="button" class="btn btn-sm btn-danger fechar" data-dismiss="modal"><strong>&times;</strong></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                    <table class="table table-bordered table-sm">
                        <thead class="thead-dark">
                            <tr class="small">
                                <th>Nome da tarifa</th>
                                <th>Valor</th>
                                <th>Quantidade de clientes</th>
                            </tr>
                        </thead>
                        <tbody>
                        @if($roteiro->tarifa == '')
                            <tr colspan="3"><td class="py-2 text-center font-italic">Sem tarifas definidas</td></tr>
                        @else
                        @foreach($roteiro->tarifa as $t)
                            <tr>
                                <td>{{$t->nome}}</td>
                                <td>{{'R$ '.$sgc->converteCentavoParaReal($t->valor)}}</td>
                                <td>ADULTOS: 
                                @if($t->distr->adultos > 0)
                                    <span class="badge badge-primary px-2 py-1">{{$t->distr->adultos}}</span>
                                @else
                                    <span class="badge badge-secondary px-2 py-1">{{$t->distr->adultos}}</span>
                                @endif
                                <br>
                                CRIANÇAS: 
                                @if($t->distr->criancas > 0)
                                    <span class="badge badge-primary px-2 py-1">{{$t->distr->criancas}}</span>
                                @else
                                    <span class="badge badge-secondary px-2 py-1">{{$t->distr->criancas}}</span>
                                @endif
                                <br>
                                CRIANÇA DE COLO: 
                                @if(isset($t->distr->colo))
                                    @if($t->distr->colo > 0)
                                    <span class="badge badge-primary px-2 py-1">{{$t->distr->colo}}</span>
                                    @else
                                    <span class="badge badge-secondary px-2 py-1">{{$t->distr->colo}}</span>
                                    @endif
                                @else
                                <span class="badge badge-secondary px-2 py-1">0</span>
                                @endif
                                </td>
                            </tr>
                        @endforeach
                        @endif
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-primary" onclick="modalTarifaRoteiro()">Alterar tarifas do roteiro</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="janCriarCopiaRoteiro">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-2 px-3 font-weight-bold">
                Criar cópia deste roteiro
                <button type="button" class="btn btn-sm btn-danger fechar" data-dismiss="modal"><strong>&times;</strong></button>
            </div>
            <div class="modal-body">
                <form method="post">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label>Data de Partida</label>
                                <input type="date" class="form-control form-control-sm form-control-solid" name="data_ini" value="{{$roteiro->data_ini}}" required>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label>Data de Retorno</label>
                                <input type="date" class="form-control form-control-sm form-control-solid" name="data_fim" value="{{$roteiro->data_fim}}" required>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="rid" value="{{$roteiro->id}}">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-sm" onclick="roteiroCriarCopia(this)">Criar cópia</button>
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="janNovaListaHospede">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-2 px-3 font-weight-bold">
                Nova Lista de Hóspedes
                <button type="button" class="btn btn-sm btn-danger fechar" data-dismiss="modal"><strong>&times;</strong></button>
            </div>
            <div class="modal-body">
                <form action="" method="post" data-aftersubmit="ListaHospedesEditar">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>Nome da Lista</label>
                                <input type="text" name="nome" class="form-control form-control-sm emptyAfterSubmit" maxlength="40">
                                <input type="hidden" name="roteiro_id" value="">
                                <input type="hidden" name="tipo" value="hospedagem">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <h5>Quartos disponíveis</h5>
                            <hr>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label>Quartos individuais</label>
                                <input type="number" name="individual" class="form-control form-control-sm emptyAfterSubmit" value="0">
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label>Quartos duplos</label>
                                <input type="number" name="duplo" class="form-control form-control-sm emptyAfterSubmit" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label>Quartos triplos</label>
                                <input type="number" name="triplo" class="form-control form-control-sm emptyAfterSubmit" value="0">
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label>Quartos quádruplos</label>
                                <input type="number" name="quadruplo" class="form-control form-control-sm emptyAfterSubmit" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label>Quartos quíntuplos</label>
                                <input type="number" name="quintuplo" class="form-control form-control-sm emptyAfterSubmit" value="0">
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label>Quartos séxtuplos</label>
                                <input type="number" name="sextuplo" class="form-control form-control-sm emptyAfterSubmit" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input" name="criancaColoIndividual" value="yes">Criança de colo como cliente individual.
                                    <i class="fas fa-question-circle fa-fw"title="O que significa?" data-toggle="popover" data-trigger="hover"
                                    data-content="Define o comportamento da <b>criança de colo</b> na lista. Inicialmente a criança de colo não é incluída na lista, pois 
                                    está implícito que ela estará com um dos responsáveis. Abaixo segue informação sobre as configurações:<br><br>

                                    <ul>
                                        <li><b>MARCADA</b>: a criança de colo nesta lista será considerada um passageiro/cliente individual, e será mostrada
                                        na lista de clientes com a marcação (<span class='text-primary font-weight-bold'>*</span>) como diferencial dos outros clientes. Você precisará
                                        alocar manualmente a criança na mesma viagem/quarto do responsável. <b>Ela poderá ocupar poltrona ou cama.</b></li>
                                        <li><b>DESMARCADA</b>: a criança de colo não será exibida na lista. Ao gerar a lista a criança de colo será lançada junto com um dos responsáveis.
                                        Nessa configuração <b>a criança de colo não poderá ocupar poltrona ou cama, pois estará no COLO</b>.</li>
                                    </ul>
                                    
                                    "></i>
                                </label>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-12 text-right">
                            <button type="submit" class="btn btn-success">Salvar lista</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="janListaHospede" data-lid="">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-2 px-3 font-weight-bold">
                <span></span>
                <button type="button" class="btn btn-sm btn-danger fechar" data-dismiss="modal"><strong>&times;</strong></button>
            </div>
            <div class="modal-body">
                    <div class="card shadow-sm">
                        <div class="card-header card-collapse font-weight-bold p-2">
                            CONFIGURAÇÕES DA LISTA
                        </div>
                        <div class="card-body p-2" data-config style="display:none;">
                            <form method="post" data-aftersubmit="ListaHospedesEditarRefresh">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>Nome da Lista</label>
                                            <input type="text" name="nome" class="form-control form-control-sm emptyAfterSubmit" maxlength="40" value="">
                                            <input type="hidden" name="roteiro_id" value="">
                                            <input type="hidden" name="id" value="">
                                            <input type="hidden" name="tipo" value="hospedagem">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <h5>Quartos disponíveis</h5>
                                        <hr>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label>Quartos individuais</label>
                                            <input type="number" name="individual" class="form-control form-control-sm emptyAfterSubmit" value="0">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label>Quartos duplos</label>
                                            <input type="number" name="duplo" class="form-control form-control-sm emptyAfterSubmit" value="0">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label>Quartos triplos</label>
                                            <input type="number" name="triplo" class="form-control form-control-sm emptyAfterSubmit" value="0">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label>Quartos quádruplos</label>
                                            <input type="number" name="quadruplo" class="form-control form-control-sm emptyAfterSubmit" value="0">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label>Quartos quíntuplos</label>
                                            <input type="number" name="quintuplo" class="form-control form-control-sm emptyAfterSubmit" value="0">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label>Quartos séxtuplos</label>
                                            <input type="number" name="sextuplo" class="form-control form-control-sm emptyAfterSubmit" value="0">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-check">
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input" name="criancaColoIndividual" value="yes">Criança de colo como cliente individual.
                                                <i class="fas fa-question-circle fa-fw"title="O que significa?" data-toggle="popover" data-trigger="hover"
                                                data-content="Define o comportamento da <b>criança de colo</b> na lista. Inicialmente a criança de colo não é incluída na lista, pois 
                                                está implícito que ela estará com um dos responsáveis. Abaixo segue informação sobre as configurações:<br><br>

                                                <ul>
                                                    <li><b>MARCADA</b>: a criança de colo nesta lista será considerada um passageiro/cliente individual, e será mostrada
                                                    na lista de clientes com a marcação (<span class='text-primary font-weight-bold'>*</span>) como diferencial dos outros clientes. Você precisará
                                                    alocar manualmente a criança na mesma viagem/quarto do responsável. <b>Ela poderá ocupar poltrona ou cama.</b></li>
                                                    <li><b>DESMARCADA</b>: a criança de colo não será exibida na lista. Ao gerar a lista a criança de colo será lançada junto com um dos responsáveis.
                                                    Nessa configuração <b>a criança de colo não poderá ocupar poltrona ou cama, pois estará no COLO</b>.</li>
                                                </ul>
                                                
                                                "></i>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 text-right">
                                        <button type="submit" class="btn btn-success btn-sm px-2">Salvar</button>
                                    </div>
                                </div>
                            </div>
                            
                        </form>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12 col-md-4"><!-- COLUNA 1 -->
                            <h5 class="font-weight-bold">CLIENTES
                            <i class="fas fa-question-circle" title="O que é?" data-toggle="popover" data-trigger="hover" data-content="Lista de passageiros, incluindo coordenadores."></i></h5>
                            <div class="border p-2" data-clientesbox style="min-height: 60vh; border-width: 3px!important;"></div>
                        </div>
                        <div class="col-12 col-md-8"><!-- COLUNA 2 -->
                            <h5 class="font-weight-bold">HOSPEDAGEM
                            <i class="fas fa-question-circle" title="O que é?" data-toggle="popover" data-trigger="hover" data-content="Os quartos definidos para esta lista de hospedagem. Se precisar 
                            a quantidade ou o tipo, use as <b>CONFIGURAÇÕES DA LISTA</b>. Os clientes dos quartos removidos, voltaram à lista de clientes, aguardando alocação.
                            <br><br>A lista em PDF não exibirá os quartos vazios."></i></h5>
                            <div class="border d-flex flex-wrap align-content-start p-2" data-hospedagembox style="min-height: 60vh;border-width: 3px!important;"></div>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-sm">Salvar</button>
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="janNovaListaTransporte">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-2 px-3 font-weight-bold">
                Nova Lista de Transporte
                <button type="button" class="btn btn-sm btn-danger fechar" data-dismiss="modal"><strong>&times;</strong></button>
            </div>
            <div class="modal-body">
                <form action="" method="post" data-aftersubmit="ListaTransporteEditar">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>Nome da Lista</label>
                                <input type="text" name="nome" class="form-control form-control-sm emptyAfterSubmit" maxlength="40">
                                <input type="hidden" name="roteiro_id" value="">
                                <input type="hidden" name="tipo" value="transporte">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>
                                    Quantidade máxima de clientes por viagem
                                    <i class="fas fa-question-circle cursor-pointer fa-fw" data-toggle="popover" data-trigger="hover" title="O que é?" 
                                    data-content="Informe o número de passageiros por viagem (inclui clientes, cortesias e coordenadores). <br><br>
                                    Ex.:<br> 45 clientes + 3 cortesias + 2 coordenadores = <b>50 passageiros</b>."></i>
                                </label>
                                <input type="number" name="clientesViagem" class="form-control form-control-sm" min="1" value="1">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input" name="criancaColoIndividual" value="yes">Criança de colo como cliente individual.
                                    <i class="fas fa-question-circle fa-fw"title="O que significa?" data-toggle="popover" data-trigger="hover"
                                    data-content="Define o comportamento da <b>criança de colo</b> na lista. Inicialmente a criança de colo não é incluída na lista, pois 
                                    está implícito que ela estará com um dos responsáveis. Abaixo segue informação sobre as configurações:<br><br>

                                    <ul>
                                        <li><b>MARCADA</b>: a criança de colo nesta lista será considerada um passageiro/cliente individual, e será mostrada
                                        na lista de clientes com a marcação (<span class='text-primary font-weight-bold'>*</span>) como diferencial dos outros clientes. Você precisará
                                        alocar manualmente a criança na mesma viagem/quarto do responsável. <b>Ela poderá ocupar poltrona ou cama.</b></li>
                                        <li><b>DESMARCADA</b>: a criança de colo não será exibida na lista. Ao gerar a lista a criança de colo será lançada junto com um dos responsáveis.
                                        Nessa configuração <b>a criança de colo não poderá ocupar poltrona ou cama, pois estará no COLO</b>.</li>
                                    </ul>
                                    
                                    "></i>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12 text-right">
                            <button type="submit" class="btn btn-success">Criar lista</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="janListaTransporte" data-lid="">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-2 px-3 font-weight-bold">
                <span></span>
                <button type="button" class="btn btn-sm btn-danger fechar" data-dismiss="modal"><strong>&times;</strong></button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-header card-collapse font-weight-bold p-2">
                        CONFIGURAÇÕES DA LISTA
                    </div>
                    <div class="card-body p-2" data-config style="display:none;">
                        <form action="" method="post" data-aftersubmit="ListaTransporteEditarRefresh">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Nome da Lista</label>
                                        <input type="text" name="nome" class="form-control form-control-sm emptyAfterSubmit" maxlength="40">
                                        <input type="hidden" name="roteiro_id" value="">
                                        <input type="hidden" name="id" value="">
                                        <input type="hidden" name="tipo" value="transporte">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>
                                            Quantidade máxima de clientes por viagem
                                            <i class="fas fa-question-circle cursor-pointer fa-fw" data-toggle="popover" data-trigger="hover" title="O que é?" 
                                            data-content="Informe o número de passageiros por viagem (inclui clientes, cortesias e coordenadores). <br><br>
                                            Ex.:<br> 45 clientes + 3 cortesias + 2 coordenadores = <b>50 passageiros</b>."></i>
                                        </label>
                                        <input type="number" name="clientesViagem" class="form-control form-control-sm" min="1" >
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input" name="criancaColoIndividual" value="yes">Criança de colo como cliente individual.
                                            <i class="fas fa-question-circle fa-fw"title="O que significa?" data-toggle="popover" data-trigger="hover"
                                            data-content="Define o comportamento da <b>criança de colo</b> na lista. Inicialmente a criança de colo não é incluída na lista, pois 
                                            está implícito que ela estará com um dos responsáveis. Abaixo segue informação sobre as configurações:<br><br>

                                            <ul>
                                                <li><b>MARCADA</b>: a criança de colo nesta lista será considerada um passageiro/cliente individual, e será mostrada
                                                na lista de clientes com a marcação (<span class='text-primary font-weight-bold'>*</span>) como diferencial dos outros clientes. Você precisará
                                                alocar manualmente a criança na mesma viagem/quarto do responsável. <b>Ela poderá ocupar poltrona ou cama.</b></li>
                                                <li><b>DESMARCADA</b>: a criança de colo não será exibida na lista. Ao gerar a lista a criança de colo será lançada junto com um dos responsáveis.
                                                Nessa configuração <b>a criança de colo não poderá ocupar poltrona ou cama, pois estará no COLO</b>.</li>
                                            </ul>
                                            
                                            "></i>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12 text-right">
                                    <button type="submit" class="btn btn-success">Salvar lista</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <hr>
                <div class="row">
                    <div class="col-12 col-md-4"><!-- COLUNA 1 -->
                        <h5 class="font-weight-bold">CLIENTES 
                        <i class="fas fa-question-circle" title="O que é?" data-toggle="popover" data-trigger="hover" data-content="Lista de passageiros, incluindo coordenadores."></i></h5>
                        <div class="border p-2" data-clientesbox style="min-height: 60vh; border-width: 3px!important;"></div>
                    </div>
                    <div class="col-12 col-md-8"><!-- COLUNA 2 -->
                        <h5 class="font-weight-bold">TRANSPORTE 
                            <i class="fas fa-question-circle" title="O que é?" data-toggle="popover" data-trigger="hover" data-content="Quantidade de viagens necessárias para transportar 
                            todos os clientes, mais 1 viagem extra para imprevisto. <br><br>Se não precisar dessa viagem extra, só ignorar. A lista em PDF não exibirá as viagens vazias."></i></h5>
                        <div class="border d-flex flex-wrap align-content-start p-2" data-transportebox style="min-height: 60vh;border-width: 3px!important;"></div>
                    </div>
                </div>
            
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-sm">Salvar</button>
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-2 px-3 font-weight-bold">
                Titulo do modal
                <button type="button" class="btn btn-sm btn-danger fechar" data-dismiss="modal"><strong>&times;</strong></button>
            </div>
            <div class="modal-body">
            
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>



<script>
    var roteiro = JSON.parse('{!!addslashes(json_encode($roteiro))!!}');
    var ValorMinimoRoteiro = {{ceil(($despesasTotal + $roteiro->lucro_previsto->lucroRateio) / $roteiro->qtd_rateio)}};

    function modalTarifaRoteiro()
    {
        $('.modal').modal('hide');
        setTimeout(function(){$('#modalRoteiroTarifas').modal('show');}, 200);
        $('#modalRoteiroTarifas').find('[name="valor"]').trigger('change');
        if($('#modalRoteiroTarifas').find('tr[data-example]').length > 1) {
            for(let i = 1; i < $('#modalRoteiroTarifas').find('tr[data-example]').length; i++) {
                $('#modalRoteiroTarifas').find('tr[data-example]').eq(i).show();
                $('#modalRoteiroTarifas').find('tr[data-example]').eq(i).removeAttr('data-example');
            }
        }
    }

    function addTarifaRoteiro(sender)
    {
        if($(sender).siblings('table').find('tr[data-example]').length == 0) {
            alerta('Essa página foi atualizada, pois houve alguma alteração indevida na página.','Espera...', 'info');
            loadLanding(location.hash);
            return false;
        }

        if($(sender).siblings('table').find('tr:not([data-example])').length >= 10) {
            alerta('Limite de tarifas atingido!', 'Ops...', 'info');
            return false;
        }

        $(sender).siblings('table').find('tr[data-example]').eq(0).clone().appendTo($(sender).siblings('table'));
        $(sender).siblings('table').find('tr').last().removeAttr('data-example').show();
        $(sender).siblings('table').find('tr').last().find(':input').prop('required', true);
        $(sender).siblings('table').find('tr').last().find('[data-valor-dias] :input').prop('required', false);
    }

    function salvarTarifasRoteiro(sender)
    {
        let modal = $(sender).parents('.modal');
        let form = modal.find('form');

        let tarifas = [];

        // Valida...
        for(let i = 0; i < form.find('tr:not([data-example]) [required]').length; i++) {
            if(form.find('tr:not([data-example]) [required]').eq(i).val() == '') {
                alerta('Preencha todos os campos ou remova a tarifa que não foi preenchida por completo.','Calma.', 'info');
                form.find('tr:not([data-example]) [required]').eq(i).focus();
                return false;
            }
        }

        let clientesVazios = [];

        // Varre cada linha para preencher array de tarifas
        for(let i = 0; i < form.find('tr:not([data-example])').length; i++) {
            if(
                parseInt(form.find('tr:not([data-example])').eq(i).find('[name="qtd_adultos"]').val()) == 0 && 
                parseInt(form.find('tr:not([data-example])').eq(i).find('[name="qtd_criancas"]').val()) == 0 &&
                parseInt(form.find('tr:not([data-example])').eq(i).find('[name="qtd_colo"]').val()) == 0
            ) {
                clientesVazios.push(form.find('tr:not([data-example])').eq(i).find('[name="nome_tarifa"]').val());
                /*
                alerta('Você não informou a quantidade de clientes (ADULTO ou CRIANÇA ou CRIANÇA DE COLO) na tarifa "'+
                form.find('tr:not([data-example])').eq(i).find('[name="nome_tarifa"]').val()+'".','Ainda tem algo faltando...', 'info');
                return false;
                */
            }
            tarifas.push({
                nome: form.find('tr:not([data-example])').eq(i).find('[name="nome_tarifa"]').val(),
                valor: Dobbin.converteRealEmCentavo(form.find('tr:not([data-example])').eq(i).find('[name="valor"]').val()),
                distr: {
                    adultos: parseInt(form.find('tr:not([data-example])').eq(i).find('[name="qtd_adultos"]').val()),
                    criancas: parseInt(form.find('tr:not([data-example])').eq(i).find('[name="qtd_criancas"]').val()),
                    colo: parseInt(form.find('tr:not([data-example])').eq(i).find('[name="qtd_colo"]').val())
                },
                qtd: parseInt(form.find('tr:not([data-example])').eq(i).find('[name="qtd_adultos"]').val()) + parseInt(form.find('tr:not([data-example])').eq(i).find('[name="qtd_criancas"]').val())+
                parseInt(form.find('tr:not([data-example])').eq(i).find('[name="qtd_colo"]').val())
            });
            form.find('tr:not([data-example])').eq(i)
        }

        if(clientesVazios.length > 0) {
            // Se tem tarifas com quantidade de clientes VAZIO, emite uma mensagem de confirmação.
            let confirma1 = confirm("Encontrei uma(s) tarifa(s) sem uma quantidade de clientes definida: \n"+
            clientesVazios.join(', ')+
            "\n\n> Se essa tarifa se aplicar a TAXAS EXTRAS no roteiro que não precisam de quantidade de clientes definida, clique em OK para continuar. "+
            "\n> Se está com dúvida e quer revisar, clique em CANCELAR.");

            if(confirma1 == false) {
                return false;
            }
        }

        console.log(tarifas);
        $.post(PREFIX_POST+'roteiros/'+roteiro.id+'/tarifa/editar', {
            tarifas: JSON.stringify(tarifas)
        }, function(res){
            if(res.success == true) {
                alerta('Tarifas adicionadas ao roteiro. Calma que vamos recarregar tudo...', 'Salvo!', 'success');
                modal.modal('hide');
                loadLanding(location.hash);
            } else {
                alerta(res.mensagem, 'Falha!', 'warning');
            }
        },'json').
        fail(function(ev){
            nativePOSTFail(ev);
        });
    }

    function getClientesRoteiro()
    {
        $.post('/roteiros/ver/'+roteiro.id+'/clientes', function(res){
            if(res.success) {
                if(debugEnabled == true){console.log(res);}
                $('#passagDiv').html('');
                $('#passagDiv').append('<div class="alert alert-info small px-2 py-1"><i class="fas fa-info-circle"></i> Após 2 dias de conclusão do roteiro, essa lista de clientes passa a ser <strong>definitiva</strong>. Antes disso, qualquer alteração pode ser feita na lista de passageiros da venda.</div>');
                
                
                if(res.tipo == 'DEFINITIVO') {
                    $('#passagDiv').append('<div class="border small px-2 py-1 mb-2" data-toggle="tooltip" title="Lista definitiva já foi arquivada."><i class="fas fa-circle text-danger mr-2"></i> DEFINITIVA'+
                    '<a class="btn btn-primary btn-xs ml-3" href="/pdf/roteiros/'+roteiro.id+'/listapassageiros" target="_blank">Gerar PDF</a></div>');
                } else if(res.tipo == 'PROVISORIO') {
                    $('#passagDiv').append('<div class="border small px-2 py-1 mb-2" data-toggle="tooltip" title="Alterações ainda são possíveis!"><i class="fas fa-circle text-success mr-2"></i> PROVISÓRIA'+
                    '<a class="btn btn-primary btn-xs ml-3" href="/pdf/roteiros/'+roteiro.id+'/listapassageiros" target="_blank">Gerar PDF</a></div>');
                } else {
                    $('#passagDiv').append('<div class="border small px-2 py-1 mb-2" data-toggle="tooltip" title="Situação da lista é indefinida."><i class="fas fa-circle mr-2"></i> INDEFINIDA'+
                    '<a class="btn btn-primary btn-xs ml-3" href="/pdf/roteiros/'+roteiro.id+'/listapassageiros" target="_blank">Gerar PDF</a></div>');
                }

                $('#passagDiv').append('<table class="table table-sm table-bordered small"><thead class="thead-dark"><tr> <th>#</th></tH> <th>Nome</th> <th>CPF</th> <th>Faixa etária</th> <th class="small">Criança de Colo</th> <th>Venda</th> </tr></thead><tbody></tbody></table>');

                if(res.clientes.length > 0) {
                    let colo;
                    let contador = 1;
                    let numero = '';
                    res.clientes.forEach(function(c, key){
                        if(c.colo === true) {
                            colo = '<span class="text-success"><i class="fas fa-check"></i></span>';
                            numero = '';
                            contador--;
                        } else {
                            colo = '-';
                            numero = contador;
                        }


                        $('#passagDiv table tbody').append('<tr> <td>'+ numero +'</td> <td><a href="javascript:void(0)" onclick="loadCliente('+c.id+')">'+c.nome+'</a></td> '+
                        '<td>'+c.cpf+'</td> <td>'+c.faixa_etaria+'</td> <td class="text-center">'+colo+'</td> '+
                        '<td><a href="javascript:void(0)" onclick="getVenda('+c.venda+')">#'+c.venda+'</a></td> </tr>');
                        contador++;
                    });
                    numero = undefined;

                    // Informações extras.
                    $('#passagDiv').append('<table class="table table-sm table-bordered small"><thead> <tr><th colspan="2">Reservado</th> <th colspan="2">Ocupado</th> </tr> </thead><tbody></tbody></table>');
                    let diffQTD = {
                        total: res.lista.total.total - res.lista.ocupado.total,
                        criancas: res.lista.total.criancas - res.lista.ocupado.criancas,
                        criancas_colo: res.lista.total.criancas_colo - res.lista.ocupado.criancas_colo,
                        adultos: res.lista.total.adultos - res.lista.ocupado.adultos,
                        total_str: '',
                        criancas_str: '',
                        criancas_colo_str: '',
                        adultos_str: '',
                    }

                    
                    if(diffQTD.total > 0) { // Faltando passageiros
                        diffQTD.total_str = '<span class="badge badge-danger badge-pill" style="font-size:.7rem;">'+res.lista.ocupado.total+'</span> '+
                        '<span class="cursor-pointer" data-toggle="tooltip" title="Falta(m) '+ (diffQTD.total)+' passageiro(s)."><i class="fas fa-question-circle"></i></span>';
                    } else if(diffQTD.total < 0) { // Passageiros a mais
                        diffQTD.total_str = '<span class="badge badge-primary badge-pill" style="font-size:.7rem;">'+res.lista.ocupado.total+'</span> '+
                        '<span class="cursor-pointer" data-toggle="tooltip" title="Há '+(diffQTD.total  * (-1))+' passageiro(s) a mais."><i class="fas fa-question-circle"></i></span>';
                    } else { // OK
                        diffQTD.total_str = '<span class="badge badge-success badge-pill" style="font-size:.7rem;">'+res.lista.ocupado.total+'</span> '+
                        '<span class="cursor-pointer" data-toggle="tooltip" title="Tudo OK!"><i class="fas fa-question-circle"></i></span>';
                    }

                    if(diffQTD.adultos > 0) { // Faltando passageiros
                        diffQTD.adultos_str = '<span class="badge badge-danger badge-pill" style="font-size:.7rem;">'+res.lista.ocupado.adultos+'</span> '+
                        '<span class="cursor-pointer" data-toggle="tooltip" title="Falta(m) '+ (diffQTD.adultos)+' adulto(s)."><i class="fas fa-question-circle"></i></span>';
                    } else if(diffQTD.adultos < 0) { // Passageiros a mais
                        diffQTD.adultos_str = '<span class="badge badge-primary badge-pill" style="font-size:.7rem;">'+res.lista.ocupado.adultos+'</span> '+
                        '<span class="cursor-pointer" data-toggle="tooltip" title="Há '+(diffQTD.adultos * (-1))+' adulto(s) a mais."><i class="fas fa-question-circle"></i></span>';
                    } else { // OK
                        diffQTD.adultos_str = '<span class="badge badge-success badge-pill" style="font-size:.7rem;">'+res.lista.ocupado.adultos+'</span> '+
                        '<span class="cursor-pointer" data-toggle="tooltip" title="Tudo OK!"><i class="fas fa-question-circle"></i></span>';
                    }

                    if(diffQTD.criancas > 0) { // Faltando passageiros
                        diffQTD.criancas_str = '<span class="badge badge-danger badge-pill" style="font-size:.7rem;">'+res.lista.ocupado.criancas+'</span> '+
                        '<span class="cursor-pointer" data-toggle="tooltip" title="Falta(m) '+ (diffQTD.criancas)+' criança(s)."><i class="fas fa-question-circle"></i></span>';
                    } else if(diffQTD.criancas < 0) { // Passageiros a mais
                        diffQTD.criancas_str = '<span class="badge badge-primary badge-pill" style="font-size:.7rem;">'+res.lista.ocupado.criancas+'</span> '+
                        '<span class="cursor-pointer" data-toggle="tooltip" title="Há '+(diffQTD.criancas * (-1))+' criança(s) a mais."><i class="fas fa-question-circle"></i></span>';
                    } else { // OK
                        diffQTD.criancas_str = '<span class="badge badge-success badge-pill" style="font-size:.7rem;">'+res.lista.ocupado.criancas+'</span> '+
                        '<span class="cursor-pointer" data-toggle="tooltip" title="Tudo OK!"><i class="fas fa-question-circle"></i></span>';
                    }

                    if(diffQTD.criancas_colo > 0) { // Faltando passageiros
                        diffQTD.criancas_colo_str = '<span class="badge badge-danger badge-pill" style="font-size:.7rem;">'+res.lista.ocupado.criancas_colo+'</span> '+
                        '<span class="cursor-pointer" data-toggle="tooltip" title="Falta(m) '+ (diffQTD.criancas_colo)+' criança(s) de colo."><i class="fas fa-question-circle"></i></span>';
                    } else if(diffQTD.criancas_colo < 0) { // Passageiros a mais
                        diffQTD.criancas_colo_str = '<span class="badge badge-primary badge-pill" style="font-size:.7rem;">'+res.lista.ocupado.criancas_colo+'</span> '+
                        '<span class="cursor-pointer" data-toggle="tooltip" title="Há '+(diffQTD.criancas_colo * (-1))+' criança(s) de colo a mais."><i class="fas fa-question-circle"></i></span>';
                    } else { // OK
                        diffQTD.criancas_colo_str = '<span class="badge badge-success badge-pill" style="font-size:.7rem;">'+res.lista.ocupado.criancas_colo+'</span> '+
                        '<span class="cursor-pointer" data-toggle="tooltip" title="Tudo OK!"><i class="fas fa-question-circle"></i></span>';
                    }

                    console.log(diffQTD);
                    $('#passagDiv table:eq(1) tbody').append('<tr> <td class="text-right">Total<br>--<br>Adultos (poltronas)<br>Crianças (poltronas)<br>Crianças de Colo</td> '+
                    '<td class="text-center">'+
                    '<span class="badge badge-dark badge-pill" style="font-size:.7rem;">'+res.lista.total.total+'</span><br>--<br>'+
                    '<span class="badge badge-dark badge-pill" style="font-size:.7rem;">'+res.lista.total.adultos+'</span><br>'+
                    '<span class="badge badge-dark badge-pill" style="font-size:.7rem;">'+res.lista.total.criancas+'</span><br>'+
                    '<span class="badge badge-dark badge-pill" style="font-size:.7rem;">'+res.lista.total.criancas_colo+'</span></td>'+
                    '<td class="text-right">Total<br>--<br>Adultos (poltronas)<br>Crianças (poltronas)<br>Crianças de Colo</td>'+
                    '<td class="text-center">'+diffQTD.total_str +'<br>--<br>'+
                    diffQTD.adultos_str+'<br>'+diffQTD.criancas_str +'<br>'+diffQTD.criancas_colo_str +'</td>'+
                    '</tr>')
                } else {
                    $('#passagDiv table tbody').append('<tr> <td colspan="5" class="text-center font-italic py-2"><strong>Não há clientes para este roteiro.</strong><br><br>'+
                    'Uma venda foi realizada e o(s) passageiro(s) não aparece(m)? Acesse a venda correspondente e informe os clientes que serão passageiros.</td></tr>');
                }
                
                $('[data-toggle="tooltip"]').tooltip();
            } else {
                alerta(res.mensagem, 'Falha ao carregar lista de passageiros.', 'warning');
            }
        }, 'json').
        fail(function(ev){nativePOSTFail(ev);});
    }

    function getEstoqueRoteiro()
    {
        $.post('/roteiros/ver/'+roteiro.id+'/estoque', function(res){
            if(res.success) {
                if(debugEnabled == true){console.log(res.estoque);}
                if(parseInt($('[data-poltrona-livre]').text()) != res.estoque.livre) {
                    loadLanding(location.hash);
                }
                
                $('#graficoVendas').children('.progress').eq(0).attr('title', 'Poltronas vendidas: '+res.estoque.vendidos);
                $('#graficoVendas').children('.progress').eq(0).css('width', res.estoque.vendidos_perc+'%');
                $('#graficoVendas').children('.progress').eq(1).attr('title', 'Poltronas reservadas: '+res.estoque.reservados);
                $('#graficoVendas').children('.progress').eq(1).css('width', res.estoque.reservados_perc+'%');
                $('#graficoVendas').children('.progress').eq(2).attr('title', 'Poltronas livres para venda: '+res.estoque.livre);
                $('#graficoVendas').children('.progress').eq(2).css('width', res.estoque.livre_perc+'%');
                $('[data-poltrona-livre]').html(res.estoque.livre);
                $('[data-toggle="tooltip"]').tooltip();
            } else {
                alerta(res.mensagem, 'Falha ao atualizar o estoque.', 'warning');
            }
        }, 'json').
        fail(function(ev){nativePOSTFail(ev);});
    }

    function getListas()
    {
        $.post('/roteiros/ver/'+roteiro.id+'/listas', function(res){
            console.log(res);
            if(res.success) {
                let listaDIV = $('#listaListas');
                listaDIV.html('');
                if(res.listas.length == 0) {
                    listaDIV.append('<div class="text-center py-3"><span class="badge badge-secondary px-2">SEM LISTAS</span></div>');
                } else {
                    res.listas.forEach(function(l) {
                        let dataCriado = new Date(l.atualizado_em);
                        let icone;
                        if(l.tipo == 'hospedagem') {
                            icone = 'fas fa-hotel';
                            listaDIV.append(
                                '<div class="mb-2 p-2 border rounded-sm d-flex flex-row justify-content-between">'+
                                    '<div onclick="janListaHospede('+l.id+')" class="cursor-pointer flex-grow-1"><h5 class="mb-1">'+l.nome+'</h5> <span class="px-2 py-1 badge badge-secondary text-uppercase">'+
                                    '<i class="'+icone+'"></i> &nbsp; '+l.tipo+'</span><br><span class="badge badge-light px-2">'+dataCriado.toLocaleDateString()+' '+dataCriado.toLocaleTimeString()+'</span></div>'+
                                    '<div><button type="button" class="btn btn-danger btn-sm mb-1" data-toggle="tooltip" title="Apaga esta lista." onclick="deleteLista('+l.id+')"><i class="fas fa-fw fa-trash"></i></button><br>'+
                                    '<a href="/pdf/roteiros/'+roteiro.id+'/lista/'+l.id+'/download" class="btn btn-primary btn-sm mb-1" data-toggle="tooltip" title="Baixar esta lista em PDF."><i class="fas fa-fw fa-file-pdf"></i></a></div>'+
                                '</div>'
                            );
                        } else if(l.tipo == 'transporte') {
                            icone = 'fas fa-shuttle-van';
                            listaDIV.append(
                                '<div class="mb-2 p-2 border rounded-sm d-flex flex-row justify-content-between">'+
                                    '<div onclick="janListaTransporte('+l.id+')" class="cursor-pointer flex-grow-1"><h5 class="mb-1">'+l.nome+'</h5> <span class="px-2 py-1 badge badge-secondary text-uppercase">'+
                                    '<i class="'+icone+'"></i> &nbsp; '+l.tipo+'</span><br><span class="badge badge-light px-2">'+dataCriado.toLocaleDateString()+' '+dataCriado.toLocaleTimeString()+'</span></div>'+
                                    '<div><button type="button" class="btn btn-danger btn-sm mb-1" data-toggle="tooltip" title="Apaga esta lista." onclick="deleteLista('+l.id+')"><i class="fas fa-fw fa-trash"></i></button><br>'+
                                    '<a href="/pdf/roteiros/'+roteiro.id+'/lista/'+l.id+'/download" class="btn btn-primary btn-sm mb-1" data-toggle="tooltip" title="Baixar esta lista em PDF."><i class="fas fa-fw fa-file-pdf"></i></a></div>'+
                                '</div>'
                            );
                        } else {
                            icone = '';
                        }
                    });
                    restartTooltip();
                }

            } else {
                alerta(res.mensagem, '', 'warning');
            }
        }, 'json').
        fail(function(ev){nativePOSTFail(ev);});

        return true;
    }

    function deleteLista(id)
    {
        $.post(PREFIX_POST+'roteiros/'+roteiro.id+'/lista/'+id+'/apagar',function(res){
            if(res.success) {
                alerta('Lista apagada.', 'Sucesso!', 'success');
                getListas();
            } else {
                alerta(res.mensagem, 'Ops!', 'info');
            }
        }, 'json');
    }

    function deleteTarifaRoteiro(sender)
    {
        if($(sender).parents('tbody').find('tr:not([data-example])').length > 1) {
            $(sender).parents('tr').attr('data-example', true);
            $(sender).parents('tr').hide();
        } else {
            alerta('Não tem como remover essa tarifa. Você precisa de pelo menos uma tarifa definida.');
        }
    }

    function janNovaListaHospede(sender)
    {
        let alvo = $(sender);
        let modal = $('#janNovaListaHospede');
        modal.find('form')[0].reset();
        modal.find('form').attr('action', 'roteiros/'+alvo.data('id')+'/novalista');
        
        modal.modal('show');
    }

    function janNovaListaTransporte(sender)
    {
        let alvo = $(sender);
        let modal = $('#janNovaListaTransporte');
        modal.find('form')[0].reset();
        modal.find('form').attr('action', 'roteiros/'+alvo.data('id')+'/novalista');
        
        modal.modal('show');
    }

    function ativaDragListas()
    {
        let modal = $('#janListaHospede, #janListaTransporte');
        
        // DROPPABLE
        setTimeout(function(){
            modal.find('[data-clientesbox]').droppable({
                activate: function(event, ui){
                    let destino = $(event.target);
                    let objeto = $(ui.draggable[0]);
                },

                drop: function(event, ui){
                    let destino = $(event.target);
                    let objeto = $(ui.draggable[0]);

                    // Clona o objeto e destroi o original. Torna o objeto clonado arrastavel.
                    objeto.clone().appendTo(destino).css({position:'relative', top:'auto', left:'auto'});
                    objeto.remove();
                    $('.arrastavel').draggable({
                        cursor: 'grabbing',
                        revert: 'invalid',
                        revertDuration: 200,
                    });

                    for(let i = 0; i < modal.find('[data-hospedagembox] .card-body, [data-transportebox] .card-body').length; i++) {
                        let maxTotal = parseInt(modal.find('[data-hospedagembox] .card-body, [data-transportebox] .card-body').eq(i).parent().data('total'));
                        let qtdAtual = modal.find('[data-hospedagembox] .card-body, [data-transportebox] .card-body').eq(i).children('.arrastavel').length;
                        
                        
                        if(qtdAtual >= maxTotal) {
                            modal.find('[data-hospedagembox] .card-body, [data-transportebox] .card-body').eq(i).droppable('disable'); // Desativa o droppable.
                            modal.find('[data-hospedagembox] .card-body, [data-transportebox] .card-body').eq(i).addClass('border border-danger');
                        } else {
                            modal.find('[data-hospedagembox] .card-body, [data-transportebox] .card-body').eq(i).droppable('enable'); // Reativa o droppable.
                            modal.find('[data-hospedagembox] .card-body, [data-transportebox] .card-body').eq(i).removeClass('border border-danger');
                        }
                    }
                }
            });

            modal.find('[data-hospedagembox] .card-body, [data-transportebox] .card-body').droppable({
                activate: function(event, ui){
                    let destino = $(event.target);
                    let objeto = $(ui.draggable[0]);
                    
                    // Definefundo
                    destino.css({backgroundColor: 'rgba(130, 201, 174, .7)', border: 'solid 1px rgba(69, 161, 125, .5)'});
                },
                deactivate: function(event, ui){
                    let destino = $(event.target);
                    let objeto = $(ui.draggable[0]);

                    // Reseta definição de fundo
                    destino.css({backgroundColor: 'rgba(0, 0, 0, 0)', border: 'none'});
                },
                over: function(event, ui){
                    let destino = $(event.target);
                    let objeto = $(ui.draggable[0]);

                    // Muda o fundo ao passar por cima.
                    destino.css({backgroundColor: 'rgba(130, 201, 174, .2)'});
                },
                out: function(event, ui){
                    let destino = $(event.target);
                    let objeto = $(ui.draggable[0]);

                    // Muda o fundo ao sair de cima.
                    destino.css({backgroundColor: 'rgba(130, 201, 174, .7)'});
                },
                drop: function(event, ui){
                    let destino = $(event.target);
                    let objeto = $(ui.draggable[0]);

                    // Muda cor ao sair de cima.
                    destino.css({backgroundColor: 'rgba(0,0,0,0)',border: 'none'});

                    // Clona o objeto e destroi o original. Torna o objeto clonado arrastavel.
                    objeto.clone().appendTo(destino).css({position:'relative', top:'auto', left:'auto'});
                    objeto.remove();
                    $('.arrastavel').draggable({
                        cursor: 'grabbing',
                        revert: 'invalid',
                        revertDuration: 200,
                    });

                    
                    for(let i = 0; i < modal.find('[data-hospedagembox] .card-body, [data-transportebox] .card-body').length; i++) {
                        let maxTotal = parseInt(modal.find('[data-hospedagembox] .card-body, [data-transportebox] .card-body').eq(i).parent().data('total'));
                        let qtdAtual = modal.find('[data-hospedagembox] .card-body, [data-transportebox] .card-body').eq(i).children('.arrastavel').length;

                        if(qtdAtual >= maxTotal) {
                            modal.find('[data-hospedagembox] .card-body, [data-transportebox] .card-body').eq(i).droppable('disable'); // Desativa o droppable.
                            modal.find('[data-hospedagembox] .card-body, [data-transportebox] .card-body').eq(i).addClass('border border-danger');
                        } else {
                            modal.find('[data-hospedagembox] .card-body, [data-transportebox] .card-body').eq(i).droppable('enable'); // Reativa o droppable.
                            modal.find('[data-hospedagembox] .card-body, [data-transportebox] .card-body').eq(i).removeClass('border border-danger');
                        }
                    }
                }
            });
    

            // Verifica se campos droppable precisam ser desativados.
            for(let i = 0; i < modal.find('[data-hospedagembox] .card-body, [data-transportebox] .card-body').length; i++) {
                let maxTotal = parseInt(modal.find('[data-hospedagembox] .card-body, [data-transportebox] .card-body').eq(i).parent().data('total'));
                let qtdAtual = modal.find('[data-hospedagembox] .card-body, [data-transportebox] .card-body').eq(i).children('.arrastavel').length;

                if(qtdAtual >= maxTotal) {
                    modal.find('[data-hospedagembox] .card-body, [data-transportebox] .card-body').eq(i).droppable('disable'); // Desativa o droppable.
                    modal.find('[data-hospedagembox] .card-body, [data-transportebox] .card-body').eq(i).addClass('border border-danger');
                } else {
                    modal.find('[data-hospedagembox] .card-body, [data-transportebox] .card-body').eq(i).droppable('enable'); // Reativa o droppable.
                    modal.find('[data-hospedagembox] .card-body, [data-transportebox] .card-body').eq(i).removeClass('border border-danger');
                }
            }

            
        }, 500);

        // DRAGGABLE
        setTimeout(function(){$('.arrastavel').draggable({
            cursor: 'grabbing',
            revert: 'invalid',
            revertDuration: 200,
        });
        $('.arrastavel').css('zIndex', 1400);}, 400);
    }

    function janListaHospede(id)
    {
        $.post('/roteiros/ver/'+roteiro.id+'/lista/'+id, function(res){
            if(debugEnabled == true) {console.log(res);}
            if(res.success) {
                if(res.lista.tipo == 'hospedagem') {
                    let modal = $('#janListaHospede');
                    modal.data('lid', id);
                    let lista = res.lista;
                    let data = new Date(lista.data);

                    let controleEtapas = 0; // Controle de etapas, para forçar uma execução linear, sem multi-thread.

                    modal.find('.modal-header span').html('LISTA: '+lista.nome+' <span class="badge badge-secondary px-2 text-uppercase"><i class="fas fa-hotel"></i> &nbsp; '+lista.tipo+'</span> '+
                    '<small>('+data.toLocaleDateString()+')</small>');
                    modal.find('[data-clientesbox]').html('');

                    // Preenche o bloco de configurações da lista.
                    //console.log(res);
                    modal.find('[data-config] [name="nome"]').val(res.lista.nome); // Nome
                    modal.find('[data-config] [name="roteiro_id"]').val(roteiro.id); // Roteiro ID
                    modal.find('[data-config] [name="id"]').val(res.lista.id); // Roteiro ID
                    let config = JSON.parse(lista.instrucoes);
                    
                    modal.find('[data-config] [name="individual"]').val(config.quartos_qtd.individual); // Individual
                    modal.find('[data-config] [name="duplo"]').val(config.quartos_qtd.duplo); // Duplo
                    modal.find('[data-config] [name="triplo"]').val(config.quartos_qtd.triplo); // Triplo
                    modal.find('[data-config] [name="quadruplo"]').val(config.quartos_qtd.quadruplo); // Quádruplo
                    modal.find('[data-config] [name="quintuplo"]').val(config.quartos_qtd.quintuplo); // Quíntuplo
                    modal.find('[data-config] [name="sextuplo"]').val(config.quartos_qtd.sextuplo); // Séxtuplo

                    // Criança colo individual.
                    modal.find('[data-config] [name="criancaColoIndividual"]').prop('checked', config.criancaColoIndividual);
                    

                    // Configura o formulário de configurações.
                    modal.find('[data-config] form').attr('action', 'roteiros/'+roteiro.id+'/lista/'+res.lista.id+'/configsalvar');
                    modal.find('[data-config]').css({display:'none'});

                    // Limpa as caixas de drag and drop.
                    modal.find('[data-hospedagembox]').html('');

                    // Escreve conteúdo das caixas drag and drop. Lado esquerdo: clientes e coordenadores. Lado direito: quartos.
                    // Individual.
                    for(let i = 0; i < config.quartos_qtd.individual; i++) {
                        modal.find('[data-hospedagembox]').append('<div class="card mb-2 mr-1" data-total="1" style="width: 300px">'+
                        '<div class="card-header px-2 py-1 bg-dark text-white">QUARTO INDIVIDUAL #'+(i+1)+'</div>'+
                        '<div class="card-body" style="min-height: 100px;"></div>'+
                        '</div>');
                    }

                    // Duplo.
                    for(let i = 0; i < config.quartos_qtd.duplo; i++) {
                        modal.find('[data-hospedagembox]').append('<div class="card mb-2 mr-1" data-total="2" style="width: 300px">'+
                        '<div class="card-header px-2 py-1 bg-dark text-white">QUARTO DUPLO #'+(i+1)+'</div>'+
                        '<div class="card-body" style="min-height: 100px;"></div>'+
                        '</div>');
                    }

                    // Triplo.
                    for(let i = 0; i < config.quartos_qtd.triplo; i++) {
                        modal.find('[data-hospedagembox]').append('<div class="card mb-2 mr-1" data-total="3" style="width: 300px">'+
                        '<div class="card-header px-2 py-1 bg-dark text-white">QUARTO TRIPLO #'+(i+1)+'</div>'+
                        '<div class="card-body" style="min-height: 100px;"></div>'+
                        '</div>');
                    }

                    // Quadruplo.
                    for(let i = 0; i < config.quartos_qtd.quadruplo; i++) {
                        modal.find('[data-hospedagembox]').append('<div class="card mb-2 mr-1" data-total="4" style="width: 300px">'+
                        '<div class="card-header px-2 py-1 bg-dark text-white">QUARTO QUÁDRUPLO #'+(i+1)+'</div>'+
                        '<div class="card-body" style="min-height: 100px;"></div>'+
                        '</div>');
                    }

                    // Quintuplo.
                    for(let i = 0; i < config.quartos_qtd.quintuplo; i++) {
                        modal.find('[data-hospedagembox]').append('<div class="card mb-2 mr-1" data-total="5" style="width: 300px">'+
                        '<div class="card-header px-2 py-1 bg-dark text-white">QUARTO QUÍNTUPLO #'+(i+1)+'</div>'+
                        '<div class="card-body" style="min-height: 100px;"></div>'+
                        '</div>');
                    }

                    // Sextuplo.
                    for(let i = 0; i < config.quartos_qtd.sextuplo; i++) {
                        modal.find('[data-hospedagembox]').append('<div class="card mb-2 mr-1" data-total="6" style="width: 300px">'+
                        '<div class="card-header px-2 py-1 bg-dark text-white">QUARTO SÉXTUPLO #'+(i+1)+'</div>'+
                        '<div class="card-body" style="min-height: 100px;"></div>'+
                        '</div>');
                    }


                    //Recupera coordenadores
                    $.post('/roteiros/ver/'+roteiro.id+'/coord', function(res){
                        if(res.success && res.coordenadores.length > 0) {

                            res.coordenadores.forEach(function(c){
                                modal.find('[data-clientesbox]').append('<div class="border border-danger bg-light text-danger font-weight-bold mb-2 px-2 py-1 rounded-sm cursor-drag arrastavel" '+
                                'style="z-index:1200; min-width: 150px; max-width:250px;" data-id="'+c.id+'">'+
                                c.nome+'</div>');
                            });
                            
                            controleEtapas++; // 0+1 = 1;

                            // Ativa o drag and drop do JQUERY UI
                            ativaDragListas();
                        } else if(res.success) {
                            alerta('Ainda não há coordenadores definidos.', '', 'info');
                        } else {
                            alerta(res.mensagem, 'Não deu para carregar a lista de coordenadores.','warning');
                            controleEtapas = false;
                        }
                        //console.log(res);
                    }, 'json');

                    // Recupera clientes.
                    $.post('/roteiros/ver/'+roteiro.id+'/clientes', function(res){
                        if(res.success && res.clientes.length > 0) {

                            res.clientes.forEach(function(c, keyC){
                                
                                // Verifica se criança de colo deve ser listado ou não.
                                if(config.criancaColoIndividual === true) {
                                    // Lista criança de colo
                                    if(c.colo == false) { // ADULTO
                                        modal.find('[data-clientesbox]').append('<div class="border border-dark bg-light mb-2 px-2 py-1 rounded-sm cursor-drag arrastavel" '+
                                        'style="z-index:1200; min-width: 150px; max-width:250px;" data-id="'+c.id+'">'+
                                        c.nome+'</div>');
                                    } else { // CRIANÇA COLO
                                        modal.find('[data-clientesbox]').append('<div class="border border-dark bg-light mb-2 px-2 py-1 rounded-sm cursor-drag arrastavel" '+
                                        'style="z-index:1200; min-width: 150px; max-width:250px;" data-id="'+c.id+'" data-resp="'+res.clientes[(keyC-1)].id+'">'+
                                        c.nome+' <span class="text-primary font-weight-bold">*</span></div>');
                                    }
                                    
                                } else {
                                    // Esconde criança de colo.
                                    if(c.colo == false) { // ADULTO
                                        modal.find('[data-clientesbox]').append('<div class="border border-dark bg-light mb-2 px-2 py-1 rounded-sm cursor-drag arrastavel" '+
                                        'style="z-index:1200; min-width: 150px; max-width:250px;" data-id="'+c.id+'">'+
                                        c.nome+'</div>');
                                    }
                                }
                                
                            });
                            
                            controleEtapas++; // 1+1 = 2;

                            // Ativa o drag and drop do JQUERY UI
                            ativaDragListas();
                        } else if(res.success) {
                            alerta('Ainda não há clientes na lista de passageiros.', '', 'info');
                        } else {
                            alerta(res.mensagem, 'Não deu para carregar a lista de passageiros.','warning');
                            controleEtapas = false;
                        }
                        //console.log(res);
                    }, 'json');

                    if(controleEtapas === false) {
                        return false;
                        // Interrompe a execução.
                    }

                    if(lista.dados.length > 0) {
                        // Lista já contém dados. Faz o ajuste dos dados.
                        // 1) Caso um cliente ou coordenador tenha sido removido da lista, não adiciona ele ao quarto.
                        // 2) Caso um quarto tenha sido removido da lista, não adiciona o quarto. Os ocupantes voltam
                        // ao box de clientes e coordenadores, para serem alocados.
                        let tentativas = 0;
                        let cronFinal = setInterval(function(){
                                if(tentativas >= 5) {
                                    clearInterval(cronFinal);
                                    alerta('O Dobbin demorou mais de 2,5s para responder e a operação foi interrompida. Se isso persistir, '+
                                    'informe ao desenvolvedor o roteiro e a lista que apresentou esse comportamento.', 'ATENÇÃO!', 'warning');
                                    return false;
                                }

                                if(controleEtapas == 2) {
                                    // EXECUTA!
                                    let dados = JSON.parse(lista.dados);
                                    let qAtual = {
                                        tipo: '',
                                        indice: 0
                                    }
                                    //console.log(dados);
                                    dados.quartos.forEach(function(q){
                                        if(qAtual.tipo == '') { // Inicio do laço.
                                            qAtual.tipo = q.total;
                                        } else if(qAtual.tipo != q.total) { // Mudança do tipo do quarto.
                                            qAtual.tipo = q.total;
                                            qAtual.indice = 0;
                                        } else { // Mesmo tipo de quarto.
                                            qAtual.indice++;
                                        }

                                        // Adiciona as pessoas ao quarto
                                        if(q.pessoas.length == 0) {
                                            // Não há pessoas para adicionar ao quarto
                                        } else {
                                            // Há pessoas para adicionar ao quarto.
                                            // Verifica se o quarto existe ou foi removido
                                            if(modal.find('[data-hospedagembox] [data-total="'+qAtual.tipo+'"]').eq(qAtual.indice).length > 0) {
                                                
                                                // Quarto existe. Adiciona pessoas
                                                let hBox = modal.find('[data-hospedagembox] [data-total="'+qAtual.tipo+'"]').eq(qAtual.indice).children('.card-body');
                                                let cBox = modal.find('[data-clientesbox]');
                                                
                                                q.pessoas.forEach(function(p){
                                                    if(p.tipo == 'coord') {
                                                        // Coordenador.

                                                        cBox.find('[data-id="'+p.id+'"].border-danger').clone().appendTo(hBox);
                                                        cBox.find('[data-id="'+p.id+'"].border-danger').remove();
                                                    } else if(p.tipo == 'cliente') {
                                                        // Cliente

                                                        cBox.find('[data-id="'+p.id+'"].border-dark').clone().appendTo(hBox);
                                                        cBox.find('[data-id="'+p.id+'"].border-dark').remove();
                                                    }
                                                });
                                            } else {
                                                // O quarto não existe. Deixa as pessoas no CLIENTEBOX.
                                            }
                                        }
                                    });

                                    // Ativa o Drag And Drop.
                                    ativaDragListas();
                            
                                    // Só mostra o modal depois de tudo preenchido.
                                    modal.modal('show');

                                    // FIM da execução!
                                    controleEtapas = 3;
                                    //console.log('Executado depois de '+tentativas+' tentativas.');
                                    clearInterval(cronFinal);
                                } else {
                                    // Adia a tentativa.
                                    tentativas++;
                                }

                        }, 500);
                    } else {
                        modal.modal('show');
                    }

                    // FIM
                } else if(res.lista.tipo == 'transporte') {
                    janListaTransporte(id);
                } else {
                    alerta('Tipo de lista desconhecida.','Interrompido!', 'warning');
                }
            } else {
                alerta(res.mensagem, '', 'warning');
            }
            
        }, 'json').
        fail(function(ev){nativePOSTFail(ev);});

        return true;
    }

    function janListaTransporte(id)
    {
        $.post('/roteiros/ver/'+roteiro.id+'/lista/'+id, function(res){
            if(debugEnabled == true) {console.log(res);}
            if(res.success) {
                if(res.lista.tipo == 'transporte') {
                    let modal = $('#janListaTransporte');
                    modal.data('lid', id);
                    let lista = res.lista;
                    let data = new Date(lista.data);
                    let controleEtapas = 0;

                    modal.find('.modal-header span').html('LISTA: '+lista.nome+' <span class="badge badge-secondary px-2 text-uppercase"><i class="fas fa-shuttle-van"></i> &nbsp; '+lista.tipo+'</span> '+
                    '<small>('+data.toLocaleDateString()+')</small>');
                    modal.find('[data-transportebox], [data-clientesbox]').html('');
                    

                    // Preenche o bloco de configurações da lista.
                    //console.log(res);
                    modal.find('[data-config] [name="nome"]').val(res.lista.nome); // Nome
                    modal.find('[data-config] [name="roteiro_id"]').val(roteiro.id); // Roteiro ID
                    modal.find('[data-config] [name="id"]').val(res.lista.id); // Roteiro ID
                    let config = JSON.parse(lista.instrucoes);
                    console.log(config);
                    
                    modal.find('[data-config] [name="clientesViagem"]').val(config.qtdClientesViagem); // Quantidade de clientes por viagem
                    
                    // Criança colo individual.
                    modal.find('[data-config] [name="criancaColoIndividual"]').prop('checked', config.criancaColoIndividual);

                    // Configura o formulário de configurações.
                    modal.find('[data-config] form').attr('action', 'roteiros/'+roteiro.id+'/lista/'+res.lista.id+'/configsalvar');
                    modal.find('[data-config]').css({display:'none'});

                    //Recupera coordenadores
                    $.post('/roteiros/ver/'+roteiro.id+'/coord', function(res){
                        if(res.success && res.coordenadores.length > 0) {

                            res.coordenadores.forEach(function(c){
                                modal.find('[data-clientesbox]').append('<div class="border border-danger bg-light text-danger font-weight-bold mb-2 px-2 py-1 rounded-sm cursor-drag arrastavel" '+
                                'style="z-index:1200; min-width: 150px; max-width:250px;" data-id="'+c.id+'">'+
                                c.nome+'</div>');
                            });
                            
                            controleEtapas++; // 0+1 = 1;

                            // Ativa o drag and drop do JQUERY UI
                            ativaDragListas();
                        } else if(res.success) {
                            alerta('Ainda não há coordenadores definidos.', '', 'info');
                        } else {
                            alerta(res.mensagem, 'Não deu para carregar a lista de coordenadores.','warning');
                            controleEtapas = false;
                        }
                        //console.log(res);
                    }, 'json');

                    // Recupera clientes.
                    $.post('/roteiros/ver/'+roteiro.id+'/clientes', function(res){
                        if(res.success && res.clientes.length > 0) {

                            res.clientes.forEach(function(c, keyC){
                                
                                // Verifica se criança de colo deve ser listado ou não.
                                if(config.criancaColoIndividual === true) {
                                    // Lista criança de colo
                                    if(c.colo == false) { // ADULTO
                                        modal.find('[data-clientesbox]').append('<div class="border border-dark bg-light mb-2 px-2 py-1 rounded-sm cursor-drag arrastavel" '+
                                        'style="z-index:1200; min-width: 150px; max-width:250px;" data-id="'+c.id+'">'+
                                        c.nome+'</div>');
                                    } else { // CRIANÇA COLO
                                        modal.find('[data-clientesbox]').append('<div class="border border-dark bg-light mb-2 px-2 py-1 rounded-sm cursor-drag arrastavel" '+
                                        'style="z-index:1200; min-width: 150px; max-width:250px;" data-id="'+c.id+'" data-resp="'+res.clientes[(keyC-1)].id+'">'+
                                        c.nome+' <span class="text-primary font-weight-bold">*</span></div>');
                                    }
                                    
                                } else {
                                    // Esconde criança de colo.
                                    if(c.colo == false) { // ADULTO
                                        modal.find('[data-clientesbox]').append('<div class="border border-dark bg-light mb-2 px-2 py-1 rounded-sm cursor-drag arrastavel" '+
                                        'style="z-index:1200; min-width: 150px; max-width:250px;" data-id="'+c.id+'">'+
                                        c.nome+'</div>');
                                    }
                                }
                                
                            });
                            
                            controleEtapas++; // 1+1 = 2;

                            // Ativa o drag and drop do JQUERY UI
                            ativaDragListas();
                        } else if(res.success) {
                            alerta('Ainda não há clientes na lista de passageiros.', '', 'info');
                        } else {
                            alerta(res.mensagem, 'Não deu para carregar a lista de passageiros.','warning');
                            controleEtapas = false;
                        }
                        //console.log(res);
                    }, 'json');

                    if(lista.dados.length > 0) {
                        // Há dados salvos.

                        let tentativas = 0;
                        let cronFinal = setInterval(function(){
                            if(tentativas >= 5) {
                                clearInterval(cronFinal);
                                alerta('O Dobbin demorou mais de 2,5s para responder e a operação foi interrompida. Se isso persistir, '+
                                'informe ao desenvolvedor o roteiro e a lista que apresentou esse comportamento.', 'ATENÇÃO!', 'warning');
                                return false;
                            }

                            if(controleEtapas == 2) {
                                let passageiros = modal.find('[data-clientesbox] div').length;
                                let qtdViagens = Math.ceil(passageiros / config.qtdClientesViagem) + 1;
                                let dados = JSON.parse(lista.dados);
                                //console.log(dados);

                                for(let i = 0; i < qtdViagens; i++) {
                                    modal.find('[data-transportebox]').append('<div class="card mb-2 mr-1" style="width:300px;" data-total="'+config.qtdClientesViagem+'">'+
                                    '<div class="card-header px-2 py-1 bg-dark text-white">VIAGEM #'+(i+1)+'</div>'+
                                    '<div class="card-body" style="min-height:100px;"></div>'+
                                    '</div>');

                                    // Lança os passageiros nas viagens.
                                    if(dados[i] != undefined) {
                                        dados[i].forEach(function(d){
                                            if(d.tipo == 'coord') {
                                                modal.find('[data-clientesbox] [data-id="'+d.id+'"].border-danger').clone().
                                                appendTo(modal.find('[data-transportebox] .card-body').last());

                                                modal.find('[data-clientesbox] [data-id="'+d.id+'"].border-danger').remove();
                                            } else if(d.tipo == 'cliente') {
                                                modal.find('[data-clientesbox] [data-id="'+d.id+'"].border-dark').clone().
                                                appendTo(modal.find('[data-transportebox] .card-body').last());

                                                modal.find('[data-clientesbox] [data-id="'+d.id+'"].border-dark').remove();
                                            }
                                            
                                        });
                                    }
                                }


                                // Ativa o drag and drop.
                                ativaDragListas();

                                // Só mostra o modal depois de tudo preenchido.
                                modal.modal('show');

                                // FIM da execução!
                                controleEtapas = 3;
                                //console.log('Executado depois de '+tentativas+' tentativas.');
                                clearInterval(cronFinal);
                            } else {
                                // Adia a tentativa.
                                tentativas++;
                            }
                        }, 500);
                        

                        modal.modal('show');
                    } else {
                        // Sem dados salvos.

                        console.log('SEM DADOS SALVOS');
                        
                        let tentativas = 0;
                        let cronFinal = setInterval(function(){
                            if(tentativas >= 5) {
                                clearInterval(cronFinal);
                                alerta('O Dobbin demorou mais de 2,5s para responder e a operação foi interrompida. Se isso persistir, '+
                                'informe ao desenvolvedor o roteiro e a lista que apresentou esse comportamento.', 'ATENÇÃO!', 'warning');
                                return false;
                            }

                            if(controleEtapas == 2) {
                                let passageiros = modal.find('[data-clientesbox] div').length;
                                let qtdViagens = Math.ceil(passageiros / config.qtdClientesViagem) + 1;

                                console.log(passageiros, config.qtdClientesViagem);

                                for(let i = 0; i < qtdViagens; i++) {
                                    modal.find('[data-transportebox]').append('<div class="card mb-2 mr-1" style="width:300px;" data-total="'+config.qtdClientesViagem+'">'+
                                    '<div class="card-header px-2 py-1 bg-dark text-white">VIAGEM #'+(i+1)+'</div>'+
                                    '<div class="card-body" style="min-height:100px;"></div>'+
                                    '</div>');
                                }

                                // Ativa o drag and drop.
                                ativaDragListas();

                                // Só mostra o modal depois de tudo preenchido.
                                modal.modal('show');

                                // FIM da execução!
                                controleEtapas = 3;
                                //console.log('Executado depois de '+tentativas+' tentativas.');
                                clearInterval(cronFinal);
                            } else {
                                // Adia a tentativa.
                                tentativas++;
                            }
                        }, 500);
                    }

                    return true;
                } else if(res.lista.tipo == 'hospedagem') {
                    janListaHospede(id);
                } else {
                    alerta('Tipo de lista desconhecida.','Interrompido!', 'warning');
                }
            } else {
                alerta(res.mensagem, '', 'warning');
            }
        },'json').
        fail(function(ev){nativePOSTFail(ev);});
    }

    $(document).ready(function(){
        //console.log(roteiro);
        @if($roteiro->tarifa == '')
        $('#modalRoteiroTarifas').modal('show');
        alerta('Informe as tarifas para este roteiro o mais breve possível.', 'Pendência...', 'light', 10000);
        @endif
        $('#roteiroTitle').html(roteiro.nome+ ' <small>('+Dobbin.formataData(new Date(roteiro.data_ini), true)+' a '+Dobbin.formataData(new Date(roteiro.data_fim), true)+')</small>');

        //SALVA LISTA DE HOSPEDAGEM
        $(document).on('click','#janListaHospede .modal-footer button.btn-success', function(ev){
            // Cria o JSON com os dados e salva.
            let hosped = {
                quartos: []
            };

            let modal = $('#janListaHospede');
            let hBox = modal.find('.modal-body [data-hospedagembox]');
            for(let i = 0; i < hBox.find('.card-body').length; i++) {
                let quarto = {
                    total: hBox.find('.card-body').eq(i).parent().data('total'),
                    pessoas: []
                }

                for(let k = 0; k < hBox.find('.card-body').eq(i).find('div').length; k++) {
                    // Busca clientes e coordenadores
                    let pessoa = {
                        id: hBox.find('.card-body').eq(i).find('div').eq(k).data('id'),
                        tipo: ''
                    }

                    if(hBox.find('.card-body').eq(i).find('div').eq(k).hasClass('border-danger')) {
                        // Coordenadores tem borda vermelha.
                        pessoa.tipo = 'coord';
                    } else {
                        // Clientes tem borda preta.
                        pessoa.tipo = 'cliente';
                    }

                    // Insere a pessoa na lista.
                    quarto.pessoas.push(pessoa);
                }

                if(quarto.pessoas.length > 0) {
                    // Insere o quarto na lista de quartos.
                    hosped.quartos.push(quarto);
                }
                
            }

            //console.log('Quartos da hospedagem:');
            //console.log(hosped);

            // Envia os dados para o servidor.
            $.post(PREFIX_POST+'roteiros/'+roteiro.id+'/lista/'+modal.data('lid')+'/salvar',{
                dados: hosped
            },function(res){
                if(res.success) {
                    alerta('Lista salva','','success');
                    getListas();
                } else {
                    alerta(res.mensagem, 'Houve problemas ao salvar.', 'warning');
                }
            },'json').
            fail(function(ev){nativePOSTFail(ev);});
        });
        
        $(document).on('click','#janListaTransporte .modal-footer button.btn-success', function(ev){
            // Cria o JSON com os dados e salva.
            let trans = [];

            let modal = $('#janListaTransporte');
            //console.log('ID da lista: '+modal.data('lid'));
            let tBox = modal.find('.modal-body [data-transportebox]');

            for(let i = 0; i < tBox.find('.card-body').length; i++) {
                if(tBox.find('.card-body').eq(i).find('div').length > 0) {
                    // Armazena os passageiros
                    let viagem = [];
                    for(let k = 0; k < tBox.find('.card-body').eq(i).find('div').length; k++) {
                        let pessoa = {
                            id: tBox.find('.card-body').eq(i).find('div').eq(k).data('id'),
                            tipo: ''
                        }

                        if(tBox.find('.card-body').eq(i).find('div').eq(k).hasClass('border-danger')) {
                            // Coordenadores tem borda vermelha.
                            pessoa.tipo = 'coord';
                        } else {
                            // Clientes tem borda preta.
                            pessoa.tipo = 'cliente';
                        }

                        // Adiciona a pessoa à viagem.
                        viagem.push(pessoa);
                        pessoa = undefined;
                    }

                    // Adiciona a viagem ao transporte.
                    trans.push(viagem);
                    viagem = undefined;
                } else {
                    // Não faz nada. Ignora viagem vazia.
                }
            }

            // Salva dados.
            //console.log(trans);

            // Envia os dados para o servidor.
            $.post(PREFIX_POST+'roteiros/'+roteiro.id+'/lista/'+modal.data('lid')+'/salvar',{
                dados: trans
            },function(res){
                if(res.success) {
                    alerta('Lista salva','','success');
                    getListas();
                } else {
                    alerta(res.mensagem, 'Houve problemas ao salvar.', 'warning');
                }
            },'json').
            fail(function(ev){nativePOSTFail(ev);});

        });

    });
</script>