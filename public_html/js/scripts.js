const PREFIX_POST = 'form/';
var systemVersion = '';
var dbLocal = {"clientesDB":[], "clientesDBHora":[], "usuariosDB":[], "usuariosDBHora":[]};
var timeoutUpdate;
var timerKEY1;
var debugEnabled = false;
var sidebarAlwaysShow = true;

var geralIntervalo5Min, geralIntervalo10Min, geralIntervalo15Min; // Intervalos

var teste;

function debugador(ev)
{
    if(debugEnabled == true) {
        console.log('%c> Debugador do Dobbin iniciado...', 'font-size: 16px; letter-spacing: 1px; font-family: Courier,monospace;');
        console.log('Evento:');
        console.log(ev);
        console.log('Resposta do servidor:');
        console.log(ev.responseText);
        console.log('%c> Debugador do Dobbin finalizado!', 'font-size: 16px; letter-spacing: 1px; font-family: Courier,monospace;');

    }
}

function configLocalStorageStart() {
    if(localStorage.Config !== undefined) {
        let config = JSON.parse(localStorage.Config);

        // Carrega os valores para suas variáveis.
        sidebarAlwaysShow = config.sidebarAlwaysShow;
        debugEnabled = config.debugEnabled;

        // Faz algumas correções no sistema.
        if(sidebarAlwaysShow == false) {
            $('#sidebarFixedToggle').removeClass('alwaysShow');
            $('#sidebarFixedToggle').html('<i class="fas fa-toggle-off"></i>');
        }

        if(debugEnabled == true){
            console.log('Configuração do Dobbin carregada: ');
            console.log(config);
        }
    } else {
        // Define parâmetros iniciais do Dobbin.
        let config = {
            sidebarAlwaysShow: sidebarAlwaysShow,
            debugEnabled: debugEnabled,
        };

        localStorage.Config = JSON.stringify(config);
    }
}

function configLocalStorageSet(chave, valor) {
    let config = JSON.parse(localStorage.Config);
    // Define valor.
    config[chave] = valor;

    // Salva
    localStorage.Config = JSON.stringify(config);

    // Recarrega
    configLocalStorageStart();
}

function loadLanding(href)
{
    if(href.charAt(0) == '#') {
        href = href.substring(1, href.length);
    }

    let querystring = '';

    if(location.search.length > 0) {
        // Há outras instruções.
        // Remove a interrogação e faz um explode.
        querystring = location.search.substring(1, location.search.length);
        querystring = querystring.split('&');
        console.log(querystring);
        querystring.forEach(function(a, chave){
            let tmp = a.split('=');
            querystring[chave] = {
                'comando': tmp[0],
                'valor': tmp[1]
            }
            tmp = undefined;
        });
        
    } else {
        if(location.href.search(/[?]/) >= 0) {
            location.href = location.href.replace('?', '');
        }
    }
    
    if(querystring.length > 0) {
        console.log(querystring);
    }
    

    // Oculta todos os tooltips.
    $('[data-toggle="tooltip"]').tooltip('hide');

    $.post(href, function(res){
        //console.log(res);
        $('#content').fadeOut('fast', function(){
            $('#bodyContent').html(res.page);
            $('#content .page-header-title').html(res.title);
            $('#content .page-header-description').html(res.description ? res.description : '');
            $('#content').fadeIn('fast');
            sidebarAutoHide();

            $('[data-toggle="tooltip"]').tooltip();
            $('[data-toggle="popover"]').popover({"html":true});

            // Varredura de querystring para comandos.
            if(querystring.length > 0) {
                querystring.forEach(function(q){
                    switch(q.comando){
                        case 'return':
                            $('#content .page-header-title').append(' <button type="button" class="ml-2 btn btn-secondary font-weight-bold" onclick="location.hash = \'#'+q.valor+'\'; location.search=\'\'; loadLanding(\'#'+q.valor+'\');"><i class="fas fa-reply"></i> Voltar para outra página</button>');
                            break;
                    }
                });
            }
            // Ativa gatilhos da página: $(document).ready();
            gatilhosLoadLanding();
        });
    }, 'json')
    .fail(function(ev, st){
        //console.log(ev);
        debugador(ev);
        switch(ev.status) {
            case 511:
                window.location.replace('/login');
                break;

            case 404:
                location.hash ='#inicio';
                loadLanding(location.hash);
        }

        alerta('A página <i><strong>'+href.toUpperCase()+'</strong></i> está indisponível.', '', 'secondary');
        if(st == 'parsererror') {
            $('#bodyContent').html('<h3 class="my-4 p-3 rounded-sm bg-white">Erro <small>(informe ao desenvolvedor)</small>:</h3>'+ev.responseText);
            $('#content').fadeIn('fast');
            console.log(ev.responseText);
        }
        

    });
}

function gatilhosLoadLanding()
{
    /**
     * Organiza todos os gatilhos de cada página em uma única função.
     * Primeiro, identifica a página e depois ativa as funções correspondentes.
     * 
     * PS: Nem todas as funções serão ativadas aqui. Somente as que precisam ser executadas em intervalos de tempo.
     */

    let local = location.hash;
    if(local.charAt(0) == '#') {
        local = local.substring(1, local.length);
    }
    clearInterval(geralIntervalo5Min);
    clearInterval(geralIntervalo10Min);
    clearInterval(geralIntervalo15Min);

    switch(true) { // procura o regex que for verdadeiro
        case /(roteiros\/ver\/)([0-9])/gi.test(local): // Roteiros > Ver
            $(document).ready(function(){
                setTimeout(function(){getClientesRoteiro();}, 1000);
                geralIntervalo5Min = setInterval(function(){getClientesRoteiro(); getEstoqueRoteiro();}, 180000);
            }); break;

        case /(vendas)/gi.test(local): // Vendas
            $(document).ready(function(){
                setTimeout(function(){
                    getVendasReservas();getVendasAguardandoPagamento();
                    getVendasPagas();getVendasEstornadas();
                }, 1000);
                geralIntervalo5Min = setInterval(function(){
                    getVendasReservas();
                    getVendasAguardandoPagamento();
                    getVendasPagas();
                    getVendasEstornadas();
                }, 180000);
            }); break;

    }
}

function checkSystemUpdate()
{
    $.post('/checkversion', function(res){
        if(systemVersion != res.version) {
            alert('A plataforma tem uma nova versão disponível ['+systemVersion+' >> '+res.version+']! A página será atualizada imediatamente...');
            location.reload();
        } else {
            //console.log('Verificando atualização: versão mais recente já instalada.');
        }
    }, 'json');

    timeoutUpdate = setTimeout(checkSystemUpdate, 300000); // Verifica a cada 5min (300000ms).
}

function converteCentavoEmReal(centavos = 0) {
    centavos = parseInt(centavos);
    let real, cents, invert = false;

    // Trato sinal (se negativo)
    if(centavos < 0) {
        invert = true;
        centavos = centavos*(-1);
    }

    // Trato REAL
    if(centavos >= 100) {
        // Mais de 1 real.
        real = Math.floor(centavos/100);
        cents = centavos%100;
    } else {
        // Menos de 1 real.
        real = 0;
        cents = centavos;
    }

    // Trato CENTAVOS
    if(cents < 10) {
        cents = '0'+cents;
    }

    if(invert === false) {
        return real+','+cents;
    } else {
        return '-'+real+','+cents;
    }
}

function converteRealEmCentavo(valor = '0,00') {
    if(Dobbin.isMoney(valor) == false) {
        return false;
    }

    if(valor.search(',') >= 0) {
        // Tem vírgula. Remove virgula.
        valor = valor.replace(',', '');
        return parseInt(valor);
    } else {
        // Não tem virgula. Multiplica por 100.
        valor = parseInt(valor)*100;
        return valor;
    }
}

function sidebarAutoHide()
{
    if($('body').outerWidth() > 576) {
        // Tela grandes, verifica se é pra ocultar ou exibir.
        
        if(sidebarAlwaysShow == true) {
            $('#sidebar').addClass('show');
        } else {
            $('#sidebar').removeClass('show');
        }
    } else {
        // Tela pequenas, sempre oculta menu
        $('#sidebar').removeClass('show');
    }
}

function highlight(texto, destaque)
{
    // Encontra DESTAQUE dentro de TEXTO e destaca ele.

    let regex = new RegExp(destaque, "ig");
    
    let novo = texto.replace(regex, function(x){
        return '<span style="background-color:yellow">'+x+'</span>';
    });
    
    
    return novo;
}

/**
 * FUNÇÕES DE VALIDAÇÃO
 */

function resetValidaOnChange(sender)
{
    $(sender).removeClass('is-invalid').removeClass('is-valid');
}

function validaValorDinheiroOnChange(sender)
{
    let valor = $(sender).val();
    if(valor.length > 0) {
        // Procura pontos.
        if(valor.search(/\./gi) >= 0) {
            // Remove os pontos
            valor = valor.replace(/\./gi, '');
            $(sender).val(valor);
        }

        // Valores entre 0,00 e 9.999.999,99
        let patt = /(^[0-9]{1,7}[,]{1}[0-9]{2}$|^([0-9]{1,7})$)/gi;
        if(patt.test(valor) == false) {
            if($(sender).siblings('.invalid-feedback').length == 0) {
                $(sender).after('<div class="invalid-feedback">Só permitido valores entre 0,00 e 9999999,99 (9 milhões). Valor sem casa decimal também é válido. Ex.: 0 a 9999999.</div>');
            }

            $(sender).addClass('is-invalid');
            $(sender).focus();
            return false;
        } else {
            $(sender).addClass('is-valid');
        }
    }
    return true;
}

function validaSenhaOnChange(sender)
{
    let valor = $(sender).val();
    let pattLetra = /[A-z]/g;
    let pattNumero = /[0-9]/g;
    let pattEspecial = /[^\w]/g;

    let aprovada = 0;

    if($(sender).siblings('.invalid-feedback').length == 0) {
        $(sender).after('<div class="invalid-feedback"><ul class="ml-1 py-0"></ul></div>');
    } else {
        $(sender).siblings('.invalid-feedback').html('<ul class="ml-1 py-0"></ul>');
    }

    let feedback = $(sender).siblings('.invalid-feedback').children('ul');

    // Verifica se cada validação foi aprovada.
    if(pattLetra.test(valor) === true) {
        aprovada++;
    } else {
        feedback.append("<li>Necessário uma letra maiúscula ou minúscula.</li>");
    }

    if(pattNumero.test(valor) === true) {
        aprovada++;
    } else {
        feedback.append("<li>Necessário um número.</li>");
    }

    if(pattEspecial.test(valor) === true) {
        aprovada++;
    } else {
        feedback.append("<li>Necessário um caractere especial.</li>");
    }

    if(aprovada == 3) {
        $(sender).addClass('is-valid');
    } else {
        $(sender).addClass('is-invalid');
        $(sender).focus();
    }
}

/**
 * ./FUNÇÕES DE VALIDAÇÃO
 */

/** FUNÇÕES NATIVAS DA PLATAFORMA */
function nativePOSTFail(ev)
{
    $('#splash-screen').fadeOut();
    switch(ev.status)
    {
        case 200:
            alerta('O servidor recebeu a solicitação, mas respondeu de forma inesperada.', 'Eu não entendi...', 'info');
            break;

        case 403:
            alerta('Essa operação foi recusada pelo servidor.', 'Negado!', 'danger');
            break;

        case 404:
            alerta('O servidor retornou 404: CAMINHO NÃO ENCONTRADO. Tente novamente mais tarde..', 'Falha!', 'warning');
            break;

        default:
            alerta('Não foi possível salvar/recuperar esses dados agora.', 'Falha na comunicação!', 'danger');
            break;
    }
    
    //console.log(ev);
    debugador(ev);
}
/** ./FUNÇÕES NATIVAS DA PLATAFORMA */

/**
 * MODAIS E JANELAS SUSPENSAS
 */

function janClienteSelect(target)
{
    if($(target).length == 0) {
        alerta('Há algum erro lógico aqui... Informe ao desenvolvedor.');
    } else {
        //console.log(target);
        $('#janClienteSelect').find('[data-selecionar]').attr('disabled','true');
        $('#janClienteSelect [name="busca"]').next('.text-muted').html('');
        $('#janClienteSelect [data-selecionar]').data('target', target);
        $('#janClienteSelect table').remove();
        $('#janClienteSelect form')[0].reset();



        $('#janClienteSelect').modal('show');
        //console.log($('#janClienteSelect [data-selecionar]').data('target'));
        
    }
}

function janParceirosSelect()
{
    //console.log(target);
    $('#janParceirosSelect').find('[data-selecionar]').attr('disabled','true');
    //$('#janParceirosSelect [name="busca"]').next('.text-muted').html('');
    $('#janParceirosSelect [name="busca"]').next().text('');

    $('#janParceirosSelect table').remove();
    $('#janParceirosSelect form')[0].reset();



    $('#janParceirosSelect').modal('show');
    //console.log($('#janClienteSelect [data-selecionar]').data('target'));
        
}

function janCoordenadorSelect(sender)
{
    if(sender == null || sender == undefined) {
        alerta('Houve um erro na solicitação. Não é possível continuar...', 'Abortado.', 'warning');
        return false;
    }

    //console.log(target);
    $('#janCoordenadorSelect').find('[data-selecionar]').attr('disabled','true');
    //$('#janParceirosSelect [name="busca"]').next('.text-muted').html('');
    $('#janCoordenadorSelect [name="busca"]').next().text('');

    $('#janCoordenadorSelect table').remove();
    $('#janCoordenadorSelect form')[0].reset();
    $('#janCoordenadorSelect form [name="rid"]').val($(sender).data('id'));



    $('#janCoordenadorSelect').modal('show');
        
}

function janRoteiroSelect(sender)
{
    if(sender == null || sender == undefined) {
        alerta('Houve um erro na solicitação. Não é possível continuar...', 'Abortado.', 'warning');
        return false;
    }

    //console.log(target);
    $('#janRoteirosSelect').find('[data-selecionar]').attr('disabled','true');
    //$('#janParceirosSelect [name="busca"]').next('.text-muted').html('');
    $('#janRoteirosSelect [name="busca"]').next().text('');

    $('#janRoteirosSelect table').remove();
    $('#janRoteirosSelect form')[0].reset();
    $('#janRoteirosSelect [data-selecionar]').data('target', sender);



    $('#janRoteirosSelect').modal('show');
}


function janDinamicaGatilhos()
{
    // Dispara alguns gatilhos da janela dinâmica.

    // Torna o campo editável.
    $('span[dobbin-campo-editavel], div[dobbin-campo-editavel]').after(
        ' <button type="button" class="btn btn-xs btn-secondary mr-md-2"><i class="fas fa-pen"></i></button> '
    );
}

function loadCoordenador(id)
{
    $.post('/coordenadores/ver/'+id, function(res){
        if(res.success == true) {
            console.log(res);
            let c = res.coordenador;
            $('#modalCoordenadorDetalhes [data-detalhes-nome]').text(c.nome);
            $('#modalCoordenadorDetalhes [data-detalhes-id]').text(c.id);
            $('#modalCoordenadorDetalhes [data-detalhes-email]').text(c.email);
            $('#modalCoordenadorDetalhes [data-detalhes-tel]').text(c.telefone);
            $('#modalCoordenadorDetalhes [data-detalhes-nascimento]').text(function(){
                if(c.nascimento == '') {return '';} else {return Dobbin.formataData(new Date(c.nascimento+'T00:00:00-0300'));}
            });
            $('#modalCoordenadorDetalhes [data-detalhes-civil]').text(c.estado_civil.toUpperCase());
            $('#modalCoordenadorDetalhes [data-detalhes-rg]').text(c.rg);
            $('#modalCoordenadorDetalhes [data-detalhes-cpf]').text(c.cpf);
            $('#modalCoordenadorDetalhes [data-detalhes-sangue]').text(c.sangue);
            $('#modalCoordenadorDetalhes [data-detalhes-endereco]').text(c.endereco);
            $('#modalCoordenadorDetalhes [data-detalhes-complemento]').text(c.complemento);
            $('#modalCoordenadorDetalhes [data-detalhes-pontoref]').text(c.ponto_referencia);
            $('#modalCoordenadorDetalhes [data-detalhes-bairro]').text(c.bairro);
            $('#modalCoordenadorDetalhes [data-detalhes-cep]').text(c.cep);
            $('#modalCoordenadorDetalhes [data-detalhes-cidade]').text(c.cidade);
            $('#modalCoordenadorDetalhes [data-detalhes-estado]').text(c.estado);
            $('#modalCoordenadorDetalhes [data-detalhes-alergia]').text(c.alergia);
            $('#modalCoordenadorDetalhes [data-detalhes-emergencianome]').text(c.emergencia_nome);
            $('#modalCoordenadorDetalhes [data-detalhes-emergenciatel]').text(c.emergencia_telefone);
            $('#modalCoordenadorDetalhes [data-detalhes-criadoem]').text(Dobbin.formataDataHora(new Date(c.criado_em)));
            $('#modalCoordenadorDetalhes [data-detalhes-atualizadoem]').text(Dobbin.formataDataHora(new Date(c.atualizado_em)));


            if(c.deletado_em == null) {
                $('#modalCoordenadorDetalhes [data-detalhes-deletado]').hide();
                $('#modalCoordenadorDetalhes [data-detalhes-deletado]').html();
            } else {
                $('#modalCoordenadorDetalhes [data-detalhes-deletado]').show();
                $('#modalCoordenadorDetalhes [data-detalhes-deletado]').html('<span class="badge badge-danger" data-toggle="tooltip" title="Apagado por: '+c.usuario+' em '+Dobbin.formataDataHora(new Date(c.deletado_em))+'"><i class="fas fa-times"></i> APAGADO</span>');
            }

            
            $('#modalCoordenadorDetalhes').modal('show');
            $('[data-toggle="popover"]').popover({'html':true});
            $('[data-toggle="tooltip"]').tooltip();
        } else {
            alerta(res.mensagem, 'Erro!', 'warning');
            return false;
        }
    }, 'json').
    fail(function(ev){
        switch(ev.statusCode) {
            case 404:
                alerta('Não encontrado.', 'Erro!', 'danger');
                break;
        }
        //console.log(ev);
        debugador(ev);
    });
}

