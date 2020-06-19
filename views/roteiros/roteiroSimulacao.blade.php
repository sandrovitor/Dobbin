<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                Simular roteiros
            </div>
            <div class="card-body">
                <form id="simulacao">
                    <div class="border bloco-acord shadow-sm my-3">
                        <div class="acord-header d-flex justify-content-between">
                            <h6 class="text-uppercase my-1 font-weight-bold text-primary">
                                Definições Gerais
                            </h6>
                            <button type="button" class="btn btn-transparent btn-sm text-dark"><i class="fas fa-angle-up"></i></button>
                        </div>
                        <div class="acord-body p-2 py-3 pt-0 border border-secondary border-bottom-0 border-left-0 border-right-0">
                            <div class="row">
                                <div class="col-12 col-md-4 col-lg-4">
                                    <div class="form-group">
                                        <label class="small">Nome do pacote</label>
                                        <input type="text" name="nome_pacote" class="form-control form-control-sm form-control-solid" value="SIMULAÇÃO">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 col-lg-4">
                                    <div class="form-group">
                                        <label class="small">Partida</label>
                                        <input type="date" name="data_ini" class="form-control form-control-sm form-control-solid" required>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 col-lg-4">
                                    <div class="form-group">
                                        <label class="small">Retorno</label>
                                        <input type="date" name="data_fim" class="form-control form-control-sm form-control-solid" required>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 col-lg-4">
                                    <div class="form-group">
                                        <label class="small">Qtd de Passageiros (Clientes)</label>
                                        <input type="number" name="passagens" class="form-control form-control-sm form-control-solid" value="" required>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 col-lg-4">
                                    <div class="form-group">
                                        <label class="small">Qtd de Coordenadores (ou isentos)</label>
                                        <input type="number" name="qtd_coord" class="form-control form-control-sm form-control-solid" value="" required>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-8 col-lg-4">
                                    <div class="form-group">
                                        <label class="small">Método de rateio</label><br>
                                        <div class="form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input" name="tipo_rateio" value="automatico">
                                                Automático
                                                <i class="far fa-question-circle" data-toggle="tooltip" title="Método automático de rateio: Valor das despesas será divido pelo total de clientes."></i>
                                            </label>
                                        </div>
                                        <div class="form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input" name="tipo_rateio" checked="checked" value="automatico66">
                                                Automático 66%
                                                <i class="far fa-question-circle" data-toggle="tooltip" title="Método: Valor das despesas será dividido por 2/3 do total de clientes. Outros 1/3 dos clientes podem ser convertidos em lucro."></i>
                                            </label>
                                        </div>
                                        <div class="form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input" name="tipo_rateio" value="personalizado">
                                                Personalizado
                                                <i class="far fa-question-circle" data-toggle="tooltip" title="Informe a quantidade de clientes para fazer o rateio das despesas."></i>
                                            </label>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-12 col-md-4 col-lg-4" data-rateio-div style="display:none;">
                                    <div class="form-group">
                                        <label class="small">Número de clientes para rateio</label>
                                        <input type="number" name="rateio_qtd" class="form-control form-control-sm form-control-solid" value="" min="1">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 text-right">
                                    <button type="button" class="btn btn-sm btn-primary" onclick="proximo(this)">Próximo</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="border bloco-acord">
                        <div class="acord-header d-flex justify-content-between">
                            <h6 class="text-uppercase my-1 font-weight-bold text-primary">
                                Despesas
                            </h6>
                            <button type="button" class="btn btn-transparent btn-sm text-dark"><i class="fas fa-angle-down"></i></button>
                        </div>
                        <div class="acord-body p-2 py-3 pt-0 border border-secondary border-bottom-0 border-left-0 border-right-0" style="display:none;">
                            <div class="row">
                                <div class="col-12 col-lg-8">
                                    <table class="table table-sm table-bordered">
                                        <tbody>
                                            <tr style="display:none;" data-example>
                                                <td>
                                                    <label class="small">Tipo despesa</label>
                                                    <select class="form-control form-control-sm form-control-solid" name="tipo_despesa">
                                                        <option value="Hospedagem">Hospedagem</option>
                                                        <option value="Transporte">Transporte</option>
                                                        <option value="Alimentação">Alimentação</option>
                                                        <option value="Personalizado">Personalizado</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <label class="small">Nome da despesa</label>
                                                    <input type="text" class="form-control form-control-sm form-control-solid" name="nome_despesa" maxlength="30">
                                                </td>
                                                <td>
                                                    <label class="small">Valor</label>
                                                    <div class="row">
                                                        <div class="col-12 col-lg-6 mb-1">
                                                            <div class="input-group input-group-sm">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text form-control-solid">R$</span>
                                                                </div>
                                                                <input type="text" class="form-control form-control-sm form-control-solid" name="valor" placeholder="1234,99" dobbin-validate-valor maxlength="30" required>
                                                                
                                                            </div>
                                                            
                                                        </div>
                                                        <div class="col-12 col-lg-6 mb-1">
                                                            <select class="form-control form-control-sm form-control-solid" name="valor_tipo">
                                                                <option value="total" title="Valor integral da despesa para todos os passageiros durante o período do roteiro.">Valor Total</option>
                                                                <option value="dia" title="Valor diário da despesa para todos passageiros. Total varia de acordo com a quantidade de dias.">Valor Total p/ Dia</option>
                                                                <option value="pessoa" title="Valor individual da despesa para todos os dias. Total varia de acordo com a quantidade de pessoas.">Valor Total p/ Pessoa</option>
                                                                <option value="pessoa_dia" title="Valor individual por dia. Total varia de acordo com a quantidade de pessoas e dias.">Valor x Pessoa x Dia</option>
                                                            </select>
                                                            <div class="mt-3 mb-2" data-valor-dias style="display:none;">
                                                                <input type="number" class="form-control form-control-sm form-control-solid" name="valor_dias" placeholder="Qtd dias da despesa" min="0" max="1" onchange="">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                </td>
                                                <td style="vertical-align:middle;">
                                                    <button type="button" class="btn btn-block btn-sm btn-danger" onclick="$(this).parents('tr').remove();"><i class="fas fa-trash"></i></button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label class="small">Tipo despesa</label>
                                                    <select class="form-control form-control-sm form-control-solid" name="tipo_despesa">
                                                        <option value="Hospedagem">Hospedagem</option>
                                                        <option value="Transporte">Transporte</option>
                                                        <option value="Alimentação">Alimentação</option>
                                                        <option value="Personalizado">Personalizado</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <label class="small">Nome da despesa</label>
                                                    <input type="text" class="form-control form-control-sm form-control-solid" name="nome_despesa" maxlength="30">
                                                </td>
                                                <td>
                                                    <label class="small">Valor</label>
                                                    <div class="row">
                                                        <div class="col-12 col-lg-6 mb-1">
                                                            <div class="input-group input-group-sm">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text form-control-solid">R$</span>
                                                                </div>
                                                                <input type="text" class="form-control form-control-sm form-control-solid" name="valor" placeholder="1234,99" dobbin-validate-valor maxlength="30" required>
                                                                
                                                            </div>
                                                            
                                                        </div>
                                                        <div class="col-12 col-lg-6">
                                                            <select class="form-control form-control-sm form-control-solid mb-1" name="valor_tipo">
                                                                <option value="total" title="Valor integral da despesa para todos os passageiros durante o período do roteiro.">Valor Total</option>
                                                                <option value="dia" title="Valor diário da despesa para todos passageiros. Total varia de acordo com a quantidade de dias.">Valor Total p/ Dia</option>
                                                                <option value="pessoa" title="Valor individual da despesa para todos os dias. Total varia de acordo com a quantidade de pessoas.">Valor Total p/ Pessoa</option>
                                                                <option value="pessoa_dia" title="Valor individual por dia. Total varia de acordo com a quantidade de pessoas e dias.">Valor x Pessoa x Dia</option>
                                                            </select>
                                                            <div class="mt-3 mb-2" data-valor-dias style="display:none;">
                                                                <input type="number" class="form-control form-control-sm form-control-solid" name="valor_dias" placeholder="Qtd dias da despesa" min="0" max="1" onchange="">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                </td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="addDespesa(this)">Adicionar despesa</button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 text-right">
                                    <button type="button" class="btn btn-sm btn-primary" onclick="proximo(this); calcular();">Próximo</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="border bloco-acord">
                        <div class="acord-header d-flex justify-content-between">
                            <h6 class="text-uppercase my-1 font-weight-bold text-primary">
                                RESULTADO
                            </h6>
                            <button type="button" class="btn btn-transparent btn-sm text-dark"><i class="fas fa-angle-down"></i></button>
                        </div>
                        <div class="acord-body p-2 py-3 pt-0 border border-secondary border-bottom-0 border-left-0 border-right-0" style="display:none;">
                            <div class="row">
                                <div class="col-12 text-center">
                                    <button type="button" class="btn btn-sm btn-primary" onclick="calcular()">Calcular resultado</button>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12">
                                    <table class="table table-sm table-bordered table-responsive-sm resultado-simu" >
                                        <thead>
                                            <tr class="table-info">
                                                <th colspan="2" class="text-center" simu-nome></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                            <tr>
                                                <td simu-ida></td>
                                                <td simu-volta></td>
                                            </tr>
                                            <tr>
                                                <td rowspan="2">
                                                    <strong>Total de passageiros:</strong>
                                                </td>
                                                <td simu-total-pass></td>
                                            </tr>
                                            <tr>
                                                <td simu-desc-pass></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <strong>Total de despesas:</strong>
                                                </td>
                                                <td simu-total-despesa></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <strong>Valor p/ cliente:</strong><br>
                                                    <span class="small">(Coordenador isento)</span>
                                                </td>
                                                <td simu-desc-despesa></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <strong>Valor p/ cliente:</strong><br>
                                                    <span class="small">(Coordenador isento)</span>
                                                </td>
                                                <td simu-desc-despesa-lucro></td>
                                            </tr>
                                            <!--
                                            <tr>
                                                <td rowspan="2">
                                                    <strong>Total das despesas:</strong>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                            </tr>
                                            -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--
                    <div class="border bloco-acord">
                        <div class="acord-header d-flex justify-content-between">
                            <h6 class="text-uppercase my-1 font-weight-bold text-primary">
                                Parte 4
                            </h6>
                            <button type="button" class="btn btn-transparent btn-sm text-dark"><i class="fas fa-angle-down"></i></button>
                        </div>
                        <div class="acord-body p-2 py-3 pt-0 border border-secondary border-bottom-0 border-left-0 border-right-0" style="display:none;">
                            <div class="row">
                                <div class="col-12">
                                
                                </div>
                            </div>
                        </div>
                    </div>
                    -->
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function proximo(sender)
{
    for(let i = 0; i < $(sender).parents('.acord-body').find('[required]').length; i++) {
        if($(sender).parents('.acord-body').find('[data-example]').length > 0) {
            if($(sender).parents('.acord-body').find('tr:not([data-example]) [required]').eq(i).val() == '') {
                $(sender).parents('.acord-body').find('tr:not([data-example]) [required]').eq(i).focus();
                alerta('Preencha todos os campos, antes de continuar.','Oops...', 'info');
                console.log($(sender).parents('.acord-body').find('tr:not([data-example]) [required]').eq(i));
                return false;
            }
        } else {
            if($(sender).parents('.acord-body').find('[required]').eq(i).val() == '') {
                $(sender).parents('.acord-body').find('[required]').eq(i).focus();
                alerta('Preencha todos os campos, antes de continuar.','Oops...', 'info');
                console.log($(sender).parents('.acord-body').find('[required]').eq(i));
                return false;
            }
        }
        
    }
    $(sender).parents('.acord-header').trigger('click');
    $(sender).parents('.bloco-acord').next().children('.acord-header').trigger('click');
    $(sender).parents('.bloco-acord').next().find(':input').eq(0).focus();
}

