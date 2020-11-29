function addLinha(sender)
{
    let tabela = $(sender).prev().children('tbody');
    
    if(tabela.find('tr:not([data-example])').length < 1000) {
        tabela.find('tr[data-example]').eq(0).clone().appendTo(tabela);
        tabela.find('tr').last().show();
        tabela.find('tr').last().removeAttr('data-example');
    } else {
        alerta('Limite de 1000 linhas de despesa alcançado. Para liberar mais entradas, consulte desenvolvedor.', '', 'warning');
    }
}

function delLinha(sender)
{
    let tabela = $(sender).parents('tbody');

    
    if(tabela.find('tr:not([data-example])').length == 1) {
        alerta('Não é possível excluir esta linha. Pelo menos uma linha (em branco ou não) deve aparecer.');
    } else if(tabela.find('tr:not([data-example])').length > 1 && $(sender).parents('tr').is('[data-example]') == false) {
        $(sender).parents('tr').remove();
    }
    

}

function delAllLinha(sender) {
    let tabela = $(sender).siblings('table').find('tbody');

    if(confirm("Atenção! A linhas abaixo serão removidas. Você precisará informar linha a linha novamente.\n\nDeseja confirmar a operação?")) {
        if(tabela.find('tr:not([data-example])').length > 1) {
            for(let i = tabela.find('tr:not([data-example])').length - 1; i > 0; i--) {
                if(tabela.find('tr:not([data-example])').length > 1) {
                    tabela.find('tr:not([data-example])').eq(i).remove();
                }
            }
        }

        tabela.find('tr:not([data-example]) :input').val('');
        tabela.find('[dobbin-mask-money]').trigger('change');
        console.log(tabela.find('tr:not([data-example]) :input'));
    }
}

function calculaBalanco()
{
    let totalDespesa = 0;
    let totalReceita = 0;

    let linha;

    $('#qtdDespesa').text($('#listaDespesas tbody').find('tr:not([data-example])').length);
    $('#qtdReceita').text($('#listaReceitas tbody').find('tr:not([data-example])').length);

    for(let i = 0; i < $('#listaDespesas').find('tr:not([data-example])').length; i++) {
        linha = $('#listaDespesas').find('tr:not([data-example])').eq(i);
        if(linha.find('[name="valor"]').val() != '0,00') {
            // Só calcula se o valor for diferente de 0,00.
            totalDespesa = totalDespesa + Dobbin.converteRealEmCentavo(linha.find('[name="valor"]').val());
        }

    }

    for(let i = 0; i < $('#listaReceitas').find('tr:not([data-example])').length; i++) {
        linha = $('#listaReceitas').find('tr:not([data-example])').eq(i);
        if(linha.find('[name="valor"]').val() != '0,00') {
            // Só calcula se o valor for diferente de 0,00.
            totalReceita = totalReceita + Dobbin.converteRealEmCentavo(linha.find('[name="valor"]').val());
        }

    }

    // Escreve valor no resultado.
    $('#resultadoBalanco').find('[data-despesa]').text('R$ '+ Dobbin.converteCentavoEmReal(totalDespesa) );
    $('#resultadoBalanco').find('[data-receita]').text('R$ '+ Dobbin.converteCentavoEmReal(totalReceita) );
    $('#resultadoBalanco').find('[data-diferenca]').text('R$ '+ Dobbin.converteCentavoEmReal(totalReceita - totalDespesa) );
    if(totalReceita - totalDespesa < 0) {
        $('#resultadoBalanco').find('[data-diferenca]').addClass('text-danger').removeClass('text-success');
    } else {
        $('#resultadoBalanco').find('[data-diferenca]').addClass('text-success').removeClass('text-danger');
    }

    balanco.entrada = totalReceita;
    balanco.saida = totalDespesa;
    balanco.saldo = totalReceita - totalDespesa;
}