function loadCliente(id)
{
    id = parseInt(id);

    $.post('/clientes/ver/'+id, function(res){
        if(res.success == true) {
            console.log(res);
            let c = res.cliente;
            $('#modalClienteDetalhes [data-detalhes-nome]').text(c.nome);
            $('#modalClienteDetalhes [data-detalhes-id]:not(button)').text(c.id);
            $('#modalClienteDetalhes button[data-detalhes-id]').data('id', c.id);
            $('#modalClienteDetalhes [data-detalhes-email]').text(c.email);
            $('#modalClienteDetalhes [data-detalhes-tel]').text(c.telefone);
            $('#modalClienteDetalhes [data-detalhes-faixaetaria]').html(function(){
                switch(c.faixa_etaria) {
                    case '0-5': return '<span class="badge badge-info">0 - 5 ANOS</span>'; break;
                    case '6-12': return '<span class="badge badge-primary">6 - 12 ANOS</span>'; break;
                    case 'ADULTO': return '<span class="badge badge-dark">ADULTO</span>'; break;
                    case '60+': return '<span class="badge badge-secondary">60+ ANOS</span>'; break;
                    default: return '<span class="badge badge-secondary"> -- </span>'; break;
                }
            });
            $('#modalClienteDetalhes [data-detalhes-nascimento]').text(function(){
                if(c.nascimento == '') {return '';} else {return Dobbin.formataData(new Date(c.nascimento+'T00:00:00-0300'));}
            });
            $('#modalClienteDetalhes [data-detalhes-civil]').text(c.estado_civil.toUpperCase());
            $('#modalClienteDetalhes [data-detalhes-rg]').text(c.rg);
            $('#modalClienteDetalhes [data-detalhes-cpf]').text(c.cpf);
            $('#modalClienteDetalhes [data-detalhes-sangue]').text(c.sangue);
            $('#modalClienteDetalhes [data-detalhes-endereco]').text(c.endereco);
            $('#modalClienteDetalhes [data-detalhes-complemento]').text(c.complemento);
            $('#modalClienteDetalhes [data-detalhes-pontoref]').text(c.ponto_referencia);
            $('#modalClienteDetalhes [data-detalhes-bairro]').text(c.bairro);
            $('#modalClienteDetalhes [data-detalhes-cep]').text(c.cep);
            $('#modalClienteDetalhes [data-detalhes-cidade]').text(c.cidade);
            $('#modalClienteDetalhes [data-detalhes-estado]').text(c.estado);
            $('#modalClienteDetalhes [data-detalhes-alergia]').text(c.alergia);
            $('#modalClienteDetalhes [data-detalhes-emergencianome]').text(c.emergencia_nome);
            $('#modalClienteDetalhes [data-detalhes-emergenciatel]').text(c.emergencia_telefone);
            $('#modalClienteDetalhes [data-detalhes-taxaextracasal]').text(converteCentavoEmReal(c.taxa_extra_casal));
            $('#modalClienteDetalhes [data-detalhes-criadoem]').text(Dobbin.formataDataHora(new Date(c.criado_em)));
            $('#modalClienteDetalhes [data-detalhes-atualizadoem]').text(Dobbin.formataDataHora(new Date(c.atualizado_em)));


            if(c.deletado_em == null) {
                $('#modalClienteDetalhes [data-detalhes-deletado]').hide();
                $('#modalClienteDetalhes [data-detalhes-deletado]').html();
            } else {
                $('#modalClienteDetalhes [data-detalhes-deletado]').show();
                $('#modalClienteDetalhes [data-detalhes-deletado]').html('<span class="badge badge-danger" data-toggle="tooltip" title="Apagado por: '+c.usuario+' em '+Dobbin.formataDataHora(new Date(c.deletado_em))+'"><i class="fas fa-times"></i> APAGADO</span>');
            }

            if(c.titular == 0) { // TITULAR
                $('#modalClienteDetalhes .tipoCliente').html('<span class="badge badge-dark">TITULAR</span>');
                $('#modalClienteDetalhes ul.breadcrumb').hide();

                if(c.dependentes.length == 0) {  // TITULAR SEM DEPENDENTE
                    $('#modalClienteDetalhes .tipoCliente').
                        attr('data-toggle', 'popover').
                        attr('data-content', "Sem dependentes.").
                        attr('data-trigger','hover focus').
                        attr('onclick', "$('#modalClienteDetalhes .listaDependentes').slideToggle(100);");
                        $('#modalClienteDetalhes .listaDependentes').html('Sem dependentes');
                        $('#modalClienteDetalhes .listaDependentes').hide();
                } else { // TITULAR COM DEPENDENTE
                    $('#modalClienteDetalhes .tipoCliente').
                        attr('data-toggle', 'popover').
                        attr('data-content', "Ver dependentes.").
                        attr('data-trigger','hover focus').
                        attr('onclick', "$('#modalClienteDetalhes .listaDependentes').slideToggle(100);");
                        $('#modalClienteDetalhes .listaDependentes').html('<table class="table table-sm table-hover table-responsive table-bordered"><thead class="bg-dark text-white"><tr><th>Cód.</th><th>Nome</th><th></th></tr></thead><tbody></tbody></table>');
                        $('#modalClienteDetalhes .listaDependentes').html('<h6 class="font-weight-bold">DEPENDENTES:</h6><div class="d-flex flex-wrap flex-column flex-md-row border border-secondary p-2 bg-light shadow-sm"></div>');
                        $('#modalClienteDetalhes .listaDependentes').show();

                        for(let i = 0; i < c.dependentes.length; i++) {
                            let d = c.dependentes[i];
                            $('#modalClienteDetalhes .listaDependentes > div').
                                append('<div onclick="$(this).parents(\'.modal\').eq(0).modal(\'hide\'); setTimeout(function(){loadCliente('+d.id+')}, 300);" '+
                                    'class="border hover px-3 py-2 mb-2 shadow-sm bg-secondary text-white" style="cursor:pointer"> '+
                                    '<strong class=" mr-2">'+d.nome+'</strong><small> [Cód.: '+d.id+']</small>'+
                                    '</div>');
                        }
                }
            } else { // DEPENDENTE
                $('#modalClienteDetalhes ul.breadcrumb').
                html('<li class="breadcrumb-item"><a href="javascript:void(0);" onclick="$(this).parents(\'.modal\').eq(0).modal(\'hide\'); setTimeout(function(){loadCliente('+c.titular+')}, 300);">Titular: '+c.titular_nome+'</a></li>').
                append('<li class="breadcrumb-item active">'+c.nome+'</li>').
                show();

                $('#modalClienteDetalhes .tipoCliente').html('<span class="badge badge-secondary">DEPENDENTE</span>');
                $('#modalClienteDetalhes .tipoCliente').attr('title', '').
                    attr('data-toggle', 'popover').
                    attr('data-content', "Clique para ver o TITULAR...").
                    attr('data-trigger','hover focus').
                    attr('onclick', "$(this).parents('.modal').eq(0).modal('hide'); setTimeout(function(){loadCliente("+c.titular+")}, 400);");
                    $('#modalClienteDetalhes .listaDependentes').html('');
                    $('#modalClienteDetalhes .listaDependentes').hide();
            }

            // Apaga espaço do histórico de compras
            $('#modalClienteDetalhes [data-historico-compras]').html('');
            getClienteVendas(id, function(vendas){
                if(vendas === false) {
                    $('#modalClienteDetalhes [data-historico-compras]').html('<div class="p-2 text-center font-italic">Houve um erro ao tentar retornar o histórico. Tente novamente mais tarde.</div>');
                } else if(vendas.length == 0) { // Sem histórico
                    $('#modalClienteDetalhes [data-historico-compras]').html('<div class="p-2 text-center font-italic">Nada no histórico.</div>');
                } else {
                    let html = '';
                    let situVenda = '';
                    let items, itemsStr = '';;
                    vendas.forEach(function(v){
                        switch(v.status) {
                            case 'Devolvida': situVenda = '<span class="ml-2 badge badge-dark">Devolvida</span>'; break;
                            case 'Cancelada': situVenda = '<span class="ml-2 badge badge-dark">Cancelada</span>'; break;
                            case 'Paga': situVenda = '<span class="ml-2 badge badge-success">Paga</span>'; break;
                            case 'Reserva': situVenda = '<span class="ml-2 badge badge-primary">Reserva</span>'; break;
                            case 'Aguardando': situVenda = '<span class="ml-2 badge badge-primary">Aguardando pagamento</span>'; break;
                            default: situVenda = '<span class="ml-2 badge badge-secondary">'+v.status+'</span>'; break;
                        }
                        if(v.items == '') {

                        } else {
                            items = JSON.parse(v.items);
                            items.forEach(function(i, indice){
                                itemsStr += "<tr> <td>"+(indice+1).toString()+"</td> <td>"+i.tarifa+"</td>  "+
                                "<td>R$ "+Dobbin.converteCentavoEmReal(i.valorUNI)+"</td> <td><span>"+i.qtd+"</span> </td> "+
                                "<td>R$ <span>"+Dobbin.converteCentavoEmReal(i.desconto)+"</span> </td> <td>R$ "+Dobbin.converteCentavoEmReal(i.subtotal)+"</td> </tr>"
                            });
                        }

                        html = '<div class="border bloco-acord">'+
                        '<div class="acord-header bg-light p-2 d-flex justify-content-between" style="cursor:pointer;">'+
                        '<h6 class="font-weight-bold text-uppercase my-1 text-primary"># '+v.id+' '+
                            '<small class="ml-2 text-dark">['+v.roteiro_nome+' - '+Dobbin.formataData(new Date(v.roteiro_data_ini), true)+' a '+
                            Dobbin.formataData(new Date(v.roteiro_data_fim), true)+'] '+situVenda+'</small></h6>'+
                        '<button class="btn btn-transparent btn-sm text-dark"><i class="fas fa-angle-down"></i></button>'+
                        '</div>'+
                        '<div class="acord-body p-2 py-3 pt-0 border border-secondary border-bottom-0 border-left-0 border-right-0" style="display:none">'+
                        '<strong>Data da reserva:</strong> '+Dobbin.formataData(new Date(v.data_reserva), true)+'<br><br>'+
                        '<table class="table table-sm table-bordered"> <thead> <tr>'+
                        '<th>#</th> <th>Tarifa</th> <th>Valor UNI</th> <th>Qtd</th> <th>Desconto</th> <th>Subtotal</th> </tr>'+
                        '</thead> <tbody> '+itemsStr+
                        '<tr> <td colspan="4"><strong>TOTAL:</strong></td> <td class="table-dark">R$ '+Dobbin.converteCentavoEmReal(v.desconto_total)+'</td> '+
                        '<td class="table-dark">R$ '+Dobbin.converteCentavoEmReal(v.valor_total)+'</td></tr> </tbody> </table>'+
                        '<a href="javascript:void(0)" onclick="getVenda('+v.id+')">Ver detalhes da compra...</a></div>'+
                        '</div>';


                        $('#modalClienteDetalhes [data-historico-compras]').append(html);
                        html = ''; situVenda = ''; itemsStr = ''; items = undefined;
                    });
                    
                }
                console.log(vendas);

            });

            
            $('#modalClienteDetalhes').modal('show');
            $('[data-toggle="popover"]').popover({'html':true});
            $('[data-toggle="tooltip"]').tooltip();
        } else {
            alerta(res.mensagem, 'Erro!', 'warning');
            return false;
        }
    }, 'json').
    fail(function(ev){
        switch(ev.statusCode) {
            case 404:
                alerta('Não encontrado.', 'Erro!', 'danger');
                break;
        }
        //console.log(ev);
        debugador(ev);
    });
}

function editaCoordenador(id)
{
    $.post('/coordenadores/ver/'+id, function(res){
        if(res.success == true) {
            console.log(res);
            let c = res.coordenador;
            $('#modalCoordenadorEditar strong[data-detalhes-nome]').text(c.nome);
            $('#modalCoordenadorEditar input[data-detalhes-nome]').val(c.nome);
            $('#modalCoordenadorEditar span[data-detalhes-id]').text(c.id);
            $('#modalCoordenadorEditar input[data-detalhes-id]').val(c.id);
            $('#modalCoordenadorEditar [data-detalhes-email]').val(c.email);
            $('#modalCoordenadorEditar [data-detalhes-tel]').val(c.telefone);
            $('#modalCoordenadorEditar [data-detalhes-nascimento]').val(c.nascimento);
            $('#modalCoordenadorEditar [data-detalhes-civil]').val(c.estado_civil);
            $('#modalCoordenadorEditar [data-detalhes-rg]').val(c.rg);
            $('#modalCoordenadorEditar [data-detalhes-cpf]').val(c.cpf);
            $('#modalCoordenadorEditar [data-detalhes-sangue]').val(c.sangue);
            $('#modalCoordenadorEditar [data-detalhes-endereco]').val(c.endereco);
            $('#modalCoordenadorEditar [data-detalhes-complemento]').val(c.complemento);
            $('#modalCoordenadorEditar [data-detalhes-pontoref]').val(c.ponto_referencia);
            $('#modalCoordenadorEditar [data-detalhes-bairro]').val(c.bairro);
            $('#modalCoordenadorEditar [data-detalhes-cep]').val(c.cep);
            $('#modalCoordenadorEditar [data-detalhes-cidade]').val(c.cidade);
            $('#modalCoordenadorEditar [data-detalhes-estado]').val(c.estado);
            $('#modalCoordenadorEditar [data-detalhes-alergia]').val(c.alergia);
            $('#modalCoordenadorEditar [data-detalhes-emergencianome]').val(c.emergencia_nome);
            $('#modalCoordenadorEditar [data-detalhes-emergenciatel]').val(c.emergencia_telefone);
            $('#modalCoordenadorEditar [data-detalhes-taxaextracasal]').val(converteCentavoEmReal(c.taxa_extra_casal));
            $('#modalCoordenadorEditar [data-detalhes-titular]').val(function(){
                if(c.titular != 0) {return c.titular;} else {return ''};
            });

            $('#modalCoordenadorEditar').modal('show');
        } else {
            alerta(res.mensagem, 'Erro!', 'warning');
            return false;
        }
    }, 'json').
    fail(function(ev){
        switch(ev.statusCode) {
            case 404:
                alerta('Não encontrado.', 'Erro!', 'danger');
                break;
        }
        //console.log(ev);
        debugador(ev);
    });;
}

function editaCliente(id)
{
    id = parseInt(id);

    $.post('/clientes/ver/'+id, function(res){
        if(res.success == true) {
            console.log(res);
            let c = res.cliente;
            $('strong[data-detalhes-nome]').text(c.nome);
            $('input[data-detalhes-nome]').val(c.nome);
            $('span[data-detalhes-id]').text(c.id);
            $('input[data-detalhes-id]').val(c.id);
            $('[data-detalhes-email]').val(c.email);
            $('[data-detalhes-tel]').val(c.telefone);
            $('[data-detalhes-nascimento]').val(c.nascimento);
            $('[data-detalhes-civil]').val(c.estado_civil);
            $('[data-detalhes-rg]').val(c.rg);
            $('[data-detalhes-cpf]').val(c.cpf);
            $('[data-detalhes-sangue]').val(c.sangue);
            $('[data-detalhes-endereco]').val(c.endereco);
            $('[data-detalhes-complemento]').val(c.complemento);
            $('[data-detalhes-pontoref]').val(c.ponto_referencia);
            $('[data-detalhes-bairro]').val(c.bairro);
            $('[data-detalhes-cep]').val(c.cep);
            $('[data-detalhes-cidade]').val(c.cidade);
            $('[data-detalhes-estado]').val(c.estado);
            $('[data-detalhes-alergia]').val(c.alergia);
            $('[data-detalhes-emergencianome]').val(c.emergencia_nome);
            $('[data-detalhes-emergenciatel]').val(c.emergencia_telefone);
            $('[data-detalhes-taxaextracasal]').val(converteCentavoEmReal(c.taxa_extra_casal));
            $('[data-detalhes-titular]').val(function(){
                if(c.titular != 0) {return c.titular;} else {return ''};
            });

            $('#modalClienteEditar').modal('show');
        } else {
            alerta(res.mensagem, 'Erro!', 'warning');
            return false;
        }
    }, 'json').
    fail(function(ev){
        switch(ev.statusCode) {
            case 404:
                alerta('Não encontrado.', 'Erro!', 'danger');
                break;
        }
        //console.log(ev);
        debugador(ev);
    });;
}

function loadUsuario(id)
{
    $.post('/usuarios/ver/'+id, function(res){
        if(res.success == true) {
            console.log(res);
            $('#modalUsuarioDetalhes').find('[data-usuario-nome]').val(res.usuario.nome);
            $('#modalUsuarioDetalhes').find('[data-usuario-avatar]').attr('src', 'media/images/av/'+res.usuario.avatar);
            $('#modalUsuarioDetalhes').find('[data-usuario-nome]').text(res.usuario.nome + ' ' + res.usuario.sobrenome);
            $('#modalUsuarioDetalhes').find('[data-usuario-usuario]').text('@'+res.usuario.usuario);
            $('#modalUsuarioDetalhes').find('[data-usuario-email]').text(res.usuario.email);
            $('#modalUsuarioDetalhes').find('[data-usuario-nivel]').text(res.usuario.nivel);
            $('#modalUsuarioDetalhes').find('[data-usuario-id]').attr('data-usuario-id', res.usuario.id);
            $('#modalUsuarioDetalhes').find('[data-usuario-criado]').text(Dobbin.formataDataHora(new Date(res.usuario.criado_em)));
            $('#modalUsuarioDetalhes').find('[data-usuario-atualizado]').text(Dobbin.formataDataHora(new Date(res.usuario.atualizado_em)));
            $('#modalUsuarioDetalhes').find('[data-usuario-logadoem]').text(Dobbin.formataDataHora(new Date(res.usuario.logado_em)));


            $('#modalUsuarioDetalhes').modal('show');
        } else {
            alerta(res.mensagem, 'Erro!', 'warning');
            return false;
        }
    },'json').
    fail(function(ev){
        switch(ev.statusCode) {
            case 404:
                alerta('Não encontrado.', 'Erro!', 'danger');
                break;
        }
        //console.log(ev);
        debugador(ev);
    });
}

