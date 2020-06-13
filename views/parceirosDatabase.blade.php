<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                BASE DE DADOS - Parceiros
            </div>
            <div class="card-body database" style="overflow-x:auto;">
                <div class="mb-3">
                    <small>Atualizado em: <span data-database-hora class="font-italic"></span></small>.
                    <button type="button" class="btn btn-info btn-sm" onclick="loadDatabaseParceiros()"><i class="fas fa-sync"></i></button>
                </div>
                <table class="table table-sm table-striped table-bordered" id="parceiros">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>Cód.</th>
                            <th>Parceiro</th>
                            <th>Cidade</th>
                            <th>Estado</th>
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
    function loadDatabaseParceiros()
    {
        let total = 0;
        //console.log(total);

        $.post(PREFIX_POST+'parceiros/database', {ini: 0, qtd: total}, function(res){
            //console.log(res);

            if(res.success == true) {
                dbLocal.parceirosDB = res.parceiros;
                dbLocal.parceirosDBHora = new Date();
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

        if(dbLocal.parceirosDB == undefined) {
            loadDatabaseParceiros();
            return false;
        } else {
            let db = dbLocal.parceirosDB;
            //console.log(db);
            let datahora = dbLocal.parceirosDBHora;

            $('[data-database-hora]').text(formataDataHora(datahora));

            // Escreve linhas da tabela
            let i = 0;
            $('.database table').find('tbody').remove();
            db.forEach(function(x){
                if(i == limiteLinhas) {
                    i = 0;
                }

                if(i == 0) {
                    pages++;
                    $('.database table').append('<tbody data-page="'+pages+'"></tbody>');
                }
                let criadoEm = new Date(x.criado_em);
                let nomeExibicao = '';
                if(x.nome_fantasia != '') {
                    nomeExibicao = '<a href="#parceiros/ver/'+x.id+'"> <span class="font-weight-bold">'+x.nome_fantasia+'</span><br>'+
                                        '<span class="text-uppercase small font-italic">'+x.razao_social+'</span></a>';
                } else {
                    nomeExibicao = '<a href="#parceiros/ver/'+x.id+'"><span class="text-uppercase font-weight-bold">'+x.razao_social+'</span></a>';
                }
                $('.database table').find('tbody').last().
                    append('<tr><td>'+x.id+'</td> <td>'+nomeExibicao+'</td><td>'+x.cidade+'</td><td>'+x.estado+'</td></tr>');

                /*
                $('.database table').find('tbody').last()
                    .append('<tr> <td>'+x.id+'</td> <td>'+x.nome+'</td> <td>'+x.email+'</td> '+
                    '<td>'+x.cidade+'</td> <td>'+x.estado+'</td> '+
                    '<td><button type="button" class="btn btn-transparent btn-rounded btn-sm dropdown-toggle no-caret" data-toggle="dropdown"> <i class="fas fa-ellipsis-v fa-fw"></i> </button>'+
                    '<div class="dropdown-menu">'+
                            '<button class="dropdown-item" onclick="loadCliente('+x.id+')"><i class="far fa-eye fa-fw mr-1"></i> Ver</button>'+
                            '<button class="dropdown-item" onclick="editaCliente('+x.id+')"><i class="fas fa-pencil-alt fa-fw mr-1"></i> Editar</button>'+
                            '<div class="dropdown-divider"></div>'+
                            '<button class="dropdown-item text-danger" onclick="deleteCliente('+x.id+')"><i class="fas fa-trash fa-fw mr-1"></i> Apagar</button>'+
                    '</div></td>'+
                    '</tr>');
                    */

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
        if(dbLocal.parceirosDB == "") {
            loadDatabaseParceiros();
        } else {
            // Verifica se já passou 10 min ou mais desde a última atualização.
            let agora = new Date();
            if(agora - dbLocal.parceirosDBHora >= 10 *60*1000) {
                loadDatabaseParceiros();
            } else {
                escreveTabela();
            }
        }

        
    });
</script>