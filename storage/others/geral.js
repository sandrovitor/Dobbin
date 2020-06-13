var dbLocal = {"clientesDB":[], "parceirosDB":[]};

function busca()
{

    let localBusca = $('#localBusca').val();
    let textoBusca = $('#textoBusca').val()
    switch(localBusca) {
        case '':
        default: 
            $('#localBusca').focus();
            break;

        // CLIENTES
        case 'clientes':
            if(textoBusca.trim() != '') {
                let t = textoBusca.trim().toLowerCase();
                let cl = $.grep(dbLocal.clientesDB, function(element, indice){
                    return(
                        element.nome.toLowerCase().indexOf(t) > -1 ||
                        element.sobrenome.toLowerCase().indexOf(t) > -1 ||
                        element.cidade.toLowerCase().indexOf(t) > -1 ||
                        element.email.toLowerCase().indexOf(t) > -1)
                });
                
                console.log(cl);
                $('#busca .database').html('<div style="overflow-x:auto; width:100%;"><table class="table table-sm table-striped table-bordered">'+
                '<thead class="bg-primary text-white">'+
                    '<tr> <th>Nome</th> <th>Sobrenome</th> <th>Email</th> <th>Cidade</th> <th>Estado</th> <th></th> </tr>'+
                '</thead><tbody class="show"></tbody></table></div>');

                if(cl.length == 0) {
                    $('#busca .database table tbody').append('<tr><td colapsn="6" class="text-center">Nada encontrado... Tente de novo</td></tr>');
                    $('#busca .database').prepend('<div>Mostrando 0 encontrados de '+dbLocal.clientesDB.length+' registros.</div>');
                } else {
                    if(cl.length > 1000) {
                        alert('A pesquisa retornou mais de 1000 resultados. Para evitar problemas no navegador, só serão exibidos 1000 resultados. Tente refinar a busca...');
                    }

                    let conta = 0;
                    for(let i = 0; i< 1000; i++) {
                        if(cl[i] == undefined) {
                            break;
                        }

                        let x = cl[i];
                        console.log(i, x);

                        $('#busca .database table tbody').append('<tr> <td>'+x.nome+'</td> <td>'+x.sobrenome+'</td> <td>'+x.email+'</td> '+
                        '<td>'+x.cidade+'</td> <td>'+x.estado+'</td> <td></td> '+
                        '</tr>');
                        conta++;
                    }

                    $('#busca .database').prepend('<div>Mostrando '+conta+' encontrados de '+dbLocal.clientesDB.length+' registros.</div>');
                }
            } else {
                $('#textoBusca').focus();
            }

            break;

        // PARCEIROS

    }
}

function writeClienteDB(db)
{
    let limiteLinhas = 25;
    let pages = 0;

    $('#clientes').find('.card-body').html('<div style="overflow-x:auto; width:100%;"><table class="table table-sm table-striped table-bordered" id="clientes">'+
    '<thead class="bg-primary text-white">'+
        '<tr> <th>Nome</th> <th>Email</th> <th>Cidade</th> <th>Estado</th> <th></th> </tr>'+
    '</thead></table></div>');

    $('#clientes').find('.card-body').append('<div class="d-flex justify-content-between"><div>'+
        '<ul class="pagination pagination-sm justify-content-end"></ul></div></div>');

    // Escreve linhas da tabela
    let i = 0;
    $('#clientes table').find('tbody').remove();
    db.forEach(function(x){
        if(i == limiteLinhas) {
            i = 0;
        }

        if(i == 0) {
            pages++;
            $('#clientes table').append('<tbody data-page="'+pages+'"></tbody>');
        }
        let criadoEm = new Date(x.criado_em);

        $('#clientes table').find('tbody').last()
            .append('<tr> <td>'+x.nome+'</td> <td>'+x.email+'</td> '+
            '<td>'+x.cidade+'</td> <td>'+x.estado+'</td> <td></td> '+
            '</tr>');

        i++;
    });
    $('#clientes table').find('tbody').first().addClass('show');
    $('#clientes .pagination').children().remove();
    $('#clientes .pagination').append('<li class="page-item disabled"><a class="page-link" data-go-prev href="javascript:void(0)">Anterior</a></li>');
    
    for(i = 0; i < pages; i++) {
        $('#clientes .pagination').append('<li class="page-item"><a class="page-link" data-goto="'+ (i+1) +'" href="javascript:void(0)">'+ (i+1) +'</a></li>');
    }
    $('#clientes .pagination').append('<li class="page-item"><a class="page-link" data-go-next href="javascript:void(0)">Próximo</a></li>');
    $('#clientes .pagination').find('[data-goto="1"]').trigger('click');
    
}

function sidebarAutoHide()
{
    if($('body').outerWidth() > 576) {
        $('#sidebar').addClass('show');
    } else {
        $('#sidebar').removeClass('show');
    }
}

function loadDBLocal()
{
    // Clientes
    rawClientes.forEach(function(c){
        let x = JSON.parse(c);
        x.forEach(function(d){
            dbLocal.clientesDB.push(d);
        });
        x = undefined;
    });

    rawClientes = undefined;

    // Escreve dados nas páginas.
    writeClienteDB(dbLocal.clientesDB);
}

$(document).ready(function(){
    sidebarAutoHide();
    
    loadDBLocal();

    $(window).resize(function(){
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

    $(document).on('click', '[data-toggle="pill"]', function(ev){
        $('.page-header-title').text($(this).text());
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
});