function gerarRelatorio()
{
    let inicio = $('input[name="inicio"]').val();
    let fim = $('input[name="fim"]').val();

    if(inicio == undefined || inicio == '') {
        alerta('Não foi possível saber o início do período do relatório.', 'Ops!', 'warning');
        return;
    }

    if(fim == undefined || fim == '') {
        alerta('Não foi possível saber o fim do período do relatório.', 'Ops!', 'warning');
        return;
    }

    $.post('/form/financeiro/relatorio', {
        inicio: inicio,
        fim: fim
    }, function(data){
        if(debugEnabled == true) {
            console.log(data);
        }

        if(typeof data == 'string') {
            alert(data);
            return false;
        }

        let resultadoDIV = $('#relatorioResultado');
        let alturaGrid = (data.meses.length * 40) + 30 + 60 + 2;
        let moedaOptions = {
            style: 'currency',
            currency: 'BRL',
            maximumFractionDigits: 2,
            minimumFractionDigits: 2,
            useGrouping: true
        };
        

        resultadoDIV.html('');
        
        if ($('[data-visao][value="1001"]').prop('checked') == 1 ){
            resultadoDIV.append('<h5>Dados do período <br><button type="button" class="btn btn-sm btn-dark mr-3 d-none" id="v1001_exportxls">Exportar XLS</button> '+
            '<button type="button" class="btn btn-sm btn-dark d-none" id="v1001_exportcsv">Exportar CSV</button></h5><br>'+
            '<div id="grid1" style="width: 100%; min-height: '+alturaGrid+'px;"></div><br>');
        };
        ($('[data-visao][value="1002"]').prop('checked') == 1 ? resultadoDIV.append('<div id="grafico1" style="width: 100%; height: 70vh;"></div><hr class="border-primary">') : '');
        ($('[data-visao][value="1004"]').prop('checked') == 1 ? resultadoDIV.append('<div id="grafico2" style="width: 100%; height: 70vh;"></div><hr class="border-primary">') : '');
        ($('[data-visao][value="1008"]').prop('checked') == 1 ? resultadoDIV.append('<div id="grafico3" style="width: 100%; height: 70vh;"></div><hr class="border-primary">') : '');
        
        
        // Inicializa o grafico 1.
        var graf1Options = {
            title: {
                text: 'PROGRESSÃO FINANCEIRA'
            },
            subtitle: {
                text: data.meses[0].mes+' a '+data.meses[data.meses.length - 1].mes
            },
            legend: { // Posição da legenda do gráfico.
                layout: 'horizontal',
                align: 'right',
                verticalAlign: 'top'
            },
            tooltip: {
                shared: true,
                crosshairs: true,
                //pointFormat: "R$ {point.y:,.2f}"
                valuePrefix: "R$ ",
                valueDecimals: 2
            },
            yAxis: { // Eixo vertical
                categories: [],
                type: 'linear',
                tickInterval: 200,
                allowDecimals: true,
                min: 0,
                title: {
                    text: "Valor (R$)",
                },
            },
            xAxis: { // Eixo horizontal.
                categories: [],
                title: {
                    text: "Meses",
                }
            },
            series: [
                {
                    name: 'Receitas',
                    type: 'line',
                    data: [],
                    lineWidth: 3
                },{
                    name: 'Despesas',
                    type: 'line',
                    data: [],
                    color: '#ff0000',
                    lineWidth: 3
                },{
                    name: 'Saldo',
                    type: 'line',
                    data: [],
                    color: '#00ff00',
                    lineWidth: 3
                },
            ],
            credits: {
                position: {
                    align: 'right',
                    verticalAlign: 'bottom',
                    x: -10,
                    y: -5
                },
                href: 'https://dssmart.com.br',
                text: 'Powered by Highcharts and Dobbin (DSSMART).',
            }
        };

        // Inicializa o gráfico 2.
        var graf2Options = {
            chart: {
                type: 'column'
            },
            title: {
                text: 'DESPESAS POR CATEGORIA'
            },
            subtitle: {
                text: data.meses[0].mes+' a '+data.meses[data.meses.length - 1].mes
            },
            legend: { // Posição da legenda do gráfico.
                layout: 'horizontal',
                align: 'right',
                verticalAlign: 'top'
            },
            tooltip: {
                shared: true,
                crosshairs: true,
                //pointFormat: "R$ {point.y:,.2f}"
                valuePrefix: "R$ ",
                valueDecimals: 2
            },
            plotOptions: {
                column: {
                    stacking: 'normal',
                    dataLabels: {
                        enabled: true
                    }
                }
            },
            yAxis: { // Eixo vertical
                categories: [],
                tickInterval: 200,
                allowDecimals: true,
                min: 0,
                title: {
                    text: "Valor (R$)",
                },
            },
            xAxis: { // Eixo horizontal.
                categories: [],
                title: {
                    text: "Meses",
                }
            },
            series: [
                {
                    name: 'FIXA',
                    data: [],
                    color: '#ffcccc',
                    lineWidth: 3
                },{
                    name: 'OCASIONAL',
                    data: [],
                    color: '#ff6666',
                    lineWidth: 3
                },{
                    name: 'NOVO ITINERÁRIO',
                    data: [],
                    color: '#ff0000',
                    lineWidth: 3
                },{
                    name: 'PAGAMENTOS',
                    data: [],
                    color: '#b30000',
                    lineWidth: 3
                },
                /*{
                    name: 'OUTROS1',
                    data: [],
                    color: '#990000',
                    lineWidth: 3
                },{
                    name: 'OUTROS2',
                    data: [],
                    color: '#4d0000',
                    lineWidth: 3
                },*/
            ],
            credits: {
                position: {
                    align: 'right',
                    verticalAlign: 'bottom',
                    x: -10,
                    y: -5
                },
                href: 'https://dssmart.com.br',
                text: 'Powered by Highcharts and Dobbin (DSSMART).',
            }
        };

        // Inicializa o gráfico 3.
        var graf3Options = {
            chart: {
                type: 'column'
            },
            title: {
                text: 'QTD. RESERVAS E VENDAS LANÇADAS'
            },
            subtitle: {
                text: data.meses[0].mes+' a '+data.meses[data.meses.length - 1].mes
            },
            legend: { // Posição da legenda do gráfico.
                layout: 'horizontal',
                align: 'right',
                verticalAlign: 'top'
            },
            tooltip: {
                shared: true,
                crosshairs: true,
                //pointFormat: "R$ {point.y:,.2f}"
                valuePrefix: "",
                valueDecimals: 0
            },
            plotOptions: {
                column: {
                    stacking: 'normal',
                    dataLabels: {
                        enabled: true
                    }
                }
            },
            yAxis: { // Eixo vertical
                categories: [],
                tickInterval: 50, // Diminui o intervalo de dados.
                allowDecimals: true,
                min: 0,
                title: {
                    text: "QTD",
                },
            },
            xAxis: { // Eixo horizontal.
                categories: [],
                title: {
                    text: "Meses",
                }
            },
            series: [
                {
                    name: 'RESERVAS',
                    data: [],
                    color: '#ffcccc',
                    lineWidth: 3
                },{
                    name: 'VENDAS',
                    data: [],
                    color: '#990000',
                    lineWidth: 3
                },
                /*{
                    name: 'NOVO ITINERÁRIO',
                    data: [],
                    color: '#ff0000',
                    lineWidth: 3
                },{
                    name: 'PAGAMENTOS',
                    data: [],
                    color: '#b30000',
                    lineWidth: 3
                },
                /*{
                    name: 'OUTROS1',
                    data: [],
                    color: '#990000',
                    lineWidth: 3
                },{
                    name: 'OUTROS2',
                    data: [],
                    color: '#4d0000',
                    lineWidth: 3
                },*/
            ],
            credits: {
                position: {
                    align: 'right',
                    verticalAlign: 'bottom',
                    x: -10,
                    y: -5
                },
                href: 'https://dssmart.com.br',
                text: 'Powered by Highcharts and Dobbin (DSSMART).',
            }
        };

        let dataset = [];
        data.meses.forEach(function(m){
            graf1Options.xAxis.categories.push(m.mes); // Nome da categoria
            graf2Options.xAxis.categories.push(m.mes); // Nome da categoria
            graf3Options.xAxis.categories.push(m.mes); // Nome da categoria

            let periodo = m.mes.split('/');
            let link = location.origin+m.link;

            if(m.temBalanco == true) {
                // Com balanço para o mês.
                let valor = m.receitas/100;

                graf1Options.series[0].data.push(valor);
                // -----------------
                valor = m.despesas/100;

                graf1Options.series[1].data.push(valor);
                // -----------------
                valor = m.saldo/100;

                graf1Options.series[2].data.push(valor);
            } else {
                // Sem balanço para o mês
                graf1Options.series[0].data.push(0);
                graf1Options.series[1].data.push(0);
                graf1Options.series[2].data.push(0);
            }


            //--------------------
            graf2Options.series[0].data.push(m.despesasCat.FIXA/100);
            graf2Options.series[1].data.push(m.despesasCat.OCASIONAL/100);
            graf2Options.series[2].data.push(m.despesasCat.NOVO_ITINERÁRIO/100);
            graf2Options.series[3].data.push(m.despesasCat.PAGAMENTOS/100);

            //--------------------
            graf3Options.series[0].data.push(parseInt(m.reservas));
            graf3Options.series[1].data.push(parseInt(m.vendas));


            dataset.push({
                'a': (m.link == '-' ? '<i class="fas fa-times text-danger" data-toggle="tooltip" title="Não há balanço para este mês."></i>' :
                        '<i class="fas fa-search cursor-pointer" onclick="window.open(\''+link+'\', \'_blank\')" '+
                        'data-toggle="tooltip" title="Clique aqui para ver o balanço do mês."'+
                        'data-placement="right"></i>'),
                'b': m.mes + (m.fechada == '0' && m.temBalanco == true ? ' <i class="fas fa-exclamation-circle" data-toggle="tooltip" '+
                            'title="O balanço desse mês ainda está aberto. Os resultados podem mudar."'+
                            'data-placement="right"></i>' :''),
                'c': (m.temBalanco == true ? (m.receitas/100).toLocaleString('pt-BR', moedaOptions) : 'R$ -'),
                'd': (m.temBalanco == true ? (m.despesas/100).toLocaleString('pt-BR', moedaOptions) : 'R$ -'),
                'e': (m.temBalanco == true ? (m.saldo/100).toLocaleString('pt-BR', moedaOptions) : 'R$ -'),
                'f': m.vendas,
                'g': m.reservas,
            });
        });


        var chart1, chart2, chart3;
        ($('[data-visao][value="1002"]').prop('checked') == true ? chart1 = Highcharts.chart('grafico1', graf1Options) : ''); // Visão 1002
        ($('[data-visao][value="1004"]').prop('checked') == true ?  chart2 = Highcharts.chart('grafico2', graf2Options) : ''); // Visão 1004
        ($('[data-visao][value="1008"]').prop('checked') == true ?  chart3 = Highcharts.chart('grafico3', graf3Options) : ''); // Visão 1008

        // Inicializa a GRID
        if($('[data-visao][value="1001"]').prop('checked') == true) { // Visão 1001
            var grid1 = new dhx.Grid('grid1', {
                columns: [
                    {id: "a", header: [{text: "Mês/Ano", colspan: 2, align: 'right'}],
                        footer:[
                            {text: "Média", colspan:2, align: 'right'},
                            {text: "Total", colspan:2, align: 'right'},
                        ],
                        width: 40, minWidth: 40, maxWidth: 40, align:'center', htmlEnable: true},
                    {id: "b", header: [], width: 120, minWidth: 120, maxWidth: 120, align: 'right', htmlEnable:true},
                    {id: "c", header: [{text: "Receitas"}], 
                        footer:[
                            {text: (data.mediaReceitaMes/100).toLocaleString('pt-BR', moedaOptions)+'/mês'},
                            {text: (data.receitaTotal/100).toLocaleString('pt-BR', moedaOptions)}
                        ], 
                        minWidth: 100, autoWidth: true},
                    {id: "d", header: [{text: "Despesas"}], 
                        footer:[
                            {text: (data.mediaDespesaMes/100).toLocaleString('pt-BR', moedaOptions)+'/mês'},
                            {text: (data.despesaTotal/100).toLocaleString('pt-BR', moedaOptions)}
                        ], 
                        minWidth: 100, autoWidth: true},
                    {id: "e", header: [{text: "Saldo"}], 
                        footer:[
                            {text: (data.mediaSaldoMes/100).toLocaleString('pt-BR', moedaOptions)+'/mês'},
                            {text: (data.saldoTotal/100).toLocaleString('pt-BR', moedaOptions)}
                        ], 
                        minWidth: 100, autoWidth: true},
                    {id: "f", header: [{text: "Vendas"}], 
                        footer:[
                            {text: data.mediaVendaMes.toLocaleString('pt-BR')+'/mês'},
                            {text: data.vendasTotal.toLocaleString('pt-BR')}
                        ], 
                        minWidth: 100, autoWidth: true, align: 'left'},
                    {id: "g", header: [{text: "Reservas"}], 
                        footer:[
                            {text: data.mediaReservaMes.toLocaleString('pt-BR')+'/mês'},
                            {text: data.reservasTotal.toLocaleString('pt-BR')}
                        ], 
                        minWidth: 100, autoWidth: true, align: 'left'},
                ],
                headerRowHeight: 30,
                footerRowHeight: 30,
                data: dataset,
                sortable: false,
                editable: false,
            });

            $('#v1001_exportxls').on('click', function(){
                grid1.export.xlsx({
                    name: "dados_por_periodo",
                    url: "//export.dhtmlx.com/excel"
                });
            });

            $('#v1001_exportcsv').on('click', function(){
                grid1.export.csv({
                    name:"dados_por_periodo", // grid data will be exported to a CSV file
                    rowDelimiter: "\n", // delimitador de linha
                    columnDelimiter: ";" // the semicolon delimiter will be used to separate columns
                });
            });
        }

        if(debugEnabled == true) {
            console.log(graf1Options, graf2Options);
            console.log(graf3Options);
        }
        
        restartTooltip();
    }, 'json');
}