function editaUsuario(id)
{
    $.post('/usuarios/ver/'+id, function(res){
        if(res.success == true) {
            console.log(res);
            $('#modalUsuarioEditar').find('[data-usuario-nome]').val(res.usuario.nome);
            $('#modalUsuarioEditar').find('[data-usuario-sobrenome]').val(res.usuario.sobrenome);
            $('#modalUsuarioEditar').find('[data-usuario-email]').val(res.usuario.email);
            $('#modalUsuarioEditar').find('[data-usuario-usuario]').val(res.usuario.usuario);
            $('#modalUsuarioEditar').find('[data-usuario-nivel]').val(res.usuario.nivel);
            $('#modalUsuarioEditar').find('[data-usuario-id]').val(res.usuario.id);
            $('#modalUsuarioEditar').find('[data-usuario-avatar]').attr('src', '/media/images/av/'+res.usuario.avatar);


            $('#modalUsuarioEditar').modal('show');
        } else {
            alerta(res.mensagem, 'Erro!', 'warning');
            return false;
        }
    },'json').
    fail(function(ev){
        switch(ev.statusCode) {
            case 404:
                alerta('Não encontrado.', 'Erro!', 'danger');
                break;
        }
        //console.log(ev);
        debugador(ev);
    });
}

function vendaEstornarModal(id)
{
    getVendaDados(id, function(v){
        console.log(v);
        if(v === false) {return false;}
        let modal = $('#modalEstornarVenda');
        modal.find('h6').html('<strong>Venda:</strong> #'+v.id+'<br>'+
        '<strong>Forma de Pagamento:</strong> '+v.forma_pagamento+'<br>'+
        '<strong>Cliente:</strong> '+v.cliente_nome);

        modal.find('[name="id"]').val(v.id);
        modal.find('[name="valor_devolvido"]').val('0,00');
        modal.find('[name="valor_devolvido"]').attr('max',v.valor_total);
        if(modal.find('[name="valor_devolvido"]').parent().next().hasClass('alert') == false) {
            modal.find('[name="valor_devolvido"]').parent().after('<div class="alert alert-info small py-1 px-2">'+
            'O valor estornado/devolvido não pode ser maior que o valor da venda (<strong>R$ '+Dobbin.converteCentavoEmReal(v.valor_total)+'</strong>).</div>');
        }
        modal.find('form button').attr('disabled', true);
        modal.modal('show');
    });
}

/**
 * ./MODAIS E JANELAS SUSPENSAS
 */


/**
 * RETORNO DE DADOS JSON
 */

function getClienteDados(id, callback)
{
    id = parseInt(id);

    $.post('/clientes/ver/'+id, function(res){
        //console.log(res);
        if(res.success == true) {
            callback(res.cliente);
        } else {
            alerta(res.mensagem, 'Falha ao retornar dados do cliente.', 'danger');
            callback(false);
        }
    }, 'json').fail(function(ev){
        nativePOSTFail(ev);
        callback(false);
    });
}

function getClienteVendas(id, callback)
{
    id = parseInt(id);

    $.post('/clientes/ver/'+id+'/vendas', function(res){
        //console.log(res);
        if(res.success == true) {
            callback(res.vendas);
        } else {
            alerta(res.mensagem, 'Falha ao retornar dados das compras do cliente.', 'danger');
            callback(false);
        }
    }, 'json').fail(function(ev){
        nativePOSTFail(ev);
        callback(false);
    });
}

function getCoordenadorDados(id, callback)
{
    id = parseInt(id);

    $.post('/coordenadores/ver/'+id, function(res){
        console.log(res);
        if(res.success == true) {
            callback(res.coordenador);
        } else {
            alerta(res.mensagem, 'Falha ao retornar dados do coordenador.', 'danger');
            callback(false);
        }
    }, 'json').fail(function(ev){
        nativePOSTFail(ev);
        callback(false);
    });
}

function getRoteiroDados(id, callback)
{
    id = parseInt(id);

    $.post('/roteiros/load/'+id, function(res){
        console.log(res);
        if(res.success == true) {
            callback(res.roteiro);
        } else {
            alerta(res.mensagem, 'Falha ao retornar dados do roteiro.', 'danger');
            callback(false);
        }
    }, 'json').fail(function(ev){
        nativePOSTFail(ev);
        callback(false);
    });
}

function getVenda(id)
{
    $.post('/vendas/database/load/venda/'+id, function(res){
        //$('.modal:not(#janDinamica)').modal('hide');
        if(res.success) {
            let venda = res.venda;
            // AJUSTA TITULO DA JANELA DINÂMICA
            $('#janDinamica').find('.tituloModal').text('Venda #'+venda.id);
            $('#janDinamica').find('.modal-body').html(res.page);
            
            setTimeout(function(){$('#janDinamica').modal('show');}, 400);
            $('#janDinamica').find('.modal-dialog').removeClass('modal-sm').addClass('modal-xl');
            janDinamicaGatilhos(); // Dispara gatilhos na janela dinâmica.
            console.log(res);
        } else {
            $('#janDinamica').find('.modal-body').html('<h6>Não conseguimos recuperar esta venda.</h6><br>'+res.mensagem);
            alerta(res.mensagem, 'Não conseguimos recuperar esta venda.', 'warning');
        }
    }, 'json').
    fail(function(ev){nativePOSTFail(ev);});
}

function getVendaDados(id, callback)
{
    id = parseInt(id);

    $.post('/vendas/database/load/venda/'+id, function(res){
        
        if(res.success == true) {
            callback(res.venda);
        } else {
            alerta(res.mensagem, 'Falha ao retornar dados da venda.', 'danger');
            callback(false);
        }
    }, 'json').fail(function(ev){
        nativePOSTFail(ev);
        callback(false);
    });
}
/**
 * ./RETORNO DE DADOS JSON
 */

/**
 * PÁGINAS
 */


function deleteCoordenador(id)
{
    let x = confirm('Tem certeza que deseja excluir esse coordenador permanentemente?\n(Os coordenadores removidos vão para lixeira e são excluídos depois de 72h.)');
    if(x === true) {
        $.post(PREFIX_POST+'coordenadores/apagar/'+id, function(res){
            if(res.success = true) {
                alerta('Coordenador foi excluído com sucesso.', 'Sucesso!', 'success');
                if(typeof loadDatabaseCoordenadores === 'function') {
                    loadDatabaseCoordenadores();
                }
                loadLanding(location.hash.substring(1, location.hash.length));
            }
        }, 'json').
        fail(function(ev){
            //console.log(ev);
            debugador(ev);
            alerta('Falha...','', 'warning');
        });
        return true;
    }
    return false;
}

function deleteCliente(id)
{
    let x = confirm('Tem certeza que deseja excluir esse cliente permanentemente?\n(Os clientes removidos vão para lixeira e são excluídos depois de 72h.)');
    if(x === true) {
        $.post(PREFIX_POST+'clientes/apagar/'+id, function(res){
            if(res.success = true) {
                alerta('Cliente foi excluído com sucesso.', 'Sucesso!', 'success');
                if(typeof loadDatabaseClientes === 'function') {
                    loadDatabaseClientes();
                }
                loadLanding(location.hash.substring(1, location.hash.length));
            }
        }, 'json').
        fail(function(ev){
            //console.log(ev);
            debugador(ev);
            alerta('Falha...','', 'warning');
        });
        return true;
    }
    return false;
}

function restauraCoordenador(id)
{
    $.post(PREFIX_POST+'coordenadores/restaurar/'+id, function(res){
        if(res.success = true) {
            alerta('Coordenador foi restaurado com sucesso.', 'Sucesso!', 'success');
            if(typeof loadDatabaseCoordenadores === 'function') {
                loadDatabaseCoordenadores();
            }
            loadLanding(location.hash.substring(1, location.hash.length));
        }
    }, 'json').
    fail(function(ev){
        //console.log(ev);
        debugador(ev);
        alerta('Falha...','', 'warning');
    });
    return true;
}

function restauraCliente(id)
{
    $.post(PREFIX_POST+'clientes/restaurar/'+id, function(res){
        if(res.success = true) {
            alerta('Cliente foi restaurado com sucesso.', 'Sucesso!', 'success');
            if(typeof loadDatabaseClientes === 'function') {
                loadDatabaseClientes();
            }
            loadLanding(location.hash.substring(1, location.hash.length));
        }
    }, 'json').
    fail(function(ev){
        //console.log(ev);
        debugador(ev);
        alerta('Falha...','', 'warning');
    });
    return true;
}

function deleteCoordenadorLixeira(id)
{
    if(id == '' || id == 0) {
        return false;
    }

    $.post(PREFIX_POST+'coordenadores/apagarlixeira/'+id, function(res){
        if(res.success = true) {
            alerta('Coordenador foi removido completamente.', 'Sucesso!', 'success');
            if(typeof loadDatabaseCoordenadores === 'function') {
                loadDatabaseCoordenadores();
            }
            loadLanding(location.hash.substring(1, location.hash.length));
        }
    }, 'json').
    fail(function(ev){
        //console.log(ev);
        debugador(ev);
        alerta('Falha...','', 'warning');
    });
    return true;
}

function deleteClienteLixeira(id)
{
    if(id == '' || id == 0) {
        return false;
    }

    $.post(PREFIX_POST+'clientes/apagarlixeira/'+id, function(res){
        if(res.success = true) {
            alerta('Cliente foi removido completamente.', 'Sucesso!', 'success');
            if(typeof loadDatabaseClientes === 'function') {
                loadDatabaseClientes();
            }
            loadLanding(location.hash.substring(1, location.hash.length));
        }
    }, 'json').
    fail(function(ev){
        //console.log(ev);
        debugador(ev);
        alerta('Falha...','', 'warning');
    });
    return true;
}

function deleteUsuario(id)
{
    let x = confirm('Tem certeza que deseja excluir esse usuário?\n(A operação não poderá ser desfeita.)');
    if(x === true) {
        $.post(PREFIX_POST+'usuarios/apagar/'+id, function(res){
            if(res.success == true) {
                alerta('Usuário removido.', 'Sucesso!', 'success');
                loadLanding(location.hash.replace('#', ''));
            }
        }, 'json').
        fail(function(ev){
            //console.log(ev);
            debugador(ev);
            alerta('Falha...','', 'warning');
        });
    }
}

function loadMinhaConta(trig)
{
    $.post(PREFIX_POST+'minhaconta', function(res){
        console.log(res);

        $('#modalMinhaConta').find('[data-minhaconta-avatar]').attr('src', 'media/images/av/'+res.avatar);
        $('#modalMinhaConta').find('[data-minhaconta-nome]').text(res.nome + ' ' + res.sobrenome);
        $('#modalMinhaConta').find('[data-minhaconta-usuario]').text('@'+res.usuario);
        $('#modalMinhaConta').find('[data-minhaconta-email]').text(res.email);
        $('#modalMinhaConta').find('[data-minhaconta-nivel]').text(res.nivel);
        $('#modalMinhaConta').find('[data-minhaconta-id]').attr('data-minhaconta-id', res.id);
        $('#modalMinhaConta').find('[data-minhaconta-criado]').text(Dobbin.formataDataHora(new Date(res.criado_em)));
        $('#modalMinhaConta').find('[data-minhaconta-atualizado]').text(Dobbin.formataDataHora(new Date(res.atualizado_em)));
        $('#modalMinhaConta').find('[data-minhaconta-logadoem]').text(Dobbin.formataDataHora(new Date(res.logado_em)));

        $('#modalMinhaContaEditar').find('[data-minhaconta-nome]').val(res.nome);
        $('#modalMinhaContaEditar').find('[data-minhaconta-sobrenome]').val(res.sobrenome);
        $('#modalMinhaContaEditar').find('[data-minhaconta-usuario]').val(res.usuario);
        $('#modalMinhaContaEditar').find('[data-minhaconta-email]').val(res.email);
        $('#modalMinhaContaEditar').find('[data-minhaconta-nivel]').val(res.nivel);
        
        $('#modalMinhaConta').modal('show'); 
        $(trig).parents('.nav-dropdown:eq(0)').fadeOut(100);
    }, 'json').
    fail(function(ev){
        switch(ev.statusCode) {
            case 404:
                alerta('Não encontrado.', 'Erro!', 'danger');
                break;
        }
        //console.log(ev);
        debugador(ev);
    });
}

function editaMinhaConta()
{
    $('#modalMinhaConta').modal('hide');

    $('#modalMinhaContaEditar').modal('show');
}

function alterarMinhaSenha()
{
    $('#modalMinhaConta').modal('hide');
    $('#modalAlterarMinhaSenha').modal('show');
}

function loadMinhaContaFotoAlterar()
{
    let id = $('#modalMinhaConta').find('[data-minhaconta-id]').attr('data-minhaconta-id');
    $('#modalMinhaConta').modal('hide');
    $('#modalMinhaContaFotoAlterar').modal('show');
    $('#modalMinhaContaFotoAlterar').find('[data-minhaconta-id]').attr('data-minhaconta-id', id);
    
    $('#modalMinhaContaFotoAlterar').find('[data-minhaconta-avatar]').
    attr('src', $('#modalMinhaConta').find('[data-minhaconta-avatar]').attr('src'));

}

function setMinhaContaFoto(target)
{
    var formdata = new FormData($(target)[0]);

    $.ajax({
        url: PREFIX_POST+$(target).attr('action'),
        method: "POST",
        contentType: false,
        dataType: "json",
        processData: false,
        cache: false,
        data: formdata,
        statusCode: {
            403: function(){
                alerta('Acesso negado.', 'Resposta do servidor:', 'warning');
            },
            404: function(){
                alerta('Não encontrado.', 'Resposta do servidor:', 'warning');
            },
        },
        success: function(res){
            if(res.success) {
                console.log('Sucesso');
                alerta('Foto atualizada. Vamos atualizar tudo, espera um pouco...', 'Sucesso!', 'success');
                setTimeout(function(){location.reload();}, 3000);
            } else {
                alerta(res.mensagem, 'Falha...', 'warning');
            }
        },
        error: function(jqXHR){
            console.log(jqXHR.responseText);
        }
    });

    return false;
}

function delMinhaContaFoto()
{
    let id = $('#modalMinhaConta').find('[data-minhaconta-id]').attr('data-minhaconta-id');
    $.post(PREFIX_POST+'minhaconta/avatar/apagar',function(res){
        if(res.success) {
            alerta('Foto removida. Vamos atualizar tudo, espera um pouco...', 'Sucesso!', 'success');
            setTimeout(function(){location.reload();}, 3000);
        } else {
            alerta(res.mensagem, 'Falha.', 'warning');
        }
    }, 'json');
}

function searchClienteNome(busca)
{
    let resultado;
    $.post(PREFIX_POST+'clientes/buscar', {busca: busca}, function(res){
        if(res.success == true) {
            let r = res.clientes;
            //console.log(res);
            if($('#janClienteSelect').find('hr').siblings('table').length == 0) {
                $('#janClienteSelect').find('hr').after('<table class="table table-selectable table-sm table-hover table-bordered small"><thead class="thead-dark"><tr><th>Cód.</th><th>Nome</th><th>Cidade/Estado</th></tr></thead><tbody></tbody></table>');
                $('#janClienteSelect').find('table tbody').slideUp(300);
            } else {
                $('#janClienteSelect').find('table tbody').slideUp(300);
                $('#janClienteSelect').find('table tbody').html('');
            }

            $('#janClienteSelect').find('[data-selecionar]').attr('disabled','true');
            $('#janClienteSelect [name="busca"]').next('.text-muted').html('');

            if(r.length == 0) {
                $('#janClienteSelect').find('table tbody').append('<tr style="cursor:not-allowed" disabled><td colspan="3" disabled>Nada encontrado</td></tr>');
            } else {
                setTimeout(function(){
                    for(let i = 0; i < r.length; i++) {
                        $('#janClienteSelect').find('table tbody').append('<tr style="cursor:pointer" data-id="'+r[i].id+'"><td class="small">'+r[i].id+'</td><td>'+r[i].nome+'</td><td>'+r[i].cidade+'/'+r[i].estado+'</td></tr>');
                    };

                }, 200);
            }

            $('#janClienteSelect [name="busca"]').next('.text-muted').html('Registros encontrados: &nbsp; '+r.length);

            $('#janClienteSelect').find('table tbody').slideDown(300);
        } else {
            alerta(res.mensagem, 'Ops, espera um pouco!',' warning');
        }
        
    }, 'json');
}

