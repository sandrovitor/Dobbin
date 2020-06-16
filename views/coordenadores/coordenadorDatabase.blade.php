<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                BASE DE DADOS - Coordenadores
            </div>
            <div class="card-body database" style="overflow-x:auto;">
                <div class="mb-3">
                    <small>Atualizado em: <span data-database-hora class="font-italic"></span></small>.
                    <button type="button" class="btn btn-info btn-sm" onclick="loadDatabaseCoordenadores()"><i class="fas fa-sync"></i></button>
                </div>
                <table class="table table-sm table-striped table-bordered" id="clientes">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>Cód.</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Cidade</th>
                            <th>Estado</th>
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
    function loadDatabaseCoordenadores()
    {
        let total = 0;
        //console.log(total);

        $.post(PREFIX_POST+'coordenadores/database', {ini: 0, qtd: total}, function(res){
            //console.log(res);

            if(res.success == true) {
                dbLocal.coordenadoresDB = res.coordenadores;
                dbLocal.coordenadoresDBHora = new Date();
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

        if(dbLocal.coordenadoresDB == undefined) {
            loadDatabaseCoordenadores();
            return false;
        } else {
            let db = dbLocal.coordenadoresDB;
            //console.log(db);
            let datahora = dbLocal.coordenadoresDBHora;

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

                $('.database table').find('tbody').last()
                    .append('<tr> <td>'+x.id+'</td> <td>'+x.nome+'</td> <td>'+x.email+'</td> '+
                    '<td>'+x.cidade+'</td> <td>'+x.estado+'</td> '+
                    '<td><button type="button" class="btn btn-transparent btn-rounded btn-sm dropdown-toggle no-caret" data-toggle="dropdown"> <i class="fas fa-ellipsis-v fa-fw"></i> </button>'+
                    '<div class="dropdown-menu">'+
                            '<button class="dropdown-item" onclick="loadCoordenador('+x.id+')"><i class="far fa-eye fa-fw mr-1"></i> Ver</button>'+
                            '<button class="dropdown-item" onclick="editaCoordenador('+x.id+')"><i class="fas fa-pencil-alt fa-fw mr-1"></i> Editar</button>'+
                            '<div class="dropdown-divider"></div>'+
                            '<button class="dropdown-item text-danger" onclick="deleteCoordenador('+x.id+')"><i class="fas fa-trash fa-fw mr-1"></i> Apagar</button>'+
                    '</div></td>'+
                    '</tr>');

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
        if(dbLocal.coordenadoresDB == "") {
            loadDatabaseCoordenadores();
        } else {
            // Verifica se já passou 10 min ou mais desde a última atualização.
            let agora = new Date();
            if(agora - dbLocal.coordenadoresDBHora >= 10 *60*1000) {
                loadDatabaseCoordenadores();
            } else {
                escreveTabela();
            }
        }

        
    });
</script>