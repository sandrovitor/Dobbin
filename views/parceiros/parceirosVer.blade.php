<div class="row">
    <div class="col-12 col-lg-10 col-xl-10 mx-auto">
        <div class="card">
            <div class="card-header">
                Parceiro: <span class="text-uppercase">{!!isset($parceiro['geral']->nome_fantasia) ? ($parceiro['geral']->nome_fantasia == '' ? $parceiro['geral']->razao_social : $parceiro['geral']->nome_fantasia .' <i class="small">('.$parceiro['geral']->razao_social.')</i>' ) : $parceiro['geral']->razao_social!!}</span>
                
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 mb-2">
                        <div class="border border-secondary mb-3 p-2">
                            <h5 class="mb-3 pb-2 font-weight-bold border border-top-0 border-left-0 border-right-0 d-flex justify-content-between">
                                <span> 
                                    <i class="fas fa-file-alt mr-2"></i> DADOS GERAIS
                                </span>
                                <span><button type="button" class="toggleMinMax btn-light rounded-0 border" data-target="#dadosgerais"><i class="fas fa-minus"></i></button></span>
                            </h5>
                            <div class="row" id="dadosgerais">
                                <div class="col-12">
                                    <div class="border py-2 px-2 mb-3">
                                        <button type="button" class="py-0 btn btn-sm btn-secondary"><i class="fas fa-pen"></i> Editar</button>
                                        <button type="button" class="py-0 btn btn-sm btn-danger"><i class="fas fa-trash"></i> Excluir parceiro</button>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <dl>
                                        <dt>Razão Social / Nome completo</dt>
                                        <dd class="mb-2 ml-3">{{$parceiro['geral']->razao_social}}</dd>
                                        <dt>Nome Fantasia</dt>
                                        <dd class="mb-2 ml-3">{{$parceiro['geral']->nome_fantasia ? $parceiro['geral']->nome_fantasia : '-'}}</dd>
                                        <dt>Documento</dt>
                                        <dd class="mb-2 ml-3">{{$parceiro['geral']->doc_tipo}}: {{$parceiro['geral']->doc_numero}}</dd>
                                    </dl>
                                </div>
                                <div class="col-12 col-md-6">
                                    <dl>
                                        <dt>Endereço</dt>
                                        <dd class="mb-2 ml-3">{{$parceiro['geral']->endereco}}</dd>
                                        <dt>Cidade / Estado</dt>
                                        <dd class="mb-2 ml-3">
                                            {{$parceiro['geral']->cidade ? $parceiro['geral']->cidade : '-'}} /
                                            {{$parceiro['geral']->estado ? $parceiro['geral']->estado : '-'}}
                                        </dd>
                                        <dt>Responsável (empresa)</dt>
                                        <dd class="mb-2 ml-3">
                                            {{$parceiro['geral']->responsavel ? $parceiro['geral']->responsavel : '-'}}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    <div class="col-12 mb-2">
                        <div class="border border-secondary mb-3 p-2">
                            <h5 class="mb-3 pb-2 font-weight-bold border border-top-0 border-left-0 border-right-0 d-flex justify-content-between">
                                <span><i class="fas fa-landmark mr-2"></i> DADOS FINANCEIROS</span>
                                <span><button type="button" class="toggleMinMax btn-light rounded-0 border" data-target="#dadosfinanceiros"><i class="fas fa-minus"></i></button></span>
                            </h5>
                                <div class="row" id="dadosfinanceiros">
                                    <div class="col-12 mb-2">
                                        <div class="border py-2 px-2 mb-3">
                                            <button type="button" class="py-0 btn btn-sm btn-primary" onclick="janParcFinanceiroAdd()"><i class="fas fa-plus"></i> Adicionar</button>
                                        </div>
                                    </div>
                                    
                                    <div class="col-12">
                                    @if(count($parceiro['financeiro']) == 0)
                                        <h6>Sem dadops financeiros do parceiro.</h6>
                                    @elseif(count($parceiro['financeiro']) > 0)
                                        @php
                                        $conta = 0;
                                        @endphp
                                        @foreach($parceiro['financeiro'] as $pf)
                                        @php
                                            $conta++;
                                            if(!empty($pf)) {
                                                $banco = $bancos[array_search($pf->banco, array_column($bancos, 'codigo'))];
                                            } else {
                                                $banco = (object)['codigo' => '-', 'banco' => '-'];
                                            }
                                        @endphp
                                        <div class="border bloco-acord">
                                            <div class="acord-header bg-light p-2 d-flex justify-content-between" style="cursor:pointer;">
                                                <h6 class="font-weight-bold text-uppercase my-1 text-primary">
                                                    FINANCEIRO {{$conta}}
                                                    <small class="ml-2 text-dark">(Banco {{$banco->codigo .' - '. $banco->banco}})</small>
                                                </h6>
                                                <button class="btn btn-transparent btn-sm text-dark"><i class="fas fa-angle-down"></i></button>
                                            </div>
                                            <div class="acord-body p-2 py-3 pt-0 border border-secondary border-bottom-0 border-left-0 border-right-0" style="display:none;">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="border py-2 px-2 mb-3">
                                                            <button type="button" class="py-0 btn btn-sm btn-secondary" onclick="parcFinanceiroEdita({{$pf->id}}, {{$parceiro['geral']->id}})"><i class="fas fa-pen"></i> Editar</button>
                                                            <button type="button" class="py-0 btn btn-sm btn-danger" onclick="parcFinanceiroDel({{$pf->id}}, {{$parceiro['geral']->id}})"><i class="fas fa-trash"></i> Excluir</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12 col-md-6">
                                                        <dl class="p-1">
                                                            <dt>Banco</dt>
                                                            <dd class="mb-2 ml-3">{{$banco->codigo .' - '. $banco->banco}}</dd>
                                                            <dt>Agência</dt>
                                                            <dd class="mb-2 ml-3">{{$pf->agencia}}-{{$pf->agencia_dv}}</dd>
                                                            <dt>Conta</dt>
                                                            <dd class="mb-2 ml-3">{{$pf->conta}}-{{$pf->conta_dv}}</dd>
                                                        </dl>
                                                    </div>
                                                    <div class="col-12 col-md-6">
                                                        <dl class="p-1">
                                                            <dt>Tipo de conta</dt>
                                                            <dd class="mb-2 ml-3">{{$pf->tipo_conta}}</dd>
                                                            <dt>Favorecido</dt>
                                                            <dd class="mb-2 ml-3">{{$pf->favorecido}}</dd>
                                                            <dt>Observação</dt>
                                                            <dd class="mb-2 ml-3">{{$pf->obs_financeiro}}</dd>
                                                        </dl>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    @endif
                                    </div>
                                </div>
                                

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 mb-2">
                        <div class="border border-secondary mb-3 p-2">
                            <h5 class="mb-3 pb-2 font-weight-bold border border-top-0 border-left-0 border-right-0 d-flex justify-content-between">
                                <span><i class="fas fa-glass-martini-alt mr-2"></i> SERVIÇOS</span>
                                <span><button type="button" class="toggleMinMax btn-light rounded-0 border" data-target="#servicos"><i class="fas fa-minus"></i></button></span>
                            </h5>
                            <div class="row" id="servicos">
                                <div class="col-12 mb-2">
                                    <div class="border py-2 px-2 mb-3">
                                        <button type="button" class="py-0 btn btn-sm btn-primary" onclick="janServicoAdd()"><i class="fas fa-plus"></i> Adicionar</button>
                                    </div>
                                </div>
                                <div class="col-12">
                                @if(empty($parceiro['servico']))
                                    Não há serviços cadastrados
                                @else
                                    @foreach($parceiro['servico'] as $s)
                                    @php
                                        if($s->tarifas != '') {$tarifas = json_decode($s->tarifas);} else { $tarifas = array();}
                                        if($s->benef_gratis != '') {$benef_gratis = json_decode($s->benef_gratis);} else { $benef_gratis = array();}
                                        if($s->benef_pago != '') {$benef_pago = json_decode($s->benef_pago);} else { $benef_pago = array();}
                                    @endphp
                                    <div class="border bloco-acord">
                                        <div class="acord-header bg-light p-2 d-flex justify-content-between" style="cursor:pointer;">
                                            <h6 class="font-weight-bold text-uppercase my-1 text-primary">
                                                {{$s->categoria}} {{$s->tipo != '' ? '- '.$s->tipo : ''}}
                                                <small class="ml-2 text-dark">({{$s->cidade.'/'.$s->estado}})</small>
                                            </h6>
                                            <button class="btn btn-transparent btn-sm text-dark"><i class="fas fa-angle-down"></i></button>
                                        </div>
                                        <div class="acord-body p-2 py-3 pt-0 border border-secondary border-bottom-0 border-left-0 border-right-0" style="display:none">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="border py-2 px-2 mb-3">
                                                        <button type="button" class="py-0 btn btn-sm btn-secondary" onclick="parcServicoEdita({{$s->id}}, {{$parceiro['geral']->id}})"><i class="fas fa-pen"></i> Editar</button>
                                                        <button type="button" class="py-0 btn btn-sm btn-danger" onclick="parcServicoDel({{$s->id}}, {{$parceiro['geral']->id}})"><i class="fas fa-trash"></i> Excluir</button>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-12 col-md-4 mb-2 mb-md-0">
                                                    <h6 class="font-weight-bold">Tarifas</h6>

                                                    <ul>
                                                    @foreach($tarifas as $t)
                                                        @switch($t->idade)
                                                            @case('0-5')
                                                            <li><strong class="mr-2">0 - 5 ANOS:</strong> R${{$par->converteCentavoParaReal($t->valor)}}</li>
                                                            @break;

                                                            @case('6-12')
                                                            <li><strong class="mr-2">6 - 12 ANOS:</strong> R${{$par->converteCentavoParaReal($t->valor)}}</li>
                                                            @break;

                                                            @case('ADULTO')
                                                            <li><strong class="mr-2">ADULTO:</strong> R${{$par->converteCentavoParaReal($t->valor)}}</li>
                                                            @break;

                                                            @case('CASAL')
                                                            <li><strong class="mr-2">CASAL:</strong> R${{$par->converteCentavoParaReal($t->valor)}}</li>
                                                            @break;

                                                            @case('60+')
                                                            <li><strong class="mr-2">60+ ANOS:</strong> R${{$par->converteCentavoParaReal($t->valor)}}</li>
                                                            @break;

                                                            @default
                                                            <li><strong class="mr-2">{{$t->idade}}:</strong> R${{$par->converteCentavoParaReal($t->valor)}}</li>
                                                            @break;
                                                            
                                                        @endswitch
                                                    @endforeach
                                                    </ul>
                                                    
                                                </div>
                                                <div class="col-12 col-md-4 mb-2 mb-md-0">
                                                    <h6 class="font-weight-bold">Benefícios Grátis</h6>
                                                    
                                                    <ul>
                                                    @foreach($benef_gratis as $b)
                                                        <li>{{$b}}</li>
                                                    @endforeach
                                                    </ul>
                                                </div>
                                                <div class="col-12 col-md-4 mb-2 mb-md-0">
                                                    <h6 class="font-weight-bold">Benefícios Pagos</h6>
                                                    
                                                    <ul>
                                                    @foreach($benef_pago as $b)
                                                        <li><strong class="mr-2">{{$b->nome}}:</strong> R${{$par->converteCentavoParaReal($b->valor)}}</li>
                                                    @endforeach
                                                    </ul>
                                                </div>
                                                <div class="col-12">
                                                    <h6 class="font-weight-bold">OBSERVAÇÕES</h6>
                                                    <textarea rows="3" class="form-control form-control-sm" disabled>{{$s->obs_servico}}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12 mb-2">
                        <div class="border border-secondary mb-3 p-2">
                            <h5 class="mb-3 pb-2 font-weight-bold border border-top-0 border-left-0 border-right-0 d-flex justify-content-between">
                                <span><i class="far fa-folder-open mr-2"></i> HISTÓRICO DE NEGOCIAÇÕES</span>
                                <span><button type="button" class="toggleMinMax btn-light rounded-0 border" data-target="#historico_negoc"><i class="fas fa-minus"></i></button></span>
                            </h5>
                            <div class="row" id="historico_negoc">
                                <div class="col-12 mb-2">
                                    <div class="border py-2 px-2 mb-3">
                                        <button type="button" class="py-0 btn btn-sm btn-primary" onclick="$('#modalParceiroHistoricoAdd').modal('show');"><i class="fas fa-plus"></i> Adicionar entrada no registro</button>
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                @php
                                    $historico = $par->getHistorico(20, 0);
                                @endphp
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
                                            @if(empty($historico))
                                                <tr><td colspan="4" class="text-center py-2">Nenhum registro no histórico.</td></tr>
                                            @else
                                            @foreach($historico as $h)

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
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-sm btn-primary" title="Editar registro." data-pid="{{$parceiro['geral']->id}}" data-hid="{{$h->id}}" dobbin-btn-editahistorico><i class="fas fa-pen"></i></button>
                                                        <button type="button" class="btn btn-sm btn-danger" title="Excluir registro." data-pid="{{$parceiro['geral']->id}}" data-hid="{{$h->id}}" onclick="parcHistoricoDelete(this)"><i class="fas fa-trash"></i></button>
                                                    </div>
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
                                    
                                    @if(count($historico) == 20)
                                    <div class="text-center py-1">
                                        <!--<button type="button" class="btn btn-sm btn-primary loadMore" data-pid="{{$parceiro['geral']->id}}" data-start="20" data-qtd="20">Carregar mais</button>-->
                                        <button type="button" class="btn btn-sm btn-primary loadMore" data-pid="{{$parceiro['geral']->id}}" data-start="0" data-qtd="20">Carregar mais</button>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            
        </div>
    </div>
