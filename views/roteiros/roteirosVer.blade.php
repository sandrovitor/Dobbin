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
                                    <a href="#roteiros/editar/{{$roteiro->id}}" class="btn btn-sm btn-primary mr-2">Editar roteiro</a>
                                    <button type="button" class="btn btn-sm btn-danger mr-2" onclick="roteiroApagar(this)" data-id="{{$roteiro->id}}">Apagar roteiro</button>
                                    <button type="button" class="btn btn-sm btn-light mr-2 disabled" disabled>|</button>
                                    <button type="button" class="btn btn-sm btn-dark mr-2" onclick="$('.modal.show').modal('hide'); $('#janCriarCopiaRoteiro').modal('show')" data-id="{{$roteiro->id}}">Criar cópia do roteiro</button>
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
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card rounded-0">
                            <div class="card-header text-dark px-2 py-1">
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
                                <div class="row">
                                    <div class="col-12 d-flex">
                                        <!-- Poltronas vendidas -->
                                        <div class="progress mr-1 rounded rounded-lg" title="Poltronas vendidas" data-toggle="tooltip" style="height:10px; width:10%;">
                                            <div class="progress-bar" style="height:10px; width:100%;"></div>
                                        </div>
                                        <!-- Poltronas reservadas -->
                                        <div class="progress mr-1 rounded rounded-lg" title="Poltronas reservadas" data-toggle="tooltip" style="height:10px; width:0%;">
                                            <div class="progress-bar bg-secondary" style="height:10px; width:100%;"></div>
                                        </div>
                                        <!-- Poltronas vagas -->
                                        <div class="progress rounded rounded-lg" title="Poltronas livres para venda" data-toggle="tooltip" style="height:10px; width:90%;">
                                            <div class="progress-bar bg-success" style="height:10px; width:100%;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <kbd>Em desenvolvimento...</kbd>
                                    <pre>Essa seção só estará disponível quando esse aviso sumir.</pre>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card rounded-0">
                                    <div class="card-header text-dark px-2 py-1">
                                        CLIENTES
                                    </div>
                                    <div class="card-body pt-3 pb-2 px-2">
                                        <div class="row">
                                            <div class="col-12">
                                                
                                                <div class="mt-3">
                                                    <kbd>Em desenvolvimento...</kbd>
                                                    <pre>Essa seção só estará disponível quando esse aviso sumir.</pre>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card rounded-0">
                                    <div class="card-header text-dark px-2 py-1">
                                        COORDENADORES
                                    </div>
                                    <div class="card-body pt-3 pb-2 px-2">
                                        <div class="row">
                                            <div class="col-12">
                                                <button type="button" class="btn btn-sm btn-primary mr-2" onclick="janCoordenadorSelect(this)" data-id="{{$roteiro->id}}">Adicionar coordenador</button>
                                                <div id="listaCoord" class="mt-3">
                                                    <ul class="list-group">
                                                    @foreach($roteiro->coordenador as $coord)
                                                        <li class="list-group-item d-flex justify-content-between align-items-center py-2 pl-3 pr-2">
                                                        {{$coord['nome']}}
                                                        <button type="button" class="btn btn-sm btn-light" data-id="{{$coord['id']}}" data-rid="{{$roteiro->id}}" onclick="roteiroRemoveCoordenador(this)"><i class="fas fa-times fa-fw"></i></button>
                                                        </li>
                                                    @endforeach
                                                    </ul>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
                        <div class="card rounded-0">
                            <div class="card-header text-dark px-2 py-1">
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
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
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
                    <div class="col-12">
                        <form method="post">
                            <table class="table table-bordered table-sm">
                                <tbody>
                                    <tr style="display:none;" data-example>
                                        <td>
                                            <label class="small">Nome da tarifa</label>
                                            <input type="text" class="form-control form-control-sm form-control-solid" name="nome_tarifa" maxlength="15" placeholder="CASADINHA">
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
                                        </td>
                                        <td style="vertical-align:middle;">
                                            <button type="button" class="btn btn-block btn-sm btn-danger" onclick="$(this).parents('tr').remove();"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                    @if($roteiro->tarifa == '')
                                    <tr>
                                        <td>
                                            <label class="small">Nome da tarifa</label>
                                            <input type="text" class="form-control form-control-sm form-control-solid" name="nome_tarifa" maxlength="15" value="Integral" placeholder="CASADINHA" required>
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
                                        </td>
                                        <td></td>
                                    </tr>
                                    @else
                                    @foreach($roteiro->tarifa as $t)
                                    <tr>
                                        <td>
                                            <label class="small">Nome da tarifa</label>
                                            <input type="text" class="form-control form-control-sm form-control-solid" name="nome_tarifa" maxlength="15" value="{{$t->nome}}" placeholder="CASADINHA" required>
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
                                        </td>
                                        <td></td>
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
                                <td>ADULTOS: {{$t->distr->adultos}}<br>CRIANÇAS: {{$t->distr->criancas}}</td>
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
    }

    function addTarifaRoteiro(sender)
    {
        if($(sender).siblings('table').find('tr[data-example]').length == 0) {
            alerta('Essa página foi atualizada, pois houve alguma alteração indevida na página.','Espera...', 'info');
            loadLanding(location.hash);
            return false;
        }

        if($(sender).siblings('table').find('tr:not([data-example])').length >= 6) {
            alerta('Limite de tarifas atingido!', 'Ops...', 'info');
            return false;
        }

        $(sender).siblings('table').find('tr[data-example]').clone().appendTo($(sender).siblings('table'));
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

        // Varre cada linha para preencher array de tarifas
        for(let i = 0; i < form.find('tr:not([data-example])').length; i++) {
            tarifas.push({
                nome: form.find('tr:not([data-example])').eq(i).find('[name="nome_tarifa"]').val(),
                valor: Dobbin.converteRealEmCentavo(form.find('tr:not([data-example])').eq(i).find('[name="valor"]').val()),
                distr: {
                    adultos: parseInt(form.find('tr:not([data-example])').eq(i).find('[name="qtd_adultos"]').val()),
                    criancas: parseInt(form.find('tr:not([data-example])').eq(i).find('[name="qtd_criancas"]').val())
                },
                qtd: parseInt(form.find('tr:not([data-example])').eq(i).find('[name="qtd_adultos"]').val()) + parseInt(form.find('tr:not([data-example])').eq(i).find('[name="qtd_criancas"]').val())
            });
            form.find('tr:not([data-example])').eq(i)
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

    $(document).ready(function(){
        //console.log(roteiro);
        @if($roteiro->tarifa == '')
        $('#modalRoteiroTarifas').modal('show');
        alerta('Informe as tarifas para este roteiro o mais breve possível.', 'Pendência...', 'light', 10000);
        @endif
        $('#roteiroTitle').html(roteiro.nome+ ' <small>('+Dobbin.formataData(new Date(roteiro.data_ini), true)+' a '+Dobbin.formataData(new Date(roteiro.data_fim), true)+')</small>');
    });
</script>