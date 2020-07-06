<div class="row">
    <div class="col-12 col-lg-12 col-xl-10 mx-auto">
        <div class="card">
            <form id="vendasNovo">
                <div class="card-body font-monospace">
                    <div class="row">
                        <div class="col-12 mb-2">
                            <label class="font-weight-bold">Cliente</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class=" form-control form-control-sm" name="cliente" disabled>
                                <input type="hidden" name="clienteID" value="" onchange="getClienteDados($(this).val(), function(c){$('form [name=\'cliente\']').val(c.id+' - '+c.nome);}); vendasNovoBtnAdicionarEnabled();">
                                <div class="input-group-append">
                                    <button type="button" class="btn-secondary btn btn-sm px-2" data-toggle="tooltip" title="Localizar cliente" onclick="janClienteSelect($('form [name=\'clienteID\']')[0])"><i class="fas fa-search fa-fw"></i></button>
                                    <button type="button" class="btn-primary btn btn-sm px-2" data-toggle="tooltip" title="Criar novo cliente" onclick="location = location.origin+'/?return=vendas/novo#clientes/novo';"><i class="fas fa-plus fa-fw"></i></button>
                                </div>
                            </div>
                            <div class="alert alert-info d-block d-md-none">
                                <strong>AVISO:</strong> Essa página pode ter problema em mostrar alguns itens em aparelhos celulares.<br>Dê preferência a dispositivos como tablets, laptops, desktops e etc.
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12 col-lg-7 mb-2">
                            <label class="font-weight-bold">Roteiro</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class=" form-control form-control-sm" name="roteiro" disabled>
                                <input type="hidden" name="roteiroID" value="" onchange="vendasLoadRoteiroInfo($(this).val())">
                                <div class="input-group-append">
                                    <button type="button" class="btn-secondary btn btn-sm px-2" onclick="janRoteiroSelect($('form [name=\'roteiroID\']')[0])"><i class="fas fa-search fa-fw"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-2 mb-2">
                            <label class="font-weight-bold">Disponível</label>
                            <input type="number" class="form-control form-control-sm " name="estoqueDisp" disabled>
                        </div>
                        <div class="col-12 col-lg-3 mb-2">
                            <label class="font-weight-bold">Tarifas</label>
                            <select class="form-control form-control-sm " name="tarifa" onchange="vendasChangeTarifa(this)">
                            
                            </select>
                        </div>
                    </div>

                    
                    <div class="d-flex flex-column flex-md-row align-items-md-end mb-2">
                        <div class="mr-md-3">
                            <label class="font-weight-bold">Quantidade</label>
                            <input type="number" class=" form-control form-control-sm" min="1" value="1" max="100" name="qtd" onchange="vendasReloadSubtotal()">
                        </div>
                        <div class="mr-md-3">
                            <label class="font-weight-bold">Valor Unitário</label>
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <div class="input-group-text ">R$</div>
                                </div>
                                <input type="text" class=" form-control form-control-sm" name="valor_unitario" onchange="vendasReloadSubtotal()" dobbin-mask-money disabled>
                            </div>
                        </div>
                        <div class="mr-md-3">
                            <label class="font-weight-bold">Desconto no Item</label>
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <div class="input-group-text ">R$</div>
                                </div>
                                <input type="text" class=" form-control form-control-sm" name="desconto_unitario" value="0,00" onchange="vendasReloadSubtotal()" dobbin-mask-money>
                            </div>
                        </div>
                        <div class="mr-md-3">
                            <label class="font-weight-bold">Subtotal</label>
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <div class="input-group-text ">R$</div>
                                </div>
                                <input type="text" class=" form-control form-control-sm" name="subtotal" disabled>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary mt-2 mt-md-0" id="vendasNovoBtnAdd" disabled>Adicionar</button>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12 mb-2">
                            <div class="border rounded-0">
                                <table class="table table-sm table-bordered table-responsive-sm">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Cód./Roteiro</th>
                                            <th>Qtd
                                            <span class="badge badge-pill badge-info" data-toggle="popover" title="<strong>O que é isso?</strong>" data-trigger="hover"
                                            data-content="Aqui consta a quantidade do item que o cliente deseja adquirir.<br><br><ul><li>Para alterá-lo, dê um duplo clique no valor;</li> <li>Para cancelar a edição, aperte ESC;</li> <li>Para manter a edição, aperte ENTER.</li></ul>">
                                            <i class="fas fa-question-circle"></i></span>
                                            </th>
                                            <th>Tarifa</th>
                                            <th>Valor (R$)</th>
                                            <th>Desconto (R$)</th>
                                            <th>Subtotal (R$)</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    
                                    
                                    
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 d-flex flex-md-row flex-column justify-content-between align-items-md-end">
                            <div class="d-flex flex-md-row flex-column flex-grow-1">
                                <div class="form-group mr-md-3">
                                    <label class="font-weight-bold">TOTAL</label>
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text ">R$</div>
                                        </div>
                                        <input type="text" class=" form-control form-control-sm" name="total" dobbin-mask-money disabled>
                                    </div>
                                </div>
                                <div class="form-group mr-md-3">
                                    <label class="font-weight-bold">Forma de pagamento</label>
                                    <select class="form-control form-control-sm" name="pagamento">
                                        <option value="Reserva">Somente reserva</option>
                                        <option disabled class="separator"></option>
                                        <option value="Crédito">Cartão de crédito</option>
                                        <option value="Débito">Cartão de débito</option>
                                        <option value="Boleto">Boleto Bancário</option>
                                        <option value="Digital">Pagamento Digital</option>
                                        <option value="Transferência">Transferência Bancária</option>
                                        <option value="Dinheiro">Dinheiro</option>
                                        <option value="Outro">Outro</option>
                                    </select>
                                </div>

                                <div class="form-group mr-md-3 flex-grow-1">
                                    <label class="font-weight-bold">Observações</label>
                                    <textarea class="form-control-sm form-control" name="obs" rows="2" placeholder="Informação adicional..." maxlength="300"></textarea>
                                </div>
                            </div>
                            <div class="ml-0 ml-md-3 d-flex flex-row flex-md-column justify-content-between justify-content-md-end ">
                                <button type="button" class="btn btn-sm btn-secondary mt-1" onclick="$('#content').scrollTop(0); loadLanding(location.hash);">Cancelar</button>
                                <button type="button" class="btn btn-success btn-sm mt-1" id="vendasNovoFecharVenda" disabled onclick="vendasNovoSalvar()">Fechar venda</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function vendasLoadRoteiroInfo(id)
{
    getRoteiroDados(id, function(r){
        // Limpa campos
        $('#vendasNovo [name="roteiro"]').val('');
        $('#vendasNovo [name="estoqueDisp"]').val('');
        $('#vendasNovo [name="tarifa"]').children('option').remove();


        if(r !== false){
            $('#vendasNovo [name="roteiro"]').val(r.id+' - '+r.nome + ' ('+Dobbin.formataData(new Date(r.data_ini), true)+' a '+Dobbin.formataData(new Date(r.data_fim), true)+')');
            $('#vendasNovo [name="estoqueDisp"]').val( (r.estoque.total - r.estoque.vendidos) );
            r.tarifa.forEach(function(t){
                $('#vendasNovo [name="tarifa"]').append('<option value="'+t.nome+'" data-valor="'+t.valor+'" data-adultos="'+t.distr.adultos+'" data-criancas="'+t.distr.criancas+'">'+
                'R$ '+ Dobbin.converteCentavoEmReal(t.valor) + ' - ' +t.nome + '</option>')
            });

            if(r.situacao == "ANDAMENTO") {
                $('#vendasNovo [name="roteiro"]').parent().after('<div class="alert alert-info small py-1 px-2">'+
                '<strong>Atenção!</strong> Esse roteiro já está <strong>ANDAMENTO (viagem)</strong>.</div>');
            } else if(r.situacao == "CONCLUIDO") {
                $('#vendasNovo [name="roteiro"]').parent().after('<div class="alert alert-warning small py-1 px-2">'+
                '<strong>Atenção!</strong> Esse roteiro já se encerrou. <strong>É recomendado que não faça vendas deste roteiro</strong>.</div><br>'+
                'Ainda é possível lançar vendas antigas no sistema, mas as datas serão de hoje.');
                
            } else {
                $('#vendasNovo [name="roteiro"]').parent().next().remove();
            }
            console.log(r);

        }
        //console.log(r);

        vendasChangeTarifa($('#vendasNovo [name="tarifa"]')[0]);
        vendasReloadSubtotal();
    });
}

