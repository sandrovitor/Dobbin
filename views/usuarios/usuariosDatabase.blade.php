<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                BASE DE DADOS - Usuários
            </div>
            <div class="card-body database" style="overflow-x:auto;">
                <div class="mb-3">
                    <small>Atualizado em: <span data-database-hora class="font-italic"></span></small>.
                    <button type="button" class="btn btn-info btn-sm" onclick="loadDatabaseUsuarios()"><i class="fas fa-sync"></i></button>
                </div>
                <table class="table table-sm table-striped table-bordered" id="clientes">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>Cód.</th>
                            <th>Nome</th>
                            <th>Usuário</th>
                            <th>Nível:</th>
                            <th>Logado em:</th>
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
    function loadDatabaseUsuarios()
    {
        let total = 0;
        //console.log(total);

        $.post(PREFIX_POST+'usuarios/database', {ini: 0, qtd: total}, function(res){
            //console.log(res);

            if(res.success == true) {
                dbLocal.usuariosDB = res.usuarios;
                dbLocal.usuariosDBHora = new Date();
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

        if(dbLocal.clientesDB == undefined) {
            loadDatabaseClientes();
            return false;
        } else {
            let db = dbLocal.usuariosDB;
            //console.log(db);
            let datahora = dbLocal.usuariosDBHora;

            $('[data-database-hora]').text(Dobbin.formataDataHora(datahora));

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
                    .append('<tr> <td>'+x.id+'</td> <td><img src="/media/images/av/'+x.avatar+'" height="25" style="border-radius:50%" class="mr-1"> '+x.nome+' '+x.sobrenome+'</td> '+
                    '<td>@'+x.usuario+'</td> <td>'+x.nivel+'</td> <td>'+Dobbin.formataDataHora(new Date(x.logado_em))+'</td> '+
                    '<td><button type="button" class="btn btn-transparent btn-rounded btn-sm dropdown-toggle no-caret" data-toggle="dropdown"> <i class="fas fa-ellipsis-v fa-fw"></i> </button>'+
                    '<div class="dropdown-menu">'+
                            '<button class="dropdown-item" onclick="loadUsuario('+x.id+')"><i class="far fa-eye fa-fw mr-1"></i> Ver</button>'+
                            '<button class="dropdown-item" onclick="editaUsuario('+x.id+')"><i class="fas fa-pencil-alt fa-fw mr-1"></i> Editar</button>'+
                            '<div class="dropdown-divider"></div>'+
                            '<button class="dropdown-item text-danger" onclick="deleteUsuario('+x.id+')"><i class="fas fa-trash fa-fw mr-1"></i> Apagar</button>'+
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
        if(dbLocal.usuariosDB == "") {
            loadDatabaseUsuarios();
        } else {
            // Verifica se já passou 10 min ou mais desde a última atualização.
            let agora = new Date();
            if(agora - dbLocal.usuariosDBHora >= 10 *60*1000) {
                loadDatabaseUsuarios();
            } else {
                escreveTabela();
            }
        }

        
    });
</script>