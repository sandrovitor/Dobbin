<div class="row">
    <div class="col-12">
        <div class="card" id="novoBalanco">
            <div class="card-body px-3 py-3">
                <input type="hidden" id="FOL_ID" value="{{$fDados->id}}">
                <input type="hidden" id="FOL_MES" value="{{$fDados->mes}}">
                <input type="hidden" id="FOL_ANO" value="{{$fDados->ano}}">
                <input type="hidden" id="FOL_FILE" value="{{substr($fDados->nomearq, 0, strrpos($fDados->nomearq, '.'))}}">
                
                <a href="#{{substr($router->generate('financeiroListar'), 1)}}"><i class="fas fa-arrow-left"></i> Voltar para lista</a>
                <hr>
                <form>
                    <div class="form-group">
                        <label class="font-weight-bold">Mês/ano do balanço</label>
                        <input type="month" class="form-control form-control-sm form-control-solid" name="periodo" disabled
                        value="{{$fDados->ano}}-{{$fDados->mes}}">
                    </div>
                    <div class="py-2 d-flex">
                        <div class="p-2 mr-2 border rounded shadow-sm bg-light"><b>Criado por</b><br>{{$fDados->criado_por_nome}}</div>
                        <div class="p-2 mr-2 border rounded shadow-sm bg-light"><b>Criado em</b><br>{{$fDados->criado_data}}</div>
                        <div class="p-2 mr-4 border rounded shadow-sm bg-light"><b>Situação</b><br>
                            {!!$fDados->fechada == "1" ? '<b class="text-danger">FECHADA</b>' : '<b class="text-success">ABERTA</b>'!!}
                        </div>


                        @if($fDados->fechada == "1")
                        <div class="p-2 mr-2 border rounded shadow-sm bg-light border-danger"><b>Fechada em</b><br><span class="text-danger">{{$fDados->fechada_data}}</span></div>
                        <div class="p-2 mr-4 border rounded shadow-sm bg-light border-danger"><b>Fechada por</b><br>
                            {{$fDados->fechada_por_nome}}
                        </div>
                        @else

                            @if($fDados->alterado_por != null)
                        <div class="p-2 mr-2 border rounded shadow-sm bg-light"><b>Alterado por</b><br><span class="text-info">{{$fDados->alterado_por_nome}}</span></div>
                        <div class="p-2 mr-4 border rounded shadow-sm bg-light"><b>Alterado em</b><br><span class="text-info">{{$fDados->alterado_data}}</span></div>
                            @endif

                        @endif
                    </div>
                    <hr>
                    <div class="bloco-acord border">
                        <div class="acord-header bg-light p-2 d-flex justify-content-between cursor-pointer">
                            <h6 class="font-weight-bold text-uppercase my-1">
                                DESPESAS <small>[SAÍDAS/PAGAMENTOS]</small>
                                <span id="qtdDespesa" class="badge badge-dark ml-4">0</span>
                            </h6>
                            <button class="btn btn-transparent btn-sm text-dark"><i class="fas fa-angle-down"></i></button>
                        </div>
                        <div class="acord-body p-2 py-3 pt-0 border border-secondary border-bottom-0 border-left-0 border-right-0" style="display:none">
                        <!-- DESPESAS -->
                            <div id="listaDespesas">
                                <h4 class="font-weight-bold">DESPESAS</h4>
                                <div class="alert alert-info small py-1 px-2">
                                    <b>Atenção!</b> Todos os lançamentos aqui serão considerados como <b>saídas (débitos)</b>.
                                </div>
                                <table class="table table-sm table-bordered">
                                    <thead class="thead-dark">
                                        <tr class="small">
                                            <th>Despesa</th>
                                            <th>Vencimento (1 - 31)</th>
                                            <th>Categoria</th>
                                            <th>Valor</th>
                                            <th style="width:45px"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr data-example style="display:none;">
                                            <td>
                                                <input type="text" class="form-control form-control-sm form-control-solid" name="nome" maxlength="40" placeholder="Nome da despesa...">
                                            </td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm form-control-solid" name="vencimento" min="1" max="31" placeholder="Data do vencimento">
                                            </td>
                                            <td>
                                                <select class="form-control form-control-sm form-control-solid" name="categoria">
                                                    <option value="FIXA">Despesa Fixa</option>
                                                    <option value="OCASIONAL">Despesa Ocasional</option>
                                                    <option value="NOVO ITINERÁRIO">Despesa Novo Itinerário</option>
                                                    <option value="PAGAMENTOS">Despesas com Pagamentos</option>
                                                </select>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text form-control-solid">R$</span>
                                                    </div>
                                                    <input type="text" class="form-control form-control-sm form-control-solid" name="valor" value="0,00" dobbin-mask-money>
                                                </div>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="delLinha(this)"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                        @foreach($folha->folha as $d)
                                            @if($d->tipo == "SAIDA")
                                        <tr>
                                            <td>
                                                <input type="text" class="form-control form-control-sm form-control-solid" name="nome" maxlength="40" 
                                                placeholder="Nome da despesa..." value="{{$d->nome}}">
                                            </td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm form-control-solid" name="vencimento" min="1" max="31"
                                                placeholder="Data do vencimento" value="{{$d->vencimento}}" dobbin-mask-number>
                                            </td>
                                            <td>
                                                <select class="form-control form-control-sm form-control-solid" name="categoria">
                                                    <option value="FIXA" {{$d->categoria=="FIXA" ? "selected" : ""}}>Despesa Fixa</option>
                                                    <option value="OCASIONAL" {{$d->categoria=="OCASIONAL" ? "selected" : ""}}>Despesa Ocasional</option>
                                                    <option value="NOVO ITINERÁRIO" {{$d->categoria=="NOVO ITINERÁRIO" ? "selected" : ""}}>Despesa Novo Itinerário</option>
                                                    <option value="PAGAMENTOS" {{$d->categoria=="PAGAMENTOS" ? "selected" : ""}}>Despesas com Pagamentos</option>
                                                </select>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text form-control-solid">R$</span>
                                                    </div>
                                                    <input type="text" class="form-control form-control-sm form-control-solid" name="valor" value="{{$d->valor}}" dobbin-mask-money>
                                                </div>
                                            </td>
                                            <td >
                                                <button type="button" class="btn btn-sm btn-danger" onclick="delLinha(this)"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                            @endif
                                        @endforeach
                                        <tr>
                                            <td>
                                                <input type="text" class="form-control form-control-sm form-control-solid" name="nome" maxlength="40" placeholder="Nome da despesa...">
                                            </td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm form-control-solid" name="vencimento" min="1" max="31" placeholder="Data do vencimento" dobbin-mask-number>
                                            </td>
                                            <td>
                                                <select class="form-control form-control-sm form-control-solid" name="categoria">
                                                    <option value="FIXA">Despesa Fixa</option>
                                                    <option value="OCASIONAL">Despesa Ocasional</option>
                                                    <option value="NOVO ITINERÁRIO">Despesa Novo Itinerário</option>
                                                    <option value="PAGAMENTOS">Despesas com Pagamentos</option>
                                                </select>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text form-control-solid">R$</span>
                                                    </div>
                                                    <input type="text" class="form-control form-control-sm form-control-solid" name="valor" value="0,00" dobbin-mask-money>
                                                </div>
                                            </td>
                                            <td >
                                                <button type="button" class="btn btn-sm btn-danger" onclick="delLinha(this)"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <button type="button" class="btn btn-primary btn-sm" onclick="addLinha(this)"><i class="fas fa-plus"></i> Nova linha de despesa</button>
                            </div>
                        <!-- ./DESPESAS -->
                        </div>
                    </div>
                    
                    <div class="bloco-acord border">
                        <div class="acord-header bg-light p-2 d-flex justify-content-between cursor-pointer">
                            <h6 class="font-weight-bold text-uppercase my-1">
                                RECEITAS <small>[ENTRADAS/RECEBÍVEIS]</small>
                                <span id="qtdReceita" class="badge badge-dark ml-4">0</span>
                            </h6>
                            <button class="btn btn-transparent btn-sm text-dark"><i class="fas fa-angle-down"></i></button>
                        </div>
                        <div class="acord-body p-2 py-3 pt-0 border border-secondary border-bottom-0 border-left-0 border-right-0" style="display:none">
                        <!-- RECEITAS -->
                            <div id="listaReceitas">
                                <h4 class="font-weight-bold">RECEITAS </h4>
                                <div class="alert alert-info small py-1 px-2">
                                    <b>Atenção!</b> Todos os lançamentos aqui serão considerados como <b>entradas (créditos)</b>.
                                </div>
                                <table class="table table-sm table-bordered">
                                    <thead class="thead-dark">
                                        <tr class="small">
                                            <th>Receita</th>
                                            <th>Vencimento (1 - 31)</th>
                                            <th>Valor</th>
                                            <th style="width:45px"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr data-example style="display:none;">
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend">
                                                        
                                                    </div>
                                                    <input type="text" class="form-control form-control-sm form-control-solid" name="nome" maxlength="40" placeholder="Nome da receita...">
                                                </div>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm form-control-solid" name="vencimento" min="1" max="31" placeholder="Data do vencimento">
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text form-control-solid">R$</span>
                                                    </div>
                                                    <input type="text" class="form-control form-control-sm form-control-solid" name="valor" value="0,00" dobbin-mask-money>
                                                </div>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="delLinha(this)"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                        @foreach($folha->folha as $r)
                                            @if($r->tipo == "ENTRADA")
                                        <tr>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend">
                                                        
                                                    </div>
                                                    <input type="text" class="form-control form-control-sm form-control-solid" name="nome" maxlength="40"
                                                    placeholder="Nome da receita..." value="{{$r->nome}}">
                                                </div>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm form-control-solid" name="vencimento" min="1" max="31"
                                                placeholder="Data do vencimento" value="{{$r->vencimento}}">
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text form-control-solid">R$</span>
                                                    </div>
                                                    <input type="text" class="form-control form-control-sm form-control-solid" name="valor"
                                                    value="{{$r->valor}}" dobbin-mask-money>
                                                </div>
                                            </td>
                                            <td >
                                                <button type="button" class="btn btn-sm btn-danger" onclick="delLinha(this)"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                            @endif
                                        @endforeach
                                        <tr>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend">
                                                        
                                                    </div>
                                                    <input type="text" class="form-control form-control-sm form-control-solid" name="nome" maxlength="40" placeholder="Nome da receita...">
                                                </div>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm form-control-solid" name="vencimento" min="1" max="31" placeholder="Data do vencimento">
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text form-control-solid">R$</span>
                                                    </div>
                                                    <input type="text" class="form-control form-control-sm form-control-solid" name="valor" value="0,00" dobbin-mask-money>
                                                </div>
                                            </td>
                                            <td >
                                                <button type="button" class="btn btn-sm btn-danger" onclick="delLinha(this)"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <button type="button" class="btn btn-primary btn-sm" onclick="addLinha(this)"><i class="fas fa-plus"></i> Nova linha de receita</button>
                            </div>
                        <!-- ./RECEITAS -->
                        </div>
                    </div>


                    
                    
                    <hr>
                    <div class="form-group mt-3">
                        <label class="font-weight-bold">OBSERVAÇÕES GERAIS</label>
                        <textarea class="form-control-solid form-control-sm form-control" name="obs_geral"
                        maxlength="400" rows="3">{{trim($folha->obs)}}</textarea>
                    </div>
                </form>
                <hr>
                <h5 class="font-weight-bold">RESULTADO DO BALANÇO</h5>
                <table class="table table-sm table-bordered table-hover" id="resultadoBalanco">
                    <thead>
                        <tr>
                            <th>Despesas</th>
                            <th>Receitas</th>
                            <th>Saldo Fechamento</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td data-despesa></td>
                            <td data-receita></td>
                            <td data-diferenca class="font-weight-bold"></td>
                        </tr>
                    </tbody>
                </table>
                <hr>
                <div class="text-right">
                    @if($fDados->fechada == '0')
                    <button type="button" class="btn btn-sm btn-danger mr-3" onclick="excluirBalanco()"><i class="fas fa-trash"></i> Excluir balanço</button>
                    <button type="button" class="btn btn-sm btn-primary" onclick="fecharBalanco()">Fechar balanço</button>
                    <button type="button" class="btn btn-sm btn-success" onclick="salvarBalanco()">Salvar balanço</button>
                    @else

                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<script src="/js/Financeiro.min.js"></script>