function vendasChangeTarifa(sender)
{
    let valor = parseInt($(sender).find(':selected').data('valor'));
    let qtd = parseInt($(sender).parents('form').find('[name="qtd"]').val());
    $(sender).parents('form').find('[name="valor_unitario"]').val( Dobbin.converteCentavoEmReal(valor) );
    
    // Limita quantidade máxima.
    let pessoas = parseInt($(sender).find(':selected').data('adultos')) + parseInt($(sender).find(':selected').data('criancas'));
    let qtdMax = Math.floor(parseInt($('#vendasNovo [name="estoqueDisp"]').val()) / pessoas);
    $('#vendasNovo [name="qtd"]').attr('max', qtdMax);
    if(qtdMax == 0) {
        $('#vendasNovo [name="qtd"]').attr('min', qtdMax);
    } else {
        $('#vendasNovo [name="qtd"]').attr('min', 1);
    }
    if($('#vendasNovo [name="qtd"]').val() > $('#vendasNovo [name="qtd"]').attr('max')) {
        $('#vendasNovo [name="qtd"]').val($('#vendasNovo [name="qtd"]').attr('max'));
    }

    vendasReloadSubtotal();
}

function vendasReloadSubtotal()
{
    let qtd = parseInt($('#vendasNovo [name="qtd"]').val());
    let valor_un = Dobbin.converteRealEmCentavo($('#vendasNovo [name="valor_unitario"]').val());
    let desconto = Dobbin.converteRealEmCentavo($('#vendasNovo [name="desconto_unitario"]').val());
    
    $('#vendasNovo [name="subtotal"]').val( Dobbin.converteCentavoEmReal((valor_un * qtd) - desconto) );
    vendasNovoBtnAdicionarEnabled();
}