function searchParceiroNome(busca)
{
    let resultado;
    $.post(PREFIX_POST+'parceiros/buscar', {busca: busca}, function(res){
        console.log(res);
        
        if(res.success == true) {
            let r = res.parceiros;
            let jan = $('#janParceirosSelect');
            //console.log(res);
            if(jan.find('hr').siblings('table').length == 0) {
                jan.find('hr').after('<table class="table table-selectable table-sm table-hover table-bordered small"><thead class="thead-dark"><tr><th>Cód.</th><th>Parceiro</th><th>Serviços</th></tr></thead><tbody></tbody></table>');
                jan.find('table tbody').slideUp(300);
            } else {
                jan.find('table tbody').slideUp(300);
                jan.find('table tbody').html('');
            }

            jan.find('[data-selecionar]').attr('disabled','true');
            jan.find('[name="busca"]').next('.text-muted').html('');

            if(r.length == 0) {
                jan.find('table tbody').append('<tr style="cursor:not-allowed" disabled><td colspan="3" disabled>Nada encontrado</td></tr>');
            } else {
                setTimeout(function(){
                    let nome = '';
                    let servicos;
                    for(let i = 0; i < r.length; i++) {
                        nome = '';
                        if(r[i].nome_fantasia != '') {
                            nome = '<strong>'+r[i].nome_fantasia+'</strong><br><small class="font-italic">'+r[i].razao_social+'</small>';
                        } else {
                            nome = '<strong>'+r[i].razao_social+'</strong>';
                        }

                        if(r[i].doc_tipo == 'CNPJ') {
                            nome += '<br><small>CNPJ: '+Dobbin.ocultaCNPJ(r[i].doc_numero)+'</small>';
                        } else {
                            nome += '<br><small>CPF: '+Dobbin.ocultaCPF(r[i].doc_numero)+'</small>'
                        }


                        if(r[i].servicos.length == 0) {
                            servicos = '<i>Nenhum</i>';
                        } else {
                            servicos = '';
                            r[i].servicos.forEach(function(j){
                                servicos += j.categoria+', ';
                            });

                            servicos = servicos.substr(0, servicos.length-2);
                        }


                        jan.find('table tbody').append('<tr style="cursor:pointer" data-id="'+r[i].id+'"><td class="small">'+r[i].id+'</td><td>'+nome+'</td><td>'+servicos+'</td></tr>');
                    };
                    nome = undefined;

                }, 200);
            }

            jan.find('[name="busca"]').next('.text-muted').html('Registros encontrados: &nbsp; '+r.length);

            jan.find('table tbody').slideDown(300);
        } else {
            alerta(res.mensagem, 'Ops, espera um pouco!',' warning');
        }
        
    }, 'json');
}

function searchCoordenadorNome(busca)
{
    let resultado;
    $.post(PREFIX_POST+'coordenadores/buscar', {busca: busca}, function(res){
        if(res.success == true) {
            let r = res.coordenadores;
            let jan = $('#janCoordenadorSelect');

            //console.log(res);
            if(jan.find('hr').siblings('table').length == 0) {
                jan.find('hr').after('<table class="table table-selectable table-sm table-hover table-bordered small"><thead class="thead-dark"><tr><th>Cód.</th><th>Nome</th><th>Cidade/Estado</th></tr></thead><tbody></tbody></table>');
                jan.find('table tbody').slideUp(300);
            } else {
                jan.find('table tbody').slideUp(300);
                jan.find('table tbody').html('');
            }

            jan.find('[data-selecionar]').attr('disabled','true');
            jan.find('[name="busca"]').next('.text-muted').html('');

            if(r.length == 0) {
                jan.find('table tbody').append('<tr style="cursor:not-allowed" disabled><td colspan="3" disabled>Nada encontrado</td></tr>');
            } else {
                setTimeout(function(){
                    for(let i = 0; i < r.length; i++) {
                        jan.find('table tbody').append('<tr style="cursor:pointer" data-id="'+r[i].id+'"><td class="small">'+r[i].id+'</td><td>'+r[i].nome+'</td><td>'+r[i].cidade+'/'+r[i].estado+'</td></tr>');
                    };

                }, 200);
            }

            jan.find('[name="busca"]').next('.text-muted').html('Registros encontrados: &nbsp; '+r.length);

            jan.find('table tbody').slideDown(300);
        } else {
            alerta(res.mensagem, 'Ops, espera um pouco!',' warning');
        }
        
    }, 'json');
}

function searchRoteiroNome(busca)
{

    let resultado;
    $.post(PREFIX_POST+'roteiros/buscar', {busca: busca}, function(res){
        if(res.success == true) {
            let r = res.roteiros;
            let jan = $('#janRoteirosSelect');

            console.log(r);
            if(jan.find('hr').siblings('table').length == 0) {
                jan.find('hr').after('<table class="table table-selectable table-sm table-hover table-bordered small"><thead class="thead-dark"><tr><th>Cód.</th><th>Roteiro</th></tr></thead><tbody></tbody></table>');
                jan.find('table tbody').slideUp(300);
            } else {
                jan.find('table tbody').slideUp(300);
                jan.find('table tbody').html('');
            }

            jan.find('[data-selecionar]').attr('disabled','true');
            jan.find('[name="busca"]').next('.text-muted').html('');

            if(r.length == 0) {
                jan.find('table tbody').append('<tr style="cursor:not-allowed" disabled><td colspan="3" disabled>Nada encontrado</td></tr>');
            } else {
                setTimeout(function(){
                    for(let i = 0; i < r.length; i++) {
                        jan.find('table tbody').append('<tr style="cursor:pointer" data-id="'+r[i].id+'"><td class="small">'+r[i].id+'</td><td>'+r[i].nome+
                        ' ('+Dobbin.formataData(new Date(r[i].data_ini),true)+' a '+Dobbin.formataData(new Date(r[i].data_fim),true)+')' + r[i].situacao_html+'</td></tr>');
                    };

                    $('[data-toggle="tooltip"').tooltip();

                }, 200);
            }

            jan.find('[name="busca"]').next('.text-muted').html('Registros encontrados: &nbsp; '+r.length);

            jan.find('table tbody').slideDown(300);
        } else {
            alerta(res.mensagem, 'Ops, espera um pouco!',' warning');
        }
        
    }, 'json');
}

function novoParceiro(sender)
{
    $.post(PREFIX_POST+$(sender).attr('action'), $(sender).serialize(), function(res){
        console.log(res);
        if(res.success == true) {
            alerta('A parceria foi cadastrada. Aguarde alguns instantes, que vamos continuar...','Cadastrado!', 'success');
            setTimeout(function(){
                location.hash = 'parceiros/ver/'+res.parceiro.id;
                loadLanding('parceiros/ver/'+res.parceiro.id);
            }, 500);
        } else {
            alerta(res.mensagem, 'Falha...', 'warning');
        }
    }, 'json').
    fail(function(ev){
        nativePOSTFail(ev);
    });
    return false;
}

function janServicoAdd()
{
    $('#modalParceiroServicoAdd .beneficiosHospedagem :input').attr('disabled', true);
    $('#modalParceiroServicoAdd .beneficiosTransporte :input').attr('disabled', true);
    $('#modalParceiroServicoAdd').modal('show');
}

function parcAddCampoTarifario(sender)
{
    if($(sender).parent('.tarifario').find('[data-servico-tarifa]').length < 5) {
        $(sender).before('<div data-servico-tarifa class="d-flex mb-2"><select class="form-control form-control-solid form-control-sm mr-1">'+
            '<option value="0-5">0 - 5 ANOS</option><option value="6-12">6 - 12 ANOS</option><option value="ADULTO">ADULTO</option>'+
            '<option value="CASAL">CASAL</option><option value="60+">60+ ANOS</option></select>'+
            '<div class="input-group input-group-sm mr-1"><div class="input-group-prepend"><span class="input-group-text form-control-solid">R$</span></div>'+
            '<input type="text" class="form-control form-control-sm form-control-solid" placeholder="Ex.: 5000,00" name="valor" dobbin-validate-valor></div>'+
            '<div class="invalid-feedback">Só permitido valores entre 0,00 e 9999999,99 (9 milhões). Valor sem casa decimal também é válido. Ex.: 0 a 9999999.</div>'+
            '<button type="button" class="btn btn-sm btn-danger" onclick="parcDelCampoTarifario(this)"><i class="fas fa-trash"></i></button></div>');
    } else {
        alerta('Você já inseriu o máximo de 5 tarifas.', 'Não dá para fazer isso.', 'warning');
    }
}

function parcDelCampoTarifario(sender)
{
    if($(sender).parents('.tarifario').find('[data-servico-tarifa]').length > 1) {
        $(sender).parents('[data-servico-tarifa]').remove();
        //console.log($(sender).parents('[data-servico-tarifa]'));
    } else {
        alerta('Você precisa definir pelo menos uma tarifa.', 'Não dá para fazer isso.', 'warning');
    }
}

function parcInserirBeneficio(sender, target = null)
{
    let ben = $(sender).prev().val();


    if(target == null) {
        target = '.beneficios';
    }

    $(target).append('<div class="form-check-inline" style="display:none"><label class="form-check-label">'+
        '<input type="checkbox" class="form-check-input" value="'+ben+'" checked="checked">'+ben+'</label></div>');
    $(target).find('.form-check-inline').fadeIn();
    $(sender).prev().val('');
}

function parcAddBeneficioPago(target)
{
    let alvo = $(target);
    

    alvo.append('<div class="beneficio d-flex mb-2" style="display:none"><input type="text" class="form-control form-control-sm form-control-solid mr-1" placeholder="Nome" data-beneficio-nome>'+
    '<div class="input-group input-group-sm mr-1"><div class="input-group-prepend"><span class="input-group-text form-control-solid">R$</span></div>'+
    '<input type="text" class="form-control form-control-sm form-control-solid" placeholder="Ex.: 5000,00" dobbin-validate-valor data-beneficio-valor>'+
    '<div class="invalid-feedback">Só permitido valores entre 0,00 e 9999999,99 (9 milhões). Valor sem casa decimal também é válido. Ex.: 0 a 9999999.</div>'+
    '<button class="btn btn-sm btn-danger" type="button" onclick="parcDelBeneficioPago(this);"><i class="fas fa-trash"></i></button></div>');

    alvo.find('.beneficio').fadeIn();
    
}

function parcDelBeneficioPago(sender)
{
    $(sender).parents('.beneficio').eq(0).remove();
}

function parcServicoNovo()
{
    //console.log(event);
    let servico = {};
    servico.categoria = $('#modalParceiroServicoAdd [name="categoria"]').find(':selected').val();
    if(servico.categoria == 'Hospedagem') {
        servico.tipo = $('#modalParceiroServicoAdd [name="tipoHospedagem"]').find(':selected').val();
    } else if(servico.categoria == 'Transporte') {
        servico.tipo = $('#modalParceiroServicoAdd [name="tipoTransporte"]').find(':selected').val();
        servico.passageiros = $('#modalParceiroServicoAdd [name="passageiros"]').val();
    } else {
        servico.tipo = null;
    }

    servico.cidade = $('#modalParceiroServicoAdd [name="cidade"]').val();
    servico.estado = $('#modalParceiroServicoAdd [name="estado"]').find(':selected').val();
    servico.tarifario = [];
    for(let i = 0; i < $('#modalParceiroServicoAdd .tarifario [data-servico-tarifa]').length; i++) {
        servico.tarifario[i] = {
            idade: $('#modalParceiroServicoAdd .tarifario [data-servico-tarifa]').eq(i).children('select').find(':selected').val(),
            valor: $('#modalParceiroServicoAdd .tarifario [data-servico-tarifa]').eq(i).find('[name="valor"]').val()
        };
    }

    // Beneficios gratis
    servico.beneficiosGratis = [];

    if(servico.categoria == 'Hospedagem') {
        if($('#modalParceiroServicoAdd .beneficiosHospedagem .form-check-input:checked').length > 0) {
            for(let i = 0; i < $('#modalParceiroServicoAdd .beneficiosHospedagem .form-check-input:checked').length; i++) {
                servico.beneficiosGratis[i] = $('#modalParceiroServicoAdd .beneficiosHospedagem .form-check-input:checked').eq(i).val();
            }
        }
    } else if(servico.categoria == 'Transporte') {
        if($('#modalParceiroServicoAdd .beneficiosTransporte .form-check-input:checked').length > 0) {
            for(let i = 0; i < $('#modalParceiroServicoAdd .beneficiosTransporte .form-check-input:checked').length; i++) {
                servico.beneficiosGratis[i] = $('#modalParceiroServicoAdd .beneficiosTransporte .form-check-input:checked').eq(i).val();
            }
        }
    }

    // Outros beneficios inclusos
    let j;
    if(servico.beneficiosGratis == undefined) {
        j = 0;
    } else {
        j = servico.beneficiosGratis.length;
    }


    if($('#modalParceiroServicoAdd .beneficiosOutros .form-check-input:checked').length > 0) {
        for(let i = 0; i < $('#modalParceiroServicoAdd .beneficiosOutros .form-check-input:checked').length; i++) {
            servico.beneficiosGratis[j+i] = $('#modalParceiroServicoAdd .beneficiosOutros .form-check-input:checked').eq(i).val();
        }
    }

    // Beneficios pagos
    servico.beneficiosPagos = [];

    if($('#modalParceiroServicoAdd .beneficiosPagos .beneficio').length > 0)
    {
        for(let i = 0; i < $('#modalParceiroServicoAdd .beneficiosPagos .beneficio').length; i++) {
            if(
                $('#modalParceiroServicoAdd .beneficiosPagos .beneficio').eq(i).find('[data-beneficio-nome]').val().trim() == '' ||
                $('#modalParceiroServicoAdd .beneficiosPagos .beneficio').eq(i).find('[data-beneficio-valor]').val() == ''
            ) {
                // Do nothing...
            } else {
                servico.beneficiosPagos[i] = {
                    nome: $('#modalParceiroServicoAdd .beneficiosPagos .beneficio').eq(i).find('[data-beneficio-nome]').val(),
                    valor: $('#modalParceiroServicoAdd .beneficiosPagos .beneficio').eq(i).find('[data-beneficio-valor]').val()
                };
            }
        }
    }
    servico.PID = $('#modalParceiroServicoAdd [name="PID"]').val();
    servico.obs = $('#modalParceiroServicoAdd [name="obs"]').val();


    $.post(PREFIX_POST+'parceiros/'+servico.PID+'/novoservico',
        {
            servico: JSON.stringify(servico)
        },
        function(res){
            if(res.success == true) {
                alerta('Serviço incluído com sucesso. Atualizando dados do parceiro...', 'Sucesso!', 'success');
                $('#modalParceiroServicoAdd').modal('hide');
                loadLanding(location.hash);
            } else {
                alerta(res.mensagem, 'Falha!', 'warning');
            }
        }, 'json').
    fail(function(ev){
        nativePOSTFail(ev);
    });
}

function parcServicoDel(id, parceiro)
{
    let x = confirm('Tem certeza que deseja remover esse serviço?');

    if(x == true) {
        $.post(PREFIX_POST+'parceiros/'+parceiro+'/apagarservico/'+id, function(res){
            if(res.success == true) {
                alerta('Serviço removido...', 'Sucesso!', 'success');
                loadLanding(location.hash);
            } else {
                alerta(res.mensagem, 'Falha!', 'warning');
            }
        }, 'json').
        fail(function(ev){
            nativePOSTFail(ev);
        });
    }
}

function parcServicoEdita(id, parceiro)
{
    $.post('parceiros/ver/'+parceiro+'/servico/'+id, function(res){
        if(res.success == true) {
            let modal = $('#modalParceiroServicoEdita');
            let servico = res.servico;
            console.log(res.servico);
            // Reseta os campos do modal.

            // Exibe o modal.
            modal.modal('show');
            modal.find(':input[name="SID"]').val(servico.id);

            // Preenche o modal com os valores.
            modal.find('[name="categoria"]').val(servico.categoria).trigger('change');
            if(servico.categoria == 'Hospedagem') {
                modal.find('[name="tipoHospedagem"]').val(servico.tipo);
                modal.find('[name="passageiros"]').val('');
            } else if(servico.categoria == 'Transporte') {
                modal.find('[name="tipoTransporte"]').val(servico.tipo);
                modal.find('[name="passageiros"]').val(servico.passageiros);
            }

            modal.find('[name="cidade"]').val(servico.cidade);
            modal.find('[name="estado"]').val(servico.estado);
            modal.find('[name="tipoTransporte"]').val(servico.tipo);
            // Tarifas
            let tarifas = JSON.parse(servico.tarifas);
            //console.log(tarifas);
            if(modal.find('[data-servico-tarifa]').length > tarifas.length) {
                // Mais campos do que valores.
                for(let i = modal.find('[data-servico-tarifa]').length-1; i >= tarifas.length; i--) {
                    modal.find('[data-servico-tarifa]').eq(i).find('button.btn-danger').click();
                }
            } else if(modal.find('[data-servico-tarifa]').length < tarifas.length) {
                // Menos campos do que valores.
                for(let i = modal.find('[data-servico-tarifa]').length; i < tarifas.length; i++) {
                    modal.find('[data-servico-tarifa]').last().siblings('button.btn-primary').click();
                }
            }
            // Preenche o valor das tarifas.
            for(let i = 0; i < tarifas.length; i++) {
                modal.find('[data-servico-tarifa]').eq(i).find('select').val(tarifas[i].idade);
                modal.find('[data-servico-tarifa]').eq(i).find('[name="valor"]').val(converteCentavoEmReal(tarifas[i].valor)).trigger('change');
            }

            // Benefícios GRATUITOS
            let benefGratis = JSON.parse(servico.benef_gratis);
            modal.find('.beneficios > .beneficiosOutros').eq(0).find('.form-check-inline').remove();

            //console.log(benefGratis);
            benefGratis.forEach(function(b){
                let ok = false;

                if(servico.categoria == 'Hospedagem') {
                    let opt = modal.find('.beneficios > .beneficiosHospedagem').find(':input[value="'+b+'"]');
                    if(opt.length == 1)
                    {
                        opt.prop('checked', true);
                        ok = true;
                    }

                    opt = undefined;
                } else if(servico.categoria == 'Transporte') {
                    let opt = modal.find('.beneficios > .beneficiosTransporte').find(':input[value="'+b+'"]');
                    if(opt.length == 1)
                    {
                        opt.prop('checked', true);
                        ok = true;
                    }

                    opt = undefined;
                }

                if(ok === false) {
                    // Adiciona nos outros Benefícios
                    let opt = modal.find('.beneficios').next();
                    opt.children(':input[type="text"]').val(b);
                    opt.children('button.btn-primary').trigger('click');
                    

                    opt = undefined;
                }
            });

            // Benefícios PAGOS
            let benefPago = JSON.parse(servico.benef_pago);

            if(benefPago.length > modal.find('.beneficiosPagos > .beneficio').length) {
                // Menos campos do que valores. Adiciona
                for(let i = benefPago.length - modal.find('.beneficiosPagos > .beneficio').length; i > 0; i--) {
                    modal.find('.beneficiosPagos').next().trigger('click');
                }
            } else if(benefPago.length < modal.find('.beneficiosPagos > .beneficio').length) {
                // Mais campos do que valores. Remove
                for(let i = modal.find('.beneficiosPagos > .beneficio').length - 1; i >= benefPago.length; i--) {
                    modal.find('.beneficiosPagos .beneficio').eq(i).find('button.btn-danger').trigger('click');
                }
            }

            for(let i = 0; i < benefPago.length; i++) {
                modal.find('.beneficiosPagos .beneficio').eq(i).find('[data-beneficio-nome]').val(benefPago[i].nome);
                modal.find('.beneficiosPagos .beneficio').eq(i).find('[data-beneficio-valor]').val(
                    converteCentavoEmReal(benefPago[i].valor)
                ).trigger('change');
            }

            // OBSERVAÇÕES
            modal.find('textarea[name="obs"]').val(servico.obs_servico);

        } else {
            alerta(res.mensagem, 'Falha!', 'warning');
        }
    }, 'json').
    fail(function(ev){
        nativePOSTFail(ev);
    });
}