function checkBalancoExiste()
{
    let periodo = $('[name="periodo"]').val();

    //console.log(periodo);
    if(periodo.substr(0,1) == '0') {
        // Inválido. Não faz pesquisa.
        return;
    } else if(periodo != '') {
        periodo = periodo.split('-');
        $.post(PREFIX_POST+'financeiro/check', {
            ano: periodo[0],
            mes: periodo[1]
        }, function(data){
            if(data.success == false) {
                alerta('Não foi possível verificar a existência de uma folha para este mês/ano.');
                return;
            } else {
                // Verifica se há folha.
                if(data.folha == false) {
                    // Não existe

                } else {
                    // Existe folha
                    var x = confirm("Foi encontrada uma folha para este mês e ano. O que gostaria de fazer?\n\n"+
                    "Clique em OK para carregar a folha do mês para edição.\nClique em Cancelar para alterar o mês/ano.");
                    
                    if(x == true) {
                        // Carrega folha existente.
                        location.href = "#financeiro/ver/"+periodo[0]+"/"+periodo[1]+"/"+data.folha;
                        
                    } else {
                        // Altera mês ano da folha.
                        $('[name="periodo"]').val('');
                        $('[name="periodo"]').focus();
                    }
                }
            }
        },'json')
    }
}

function excluirBalanco() {
    let x = confirm("Você está prestes a excluir este balanço. Você tem certeza de que quer continuar?\n\n"+
    "Clique em OK para confirmar e excluir.\nClique em Cancelar para sair sem excluir.");
    if(x == true) {
        let arq = $('#FOL_FILE').val();

        if(arq.length == 0) {
            alerta('Ocorreu um erro na interface. Não foi possível excluir. Aguarde alguns segundos para atualizarmos a interface.');
            setTimeout(function(){location.reload();}, 3000);
            return;
        }

        $.post(PREFIX_POST+'financeiro/apagar', {
            arq: arq
        }, function(data){
            console.log(data);
            if(data.success == true) {
                alert('O balanço foi removido com sucesso. Você voltará para a tela de listagem.');
                location.href = '#financeiro/listar';
            } else {
                alerta(data.mensagem, 'Falha!', 'warning');
            }
        }, 'json');
    }
}