function vendasNovoBtnAdicionarEnabled()
{
    if(parseInt($('#vendasNovo [name="qtd"]').val()) == 0) {
        return false;
    } else if($('#vendasNovo [name="roteiroID"]').val() == '') {
        return false;
    } else if($('#vendasNovo [name="clienteID"]').val() == '') {
        return false
    } else {
        $('#vendasNovoBtnAdd').attr('disabled', false);
    }
}

function vendasNovoCalcularTotal()
{
    if($('#vendasNovo table tbody').find('tr').length == 0) {
        $('#vendasNovo [name="total"]').val(0);
    } else {
        let total = 0;
        for(let i = 0; i < $('#vendasNovo table tbody').find('tr').length; i++) {
            let sub = $('#vendasNovo table tbody').find('tr').eq(i).children('td:eq(5)').text();
            sub = Dobbin.converteRealEmCentavo(sub);

            // Verifica cálculo linha a linha.
            let temp1, temp2, temp3;
            temp1 = $('#vendasNovo table tbody').find('tr').eq(i).children('td:eq(3)').text(); // Valor
            temp1 = Dobbin.converteRealEmCentavo(temp1);
            temp2 = $('#vendasNovo table tbody').find('tr').eq(i).children('td:eq(1)').text(); // Qtd
            temp3 = $('#vendasNovo table tbody').find('tr').eq(i).children('td:eq(4)').text(); // Desconto
            temp3 = Dobbin.converteRealEmCentavo(temp3);

            if( (temp1 * temp2) - temp3 !== sub ) {
                sub = (temp1 * temp2) - temp3;
                $('#vendasNovo table tbody').find('tr').eq(i).children('td:eq(5)').text(Dobbin.converteCentavoEmReal(sub));
            }

            total = total+sub;
        }
        $('#vendasNovo [name="total"]').val(total);
    }
    $('#vendasNovo [name="total"]').trigger('change');
    
}