function parcServicoSalvar() {
    let servico = {};
    servico.categoria = $('#modalParceiroServicoEdita [name="categoria"]').find(':selected').val();
    if(servico.categoria == 'Hospedagem') {
        servico.tipo = $('#modalParceiroServicoEdita [name="tipoHospedagem"]').find(':selected').val();
    } else if(servico.categoria == 'Transporte') {
        servico.tipo = $('#modalParceiroServicoEdita [name="tipoTransporte"]').find(':selected').val();
        servico.passageiros = $('#modalParceiroServicoEdita [name="passageiros"]').val();
    } else {
        servico.tipo = null;
    }

    servico.cidade = $('#modalParceiroServicoEdita [name="cidade"]').val();
    servico.estado = $('#modalParceiroServicoEdita [name="estado"]').find(':selected').val();
    servico.tarifario = [];
    for(let i = 0; i < $('#modalParceiroServicoEdita .tarifario [data-servico-tarifa]').length; i++) {
        servico.tarifario[i] = {
            idade: $('#modalParceiroServicoEdita .tarifario [data-servico-tarifa]').eq(i).children('select').find(':selected').val(),
            valor: $('#modalParceiroServicoEdita .tarifario [data-servico-tarifa]').eq(i).find('[name="valor"]').val()
        };
    }

    // Beneficios gratis
    servico.beneficiosGratis = [];

    if(servico.categoria == 'Hospedagem') {
        if($('#modalParceiroServicoEdita .beneficiosHospedagem .form-check-input:checked').length > 0) {
            for(let i = 0; i < $('#modalParceiroServicoEdita .beneficiosHospedagem .form-check-input:checked').length; i++) {
                servico.beneficiosGratis[i] = $('#modalParceiroServicoEdita .beneficiosHospedagem .form-check-input:checked').eq(i).val();
            }
        }
    } else if(servico.categoria == 'Transporte') {
        if($('#modalParceiroServicoEdita .beneficiosTransporte .form-check-input:checked').length > 0) {
            for(let i = 0; i < $('#modalParceiroServicoEdita .beneficiosTransporte .form-check-input:checked').length; i++) {
                servico.beneficiosGratis[i] = $('#modalParceiroServicoEdita .beneficiosTransporte .form-check-input:checked').eq(i).val();
            }
        }
    }

    // Outros beneficios inclusos
    let j;
    if(servico.beneficiosGratis == undefined) {
        j = 0;
    } else {
        j = servico.beneficiosGratis.length;
    }


    if($('#modalParceiroServicoEdita .beneficiosOutros .form-check-input:checked').length > 0) {
        for(let i = 0; i < $('#modalParceiroServicoEdita .beneficiosOutros .form-check-input:checked').length; i++) {
            servico.beneficiosGratis[j+i] = $('#modalParceiroServicoEdita .beneficiosOutros .form-check-input:checked').eq(i).val();
        }
    }

    // Beneficios pagos
    servico.beneficiosPagos = [];

    if($('#modalParceiroServicoEdita .beneficiosPagos .beneficio').length > 0)
    {
        for(let i = 0; i < $('#modalParceiroServicoEdita .beneficiosPagos .beneficio').length; i++) {
            if(
                $('#modalParceiroServicoEdita .beneficiosPagos .beneficio').eq(i).find('[data-beneficio-nome]').val().trim() == '' ||
                $('#modalParceiroServicoEdita .beneficiosPagos .beneficio').eq(i).find('[data-beneficio-valor]').val() == ''
            ) {
                // Do nothing...
            } else {
                servico.beneficiosPagos[i] = {
                    nome: $('#modalParceiroServicoEdita .beneficiosPagos .beneficio').eq(i).find('[data-beneficio-nome]').val(),
                    valor: $('#modalParceiroServicoEdita .beneficiosPagos .beneficio').eq(i).find('[data-beneficio-valor]').val()
                };
            }
        }
    }
    servico.PID = $('#modalParceiroServicoEdita [name="PID"]').val();
    servico.SID = $('#modalParceiroServicoEdita [name="SID"]').val();
    servico.obs = $('#modalParceiroServicoEdita [name="obs"]').val();


    $.post(PREFIX_POST+'parceiros/'+servico.PID+'/salvarservico/'+servico.SID,
        {
            servico: JSON.stringify(servico)
        },
        function(res){
            if(res.success == true) {
                alerta('Serviço salvo com sucesso. Atualizando dados do parceiro...', 'Sucesso!', 'success');
                $('#modalParceiroServicoEdita').modal('hide');
                loadLanding(location.hash);
            } else {
                alerta(res.mensagem, 'Falha!', 'warning');
            }
        }, 'json').
    fail(function(ev){
        nativePOSTFail(ev);
    });
}

function janParcFinanceiroAdd()
{
    $('#modalParceiroFinanceiroAdd').modal('show');
}

function parcFinanceiroNovo() {
    let modal = $('#modalParceiroFinanceiroAdd');

    for(let i = 0; i < modal.find('[required]').length; i++) {
        if(modal.find('[required]').eq(i).val().trim() == '') {
            modal.find('[required]').eq(i).focus();
            alerta('Há campos em branco que precisam ser preenchidos.','Não é possível continuar...', 'info');
            return false;
        }
    }

    let financ = {
        banco: modal.find('[name="banco"]').val(),
        favorecido: modal.find('[name="favorecido"]').val(),
        agencia: modal.find('[name="agencia"]').val(),
        agencia_dv: modal.find('[name="agencia_dv"]').val(),
        conta: modal.find('[name="conta"]').val(),
        conta_dv: modal.find('[name="conta_dv"]').val(),
        tipo_conta: modal.find('[name="tipo_conta"]').val(),
        obs_financeiro: modal.find('[name="obs_financeiro"]').val(),
        PID: modal.find('[name="PID"]').val(),
    };

    $.post(PREFIX_POST+'parceiros/'+financ.PID+'/novofinanceiro',
        {
            financeiro: JSON.stringify(financ)
        },
        function(res){
            if(res.success == true) {
                alerta('Financeiro incluído com sucesso. Atualizando dados do parceiro...', 'Sucesso!', 'success');
                modal.modal('hide');
                loadLanding(location.hash);
            } else {
                alerta(res.mensagem, 'Falha!', 'warning');
            }
        }, 'json').
    fail(function(ev){
        nativePOSTFail(ev);
    });

}

function parcFinanceiroEdita(id, parceiro) {
    $.post('parceiros/ver/'+parceiro+'/financeiro/'+id, function(res){
        if(res.success == true) {
            console.log(res);
            let modal = $('#modalParceiroFinanceiroEdita');
            let fin = res.financeiro;

            // Preenche valores.
            modal.find('[name="banco"]').val(fin.banco);
            modal.find('[name="favorecido"]').val(fin.favorecido);
            modal.find('[name="agencia"]').val(fin.agencia);
            modal.find('[name="agencia_dv"]').val(fin.agencia_dv);
            modal.find('[name="conta"]').val(fin.conta);
            modal.find('[name="conta_dv"]').val(fin.conta_dv);
            modal.find('[name="tipo_conta"]').val(fin.tipo_conta);
            modal.find('[name="obs_financeiro"]').val(fin.obs_financeiro);
            modal.find('[name="FID"]').val(fin.id);

            modal.modal('show');
        } else {
            alerta(res.mensagem, 'Falha!', 'warning');
        }
    }, 'json').
    fail(function(ev){
        nativePOSTFail(ev);
    });
}

function parcFinanceiroSalvar() {
    let modal = $('#modalParceiroFinanceiroEdita');

    for(let i = 0; i < modal.find('[required]').length; i++) {
        if(modal.find('[required]').eq(i).val().trim() == '') {
            modal.find('[required]').eq(i).focus();
            alerta('Há campos em branco que precisam ser preenchidos.','Não é possível continuar...', 'info');
            return false;
        }
    }

    let financ = {
        banco: modal.find('[name="banco"]').val(),
        favorecido: modal.find('[name="favorecido"]').val(),
        agencia: modal.find('[name="agencia"]').val(),
        agencia_dv: modal.find('[name="agencia_dv"]').val(),
        conta: modal.find('[name="conta"]').val(),
        conta_dv: modal.find('[name="conta_dv"]').val(),
        tipo_conta: modal.find('[name="tipo_conta"]').val(),
        obs_financeiro: modal.find('[name="obs_financeiro"]').val(),
        PID: modal.find('[name="PID"]').val(),
        FID: modal.find('[name="FID"]').val(),
    };

    $.post(PREFIX_POST+'parceiros/'+financ.PID+'/salvarfinanceiro/'+financ.FID,
        {
            financeiro: JSON.stringify(financ)
        },
        function(res){
            if(res.success == true) {
                alerta('Financeiro alterado com sucesso. Atualizando dados do parceiro...', 'Sucesso!', 'success');
                modal.modal('hide');
                loadLanding(location.hash);
            } else {
                alerta(res.mensagem, 'Falha!', 'warning');
            }
        }, 'json').
    fail(function(ev){
        nativePOSTFail(ev);
    });

}

function parcFinanceiroDel(id, parceiro) {
    let x = confirm('Tem certeza que deseja remover essa entrada dos dados financeiros?');

    if(x == true) {
        $.post(PREFIX_POST+'parceiros/'+parceiro+'/apagarfinanceiro/'+id, function(res){
            if(res.success == true) {
                alerta('Dado financeiro removido...', 'Sucesso!', 'success');
                loadLanding(location.hash);
            } else {
                alerta(res.mensagem, 'Falha!', 'warning');
            }
        }, 'json').
        fail(function(ev){
            nativePOSTFail(ev);
        });
    }
}

function parcHistoricoNovo(sender) {
    let form = $(sender).parents('form');
    let modal = $(sender).parents('.modal');
    let parcid, detalhes, etapa;

    parcid = form.find('[name="parcid"]').val();
    detalhes = form.find('[name="detalhes"]').val();
    etapa = form.find('[name="etapa"]').val();

    $.post(PREFIX_POST+'parceiros/'+parcid+'/addhistorico',
        {
            detalhes: detalhes,
            etapa: etapa
        },
        function(res){
            if(res.success == true) {
                alerta('Registro inserido com sucesso.', 'Sucesso!', 'success');
                modal.modal('hide');
                loadLanding(location.hash);
            } else {
                alerta(res.mensagem, 'Falha!', 'warning');
            }
        }, 'json').
    fail(function(ev){
        nativePOSTFail(ev);
    });
}

function parcHistoricoEdita(sender) {
    let form = $(sender).parents('form');
    let modal = $(sender).parents('.modal');
    let parcid, detalhes, etapa, hid;

    parcid = form.find('[name="parcid"]').val();
    detalhes = form.find('[name="detalhes"]').val();
    etapa = form.find('[name="etapa"]').val();
    hid = form.find('[name="hid"]').val();

    $.post(PREFIX_POST+'parceiros/'+parcid+'/editarhistorico/'+hid,
        {
            detalhes: detalhes,
            etapa: etapa,
            hid: hid
        },
        function(res){
            if(res.success == true) {
                alerta('Registro salvo com sucesso.', 'Sucesso!', 'success');
                modal.modal('hide');
                loadLanding(location.hash);
            } else {
                alerta(res.mensagem, 'Falha!', 'warning');
            }
        }, 'json').
    fail(function(ev){
        nativePOSTFail(ev);
    });
}

function parcHistoricoDelete(sender) {
    let pid, hid;

    if(sender == null || sender == undefined) {
        return false;
    }

    pid = $(sender).data('pid');
    hid = $(sender).data('hid');

    if(pid == null || pid == undefined || hid == null || hid == undefined) {
        return false;
    }

    $.post(PREFIX_POST+'parceiros/'+pid+'/apagarhistorico/'+hid,
        function(res){
            console.log(res);
            if(res.success == true) {
                $(sender).parents('tr').eq(0).next().remove();
                $(sender).parents('tr').eq(0).remove();
            } else {
                alerta(res.mensagem, 'Falha!', 'warning');
            }
            return true;
        }, 'json').
    fail(function(ev){
        nativePOSTFail(ev);
    });
}
/**
 * ROTEIROS
 */

function roteiroAddParceiroNovoRoteiro(sender) {
    let conteudo = sender.children('td:eq(1)').html();
    let pid = sender.data('id');

    // Verifica se esse parceiro já existe na lista.
    if($('#listaParceiros').find('[data-parceiro][data-pid="'+pid+'"]').length == 0) {

        $('#listaParceiros').append('<div data-parceiro data-pid="'+pid+'" class="border pl-2 pr-1 py-1 mr-2 d-flex">'+
        '<div class="flex-grow-1 pr-2">'+
        '<small>'+conteudo+'</small>'+
        '</div>'+
        '<button type="button" class="btn btn-sm btn-transparent align-self-stretch" onclick="$(this).parents(\'[data-parceiro]\').eq(0).fadeOut(200, function(){$(this).remove();})"><i class="fas fa-times"></i></button></div>');
        
        sender.parents('.modal').find('[data-dismiss="modal"]').trigger('click');
    } else {
        // Já existe.
        alerta('Este parceiro já foi adicionado.','Tente outro.', 'warning');
    }
}

function roteiroNovo(roteiro) {

    $.post(PREFIX_POST+'roteiros/novo', {dados: JSON.stringify(roteiro)}, function(res){
        if(res.success == true) {
            alerta('Roteiro foi lançado com êxito.', 'Sucesso.', 'success');
            loadLanding(location.hash);
        } else {
            alerta(res.mensagem, 'Ops, falha.', 'warning');
        }
    }, 'json').fail(function(ev){
        nativePOSTFail(ev);
    });
}

function roteiroHistoricoNovo(sender) {
    let form = $(sender).parents('form');
    let modal = $(sender).parents('.modal');
    let parcid, detalhes, etapa, roteiroid;

    parcid = form.find('[name="parcid"]').val();
    roteiroid = form.find('[name="roteiroid"]').val();
    detalhes = form.find('[name="detalhes"]').val();
    etapa = form.find('[name="etapa"]').val();

    $.post(PREFIX_POST+'roteiros/'+roteiroid+'/addhistorico/'+parcid,
        {
            detalhes: detalhes,
            etapa: etapa
        },
        function(res){
            if(res.success == true) {
                alerta('Registro inserido com sucesso.', 'Sucesso!', 'success');
                modal.modal('hide');
                loadLanding(location.hash);
            } else {
                alerta(res.mensagem, 'Falha!', 'warning');
            }
        }, 'json').
    fail(function(ev){
        nativePOSTFail(ev);
    });
}

function roteiroEditaSalva() {
    $.post(PREFIX_POST+'roteiros/salvar/'+roteiro.id, {dados: JSON.stringify(roteiro)}, function(res){
        if(res.success == true) {
            alerta('Alterações no roteiro foram salvas com êxito.', 'Sucesso.', 'success');
            location.hash = '#roteiros/ver/'+roteiro.id;
            loadLanding(location.hash);
        } else {
            alerta(res.mensagem, 'Ops, falha.', 'warning');
        }
    }, 'json').fail(function(ev){
        nativePOSTFail(ev);
    });
}

function roteiroApagar(sender)
{
    if($(sender).data('id')  == '') {
        alerta('Não é possível continuar com essa operação.', 'Espera...', 'info');
        return false;
    }

    let x = confirm("Tem certeza que deseja apagar este roteiro? \nEsse roteiro vai ser movido para lixeira.");
    if(x == true) {
        $.post(PREFIX_POST+'roteiros/apagar/'+$(sender).data('id'), function(res) {
            if(res.success == true) {
                alerta('Roteiro foi movido para lixeira.', 'Sucesso.', 'success');
                location.hash = '#roteiros';
                loadLanding(location.hash);
            } else {
                alerta(res.mensagem, 'Ops, falha.', 'warning');
            }
        },'json');
    }
}

function roteiroRestaurar(id)
{
    $.post(PREFIX_POST+'roteiros/restaurar/'+id, function(res) {
        if(res.success == true) {
            alerta('Roteiro foi restaurado da lixeira.', 'Sucesso.', 'success');
            location.hash = '#roteiros/lixeira';
            loadLanding(location.hash);
        } else {
            alerta(res.mensagem, 'Ops, falha.', 'warning');
        }
    },'json');
}

function roteiroApagarLixeira(id)
{
    if(id == '' || id == 0) {
        return false;
    }

    $.post(PREFIX_POST+'roteiros/apagarlixeira/'+id, function(res){
        if(res.success = true) {
            alerta('Roteiro foi removido completamente.', 'Sucesso!', 'success');
            loadLanding(location.hash);
        }
    }, 'json').
    fail(function(ev){
        //console.log(ev);
        debugador(ev);
        alerta('Falha...','', 'warning');
    });
    return true;
}