function carregaReceitasAutomatico() {
    if($('[name="periodo"]').val() == '') {
        alerta('Informe o período antes de continuar.','Calma.', 'info');
        return; 
    }
    
    $('#splash-screen').show();

    $.post(PREFIX_POST+'financeiro/loadListaReceita', {
        periodo: $('[name="periodo"]').val()
    }, function(data){
        if(debugEnabled == true) {
            console.log(data);
        }

        if(data.mensagem != undefined) {
            alert(data.mensagem);
            return false;
        }

        if(data.length == 0) {
            $('#splash-screen').hide();
            alerta('A busca automática não encontrou nenhuma receita elegível para o período.', 'Aviso:', 'info');
            return;
        } else {
            let tabelaReceitas = $('#listaReceitas table tbody');
            data.forEach(function(rec){
                // Primeiro adiciona uma nova linha.
                $('#btnAddLinhaReceita').trigger('click');

                // Armazena a última linha numa variavel.
                let ultLinhaTab = tabelaReceitas.find('tr:not([data-example])').last();

                // Conteúdo popover
                let conteudoPopover = "<b>- Roteiro</b>: "+rec.roteiro_nome+"<br><b>- Cliente:</b> ("+rec.cliente_id+") "+rec.cliente_nome+"<br>"+
                "<hr><b>Forma de pagamento:</b> "+rec.forma_pagamento+"<br><b>Valor Total (Valor Recebido no mês):</b><br>R$ "+rec.valor_total+
                " (R$ "+rec.valor_recebido_mes+")<hr><small>Gerado automaticamente.<br>Clique no olho para ver mais.</small>";

                // Inputa os valores.
                ultLinhaTab.find('[name="nome"]').val('VENDA#'+rec.id);
                ultLinhaTab.find('[name="vencimento"]').val(rec.vencimento);
                ultLinhaTab.find('[name="valor"]').val(rec.valor).trigger('change');

                // Adiciona uma flag para sinalizar que o campo possui um "info".
                ultLinhaTab.find('[name="nome"]').attr('dobbin-flag-info', 'VENDA#'+rec.id);
                
                // Ícone
                ultLinhaTab.find('[name="nome"]').siblings('.input-group-prepend').append('<a href="javascript:void(0)" onclick="getVenda('+rec.id+')" class="btn btn-sm btn-secondary" '+
                    'data-toggle="popover" data-trigger="hover" data-content="'+conteudoPopover+'" title="Detalhes da VENDA #'+rec.id+'">'+
                    '<i class="fas fa-eye"></i>'+
                    '</a>');
            });

            
            restartTooltip();
            calculaBalanco();
            $('#splash-screen').hide();
        }
    },'json');
}