function anterior(sender)
{
    $(sender).parents('.acord-header').trigger('click');
    $(sender).parents('.bloco-acord').prev().children('.acord-header').trigger('click');
    $(sender).parents('.bloco-acord').prev().find(':input').eq(0).focus();
}

function addDespesa(sender)
{
    if($(sender).siblings('table').find('tr[data-example]').length == 0) {
        alerta('Essa página foi atualizada, pois houve alguma alteração indevida na página.','Espera...', 'info');
        loadLanding(location.hash);
        return false;
    }

    if($(sender).siblings('table').find('tr:not([data-example])').length >= 10) {
        alerta('Limite de despesas atingido!', 'Ops...', 'info');
        return false;
    }


    $(sender).siblings('table').find('tr[data-example]').clone().appendTo($(sender).siblings('table'));
    $(sender).siblings('table').find('tr').last().removeAttr('data-example').show();
    $(sender).siblings('table').find('tr').last().find(':input').prop('required', true);
    $(sender).siblings('table').find('tr').last().find('[data-valor-dias] :input').prop('required', false);
}

function calcular()
{
    let result = $('.resultado-simu');
    let form = $('#simulacao');
    //result.slideUp(100);

    // Validação!
    for(let i = 0; i < form.find('[required]').length; i++) {
        if(form.find('[required]').eq(i).parents('[data-example]').length == 0) {
            if(form.find('[required]').eq(i).val() == '') {
                if(form.find('[required]').eq(i).parents('.bloco-acord').children('.acord-body').is(':visible') == false) {
                    form.find('[required]').eq(i).parents('.bloco-acord').children('.acord-header').trigger('click');
                }
                form.find('[required]').eq(i).focus();
                alerta('Preencha todos os campos, antes de continuar.','Oops...', 'info');
                return false;
            }
        }
    }

    // Nome da simulação.
    $('[simu-nome]').html('<strong class="small">Nome da simulação: </strong><br>'+ form.find('[name="nome_pacote"]').val());

    // Data da viagem
    $('[simu-ida]').html('<strong class="small">PARTIDA: </strong><br> '+ Dobbin.formataData(new Date(form.find('[name="data_ini"]').val()), true));
    $('[simu-volta]').html('<strong class="small">RETORNO: </strong><br> '+ Dobbin.formataData(new Date(form.find('[name="data_fim"]').val()), true));

    let clientes = parseInt(form.find('[name="passagens"]').val());
    let coord = parseInt(form.find('[name="qtd_coord"]').val());
    let passagens = clientes + coord;
    let dias_total;
    let rateio_qtd;

    let tipo_rateio = $('#simulacao [name="tipo_rateio"]:checked').val();
    switch(tipo_rateio) {
        case 'automatico66': rateio_qtd = Math.ceil(clientes*0.66666); break;
        case 'personalizado': rateio_qtd = $('#simulacao [name="rateio_qtd"]').val(); break;
        case 'automatico': default: rateio_qtd = clientes; break;
    }
    

    $('[simu-total-pass]').text(passagens);
    $('[simu-desc-pass]').html('<ul><li><b>Clientes:</b> '+clientes+'</li> <li><b>Coordenadores:</b> '+coord+'</li></ul>');

    let valores = [];
    let despesa_total = 0;
    let temp1, temp2;

    // Descritivo do cálculo
    let calc_desc = [];
    let calc_valor = [];
    let calc_oper =  [];

    for(let i = 0; i < form.find('tr:not([data-example])').length; i++) {
        switch(form.find('tr:not([data-example])').eq(i).find('[name="valor_tipo"] :selected').val()) {
            case 'total': // Valor integral. Sem alteração.
                temp1 = form.find('tr:not([data-example])').eq(i).find('[name="valor"]').val();
                if(Dobbin.isMoney(temp1) == false) {
                    form.find('tr:not([data-example])').eq(i).find('[name="valor"]').focus();
                    alerta('Esse valor é inválido.');
                }

                temp1 = converteRealEmCentavo(temp1);
                valores.push({
                    valor: temp1,
                    tipo: 'total'
                });
                despesa_total += temp1;

                // Descrição do cálculo.
                calc_desc.push('<span class="text-uppercase">'+form.find('tr:not([data-example])').eq(i).find('[name="tipo_despesa"] :selected').val() + '</span> - '+
                    form.find('tr:not([data-example])').eq(i).find('[name="nome_despesa"]').val());
                calc_valor.push('<strong>R$'+ converteCentavoEmReal(temp1)+'</strong>');
                calc_oper.push('<span class="text-primary">+</span>');
                break;

            case 'pessoa': // Valor por pessoa. Multiplica pelo total de passageiros e armazena valor integral.
                temp1 = form.find('tr:not([data-example])').eq(i).find('[name="valor"]').val();
                if(Dobbin.isMoney(temp1) == false) {
                    form.find('tr:not([data-example])').eq(i).find('[name="valor"]').focus();
                    alerta('Esse valor é inválido.');
                }

                temp1 = converteRealEmCentavo(temp1);

                valores.push({
                    valor: temp1,
                    tipo: 'pessoa'
                });
                

                // Descrição do cálculo.
                calc_desc.push('<span class="text-uppercase">'+form.find('tr:not([data-example])').eq(i).find('[name="tipo_despesa"] :selected').val() + '</span> - '+
                    form.find('tr:not([data-example])').eq(i).find('[name="nome_despesa"]').val());
                calc_valor.push('R$'+ converteCentavoEmReal(temp1)+ ' (x '+passagens+' passageiros) = <br> <strong>R$ '+ converteCentavoEmReal(temp1*passagens) +'</strong>');
                calc_oper.push('<span class="text-primary">+</span>');

                
                // Multiplica pelo total de passageiros.
                temp1 = temp1*passagens;

                despesa_total += temp1;
                break;

            case 'dia': // Valor total do dia. Depende da quantidade de dias do roteiro.
                temp1 = form.find('tr:not([data-example])').eq(i).find('[name="valor"]').val();
                if(Dobbin.isMoney(temp1) == false) {
                    form.find('tr:not([data-example])').eq(i).find('[name="valor"]').focus();
                    alerta('Esse valor é inválido.');
                    return false;
                }

                if(
                    form.find('tr:not([data-example])').eq(i).find('[name="valor_dias"]').val() == '' || 
                    form.find('tr:not([data-example])').eq(i).find('[name="valor_dias"]').val() == 0
                ) {
                    form.find('tr:not([data-example])').eq(i).find('[name="valor_dias"]').focus();
                    alerta('Informe uma quantidade de dias válido. Cálculo abortado.');
                    return false;
                }

                // Quantidade de dias.
                temp2 = form.find('tr:not([data-example])').eq(i).find('[name="valor_dias"]').val();

                temp1 = converteRealEmCentavo(temp1);
                valores.push({
                    valor: temp1,
                    tipo: 'dia',
                    dias: temp2
                });

                // Multiplica o valor pela quantidade de dias.
                despesa_total += temp1 * temp2;

                // Descrição do cálculo.
                calc_desc.push('<span class="text-uppercase">'+form.find('tr:not([data-example])').eq(i).find('[name="tipo_despesa"] :selected').val() + '</span> - '+
                    form.find('tr:not([data-example])').eq(i).find('[name="nome_despesa"]').val());
                calc_valor.push('R$'+ converteCentavoEmReal(temp1)+ ' (x '+temp2+' dia(s)) = <br> <strong>R$ '+ converteCentavoEmReal(temp1*temp2) +'</strong>');
                calc_oper.push('<span class="text-primary">+</span>');

                break;

            case 'pessoa_dia': // Valor por pessoa e por dia. Depende da quantidade de pessoas e dias do roteiro.
                temp1 = form.find('tr:not([data-example])').eq(i).find('[name="valor"]').val();
                if(Dobbin.isMoney(temp1) == false) {
                    form.find('tr:not([data-example])').eq(i).find('[name="valor"]').focus();
                    alerta('Esse valor é inválido.');
                    return false;
                }

                if(
                    form.find('tr:not([data-example])').eq(i).find('[name="valor_dias"]').val() == '' || 
                    form.find('tr:not([data-example])').eq(i).find('[name="valor_dias"]').val() == 0
                ) {
                    form.find('tr:not([data-example])').eq(i).find('[name="valor_dias"]').focus();
                    alerta('Informe uma quantidade de dias válido. Cálculo abortado.');
                    return false;
                }

                // Quantidade de dias.
                temp2 = form.find('tr:not([data-example])').eq(i).find('[name="valor_dias"]').val();

                temp1 = converteRealEmCentavo(temp1);
                valores.push({
                    valor: temp1,
                    tipo: 'pessoa_dia',
                    dias: temp2
                });

                // Multiplica o valor pela quantidade de dias e pela quantidade de pessoas.
                despesa_total += temp1 * temp2 * passagens;

                // Descrição do cálculo.
                calc_desc.push('<span class="text-uppercase">'+form.find('tr:not([data-example])').eq(i).find('[name="tipo_despesa"] :selected').val() + '</span> - '+
                    form.find('tr:not([data-example])').eq(i).find('[name="nome_despesa"]').val());
                calc_valor.push('R$'+ converteCentavoEmReal(temp1)+ ' (x '+temp2+' dia(s)) (x '+passagens+' passageiros) = <br> <strong>R$ '+ converteCentavoEmReal(temp1*temp2*passagens) +'</strong>');
                calc_oper.push('<span class="text-primary">+</span>');
                break;
        }
    }
    temp1 = undefined;

    temp1 = Math.ceil(despesa_total/rateio_qtd);
    

    $('[simu-total-despesa]').text('R$ '+converteCentavoEmReal(despesa_total));
    $('[simu-desc-despesa]').html('R$ '+converteCentavoEmReal(temp1)+' <small>(sem lucro)</small>');
    // Com lucro
    $('[simu-desc-despesa-lucro]').html('R$ '+converteCentavoEmReal( Math.ceil((despesa_total * 1.3)/rateio_qtd) ) +' <small>(lucro 30%)</small>');

    if(result.siblings('table').length > 0) {
        result.siblings('table').remove();
    }
    result.after('<table class="table table-bordered table-responsive-sm table-sm" simu-table-calculo><thead class="thead-dark font-weight-bold"><tr><th colspan="3" class="text-center text-uppercase">Demonstrativo detalhado de cálculo</th></tr><tr><th>Item</th> <th>Valor</th> <th class="small">Operação</th></tr>'+
    '</thead><tbody></tbody></table>');
    calc_desc.forEach(function(val, key){
        $('[simu-table-calculo]').find('tbody').append('<tr><td>'+val+'</td> <td>'+calc_valor[key]+'</td> <td>'+calc_oper[key]+'</td></tr>');
    });
    $('[simu-table-calculo]').find('tbody').append('<tr class="p-2"><td colspan="3">&nbsp;</td></tr> <tr><td><strong>TOTAL DAS DESPESAS</strong></td> <td>R$ '+converteCentavoEmReal(despesa_total)+'</td> <td><span class="text-success">=</span></td></tr>');
    $('[simu-table-calculo]').find('tbody').append('<tr><td><label class="small">LUCRO NO RATEIO (%)</label><div class="input-group input-group-sm">'+
    '<input type="number" class="form-control form-control-sm form-control-solid" name="lucro" value="30" min="0" data-despesa-total="'+despesa_total+'" data-rateio="'+rateio_qtd+'" data-clientes="'+clientes+'" onchange="lucroCalculo(this)">'+
    '<div class="input-group-append"><span class="input-group-text form-control-solid form-control-sm">%</span></div></div></td> '+
    '<td>R$ '+converteCentavoEmReal(despesa_total)+'</td> <td><span class="text-info">%</span></td></tr>');

    $('[simu-table-calculo]').find('[name="lucro"]').trigger('change');

    console.log(valores);

}