function roteiroCriarCopia(sender)
{
    let form = $(sender).parents('.modal').find('form');

    // Valida dados
    if(form.find('[name="rid"]').val() == '') {
        alerta('Houve alguma modificação inválida nessa página. Atualizando...','Aguarde...', 'info');
        return false;
    }


    let rot = {
        id: form.find('[name="rid"]').val(),
        data_ini: form.find('[name="data_ini"]').val(),
        data_fim: form.find('[name="data_fim"]').val(),
    }

    $.post(PREFIX_POST+'roteiros/'+rot.id+'/copiar', {
        roteiro: JSON.stringify(rot)
    }, function(res){
        console.log(res);
        if(res.success == true) {
            alerta('Cópia do roteiro criada com sucesso...', 'Criado!', 'success');
            if(res.roteiro.id == '') {
                location.hash = '#roteiros';
                loadLanding(location.hash);
            } else {
                alerta('Abrindo roteiro recém-criado...', 'Aguarde.', 'info');
                location.hash = '#roteiros/ver/'+res.roteiro.id;
                setTimeout(function(){location.reload();}, 2000);
            }
            
        } else {
            alerta(res.mensagem, 'Falha.','warning');
        }
    }, 'json').
    fail(function(ev){
        nativePOSTFail(ev);
    });
}

function roteiroAddCoordenador(sender)
{
    if(sender == null || sender == undefined) {
        alerta('Houve um erro na solicitação. Não é possível continuar...', 'Abortado.', 'warning');
        return false;
    }

    let rid = $(sender).siblings('[name="rid"]').val();
    let linha = $(sender).parents('form').find('tr.selecionado');
    let coord = linha.data('id');

    if(coord == undefined || rid == '') {
        return false;
    }

    $.post(PREFIX_POST+'roteiros/'+rid+'/addcoordenador/'+coord, function(res){
        if(res.success) {
            // Verifica se esse coordenador já consta.
            if($('#listaCoord').find('[data-id="'+coord+'"]').length == 0) {
                $('#listaCoord').find('ul.list-group').append('<li class="list-group-item d-flex justify-content-between align-items-center py-2 pl-3 pr-2">'+linha.children('td:eq(1)').text() +
                ' <button type="button" class="btn btn-sm btn-light" data-id="'+coord+'" data-rid="'+rid+'" onclick="roteiroRemoveCoordenador(this)"><i class="fas fa-times fa-fw"></i></button> </li>');
            }
            $(sender).siblings('[data-dismiss="modal"]').click();

        } else {
            alerta(res.mensagem, 'Falha!', 'warning');
            return false;
        }
    }, 'json').fail(function(ev) {
        nativePOSTFail(ev);
    });
}

function roteiroRemoveCoordenador(sender)
{
    if(sender == null || sender == undefined) {
        alerta('Houve um erro na solicitação. Não é possível continuar...', 'Abortado.', 'warning');
        return false;
    }

    let rid = $(sender).data('rid');
    let coord = $(sender).data('id');

    if(coord == undefined || rid == '') {
        return false;
    }

    $.post(PREFIX_POST+'roteiros/'+rid+'/delcoordenador/'+coord, function(res){
        if(res.success) {
            $(sender).parents('li').eq(0).remove();
        } else {
            alerta(res.mensagem, 'Falha!', 'warning');
            return false;
        }
    }, 'json').fail(function(ev) {
        nativePOSTFail(ev);
    });
}

/**
 * VENDAS
 */

/**
 * 
 * @param {Array} c Dados do cliente.
 * @param {Element} sender Elemento que disparou a função
 */
function vendaAddPassageiroLista(c, sender)
{
    let faixa = $(sender).data('faixa-etaria');
    let venda = $(sender).data('venda');
    if(faixa.indexOf(' ') == -1) {
        let x = faixa;
        faixa = [x];
        x = undefined;
    } else {
        faixa = faixa.split(' ');
    }
    console.log(c);
    console.log(sender);
    console.log(faixa);

    // Verifica se está dentro da faixa etária permitida.
    if(
        ( (c.faixa_etaria == '0-5' || c.faixa_etaria == '6-12') && faixa.find(function(f){return f == 'CRIANCA'}) ) || // Só crianças
        ( c.faixa_etaria == '60+' && faixa.find(function(f){return f == 'IDOSO'}) ) || // Só idosos
        ( faixa.find(function(f){return f == 'ADULTO'}) ) // Todos como adultos
    ) {
        // Envia informação do cliente para a plataforma.
        $.post(PREFIX_POST+'vendas/'+venda+'/clientes/add/'+c.id, function(res){
            if(res.success == true) {
                // Verifica se é janela dinâmica.
                if($(sender).parents('.modal').length == 1 && $(sender).parents('.modal').attr('id') == 'janDinamica') {
                    getVenda(venda); // Atualiza janela dinâmica.
                }

                alerta('Cliente adicionado.', 'Sucesso!', 'success');
            } else {
                alerta(res.mensagem, 'Falha...', 'warning');
            }
        },'json').
        fail(function(ev){nativePOSTFail(ev);});
    } else {
        alerta('A faixa etária deste cliente não corresponde à vaga.', 'Escolha outro cliente.', 'warning');
        $(sender).val('');
    }

    /*
    // verifica se o cliente é da faixa etária permitida.
    if(faixa == 'CRIANCA' && (c.faixa_etaria == '0-5' || c.faixa_etaria == '6-12')) { // CRIANÇA
        $(target).val(c.id +' - '+c.nome);
        alerta('OK CRIANCA');
    } else if(faixa === c.faixa_etaria || c.faixa_etaria == '0-5' || c.faixa_etaria == '6-12') { // ADULTO
        $(target).val(c.id +' - '+c.nome);
        alerta('OK ADULTO');
    } else if(faixa == 'IDOSO' && c.faixa_etaria == '60+'){
        $(target).val(c.id +' - '+c.nome);
        alerta('OK IDOSO');
    } else {
        if(faixa === 'CRIANCA') {
            alerta('Esse cliente não pode ser adicionado, pois essa vaga é destinada à uma CRIANÇA (0-12).', 'Não foi possível continuar', 'info');
        } else if(faixa === 'IDOSO') {
            alerta('Esse cliente não pode ser adicionado, pois essa vaga é destinada à um IDOSO (60+).', 'Não foi possível continuar', 'info');
        } else {
            alerta('Erro inexplicável.', 'Não foi possível continuar', 'info');
        }
        $(sender).val('');
    }
    */
}

/**
 * 
 * @param {int} id ID da venda
 * @param {string} situacao Situação da venda
 * @param {string} outro Informação adicional. Se "paga", informar forma de pagamento; se "devolvida", informar valor em centavos.
 * @param {Element} sender Elemento que disparou a função.
 */
function vendaAlteraSituacao(id, situacao, outro, sender)
{
    $.post(PREFIX_POST+'vendas/'+id+'/situacao/editar',{
        situacao: situacao,
        outro: outro
    }, function(res){
        if(res.success == true) {
            // Verifica se é janela dinâmica.
            if($(sender).parents('.modal').length == 1 && $(sender).parents('.modal').attr('id') == 'janDinamica') {
                getVenda(id); // Atualiza janela dinâmica.
            }

            alerta('Situação da venda alterada.', 'Sucesso!', 'success');
            getReservas();
            getAguardandoPagamento();
        } else {
            alerta(res.mensagem, 'Falha...', 'warning');
        }
    }, 'json').
    fail(function(ev){nativePOSTFail(ev);});
}

function vendaConfirmarEstorno(sender)
{
    if(sender == undefined) {
        alerta('Erro lógico!', 'PAROU!', 'danger');
        return false;
    }
    //console.log(sender);
    let id = $(sender).parents('form').find('[name="id"]').val();
    let outro = Dobbin.converteRealEmCentavo($(sender).parents('form').find('[name="valor_devolvido"]').val());

    $.post(PREFIX_POST+'vendas/'+id+'/situacao/editar',{
        situacao: 'Devolvida',
        outro: outro
    }, function(res){
        if(res.success == true) {
            $(sender).parents('.modal').modal('hide');
            getVenda(id); // Atualiza janela dinâmica.
            

            alerta('Situação da venda alterada.', 'Sucesso!', 'success');
            getReservas();
            getAguardandoPagamento();
        } else {
            alerta(res.mensagem, 'Falha...', 'warning');
        }
    }, 'json').
    fail(function(ev){nativePOSTFail(ev);});
}

function getVendasReservas()
{
    $.post('/vendas/database/get/reservas', function(res){
        if(res.success) {
            //console.log(res.vendas);
            //console.log(res.vendas.length);
            if(res.vendas.length == 0) {
                if($('#reservasDiv > div').length == 0) {
                    $('#reservasDiv').append('<div class="text-center mx-2"></div>');
                }
                $('#reservasDiv > div').html('<i>Nada encontrado...</i>');
                $('#reservasDiv').find('table').remove();
            } else {
                $('#reservasDiv').find('table').remove();
                $('#reservasDiv').append('<table class="table table-sm table-bordered table-hover mb-0 table-responsive-sm" style="display:none;">'+
                    '<thead class="thead-dark"> <tr>'+
                    '<th>Cód</th> <th>(Cód) Roteiro</th> <th>(Cód) Cliente</th> <th>Status</th> <th>Data</th> <th>Valor Total</th>'+
                    '</tr> </thead> <tbody> </tbody> </table>');
                res.vendas.forEach(function(v){
                    let dataReserva = Dobbin.formataDataHora( new Date(v.data_reserva), true);
                    $('#reservasDiv tbody').append(
                        '<tr class="small cursor-pointer" onclick="getVenda('+v.id+')"> <td>'+v.id+'</td> <td>( '+v.roteiro_id+' ) '+v.roteiro_nome+'</td> '+
                        '<td>( '+v.cliente_id+' ) '+v.cliente_nome+'</td> <td>'+v.status+'</td> '+
                        '<td>'+dataReserva+'</td> <td>R$ '+Dobbin.converteCentavoEmReal(v.valor_total)+'</td></tr>'
                    );
                });

                $('#reservasDiv > div').fadeOut('fast', function(){
                    $(this).remove();
                    $('#reservasDiv table').slideDown();
                });
            }
            
        } else {
            alerta(res.mensagem, 'Falha ao obter lista de reservas.', 'warning');
            if(debugEnabled === true) {
                console.log(res);
            }
        }
    }, 'json').
    fail(function(ev){nativePOSTFail(ev);});
}

function getVendasAguardandoPagamento()
{
    $.post('/vendas/database/get/aguardando', function(res){
        if(res.success) {
            //console.log(res.vendas);
            //console.log(res.vendas.length);
            if(res.vendas.length == 0) {
                if($('#aguardPagDiv > div').length == 0) {
                    $('#aguardPagDiv').append('<div class="text-center mx-2"></div>');
                }
                $('#aguardPagDiv > div').html('<i>Nada encontrado...</i>');
                $('#aguardPagDiv').find('table').remove();
            } else {
                $('#aguardPagDiv').find('table').remove();
                $('#aguardPagDiv').append('<table class="table table-sm table-bordered table-hover mb-0 table-responsive-sm" style="display:none;">'+
                    '<thead class="thead-dark"> <tr>'+
                    '<th>Cód</th> <th>Roteiro</th> <th>Cliente</th> <th>Status</th> <th>Data</th> <th>Valor Total</th>'+
                    '</tr> </thead> <tbody> </tbody> </table>');
                res.vendas.forEach(function(v){
                    let dataReserva = Dobbin.formataDataHora( new Date(v.data_reserva), true);
                    $('#aguardPagDiv tbody').append(
                        '<tr class="small cursor-pointer" onclick="getVenda('+v.id+')"> <td>'+v.id+'</td> <td>( '+v.roteiro_id+' ) '+v.roteiro_nome+'</td> '+
                        '<td>( '+v.cliente_id+' ) '+v.cliente_nome+'</td> <td>'+v.status+'</td> '+
                        '<td>'+dataReserva+'</td> <td>R$ '+Dobbin.converteCentavoEmReal(v.valor_total)+'</td></tr>'
                    );
                });

                $('#aguardPagDiv > div').fadeOut('fast', function(){
                    $(this).remove();
                    $('#aguardPagDiv table').slideDown();
                });
            }
            
        } else {
            alerta(res.mensagem, 'Falha ao obter lista de vendas Aguardando Pagamento.', 'warning');
            if(debugEnabled === true) {
                console.log(res);
            }
        }
    }, 'json').
    fail(function(ev){nativePOSTFail(ev);});
}

function getVendasPagas()
{
    $.post('/vendas/database/get/pagas', function(res){
        if(res.success) {
            //console.log(res.vendas);
            //console.log(res.vendas.length);
            if(res.vendas.length == 0) {
                if($('#pagasDiv > div').length == 0) {
                    $('#pagasDiv').append('<div class="text-center mx-2"></div>');
                }
                $('#pagasDiv > div').html('<i>Nada encontrado...</i>');
                $('#pagasDiv').find('table').remove();
            } else {
                $('#pagasDiv').find('table').remove();
                $('#pagasDiv').append('<table class="table table-sm table-bordered table-hover mb-0 table-responsive-sm" style="display:none;">'+
                    '<thead class="thead-dark"> <tr>'+
                    '<th>Cód</th> <th>(Cód) Roteiro</th> <th>(Cód) Cliente</th> <th>Status</th> <th>Data</th> <th>Valor Total</th>'+
                    '</tr> </thead> <tbody> </tbody> </table>');
                res.vendas.forEach(function(v){
                    let dataReserva = Dobbin.formataDataHora( new Date(v.data_reserva), true);
                    $('#pagasDiv tbody').append(
                        '<tr class="small cursor-pointer" onclick="getVenda('+v.id+')"> <td>'+v.id+'</td> <td>( '+v.roteiro_id+' ) '+v.roteiro_nome+'</td> '+
                        '<td>( '+v.cliente_id+' ) '+v.cliente_nome+'</td> <td>'+v.status+'</td> '+
                        '<td>'+dataReserva+'</td> <td>R$ '+Dobbin.converteCentavoEmReal(v.valor_total)+'</td></tr>'
                    );
                });

                $('#pagasDiv > div').fadeOut('fast', function(){
                    $(this).remove();
                    $('#pagasDiv table').slideDown();
                });
            }
            
        } else {
            alerta(res.mensagem, 'Falha ao obter lista de vendas pagas.', 'warning');
            if(debugEnabled === true) {
                console.log(res);
            }
        }
    }, 'json').
    fail(function(ev){nativePOSTFail(ev);});
}

function getVendasEstornadas()
{
    $.post('/vendas/database/get/estornadas', function(res){
        if(res.success) {
            //console.log(res.vendas);
            //console.log(res.vendas.length);
            if(res.vendas.length == 0) {
                if($('#estornadasDiv > div').length == 0) {
                    $('#estornadasDiv').append('<div class="text-center mx-2"></div>');
                }
                $('#estornadasDiv > div').html('<i>Nada encontrado...</i>');
                $('#estornadasDiv').find('table').remove();
            } else {
                $('#estornadasDiv').find('table').remove();
                $('#estornadasDiv').append('<table class="table table-sm table-bordered table-hover mb-0 table-responsive-sm" style="display:none;">'+
                    '<thead class="thead-dark"> <tr>'+
                    '<th>Cód</th> <th>(Cód) Roteiro</th> <th>(Cód) Cliente</th> <th>Status</th> <th>Data</th> <th>Valor Total</th>'+
                    '</tr> </thead> <tbody> </tbody> </table>');
                res.vendas.forEach(function(v){
                    let dataReserva = Dobbin.formataDataHora( new Date(v.data_reserva), true);
                    $('#estornadasDiv tbody').append(
                        '<tr class="small cursor-pointer" onclick="getVenda('+v.id+')"> <td>'+v.id+'</td> <td>( '+v.roteiro_id+' ) '+v.roteiro_nome+'</td> '+
                        '<td>( '+v.cliente_id+' ) '+v.cliente_nome+'</td> <td>'+v.status+'</td> '+
                        '<td>'+dataReserva+'</td> <td>R$ '+Dobbin.converteCentavoEmReal(v.valor_total)+'</td></tr>'
                    );
                });

                $('#estornadasDiv > div').fadeOut('fast', function(){
                    $(this).remove();
                    $('#estornadasDiv table').slideDown();
                });
            }
            
        } else {
            alerta(res.mensagem, 'Falha ao obter lista de vendas estornadas/devolvidas.', 'warning');
            if(debugEnabled === true) {
                console.log(res);
            }
        }
    }, 'json').
    fail(function(ev){nativePOSTFail(ev);});
}



/**
 * ./FIM MODAIS E PÁGINAS
 */

