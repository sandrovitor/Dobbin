<div class="row">
    <div class="col-12">
        <div class="card" id="novoBalanco">
            <div class="card-body px-3 py-3">
                <!--<div class="alert alert-danger">
                    <strong><i class="fas fa-microscope"></i> ÁREA DE TESTE!</strong><br>
                    Por favor, essa área não está funcionando corretamente, ou os dados lançados aqui <strong>não são salvos</strong>.
                    Só interaja com essa página ou área se o programador lhe der permissão, ou lhe solicitar que o faça.
                </div> -->
                <form>
                    <div class="form-group">
                        <label class="font-weight-bold">Mês/ano do balanço</label>
                        <input type="month" class="form-control form-control-sm form-control-solid" name="periodo" onchange="checkBalancoExiste()">
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
                                <button type="button" class="btn btn-primary btn-sm" id="btnAddLinhaDespesa" onclick="addLinha(this)"><i class="fas fa-plus"></i> Nova linha de despesa</button>
                                <button type="button" class="btn btn-primary btn-sm ml-2"
                                    data-toggle="tooltip" title="Carregar despesas do mês anterior automaticamente." onclick="carregaDespesasAutomatico()">Carregar despesas</button>
                                <button type="button" class="btn btn-warning btn-sm" onclick="delAllLinha(this)"><i class="fas fa-undo"></i> Resetar</button>
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
                                <button type="button" class="btn btn-primary btn-sm" id="btnAddLinhaReceita" onclick="addLinha(this)"><i class="fas fa-plus"></i> Nova linha de receita</button>
                                <button type="button" class="btn btn-primary btn-sm ml-2"
                                    data-toggle="tooltip" title="Listar todas as vendas no período automaticamente." onclick="carregaReceitasAutomatico()">Carregar vendas automaticamente</button>
                                <button type="button" class="btn btn-warning btn-sm" onclick="delAllLinha(this)"><i class="fas fa-undo"></i> Resetar</button>
                            </div>
                        <!-- ./RECEITAS -->
                        </div>
                    </div>


                    
                    
                    <hr>
                    <div class="form-group mt-3">
                        <label class="font-weight-bold">OBSERVAÇÕES GERAIS</label>
                        <textarea class="form-control-solid form-control-sm form-control" name="obs_geral" maxlength="400" rows="3"></textarea>
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
                    <button type="button" class="btn btn-sm btn-success" onclick="salvarBalanco()">Salvar balanço</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="/js/Financeiro.min.js"></script>
<script>
function salvarBalanco()
{
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

    console.log(balanco);
    
    $.post(PREFIX_POST+'financeiro/novo', {
        balanco: JSON.stringify(balanco)
    }, function(res){
        console.log(res);
        if(res == "1") {
            // Salvo
            alerta("Folha salva...", "Sucesso!", "success");
            
        } else {
            // Salvo
            alerta("Não foi possível salvar.", "Falha!", "warning");
        }
    }).fail(function(ev){nativePOSTFail(ev);});
    
}

$(document).ready(function(){
    
});
</script>