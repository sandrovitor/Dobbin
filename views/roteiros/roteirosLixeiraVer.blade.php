<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-8">
                        <!-- COLUNA 1 -->
                        <div class="card rounded-0">
                            <div class="card-header text-dark px-2 py-1">
                                GERAL
                            </div>
                            <div class="card-body pt-3 pb-0 px-2">
                                <div class="mb-3">
                                    <button type="button" class="btn btn-sm btn-success mr-2" onclick="roteiroRestaurar({{$roteiro->id}})" data-id="{{$roteiro->id}}">Restaurar roteiro</button>
                                </div>
                            @php
                                $partida = new DateTime($roteiro->data_ini);
                                $retorno = new DateTime($roteiro->data_fim);
                                $criado = new DateTime($roteiro->criado_em);
                                $atualizado = new DateTime($roteiro->atualizado_em);
                            @endphp
                                <table class="table table-bordered table-sm">
                                    <tbody>
                                        <tr>
                                            <td class="px-3 py-1" colspan="2"><strong>Roteiro:</strong> <span class="ml-2">{{$roteiro->nome}}</span></td>
                                        </tr>
                                        <tr>
                                            <td class="px-3 py-1"><strong>Partida:</strong> <span class="ml-2">{{$partida->format('d/m/Y')}}</span></td>
                                            <td class="px-3 py-1"><strong>Retorno:</strong> <span class="ml-2">{{$retorno->format('d/m/Y')}}</span></td>
                                        </tr>
                                        <tr>
                                            <td class="px-3 py-2" colspan="2"></td>
                                        </tr>
                                        <tr>
                                            <td class="px-3 py-1" colspan="2"><strong>Total de poltronas:</strong> <span class="ml-2">{{(int)$roteiro->passagens + (int)$roteiro->qtd_coordenador}}</span></td>
                                        </tr>
                                        <tr>
                                            <td class="px-3 py-1"><strong>Clientes (pagantes):</strong> <span class="ml-2">{{(int)$roteiro->passagens}}</span></td>
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
                                        
                                        <tr class="table-danger">
                                            <td class="px-3 py-1"><strong>Deletado por:</strong> <span class="ml-2">{!!$deletado_por!!}</span></td>
                                            <td class="px-3 py-1"><strong>Deletado em:</strong> <span class="ml-2">{{$atualizado->format('d/m/Y H:i:s')}}</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                    </div>
                    <div class="col-lg-4">
                        <!-- COLUNA 2 -->
                        <div class="card rounded-0">
                            <div class="card-header text-dark px-2 py-1">
                                DESPESAS
                            </div>
                            <div class="card-body pt-3 pb-2 px-2">
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
                                            <td colspan="2"></td>
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

                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

<!-- MODAL -->




<script>
    var roteiro = JSON.parse('{!!addslashes(json_encode($roteiro))!!}');
    var ValorMinimoRoteiro = {{ceil(($despesasTotal + $roteiro->lucro_previsto->lucroRateio) / $roteiro->qtd_rateio)}};


    $(document).ready(function(){
        console.log(roteiro);
        @if($roteiro->tarifa == '')
        $('#modalRoteiroTarifas').modal('show');
        alerta('Informe as tarifas para este roteiro o mais breve possível.', 'Pendência...', 'light', 10000);
        @endif
        $('#roteiroTitle').html(roteiro.nome+ ' <small>('+Dobbin.formataData(new Date(roteiro.data_ini))+' a '+Dobbin.formataData(new Date(roteiro.data_fim))+')</small>');
    });
</script>