function carregaDespesasAutomatico() {
    if($('[name="periodo"]').val() == '') {
        alerta('Informe o período antes de continuar.','Calma.', 'info');
        return; 
    }
    
    $('#splash-screen').show();
    $.post(PREFIX_POST+'financeiro/loadListaDespesa', {
        periodo: $('[name="periodo"]').val()
    }, function(data){
        if(debugEnabled == true) {
            console.log(data);
        }

        if(typeof data == 'string') {
            alert(data);
            return false;
        }
        
        $('#splash-screen').hide();
        let tabelaDespesas = $('#listaDespesas table tbody');
        data.forEach(function(d){
            // Primeiro adiciona uma nova linha.
            $('#btnAddLinhaDespesa').trigger('click');

            // Armazena a última linha numa variavel.
            let ultLinhaTab = tabelaDespesas.find('tr:not([data-example])').last();

            // Inputa os valores.
            ultLinhaTab.find('[name="nome"]').val(d.nome);
            ultLinhaTab.find('[name="vencimento"]').val(d.vencimento);
            ultLinhaTab.find('[name="categoria"]').val(d.categoria);
            ultLinhaTab.find('[name="valor"]').val(d.valor).trigger('change');
        });

        calculaBalanco();
        restartTooltip();
        
     }, 'json').fail(function(ev) {console.log("Erro:\n"+ev.responseText);$('#splash-screen').hide();});

}