function lucroCalculo(sender)
{
    let valor = parseInt($(sender).data('despesa-total'));
    let margem = 1+ (parseInt($(sender).val())/100);
    let clientes = parseInt($(sender).data('clientes'));
    let rateio_qtd = parseInt($(sender).data('rateio'));
    let lucPoltronaLivre = Math.ceil((valor*margem)/rateio_qtd) * (clientes - rateio_qtd);
    let lucRateio = (valor*margem) - valor;
    
    $(sender).parents('tr').eq(0).children('td:eq(1)').html('R$ '+converteCentavoEmReal(valor*margem)+' [LUCRO DO RATEIO: <span class="text-success">R$ '+converteCentavoEmReal(lucRateio)+'</span> ]'+
    '<br>Valor p/ cliente: <strong>R$ '+converteCentavoEmReal(Math.ceil((valor*margem)/rateio_qtd))+'</strong> (x'+rateio_qtd+' cliente(s))');

    if(rateio_qtd < clientes) {
        // Calcula o valor de lucro das poltronas livres.
        $(sender).parents('tr').eq(0).children('td:eq(1)').append('<hr> <span class="text-success">+ R$ '+converteCentavoEmReal( lucPoltronaLivre ) + '</span> &nbsp; '+
        '<small>(LUCRO POLTRONAS LIVRES: <span class="font-weight-bold">'+(clientes - rateio_qtd)+'</span>)</small>'+
        '<br><span class="text-primary font-weight-bold">= R$ '+ converteCentavoEmReal( lucPoltronaLivre + lucRateio) +'</span> &nbsp; <small>(LUCRO TOTAL PREVISTO)</small>');
    }

}