<script>
function salvarBalanco() {
    let form = $('#novoBalanco');
    balanco.obs = $('#novoBalanco').find('[name="obs_geral"]').val();
    let lDesp = $('#listaDespesas tbody');
    let lRec = $('#listaReceitas tbody');

    balanco.folha = [];

    // Despesas!
    for(let i = 0; i < lDesp.find('tr:not([data-example])').length; i++) {
        let linha = lDesp.find('tr:not([data-example])').eq(i);

        if(
            linha.find('[name="nome"]').val().trim() != '' &&
            linha.find('[name="vencimento"]').val().trim() != '' &&
            linha.find('[name="valor"]').val().trim() != ''
        ) {
            balanco.folha.push(
                {
                    nome: linha.find('[name="nome"]').val().trim(),
                    vencimento: linha.find('[name="vencimento"]').val(),
                    categoria: linha.find('[name="categoria"] :selected').val(),
                    tipo: 'SAIDA',
                    valor: linha.find('[name="valor"]').val(),
                    obs: ''
                }
            );
        } else {
            // Caso outros campos da linha estejam preenchidos, mostra linha em aberto. Interrompe a execução! 
            // Caso todos os campos da linha estiverem em branco, só conta as linhas em branco e continua a execução.
        }
        
    }

    // Receitas!
    for(let i = 0; i < lRec.find('tr:not([data-example])').length; i++) {
        let linha = lRec.find('tr:not([data-example])').eq(i);

        if(
            linha.find('[name="nome"]').val().trim() != '' &&
            linha.find('[name="vencimento"]').val().trim() != '' &&
            linha.find('[name="valor"]').val().trim() != ''
        ) {
            balanco.folha.push(
                {
                    nome: linha.find('[name="nome"]').val().trim(),
                    vencimento: linha.find('[name="vencimento"]').val(),
                    categoria: null,
                    tipo: 'ENTRADA',
                    valor: linha.find('[name="valor"]').val(),
                    obs: ''
                }
            );
        } else {
            // Caso outros campos da linha estejam preenchidos, mostra linha em aberto. Interrompe a execução! 
            // Caso todos os campos da linha estiverem em branco, só conta as linhas em branco e continua a execução.
        }
        
    }

    if(form.find('[name="periodo"]').val() == '') {
        alert("O campo 'Mês/ano do balanço' precisa ser informado.");
        form.find('[name="periodo"]').focus();
        return false;
    } else {
        let periodo = form.find('[name="periodo"]').val().split('-');
        balanco.ano = periodo[0];
        balanco.mes = periodo[1];
    }

    // Adiciona dados DEFAULT.
    balanco.id = $('#FOL_ID').val();

    console.log(balanco);
    
    $.post(PREFIX_POST+'financeiro/salvar', {
        balanco: JSON.stringify(balanco)
    }, function(res){
        console.log(res);
        if(res == true) {
            // Salvo
            alerta("Folha salva...", "Sucesso!", "success");
            
            location.href = '/#financeiro/ver/'+balanco.ano+'/'+balanco.mes+'/'+$('#FOL_FILE').val();
        } else {
            // Salvo
            alerta(res, "Falha!", "warning");
        }
    }, 'json').fail(function(ev){nativePOSTFail(ev);console.log(ev.responseText);});
    
}

function fecharBalanco() {
    let x = confirm("Tem certeza de que desejar fechar este balanço?\n\nSe você fez alterações, primeiro precisará Salvar o balanço.\nSe está tudo certo, clique em OK para continuar.");

    if(x == true) {
        $.post(PREFIX_POST+'financeiro/fechar', {
            id: {{$fDados->id}}
        }, function(data){
            if(data.success == true) {
                location.href = '/#financeiro/ver/'+$('#FOL_ANO').val()+
                '/'+$('#FOL_MES').val()+'/'+$('#FOL_FILE').val();
            } else {
                alerta(data.mensagem, "Falha!", "warning");
            }
        }, 'json');
    }
}

$(document).ready(function(){
    calculaBalanco();

    // Dispara busca por informações em todos os campos
    for(let i = 0; i < $('#listaReceitas tr:not([data-example]) [name="nome"]').length; i++) {
        let alvo = $('#listaReceitas tr:not([data-example]) [name="nome"]').eq(i);
        loadInformacaoCampoReceita(alvo[0]);
    }
});

</script>