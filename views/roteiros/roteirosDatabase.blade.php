<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                BASE DE DADOS - Roteiros
            </div>
            <div class="card-body database" style="overflow-x:auto;">
                <div class="mb-3">
                    <small>Atualizado em: <span data-database-hora class="font-italic"></span></small>.
                    <button type="button" class="btn btn-info btn-sm" onclick="loadDatabaseRoteiros()"><i class="fas fa-sync"></i></button>
                </div>
                <table class="table table-sm table-bordered" id="clientes">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>Cód.</th>
                            <th>Nome (Partida e Retorno)</th>
                            <th>Total de Passagens</th>
                            <th></th>
                        </tr>
                    </thead>
                </table>
                <div class="d-flex justify-content-between">
                    <div id="infoDB" style="font-size:.8rem;"><!--Mostrando 1 a 25 de 100 entradas.--></div>
                    <div>
                        <ul class="pagination pagination-sm justify-content-end">
                            <li class="page-item disabled"><a class="page-link" data-go-prev href="javascript:void(0)">Anterior</a></li>
                            <li class="page-item active"><a class="page-link" data-goto="1" href="javascript:void(0)">1</a></li>
                            <li class="page-item"><a class="page-link" data-goto="2" href="javascript:void(0)">2</a></li>
                            <li class="page-item"><a class="page-link" data-goto="3" href="javascript:void(0)">3</a></li>
                            <li class="page-item"><a class="page-link" data-goto="4" href="javascript:void(0)">4</a></li>
                            <li class="page-item"><a class="page-link" data-goto="5" href="javascript:void(0)">5</a></li>
                            <li class="page-item"><a class="page-link" data-go-next href="javascript:void(0)">Próximo</a></li>
                        </ul>
                    </div>
                </div>
                
                
            </div>
        </div>
    </div>
</div>



<script>
    function loadDatabaseRoteiros()
    {
        let total = 0;
        //console.log(total);

        $.post(PREFIX_POST+'roteiros/database', {ini: 0, qtd: total}, function(res){
            //console.log(res);

            if(res.success == true) {
                dbLocal.roteirosDB = res.roteiros;
                dbLocal.roteirosDBHora = new Date();
                escreveTabela();
            } else {
                alerta('Erro ao recuperar base de dados: '+ res.mensagem)
            }
        }, 'json');
    }

    function escreveTabela()
    {
        let limiteLinhas = 25;
        let pages = 0;

        if(dbLocal.roteirosDB == undefined) {
            loadDatabaseRoteiros();
            return false;
        } else {
            let db = dbLocal.roteirosDB;
            //console.log(db);
            let datahora = dbLocal.roteirosDBHora;

            $('[data-database-hora]').text(Dobbin.formataDataHora(datahora));

            // Escreve linhas da tabela
            let i = 0;
            let mesCorrente = null;
            $('.database table').find('tbody').remove();
            db.forEach(function(x){
                if(i >= limiteLinhas) {
                    i = 0;
                }

                if(i == 0) {
                    pages++;
                    $('.database table').append('<tbody data-page="'+pages+'"></tbody>');
                }

                let dataIni = new Date(x.data_ini);
                
                // Verifica o mês
                if(mesCorrente == null) {
                    // Abre o mês.
                    mesCorrente = Dobbin.formataMesAno(dataIni, true);
                    $('.database table').find('tbody').last().append('<tr> <td colspan="4" class="py-2 pl-2 table-secondary"><strong>'+mesCorrente+'</strong></td> </tr>');
                    i++;
                } else {
                    // Verifica se continua no mesmo mês ou se é outro.
                    let mesmoMes = Dobbin.formataMesAno(dataIni, true);
                    if(mesCorrente !== mesmoMes) {
                        // Novo mesmo, abre novo mês.
                        mesCorrente = mesmoMes;
                        $('.database table').find('tbody').last().append('<tr> <td colspan="4" class="py-2 pl-2 table-secondary"><strong>'+mesCorrente+'</strong></td> </tr>');
                        i++;
                    }

                    mesmoMes = undefined;
                }
                


                $('.database table').find('tbody').last()
                    .append('<tr> <td>'+x.id+'</td> <td><a href="#roteiros/ver/'+x.id+'">'+x.nome+' ('+Dobbin.formataData(new Date(x.data_ini), true)+' a '+Dobbin.formataData(new Date(x.data_fim), true)+')</a></td>'+
                    '<td>'+( parseInt(x.passagens) + parseInt(x.qtd_coordenador) )+'</td> '+
                    '<td></td> </tr>');

                i++;
            });
            $('.database table').find('tbody').first().addClass('show');
            $('.database .pagination').children().remove();
            $('.database .pagination').append('<li class="page-item disabled"><a class="page-link" data-go-prev href="javascript:void(0)">Anterior</a></li>');
            
            for(i = 0; i < pages; i++) {
                $('.database .pagination').append('<li class="page-item"><a class="page-link" data-goto="'+ (i+1) +'" href="javascript:void(0)">'+ (i+1) +'</a></li>');
            }
            $('.database .pagination').append('<li class="page-item"><a class="page-link" data-go-next href="javascript:void(0)">Próximo</a></li>');
            $('.database .pagination').find('[data-goto="1"]').trigger('click');


        }


    }

    $(document).ready(function(){
        if(dbLocal.roteirosDB == "") {
            loadDatabaseRoteiros();
        } else {
            // Verifica se já passou 10 min ou mais desde a última atualização.
            let agora = new Date();
            if(agora - dbLocal.roteirosDBHora >= 10 *60*1000) {
                loadDatabaseRoteiros();
            } else {
                escreveTabela();
            }
        }

        
    });
</script>