function vendasNovoSalvar()
{
    let venda = {
        items: [],
        clienteID: $('#vendasNovo [name="clienteID"]').val(),
        valorTotal: Dobbin.converteRealEmCentavo($('#vendasNovo [name="total"]').val()),
        formaPagamento: $('#vendasNovo [name="pagamento"]').val(),
        obs: $('#vendasNovo [name="obs"]').val(),
    };
    let tabela = $('#vendasNovo table tbody');

    if(tabela.children('tr').length == 0) {
        return false;
    } else {
        for(let i = 0; i < tabela.children('tr').length; i++) {
            let temp1, temp2, temp3;

            temp1 = tabela.children('tr').eq(i).children('td:eq(0)').text();
            temp2 = temp1.indexOf(' - ');
            
            let v = {
                roteiroID: temp1.slice(0, temp2),
                qtd: tabela.children('tr').eq(i).children('td:eq(1)').text(),
                tarifa: tabela.children('tr').eq(i).children('td:eq(2)').text(),
                valorUNI: Dobbin.converteRealEmCentavo(tabela.children('tr').eq(i).children('td:eq(3)').text()),
                desconto: Dobbin.converteRealEmCentavo(tabela.children('tr').eq(i).children('td:eq(4)').text()),
                subtotal: Dobbin.converteRealEmCentavo(tabela.children('tr').eq(i).children('td:eq(5)').text()),
            };

            venda.items.push(v);

            temp1 = undefined;
            temp2 = undefined;
            temp3 = undefined;
        }
    }

    console.log(venda);
    $.post(PREFIX_POST+'vendas/novo', {venda: JSON.stringify(venda)}, function(res){
        if(res.success === true && res.mensagem.length == 0) {
            // Sucesso absoluto;
            alerta('A venda foi lançada na plataforma... Consulte a venda para mais detalhes.', 'Venda efetuada com sucesso.', 'success');
            loadLanding(location.hash);
        } else if (res.success === true) {
            alerta('A venda foi lançada na plataforma, mas há ressalvas: <br><br>'+res.mensagem, 'Venda efetuada com sucesso.', 'success', 15000);
            loadLanding(location.hash);
        } else {
            alerta(res.mensagem, 'Não foi possível fechar a venda.', 'warning');
        }
    }, 'json').fail(function(ev){nativePOSTFail(ev);});
}