$(document).ready(function(){
    $(document).on('blur', '#simulacao [name="data_ini"], #simulacao [name="data_fim"]', function(){
        if($('#simulacao [name="data_ini"]').val() != '' && $('#simulacao [name="data_fim"]').val() != '') {
            if($('#simulacao [name="data_ini"]').val() > $('#simulacao [name="data_fim"]').val()) {
                alerta('Data de retorno é menor que a data de partida.', 'Inválido!', 'warning');
                $('#simulacao [name="data_fim"]').focus();

                return false;
            }
            $('#simulacao [name="valor_dias"]').attr('max', Dobbin.diffDays( new Date($('#simulacao [name="data_ini"]').val()), new Date($('#simulacao [name="data_fim"]').val()) ) + 1);
        } else {
            $('#simulacao [name="valor_dias"]').attr('max', '1');
        }
    });

    $(document).on('change', '#simulacao [name="valor_dias"]', function(){
        if(parseInt($(this).val()) > parseInt($(this).attr('max'))) {
            $(this).val($(this).attr('max'));
            
        }
    });

    $(document).on('change', '#simulacao [name="passagens"]', function(){
        $('#simulacao [name="rateio_qtd"]').trigger('change');
    });

    $(document).on('change', '#simulacao [name="tipo_rateio"]', function(){
        console.log($(this).val());
        if($(this).val() == 'personalizado') {
            $('[data-rateio-div]').fadeIn(200);
            $('#simulacao [name="rateio_qtd"]').attr('required', true);
        } else {
            $('[data-rateio-div]').fadeOut(200);
            $('#simulacao [name="rateio_qtd"]').attr('required', false).val('');
        }
    });

    $(document).on('change', '#simulacao [name="rateio_qtd"]', function(){
        if($('#simulacao [name="tipo_rateio"]:checked').val() == 'personalizado') {
            // Verifica se o valor é maior que o número de clientes.
            if(parseInt($(this).val()) > parseInt($('#simulacao [name="passagens"]').val())) {
                $(this).val($('#simulacao [name="passagens"]').val());
            }

            $(this).prop('max', $('#simulacao [name="passagens"]').val());
        }
    });
});
</script>