$(document).ready(function(){
    // Auto start
    console.log('%cPare agora!', 'font-size:50px; font-weight:bold; color:red; text-shadow: 1px 1px 0px black, 1px -1px 0px black, -1px 1px 0px black, -1px -1px 0px black');
    console.log('%cEssa ferramenta do navegador pode expor os dados sensíveis da plataforma. Não utilize-a sem permissão, nem cole códigos não autorizados!', 'font-size: 20px;')
    sidebarAutoHide();
    $('[data-toggle="popover"]').popover({'html':true});
    $('[data-toggle="tooltip"]').tooltip();
    $('#splash-screen').fadeOut('fast');
    systemVersion = $('[data-systemversion]:eq(0)').text();
    console.log('%cDobbin v'+systemVersion, 'font-size: 30px; font-weight:bold;');
    timeoutUpdate = setTimeout(checkSystemUpdate, 600000);
    if(sessionStorage.debug == undefined || sessionStorage.debug == 'false') {
        sessionStorage.debug = false;
        debugEnabled = false;
    } else {
        debugEnabled = true;
    }
    
    configLocalStorageStart();
    


    if(location.hash != '') {
        loadLanding(location.hash.substring(1, location.hash.length));
    } else {
        loadLanding('inicio');
    }
    $(window).resize(function(){
        sidebarAutoHide();
    });

    $(document).on('click', function(ev){
        if($(ev.target).parents('#main').length > 0 && $('nav.ds-navbar .nav-dropdown').is(':visible')) {
            $('nav.ds-navbar .nav-dropdown').fadeOut(200);
        }
        
    });

    // Alterna entre sempre exibir menu lateral e sempre ocultar menu lateral.
    $(document).on('click', '#sidebarFixedToggle', function(ev){
        if($(this).hasClass('alwaysShow')) {
            // Já está ativo. Desativa agora
            $(this).removeClass('alwaysShow');
            $(this).html('<i class="fas fa-toggle-off"></i>');
            $(this).attr('data-original-title', 'DESATIVADO. Clique para sempre exibir o menu.');
            configLocalStorageSet('sidebarAlwaysShow', false);
        } else {
            // Está desativado. Ativa agora.
            $(this).addClass('alwaysShow');
            $(this).html('<i class="fas fa-toggle-on"></i>');
            $(this).attr('data-original-title', 'ATIVADO. Clique para sempre ocultar o menu.');
            configLocalStorageSet('sidebarAlwaysShow', true);
        }
        sidebarAutoHide();
    });

    $(document).on('click', '[data-sidebar-toggle]', function(ev){
        ev.stopPropagation();
        ev.preventDefault();

        if($('#sidebar').hasClass('show')) {
            $('#sidebar').removeClass('show');
        } else {
            $('#sidebar').addClass('show');
        }
    });

    $(document).on('click', '.nav .btn-dropdown', function(ev){
        ev.stopPropagation();
        let btn = $(this);

        if(btn.siblings('ul').length > 0) {
            let drop = btn.siblings('ul').eq(0);
            //console.log(drop);
            if(drop.css('display') == 'block') {
                drop.slideUp('fast');
                btn.css('transform', 'rotate(0deg)');
            } else {
                drop.slideDown('fast');
                btn.css('transform', 'rotate(90deg)');
            }
        }
    });

    $(document).on('click', 'a', function(ev){ // Evento desativado. Navegação agora é feita pelo evento PopState
        /*
        let href = $(this).attr('href');

        if(href.charAt(0) == '#' && $(this).attr('target') != '_blank') {
            loadLanding(href.substring(1, href.length));
        }*/
    });

    $(document).on('submit', 'form', function(ev){
        if($(this).data('manual') == undefined) { // Envio automático
            ev.stopPropagation();
            ev.preventDefault();
            let form = $(this);
            let href = $(this).attr('action');
            $('#splash-screen').fadeIn();

            if(href == undefined) {
                // Do nothing..
            } else if(href == '#' || href == '') {
                alerta('Dados não foram enviados...', '', 'warning');
            } else {
                $.post(PREFIX_POST+href, $(this).serialize(), function(res){
                    $('#splash-screen').fadeOut();
                    if(res.success == true) {
                        form[0].reset();

                        // Apaga campos que devem ser apagados após a conclusão do envio.
                        form.find('.emptyAfterSubmit').val('');

                        alerta('Dados salvos.', '', 'success');
                        $('#modalClienteEditar, #modalUsuarioEditar, #modalMinhaContaEditar, '+
                        '#modalAlterarMinhaSenha, #modalCoordenadorEditar').modal('hide');
                    } else {
                        alerta(res.mensagem, 'Erro: ', 'warning');
                        console.log(res);
                    }
                }, 'json')
                .fail(function(ev){
                    nativePOSTFail(ev);
                });
            }
        } else {
            return true;
        }
    });

    $(document).on('submit', 'form#buscarClientes', function(ev){
        ev.stopPropagation();
        ev.preventDefault();

        let form = $(this);
        let href = 'clientes/buscar';
        let valor = form.find('[name="busca"]').val().trim();

        $.post(PREFIX_POST+href, {busca: valor}, function(res){
            //console.log(res);
            if(res.success == false) {
                alerta('Não conseguimos recuperar a pesquisa. Erro do servidor: '+res.mensagem, 'Falha!', 'warning');
            } else {
                $('#retornoBusca').html('<table class="table table-bordered table-sm">'+
                '<thead class="bg-dark text-white">'+
                    '<tr><th>Cód.</th><th>Nome</th><th class="d-none d-lg-table-cell">Email</th><th>Cidade</th>'+
                    '<th>Estado</th><th class="d-none d-lg-table-cell">Criado em</th><th></th></tr>'+
                '</thead><tbody style="font-size: .9rem;"></tbody></table>');

                $('#retornoBusca').prepend('<div class="mb-2">Total de registros: <span class="badge badge-info">'+res.clientes.length+'</span></div>');
                if(res.clientes.length == 0) {
                    $('#retornoBusca').find('table tbody').html('<tr><td class="text-center" colspan="7">Nada encontrado.</td></tr>');
                } else {
                    $('#retornoBusca').find('table tbody').html('');
                    for(let i = 0; i < res.clientes.length; i++) {
                        let criadoEm = new Date(res.clientes[i].criado_em);
                        
                        $('#retornoBusca').find('table tbody')
                            .append('<tr>'+
                            '<td>'+res.clientes[i].id+'</td>'+
                            '<td>'+highlight(res.clientes[i].nome, valor)+'</td>'+
                            '<td class="d-none d-lg-table-cell">'+highlight(res.clientes[i].email, valor)+'</td>'+
                            '<td>'+highlight(res.clientes[i].cidade, valor)+'</td>'+
                            '<td>'+res.clientes[i].estado+'</td>'+
                            '<td class="d-none d-lg-table-cell">'+Dobbin.formataDataHora(criadoEm)+'</td>'+
                            '<td><button type="button" class="btn btn-transparent btn-rounded btn-sm dropdown-toggle no-caret" data-toggle="dropdown"> <i class="fas fa-ellipsis-v fa-fw"></i> </button>'+
                            '<div class="dropdown-menu">'+
                                    '<button class="dropdown-item" onclick="loadCliente('+res.clientes[i].id+')"><i class="far fa-eye fa-fw mr-1"></i> Ver</button>'+
                                    '<button class="dropdown-item" onclick="editaCliente('+res.clientes[i].id+')"><i class="fas fa-pencil-alt fa-fw mr-1"></i> Editar</button>'+
                                    '<div class="dropdown-divider"></div>'+
                                    '<button class="dropdown-item text-danger" onclick="deleteCliente('+res.clientes[i].id+')"><i class="fas fa-trash fa-fw mr-1"></i> Apagar</button>'+
                            '</div></td>'+
                            '</tr>');
                    }


                    if(res.mensagem != '') {
                        $('#retornoBusca').append('<div class="alert alert-info">'+res.mensagem+'</div>')
                    }
                }
            }
        }, 'json').fail(function(ev){
            nativePOSTFail(ev);
        });
    });

    $(document).on('keyup', 'form#buscarClientes [name="busca"], form#buscarUsuarios [name="busca"], form#buscarCoordenadores [name="busca"] form#buscarVendas [name="busca"]', function(ev){
        let atual = $(this).val();
        let novo = atual.replace(/[=;]/gi, function(x){return '';});
        $(this).val(novo);
    });
    
    // Buscar coordenadores.
    $(document).on('submit', 'form#buscarCoordenadores', function(ev){
        ev.stopPropagation();
        ev.preventDefault();

        let form = $(this);
        let href = 'coordenadores/buscar';
        let valor = form.find('[name="busca"]').val().trim();

        $.post(PREFIX_POST+href, {busca: valor}, function(res){
            //console.log(res);
            if(res.success == false) {
                alerta('Não conseguimos recuperar a pesquisa. Erro do servidor: '+res.mensagem, 'Falha!', 'warning');
            } else {
                $('#retornoBusca').html('<table class="table table-bordered table-sm">'+
                '<thead class="bg-dark text-white">'+
                    '<tr><th>Cód.</th><th>Nome</th><th class="d-none d-lg-table-cell">Email</th><th>Cidade</th>'+
                    '<th>Estado</th><th class="d-none d-lg-table-cell">Criado em</th><th></th></tr>'+
                '</thead><tbody style="font-size: .9rem;"></tbody></table>');

                $('#retornoBusca').prepend('<div class="mb-2">Total de registros: <span class="badge badge-info">'+res.coordenadores.length+'</span></div>');
                if(res.coordenadores.length == 0) {
                    $('#retornoBusca').find('table tbody').html('<tr><td class="text-center" colspan="7">Nada encontrado.</td></tr>');
                } else {
                    $('#retornoBusca').find('table tbody').html('');
                    for(let i = 0; i < res.coordenadores.length; i++) {
                        let criadoEm = new Date(res.coordenadores[i].criado_em);
                        
                        $('#retornoBusca').find('table tbody')
                            .append('<tr>'+
                            '<td>'+res.coordenadores[i].id+'</td>'+
                            '<td>'+highlight(res.coordenadores[i].nome, valor)+'</td>'+
                            '<td class="d-none d-lg-table-cell">'+highlight(res.coordenadores[i].email, valor)+'</td>'+
                            '<td>'+highlight(res.coordenadores[i].cidade, valor)+'</td>'+
                            '<td>'+res.coordenadores[i].estado+'</td>'+
                            '<td class="d-none d-lg-table-cell">'+Dobbin.formataDataHora(criadoEm)+'</td>'+
                            '<td><button type="button" class="btn btn-transparent btn-rounded btn-sm dropdown-toggle no-caret" data-toggle="dropdown"> <i class="fas fa-ellipsis-v fa-fw"></i> </button>'+
                            '<div class="dropdown-menu">'+
                                    '<button class="dropdown-item" onclick="loadCoordenador('+res.coordenadores[i].id+')"><i class="far fa-eye fa-fw mr-1"></i> Ver</button>'+
                                    '<button class="dropdown-item" onclick="editaCoordenador('+res.coordenadores[i].id+')"><i class="fas fa-pencil-alt fa-fw mr-1"></i> Editar</button>'+
                                    '<div class="dropdown-divider"></div>'+
                                    '<button class="dropdown-item text-danger" onclick="deleteCoordenador('+res.coordenadores[i].id+')"><i class="fas fa-trash fa-fw mr-1"></i> Apagar</button>'+
                            '</div></td>'+
                            '</tr>');
                    }


                    if(res.mensagem != '') {
                        $('#retornoBusca').append('<div class="alert alert-info">'+res.mensagem+'</div>')
                    }
                }
            }
        }, 'json').fail(function(ev){
            nativePOSTFail(ev);
        });
    });

    $(document).on('submit', 'form#buscarUsuarios', function(ev){
        ev.stopPropagation();
        ev.preventDefault();

        let form = $(this);
        let href = 'usuarios/buscar';
        let valor = form.find('[name="busca"]').val().trim();

        $.post(PREFIX_POST+href, {busca: valor}, function(res){
            //console.log(res);
            if(res.success == false) {
                alerta('Não conseguimos recuperar a pesquisa. Erro do servidor: '+res.mensagem, 'Falha!', 'warning');
            } else {
                $('#retornoBusca').html('<table class="table table-bordered table-sm">'+
                '<thead class="bg-dark text-white">'+
                    '<tr><th>Cód.</th><th class="">Perfil</th><th>Usuário</th><th>Nível</th>'+
                    '<th class="d-none d-lg-table-cell">Criado em</th><th></th></tr>'+
                '</thead><tbody style="font-size: .9rem;"></tbody></table>');

                $('#retornoBusca').prepend('<div class="mb-2">Total de registros: <span class="badge badge-info">'+res.usuarios.length+'</span></div>');

                if(res.usuarios.length == 0) {
                    $('#retornoBusca').find('table tbody').html('<tr><td class="text-center" colspan="6">Nada encontrado.</td></tr>');
                } else {
                    $('#retornoBusca').find('table tbody').html('');
                    for(let i = 0; i < res.usuarios.length; i++) {
                        let criadoEm = new Date(res.usuarios[i].criado_em);
                        
                        $('#retornoBusca').find('table tbody')
                            .append('<tr>'+
                            '<td>'+res.usuarios[i].id+'</td>'+
                            '<td><img src="/media/images/av/'+res.usuarios[i].avatar+'" height="50" style="border-radius: 50%;"></td>'+
                            '<td>'+highlight(res.usuarios[i].nome, valor)+' '+highlight(res.usuarios[i].sobrenome, valor)+'<br>'+
                            '<small class="font-italic">@'+highlight(res.usuarios[i].usuario, valor)+'</small></td>'+
                            '<td>'+res.usuarios[i].nivel+'</td>'+
                            '<td class="d-none d-lg-table-cell">'+Dobbin.formataDataHora(criadoEm)+'</td>'+
                            '<td><button type="button" class="btn btn-transparent btn-rounded btn-sm dropdown-toggle no-caret" data-toggle="dropdown"> <i class="fas fa-ellipsis-v fa-fw"></i> </button>'+
                            '<div class="dropdown-menu">'+
                                    '<button class="dropdown-item" onclick="loadUsuario('+res.usuarios[i].id+')"><i class="far fa-eye fa-fw mr-1"></i> Ver</button>'+
                                    '<button class="dropdown-item" onclick="editaUsuario('+res.usuarios[i].id+')"><i class="fas fa-pencil-alt fa-fw mr-1"></i> Editar</button>'+
                                    '<div class="dropdown-divider"></div>'+
                                    '<button class="dropdown-item text-danger" onclick="deleteUsuario('+res.usuarios[i].id+')"><i class="fas fa-trash fa-fw mr-1"></i> Apagar</button>'+
                            '</div></td>'+
                            '</tr>');
                    }

                    if(res.mensagem != '') {
                        $('#retornoBusca').append('<div class="alert alert-info">'+res.mensagem+'</div>')
                    }
                }
            }
        }, 'json').
        fail(function(ev){
            alerta('Não foi possível recuperar esses dados agora.', 'Falha!', 'danger');
            //console.log(ev);
            debugador(ev);;
        });
    });

    // Buscar vendas
    $(document).on('submit', 'form#buscarVendas', function(ev){
        ev.stopPropagation();
        ev.preventDefault();

        let form = $(this);
        let href = 'vendas/buscar';
        let valor = form.find('[name="busca"]').val().trim();

        $.post(PREFIX_POST+href, {busca: valor}, function(res){
            //console.log(res);
            if(res.success == false) {
                alerta('Não conseguimos recuperar a pesquisa. Erro do servidor: '+res.mensagem, 'Falha!', 'warning');
            } else {
                
                $('#retornoBusca').html('<table class="table table-bordered table-sm">'+
                '<thead class="bg-dark text-white">'+
                    '<tr><th>Cód</th> <th>(Cód) Roteiro</th> <th>(Cód) Cliente</th>'+
                    '<th>Status</th> <th>Data</th> <th>Valor Total</th></tr>'+
                '</thead><tbody style="font-size: .9rem;"></tbody></table>');

                $('#retornoBusca').prepend('<div class="mb-2">Total de registros: <span class="badge badge-info">'+res.vendas.length+'</span></div>');

                if(res.vendas.length == 0) {
                    $('#retornoBusca').find('table tbody').html('<tr><td class="text-center" colspan="6">Nada encontrado.</td></tr>');
                } else {
                    $('#retornoBusca').find('table tbody').html('');
                    for(let i = 0; i < res.vendas.length; i++) {
                        
                        $('#retornoBusca').find('table tbody')
                            .append('<tr class="cursor-pointer" onclick="getVenda('+res.vendas[i].id+')">'+
                            '<td>'+res.vendas[i].id+'</td>'+
                            '<td>( '+res.vendas[i].roteiro_id+' ) '+res.vendas[i].roteiro_nome+' ('+Dobbin.formataData(new Date(res.vendas[i].roteiro_data_ini))+' a '+Dobbin.formataData(new Date(res.vendas[i].roteiro_data_fim))+')</td>'+
                            '<td>( '+res.vendas[i].cliente_id+' ) '+res.vendas[i].cliente_nome+'</td>'+
                            '<td>'+res.vendas[i].status+'</td>'+
                            '<td>'+Dobbin.formataDataHora(new Date(res.vendas[i].data_reserva))+'</td>'+
                            '<td>R$ '+Dobbin.converteCentavoEmReal(res.vendas[i].valor_total)+'</td>'+
                            '</tr>');
                    }

                    if(res.mensagem != '') {
                        $('#retornoBusca').append('<div class="alert alert-info">'+res.mensagem+'</div>')
                    }
                }
               console.log(res);
            }
        }, 'json').
        fail(function(ev){
            alerta('Não foi possível recuperar esses dados agora.', 'Falha!', 'danger');
            //console.log(ev);
            debugador(ev);;
        });
    });

    $(document).on('click', '.database .page-link', function(ev){
        ev.preventDefault();
        ev.stopPropagation();
        let pagelink = $(this);
        let pagination = $(this).parents('.pagination');
        
        //console.log($(this));
        if($(this).data('go-prev') != undefined) {
            //console.log('Voltar');
            let atual = parseInt($(this).parents('.database').eq(0).find('tbody.show').data('page'));
            if(atual > 1) {
                atual--;
                $(this).parents('.database').eq(0).find('tbody.show').removeClass('show');
                $(this).parents('.database').eq(0).find('[data-page="'+ atual +'"]').addClass('show');
                
            }
        } else if($(this).data('go-next') != undefined) {
            //console.log('Avançar');
            let atual = parseInt($(this).parents('.database').eq(0).find('tbody.show').data('page'));
            if(atual < $(this).parents('.database').eq(0).find('tbody').length) {
                atual++;
                $(this).parents('.database').eq(0).find('tbody.show').removeClass('show');
                $(this).parents('.database').eq(0).find('[data-page="'+ atual +'"]').addClass('show');
                
            }
        } else {
            let pagina = $(this).data('goto');

            // Verifica se página já está selecionada.
            if($(this).parents('.database').eq(0).find('tbody[data-page="'+pagina+'"]').hasClass('show') == false) {
                // Página não está selecionada.
                $(this).parents('.database').eq(0).find('tbody.show').removeClass('show');
                $(this).parents('.database').eq(0).find('tbody[data-page="'+pagina+'"]').addClass('show');
            }
        }

        // Define estado da paginação.
        let pagAtual = parseInt($(this).parents('.database').eq(0).find('tbody.show').data('page'));
        let pagTotal = parseInt($(this).parents('.database').eq(0).find('tbody').length);

        pagination.find('.active').removeClass('active');
        pagination.find('[data-goto="'+pagAtual+'"]').parents('.page-item').addClass('active');

        if(pagAtual == 1) {
            pagination.find('[data-go-prev]').parents('.page-item').addClass('disabled');
            pagination.find('[data-go-next]').parents('.page-item').removeClass('disabled');
        }
        if(pagAtual == pagTotal) {
            pagination.find('[data-go-prev]').parents('.page-item').removeClass('disabled');
            pagination.find('[data-go-next]').parents('.page-item').addClass('disabled');
        }
        if(pagAtual == 1 && pagAtual == pagTotal) {
            pagination.find('[data-go-prev], [data-go-next]').parents('.page-item').addClass('disabled');
        }
        if(pagAtual > 1 && pagAtual < pagTotal){
            pagination.find('[data-go-prev]').parents('.page-item').removeClass('disabled');
            pagination.find('[data-go-next]').parents('.page-item').removeClass('disabled');
        }
    });

    // Busca nome de cliente
    $(document).on('keyup', '#janClienteSelect :input[name="busca"]', function(ev){
        if(
            (ev.keyCode >= 48 && ev.keyCode <= 90) ||  //[0-9a-z]
            ev.keyCode == 8 || //[backspace]
            ev.keyCode == 32 || //[space]
            ev.keyCode == 46 || //[delete]
            (ev.keyCode >= 96 && ev.keyCode <= 105) //[numpad 0-9]
        ) {
            clearTimeout(timerKEY1);
            timerKEY1 = setTimeout(function(){
                
                if($(ev.target).val().trim().length >= 3) {
                    searchClienteNome($(ev.target).val().trim());
                } else {
                    $('#janClienteSelect').find('[data-selecionar]').attr('disabled','true');
                    $('#janClienteSelect [name="busca"]').next('.text-muted').html('');
                    $('#janClienteSelect table').slideUp(300, function(){$(this).remove()});
                }
            }, 700);
        }
    });

    // Busca nome do parceiro
    $(document).on('keyup', '#janParceirosSelect :input[name="busca"]', function(ev){
        if(
            (ev.keyCode >= 48 && ev.keyCode <= 90) ||  //[0-9a-z]
            ev.keyCode == 8 || //[backspace]
            ev.keyCode == 32 || //[space]
            ev.keyCode == 46 || //[delete]
            (ev.keyCode >= 96 && ev.keyCode <= 105) //[numpad 0-9]
        ) {
            clearTimeout(timerKEY1);
            timerKEY1 = setTimeout(function(){
                
                
                if($(ev.target).val().trim().length >= 3) {
                    searchParceiroNome($(ev.target).val().trim());
                } else {
                    $('#janParceirosSelect').find('[data-selecionar]').attr('disabled','true');
                    $('#janParceirosSelect [name="busca"]').next('.text-muted').html('');
                    $('#janParceirosSelect table').slideUp(300, function(){$(this).remove()});
                }
            }, 700);
        }
    });

    // Busca nome do coordenador
    $(document).on('keyup', '#janCoordenadorSelect :input[name="busca"]', function(ev){
        if(
            (ev.keyCode >= 48 && ev.keyCode <= 90) ||  //[0-9a-z]
            ev.keyCode == 8 || //[backspace]
            ev.keyCode == 32 || //[space]
            ev.keyCode == 46 || //[delete]
            (ev.keyCode >= 96 && ev.keyCode <= 105) //[numpad 0-9]
        ) {
            clearTimeout(timerKEY1);
            timerKEY1 = setTimeout(function(){
                
                
                if($(ev.target).val().trim().length >= 3) {
                    searchCoordenadorNome($(ev.target).val().trim());
                } else {
                    $('#janCoordenadorSelect').find('[data-selecionar]').attr('disabled','true');
                    $('#janCoordenadorSelect [name="busca"]').next('.text-muted').html('');
                    $('#janCoordenadorSelect table').slideUp(300, function(){$(this).remove()});
                }
            }, 700);
        }
    });

    // Busca nome do roteiro
    $(document).on('keyup', '#janRoteirosSelect :input[name="busca"]', function(ev){
        if(
            (ev.keyCode >= 48 && ev.keyCode <= 90) ||  //[0-9a-z]
            ev.keyCode == 8 || //[backspace]
            ev.keyCode == 32 || //[space]
            ev.keyCode == 46 || //[delete]
            (ev.keyCode >= 96 && ev.keyCode <= 105) //[numpad 0-9]
        ) {
            clearTimeout(timerKEY1);
            timerKEY1 = setTimeout(function(){
                
                
                if($(ev.target).val().trim().length >= 3) {
                    searchRoteiroNome($(ev.target).val().trim());
                } else {
                    $('#janRoteirosSelect').find('[data-selecionar]').attr('disabled','true');
                    $('#janRoteirosSelect [name="busca"]').next('.text-muted').html('');
                    $('#janRoteirosSelect table').slideUp(300, function(){$(this).remove()});
                }
            }, 700);
        }
    });

    $(document).on('click', '#janClienteSelect table tbody tr, #janParceirosSelect table tbody tr, #janCoordenadorSelect table tbody tr, #janRoteirosSelect table tbody tr', function(ev){
        let tr = $(this);
        let jan = tr.parents('.modal');

        if($(this).attr('disabled') == false || $(this).attr('disabled') == undefined) {
            $(this).parents('table').eq(0).find('.selecionado').removeClass('table-primary').removeClass('selecionado');
            tr.addClass('table-primary').addClass('selecionado');
            jan.find('[data-selecionar]').attr('disabled', false);
        }
    });

    $(document).on('change', '[name|="pseudo"]', function(ev){
        let tarNome = $(this).attr('name');
        tarNome = tarNome.replace("pseudo-", "");
        let tarObj = $(this).siblings('[name="'+tarNome+'"]');

        tarObj.val($(this).val());
        //console.log(tarNome);
    });

    $(document).on('click', '.toggleMinMax', function(ev){
        if($(this).data('target') != undefined && $(this).data('target') != null && $(this).data('target') != '') {
            let alvo = $(this).data('target');
            if($(alvo).is(':visible')) {
                $(alvo).slideUp();
                $(this).html('<i class="fas fa-plus"></i>');
            } else {
                $(alvo).slideDown();
                $(this).html('<i class="fas fa-minus"></i>');
            }
        }
    });

    $(document).on('change', '#modalParceiroServicoAdd [name="categoria"], #modalParceiroServicoEdita [name="categoria"]', function(ev){
        let modal = $(this).parents('.modal');
        if($(this).val() == 'Hospedagem') {
            modal.find('[name="tipoHospedagem"]').parents('.form-group').eq(0).show();
            modal.find('[name="tipoTransporte"]').parents('.form-group').eq(0).hide();
            modal.find('.beneficiosHospedagem :input').prop('checked', false).attr('disabled', false);
            modal.find('.beneficiosTransporte :input').prop('checked', false).attr('disabled', true);
        } else if($(this).val() == 'Transporte') {
            modal.find('[name="tipoHospedagem"]').parents('.form-group').eq(0).hide();
            modal.find('[name="tipoTransporte"]').parents('.form-group').eq(0).show();
            modal.find('.beneficiosHospedagem :input').prop('checked', false).attr('disabled', true);
            modal.find('.beneficiosTransporte :input').prop('checked', false).attr('disabled', false);
        } else {
            modal.find('[name="tipoHospedagem"]').parents('.form-group').eq(0).hide();
            modal.find('[name="tipoTransporte"]').parents('.form-group').eq(0).hide();
            modal.find('.beneficiosHospedagem :input').prop('checked', false).attr('disabled', true);
            modal.find('.beneficiosTransporte :input').prop('checked', false).attr('disabled', true);
        }
    });

    $(document).on('click', '.bloco-acord .acord-header', function(ev){
        let sender = $(ev.currentTarget);

        if(sender.siblings('.acord-body').is(':visible')) {
            // Oculta os detalhes
            sender.siblings('.acord-body').slideUp('fast');
            sender.parents('.bloco-acord').eq(0).removeClass('shadow-sm my-3');
            sender.find('button').html('<i class="fas fa-angle-down"></i>');
            sender.removeClass('py-3');
        } else {
            // Oculta todos os outros
            sender.parents('.bloco-acord').siblings('.bloco-acord.shadow-sm.my-3').children('.acord-body').hide();
            sender.parents('.bloco-acord').siblings('.bloco-acord.shadow-sm.my-3').children('.acord-header').find('button').html('<i class="fas fa-angle-down"></i>');
            sender.parents('.bloco-acord').siblings('.bloco-acord.shadow-sm.my-3').children('.acord-header').removeClass('py-3');
            sender.parents('.bloco-acord').siblings('.bloco-acord').removeClass('shadow-sm my-3');

            // Exibe os detalhes
            sender.siblings('.acord-body').slideDown('fast');
            sender.parents('.bloco-acord').eq(0).addClass('shadow-sm my-3');
            sender.find('button').html('<i class="fas fa-angle-up"></i>');
            sender.addClass('py-3');
            
        }
    });

    $(document).on('click', '.card-header.card-collapse', function(ev){
        let sender = $(ev.currentTarget);

        if(sender.siblings('.card-body').is(':visible')) {
            // Oculta os detalhes
            sender.siblings('.card-body').slideUp('fast');
        } else {
            // Exibe os detalhes
            sender.siblings('.card-body').slideDown('fast');
            
        }
    });

    // Adiciona contador aos TEXTAREA
    $(document).on('change keyup', 'textarea[maxlength]', function(ev){
        if($(this).prop('maxlength') != '' && $(this).prop('maxlength') > 0){
            let caracteres = $(this).val().length;
            let total = $(this).prop('maxlength');

            if($(this).next('div[data-contador]').length == 0) {
                $(this).after('<div data-contador class="small mt-1 text-right"></div>');
            }

            $(this).next('div[data-contador]').html(caracteres+'/'+total);
        }
    });

    $(document).on('change', '#controlesLog select', function(ev){
        let qtd = $('#controlesLog [name="qtd"]').val();
        let pagina = $('#controlesLog [name="pagina"]').val();
        location.hash = '#log/'+qtd+'/p/'+ pagina;
        loadLanding(location.hash);
    });

    $(document).on('click', '#controlesLog [data-controle-prev], #controlesLog [data-controle-next]', function(ev){
        let op = $('#controlesLog select[name="pagina"] > option:selected');
        console.log(op);
        if($(ev.currentTarget).data('controle-prev') != undefined) {
            console.log('PREV');

            // Prev
            if(op.prev().length > 0) {
                op.attr('selected', false);
                op.prev().attr('selected', true);
                $('#controlesLog select[name="pagina"]').trigger('change');
            }
        } else {
            console.log('NEXT');
            
            // Next
            if(op.next().length > 0) {
                op.attr('selected', false);
                op.next().attr('selected', true);
                $('#controlesLog select[name="pagina"]').trigger('change');
            }
        }


    });

    // Simulação: exibe campo quantidade de dias.
    $(document).on('change', '#simulacao [name="valor_tipo"], #roteironovo [name="valor_tipo"], #roteiroedita [name="valor_tipo"]', function(ev){
        if($(ev.currentTarget).find(':selected').val() == 'dia' || $(ev.currentTarget).find(':selected').val() == 'pessoa_dia') {
            $(ev.currentTarget).siblings('[data-valor-dias]').slideDown(100);
            $(ev.currentTarget).siblings('[data-valor-dias]').children(':input').attr('required', true);
        } else {
            $(ev.currentTarget).siblings('[data-valor-dias]').slideUp(100);
            $(ev.currentTarget).siblings('[data-valor-dias]').children(':input').attr('required', false);
        }
    });

    // Botão de "Carregar mais" registros do histórico.
    $(document).on('click', '#historico_negoc .loadMore', function(ev){
        let btn = $(ev.currentTarget);
        $.post(PREFIX_POST+'parceiros/'+btn.data('pid')+'/listahistorico/'+btn.data('qtd')+'/'+btn.data('start'), function(res){
            console.log(res);
            if(res.historico.length > 0) {
                let temp1, temp2, temp3;
                res.historico.forEach(function(h){
                    console.log(h);
                    let table = btn.parent().siblings('table');
                    
                    if(h.usuario_nome == null) {
                        temp1 = '<i>Usuário removido</i>';
                    } else {
                        temp1 = h.usuario_nome;
                    }

                    if(h.roteiro_nome == null && parseInt(h.roteiro_id) == 0) {
                        temp2 = '';
                    } else if(h.roteiro_nome == null && parseInt(h.roteiro_id) > 0) {
                        temp2 = ' &nbsp;|&nbsp; <strong>ROTEIRO:</strong> <i>Roteiro removido.</i>';
                    } else {
                        temp2 = ' &nbsp;|&nbsp; <strong>ROTEIRO:</strong> '+ h.roteiro_nome+' ('+Dobbin.formataData(new Date(h.data_ini))+')';
                    }

                    if(h.atualizado_em != null && h.atualizado_por != null) {
                        temp3 = '<br><strong>[Atualizado por: '+h.atualizado_por_nome+' em '+Dobbin.formataDataHora(new Date(h.atualizado_em))+']</strong>';
                    } else {
                        temp3 = '';
                    }

                    table.find('tbody').append('<tr data-detalhes="'+h.detalhes+'" data-etapa="'+h.etapa+'"><td rowspan="2" class="font-monospace">'+Dobbin.formataDataHora(new Date(h.criado_em))+'</td>'+
                    '<td class="font-weight-bold">'+h.etapa+'</td> <td>'+h.detalhes+'</td><td rowspan="2">'+
                    '<div class="btn-group"> '+
                    '<button type="button" class="btn btn-sm btn-primary" title="Editar registro." data-pid="'+btn.data('pid')+'" data-hid="'+h.id+'" dobbin-btn-editahistorico><i class="fas fa-pen"></i></button>'+
                    '<button type="button" class="btn btn-sm btn-danger" title="Excluir registro." data-pid="'+btn.data('pid')+'" data-hid="'+h.id+'" onclick="parcHistoricoDelete(this)"><i class="fas fa-trash"></i></button>'+
                    '</div> </td></tr>'+
                    '<tr><td colspan="2"><strong>AUTOR:</strong> '+temp1+temp2+temp3+'</td></tr>')
                });
            }

            if(res.historico.length < parseInt(btn.data('qtd')))
            {
                btn.parent().fadeOut(200, function(){btn.parent().remove();});
            } else {
                btn.data('start', parseInt(btn.data('start')) + parseInt(btn.data('qtd')));
            }

            
        }, 'json').
        fail(function(ev){
            nativePOSTFail(ev);
        });
    });

    $(document).on('click', '#janDinamica [data-dismiss="modal"]', function(ev){
        $(this).parents('.modal').eq(0).find('.modal-body').html('<div class="text-center py-2"><div class="spinner-grow text-primary"></div></div>');
        $('[data-toggle="tooltip"]').tooltip();
    });

    $(document).on('keyup change', '#modalEstornarVenda [name="valor_devolvido"]', function(ev){
        let alvo = $(ev.currentTarget);
        setTimeout(function(){
            if(alvo.val() == '0,00') {
                alvo.parents('form').find('button').attr('disabled', true);
            } else {
                alvo.parents('form').find('button').attr('disabled', false);
            }
        }, 100);
        
    });

    /**
     * EDITAR CAMPOS
     */

    
    /**
     * EDITAR CAMPOS
     */

    /**
     * VALIDAÇÃO AUTOMÁTICA
     */
    

    $(document).on('blur', '[data-validate-rg]', function(ev){
        if($(this).val().trim().length !== 0 && $(this).val().trim().length !== 10) {
            //ERRADO
            $(this).addClass('border-danger');
            if($(this).siblings('.invalid-feedback').length == 0) {
                $(this).after('<div class="invalid-feedback d-block">O RG precisa ter 10 dígitos (ou deixe em branco).</div>');
            }
        } else {
            $(this).removeClass('border-danger');
            $(this).siblings('.invalid-feedback').remove();
        }
    });

    $(document).on('blur', '[data-validate-cpf]', function(ev){
        if($(this).val().trim().length !== 0 && $(this).val().trim().length !== 11) {
            //ERRADO
            $(this).addClass('border-danger');
            if($(this).siblings('.invalid-feedback').length == 0) {
                $(this).after('<div class="invalid-feedback d-block">O CPF precisa ter 11 dígitos (ou deixe em branco).</div>');
            }
        } else {
            $(this).removeClass('border-danger');
            $(this).siblings('.invalid-feedback').remove();
        }
    });
    
    $(document).on('change', ':input[type="file"]', function(ev){
        //console.log(ev);
        let arq = $(ev.target)[0].files[0];
        if(arq.size / 1024 > $(ev.target).attr('max-size')) {
            alerta('Arquivo maior ['+(arq.size / 1024 / 1024)+'MB] que o permitido ['+($(ev.target).attr('max-size')/1024 )+'MB].');
            $(ev.target).val('');
        } else {
            //console.log( arq.size / 1024 + 'KB');
        }
        //console.log($(ev.target)[0].files[0]);
    });

    /**
     * ./VALIDAÇÃO AUTOMÁTICA
     */

    // Altera eventos dos modais, para permitir múltiplos modais.
    $('.modal').on({
        'show.bs.modal': function() { // Antes de abrir o modal
            var idx = $('.modal:visible').length;
          
            $(this).css('z-index', 1040 + (10 * idx));
            $('[data-toggle="tooltip"]').tooltip();
        },
        'shown.bs.modal': function() { // Depois de abrir o modal
            var idx = ($('.modal:visible').length) - 1; // raise backdrop after animation.
            $('.modal-backdrop').not('.stacked')
            .css('z-index', 1039 + (10 * idx))
            .addClass('stacked');
            $('[data-toggle="tooltip"]').tooltip();
        },
        'hidden.bs.modal': function() { // Depois de ter fechado o modal.
            if ($('.modal:visible').length > 0) {
                // restore the modal-open class to the body element, so that scrolling works
                // properly after de-stacking a modal.

                // Restaura a classe 'modal-open' ao elemento BODY, para o scroll
                // funcionar corretamente, depois de 'de-stacking' o modal.
                setTimeout(function() {
                    $(document.body).addClass('modal-open');
                    $('[data-toggle="tooltip"]').tooltip();
                }, 0);
            }
        }
    });

     // Carrega a página quando botões de voltar e avançar forem alterados.
    $(window).on('popstate', function(ev){
        //console.log(ev.currentTarget.location.hash);
        loadLanding(ev.currentTarget.location.hash);
    });
});