function loadInformacaoCampoReceita(sender) {
    let alvo = $(sender);
    let alvoValor = alvo.val().split(' ');

    if($('[name="periodo"]').val() == '') {
        alerta('Para obter alguns dados automáticos, informe o período antes de continuar.','Faltou algo.', 'info');
        $('[name="periodo"]').focus();
        return; 
    }
    let periodo = $('[name="periodo"]').val();

    alvoValor[0] = alvoValor[0].toUpperCase();

    // Verifica se o campo possui uma flag de info.
    if(alvo.attr('dobbin-flag-info') != undefined) {
        // Verifica se o conteudo da flag corresponde ao valor do input.
        if(alvo.attr('dobbin-flag-info') == alvoValor[0]) {
            // Não precisa atualiza. Interrompe aqui;
            //console.log('Não precisa atualizar.');
            return true;
        }
    }
    
    if(alvoValor[0].substr(0,6) == "VENDA#") {
        // Faz uma busca nas vendas, para trazer informação resumida.
        let x = alvoValor[0].split('#');
        let id = x[1];
        x = undefined;
        //$.post(PREFIX_POST+ 'financeiro/loadVendaInfo', {
        $.post('/vendas/database/load/venda/'+id, {
            id: id,
            periodo: periodo
        }, function(data){
            if(debugEnabled == true) {console.log(data);}
            if(data.success == true) {
                let rec = data.venda;

                let roteiroDataIni = rec.roteiro_data_ini.split('-');
                let roteiroDataFim = rec.roteiro_data_fim.split('-');

                // Adiciona uma flag para sinalizar que o campo possui um "info".
                alvo.attr('dobbin-flag-info', 'VENDA#'+rec.id);


                let conteudoPopover = "<b>- Roteiro</b>: "+rec.roteiro_nome+" ("+roteiroDataIni[2]+"/"+roteiroDataIni[1]+"/"+roteiroDataIni[0]+" a "+
                roteiroDataFim[2]+"/"+roteiroDataFim[1]+"/"+roteiroDataFim[0]+")<br><b>- Cliente:</b> ("+rec.cliente_id+") "+rec.cliente_nome+"<br>"+
                "<hr><b>Forma de pagamento:</b> "+rec.forma_pagamento+"<hr><small>Clique no olho para ver mais.</small>";

                // Adiciona o button e o popover.
                alvo.prev().html('<a href="javascript:void(0)" onclick="getVenda('+rec.id+')" class="btn btn-sm btn-secondary" '+
                    'data-toggle="popover" data-trigger="hover" data-content="'+conteudoPopover+'" dobbin-flag-info title="Detalhes da VENDA #'+rec.id+'">'+
                    '<i class="fas fa-eye"></i>'+
                    '</a>');

            }
        }, 'json')
    } else {
        // Limpa o button e o popover
        alvo.prev().find('[dobbin-flag-info]').remove();
        alvo.removeAttr('dobbin-flag-info');
    }

    alvo.val(alvoValor.join(' '));
    setTimeout(restartTooltip, 600);

    //console.log(alvoValor);
}

$(document).ready(function(){
    if(typeof balanco == undefined) {
        let balanco = {
            'folha': [],
            'mes': '',
            'ano': '',
            'obs': '',
            'entrada': '',
            'saida': '',
            'saldo': ''
        };
    } else {
        balanco = {
            'folha': [],
            'mes': '',
            'ano': '',
            'obs': '',
            'entrada': '',
            'saida': '',
            'saldo': ''
        };
    }
    
    $(document).on('blur', '[dobbin-mask-money]', function(){
        setTimeout(200, calculaBalanco());
    });

    $(document).on('change', '#listaReceitas [name="nome"]', function(ev){
        ev.stopPropagation();
        setTimeout(function(){loadInformacaoCampoReceita(ev.target);}, 300);
    });
});