$(document).ready(function(){
    $('#vendasNovoBtnAdd').on('click', function(){
        let tabela = $('#vendasNovo table tbody');

        let cod = $('#vendasNovo').find('[name="roteiroID"]').val();
        let rot = $('#vendasNovo').find('[name="roteiro"]').val();
        let qtd = $('#vendasNovo').find('[name="qtd"]').val();
        let tarifa = $('#vendasNovo').find('[name="tarifa"] :selected').val();
        let valor = $('#vendasNovo').find('[name="valor_unitario"]').val();
        let desconto = $('#vendasNovo').find('[name="desconto_unitario"]').val();
        let subtotal = $('#vendasNovo').find('[name="subtotal"]').val();


        tabela.append('<tr class="small"> <td>'+rot+'</td> '+
        '<td>'+qtd+'</td> <td>'+tarifa+'</td> '+
        '<td>'+valor+'</td> <td>'+desconto+'</td> '+
        '<td>'+subtotal+'</td> <td>'+
        '<button type="button" class="btn btn-sm btn-danger" data-remove onclick=""><i class="fas fa-trash"></i></button>'+
        '</td> </tr>');

        $('#vendasNovoFecharVenda').attr('disabled', false);
        $('#vendasNovo').find('[name="qtd"]').val('1'); // Redefine quantidade para 1.
        $('#vendasNovo').find('[name="desconto_unitario"]').val('0,00'); // Redefine o desconto unitário

        vendasNovoCalcularTotal();
        
    });

    // VENDAS > NOVO: Remove linha da tabela
    $(document).on('click', '#vendasNovo table tbody tr [data-remove]', function(ev) {
        let tabela = $(ev.currentTarget).parents('tbody');
        $(ev.currentTarget).parents('tr').remove();
        vendasNovoCalcularTotal();
        
        if(tabela.find('tr').length == 0) {
            $('#vendasNovoFecharVenda').attr('disabled', true);
        }
    });

    // VENDAS > NOVO: Dois cliques para editar desconto
    $(document).on('dblclick', '#vendasNovo table tbody tr td:nth-child(5)', function(ev){
        let valor = $(ev.currentTarget).text();
        $(ev.currentTarget).html('<input type="text" class="form-control form-control-sm" value="'+
        Dobbin.converteRealEmCentavo(valor)+'" dobbin-mask-money>');
        $(ev.currentTarget).children(':input').trigger('change');
        $(ev.currentTarget).children(':input').focus();
    
    });

    // VENDAS > NOVO: Botão Enter ou ESC ao editar desconto
    $(document).on('keyup', '#vendasNovo table tbody td:nth-child(5) > :input', function(ev){
        //console.log(ev);
        if(ev.which == 13) { // Enter
            // Salva novo valor
            $(ev.currentTarget).trigger('change');
            let valor = $(ev.currentTarget).val();
            let pai = $(ev.currentTarget).parents('td').eq(0);
            $(ev.currentTarget).remove();
            pai.text(valor);
            vendasNovoCalcularTotal();
        } else if(ev.which == 27) { // ESC ou Escape
            // Retorna valor default
            let valor = $(ev.currentTarget)[0].defaultValue;
            let pai = $(ev.currentTarget).parents('td').eq(0);
            $(ev.currentTarget).remove();
            pai.text(valor);
        }
    
    });

    // VENDAS > NOVO: Dois cliques para editar quantidade
    $(document).on('dblclick', '#vendasNovo table tbody tr td:nth-child(2)', function(ev){
        if($(ev.currentTarget).children(':input').length == 0) {
            let valor = $(ev.currentTarget).text();
            $(ev.currentTarget).html('<input type="number" class="form-control form-control-sm" value="'+valor+'">');
            $(ev.currentTarget).children(':input').trigger('change');
            $(ev.currentTarget).children(':input').focus();
        }
        
    
    });

    // VENDAS > NOVO: Botão Enter ou ESC ao editar quantidade
    $(document).on('keyup', '#vendasNovo table tbody td:nth-child(2) > :input', function(ev){
        //console.log(ev);
        if(ev.which == 13) { // Enter
            // Salva nova quantidade.
            let valor = $(ev.currentTarget).val();
            let pai = $(ev.currentTarget).parents('td').eq(0);
            $(ev.currentTarget).remove();
            pai.text(valor);
            vendasNovoCalcularTotal();
        } else if(ev.which == 27) { // ESC ou Escape
            // Retorna quantidade default
            let valor = $(ev.currentTarget)[0].defaultValue;
            let pai = $(ev.currentTarget).parents('td').eq(0);
            $(ev.currentTarget).remove();
            pai.text(valor);
        }

    });
});
</script>