</div>  

<!-- MODAIS -->
<div class="modal fade" id="modalParceiroServicoAdd">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-2 px-3 font-weight-bold">
                Adicionar serviço
                <button type="button" class="btn btn-sm btn-danger fechar" data-dismiss="modal"><strong>&times;</strong></button>
            </div>
            <div class="modal-body">
                <form onsubmit="return parcServicoNovo();">
                    <div class="form-group">
                        <label class="small font-weight-bold">Categoria do serviço</label>
                        <select class="form-control form-control-solid form-control-sm" name="categoria">
                            <option value="Atração">Atração</option>
                            <option value="Guia">Guia</option>
                            <option value="Hospedagem">Hospedagem</option>
                            <option value="Parceria">Parceria (outros)</option>
                            <option value="Transporte">Transporte</option>
                        </select>
                        
                    </div>
                    <div class="form-group" style="display:none;">
                        <label class="small font-weight-bold">Tipo de Hospedagem</label>
                        <select class="form-control form-control-solid form-control-sm" name="tipoHospedagem">
                            <option value="Hotel">Hotel</option>
                            <option value="Pousada">Pousada</option>
                            <option value="Resort">Resort</option>
                        </select>
                    </div>
                    <div class="form-group row" style="display:none;">
                        <div class="col-6">
                            <label class="small font-weight-bold">Tipo de Transporte</label>
                            <select class="form-control form-control-solid form-control-sm" name="tipoTransporte">
                                <option value="Van">Van</option>
                                <option value="Ônibus Executivo">Ônibus Executivo</option>
                                <option value="Ônibus Semi-Leito">Ônibus Semi-Leito</option>
                                <option value="Ônibus Leito Total">Ônibus Leito Total</option>
                                <option value="Escuna">Escuna</option>
                                <option value="Buggy">Buggy</option>
                                <option value="Lancha">Lancha</option>
                                <option value="Avião">Avião</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="small font-weight-bold">Qtd Passageiros</label>
                            <input type="number" class="form-control form-control-solid form-control-sm" name="passageiros">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-8">
                            <label class="small font-weight-bold">Cidade</label>
                            <input type="text" name="cidade" class="form-control form-control-solid form-control-sm" value="{{$parceiro['geral']->cidade}}" required title="Nome da cidade do serviço.">
                        </div>
                        <div class="col-4">
                            <label class="small font-weight-bold">Estado</label>
                            <select name="estado" class="form-control form-control-solid form-control-sm">
                                <option value="">Escolha:</option>
                                <option value="AC">Acre</option>
                                <option value="AL">Alagoas</option>
                                <option value="AP">Amapá</option>
                                <option value="AM">Amazonas</option>
                                <option value="BA" selected>Bahia</option>
                                <option value="CE">Ceará</option>
                                <option value="DF">Distrito Federal</option>
                                <option value="ES">Espírito Santo</option>
                                <option value="GO">Goiás</option>
                                <option value="MA">Maranhão</option>
                                <option value="MT">Mato Grosso</option>
                                <option value="MS">Mato Grosso do Sul</option>
                                <option value="MG">Minas Gerais</option>
                                <option value="PA">Pará</option>
                                <option value="PB">Paraíba</option>
                                <option value="PR">Paraná</option>
                                <option value="PE">Pernambuco</option>
                                <option value="PI">Piauí</option>
                                <option value="RJ">Rio de Janeiro</option>
                                <option value="RN">Rio Grande do Norte</option>
                                <option value="RS">Rio Grande do Sul</option>
                                <option value="RO">Rondônia</option>
                                <option value="RR">Roraima</option>
                                <option value="SC">Santa Catarina</option>
                                <option value="SP">São Paulo</option>
                                <option value="SE">Sergipe</option>
                                <option value="TO">Tocantins</option>
                            </select>
                        </div>
                    </div>
                    <hr>
                    <div class="p-2 border mb-3 tarifario">
                        <h6 class="small font-weight-bold mt-n3 text-uppercase"><span class="bg-white px-2">Tarifário</span></h6>
                        <div data-servico-tarifa class="d-flex mb-2">
                            <select class="form-control form-control-solid form-control-sm mr-1">
                                <option value="0-5">0 - 5 ANOS</option>
                                <option value="6-12">6 - 12 ANOS</option>
                                <option value="ADULTO">ADULTO</option>
                                <option value="CASAL">CASAL</option>
                                <option value="60+">60+ ANOS</option>
                                <option value="OUTROS">OUTROS</option>
                            </select>
                                
                            <div class="input-group input-group-sm mr-1">
                                <div class="input-group-prepend">
                                    <span class="input-group-text form-control-solid">R$</span>
                                </div>
                                <input type="text" class="form-control form-control-sm form-control-solid" placeholder="Ex.: 5000,00" name="valor" dobbin-validate-valor>
                                
                            </div>
                            <button type="button" class="btn btn-sm btn-danger" onclick="parcDelCampoTarifario(this)"><i class="fas fa-trash"></i></button>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary mt-2" onclick="parcAddCampoTarifario(this)"><i class="fas fa-plus"></i> Adicionar campo tarifa</button>
                    </div>
                    <div class="p-2 border mb-3">
                        <h6 class="small font-weight-bold mt-n3 text-uppercase mb-3"><span class="bg-white px-2">Benefícios Gratuitos</span></h6>
                        <div class="beneficios mb-2">

                            <div class="beneficiosHospedagem pl-2 pb-3 mb-3 border border-top-0 border-left-0 border-right-0">
                                <label class="small font-weight-bold ml-n2">Benefícios de Hospedagem</label><br>
                                <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" value="Café da manhã incluso"> Café da manhã incluso
                                    </label>
                                </div>
                                <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" value="Piscina"> Piscina
                                    </label>
                                </div>
                            </div>

                            <div class="beneficiosTransporte pl-2 pb-3 mb-3 border border-top-0 border-left-0 border-right-0">
                                <label class="small font-weight-bold ml-n2">Benefícios de Transporte</label><br>
                                <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" value="TV/DVD/SOM"> TV/DVD/SOM
                                    </label>
                                </div>
                                <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" value="Ar-condicionado"> Ar-condicionado
                                    </label>
                                </div>
                                <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" value="Banheiro"> Banheiro
                                    </label>
                                </div>
                                <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" value="Água a Bordo"> Água a Bordo
                                    </label>
                                </div>
                                <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" value="Manta"> Manta
                                    </label>
                                </div>
                                <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" value="Service Board"> Service Board
                                    </label>
                                </div>
                            </div>
                            
                        
                            <div class="beneficiosOutros pl-2 pb-3">
                                <label class="small font-weight-bold ml-n2">Outros Benefícios Inclusos</label><br>
                            </div>
                        </div>
                        <div class="d-flex w-75 mt-2">
                            <input type="text" class="form-control form-control-solid form-control-sm mr-1" placeholder="Nome do benefício" maxlength="25">
                            <button type="button" class="btn btn-sm btn-primary" onclick="parcInserirBeneficio(this, $(this).parent().parent().find('.beneficiosOutros'))">Inserir</button>
                        </div>
                    </div>
                    <div class="p-2 border mb-3" id="">
                        <h6 class="small font-weight-bold mt-n3 text-uppercase mb-3"><span class="bg-white px-2">Benefícios Pagos</span></h6>
                        <div class="beneficiosPagos">
                            <div class="beneficio d-flex mb-2">
                                <input type="text" class="form-control form-control-sm form-control-solid mr-1" placeholder="Nome" data-beneficio-nome>
                                <div class="input-group input-group-sm mr-1">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text form-control-solid">R$</span>
                                    </div>
                                    <input type="text" class="form-control form-control-sm form-control-solid" placeholder="Ex.: 5000,00" dobbin-validate-valor data-beneficio-valor>
                                    
                                </div>
                                <button class="btn btn-sm btn-danger" type="button" onclick="parcDelBeneficioPago(this);"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>

                        <button type="button" class="btn btn-sm btn-primary mt-2" onclick="parcAddBeneficioPago($(this).prev('.beneficiosPagos'))">Adicionar campo beneficio</button>
                    </div>
                    <div class="p-2 border mb-3" id="">
                        <h6 class="small font-weight-bold mt-n3 text-uppercase mb-3"><span class="bg-white px-2">OBSERVAÇÕES</span></h6>
                        <textarea rows="4" class="form-control-sm form-control form-control-solid" maxlength="300" name="obs"></textarea>
                    </div>
                    <div class="form-group">
                        <input type="hidden" name="PID" value="{{$parceiro['geral']->id}}">
                    </div>
                    
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-sm" onclick="parcServicoNovo()">Salvar</button>
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalParceiroServicoEdita">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-2 px-3 font-weight-bold">
                Editar serviço
                <button type="button" class="btn btn-sm btn-danger fechar" data-dismiss="modal"><strong>&times;</strong></button>
            </div>
            <div class="modal-body">
                <form onsubmit="return parcServicoSalvar();">
                    <div class="form-group">
                        <label class="small font-weight-bold">Categoria do serviço</label>
                        <select class="form-control form-control-solid form-control-sm" name="categoria">
                            <option value="Atração">Atração</option>
                            <option value="Guia">Guia</option>
                            <option value="Hospedagem">Hospedagem</option>
                            <option value="Parceria">Parceria (outros)</option>
                            <option value="Transporte">Transporte</option>
                        </select>
                        
                    </div>
                    <div class="form-group" style="display:none;">
                        <label class="small font-weight-bold">Tipo de Hospedagem</label>
                        <select class="form-control form-control-solid form-control-sm" name="tipoHospedagem">
                            <option value="Hotel">Hotel</option>
                            <option value="Pousada">Pousada</option>
                            <option value="Resort">Resort</option>
                        </select>
                    </div>
                    <div class="form-group row" style="display:none;">
                        <div class="col-6">
                            <label class="small font-weight-bold">Tipo de Transporte</label>
                            <select class="form-control form-control-solid form-control-sm" name="tipoTransporte">
                                <option value="Van">Van</option>
                                <option value="Ônibus Executivo">Ônibus Executivo</option>
                                <option value="Ônibus Semi-Leito">Ônibus Semi-Leito</option>
                                <option value="Ônibus Leito Total">Ônibus Leito Total</option>
                                <option value="Escuna">Escuna</option>
                                <option value="Buggy">Buggy</option>
                                <option value="Lancha">Lancha</option>
                                <option value="Avião">Avião</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="small font-weight-bold">Qtd Passageiros</label>
                            <input type="number" class="form-control form-control-solid form-control-sm" name="passageiros">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-8">
                            <label class="small font-weight-bold">Cidade</label>
                            <input type="text" name="cidade" class="form-control form-control-solid form-control-sm" value="{{$parceiro['geral']->cidade}}" required title="Nome da cidade do serviço.">
                        </div>
                        <div class="col-4">
                            <label class="small font-weight-bold">Estado</label>
                            <select name="estado" class="form-control form-control-solid form-control-sm">
                                <option value="">Escolha:</option>
                                <option value="AC">Acre</option>
                                <option value="AL">Alagoas</option>
                                <option value="AP">Amapá</option>
                                <option value="AM">Amazonas</option>
                                <option value="BA" selected>Bahia</option>
                                <option value="CE">Ceará</option>
                                <option value="DF">Distrito Federal</option>
                                <option value="ES">Espírito Santo</option>
                                <option value="GO">Goiás</option>
                                <option value="MA">Maranhão</option>
                                <option value="MT">Mato Grosso</option>
                                <option value="MS">Mato Grosso do Sul</option>
                                <option value="MG">Minas Gerais</option>
                                <option value="PA">Pará</option>
                                <option value="PB">Paraíba</option>
                                <option value="PR">Paraná</option>
                                <option value="PE">Pernambuco</option>
                                <option value="PI">Piauí</option>
                                <option value="RJ">Rio de Janeiro</option>
                                <option value="RN">Rio Grande do Norte</option>
                                <option value="RS">Rio Grande do Sul</option>
                                <option value="RO">Rondônia</option>
                                <option value="RR">Roraima</option>
                                <option value="SC">Santa Catarina</option>
                                <option value="SP">São Paulo</option>
                                <option value="SE">Sergipe</option>
                                <option value="TO">Tocantins</option>
                            </select>
                        </div>
                    </div>
                    <hr>
                    <div class="p-2 border mb-3 tarifario">
                        <h6 class="small font-weight-bold mt-n3 text-uppercase"><span class="bg-white px-2">Tarifário</span></h6>
                        <div data-servico-tarifa class="d-flex mb-2">
                            <select class="form-control form-control-solid form-control-sm mr-1">
                                <option value="0-5">0 - 5 ANOS</option>
                                <option value="6-12">6 - 12 ANOS</option>
                                <option value="ADULTO">ADULTO</option>
                                <option value="CASAL">CASAL</option>
                                <option value="60+">60+ ANOS</option>
                                <option value="OUTROS">OUTROS</option>
                            </select>
                                
                            <div class="input-group input-group-sm mr-1">
                                <div class="input-group-prepend">
                                    <span class="input-group-text form-control-solid">R$</span>
                                </div>
                                <input type="text" class="form-control form-control-sm form-control-solid" placeholder="Ex.: 5000,00" name="valor" dobbin-validate-valor>
                                
                            </div>
                            <button type="button" class="btn btn-sm btn-danger" onclick="parcDelCampoTarifario(this)"><i class="fas fa-trash"></i></button>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary mt-2" onclick="parcAddCampoTarifario(this)"><i class="fas fa-plus"></i> Adicionar campo tarifa</button>
                    </div>
                    <div class="p-2 border mb-3">
                        <h6 class="small font-weight-bold mt-n3 text-uppercase mb-3"><span class="bg-white px-2">Benefícios Gratuitos</span></h6>
                        <div class="beneficios mb-2">

                            <div class="beneficiosHospedagem pl-2 pb-3 mb-3 border border-top-0 border-left-0 border-right-0">
                                <label class="small font-weight-bold ml-n2">Benefícios de Hospedagem</label><br>
                                <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" value="Café da manhã incluso"> Café da manhã incluso
                                    </label>
                                </div>
                                <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" value="Piscina"> Piscina
                                    </label>
                                </div>
                            </div>

                            <div class="beneficiosTransporte pl-2 pb-3 mb-3 border border-top-0 border-left-0 border-right-0">
                                <label class="small font-weight-bold ml-n2">Benefícios de Transporte</label><br>
                                <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" value="TV/DVD/SOM"> TV/DVD/SOM
                                    </label>
                                </div>
                                <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" value="Ar-condicionado"> Ar-condicionado
                                    </label>
                                </div>
                                <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" value="Banheiro"> Banheiro
                                    </label>
                                </div>
                                <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" value="Água a Bordo"> Água a Bordo
                                    </label>
                                </div>
                                <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" value="Manta"> Manta
                                    </label>
                                </div>
                                <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" value="Service Board"> Service Board
                                    </label>
                                </div>
                            </div>
                            
                        
                            <div class="beneficiosOutros pl-2 pb-3">
                                <label class="small font-weight-bold ml-n2">Outros Benefícios Inclusos</label><br>
                            </div>
                        </div>
                        <div class="d-flex w-75 mt-2">
                            <input type="text" class="form-control form-control-solid form-control-sm mr-1" placeholder="Nome do benefício" maxlength="25">
                            <button type="button" class="btn btn-sm btn-primary" onclick="parcInserirBeneficio(this, $(this).parent().parent().find('.beneficiosOutros'))">Inserir</button>
                        </div>
                    </div>
                    <div class="p-2 border mb-3" id="">
                        <h6 class="small font-weight-bold mt-n3 text-uppercase mb-3"><span class="bg-white px-2">Benefícios Pagos</span></h6>
                        <div class="beneficiosPagos">
                            <div class="beneficio d-flex mb-2">
                                <input type="text" class="form-control form-control-sm form-control-solid mr-1" placeholder="Nome" data-beneficio-nome>
                                <div class="input-group input-group-sm mr-1">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text form-control-solid">R$</span>
                                    </div>
                                    <input type="text" class="form-control form-control-sm form-control-solid" placeholder="Ex.: 5000,00" dobbin-validate-valor data-beneficio-valor>
                                    
                                </div>
                                <button class="btn btn-sm btn-danger" type="button" onclick="parcDelBeneficioPago(this);"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>

                        <button type="button" class="btn btn-sm btn-primary mt-2" onclick="parcAddBeneficioPago($(this).prev('.beneficiosPagos'))">Adicionar campo beneficio</button>
                    </div>
                    <div class="p-2 border mb-3" id="">
                        <h6 class="small font-weight-bold mt-n3 text-uppercase mb-3"><span class="bg-white px-2">OBSERVAÇÕES</span></h6>
                        <textarea rows="4" class="form-control-sm form-control form-control-solid" maxlength="300" name="obs"></textarea>
                    </div>
                    <div class="form-group">
                        <input type="hidden" name="PID" value="{{$parceiro['geral']->id}}">
                        <input type="hidden" name="SID" value="">
                    </div>
                    
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-sm" onclick="parcServicoSalvar()">Salvar</button>
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalParceiroFinanceiroAdd">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-2 px-3 font-weight-bold">
                Adicionar dados financeiros
                <button type="button" class="btn btn-sm btn-danger fechar" data-dismiss="modal"><strong>&times;</strong></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group row">
                        <div class="col-12 col-lg-6 mb-2">
                            <label class="small">Banco</label>
                            <select name="banco" class="form-control form-control-solid form-control-sm" required>
                                <option value="">Escolha</option>
                                @foreach($bancos as $b)
                                <option value="{{$b->codigo}}">{{$b->codigo}} - {{$b->banco}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-lg-6 mb-2">
                            <label class="small">Favorecido</label>
                            <input type="text" name="favorecido" class="form-control form-control-solid form-control-sm" maxlength="70"  required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-8 col-lg-4 mb-2">
                            <label class="small">Agência</label>
                            <input type="text" name="agencia" class="form-control form-control-solid form-control-sm" pattern="[0-9]{0,}" maxlength="10" required>
                        </div>
                        <div class="col-4 col-lg-2 mb-2">
                            <label class="small">DV</label>
                            <input type="text" name="agencia_dv" class="form-control form-control-solid form-control-sm" pattern="[0-9]{0,1}" maxlength="1" required>
                        </div>
                        <div class="col-8 col-lg-4 mb-2">
                            <label class="small">Conta</label>
                            <input type="text" name="conta" class="form-control form-control-solid form-control-sm" pattern="[0-9]{0,}" maxlength="10" required>
                        </div>
                        <div class="col-4 col-lg-2 mb-2">
                            <label class="small">DV</label>
                            <input type="text" name="conta_dv" class="form-control form-control-solid form-control-sm" pattern="[0-9]{0,1}" maxlength="1" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-lg-6 mb-2">
                            <label class="small">Tipo de conta</label>
                            <select name="tipo_conta" class="form-control form-control-solid form-control-sm">
                                <option value="CORRENTE">Conta Corrente</option>
                                <option value="POUPANÇA">Conta Poupança</option>
                                <option value="SALÁRIO">Conta-Salário</option>
                                <option value="DIGITAL">Conta Digital</option>
                                <option value="PAGAMENTOS">Conta de Pagamentos</option>
                            </select>
                        </div>
                        <div class="col-12 col-lg-6 mb-2">
                            <label class="small">OBSERVAÇÕES FINANCEIRAS</label>
                            <textarea rows="3" cols="" name="obs_financeiro" class="form-control form-control-solid form-control-sm" maxlength="300"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <input type="hidden" name="PID" value="{{$parceiro['geral']->id}}">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-sm" onclick="parcFinanceiroNovo()">Salvar</button>
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalParceiroFinanceiroEdita">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-2 px-3 font-weight-bold">
                Editar dados financeiros
                <button type="button" class="btn btn-sm btn-danger fechar" data-dismiss="modal"><strong>&times;</strong></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group row">
                        <div class="col-12 col-lg-6 mb-2">
                            <label class="small">Banco</label>
                            <select name="banco" class="form-control form-control-solid form-control-sm" required>
                                <option value="">Escolha</option>
                                @foreach($bancos as $b)
                                <option value="{{$b->codigo}}">{{$b->codigo}} - {{$b->banco}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-lg-6 mb-2">
                            <label class="small">Favorecido</label>
                            <input type="text" name="favorecido" class="form-control form-control-solid form-control-sm" maxlength="70"  required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-8 col-lg-4 mb-2">
                            <label class="small">Agência</label>
                            <input type="text" name="agencia" class="form-control form-control-solid form-control-sm" pattern="[0-9]{0,}" maxlength="10" required>
                        </div>
                        <div class="col-4 col-lg-2 mb-2">
                            <label class="small">DV</label>
                            <input type="text" name="agencia_dv" class="form-control form-control-solid form-control-sm" pattern="[0-9]{0,1}" maxlength="1" required>
                        </div>
                        <div class="col-8 col-lg-4 mb-2">
                            <label class="small">Conta</label>
                            <input type="text" name="conta" class="form-control form-control-solid form-control-sm" pattern="[0-9]{0,}" maxlength="10" required>
                        </div>
                        <div class="col-4 col-lg-2 mb-2">
                            <label class="small">DV</label>
                            <input type="text" name="conta_dv" class="form-control form-control-solid form-control-sm" pattern="[0-9]{0,1}" maxlength="1" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-lg-6 mb-2">
                            <label class="small">Tipo de conta</label>
                            <select name="tipo_conta" class="form-control form-control-solid form-control-sm">
                                <option value="CORRENTE">Conta Corrente</option>
                                <option value="POUPANÇA">Conta Poupança</option>
                                <option value="SALÁRIO">Conta-Salário</option>
                                <option value="DIGITAL">Conta Digital</option>
                                <option value="PAGAMENTOS">Conta de Pagamentos</option>
                            </select>
                        </div>
                        <div class="col-12 col-lg-6 mb-2">
                            <label class="small">OBSERVAÇÕES FINANCEIRAS</label>
                            <textarea rows="3" cols="" name="obs_financeiro" class="form-control form-control-solid form-control-sm" maxlength="300"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <input type="hidden" name="PID" value="{{$parceiro['geral']->id}}">
                        <input type="hidden" name="FID" value="">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-sm" onclick="parcFinanceiroSalvar()">Salvar</button>
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

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
                        <input type="hidden" name="parcid" value="{{$parceiro['geral']->id}}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-success" onclick="parcHistoricoNovo(this)">Salvar</button>
                    <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Fechar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalParceiroHistoricoEdita">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-2 px-3 font-weight-bold">
                Edita Registro de Negociações
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
                        <input type="hidden" name="parcid" value="{{$parceiro['geral']->id}}">
                        <input type="hidden" name="hid" value="">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-success" onclick="parcHistoricoEdita(this)">Salvar</button>
                    <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Fechar</button>
                </div>
            </form>
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
<!-- ./MODAIS -->

<script>
    $(document).ready(function(){
        var parceiroNome = "{{$parceiro['geral']->razao_social}}";
        $('#parceiroTitle').html(parceiroNome);
        $('.toggleMinMax').eq(1).trigger('click');
        $('.toggleMinMax').eq(2).trigger('click');
        $('.toggleMinMax').eq(3).trigger('click');

        $(document).on('click', '#historico_negoc [dobbin-btn-editahistorico]', function(ev){
            let linha = $(ev.currentTarget).parents('tr').eq(0);
            $('#modalParceiroHistoricoEdita').modal('show');
            $('#modalParceiroHistoricoEdita').find('[name="hid"]').val($(ev.currentTarget).data('hid'));
            $('#modalParceiroHistoricoEdita').find('[name="etapa"]').val(linha.data('etapa'));
            $('#modalParceiroHistoricoEdita').find('[name="detalhes"]').val(linha.data('detalhes'));
        });
    });
</script>